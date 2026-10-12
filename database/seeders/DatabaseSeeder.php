<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\StockTransaction;
use App\Models\StockTransactionItem;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users
        $admin = User::create([
            'name' => 'Budi Santoso (Admin)',
            'email' => 'admin@halfkopi.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '0812-3456-7890',
            'is_active' => true,
        ]);

        $pegawai1 = User::create([
            'name' => 'Dimas Pratama (Head Barista)',
            'email' => 'barista@halfkopi.com',
            'password' => Hash::make('password'),
            'role' => 'pegawai',
            'phone' => '0813-9876-5432',
            'is_active' => true,
        ]);

        $pegawai2 = User::create([
            'name' => 'Siti Nurhaliza (Staff Gudang)',
            'email' => 'staff.gudang@halfkopi.com',
            'password' => Hash::make('password'),
            'role' => 'pegawai',
            'phone' => '0811-2233-4455',
            'is_active' => true,
        ]);

        // 2. Categories
        $catBeans = Category::create([
            'name' => 'Biji Kopi (Beans)',
            'slug' => 'biji-kopi-beans',
            'icon' => 'coffee',
            'description' => 'Biji kopi sangrai untuk espresso based dan manual brew.',
        ]);

        $catDairy = Category::create([
            'name' => 'Susu & Dairy',
            'slug' => 'susu-dairy',
            'icon' => 'milk',
            'description' => 'Susu fresh milk, UHT, oatmilk, dan produk olahan susu.',
        ]);

        $catSyrup = Category::create([
            'name' => 'Sirup & Saus',
            'slug' => 'sirup-saus',
            'icon' => 'droplets',
            'description' => 'Perasa sirup konsentrat, saus caramel, cokelat, dan puree.',
        ]);

        $catPowder = Category::create([
            'name' => 'Bubuk Minuman (Powder)',
            'slug' => 'bubuk-minuman',
            'icon' => 'sparkles',
            'description' => 'Bubuk matcha, chocolate, red velvet, taro konsentrat.',
        ]);

        $catPackaging = Category::create([
            'name' => 'Kemasan & Disposable',
            'slug' => 'kemasan-disposable',
            'icon' => 'package',
            'description' => 'Cup hot & cold, straw, lid, paper bag, cup sleeve.',
        ]);

        $catCleaning = Category::create([
            'name' => 'Perlengkapan & Sanitasi',
            'slug' => 'perlengkapan-sanitasi',
            'icon' => 'shield-check',
            'description' => 'Detergen mesin espresso, filter kertas, sanitasi bar.',
        ]);

        // 3. Suppliers
        $supRoastery = Supplier::create([
            'name' => 'Roastery Nusantara Perkasa',
            'contact_person' => 'Hendrik Kurniawan',
            'phone' => '0821-1122-3344',
            'email' => 'order@roasterynusantara.id',
            'address' => 'Jl. Kopi Harum No. 12, Bandung',
        ]);

        $supDairy = Supplier::create([
            'name' => 'PT Sumber Dairy Segar',
            'contact_person' => 'Ratna Dewi',
            'phone' => '0812-8899-7766',
            'email' => 'sales@sumberdairy.co.id',
            'address' => 'Kawasan Industri Pulo Gadung, Jakarta Timur',
        ]);

        $supSyrup = Supplier::create([
            'name' => 'Sweet Flavours Indonesia',
            'contact_person' => 'Doni Saputra',
            'phone' => '0878-5544-3322',
            'email' => 'info@sweetflavours.com',
            'address' => 'Ruko Golden Boulevard Blok C-8, BSD City',
        ]);

        $supPack = Supplier::create([
            'name' => 'Kemas Kopi Packindo',
            'contact_person' => 'Linda Wijaya',
            'phone' => '0819-0022-8811',
            'email' => 'cs@kemaskopi.id',
            'address' => 'Jl. Daan Mogot KM 14, Jakarta Barat',
        ]);

        // 4. Items
        $itemsData = [
            // Beans
            [
                'category_id' => $catBeans->id,
                'sku' => 'BN-HB-01',
                'name' => 'House Blend Espresso (70/30)',
                'unit' => 'kg',
                'minimum_stock' => 10,
                'current_stock' => 28,
                'cost_price' => 135000,
                'description' => 'Blend Arabica Gayo & Robusta Temanggung, medium-dark roast untuk espresso.',
            ],
            [
                'category_id' => $catBeans->id,
                'sku' => 'BN-SO-GAYO',
                'name' => 'Single Origin Aceh Gayo Natural',
                'unit' => 'kg',
                'minimum_stock' => 5,
                'current_stock' => 8,
                'cost_price' => 190000,
                'description' => 'Light-medium roast untuk manual brew V60, floral & fruity notes.',
            ],
            [
                'category_id' => $catBeans->id,
                'sku' => 'BN-SO-ETH',
                'name' => 'Single Origin Ethiopia Yirgacheffe',
                'unit' => 'kg',
                'minimum_stock' => 4,
                'current_stock' => 2.5, // LOW STOCK!
                'cost_price' => 260000,
                'description' => 'Import specialty beans, jasmine aroma & bergamot acidity.',
            ],
            // Dairy
            [
                'category_id' => $catDairy->id,
                'sku' => 'DR-FM-01',
                'name' => 'Fresh Milk Pasteurisasi 1L (Diamond/Greenfields)',
                'unit' => 'liter',
                'minimum_stock' => 24,
                'current_stock' => 40,
                'cost_price' => 21000,
                'description' => 'Susu segar pasteurisasi dingin untuk latte art dan minuman kopi susu.',
            ],
            [
                'category_id' => $catDairy->id,
                'sku' => 'DR-OM-01',
                'name' => 'Oatside Barista Blend 1L',
                'unit' => 'liter',
                'minimum_stock' => 12,
                'current_stock' => 6, // LOW STOCK!
                'cost_price' => 38000,
                'description' => 'Plant-based oat milk ramah vegan dengan rasa creamy balance.',
            ],
            // Syrup
            [
                'category_id' => $catSyrup->id,
                'sku' => 'SY-VAN-01',
                'name' => 'Monin Syrup Vanilla 700ml',
                'unit' => 'botol',
                'minimum_stock' => 4,
                'current_stock' => 7,
                'cost_price' => 145000,
                'description' => 'Sirup vanilla aroma madagaskar murni.',
            ],
            [
                'category_id' => $catSyrup->id,
                'sku' => 'SY-CAR-01',
                'name' => 'Monin Syrup Salted Caramel 700ml',
                'unit' => 'botol',
                'minimum_stock' => 4,
                'current_stock' => 9,
                'cost_price' => 148000,
                'description' => 'Sirup salted caramel konsentrat.',
            ],
            [
                'category_id' => $catSyrup->id,
                'sku' => 'SY-HAZ-01',
                'name' => 'Monin Syrup Hazelnut 700ml',
                'unit' => 'botol',
                'minimum_stock' => 3,
                'current_stock' => 1, // LOW STOCK!
                'cost_price' => 145000,
                'description' => 'Sirup hazelnut kaya rasa kacang panggang.',
            ],
            // Powder
            [
                'category_id' => $catPowder->id,
                'sku' => 'PW-MTC-01',
                'name' => 'Pure Uji Matcha Powder 500g',
                'unit' => 'pack',
                'minimum_stock' => 3,
                'current_stock' => 5,
                'cost_price' => 185000,
                'description' => 'Ceremonial grade matcha powder asli Jepang untuk Matcha Latte.',
            ],
            [
                'category_id' => $catPowder->id,
                'sku' => 'PW-CHK-01',
                'name' => 'Dark Belgian Chocolate Powder 1kg',
                'unit' => 'pack',
                'minimum_stock' => 5,
                'current_stock' => 12,
                'cost_price' => 95000,
                'description' => 'Cokelat bubuk pekat 70% kakao blend.',
            ],
            // Packaging
            [
                'category_id' => $catPackaging->id,
                'sku' => 'PC-CP16-01',
                'name' => 'Cold Cup Injection 16oz + Strawless Lid',
                'unit' => 'dus (500pcs)',
                'minimum_stock' => 5,
                'current_stock' => 14,
                'cost_price' => 485000,
                'description' => 'Cup plastik tebal custom print logo Half Kopi + tutup cembung.',
            ],
            [
                'category_id' => $catPackaging->id,
                'sku' => 'PC-CP08-01',
                'name' => 'Hot Paper Cup 8oz Double Wall',
                'unit' => 'dus (500pcs)',
                'minimum_stock' => 3,
                'current_stock' => 4,
                'cost_price' => 375000,
                'description' => 'Cup panas tahan panas warna hitam dop emboss.',
            ],
            [
                'category_id' => $catPackaging->id,
                'sku' => 'PC-STR-01',
                'name' => 'Sedotan Kertas Steril Bungkus Kertas',
                'unit' => 'pack (100pcs)',
                'minimum_stock' => 10,
                'current_stock' => 25,
                'cost_price' => 22000,
                'description' => 'Eco-friendly biodegradable black paper straw.',
            ],
            // Cleaning
            [
                'category_id' => $catCleaning->id,
                'sku' => 'CL-CLN-01',
                'name' => 'Cafiza Espresso Cleaning Powder 566g',
                'unit' => 'botol',
                'minimum_stock' => 2,
                'current_stock' => 3,
                'cost_price' => 235000,
                'description' => 'Detergen bubuk pembersih backflush group head mesin espresso.',
            ],
        ];

        $items = [];
        foreach ($itemsData as $data) {
            $items[$data['sku']] = Item::create($data);
        }

        // 5. Sample Transactions
        // Transaksi Masuk (PO Penerimaan Barang dari Roastery Nusantara)
        $txIn1 = StockTransaction::create([
            'transaction_code' => 'IN-' . date('Ymd') . '-0001',
            'type' => 'in',
            'date' => Carbon::now()->subDays(3)->toDateString(),
            'supplier_id' => $supRoastery->id,
            'user_id' => $pegawai2->id,
            'destination_or_reason' => 'Penerimaan PO Bahan Baku Biji Kopi',
            'reference_number' => 'INV/RN/2026/1092',
            'notes' => 'Barang diterima dalam kondisi baik, segel utuh, roast date H-2.',
            'total_cost' => (20 * 135000) + (5 * 190000),
        ]);

        StockTransactionItem::create([
            'stock_transaction_id' => $txIn1->id,
            'item_id' => $items['BN-HB-01']->id,
            'quantity' => 20,
            'unit_cost' => 135000,
            'subtotal' => 20 * 135000,
            'expiry_date' => Carbon::now()->addMonths(6)->toDateString(),
            'notes' => 'Batch Roasting #441',
        ]);

        StockTransactionItem::create([
            'stock_transaction_id' => $txIn1->id,
            'item_id' => $items['BN-SO-GAYO']->id,
            'quantity' => 5,
            'unit_cost' => 190000,
            'subtotal' => 5 * 190000,
            'expiry_date' => Carbon::now()->addMonths(6)->toDateString(),
            'notes' => 'Batch Roasting #442',
        ]);

        // Transaksi Masuk Susu
        $txIn2 = StockTransaction::create([
            'transaction_code' => 'IN-' . date('Ymd') . '-0002',
            'type' => 'in',
            'date' => Carbon::now()->subDays(1)->toDateString(),
            'supplier_id' => $supDairy->id,
            'user_id' => $admin->id,
            'destination_or_reason' => 'Penerimaan Rutin Susu Fresh Milk & Oatmilk',
            'reference_number' => 'SJ-DS-88219',
            'notes' => 'Suhu chiller pengiriman 3°C, dingin optimal.',
            'total_cost' => (30 * 21000) + (10 * 38000),
        ]);

        StockTransactionItem::create([
            'stock_transaction_id' => $txIn2->id,
            'item_id' => $items['DR-FM-01']->id,
            'quantity' => 30,
            'unit_cost' => 21000,
            'subtotal' => 30 * 21000,
            'expiry_date' => Carbon::now()->addDays(12)->toDateString(),
            'notes' => 'Exp date 12 hari lagi (Chilled)',
        ]);

        StockTransactionItem::create([
            'stock_transaction_id' => $txIn2->id,
            'item_id' => $items['DR-OM-01']->id,
            'quantity' => 10,
            'unit_cost' => 38000,
            'subtotal' => 10 * 38000,
            'expiry_date' => Carbon::now()->addMonths(8)->toDateString(),
            'notes' => 'Dus segel rapat',
        ]);

        // Transaksi Stok Keluar (Pemakaian Bar Utama oleh Barista)
        $txOut1 = StockTransaction::create([
            'transaction_code' => 'OUT-' . date('Ymd') . '-0001',
            'type' => 'out',
            'date' => Carbon::now()->subDays(1)->toDateString(),
            'supplier_id' => null,
            'user_id' => $pegawai1->id,
            'destination_or_reason' => 'Pemakaian Operasional Bar Espresso',
            'reference_number' => 'REQ-BAR-041',
            'notes' => 'Permintaan bahan shift pagi dan sore.',
            'total_cost' => (4 * 135000) + (12 * 21000) + (1 * 145000),
        ]);

        StockTransactionItem::create([
            'stock_transaction_id' => $txOut1->id,
            'item_id' => $items['BN-HB-01']->id,
            'quantity' => 4,
            'unit_cost' => 135000,
            'subtotal' => 4 * 135000,
            'notes' => 'Dipakai di hopper mesin La Marzocco',
        ]);

        StockTransactionItem::create([
            'stock_transaction_id' => $txOut1->id,
            'item_id' => $items['DR-FM-01']->id,
            'quantity' => 12,
            'unit_cost' => 21000,
            'subtotal' => 12 * 21000,
            'notes' => 'Dipindahkan ke undercounter chiller bar',
        ]);

        StockTransactionItem::create([
            'stock_transaction_id' => $txOut1->id,
            'item_id' => $items['SY-VAN-01']->id,
            'quantity' => 1,
            'unit_cost' => 145000,
            'subtotal' => 1 * 145000,
            'notes' => 'Restock sirup bar station',
        ]);

        // Transaksi Stok Keluar - Spillage / Waste
        $txOut2 = StockTransaction::create([
            'transaction_code' => 'OUT-' . date('Ymd') . '-0002',
            'type' => 'out',
            'date' => Carbon::now()->toDateString(),
            'supplier_id' => null,
            'user_id' => $pegawai1->id,
            'destination_or_reason' => 'Bahan Rusak / Basi / Spillage',
            'reference_number' => 'WASTE-2026-10-13',
            'notes' => '1 botol sirup hazelnut terjatuh pecah saat restock display.',
            'total_cost' => 1 * 145000,
        ]);

        StockTransactionItem::create([
            'stock_transaction_id' => $txOut2->id,
            'item_id' => $items['SY-HAZ-01']->id,
            'quantity' => 1,
            'unit_cost' => 145000,
            'subtotal' => 1 * 145000,
            'notes' => 'Pecah di lantai bar, difoto untuk bukti waste',
        ]);
    }
}
