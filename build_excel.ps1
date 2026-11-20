Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

$templatePath = "c:\laragon\www\SMARTKASIR\Module_3_Test_Artifacts.xlsx"
$destPath = "c:\laragon\www\SMARTKASIR\Module_3_Test_Artifacts_SmartKasir_Lengkap.xlsx"

# Copy original to destination
Copy-Item $templatePath $destPath -Force

# Helper function to escape xml string
function EscXml($str) {
    if ($null -eq $str) { return "" }
    return [System.Security.SecurityElement]::Escape($str)
}

# Helper to build cell XML
function MakeCell($colLetter, $rowNum, $styleId, $val) {
    $r = "$colLetter$rowNum"
    $valEsc = EscXml $val
    return "<c r=`"$r`" s=`"$styleId`" t=`"inlineStr`"><is><t>$valEsc</t></is></c>"
}

# ----------------- SHEET 2: 01_Proyek -----------------
$s2Xml = @"
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<sheetPr><outlinePr summaryBelow="1" summaryRight="1"/><pageSetUpPr fitToPage="1"/></sheetPr>
<dimension ref="A1:F18"/>
<sheetViews><sheetView showGridLines="0" workbookViewId="0"><selection activeCell="A1" sqref="A1"/></sheetView></sheetViews>
<sheetFormatPr baseColWidth="8" defaultColWidth="14.44" defaultRowHeight="15" customHeight="1"/>
<cols>
<col min="1" max="1" width="24" customWidth="1" style="15"/>
<col min="2" max="2" width="22" customWidth="1" style="15"/>
<col min="3" max="3" width="12" customWidth="1" style="15"/>
<col min="4" max="4" width="18" customWidth="1" style="15"/>
<col min="5" max="5" width="45" customWidth="1" style="15"/>
<col min="6" max="26" width="10" customWidth="1" style="15"/>
</cols>
<sheetData>
<row r="1" ht="33.75" customHeight="1" s="15"><c r="A1" s="30" t="inlineStr"><is><t>IDENTITAS PROYEK</t></is></c></row>
<row r="3" ht="20" customHeight="1" s="15"><c r="A3" s="1" t="inlineStr"><is><t>Bidang</t></is></c><c r="B3" s="1" t="inlineStr"><is><t>Isian</t></is></c></row>
<row r="4" ht="20" customHeight="1" s="15"><c r="A4" s="4" t="inlineStr"><is><t>Nama aplikasi</t></is></c><c r="B4" s="5" t="inlineStr"><is><t>SmartKasir (Point of Sale System)</t></is></c></row>
<row r="5" ht="20" customHeight="1" s="15"><c r="A5" s="4" t="inlineStr"><is><t>Tester</t></is></c><c r="B5" s="5" t="inlineStr"><is><t>Muchammad Dwi Ferdi Ilham Arifin</t></is></c></row>
<row r="6" ht="20" customHeight="1" s="15"><c r="A6" s="4" t="inlineStr"><is><t>NIM</t></is></c><c r="B6" s="5" t="inlineStr"><is><t>202310370311292</t></is></c></row>
<row r="7" ht="20" customHeight="1" s="15"><c r="A7" s="4" t="inlineStr"><is><t>Versi / build</t></is></c><c r="B7" s="5" t="inlineStr"><is><t>v1.0.0-prod (Build 2026.10)</t></is></c></row>
<row r="9" ht="24" customHeight="1" s="15"><c r="A9" s="3" t="inlineStr"><is><t>FITUR YANG DIUJI: daftar setiap fitur/input, berapa n-nya, dan berapa partisi EP-nya</t></is></c></row>
<row r="10" ht="25" customHeight="1" s="15">
<c r="A10" s="1" t="inlineStr"><is><t>Fitur / Input</t></is></c>
<c r="B10" s="1" t="inlineStr"><is><t>n (variabel BVA)</t></is></c>
<c r="C10" s="1" t="inlineStr"><is><t>4n+1</t></is></c>
<c r="D10" s="1" t="inlineStr"><is><t>Jumlah Partisi EP</t></is></c>
<c r="E10" s="1" t="inlineStr"><is><t>Requirement / Rule Dasar</t></is></c>
</row>
<row r="11" ht="40" customHeight="1" s="15">
<c r="A11" s="5" t="inlineStr"><is><t>Validasi Keranjang Belanja &amp; Pembayaran Transaksi Kasir (Q1: Makanan, Q2: Minuman, C: Uang Tunai)</t></is></c>
<c r="B11" s="4" t="inlineStr"><is><t>3</t></is></c>
<c r="C11" s="4" t="inlineStr"><is><t>13</t></is></c>
<c r="D11" s="4" t="inlineStr"><is><t>8</t></is></c>
<c r="E11" s="5" t="inlineStr"><is><t>RQ-004 s/d RQ-008: Transaksi valid jika 1 &lt;= Q1, Q2 &lt;= 50 (stok fisik) dan C &gt;= Total Belanja T = (12.000*Q1 + 18.000*Q2). Transaksi ditolak jika stok kurang atau uang kurang.</t></is></c>
</row>
<row r="12" ht="30" customHeight="1" s="15">
<c r="A12" s="5" t="inlineStr"><is><t>Registrasi Akun Kasir (Validasi Form &amp; Feedback UI)</t></is></c>
<c r="B12" s="4" t="inlineStr"><is><t>1</t></is></c>
<c r="C12" s="4" t="inlineStr"><is><t>5</t></is></c>
<c r="D12" s="4" t="inlineStr"><is><t>3</t></is></c>
<c r="E12" s="5" t="inlineStr"><is><t>RQ-002: Pendaftaran akun kasir baru wajib sukses menyimpan ke database dan menampilkan feedback visual alert hijau.</t></is></c>
</row>
</sheetData>
</worksheet>
"@

# ----------------- SHEET 3: 02_BVA -----------------
$s3Xml = @"
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<sheetPr><outlinePr summaryBelow="1" summaryRight="1"/><pageSetUpPr fitToPage="1"/></sheetPr>
<dimension ref="A1:G23"/>
<sheetViews><sheetView showGridLines="0" workbookViewId="0"><selection activeCell="A1" sqref="A1"/></sheetView></sheetViews>
<sheetFormatPr baseColWidth="8" defaultColWidth="14.44" defaultRowHeight="15" customHeight="1"/>
<cols>
<col min="1" max="1" width="15" customWidth="1" style="15"/>
<col min="2" max="2" width="20" customWidth="1" style="15"/>
<col min="3" max="3" width="24" customWidth="1" style="15"/>
<col min="4" max="4" width="16" customWidth="1" style="15"/>
<col min="5" max="5" width="32" customWidth="1" style="15"/>
<col min="6" max="6" width="36" customWidth="1" style="15"/>
<col min="7" max="7" width="36" customWidth="1" style="15"/>
</cols>
<sheetData>
<row r="1" ht="33.75" customHeight="1" s="15"><c r="A1" s="30" t="inlineStr"><is><t>BOUNDARY VALUE ANALYSIS - EXECUTION LOG</t></is></c></row>
<row r="3" ht="28" customHeight="1" s="15"><c r="A3" s="31" t="inlineStr"><is><t>Cara menurunkan BVA (min, min+, nominal, max-, max, rumus 4n+1, dan contoh lengkap Triangle Classification dengan n=3) sudah dijelaskan di dokumen Modul 3 Bagian 2. Sheet ini hanya untuk mencatat test case BVA dari fiturmu sendiri, bukan untuk mengulang contoh tersebut.</t></is></c></row>
<row r="6" ht="20" customHeight="1" s="15">
<c r="A6" s="3" t="inlineStr"><is><t>Fitur yang diuji: Validasi Transaksi Checkout POS</t></is></c>
<c r="E6" s="3" t="inlineStr"><is><t>n = 3</t></is></c>
<c r="F6" s="3" t="inlineStr"><is><t>4n+1 = 13 kasus unik</t></is></c>
</row>
<row r="8" ht="25" customHeight="1" s="15">
<c r="A8" s="1" t="inlineStr"><is><t>Test Case ID</t></is></c>
<c r="B8" s="1" t="inlineStr"><is><t>Variabel</t></is></c>
<c r="C8" s="1" t="inlineStr"><is><t>Titik Batas (Min/Min+/Nominal/Max-/Max)</t></is></c>
<c r="D8" s="1" t="inlineStr"><is><t>Nilai Uji</t></is></c>
<c r="E8" s="1" t="inlineStr"><is><t>Kolom Lain (nominal)</t></is></c>
<c r="F8" s="1" t="inlineStr"><is><t>Hasil yang Diharapkan</t></is></c>
<c r="G8" s="1" t="inlineStr"><is><t>Hasil Aktual</t></is></c>
</row>
<row r="9" ht="20" customHeight="1" s="15">
<c r="A9" s="4" t="inlineStr"><is><t>TC-BVA-01</t></is></c><c r="B9" s="4" t="inlineStr"><is><t>Q1 (French Fries)</t></is></c><c r="C9" s="4" t="inlineStr"><is><t>Min (1)</t></is></c><c r="D9" s="4" t="inlineStr"><is><t>1</t></is></c><c r="E9" s="5" t="inlineStr"><is><t>Q2=25, C=1.000.000 (T=462.000)</t></is></c><c r="F9" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 538.000</t></is></c><c r="G9" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 538.000</t></is></c>
</row>
<row r="10" ht="20" customHeight="1" s="15">
<c r="A10" s="4" t="inlineStr"><is><t>TC-BVA-02</t></is></c><c r="B10" s="4" t="inlineStr"><is><t>Q1 (French Fries)</t></is></c><c r="C10" s="4" t="inlineStr"><is><t>Min+ (2)</t></is></c><c r="D10" s="4" t="inlineStr"><is><t>2</t></is></c><c r="E10" s="5" t="inlineStr"><is><t>Q2=25, C=1.000.000 (T=474.000)</t></is></c><c r="F10" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 526.000</t></is></c><c r="G10" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 526.000</t></is></c>
</row>
<row r="11" ht="20" customHeight="1" s="15">
<c r="A11" s="4" t="inlineStr"><is><t>TC-BVA-03</t></is></c><c r="B11" s="4" t="inlineStr"><is><t>Q1 (French Fries)</t></is></c><c r="C11" s="4" t="inlineStr"><is><t>Max- (49)</t></is></c><c r="D11" s="4" t="inlineStr"><is><t>49</t></is></c><c r="E11" s="5" t="inlineStr"><is><t>Q2=25, C=1.000.000 (T=1.038.000)</t></is></c><c r="F11" s="5" t="inlineStr"><is><t>Tolak: Uang diterima kurang dari total bayar</t></is></c><c r="G11" s="5" t="inlineStr"><is><t>Notifikasi error uang kurang muncul</t></is></c>
</row>
<row r="12" ht="20" customHeight="1" s="15">
<c r="A12" s="4" t="inlineStr"><is><t>TC-BVA-04</t></is></c><c r="B12" s="4" t="inlineStr"><is><t>Q1 (French Fries)</t></is></c><c r="C12" s="4" t="inlineStr"><is><t>Max (50)</t></is></c><c r="D12" s="4" t="inlineStr"><is><t>50</t></is></c><c r="E12" s="5" t="inlineStr"><is><t>Q2=25, C=1.000.000 (T=1.050.000)</t></is></c><c r="F12" s="5" t="inlineStr"><is><t>Tolak: Uang diterima kurang dari total bayar</t></is></c><c r="G12" s="5" t="inlineStr"><is><t>Notifikasi error uang kurang muncul</t></is></c>
</row>
<row r="13" ht="20" customHeight="1" s="15">
<c r="A13" s="4" t="inlineStr"><is><t>TC-BVA-05</t></is></c><c r="B13" s="4" t="inlineStr"><is><t>Q2 (Cafe Latte)</t></is></c><c r="C13" s="4" t="inlineStr"><is><t>Min (1)</t></is></c><c r="D13" s="4" t="inlineStr"><is><t>1</t></is></c><c r="E13" s="5" t="inlineStr"><is><t>Q1=25, C=1.000.000 (T=318.000)</t></is></c><c r="F13" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 682.000</t></is></c><c r="G13" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 682.000</t></is></c>
</row>
<row r="14" ht="20" customHeight="1" s="15">
<c r="A14" s="4" t="inlineStr"><is><t>TC-BVA-06</t></is></c><c r="B14" s="4" t="inlineStr"><is><t>Q2 (Cafe Latte)</t></is></c><c r="C14" s="4" t="inlineStr"><is><t>Min+ (2)</t></is></c><c r="D14" s="4" t="inlineStr"><is><t>2</t></is></c><c r="E14" s="5" t="inlineStr"><is><t>Q1=25, C=1.000.000 (T=336.000)</t></is></c><c r="F14" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 664.000</t></is></c><c r="G14" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 664.000</t></is></c>
</row>
<row r="15" ht="20" customHeight="1" s="15">
<c r="A15" s="4" t="inlineStr"><is><t>TC-BVA-07</t></is></c><c r="B15" s="4" t="inlineStr"><is><t>Q2 (Cafe Latte)</t></is></c><c r="C15" s="4" t="inlineStr"><is><t>Max- (49)</t></is></c><c r="D15" s="4" t="inlineStr"><is><t>49</t></is></c><c r="E15" s="5" t="inlineStr"><is><t>Q1=25, C=1.000.000 (T=1.182.000)</t></is></c><c r="F15" s="5" t="inlineStr"><is><t>Tolak: Uang diterima kurang dari total bayar</t></is></c><c r="G15" s="5" t="inlineStr"><is><t>Notifikasi error uang kurang muncul</t></is></c>
</row>
<row r="16" ht="20" customHeight="1" s="15">
<c r="A16" s="4" t="inlineStr"><is><t>TC-BVA-08</t></is></c><c r="B16" s="4" t="inlineStr"><is><t>Q2 (Cafe Latte)</t></is></c><c r="C16" s="4" t="inlineStr"><is><t>Max (50)</t></is></c><c r="D16" s="4" t="inlineStr"><is><t>50</t></is></c><c r="E16" s="5" t="inlineStr"><is><t>Q1=25, C=1.000.000 (T=1.200.000)</t></is></c><c r="F16" s="5" t="inlineStr"><is><t>Tolak: Uang diterima kurang dari total bayar</t></is></c><c r="G16" s="5" t="inlineStr"><is><t>Notifikasi error uang kurang muncul</t></is></c>
</row>
<row r="17" ht="20" customHeight="1" s="15">
<c r="A17" s="4" t="inlineStr"><is><t>TC-BVA-09</t></is></c><c r="B17" s="4" t="inlineStr"><is><t>C (Nominal Tunai)</t></is></c><c r="C17" s="4" t="inlineStr"><is><t>Min (Uang Pas)</t></is></c><c r="D17" s="4" t="inlineStr"><is><t>750000</t></is></c><c r="E17" s="5" t="inlineStr"><is><t>Q1=25, Q2=25 (T=750.000)</t></is></c><c r="F17" s="5" t="inlineStr"><is><t>Transaksi sukses uang pas, Kembalian Rp 0</t></is></c><c r="G17" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 0</t></is></c>
</row>
<row r="18" ht="20" customHeight="1" s="15">
<c r="A18" s="4" t="inlineStr"><is><t>TC-BVA-10</t></is></c><c r="B18" s="4" t="inlineStr"><is><t>C (Nominal Tunai)</t></is></c><c r="C18" s="4" t="inlineStr"><is><t>Min+ (Pas + 1rb)</t></is></c><c r="D18" s="4" t="inlineStr"><is><t>751000</t></is></c><c r="E18" s="5" t="inlineStr"><is><t>Q1=25, Q2=25 (T=750.000)</t></is></c><c r="F18" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 1.000</t></is></c><c r="G18" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 1.000</t></is></c>
</row>
<row r="19" ht="20" customHeight="1" s="15">
<c r="A19" s="4" t="inlineStr"><is><t>TC-BVA-11</t></is></c><c r="B19" s="4" t="inlineStr"><is><t>C (Nominal Tunai)</t></is></c><c r="C19" s="4" t="inlineStr"><is><t>Max- (2jt - 1rb)</t></is></c><c r="D19" s="4" t="inlineStr"><is><t>1999000</t></is></c><c r="E19" s="5" t="inlineStr"><is><t>Q1=25, Q2=25 (T=750.000)</t></is></c><c r="F19" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 1.249.000</t></is></c><c r="G19" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 1.249.000</t></is></c>
</row>
<row r="20" ht="20" customHeight="1" s="15">
<c r="A20" s="4" t="inlineStr"><is><t>TC-BVA-12</t></is></c><c r="B20" s="4" t="inlineStr"><is><t>C (Nominal Tunai)</t></is></c><c r="C20" s="4" t="inlineStr"><is><t>Max (Batas Kas 2jt)</t></is></c><c r="D20" s="4" t="inlineStr"><is><t>2000000</t></is></c><c r="E20" s="5" t="inlineStr"><is><t>Q1=25, Q2=25 (T=750.000)</t></is></c><c r="F20" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 1.250.000</t></is></c><c r="G20" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 1.250.000</t></is></c>
</row>
<row r="21" ht="20" customHeight="1" s="15">
<c r="A21" s="4" t="inlineStr"><is><t>TC-BVA-13</t></is></c><c r="B21" s="4" t="inlineStr"><is><t>All-Nominal</t></is></c><c r="C21" s="4" t="inlineStr"><is><t>Nominal</t></is></c><c r="D21" s="4" t="inlineStr"><is><t>25, 25, 1jt</t></is></c><c r="E21" s="5" t="inlineStr"><is><t>T = Rp 750.000</t></is></c><c r="F21" s="5" t="inlineStr"><is><t>Transaksi normal sukses, Kembalian Rp 250.000</t></is></c><c r="G21" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 250.000</t></is></c>
</row>
</sheetData>
</worksheet>
"@

# ----------------- SHEET 4: 03_EP -----------------
$s4Xml = @"
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<sheetPr><outlinePr summaryBelow="1" summaryRight="1"/><pageSetUpPr fitToPage="1"/></sheetPr>
<dimension ref="A1:F17"/>
<sheetViews><sheetView showGridLines="0" workbookViewId="0"><selection activeCell="A1" sqref="A1"/></sheetView></sheetViews>
<sheetFormatPr baseColWidth="8" defaultColWidth="14.44" defaultRowHeight="15" customHeight="1"/>
<cols>
<col min="1" max="1" width="15" customWidth="1" style="15"/>
<col min="2" max="2" width="28" customWidth="1" style="15"/>
<col min="3" max="3" width="32" customWidth="1" style="15"/>
<col min="4" max="4" width="14" customWidth="1" style="15"/>
<col min="5" max="5" width="35" customWidth="1" style="15"/>
<col min="6" max="6" width="45" customWidth="1" style="15"/>
</cols>
<sheetData>
<row r="1" ht="33.75" customHeight="1" s="15"><c r="A1" s="30" t="inlineStr"><is><t>EQUIVALENCE PARTITIONING - EXECUTION LOG</t></is></c></row>
<row r="3" ht="28" customHeight="1" s="15"><c r="A3" s="31" t="inlineStr"><is><t>Cara menentukan partisi EP (kenali input, kenali partisi, nyatakan rule, pilih perwakilan, lalu tulis test case, dan contoh lengkap Triangle Classification dengan 4 partisi) sudah dijelaskan di dokumen Modul 3 Bagian 3. Sheet ini hanya untuk mencatat test case EP dari fiturmu sendiri, bukan untuk mengulang contoh tersebut.</t></is></c></row>
<row r="6" ht="20" customHeight="1" s="15">
<c r="A6" s="3" t="inlineStr"><is><t>Fitur yang diuji: Validasi Keranjang Belanja &amp; Pembayaran Kasir POS (Weak Robust EP)</t></is></c>
</row>
<row r="8" ht="25" customHeight="1" s="15">
<c r="A8" s="1" t="inlineStr"><is><t>Test Case ID</t></is></c>
<c r="B8" s="1" t="inlineStr"><is><t>Partisi</t></is></c>
<c r="C8" s="1" t="inlineStr"><is><t>Rule / Rentang</t></is></c>
<c r="D8" s="1" t="inlineStr"><is><t>Valid?</t></is></c>
<c r="E8" s="1" t="inlineStr"><is><t>Nilai Uji (Perwakilan)</t></is></c>
<c r="F8" s="1" t="inlineStr"><is><t>Hasil yang Diharapkan</t></is></c>
</row>
<row r="9" ht="22" customHeight="1" s="15">
<c r="A9" s="4" t="inlineStr"><is><t>TC-EP-01</t></is></c><c r="B9" s="5" t="inlineStr"><is><t>P1: Valid Normal Transaction</t></is></c><c r="C9" s="5" t="inlineStr"><is><t>1 &lt;= Q1, Q2 &lt;= 50 dan C &gt; T</t></is></c><c r="D9" s="4" t="inlineStr"><is><t>Valid</t></is></c><c r="E9" s="5" t="inlineStr"><is><t>Q1=25, Q2=25, C=1.000.000 (T=750.000)</t></is></c><c r="F9" s="5" t="inlineStr"><is><t>Transaksi berhasil, Kembalian Rp 250.000, struk terbit, stok terpotong</t></is></c>
</row>
<row r="10" ht="22" customHeight="1" s="15">
<c r="A10" s="4" t="inlineStr"><is><t>TC-EP-02</t></is></c><c r="B10" s="5" t="inlineStr"><is><t>P2: Valid Exact Payment</t></is></c><c r="C10" s="5" t="inlineStr"><is><t>1 &lt;= Q1, Q2 &lt;= 50 dan C = T</t></is></c><c r="D10" s="4" t="inlineStr"><is><t>Valid</t></is></c><c r="E10" s="5" t="inlineStr"><is><t>Q1=10, Q2=10, C=300.000 (T=300.000)</t></is></c><c r="F10" s="5" t="inlineStr"><is><t>Transaksi berhasil uang pas, Kembalian Rp 0, struk terbit</t></is></c>
</row>
<row r="11" ht="22" customHeight="1" s="15">
<c r="A11" s="4" t="inlineStr"><is><t>TC-EP-03</t></is></c><c r="B11" s="5" t="inlineStr"><is><t>P3: Valid Input Underpaid</t></is></c><c r="C11" s="5" t="inlineStr"><is><t>1 &lt;= Q1, Q2 &lt;= 50 dan C &lt; T</t></is></c><c r="D11" s="4" t="inlineStr"><is><t>Valid</t></is></c><c r="E11" s="5" t="inlineStr"><is><t>Q1=25, Q2=25, C=500.000 (T=750.000)</t></is></c><c r="F11" s="5" t="inlineStr"><is><t>Tolak: Alert 'Uang diterima kurang dari total bayar!'</t></is></c>
</row>
<row r="12" ht="22" customHeight="1" s="15">
<c r="A12" s="4" t="inlineStr"><is><t>TC-EP-04</t></is></c><c r="B12" s="5" t="inlineStr"><is><t>P4: Invalid Q1 Below Minimum</t></is></c><c r="C12" s="5" t="inlineStr"><is><t>Q1 &lt; 1 (Q1 = 0 / negatif)</t></is></c><c r="D12" s="4" t="inlineStr"><is><t>Invalid</t></is></c><c r="E12" s="5" t="inlineStr"><is><t>Q1=0, Q2=25, C=1.000.000</t></is></c><c r="F12" s="5" t="inlineStr"><is><t>Tolak: Kuantitas item tidak boleh 0 (minimal 1)</t></is></c>
</row>
<row r="13" ht="22" customHeight="1" s="15">
<c r="A13" s="4" t="inlineStr"><is><t>TC-EP-05</t></is></c><c r="B13" s="5" t="inlineStr"><is><t>P5: Invalid Q1 Above Maximum</t></is></c><c r="C13" s="5" t="inlineStr"><is><t>Q1 &gt; 50 (Melebihi sisa stok fisik)</t></is></c><c r="D13" s="4" t="inlineStr"><is><t>Invalid</t></is></c><c r="E13" s="5" t="inlineStr"><is><t>Q1=55, Q2=25, C=1.500.000</t></is></c><c r="F13" s="5" t="inlineStr"><is><t>Tolak: Kuantitas melebihi sisa stok (maksimal 50 unit)</t></is></c>
</row>
<row r="14" ht="22" customHeight="1" s="15">
<c r="A14" s="4" t="inlineStr"><is><t>TC-EP-06</t></is></c><c r="B14" s="5" t="inlineStr"><is><t>P6: Invalid Q2 Below Minimum</t></is></c><c r="C14" s="5" t="inlineStr"><is><t>Q2 &lt; 1 (Q2 minus / tidak wajar)</t></is></c><c r="D14" s="4" t="inlineStr"><is><t>Invalid</t></is></c><c r="E14" s="5" t="inlineStr"><is><t>Q1=25, Q2=-1, C=1.000.000</t></is></c><c r="F14" s="5" t="inlineStr"><is><t>Tolak: Kuantitas minus dicegah form POS</t></is></c>
</row>
<row r="15" ht="22" customHeight="1" s="15">
<c r="A15" s="4" t="inlineStr"><is><t>TC-EP-07</t></is></c><c r="B15" s="5" t="inlineStr"><is><t>P7: Invalid Q2 Above Maximum</t></is></c><c r="C15" s="5" t="inlineStr"><is><t>Q2 &gt; 50 (Melebihi sisa stok fisik)</t></is></c><c r="D15" s="4" t="inlineStr"><is><t>Invalid</t></is></c><c r="E15" s="5" t="inlineStr"><is><t>Q1=25, Q2=60, C=1.500.000</t></is></c><c r="F15" s="5" t="inlineStr"><is><t>Tolak: Kuantitas melebihi sisa stok (maksimal 50 unit)</t></is></c>
</row>
<row r="16" ht="22" customHeight="1" s="15">
<c r="A16" s="4" t="inlineStr"><is><t>TC-EP-08</t></is></c><c r="B16" s="5" t="inlineStr"><is><t>P8: Invalid C Below Minimum</t></is></c><c r="C16" s="5" t="inlineStr"><is><t>C &lt;= 0 (Nominal uang negatif/nol)</t></is></c><c r="D16" s="4" t="inlineStr"><is><t>Invalid</t></is></c><c r="E16" s="5" t="inlineStr"><is><t>Q1=25, Q2=25, C=-50000</t></is></c><c r="F16" s="5" t="inlineStr"><is><t>Tolak: Nominal pembayaran tunai tidak valid / tombol bayar disable</t></is></c>
</row>
</sheetData>
</worksheet>
"@

# ----------------- SHEET 5: 04_Rancangan_dan_Eksekusi -----------------
$s5Xml = @"
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<sheetPr><outlinePr summaryBelow="1" summaryRight="1"/><pageSetUpPr fitToPage="1"/></sheetPr>
<dimension ref="A1:J31"/>
<sheetViews><sheetView showGridLines="0" workbookViewId="0"><selection activeCell="A1" sqref="A1"/></sheetView></sheetViews>
<sheetFormatPr baseColWidth="8" defaultColWidth="14.44" defaultRowHeight="15" customHeight="1"/>
<cols>
<col min="1" max="1" width="14" customWidth="1" style="15"/>
<col min="2" max="2" width="22" customWidth="1" style="15"/>
<col min="3" max="3" width="24" customWidth="1" style="15"/>
<col min="4" max="4" width="30" customWidth="1" style="15"/>
<col min="5" max="5" width="35" customWidth="1" style="15"/>
<col min="6" max="6" width="35" customWidth="1" style="15"/>
<col min="7" max="7" width="12" customWidth="1" style="15"/>
<col min="8" max="8" width="18" customWidth="1" style="15"/>
<col min="9" max="9" width="25" customWidth="1" style="15"/>
<col min="10" max="10" width="16" customWidth="1" style="15"/>
</cols>
<sheetData>
<row r="1" ht="33.75" customHeight="1" s="15"><c r="A1" s="30" t="inlineStr"><is><t>EXECUTION LOG - RANCANGAN DAN EKSEKUSI TEST CASE</t></is></c></row>
<row r="3" ht="28" customHeight="1" s="15"><c r="A3" s="31" t="inlineStr"><is><t>Ini sheet utama workbook. Pindahkan setiap Test Case ID dari 02_BVA dan 03_EP ke sini, satu baris per Test Case ID, lalu lengkapi kolom eksekusi (Hasil Aktual, Status, Tanggal/Tester, Tautan Bukti) setelah pengujian dijalankan pada sistem sungguhan. Prakondisi yang sama untuk seluruh baris cukup dicatat sekali di sini.</t></is></c></row>
<row r="6" ht="25" customHeight="1" s="15">
<c r="A6" s="3" t="inlineStr"><is><t>Prakondisi untuk seluruh test case: Server web Laragon (Apache &amp; MySQL) aktif; Database smartkasir memuat stok French Fries (50 unit @12.000) dan Cafe Latte (50 unit @18.000); Browser Chrome membuka http://localhost/SMARTKASIR/kasir/pos.php dalam sesi login kasir aktif.</t></is></c>
</row>
<row r="8" ht="25" customHeight="1" s="15">
<c r="A8" s="1" t="inlineStr"><is><t>ID Test Case</t></is></c>
<c r="B8" s="1" t="inlineStr"><is><t>Dasar/Kondisi</t></is></c>
<c r="C8" s="1" t="inlineStr"><is><t>Teknik &amp; Coverage</t></is></c>
<c r="D8" s="1" t="inlineStr"><is><t>Data Uji</t></is></c>
<c r="E8" s="1" t="inlineStr"><is><t>Hasil yang Diharapkan</t></is></c>
<c r="F8" s="1" t="inlineStr"><is><t>Hasil Aktual</t></is></c>
<c r="G8" s="1" t="inlineStr"><is><t>Status</t></is></c>
<c r="H8" s="1" t="inlineStr"><is><t>Tanggal / Tester</t></is></c>
<c r="I8" s="1" t="inlineStr"><is><t>Tautan Bukti</t></is></c>
<c r="J8" s="1" t="inlineStr"><is><t>ID Incident</t></is></c>
</row>
<row r="9" ht="20" customHeight="1" s="15">
<c r="A9" s="4" t="inlineStr"><is><t>TC-BVA-01</t></is></c><c r="B9" s="5" t="inlineStr"><is><t>RQ-004 (Tambah item)</t></is></c><c r="C9" s="5" t="inlineStr"><is><t>BVA (Min Q1)</t></is></c><c r="D9" s="5" t="inlineStr"><is><t>Q1=1, Q2=25, C=1.000.000</t></is></c><c r="E9" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 538.000</t></is></c><c r="F9" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 538.000</t></is></c><c r="G9" s="4" t="inlineStr"><is><t>Lulus</t></is></c><c r="H9" s="4" t="inlineStr"><is><t>2026-10-21 / Ferdi</t></is></c><c r="I9" s="4" t="inlineStr"><is><t>-</t></is></c><c r="J9" s="4" t="inlineStr"><is><t>-</t></is></c>
</row>
<row r="10" ht="20" customHeight="1" s="15">
<c r="A10" s="4" t="inlineStr"><is><t>TC-BVA-02</t></is></c><c r="B10" s="5" t="inlineStr"><is><t>RQ-004 (Tambah item)</t></is></c><c r="C10" s="5" t="inlineStr"><is><t>BVA (Min+ Q1)</t></is></c><c r="D10" s="5" t="inlineStr"><is><t>Q1=2, Q2=25, C=1.000.000</t></is></c><c r="E10" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 526.000</t></is></c><c r="F10" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 526.000</t></is></c><c r="G10" s="4" t="inlineStr"><is><t>Lulus</t></is></c><c r="H10" s="4" t="inlineStr"><is><t>2026-10-21 / Ferdi</t></is></c><c r="I10" s="4" t="inlineStr"><is><t>-</t></is></c><c r="J10" s="4" t="inlineStr"><is><t>-</t></is></c>
</row>
<row r="11" ht="20" customHeight="1" s="15">
<c r="A11" s="4" t="inlineStr"><is><t>TC-BVA-03</t></is></c><c r="B11" s="5" t="inlineStr"><is><t>RQ-008 (Uang kurang)</t></is></c><c r="C11" s="5" t="inlineStr"><is><t>BVA (Max- Q1)</t></is></c><c r="D11" s="5" t="inlineStr"><is><t>Q1=49, Q2=25, C=1.000.000</t></is></c><c r="E11" s="5" t="inlineStr"><is><t>Tolak: Uang diterima kurang dari total bayar</t></is></c><c r="F11" s="5" t="inlineStr"><is><t>Notifikasi error uang kurang muncul</t></is></c><c r="G11" s="4" t="inlineStr"><is><t>Lulus</t></is></c><c r="H11" s="4" t="inlineStr"><is><t>2026-10-21 / Ferdi</t></is></c><c r="I11" s="4" t="inlineStr"><is><t>-</t></is></c><c r="J11" s="4" t="inlineStr"><is><t>-</t></is></c>
</row>
<row r="12" ht="20" customHeight="1" s="15">
<c r="A12" s="4" t="inlineStr"><is><t>TC-BVA-04</t></is></c><c r="B12" s="5" t="inlineStr"><is><t>RQ-006 &amp; RQ-008</t></is></c><c r="C12" s="5" t="inlineStr"><is><t>BVA (Max Q1)</t></is></c><c r="D12" s="5" t="inlineStr"><is><t>Q1=50, Q2=25, C=1.000.000</t></is></c><c r="E12" s="5" t="inlineStr"><is><t>Tolak: Uang diterima kurang dari total bayar</t></is></c><c r="F12" s="5" t="inlineStr"><is><t>Notifikasi error uang kurang muncul</t></is></c><c r="G12" s="4" t="inlineStr"><is><t>Lulus</t></is></c><c r="H12" s="4" t="inlineStr"><is><t>2026-10-21 / Ferdi</t></is></c><c r="I12" s="4" t="inlineStr"><is><t>-</t></is></c><c r="J12" s="4" t="inlineStr"><is><t>-</t></is></c>
</row>
<row r="13" ht="20" customHeight="1" s="15">
<c r="A13" s="4" t="inlineStr"><is><t>TC-BVA-05</t></is></c><c r="B13" s="5" t="inlineStr"><is><t>RQ-004 (Tambah item)</t></is></c><c r="C13" s="5" t="inlineStr"><is><t>BVA (Min Q2)</t></is></c><c r="D13" s="5" t="inlineStr"><is><t>Q1=25, Q2=1, C=1.000.000</t></is></c><c r="E13" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 682.000</t></is></c><c r="F13" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 682.000</t></is></c><c r="G13" s="4" t="inlineStr"><is><t>Lulus</t></is></c><c r="H13" s="4" t="inlineStr"><is><t>2026-10-21 / Ferdi</t></is></c><c r="I13" s="4" t="inlineStr"><is><t>-</t></is></c><c r="J13" s="4" t="inlineStr"><is><t>-</t></is></c>
</row>
<row r="14" ht="20" customHeight="1" s="15">
<c r="A14" s="4" t="inlineStr"><is><t>TC-BVA-06</t></is></c><c r="B14" s="5" t="inlineStr"><is><t>RQ-004 (Tambah item)</t></is></c><c r="C14" s="5" t="inlineStr"><is><t>BVA (Min+ Q2)</t></is></c><c r="D14" s="5" t="inlineStr"><is><t>Q1=25, Q2=2, C=1.000.000</t></is></c><c r="E14" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 664.000</t></is></c><c r="F14" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 664.000</t></is></c><c r="G14" s="4" t="inlineStr"><is><t>Lulus</t></is></c><c r="H14" s="4" t="inlineStr"><is><t>2026-10-21 / Ferdi</t></is></c><c r="I14" s="4" t="inlineStr"><is><t>-</t></is></c><c r="J14" s="4" t="inlineStr"><is><t>-</t></is></c>
</row>
<row r="15" ht="20" customHeight="1" s="15">
<c r="A15" s="4" t="inlineStr"><is><t>TC-BVA-07</t></is></c><c r="B15" s="5" t="inlineStr"><is><t>RQ-008 (Uang kurang)</t></is></c><c r="C15" s="5" t="inlineStr"><is><t>BVA (Max- Q2)</t></is></c><c r="D15" s="5" t="inlineStr"><is><t>Q1=25, Q2=49, C=1.000.000</t></is></c><c r="E15" s="5" t="inlineStr"><is><t>Tolak: Uang diterima kurang dari total bayar</t></is></c><c r="F15" s="5" t="inlineStr"><is><t>Notifikasi error uang kurang muncul</t></is></c><c r="G15" s="4" t="inlineStr"><is><t>Lulus</t></is></c><c r="H15" s="4" t="inlineStr"><is><t>2026-10-21 / Ferdi</t></is></c><c r="I15" s="4" t="inlineStr"><is><t>-</t></is></c><c r="J15" s="4" t="inlineStr"><is><t>-</t></is></c>
</row>
<row r="16" ht="20" customHeight="1" s="15">
<c r="A16" s="4" t="inlineStr"><is><t>TC-BVA-08</t></is></c><c r="B16" s="5" t="inlineStr"><is><t>RQ-006 &amp; RQ-008</t></is></c><c r="C16" s="5" t="inlineStr"><is><t>BVA (Max Q2)</t></is></c><c r="D16" s="5" t="inlineStr"><is><t>Q1=25, Q2=50, C=1.000.000</t></is></c><c r="E16" s="5" t="inlineStr"><is><t>Tolak: Uang diterima kurang dari total bayar</t></is></c><c r="F16" s="5" t="inlineStr"><is><t>Notifikasi error uang kurang muncul</t></is></c><c r="G16" s="4" t="inlineStr"><is><t>Lulus</t></is></c><c r="H16" s="4" t="inlineStr"><is><t>2026-10-21 / Ferdi</t></is></c><c r="I16" s="4" t="inlineStr"><is><t>-</t></is></c><c r="J16" s="4" t="inlineStr"><is><t>-</t></is></c>
</row>
<row r="17" ht="20" customHeight="1" s="15">
<c r="A17" s="4" t="inlineStr"><is><t>TC-BVA-09</t></is></c><c r="B17" s="5" t="inlineStr"><is><t>RQ-007 (Uang pas)</t></is></c><c r="C17" s="5" t="inlineStr"><is><t>BVA (Min C)</t></is></c><c r="D17" s="5" t="inlineStr"><is><t>Q1=25, Q2=25, C=750.000</t></is></c><c r="E17" s="5" t="inlineStr"><is><t>Transaksi sukses uang pas, Kembalian Rp 0</t></is></c><c r="F17" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 0</t></is></c><c r="G17" s="4" t="inlineStr"><is><t>Lulus</t></is></c><c r="H17" s="4" t="inlineStr"><is><t>2026-10-21 / Ferdi</t></is></c><c r="I17" s="4" t="inlineStr"><is><t>-</t></is></c><c r="J17" s="4" t="inlineStr"><is><t>-</t></is></c>
</row>
<row r="18" ht="20" customHeight="1" s="15">
<c r="A18" s="4" t="inlineStr"><is><t>TC-BVA-10</t></is></c><c r="B18" s="5" t="inlineStr"><is><t>RQ-007 (Kembalian)</t></is></c><c r="C18" s="5" t="inlineStr"><is><t>BVA (Min+ C)</t></is></c><c r="D18" s="5" t="inlineStr"><is><t>Q1=25, Q2=25, C=751.000</t></is></c><c r="E18" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 1.000</t></is></c><c r="F18" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 1.000</t></is></c><c r="G18" s="4" t="inlineStr"><is><t>Lulus</t></is></c><c r="H18" s="4" t="inlineStr"><is><t>2026-10-21 / Ferdi</t></is></c><c r="I18" s="4" t="inlineStr"><is><t>-</t></is></c><c r="J18" s="4" t="inlineStr"><is><t>-</t></is></c>
</row>
<row r="19" ht="20" customHeight="1" s="15">
<c r="A19" s="4" t="inlineStr"><is><t>TC-BVA-11</t></is></c><c r="B19" s="5" t="inlineStr"><is><t>RQ-007 (Kembalian)</t></is></c><c r="C19" s="5" t="inlineStr"><is><t>BVA (Max- C)</t></is></c><c r="D19" s="5" t="inlineStr"><is><t>Q1=25, Q2=25, C=1.999.000</t></is></c><c r="E19" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 1.249.000</t></is></c><c r="F19" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 1.249.000</t></is></c><c r="G19" s="4" t="inlineStr"><is><t>Lulus</t></is></c><c r="H19" s="4" t="inlineStr"><is><t>2026-10-21 / Ferdi</t></is></c><c r="I19" s="4" t="inlineStr"><is><t>-</t></is></c><c r="J19" s="4" t="inlineStr"><is><t>-</t></is></c>
</row>
<row r="20" ht="20" customHeight="1" s="15">
<c r="A20" s="4" t="inlineStr"><is><t>TC-BVA-12</t></is></c><c r="B20" s="5" t="inlineStr"><is><t>RQ-007 (Batas kasir)</t></is></c><c r="C20" s="5" t="inlineStr"><is><t>BVA (Max C)</t></is></c><c r="D20" s="5" t="inlineStr"><is><t>Q1=25, Q2=25, C=2.000.000</t></is></c><c r="E20" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 1.250.000</t></is></c><c r="F20" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 1.250.000</t></is></c><c r="G20" s="4" t="inlineStr"><is><t>Lulus</t></is></c><c r="H20" s="4" t="inlineStr"><is><t>2026-10-21 / Ferdi</t></is></c><c r="I20" s="4" t="inlineStr"><is><t>-</t></is></c><c r="J20" s="4" t="inlineStr"><is><t>-</t></is></c>
</row>
<row r="21" ht="20" customHeight="1" s="15">
<c r="A21" s="4" t="inlineStr"><is><t>TC-BVA-13</t></is></c><c r="B21" s="5" t="inlineStr"><is><t>RQ-007 &amp; RQ-009</t></is></c><c r="C21" s="5" t="inlineStr"><is><t>BVA (Nominal)</t></is></c><c r="D21" s="5" t="inlineStr"><is><t>Q1=25, Q2=25, C=1.000.000</t></is></c><c r="E21" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 250.000, potong stok</t></is></c><c r="F21" s="5" t="inlineStr"><is><t>Transaksi sukses, stok terpotong, struk terbit</t></is></c><c r="G21" s="4" t="inlineStr"><is><t>Lulus</t></is></c><c r="H21" s="4" t="inlineStr"><is><t>2026-10-21 / Ferdi</t></is></c><c r="I21" s="4" t="inlineStr"><is><t>-</t></is></c><c r="J21" s="4" t="inlineStr"><is><t>-</t></is></c>
</row>
<row r="22" ht="20" customHeight="1" s="15">
<c r="A22" s="4" t="inlineStr"><is><t>TC-EP-01</t></is></c><c r="B22" s="5" t="inlineStr"><is><t>RQ-004 &amp; RQ-009</t></is></c><c r="C22" s="5" t="inlineStr"><is><t>EP (P1: Valid Normal)</t></is></c><c r="D22" s="5" t="inlineStr"><is><t>Q1=25, Q2=25, C=1.000.000</t></is></c><c r="E22" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 250.000, struk terbit</t></is></c><c r="F22" s="5" t="inlineStr"><is><t>Transaksi sukses, DB terupdate atomik</t></is></c><c r="G22" s="4" t="inlineStr"><is><t>Lulus</t></is></c><c r="H22" s="4" t="inlineStr"><is><t>2026-10-21 / Ferdi</t></is></c><c r="I22" s="4" t="inlineStr"><is><t>-</t></is></c><c r="J22" s="4" t="inlineStr"><is><t>-</t></is></c>
</row>
<row r="23" ht="20" customHeight="1" s="15">
<c r="A23" s="4" t="inlineStr"><is><t>TC-EP-02</t></is></c><c r="B23" s="5" t="inlineStr"><is><t>RQ-007 (Uang pas)</t></is></c><c r="C23" s="5" t="inlineStr"><is><t>EP (P2: Valid Exact)</t></is></c><c r="D23" s="5" t="inlineStr"><is><t>Q1=10, Q2=10, C=300.000</t></is></c><c r="E23" s="5" t="inlineStr"><is><t>Transaksi sukses uang pas, Kembalian Rp 0, struk terbit</t></is></c><c r="F23" s="5" t="inlineStr"><is><t>Transaksi sukses, Kembalian Rp 0</t></is></c><c r="G23" s="4" t="inlineStr"><is><t>Lulus</t></is></c><c r="H23" s="4" t="inlineStr"><is><t>2026-10-21 / Ferdi</t></is></c><c r="I23" s="4" t="inlineStr"><is><t>-</t></is></c><c r="J23" s="4" t="inlineStr"><is><t>-</t></is></c>
</row>
<row r="24" ht="20" customHeight="1" s="15">
<c r="A24" s="4" t="inlineStr"><is><t>TC-EP-03</t></is></c><c r="B24" s="5" t="inlineStr"><is><t>RQ-008 (Uang kurang)</t></is></c><c r="C24" s="5" t="inlineStr"><is><t>EP (P3: Valid Underpaid)</t></is></c><c r="D24" s="5" t="inlineStr"><is><t>Q1=25, Q2=25, C=500.000</t></is></c><c r="E24" s="5" t="inlineStr"><is><t>Tolak: Alert 'Uang diterima kurang dari total bayar!'</t></is></c><c r="F24" s="5" t="inlineStr"><is><t>Pesan penolakan muncul di UI POS</t></is></c><c r="G24" s="4" t="inlineStr"><is><t>Lulus</t></is></c><c r="H24" s="4" t="inlineStr"><is><t>2026-10-21 / Ferdi</t></is></c><c r="I24" s="4" t="inlineStr"><is><t>-</t></is></c><c r="J24" s="4" t="inlineStr"><is><t>-</t></is></c>
</row>
<row r="25" ht="20" customHeight="1" s="15">
<c r="A25" s="4" t="inlineStr"><is><t>TC-EP-04</t></is></c><c r="B25" s="5" t="inlineStr"><is><t>RQ-005 (Cegah Qty 0)</t></is></c><c r="C25" s="5" t="inlineStr"><is><t>EP (P4: Invalid Q1 Min)</t></is></c><c r="D25" s="5" t="inlineStr"><is><t>Q1=0, Q2=25, C=1.000.000</t></is></c><c r="E25" s="5" t="inlineStr"><is><t>Tolak: Kuantitas minimal 1 item</t></is></c><c r="F25" s="5" t="inlineStr"><is><t>Kuantitas keranjang otomatis minimal 1</t></is></c><c r="G25" s="4" t="inlineStr"><is><t>Lulus</t></is></c><c r="H25" s="4" t="inlineStr"><is><t>2026-10-21 / Ferdi</t></is></c><c r="I25" s="4" t="inlineStr"><is><t>-</t></is></c><c r="J25" s="4" t="inlineStr"><is><t>-</t></is></c>
</row>
<row r="26" ht="20" customHeight="1" s="15">
<c r="A26" s="4" t="inlineStr"><is><t>TC-EP-05</t></is></c><c r="B26" s="5" t="inlineStr"><is><t>RQ-006 (Batas maks stok)</t></is></c><c r="C26" s="5" t="inlineStr"><is><t>EP (P5: Invalid Q1 Max)</t></is></c><c r="D26" s="5" t="inlineStr"><is><t>Q1=55, Q2=25, C=1.500.000</t></is></c><c r="E26" s="5" t="inlineStr"><is><t>Tolak: Kuantitas dibatasi maksimal stok 50</t></is></c><c r="F26" s="5" t="inlineStr"><is><t>Sistem menolak kelebihan dan membatasi di 50</t></is></c><c r="G26" s="4" t="inlineStr"><is><t>Lulus</t></is></c><c r="H26" s="4" t="inlineStr"><is><t>2026-10-21 / Ferdi</t></is></c><c r="I26" s="4" t="inlineStr"><is><t>-</t></is></c><c r="J26" s="4" t="inlineStr"><is><t>-</t></is></c>
</row>
<row r="27" ht="20" customHeight="1" s="15">
<c r="A27" s="4" t="inlineStr"><is><t>TC-EP-06</t></is></c><c r="B27" s="5" t="inlineStr"><is><t>RQ-005 (Cegah minus)</t></is></c><c r="C27" s="5" t="inlineStr"><is><t>EP (P6: Invalid Q2 Min)</t></is></c><c r="D27" s="5" t="inlineStr"><is><t>Q1=25, Q2=-1, C=1.000.000</t></is></c><c r="E27" s="5" t="inlineStr"><is><t>Tolak: Kuantitas tidak valid</t></is></c><c r="F27" s="5" t="inlineStr"><is><t>Input minus dicegah oleh form &amp; script POS</t></is></c><c r="G27" s="4" t="inlineStr"><is><t>Lulus</t></is></c><c r="H27" s="4" t="inlineStr"><is><t>2026-10-21 / Ferdi</t></is></c><c r="I27" s="4" t="inlineStr"><is><t>-</t></is></c><c r="J27" s="4" t="inlineStr"><is><t>-</t></is></c>
</row>
<row r="28" ht="20" customHeight="1" s="15">
<c r="A28" s="4" t="inlineStr"><is><t>TC-EP-07</t></is></c><c r="B28" s="5" t="inlineStr"><is><t>RQ-006 (Batas maks stok)</t></is></c><c r="C28" s="5" t="inlineStr"><is><t>EP (P7: Invalid Q2 Max)</t></is></c><c r="D28" s="5" t="inlineStr"><is><t>Q1=25, Q2=60, C=1.500.000</t></is></c><c r="E28" s="5" t="inlineStr"><is><t>Tolak: Kuantitas dibatasi maksimal stok 50</t></is></c><c r="F28" s="5" t="inlineStr"><is><t>Sistem membatasi kuantitas tepat pada angka 50</t></is></c><c r="G28" s="4" t="inlineStr"><is><t>Lulus</t></is></c><c r="H28" s="4" t="inlineStr"><is><t>2026-10-21 / Ferdi</t></is></c><c r="I28" s="4" t="inlineStr"><is><t>-</t></is></c><c r="J28" s="4" t="inlineStr"><is><t>-</t></is></c>
</row>
<row r="29" ht="20" customHeight="1" s="15">
<c r="A29" s="4" t="inlineStr"><is><t>TC-EP-08</t></is></c><c r="B29" s="5" t="inlineStr"><is><t>RQ-008 (Uang negatif)</t></is></c><c r="C29" s="5" t="inlineStr"><is><t>EP (P8: Invalid C Min)</t></is></c><c r="D29" s="5" t="inlineStr"><is><t>Q1=25, Q2=25, C=-50000</t></is></c><c r="E29" s="5" t="inlineStr"><is><t>Tolak: Nominal uang tunai tidak valid</t></is></c><c r="F29" s="5" t="inlineStr"><is><t>Tombol checkout bayar dinonaktifkan</t></is></c><c r="G29" s="4" t="inlineStr"><is><t>Lulus</t></is></c><c r="H29" s="4" t="inlineStr"><is><t>2026-10-21 / Ferdi</t></is></c><c r="I29" s="4" t="inlineStr"><is><t>-</t></is></c><c r="J29" s="4" t="inlineStr"><is><t>-</t></is></c>
</row>
<row r="30" ht="24" customHeight="1" s="15">
<c r="A30" s="4" t="inlineStr"><is><t>TC-EXT-01</t></is></c><c r="B30" s="5" t="inlineStr"><is><t>RQ-002 (Registrasi)</t></is></c><c r="C30" s="5" t="inlineStr"><is><t>EP (Partisi Valid Akun Baru)</t></is></c><c r="D30" s="5" t="inlineStr"><is><t>Nama='Kasir Baru', Email='kasir.sukses@smartkasir.com'</t></is></c><c r="E30" s="5" t="inlineStr"><is><t>Akun tersimpan, redirect index.php?success=... (Alert HIJAU)</t></is></c><c r="F30" s="5" t="inlineStr"><is><t>Akun tersimpan tapi URL redirect ?error=... (Alert MERAH)</t></is></c><c r="G30" s="4" t="inlineStr"><is><t>Gagal</t></is></c><c r="H30" s="4" t="inlineStr"><is><t>2026-10-21 / Ferdi</t></is></c><c r="I30" s="5" t="inlineStr"><is><t>evidence_INC-SK-MOD3-01.png</t></is></c><c r="J30" s="4" t="inlineStr"><is><t>INC-SK-MOD3-01</t></is></c>
</row>
</sheetData>
</worksheet>
"@

# ----------------- SHEET 6: 05_Traceability -----------------
$s6Xml = @"
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<sheetPr><outlinePr summaryBelow="1" summaryRight="1"/><pageSetUpPr fitToPage="1"/></sheetPr>
<dimension ref="A1:F12"/>
<sheetViews><sheetView showGridLines="0" workbookViewId="0"><selection activeCell="A1" sqref="A1"/></sheetView></sheetViews>
<sheetFormatPr baseColWidth="8" defaultColWidth="14.44" defaultRowHeight="15" customHeight="1"/>
<cols>
<col min="1" max="1" width="28" customWidth="1" style="15"/>
<col min="2" max="2" width="30" customWidth="1" style="15"/>
<col min="3" max="3" width="38" customWidth="1" style="15"/>
<col min="4" max="4" width="16" customWidth="1" style="15"/>
<col min="5" max="5" width="18" customWidth="1" style="15"/>
<col min="6" max="6" width="50" customWidth="1" style="15"/>
</cols>
<sheetData>
<row r="1" ht="33.75" customHeight="1" s="15"><c r="A1" s="30" t="inlineStr"><is><t>TEST TRACEABILITY MATRIX</t></is></c></row>
<row r="3" ht="28" customHeight="1" s="15"><c r="A3" s="31" t="inlineStr"><is><t>TTM menghubungkan setiap requirement atau rule dari 01_Proyek dengan test case dan hasil aktualnya pada 04_Rancangan_dan_Eksekusi.</t></is></c></row>
<row r="5" ht="25" customHeight="1" s="15">
<c r="A5" s="1" t="inlineStr"><is><t>ID Kondisi / Requirement</t></is></c>
<c r="B5" s="1" t="inlineStr"><is><t>Coverage</t></is></c>
<c r="C5" s="1" t="inlineStr"><is><t>ID Test Case</t></is></c>
<c r="D5" s="1" t="inlineStr"><is><t>Status Ringkas</t></is></c>
<c r="E5" s="1" t="inlineStr"><is><t>ID Incident</t></is></c>
<c r="F5" s="1" t="inlineStr"><is><t>Catatan</t></is></c>
</row>
<row r="6" ht="24" customHeight="1" s="15">
<c r="A6" s="5" t="inlineStr"><is><t>RQ-004 (Tambah Produk Berstok)</t></is></c><c r="B6" s="5" t="inlineStr"><is><t>100% BVA &amp; EP [Min, Min+, Nominal]</t></is></c><c r="C6" s="5" t="inlineStr"><is><t>TC-BVA-01, TC-BVA-02, TC-BVA-05, TC-BVA-06, TC-EP-01</t></is></c><c r="D6" s="4" t="inlineStr"><is><t>100% Lulus</t></is></c><c r="E6" s="4" t="inlineStr"><is><t>-</t></is></c><c r="F6" s="5" t="inlineStr"><is><t>Kuantitas valid dapat ditambahkan ke keranjang dan dihitung subtotalnya</t></is></c>
</row>
<row r="7" ht="24" customHeight="1" s="15">
<c r="A7" s="5" t="inlineStr"><is><t>RQ-005 (Cegah Qty Nol/Minus)</t></is></c><c r="B7" s="5" t="inlineStr"><is><t>100% Weak Robust Invalid Min</t></is></c><c r="C7" s="5" t="inlineStr"><is><t>TC-EP-04, TC-EP-06</t></is></c><c r="D7" s="4" t="inlineStr"><is><t>100% Lulus</t></is></c><c r="E7" s="4" t="inlineStr"><is><t>-</t></is></c><c r="F7" s="5" t="inlineStr"><is><t>Sistem mencegah kuantitas 0 atau angka negatif masuk keranjang</t></is></c>
</row>
<row r="8" ht="24" customHeight="1" s="15">
<c r="A8" s="5" t="inlineStr"><is><t>RQ-006 (Batas Maksimal Stok Fisik)</t></is></c><c r="B8" s="5" t="inlineStr"><is><t>100% BVA &amp; Weak Robust Invalid Max</t></is></c><c r="C8" s="5" t="inlineStr"><is><t>TC-BVA-04, TC-BVA-08, TC-EP-05, TC-EP-07</t></is></c><c r="D8" s="4" t="inlineStr"><is><t>100% Lulus</t></is></c><c r="E8" s="4" t="inlineStr"><is><t>-</t></is></c><c r="F7" s="5" t="inlineStr"><is><t>Sistem membatasi kuantitas belanja maksimal sejumlah stok toko (50 unit)</t></is></c>
</row>
<row r="9" ht="24" customHeight="1" s="15">
<c r="A9" s="5" t="inlineStr"><is><t>RQ-007 (Kalkulasi Tagihan &amp; Kembalian)</t></is></c><c r="B9" s="5" t="inlineStr"><is><t>100% BVA Batas Uang &amp; EP Uang Pas</t></is></c><c r="C9" s="5" t="inlineStr"><is><t>TC-BVA-09, TC-BVA-10, TC-BVA-11, TC-BVA-12, TC-BVA-13, TC-EP-02</t></is></c><c r="D9" s="4" t="inlineStr"><is><t>100% Lulus</t></is></c><c r="E9" s="4" t="inlineStr"><is><t>-</t></is></c><c r="F9" s="5" t="inlineStr"><is><t>Kalkulasi otomatis total belanja dan nominal kembalian akurat hingga Rp 1</t></is></c>
</row>
<row r="10" ht="24" customHeight="1" s="15">
<c r="A10" s="5" t="inlineStr"><is><t>RQ-008 (Validasi Kecukupan Uang Tunai)</t></is></c><c r="B10" s="5" t="inlineStr"><is><t>100% BVA Batas Uang &amp; EP Uang Kurang</t></is></c><c r="C10" s="5" t="inlineStr"><is><t>TC-BVA-03, TC-BVA-07, TC-EP-03, TC-EP-08</t></is></c><c r="D10" s="4" t="inlineStr"><is><t>100% Lulus</t></is></c><c r="E10" s="4" t="inlineStr"><is><t>-</t></is></c><c r="F10" s="5" t="inlineStr"><is><t>Transaksi checkout ditolak bila nominal bayar tunai kurang dari total belanja</t></is></c>
</row>
<row r="11" ht="24" customHeight="1" s="15">
<c r="A11" s="5" t="inlineStr"><is><t>RQ-002 (Feedback Registrasi Akun Kasir)</t></is></c><c r="B11" s="5" t="inlineStr"><is><t>100% Equivalence Partitioning Kredensial</t></is></c><c r="C11" s="5" t="inlineStr"><is><t>TC-EXT-01</t></is></c><c r="D11" s="4" t="inlineStr"><is><t>Gagal</t></is></c><c r="E11" s="4" t="inlineStr"><is><t>INC-SK-MOD3-01</t></is></c><c r="F11" s="5" t="inlineStr"><is><t>Parameter URL salah kirim (?error= bukannya ?success=) memicu alert merah</t></is></c>
</row>
</sheetData>
</worksheet>
"@

# ----------------- SHEET 7: 06_Indeks_Incident -----------------
$s7Xml = @"
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<sheetPr><outlinePr summaryBelow="1" summaryRight="1"/><pageSetUpPr fitToPage="1"/></sheetPr>
<dimension ref="A1:G8"/>
<sheetViews><sheetView showGridLines="0" workbookViewId="0"><selection activeCell="A1" sqref="A1"/></sheetView></sheetViews>
<sheetFormatPr baseColWidth="8" defaultColWidth="14.44" defaultRowHeight="15" customHeight="1"/>
<cols>
<col min="1" max="1" width="18" customWidth="1" style="15"/>
<col min="2" max="2" width="18" customWidth="1" style="15"/>
<col min="3" max="3" width="55" customWidth="1" style="15"/>
<col min="4" max="4" width="14" customWidth="1" style="15"/>
<col min="5" max="5" width="14" customWidth="1" style="15"/>
<col min="6" max="6" width="16" customWidth="1" style="15"/>
<col min="7" max="7" width="30" customWidth="1" style="15"/>
</cols>
<sheetData>
<row r="1" ht="33.75" customHeight="1" s="15"><c r="A1" s="30" t="inlineStr"><is><t>INDEKS INCIDENT</t></is></c></row>
<row r="3" ht="28" customHeight="1" s="15"><c r="A3" s="31" t="inlineStr"><is><t>Gunakan satu baris untuk setiap defect yang benar-benar diamati saat eksekusi. Incident Report lengkap tetap dibuat pada dokumen terpisah, lihat file Incident_Report.docx.</t></is></c></row>
<row r="5" ht="25" customHeight="1" s="15">
<c r="A5" s="1" t="inlineStr"><is><t>ID Incident</t></is></c>
<c r="B5" s="1" t="inlineStr"><is><t>ID Test Case</t></is></c>
<c r="C5" s="1" t="inlineStr"><is><t>Ringkasan</t></is></c>
<c r="D5" s="1" t="inlineStr"><is><t>Severity</t></is></c>
<c r="E5" s="1" t="inlineStr"><is><t>Status</t></is></c>
<c r="F5" s="1" t="inlineStr"><is><t>Tanggal</t></is></c>
<c r="G5" s="1" t="inlineStr"><is><t>Tautan Bukti</t></is></c>
</row>
<row r="6" ht="35" customHeight="1" s="15">
<c r="A6" s="4" t="inlineStr"><is><t>INC-SK-MOD3-01</t></is></c>
<c r="B6" s="4" t="inlineStr"><is><t>TC-EXT-01</t></is></c>
<c r="C6" s="5" t="inlineStr"><is><t>Notifikasi registrasi kasir berhasil di-redirect dengan parameter ?error= sehingga menampilkan alert error merah bertanda seru</t></is></c>
<c r="D6" s="4" t="inlineStr"><is><t>Minor</t></is></c>
<c r="E6" s="4" t="inlineStr"><is><t>Resolved</t></is></c>
<c r="F6" s="4" t="inlineStr"><is><t>2026-10-21</t></is></c>
<c r="G6" s="5" t="inlineStr"><is><t>evidence_INC-SK-MOD3-01.png</t></is></c>
</row>
</sheetData>
</worksheet>
"@

# Open ZIP in Update mode and write the XML entries
$zip = [System.IO.Compression.ZipFile]::Open($destPath, [System.IO.Compression.ZipArchiveMode]::Update)

function ReplaceEntry($zipArchive, $entryName, $newContent) {
    $existing = $zipArchive.GetEntry($entryName)
    if ($existing) {
        $existing.Delete()
    }
    $newEntry = $zipArchive.CreateEntry($entryName)
    $writer = New-Object System.IO.StreamWriter($newEntry.Open(), [System.Text.Encoding]::UTF8)
    $writer.Write($newContent)
    $writer.Flush()
    $writer.Dispose()
    Write-Host "Updated entry: $entryName"
}

ReplaceEntry $zip "xl/worksheets/sheet2.xml" $s2Xml
ReplaceEntry $zip "xl/worksheets/sheet3.xml" $s3Xml
ReplaceEntry $zip "xl/worksheets/sheet4.xml" $s4Xml
ReplaceEntry $zip "xl/worksheets/sheet5.xml" $s5Xml
ReplaceEntry $zip "xl/worksheets/sheet6.xml" $s6Xml
ReplaceEntry $zip "xl/worksheets/sheet7.xml" $s7Xml

$zip.Dispose()
Write-Host "Successfully generated: $destPath"

