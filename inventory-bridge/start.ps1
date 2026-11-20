# Arranca el bridge en el nodo (IP publica).
# Uso:  .\start.ps1
#       .\start.ps1 -Port 8787 -Listen 0.0.0.0

param(
    [string]$Listen = '0.0.0.0',
    [int]$Port = 8787
)

$ErrorActionPreference = 'Stop'
$Here = $PSScriptRoot
Set-Location $Here

if (-not (Test-Path (Join-Path $Here 'config.php'))) {
    Copy-Item (Join-Path $Here 'config.example.php') (Join-Path $Here 'config.php')
    Write-Host "Se creo config.php desde el ejemplo. Editalo antes de usar en produccion." -ForegroundColor Yellow
}

$php = (Get-Command php -ErrorAction SilentlyContinue)?.Source
if (-not $php) {
    throw 'No se encontro php.exe en el PATH (hace falta PHP con extension odbc).'
}

Write-Host "Bridge escuchando en http://${Listen}:${Port}" -ForegroundColor Cyan
Write-Host "Health:    GET http://<IP>:${Port}/health"
Write-Host "Inventory: GET http://<IP>:${Port}/inventory  (Bearer token)"
Write-Host "Ctrl+C para detener.`n"

& $php -S "${Listen}:${Port}" (Join-Path $Here 'index.php')
