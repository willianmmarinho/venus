<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AtendentePlantonistaController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /*
/--------------------------------------------------------------------------
/              Controller de Atendentes Plantonistas
/
/ #Função de Marcar o Horário ao qual os plantonistas devem comparecer à comunhão
/--------------------------------------------------------------------------
*/

    public function index(Request $request)
    {
        try {
            $pesquisaNome = $request->input('nome');
            $pesquisaCpf = $request->input('cpf');

            //Pesquisa Mal Otimisada
            if ($pesquisaNome) {
                $atendente = DB::table('atendente_plantonista AS at')
                    ->select('at.id', 'p.nome_completo', 'p.cpf', 'tp.tipo')
                    ->leftJoin('pessoas AS p', 'at.id_pessoa', '=', 'p.id')
                    ->leftJoin('tipo_status_pessoa AS tp', 'p.status', '=', 'tp.id')
                    ->where('p.nome_completo', 'ilike', "%$pesquisaNome%")
                    ->get();
            } elseif ($pesquisaCpf) {
                $atendente = DB::table('atendente_plantonista AS at')
                    ->select('at.id', 'p.nome_completo', 'p.cpf', 'tp.tipo')
                    ->leftJoin('pessoas AS p', 'at.id_pessoa', '=', 'p.id')
                    ->leftJoin('tipo_status_pessoa AS tp', 'p.status', '=', 'tp.id')
                    ->where('p.cpf', 'ilike', "%$pesquisaCpf%")
                    ->get();
            } else {
                $atendente = DB::table('atendente_plantonista AS at')->select('at.id', 'p.nome_completo', 'p.cpf', 'tp.tipo')->leftJoin('pessoas AS p', 'at.id_pessoa', '=', 'p.id')->leftJoin('tipo_status_pessoa AS tp', 'p.status', '=', 'tp.id')->get();
            }

            $conta = $atendente->count();

            return view('/atendentes-plantonistas/gerenciar-atendente-plantonista', compact('atendente', 'conta'));
        } catch (\Exception $e) {
            $code = $e->getCode();
            return view('tratamento-erro.erro-inesperado', compact('code'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $nomes = DB::table('pessoas')->where('status', '=', '1')->get();

            $dias = DB::table('tipo_dia')->get();

            return view('/atendentes-plantonistas/incluir-atendente-plantonista', compact('nomes', 'dias'));
        } catch (\Exception $e) {
            app('flasher')->addError('Houve um erro inesperado: #' . $e->getCode());
            return redirect()->back();
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $req = $request->all();
            $dataHoje = Carbon::today()->toDateString();

            $idAtendente = DB::table('atendente_plantonista')->insertGetId([
                'id_pessoa' => $request->input('nome'),
            ]);

            $i = 0;

            foreach ($request->checkbox as $checked) {
                DB::table('atendente_plantonista_dia')->insert([
                    'id_atendente' => $idAtendente,
                    'id_dia' => $checked, // Estou assumindo que $checked é o ID do dia
                    'dh_inicio' => $req['dhInicio'][$i], // Use o array $req para obter os horários
                    'dh_fim' => $req['dhFim'][$i], // Se você também tiver um array para dhFim
                ]);
                $i += 1;
            }
            $i = 0;
            foreach ($request->checkbox as $checked) {
                DB::table('historico_atendente_plantonista')->insert([
                    'id_atendente' => $idAtendente,
                    'id_dia' => $checked, // Estou assumindo que $checked é o ID do dia
                    'dh_inicio' => $req['dhInicio'][$i], // Use o array $req para obter os horários
                    'dh_fim' => $req['dhFim'][$i],
                    'dt_inicio' => $dataHoje, // Se você também tiver um array para dhFim
                ]);
                $i += 1;
            }

            DB::commit();
            return redirect()->route('indexAtendentePlantonista');
        } catch (\Exception $e) {
            app('flasher')->addError('Houve um erro inesperado: #' . $e->getCode());
            DB::rollBack();
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $idp = DB::table('atendente_plantonista')->where('id', '=', $id)->get();

            $nomes = DB::table('pessoas')
                ->where('id', '=', $idp[0]->id_pessoa)
                ->get();

            $historico = DB::table('historico_atendente_plantonista as hs')
                ->select(['hs.dt_inicio', 'hs.dt_fim', 'hs.dh_inicio', 'hs.dh_fim', 'd.nome'])
                ->leftJoin('tipo_dia as d', 'hs.id_dia', '=', 'd.id')
                ->where('id_atendente', '=', $id, 'and', 'dt_fim', '=', null)
                ->get();

            $dias = DB::table('tipo_dia')->get();
        } catch (\Exception $e) {
            app('flasher')->addError('Houve um erro inesperado: #' . $e->getCode());
            return redirect()->back();
        }

        return view('/atendentes-plantonistas/visualizar-atendente-plantonista', compact('nomes', 'dias', 'historico'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $nomes = DB::table('atendente_plantonista as at')->select('p.nome_completo', 'at.id', 'p.status')->leftJoin('pessoas as p', 'at.id_pessoa', '=', 'p.id')->where('at.id', '=', $id)->get();

            $pessoas = DB::select('select id as idp, nome_completo from pessoas');
            $tipo_status_pessoa = DB::select('select * from tipo_status_pessoa');

            $dias = DB::table('tipo_dia')->get();

            $diasHorarios = DB::table('atendente_plantonista_dia')->where('id_atendente', '=', $id)->get();
            $checkTheBox = [];

            foreach ($diasHorarios as $dia) {
                $checkTheBox[] = $dia->id_dia;
            }
            return view('atendentes-plantonistas/editar-atendente-plantonista', compact('nomes', 'dias', 'diasHorarios', 'checkTheBox'));
        } catch (\Exception $e) {
            app('flasher')->addError('Houve um erro inesperado: #' . $e->getCode());
            return redirect()->back();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $dataHoje = Carbon::today()->toDateString();
            $req = $request->all();
            $hist = DB::table('historico_atendente_plantonista')->get();

            $diasHorarios = DB::table('atendente_plantonista_dia')->where('id_atendente', '=', $id)->get();

            DB::table('atendente_plantonista_dia')->where('id_atendente', '=', $id)->delete();

            DB::table('historico_atendente_plantonista')->where('id_atendente', '=', $id)->delete();

            foreach ($request->checkbox as $checked) {
                DB::table('atendente_plantonista_dia')->insert([
                    'id_atendente' => $id,
                    'id_dia' => $checked, // Estou assumindo que $checked é o ID do dia
                    'dh_inicio' => $req['dhInicio'][$checked], // Use o array $req para obter os horários
                    'dh_fim' => $req['dhFim'][$checked], // Se você também tiver um array para dhFim
                ]);

                DB::table('historico_atendente_plantonista')->insert([
                    'id_atendente' => $id,
                    'id_dia' => $checked, // Estou assumindo que $checked é o ID do dia
                    'dh_inicio' => $req['dhInicio'][$checked], // Use o array $req para obter os horários
                    'dh_fim' => $req['dhFim'][$checked],
                    'dt_inicio' => $dataHoje, // Se você também tiver um array para dhFim
                ]);
            }

            DB::commit();
            return redirect()->route('indexAtendentePlantonista');
        } catch (\Exception $e) {
            app('flasher')->addError('Houve um erro inesperado: #' . $e->getCode());
            DB::rollBack();
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

}
