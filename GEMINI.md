# Aturan Pengembangan & Desain Proyek (NetFly)

## Aturan Wajib Tata Letak Font & Ruang Tipografi (Anti-Sempit & Anti-AI Slop)
Setiap kali melakukan modifikasi antarmuka (UI) baik di Web maupun Mobile:
1. **Pemeriksaan Lebar & Ruang Font (No Cramped Layouts):**
   - Selalu periksa alokasi ruang tipografi agar teks tidak tampak sempit, terjepit, atau saling bertumpuk.
   - Jangan memaksakan teks panjang ke dalam kolom atau kartu berdimensi sempit tanpa pembagian ruang yang jelas.
2. **Proporsi Bobot Flex pada Baris Berdampingan (Row Layout):**
   - Jika terdapat Label di kiri dan Nilai di kanan, wajib gunakan alokasi bobot proporsional (`Modifier.weight(...)` di Jetpack Compose atau `flex-1` / `min-w-0` di CSS) beserta `Spacer` / `gap` pemisah.
   - Hindari meletakkan dua teks tanpa batas bobot dalam satu baris horizontal yang dapat memicu tabrakan (*text collision / overlap*).
3. **Ruang Vertikal & Line Height:**
   - Gunakan `line-height` proporsional (minimal 1.3x – 1.5x ukuran font) agar teks multi-line nyaman dibaca dan tidak menempel satu sama lain.
   - Berikan padding vertikal yang cukup antar elemen teks.
4. **Perlindungan Terhadap Pemotongan Kata Cacat:**
   - Cegah pemotongan kata di tengah huruf (misal: "Inboun / d", "Outbo / und", atau "Terp / enuh / i").
   - Terapkan `maxLines = 1` dan `softWrap = false` / `TextOverflow.Ellipsis` untuk teks tunggal dinamis, atau gunakan layout bertingkat (*stacked block*) untuk data multi-baris seperti alamat kantor dan deskripsi.
5. **Standar Ukuran Font Minimal:**
   - Teks metrik & judul: 14–24sp (mobile) / 14–24px (web).
   - Teks isi & nilai data: 13–15sp (mobile) / 14–16px (web).
   - Label & caption: 11–12sp (mobile) / 12px (web). Hindari font <10sp.
6. **Larangan Kotak Status Badge / Pill "AI Slop" & Alternatif Indikator Bersih:**
   - **DILARANG** menaruh kotak/pill kecil berlatar warna transparan (`Surface` / `Box` dengan `color.copy(alpha = 0.12f)`) di sudut kanan atas header kartu (seperti kotak "Terpenuhi", "CIR 1:1 DEDICATED", "Aktif") karena memicu kesan *AI slop* dan menyempitkan ruang judul.
   - **Alternatif Resmi untuk Komponen Aktif / Status:**
     - Gunakan **Indikator Titik LED Inline Tanpa Kotak** (`StatusDot` / `PulsingStatusDot` 7–8dp + teks polos dengan `maxLines = 1, softWrap = false`).
     - Gunakan **Subjudul / Metadata Baris Bawah** di bawah judul utama kartu.
     - Gunakan **Garis Aksen Vertikal Kiri (3–4dp)** pada kartu sirkuit atau tiket untuk menandakan status operasional secara bersih.
