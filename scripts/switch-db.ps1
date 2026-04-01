param(
    [Parameter(Mandatory = $true)]
    [ValidateSet('sqlite', 'supabase', 'auto')]
    [string]$Mode
)

$ErrorActionPreference = 'Stop'

$repoRoot = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$envPath = Join-Path $repoRoot '.env'

if (-not (Test-Path $envPath)) {
    throw ".env file not found at $envPath"
}

$lines = Get-Content -Path $envPath

function Set-EnvValue {
    param(
        [string[]]$InputLines,
        [string]$Key,
        [string]$Value
    )

    $pattern = "^\s*#?\s*" + [regex]::Escape($Key) + "\s*=.*$"
    $replacement = "$Key=$Value"
    $found = $false

    for ($i = 0; $i -lt $InputLines.Count; $i++) {
        if ($InputLines[$i] -match $pattern) {
            $InputLines[$i] = $replacement
            $found = $true
            break
        }
    }

    if (-not $found) {
        $InputLines += $replacement
    }

    return ,$InputLines
}

$lines = Set-EnvValue -InputLines $lines -Key 'DB_CONNECTION' -Value $Mode

if ($Mode -eq 'sqlite' -or $Mode -eq 'auto') {
    $lines = Set-EnvValue -InputLines $lines -Key 'DB_DATABASE' -Value 'database/database.sqlite'
    $lines = Set-EnvValue -InputLines $lines -Key 'DB_FOREIGN_KEYS' -Value 'true'
}

Set-Content -Path $envPath -Value $lines -Encoding ASCII

$phpExe = 'C:/php/php.exe'
if (Test-Path $phpExe) {
    Push-Location $repoRoot
    try {
        & $phpExe artisan optimize:clear | Out-Host
    }
    finally {
        Pop-Location
    }
} else {
    Write-Host 'Warning: C:/php/php.exe not found. Run artisan optimize:clear manually.'
}

Write-Host "Switched DB mode to: $Mode"
Write-Host 'Current DB_CONNECTION line:'
(Get-Content -Path $envPath | Where-Object { $_ -match '^DB_CONNECTION=' } | Select-Object -First 1) | ForEach-Object { Write-Host $_ }
