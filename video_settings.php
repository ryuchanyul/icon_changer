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

        /* 사용자 등록 카드 */
        .custom-style-card {
            height: 180px;
            border: 2px dashed #ccc;
            background: #fafbfc;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: all 0.3s;
            border-radius: 12px;
            position: relative;
        }

        .custom-style-card:hover {
            border-color: #667eea;
            background: #f0f2ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }

        .custom-style-card .upload-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .custom-style-card .upload-text {
            font-size: 0.85rem;
            font-weight: 600;
            color: #667eea;
        }

        .custom-style-card .upload-hint {
            font-size: 0.75rem;
            color: #999;
        }

        /* 사용자 등록된 스타일 카드 */
        .user-style-card {
            position: relative;
        }

        .user-style-card .delete-style-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: rgba(239, 68, 68, 0.9);
            border: none;
            color: white;
            font-size: 0.85rem;
            cursor: pointer;
            z-index: 10;
            display: none;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .user-style-card:hover .delete-style-btn {
            display: flex;
        }

        .user-style-card .delete-style-btn:hover {
            background: rgba(220, 38, 38, 1);
            transform: scale(1.1);
        }

        .user-style-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 0.65rem;
            font-weight: 600;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            z-index: 10;
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

        /* 모달 */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 2000;
            animation: fadeIn 0.2s ease-in;
        }

        .modal-overlay.active {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            max-width: 480px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .modal-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #333;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.75rem;
            color: #999;
            cursor: pointer;
            padding: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .modal-close:hover {
            background-color: #f5f5f5;
            color: #333;
        }

        .modal-body {
            margin-bottom: 1.5rem;
        }

        .modal-body .preview-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 1rem;
            background: #f8f9fa;
        }

        .modal-body .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.3s;
        }

        .modal-body .form-input:focus {
            border-color: #667eea;
        }

        .modal-body .form-input-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #555;
            margin-bottom: 0.5rem;
        }

        .modal-footer {
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
        }

        .modal-footer .btn-cancel {
            padding: 0.6rem 1.5rem;
            background: #f0f0f0;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            color: #666;
            cursor: pointer;
            transition: all 0.2s;
        }

        .modal-footer .btn-cancel:hover {
            background: #e0e0e0;
        }

        .modal-footer .btn-confirm {
            padding: 0.6rem 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            color: white;
            cursor: pointer;
            transition: all 0.2s;
        }

        .modal-footer .btn-confirm:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .modal-footer .btn-confirm:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
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

                                <!-- 사용자 등록 스타일 (동적으로 추가됨) -->
                                <div id="userStyleSlot"></div>

                                <!-- 사용자 스타일 등록 버튼 -->
                                <div class="custom-style-card" onclick="openCustomStyleUpload()">
                                    <div class="upload-icon">+</div>
                                    <div class="upload-text">직접 등록</div>
                                    <div class="upload-hint">이미지를 업로드하세요</div>
                                </div>
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

    <!-- 숨겨진 파일 입력 -->
    <input type="file" id="customStyleFileInput" accept="image/*" style="display: none;" onchange="handleCustomStyleFile(event)">

    <!-- 사용자 스타일 등록 모달 -->
    <div class="modal-overlay" id="customStyleModal">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">스타일 직접 등록</h2>
                <button class="modal-close" onclick="closeCustomStyleModal()">&times;</button>
            </div>
            <div class="modal-body">
                <img id="customStylePreview" class="preview-image" src="" alt="미리보기" style="display: none;">
                <div id="customStylePlaceholder" style="width: 100%; height: 200px; background: #f8f9fa; border: 2px dashed #ddd; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #999; font-size: 0.9rem; margin-bottom: 1rem; cursor: pointer;" onclick="document.getElementById('customStyleFileInput').click();">
                    클릭하여 이미지를 선택하세요
                </div>
                <label class="form-input-label">스타일 이름</label>
                <input type="text" id="customStyleName" class="form-input" placeholder="예: 나만의 스타일" maxlength="20">
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="closeCustomStyleModal()">취소</button>
                <button class="btn-confirm" id="customStyleConfirmBtn" onclick="saveCustomStyle()" disabled>등록하기</button>
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

        let customStyleImageData = null;

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

            container.querySelectorAll('.option-card.selected, .user-style-card.selected')
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
        // 사용자 스타일 등록 기능
        // ==========================================

        // 사용자 스타일 업로드 시작
        function openCustomStyleUpload() {
            // 모달 열기
            document.getElementById('customStyleModal').classList.add('active');
            document.body.style.overflow = 'hidden';

            // 초기화
            customStyleImageData = null;
            document.getElementById('customStyleName').value = '';
            document.getElementById('customStylePreview').style.display = 'none';
            document.getElementById('customStylePlaceholder').style.display = 'flex';
            document.getElementById('customStyleConfirmBtn').disabled = true;
        }

        // 모달 닫기
        function closeCustomStyleModal() {
            document.getElementById('customStyleModal').classList.remove('active');
            document.body.style.overflow = 'auto';
            customStyleImageData = null;
        }

        // 파일 선택 처리
        function handleCustomStyleFile(event) {
            const file = event.target.files[0];
            if (!file) return;

            // 파일 크기 제한 (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('이미지 파일 크기는 5MB 이하만 가능합니다.');
                event.target.value = '';
                return;
            }

            // 이미지 파일 확인
            if (!file.type.startsWith('image/')) {
                alert('이미지 파일만 업로드 가능합니다.');
                event.target.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                customStyleImageData = e.target.result;

                // 미리보기 표시
                const preview = document.getElementById('customStylePreview');
                preview.src = customStyleImageData;
                preview.style.display = 'block';
                document.getElementById('customStylePlaceholder').style.display = 'none';

                // 모달이 열려있지 않으면 열기
                if (!document.getElementById('customStyleModal').classList.contains('active')) {
                    document.getElementById('customStyleModal').classList.add('active');
                    document.body.style.overflow = 'hidden';
                }

                updateConfirmButton();
            };
            reader.readAsDataURL(file);

            // 파일 입력 초기화 (같은 파일 재선택 가능)
            event.target.value = '';
        }

        // 등록 버튼 활성화 체크
        function updateConfirmButton() {
            const name = document.getElementById('customStyleName').value.trim();
            const btn = document.getElementById('customStyleConfirmBtn');
            btn.disabled = !(customStyleImageData && name.length > 0);
        }

        // 이름 입력 시 버튼 상태 업데이트
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('customStyleName').addEventListener('input', updateConfirmButton);
        });

        // 사용자 스타일 저장
        function saveCustomStyle() {
            const name = document.getElementById('customStyleName').value.trim();

            if (!customStyleImageData || !name) {
                alert('이미지와 스타일 이름을 입력해주세요.');
                return;
            }

            // localStorage에서 기존 사용자 스타일 가져오기
            const userStyles = JSON.parse(localStorage.getItem('userCustomStyles') || '[]');

            // 새 스타일 추가
            const newStyle = {
                id: 'custom_' + Date.now(),
                name: name,
                image: customStyleImageData,
                createdAt: new Date().toISOString()
            };

            userStyles.push(newStyle);
            localStorage.setItem('userCustomStyles', JSON.stringify(userStyles));

            // 모달 닫기
            closeCustomStyleModal();

            // 스타일 그리드 업데이트
            renderUserStyles();
        }

        // 사용자 스타일 삭제
        function deleteCustomStyle(styleId, event) {
            event.stopPropagation();

            if (!confirm('이 스타일을 삭제하시겠습니까?')) return;

            const userStyles = JSON.parse(localStorage.getItem('userCustomStyles') || '[]');
            const updated = userStyles.filter(s => s.id !== styleId);
            localStorage.setItem('userCustomStyles', JSON.stringify(updated));

            // 선택된 스타일이 삭제된 것이면 초기화
            if (formData.style === styleId) {
                formData.style = null;
            }

            renderUserStyles();
        }

        // 사용자 스타일 렌더링
        function renderUserStyles() {
            const slot = document.getElementById('userStyleSlot');
            if (!slot) return;

            const userStyles = JSON.parse(localStorage.getItem('userCustomStyles') || '[]');

            let html = '';
            userStyles.forEach(style => {
                html += `
                    <div class="option-card style-card user-style-card"
                        onclick="selectStyle('${style.id}', this)"
                        style="background-image: url('${style.image}');">
                        <span class="user-style-badge">직접 등록</span>
                        <button class="delete-style-btn" onclick="deleteCustomStyle('${style.id}', event)" title="삭제">&times;</button>
                        <div class="option-title">${escapeHtml(style.name)}</div>
                    </div>
                `;
            });

            slot.innerHTML = html;
        }

        // HTML 이스케이프
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
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
        // 페이지 로드 시 초기화
        // ==========================================
        window.addEventListener('DOMContentLoaded', function() {
            console.log('🎬 영상 설정 페이지 로드');
            renderUserStyles();
        });
    </script>
</body>
</html>
