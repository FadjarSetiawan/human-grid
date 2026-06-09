# HumanGrid

Platform media sosial anti-AI yang mengutamakan konten asli buatan manusia.

## 📷 Visi & Prinsip

- **Zero AI Content** – Semua unggahan wajib hasil kreasi manusia
- **Verifikasi Keaslian** – Pemeriksaan metadata EXIF dan sistem laporan komunitas
- **Transparansi Algoritma** – Feed kronologis murni tanpa ranking AI
- **Privasi & Etika** – Tidak ada pengumpulan data untuk pelatihan model AI

## 🚀 Teknologi

| Lapisan | Teknologi |
|---------|-----------|
| Frontend | Alpine.js, Tailwind CSS, HTML5 |
| Backend | PHP Native (tanpa framework), MVC sederhana |
| Database | MySQL (InnoDB) |
| Server | Apache/Nginx, PHP 8.1+ |

## 📁 Struktur Folder

```
humangrid/
├── public/                 # Document root
│   ├── index.php           # Front controller
│   ├── .htaccess           # URL rewriting
│   └── uploads/            # User uploads
├── app/
│   ├── Controllers/        # Auth, Post, User controllers
│   ├── Models/             # User, Post, Comment models
│   ├── Helpers/            # Helper functions
│   └── Views/              # PHP templates
├── config/
│   ├── database.php        # PDO connection
│   └── app.php             # App constants
├── core/                   # Router, Controller, Model base classes
└── migrations/             # SQL schema
```

## ⚙️ Instalasi

### 1. Clone & Setup Database

```bash
# Buat database MySQL
mysql -u root -p
CREATE DATABASE humangrid CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Import schema
mysql -u root -p humangrid < migrations/001_initial_schema.sql
```

### 2. Konfigurasi

Edit `config/database.php`:

```php
return new PDO(
    'mysql:host=localhost;dbname=humangrid;charset=utf8mb4',
    'username',
    'password',
    [...]
);
```

Edit `config/app.php` jika perlu (BASE_URL).

### 3. Permissions

```bash
chmod 755 public/uploads
chown www-data:www-data public/uploads
```

### 4. Web Server

**Apache:** Pastikan `mod_rewrite` aktif dan `public/` adalah document root.

**Nginx:**
```nginx
server {
    listen 80;
    server_name humangrid.local;
    root /path/to/humangrid/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }
}
```

## 🔐 Keamanan

- CSRF protection pada semua form
- Prepared statements (PDO) untuk mencegah SQL injection
- XSS prevention dengan `htmlspecialchars()`
- Validasi EXIF untuk deteksi konten AI
- File upload validation (tipe, ukuran, MIME)

## 📝 Fitur Utama

- ✅ Registrasi & Login
- ✅ Upload foto/video dengan validasi EXIF
- ✅ Feed kronologis (tanpa algoritma AI)
- ✅ Like & Komentar (AJAX dengan Alpine.js)
- ✅ Follow/Unfollow pengguna
- ✅ Laporan konten AI
- ✅ Badge "Verified Human"
- ✅ Pencarian pengguna
- ✅ Profil & edit profil

## 🛡️ Anti-AI Features

1. **EXIF Validation**: Setiap upload diperiksa metadata kameranya
2. **AI Keyword Detection**: Deteksi kata kunci AI generator di EXIF
3. **Community Reporting**: Pengguna bisa laporkan konten mencurigakan
4. **Auto-flagging**: 3 laporan = konten disembunyikan otomatis
5. **Verified Human Badge**: Verifikasi manual untuk kreator asli

## 📄 License

MIT License

---

**HumanGrid** - Media sosial untuk manusia, oleh manusia.
