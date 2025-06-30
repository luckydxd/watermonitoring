<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(DeviceTypeSeeder::class);
        $this->call(DeviceSeeder::class);
        $this->call(DeviceAssignmentSeeder::class);
        $this->call(WaterConsumptionLogSeeder::class);
        $this->call(VisitorActivitiesSeeder::class);
        $this->call(SensorDataSeeder::class);
        $this->call(AppSettingSeeder::class);
        $this->call(AboutSettingSeeder::class);
        $this->call(PageSeeder::class);
        $this->call(LAndingPageSeeder::class);



        // $this->call(ComplaintSeeder::class);
    }
}
