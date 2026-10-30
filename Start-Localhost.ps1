param([switch]$WithVite)
$ErrorActionPreference = 'Stop'
$projectRoot = $PSScriptRoot
$runtimeRoot = Join-Path $projectRoot '.local'
New-Item -ItemType Directory -Path $runtimeRoot -Force | Out-Null

function Get-Listener([int]$Port) {
    Get-NetTCPConnection -LocalPort $Port -State Listen -ErrorAction SilentlyContinue | Select-Object -First 1
}

function Wait-Listener([int]$Port, $Process) {
    for ($attempt = 0; $attempt -lt 40; $attempt++) {
        if (Get-Listener $Port) { return }
        if ($Process.HasExited) { throw "Proses gagal berjalan. Periksa log pada $runtimeRoot." }
        Start-Sleep -Milliseconds 250
    }
    throw "Server port $Port belum siap. Periksa log pada $runtimeRoot."
}

if (-not (Get-Listener 3306)) {
    $mysqlRoot = Join-Path $runtimeRoot 'mysql-8.4.10-winx64'
    $mysqlBinary = Join-Path $mysqlRoot 'bin\mysqld.exe'
    if (-not (Test-Path -LiteralPath $mysqlBinary)) { throw 'MySQL lokal belum tersedia. Lihat docs/panduan-mysql.md.' }
    $arguments = @('--no-defaults', ('--basedir="' + $mysqlRoot + '"'), ('--datadir="' + (Join-Path $runtimeRoot 'mysql-data') + '"'), '--bind-address=127.0.0.1', '--port=3306', '--mysqlx=OFF', ('--log-error="' + (Join-Path $runtimeRoot 'mysql-error.log') + '"'))
    $process = Start-Process -FilePath $mysqlBinary -ArgumentList $arguments -WindowStyle Hidden -PassThru
    Wait-Listener 3306 $process
}

$phpCommand = Get-Command php -ErrorAction SilentlyContinue
$phpBinary = if ($phpCommand) { $phpCommand.Source } else { 'C:\xampp\php\php.exe' }
if (-not (Test-Path -LiteralPath $phpBinary)) { throw 'PHP 8.2+ tidak ditemukan.' }

# The health command verifies that port 3306 actually belongs to the configured MySQL server.
Push-Location $projectRoot
try {
    & $phpBinary artisan sipas:health
    if ($LASTEXITCODE -ne 0) { throw 'Koneksi database gagal. Server web tidak dijalankan.' }
} finally { Pop-Location }

if (-not (Get-Listener 8000)) {
    $server = Start-Process -FilePath $phpBinary -ArgumentList @('artisan', 'serve', '--host=127.0.0.1', '--port=8000') -WorkingDirectory $projectRoot -WindowStyle Hidden -PassThru -RedirectStandardOutput (Join-Path $runtimeRoot 'laravel-server.log') -RedirectStandardError (Join-Path $runtimeRoot 'laravel-server-error.log')
    Wait-Listener 8000 $server
}

if ($WithVite -and -not (Get-Listener 5173)) {
    $nodeBinary = (Get-Command node -ErrorAction Stop).Source
    $vite = Start-Process -FilePath $nodeBinary -ArgumentList @('node_modules/vite/bin/vite.js', '--host', '127.0.0.1', '--port', '5173', '--strictPort') -WorkingDirectory $projectRoot -WindowStyle Hidden -PassThru -RedirectStandardOutput (Join-Path $runtimeRoot 'vite.log') -RedirectStandardError (Join-Path $runtimeRoot 'vite-error.log')
    Wait-Listener 5173 $vite
} elseif (-not $WithVite -and -not (Get-Listener 5173)) {
    # A stale hot file makes Laravel request assets from a Vite process that is no longer running.
    $hotFile = Join-Path $projectRoot 'public\hot'
    if (Test-Path -LiteralPath $hotFile) { Remove-Item -LiteralPath $hotFile }
}

Write-Host 'SIPAS berjalan di http://127.0.0.1:8000'
