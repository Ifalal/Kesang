README

# KéSANG - kesehatan Sanggoro

Aplikasi sederhana berbasis web untuk menampilkan riwayat kesehatan pengguna.  
Dibangun dengan **PHP NATIVE**, **MySQL**, **Bootstrap 5**, dan **Dompdf** untuk export PDF.  

---

## 🛠️ Teknologi yang Digunakan
- Aplikasi berbasis web, terdiri dari front-end dan back-end
- Integrasi Data terpusat
- Akses Data yang flexible
- Keamanan Data, dengan adanya OTP 6 digit
- Dokumen Riwayat Kesehatad (.pdf) yang dapat diunduh


---
## 🛠️ Software dan Tools yang Digunakan
- PHP 
- MySQL  
- Bootstrap 5  
- Dompdf (untuk generate PDF)  
- XAMPP

---

## 🚀 Cara Menjalankan Aplikasi KéSANG

1. Clone atau download project ini.  
2. Ekstrak file kesang.zip ke dalam folder htdocs 
2. Ekstrak file `vendor.zip` yang ada di dalam folder Kesang → hasil ekstrakannya akan menjadi folder `vendor`.  
3. Import database:  
   - Buka `phpMyAdmin`.  
   - Buat database baru `kesang`.  
   - Import file `kesang.sql` yang ada di folder kesang.  
4. Jika Menggunakan XAMPP agar dompdf berjalan secara maksimal maka pada XAMPP control panel klik 'config' pada Apache Cari dan buka 'Php.ini'
   Cari ;extension=gd Kemudian Hilangkan titik Koma (;) pada extension=gd, Lalu Restart Apache pada XAMPP control panel
5.  Jalankan aplikasi KéSANG di browser:  

---
#Tahapan yang harus di lakukan oleh pengguna
1. Registrasi (apabila akun belum terdaftar)
2. Menerima OTP (Untuk Registrasi Baru)
3. Login (Memasukan NIK)
4. Penggunna Mengakses Riwayat kesehatan
5. Pengguna Mendownload Dokumen Riwayat Kesehatan (.PDF)

## 🔑 REGISTRASI
Untuk Login, Registrasi terlebih dahulu dengan mengunakan akun berikut, karena akun yang tercantum sudah terintegrasi dengan database 
terpusat (dummy) 
 
PENGGUNA 1.
- NAMA:        *Ifal Alfalaq*
- NO TELEPON:  *085773474144*
- EMAIL:       *ifalpal@gmail.com*
- NIK:         *3201053012780011*

PENGGUNA 2.
- NAMA:        *alvinodiansyah*
- NO TELEPON:  *085773474145*
- EMAIL:       *Vinodian@gmail.com*
- NIK:         *3201053012780012*

PENGGUNA 3.
- NAMA:        *Farel Prasetia*
- NO TELEPON:  *085773474146*
- EMAIL:       *farelpras@gmail.com*
- NIK:         *3201053012780013*

Setelah Registrasi Pengguna Mendapatkan KODE OTP (4 Digit Angka Acak) 
Untuk Uji coba OTP langsung Muncul di popup notifikasi 
Pengguna Memasukan Kode OTP tersebut.

## 🔑 LOGIN
Login dengan cara memasukan NIK Pengguna yang sebelumnya sudah ter-registrasi.
Login Tidak Berhasil akan muncul pesan kesalahan "NIK tidak ditemukan. Silakan coba lagi."
Login Berhasil Akan Menampilkan Riwayat Kesehatan Pengguna Dengan Keterangan Status Bar Warna:
-Biru = Sehat
-Oranye = Cukup Sehat
-Merah = Tidak Sehat
Riwayat Kesehatan Dapat diunduh dalam bentuk PDF
 
