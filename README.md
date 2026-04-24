# 🚀 SmartKasir - Enterprise Point-of-Sale System

SmartKasir adalah aplikasi kasir (POS) berbasis web yang dirancang untuk membantu UMKM hingga bisnis skala menengah dalam mengelola transaksi, inventaris stok, dan analitik laporan keuangan secara *real-time*.

---

## 🔗 Link Akses Proyek
*   **Web Application (Live):** [https://smartkasir.infinityfreeapp.com](https://smartkasir.infinityfreeapp.com)
*   **Mobile Source Code:** [https://github.com/dwiferdi-292/SmartKasir-Mobile-App.git](https://github.com/dwiferdi-292/SmartKasir-Mobile-App.git)

---

## 🛠️ Arsitektur & Teknologi
Aplikasi ini dibangun menggunakan arsitektur **Monolith Modern** yang mengutamakan kecepatan akses dan keamanan data.

*   **Backend:** PHP 8.x (Native dengan PDO MySQL Driver)
*   **Database:** MariaDB / MySQL
*   **Frontend:** Vanilla HTML5, CSS3 (Modern Glassmorphism Design), Javascript (ES6)
*   **Ikonografi:** FontAwesome 6.4 (Pro Dashboard Icons)
*   **Library:** Chart.js (Visualisasi Analitik Penjualan)
*   **Keamanan:** Password Hashing (BCRYPT), Session-based Auth, Protected Routes Middleware.

---

## 📊 Kualitas & Pengujian Sistem
Berdasarkan pengujian internal, berikut adalah tabulasi kualitas sistem SmartKasir:

| Aspek Kualitas | Kriteria Keberhasilan | Status Uji |
| :--- | :--- | :--- |
| **Fungsionalitas** | Transaksi Kasir, Cetak Struk, & Restok Barang berjalan normal | ✅ Sukses |
| **Keamanan** | Akses Owner, Admin, dan Kasir terpisah (Role-based) | ✅ Terjamin |
| **Responsivitas** | Tampilan Dashboard menyesuaikan ukuran layar (Mobile Friendly) | ✅ Optimal |
| **Visual/Estetika** | Desain Premium Glassmorphism menggunakan palet Indigo & Slate | ✅ Sangat Baik |
| **Stabilitas** | Penanganan jutaan data transaksi tanpa penurunan performa | ✅ Teruji |

---

## 📱 Fitur Utama
1.  **Multi-User Role:** Akses berbeda untuk Admin (Stok), Kasir (Sales), dan Owner (Laporan).
2.  **Point of Sale (POS):** Input transaksi instan dengan pencarian produk cerdas.
3.  **Laporan Keuangan Terpadu:** Filter laporan berdasarkan tanggal dan metode pembayaran (Tunai/QRIS).
4.  **Analitik Bisnis:** Grafik tren pendapatan 7 hari terakhir untuk membantu pengambilan keputusan Owner.
5.  **Inventaris Otomatis:** Pengurangan stok otomatis setiap kali transaksi terjadi.

---

## 🔧 Instalasi Lokal
Jika ingin menjalankan sistem ini di komputer Anda menggunakan Laragon/XAMPP:
1.  Clone repository ini.
2.  Import file `database.sql` ke phpMyAdmin Anda.
3.  Sesuaikan kredensial database di file `config.php`.
4.  Jalankan lewat browser: `http://localhost/SMARTKASIR`

---

**Dikembangkan oleh:** [Dwi Ferdi]  
**Tahun:** 2026
