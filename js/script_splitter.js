/**
 * 대본 분할 모듈
 * 스토리를 문맥에 맞춰 이미지 생성에 적합한 씬으로 분리
 */

const ScriptSplitter = {
    // 상태
    state: {
        isProcessing: false,
        scenes: [],
        currentScript: '',
        style: '',
        ratio: '',
        characterImage: null
    },

    /**
     * 대본 분할 실행
     */
    async splitScript() {
        const script = document.getElementById('scriptContent')?.value?.trim();
        const apiKey = this.getGeminiKey();

        // 유효성 검사
        if (!script) {
            alert('대본을 입력해주세요.');
            return null;
        }

        if (!apiKey) {
            alert('Gemini API 키가 필요합니다. API 설정에서 키를 입력해주세요.');
            return null;
        }

        if (script.length < 100) {
            alert('대본이 너무 짧습니다. 최소 100자 이상 입력해주세요.');
            return null;
        }

        // 선택된 스타일과 비율 가져오기
        const selectedStyle = document.querySelector('#styleOptions .option-card.selected');
        const selectedRatio = document.querySelector('#ratioOptions .option-card.selected');

        const style = selectedStyle?.querySelector('.option-title')?.textContent || '실사';
        const ratio = selectedRatio?.dataset?.value || '16:9';

        this.state.currentScript = script;
        this.state.style = style;
        this.state.ratio = ratio;

        // 로딩 표시
        this.showLoading(true);

        try {
            const response = await fetch('/api/split_script.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    script: script,
                    style: style,
                    ratio: ratio,
                    apiKey: apiKey
                })
            });

            const result = await response.json();

            if (!result.ok) {
                throw new Error(result.error || '대본 분할에 실패했습니다.');
            }

            this.state.scenes = result.data.scenes || [];

            // 결과 표시
            this.renderScenes(result.data);

            console.log('✅ 대본 분할 완료:', result.data);
            return result.data;

        } catch (error) {
            console.error('❌ 대본 분할 오류:', error);
            alert('대본 분할 중 오류가 발생했습니다.\n\n' + error.message);
            return null;
        } finally {
            this.showLoading(false);
        }
    },

    /**
     * Gemini API 키 가져오기
     */
    getGeminiKey() {
        // localStorage에서 가져오거나, 설정 페이지에서 입력받은 값 사용
        return localStorage.getItem('geminiApiKey') || '';
    },

    /**
     * 로딩 표시
     */
    showLoading(show) {
        this.state.isProcessing = show;

        let loader = document.getElementById('scriptSplitLoader');

        if (show) {
            if (!loader) {
                loader = document.createElement('div');
                loader.id = 'scriptSplitLoader';
                loader.innerHTML = `
                    <div class="split-loader-overlay">
                        <div class="split-loader-content">
                            <div class="split-loader-spinner"></div>
                            <p>대본을 분석하고 있습니다...</p>
                            <p class="split-loader-sub">AI가 씬을 분할하는 중입니다. 잠시만 기다려주세요.</p>
                        </div>
                    </div>
                `;
                document.body.appendChild(loader);
            }
            loader.style.display = 'flex';
        } else if (loader) {
            loader.style.display = 'none';
        }
    },

    /**
     * 씬 목록 렌더링
     */
    renderScenes(data) {
        const container = document.getElementById('scenesContainer');
        if (!container) {
            console.warn('scenesContainer 요소를 찾을 수 없습니다.');
            return;
        }

        const scenes = data.scenes || [];

        if (scenes.length === 0) {
            container.innerHTML = '<p class="no-scenes">분할된 씬이 없습니다.</p>';
            return;
        }

        let html = `
            <div class="scenes-header">
                <h4>📝 분할된 씬 목록</h4>
                <span class="scenes-count">총 ${scenes.length}개 씬 | 예상 시간: ${data.estimatedDuration || '계산 중'}</span>
            </div>
            <div class="scenes-list">
        `;

        scenes.forEach((scene, index) => {
            html += `
                <div class="scene-item" data-scene="${scene.sceneNumber}">
                    <div class="scene-header">
                        <span class="scene-number">씬 ${scene.sceneNumber}</span>
                        <span class="scene-mood">${scene.mood || ''}</span>
                        <span class="scene-duration">${scene.duration || ''}초</span>
                    </div>
                    <div class="scene-body">
                        <div class="scene-korean">
                            <label>원본 텍스트:</label>
                            <p>${this.escapeHtml(scene.koreanText)}</p>
                        </div>
                        <div class="scene-summary">
                            <label>요약:</label>
                            <p>${this.escapeHtml(scene.summary)}</p>
                        </div>
                        <div class="scene-prompt">
                            <label>이미지 프롬프트:</label>
                            <textarea class="scene-prompt-input"
                                      data-scene="${scene.sceneNumber}"
                                      rows="3">${this.escapeHtml(scene.imagePrompt)}</textarea>
                        </div>
                    </div>
                    <div class="scene-actions">
                        <button class="btn-scene-preview" onclick="ScriptSplitter.previewScene(${index})">
                            👁️ 미리보기
                        </button>
                        <button class="btn-scene-edit" onclick="ScriptSplitter.editScene(${index})">
                            ✏️ 수정
                        </button>
                    </div>
                </div>
            `;
        });

        html += '</div>';

        // 전체 이미지 생성 버튼
        html += `
            <div class="scenes-footer">
                <button class="btn btn-primary" onclick="ScriptSplitter.generateAllImages()">
                    🎨 전체 이미지 생성 시작
                </button>
            </div>
        `;

        container.innerHTML = html;
        container.style.display = 'block';
    },

    /**
     * HTML 이스케이프
     */
    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },

    /**
     * 씬 미리보기
     */
    previewScene(index) {
        const scene = this.state.scenes[index];
        if (!scene) return;

        alert(`씬 ${scene.sceneNumber} 미리보기\n\n${scene.summary}\n\n프롬프트:\n${scene.imagePrompt}`);
    },

    /**
     * 씬 수정
     */
    editScene(index) {
        const scene = this.state.scenes[index];
        if (!scene) return;

        const newPrompt = prompt('이미지 프롬프트 수정:', scene.imagePrompt);
        if (newPrompt !== null) {
            this.state.scenes[index].imagePrompt = newPrompt;

            // 입력 필드 업데이트
            const input = document.querySelector(`.scene-prompt-input[data-scene="${scene.sceneNumber}"]`);
            if (input) {
                input.value = newPrompt;
            }
        }
    },

    /**
     * 전체 이미지 생성
     */
    async generateAllImages() {
        if (this.state.scenes.length === 0) {
            alert('먼저 대본을 분할해주세요.');
            return;
        }

        // 수정된 프롬프트 반영
        document.querySelectorAll('.scene-prompt-input').forEach(input => {
            const sceneNum = parseInt(input.dataset.scene);
            const scene = this.state.scenes.find(s => s.sceneNumber === sceneNum);
            if (scene) {
                scene.imagePrompt = input.value;
            }
        });

        // 캐릭터 이미지 가져오기
        const characterPreview = document.getElementById('characterPreview');
        if (characterPreview && characterPreview.src) {
            this.state.characterImage = characterPreview.src;
        }

        console.log('🎨 이미지 생성 시작:', {
            scenes: this.state.scenes,
            style: this.state.style,
            ratio: this.state.ratio,
            hasCharacter: !!this.state.characterImage
        });

        // 다음 단계로 이동
        if (typeof nextStep === 'function') {
            nextStep();
        }

        // 이미지 생성 이벤트 발생
        window.dispatchEvent(new CustomEvent('startImageGeneration', {
            detail: {
                scenes: this.state.scenes,
                style: this.state.style,
                ratio: this.state.ratio,
                characterImage: this.state.characterImage
            }
        }));
    },

    /**
     * 현재 씬 데이터 가져오기
     */
    getScenes() {
        return this.state.scenes;
    },

    /**
     * 상태 초기화
     */
    reset() {
        this.state = {
            isProcessing: false,
            scenes: [],
            currentScript: '',
            style: '',
            ratio: '',
            characterImage: null
        };

        const container = document.getElementById('scenesContainer');
        if (container) {
            container.innerHTML = '';
            container.style.display = 'none';
        }
    }
};

// 전역 등록
window.ScriptSplitter = ScriptSplitter;
