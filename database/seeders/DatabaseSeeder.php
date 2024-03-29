<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Deposito;
use App\Models\Categoria;
use App\Models\Departamento;
use App\Models\Inventario;
use App\Models\Material;
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
    // Departamento::factory()->count(19)->create();
    // Deposito::factory()->count(30)->create();
    // User::factory()->count(40)->create();
    // Categoria::factory()->count(20)->create();
    // Material::factory()->count(100)->create();
    // Inventario::factory()->count(50)->create();

    $this->call(DepartamentoSeeder::class);
    $this->call(UserSeeder::class);
  }
}
