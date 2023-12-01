<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class ProjectTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 100; $i++) {
            Project::create([
                'name' => 'Project ' . $i,
                'reference' => Uuid::uuid4(),
                'description' => 'This is a description for project ' . $i,
                'created_by' => rand(1, 10),
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);
        }
    }
}
