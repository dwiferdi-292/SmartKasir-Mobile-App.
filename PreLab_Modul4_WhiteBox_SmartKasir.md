# LAPORAN PRE-LAB MODUL 4
## SOFTWARE QUALITY ASSURANCE: WHITE-BOX TESTING & UNIT TESTING
**Laboratorium Informatika – Universitas Muhammadiyah Malang (UMM)**

---

### **Identitas Praktikan**
* **Nama Praktikan:** MUCHAMMAD DWI FERDI ILHAM ARIFIN
* **NIM:** 202310370311292
* **Kelas:** PKPL - B (Pengujian Kualitas Perangkat Lunak)
* **Aplikasi Kasus Uji:** SmartKasir (Point of Sale System)
* **Fitur yang Dianalisis:** Validasi Transaksi Checkout & Pembayaran Kasir POS (`process_checkout`)
* **Traceability Requirement:** RQ-007, RQ-008, RQ-009 (Feature Set: FS-004)

---

## 1. Ringkasan Pemahaman Materi Modul 4

### A. Konsep White-Box Testing (Structural / Glass-Box Testing)
1. **Definisi:** Metode pengujian perangkat lunak yang berfokus pada pemeriksaan struktur internal, alur logika kendali (*control flow*), dan kode sumber program. Penguji memiliki visibilitas penuh terhadap kode program.
2. **Tujuan:** Memverifikasi kebenaran eksekusi setiap baris pernyataan, kondisi percabangan (*branches*), mendeteksi *unreachable/dead code*, kesalahan logika internal, serta celah keamanan.
3. **Kelebihan & Keterbatasan:**
   * *Kelebihan:* Menemukan cacat yang terlewat oleh Black-Box testing, memastikan optimasi kode, dan memudahkan pengukuran *test completeness* melalui metrik cakupan (*code coverage*).
   * *Keterbatasan:* Membutuhkan keahlian pemrograman tinggi dan waktu lebih lama, serta tidak dapat menguji ekspektasi pengalaman pengguna (*user experience*) atau kesesuaian kebutuhan bisnis eksternal secara komprehensif tanpa Black-Box testing.

### B. Teknik Structure-Based Testing
1. **Basis Path Testing (Thomas McCabe):**
   * Mengukur kompleksitas logika menggunakan metrik *Cyclomatic Complexity* $V(G)$.
   * Rumus: 
     $$V(G) = E - N + 2P$$
     atau disederhanakan:
     $$V(G) = \text{Jumlah Titik Keputusan (Predicate Nodes)} + 1$$
   * Menentukan batas bawah jumlah jalur independen (*basis paths*) yang wajib diuji agar seluruh alur kode terlewati.
2. **Statement Testing & Coverage:**
   * Memastikan setiap baris pernyataan *executable* tereksekusi minimal satu kali.
   * Rumus:
     $$\text{Statement Coverage (\%)} = \frac{\text{Jumlah Executable Statements yang Dieksekusi}}{\text{Total Executable Statements}} \times 100\%$$
3. **Branch Testing & Coverage:**
   * Memastikan setiap cabang keputusan logika (baik kondisi bernilai `True` maupun `False`) terevaluasi selama pengujian.
   * Rumus:
     $$\text{Branch Coverage (\%)} = \frac{\text{Jumlah Decision Branches yang Dieksekusi}}{\text{Total Decision Branches}} \times 100\%$$

### C. Implementasi Unit Testing & Pola AAA (Arrange-Act-Assert)
* **Konsep:** Pengujian komponen kode terkecil (fungsi/metode/kelas) secara terisolasi.
* **Pola AAA:**
  * **Arrange:** Menyiapkan data masukan, inisialisasi objek, dan menyetel konfigurasi *Test Double* (mock/stub).
  * **Act:** Menjalankan satu operasi/fungsi utama yang sedang diuji.
  * **Assert:** Membandingkan nilai kembalian / perubahan status aktual terhadap ekspektasi yang telah didefinisikan.

### D. Taksonomi 5 Jenis Test Double
Untuk mengisolasi fungsi dari dependensi eksternal (database, API, jaringan):
1. **Dummy:** Objek placeholder tanpa implementasi, hanya untuk memenuhi parameter tanda tangan fungsi/konstruktor.
2. **Stub:** Menyediakan respon statis/hardcoded yang telah ditentukan sebelumnya untuk menggantikan panggilan eksternal.
3. **Mock:** Mengontrol respon sekaligus memverifikasi interaksi panggilan (apakah metode dipanggil, berapa kali, dan parameter apa yang dikirim).
4. **Fake:** Implementasi fungsional nyata namun disederhanakan untuk lingkungan uji (misalnya *in-memory repository*).
5. **Spy:** Membungkus objek riil untuk mengamati, mencatat riwayat pemanggilan dan parameter tanpa mengubah jalannya logika asli.

### E. Alur Kerja Git & GitHub untuk Unit Testing
1. Inisialisasi repositori lokal (`git init`), tambahkan file (`git add .`), dan buat commit awal (`git commit -m "Initial commit"`).
2. Hubungkan ke GitHub remote (`git remote add origin <url>`), set branch utama (`git branch -M main`), dan unggah kode (`git push -u origin main`).
3. Buat branch terisolasi untuk testing (`git checkout -b testing`).
4. Tulis skrip unit test, jalankan pengujian, lakukan commit dengan pesan deskriptif (misal: `Unit Testing`), dan push branch testing (`git push origin testing`).
5. Buat dan tautkan *GitHub Issues* sebagai bukti dokumentasi pengujian terintegrasi.

---

## 2. Lembar Kerja Pre-Lab: Analisis White-Box Testing Manual

### A. Fitur Terpilih & Cuplikan Kode Beranotasi
Fitur yang dianalisis adalah logika validasi transaksi kasir POS pada SmartKasir (diadaptasi dari `kasir/process.php`):

```php
function process_checkout($cartData, $totalAmount, $paidAmount, $paymentMethod, $inventoryStock) {
    // S1: Inisialisasi struktur array hasil respon transaksi
    $res = ['status' => false, 'message' => '', 'change' => 0, 'invoice' => '']; // S1

    // S2: Titik Keputusan 1 (D1) - Validasi apakah keranjang belanja kosong
    if (empty($cartData)) {                                                 // S2
        $res['message'] = "Keranjang belanja kosong!";                     // S3
        return $res;                                                        // S4
    }

    // S5: Titik Keputusan 2 (D2) - Validasi kecukupan uang pembayaran tunai
    if ($paidAmount < $totalAmount) {                                       // S5
        $res['message'] = "Uang diterima kurang dari total bayar!";         // S6
        return $res;                                                        // S7
    }

    // S8: Titik Keputusan 3 (D3) - Validasi stok gudang untuk tiap item
    $hasStock = true;
    foreach ($cartData as $item) {
        if ($item['qty'] > ($inventoryStock[$item['id']] ?? 0)) {
            $hasStock = false;
            break;
        }
    }
    if (!$hasStock) {                                                       // S8
        $res['message'] = "Stok produk tidak mencukupi!";                   // S9
        return $res;                                                        // S10
    }

    // S11: Titik Keputusan 4 (D4) - Formula perhitungan uang kembalian
    if ($paymentMethod === "TUNAI") {                                       // S11
        $res['change'] = $paidAmount - $totalAmount;                        // S12
    } else {
        $res['change'] = 0;                                                 // S13
    }

    // S14: Generate nomor invoice unik
    $res['invoice'] = "INV-" . date("YmdHis");                              // S14

    // S15: Set status transaksi berhasil
    $res['status'] = true;                                                  // S15
    $res['message'] = "Transaksi berhasil diproses!";                       // S16

    // S17: Kembalikan hasil respon transaksi
    return $res;                                                            // S17
}
```

---

### B. Control Flow Graph (CFG) & Cyclomatic Complexity ($V(G)$)

#### **1. Titik Keputusan (Decision Points):**
* **D1 (S2):** `empty($cartData)`
* **D2 (S5):** `$paidAmount < $totalAmount`
* **D3 (S8):** `!$hasStock`
* **D4 (S11):** `$paymentMethod === "TUNAI"`

#### **2. Perhitungan Cyclomatic Complexity McCabe:**
* Berdasarkan Decision Points:
  $$V(G) = P + 1 = 4 + 1 = 5$$
* Berdasarkan Graf Alir (Edges $E = 13$, Nodes $N = 10$, Components $P = 1$):
  $$V(G) = E - N + 2P = 13 - 10 + 2(1) = 5$$

Nilai $V(G) = 5$ menunjukkan terdapat **5 jalur eksekusi independen (*Basis Paths*)** yang wajib diuji.

#### **3. Daftar Basis Paths:**
* **Path 1:** $S1 \rightarrow S2 \text{ (True)} \rightarrow S3 \rightarrow S4 \text{ (Exit)}$
* **Path 2:** $S1 \rightarrow S2 \text{ (False)} \rightarrow S5 \text{ (True)} \rightarrow S6 \rightarrow S7 \text{ (Exit)}$
* **Path 3:** $S1 \rightarrow S2 \text{ (False)} \rightarrow S5 \text{ (False)} \rightarrow S8 \text{ (True)} \rightarrow S9 \rightarrow S10 \text{ (Exit)}$
* **Path 4:** $S1 \rightarrow S2 \text{ (False)} \rightarrow S5 \text{ (False)} \rightarrow S8 \text{ (False)} \rightarrow S11 \text{ (True)} \rightarrow S12 \rightarrow S14 \rightarrow S15 \rightarrow S16 \rightarrow S17 \text{ (Exit)}$
* **Path 5:** $S1 \rightarrow S2 \text{ (False)} \rightarrow S5 \text{ (False)} \rightarrow S8 \text{ (False)} \rightarrow S11 \text{ (False)} \rightarrow S13 \rightarrow S14 \rightarrow S15 \rightarrow S16 \rightarrow S17 \text{ (Exit)}$

---

### C. Analisis Statement Coverage

* **Total Executable Statements:** 17 baris ($S1$ s.d. $S17$)

#### **Perancangan Test Case Statement Coverage:**
1. **Test Case 1 (Keranjang Kosong):**
   * *Input:* `cartData = []`, `totalAmount = 0`, `paidAmount = 50000`, `paymentMethod = 'TUNAI'`, `inventoryStock = [1=>50]`
   * *Execution Path:* $S1 \rightarrow S2 \rightarrow S3 \rightarrow S4$
   * *Covered Statements:* $\{S1, S2, S3, S4\}$
2. **Test Case 2 (Uang Tunai Kurang):**
   * *Input:* `cartData = [['id'=>1, 'qty'=>2, 'price'=>12000]]`, `totalAmount = 24000`, `paidAmount = 20000`, `paymentMethod = 'TUNAI'`, `inventoryStock = [1=>50]`
   * *Execution Path:* $S1 \rightarrow S2 \rightarrow S5 \rightarrow S6 \rightarrow S7$
   * *Covered Statements:* $\{S1, S2, S5, S6, S7\}$
3. **Test Case 3 (Kuantitas Melebihi Stok Gudang):**
   * *Input:* `cartData = [['id'=>1, 'qty'=>10, 'price'=>12000]]`, `totalAmount = 120000`, `paidAmount = 150000`, `paymentMethod = 'TUNAI'`, `inventoryStock = [1=>5]`
   * *Execution Path:* $S1 \rightarrow S2 \rightarrow S5 \rightarrow S8 \rightarrow S9 \rightarrow S10$
   * *Covered Statements:* $\{S1, S2, S5, S8, S9, S10\}$
4. **Test Case 4 (Transaksi Tunai Berhasil dengan Kembalian):**
   * *Input:* `cartData = [['id'=>1, 'qty'=>2, 'price'=>12000]]`, `totalAmount = 24000`, `paidAmount = 50000`, `paymentMethod = 'TUNAI'`, `inventoryStock = [1=>50]`
   * *Execution Path:* $S1 \rightarrow S2 \rightarrow S5 \rightarrow S8 \rightarrow S11 \rightarrow S12 \rightarrow S14 \rightarrow S15 \rightarrow S16 \rightarrow S17$
   * *Covered Statements:* $\{S1, S2, S5, S8, S11, S12, S14, S15, S16, S17\}$
5. **Test Case 5 (Transaksi Non-Tunai / QRIS Berhasil):**
   * *Input:* `cartData = [['id'=>1, 'qty'=>1, 'price'=>18000]]`, `totalAmount = 18000`, `paidAmount = 18000`, `paymentMethod = 'QRIS'`, `inventoryStock = [1=>50]`
   * *Execution Path:* $S1 \rightarrow S2 \rightarrow S5 \rightarrow S8 \rightarrow S11 \rightarrow S13 \rightarrow S14 \rightarrow S15 \rightarrow S16 \rightarrow S17$
   * *Covered Statements:* $\{S1, S2, S5, S8, S11, S13, S14, S15, S16, S17\}$

#### **Kalkulasi Statement Coverage:**
$$\text{Union Set} = \{S1, S2, S3, S4, S5, S6, S7, S8, S9, S10, S11, S12, S13, S14, S15, S16, S17\}$$
$$\text{Statement Coverage} = \frac{17}{17} \times 100\% = \mathbf{100\%}$$

---

### D. Analisis Branch Coverage

* **Total Titik Keputusan:** 4
* **Total Cabang Keputusan:** 8 Cabang ($B1$ s.d. $B8$)

| Titik Keputusan | Kondisi Logika | Cabang True | Cabang False |
| :--- | :--- | :--- | :--- |
| **D1 (S2)** | `empty($cartData)` | **B1:** Tolak (Keranjang Kosong) | **B2:** Lanjut ke evaluasi D2 |
| **D2 (S5)** | `$paidAmount < $totalAmount` | **B3:** Tolak (Uang Kurang) | **B4:** Lanjut ke evaluasi D3 |
| **D3 (S8)** | `!$hasStock` | **B5:** Tolak (Stok Kurang) | **B6:** Lanjut ke evaluasi D4 |
| **D4 (S11)** | `$paymentMethod === "TUNAI"` | **B7:** Hitung Kembalian Uang Tunai | **B8:** Kembalian = 0 (Non-Tunai) |

#### **Matriks Eksekusi Cabang per Test Case:**

| Test Case | Kondisi yang Diuji | Alur Percabangan yang Dilalui | Cabang Tereksekusi |
| :--- | :--- | :--- | :--- |
| **TC-01** | Keranjang Kosong | $D1 \rightarrow \text{True}$ (B1) | $\{B1\}$ |
| **TC-02** | Uang Tunai Kurang | $D1 \rightarrow \text{False}$ (B2), $D2 \rightarrow \text{True}$ (B3) | $\{B2, B3\}$ |
| **TC-03** | Stok Gudang Kurang | $D1 \rightarrow \text{False}$ (B2), $D2 \rightarrow \text{False}$ (B4), $D3 \rightarrow \text{True}$ (B5) | $\{B2, B4, B5\}$ |
| **TC-04** | Pembayaran Tunai Valid | $D1 \rightarrow \text{False}$ (B2), $D2 \rightarrow \text{False}$ (B4), $D3 \rightarrow \text{False}$ (B6), $D4 \rightarrow \text{True}$ (B7) | $\{B2, B4, B6, B7\}$ |
| **TC-05** | Pembayaran QRIS Valid | $D1 \rightarrow \text{False}$ (B2), $D2 \rightarrow \text{False}$ (B4), $D3 \rightarrow \text{False}$ (B6), $D4 \rightarrow \text{False}$ (B8) | $\{B2, B4, B6, B8\}$ |

#### **Kalkulasi Branch Coverage:**
$$\text{Union Branches} = \{B1\} \cup \{B2, B3\} \cup \{B2, B4, B5\} \cup \{B2, B4, B6, B7\} \cup \{B2, B4, B6, B8\} = \{B1, B2, B3, B4, B5, B6, B7, B8\}$$
$$\text{Branch Coverage} = \frac{8}{8} \times 100\% = \mathbf{100\%}$$

---

## 3. Kesimpulan & Kesiapan Menuju Lab Session
1. **Analisis Struktur:** Logika fungsi `process_checkout` terbukti memenuhi 100% *Statement Coverage* dan 100% *Branch Coverage* melalui perancangan 5 kasus uji independen berbasis kompleksitas McCabe ($V(G) = 5$).
2. **Kesiapan Unit Testing (Lab Session):**
   * *Framework:* Menggunakan **PHPUnit** pada lingkungan PHP 8.2 (Laragon).
   * *Pola AAA:* Struktur pengujian dipisahkan secara tegas antara setup data (Arrange), eksekusi fungsi (Act), dan assert return value (Assert).
   * *Penerapan Test Double:*
     * **Stub:** Untuk menggantikan akses tabel database MySQL inventaris dengan data stok statis in-memory.
     * **Mock:** Untuk memvalidasi interaksi pemanggilan method `updateStock()` dan `logMutation()` pada objek dependensi.
   * *Git/GitHub Evidence:* Pembuatan branch `testing`, commit berlabel `Unit Testing`, dan penautan ke GitHub Issues.

