<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title>OAuth 인증 처리 중...</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .container {
            background: white;
            border-radius: 16px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            max-width: 500px;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top-color: #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 2rem;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        h1 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 1rem;
        }

        p {
            color: #666;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .error {
            color: #ef4444;
            font-weight: 600;
        }

        .success {
            color: #10b981;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="spinner"></div>
        <h1 id="status">OAuth 인증 처리 중...</h1>
        <p id="message">잠시만 기다려주세요.</p>
    </div>

    <script>
        // ==========================================
        // OAuth 콜백 처리
        // ==========================================
        async function handleOAuthCallback() {
            const urlParams = new URLSearchParams(window.location.search);
            const code = urlParams.get('code');
            const state = urlParams.get('state');
            const error = urlParams.get('error');

            const statusEl = document.getElementById('status');
            const messageEl = document.getElementById('message');

            // 에러 체크
            if (error) {
                statusEl.textContent = '❌ 인증 실패';
                statusEl.className = 'error';
                messageEl.textContent = `오류: ${error}`;
                messageEl.className = 'error';

                setTimeout(() => {
                    window.location.href = 'channel_manager.php';
                }, 3000);
                return;
            }

            // State 검증
            const savedState = localStorage.getItem('oauthState');
            if (state !== savedState) {
                statusEl.textContent = '❌ 보안 오류';
                statusEl.className = 'error';
                messageEl.textContent = 'State 값이 일치하지 않습니다.';
                messageEl.className = 'error';

                setTimeout(() => {
                    window.location.href = 'channel_manager.php';
                }, 3000);
                return;
            }

            // Authorization Code가 없으면
            if (!code) {
                statusEl.textContent = '❌ 인증 코드 없음';
                statusEl.className = 'error';
                messageEl.textContent = 'Authorization Code를 받지 못했습니다.';
                messageEl.className = 'error';

                setTimeout(() => {
                    window.location.href = 'channel_manager.php';
                }, 3000);
                return;
            }

            try {
                // Client ID/Secret 가져오기
                const clientId = localStorage.getItem('tempClientId');
                const clientSecret = localStorage.getItem('tempClientSecret');

                if (!clientId || !clientSecret) {
                    throw new Error('Client ID/Secret이 없습니다.');
                }

                statusEl.textContent = '🔄 토큰 교환 중...';
                messageEl.textContent = 'Authorization Code를 Access Token으로 교환하고 있습니다.';

                // Step 1: Authorization Code를 Access Token으로 교환
                const redirectUri = window.location.origin + window.location.pathname;

                const tokenResponse = await fetch('https://oauth2.googleapis.com/token', {
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
                });

                if (!tokenResponse.ok) {
                    const errorData = await tokenResponse.json();
                    throw new Error(errorData.error_description || '토큰 교환 실패');
                }

                const tokenData = await tokenResponse.json();
                const accessToken = tokenData.access_token;
                const refreshToken = tokenData.refresh_token; // ✅ 중요!
                const expiresIn = tokenData.expires_in || 3600;
                const expiresAt = new Date().getTime() + (expiresIn * 1000);

                console.log('✅ 토큰 발급 성공:', {
                    hasAccessToken: !!accessToken,
                    hasRefreshToken: !!refreshToken,
                    expiresIn: expiresIn + '초'
                });

                // ⚠️ Refresh Token이 없으면 경고
                if (!refreshToken) {
                    console.warn('⚠️ Refresh Token이 발급되지 않았습니다!');
                    alert('⚠️ Refresh Token이 발급되지 않았습니다.\n\n다시 연결을 시도해주세요.');
                    window.location.href = 'channel_manager.php';
                    return;
                }

                statusEl.textContent = '📧 사용자 정보 가져오는 중...';
                messageEl.textContent = 'Google 계정 정보를 확인하고 있습니다.';

                // Step 2: 사용자 정보 가져오기
                const userInfoResponse = await fetch('https://www.googleapis.com/oauth2/v2/userinfo', {
                    headers: {
                        'Authorization': `Bearer ${accessToken}`
                    }
                });

                if (!userInfoResponse.ok) {
                    throw new Error('사용자 정보를 가져올 수 없습니다.');
                }

                const userInfo = await userInfoResponse.json();
                const email = userInfo.email;

                statusEl.textContent = '📺 채널 정보 가져오는 중...';
                messageEl.textContent = 'YouTube 채널 정보를 확인하고 있습니다.';

                // Step 3: YouTube 채널 정보 가져오기
                const channelResponse = await fetch('https://www.googleapis.com/youtube/v3/channels?part=snippet&mine=true', {
                    headers: {
                        'Authorization': `Bearer ${accessToken}`
                    }
                });

                if (!channelResponse.ok) {
                    throw new Error('채널 정보를 가져올 수 없습니다.');
                }

                const channelData = await channelResponse.json();

                if (!channelData.items || channelData.items.length === 0) {
                    throw new Error('YouTube 채널이 없습니다.');
                }

                const channel = channelData.items[0];
                const channelId = channel.id;
                const channelName = channel.snippet.title;
                const channelThumbnail = channel.snippet.thumbnails?.default?.url || '';

                console.log('✅ 채널 정보:', {
                    channelId,
                    channelName,
                    email
                });

                statusEl.textContent = '💾 채널 정보 저장 중...';
                messageEl.textContent = '채널 정보를 로컬에 저장하고 있습니다.';

                // Step 4: 채널 정보 저장
                const oauthMode = localStorage.getItem('oauthMode') || 'initial';

                if (oauthMode === 'addChannel') {
                    // 기존 채널 목록에 추가
                    const channels = JSON.parse(localStorage.getItem('connectedChannels') || '[]');

                    // 중복 체크
                    const existingIndex = channels.findIndex(ch => ch.id === channelId);

                    if (existingIndex !== -1) {
                        // 기존 채널 업데이트
                        channels[existingIndex] = {
                            id: channelId,
                            email: email,
                            channelName: channelName,
                            thumbnail: channelThumbnail,
                            accessToken: accessToken,
                            refreshToken: refreshToken, // ✅ 반드시 저장!
                            expiresAt: expiresAt,
                            clientId: clientId,
                            clientSecret: clientSecret,
                            connectedAt: channels[existingIndex].connectedAt || new Date().toISOString()
                        };

                        console.log('✅ 기존 채널 업데이트:', channelName);
                    } else {
                        // 새 채널 추가
                        channels.push({
                            id: channelId,
                            email: email,
                            channelName: channelName,
                            thumbnail: channelThumbnail,
                            accessToken: accessToken,
                            refreshToken: refreshToken, // ✅ 반드시 저장!
                            expiresAt: expiresAt,
                            clientId: clientId,
                            clientSecret: clientSecret,
                            connectedAt: new Date().toISOString()
                        });

                        console.log('✅ 새 채널 추가:', channelName);
                    }

                    localStorage.setItem('connectedChannels', JSON.stringify(channels));

                } else {
                    // 첫 번째 OAuth 인증 (기존 방식 호환)
                    localStorage.setItem('oauthAccessToken', accessToken);
                    localStorage.setItem('oauthRefreshToken', refreshToken); // ✅ 반드시 저장!
                    localStorage.setItem('oauthEmail', email);

                    // connectedChannels에도 저장
                    const channels = [{
                        id: channelId,
                        email: email,
                        channelName: channelName,
                        thumbnail: channelThumbnail,
                        accessToken: accessToken,
                        refreshToken: refreshToken, // ✅ 반드시 저장!
                        expiresAt: expiresAt,
                        clientId: clientId,
                        clientSecret: clientSecret,
                        connectedAt: new Date().toISOString()
                    }];

                    localStorage.setItem('connectedChannels', JSON.stringify(channels));
                    localStorage.setItem('currentChannelId', channelId);

                    console.log('✅ 첫 번째 채널 연결:', channelName);
                }

                // Client ID/Secret 영구 저장
                localStorage.setItem('oauthClientId', clientId);
                localStorage.setItem('oauthClientSecret', clientSecret);

                // 임시 데이터 삭제
                localStorage.removeItem('tempClientId');
                localStorage.removeItem('tempClientSecret');
                localStorage.removeItem('oauthState');
                localStorage.removeItem('oauthMode');

                // 성공 메시지
                statusEl.textContent = '✅ 인증 완료!';
                statusEl.className = 'success';
                messageEl.textContent = `${channelName} 채널이 성공적으로 연결되었습니다.`;
                messageEl.className = 'success';

                console.log('✅ OAuth 인증 완료');

                // 3초 후 리다이렉트
                setTimeout(() => {
                    window.location.href = 'channel_manager.php';
                }, 2000);

            } catch (error) {
                console.error('❌ OAuth 처리 실패:', error);

                statusEl.textContent = '❌ 인증 실패';
                statusEl.className = 'error';
                messageEl.textContent = error.message;
                messageEl.className = 'error';

                setTimeout(() => {
                    window.location.href = 'channel_manager.php';
                }, 3000);
            }
        }

        // 페이지 로드 시 실행
        window.addEventListener('DOMContentLoaded', handleOAuthCallback);
    </script>
</body>
</html>
