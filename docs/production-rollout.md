# Persiapan rilis production SIMASTER (PHP 8.2)

Repository ini berisi Infrastruktur Management dan submodulnya, Equipment Relocation, serta PO Monitoring (PO HQ, PO Varcost, Presales). Kode diuji dengan Laravel 12, Filament 5, PHP 8.2, SQLite, dan MySQL. Jenkinsfile menjalankan pengujian pada Windows melalui Docker.

## Jalankan CI di Jenkins Windows

1. Pasang Docker Desktop (Linux containers), Git, dan Jenkins agent Windows yang dapat menjalankan perintah Docker.
2. Buat job **Pipeline** dari SCM, pilih repository ini dan branch yang akan dirilis. Isi Script Path dengan `Jenkinsfile`.
3. Jalankan build. Pipeline membangun image PHP 8.2 dari `ci/Dockerfile`, memeriksa dependency Composer, cache route/view, test Laravel, dan test JavaScript Equipment Relocation.
4. Terapkan branch protection agar rilis hanya memakai commit dengan build Jenkins yang lulus. Catat SHA commit dari build.

Jenkins tidak memakai `.env` production dan tidak terhubung ke database production. Test memakai database sementara. Jangan menyalin `.env`, `APP_KEY`, database, dokumen privat, atau cache lokal ke GitHub/artifact.

## Prasyarat sebelum mengaktifkan deployment otomatis

Aplikasi cPanel yang sudah berjalan adalah SIMASTER lengkap. Repository ini hanya memuat modul terpilih dan beberapa file integrasi bersama. **Jangan melakukan `git pull` atau mengekstrak seluruh repository ini di atas root aplikasi live**: langkah itu dapat menghapus atau mengganti modul lain.

Untuk pemasangan pertama, kumpulkan data berikut dari cPanel tanpa membagikan sandi atau private key:

- Path absolut root Laravel (folder berisi `artisan`) dan document root domain/subdomain.
- Akses yang tersedia: SSH/Terminal, cPanel Git Version Control, SFTP, atau FTP. SSH lebih cocok untuk backup, migrasi, cache, dan rollback.
- Versi PHP untuk web **dan** CLI, database MySQL/MariaDB, ekstensi PHP, dan path executable PHP 8.2.
- Sumber kode live: remote Git/commit jika ada, atau salinan kode live tanpa `.env`, `storage` operasional, `vendor`, dan data privat untuk dibandingkan.
- Daftar perubahan lokal yang pernah dibuat langsung di cPanel.
- Lokasi backup database dan dokumen privat serta prosedur pemulihannya.

File bersama yang wajib dibandingkan dan digabungkan dengan aplikasi lengkap adalah `composer.json`, `composer.lock`, `bootstrap/*`, `config/*`, `routes/*`, `app/Models/User.php`, `app/Providers/*`, dan file autentikasi/panel. Perubahan pada file ini memengaruhi modul lain. File khusus modul dapat dipindahkan sebagai satu paket setelah dependensinya diperiksa.

## Urutan deployment setelah baseline live diverifikasi

1. Ambil backup database, dokumen `storage/app` dan kode live. Catat SHA/checksum paket lama.
2. Buat paket rilis dari commit yang lulus Jenkins. Gabungkan file bersama terhadap **baseline aplikasi live**; verifikasi bahwa rilis hanya mengubah modul terpilih serta dependensi bersama yang diperlukan. Jalankan CI terhadap hasil gabungan.
3. GitHub Actions mengunggah paket ke folder staging di luar document root lewat SSH/SFTP. Proses ini harus berhenti bila baseline/hash file live tidak cocok dengan yang disetujui.
4. Di server, periksa PHP CLI 8.2 dan `composer check-platform-reqs --no-dev`. Pertahankan `.env`, `APP_KEY`, `storage/app`, database, dan modul lain. Publikasikan perubahan secara atomik atau dengan waktu maintenance singkat; jangan memakai sinkronisasi `--delete` pada root live.
5. Jalankan `php artisan optimize:clear`, `php artisan migrate --force`, `php artisan filament:assets`, lalu `php artisan optimize`. Jangan jalankan `composer setup`, `php artisan key:generate`, `migrate:fresh`, atau seeder data pada aplikasi lama.
6. Uji login, izin admin/viewer, semua submodul Infrastruktur, Equipment Relocation, PO HQ, PO Varcost, dan Presales, termasuk upload/download, tabel, ekspor, serta aset CSS/JS. Setelah lulus, aktifkan aplikasi.
7. Bila gagal, kembalikan paket kode lama dan backup database/dokumen yang sesuai. Migrasi database tidak otomatis dibatalkan hanya dengan mengembalikan file PHP.

Workflow CD sengaja belum diberi host/path/credential fiktif. Setelah data cPanel dan baseline live tersedia, isi GitHub Environment secrets untuk SSH/SFTP dan buat workflow deployment dengan pemeriksaan baseline serta rollback. Hindari trigger otomatis dari setiap push sampai rilis awal berhasil diverifikasi.

## Kompatibilitas PHP

`composer.json` mengunci platform PHP 8.2 dan Laravel 12. Pastikan PHP web dan CLI cPanel sama-sama memenuhi persyaratan. Jalankan `composer check-platform-reqs --no-dev` pada PHP server yang sebenarnya; lockfile yang lulus di CI belum membuktikan ekstensi hosting sudah lengkap. Gunakan `.env` production milik sistem yang sudah berjalan dengan `APP_ENV=production`, `APP_DEBUG=false`, dan `APP_URL` HTTPS. Jangan mengganti `APP_KEY` lama.
