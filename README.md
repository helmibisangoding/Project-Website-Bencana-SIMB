# Siaga Lereng — PHP native + MySQL / XAMPP

Web edukasi tanah longsor dan pencatatan kejadian, korban/orang hilang, serta pengungsian.
Versi ini merupakan proyek PHP mandiri untuk VS Code dan XAMPP. Berbeda dari versi React yang sebelumnya diterbitkan; database keduanya tidak tersinkron otomatis.

## Mulai cepat (Windows)

1. Pasang XAMPP dengan PHP 8.0 atau lebih baru dari https://www.apachefriends.org/ .
2. Ekstrak ZIP. Salin folder **siaga-lereng** ke **C:\xampp\htdocs\siaga-lereng**.
   Pastikan file `index.php` berada langsung di folder tersebut, bukan di dalam folder ganda.
3. Buka XAMPP Control Panel. Klik **Start** untuk **Apache** dan **MySQL**.
4. Buka **http://localhost/phpmyadmin/**. Pilih **Import** → pilih `siaga-lereng/database/schema.sql` → **Go/Kirim**.
   SQL membuat database `siaga_lereng` dan empat tabel. Tidak perlu membuat tabel satu per satu.
5. Buka folder `C:\xampp\htdocs\siaga-lereng` di VS Code melalui **File → Open Folder**.
6. Periksa `config/database.php`. Default untuk instalasi XAMPP lokal:
   - host: `127.0.0.1`
   - port: `3306`
   - database: `siaga_lereng`
   - username: `root`
   - password: string kosong `''`
   Jika pengaturan Anda berbeda, sesuaikan. Root kosong hanya contoh default lokal.
7. Buka **http://localhost/siaga-lereng/** di browser. Aplikasi mengarahkan ke pembuatan admin pertama.
8. Buat username dan password sendiri (minimal 10 karakter ASCII), lalu login.
9. Mulai dari **Data Kejadian → Tambah kejadian**, kemudian tambah posko atau registrasi orang.

**Jangan membuka file dengan double click atau VS Code Live Server.** PHP perlu dijalankan oleh Apache/PHP. VS Code dipakai untuk mengedit; browser membuka alamat localhost di atas.

Jika Apache menggunakan port 8080, gunakan `http://localhost:8080/siaga-lereng/` dan `http://localhost:8080/phpmyadmin/`.

## Data contoh (opsional)

Database awal kosong agar data simulasi tidak tercampur dengan laporan nyata.
Untuk demonstrasi pada database latihan kosong, import `database/demo_opsional.sql` setelah `schema.sql` melalui phpMyAdmin.
Seluruh kejadian dan identitas dalam demo adalah **FIKTIF**, ditandai `[SIMULASI]`.
Jalankan import demo hanya sekali; pengulangan menambahkan catatan duplikat. File demo tidak dijalankan otomatis.

## Fitur

- Materi definisi, enam jenis longsor, penyebab alam/manusia, dampak negatif dan positif tidak langsung.
- Panduan sebelum, saat, setelah bencana dan mitigasi struktural/nonstruktural.
- Login admin; password disimpan menggunakan `password_hash`, bukan teks biasa.
- CRUD kejadian: tanggal (tahun diturunkan dari tanggal), provinsi, kabupaten, lokasi, jenis, status, meninggal, luka, hilang, rumah rusak, sumber/catatan.
- CRUD korban/orang hilang: identitas minimum, umur, status, lokasi terakhir, kejadian, posko opsional.
- CRUD posko: kejadian, lokasi, kapasitas, jumlah penghuni, kebutuhan logistik.
- Grafik jumlah kejadian per tahun yang dirender oleh PHP + CSS (tanpa JavaScript/CDN).
- Pencarian, filter tahun kejadian, ekspor CSV sesuai filter aktif.
- Konfirmasi penghapusan dan relasi foreign key untuk mencegah penghapusan data induk yang masih dipakai.
- Validasi tanggal, angka tidak negatif, usia 0–120, kapasitas posko, dan kecocokan kejadian-posko.
- Prepared statements PDO, escaping HTML, CSRF, sesi login, dan pembatasan localhost.

## Teknologi

PHP native, HTML/CSS, SVG lokal, PDO MySQL, SQL InnoDB/utf8mb4.
Tidak membutuhkan Laravel, React, Node.js, npm, Composer, JavaScript, atau internet setelah XAMPP terpasang.
XAMPP adalah paket server, bukan nama database. XAMPP menggunakan MariaDB pada versi modern, meskipun tombol kontrol sering bernama “MySQL”. SQL proyek ini memakai fitur umum yang kompatibel dengan MySQL dan MariaDB.

## Struktur folder

| File/folder | Fungsi |
| --- | --- |
| `index.php` | Dashboard dan grafik |
| `edukasi.php` | Definisi, jenis, penyebab, dampak |
| `mitigasi.php` | Panduan dan mitigasi |
| `data.php` | Tabel, pencarian, filter |
| `form.php` | Tambah dan ubah ketiga jenis data |
| `delete.php` | Halaman konfirmasi hapus |
| `export.php` | Ekspor CSV yang terlindungi login |
| `setup.php`, `login.php`, `logout.php` | Admin awal dan autentikasi |
| `config/database.php` | Pengaturan koneksi database |
| `includes/bootstrap.php` | Sesi, koneksi PDO, keamanan dasar |
| `includes/definitions.php` | Metadata kolom dan validasi data |
| `includes/records.php` | Query pencarian/filter |
| `includes/layout.php` | Navigasi, header, footer, referensi |
| `assets/style.css` | Tampilan responsif, dapat diedit |
| `assets/lereng.svg` | Ilustrasi lokal |
| `database/schema.sql` | Struktur database |
| `database/demo_opsional.sql` | Contoh data fiktif opsional |
| `tests/manual.md` | Skenario uji fitur di XAMPP |
| `tests/reset_admin.php` | Reset password melalui PHP CLI lokal |

## Relasi dan cara menghitung

- Satu `incidents` dapat memiliki banyak `people` dan banyak `shelters`.
- Satu `shelters` dapat terkait banyak `people`; satu orang bisa belum memiliki posko.
- Posko yang dipilih untuk seseorang harus berasal dari kejadian yang sama.
- Angka korban di kejadian merupakan **rekap manual**, bukan penjumlahan registrasi individu.
- Jumlah penghuni posko merupakan **rekap manual**, bukan jumlah otomatis baris pada tabel orang.
- Dashboard memakai rekap kejadian dan rekap penghuni. Jangan menjumlahkannya lagi dengan registrasi individu.
- Menghapus kejadian yang masih memiliki korban/posko ditolak. Hapus/pindahkan data anak terlebih dahulu.
- Mengubah kejadian suatu posko ditolak bila masih ada registrasi orang dari kejadian lama.

## Login dan privasi

Tidak ada username/password admin bawaan. `setup.php` hanya aktif saat tabel users kosong.
Semua halaman data, dashboard, ekspor, dan mutasi memerlukan login. Materi edukasi dapat dibuka tanpa login setelah database siap.
Paket ini sengaja dibatasi ke alamat loopback (`127.0.0.1` / `::1`), sehingga perangkat lain di LAN ditolak.
Ini aplikasi tugas/latihan lokal, bukan sistem tanggap darurat produksi. Jangan membuka XAMPP ke internet atau menghapus batas localhost untuk data nyata tanpa peninjauan keamanan, HTTPS, hak akses, backup, audit log, dan pengaturan server yang sesuai.

### Reset password admin lokal

Jika lupa password, buka terminal VS Code di folder proyek:

```bat
C:\xampp\php\php.exe tests\reset_admin.php
```

Masukkan username dan password baru saat diminta. Input password terlihat di terminal, jadi jalankan sendiri di komputer pribadi. Script tidak dapat dijalankan melalui browser dan tidak membuat akun baru.

## Backup

Di phpMyAdmin, pilih database `siaga_lereng` → Export → SQL → Go. Simpan hasil export di lokasi privat. CSV hanya untuk laporan, bukan backup lengkap relasi dan akun.
Jangan mengirimkan export berisi identitas korban atau hash password ke repositori publik.

## Troubleshooting

| Gejala | Langkah |
| --- | --- |
| “Database belum siap” | Start MySQL, import schema.sql, cocokkan database.php |
| Access denied pada MySQL | Periksa username/password/host database |
| Unknown database/table | Import schema.sql ke server MySQL yang dipakai |
| Apache tidak start | Periksa konflik port 80/443 dengan IIS/aplikasi lain; pakai port lain bila perlu |
| MySQL tidak start | Periksa konflik port 3306 dan log XAMPP; jangan menghapus folder data |
| could not find driver | Aktifkan ekstensi pdo_mysql di php.ini, restart Apache |
| Halaman 404 | Periksa folder langsung di htdocs, bukan folder ganda, serta URL localhost |
| File PHP tampil sebagai teks | Pastikan membuka melalui Apache, bukan file:// atau Live Server |
| HTTP 403 | Buka dari komputer XAMPP sendiri menggunakan localhost |
| Posko ditolak saat menyimpan orang | Kejadian posko dan kejadian orang harus sama |
| Gagal hapus induk | Hapus/pindahkan data orang/posko yang masih terhubung |

## Pengujian

Baca `tests/manual.md`. Lint PHP di Windows, dari PowerShell pada folder proyek:

```powershell
Get-ChildItem -Recurse -Filter *.php | ForEach-Object { & 'C:\xampp\php\php.exe' -l $_.FullName }
```

Paket diperiksa dengan parser PHP dan pemeriksaan struktur file. Pengujian end-to-end Apache + PHP + MySQL/XAMPP tidak dapat dijalankan di lingkungan pembuatan ini karena runtime PHP/MySQL tidak tersedia. Lakukan checklist lokal sebelum memasukkan data sungguhan.

## Referensi

- ESDM: https://www.esdm.go.id/assets/media/content/Pengenalan_Gerakan_Tanah
- BNPB: https://bnpb.go.id/berita/tanah-longsor-1
- BPBD DIY: https://bpbd.jogjaprov.go.id/berita/mitigasi-bencana-tanah-longsor-1
- XAMPP: https://www.apachefriends.org/faq_windows.html
- PHP PDO: https://www.php.net/manual/en/pdo.prepare.php
- PHP password hashing: https://www.php.net/manual/en/function.password-hash.php
