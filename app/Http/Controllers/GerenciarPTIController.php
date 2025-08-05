<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GerenciarPTIController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {


        try {

            $hoje = Carbon::today();

            // Traz os cronogramas que o usuário logado tem acesso
            $dirigentes = DB::table('membro as mem')
                ->select('ass.id_pessoa', 'gr.nome', 'cr.id', 'gr.status_grupo', 'd.nome as dia')
                ->leftJoin('associado as ass', 'mem.id_associado', 'ass.id')
                ->leftJoin('cronograma as cr', 'mem.id_cronograma', 'cr.id')
                ->leftJoin('grupo as gr', 'cr.id_grupo', 'gr.id')
                ->leftJoin('tipo_dia as d', 'cr.dia_semana', 'd.id')
                ->where('cr.id_tipo_tratamento', 2) // Grupos de PTI
                ->where(function ($query) use ($hoje) { // Apenas cronogramas ativos
                    $query->whereRaw("cr.data_fim < ?", [$hoje])
                        ->orWhereNull('cr.data_fim');
                })
                ->distinct('gr.id');

            // Caso o usuário não seja Master Admin, apenas grupos onde o usuário é dirigente
            if (!in_array(36, session()->get('usuario.acesso'))) {
                $dirigentes =  $dirigentes->where('ass.id_pessoa', session()->get('usuario.id_pessoa'))
                    ->where('id_funcao', '<', 3);
            }

            $dirigentes = $dirigentes->get();

            // Pega apenas os IDs dos grupos possíveis
            $grupos_autorizados = [];
            foreach ($dirigentes as $dir) {
                $grupos_autorizados[] = $dir->id;
            }


            // Traz todos os encaminhamentos de todos os grupos selecionados
            $encaminhamentos = DB::table('tratamento as tr')
                ->select(
                    'tr.id',
                    'p.nome_completo',
                    'cro.h_inicio',
                    'cro.h_fim',
                    'gr.nome',
                    'tse.nome as status',
                    'tr.status as id_status',
                    'tr.id_reuniao'
                )
                ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
                ->leftJoin('cronograma as cro', 'tr.id_reuniao', 'cro.id')
                ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
                ->leftJoin('atendimentos as atd', 'enc.id_atendimento', 'atd.id')
                ->leftJoin('pessoas as p', 'atd.id_assistido', 'p.id')
                ->leftJoin('tipo_status_tratamento as tse', 'tr.status', 'tse.id')
                ->where('enc.id_tipo_tratamento', 2)
                ->whereIn('tr.status',  [1, 2])
                ->whereIn('tr.id_reuniao', $grupos_autorizados);

            // Caso um nome seja pesquisado, traz o assistido no grupo selecionado
            if ($request->nome_pesquisa) {
                $encaminhamentos = $encaminhamentos->where('p.nome_completo', 'ilike', "%$request->nome_pesquisa%");
            }

            $selected_grupo = $request->grupo; // Usado no select de pesquisa para trazer o grupo selecionado
            if ($request->grupo) { // Caso um grupo seja pesquisado, traz apenas assistidos deste grupo
                $encaminhamentos = $encaminhamentos->where('tr.id_reuniao', $request->grupo);
            } else { // Caso nenhum grupo seja pesquisado, traz o primeiro como padrão
                $selected_grupo = current($grupos_autorizados);
                $encaminhamentos = $encaminhamentos->where('tr.id_reuniao', current($grupos_autorizados));
            }

            $encaminhamentos = $encaminhamentos->get()->toArray();
            $totalAssistidos = count($encaminhamentos);

            // Recolhe todos os IDs dos presentes do grupo selecionado
            $presencaHoje = DB::table('presenca_cronograma as pc')
                ->leftJoin('dias_cronograma as dc', 'pc.id_dias_cronograma', 'dc.id')
                ->whereIn('id_tratamento', array_column($encaminhamentos, 'id'))
                ->where('dc.data', $hoje)
                ->pluck('id_tratamento')
                ->toArray();


            return view('pti.gerenciar-pti', compact('encaminhamentos', 'dirigentes', 'selected_grupo', 'totalAssistidos', 'presencaHoje'));
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $result = DB::table('tratamento AS tr')
                ->select(
                    'enc.id AS ide',
                    'tr.dt_inicio',
                    'tr.dt_fim',
                    'tr.id_reuniao',
                    'tse.descricao AS tsenc',
                    'at.id AS ida',
                    'p1.dt_nascimento',
                    'p1.nome_completo AS nm_1',
                    'p2.nome_completo as nm_2',
                    'pa.nome',
                    'tt.descricao AS desctrat',
                    'tx.tipo',
                    'p4.nome_completo AS nm_4',
                    'at.dh_inicio',
                    'at.dh_fim',
                    'gr.nome AS nomeg',
                    'rm.h_inicio AS rm_inicio',
                    'tm.tipo AS tpmotivo',
                    'sat.descricao AS statat',
                    'sl.numero as sala',
                    't.cod_tca'
                )
                ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id')
                ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
                ->leftJoin('registro_tema AS rt', 'at.id', 'rt.id_atendimento')
                ->leftJoin('tipo_temas AS t', 'rt.id_tematica', 't.id')
                ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                ->leftjoin('associado as ass', 'at.id_atendente', 'ass.id')
                ->leftjoin('pessoas AS p4', 'ass.id_pessoa', 'p4.id')
                ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
                ->leftJoin('tipo_status_encaminhamento AS tse', 'enc.status_encaminhamento', 'tse.id')
                ->leftJoin('tipo_status_atendimento AS sat', 'at.status_atendimento', 'sat.id')
                ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
                ->leftJoin('tp_sexo AS tx', 'p1.sexo', 'tx.id')
                ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                ->leftjoin('grupo AS gr', 'rm.id_grupo', 'gr.id')
                ->leftJoin('tipo_motivo AS tm', 'enc.motivo', 'tm.id')
                ->leftJoin('salas as sl', 'rm.id_sala', 'sl.id')
                ->where('tr.id', $id)
                ->get();

            $list = DB::table('presenca_cronograma AS dt')
                ->leftJoin('tratamento as tr', 'dt.id_tratamento', 'tr.id')
                ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'enc.dh_enc', 'enc.status_encaminhamento AS tst', 'tr.id AS idtr', 'rm.h_inicio AS rm_inicio', 'dt.id AS idp', 'dt.presenca', 'dc.data', 'gp.nome')
                ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id')
                ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                ->leftJoin('dias_cronograma as dc', 'dt.id_dias_cronograma', 'dc.id')
                ->leftjoin('cronograma AS rm1', 'dc.id_cronograma', 'rm1.id')
                ->leftjoin('grupo AS gp', 'rm1.id_grupo', 'gp.id')
                ->where('tr.id', $id)
                ->get();

            $faul = DB::table('tratamento AS tr')
                ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'enc.dh_enc', 'enc.status_encaminhamento AS tst', 'tr.id AS idtr', 'rm.h_inicio AS rm_inicio', 'dt.id AS idp',  'dt.presenca')
                ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id')
                ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                ->leftJoin('presenca_cronograma AS dt', 'tr.id', 'dt.id_tratamento')
                ->where('tr.id', $id)
                ->where('dt.presenca', 0)
                ->count();

            return view('pti.historico-pti', compact('result', 'list', 'faul'));
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('tratamento-erro.erro-inesperado', compact('code'));
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Usada Para dar Alta em um Assistido
     */
    public function update(Request $request, string $id)
    {
        try {
            $hoje = Carbon::today();
            $dt_hora = Carbon::now();
            $todosIDs = DB::table('tratamento as t')
                ->select('t.id as idt', 'e.id as ide', 'a.id as ida', 'a.id_assistido')
                ->leftJoin('encaminhamento as e', 't.id_encaminhamento', 'e.id')
                ->leftJoin('atendimentos as a', 'e.id_atendimento', 'a.id')
                ->where('t.id', $id)->first();

            // Retorna todos os IDs dos encaminhamentos de tratamento
            $countTratamentos = DB::table('encaminhamento as enc')
                ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
                ->where('enc.id_tipo_encaminhamento', 2) // Encaminhamento de Tratamento
                ->where('at.id_assistido', $todosIDs->id_assistido)
                ->where('enc.status_encaminhamento', '<', 3) // 3 => Finalizado, Traz apenas os ativos (Para Agendar, Agendado)
                ->pluck('id_tipo_tratamento')->toArray();


            // Finaliza o tratamento PTI
            DB::table('tratamento')->where('id', $id)->update(['status' => 4]);

            // Insere no histórico a criação do atendimento
            DB::table('log_atendimentos')->insert([
                'id_referencia' => $id,
                'id_usuario' => session()->get('usuario.id_usuario'),
                'id_acao' => 1, // mudou de Status para
                'id_origem' => 3, // Tratamento
                'id_observacao' => 4,
                'data_hora' =>  $dt_hora
            ]);

            // Finaliza o encaminhamento PTI
            DB::table('encaminhamento')->where('id', $todosIDs->ide)->update(['status_encaminhamento' => 3]);

            // Insere no histórico a criação do atendimento
            DB::table('log_atendimentos')->insert([
                'id_referencia' => $id,
                'id_usuario' => session()->get('usuario.id_usuario'),
                'id_acao' => 1, // mudou de Status para
                'id_origem' => 2, // Encaminhamento
                'id_observacao' => 3,
                'data_hora' => $dt_hora
            ]);

            // Caso já exista um encaminhamento PTD
            if (in_array(1, $countTratamentos)) {

                // Atualiza todos os tratamentos PTD ativos para infinitos
                $ptdInfinito = DB::table('tratamento as tr')
                    ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
                    ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
                    ->where('at.id_assistido', $todosIDs->id_assistido)
                    ->where('enc.id_tipo_tratamento', 1) // PTD
                    ->where('tr.status', '<', 3); // Ativos

                $idPtdInfinito = $ptdInfinito->first();

                if ($idPtdInfinito) {
                    $ptdInfinito->update([
                        'tr.dt_fim' => null
                    ]);

                    DB::table('log_atendimentos')->insert([
                        'id_referencia' => $idPtdInfinito,
                        'id_usuario' => session()->get('usuario.id_usuario'),
                        'id_acao' => 1, // mudou de Status para
                        'id_origem' => 2, // Encaminhamento
                        'id_observacao' => 3,
                        'data_hora' => $dt_hora
                    ]);
                }
            } else {

                // Insere um novo encaminhamento PTD
                $ptd = DB::table('encaminhamento')->insertGetId([
                    'dh_enc' => $hoje,
                    'id_usuario' => session()->get('usuario.id_pessoa'),
                    'status_encaminhamento' => 1, // Aguardando Agendamento
                    'id_tipo_encaminhamento' => 2, // Tratamento
                    'id_atendimento' => $todosIDs->ida,
                    'id_tipo_tratamento' => 1 // PTD

                ]);

                DB::table('log_atendimentos')->insert([
                    'id_referencia' => $ptd,
                    'id_usuario' => session()->get('usuario.id_usuario'),
                    'id_acao' => 2, // mudou de Status para
                    'id_origem' => 2, // Encaminhamento
                    'data_hora' => $dt_hora
                ]);
            }

            return redirect()->back();
        } catch (\Exception $e) {
            $code = $e->getCode();
            return view('tratamento-erro.erro-inesperado', compact('code'));
        }
    }

    public function nutres(Request $request, string $id)
    {
        try {
            $hoje = Carbon::today();
            $dt_hora = Carbon::now();
            $todosIDs = DB::table('tratamento as t')
                ->select('t.id as idt', 'e.id as ide', 'a.id as ida', 'a.id_assistido')
                ->leftJoin('encaminhamento as e', 't.id_encaminhamento', 'e.id')
                ->leftJoin('atendimentos as a', 'e.id_atendimento', 'a.id')
                ->where('t.id', $id)->first();

            // Retorna todos os IDs dos encaminhamentos de tratamento
            $countTratamentos = DB::table('encaminhamento as enc')
                ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
                ->where('enc.id_tipo_encaminhamento', 2) // Encaminhamento de Tratamento
                ->where('at.id_assistido', $todosIDs->id_assistido)
                ->where('enc.status_encaminhamento', '<', 5) // 3 => Finalizado, Traz apenas os ativos (Para Agendar, Agendado)
                ->pluck('id_tipo_tratamento')->toArray();


            // Finaliza o tratamento PTI
            DB::table('tratamento')->where('id', $id)->update(['status' => 4]);

            // Insere no histórico a criação do atendimento
            DB::table('log_atendimentos')->insert([
                'id_referencia' => $id,
                'id_usuario' => session()->get('usuario.id_usuario'),
                'id_acao' => 1, // mudou de Status para
                'id_origem' => 3, // Tratamento
                'id_observacao' => 4,
                'data_hora' =>  $dt_hora
            ]);

            // Finaliza o encaminhamento PTI
            DB::table('encaminhamento')->where('id', $todosIDs->ide)->update(['status_encaminhamento' => 3]);

            // Insere no histórico a criação do atendimento
            DB::table('log_atendimentos')->insert([
                'id_referencia' => $id,
                'id_usuario' => session()->get('usuario.id_usuario'),
                'id_acao' => 1, // mudou de Status para
                'id_origem' => 2, // Encaminhamento
                'id_observacao' => 3,
                'data_hora' => $dt_hora
            ]);

            // Caso já exista um encaminhamento PTD
            if (in_array(1, $countTratamentos)) {

                // Atualiza todos os tratamentos PTD ativos para infinitos
                $ptdInfinito = DB::table('tratamento as tr')
                    ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
                    ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
                    ->where('at.id_assistido', $todosIDs->id_assistido)
                    ->where('enc.id_tipo_tratamento', 1) // PTD
                    ->where('tr.status', '<', 3); // Ativos

                $idPtdInfinito = $ptdInfinito->first();

                if ($idPtdInfinito) {
                    $ptdInfinito->update([
                        'tr.dt_fim' => null
                    ]);

                    DB::table('log_atendimentos')->insert([
                        'id_referencia' => $idPtdInfinito->id,
                        'id_usuario' => session()->get('usuario.id_usuario'),
                        'id_acao' => 1, // mudou de Status para
                        'id_origem' => 2, // Encaminhamento
                        'id_observacao' => 3,
                        'data_hora' => $dt_hora
                    ]);
                }
            } else {

                // Insere um novo encaminhamento PTD
                $ptd = DB::table('encaminhamento')->insertGetId([
                    'dh_enc' => $hoje,
                    'id_usuario' => session()->get('usuario.id_pessoa'),
                    'status_encaminhamento' => 1, // Aguardando Agendamento
                    'id_tipo_encaminhamento' => 2, // Tratamento
                    'id_atendimento' => $todosIDs->ida,
                    'id_tipo_tratamento' => 1 // PTD

                ]);
               
                DB::table('log_atendimentos')->insert([
                    'id_referencia' => $ptd,
                    'id_usuario' => session()->get('usuario.id_usuario'),
                    'id_acao' => 2, // foi criado
                    'id_origem' => 2, // Encaminhamento
                    'data_hora' => $dt_hora
                ]);
            }



            // Insere uma entrevista NUTRES
            $nutres = DB::table('encaminhamento')->insertGetId([
                'dh_enc' => $hoje,
                'id_usuario' => session()->get('usuario.id_pessoa'),
                'status_encaminhamento' => 6, // Aguardando Manutenção
                'id_tipo_encaminhamento' => 1, // Entrevista
                'id_atendimento' => $todosIDs->ida,
                'id_tipo_entrevista' => 4 // NUTRES

            ]);

            DB::table('log_atendimentos')->insert([
                'id_referencia' => $nutres,
                'id_usuario' => session()->get('usuario.id_usuario'),
                'id_acao' => 2, // foi Criado
                'id_origem' => 2, // Encaminhamento
                'data_hora' => $dt_hora
            ]);

            return redirect()->back();
        } catch (\Exception $e) {
            $code = $e->getCode();
            return view('tratamento-erro.erro-inesperado', compact('code'));
        }
    }
}
