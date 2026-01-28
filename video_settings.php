<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ONETOP - 영상 설정</title>

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
            overflow-y: auto;
            padding: 2rem;
        }

        /* 섹션 카드 */
        .section-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-card-header {
            padding: 1.5rem 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .section-card-header h3 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .section-card-header p {
            font-size: 0.9rem;
            opacity: 0.85;
        }

        .section-card-body {
            padding: 2rem;
        }

        .section-card-footer {
            padding: 1.5rem 2rem;
            border-top: 1px solid #eee;
            background: #fafbfc;
        }

        /* 폼 그룹 */
        .form-group {
            margin-bottom: 2rem;
        }

        .form-label {
            display: block;
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
        }

        /* 옵션 그리드 */
        .options-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .options-grid.style-grid {
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }

        /* 옵션 카드 */
        .option-card {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 1.25rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .option-card:hover {
            border-color: #667eea;
            background: #f0f2ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }

        .option-card.selected {
            border-color: #667eea;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }

        .option-icon {
            margin-bottom: 0.5rem;
        }

        .option-icon .icon-svg {
            width: 32px;
            height: 32px;
        }

        .option-title {
            font-weight: 600;
            font-size: 0.95rem;
            color: #333;
            margin-bottom: 0.25rem;
        }

        .option-description {
            font-size: 0.8rem;
            color: #888;
        }

        /* 스타일 카드 */
        .style-card {
            height: 180px;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            position: relative;
            overflow: hidden;
            padding: 0;
        }

        .style-card img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .style-card .option-title {
            position: relative;
            z-index: 1;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
            color: white;
            padding: 2rem 0.75rem 0.75rem;
            font-size: 0.85rem;
            margin: 0;
        }

        /* 사용자 정의 타일 (초기 상태: 점선 테두리) */
        .style-card--custom {
            border: 2px dashed #ccc;
            background: #fafbfc;
        }

        .style-card--custom:hover {
            border-color: #667eea;
            background: #f0f2ff;
        }

        .custom-style-center {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .custom-style-icon {
            font-size: 2.5rem;
            color: #667eea;
            line-height: 1;
        }

        /* 사용자 등록된 스타일 삭제 버튼 */
        .user-style-delete {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: rgba(239, 68, 68, 0.9);
            border: none;
            color: white;
            font-size: 1rem;
            line-height: 1;
            cursor: pointer;
            z-index: 10;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .option-card:hover .user-style-delete {
            display: flex;
        }

        .user-style-delete:hover {
            background: #dc2626;
            transform: scale(1.1);
        }

        /* 버튼 */
        .btn {
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .button-group {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }

        /* 스크롤바 */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #888; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #555; }
    </style>
</head>
<body>
    <!-- 사이드바 Include -->
    <?php include 'z_menu.php'; ?>

    <!-- 메인 컨텐츠 -->
    <div class="main-content">
        <div class="page-header">
            <button class="menu-toggle" onclick="toggleSidebar()">☰</button>
            <h1 class="page-title" style="flex: 1; text-align: center;">🎬 영상 설정</h1>
            <div style="width: 60px;"></div>
        </div>

        <div class="content-container">
            <!-- 1단계: 설정 -->
            <div class="step-content active" data-step="1">
                <div class="section-card">
                    <!-- 상단: 타이틀 -->
                    <div class="section-card-header">
                        <h3>영상 설정</h3>
                        <p>스타일과 비율을 선택하고 시작하세요.</p>
                    </div>

                    <!-- 중단: 옵션 선택 -->
                    <div class="section-card-body">
                        <div class="form-group">
                            <label class="form-label">영상 비율 선택</label>
                            <br>
                            <div class="options-grid" id="ratioOptions">
                                <div class="option-card" onclick="selectRatio('16:9', this)">
                                    <div class="option-icon"><img src="img/icon_feather/monitor.svg" alt="" class="icon-svg"></div>
                                    <div class="option-title">16:9</div>
                                    <div class="option-description">유튜브,데스크톱</div>
                                </div>
                                <div class="option-card" onclick="selectRatio('1:1', this)">
                                    <div class="option-icon"><img src="img/icon_feather/square.svg" alt="" class="icon-svg"></div>
                                    <div class="option-title">1:1</div>
                                    <div class="option-description">인스타그램,소셜</div>
                                </div>
                                <div class="option-card" onclick="selectRatio('9:16', this)">
                                    <div class="option-icon"><img src="img/icon_feather/smartphone.svg" alt="" class="icon-svg"></div>
                                    <div class="option-title">9:16</div>
                                    <div class="option-description">틱톡,릴스,쇼츠</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">스타일 선택</label>

                            <div class="options-grid style-grid" id="styleOptions">
                                <div class="option-card style-card" onclick="selectStyle('style01', this)">
                                    <img src="/img/icons/samsple_style/1. Cute Stick Figure.png" alt="">
                                    <div class="option-title">귀여운 졸라맨</div>
                                </div>

                                <div class="option-card style-card"
                                    onclick="selectStyle('style02', this)"
                                    style="background-image: url('/img/icons/samsple_style/2. Japanese Anime Style.png');">
                                    <div class="option-title">일본 애니메이션</div>
                                </div>

                                <div class="option-card style-card"
                                    onclick="selectStyle('style03', this)"
                                    style="background-image: url('/img/icons/samsple_style/3. Cartoon Explainer.png');">
                                    <div class="option-title">카툰 해설</div>
                                </div>

                                <div class="option-card style-card"
                                    onclick="selectStyle('style04', this)"
                                    style="background-image: url('/img/icons/samsple_style/4. 3D Animation.png');">
                                    <div class="option-title">3D 애니메이션</div>
                                </div>

                                <div class="option-card style-card"
                                    onclick="selectStyle('style05', this)"
                                    style="background-image: url('/img/icons/samsple_style/5. Real Photograph.png');">
                                    <div class="option-title">실제 사진</div>
                                </div>

                                <div class="option-card style-card"
                                    onclick="selectStyle('style06', this)"
                                    style="background-image: url('/img/icons/samsple_style/6. Movie Still Cut.png');">
                                    <div class="option-title">영화 스틸컷</div>
                                </div>

                                <div class="option-card style-card"
                                    onclick="selectStyle('style07', this)"
                                    style="background-image: url('/img/icons/samsple_style/7. Documentary.png');">
                                    <div class="option-title">다큐멘터리</div>
                                </div>

                                <div class="option-card style-card"
                                    onclick="selectStyle('style08', this)"
                                    style="background-image: url('/img/icons/samsple_style/8. Minimal Infographic.png');">
                                    <div class="option-title">미니멀 인포그래픽</div>
                                </div>

                                <div class="option-card style-card"
                                    onclick="selectStyle('style09', this)"
                                    style="background-image: url('/img/icons/samsple_style/9. Retro Pixel Art.png');">
                                    <div class="option-title">레트로 픽셀 아트</div>
                                </div>

                                <div class="option-card style-card"
                                    onclick="selectStyle('style10', this)"
                                    style="background-image: url('/img/icons/samsple_style/10. Korean Webtoon Style.png');">
                                    <div class="option-title">한국 웹툰</div>
                                </div>

                                <div class="option-card style-card"
                                    onclick="selectStyle('style11', this)"
                                    style="background-image: url('/img/icons/samsple_style/11. Pen Sketch.png');">
                                    <div class="option-title">펜 스케치</div>
                                </div>

                                <div class="option-card style-card"
                                    onclick="selectStyle('style12', this)"
                                    style="background-image: url('/img/icons/samsple_style/12. American Cartoon Style.png');">
                                    <div class="option-title">미국 카툰</div>
                                </div>

                                <div class="option-card style-card"
                                    onclick="selectStyle('style13', this)"
                                    style="background-image: url('/img/icons/samsple_style/13. Flat Illustration.png');">
                                    <div class="option-title">플랫 일러스트</div>
                                </div>

                                <div class="option-card style-card"
                                    onclick="selectStyle('style14', this)"
                                    style="background-image: url('/img/icons/samsple_style/14. Korean Folktale Style.png');">
                                    <div class="option-title">한국 야담</div>
                                </div>

                                <div class="option-card style-card"
                                    onclick="selectStyle('style15', this)"
                                    style="background-image: url('/img/icons/samsple_style/15. Clay Animation.png');">
                                    <div class="option-title">클레이 애니메이션</div>
                                </div>

                                <div class="option-card style-card"
                                    onclick="selectStyle('style16', this)"
                                    style="background-image: url('/img/icons/samsple_style/16. Vlog.png');">
                                    <div class="option-title">브이로그</div>
                                </div>

                                <!-- 사용자 정의 타일 (첨부 이미지 방식) -->
                                <div class="option-card style-card style-card--custom"
                                    id="customStyleTile"
                                    onclick="openCustomStylePicker()">
                                    <div class="custom-style-center">
                                        <span class="custom-style-icon">✦</span>
                                    </div>
                                    <div class="option-title">사용자 정의</div>
                                </div>

                                <!-- 숨김 파일 선택기 -->
                                <input id="customStyleFileInput"
                                    type="file"
                                    accept="image/jpg,image/jpeg,image/png,image/webp"
                                    style="display:none" />
                            </div>
                        </div>
                    </div>

                    <!-- 하단: 버튼 -->
                    <div class="section-card-footer">
                        <div class="button-group">
                            <button class="btn btn-primary" onclick="nextStep()">다음 단계 →</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ==========================================
        // 전역 변수
        // ==========================================
        const formData = {
            ratio: null,
            style: null
        };

        let isUploading = false;

        // ==========================================
        // 비율 선택
        // ==========================================
        function selectRatio(value, el) {
            const container = document.getElementById('ratioOptions');
            if (!container) return;

            container.querySelectorAll('.option-card.selected')
                .forEach(card => card.classList.remove('selected'));

            el.classList.add('selected');
            formData.ratio = value;
        }

        // ==========================================
        // 스타일 선택
        // ==========================================
        function selectStyle(value, el) {
            const container = document.getElementById('styleOptions');
            if (!container) return;

            container.querySelectorAll('.option-card.selected')
                .forEach(card => card.classList.remove('selected'));

            el.classList.add('selected');
            formData.style = value;
        }

        // ==========================================
        // 다음 단계
        // ==========================================
        function nextStep() {
            if (!formData.ratio) {
                alert('영상 비율을 선택해주세요.');
                return;
            }
            if (!formData.style) {
                alert('스타일을 선택해주세요.');
                return;
            }

            console.log('다음 단계로 이동:', formData);
            // TODO: 다음 단계 구현
        }

        // ==========================================
        // DB에서 사용자 스타일 목록 로드
        // ==========================================
        async function loadUserStyles() {
            const grid = document.getElementById('styleOptions');
            const uploadTile = document.getElementById('customStyleTile');
            if (!grid || !uploadTile) return;

            // 기존 사용자 스타일 타일 제거
            grid.querySelectorAll('.user-style-tile').forEach(el => el.remove());

            try {
                const res = await fetch('/upload/get_user_styles.php?userid=admin');
                if (!res.ok) return;

                const data = await res.json();
                if (!data.ok || !data.styles || data.styles.length === 0) return;

                // 업로드 타일 앞에 사용자 스타일 삽입
                data.styles.forEach(style => {
                    const tile = document.createElement('div');
                    tile.className = 'option-card style-card user-style-tile';
                    tile.style.backgroundImage = `url('${style.image_url}')`;
                    tile.onclick = function() { selectStyle(style.style_key, tile); };

                    tile.innerHTML =
                        `<button class="user-style-delete" onclick="event.stopPropagation(); deleteUserStyle('${style.style_key}', this.parentElement)">&times;</button>` +
                        `<div class="option-title">사용자 #${style.style_key.replace('custom_','')}</div>`;

                    grid.insertBefore(tile, uploadTile);
                });

            } catch (err) {
                console.error('사용자 스타일 로드 실패:', err);
            }
        }

        // ==========================================
        // 사용자 스타일 삭제
        // ==========================================
        async function deleteUserStyle(styleKey, tileEl) {
            if (!confirm('이 스타일을 삭제하시겠습니까?')) return;

            const userdno = styleKey.replace('custom_', '');
            try {
                const res = await fetch('/upload/delete_user_style.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `userid=admin&userdno=${userdno}`
                });
                const data = await res.json();
                if (!data.ok) throw new Error(data.error);

                // 타일 제거
                tileEl.remove();

                // 선택된 스타일이었으면 초기화
                if (formData.style === styleKey) {
                    formData.style = null;
                }
            } catch (err) {
                alert('삭제 실패: ' + err.message);
            }
        }

        // ==========================================
        // 사용자 정의 스타일 업로드
        // ==========================================

        // 타일 클릭 → 파일 선택
        function openCustomStylePicker() {
            const input = document.getElementById('customStyleFileInput');
            if (!input) return;
            input.value = '';
            input.click();
        }

        document.addEventListener('DOMContentLoaded', () => {
            // 페이지 로드 시 사용자 스타일 불러오기
            loadUserStyles();

            const input = document.getElementById('customStyleFileInput');
            if (!input) return;

            input.addEventListener('change', async (e) => {
                if (isUploading) return;
                isUploading = true;

                const file = e.target.files?.[0];
                if (!file) {
                    isUploading = false;
                    return;
                }

                // 클라이언트 검증
                const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    alert('JPG, PNG, WEBP 이미지 파일만 업로드 가능합니다.');
                    isUploading = false;
                    input.value = '';
                    return;
                }

                try {
                    const fd = new FormData();
                    fd.append('image', file);
                    fd.append('userid', 'admin');

                    const res = await fetch('/upload/upload_user_style.php', {
                        method: 'POST',
                        body: fd
                    });

                    if (!res.ok) {
                        throw new Error('서버 응답 오류 (HTTP ' + res.status + ')');
                    }

                    const data = await res.json();
                    if (!data.ok) throw new Error(data.error || '알 수 없는 오류');

                    // 업로드 성공 → 목록 새로고침 후 자동 선택
                    await loadUserStyles();

                    // 새로 추가된 타일 자동 선택
                    const grid = document.getElementById('styleOptions');
                    const tiles = grid.querySelectorAll('.user-style-tile');
                    const newTile = tiles[tiles.length - 1];
                    if (newTile) {
                        selectStyle(data.style_key, newTile);
                    }

                } catch (err) {
                    alert('업로드 실패: ' + err.message);
                } finally {
                    isUploading = false;
                    input.value = '';
                }
            });
        });

        // ==========================================
        // 사이드바 토글
        // ==========================================
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.classList.toggle('hidden');
            }
        }
    </script>
</body>
</html>
