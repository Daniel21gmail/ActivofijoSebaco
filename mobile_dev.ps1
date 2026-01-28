# Start ngrok in the background
Write-Host "Iniciando ngrok..."
$ngrokProcess = Start-Process -FilePath ".\ngrok.exe" -ArgumentList "http 8000" -PassThru -WindowStyle Hidden

# Wait for ngrok to initialize
Write-Host "Esperando a que ngrok conecte..."
Start-Sleep -Seconds 3

# Fetch the public URL from ngrok's local API
try {
    $response = Invoke-RestMethod -Uri "http://localhost:4040/api/tunnels"
    $publicUrl = $response.tunnels[0].public_url
}
catch {
    Write-Error "No se pudo obtener la URL de ngrok. Asegurese de que ngrok se inicio correctamente."
    Stop-Process -Id $ngrokProcess.Id
    pause
    exit
}

if (-not $publicUrl) {
    Write-Error "No se encontro un tunel activo."
    Stop-Process -Id $ngrokProcess.Id
    pause
    exit
}

Write-Host "URL publica obtenida: $publicUrl"

# Update .env file
$envFile = ".env"
if (Test-Path $envFile) {
    $content = Get-Content $envFile
    $newContent = $content -replace '^APP_URL=.*$', "APP_URL=$publicUrl"
    Set-Content -Path $envFile -Value $newContent
    Write-Host "Archivo .env actualizado con exito."
}
else {
    Write-Error "No se encontro el archivo .env"
}

Write-Host "`n!LISTO! Modo movil configurado."
Write-Host "1. Si ya tenia servidores corriendo, reinicielos ahora."
Write-Host "2. Si no, inicie 'npm run dev' y 'php artisan serve'."
Write-Host "`nPresione cualquier tecla para cerrar ngrok y salir..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")

# Cleanup
Stop-Process -Id $ngrokProcess.Id
Write-Host "Ngrok detenido."
