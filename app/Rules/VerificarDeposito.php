<?php

namespace App\Rules;

use App\Models\Deposito;
use Illuminate\Contracts\Validation\Rule;

class VerificarDeposito implements Rule
{
  /**
   * Create a new rule instance.
   *
   * @return void
   */
  public function __construct($departamento_id)
  {
    $this->departamento_id = $departamento_id;
  }

  /**
   * Determine if the validation rule passes.
   *
   * @param  string  $attribute
   * @param  mixed  $value
   * @return bool
   */
  public function passes($attribute, $value)
  {
    return !Deposito::where([
      ['departamento_id', $this->departamento_id],
      ['nombre', $value]
    ])->exists();
  }

  /**
   * Get the validation error message.
   *
   * @return string
   */
  public function message()
  {
    return 'Ya existe un depósito con este nombre.';
  }
}
