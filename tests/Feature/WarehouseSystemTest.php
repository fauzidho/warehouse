<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\StockTransaction;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WarehouseSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('HALF KOPI');
        $response->assertSee('admin@halfkopi.com');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $admin = User::where('email', 'admin@halfkopi.com')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin Test',
                'email' => 'admin@halfkopi.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]);
        }

        $response = $this->post('/login', [
            'email' => 'admin@halfkopi.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_can_access_user_management(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/users');
        $response->assertStatus(200);
        $response->assertSee('Manajemen Pengguna');
    }

    public function test_pegawai_is_forbidden_from_user_management(): void
    {
        $pegawai = User::where('role', 'pegawai')->first();

        $response = $this->actingAs($pegawai)->get('/users');
        $response->assertStatus(403);
    }

    public function test_stock_in_increases_item_quantity(): void
    {
        $pegawai = User::where('role', 'pegawai')->first();
        $cat = Category::create(['name' => 'Kopi Test', 'slug' => 'kopi-test']);
        $item = Item::create([
            'category_id' => $cat->id,
            'name' => 'Biji Kopi Test',
            'unit' => 'kg',
            'minimum_stock' => 5,
            'current_stock' => 10,
            'cost_price' => 100000,
        ]);
        $initialStock = floatval($item->current_stock);

        $response = $this->actingAs($pegawai)->post('/stock-in', [
            'date' => Carbon::today()->toDateString(),
            'destination_or_reason' => 'Penerimaan Test',
            'reference_number' => 'TEST-PO-01',
            'items' => [
                [
                    'item_id' => $item->id,
                    'quantity' => 10,
                    'unit_cost' => 150000,
                    'expiry_date' => Carbon::today()->addMonths(3)->toDateString(),
                ]
            ]
        ]);

        $response->assertRedirect();
        $item->refresh();

        $this->assertEquals($initialStock + 10, floatval($item->current_stock));
    }

    public function test_stock_out_decreases_item_quantity(): void
    {
        $pegawai = User::where('role', 'pegawai')->first();
        $cat = Category::create(['name' => 'Kopi Test 2', 'slug' => 'kopi-test-2']);
        $item = Item::create([
            'category_id' => $cat->id,
            'name' => 'Biji Kopi Test 2',
            'unit' => 'kg',
            'minimum_stock' => 5,
            'current_stock' => 15,
            'cost_price' => 100000,
        ]);
        $initialStock = floatval($item->current_stock);

        $response = $this->actingAs($pegawai)->post('/stock-out', [
            'date' => Carbon::today()->toDateString(),
            'destination_or_reason' => 'Pemakaian Bar Espresso',
            'reference_number' => 'TEST-REQ-01',
            'items' => [
                [
                    'item_id' => $item->id,
                    'quantity' => 2,
                    'notes' => 'Test pemakaian shift pagi',
                ]
            ]
        ]);

        $response->assertRedirect();
        $item->refresh();

        $this->assertEquals($initialStock - 2, floatval($item->current_stock));
    }

    public function test_stock_out_fails_if_quantity_exceeds_stock(): void
    {
        $pegawai = User::where('role', 'pegawai')->first();
        $cat = Category::create(['name' => 'Kopi Test 3', 'slug' => 'kopi-test-3']);
        $item = Item::create([
            'category_id' => $cat->id,
            'name' => 'Biji Kopi Test 3',
            'unit' => 'kg',
            'minimum_stock' => 5,
            'current_stock' => 5,
            'cost_price' => 100000,
        ]);

        $excessQuantity = floatval($item->current_stock) + 99999;

        $response = $this->actingAs($pegawai)->post('/stock-out', [
            'date' => Carbon::today()->toDateString(),
            'destination_or_reason' => 'Pemakaian Bar Espresso',
            'items' => [
                [
                    'item_id' => $item->id,
                    'quantity' => $excessQuantity,
                ]
            ]
        ]);

        $response->assertSessionHasErrors(['items']);
    }
}
