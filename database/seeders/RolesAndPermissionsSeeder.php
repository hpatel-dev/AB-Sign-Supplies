<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        Artisan::call('shield:generate', [
            '--panel' => 'admin',
            '--all' => true,
            '--no-interaction' => true,
            '--silent' => true,
        ]);

        $permissions = Permission::all();

        $superAdminRole = Role::findOrCreate('SuperAdmin', 'web');
        $editorRole = Role::findOrCreate('Editor', 'web');
        $viewerRole = Role::findOrCreate('Viewer', 'web');

        $superAdminRole->syncPermissions($permissions);

        $editorRole->syncPermissions(
            $this->filterPermissions($permissions, ['product', 'company'], includeWidgets: true)
        );

        $viewerRole->syncPermissions(
            $permissions->filter(function (Permission $permission): bool {
                $name = $permission->name;

                if (str_starts_with($name, 'widget_')) {
                    return true;
                }

                if (! str_starts_with($name, 'view')) {
                    return false;
                }

                return str_contains($name, 'product')
                    || str_contains($name, 'company::info')
                    || str_contains($name, 'company');
            })
        );
    }

    /**
     * @param  Collection<int, Permission>  $permissions
     */
    protected function filterPermissions(Collection $permissions, array $keywords, bool $includeWidgets = false): Collection
    {
        return $permissions->filter(function (Permission $permission) use ($keywords, $includeWidgets): bool {
            $name = $permission->name;

            if ($includeWidgets && str_starts_with($name, 'widget_')) {
                return true;
            }

            foreach ($keywords as $keyword) {
                if (str_contains($name, $keyword)) {
                    return true;
                }
            }

            return false;
        });
    }
}

