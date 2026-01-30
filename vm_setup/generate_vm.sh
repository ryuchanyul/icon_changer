#!/bin/bash
# ============================================================
#  VMware VM 환경 자동 생성 스크립트
#  각 VM마다 고유한 하드웨어 프로필을 자동 생성합니다.
# ============================================================

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
TEMPLATE_FILE="$SCRIPT_DIR/template.vmx"
CONFIG_DIR="$SCRIPT_DIR/configs"
DEFAULT_VM_BASE_DIR="$HOME/VMware_VMs"

# === 색상 출력 ===
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m'

# === 하드웨어 제조사 데이터베이스 ===
declare -a MANUFACTURERS=(
    "Dell Inc.|Dell Inc.|Inspiron 5505|DELL0001"
    "LENOVO|LENOVO|ThinkPad T14 Gen 2|LNVO0001"
    "Hewlett-Packard|HP|HP Pavilion 15|HP000001"
    "ASUSTeK COMPUTER INC.|ASUSTeK|ASUS TUF Gaming|ASUS0001"
    "Acer|Acer|Aspire 5 A515|ACER0001"
    "MSI|Micro-Star International|MSI Modern 15|MSI00001"
    "Samsung Electronics|Samsung|Galaxy Book Pro|SMSN0001"
    "LG Electronics|LG|LG Gram 16|LGELC001"
    "TOSHIBA|TOSHIBA|Satellite Pro C50|TSBA0001"
    "Gigabyte Technology|Gigabyte|AORUS 15P|GBTC0001"
)

declare -a NET_ADAPTERS=("e1000e" "vmxnet3" "e1000")

declare -a RESOLUTIONS=(
    "1920x1080" "2560x1440" "1366x768" "1600x900"
    "1920x1200" "2560x1600" "3840x2160"
)

declare -a TIMEZONES=(
    "Korea Standard Time"
    "Tokyo Standard Time"
    "China Standard Time"
    "Pacific Standard Time"
    "Eastern Standard Time"
    "Central Standard Time"
    "Mountain Standard Time"
    "GMT Standard Time"
    "Romance Standard Time"
    "W. Europe Standard Time"
)

declare -a COMPUTER_NAMES=(
    "DESKTOP" "WORKSTATION" "PC" "HOME" "OFFICE"
    "MYPC" "USER" "WIN" "LAPTOP" "MAIN"
)

# === 유틸리티 함수 ===

# 랜덤 UUID 생성
generate_uuid() {
    local p1=$(printf '%04x%04x' $((RANDOM)) $((RANDOM)))
    local p2=$(printf '%04x' $((RANDOM)))
    local p3=$(printf '%04x' $((RANDOM % 4096 + 16384)))  # version 4
    local p4=$(printf '%04x' $((RANDOM % 16384 + 32768)))  # variant 1
    local p5=$(printf '%04x%04x%04x' $((RANDOM)) $((RANDOM)) $((RANDOM)))
    echo "${p1}-${p2}-${p3}-${p4}-${p5}"
}

# VMware 형식 UUID 생성 (56 XX XX XX XX XX XX XX-XX XX XX XX XX XX XX XX)
generate_vmware_uuid() {
    local bytes=""
    for i in $(seq 1 16); do
        local byte=$(printf '%02x' $((RANDOM % 256)))
        if [ $i -le 8 ]; then
            bytes="${bytes}${byte} "
        elif [ $i -eq 9 ]; then
            bytes="${bytes}-${byte} "
        else
            bytes="${bytes}${byte} "
        fi
    done
    # 첫 바이트를 56으로 고정 (VMware convention)
    bytes="56 ${bytes:3}"
    echo "$bytes" | sed 's/ *$//'
}

# 랜덤 MAC 주소 생성 (VMware OUI: 00:0C:29 또는 00:50:56)
generate_mac() {
    local oui_choice=$((RANDOM % 2))
    if [ $oui_choice -eq 0 ]; then
        printf '00:0c:29:%02x:%02x:%02x' $((RANDOM%256)) $((RANDOM%256)) $((RANDOM%256))
    else
        printf '00:50:56:%02x:%02x:%02x' $((RANDOM%64)) $((RANDOM%256)) $((RANDOM%256))
    fi
}

# 랜덤 시리얼 번호 생성
generate_serial() {
    local prefix=$1
    local length=${2:-10}
    local chars="ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"
    local serial="${prefix}"
    for i in $(seq 1 $((length - ${#prefix}))); do
        serial="${serial}${chars:$((RANDOM % ${#chars})):1}"
    done
    echo "$serial"
}

# 랜덤 Board ID 생성
generate_board_id() {
    local chars="ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"
    local id="Mac-"
    for i in $(seq 1 16); do
        id="${id}${chars:$((RANDOM % ${#chars})):1}"
    done
    echo "$id"
}

# 랜덤 Asset Tag 생성
generate_asset_tag() {
    printf 'AT-%04d-%04d-%04d' $((RANDOM%10000)) $((RANDOM%10000)) $((RANDOM%10000))
}

# 랜덤 컴퓨터 이름 생성
generate_computer_name() {
    local prefix=${COMPUTER_NAMES[$((RANDOM % ${#COMPUTER_NAMES[@]}))]}
    local suffix=$(printf '%04X' $((RANDOM % 65536)))
    echo "${prefix}-${suffix}"
}

# VRAM 크기 랜덤 선택 (MB -> bytes)
random_vram() {
    local sizes=(67108864 134217728 268435456)  # 64MB, 128MB, 256MB
    echo "${sizes[$((RANDOM % ${#sizes[@]}))]}"
}

# === 메인 생성 함수 ===

generate_vm_config() {
    local vm_number=$1
    local vm_base_dir=$2
    local iso_path=$3
    local disk_size_gb=${4:-80}

    # 제조사 랜덤 선택
    local mfg_data=${MANUFACTURERS[$((RANDOM % ${#MANUFACTURERS[@]}))]}
    IFS='|' read -r mfg_name board_mfg hw_model board_prefix <<< "$mfg_data"

    # VM 이름 생성
    local vm_name="VM_ENV_$(printf '%03d' $vm_number)"
    local vm_dir="${vm_base_dir}/${vm_name}"
    local disk_file="${vm_name}.vmdk"
    local config_file="${vm_dir}/${vm_name}.vmx"

    # 고유 값 생성
    local uuid_bios=$(generate_vmware_uuid)
    local uuid_location=$(generate_vmware_uuid)
    local mac_address=$(generate_mac)
    local serial_number=$(generate_serial "${board_prefix:0:4}" 12)
    local board_id=$(generate_board_id)
    local asset_tag=$(generate_asset_tag)
    local net_adapter=${NET_ADAPTERS[$((RANDOM % ${#NET_ADAPTERS[@]}))]}
    local vram_size=$(random_vram)
    local computer_name=$(generate_computer_name)
    local timezone=${TIMEZONES[$((RANDOM % ${#TIMEZONES[@]}))]}
    local resolution=${RESOLUTIONS[$((RANDOM % ${#RESOLUTIONS[@]}))]}

    # CPU/RAM 랜덤 설정
    local cpu_options=(2 4 6 8)
    local ram_options=(4096 8192 12288 16384)
    local cpu_cores=${cpu_options[$((RANDOM % ${#cpu_options[@]}))]}
    local cores_per_socket=$((cpu_cores / 2))
    [ $cores_per_socket -lt 1 ] && cores_per_socket=1
    local ram_mb=${ram_options[$((RANDOM % ${#ram_options[@]}))]}

    # 디렉토리 생성
    mkdir -p "$vm_dir"

    # 템플릿 복사 및 값 치환
    cp "$TEMPLATE_FILE" "$config_file"

    sed -i "s|{{VM_NAME}}|${vm_name}|g" "$config_file"
    sed -i "s|{{CPU_CORES}}|${cpu_cores}|g" "$config_file"
    sed -i "s|{{CORES_PER_SOCKET}}|${cores_per_socket}|g" "$config_file"
    sed -i "s|{{RAM_MB}}|${ram_mb}|g" "$config_file"
    sed -i "s|{{UUID_BIOS}}|${uuid_bios}|g" "$config_file"
    sed -i "s|{{UUID_LOCATION}}|${uuid_location}|g" "$config_file"
    sed -i "s|{{SERIAL_NUMBER}}|${serial_number}|g" "$config_file"
    sed -i "s|{{BOARD_ID}}|${board_id}|g" "$config_file"
    sed -i "s|{{HW_MODEL}}|${hw_model}|g" "$config_file"
    sed -i "s|{{ASSET_TAG}}|${asset_tag}|g" "$config_file"
    sed -i "s|{{NET_ADAPTER}}|${net_adapter}|g" "$config_file"
    sed -i "s|{{MAC_ADDRESS}}|${mac_address}|g" "$config_file"
    sed -i "s|{{DISK_FILE}}|${disk_file}|g" "$config_file"
    sed -i "s|{{ISO_PATH}}|${iso_path}|g" "$config_file"
    sed -i "s|{{VRAM_SIZE}}|${vram_size}|g" "$config_file"

    # 환경 프로필 JSON 저장
    local profile_file="${CONFIG_DIR}/${vm_name}_profile.json"
    cat > "$profile_file" << PROFILE_EOF
{
    "vm_name": "${vm_name}",
    "vm_number": ${vm_number},
    "vm_dir": "${vm_dir}",
    "vmx_file": "${config_file}",
    "created_at": "$(date -Iseconds)",
    "hardware": {
        "manufacturer": "${mfg_name}",
        "model": "${hw_model}",
        "board_id": "${board_id}",
        "serial_number": "${serial_number}",
        "asset_tag": "${asset_tag}",
        "uuid_bios": "${uuid_bios}",
        "uuid_location": "${uuid_location}"
    },
    "specs": {
        "cpu_cores": ${cpu_cores},
        "ram_mb": ${ram_mb},
        "disk_size_gb": ${disk_size_gb},
        "vram_bytes": ${vram_size}
    },
    "network": {
        "adapter_type": "${net_adapter}",
        "mac_address": "${mac_address}"
    },
    "os_settings": {
        "computer_name": "${computer_name}",
        "timezone": "${timezone}",
        "resolution": "${resolution}"
    }
}
PROFILE_EOF

    # 결과 출력
    echo -e "${GREEN}[VM #${vm_number}] ${vm_name} 생성 완료${NC}"
    echo -e "  ${CYAN}제조사:${NC}    ${mfg_name} / ${hw_model}"
    echo -e "  ${CYAN}시리얼:${NC}    ${serial_number}"
    echo -e "  ${CYAN}MAC:${NC}       ${mac_address}"
    echo -e "  ${CYAN}CPU/RAM:${NC}   ${cpu_cores}코어 / ${ram_mb}MB"
    echo -e "  ${CYAN}컴퓨터명:${NC}  ${computer_name}"
    echo -e "  ${CYAN}시간대:${NC}    ${timezone}"
    echo -e "  ${CYAN}해상도:${NC}    ${resolution}"
    echo -e "  ${CYAN}경로:${NC}      ${config_file}"
    echo ""
}

# === 사용법 출력 ===
usage() {
    echo -e "${BLUE}============================================================${NC}"
    echo -e "${BLUE}  VMware VM 환경 자동 생성기${NC}"
    echo -e "${BLUE}============================================================${NC}"
    echo ""
    echo -e "${YELLOW}사용법:${NC}"
    echo "  $0 <VM개수> [옵션]"
    echo ""
    echo -e "${YELLOW}옵션:${NC}"
    echo "  -d, --dir <경로>       VM 저장 디렉토리 (기본: ~/VMware_VMs)"
    echo "  -i, --iso <경로>       Windows ISO 파일 경로"
    echo "  -s, --disk-size <GB>   디스크 크기 (기본: 80GB)"
    echo "  -h, --help             도움말 출력"
    echo ""
    echo -e "${YELLOW}예시:${NC}"
    echo "  $0 5                                    # VM 5개 생성"
    echo "  $0 3 -d /data/vms -i /iso/win10.iso     # 경로 지정"
    echo "  $0 10 -s 120                             # 120GB 디스크"
    echo ""
}

# === 메인 실행 ===
main() {
    local vm_count=""
    local vm_base_dir="$DEFAULT_VM_BASE_DIR"
    local iso_path=""
    local disk_size=80

    # 인자 파싱
    while [[ $# -gt 0 ]]; do
        case $1 in
            -d|--dir)
                vm_base_dir="$2"
                shift 2
                ;;
            -i|--iso)
                iso_path="$2"
                shift 2
                ;;
            -s|--disk-size)
                disk_size="$2"
                shift 2
                ;;
            -h|--help)
                usage
                exit 0
                ;;
            *)
                if [[ -z "$vm_count" && "$1" =~ ^[0-9]+$ ]]; then
                    vm_count=$1
                else
                    echo -e "${RED}오류: 알 수 없는 옵션 '$1'${NC}"
                    usage
                    exit 1
                fi
                shift
                ;;
        esac
    done

    # 필수 인자 확인
    if [[ -z "$vm_count" ]]; then
        usage
        exit 1
    fi

    if [[ $vm_count -lt 1 || $vm_count -gt 50 ]]; then
        echo -e "${RED}오류: VM 개수는 1~50 사이여야 합니다.${NC}"
        exit 1
    fi

    # ISO 경로 기본값
    if [[ -z "$iso_path" ]]; then
        iso_path="auto detect"
        echo -e "${YELLOW}[알림] ISO 파일이 지정되지 않았습니다. VMware에서 직접 설정해주세요.${NC}"
    fi

    # 템플릿 파일 확인
    if [[ ! -f "$TEMPLATE_FILE" ]]; then
        echo -e "${RED}오류: 템플릿 파일을 찾을 수 없습니다: ${TEMPLATE_FILE}${NC}"
        exit 1
    fi

    # 설정 디렉토리 생성
    mkdir -p "$CONFIG_DIR"
    mkdir -p "$vm_base_dir"

    echo -e "${BLUE}============================================================${NC}"
    echo -e "${BLUE}  VMware VM 환경 자동 생성${NC}"
    echo -e "${BLUE}============================================================${NC}"
    echo ""
    echo -e "${CYAN}생성 개수:${NC}   ${vm_count}개"
    echo -e "${CYAN}저장 경로:${NC}   ${vm_base_dir}"
    echo -e "${CYAN}ISO 파일:${NC}    ${iso_path}"
    echo -e "${CYAN}디스크 크기:${NC} ${disk_size}GB"
    echo ""
    echo -e "${YELLOW}VM 환경 생성을 시작합니다...${NC}"
    echo ""

    # VM 생성 루프
    for i in $(seq 1 $vm_count); do
        generate_vm_config "$i" "$vm_base_dir" "$iso_path" "$disk_size"
    done

    echo -e "${BLUE}============================================================${NC}"
    echo -e "${GREEN}  총 ${vm_count}개의 VM 환경이 생성되었습니다!${NC}"
    echo -e "${BLUE}============================================================${NC}"
    echo ""
    echo -e "${YELLOW}다음 단계:${NC}"
    echo "  1. VMware Workstation에서 각 .vmx 파일을 열어주세요"
    echo "  2. Windows 설치 후 vm_setup/scripts/setup_windows.ps1 을 실행하세요"
    echo "  3. vm_setup/scripts/manage_vms.sh 로 VM을 관리할 수 있습니다"
    echo ""
    echo -e "${CYAN}생성된 프로필 위치:${NC} ${CONFIG_DIR}/"
    echo ""
}

main "$@"
