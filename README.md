# Konverter TXT ke CSV

Ini adalah aplikasi web yang memungkinkan pengguna mengunggah file TXT dan mengonversinya ke format CSV. Aplikasi ini memiliki antarmuka pengguna frontend yang sederhana dan bersih serta backend PHP yang menangani unggahan file, konversi, dan pengunduhan.

>>>>> dctxtcsv.infy.uk <<<<<
## Fitur

- Unggah file TXT melalui input file atau drag-and-drop
- Deteksi delimiter otomatis untuk berbagai format TXT
- Konversi konten TXT ke format CSV
- Unduh file CSV yang telah dikonversi
- Penghapusan otomatis file TXT yang diunggah setelah konversi
- Penanganan error dengan pesan yang deskriptif
- UI minimalis dengan skema warna terinspirasi Komatsu

## Teknologi yang Digunakan

- Frontend: HTML, CSS, JavaScript
- Backend: PHP
- Library: Font Awesome untuk ikon, Google Fonts (Roboto)

## Struktur File

- `index.html`: UI frontend
- `styles.css`: Styling untuk frontend
- `script.js`: JavaScript frontend untuk menangani unggahan file dan permintaan konversi
- `upload.php`: Skrip PHP backend untuk menangani unggahan file dan memanggil layanan konversi
- `convert.php`: Skrip PHP backend untuk mengonversi file TXT ke CSV
- `uploads/`: Direktori untuk menyimpan sementara file TXT yang diunggah
- `csv/`: Direktori untuk menyimpan file CSV yang telah dikonversi

## Cara Penggunaan dan Instalasi

1. Pastikan Anda memiliki server web yang mendukung PHP (misalnya, XAMPP, WAMP).
2. Tempatkan file proyek di direktori root server web Anda.
3. Pastikan direktori `uploads` dan `csv` ada dan dapat ditulis oleh server web. Skrip PHP akan mencoba membuatnya jika belum ada.
4. Akses aplikasi melalui browser Anda di `http://localhost/txt_tocsv/index.html` (sesuaikan path jika perlu).
5. Unggah file TXT menggunakan input file atau area drag-and-drop.
6. Klik "Convert to CSV" untuk mengonversi file.
7. Unduh file CSV yang telah dikonversi menggunakan tautan yang disediakan.

## Catatan

- Aplikasi mendukung file TXT dengan berbagai delimiter (tab, koma, titik koma, pipe).
- File TXT yang diunggah dihapus segera setelah konversi untuk menjaga privasi.
- Aplikasi menggunakan curl di PHP untuk memanggil layanan konversi secara internal.
- Pesan error dicatat di log error server untuk debugging.

## Lisensi

Proyek ini bersifat open source dan tersedia di bawah Lisensi MIT.

## Penulis

Dikembangkan oleh @iqbalmaulana28_.
