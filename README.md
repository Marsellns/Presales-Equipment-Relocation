# Equipment Relocation & Presales

Repository ini berisi bagian aplikasi SIMASTER yang khusus menjalankan dua alur:

- Equipment Relocation: inventaris equipment RU/BBP, filter, monitoring relokasi, dan pembaruan progress.
- Presales: upload dokumen PDF dan sirkulasi approval Manager NOP → SQ → NOS → NBAE.

Modul lain dari SIMASTER tidak disertakan. Snapshot inventaris runtime berada di
`public/data/equipment_relocation_inventory.json`; aplikasi tidak membutuhkan file
sumber inventaris saat berjalan.

## Menjalankan lokal

```powershell
composer install
Copy-Item .env.example .env
New-Item -ItemType File database/database.sqlite -Force
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Buka `http://localhost:8000`. Seeder membuat `admin@example.com` dan
`viewer@example.com` dengan password `password`. Role Manager dapat dibuat dari
halaman registrasi lalu disetujui admin.

Untuk memperbarui snapshot inventaris, jalankan:

```powershell
node scripts/build-equipment-relocation-inventory.cjs "C:\path\ke\equipment_inventory.json"
```

Test aplikasi:

```powershell
php artisan test tests/Feature/EquipmentRelocationPresalesTest.php
node --test tests/equipment-relocation-inventory.test.cjs
```
