<?php

namespace Database\Seeders;

use App\Models\Departamento;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    $dep = Departamento::factory()->create();
    User::factory()->count(10)->for($dep)->create();
    $this->call(UserSeeder::class);
  }
}
