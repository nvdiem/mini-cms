# make-patch-zip.ps1
# Usage:
#   Right-click -> Run with PowerShell
#   or in terminal: powershell -ExecutionPolicy Bypass -File .\make-patch-zip.ps1

$ErrorActionPreference = "Stop"

$projectRoot = Get-Location
$stamp = Get-Date -Format "yyyyMMdd_HHmm"
$outName = "mini-cms-patch-for-ai_$stamp.zip"
$outPath = Join-Path $projectRoot $outName

# Files/folders to include
$include = @(
  "app",
  "docs",
  "resources\views",
  "routes\web.php",
  "database\migrations",
  "database\seeders",
  "composer.json",
  "composer.lock",
  ".env.example"
)

# Optional include if exists
if (Test-Path (Join-Path $projectRoot "config")) {
  $include += "config"
}

# Check existence
Write-Host "== Checking required paths =="
foreach ($p in $include) {
  if (-not (Test-Path (Join-Path $projectRoot $p))) {
    Write-Warning "Missing: $p"
  } else {
    Write-Host "OK: $p"
  }
}

# Do NOT include these (informational)
$excludeInfo = @("vendor", "node_modules", "storage\logs", "storage\framework", ".env")
Write-Host ""
Write-Host "== Excluded by design =="
$excludeInfo | ForEach-Object { Write-Host "- $_" }

# Clean existing zip if exists
if (Test-Path $outPath) {
  Remove-Item $outPath -Force
}

# Build temp staging folder to zip (ensures clean output)
$tempDir = Join-Path $env:TEMP ("mini_cms_patch_" + [guid]::NewGuid().ToString("N"))
New-Item -ItemType Directory -Path $tempDir | Out-Null

function Copy-IntoTemp($relPath) {
  $src = Join-Path $projectRoot $relPath
  if (Test-Path $src) {
    $dest = Join-Path $tempDir $relPath
    $destParent = Split-Path $dest -Parent
    if (-not (Test-Path $destParent)) { New-Item -ItemType Directory -Path $destParent -Force | Out-Null }

    if ((Get-Item $src).PSIsContainer) {
      Copy-Item $src $dest -Recurse -Force
    } else {
      Copy-Item $src $dest -Force
    }
  }
}

Write-Host ""
Write-Host "== Copying into staging folder =="
foreach ($p in $include) {
  Copy-IntoTemp $p
  if (Test-Path (Join-Path $projectRoot $p)) {
    Write-Host "Added: $p"
  }
}

# Safety: ensure .env is not copied
$envFile = Join-Path $tempDir ".env"
if (Test-Path $envFile) {
  Remove-Item $envFile -Force
}

# Zip
Write-Host ""
Write-Host "== Creating zip: $outName =="
Compress-Archive -Path (Join-Path $tempDir "*") -DestinationPath $outPath -Force

# Cleanup temp
Remove-Item $tempDir -Recurse -Force

Write-Host ""
Write-Host "DONE ✅ Created: $outPath"
Write-Host "You can now upload this zip to AI for analysis."
