<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PartidoPolitico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PartidoPoliticoController extends Controller
{
    public function index()
    {
        return view('page.partidoPolitico.index');
    }


    public function getListPartidoPoliticos(Request $request)
    {
        $query = $request->input('q');

        $pageSize = $request->input('pageSize', 10);

        $partido_politicos = PartidoPolitico::select(
            'id',
            'nombre',
            'partido_politico',
            'logo',
            'orden',
            'estado'
        )
            ->where('nombre', 'like', "%$query%")
            ->orderBy('id', 'desc');


        // $partido_politicos->paginate($pageSize);


        $paginatedResults = $partido_politicos->paginate($pageSize);


        // Transformar los resultados antes de devolverlos como JSON
        $modifiedResults = $paginatedResults->getCollection()->map(function ($item) {
            // Convertir el campo 'url' a una URL utilizando Storage::url()
            if (isset($item->logo)) {
                $item->logo = Storage::url($item->logo);
            }
            return $item;
        });

        // Actualizar la colección de resultados paginados con la colección modificada
        $paginatedResults->setCollection($modifiedResults);

        return response()->json($paginatedResults);
    }

    public function postShowPartido(Request $request)
    {
        $query = $request->input('params');
        $partido_politico = PartidoPolitico::select(
            'id',
            'nombre',
            'partido_politico',
            'logo',
            'orden',
            'estado'
        )
            ->where('id', '=', $query)->first();
        // Modificar el logo si está definido
        if (isset($partido_politico->logo)) {
            $partido_politico->logo = Storage::url($partido_politico->logo);
        }
        return response()->json($partido_politico);
    }

    public function agregarPartido(Request $request)
    {
        try {
            DB::beginTransaction();

            $logo_input = $request->file('logo');
            $nombre_input = $request->input('nombre');
            $orden_input = $request->input('orden');



            $ruta_logo = Storage::put('public/logo-partidos', $logo_input);

            $partido = new PartidoPolitico();
            $partido->nombre = $nombre_input;
            $partido->partido_politico = $nombre_input;

            $partido->logo = $ruta_logo;
            $partido->orden = $orden_input;

            if ($partido->save()) {
                $partido_id = $partido->id;
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'La informacíon se guardó correctamente'
                ]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'La informacíon no se guardó'
            ]);
            throw $th;
        }
    }



    public function postUpdatePartido(Request $request)
    {
        try {
            DB::beginTransaction();
            $logo_input = $request->file('logo');
            $nombre_input = $request->input('nombre');
            $orden_input = $request->input('orden');

            $partido = PartidoPolitico::where('id', '=', $request->id)->first();
            $partido->nombre = $nombre_input;
            $partido->partido_politico = $nombre_input;
            if ($logo_input) {
                $ruta_logo = Storage::put('public/logo-partidos', $logo_input);
                $partido->logo = $ruta_logo;
            }
            $partido->orden = $orden_input;


            if ($partido->save()) {
                $partido_id = $partido->id;
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'La informacíon se actualizo correctamente'
                ]);
            }
        } catch (\Throwable $th) {
            // DB::rollBack();
            // return response()->json([
            //     'status' => false,
            //     'message' => 'La informacíon no se guardó'
            // ]);
            throw $th;
        }
    }


    public function postDeletePartido(Request $request)
    {
        try {
            $partido = PartidoPolitico::findOrFail($request->id);
            $partido->estado = 0;
            if ($partido->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'La informacíon se eliminó correctamente'
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function postActivePartido(Request $request)
    {
        try {
            $partido = PartidoPolitico::findOrFail($request->id);
            $partido->estado = 1;
            if ($partido->save()) {
                return response()->json([
                    'status' => true,
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
