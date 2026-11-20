# SmartKasir - Enterprise Point-of-Sale System

SmartKasir adalah sistem Point-of-Sale (Kasir) berbasis web yang dirancang dengan antarmuka premium (Modern UI/UX). Aplikasi ini memfasilitasi pengguna untuk mengelola penjualan, memantau riwayat transaksi, hingga mendapatkan prediksi cerdas untuk keberlangsungan stok bisnis restoran, kafe, atau retail.

## 🔗 Links
- **Live Web Application:** [🔗 KLIK DI SINI UNTUK MEMBUKA WEB](#) *(Tolong Isi dengan Link Web Hosting)*
- **Mobile Source Code:** [🔗 REPOSITORI MOBILE APP](#) *(Tolong Isi dengan Link Github Mobile)*

## 🛠️ Teknologi yang Digunakan
- **Frontend**: HTML5, Vanilla CSS, framework Javascript, FontAwesome 6, Chart.js
- **Backend & Database**: PHP 8.x Native (PDO), MySQL / MariaDB
- **Arsitektur**: Monolith dengan Role Based Access Control (RBAC) & Pemrosesan Enkripsi Algoritma Hash.

## 👤 Simulasi Login (Demo)
Sistem dilengkapi dengan 3 pilar batas akses (*Role*):
1. **Admin** (`admin@smartkasir.com`) - Mengatur produk, memonitor stok, dan membuka akses akun baru.
2. **Kasir** (`kasir@smartkasir.com`) - Mengoperasikan mesin POS penjualan ala *Tablet*.
3. **Owner** (`owner@smartkasir.com`) - Melihat laporan keuangan transaksi dan analisis laba.

*(Pada saat pameran/sidang, Anda dapat menggunakan password default: `password`)*

---

## 🧪 Tabel Pengujian Aspek Kualitas (Quality Assurance / Blackbox Testing)

Berikut adalah rekapitulasi pengujian fungsionalitas dan aspek kualitas perangkat lunak berdasarkan skenario desain yang telah ditentukan di awal:

| No | Modul / Fitur yang Diuji | Skenario Pengujian Sistem | Aspek Kualitas / Kriteria yang Diharapkan | Hasil Pengujian (Status) |
|----|--------------------------|-------------------|-------------------------------------------|-------------------------|
| 1 | **Login & Otorisasi** | Mencoba *login* dengan 3 *Role* berbeda. | Akses ditolak jika kredensial salah. Jika benar, sistem melarang loncat akses (*Bypass*) dan masuk sesuai ranahnya. (Faktor: Keamanan & Hak Akses) | ✅ Berhasil (Aman) |
| 2 | **Olah Data Produk** | Menambahkan, mengedit, dan menghapus data barang/menu dari panel Admin. | Data tersimpan secara akurat ke MariaDB, muncul seketika di monitor POS. (Faktor: Kesesuaian Fungsional Bebas *Bug*) | ✅ Berhasil (Akurat) |
| 3 | **Stok Inventaris Real-time** | Membeli barang melalui POS Kasir. | Stok barang di rak/gudang otomatis berkurang murni *real-time* berdasarkan pembelian. (Faktor: Konsistensi Integritas Data) | ✅ Berhasil (Sukses) |
| 4 | **Sistem Kewaspadaan (*Alerting*)**| Membeli produk hingga menyentuh / di bawah batas minimum stok yang ditetapkan. | Dashboard Admin bereaksi dengan membunyikan banner *Warning* merah "Peringatan Stok Habis" *(Low-Stock Notifier)*. (Faktor: Keandalan Sistem / *Reliability*) | ✅ Berhasil (Waspada) |
| 5 | **Mesin Transaksi (POS)** | Memproses struk, merubah metode bayar (Tunai/QRIS), menghitung uang dibayar. | Algoritma penambahan sub-total di keranjang sangat teliti, nominal pengembalian presisi *(Zero Mathematical Error)*. (Faktor: Akurasi Matematika *Finance*) | ✅ Berhasil (Tepat) |
| 6 | **User Experience & Responsivitas** | Menekan tombol bayar, melihat layar penuh, *hover* kartu produk. | Visual responsif, interaktif (bayangan bergerak/animasi *Pulse*), memanjakan mata pengguna agar tidak cepat bosan. (Faktor: *Usability* / Estetika GUI) | ✅ Berhasil (Nyaman) |
| 7 | **Analisis AI Tren & Prediksi** | Mencocokkan data total pendapatan di Dashboard Owner. | Membaca riwayat nota dan mewujudkannya dalam *Chart* harian + menjalankan algoritma analitik *Simple Moving Average*. (Faktor: Validitas Kalkulasi Sistem Pintar) | ✅ Berhasil (Beroperasi) |

---

## 🚀 Cara Instalasi Lokal (Bagi Pengembang Lanjutan)
1. Pasang *XAMPP* atau *Laragon* pada OS Windows Anda.
2. Buka folder `c:\laragon\www\` dan tuangkan folder kode sumber ini dengan nama `SMARTKASIR`.
3. Buka *phpMyAdmin* dan buat pangkalan data (*Database*) kosong bernama `smartkasir`.
4. Klik tombol **Import** lalu masukkan file `database.sql` yang tersedia pada folder aplikasi ini.
5. Akses `http://localhost/SMARTKASIR` atau `http://smartkasir.test` lewat peramban.

> *Catatan Keamanan: Aplikasi ini telah disetel khusus untuk mode Produksi massal. Laporan error telah diredam (silent mode) guna mematuhi standar kerahasiaan keamanan backend korporat.*
