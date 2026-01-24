<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OAuth 인증 처리</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(180deg, #6f2dff 0%, #5a1fd9 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: white;
            margin: 0;
            padding: 20px;
        }
        .container {
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 2rem;
            max-width: 600px;
            width: 100%;
        }
        .spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1.5rem;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .error {
            background: rgba(239, 68, 68, 0.3);
            border: 1px solid rgba(239, 68, 68, 0.5);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            text-align: left;
        }
        .success {
            background: rgba(52, 168, 83, 0.3);
            border: 1px solid rgba(52, 168, 83, 0.5);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
        }
        .debug {
            background: rgba(0, 0, 0, 0.3);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            text-align: left;
            font-size: 0.85rem;
            font-family: monospace;
            word-break: break-all;
        }
        .back-btn {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.75rem 1.5rem;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            color: white;
            text-decoration: none;
        }
        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="spinner" id="spinner"></div>
        <h1 id="title">OAuth 인증 처리 중...</h1>
        <p id="message">잠시만 기다려주세요...</p>
        <div id="result"></div>
        <div id="debug" class="debug" style="display: none;"></div>
    </div>

    <script>
        console.log('=== OAuth2 Callback 시작 ===');

        const REDIRECT_URL = 'channel_manager.php';

        // 디버그 정보 표시 함수
        function showDebug(info) {
            const debugDiv = document.getElementById('debug');
            debugDiv.style.display = 'block';
            debugDiv.innerHTML = '<strong>디버그 정보:</strong><br>' + JSON.stringify(info, null, 2);
        }

        // 에러 표시 함수
        function showError(message, debugInfo) {
            console.error('에러:', message);
            document.getElementById('spinner').style.display = 'none';
            document.getElementById('title').textContent = '인증 오류';
            document.getElementById('message').textContent = '';
            document.getElementById('result').innerHTML = `
                <div class="error">
                    <strong>❌ 오류:</strong><br><br>
                    ${message}
                </div>
                <a href="${REDIRECT_URL}" class="back-btn">채널 관리로 돌아가기</a>
            `;
            if (debugInfo) showDebug(debugInfo);
        }

        // 성공 표시 함수
        function showSuccess() {
            document.getElementById('spinner').style.display = 'none';
            document.getElementById('title').textContent = '인증 성공!';
            document.getElementById('message').textContent = '';
            document.getElementById('result').innerHTML = `
                <div class="success">
                    <strong>✅ OAuth 인증 완료!</strong><br><br>
                    <small>2초 후 자동으로 채널 관리 페이지로 이동합니다...</small>
                </div>
                <a href="${REDIRECT_URL}" class="back-btn">채널 관리로 돌아가기</a>
            `;
            setTimeout(() => {
                window.location.href = REDIRECT_URL;
            }, 2000);
        }

        // URL 파라미터 가져오기
        const urlParams = new URLSearchParams(window.location.search);
        const code = urlParams.get('code');
        const error = urlParams.get('error');

        console.log('URL 파라미터:', { code: code ? code.substring(0, 20) + '...' : null, error });

        // 에러 체크
        if (error) {
            showError('Google 인증 거부: ' + error);
            throw new Error('Google OAuth 에러: ' + error);
        }

        if (!code) {
            showError('Authorization Code가 없습니다.');
            throw new Error('Authorization Code 없음');
        }

        // localStorage에서 Client ID/Secret 가져오기
        const clientId = localStorage.getItem('tempClientId') || localStorage.getItem('oauthClientId');
        const clientSecret = localStorage.getItem('tempClientSecret') || localStorage.getItem('oauthClientSecret');

        console.log('Client ID:', clientId ? clientId.substring(0, 30) + '...' : 'null');
        console.log('Client Secret:', clientSecret ? 'exists' : 'null');

        if (!clientId || !clientSecret) {
            showError('Client ID 또는 Client Secret이 없습니다.<br>채널 관리 페이지에서 먼저 입력해주세요.');
            throw new Error('OAuth credentials 없음');
        }

        // Token 교환 시작
        console.log('Token 교환 시작...');
        document.getElementById('message').textContent = 'Access Token을 가져오는 중...';

        const redirectUri = window.location.href.split('?')[0];
        console.log('Redirect URI:', redirectUri);

        fetch('https://oauth2.googleapis.com/token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                code: code,
                client_id: clientId,
                client_secret: clientSecret,
                redirect_uri: redirectUri,
                grant_type: 'authorization_code'
            })
        })
        .then(response => {
            console.log('Token 응답:', response.status, response.statusText);
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('에러 응답:', text);
                    let errorData;
                    try {
                        errorData = JSON.parse(text);
                    } catch (e) {
                        errorData = { error: text };
                    }
                    throw new Error(errorData.error_description || errorData.error || 'Token 교환 실패');
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Token 교환 성공!');
            console.log('Access Token:', data.access_token ? data.access_token.substring(0, 20) + '...' : 'null');
            console.log('Refresh Token:', data.refresh_token ? data.refresh_token.substring(0, 20) + '...' : '❌ 없음!');

            // ✅✅✅ 중요: Refresh Token 체크 ✅✅✅
            if (!data.refresh_token) {
                console.error('⚠️⚠️⚠️ Refresh Token이 발급되지 않았습니다!');
                showError(
                    '<strong style="font-size: 1.2rem;">⚠️ Refresh Token이 발급되지 않았습니다!</strong><br><br>' +
                    '이전에 이미 인증한 적이 있는 계정입니다.<br><br>' +
                    '<strong>해결 방법:</strong><br>' +
                    '1. <a href="https://myaccount.google.com/permissions" target="_blank" style="color: #ffeb3b; text-decoration: underline; font-weight: bold;">👉 Google 계정 권한 설정 열기</a><br>' +
                    '2. "타사 앱 액세스 권한이 있는 앱" 목록에서 이 앱을 찾아서 <strong>삭제</strong><br>' +
                    '3. 채널 관리 페이지로 돌아가서 <strong>다시 "Google 계정 연결"</strong> 클릭<br><br>' +
                    '<small style="opacity: 0.8;">※ Refresh Token이 없으면 1시간 후 자동으로 로그아웃됩니다.</small>',
                    {
                        hasAccessToken: !!data.access_token,
                        hasRefreshToken: false,
                        reason: 'Google이 Refresh Token을 발급하지 않았습니다. 기존 인증을 삭제하고 다시 시도하세요.',
                        expiresIn: data.expires_in
                    }
                );
                return; // ✅ 중요: 여기서 중단!
            }

            // localStorage에 저장
            const expiresAt = new Date().getTime() + (data.expires_in * 1000);

            localStorage.setItem('oauthAccessToken', data.access_token);
            localStorage.setItem('oauthRefreshToken', data.refresh_token); // ✅ 반드시 저장!
            localStorage.setItem('oauthExpiresAt', expiresAt);
            localStorage.setItem('oauthExpiresIn', data.expires_in);
            localStorage.setItem('oauthTokenType', data.token_type);

            // Client ID/Secret도 저장 (입력값 유지)
            localStorage.setItem('oauthClientId', clientId);
            localStorage.setItem('oauthClientSecret', clientSecret);

            // 임시 데이터 삭제
            localStorage.removeItem('tempClientId');
            localStorage.removeItem('tempClientSecret');

            console.log('localStorage 저장 완료');
            console.log('✅ Refresh Token 저장됨:', data.refresh_token.substring(0, 20) + '...');

            // 모드 확인
            const mode = localStorage.getItem('oauthMode');

            if (mode === 'addChannel') {
                // 채널 추가 모드: 이메일 + 채널 정보 자동 수집
                console.log('📺 채널 추가 모드 - 사용자 정보 및 채널 가져오기...');
                localStorage.removeItem('oauthMode');
                fetchUserInfoAndChannels(data.access_token, data.refresh_token);
            } else {
                // 일반 OAuth 인증 모드
                showSuccess();
            }
        })
        .catch(error => {
            console.error('최종 에러:', error);
            showError(error.message || '알 수 없는 오류', {
                error: error.message,
                clientId: clientId ? clientId.substring(0, 30) + '...' : 'null',
                redirectUri: redirectUri,
                hasCode: !!code,
                hasClientSecret: !!clientSecret
            });
        });

        // ==========================================
        // 사용자 정보 및 채널 가져오기 (자동 수집)
        // ==========================================

        function fetchUserInfoAndChannels(accessToken, refreshToken) {
            document.getElementById('message').textContent = '사용자 정보를 가져오는 중...';

            // 1단계: 사용자 이메일 가져오기
            fetch('https://www.googleapis.com/oauth2/v2/userinfo', {
                headers: { 'Authorization': `Bearer ${accessToken}` }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('사용자 정보 가져오기 실패');
                }
                return response.json();
            })
            .then(userInfo => {
                console.log('✅ 사용자 정보:', userInfo);
                const userEmail = userInfo.email;

                if (!userEmail) {
                    throw new Error('이메일 정보를 가져올 수 없습니다.');
                }

                console.log('✅ 사용자 이메일:', userEmail);
                document.getElementById('message').textContent = `📧 ${userEmail}\n채널 정보를 가져오는 중...`;

                // 2단계: 채널 정보 가져오기
                return fetch('https://www.googleapis.com/youtube/v3/channels?part=snippet&mine=true', {
                    headers: { 'Authorization': `Bearer ${accessToken}` }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('채널 정보 가져오기 실패');
                    }
                    return response.json();
                })
                .then(channelsData => {
                    console.log('✅ 채널 정보:', channelsData);

                    if (!channelsData.items || channelsData.items.length === 0) {
                        throw new Error('이 계정에는 YouTube 채널이 없습니다.');
                    }

                    const channel = channelsData.items[0];
                    const channelId = channel.id;
                    const channelName = channel.snippet.title;

                    console.log('✅ 채널 ID:', channelId);
                    console.log('✅ 채널명:', channelName);

                    // 3단계: 채널 추가
                    addChannelToList(userEmail, channelId, channelName, accessToken, refreshToken);
                });
            })
            .catch(error => {
                console.error('❌ 사용자 정보 또는 채널 정보 가져오기 실패:', error);
                showError('정보를 가져올 수 없습니다: ' + error.message);
            });
        }

        // ==========================================
        // 채널 목록에 추가
        // ==========================================

        function addChannelToList(email, channelId, channelName, accessToken, refreshToken) {
            console.log('📋 채널 추가 시작:', { email, channelId, channelName });
            console.log('🔑 Refresh Token:', refreshToken ? refreshToken.substring(0, 20) + '...' : '❌ 없음!');

            document.getElementById('spinner').style.display = 'block';
            document.getElementById('message').textContent = '채널을 추가하는 중...';

            // 기존 채널 목록 가져오기
            let channels = JSON.parse(localStorage.getItem('connectedChannels') || '[]');

            // 중복 체크 (channelId 기준)
            const existingIndex = channels.findIndex(ch => ch.id === channelId);

            // OAuth 토큰 정보
            const expiresAt = localStorage.getItem('oauthExpiresAt');
            const clientId = localStorage.getItem('oauthClientId');
            const clientSecret = localStorage.getItem('oauthClientSecret');

            const channelData = {
                id: channelId,                   // ✅ YouTube 채널 ID
                email: email,                    // ✅ 자동 수집된 이메일
                channelName: channelName,        // ✅ 채널명
                accessToken: accessToken,        // ✅ Access Token
                refreshToken: refreshToken,      // ✅✅✅ Refresh Token (필수!)
                expiresAt: expiresAt,           // ✅ 만료 시간
                clientId: clientId,             // ✅ Client ID
                clientSecret: clientSecret,     // ✅ Client Secret
                addedAt: new Date().toISOString()
            };

            console.log('💾 저장할 채널 데이터:', {
                ...channelData,
                accessToken: accessToken.substring(0, 20) + '...',
                refreshToken: refreshToken ? refreshToken.substring(0, 20) + '...' : '❌ 없음!',
                clientSecret: '***'
            });

            if (existingIndex !== -1) {
                // 기존 채널 업데이트
                channels[existingIndex] = channelData;
                console.log('✅ 기존 채널 업데이트:', channelName);
            } else {
                // 새 채널 추가
                channels.push(channelData);
                console.log('✅ 새 채널 추가:', channelName);
            }

            // 저장
            localStorage.setItem('connectedChannels', JSON.stringify(channels));
            localStorage.setItem('currentChannelId', channelId);

            console.log('📊 전체 채널 목록:', channels);

            // ✅ Refresh Token 저장 확인
            const savedChannels = JSON.parse(localStorage.getItem('connectedChannels'));
            const savedChannel = savedChannels.find(ch => ch.id === channelId);
            console.log('🔍 저장 확인 - Refresh Token:', savedChannel?.refreshToken ? '✅ 있음' : '❌ 없음!');

            // 성공 메시지 표시
            document.getElementById('spinner').style.display = 'none';
            document.getElementById('title').textContent = '채널 추가 완료!';
            document.getElementById('message').textContent = '';
            document.getElementById('result').innerHTML = `
                <div class="success">
                    <strong>✅ 채널이 추가되었습니다!</strong><br><br>
                    <div style="font-size: 1.2rem; margin: 1rem 0;">
                        <strong>📺 ${escapeHtml(channelName)}</strong>
                    </div>
                    <div style="font-size: 0.9rem; opacity: 0.9; margin-bottom: 0.5rem;">
                        📧 ${escapeHtml(email)}
                    </div>
                    <div style="font-size: 0.8rem; opacity: 0.7; font-family: monospace;">
                        ID: ${channelId}
                    </div>
                    <div style="margin-top: 1rem; padding: 0.5rem; background: rgba(76, 175, 80, 0.3); border-radius: 6px; font-size: 0.85rem;">
                        ${refreshToken ? '✅ Refresh Token 저장 완료 (자동 갱신 가능)' : '⚠️ Refresh Token 없음 (1시간 후 만료)'}
                    </div>
                    <div style="margin-top: 1.5rem; font-size: 0.85rem; opacity: 0.8;">
                        2초 후 채널 관리 페이지로 돌아갑니다...
                    </div>
                </div>
            `;

            setTimeout(() => {
                window.location.href = REDIRECT_URL;
            }, 2000);
        }

        // HTML 이스케이프 (XSS 방지)
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>
