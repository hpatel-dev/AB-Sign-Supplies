<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyInfo;
use App\Models\CompanyService;
use App\Models\ContactMessage;
use App\Models\Product;
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
        Product::factory(20)->create();

        CompanyInfo::factory()->create();

        Company::factory(3)
            ->create()
            ->each(function (Company $company, int $index): void {
                $services = [
                    ['title' => 'Wayfinding Strategy', 'description' => 'Needs assessments, nomenclature plans, and site logistics.'],
                    ['title' => 'Fabrication & Installation', 'description' => 'Managed production and installation for complex rollouts.'],
                    ['title' => 'Maintenance Programs', 'description' => 'Preventative maintenance and emergency service plans.'],
                ];

                CompanyService::factory()
                    ->count(count($services))
                    ->sequence(...$services)
                    ->for($company)
                    ->create();

                $company->update(['sort_order' => $index]);
            });

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
