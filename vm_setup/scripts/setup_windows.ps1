# ============================================================
#  VM 내부 Windows 환경 셋업 스크립트
#  각 VM에서 실행하여 고유한 PC 환경을 구성합니다.
#  관리자 권한으로 실행해야 합니다.
# ============================================================

param(
    [string]$ProfilePath = "",
    [string]$ComputerName = "",
    [string]$Timezone = "Korea Standard Time",
    [string]$Resolution = "1920x1080",
    [switch]$SkipReboot
)

$ErrorActionPreference = "Stop"

# === 색상 출력 함수 ===
function Write-Step {
    param([string]$Message)
    Write-Host "`n[STEP] " -ForegroundColor Cyan -NoNewline
    Write-Host $Message -ForegroundColor White
}

function Write-OK {
    param([string]$Message)
    Write-Host "  [OK] " -ForegroundColor Green -NoNewline
    Write-Host $Message
}

function Write-Warn {
    param([string]$Message)
    Write-Host "  [!] " -ForegroundColor Yellow -NoNewline
    Write-Host $Message
}

function Write-Err {
    param([string]$Message)
    Write-Host "  [X] " -ForegroundColor Red -NoNewline
    Write-Host $Message
}

# === 관리자 권한 확인 ===
function Test-Admin {
    $currentUser = [Security.Principal.WindowsIdentity]::GetCurrent()
    $principal = New-Object Security.Principal.WindowsPrincipal($currentUser)
    return $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
}

if (-not (Test-Admin)) {
    Write-Err "이 스크립트는 관리자 권한으로 실행해야 합니다!"
    Write-Host "PowerShell을 '관리자 권한으로 실행' 후 다시 시도하세요." -ForegroundColor Yellow
    exit 1
}

# === JSON 프로필 로드 ===
$profile = $null
if ($ProfilePath -and (Test-Path $ProfilePath)) {
    Write-Step "프로필 파일 로드: $ProfilePath"
    $profile = Get-Content $ProfilePath | ConvertFrom-Json
    if (-not $ComputerName) { $ComputerName = $profile.os_settings.computer_name }
    if ($profile.os_settings.timezone) { $Timezone = $profile.os_settings.timezone }
    if ($profile.os_settings.resolution) { $Resolution = $profile.os_settings.resolution }
    Write-OK "프로필 로드 완료: $($profile.vm_name)"
} else {
    Write-Warn "프로필 파일이 지정되지 않았습니다. 기본값을 사용합니다."
}

# 컴퓨터 이름 기본값
if (-not $ComputerName) {
    $suffix = (Get-Random -Maximum 65535).ToString("X4")
    $ComputerName = "DESKTOP-$suffix"
}

Write-Host ""
Write-Host "============================================================" -ForegroundColor Blue
Write-Host "  Windows 환경 셋업 시작" -ForegroundColor Blue
Write-Host "============================================================" -ForegroundColor Blue
Write-Host ""
Write-Host "  컴퓨터 이름: $ComputerName" -ForegroundColor Cyan
Write-Host "  시간대:      $Timezone" -ForegroundColor Cyan
Write-Host "  해상도:      $Resolution" -ForegroundColor Cyan
Write-Host ""

# ============================================================
# 1. 컴퓨터 이름 변경
# ============================================================
Write-Step "컴퓨터 이름 변경: $ComputerName"
try {
    $currentName = $env:COMPUTERNAME
    if ($currentName -ne $ComputerName) {
        Rename-Computer -NewName $ComputerName -Force
        Write-OK "컴퓨터 이름이 '$currentName' -> '$ComputerName' 으로 변경됩니다. (재부팅 후 적용)"
    } else {
        Write-OK "이미 올바른 이름입니다: $ComputerName"
    }
} catch {
    Write-Err "컴퓨터 이름 변경 실패: $_"
}

# ============================================================
# 2. 시간대 설정
# ============================================================
Write-Step "시간대 설정: $Timezone"
try {
    Set-TimeZone -Id $Timezone
    Write-OK "시간대가 '$Timezone' 으로 설정되었습니다."
} catch {
    Write-Err "시간대 설정 실패: $_"
    Write-Warn "사용 가능한 시간대: Get-TimeZone -ListAvailable"
}

# ============================================================
# 3. Windows 시간 동기화 비활성화 (VM별 시간 독립)
# ============================================================
Write-Step "Windows 시간 동기화 설정"
try {
    # Windows Time 서비스 중지
    Stop-Service w32time -Force -ErrorAction SilentlyContinue
    Set-Service w32time -StartupType Disabled -ErrorAction SilentlyContinue
    Write-OK "Windows Time 서비스가 비활성화되었습니다."
} catch {
    Write-Warn "Windows Time 서비스 설정 실패 (이미 비활성화일 수 있음)"
}

# ============================================================
# 4. VMware Tools 시간 동기화 비활성화
# ============================================================
Write-Step "VMware Tools 시간 동기화 비활성화"
try {
    $vmtoolsReg = "HKLM:\SOFTWARE\VMware, Inc.\VMware Tools"
    if (Test-Path $vmtoolsReg) {
        Set-ItemProperty -Path $vmtoolsReg -Name "SyncTime" -Value 0 -Type DWord -Force
        Write-OK "VMware Tools 시간 동기화가 비활성화되었습니다."
    } else {
        Write-Warn "VMware Tools 레지스트리 키를 찾을 수 없습니다."
    }
} catch {
    Write-Warn "VMware Tools 설정 실패: $_"
}

# ============================================================
# 5. 해상도 설정 (레지스트리)
# ============================================================
Write-Step "디스플레이 해상도 설정: $Resolution"
try {
    $resParts = $Resolution -split 'x'
    $width = [int]$resParts[0]
    $height = [int]$resParts[1]

    # 해상도 적용 함수
    Add-Type @"
    using System;
    using System.Runtime.InteropServices;

    public class DisplaySettings {
        [DllImport("user32.dll")]
        public static extern int EnumDisplaySettings(string deviceName, int modeNum, ref DEVMODE devMode);

        [DllImport("user32.dll")]
        public static extern int ChangeDisplaySettings(ref DEVMODE devMode, int flags);

        [StructLayout(LayoutKind.Sequential)]
        public struct DEVMODE {
            [MarshalAs(UnmanagedType.ByValTStr, SizeConst = 32)]
            public string dmDeviceName;
            public short dmSpecVersion;
            public short dmDriverVersion;
            public short dmSize;
            public short dmDriverExtra;
            public int dmFields;
            public int dmPositionX;
            public int dmPositionY;
            public int dmDisplayOrientation;
            public int dmDisplayFixedOutput;
            public short dmColor;
            public short dmDuplex;
            public short dmYResolution;
            public short dmTTOption;
            public short dmCollate;
            [MarshalAs(UnmanagedType.ByValTStr, SizeConst = 32)]
            public string dmFormName;
            public short dmLogPixels;
            public int dmBitsPerPel;
            public int dmPelsWidth;
            public int dmPelsHeight;
            public int dmDisplayFlags;
            public int dmDisplayFrequency;
            public int dmICMMethod;
            public int dmICMIntent;
            public int dmMediaType;
            public int dmDitherType;
            public int dmReserved1;
            public int dmReserved2;
            public int dmPanningWidth;
            public int dmPanningHeight;
        }
    }
"@

    $dm = New-Object DisplaySettings+DEVMODE
    $dm.dmSize = [System.Runtime.InteropServices.Marshal]::SizeOf($dm)
    [DisplaySettings]::EnumDisplaySettings($null, -1, [ref]$dm) | Out-Null
    $dm.dmPelsWidth = $width
    $dm.dmPelsHeight = $height
    $dm.dmFields = 0x80000 -bor 0x100000  # DM_PELSWIDTH | DM_PELSHEIGHT
    $result = [DisplaySettings]::ChangeDisplaySettings([ref]$dm, 0)

    if ($result -eq 0) {
        Write-OK "해상도가 ${width}x${height} 으로 변경되었습니다."
    } else {
        Write-Warn "해상도 변경 결과 코드: $result (재부팅 후 적용될 수 있음)"
    }
} catch {
    Write-Warn "해상도 설정 실패: $_"
}

# ============================================================
# 6. Windows 텔레메트리 비활성화
# ============================================================
Write-Step "Windows 텔레메트리 비활성화"
try {
    # 텔레메트리 서비스 비활성화
    $telemetryServices = @("DiagTrack", "dmwappushservice")
    foreach ($svc in $telemetryServices) {
        $service = Get-Service -Name $svc -ErrorAction SilentlyContinue
        if ($service) {
            Stop-Service $svc -Force -ErrorAction SilentlyContinue
            Set-Service $svc -StartupType Disabled -ErrorAction SilentlyContinue
            Write-OK "  서비스 비활성화: $svc"
        }
    }

    # 레지스트리로 텔레메트리 레벨 최소화
    $telemetryReg = "HKLM:\SOFTWARE\Policies\Microsoft\Windows\DataCollection"
    if (-not (Test-Path $telemetryReg)) {
        New-Item -Path $telemetryReg -Force | Out-Null
    }
    Set-ItemProperty -Path $telemetryReg -Name "AllowTelemetry" -Value 0 -Type DWord -Force
    Write-OK "텔레메트리 레벨이 최소(0)로 설정되었습니다."
} catch {
    Write-Warn "텔레메트리 설정 실패: $_"
}

# ============================================================
# 7. Windows 업데이트 지연 설정
# ============================================================
Write-Step "Windows 업데이트 설정"
try {
    $wuReg = "HKLM:\SOFTWARE\Policies\Microsoft\Windows\WindowsUpdate\AU"
    if (-not (Test-Path $wuReg)) {
        New-Item -Path $wuReg -Force | Out-Null
    }
    # 자동 업데이트를 '알림만' 으로 설정
    Set-ItemProperty -Path $wuReg -Name "AUOptions" -Value 2 -Type DWord -Force
    Set-ItemProperty -Path $wuReg -Name "NoAutoUpdate" -Value 0 -Type DWord -Force
    Write-OK "Windows 업데이트: 알림만 (자동 설치 안 함)"
} catch {
    Write-Warn "Windows 업데이트 설정 실패: $_"
}

# ============================================================
# 8. 네트워크 프로필 설정 (Private)
# ============================================================
Write-Step "네트워크 프로필을 Private으로 설정"
try {
    Get-NetConnectionProfile | Set-NetConnectionProfile -NetworkCategory Private -ErrorAction SilentlyContinue
    Write-OK "네트워크 프로필이 Private으로 설정되었습니다."
} catch {
    Write-Warn "네트워크 프로필 설정 실패: $_"
}

# ============================================================
# 9. 방화벽 기본 설정
# ============================================================
Write-Step "방화벽 설정"
try {
    Set-NetFirewallProfile -Profile Domain,Public,Private -Enabled True
    Write-OK "방화벽이 모든 프로필에서 활성화되었습니다."
} catch {
    Write-Warn "방화벽 설정 실패: $_"
}

# ============================================================
# 10. 고유한 Machine GUID 재생성
# ============================================================
Write-Step "Machine GUID 재생성"
try {
    $newGuid = [guid]::NewGuid().ToString()
    $machineGuidReg = "HKLM:\SOFTWARE\Microsoft\Cryptography"
    Set-ItemProperty -Path $machineGuidReg -Name "MachineGuid" -Value $newGuid -Force
    Write-OK "새 Machine GUID: $newGuid"
} catch {
    Write-Err "Machine GUID 재생성 실패: $_"
}

# ============================================================
# 11. 고유한 SID 관련 - Sysprep 안내
# ============================================================
Write-Step "Windows SID 고유화 안내"
Write-Warn "Windows SID를 변경하려면 Sysprep을 실행해야 합니다."
Write-Host "  실행 방법: C:\Windows\System32\Sysprep\sysprep.exe /oobe /generalize /reboot" -ForegroundColor DarkGray
Write-Host "  주의: Sysprep 실행 시 초기 설정 화면으로 돌아갑니다." -ForegroundColor DarkGray

# ============================================================
# 12. 환경 설정 요약 저장
# ============================================================
Write-Step "환경 설정 요약 저장"
$summaryPath = "$env:USERPROFILE\Desktop\VM_Environment_Info.txt"
try {
    $summary = @"
============================================================
  VM 환경 설정 요약
  생성일: $(Get-Date -Format "yyyy-MM-dd HH:mm:ss")
============================================================

[시스템 정보]
  컴퓨터 이름:    $ComputerName
  시간대:         $Timezone
  해상도:         $Resolution
  Machine GUID:   $newGuid

[하드웨어 정보 (VMware)]
$(if ($profile) {
"  제조사:         $($profile.hardware.manufacturer)
  모델:           $($profile.hardware.model)
  시리얼 번호:    $($profile.hardware.serial_number)
  MAC 주소:       $($profile.network.mac_address)
  CPU 코어:       $($profile.specs.cpu_cores)
  RAM:            $($profile.specs.ram_mb)MB"
} else {
"  (프로필 없이 실행됨)"
})

[네트워크]
  어댑터:         $(Get-NetAdapter | Select-Object -First 1 -ExpandProperty Name)
  MAC 주소:       $(Get-NetAdapter | Select-Object -First 1 -ExpandProperty MacAddress)
  IP 주소:        $((Get-NetIPAddress -AddressFamily IPv4 | Where-Object { $_.InterfaceAlias -notmatch 'Loopback' } | Select-Object -First 1).IPAddress)

[설정 완료 항목]
  - 컴퓨터 이름 변경 (재부팅 후 적용)
  - 시간대 설정
  - 시간 동기화 비활성화
  - 해상도 설정
  - 텔레메트리 비활성화
  - Windows 업데이트 알림 모드
  - 네트워크 프로필 Private
  - 방화벽 활성화
  - Machine GUID 재생성

============================================================
"@
    $summary | Out-File -FilePath $summaryPath -Encoding UTF8
    Write-OK "요약 파일 저장: $summaryPath"
} catch {
    Write-Warn "요약 파일 저장 실패: $_"
}

# ============================================================
# 완료
# ============================================================
Write-Host ""
Write-Host "============================================================" -ForegroundColor Green
Write-Host "  Windows 환경 셋업이 완료되었습니다!" -ForegroundColor Green
Write-Host "============================================================" -ForegroundColor Green
Write-Host ""

if (-not $SkipReboot) {
    Write-Host "  10초 후 재부팅됩니다... (취소: Ctrl+C)" -ForegroundColor Yellow
    Write-Host "  재부팅을 건너뛰려면 -SkipReboot 옵션을 사용하세요." -ForegroundColor DarkGray
    Start-Sleep -Seconds 10
    Restart-Computer -Force
} else {
    Write-Warn "재부팅이 필요합니다. 수동으로 재부팅해주세요."
}
