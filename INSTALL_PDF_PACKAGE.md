# Instalasi Package untuk Export PDF Buku Besar

## Package yang Diperlukan

Untuk menggunakan fitur export PDF pada Buku Besar, Anda perlu menginstall package `barryvdh/laravel-dompdf`.

## Cara Instalasi

Jalankan perintah berikut di terminal:

```bash
composer require barryvdh/laravel-dompdf
```

## Konfigurasi (Opsional)

Jika ingin mengkustomisasi konfigurasi PDF, publish config file:

```bash
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

## Verifikasi

Setelah instalasi, pastikan package sudah terdaftar di `composer.json`:

```json
"require": {
    "barryvdh/laravel-dompdf": "^2.0"
}
```

## Penggunaan

Fitur export PDF sudah terintegrasi di halaman Buku Besar:
1. Pilih akun yang ingin dilihat
2. Klik tombol "Export PDF"
3. File PDF akan otomatis terdownload

## Troubleshooting

Jika terjadi error setelah instalasi:

```bash
# Clear cache
php artisan config:clear
php artisan cache:clear

# Regenerate autoload
composer dump-autoload
```
