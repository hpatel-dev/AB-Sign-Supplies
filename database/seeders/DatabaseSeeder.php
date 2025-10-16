<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CompanyInfo;
use App\Models\ContactMessage;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = Category::factory(5)->create();
        $suppliers = Supplier::factory(5)->create();

        Product::factory(20)
            ->recycle($categories)
            ->recycle($suppliers)
            ->create();

        CompanyInfo::factory()->create();

        ContactMessage::factory(5)->create();

        $this->call(RolesAndPermissionsSeeder::class);

        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@absigns.test',
        ]);
        $superAdmin->assignRole('SuperAdmin');

        $editor = User::factory()->create([
            'name' => 'Content Editor',
            'email' => 'editor@absigns.test',
        ]);
        $editor->assignRole('Editor');

        $viewer = User::factory()->create([
            'name' => 'Catalog Viewer',
            'email' => 'viewer@absigns.test',
        ]);
        $viewer->assignRole('Viewer');
    }
}
