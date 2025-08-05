<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GerenciarCriterioAtividadeController extends Controller
{


    public function index(Request $request)
    {

        $criterio = DB::table('criterios_tipo_atividade AS cta')
            ->leftJoin('tipo_tratamento AS tt', 'cta.id_tipo_atividade', 'tt.id')
            ->leftJoin('tipo_semestre AS ts', 'tt.id_semestre', 'ts.id')
            ->leftJoin('tipo_grupo AS tg', 'tt.id_tipo_grupo', 'tg.id')
            ->select(
                'cta.id',
                'ts.sigla AS ssigla',
                'tt.descricao AS tnome',
                'tt.sigla AS tsigla',
                'tg.nm_tipo_grupo',
                DB::raw("CASE WHEN status = true THEN 'Ativo' ELSE 'Inativo' END AS status")
            );

        $snome = $request->nome_setor;

        $status = $request->status;
        $setores = DB::table('setor AS s')->select('s.id AS ids', 's.sigla', 's.nome')->get();
        if ($snome) {
            $criterio->where('s.nome', 'like', '%' . $snome . '%');
        }




        if ($request->status) {
            $criterio->where('cta.status', $status);
        }

        $criterio = $criterio->paginate(10);

        $contar = $criterio->total();

        $tipo_motivo = DB::table('tipo_mot_inat_gr_reu')->get();

        $tipo_atv = DB::table('tipo_tratamento AS tt')
            ->leftJoin('tipo_semestre AS ts', 'tt.id_semestre', 'ts.id')
            ->select('tt.id', 'tt.descricao', 'tt.sigla', 'tt.id_tipo_grupo', 'ts.sigla AS ssigla')
            ->get();


        return view('criterio.gerenciar-criterio', compact('criterio', 'contar', 'tipo_motivo', 'tipo_atv', 'setores', 'snome', 'status'));
    }

    public function create()
    {

        $tipo_atv = DB::table('tipo_tratamento AS tt')
            ->leftJoin('tipo_semestre AS ts', 'tt.id_semestre', 'ts.id')
            ->select('tt.id', 'tt.descricao', 'tt.sigla', 'tt.id_tipo_grupo', 'ts.sigla AS ssigla')
            ->get();

        return view('criterio.criar-criterio', compact('tipo_atv'));
    }

    public function include(Request $request)
    {

        $now = Carbon::now()->format('Y-m-d');

        $criterio = DB::table('criterios_tipo_atividade AS cta')
            ->insert([
                'id_atividade' => $request->input('atividade'),
                'semestre' => $request->input('semestre'),
                'id_atividade_requisito' => $request->input('atividadereq'),
                'semestre_requisito' => $request->input('semestrereq'),
                'idade_minima' => $request->input('idademin'),
                'idade_maxima' => $request->input('idademax'),
                'dt_criacao' => $now
            ]);

        return view('criterio.gerenciar-criterio', compact('criterio'));
    }

    public function equivale(Request $request, $id)
    {

        $atividade = DB::table('tipo_tratamento AS t')
            ->leftJoin('tipo_semestre AS ts', 't.id_semestre', 'ts.id')
            ->select('t.id', 't.descricao', 't.sigla', 't.id_tipo_grupo', 't.validade_dias', 'ts.sigla')
            ->orderBy('descricao')->get();

        $semestre = DB::table('tipo_semestre AS ts')->select('ts.id', 'nome', 'ts.sigla')->get();

        $setor_user = session()->get('usuario.acesso');

        $setor = DB::table('setor AS s')->select('s.id AS ids', 's.sigla', 's.nome')->whereIn('s.id', $setor_user)->orderBy('s.sigla')->get();


        $requisito = DB::table('criterios_tipo_atividade AS cta')
            ->leftJoin('tipo_tratamento AS tt', 'cta.id_atividade', 'tt.id')
            ->leftJoin('tipo_grupo AS tg', 'tt.id_tipo_grupo', 'tg.id')
            ->leftJoin('tipo_semestre AS ts', 'cta.semestre', 'ts.id')
            ->leftJoin('tipo_semestre AS sr', 'cta.semestre_requisito', 'sr.id')
            ->leftJoin('tipo_tratamento AS tr', 'cta.id_atividade_requisito', 'tr.id')
            ->where('cta.id', $id)
            ->select(
                'cta.id AS idatv',
                'cta.dt_criacao',
                'ts.sigla AS ssigla',
                'tt.descricao AS tnome',
                'tg.nm_tipo_grupo',
                'tr.id AS idar',
                'tr.descricao AS tnomereq',
                'sr.sigla AS srsigla'
            )
            ->get();

        //dd($requisito);

        return view('requisito.equivaler-requisito', compact('requisito', 'atividade', 'semestre', 'setor'));
    }

    public function vincular(Request $request, $idatv)
    {

        $now = Carbon::now()->format('Y-m-d');

        $equivalencia = DB::table('atividade_equivalente AS aq')
            ->insert([
                'id_atividade' => $idatv,
                'semestre' => $request->input('semestre'),
                'id_equivalente' => $request->input('atividade')
            ]);

        return redirect()->route('index.req');
    }
}
