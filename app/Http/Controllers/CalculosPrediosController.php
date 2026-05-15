<?php

namespace App\Http\Controllers;

use App\Models\PredioCalculoGeneral;
use Illuminate\Http\Request;

class CalculosPrediosController extends Controller
{
    public function index(Request $request)
    {
        

        return response()->json($calculos);
    }
}
