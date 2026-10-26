# Paket modul SIMASTER untuk cPanel

Sumber modul: repository Presales-Equipment-Relocation.
Cakupan: **Equipment Relocation** dan **PO Monitoring** dengan tiga submodul: PO HQ, PO Varcost, dan Presales.

Folder modules/ berisi 43 file khusus modul dan dependensi langsungnya. Folder integration-review/ berisi 46 file referensi integrasi bersama yang harus dibandingkan dengan aplikasi lama. File autentikasi, route login, dan kerangka Laravel hanya dipertahankan sebagai konteks integrasi; jangan menyalinnya langsung ke aplikasi live.

Framework lama yang ditunjukkan pengguna memakai PHP ^8.2, Laravel ^12.0, dan Filament ^4.0. Sumber modul memakai PHP ^8.2, Laravel ^12.0, dan Filament ^5.0, ditambah paket Excel, Spatie Permission/Activitylog, dan Yajra DataTables. Jangan mengganti composer.json, composer.lock, vendor, atau file bersama aplikasi lama sebelum perbedaan serta kompatibilitasnya diperiksa.

File migrasi hanya referensi untuk tabel Equipment Relocation, PO Monitoring, autentikasi, izin, dan audit. Periksa tabel/kolom MySQL live sebelum memilih migrasi; jangan menjalankan seluruh migrasi pada aplikasi lama.

Perintah Deploy HEAD Commit di cPanel hanya menyalin dua kelompok file ini ke folder staging di luar public_html. Perintah ini tidak menyentuh kode live, database, .env, atau unggahan. Setelah staging, cocokkan dengan root framework live, backup, gabungkan file yang diperlukan, lalu aktifkan modul dan lakukan pemeriksaan fungsional. Skrip staging membuat target-comparison.txt berisi SAME, DIFFERENT, atau MISSING dengan hanya membaca laravel-core. Jangan menyalin folder referensi secara langsung ke aplikasi live.
