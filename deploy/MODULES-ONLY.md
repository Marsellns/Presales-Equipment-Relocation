# Paket modul SIMASTER untuk cPanel

Sumber modul: repository Presales-Equipment-Relocation.
Cakupan: Infrastruktur Management dan submodul, Equipment Relocation, PO Monitoring (PO HQ, PO Varcost, Presales).

Folder modules/ berisi 125 file kandidat modul: kelas Filament tiga kelompok modul, controller, request, model selain User, import/export, helper, view modul, serta aset CSS/JS. Folder ini tidak berisi login, register, public/index.php, .htaccess, vendor, atau konfigurasi aplikasi.

Folder integration-review/ berisi 51 file referensi yang perlu dibandingkan dan digabungkan dengan aplikasi lama: route, registry/panel Filament, migrasi, Composer, bootstrap, config, dan layout. JANGAN menyalin folder ini secara langsung ke aplikasi live. Beberapa file referensi masih memperlihatkan route login dari repository sumber; itu hanya untuk perbandingan, bukan untuk deployment login.

Framework lama yang ditunjukkan pengguna memakai PHP ^8.2, Laravel ^12.0, dan Filament ^4.0. Sumber modul memakai PHP ^8.2, Laravel ^12.0, dan Filament ^5.0, ditambah paket Excel, DomPDF, Spatie Permission/Activitylog, dan Yajra DataTables. Sebelum live, sesuaikan modul atau dependensi pada salinan aplikasi lama dan pastikan versi Filament/Livewire serta semua plugin kompatibel. Jangan mengganti composer.json, composer.lock, vendor, atau file bersama aplikasi lama dengan yang ada di folder referensi.

File migrasi adalah referensi. Periksa tabel dan kolom yang sudah ada pada MySQL live sebelum memilih migrasi; jangan jalankan seluruh migrasi repository modul, karena ada migrasi pengguna dan tabel bersama dalam sumber lengkap.

Perintah Deploy HEAD Commit di cPanel hanya menyalin dua kelompok file ini ke folder staging baru di luar public_html. Perintah ini tidak menyentuh kode live, database, .env, atau unggahan. Setelah staging, cocokkan dengan root framework PHP 8.2 live, backup, gabungkan file yang diperlukan, lalu aktifkan modul dan lakukan pemeriksaan fungsional. Path absolut root framework live masih perlu diketahui sebelum membuat perintah publikasi final.