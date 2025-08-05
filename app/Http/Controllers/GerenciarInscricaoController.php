<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GerenciarInscricaoController extends Controller
{
     public function index(Request $request)
    {

       $reuniao = DB::table('cronograma AS cro')
            ->select(
                'cro.id AS idr',
                'gr.nome AS nomeg',
                'cro.dia_semana AS idd',
                'cro.id_sala',
                'cro.id_tipo_tratamento',
                'cro.id_tipo_semestre',
                'cro.h_inicio',
                'td.nome AS nomed',
                'cro.h_fim',
                'cro.max_atend',
                'cro.max_trab',
                'cro.data_inicio',
                'cro.data_fim',
                'gr.status_grupo AS idst',
                'tst.descricao AS trnome',
                'tst.sigla AS trsigla',
                's.sigla as stsigla',
                'tse.sigla as sesigla',
                'sa.numero',
                't.descricao',
                'tm.nome as nmodal',
                'ts.nome as nsemana',
                'tst.descricao as tipo',
                'tst.id as idt',
                DB::raw("(CASE WHEN cro.data_fim is not null THEN 'Inativo' ELSE 'Ativo' END) as status")
            )
            ->leftJoin('tipo_tratamento AS tst', 'cro.id_tipo_tratamento', 'tst.id')
            ->leftJoin('tipo_observacao_reuniao AS t', 'cro.observacao', 't.id')
            ->leftJoin('grupo AS gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('setor as s', 'gr.id_setor', 's.id')
            ->leftJoin('membro AS me', 'gr.id', 'me.id_cronograma')
            ->leftJoin('salas AS sa', 'cro.id_sala', 'sa.id')
            ->leftJoin('tipo_dia AS td', 'cro.dia_semana', 'td.id')
            ->leftJoin('tipo_modalidade AS tm', 'cro.id_tipo_modalidade', 'tm.id')
            ->leftJoin('tipo_semana AS ts', 'cro.id_tipo_semana', 'ts.id')
            ->leftJoin('tipo_semestre as tse', 'tst.id_semestre', 'tse.id')
            ->where('tst.id_tipo_grupo', 2);



        // Obtém os valores de pesquisa da requisição
        $semana = $request->input('semana', null);
        $grupo = $request->input('grupo', null);
        $tipo_tratamento = $request->input('tipo_tratamento', null);
        $semestre = $request->input('semestre', null);
        $setor = $request->input('setor', null);
        $status = $request->input('status','');
        $modalidade = $request->input('modalidade', null);


        //dd($tipo_tratamento, $semestre );
        // Aplica filtro por semana
        if ($semana != '') {
            // Se o valor de semana não for vazio, aplica o filtro
            $reuniao->where('cro.dia_semana', '=', $semana);
        }

        if ($grupo) {
            $reuniao->where('cro.id_grupo', $grupo);
        }


        if ($request->filled('tipo_tratamento')) {
            $descricao = DB::table('tipo_tratamento')
                ->where('id', $request->input('tipo_tratamento'))
                ->value('descricao');

            $ids = DB::table('tipo_tratamento')
                ->where('descricao', $descricao)
                ->pluck('id');

            $reuniao->whereIn('cro.id_tipo_tratamento', $ids);
        }

        if ($semestre) {
            $reuniao->when($semestre, function ($query, $semestre) {
            return $query->where('id_tipo_semestre', $semestre);
            });
        }

        if ($setor) {
            $reuniao->where('gr.id_setor', $setor);
        }
        // Aplica filtro por status com base na expressão CASE WHEN
        $statusCaseWhen = DB::raw("CASE WHEN cro.data_fim is not null THEN 'Inativo' ELSE 'Ativo' END");
        // dd($reuniao->get());
        if ($status) {
            switch ($status) {
                case 1:
                    $reuniao->where($statusCaseWhen, 'Ativo');
                    break;
                case 2:
                    $reuniao->where($statusCaseWhen, 'Inativo');
                    break;
                case 3:
                    $reuniao->where($statusCaseWhen, 'Experimental');
                    break;
                case 4:
                    $reuniao->where($statusCaseWhen, 'Em ferias');
                    break;
            }
        }

        // Aplica filtro por setor
        if ($modalidade) {
            $reuniao->where('tm.id', $modalidade);
        }

        // Conta o número de registros
        $contar = $reuniao->distinct()->count('cro.id');

          // Carregar a lista de grupos para o Select2
          $grupos = DB::table('cronograma as c')
          ->leftJoin('grupo AS g', 'c.id_grupo', 'g.id')
          ->leftJoin('setor AS s', 'g.id_setor', 's.id')
          ->select(
              'g.id AS idg',
              'g.nome AS nomeg',
              's.sigla'
          )
          ->orderBy('g.nome', 'asc')
          ->get()
          ->unique('idg') // aqui garantimos que o ID do grupo seja único
          ->values();     // reindexa os itens do array



        // Aplica a paginação e mantém os parâmetros de busca na URL
        $reuniao = $reuniao
            ->orderBy('status', 'ASC')
            ->orderBy('cro.id_tipo_tratamento', 'ASC')
            ->orderBy('nomeg', 'ASC')
            ->groupBy('idt', 'idr', 'gr.nome', 'td.nome', 'tse.sigla', 't.descricao', 'gr.status_grupo', 'tst.descricao', 's.sigla', 'sa.numero', 'tm.nome', 'ts.nome')
            ->paginate(50)
            ->appends([
                'status' => $status,
                'semana' => $semana,
                'grupo' => $grupo,
                'setor' => $setor,
                'tipo_tratamento' => $tipo_tratamento,
                'modalidade' => $modalidade
            ]);

        // Obtém os dados para os filtros
        $situacao = DB::table('tipo_status_grupo')->select('id AS ids', 'descricao AS descs')->get();

        $tipo_tratamento = DB::table('tipo_tratamento AS tt')
        ->select('tt.id AS idt','tt.descricao', 'tt.sigla AS tipo')
        ->orderBy('tt.sigla')
        ->distinct('tt.sigla')
        ->get();

         $tipo_semestre = DB::table('tipo_tratamento AS tt')
        ->leftJoin('tipo_semestre AS ts', 'tt.id_semestre', 'ts.id')
        ->whereNotNull('tt.id_semestre')
        ->select('ts.id AS ids', 'ts.sigla')
        ->orderBy('ts.id')
        ->get();

        $tipo_motivo = DB::table('tipo_mot_inat_gr_reu')->get();

        $tmodalidade = DB::table('tipo_modalidade')->get();

        $tpdia = DB::table('tipo_dia')
            ->select('id AS idtd', 'nome AS nomed')
            ->orderByRaw('CASE WHEN id = 0 THEN 1 ELSE 0 END, idtd ASC')
            ->get();

        // Carregar a lista de setores para o Select2
        $setores = DB::table('setor')->orderBy('nome', 'asc')->get();

         return view('/inscricao/gerenciar-inscricao', compact('tipo_semestre', 'tipo_motivo', 'reuniao', 'tpdia', 'situacao', 'status', 'contar', 'semana', 'grupos', 'setores', 'tmodalidade', 'modalidade', 'tipo_tratamento'));
    }


}
