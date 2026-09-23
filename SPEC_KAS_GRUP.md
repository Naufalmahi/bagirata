# SPESIFIKASI FITUR KAS GRUP (GROUP TREASURY)

## 1. Functional Requirements
- **Create Group Treasury**: Otomatis atau manual inisialisasi wallet grup saat grup dibuat.
- **Cash In (Pemasukan Kas)**: Pencatatan dana masuk (iuran, patungan bulanan, donasi) dengan kategori dan opsional lampiran bukti/struk.
- **Cash Out (Pengeluaran Kas)**: Pencatatan penggunaan dana kas untuk keperluan bersama (beli konsumsi, sewa tempat, dll).
- **Recorded Balance**: Perhitungan saldo berjalan secara real-time berdasarkan *double-entry/single-entry ledger* (`Opening Balance` + `Cash In` - `Cash Out`).
- **Transaction History**: Daftar riwayat transaksi kas lengkap dengan filter kategori, status, dan pencari.
- **Category**: Pengelompokan transaksi (Iuran, Konsumsi, Logistik, Lainnya).
- **Receipt Attachment**: Upload file bukti/struk transaksi (`storage/public/treasury-receipts`).
- **Approval Workflow**: Transaksi kas tertentu (atau nominal besar/oleh non-admin) melalui tahap: `Created` → `Pending Approval` → `Approved` / `Rejected`.
- **Audit Log**: Setiap mutasi, pembuatan, persetujuan, atau penolakan wajib tercatat di tabel `audit_logs` tanpa silent update.
- **Single Page Architecture**: Seluruh fungsionalitas Kas Grup ditampilkan dalam satu halaman utama grup (`/groups/{group}/treasury`) menggunakan komponen terintegrasi (Alpine.js / Livewire / Blade partials).

---

## 2. Database Schema (Migration Structure)

### `group_wallets`
- `id` (PK, bigint)
- `group_id` (unsignedBigInteger, foreign key to groups)
- `name` (string, default: 'Kas Utama')
- `balance` (integer, default: 0 - disimpan dalam satuan rupiah bulat)
- `timestamps`, `softDeletes`

### `wallet_entries`
- `id` (PK, bigint)
- `group_wallet_id` (unsignedBigInteger, foreign key to group_wallets)
- `user_id` (unsignedBigInteger, pencatat transaksi)
- `type` (enum: `in`, `out`)
- `category` (string: `iuran`, `konsumsi`, `sewa`, `lainnya`)
- `amount` (integer, unsigned)
- `description` (text, nullable)
- `receipt_photo` (string, nullable)
- `status` (enum: `pending`, `approved`, `rejected`), default: `approved` (atau `pending` jika butuh approval)
- `approved_by` (unsignedBigInteger, nullable, foreign key to users)
- `timestamps`, `softDeletes`

### `wallet_approvals` (Opsional / Log Approval Detail)
- `id` (PK, bigint)
- `wallet_entry_id` (unsignedBigInteger)
- `user_id` (unsignedBigInteger, reviewer)
- `action` (enum: `approved`, `rejected`)
- `note` (text, nullable)
- `timestamps`

---

## 3. Ledger Architecture
- Prinsip pencatatan bersifat **Append-Only Ledger**.
- Saldo (`balance` di `group_wallets`) diperbarui secara transaksional (`DB::transaction`) setiap kali sebuah `wallet_entry` berstatus `approved` ditambahkan, diubah, atau dihapus.
- Formula: 
  $$\text{Current Balance} = \sum (\text{Approved Cash In}) - \sum (\text{Approved Cash Out})$$
- Mencegah inkonsistensi saldo dengan memanfaatkan database row locking (`lockForUpdate()`) saat mutasi saldo terjadi.

---

## 4. API Endpoints
- `GET /api/v1/groups/{group}/treasury` — Ambil ringkasan saldo & riwayat mutasi kas.
- `POST /api/v1/groups/{group}/treasury/entries` — Catat transaksi baru (`in` / `out`).
- `POST /api/v1/groups/{group}/treasury/entries/{entry}/approve` — Setujui transaksi kas.
- `POST /api/v1/groups/{group}/treasury/entries/{entry}/reject` — Tolak transaksi kas.

---

## 5. Approval Flow
1. **Pencatatan**: Anggota atau pengurus mencatat transaksi Cash In / Cash Out. Jika nominal melampaui batas kebijakan atau diinput oleh non-admin, status otomatis `pending`.
2. **Review**: Pengurus/Owner melihat daftar pending di halaman Kas Grup.
3. **Keputusan**: 
   - **Approve**: Status berubah menjadi `approved`, saldo wallet otomatis bertambah (jika `in`) atau berkurang (jika `out`).
   - **Reject**: Status berubah menjadi `rejected`, saldo tidak berubah.
4. **Audit**: Setiap perubahan status dicatat ke `audit_logs`.

---

## 6. Security & Authorization
- **Policy (`GroupWalletPolicy` / `WalletEntryPolicy`)**: 
  - Hanya member grup yang dapat melihat saldo & riwayat.
  - Hanya Role dengan permission `manage_treasury` (biasanya Owner/Treasurer) yang dapat melakukan `approve` / `reject`.
  - Validasi ketat bahwa nominal berupa integer positif, mencegah manipulasi angka negatif.

---

## 7. Audit Logging
- Setiap aksi (`treasury_created`, `wallet_entry_reported`, `wallet_entry_approved`, `wallet_entry_rejected`) wajib memanggil trait `LogsActivity` atau mencatat manual ke `audit_logs` menyertakan `user_id`, `before`, dan `after` properties.

---

## 8. Single Page UI Design (`/groups/{group}/treasury`)
Halaman tunggal mencakup:
1. **Header & Summary Card**: Menampilkan Saldo Total Kas, Total Pemasukan, Total Pengeluaran, dan tombol aksi `+ Catat Masuk` / `+ Catat Keluar`.
2. **Tab / Filter Section**: Filter berdasarkan status (`All`, `Pending`, `Approved`, `Rejected`) dan kategori.
3. **Pending Approvals Box**: Kotak khusus bagi Admin untuk menyetujui transaksi tertunda.
4. **Transaction Table / List**: Tabel riwayat mutasi lengkap dengan badge status, nominal berwarna (hijau untuk masuk, merah untuk keluar), nama pencatat, dan tombol lihat struk.
5. **Modal Form**: Form interaktif Alpine.js untuk input Cash In / Cash Out tanpa reload halaman.

---

## 9. Testing Strategy
- **Feature Tests (`TreasuryTest.php`)**:
  - Test member bisa melihat kas.
  - Test catat kas masuk & keluar menambah/mengurangi saldo dengan benar.
  - Test approval flow (pending → approved mengubah saldo, rejected tidak mengubah saldo).
  - Test otorisasi (non-member / member tanpa hak akses ditolak).

---

## 10. Edge Cases
- **Penghapusan/Pembatalan Transaksi yang Sudah Approved**: Harus membalikkan (reversing) saldo wallet secara proporsional dan mencatatnya di audit log.
- **Race Condition**: Mutasi saldo bersamaan menggunakan `DB::transaction` + `lockForUpdate()`.
- **Nominal 0 atau Negatif**: Dicegah di Form Request validation.

---

## 11. Definition of Done (DoD)
- Migrasi database dan model relasi (`GroupWallet`, `WalletEntry`) selesai dan tereksekusi tanpa error.
- Logika ledger saldo akurat dan teruji dengan unit/feature test.
- Halaman UI single-page (`/groups/{group}/treasury`) interaktif dan responsif.
- Fitur Approval dan Audit Log berjalan sesuai spesifikasi tanpa ada silent update.
- Seluruh test suite (`php artisan test`) hijau (lulus 100%).
