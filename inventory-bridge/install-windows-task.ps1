# Registra una tarea de Windows para que el bridge arranque al iniciar sesion.
# Ejecutar UNA vez en el nodo con IP publica (PowerShell como Administrador recomendado):
#   .\install-windows-task.ps1

param(
    [string]$TaskName = 'PaticasInventoryBridge',
    [int]$Port = 8787
)

$ErrorActionPreference = 'Stop'
$Here = $PSScriptRoot
$startScript = Join-Path $Here 'start.ps1'

if (-not (Test-Path $startScript)) {
    throw "No existe $startScript"
}

$action = New-ScheduledTaskAction `
    -Execute 'powershell.exe' `
    -Argument "-NoProfile -ExecutionPolicy Bypass -WindowStyle Hidden -File `"$startScript`" -Port $Port"

$trigger = New-ScheduledTaskTrigger -AtLogOn
$settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -RestartCount 3 -RestartInterval (New-TimeSpan -Minutes 1)

Register-ScheduledTask -TaskName $TaskName -Action $action -Trigger $trigger -Settings $settings -Force | Out-Null
Start-ScheduledTask -TaskName $TaskName

Write-Host "Tarea '$TaskName' registrada y arrancada (puerto $Port)." -ForegroundColor Green
Write-Host "Firewall: abre TCP $Port hacia este equipo (idealmente solo IP de SiteGround)."
Write-Host "Portal: INVENTORY_BRIDGE_URL=http://<IP-PUBLICA>:$Port/inventory"
