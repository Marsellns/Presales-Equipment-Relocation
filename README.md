# SIMASTER: Infrastructure, Equipment Relocation, PO Monitoring

Repository ini memuat tiga area SIMASTER yang dipilih:

- **Infrastructure Management**: dashboard, BAPSS, Combat, Sewa Lahan, Site Telkomsel, Site TP, Recurring IPAS, Recurring Tagihan IPAS, Jaknet, Data Site Unlock, dan Upload File.
- **Equipment Relocation**: inventaris RU/BBP, monitoring relokasi, pembaruan progress, impor, dan ekspor.
- **PO Monitoring**: PO HQ, PO Varcost, dan Presales.

Halaman Filament untuk ketiga area tersedia melalui panel `/report`. Setup lokal:

```powershell
composer install
Copy-Item .env.example .env
New-Item -ItemType File database/database.sqlite -Force
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

Untuk mengisi inventaris Equipment Relocation dari snapshot yang tersedia di repository, jalankan setelah migrasi:

```powershell
php artisan equipment:import-inventory public/data/equipment_relocation_inventory.json
```

Jika tabel inventaris sudah berisi data dan memang perlu diganti dengan snapshot, tambahkan opsi `--replace` setelah meninjau dampaknya.

Jalankan aplikasi lokal dengan `php artisan serve`, lalu buka `http://localhost:8000`.

Untuk membangun ulang snapshot dari file sumber inventaris:

```powershell
node scripts/build-equipment-relocation-inventory.cjs "C:\path\ke\equipment_inventory.json"
```

Test aplikasi:

```powershell
php artisan test
node --test tests/equipment-relocation-inventory.test.cjs
```

## Production dan Jenkins

Lihat [panduan rilis production](docs/production-rollout.md) untuk CI Windows/PHP 8.2 dan pemasangan selektif ke SIMASTER yang sudah berjalan.
