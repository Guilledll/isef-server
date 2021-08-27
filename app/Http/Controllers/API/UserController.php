<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
  function __construct()
  {
    $this->middleware('auth:sanctum')->only('show');
  }

  /**
   * Muestra todos los usuarios
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    return response()->json(User::all());
  }

  /**
   * Muestra un usuario especifico
   *
   * @param  User  $user
   * @return \Illuminate\Http\Response
   */
  public function show(User $user)
  {
    return response()->json($user);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    //
  }
}
