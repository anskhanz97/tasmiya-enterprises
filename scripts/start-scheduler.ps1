$ErrorActionPreference = 'Stop'
$projectRoot = Split-Path $PSScriptRoot -Parent
if ([System.IO.Path]::GetPathRoot($projectRoot) -ine 'D:\') {
    throw 'Run this local scheduler from the project on D:.'
}
$runtimeDir = Join-Path $projectRoot 'storage\app\local-runtime'
New-Item -ItemType Directory -Force -Path $runtimeDir | Out-Null
$env:TEMP = $runtimeDir
$env:TMP = $runtimeDir
if (Test-Path -LiteralPath (Join-Path $runtimeDir 'php.ini')) {
    $env:PHPRC = Join-Path $runtimeDir 'php.ini'
}
$artisanPath = Join-Path $projectRoot 'artisan'
$existing = Get-CimInstance Win32_Process -Filter "name = 'php.exe'" | Where-Object {
    $_.CommandLine -like '*schedule:work*' -and $_.CommandLine.Contains($artisanPath)
}
if ($existing) {
    Write-Output "Scheduler already running: $($existing.ProcessId)"
    exit 0
}
$phpPath = (Get-Command php).Source
$scheduler = Start-Process -FilePath $phpPath -ArgumentList ('"' + $artisanPath + '"'), 'schedule:work', '--no-interaction' -WorkingDirectory $projectRoot -WindowStyle Hidden -RedirectStandardOutput (Join-Path $runtimeDir 'scheduler.log') -RedirectStandardError (Join-Path $runtimeDir 'scheduler-error.log') -PassThru
Write-Output "Scheduler started: $($scheduler.Id). Logs: $runtimeDir"
