Add-Type -AssemblyName System.IO.Compression.FileSystem
$zip = [System.IO.Compression.ZipFile]::OpenRead((Resolve-Path 'Module_3_Test_Artifacts_8Fitur.xlsx'))

foreach ($shNum in 2..7) {
    $entry = $zip.GetEntry("xl/worksheets/sheet$shNum.xml")
    $sr = New-Object System.IO.StreamReader($entry.Open())
    $xml = [xml]$sr.ReadToEnd()
    $sr.Dispose()
    Write-Host "================== SHEET $shNum =================="
    $count = $xml.worksheet.sheetData.row.Count
    Write-Host "Total rows: $count"
    $rows = $xml.worksheet.sheetData.row
    # Print first 3 and last 3 rows
    for ($i = 0; $i -lt [Math]::Min(5, $count); $i++) {
        $r = $rows[$i]
        $cells = @($r.c | ForEach-Object { "$($_.r): $(if ($_.is.t) {$_.is.t} else {$_.v})" })
        Write-Host "Row $($r.r): $($cells -join ' | ')"
    }
    Write-Host "..."
    for ($i = [Math]::Max(5, $count - 4); $i -lt $count; $i++) {
        $r = $rows[$i]
        $cells = @($r.c | ForEach-Object { "$($_.r): $(if ($_.is.t) {$_.is.t} else {$_.v})" })
        Write-Host "Row $($r.r): $($cells -join ' | ')"
    }
}
$zip.Dispose()
