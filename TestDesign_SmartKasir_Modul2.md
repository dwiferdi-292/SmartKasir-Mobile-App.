# TEST DESIGN SPECIFICATION (TDS)
**Modul 2 – SmartKasir (Point of Sale System).**  
**Nama:** MUCHAMMAD DWI FERDI ILHAM ARIFIN | **NIM:** 202310370311292 | **Kelas:** PKPL-B

---

## 1. Requirements

* **RQ-001:** Sistem harus memungkinkan kasir melakukan login dengan memasukkan alamat email dan kata sandi yang valid dan terdaftar.
* **RQ-002:** Sistem harus menolak upaya login dan menampilkan notifikasi kesalahan jika kasir memasukkan kata sandi salah atau email tidak terdaftar.
* **RQ-003:** Sistem harus memungkinkan kasir mencari produk menu secara langsung (*real-time*) berdasarkan kata kunci nama produk atau kode SKU.
* **RQ-004:** Sistem harus memungkinkan kasir menambahkan produk yang memiliki stok tersedia ke dalam keranjang pesanan serta menghitung kuantitas dan subtotal harga secara otomatis.
* **RQ-005:** Sistem harus mencegah penambahan produk dengan status stok habis (stok = 0) ke dalam keranjang belanja.
* **RQ-006:** Sistem harus mencegah penambahan kuantitas produk dalam keranjang belanja melebihi batas ketersediaan sisa stok fisik di toko.
* **RQ-007:** Sistem harus menghitung total tagihan belanja dan menghitung nominal uang kembalian secara otomatis berdasarkan nominal uang tunai yang diterima dari pelanggan.
* **RQ-008:** Sistem harus memvalidasi pembayaran tunai dan menolak transaksi checkout apabila uang tunai yang diterima kurang dari total nilai belanjaan.
* **RQ-009:** Sistem harus memproses transaksi penjualan, mengurangi stok inventaris produk di database secara atomik, dan menerbitkan struk transaksi resmi.
* **RQ-010:** Sistem harus memungkinkan kasir melihat daftar riwayat pemesanan/transaksi lampau, melihat detail item belanjaan, dan mencetak ulang (*reprint*) struk transaksi.
* **RQ-011:** Sistem harus memungkinkan kasir memantau level ketersediaan stok produk, menambah stok fisik (*quick restock*), dan mencatat riwayat mutasi stok ke tabel log.
* **RQ-012:** Sistem harus menyediakan tombol keluar (*logout*) yang dapat diakses dengan aman di pojok kanan atas untuk mengakhiri sesi kasir.

---

## 2. Feature Sets

### FS-001 – Cashier Authentication
* **Objective:** Mengelola otentikasi login kasir ke dalam sistem POS serta memvalidasi kredensial pengguna.
* **Priority:** High
* **Traceability:** RQ-001, RQ-002

### FS-002 – Product Catalog & Search
* **Objective:** Menampilkan katalog menu barang dagangan dan memfilter produk secara real-time berdasarkan kata kunci pencarian.
* **Priority:** Medium
* **Traceability:** RQ-003

### FS-003 – Cart & Stock Boundary Management
* **Objective:** Mengatur item belanjaan kasir, pembatasan kuantitas agar tidak melebihi stok, dan penolakan produk berstok kosong.
* **Priority:** High
* **Traceability:** RQ-004, RQ-005, RQ-006

### FS-004 – Checkout & Payment Processing
* **Objective:** Menghitung total tagihan, memvalidasi kecukupan pembayaran tunai, menghitung kembalian, memotong stok inventaris, dan mencetak struk transaksi.
* **Priority:** High
* **Traceability:** RQ-007, RQ-008, RQ-009

### FS-005 – Order History & Reprint
* **Objective:** Menampilkan rekapitulasi riwayat transaksi penjualan kasir, rincian item, dan pencetakan ulang invoice belanja.
* **Priority:** Medium
* **Traceability:** RQ-010

### FS-006 – Stock Management & Session
* **Objective:** Memantau sisa stok fisik toko, penambahan stok cepat (*quick restock*), pencatatan kartu audit mutasi stok, serta penutupan sesi login (*logout*).
* **Priority:** High
* **Traceability:** RQ-011, RQ-012

---

## 3. Test Conditions

| ID | Feature Set | Description | Priority | Traceability |
| :--- | :--- | :--- | :--- | :--- |
| **TCond-001** | FS-001 | Melakukan login dengan email dan kata sandi kasir yang valid serta terdaftar | High | RQ-001 |
| **TCond-002** | FS-001 | Melakukan login dengan kata sandi salah atau email tidak terdaftar (sistem menolak dan menampilkan pesan error) | High | RQ-002 |
| **TCond-003** | FS-002 | Melakukan pencarian produk menggunakan kata kunci nama yang tersedia di katalog menu | Medium | RQ-003 |
| **TCond-004** | FS-003 | Menambahkan produk yang berstatus stok tersedia (stok > 0) ke dalam keranjang pesanan | High | RQ-004 |
| **TCond-005** | FS-003 | Menambahkan produk yang berstatus stok habis (stok = 0) ke keranjang (sistem memblokir dan tombol nonaktif) | High | RQ-005 |
| **TCond-006** | FS-003 | Meningkatkan kuantitas produk dalam keranjang melebihi sisa stok fisik toko (sistem membatasi pada angka stok maksimal) | High | RQ-006 |
| **TCond-007** | FS-004 | Melakukan pembayaran tunai dengan nominal uang pas atau uang lebih (sistem menghitung kembalian secara tepat) | High | RQ-007, RQ-009 |
| **TCond-008** | FS-004 | Melakukan pembayaran tunai dengan nominal uang kurang dari total tagihan (sistem menolak proses bayar) | High | RQ-008 |
| **TCond-009** | FS-005 | Melihat daftar riwayat pemesanan transaksi penjualan kasir dan mencetak ulang struk (*reprint receipt*) | Medium | RQ-010 |
| **TCond-010** | FS-006 | Melakukan penambahan stok fisik via Quick Restock dan verifikasi catatan log mutasi barang | High | RQ-011 |
| **TCond-011** | FS-006 | Menekan tombol Keluar (*Logout*) di pojok kanan atas dan memastikan sesi login kasir dihapus secara aman | High | RQ-012 |

---

## 4. Coverage Items

| ID | Description | Related Test Condition | Priority | Traceability |
| :--- | :--- | :--- | :--- | :--- |
| **CI-001** | Pemeriksaan validitas email dan enkripsi kata sandi akun kasir saat autentikasi | TCond-001 | High | RQ-001 |
| **CI-002** | Penolakan login ketika kredensial salah, disertai tampilan pesan notifikasi error merah | TCond-002 | High | RQ-002 |
| **CI-003** | Penyaringan katalog produk kasir berdasarkan kecocokan kata kunci pencarian | TCond-003 | Medium | RQ-003 |
| **CI-004** | Penambahan item ke keranjang dan kalkulasi subtotal harga pesanan | TCond-004 | High | RQ-004 |
| **CI-005** | Pencegahan penambahan produk berstok kosong (badge stok habis & tombol disabled) | TCond-005 | High | RQ-005 |
| **CI-006** | Pembatasan nilai kuantitas maksimum keranjang tepat pada sisa stok fisik produk | TCond-006 | High | RQ-006 |
| **CI-007** | Kalkulasi total nilai belanjaan dan perhitungan otomatis nominal uang kembalian | TCond-007 | High | RQ-007 |
| **CI-008** | Penolakan transaksi checkout ketika nominal uang tunai yang diinput kurang dari total tagihan | TCond-008 | High | RQ-008 |
| **CI-009** | Eksekusi transaksi atomik, pengurangan saldo stok inventaris, dan penerbitan tampilan struk resmi | TCond-007 | High | RQ-009 |
| **CI-010** | Tampilan tabel data riwayat pemesanan kasir dan fungsionalitas cetak ulang struk | TCond-009 | Medium | RQ-010 |
| **CI-011** | Form input penambahan stok produk (*Quick Restock*) dan perekaman audit mutasi stok ke database | TCond-010 | High | RQ-011 |
| **CI-012** | Pemusnahan sesi aktif pengguna dan pengalihan ke halaman login awal (*logout action*) | TCond-011 | High | RQ-012 |

---

## 5. Test Traceability Matrix (TTM)

Matriks berikut menelusuri setiap software requirement pada SmartKasir (Point of Sale System) ke feature set, test condition, dan coverage item terkait, berdasarkan Test Design Specification pada dokumen ini.

| Requirement | Feature Set | Test Condition | Coverage Item |
| :--- | :--- | :--- | :--- |
| **RQ-001** | FS-001 | TCond-001 | CI-001 |
| **RQ-002** | FS-001 | TCond-002 | CI-002 |
| **RQ-003** | FS-002 | TCond-003 | CI-003 |
| **RQ-004** | FS-003 | TCond-004 | CI-004 |
| **RQ-005** | FS-003 | TCond-005 | CI-005 |
| **RQ-006** | FS-003 | TCond-006 | CI-006 |
| **RQ-007** | FS-004 | TCond-007 | CI-007 |
| **RQ-008** | FS-004 | TCond-008 | CI-008 |
| **RQ-009** | FS-004 | TCond-007 | CI-009 |
| **RQ-010** | FS-005 | TCond-009 | CI-010 |
| **RQ-011** | FS-006 | TCond-010 | CI-011 |
| **RQ-012** | FS-006 | TCond-011 | CI-012 |
