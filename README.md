<p align="center">
  <img src="public/assets/images/netfly-logo.png" width="220" alt="NetFly Logo">
</p>

<h1 align="center">NetFly — Sistem IT Solution & Network Monitoring</h1>

<p align="center">
  Platform Terintegrasi Solusi Infrastruktur Jaringan, Website Company Profile, Web Admin (NOC Console), dan Portal Customer.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/Tailwind_CSS-v4.0-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Vite-6.x-646CFF?style=flat-square&logo=vite&logoColor=white" alt="Vite">
  <img src="https://img.shields.io/badge/SLA_Guarantee-99.5%25-10B981?style=flat-square" alt="SLA 99.5%">
  <img src="https://img.shields.io/badge/License-MIT-blue?style=flat-square" alt="License">
</p>

---

## 📌 Tentang Proyek

**NetFly (PT. Netmedia Fiberoptik Lancarmulya)** adalah sistem terintegrasi yang dirancang untuk penyedia layanan jaringan (*Internet Service Provider / Managed Service Provider*) guna memantau ketersediaan infrastruktur (*uptime*), telemetri perangkat jaringan klien B2B secara *real-time*, serta pengelolaan tiket gangguan helpdesk.

Sistem terdiri dari 3 pilar platform utama:
1. **Website Company Profile:** Etalase digital korporat interaktif untuk publik, menyajikan profil perusahaan, katalog solusi IT, bukti rekam jejak portofolio mitra, dan form penawaran (*leads generator*).
2. **Website Admin (NOC Console):** Pusat kendali operasional internal bagi tim NOC dan administrator untuk mengelola master data customer, project, perangkat jaringan, telemetri live monitoring, alert insiden, eskalasi tiket gangguan, dan pencetakan laporan bulanan SLA resmi.
3. **Aplikasi Mobile Customer (B2B Client Portal):** Aplikasi mandiri khusus perwakilan klien perusahaan untuk memantau status kesehatan jaringan kantor (*Hijau/Kuning/Merah*), grafik pemakaian bandwidth, membuat tiket laporan gangguan disertai lampiran foto kamera/galeri, serta mengunduh berkas laporan bulanan resmi berformat PDF.

---

## 🏛️ Arsitektur & Prinsip Sistem

> **Prinsip Tata Kelola Akun:**  
> Customer tidak melakukan pendaftaran akun secara mandiri (*no self-registration*). Seluruh akun dibuat secara terpusat oleh Admin melalui Web Admin dan diverifikasi menggunakan token Laravel Sanctum. Customer hanya memiliki akses ke data dan perangkat yang terkait dengan entitas perusahaannya sendiri (*Strict Multi-Tenant Scoping*).

### Alur Integrasi Data:
```text
[ Perangkat Jaringan (Router / Switch / Firewall / AP) ]
                       │
                       ▼ (SNMP v2c/v3, ICMP Ping, REST API Telemetry)
[ Monitoring Service Engine (Daemon / Background Worker) ]
                       │
                       ▼ (Persistensi Data Deret Waktu)
[ Backend Core (Laravel 12) + Database Server + Redis ]
          │                                     │
          ▼ (Admin HTTPS)                       ▼ (RESTful API / Sanctum Token)
[ Website Admin (NOC Console) ]        [ Aplikasi Mobile Customer ]
```

---

## 🚀 Fitur Utama

### 1. Website Company Profile (Publik)
- **Hero & Slogan Korporat:** Proposisi nilai layanan dengan garansi SLA 99.5% dan peering IIX 10G.
- **Katalog Solusi:** Dedicated Internet 1:1, Microwave Wireless Broadband, IP-VPN Interbranch, Cloud Infrastructure, dan Cyber Security.
- **Portofolio Klien:** Galeri mitra institusi pendidikan, perhotelan, manufaktur, dan industri FMCG.
- **Formulir Permintaan Penawaran:** Jalur konsultasi teknis & audit kebutuhan jaringan bagi calon klien baru.

### 2. Website Admin (NOC Console)
- **Dashboard Ringkasan Operasional:** Kartu total customer aktif, project aktif, rasio perangkat online vs offline, dan papan peringatan insiden (*Live Alert Board*).
- **Master Customer & Project Management:** Tata kelola data kemitraan, alamat titik cabang, koordinat geografis (GPS), dan status kontrak.
- **Pusat Kredensial (Account Management):** Penerbitan username dan password awal akun customer B2B, kendali status aktif/nonaktif, dan fitur reset password.
- **Inventaris Perangkat Jaringan:** Manajemen router, switch, firewall, access point, IP address manajemen, lokasi rak, dan kredensial SNMP.
- **Live Telemetry & Monitoring:** Pengukuran ketersediaan online/offline, uptime, throughput bandwidth (Inbound/Outbound Mbps), CPU & RAM usage, packet loss, dan status antarmuka port.
- **Helpdesk Ticketing & PDF Report:** Penugasan teknisi lapangan, pembaruan tahapan status penanganan (`Open` ➔ `Assigned` ➔ `In Progress` ➔ `Resolved` ➔ `Closed`), dan ekspor berkas laporan resmi SLA bulanan ke PDF.

### 3. Aplikasi Mobile Customer (Android)
- **Login Terproteksi:** Autentikasi menggunakan akun resmi dari Admin (*tanpa fitur register*).
- **Home Service Health:** Kartu kesehatan jaringan universal (🟢 Normal, 🟡 Warning/Degradasi, 🔴 Gangguan/Down).
- **Service & Traffic:** Grafik pemakaian kuota dan bandwidth harian/mingguan.
- **Pengaduan Tiket Gangguan:** Input deskripsi kendala dengan lampiran bukti foto kamera/galeri.
- **Laporan SLA:** Unduh berkas dokumen laporan bulanan resmi ke penyimpanan lokal ponsel.

---

## 🛠️ Tech Stack

- **Backend Framework:** Laravel 12.x (PHP 8.2+)
- **Frontend Web:** Blade Templating, Tailwind CSS v4, JavaScript (ES Modules)
- **Asset Bundler:** Vite 6.x
- **API:** Restful Api
- **Database:** MySQL
- **Font & Tipografi:** Plus Jakarta Sans & JetBrains Mono

---

## 💻 Panduan Instalasi Lokal

### 1. Clone Repositori
```bash
git clone https://github.com/RifkiNizami/web-monitoring-jaringan.git
cd web-monitoring-jaringan
```

### 2. Install Dependensi PHP & JavaScript
```bash
composer install
npm install
```

### 3. Konfigurasi Environment
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan konfigurasi database pada `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_netfly_monitoring
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Migrasi & Seeder Database
```bash
php artisan migrate --seed
```

### 5. Jalankan Server Pengembangan
Jalankan server Laravel dan compiler asset Vite:
```bash
# Terminal 1 (Laravel Server)
php artisan serve

# Terminal 2 (Vite Hot-Reload)
npm run dev
```

Akses sistem di browser: `http://localhost:8000`

---

## 👥 Tim Pengembang

Proyek ini dikembangkan oleh tim 4 orang dengan spesialisasi modul fitur:
- **Developer 1:** Website Company Profile & Form Leads
- **Developer 2:** Autentikasi Admin & Dashboard Utama NOC
- **Developer 3:** Master Data Customer, Project & Account Management Klien B2B
- **Developer 4:** Inventaris Perangkat, Monitoring Telemetri, Tiket Helpdesk & Laporan PDF

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).
