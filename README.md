# HumanGrid - Sosial Media Anti‑AI, Karya Manusia Sejati

**HumanGrid** adalah platform berbagi foto dan video yang dibangun dengan prinsip dasar: **tanpa konten AI, tanpa algoritma AI, tanpa pelatihan data AI**. Kami merayakan kreativitas orisinal manusia dalam ruang digital yang bersih dari rekayasa mesin.

Dibangun dengan stack minimalis dan efisien: **Alpine.js**, **Tailwind CSS**, **PHP native**, dan **MySQL**.

---

## Visi & Prinsip Anti‑AI

- **Nol Konten AI** – Setiap unggahan harus hasil kreasi manusia. Tidak ada generator, filter cerdas, atau rekomendasi berbasis AI.
- **Verifikasi Keaslian** – Metadata kamera (EXIF) digunakan untuk memvalidasi orisinalitas. Pengguna bisa mendapatkan lencana **Verified Human**.
- **Algoritma Transparan** – Feed hanya menampilkan postingan dari akun yang diikuti secara **kronologis murni**, tanpa peringkat otomatis.
- **Privasi Mutlak** – Data pengguna tidak pernah digunakan untuk pelatihan model AI. Gambar tidak diproses oleh layanan AI pihak ketiga.
- **Komunitas Penjaga** – Sistem laporan konten AI berbasis partisipasi pengguna.

---

## Fitur Utama

- 🧑‍🤝‍🧑 **Profil & Jejaring Sosial** – Ikuti, pengikut, bio, avatar.
- 📸 **Unggah Foto & Video** – Dengan validasi metadata kamera.
- ❤️ **Like & Komentar** – Interaksi real‑time tanpa muat ulang.
- 🚩 **Laporkan Konten AI** – Jaga kemurnian platform.
- ✅ **Lencana Verified Human** – Bukti bahwa kamu manusia di balik kamera.
- 🔍 **Pencarian Pengguna** – Temukan sesama kreator.
- 📜 **Feed Kronologis** – Bebas dari manipulasi.

---

## Teknologi

| Lapisan   | Teknologi                              |
|-----------|----------------------------------------|
| Frontend  | Alpine.js, Tailwind CSS, HTML5         |
| Backend   | PHP 8.1+ (native, tanpa framework)     |
| Database  | MySQL (InnoDB)                         |
| Server    | Apache / Nginx                         |
| Keamanan  | PDO Prepared Statements, CSRF, sanitasi EXIF |

---

## Persyaratan Sistem

- PHP >= 8.1 dengan ekstensi `pdo_mysql`, `exif`, `fileinfo`
- MySQL >= 5.7 atau MariaDB >= 10.3
- Web server (Apache dengan mod_rewrite / Nginx)
- Composer (opsional, untuk tools pengembangan)

---

## Instalasi

### 1. Clone repositori
```bash
git clone https://github.com/username/humangrid.git
cd humangrid
