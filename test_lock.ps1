try {
    $stream = [System.IO.File]::Open("Module_3_Test_Artifacts.xlsx", [System.IO.FileMode]::Open, [System.IO.FileAccess]::ReadWrite)
    $stream.Close()
    Write-Host "WRITABLE"
} catch {
    Write-Host "LOCKED"
}

