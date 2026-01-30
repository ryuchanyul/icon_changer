# ============================================================
#  VMware VM 환경 자동 생성 스크립트 (Windows PowerShell)
#  각 VM마다 고유한 하드웨어 프로필을 자동 생성합니다.
# ============================================================

param(
    [Parameter(Position=0)]
    [int]$VmCount = 0,

    [Alias("d")]
    [string]$Dir = "$env:USERPROFILE\VMware_VMs",

    [Alias("i")]
    [string]$Iso = "",

    [Alias("s")]
    [int]$DiskSize = 80,

    [switch]$Help
)

$ErrorActionPreference = "Stop"
$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$TemplateFile = Join-Path $ScriptDir "template.vmx"
$ConfigDir = Join-Path $ScriptDir "configs"

# === 하드웨어 제조사 데이터베이스 ===
$Manufacturers = @(
    @{ Name="Dell Inc.";                  Board="Dell Inc.";              Model="Inspiron 5505";       Prefix="DELL" }
    @{ Name="LENOVO";                     Board="LENOVO";                 Model="ThinkPad T14 Gen 2";  Prefix="LNVO" }
    @{ Name="Hewlett-Packard";            Board="HP";                     Model="HP Pavilion 15";      Prefix="HP00" }
    @{ Name="ASUSTeK COMPUTER INC.";      Board="ASUSTeK";                Model="ASUS TUF Gaming";     Prefix="ASUS" }
    @{ Name="Acer";                       Board="Acer";                   Model="Aspire 5 A515";       Prefix="ACER" }
    @{ Name="MSI";                        Board="Micro-Star International"; Model="MSI Modern 15";     Prefix="MSI0" }
    @{ Name="Samsung Electronics";        Board="Samsung";                Model="Galaxy Book Pro";     Prefix="SMSN" }
    @{ Name="LG Electronics";             Board="LG";                     Model="LG Gram 16";         Prefix="LGEL" }
    @{ Name="TOSHIBA";                    Board="TOSHIBA";                Model="Satellite Pro C50";   Prefix="TSBA" }
    @{ Name="Gigabyte Technology";        Board="Gigabyte";               Model="AORUS 15P";           Prefix="GBTC" }
)

$NetAdapters = @("e1000e", "vmxnet3", "e1000")

$Resolutions = @(
    "1920x1080", "2560x1440", "1366x768", "1600x900",
    "1920x1200", "2560x1600", "3840x2160"
)

$Timezones = @(
    "Korea Standard Time",
    "Tokyo Standard Time",
    "China Standard Time",
    "Pacific Standard Time",
    "Eastern Standard Time",
    "Central Standard Time",
    "Mountain Standard Time",
    "GMT Standard Time",
    "Romance Standard Time",
    "W. Europe Standard Time"
)

$ComputerPrefixes = @(
    "DESKTOP", "WORKSTATION", "PC", "HOME", "OFFICE",
    "MYPC", "USER", "WIN", "LAPTOP", "MAIN"
)

# === 유틸리티 함수 ===

function Generate-VmwareUuid {
    $bytes = @()
    for ($i = 0; $i -lt 16; $i++) {
        $bytes += '{0:x2}' -f (Get-Random -Minimum 0 -Maximum 256)
    }
    $bytes[0] = "56"  # VMware convention
    $part1 = ($bytes[0..7]) -join " "
    $part2 = ($bytes[8..15]) -join " "
    return "$part1-$part2"
}

function Generate-MacAddress {
    $oui = if ((Get-Random -Minimum 0 -Maximum 2) -eq 0) { "00:0c:29" } else { "00:50:56" }
    $b1 = '{0:x2}' -f (Get-Random -Minimum 0 -Maximum 256)
    $b2 = '{0:x2}' -f (Get-Random -Minimum 0 -Maximum 256)
    $b3 = '{0:x2}' -f (Get-Random -Minimum 0 -Maximum 256)
    return "${oui}:${b1}:${b2}:${b3}"
}

function Generate-Serial {
    param([string]$Prefix, [int]$Length = 12)
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"
    $serial = $Prefix
    for ($i = $serial.Length; $i -lt $Length; $i++) {
        $serial += $chars[(Get-Random -Minimum 0 -Maximum $chars.Length)]
    }
    return $serial
}

function Generate-BoardId {
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"
    $id = "Mac-"
    for ($i = 0; $i -lt 16; $i++) {
        $id += $chars[(Get-Random -Minimum 0 -Maximum $chars.Length)]
    }
    return $id
}

function Generate-AssetTag {
    return "AT-{0:D4}-{1:D4}-{2:D4}" -f (Get-Random -Max 10000), (Get-Random -Max 10000), (Get-Random -Max 10000)
}

function Generate-ComputerName {
    $prefix = $ComputerPrefixes[(Get-Random -Minimum 0 -Maximum $ComputerPrefixes.Count)]
    $suffix = '{0:X4}' -f (Get-Random -Minimum 0 -Maximum 65536)
    return "${prefix}-${suffix}"
}

# === 메인 생성 함수 ===

function Generate-VmConfig {
    param(
        [int]$VmNumber,
        [string]$VmBaseDir,
        [string]$IsoPath,
        [int]$DiskSizeGb
    )

    # 제조사 랜덤 선택
    $mfg = $Manufacturers[(Get-Random -Minimum 0 -Maximum $Manufacturers.Count)]

    # VM 이름
    $vmName = "VM_ENV_{0:D3}" -f $VmNumber
    $vmDir = Join-Path $VmBaseDir $vmName
    $diskFile = "${vmName}.vmdk"
    $configFile = Join-Path $vmDir "${vmName}.vmx"

    # 고유 값 생성
    $uuidBios = Generate-VmwareUuid
    $uuidLocation = Generate-VmwareUuid
    $macAddress = Generate-MacAddress
    $serialNumber = Generate-Serial -Prefix $mfg.Prefix -Length 12
    $boardId = Generate-BoardId
    $assetTag = Generate-AssetTag
    $netAdapter = $NetAdapters[(Get-Random -Minimum 0 -Maximum $NetAdapters.Count)]
    $computerName = Generate-ComputerName
    $timezone = $Timezones[(Get-Random -Minimum 0 -Maximum $Timezones.Count)]
    $resolution = $Resolutions[(Get-Random -Minimum 0 -Maximum $Resolutions.Count)]

    # CPU/RAM 랜덤
    $cpuOptions = @(2, 4, 6, 8)
    $ramOptions = @(4096, 8192, 12288, 16384)
    $vramOptions = @(67108864, 134217728, 268435456)
    $cpuCores = $cpuOptions[(Get-Random -Minimum 0 -Maximum $cpuOptions.Count)]
    $coresPerSocket = [Math]::Max(1, [Math]::Floor($cpuCores / 2))
    $ramMb = $ramOptions[(Get-Random -Minimum 0 -Maximum $ramOptions.Count)]
    $vramSize = $vramOptions[(Get-Random -Minimum 0 -Maximum $vramOptions.Count)]

    # 디렉토리 생성
    if (-not (Test-Path $vmDir)) {
        New-Item -ItemType Directory -Path $vmDir -Force | Out-Null
    }

    # 템플릿 읽기 및 치환
    $content = Get-Content $TemplateFile -Raw -Encoding UTF8
    $content = $content -replace '\{\{VM_NAME\}\}', $vmName
    $content = $content -replace '\{\{CPU_CORES\}\}', $cpuCores
    $content = $content -replace '\{\{CORES_PER_SOCKET\}\}', $coresPerSocket
    $content = $content -replace '\{\{RAM_MB\}\}', $ramMb
    $content = $content -replace '\{\{UUID_BIOS\}\}', $uuidBios
    $content = $content -replace '\{\{UUID_LOCATION\}\}', $uuidLocation
    $content = $content -replace '\{\{SERIAL_NUMBER\}\}', $serialNumber
    $content = $content -replace '\{\{BOARD_ID\}\}', $boardId
    $content = $content -replace '\{\{HW_MODEL\}\}', $mfg.Model
    $content = $content -replace '\{\{ASSET_TAG\}\}', $assetTag
    $content = $content -replace '\{\{NET_ADAPTER\}\}', $netAdapter
    $content = $content -replace '\{\{MAC_ADDRESS\}\}', $macAddress
    $content = $content -replace '\{\{DISK_FILE\}\}', $diskFile
    $content = $content -replace '\{\{ISO_PATH\}\}', $IsoPath
    $content = $content -replace '\{\{VRAM_SIZE\}\}', $vramSize

    # VMX 파일 저장
    $content | Out-File -FilePath $configFile -Encoding UTF8 -NoNewline

    # 프로필 JSON 저장
    if (-not (Test-Path $ConfigDir)) {
        New-Item -ItemType Directory -Path $ConfigDir -Force | Out-Null
    }
    $profileFile = Join-Path $ConfigDir "${vmName}_profile.json"
    $profileData = @{
        vm_name    = $vmName
        vm_number  = $VmNumber
        vm_dir     = $vmDir
        vmx_file   = $configFile
        created_at = (Get-Date -Format "yyyy-MM-ddTHH:mm:ss")
        hardware   = @{
            manufacturer  = $mfg.Name
            model         = $mfg.Model
            board_id      = $boardId
            serial_number = $serialNumber
            asset_tag     = $assetTag
            uuid_bios     = $uuidBios
            uuid_location = $uuidLocation
        }
        specs      = @{
            cpu_cores    = $cpuCores
            ram_mb       = $ramMb
            disk_size_gb = $DiskSizeGb
            vram_bytes   = $vramSize
        }
        network    = @{
            adapter_type = $netAdapter
            mac_address  = $macAddress
        }
        os_settings = @{
            computer_name = $computerName
            timezone      = $timezone
            resolution    = $resolution
        }
    }
    $profileData | ConvertTo-Json -Depth 5 | Out-File -FilePath $profileFile -Encoding UTF8

    # 결과 출력
    Write-Host ""
    Write-Host "  [VM #$VmNumber] $vmName 생성 완료" -ForegroundColor Green
    Write-Host "    제조사:    $($mfg.Name) / $($mfg.Model)" -ForegroundColor Cyan
    Write-Host "    시리얼:    $serialNumber" -ForegroundColor Cyan
    Write-Host "    MAC:       $macAddress" -ForegroundColor Cyan
    Write-Host "    CPU/RAM:   ${cpuCores}코어 / ${ramMb}MB" -ForegroundColor Cyan
    Write-Host "    컴퓨터명:  $computerName" -ForegroundColor Cyan
    Write-Host "    시간대:    $timezone" -ForegroundColor Cyan
    Write-Host "    해상도:    $resolution" -ForegroundColor Cyan
    Write-Host "    경로:      $configFile" -ForegroundColor DarkGray
}

# === 사용법 출력 ===

function Show-Usage {
    Write-Host ""
    Write-Host "  ============================================================" -ForegroundColor Blue
    Write-Host "    VMware VM 환경 자동 생성기 (Windows)" -ForegroundColor Blue
    Write-Host "  ============================================================" -ForegroundColor Blue
    Write-Host ""
    Write-Host '  Usage:' -ForegroundColor Yellow
    Write-Host '    .\generate_vm.ps1 [VM Count] [Options]'
    Write-Host ''
    Write-Host '  Options:' -ForegroundColor Yellow
    Write-Host '    -Dir [Path]        VM save directory  (default: ~\VMware_VMs)'
    Write-Host '    -Iso [Path]        Windows ISO file path'
    Write-Host '    -DiskSize [GB]     Disk size          (default: 80GB)'
    Write-Host '    -Help              Show help'
    Write-Host ''
    Write-Host '  Examples:' -ForegroundColor Yellow
    Write-Host '    .\generate_vm.ps1 5'
    Write-Host '    .\generate_vm.ps1 3 -Dir "D:\MyVMs" -Iso "C:\ISO\Win10.iso"'
    Write-Host '    .\generate_vm.ps1 10 -DiskSize 120'
    Write-Host ""
}

# === 메인 실행 ===

if ($Help -or $VmCount -eq 0) {
    Show-Usage
    exit 0
}

if ($VmCount -lt 1 -or $VmCount -gt 50) {
    Write-Host "  [오류] VM 개수는 1~50 사이여야 합니다." -ForegroundColor Red
    exit 1
}

if (-not (Test-Path $TemplateFile)) {
    Write-Host "  [오류] 템플릿 파일을 찾을 수 없습니다: $TemplateFile" -ForegroundColor Red
    exit 1
}

if ([string]::IsNullOrEmpty($Iso)) {
    $Iso = "auto detect"
    Write-Host "  [알림] ISO 파일이 지정되지 않았습니다. VMware에서 직접 설정해주세요." -ForegroundColor Yellow
}

# 디렉토리 생성
if (-not (Test-Path $Dir)) { New-Item -ItemType Directory -Path $Dir -Force | Out-Null }
if (-not (Test-Path $ConfigDir)) { New-Item -ItemType Directory -Path $ConfigDir -Force | Out-Null }

Write-Host ""
Write-Host "  ============================================================" -ForegroundColor Blue
Write-Host "    VMware VM 환경 자동 생성" -ForegroundColor Blue
Write-Host "  ============================================================" -ForegroundColor Blue
Write-Host ""
Write-Host "    생성 개수:   ${VmCount}개" -ForegroundColor Cyan
Write-Host "    저장 경로:   $Dir" -ForegroundColor Cyan
Write-Host "    ISO 파일:    $Iso" -ForegroundColor Cyan
Write-Host "    디스크 크기: ${DiskSize}GB" -ForegroundColor Cyan
Write-Host ""
Write-Host "    VM 환경 생성을 시작합니다..." -ForegroundColor Yellow

# VM 생성 루프
for ($i = 1; $i -le $VmCount; $i++) {
    Generate-VmConfig -VmNumber $i -VmBaseDir $Dir -IsoPath $Iso -DiskSizeGb $DiskSize
}

Write-Host ""
Write-Host "  ============================================================" -ForegroundColor Blue
Write-Host "    총 ${VmCount}개의 VM 환경이 생성되었습니다!" -ForegroundColor Green
Write-Host "  ============================================================" -ForegroundColor Blue
Write-Host ""
Write-Host "  다음 단계:" -ForegroundColor Yellow
Write-Host "    1. VMware Workstation에서 각 .vmx 파일을 열어주세요"
Write-Host "    2. Windows 설치 후 scripts\setup_windows.ps1 을 실행하세요"
Write-Host "    3. scripts\manage_vms.ps1 로 VM을 관리할 수 있습니다"
Write-Host ""
Write-Host "  생성된 프로필 위치: $ConfigDir" -ForegroundColor Cyan
Write-Host ""
