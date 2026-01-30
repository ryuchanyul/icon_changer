#!/bin/bash
# ============================================================
#  VMware VM 환경 관리 스크립트
#  생성된 VM을 목록 조회, 시작, 중지, 삭제할 수 있습니다.
# ============================================================

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CONFIG_DIR="$(dirname "$SCRIPT_DIR")/configs"

# === 색상 ===
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
BOLD='\033[1m'
NC='\033[0m'

# === vmrun 경로 탐색 ===
find_vmrun() {
    local paths=(
        "/usr/bin/vmrun"
        "/usr/local/bin/vmrun"
        "/opt/vmware/bin/vmrun"
        "C:/Program Files (x86)/VMware/VMware Workstation/vmrun.exe"
        "C:/Program Files/VMware/VMware Workstation/vmrun.exe"
    )
    for p in "${paths[@]}"; do
        if command -v "$p" &>/dev/null || [ -f "$p" ]; then
            echo "$p"
            return
        fi
    done
    # PATH에서 탐색
    if command -v vmrun &>/dev/null; then
        echo "vmrun"
        return
    fi
    echo ""
}

VMRUN=$(find_vmrun)

# === 유틸리티 함수 ===
check_vmrun() {
    if [ -z "$VMRUN" ]; then
        echo -e "${RED}오류: vmrun을 찾을 수 없습니다.${NC}"
        echo "VMware Workstation이 설치되어 있는지 확인하세요."
        echo "또는 vmrun 경로를 PATH에 추가하세요."
        return 1
    fi
    return 0
}

# === VM 목록 조회 ===
list_vms() {
    echo -e "${BLUE}============================================================${NC}"
    echo -e "${BLUE}  등록된 VM 환경 목록${NC}"
    echo -e "${BLUE}============================================================${NC}"
    echo ""

    if [ ! -d "$CONFIG_DIR" ] || [ -z "$(ls -A "$CONFIG_DIR" 2>/dev/null)" ]; then
        echo -e "${YELLOW}등록된 VM이 없습니다.${NC}"
        echo "generate_vm.sh 를 실행하여 VM을 생성하세요."
        return
    fi

    local count=0
    printf "${BOLD}%-5s %-15s %-20s %-12s %-8s %-18s %-10s${NC}\n" \
        "#" "VM 이름" "제조사/모델" "시리얼" "CPU" "MAC 주소" "RAM"
    echo "-----  ---------------  --------------------  ------------  --------  ------------------  ----------"

    for profile in "$CONFIG_DIR"/*_profile.json; do
        [ -f "$profile" ] || continue
        count=$((count + 1))

        local vm_name=$(python3 -c "import json; d=json.load(open('$profile')); print(d['vm_name'])" 2>/dev/null || echo "N/A")
        local manufacturer=$(python3 -c "import json; d=json.load(open('$profile')); print(d['hardware']['manufacturer'][:18])" 2>/dev/null || echo "N/A")
        local serial=$(python3 -c "import json; d=json.load(open('$profile')); print(d['hardware']['serial_number'][:10])" 2>/dev/null || echo "N/A")
        local cpu=$(python3 -c "import json; d=json.load(open('$profile')); print(str(d['specs']['cpu_cores'])+'Core')" 2>/dev/null || echo "N/A")
        local mac=$(python3 -c "import json; d=json.load(open('$profile')); print(d['network']['mac_address'])" 2>/dev/null || echo "N/A")
        local ram=$(python3 -c "import json; d=json.load(open('$profile')); print(str(d['specs']['ram_mb'])+'MB')" 2>/dev/null || echo "N/A")

        # 실행 상태 확인
        local status="${RED}OFF${NC}"
        local vmx_path=$(python3 -c "import json; d=json.load(open('$profile')); print(d['vmx_file'])" 2>/dev/null || echo "")
        if [ -n "$VMRUN" ] && [ -n "$vmx_path" ]; then
            if "$VMRUN" list 2>/dev/null | grep -q "$vmx_path"; then
                status="${GREEN}ON${NC}"
            fi
        fi

        printf "%-5s %-15s %-20s %-12s %-8s %-18s %-10s [%b]\n" \
            "$count" "$vm_name" "$manufacturer" "$serial" "$cpu" "$mac" "$ram" "$status"
    done

    echo ""
    echo -e "${CYAN}총 ${count}개의 VM 환경이 등록되어 있습니다.${NC}"
    echo ""
}

# === VM 상세 정보 ===
show_vm_detail() {
    local vm_number=$1

    local profile=$(ls "$CONFIG_DIR"/*_profile.json 2>/dev/null | sed -n "${vm_number}p")
    if [ -z "$profile" ] || [ ! -f "$profile" ]; then
        echo -e "${RED}오류: VM #${vm_number} 을 찾을 수 없습니다.${NC}"
        return 1
    fi

    echo -e "${BLUE}============================================================${NC}"
    echo -e "${BLUE}  VM 상세 정보${NC}"
    echo -e "${BLUE}============================================================${NC}"

    python3 -c "
import json
d = json.load(open('$profile'))
print(f'''
  VM 이름:       {d['vm_name']}
  생성일:        {d['created_at']}
  VMX 경로:      {d['vmx_file']}

  [하드웨어]
  제조사:        {d['hardware']['manufacturer']}
  모델:          {d['hardware']['model']}
  Board ID:      {d['hardware']['board_id']}
  시리얼 번호:   {d['hardware']['serial_number']}
  Asset Tag:     {d['hardware']['asset_tag']}
  BIOS UUID:     {d['hardware']['uuid_bios']}
  Location UUID: {d['hardware']['uuid_location']}

  [스펙]
  CPU:           {d['specs']['cpu_cores']} 코어
  RAM:           {d['specs']['ram_mb']} MB
  디스크:        {d['specs']['disk_size_gb']} GB

  [네트워크]
  어댑터 타입:   {d['network']['adapter_type']}
  MAC 주소:      {d['network']['mac_address']}

  [OS 설정]
  컴퓨터 이름:   {d['os_settings']['computer_name']}
  시간대:        {d['os_settings']['timezone']}
  해상도:        {d['os_settings']['resolution']}
''')
" 2>/dev/null || echo -e "${RED}프로필 파싱 실패${NC}"
}

# === VM 시작 ===
start_vm() {
    check_vmrun || return 1
    local vm_number=$1
    local gui_mode=${2:-"gui"}  # gui 또는 nogui

    local profile=$(ls "$CONFIG_DIR"/*_profile.json 2>/dev/null | sed -n "${vm_number}p")
    if [ -z "$profile" ] || [ ! -f "$profile" ]; then
        echo -e "${RED}오류: VM #${vm_number} 을 찾을 수 없습니다.${NC}"
        return 1
    fi

    local vmx_path=$(python3 -c "import json; print(json.load(open('$profile'))['vmx_file'])" 2>/dev/null)
    local vm_name=$(python3 -c "import json; print(json.load(open('$profile'))['vm_name'])" 2>/dev/null)

    if [ ! -f "$vmx_path" ]; then
        echo -e "${RED}오류: VMX 파일을 찾을 수 없습니다: ${vmx_path}${NC}"
        return 1
    fi

    echo -e "${CYAN}VM 시작 중: ${vm_name}...${NC}"
    "$VMRUN" start "$vmx_path" "$gui_mode"
    echo -e "${GREEN}VM이 시작되었습니다: ${vm_name}${NC}"
}

# === VM 중지 ===
stop_vm() {
    check_vmrun || return 1
    local vm_number=$1
    local mode=${2:-"soft"}  # soft 또는 hard

    local profile=$(ls "$CONFIG_DIR"/*_profile.json 2>/dev/null | sed -n "${vm_number}p")
    if [ -z "$profile" ] || [ ! -f "$profile" ]; then
        echo -e "${RED}오류: VM #${vm_number} 을 찾을 수 없습니다.${NC}"
        return 1
    fi

    local vmx_path=$(python3 -c "import json; print(json.load(open('$profile'))['vmx_file'])" 2>/dev/null)
    local vm_name=$(python3 -c "import json; print(json.load(open('$profile'))['vm_name'])" 2>/dev/null)

    echo -e "${CYAN}VM 중지 중: ${vm_name}...${NC}"
    "$VMRUN" stop "$vmx_path" "$mode"
    echo -e "${GREEN}VM이 중지되었습니다: ${vm_name}${NC}"
}

# === 모든 VM 시작 ===
start_all() {
    check_vmrun || return 1
    echo -e "${CYAN}모든 VM을 시작합니다...${NC}"

    local count=0
    for profile in "$CONFIG_DIR"/*_profile.json; do
        [ -f "$profile" ] || continue
        count=$((count + 1))
        start_vm "$count" "gui"
        sleep 2  # VM 간 시작 간격
    done

    echo -e "${GREEN}총 ${count}개의 VM이 시작되었습니다.${NC}"
}

# === 모든 VM 중지 ===
stop_all() {
    check_vmrun || return 1
    echo -e "${CYAN}모든 VM을 중지합니다...${NC}"

    local count=0
    for profile in "$CONFIG_DIR"/*_profile.json; do
        [ -f "$profile" ] || continue
        count=$((count + 1))
        stop_vm "$count" "soft"
    done

    echo -e "${GREEN}총 ${count}개의 VM이 중지되었습니다.${NC}"
}

# === VM 삭제 ===
delete_vm() {
    local vm_number=$1

    local profile=$(ls "$CONFIG_DIR"/*_profile.json 2>/dev/null | sed -n "${vm_number}p")
    if [ -z "$profile" ] || [ ! -f "$profile" ]; then
        echo -e "${RED}오류: VM #${vm_number} 을 찾을 수 없습니다.${NC}"
        return 1
    fi

    local vm_name=$(python3 -c "import json; print(json.load(open('$profile'))['vm_name'])" 2>/dev/null)
    local vm_dir=$(python3 -c "import json; print(json.load(open('$profile'))['vm_dir'])" 2>/dev/null)
    local vmx_path=$(python3 -c "import json; print(json.load(open('$profile'))['vmx_file'])" 2>/dev/null)

    echo -e "${YELLOW}정말 삭제하시겠습니까? ${vm_name}${NC}"
    echo -e "  VM 디렉토리: ${vm_dir}"
    echo -e "  프로필 파일: ${profile}"
    read -p "삭제하려면 'yes'를 입력하세요: " confirm

    if [ "$confirm" != "yes" ]; then
        echo -e "${YELLOW}삭제가 취소되었습니다.${NC}"
        return
    fi

    # 실행 중이면 먼저 중지
    if [ -n "$VMRUN" ]; then
        if "$VMRUN" list 2>/dev/null | grep -q "$vmx_path"; then
            echo -e "${CYAN}실행 중인 VM을 중지합니다...${NC}"
            "$VMRUN" stop "$vmx_path" hard 2>/dev/null || true
            sleep 2
        fi
    fi

    # VMware에서 등록 해제
    if [ -n "$VMRUN" ]; then
        "$VMRUN" deleteVM "$vmx_path" 2>/dev/null || true
    fi

    # 파일 삭제
    if [ -d "$vm_dir" ]; then
        rm -rf "$vm_dir"
        echo -e "${GREEN}VM 디렉토리 삭제됨: ${vm_dir}${NC}"
    fi

    rm -f "$profile"
    echo -e "${GREEN}프로필 삭제됨: ${profile}${NC}"
    echo -e "${GREEN}VM #${vm_number} (${vm_name}) 가 삭제되었습니다.${NC}"
}

# === 스냅샷 생성 ===
snapshot_vm() {
    check_vmrun || return 1
    local vm_number=$1
    local snapshot_name=${2:-"snapshot_$(date +%Y%m%d_%H%M%S)"}

    local profile=$(ls "$CONFIG_DIR"/*_profile.json 2>/dev/null | sed -n "${vm_number}p")
    if [ -z "$profile" ] || [ ! -f "$profile" ]; then
        echo -e "${RED}오류: VM #${vm_number} 을 찾을 수 없습니다.${NC}"
        return 1
    fi

    local vmx_path=$(python3 -c "import json; print(json.load(open('$profile'))['vmx_file'])" 2>/dev/null)
    local vm_name=$(python3 -c "import json; print(json.load(open('$profile'))['vm_name'])" 2>/dev/null)

    echo -e "${CYAN}스냅샷 생성 중: ${vm_name} -> ${snapshot_name}${NC}"
    "$VMRUN" snapshot "$vmx_path" "$snapshot_name"
    echo -e "${GREEN}스냅샷이 생성되었습니다: ${snapshot_name}${NC}"
}

# === 환경 비교 ===
compare_vms() {
    echo -e "${BLUE}============================================================${NC}"
    echo -e "${BLUE}  VM 환경 비교표${NC}"
    echo -e "${BLUE}============================================================${NC}"
    echo ""

    printf "${BOLD}%-12s %-18s %-12s %-18s %-6s %-8s %-15s${NC}\n" \
        "VM 이름" "제조사" "시리얼" "MAC 주소" "CPU" "RAM" "컴퓨터명"
    echo "------------  ------------------  ------------  ------------------  ------  --------  ---------------"

    for profile in "$CONFIG_DIR"/*_profile.json; do
        [ -f "$profile" ] || continue

        python3 -c "
import json
d = json.load(open('$profile'))
print(f\"{d['vm_name']:<12s} {d['hardware']['manufacturer'][:16]:<18s} {d['hardware']['serial_number'][:10]:<12s} {d['network']['mac_address']:<18s} {str(d['specs']['cpu_cores'])+'C':<6s} {str(d['specs']['ram_mb'])+'M':<8s} {d['os_settings']['computer_name']:<15s}\")
" 2>/dev/null
    done

    echo ""
    echo -e "${CYAN}모든 VM이 서로 다른 하드웨어 프로필을 가지고 있는지 확인하세요.${NC}"
}

# === 사용법 ===
usage() {
    echo -e "${BLUE}============================================================${NC}"
    echo -e "${BLUE}  VMware VM 환경 관리 도구${NC}"
    echo -e "${BLUE}============================================================${NC}"
    echo ""
    echo -e "${YELLOW}사용법:${NC}"
    echo "  $0 <명령어> [옵션]"
    echo ""
    echo -e "${YELLOW}명령어:${NC}"
    echo "  list                    등록된 VM 목록 조회"
    echo "  detail <번호>           VM 상세 정보 조회"
    echo "  compare                 모든 VM 환경 비교표"
    echo "  start <번호>            VM 시작"
    echo "  stop <번호>             VM 중지"
    echo "  start-all               모든 VM 시작"
    echo "  stop-all                모든 VM 중지"
    echo "  snapshot <번호> [이름]  스냅샷 생성"
    echo "  delete <번호>           VM 삭제"
    echo "  help                    도움말"
    echo ""
    echo -e "${YELLOW}예시:${NC}"
    echo "  $0 list                # VM 목록 조회"
    echo "  $0 detail 1            # VM #1 상세 정보"
    echo "  $0 start 3             # VM #3 시작"
    echo "  $0 stop 3              # VM #3 중지"
    echo "  $0 compare             # 모든 VM 환경 비교"
    echo "  $0 snapshot 1 clean    # VM #1 스냅샷 'clean' 생성"
    echo "  $0 delete 2            # VM #2 삭제"
    echo ""
}

# === 메인 ===
case "${1:-help}" in
    list)       list_vms ;;
    detail)     show_vm_detail "$2" ;;
    compare)    compare_vms ;;
    start)      start_vm "$2" "gui" ;;
    stop)       stop_vm "$2" "soft" ;;
    start-all)  start_all ;;
    stop-all)   stop_all ;;
    snapshot)   snapshot_vm "$2" "$3" ;;
    delete)     delete_vm "$2" ;;
    help|*)     usage ;;
esac
