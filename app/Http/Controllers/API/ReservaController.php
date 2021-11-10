<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reserva\IniciarReservaRequest;
use App\Http\Requests\Reserva\StoreReservaRequest;
use App\Http\Resources\MaterialResource;
use App\Models\Material;
use App\Models\Reserva;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservaController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    //
  }

  /**
   * Devuelve los materiales disponibles para la reserva
   *
   * @return \Illuminate\Http\Response
   */
  public function iniciar(IniciarReservaRequest $request)
  {
    $inicio = new Carbon($request->inicio);
    $fin = new Carbon($request->fin);

    // Todas las reservas que entren en el horarios indicado
    $reservas = Reserva::where('deposito_id', $request->deposito_id)
      ->where(
        fn ($query) => $query->where('fin', '>', $inicio)
          ->where('inicio', '<', $inicio)
      )->orWhere(
        fn ($query) => $query->where('inicio', '>=', $inicio)
          ->where('inicio', '<', $fin)
      )
      ->with('materiales')
      ->get();

    // matOcupados = Los materiales que esten siendo usados
    // matDisponibles = Los materiales disponibles para el usuario
    // matIDs = ID de los materiales ocupados
    $matOcupados = $matDisponibles = $matIDs = array();

    // De esas reservas todos los materiales reservados
    foreach ($reservas as $reserva) {
      foreach ($reserva['materiales'] as $material) {
        // Si el material ya esta en el arreglo le sumo la nueva cantidad
        if (isset($matOcupados[$material->material_id])) {
          $matOcupados[$material->material_id]->cantidad += $material->cantidad;
        } else { // si no esta lo agrego
          $matOcupados[$material->material_id] = $material;
          array_push($matIDs, $material->material_id);
        }
      }
    }

    // Busco los materiales que encontre reservados
    $materialesReservados = Material::whereIn('id', $matIDs)
      ->with(['deposito', 'categoria'])
      ->get();

    // Verifico que la diferencia de cantidades entre los utilizados
    // y los existentes sea > 0
    foreach ($materialesReservados as $mat) {
      if ($mat->cantidad - $matOcupados[$mat->id]->cantidad > 0) {
        // Resto lo ocupado
        $mat->cantidad -= $matOcupados[$mat->id]->cantidad;
        array_push($matDisponibles, $mat);
      };
    }

    // Los materiales que no estan reservados
    $matLibres = Material::whereNotIn('id', $matIDs)
      ->where('deposito_id', $request->deposito_id)
      ->with(['deposito', 'categoria'])
      ->get();

    // Combino el array de no utilizados con el de cantidad
    // disponible calculado antes
    $materiales = $matLibres->merge($matDisponibles);

    return MaterialResource::collection($materiales);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(StoreReservaRequest $request)
  {
    $reserva = Reserva::create([
      'user_ci' => $request->user_ci,
      'inicio' => new Carbon($request->inicio),
      'fin' => new Carbon($request->fin),
      'deposito_id' => $request->deposito_id,
      'lugar' => $request->lugar,
      'razon' => $request->razon,
      'estado' => $request->validar ? 1 : 2,
      'nota_usuario' => $request->notas,
    ]);

    $materiales = array();

    foreach ($request->materiales as $material) {
      array_push($materiales, [
        'reserva_id' => $reserva->id,
        'material_id' => $material['id'],
        'cantidad' => $material['cantidad'],
      ]);
    }

    DB::table('materiales_reservados')->insert($materiales);

    return response()->json(['message' => 'Reserva realizada con éxito!']);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Reserva  $reserva
   * @return \Illuminate\Http\Response
   */
  public function show(Reserva $reserva)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Reserva  $reserva
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Reserva $reserva)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Reserva  $reserva
   * @return \Illuminate\Http\Response
   */
  public function destroy(Reserva $reserva)
  {
    //
  }
}
