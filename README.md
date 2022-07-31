# WPU Admin Pro

Website WPU Admin dari channel WPU namun dengan tambahan beberapa fitur dan menggunakan konsep MVC.<br>
Dibuat di/dengan :
- Fedora linux 36
- VS Code
- PHP 8.1.7
- 10.5.16-MariaDB
- Apache/2.4.54
- CodeIgniter 3.1.13

Untuk windows, semoga saja bisa dan sama berfungsinya. Silahkan ikuti instruksi di bawah ini untuk pemasangan!

## Instruksi
> :warning: **Jika kamu mengggunakan php dengan versi di bawah 8**: Silahkan ubah kode berikut pada helper!
```php
if (!str_contains($menu, 'auth')) {
// Ubah menjadi
if (!preg_match('/auth/', $menu)) {
```
dan
```php
if (str_contains($menu, 'auth') || is_null($menu)) {
// Ubah menjadi
if (preg_match('/auth/', $menu) || is_null($menu)) {
```
juga <br>
**Silahkan ubah email dan smtp pass di controller Auth**

### Akun Percobaan untuk Login
**Admin** <br>
Email : admin123@gmail.com <br>
Password : admin123 <br>

**User** <br>
Email : user123@gmail.com <br>
Password : user1234 <br>

## Fitur tambahan
- Query ke database menggunakan model
- Resize foto profil agar tidak terlalu besar
- Hapus foto profil sekarang dan ubah kembali menjadi default.png
- Edit dan delete menu dan submenu
- Jika submenu tersebut tidak aktif, maka user tidak bisa membuka submenu tersebut melalui url
