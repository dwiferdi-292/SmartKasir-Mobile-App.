# PANDUAN LENGKAP DEMO PRAKTIKUM (LAB SESSION) &bull; MODUL 4
## SOFTWARE QUALITY ASSURANCE: WHITE-BOX TESTING & UNIT TESTING (ALL 8 FEATURES)
**Laboratorium Informatika – Universitas Muhammadiyah Malang**

---

### **Identitas Praktikan**
* **Nama Praktikan:** MUCHAMMAD DWI FERDI ILHAM ARIFIN
* **NIM:** 202310370311292
* **Kelas:** PKPL - B
* **Aplikasi:** SmartKasir (Point of Sale System)
* **Cakupan Pengujian:** **SELURUH 8 FITUR SISTEM SMARTKASIR (28 Kasus Uji Unit Test)**
* **Repositori GitHub:** [https://github.com/dwiferdi-292/SmartKasir-Mobile-App.](https://github.com/dwiferdi-292/SmartKasir-Mobile-App.)
* **Branch Testing:** `testing`
* **GitHub Issue:** [https://github.com/dwiferdi-292/SmartKasir-Mobile-App./issues/1](https://github.com/dwiferdi-292/SmartKasir-Mobile-App./issues/1)

---

## 1. Daftar Lengkap 8 Fitur yang Diuji (Jawaban untuk Aslab)

Jika Aslab bertanya: *"Apa saja seluruh fitur yang kamu uji di kodinganmu?"*, sebutkan 8 fitur ini:

| No | Kode Fitur | Nama Fitur & Logika yang Diuji | Jumlah Test Case | Status |
| :-: | :--- | :--- | :-: | :-: |
| **1** | **FS-001** | **Autentikasi & Login Kasir:** Validasi form kosong, email tak terdaftar, password salah, dan login sukses role kasir. | 4 TC | **PASS** |
| **2** | **FS-002** | **Pencarian Produk & Katalog POS:** Kata kunci kosong (tampilkan semua), kata kunci cocok ('latte'), dan produk tidak ada. | 3 TC | **PASS** |
| **3** | **FS-003** | **Batas Kuantitas Keranjang & Stok:** Kuantitas 0, stok toko habis (0), kuantitas melebihi stok, dan kuantitas valid. | 4 TC | **PASS** |
| **4** | **FS-004** | **Validasi Checkout & Pembayaran Kasir (Fitur Inti White-Box):** Keranjang kosong, uang tunai kurang, stok habis, bayar tunai dengan kembalian, dan bayar QRIS pas. | 5 TC | **PASS** |
| **5** | **FS-005** | **Filter Riwayat Pemesanan:** Validasi tanggal mulai > tanggal akhir, dan filter rentang tanggal valid. | 2 TC | **PASS** |
| **6** | **FS-006** | **Penambahan Stok Cepat (Quick Restock):** Input restok negatif, restok melebihi batas 1000 unit, dan restok valid. | 3 TC | **PASS** |
| **7** | **FS-007** | **Validasi Harga Produk Baru:** Harga di bawah Rp 1.000, harga di atas Rp 10.000.000, dan harga valid. | 3 TC | **PASS** |
| **8** | **FS-008** | **Validasi Format Email Pengguna:** Email tanpa '@'/domain, dan format RFC valid. | 2 TC | **PASS** |
| **+** | **TD-01 & 02** | **Test Doubles Implementation:** Isolasi database inventaris (STUB) dan verifikasi interaksi audit log (MOCK). | 2 TC | **PASS** |
| | | **TOTAL KESELURUHAN PENGUJIAN** | **28 TC** | **100% LULUS** |

---

## 2. Cara Menjalankan Demo di Terminal
Buka PowerShell, ketik:
```powershell
cd C:\laragon\www\SMARTKASIR
php test_checkout.php
```
*Hasil akan menampilkan eksekusi seluruh 8 fitur (28 kasus uji) dengan status `[PASS]` dan ringkasan `100.00% Success Rate`.*

---

## 3. Skrip Jawaban untuk Aslab: "Apakah Sudah Semua Fitur?"
> *"Sudah SEMUANYA Mas/Mbak! Sesuai arsitektur sistem SmartKasir dari modul sebelumnya, seluruh 8 fitur sistem telah saya buatkan unit test-nya secara lengkap di `test_checkout.php`:*
> 1. *Fitur Autentikasi Login (FS-001)*
> 2. *Fitur Pencarian Katalog Menu (FS-002)*
> 3. *Fitur Validasi Keranjang & Batas Stok (FS-003)*
> 4. *Fitur Inti Checkout & Pembayaran White-Box (FS-004)*
> 5. *Fitur Filter Riwayat Transaksi (FS-005)*
> 6. *Fitur Quick Restock Inventaris (FS-006)*
> 7. *Fitur Validasi Harga Produk (FS-007)*
> 8. *Fitur Validasi Format Email Kasir (FS-008)*
> *Ditambah implementasi 2 Test Double (Stub dan Mock). Total ada **28 kasus uji** dan semuanya lulus 100% tanpa ada yang gagal."*
