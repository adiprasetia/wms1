# Fitur Input Barang Masuk (Goods In)

## Deskripsi
Halaman input barang masuk yang mengintegrasikan pembuatan batch, penambahan stock, dan pencatatan history transaksi dalam satu alur kerja yang terkoneksi.

## Fitur Utama

### 1. Input Form Barang Masuk
Terdiri dari 3 bagian utama:

#### Informasi Pengiriman
- **Nomor Referensi**: Auto-generate dengan format `GI-YYYYMMDD-XXXX`
- **Supplier**: Pilih supplier yang mengirim barang
- **Catatan**: Tempat untuk mencatat informasi tambahan

#### Detail Produk
- **Produk**: Pilih produk yang diterima
- **Jumlah**: Masukkan jumlah barang yang diterima
- **Lokasi Penyimpanan**: Pilih lokasi di warehouse

#### Informasi Batch
- **Kode Batch**: Identifikasi batch dari supplier
- **Tanggal Produksi**: Kapan batch diproduksi
- **Tanggal Kadaluarsa**: Kapan batch kadaluarsa

### 2. Proses Otomatis "Proses Masuk"
Dengan satu klik tombol "Proses Masuk", sistem akan otomatis:

#### A. Membuat/Update Batch
- Cek apakah batch dengan kode yang sama sudah ada
- Jika belum ada: Buat batch baru
- Jika sudah ada: Tambahkan quantity ke batch yang ada

#### B. Membuat/Update Stock
- Cek apakah stock untuk kombinasi product + batch + location sudah ada
- Jika belum ada: Buat record stock baru
- Jika sudah ada: Tambahkan quantity ke stock yang ada

#### C. Catat History (Stock Movement)
- Otomatis membuat record di tabel `stock_movements`
- Type: IN (barang masuk)
- Reference: Nomor referensi GoodsIn
- Tanggal: Hari ini
- Semua detail: product, batch, location, quantity

#### D. Update Status
- Status berubah dari `pending` menjadi `completed`
- Record GoodsIn tersimpan beserta batch_id yang digunakan

### 3. Status Management
- **Pending**: Barang baru dicatat, belum diproses
- **Completed**: Barang sudah diproses dan masuk ke sistem
- **Cancelled**: Barang dibatalkan (opsional)

### 4. Tabel & Filter
Tampilkan semua barang masuk dengan fitur:
- Filter berdasarkan status
- Filter berdasarkan supplier
- Sorting dan search
- Kolom: No Ref, Supplier, Produk, Jumlah, Batch Code, Lokasi, Status, Tanggal

## Model & Database

### Model GoodsIn
```php
- reference_number (string, unique)
- supplier_id (foreign key)
- product_id (foreign key)
- batch_id (foreign key, nullable)
- batch_code (string)
- manufacture_date (date)
- expiry_date (date)
- quantity (integer)
- location_id (foreign key)
- notes (text, nullable)
- status (enum: pending, completed, cancelled)
```

### Relasi
- GoodsIn → Supplier
- GoodsIn → Product
- GoodsIn → Location
- GoodsIn → Batch
- GoodsIn → Stock
- GoodsIn → StockMovement

## Navigasi
Menu ditemukan di sidebar Filament dengan label "Barang Masuk" dan icon panah ke bawah.

## Use Case

### Contoh Penggunaan:
1. Supplier "PT ABC" mengirimkan 100 unit produk "Widget A"
2. Batch code: WIDGET-20250101-001
3. User membuka form "Input Barang Masuk"
4. Isi form dengan data di atas
5. Klik tombol "Input Barang Masuk" untuk save draft
6. Kembali ke list, lihat status "Pending"
7. Klik "Proses Masuk" pada record tadi
8. Sistem otomatis:
   - Membuat batch WIDGET-20250101-001 jika belum ada
   - Membuat/update stock untuk batch tersebut di lokasi yang ditentukan
   - Mencatat history di StockMovement dengan type IN
   - Mengubah status menjadi "Completed"
9. Selesai! Barang sudah terinput di sistem.

## File-File yang Dibuat

### Models
- `app/Models/GoodsIn.php`

### Filament Resource
- `app/Filament/Resources/GoodsInResource.php`
- `app/Filament/Resources/GoodsInResource/Schemas/GoodsInForm.php`
- `app/Filament/Resources/GoodsInResource/Tables/GoodsInTable.php`
- `app/Filament/Resources/GoodsInResource/Pages/ListGoodsIns.php`
- `app/Filament/Resources/GoodsInResource/Pages/CreateGoodsIn.php`
- `app/Filament/Resources/GoodsInResource/Pages/EditGoodsIn.php`

### Database
- `database/migrations/2025_12_31_100000_create_goods_ins_table.php`

### Updated Models
- `app/Models/StockMovement.php` - Ditambah fillable dan relasi
- `app/Models/Location.php` - Ditambah field 'name' di fillable dan relasi GoodsIn
- `app/Models/Supplier.php` - Ditambah relasi GoodsIn

## Testing

Untuk menguji fitur:
1. Pastikan sudah ada data:
   - Minimal 1 supplier
   - Minimal 1 product
   - Minimal 1 location
2. Klik menu "Barang Masuk"
3. Klik tombol "Input Barang Masuk"
4. Isi semua field dan submit
5. Lihat status berubah menjadi "Pending"
6. Klik "Proses Masuk"
7. Check tabel Batch, Stock, dan StockMovement apakah data sudah terbuat
