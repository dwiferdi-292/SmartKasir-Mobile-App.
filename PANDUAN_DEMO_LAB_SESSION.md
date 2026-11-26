# PANDUAN LENGKAP DEMO PRAKTIKUM (LAB SESSION) &bull; MODUL 4
## SOFTWARE QUALITY ASSURANCE: WHITE-BOX TESTING & UNIT TESTING
**Laboratorium Informatika – Universitas Muhammadiyah Malang**

---

### **Identitas Praktikan**
* **Nama Praktikan:** MUCHAMMAD DWI FERDI ILHAM ARIFIN
* **NIM:** 202310370311292
* **Kelas:** PKPL - B
* **Aplikasi:** SmartKasir (Point of Sale System)
* **Fitur Uji:** Checkout & Payment Validation (FS-004 / RQ-007, RQ-008, RQ-009)
* **Repositori GitHub:** [https://github.com/dwiferdi-292/SmartKasir-Mobile-App.](https://github.com/dwiferdi-292/SmartKasir-Mobile-App.)
* **Branch Testing:** `testing`
* **GitHub Issue:** [https://github.com/dwiferdi-292/SmartKasir-Mobile-App./issues/1](https://github.com/dwiferdi-292/SmartKasir-Mobile-App./issues/1)

---

## 1. Persiapan Cepat Sebelum Maju Demo (Checklist 1 Menit)
Buka 3 jendela berikut di laptop Anda sebelum memanggil asisten:
1. **Jendela 1: Terminal PowerShell**
   Sudah masuk ke direktori: `cd C:\laragon\www\SMARTKASIR`
   Siap menjalankan: `php test_checkout.php`
2. **Jendela 2: VS Code / Text Editor**
   Membuka file `test_checkout.php` pada baris 15 (fungsi yang diuji) dan baris 40 (Test Double & Unit Tests).
3. **Jendela 3: Browser (Google Chrome)**
   Tab 1: Repositori GitHub branch `testing` (menampilkan file `test_checkout.php`).
   Tab 2: Halaman GitHub Issue #1 (`White-Box Testing & Unit Testing Implementation`).
   Tab 3: Laporan PDF/HTML `modul4_prelab.html`.

---

## 2. Skrip Presentasi Demo di Depan Aslab (Tinggal Dibaca/Dihafalkan)

### A. Pembukaan (30 Detik)
> *"Selamat pagi/siang Mas/Mbak Aslab. Saya Muchammad Dwi Ferdi Ilham Arifin, NIM 202310370311292 dari kelas PKPL-B.*
> *Pada Lab Session Modul 4 ini, saya mengimplementasikan White-Box Testing dan Unit Testing pada aplikasi **SmartKasir (Point of Sale)**, khususnya pada fitur **Validasi Checkout dan Pembayaran Kasir (FS-004)**."*

### B. Penjelasan Analisis White-Box (1 Menit)
> *"Pada fungsi `process_checkout`, saya menganalisis struktur internal kodenya:*
> 1. *Terdapat **12 executable statements** (S1 sampai S12).*
> 2. *Terdapat **4 titik keputusan logika (Decision Points D1 s.d. D4)**, yaitu validasi keranjang kosong, validasi uang tunai kurang, validasi stok gudang, dan penentuan metode pembayaran tunai vs QRIS.*
> 3. *Berdasarkan rumus McCabe, Kompleksitas Siklomatisnya adalah: $V(G) = P + 1 = 4 + 1 = 5$. Artinya terdapat **5 jalur independen (Basis Paths)** yang wajib diuji untuk memastikan 100% alur teruji.*
> 4. *Dengan 5 skenario uji yang dirancang, sistem mencapai **100% Statement Coverage** dan **100% Branch Coverage (8 dari 8 cabang True/False)**."*

### C. Live Demo Eksekusi Unit Test (30 Detik)
*(Ketik di terminal: `php test_checkout.php` lalu tekan Enter)*
> *"Berikut saya demokan eksekusi script unit testing menggunakan engine CLI PHP 8.2:*
> *(Tunjukkan outputnya)*
> *Dapat dilihat Mas/Mbak, seluruh 7 pengujian berstatus **PASS (100% Success Rate)**.*
> * *TC-01 sampai TC-05 menguji kelima Basis Path untuk mencapai 100% Statement & Branch Coverage.*
> * *TD-01 dan TD-02 mendemonstrasikan implementasi dua jenis **Test Double**, yaitu **Stub** dan **Mock**."*

### D. Penjelasan Pola AAA & Test Double (1 Menit)
*(Tunjukkan kodingan di VS Code / GitHub)*
> 1. ***Pola AAA (Arrange-Act-Assert):***
>    * *Pada setiap method test (contohnya `testValidCashPayment`), saya memisahkan secara tegas:*
>      * **Arrange:** Menyiapkan data keranjang belanja Rp 24.000, uang tunai Rp 50.000, dan stok 10.
>      * **Act:** Memanggil fungsi `process_checkout()`.
>      * **Assert:** Memvalidasi bahwa hasilnya mengembalikan status 'Sukses' dan uang kembalian Rp 26.000.*
> 2. ***Test Double 1 (STUB):***
>    * *Class `InventoryRepositoryStub` digunakan untuk mengisolasi query database MySQL pada tabel `inventory`.*
>    * *Stub mengembalikan nilai stok statis yang ditentukan di awal (predefined) tanpa query ke MySQL. Ini membuat pengujian deterministik, cepat, dan tidak merusak database fisik.*
> 3. ***Test Double 2 (MOCK):***
>    * *Class `StockMutationLoggerMock` digunakan untuk memverifikasi interaksi pemanggilan method pencatatan audit log mutasi stok.*
>    * *Mock memvalidasi apakah method `logMutation` dipanggil tepat 1 kali (`verifyCalledTimes(1)`) dengan parameter Item ID dan Qty yang tepat saat transaksi berhasil."*

### E. Penunjukan Bukti Git & GitHub (30 Detik)
*(Tunjukkan tab browser)*
> *"Sesuai rubrik penilaian, seluruh kode ini di-commit pada branch terpisah bernama **`testing`** dengan commit message **`Unit Testing`**, dan tugas pengujian ini dikelola melalui **GitHub Issues #1** di repositori resmi saya."*

---

## 3. Bank Soal & Kunci Jawaban Tanya-Jawab Aslab (Q&A)

### Q1: "Kenapa harus pakai White-Box Testing padahal di Modul 2 & 3 sudah pakai Black-Box?"
**Jawaban:**
> *"Black-Box Testing hanya menguji antarmuka luar berdasarkan input dan output tanpa tahu struktur kode di dalamnya. Celah seperti **dead code (kode yang tidak pernah dieksekusi)**, kesalahan percabangan kondisi batas, atau error penanganan logika internal hanya bisa diidentifikasi melalui White-Box Testing dengan memeriksa langsung alur kode sumbernya."*

### Q2: "Apa bedanya Statement Coverage dengan Branch Coverage? Mana yang lebih kuat?"
**Jawaban:**
> *"**Statement Coverage** hanya mengukur apakah setiap baris kode pernyataan pernah dieksekusi minimal sekali.*
> *Sedangkan **Branch Coverage** memastikan setiap cabang keputusan (baik bernilai `True` maupun `False`) keduanya pernah dievaluasi.*
> *Branch Coverage **jauh lebih kuat dan ketat** daripada Statement Coverage, karena kode yang memiliki 100% Statement Coverage belum tentu 100% Branch Coverage jika ada kondisi `if` tanpa `else` yang cabang `False`-nya tidak pernah dilewati."*

### Q3: "Bagaimana kamu menghitung Cyclomatic Complexity V(G) = 5?"
**Jawaban:**
> *"Menggunakan rumus Thomas McCabe:*
> *Melalui Predicate Nodes (titik keputusan kondisional): $V(G) = P + 1$. Fungsi saya memiliki 4 titik keputusan (`if empty cart`, `elseif cash < total`, `elseif stock <= 0`, `if method == TUNAI`), sehingga $V(G) = 4 + 1 = 5$.*
> *Atau melalui Control Flow Graph: $V(G) = E - N + 2P = 13 \text{ Edges} - 10 \text{ Nodes} + 2(1) = 5$. Nilai 5 ini berarti wajib ada minimal 5 test case untuk menguji seluruh jalur independen."*

### Q4: "Apa perbedaan nyata antara Stub dan Mock pada pengujianmu?"
**Jawaban:**
> *"Perbedaannya ada pada **fokus pengujiannya**:*
> * * **Stub** berfokus pada **State Verification (Data Masukan)**: Stub hanya bertugas menyediakan data balasan tetap (statis) tanpa mengecek apakah method-nya dipanggil berapa kali. Pada kode saya, `InventoryRepositoryStub` hanya menyuplai angka stok tanpa mengecek interaksi.*
> * * **Mock** berfokus pada **Behavior Verification (Interaksi Panggilan)**: Mock tidak hanya menyimulasikan objek, tapi secara aktif memvalidasi interaksi: apakah method `logMutation` benar-benar dipanggil? Berapa kali dipanggil? Dan apa argumen yang dikirimkan? Pada kode saya dibuktikan dengan `$mock->verifyCalledTimes(1)` dan `$mock->verifyCalledWith(1, 3)`."*

### Q5: "Kenapa tidak langsung koneksi ke database MySQL asli saja saat unit testing?"
**Jawaban:**
> *"Prinsip utama Unit Testing adalah **Isolasi dan Kecepatan (Fast & Deterministic)**:*
> 1. *Koneksi ke database fisik membuat unit test menjadi lambat karena ada I/O disk dan jaringan.*
> 2. *Jika database mati, unit test akan gagal padahal logika fungsinya benar.*
> 3. *Menghindari polusi data uji (kotornya tabel database produksi oleh data testing palsu).*
> 4. *Dengan Test Double, kita bisa menyimulasikan kondisi ekstrem (seperti stok tiba-tiba 0 atau koneksi gagal) kapan saja dengan mudah."*

---

## 4. Rangkuman Perintah Terminal untuk Demo
```powershell
# 1. Pindah ke direktori proyek SmartKasir
cd C:\laragon\www\SMARTKASIR

# 2. Jalankan pengujian unit dan demo Test Double
php test_checkout.php

# 3. Cek branch Git saat ini (harus di branch testing)
git branch

# 4. Cek riwayat commit untuk menunjukkan commit "Unit Testing"
git log --oneline -n 3
```
