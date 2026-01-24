<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ONETOP 프리미엄 채널관리</title>

    <!-- 파비콘 추가 -->
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="shortcut icon" href="img/favicon.ico">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        .main-content {
            margin-left: 600px;
            height: 100vh;
            display: flex;
            flex-direction: column;
            background: #f5f7fa;
        }

        .page-header {
            background: white;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .menu-toggle {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
            margin-right: 1rem;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
        }

        .content-container {
            flex: 1;
            display: flex;
            overflow: hidden;
            gap: 1rem;
            padding: 1rem;
        }

        .sidebar-panel {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .sidebar-header {
            padding: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .badge {
            background: rgba(255, 255, 255, 0.3);
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .list-container {
            flex: 1;
            overflow-y: auto;
            padding: 0.5rem;
        }

        /* 구독 채널 아이템 */
        .subscription-item {
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            background: #f8f9fa;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            border: 2px solid transparent;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .subscription-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
            border-color: #667eea;
        }

        .subscription-item.active {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border-color: #667eea;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
        }

        .subscription-thumbnail {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            border: 2px solid #ddd;
        }

        .subscription-info {
            flex: 1;
            min-width: 0;
        }

        .subscription-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.25rem;
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .subscription-desc {
            font-size: 0.75rem;
            color: #666;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .content-area {
            flex: 1;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .content-header {
            padding: 1rem 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content-title {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .refresh-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.5);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
            font-size: 0.9rem;
        }

        .refresh-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .detail-container {
            flex: 1;
            overflow-y: auto;
            padding: 2rem;
        }

        .loading {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            color: #999;
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top-color: #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 1rem;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .error-message {
            padding: 2rem;
            text-align: center;
            color: #ef4444;
            font-size: 0.95rem;
        }

        .empty-state {
            padding: 3rem;
            text-align: center;
            color: #999;
        }

        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        /* 상세 정보 스타일 */
        .detail-thumbnail {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin: 0 auto 2rem;
            display: block;
        }

        .detail-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1rem;
            text-align: center;
        }

        .detail-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .detail-box-title {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .detail-box-content {
            font-size: 0.9rem;
            color: #333;
            line-height: 1.6;
        }

        .detail-box-content.code {
            font-family: monospace;
            font-size: 0.8rem;
            word-break: break-all;
        }

        .action-btn {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.95rem;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
    <style>
        /* 즐겨찾기 버튼 스타일 */
        .favorite-btn {
            background: none;
            border: none;
            font-size: 1.4rem;
            cursor: pointer;
            padding: 0.25rem;
            transition: all 0.2s;
            flex-shrink: 0;
            opacity: 0.8;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.5rem;
        }

        .subscription-item:hover .favorite-btn {
            opacity: 1;
        }

        .favorite-btn:hover {
            transform: scale(1.2);
        }

        /* 복사 버튼 스타일 */
        .copy-box {
            position: relative;
        }

        .copy-btn {
            position: absolute;
            right: 0.5rem;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 6px;
            color: white;
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .copy-btn:hover {
            transform: translateY(-50%) scale(1.05);
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
        }

        .copy-btn:active {
            transform: translateY(-50%) scale(0.95);
        }

        .copy-btn.copied {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .copy-box .detail-box-content {
            padding-right: 5rem;
        }
    </style>
</head>
<body>
    <!-- 사이드바 Include -->
    <?php include 'z_menu.php'; ?>

    <!-- 메인 컨텐츠 -->
    <div class="main-content">
        <div class="page-header">
            <button class="menu-toggle" onclick="toggleSidebar()">☰</button>
            <h1 class="page-title" style="flex: 1; text-align: center;">🚗 채널 관리</h1>
            <div style="width: 60px;"></div>
        </div>

        <!-- 채널 관리 컨텐츠 -->
        <div class="content-container">
            <!-- 왼쪽: 구독 리스트 -->
            <div style="width: 40%; display: flex; flex-direction: column;">
                <div class="sidebar-panel" style="height: 100%;">
                    <div class="sidebar-header">
                        <span>📺 구독 리스트</span>
                        <span class="badge" id="subscriptionCount">0</span>
                    </div>
                    <div class="list-container" id="subscriptionList">
                        <div class="empty-state">
                            <div class="empty-state-icon">📺</div>
                            <div>연결된 채널을 선택하면<br>구독 리스트가 표시됩니다.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 오른쪽: 상세 정보 -->
            <div class="content-area" style="flex: 1;">
                <div class="content-header">
                    <div class="content-title" id="contentTitle">📋 채널 정보</div>
                    <button class="refresh-btn" id="toggleViewBtn" onclick="toggleView()" style="display: none;">
                        🎬 영상 목록 보기
                    </button>
                </div>
                <div class="detail-container" id="detailContainer">
                    <div class="empty-state">
                        <div class="empty-state-icon">📋</div>
                        <div>구독 채널을 선택하면<br>상세 정보가 표시됩니다.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        // ==========================================
        // 전역 변수
        // ==========================================
        let currentChannelId = null;
        let currentSubscriptions = [];
        let selectedSubscription = null;
        let lastChannelChangeTimestamp = null;
        let isLoading = false;
        let favoriteChannels = JSON.parse(localStorage.getItem('favoriteChannels') || '{}');

        // ==========================================
        // 페이지 로드 시 초기화
        // ==========================================
        window.addEventListener('DOMContentLoaded', function() {
            console.log('📺 채널 관리 페이지 로드');

            // localStorage에서 선택된 채널 ID 확인
            currentChannelId = localStorage.getItem('currentChannelId');
            lastChannelChangeTimestamp = localStorage.getItem('channelChangedTimestamp');

            console.log('초기 채널 ID:', currentChannelId);
            console.log('초기 타임스탬프:', lastChannelChangeTimestamp);

            if (currentChannelId) {
                console.log('✅ 선택된 채널 ID:', currentChannelId);
                loadSubscriptions();
            } else {
                console.log('⚠️ 선택된 채널이 없습니다.');
                showEmptyState();
            }

            // ✅ 전역 함수로 등록 (z_menu.php에서 직접 호출 가능)
            window.loadSubscriptions = loadSubscriptions;

            // ✅ CustomEvent 리스너
            window.addEventListener('channelChanged', function(e) {
                console.log('🔔 CustomEvent 수신:', e.detail);
                const newChannelId = e.detail.channelId;

                if (newChannelId && newChannelId !== currentChannelId) {
                    console.log('채널 변경 감지:', currentChannelId, '->', newChannelId);
                    currentChannelId = newChannelId;
                    lastChannelChangeTimestamp = e.detail.timestamp;
                    loadSubscriptions();
                }
            });

            // ✅ Storage 이벤트 리스너
            window.addEventListener('storage', function(e) {
                console.log('Storage 이벤트:', e.key, e.newValue);

                if (e.key === 'currentChannelId' && e.newValue) {
                    if (e.newValue !== currentChannelId) {
                        console.log('Storage 이벤트 - 채널 변경:', e.newValue);
                        currentChannelId = e.newValue;
                        loadSubscriptions();
                    }
                }

                if (e.key === 'channelChangedTimestamp' && e.newValue) {
                    const newChannelId = localStorage.getItem('currentChannelId');
                    if (e.newValue !== lastChannelChangeTimestamp && newChannelId) {
                        console.log('타임스탬프 변경 감지');
                        lastChannelChangeTimestamp = e.newValue;
                        currentChannelId = newChannelId;
                        loadSubscriptions();
                    }
                }
            });

            // ✅ 폴링 (0.3초마다 - 더 빠르게)
            setInterval(function() {
                const newTimestamp = localStorage.getItem('channelChangedTimestamp');
                const newChannelId = localStorage.getItem('currentChannelId');

                if (newTimestamp !== lastChannelChangeTimestamp) {
                    console.log('폴링 - 타임스탬프 변경 감지:', lastChannelChangeTimestamp, '->', newTimestamp);

                    lastChannelChangeTimestamp = newTimestamp;

                    if (newChannelId && newChannelId !== currentChannelId) {
                        console.log('폴링 - 채널 변경:', currentChannelId, '->', newChannelId);
                        currentChannelId = newChannelId;
                        loadSubscriptions();
                    } else if (!currentChannelId && newChannelId) {
                        console.log('폴링 - 새 채널 설정:', newChannelId);
                        currentChannelId = newChannelId;
                        loadSubscriptions();
                    }
                }
            }, 300); // 0.3초
        });

        // ==========================================
        // 빈 상태 표시
        // ==========================================
        function showEmptyState() {
            const subscriptionList = document.getElementById('subscriptionList');
            const subscriptionCount = document.getElementById('subscriptionCount');
            const detailContainer = document.getElementById('detailContainer');

            if (subscriptionList) {
                subscriptionList.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-state-icon">📺</div>
                        <div>연결된 채널을 선택하면<br>구독 리스트가 표시됩니다.</div>
                    </div>
                `;
            }

            if (subscriptionCount) {
                subscriptionCount.textContent = '0';
            }

            if (detailContainer) {
                detailContainer.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-state-icon">🎬</div>
                        <div>구독 채널을 선택하면<br>상세 정보가 표시됩니다.</div>
                    </div>
                `;
            }

            // 버튼 숨기기
            const toggleBtn = document.getElementById('toggleViewBtn');
            if (toggleBtn) {
                toggleBtn.style.display = 'none';
            }

            // 타이틀 초기화
            const contentTitle = document.getElementById('contentTitle');
            if (contentTitle) {
                contentTitle.textContent = '📋 채널 정보';
            }

            isLoading = false;
        }

        // ==========================================
        // 🔥 구독 리스트 로드 (개선된 버전)
        // ==========================================
        async function loadSubscriptions() {
            // 중복 로딩 방지
            if (isLoading) {
                console.log('⚠️ 이미 로딩 중...');
                return;
            }

            console.log('📺 구독 리스트 로드 시작... (채널 ID:', currentChannelId, ')');

            if (!currentChannelId) {
                console.log('⚠️ 선택된 채널이 없습니다.');
                showEmptyState();
                return;
            }

            isLoading = true;

            // 채널 정보 가져오기
            const channels = JSON.parse(localStorage.getItem('connectedChannels') || '[]');
            const channel = channels.find(ch => ch.id === currentChannelId);

            console.log('전체 채널 목록:', channels);
            console.log('선택된 채널:', channel);

            if (!channel) {
                console.log('⚠️ 채널 정보를 찾을 수 없습니다.');
                showError('채널 정보를 찾을 수 없습니다.');
                isLoading = false;
                return;
            }

            // 로딩 표시
            showLoading();

            try {
                console.log('🔄 구독 리스트 로드 중...');

                // ✅ 구독 리스트 가져오기 (자동 토큰 갱신 포함)
                const subscriptions = await fetchAllSubscriptions(currentChannelId);

                console.log(`✅ 구독 채널 ${subscriptions.length}개 로드 완료`);

                currentSubscriptions = subscriptions;

                // 구독 리스트 렌더링
                renderSubscriptionList(subscriptions);

            } catch (error) {
                console.error('❌ 구독 리스트 로드 실패:', error);

                // 인증 오류인 경우 특별 메시지
                if (error.message.includes('인증') || error.message.includes('Token') || error.message.includes('invalid_grant')) {
                    showError(
                        '인증이 만료되었습니다.<br><br>' +
                        '해결 방법:<br>' +
                        '1. 왼쪽 메뉴에서 "🔄 토큰 갱신" 버튼 클릭<br>' +
                        '2. 또는 이 채널을 삭제하고 다시 연결'
                    );
                } else {
                    showError('구독 리스트를 불러오는데 실패했습니다: ' + error.message);
                }
            } finally {
                isLoading = false;
            }
        }

        // ==========================================
        // 로딩 표시
        // ==========================================
        function showLoading() {
            const subscriptionList = document.getElementById('subscriptionList');

            if (subscriptionList) {
                subscriptionList.innerHTML = `
                    <div class="loading">
                        <div class="loading-spinner"></div>
                        <div>구독 리스트를 불러오는 중...</div>
                    </div>
                `;
            }
        }

        // ==========================================
        // 에러 표시
        // ==========================================
        function showError(message) {
            const subscriptionList = document.getElementById('subscriptionList');
            const subscriptionCount = document.getElementById('subscriptionCount');

            if (subscriptionList) {
                subscriptionList.innerHTML = `
                    <div class="error-message">
                        <div style="font-size: 2rem; margin-bottom: 1rem;">❌</div>
                        <div>${message}</div>
                    </div>
                `;
            }

            if (subscriptionCount) {
                subscriptionCount.textContent = '0';
            }
        }

        // ==========================================
        // 🔥 YouTube API로 구독 리스트 가져오기 (개선됨)
        // ==========================================
        async function fetchAllSubscriptions(channelId) {
            console.log('📡 [API] 구독 리스트 가져오기 시작...');

            let allSubscriptions = [];
            let nextPageToken = null;

            do {
                const url = new URL('https://www.googleapis.com/youtube/v3/subscriptions');
                url.searchParams.append('part', 'snippet');
                url.searchParams.append('mine', 'true');
                url.searchParams.append('maxResults', '50');

                if (nextPageToken) {
                    url.searchParams.append('pageToken', nextPageToken);
                }

                // ✅ z_menu.php의 makeYouTubeAPICall 사용 (자동 토큰 갱신 포함)
                const response = await window.makeYouTubeAPICall(url.toString(), channelId);

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.error?.message || 'API 요청 실패');
                }

                const data = await response.json();

                if (data.items) {
                    allSubscriptions = allSubscriptions.concat(data.items);
                }

                nextPageToken = data.nextPageToken;

                console.log(`📄 페이지 로드: ${data.items?.length || 0}개, 총: ${allSubscriptions.length}개`);

            } while (nextPageToken);

            console.log('✅ [API] 구독 리스트 로드 완료:', allSubscriptions.length);
            return allSubscriptions;
        }

        // ==========================================
        // 🔥 채널 통계 정보 가져오기 (개선됨)
        // ==========================================
        async function fetchChannelStatistics(targetChannelId, authChannelId) {
            console.log('📊 채널 통계 로드 시작:', targetChannelId);

            try {
                const url = new URL('https://www.googleapis.com/youtube/v3/channels');
                url.searchParams.append('part', 'statistics,snippet');
                url.searchParams.append('id', targetChannelId);

                // ✅ z_menu.php의 makeYouTubeAPICall 사용
                const response = await window.makeYouTubeAPICall(url.toString(), authChannelId);

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.error?.message || 'API 요청 실패');
                }

                const data = await response.json();

                if (!data.items || data.items.length === 0) {
                    throw new Error('채널 정보를 찾을 수 없습니다.');
                }

                const channel = data.items[0];
                const statistics = channel.statistics || {};
                const snippet = channel.snippet || {};

                console.log('✅ 채널 통계 로드 성공:', statistics);

                // 채널 가입일 포맷팅
                let publishedAt = null;
                if (snippet.publishedAt) {
                    publishedAt = new Date(snippet.publishedAt).toLocaleDateString('ko-KR', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                }

                return {
                    subscriberCount: statistics.subscriberCount || '비공개',
                    videoCount: statistics.videoCount || '0',
                    viewCount: statistics.viewCount || '0',
                    publishedAt: publishedAt
                };

            } catch (error) {
                console.error('❌ 채널 통계 로드 실패:', error);
                throw error;
            }
        }

        // ==========================================
        // 즐겨찾기 토글
        // ==========================================
        function toggleFavorite(channelId, event) {
            // 이벤트 버블링 방지 (구독 아이템 클릭 방지)
            event.stopPropagation();

            // 즐겨찾기 상태 토글
            if (favoriteChannels[channelId]) {
                delete favoriteChannels[channelId];
            } else {
                favoriteChannels[channelId] = true;
            }

            // localStorage에 저장
            localStorage.setItem('favoriteChannels', JSON.stringify(favoriteChannels));

            // 리스트 다시 렌더링
            renderSubscriptionList(currentSubscriptions);
        }

        // ==========================================
        // 구독 리스트 렌더링
        // ==========================================
        function renderSubscriptionList(subscriptions) {
            const subscriptionList = document.getElementById('subscriptionList');
            const subscriptionCount = document.getElementById('subscriptionCount');

            if (!subscriptionList) return;

            // 카운트 업데이트
            if (subscriptionCount) {
                subscriptionCount.textContent = subscriptions.length;
            }

            // 구독 없음
            if (subscriptions.length === 0) {
                subscriptionList.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-state-icon">📺</div>
                        <div>구독 중인 채널이 없습니다.</div>
                    </div>
                `;
                return;
            }

            // ✅ 즐겨찾기 기준으로 정렬
            const sortedSubscriptions = [...subscriptions].sort((a, b) => {
                const aChannelId = a.snippet.resourceId.channelId;
                const bChannelId = b.snippet.resourceId.channelId;

                const aFavorite = favoriteChannels[aChannelId] ? 1 : 0;
                const bFavorite = favoriteChannels[bChannelId] ? 1 : 0;

                // 즐겨찾기가 있는 것을 우선
                if (aFavorite !== bFavorite) {
                    return bFavorite - aFavorite;
                }

                // 같은 그룹 내에서는 원래 순서 유지
                return 0;
            });


            // 구독 리스트 렌더링
            let html = '';
            let lastWasFavorite = false;

            sortedSubscriptions.forEach((subscription, index) => {
                const snippet = subscription.snippet;
                const channelId = snippet.resourceId.channelId;
                const title = snippet.title;
                const description = snippet.description || '설명 없음';
                const thumbnail = snippet.thumbnails?.default?.url || snippet.thumbnails?.medium?.url || '';
                const isFavorite = favoriteChannels[channelId] || false;

                // ✅ 즐겨찾기와 일반 구독 사이에 구분선 추가
                if (index > 0 && lastWasFavorite && !isFavorite) {
                    html += `
                        <div style="height: 1px; background: linear-gradient(90deg, transparent, #ddd, transparent); margin: 1rem 0;"></div>
                    `;
                }

                lastWasFavorite = isFavorite;

                // 원래 인덱스 찾기 (선택 시 필요)
                const originalIndex = subscriptions.findIndex(s =>
                    s.snippet.resourceId.channelId === channelId
                );

                html += `
                    <div class="subscription-item" onclick="selectSubscription(${originalIndex})" data-channel-id="${channelId}">
                        <button class="favorite-btn" onclick="toggleFavorite('${channelId}', event)" title="${isFavorite ? '즐겨찾기 해제' : '즐겨찾기 추가'}">
                            ${isFavorite ? '⭐' : '☆'}
                        </button>
                        ${thumbnail ? `<img src="${thumbnail}" alt="${escapeHtml(title)}" class="subscription-thumbnail">` : '<div class="subscription-thumbnail" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>'}
                        <div class="subscription-info">
                            <div class="subscription-name">${escapeHtml(title)}</div>
                            <div class="subscription-desc">${escapeHtml(description)}</div>
                        </div>
                    </div>
                `;
            });

            subscriptionList.innerHTML = html;

            // ✅ 선택된 구독이 있으면 활성화 표시 복원
            if (selectedSubscription) {
                const selectedChannelId = selectedSubscription.snippet.resourceId.channelId;
                const selectedItem = subscriptionList.querySelector(`[data-channel-id="${selectedChannelId}"]`);
                if (selectedItem) {
                    selectedItem.classList.add('active');
                }
            }
        }

        // ==========================================
        // 구독 채널 선택
        // ==========================================
        function selectSubscription(index) {
            if (index < 0 || index >= currentSubscriptions.length) return;

            selectedSubscription = currentSubscriptions[index];

            // 활성화 표시
            const items = document.querySelectorAll('.subscription-item');
            items.forEach((item, i) => {
                if (i === index) {
                    item.classList.add('active');
                } else {
                    item.classList.remove('active');
                }
            });

            // 뷰 초기화 (채널 정보로)
            currentView = 'channel';
            const contentTitle = document.getElementById('contentTitle');
            const toggleBtn = document.getElementById('toggleViewBtn');

            if (contentTitle) contentTitle.textContent = '📋 채널 정보';
            if (toggleBtn) {
                toggleBtn.textContent = '🎬 영상 목록 보기';
                toggleBtn.style.display = 'block'; // 버튼 표시
            }

            // 채널 정보 표시
            showSubscriptionDetail(selectedSubscription);
        }


        let currentView = 'channel'; // 'channel' 또는 'videos'

        // ==========================================
        // 뷰 전환 (채널 정보 ↔ 영상 목록)
        // ==========================================
        function toggleView() {
            const contentTitle = document.getElementById('contentTitle');
            const toggleBtn = document.getElementById('toggleViewBtn');

            if (currentView === 'channel') {
                // 영상 목록으로 전환
                currentView = 'videos';
                contentTitle.textContent = '🎬 영상 목록';
                toggleBtn.textContent = '📋 채널 정보 보기';
                loadVideos();
            } else {
                // 채널 정보로 전환
                currentView = 'channel';
                contentTitle.textContent = '📋 채널 정보';
                toggleBtn.textContent = '🎬 영상 목록 보기';
                if (selectedSubscription) {
                    showSubscriptionDetail(selectedSubscription);
                }
            }
        }

        // ==========================================
        // 🔥 구독 채널 상세 정보 표시 (개선됨)
        // ==========================================
        async function showSubscriptionDetail(subscription) {
            const detailContainer = document.getElementById('detailContainer');

            if (!detailContainer) return;

            const snippet = subscription.snippet;
            const channelId = snippet.resourceId.channelId;
            const title = snippet.title;
            const description = snippet.description || '설명 없음';
            const thumbnail = snippet.thumbnails?.high?.url || snippet.thumbnails?.medium?.url || '';
            const publishedAt = new Date(snippet.publishedAt).toLocaleDateString('ko-KR', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            // 로딩 상태 표시
            detailContainer.innerHTML = `
                <div class="loading">
                    <div class="loading-spinner"></div>
                    <div>채널 정보를 불러오는 중...</div>
                </div>
            `;

            try {
                // ✅ 채널 통계 정보 가져오기 (자동 토큰 갱신 포함)
                const channelStats = await fetchChannelStatistics(channelId, currentChannelId);

                // 숫자 포맷팅 함수
                const formatNumber = (num) => {
                    if (!num) return '0';
                    return parseInt(num).toLocaleString('ko-KR');
                };

                detailContainer.innerHTML = `
                    <div style="max-width: 800px; margin: 0 auto;">
                        ${thumbnail ? `<img src="${thumbnail}" alt="${escapeHtml(title)}" class="detail-thumbnail">` : ''}

                        <h2 class="detail-title">${escapeHtml(title)}</h2>

                        <div class="detail-box copy-box">
                            <div class="detail-box-title">📺 채널 ID</div>
                            <div class="detail-box-content code">${channelId}</div>
                            <button class="copy-btn" id="copyChannelId" onclick="copyToClipboard('${channelId}', 'copyChannelId')">📋 복사</button>
                        </div>

                        <div class="detail-box">
                            <div class="detail-box-title">📝 채널 설명</div>
                            <div class="detail-box-content">${escapeHtml(description)}</div>
                        </div>

                        <div class="detail-box">
                            <div class="detail-box-title">📅 내 구독일</div>
                            <div class="detail-box-content">${publishedAt}</div>
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                            ${channelStats.publishedAt ? `
                            <div class="detail-box" style="text-align: center;">
                                <div class="detail-box-title">🎂 채널 가입일</div>
                                <div class="detail-box-content" style="font-size: 0.85rem; font-weight: 600; color: #333;">
                                    ${channelStats.publishedAt}
                                </div>
                            </div>
                            ` : ''}

                            <div class="detail-box" style="text-align: center;">
                                <div class="detail-box-title">👥 구독자 수</div>
                                <div class="detail-box-content" style="font-size: 1.3rem; font-weight: 700; color: #667eea;">
                                    ${formatNumber(channelStats.subscriberCount)}명
                                </div>
                            </div>

                            <div class="detail-box" style="text-align: center;">
                                <div class="detail-box-title">🎬 동영상 수</div>
                                <div class="detail-box-content" style="font-size: 1.3rem; font-weight: 700; color: #764ba2;">
                                    ${formatNumber(channelStats.videoCount)}개
                                </div>
                            </div>

                            <div class="detail-box" style="text-align: center;">
                                <div class="detail-box-title">👁️ 총 조회수</div>
                                <div class="detail-box-content" style="font-size: 1.3rem; font-weight: 700; color: #f093fb;">
                                    ${formatNumber(channelStats.viewCount)}회
                                </div>
                            </div>
                        </div>
                    </div>
                `;

            } catch (error) {
                console.error('❌ 채널 통계 로드 실패:', error);

                // 기본 정보만 표시
                detailContainer.innerHTML = `
                    <div style="max-width: 800px; margin: 0 auto;">
                        ${thumbnail ? `<img src="${thumbnail}" alt="${escapeHtml(title)}" class="detail-thumbnail">` : ''}

                        <h2 class="detail-title">${escapeHtml(title)}</h2>

                        <div class="detail-box copy-box">
                            <div class="detail-box-title">📺 채널 ID</div>
                            <div class="detail-box-content code">${channelId}</div>
                            <button class="copy-btn" id="copyChannelId" onclick="copyToClipboard('${channelId}', 'copyChannelId')">📋 복사</button>
                        </div>

                        <div class="detail-box">
                            <div class="detail-box-title">📝 채널 설명</div>
                            <div class="detail-box-content">${escapeHtml(description)}</div>
                        </div>

                        <div class="detail-box">
                            <div class="detail-box-title">📅 구독일</div>
                            <div class="detail-box-content">${publishedAt}</div>
                        </div>

                        <div class="error-message" style="padding: 1rem; margin-bottom: 1rem;">
                            ⚠️ 채널 통계를 불러올 수 없습니다.
                        </div>

                        <button class="action-btn" onclick="openChannelPage('${channelId}')">
                            🔗 YouTube 채널 페이지 열기
                        </button>
                    </div>
                `;
            }
        }


        // ==========================================
        // 🔥 영상 목록 로드 (개선됨)
        // ==========================================
        async function loadVideos() {
            if (!selectedSubscription) {
                alert('채널을 먼저 선택해주세요.');
                return;
            }

            const detailContainer = document.getElementById('detailContainer');
            const channelId = selectedSubscription.snippet.resourceId.channelId;

            // 로딩 표시
            detailContainer.innerHTML = `
                <div class="loading">
                    <div class="loading-spinner"></div>
                    <div>영상 목록을 불러오는 중...</div>
                </div>
            `;

            try {
                // ✅ 채널의 업로드 플레이리스트 ID 가져오기
                const channelUrl = new URL('https://www.googleapis.com/youtube/v3/channels');
                channelUrl.searchParams.append('part', 'contentDetails');
                channelUrl.searchParams.append('id', channelId);

                const channelResponse = await window.makeYouTubeAPICall(channelUrl.toString(), currentChannelId);

                if (!channelResponse.ok) {
                    throw new Error('채널 정보를 가져올 수 없습니다.');
                }

                const channelData = await channelResponse.json();
                const uploadsPlaylistId = channelData.items[0]?.contentDetails?.relatedPlaylists?.uploads;

                if (!uploadsPlaylistId) {
                    throw new Error('업로드된 영상이 없습니다.');
                }

                // ✅ 영상 목록 가져오기 (최신 50개)
                const videosUrl = new URL('https://www.googleapis.com/youtube/v3/playlistItems');
                videosUrl.searchParams.append('part', 'snippet');
                videosUrl.searchParams.append('playlistId', uploadsPlaylistId);
                videosUrl.searchParams.append('maxResults', '50');

                const videosResponse = await window.makeYouTubeAPICall(videosUrl.toString(), currentChannelId);

                if (!videosResponse.ok) {
                    throw new Error('영상 목록을 가져올 수 없습니다.');
                }

                const videosData = await videosResponse.json();

                // ✅ 영상 ID 추출
                const videoIds = videosData.items.map(item => item.snippet.resourceId.videoId).join(',');

                // ✅ 영상 통계 정보 가져오기 (조회수, 좋아요 등)
                const statsUrl = new URL('https://www.googleapis.com/youtube/v3/videos');
                statsUrl.searchParams.append('part', 'statistics');
                statsUrl.searchParams.append('id', videoIds);

                const statsResponse = await window.makeYouTubeAPICall(statsUrl.toString(), currentChannelId);

                if (!statsResponse.ok) {
                    throw new Error('영상 통계를 가져올 수 없습니다.');
                }

                const statsData = await statsResponse.json();

                // ✅ 통계 정보를 영상 데이터와 매핑
                const videosWithStats = videosData.items.map(video => {
                    const videoId = video.snippet.resourceId.videoId;
                    const stats = statsData.items.find(s => s.id === videoId);

                    return {
                        ...video,
                        statistics: stats?.statistics || {}
                    };
                });

                // 영상 목록 렌더링
                renderVideoList(videosWithStats);

            } catch (error) {
                console.error('❌ 영상 목록 로드 실패:', error);
                detailContainer.innerHTML = `
                    <div class="error-message">
                        <div style="font-size: 2rem; margin-bottom: 1rem;">❌</div>
                        <div>${error.message}</div>
                    </div>
                `;
            }
        }

        // ==========================================
        // 영상 목록 렌더링
        // ==========================================
        function renderVideoList(videos) {
            const detailContainer = document.getElementById('detailContainer');

            if (!videos || videos.length === 0) {
                detailContainer.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-state-icon">🎬</div>
                        <div>업로드된 영상이 없습니다.</div>
                    </div>
                `;
                return;
            }

            let html = '<div style="max-width: 1000px; margin: 0 auto;">';
            let previousDate = null; // 이전 영상의 날짜 저장

            videos.forEach((video, index) => {
                const snippet = video.snippet;
                const statistics = video.statistics || {};
                const videoId = snippet.resourceId.videoId;
                const title = snippet.title;
                const description = snippet.description || '설명 없음';
                const thumbnail = snippet.thumbnails?.medium?.url || '';

                // 날짜 객체 생성
                const currentDate = new Date(snippet.publishedAt);
                const publishedAt = currentDate.toLocaleDateString('ko-KR', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                // ✅ 조회수 및 시간당 조회수 계산
                const viewCount = parseInt(statistics.viewCount || 0);
                const now = new Date();
                const hoursElapsed = Math.max(1, (now - currentDate) / (1000 * 60 * 60)); // 최소 1시간
                const viewsPerHour = Math.round(viewCount / hoursElapsed);

                // 조회수 포맷팅
                const formatNumber = (num) => {
                    if (num >= 1000000) {
                        return (num / 1000000).toFixed(1) + 'M';
                    } else if (num >= 1000) {
                        return (num / 1000).toFixed(1) + 'K';
                    }
                    return num.toLocaleString('ko-KR');
                };

                // 날짜 차이 계산
                let dateDiffText = '';
                if (index > 0 && previousDate) {
                    const diffMs = previousDate - currentDate;
                    const diffDays = Math.round(diffMs / (1000 * 60 * 60 * 24));

                    if (diffDays > 0) {
                        if (diffDays === 1) {
                            dateDiffText = ' <span style="color: #667eea; font-weight: 600;">(1일 후)</span>';
                        } else if (diffDays < 30) {
                            dateDiffText = ` <span style="color: #667eea; font-weight: 600;">(${diffDays}일 후)</span>`;
                        } else if (diffDays < 365) {
                            const months = Math.floor(diffDays / 30);
                            const remainDays = diffDays % 30;
                            if (remainDays === 0) {
                                dateDiffText = ` <span style="color: #764ba2; font-weight: 600;">(${months}개월 후)</span>`;
                            } else {
                                dateDiffText = ` <span style="color: #764ba2; font-weight: 600;">(${months}개월 ${remainDays}일 후)</span>`;
                            }
                        } else {
                            const years = Math.floor(diffDays / 365);
                            const remainDays = diffDays % 365;
                            if (remainDays === 0) {
                                dateDiffText = ` <span style="color: #f093fb; font-weight: 600;">(${years}년 후)</span>`;
                            } else {
                                const months = Math.floor(remainDays / 30);
                                dateDiffText = ` <span style="color: #f093fb; font-weight: 600;">(${years}년 ${months}개월 후)</span>`;
                            }
                        }
                    } else if (diffDays === 0) {
                        dateDiffText = ' <span style="color: #10b981; font-weight: 600;">(같은 날)</span>';
                    }
                }

                previousDate = currentDate;

                html += `
                    <div style="display: flex; gap: 1rem; padding: 1rem; background: #f8f9fa; border-radius: 8px; margin-bottom: 1rem; cursor: pointer; transition: all 0.2s;"
                        onmouseover="this.style.background='#e9ecef'; this.style.transform='translateY(-2px)'"
                        onmouseout="this.style.background='#f8f9fa'; this.style.transform='translateY(0)'"
                        onclick="window.open('https://www.youtube.com/watch?v=${videoId}', '_blank')">
                        <img src="${thumbnail}" alt="${escapeHtml(title)}"
                            style="width: 200px; height: 112px; object-fit: cover; border-radius: 8px; flex-shrink: 0;">
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-weight: 600; font-size: 1rem; margin-bottom: 0.5rem; color: #333;">
                                ${escapeHtml(title)}
                            </div>
                            <div style="font-size: 0.85rem; color: #666; margin-bottom: 0.5rem; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                ${escapeHtml(description.substring(0, 150))}${description.length > 150 ? '...' : ''}
                            </div>
                            <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                                <div style="font-size: 0.8rem; color: #999;">
                                    📅 ${publishedAt}${dateDiffText}
                                </div>
                                <div style="font-size: 0.8rem; color: #333; font-weight: 600;">
                                    👁️ ${formatNumber(viewCount)}회
                                </div>
                                <div style="font-size: 0.8rem; color: #667eea; font-weight: 600; background: rgba(102, 126, 234, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px;">
                                    VPH⚡ ${formatNumber(viewsPerHour)}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            html += '</div>';

            detailContainer.innerHTML = html;
        }


        // ==========================================
        // 채널 페이지 열기
        // ==========================================
        function openChannelPage(channelId) {
            const url = `https://www.youtube.com/channel/${channelId}`;
            window.open(url, '_blank');
        }

        // ==========================================
        // HTML 이스케이프
        // ==========================================
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // ==========================================
        // 클립보드 복사
        // ==========================================
        function copyToClipboard(text, buttonId) {
            const button = document.getElementById(buttonId);

            // 클립보드 API 사용
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(() => {
                    // 성공 피드백
                    const originalText = button.textContent;
                    button.textContent = '✓ 복사됨!';
                    button.classList.add('copied');

                    setTimeout(() => {
                        button.textContent = originalText;
                        button.classList.remove('copied');
                    }, 2000);
                }).catch(err => {
                    console.error('클립보드 복사 실패:', err);
                    fallbackCopy(text, button);
                });
            } else {
                // 폴백: 구형 브라우저 지원
                fallbackCopy(text, button);
            }
        }

        // 폴백 복사 방법
        function fallbackCopy(text, button) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();

            try {
                document.execCommand('copy');
                const originalText = button.textContent;
                button.textContent = '✓ 복사됨!';
                button.classList.add('copied');

                setTimeout(() => {
                    button.textContent = originalText;
                    button.classList.remove('copied');
                }, 2000);
            } catch (err) {
                console.error('폴백 복사 실패:', err);
                alert('복사에 실패했습니다. 직접 선택해서 복사해주세요.');
            } finally {
                document.body.removeChild(textarea);
            }
        }

        // ==========================================
        // 사이드바 토글
        // ==========================================
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.classList.toggle('hidden');
            }
        }

        // ==========================================
        // 새로고침 (호환성)
        // ==========================================
        function refreshComments() {
            isLoading = false; // 로딩 플래그 초기화
            loadSubscriptions();
        }
    </script>
</body>
</html>
