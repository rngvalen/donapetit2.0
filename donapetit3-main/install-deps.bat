@echo off
setlocal

REM Comprueba que package.json exista
if not exist "%~dp0package.json" (
    echo [ERROR] Ejecuta este archivo desde la carpeta raiz del proyecto.
    exit /b 1
)

REM Comprueba que npm este disponible
where npm >nul 2>&1
if errorlevel 1 (
    echo [ERROR] npm no se encuentra en el PATH. Instala Node.js desde https://nodejs.org/.
    exit /b 1
)

pushd "%~dp0"
echo Instalando dependencias con npm install...
npm install
if errorlevel 1 (
    echo [ERROR] La instalacion de dependencias fallo. Revisa el mensaje anterior.
    popd
    exit /b 1
)
popd

echo.
echo Dependencias instaladas correctamente.
echo Para compilar los estilos ejecuta: npm run build
echo Para trabajar en desarrollo ejecuta: npm run dev
