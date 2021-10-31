<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialReservado extends Model
{
  use HasFactory;


  /**  MaterialReservado -> Material (1:1) */
  public function material()
  {
    return $this->belongsTo(Material::class);
  }
  /**  MaterialReservado -> Reserva (1:1) */
  public function reserva()
  {
    return $this->belongsTo(Reserva::class);
  }

}
