<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
  use PasswordValidationRules;

  /**
   * Valida y crea un usuario.
   *
   * @param  array  $input
   * @return \App\Models\User
   */
  public function create(array $input)
  {
    Validator::make($input, [
      'ci' => ['required', 'integer', 'digits:8', 'unique:users,ci'],
      'correo' => ['required', 'string', 'email', 'max:255', 'unique:users,correo'],
      'nombre' => ['required', 'string', 'max:50'],
      'apellido' => ['required', 'string', 'max:50'],
      'departamento' => ['required', 'integer', 'exists:departamentos,id'],
      'telefono' => ['required', 'string', 'size:9', 'unique:users,telefono'],
      'password' => $this->passwordRules(),
    ])->validate();

    return User::create([
      'ci' => $input['ci'],
      'nombre' => $input['nombre'],
      'apellido' => $input['apellido'],
      'correo' => $input['correo'],
      'departamento_id' => $input['departamento'],
      'telefono' => $input['telefono'],
      'password' => Hash::make($input['password']),
      'rol' => 0, // 0 => Por verificar // 1 => Usuario // 2 => Guardia // 3 => Admin
    ]);
  }
}
