# Checklist uji XAMPP
Gunakan database latihan dan identitas fiktif. Ini skenario uji yang disediakan, bukan klaim telah dijalankan.

1. Import schema.sql sekali → empat tabel terbentuk, data kosong.
2. Buka index.php → diarahkan ke setup; buat admin; login berhasil; password salah ditolak.
3. Buka setup.php lagi → diarahkan ke login/dashboard; admin kedua tidak dibuat.
4. Tanpa login, buka data.php, export.php, form.php, delete.php → diarahkan ke login.
5. Buat kejadian tahun 2025 dan 2026 → keduanya muncul; grafik menunjukkan masing-masing tahun.
6. Ubah lokasi/tanggal/jumlah korban → data dan dashboard diperbarui; refresh browser tetap menyimpan data.
7. Cari nama/lokasi dan filter tahun → daftar benar; ekspor CSV hanya memuat hasil filter tersebut.
8. Buat posko dengan penghuni > kapasitas → ditolak. Koreksi → berhasil.
9. Buat orang terkait kejadian A dan posko kejadian B → ditolak. Gunakan posko kejadian A → berhasil.
10. Ubah status orang Hilang menjadi Ditemukan selamat → tersimpan. Rekap kejadian tetap perlu diperbarui manual.
11. Ubah kejadian posko yang masih terkait orang kejadian lama → ditolak.
12. Hapus kejadian yang masih memiliki orang/posko → ditolak; hapus orang, posko, kemudian kejadian → berhasil setelah konfirmasi masing-masing.
13. Angka negatif, usia 121, tanggal 2026-02-31, field wajib kosong → ditolak oleh browser/server.
14. Masukkan `<script>alert(1)</script>` sebagai nama fiktif → tampil sebagai teks, tidak dieksekusi.
15. Ekspor catatan dengan awalan `=1+1` → sel CSV diberi apostrof, tidak menjadi formula.
16. Hapus token CSRF dari kiriman POST melalui alat pengembang → HTTP 403.
17. Klik Keluar → halaman data kembali memerlukan login.
18. Pada layar kecil, navigasi dapat digeser, formulir satu kolom, tabel dapat digeser horizontal.
19. Materi edukasi dan mitigasi bisa dibaca; tautan referensi membuka sumber resmi.
20. Backup database melalui phpMyAdmin sebelum menggunakan catatan nyata.
