# ============================================================
#  VMware VM 환경 관리 스크립트 (Windows PowerShell)
#  생성된 VM을 목록 조회, 시작, 중지, 삭제할 수 있습니다.
# ============================================================

param(
    [Parameter(Position=0)]
    [string]$Command = "help",

    [Parameter(Position=1)]
    [string]$Arg1 = "",

    [Parameter(Position=2)]
    [string]$Arg2 = ""
)

$ErrorActionPreference = "SilentlyContinue"
$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$ConfigDir = Join-Path (Split-Path -Parent $ScriptDir) "configs"

# === vmrun 경로 탐색 ===
function Find-VmRun {
    $paths = @(
        "${env:ProgramFiles}\VMware\VMware Workstation\vmrun.exe",
        "${env:ProgramFiles(x86)}\VMware\VMware Workstation\vmrun.exe",
        "${env:ProgramFiles}\VMware\VMware Player\vmrun.exe"
    )
    foreach ($p in $paths) {
        if (Test-Path $p) { return $p }
    }
    $inPath = Get-Command vmrun -ErrorAction SilentlyContinue
    if ($inPath) { return $inPath.Source }
    return $null
}

$VmRun = Find-VmRun

function Test-VmRun {
    if (-not $VmRun) {
        Write-Host "  [오류] vmrun.exe를 찾을 수 없습니다." -ForegroundColor Red
        Write-Host "  VMware Workstation이 설치되어 있는지 확인하세요." -ForegroundColor Yellow
        return $false
    }
    return $true
}

# === 프로필 파일 목록 가져오기 ===
function Get-Profiles {
    if (-not (Test-Path $ConfigDir)) { return @() }
    return @(Get-ChildItem -Path $ConfigDir -Filter "*_profile.json" | Sort-Object Name)
}

function Get-ProfileByNumber {
    param([int]$Number)
    $profiles = Get-Profiles
    if ($Number -lt 1 -or $Number -gt $profiles.Count) {
        Write-Host "  [오류] VM #$Number 을 찾을 수 없습니다. (총 $($profiles.Count)개)" -ForegroundColor Red
        return $null
    }
    return (Get-Content $profiles[$Number - 1].FullName -Raw | ConvertFrom-Json)
}

# === VM 목록 조회 ===
function Show-VmList {
    Write-Host ""
    Write-Host "  ============================================================" -ForegroundColor Blue
    Write-Host "    등록된 VM 환경 목록" -ForegroundColor Blue
    Write-Host "  ============================================================" -ForegroundColor Blue
    Write-Host ""

    $profiles = Get-Profiles
    if ($profiles.Count -eq 0) {
        Write-Host "  등록된 VM이 없습니다." -ForegroundColor Yellow
        Write-Host "  generate_vm.ps1 을 실행하여 VM을 먼저 생성하세요." -ForegroundColor Yellow
        return
    }

    # 헤더
    $header = "  {0,-4} {1,-14} {2,-22} {3,-14} {4,-8} {5,-18} {6,-8}" -f "#", "VM 이름", "제조사/모델", "시리얼", "CPU", "MAC 주소", "RAM"
    Write-Host $header -ForegroundColor White
    Write-Host "  ---- -------------- ---------------------- -------------- -------- ------------------ --------"

    $count = 0
    foreach ($file in $profiles) {
        $count++
        $d = Get-Content $file.FullName -Raw | ConvertFrom-Json
        $mfgShort = $d.hardware.manufacturer
        if ($mfgShort.Length -gt 20) { $mfgShort = $mfgShort.Substring(0, 20) }
        $serialShort = $d.hardware.serial_number
        if ($serialShort.Length -gt 12) { $serialShort = $serialShort.Substring(0, 12) }

        # 실행 상태 확인
        $status = "OFF"
        $statusColor = "Red"
        if ($VmRun -and (Test-Path $d.vmx_file -ErrorAction SilentlyContinue)) {
            $running = & $VmRun list 2>$null
            if ($running -and $running -match [regex]::Escape($d.vmx_file)) {
                $status = "ON"
                $statusColor = "Green"
            }
        }

        $line = "  {0,-4} {1,-14} {2,-22} {3,-14} {4,-8} {5,-18} {6,-8}" -f `
            $count, $d.vm_name, $mfgShort, $serialShort, "$($d.specs.cpu_cores)Core", $d.network.mac_address, "$($d.specs.ram_mb)MB"
        Write-Host $line -NoNewline
        Write-Host " [$status]" -ForegroundColor $statusColor
    }

    Write-Host ""
    Write-Host "  총 $count 개의 VM 환경이 등록되어 있습니다." -ForegroundColor Cyan
    Write-Host ""
}

# === VM 상세 정보 ===
function Show-VmDetail {
    param([int]$Number)
    $d = Get-ProfileByNumber -Number $Number
    if (-not $d) { return }

    Write-Host ""
    Write-Host "  ============================================================" -ForegroundColor Blue
    Write-Host "    VM 상세 정보" -ForegroundColor Blue
    Write-Host "  ============================================================" -ForegroundColor Blue
    Write-Host ""
    Write-Host "    VM 이름:       $($d.vm_name)"
    Write-Host "    생성일:        $($d.created_at)"
    Write-Host "    VMX 경로:      $($d.vmx_file)"
    Write-Host ""
    Write-Host "    [하드웨어]" -ForegroundColor Yellow
    Write-Host "    제조사:        $($d.hardware.manufacturer)"
    Write-Host "    모델:          $($d.hardware.model)"
    Write-Host "    Board ID:      $($d.hardware.board_id)"
    Write-Host "    시리얼 번호:   $($d.hardware.serial_number)"
    Write-Host "    Asset Tag:     $($d.hardware.asset_tag)"
    Write-Host "    BIOS UUID:     $($d.hardware.uuid_bios)"
    Write-Host "    Location UUID: $($d.hardware.uuid_location)"
    Write-Host ""
    Write-Host "    [스펙]" -ForegroundColor Yellow
    Write-Host "    CPU:           $($d.specs.cpu_cores) 코어"
    Write-Host "    RAM:           $($d.specs.ram_mb) MB"
    Write-Host "    디스크:        $($d.specs.disk_size_gb) GB"
    Write-Host ""
    Write-Host "    [네트워크]" -ForegroundColor Yellow
    Write-Host "    어댑터 타입:   $($d.network.adapter_type)"
    Write-Host "    MAC 주소:      $($d.network.mac_address)"
    Write-Host ""
    Write-Host "    [OS 설정]" -ForegroundColor Yellow
    Write-Host "    컴퓨터 이름:   $($d.os_settings.computer_name)"
    Write-Host "    시간대:        $($d.os_settings.timezone)"
    Write-Host "    해상도:        $($d.os_settings.resolution)"
    Write-Host ""
}

# === VM 환경 비교표 ===
function Show-VmCompare {
    Write-Host ""
    Write-Host "  ============================================================" -ForegroundColor Blue
    Write-Host "    VM 환경 비교표" -ForegroundColor Blue
    Write-Host "  ============================================================" -ForegroundColor Blue
    Write-Host ""

    $header = "  {0,-12} {1,-18} {2,-14} {3,-18} {4,-6} {5,-8} {6,-15}" -f `
        "VM 이름", "제조사", "시리얼", "MAC 주소", "CPU", "RAM", "컴퓨터명"
    Write-Host $header -ForegroundColor White
    Write-Host "  ------------ ------------------ -------------- ------------------ ------ -------- ---------------"

    $profiles = Get-Profiles
    foreach ($file in $profiles) {
        $d = Get-Content $file.FullName -Raw | ConvertFrom-Json
        $mfg = $d.hardware.manufacturer
        if ($mfg.Length -gt 16) { $mfg = $mfg.Substring(0, 16) }
        $serial = $d.hardware.serial_number
        if ($serial.Length -gt 12) { $serial = $serial.Substring(0, 12) }

        $line = "  {0,-12} {1,-18} {2,-14} {3,-18} {4,-6} {5,-8} {6,-15}" -f `
            $d.vm_name, $mfg, $serial, $d.network.mac_address, "$($d.specs.cpu_cores)C", "$($d.specs.ram_mb)M", $d.os_settings.computer_name
        Write-Host $line
    }

    Write-Host ""
    Write-Host "  모든 VM이 서로 다른 하드웨어 프로필을 가지고 있는지 확인하세요." -ForegroundColor Cyan
    Write-Host ""
}

# === VM 시작 ===
function Start-Vm {
    param([int]$Number)
    if (-not (Test-VmRun)) { return }
    $d = Get-ProfileByNumber -Number $Number
    if (-not $d) { return }

    if (-not (Test-Path $d.vmx_file)) {
        Write-Host "  [오류] VMX 파일을 찾을 수 없습니다: $($d.vmx_file)" -ForegroundColor Red
        return
    }

    Write-Host "  VM 시작 중: $($d.vm_name)..." -ForegroundColor Cyan
    & $VmRun start $d.vmx_file gui
    Write-Host "  VM이 시작되었습니다: $($d.vm_name)" -ForegroundColor Green
}

# === VM 중지 ===
function Stop-Vm {
    param([int]$Number)
    if (-not (Test-VmRun)) { return }
    $d = Get-ProfileByNumber -Number $Number
    if (-not $d) { return }

    Write-Host "  VM 중지 중: $($d.vm_name)..." -ForegroundColor Cyan
    & $VmRun stop $d.vmx_file soft
    Write-Host "  VM이 중지되었습니다: $($d.vm_name)" -ForegroundColor Green
}

# === 모든 VM 시작 ===
function Start-AllVms {
    if (-not (Test-VmRun)) { return }
    Write-Host "  모든 VM을 시작합니다..." -ForegroundColor Cyan
    $profiles = Get-Profiles
    $count = 0
    foreach ($file in $profiles) {
        $count++
        Start-Vm -Number $count
        Start-Sleep -Seconds 2
    }
    Write-Host "  총 $count 개의 VM이 시작되었습니다." -ForegroundColor Green
}

# === 모든 VM 중지 ===
function Stop-AllVms {
    if (-not (Test-VmRun)) { return }
    Write-Host "  모든 VM을 중지합니다..." -ForegroundColor Cyan
    $profiles = Get-Profiles
    $count = 0
    foreach ($file in $profiles) {
        $count++
        Stop-Vm -Number $count
    }
    Write-Host "  총 $count 개의 VM이 중지되었습니다." -ForegroundColor Green
}

# === 스냅샷 생성 ===
function New-VmSnapshot {
    param([int]$Number, [string]$Name)
    if (-not (Test-VmRun)) { return }
    $d = Get-ProfileByNumber -Number $Number
    if (-not $d) { return }
    if ([string]::IsNullOrEmpty($Name)) {
        $Name = "snapshot_$(Get-Date -Format 'yyyyMMdd_HHmmss')"
    }
    Write-Host "  스냅샷 생성 중: $($d.vm_name) -> $Name" -ForegroundColor Cyan
    & $VmRun snapshot $d.vmx_file $Name
    Write-Host "  스냅샷이 생성되었습니다: $Name" -ForegroundColor Green
}

# === VM 삭제 ===
function Remove-Vm {
    param([int]$Number)
    $d = Get-ProfileByNumber -Number $Number
    if (-not $d) { return }

    Write-Host ""
    Write-Host "  정말 삭제하시겠습니까? $($d.vm_name)" -ForegroundColor Yellow
    Write-Host "    VM 디렉토리: $($d.vm_dir)"
    $confirm = Read-Host "  삭제하려면 'yes'를 입력하세요"

    if ($confirm -ne "yes") {
        Write-Host "  삭제가 취소되었습니다." -ForegroundColor Yellow
        return
    }

    # 실행 중이면 중지
    if ($VmRun -and (Test-Path $d.vmx_file)) {
        $running = & $VmRun list 2>$null
        if ($running -and $running -match [regex]::Escape($d.vmx_file)) {
            Write-Host "  실행 중인 VM을 중지합니다..." -ForegroundColor Cyan
            & $VmRun stop $d.vmx_file hard 2>$null
            Start-Sleep -Seconds 2
        }
        & $VmRun deleteVM $d.vmx_file 2>$null
    }

    # 파일 삭제
    if (Test-Path $d.vm_dir) {
        Remove-Item -Path $d.vm_dir -Recurse -Force
        Write-Host "  VM 디렉토리 삭제됨: $($d.vm_dir)" -ForegroundColor Green
    }

    $profiles = Get-Profiles
    $profileFile = $profiles[$Number - 1].FullName
    if (Test-Path $profileFile) {
        Remove-Item -Path $profileFile -Force
        Write-Host "  프로필 삭제됨" -ForegroundColor Green
    }

    Write-Host "  VM #$Number ($($d.vm_name)) 가 삭제되었습니다." -ForegroundColor Green
}

# === 사용법 ===
function Show-Usage {
    Write-Host ""
    Write-Host "  ============================================================" -ForegroundColor Blue
    Write-Host "    VMware VM 환경 관리 도구 (Windows)" -ForegroundColor Blue
    Write-Host "  ============================================================" -ForegroundColor Blue
    Write-Host ""
    Write-Host "  사용법:" -ForegroundColor Yellow
    Write-Host '    .\manage_vms.ps1 <명령어> [옵션]'
    Write-Host ""
    Write-Host "  명령어:" -ForegroundColor Yellow
    Write-Host "    list                    등록된 VM 목록 조회"
    Write-Host "    detail <번호>           VM 상세 정보 조회"
    Write-Host "    compare                 모든 VM 환경 비교표"
    Write-Host "    start <번호>            VM 시작"
    Write-Host "    stop <번호>             VM 중지"
    Write-Host "    start-all               모든 VM 시작"
    Write-Host "    stop-all                모든 VM 중지"
    Write-Host "    snapshot <번호> [이름]  스냅샷 생성"
    Write-Host "    delete <번호>           VM 삭제"
    Write-Host "    help                    도움말"
    Write-Host ""
    Write-Host "  예시:" -ForegroundColor Yellow
    Write-Host '    .\manage_vms.ps1 list'
    Write-Host '    .\manage_vms.ps1 detail 1'
    Write-Host '    .\manage_vms.ps1 start 3'
    Write-Host '    .\manage_vms.ps1 compare'
    Write-Host '    .\manage_vms.ps1 snapshot 1 "clean_install"'
    Write-Host '    .\manage_vms.ps1 delete 2'
    Write-Host ""
}

# === 메인 실행 ===
switch ($Command.ToLower()) {
    "list"      { Show-VmList }
    "detail"    { Show-VmDetail -Number ([int]$Arg1) }
    "compare"   { Show-VmCompare }
    "start"     { Start-Vm -Number ([int]$Arg1) }
    "stop"      { Stop-Vm -Number ([int]$Arg1) }
    "start-all" { Start-AllVms }
    "stop-all"  { Stop-AllVms }
    "snapshot"  { New-VmSnapshot -Number ([int]$Arg1) -Name $Arg2 }
    "delete"    { Remove-Vm -Number ([int]$Arg1) }
    default     { Show-Usage }
}
