# Mini CMS Build Script
# Creates a production-ready release ZIP

param(
    [string]$Version = "1.0.0"
)

$ErrorActionPreference = "Stop"

Write-Host "=== Mini CMS Build Script ===" -ForegroundColor Cyan
Write-Host "Building version: $Version" -ForegroundColor Yellow

# Paths
# Adjusted for script being in /scripts folder
$SourceDir = Resolve-Path "$PSScriptRoot\.."
$BuildDir = "$env:TEMP\mini-cms-build"
$OutputFile = Join-Path $SourceDir "mini-cms-v$Version.zip"

Write-Host "Source Dir: $SourceDir" -ForegroundColor Gray

# Clean previous build
if (Test-Path $BuildDir) {
    Write-Host "Cleaning previous build..." -ForegroundColor Gray
    Remove-Item -Recurse -Force $BuildDir
}

if (Test-Path $OutputFile) {
    Remove-Item -Force $OutputFile
}

# Create build directory
Write-Host "Creating build directory..." -ForegroundColor Gray
New-Item -ItemType Directory -Path $BuildDir | Out-Null

# Copy files (excluding dev files)
Write-Host "Copying files..." -ForegroundColor Gray
$excludeDirs = @('.git', 'node_modules', 'tests', '.github', '.vscode', '.idea')
# Added 'scripts' to exclude list so we don't include build scripts in the final zip
$excludeFiles = @('.env', '.gitignore', '.gitattributes', '.editorconfig', 'phpunit.xml', 'vite.config.js', 'package.json', 'package-lock.json', 'build.ps1', 'make-patch-zip.ps1', '*.zip', 'scripts')

Get-ChildItem -Path $SourceDir -Exclude $excludeFiles | Where-Object {
    $item = $_
    -not ($excludeDirs | Where-Object { $item.Name -eq $_ })
} | ForEach-Object {
    if ($_.PSIsContainer) {
        Copy-Item -Recurse -Path $_.FullName -Destination "$BuildDir\$($_.Name)"
    } else {
        Copy-Item -Path $_.FullName -Destination $BuildDir
    }
}

# Remove additional dev files from copied directories
Write-Host "Removing dev files..." -ForegroundColor Gray
$devFilesToRemove = @(
    "$BuildDir\storage\installed",
    "$BuildDir\storage\installing",
    "$BuildDir\storage\app\pagebuilder",
    "$BuildDir\storage\logs\*.log",
    "$BuildDir\bootstrap\cache\*.php",
    "$BuildDir\public\pagebuilder"
)

foreach ($pattern in $devFilesToRemove) {
    Get-Item -Path $pattern -ErrorAction SilentlyContinue | Remove-Item -Recurse -Force -ErrorAction SilentlyContinue
}

# Ensure storage directories exist
Write-Host "Creating storage structure..." -ForegroundColor Gray
$storageDirs = @(
    "$BuildDir\storage\app\public",
    "$BuildDir\storage\app\pagebuilder\zips",
    "$BuildDir\storage\framework\cache\data",
    "$BuildDir\storage\framework\sessions",
    "$BuildDir\storage\framework\views",
    "$BuildDir\storage\logs",
    "$BuildDir\bootstrap\cache",
    "$BuildDir\public\pagebuilder"
)

foreach ($dir in $storageDirs) {
    if (-not (Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir -Force | Out-Null
    }
    # Create .gitkeep
    New-Item -ItemType File -Path "$dir\.gitkeep" -Force | Out-Null
}

# Create empty .env (to be configured during install)
Write-Host "Creating placeholder .env..." -ForegroundColor Gray
Copy-Item -Path "$BuildDir\.env.example" -Destination "$BuildDir\.env" -ErrorAction SilentlyContinue

# Run composer (if available)
if (Get-Command "composer" -ErrorAction SilentlyContinue) {
    Write-Host "Installing production dependencies..." -ForegroundColor Gray
    Push-Location $BuildDir
    
    # Configure composer to run without interaction and dev deps
    $composerArgs = @("install", "--no-dev", "--optimize-autoloader", "--no-interaction", "--ignore-platform-reqs")
    
    # Run composer using Start-Process to avoid PowerShell stderr capturing issues
    $process = Start-Process -FilePath "composer" -ArgumentList $composerArgs -NoNewWindow -Wait -PassThru
    
    if ($process.ExitCode -ne 0) {
        Write-Warning "Composer finished with exit code $($process.ExitCode). Please check if vendor directory exists."
    }
    
    Pop-Location
} else {
    Write-Host "Composer not found, skipping dependency install" -ForegroundColor Yellow
    Write-Host "Make sure vendor/ is included in source" -ForegroundColor Yellow
}

# Create ZIP
Write-Host "Creating ZIP archive..." -ForegroundColor Gray
Compress-Archive -Path "$BuildDir\*" -DestinationPath $OutputFile -CompressionLevel Optimal

# Clean up
Write-Host "Cleaning up..." -ForegroundColor Gray
Remove-Item -Recurse -Force $BuildDir

# Done
$fileSize = (Get-Item $OutputFile).Length / 1MB
Write-Host ""
Write-Host "=== Build Complete! ===" -ForegroundColor Green
Write-Host "Output: $OutputFile" -ForegroundColor White
Write-Host "Size: $([math]::Round($fileSize, 2)) MB" -ForegroundColor White
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "  1. Test installation on a fresh server"
Write-Host "  2. Upload to your sales platform"
Write-Host "  3. Profit! 💰"
