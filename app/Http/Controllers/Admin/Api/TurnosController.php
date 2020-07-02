<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use App\Models\Turnero;
use App\Models\Paciente;
use DebugBar\DebugBar;

class TurnosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $hora_turno = $request->input('q');
        $dia_turno = $request->input('dia-inicio-turno');
        $dia_turno = "2020-06-14";
        //OBTENGO LOS TURNOS CREADOS
        $tiempo_turno = Config::get('settings.tiempo_por_turno');
        $hora_inicio = Carbon::createFromFormat("H:i", '6:00');
        $cierre_turnos = Carbon::createFromFormat("H:i", '21:00');
        $arr_horarios_disponibles = array('current_page' => 1);
        while ($hora_inicio < $cierre_turnos) {
            //CREO TODOS LOS HORARIOS PARA ESE DÍA
            $turno_ocupado = Turnero::where('inicio_turno', 'LIKE', $dia_turno . ' ' . $hora_inicio->format("H:i") . '%')->first();
            // echo "<pre>";
            // var_dump($turno_ocupado[0]);
            // echo "</pre>";
            if (isset($turno_ocupado->inicio_turno)) {
                if (Carbon::parse($turno_ocupado->inicio_turno)->format("H:i") == $hora_inicio->format("H:i")) {
                    $paciente = $turno_ocupado->paciente()->first();
                    // echo "<pre>";
                    // var_dump($paciente->apellido);
                    // echo "</pre>";
                    $arr_horarios_disponibles['data'][] = array(
                        "id" => $hora_inicio->format('Y-m-d H:i:s'),
                        "inicio_turno" => $hora_inicio->format('H:i') . ' | Reservado a ' . $paciente->apellido . ', ' . $paciente->nombre,
                    );
                } else {
                    $arr_horarios_disponibles['data'][] = array(
                        "id" => $hora_inicio->format('Y-m-d H:i:s'),
                        "inicio_turno" => $hora_inicio->format('H:i'),
                    );
                }
            } else {
                $arr_horarios_disponibles['data'][] = array(
                    "id" => $hora_inicio->format('Y-m-d H:i:s'),
                    "inicio_turno" => $hora_inicio->format('H:i'),
                );
            }
            $hora_inicio->addMinutes($tiempo_turno);
        }
        return $arr_horarios_disponibles;
        //$results = Turnero::paginate(10);
        //return $results;
    }

    public function show($id)
    {
        return Turnero::find($id);
    }
}
