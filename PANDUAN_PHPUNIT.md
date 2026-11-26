# Menjalankan Unit Test (PHPUnit)

```
cd C:\laragon\www\SMARTKASIR
composer install
vendor\bin\phpunit --testdox
```

Coverage (butuh ekstensi Xdebug aktif, cek dengan `php -m`):

```
$env:XDEBUG_MODE="coverage"
vendor\bin\phpunit --coverage-text
```

Upload ke hosting: `kasir/process.php` dan folder `src/` harus diunggah bersamaan.
Folder `tests/` dan `vendor/` tidak perlu diunggah.
