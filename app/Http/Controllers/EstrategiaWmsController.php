<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EstrategiaWms;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\EstrategiaWmsHorarioPrioridade;

/**
 * @OA\Info(title="API de Estratégia WMS", version="1.0")
*/

class EstrategiaWmsController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/estrategiaWMS",
     *     summary="Cria uma nova estratégia",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="dsEstrategia", type="string"),
     *             @OA\Property(property="nrPrioridade", type="integer"),
     *             @OA\Property(
     *                 property="horarios",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="dsHorarioInicio", type="string"),
     *                     @OA\Property(property="dsHorarioFinal", type="string"),
     *                     @OA\Property(property="nrPrioridade", type="integer")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Dados salvos com sucesso"),
     *     @OA\Response(response=422, description="Erro de validação")
     * )
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dsEstrategia' => 'required|string',
            'nrPrioridade' => 'required|integer',
            'horarios' => 'required|array',
            'horarios.*.dsHorarioInicio' => 'required|string',
            'horarios.*.dsHorarioFinal' => 'required|string',
            'horarios.*.nrPrioridade' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $estrategia = EstrategiaWms::create([
                'ds_estrategia_wms' => $request->dsEstrategia,
                'nr_prioridade' => $request->nrPrioridade,
            ]);

            foreach ($request->horarios as $horario) {
                EstrategiaWmsHorarioPrioridade::create([
                    'cd_estrategia_wms' => $estrategia->cd_estrategia_wms,
                    'ds_horario_inicio' => $horario['dsHorarioInicio'],
                    'ds_horario_final' => $horario['dsHorarioFinal'],
                    'nr_prioridade' => $horario['nrPrioridade'],
                ]);
            }

            DB::commit();

            return response()->json(['message' => 'Dados salvos com sucesso'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erro ao salvar dados', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/estrategiaWMS/{cdEstrategia}/{dsHora}/{dsMinuto}/prioridade",
     *     summary="Obtém a prioridade com base na hora e minuto informados",
     *     @OA\Parameter(
     *         name="cdEstrategia",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="dsHora",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="dsMinuto",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Prioridade retornada com sucesso"),
     *     @OA\Response(response=404, description="Estratégia não encontrada")
     * )
     */

    public function show($cdEstrategia, $dsHora, $dsMinuto)
    {
        if (empty($cdEstrategia) || empty($dsHora) || empty($dsMinuto)) {
            return response()->json(['error' => 'Parâmetros inválidos'], 400);
        }

        $horaCompleta = $dsHora . ':' . $dsMinuto;

        $estrategia = EstrategiaWms::find($cdEstrategia);

        if (!$estrategia) {
            return response()->json(['error' => 'Estratégia não encontrada'], 404);
        }

        $horario = EstrategiaWmsHorarioPrioridade::where('cd_estrategia_wms', $cdEstrategia)
            ->where('ds_horario_inicio', '<=', $horaCompleta)
            ->where('ds_horario_final', '>=', $horaCompleta)
            ->first();

        $prioridade = $horario ? $horario->nr_prioridade : $estrategia->nr_prioridade;

        return response()->json(['prioridade' => $prioridade], 200);
    }

}
