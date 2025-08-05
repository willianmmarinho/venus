<?php

namespace App\Http\Controllers;

use Carbon\CarbonPeriod;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Barryvdh\DomPDF\Facade\Pdf;

class RelatoriosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexAFI(Request $request)
    {
        $afiSelecionado = $request->afi;
        $dt_inicio = $request->dt_inicio == null ? (Carbon::now()->subMonth()->firstOfMonth()->format('Y-m-d')) : $request->dt_inicio;
        $dt_fim =  $request->dt_fim == null ? Carbon::today()->format('Y-m-d') : $request->dt_fim;

        $atendentes = DB::table('membro as m')
            ->leftJoin('cronograma as cro', 'm.id_cronograma', 'cro.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('associado as ass', 'm.id_associado', 'ass.id')
            ->leftJoin('pessoas as p', 'ass.id_pessoa', 'p.id')
            ->where(function ($query) {
                $query->where('id_funcao', 5);
                $query->orWhere('id_funcao', 6);
            })
            ->where('id_tipo_grupo', 3)
            ->distinct('p.nome_completo')
            ->orderBy('p.nome_completo')
            ->select('m.id_associado', 'p.nome_completo')
            ->get();

        foreach ($atendentes as $key => $atendente) {
            $diasAtendente = DB::table('atendente_dia')
                ->where('id_associado', $atendente->id_associado)
                ->get();
            $dados = [];
            foreach ($diasAtendente as $mKey => $diaAtendente) {
                foreach ($diasAtendente as $diaAtendenteCompare) {
                    if (Carbon::parse($diaAtendente->dh_inicio)->format('Y-m-d') == Carbon::parse($diaAtendenteCompare->dh_inicio)->format('Y-m-d') and $diaAtendente->id != $diaAtendenteCompare->id) {
                        unset($diasAtendente[$mKey]);
                    }
                }
            }


            // Pega um array com todas as reuniões que o atendente participa
            $cronogramasParticipa = DB::table('membro')
                ->where('id_associado', $atendente->id_associado)
                ->where(function ($query) {
                    $query->where('id_funcao', 5);
                    $query->orWhere('id_funcao', 6);
                })
                ->pluck('id_cronograma');

            //Pega datas como id do cronograma, e data das reuniões que aconteceram durante o período selecionado
            $cronogramaAFI = DB::table('dias_cronograma as dc')
                ->leftJoin('cronograma as cro', 'dc.id_cronograma', 'cro.id')
                ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
                ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
                ->where('id_tipo_grupo', 3)
                ->where('dc.data', '>', $dt_inicio)
                ->where('dc.data', '<', $dt_fim)
                ->whereIn('cro.id', $cronogramasParticipa)
                ->select('cro.id', 'dc.data', 'gr.nome', 'cro.h_inicio', 'td.nome as dia')
                ->orderBy('dc.data')->get();

            //Confere se a data de uma reunião está presente na lista de distinct criada acima, gerando um array completo, com o dado de presenca
            foreach ($cronogramaAFI as $datas) {
                $i = 0;
                foreach ($diasAtendente as $diaAtendente) {
                    if ($datas->data == Carbon::parse($diaAtendente->dh_inicio)->format('Y-m-d') and $diaAtendente->id_grupo == $datas->id) {
                        array_push($dados, ['id' => $datas->id, 'data' => $datas->data, 'nome' => $datas->nome, 'h_inicio' => $datas->h_inicio, 'dia' => $datas->dia, 'presenca' => 1]);
                        break;
                    } elseif (++$i === count($diasAtendente)) {
                        array_push($dados, ['id' => $datas->id, 'data' => $datas->data, 'nome' => $datas->nome, 'h_inicio' => $datas->h_inicio, 'dia' => $datas->dia, 'presenca' => 0]);
                    }
                }
            }

            //Gera numa variável a contagem total de cada item de presença
            $contaFaltas = array_count_values(array_column($dados, 'presenca'));
            $atendentes[$key]->presenca = $contaFaltas;
        }
        return view('relatorios.gerenciar-relatorio-afi', compact('atendentes', 'afiSelecionado', 'dt_inicio', 'dt_fim'));
    }


    public function visualizarAFI(Request $request)
    {

        // Devolve a data selecioanda, se não tiver nenhuma coloca uma padrão
        $dt_inicio = $request->dt_inicio == null ? (Carbon::now()->subMonth()->firstOfMonth()->format('Y-m-d')) : $request->dt_inicio;
        $dt_fim =  $request->dt_fim == null ? Carbon::today()->format('Y-m-d') : $request->dt_fim;
        //  dd($request->dt_inicio);

        // Faz um distinct caso o atendente venha duas vezes em um mesmo dia
        $diasAtendente = DB::table('atendente_dia')
            ->where('id_associado', $request->afi) // Colocar trava de data
            ->get();
        $dados = [];
        foreach ($diasAtendente as $mKey => $diaAtendente) {
            foreach ($diasAtendente as $diaAtendenteCompare) {
                if (Carbon::parse($diaAtendente->dh_inicio)->format('Y-m-d') == Carbon::parse($diaAtendenteCompare->dh_inicio)->format('Y-m-d') and $diaAtendente->id != $diaAtendenteCompare->id) {
                    unset($diasAtendente[$mKey]);
                }
            }
        }

        // Pega um array com todas as reuniões que o atendente participa
        $cronogramasParticipa = DB::table('membro')
            ->where('id_associado', $request->afi)
            ->where(function ($query) {
                $query->where('id_funcao', 5);
                $query->orWhere('id_funcao', 6);
            })
            ->pluck('id_cronograma');

        //Pega datas como id do cronograma, e data das reuniões que aconteceram durante o período selecionado
        $cronogramaAFI = DB::table('dias_cronograma as dc')
            ->leftJoin('cronograma as cro', 'dc.id_cronograma', 'cro.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
            ->where('id_tipo_grupo', 3)
            ->where('dc.data', '>', $dt_inicio)
            ->where('dc.data', '<', $dt_fim)
            ->whereIn('cro.id', $cronogramasParticipa)
            ->select('cro.id', 'dc.data', 'gr.nome', 'cro.h_inicio', 'td.nome as dia')
            ->orderBy('dc.data')->get();

        //Confere se a data de uma reunião está presente na lista de distinct criaada acima, gerando um array completo, com o dado de presenca
        foreach ($cronogramaAFI as $datas) {
            $i = 0;
            foreach ($diasAtendente as $diaAtendente) {
                if ($datas->data == Carbon::parse($diaAtendente->dh_inicio)->format('Y-m-d') and $diaAtendente->id_grupo == $datas->id) {
                    array_push($dados, ['id' => $datas->id, 'data' => $datas->data, 'nome' => $datas->nome, 'h_inicio' => $datas->h_inicio, 'dia' => $datas->dia, 'presenca' => 1]);
                    break;
                } elseif (++$i === count($diasAtendente)) {
                    array_push($dados, ['id' => $datas->id, 'data' => $datas->data, 'nome' => $datas->nome, 'h_inicio' => $datas->h_inicio, 'dia' => $datas->dia, 'presenca' => 0]);
                }
            }
        }

        //Gera numa variável a contagem total de cada item de presença
        $contaFaltas = array_count_values(array_column($dados, 'presenca'));
        // dd($diasAtendente, $cronogramaAFI, $dados, $cronogramasParticipa, $contaFaltas);

        // Devolve todos os atendentes membros de uma reunião
        $atendentes = DB::table('membro as m')
            ->leftJoin('cronograma as cro', 'm.id_cronograma', 'cro.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('associado as ass', 'm.id_associado', 'ass.id')
            ->leftJoin('pessoas as p', 'ass.id_pessoa', 'p.id')
            ->where(function ($query) {
                $query->where('id_funcao', 5);
                $query->orWhere('id_funcao', 6);
            })
            ->where('id_tipo_grupo', 3)
            ->distinct('p.nome_completo')
            ->orderBy('p.nome_completo')
            ->select('m.id_associado', 'p.nome_completo')
            ->get();

        //Devolve dados como nome do atendente selecionado na pesquisa
        $afiSelecionado = DB::table('membro as m')
            ->leftJoin('associado as ass', 'm.id_associado', 'ass.id')
            ->leftJoin('pessoas as p', 'ass.id_pessoa', 'p.id')
            ->where('m.id_associado', $request->afi)
            ->select('m.id_associado', 'p.nome_completo')
            ->first();


        return view('relatorios.visualizar-presenca-afi', compact('contaFaltas', 'dados', 'atendentes', 'afiSelecionado', 'dt_inicio', 'dt_fim'));
    }

    /**
     * Show the form for creating a new resource.
     */


    /**
     * Store a newly created resource in storage.
     */
    public function tematicas(Request $request)
    {
        $dt_inicio = $request->dt_inicio == null ? (Carbon::now()->subMonth()->firstOfMonth()->format('Y-m-d')) : $request->dt_inicio;
        $dt_fim =  $request->dt_fim == null ? Carbon::today()->format('Y-m-d') : $request->dt_fim;
        $tematicas = DB::table('registro_tema as rt')
            ->leftJoin('atendimentos as at', 'rt.id_atendimento', 'at.id')
            ->rightJoin('tipo_temas as tm', 'rt.id_tematica', 'tm.id')
            ->where('at.dh_chegada', '>=', $dt_inicio)
            ->where('at.dh_chegada', '<', $dt_fim)
            ->groupBy('nm_tca')
            ->select('nm_tca', DB::raw("count(*) as total"))
            ->get();

        $tematicas = json_decode(json_encode($tematicas), true);
        $nomes_temas = DB::table('tipo_temas')->pluck('nm_tca');

        $tematicasArray = array();
        foreach ($nomes_temas as $tema) {
            $tematicasArray[$tema] = in_array($tema, array_column($tematicas, 'nm_tca')) ? $tematicas[array_search($tema, array_column($tematicas, 'nm_tca'))]['total'] : 0;
        }


        return view('relatorios.tematicas', compact('tematicasArray', 'dt_inicio', 'dt_fim', 'tematicas'));
    }

    /**
     * Display the specified resource.
     */
    public function cronograma(Request $request)
    {

        $cronogramas = DB::table('cronograma as cro')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('salas as sl', 'cro.id_sala', 'sl.id')
            ->leftJoin('setor as st', 'gr.id_setor', 'st.id')
            ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
            ->select('cro.id', 'gr.nome', 'st.nome as setor', 'st.sigla', 'cro.h_inicio', 'cro.h_fim', 'cro.data_inicio', 'cro.data_fim', 'cro.dia_semana', 'td.nome as dia');

        $salas = DB::table('salas')->get();

        $cronogramasPesquisa = DB::table('cronograma as cro')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->select('gr.id', 'gr.nome')
            ->distinct('gr.nome')
            ->orderBy('gr.nome')
            ->get();

        $setoresPesquisa = DB::table('setor')->get();

        $requestSala = $request->sala;
        if ($requestSala) {
            $cronogramas = $cronogramas->where('cro.id_sala', $requestSala);
        }
        $requestGrupo = $request->grupo;
        if ($request->grupo) {
            $cronogramas = $cronogramas->where('cro.id_grupo', $requestGrupo);
        }
        $requestSetor = $request->setor;
        if ($request->setor) {
            $cronogramas = $cronogramas->where('gr.id_setor', $requestSetor);
        }
        // dd($cronogramas->get());
        $cronogramas = $cronogramas->get();

        $eventosCronogramas = [];
        $i = 0;
        foreach ($cronogramas as $cronograma) {

            $eventosCronogramas[$i]['id'] = $i;
            $eventosCronogramas[$i]['title'] = $cronograma->sigla;
            $eventosCronogramas[$i]['daysOfWeek'] = [$cronograma->dia_semana];
            $eventosCronogramas[$i]['startTime'] = $cronograma->h_inicio;
            $eventosCronogramas[$i]['endTime'] = $cronograma->h_fim;
            $cronograma->data_inicio == null ? '2024-09-02' : $eventosCronogramas[$i]['startRecur'] = $cronograma->data_inicio;
            $cronograma->data_fim == null ? null : $eventosCronogramas[$i]['endRecur'] = $cronograma->data_fim;
            $eventosCronogramas[$i]['extendedProps'] =
                [
                    'setor' => $cronograma->setor,
                    'dia' => $cronograma->dia,
                    'nome' => $cronograma->nome,
                    'h_inicio' => $cronograma->h_inicio,
                    'h_fim' => $cronograma->h_fim
                ];

            $i++;
        }
        json_encode($eventosCronogramas);

        //   dd($cronogramas, $eventosCronogramas);
        return view('relatorios.relatorio-salas-cronograma', compact('requestSetor', 'setoresPesquisa', 'eventosCronogramas', 'salas', 'cronogramasPesquisa', 'requestSala', 'requestGrupo'));
    }

    public function indexmembro(Request $request)
    {

        // Obter os parâmetros de busca
        $setorId = $request->input('setor');
        $grupoId = $request->input('grupo');
        $diaId = $request->input('dia');
        $nomeId = $request->input('nome');
        $funcaoId = $request->input('funcao');
        $status = $request->input('status');

        // Definir o número de itens por página
        $itemsPerPage = 50;
        $setoresAutorizado = array();
        foreach (session()->get('acessoInterno') as $perfil) {

            $setoresAutorizado = array_merge($setoresAutorizado, array_column($perfil, 'id_setor'));
        }

        // Obter os atendentes para o select2
        $atendentesParaSelect = DB::table('membro AS m')
            ->select('m.id_associado AS ida', 'a.nr_associado', 'p.nome_completo AS nm_4')
            ->leftJoin('associado AS a', 'm.id_associado', 'a.id')
            ->leftJoin('pessoas AS p', 'a.id_pessoa', 'p.id')
            ->leftJoin('cronograma as cro', 'm.id_cronograma', 'cro.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->where('p.status', 1)
            ->distinct()
            ->orderBy('p.nome_completo')
            ->get();


        $membrosQuery = DB::table('membro as m')
            ->leftJoin('cronograma as cro', 'm.id_cronograma', 'cro.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('associado as ass', 'm.id_associado', 'ass.id')
            ->leftJoin('pessoas as p', 'ass.id_pessoa', 'p.id')
            ->leftJoin('setor as st', 'gr.id_setor', 'st.id')
            ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
            ->leftJoin('tipo_funcao as tf', 'm.id_funcao', 'tf.id')
            ->orderBy('p.nome_completo')
            ->whereIn('gr.id_setor', $setoresAutorizado)
            ->select(
                'm.id',
                'p.nome_completo',
                'gr.nome as grupo_nome',
                'st.nome as setor_nome',
                'st.sigla as setor_sigla',
                'td.nome as dia_nome',
                'cro.h_inicio',
                'cro.h_fim',
                'tf.nome as nome_funcao',
                'st.nome as sala',
                'm.dt_inicio',
                'm.dt_fim',
                'ass.nr_associado',
                DB::raw("CASE WHEN m.dt_fim IS NOT NULL THEN 'Inativo' ELSE 'Ativo' END AS status")
            )
            // Aplicação dos filtros opcionais
            ->when($setorId, function ($query, $setorId) {
                return $query->where('st.id', $setorId);
            })
            ->when($grupoId, function ($query, $grupoId) {
                return $query->where('gr.id', $grupoId);
            })
            ->when($diaId !== null, function ($query) use ($diaId) {
                if ($diaId == 0) {
                    return $query->where('cro.dia_semana', 0);
                }
                return $query->where('cro.dia_semana', $diaId);
            })
            ->when($funcaoId, function ($query, $funcaoId) {
                return $query->where('m.id_funcao', $funcaoId);
            })
            ->when($nomeId, function ($query, $nomeId) {
                return $query->where('m.id_associado', $nomeId);
            });

        // Filtro de status
        if ($status && $status != 'Todos') {
            if ($status == 'Ativo') {
                $membrosQuery->whereNull('m.dt_fim')->orWhere('m.dt_fim', '<=', '1969-06-12');
            } elseif ($status == 'Inativo') {
                $membrosQuery->where('m.dt_fim', '>', '1969-06-12');
            }
        }

        // Definição dos status disponíveis
        $statu = [
            (object) ['nome' => 'Ativo'],
            (object) ['nome' => 'Inativo'],
            (object) ['nome' => 'Todos']
        ];

        // Paginação e organização dos resultados
        $membros = $membrosQuery->get();
        $result = [];

        foreach ($membros as $element) {
            $result[$element->nome_completo . ' - ' . $element->nr_associado][$element->id] = $element;
        }

        $result = $this->paginate($result, 50);
        $result->withPath('');

        // Obter dados para os selects do filtro
        $grupo = DB::table('grupo')
            ->leftJoin('setor', 'grupo.id_setor', 'setor.id')
            ->select('grupo.id', 'grupo.nome as nome_grupo', 'setor.sigla')
            ->whereIn('id_setor', $setoresAutorizado)
            ->get();

        $setor = DB::table('setor')
            ->select('id', 'nome', 'sigla')
            ->whereIn('id', $setoresAutorizado)
            ->get();

        $dias = DB::table('tipo_dia')
            ->select('id', 'nome')
            ->get();

        $funcao = DB::table('tipo_funcao')->get();

        // Retorno para a view
        return view('relatorios.gerenciar-relatorio-pessoas-grupo', compact(
            'membros',
            'grupo',
            'setor',
            'dias',
            'atendentesParaSelect',
            'result',
            'funcao',
            'statu'
        ));
    }
    public function indexSetor(Request $request)
    {
        // Obter os parâmetros de busca
        $setorId = $request->input('setor');
        $grupoId = $request->input('grupo');
        $diaId = $request->input('dia');
        $nomeId = $request->input('nome');
        $funcaoId = $request->input('funcao');

        // Definir o número de itens por página
        $itemsPerPage = 50;
        $itemsPerPage = 50;
        $setoresAutorizado = array();
        foreach (session()->get('acessoInterno') as $perfil) {

            $setoresAutorizado = array_merge($setoresAutorizado, array_column($perfil, 'id_setor'));
        }

        // Obter os atendentes para o select2
        $atendentesParaSelect = DB::table('membro AS m')
            ->select('m.id_associado AS ida', 'p.nome_completo AS nm_4')
            ->leftJoin('associado AS a', 'm.id_associado', 'a.id')
            ->leftJoin('pessoas AS p', 'a.id_pessoa', 'p.id')
            ->leftJoin('cronograma as cro', 'm.id_cronograma', 'cro.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->where('p.status', 1)
            ->distinct()
            ->orderBy('p.nome_completo')
            ->get();


        $membrosQuery = DB::table('membro as m')
            ->leftJoin('cronograma as cro', 'm.id_cronograma', 'cro.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('associado as ass', 'm.id_associado', 'ass.id')
            ->leftJoin('pessoas as p', 'ass.id_pessoa', 'p.id')
            ->leftJoin('setor as st', 'gr.id_setor', 'st.id')
            ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
            ->leftJoin('tipo_funcao as tf', 'm.id_funcao', 'tf.id')
            ->orderBy('st.nome')
            ->orderBy('td.id')
            ->orderBy('gr.nome')
            ->orderBy('tf.id')
            ->whereIn('gr.id_setor', $setoresAutorizado)
            ->select('m.id', 'p.nome_completo', 'gr.nome as grupo_nome', 'st.nome as setor_nome', 'st.sigla as setor_sigla', 'td.nome as dia_nome', 'cro.h_inicio', 'cro.h_fim', 'tf.nome as nome_funcao', 'st.nome as sala') // Selecionando o nome da função
            ->when($setorId, function ($query, $setorId) {
                return $query->where('st.id', $setorId);
            })
            ->when($grupoId, function ($query, $grupoId) {

                return $query->where('gr.id', $grupoId);
            })
            ->when($diaId, function ($query, $diaId) {
                return $query->where('cro.dia_semana', $diaId);
            })
            ->when($funcaoId, function ($query, $funcaoId) {
                return $query->where('m.id_funcao', $funcaoId);
            })
            ->when($diaId == 0 && $diaId != null, function ($query) {
                return $query->where('cro.dia_semana', 0);
            })
            ->when($nomeId, function ($query, $nomeId) {
                return $query->where('m.id_associado', $nomeId);
            });


        // Paginar os resultados
        $membros = $membrosQuery->paginate(50)
            ->appends([
                'setor' => $setorId,
                'grupo' => $grupoId,
                'dia' => $diaId,
                'funcao' => $funcaoId,
                'nome' => $nomeId,
            ]);

        // Obter os grupos
        $grupo = DB::table('grupo')
            ->leftJoin('setor', 'grupo.id_setor', 'setor.id')
            ->select('grupo.id', 'grupo.nome as nome_grupo', 'setor.sigla')
            ->whereIn('id_setor', $setoresAutorizado)
            ->get();

        // Obter os setores
        $setor = DB::table('setor')
            ->select('id', 'nome', 'sigla')
            ->whereIn('id', $setoresAutorizado)
            ->get();

        // Obter os dias
        $dias = DB::table('tipo_dia')
            ->select('id', 'nome')
            ->get();

        $funcao = DB::table('tipo_funcao')->get();



        //      dd($membros, $result);



        return view('relatorios.gerenciar-relatorio-setor-pessoas', compact('membros', 'grupo', 'setor', 'dias', 'atendentesParaSelect', 'funcao'));
    }



    public function paginate($items, $perPage = 5, $page = null)
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $total = count($items);
        $currentpage = $page;
        $offset = ($currentpage * $perPage) - $perPage;
        $itemstoshow = array_slice($items, $offset, $perPage);
        return new LengthAwarePaginator($itemstoshow, $total, $perPage);
    }

    public function relatorioReuniao(Request $request)
    {
        $dt_inicio = $request->dt_inicio == null ? (Carbon::now()->subMonth()->format('Y-m-d')) : $request->dt_inicio;
        $dt_fim =  $request->dt_fim == null ? Carbon::today()->format('Y-m-d') : $request->dt_fim;

        //Traz todas as reuniões onde a pessoa logada é Dirigente ou Sub-dirigente
        $cronogramasAutorizados = DB::table('membro as m')
            ->leftJoin('associado as ass', 'm.id_associado', 'ass.id')
            ->where('ass.id_pessoa', session()->get('usuario.id_pessoa'))
            ->where('id_funcao', '<', 3)
            ->distinct('m.id_cronograma')
            ->pluck('m.id_cronograma');

        // Retorna a lista de grupos para a tabela
        $reunioesDirigentes = DB::table('cronograma as cr')
            ->select('cr.id', 'gr.nome', 'cr.id', 'gr.status_grupo', 'd.nome as dia', 'cr.h_inicio', 'cr.h_fim')
            ->leftJoin('grupo as gr', 'cr.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as d', 'cr.dia_semana', 'd.id')
            ->orderBy('gr.nome');

        // Retorna os nomes dos grupo para o campo de pesquisa, com todos os dados
        $reunioesPesquisa = DB::table('cronograma as cr')
            ->select(
                'cr.id',
                'cr.h_fim',
                'st.sigla',
                't.sigla as SiglaTratamento',
                'cr.modificador',
                'ts.descricao',
                'gr.nome',
                'd.nome as dia',
                'cr.h_inicio',
                'cr.h_fim',
                DB::raw("(CASE WHEN cr.data_fim IS NOT NULL THEN 'Inativo' ELSE 'Ativo' END) AS status") // Correção aqui
            )
            ->leftJoin('tipo_tratamento as t', 'cr.id_tipo_tratamento', 't.id')
            ->leftJoin('grupo as gr', 'cr.id_grupo', 'gr.id')
            ->leftJoin('setor as st', 'gr.id_setor', 'st.id')
            ->leftJoin('tipo_dia as d', 'cr.dia_semana', 'd.id')
            ->leftJoin('tipo_status_grupo AS ts', 'gr.status_grupo', 'ts.id')
            ->orderBy('gr.nome', 'asc');

        // Conta todas as presenças nas datas especificadas
        $presencasCountAssistidos = DB::table('presenca_cronograma as pc')
            ->leftJoin('dias_cronograma as dc', 'pc.id_dias_cronograma', 'dc.id')
            ->where('dc.data', '>=', $dt_inicio)
            ->where('dc.data', '<=', $dt_fim)
            ->groupBy('presenca')
            ->select('presenca', DB::raw("count(*) as total"));

        // Conta todos os acompanhantes, exclui PTH por incosistencia
        $acompanhantes = DB::table('dias_cronograma as dc')
            ->leftJoin('cronograma as cr', 'dc.id_cronograma', 'cr.id')
            ->where('dc.data', '>=', $dt_inicio)
            ->where('dc.data', '<=', $dt_fim)
            ->whereNot('id_tipo_tratamento', 3);

        // Conta os assistidos PTH
        $presencasCountPTH = DB::table('dias_cronograma as dc')
            ->leftJoin('cronograma as cr', 'dc.id_cronograma', 'cr.id')
            ->where('dc.data', '>=', $dt_inicio)
            ->where('dc.data', '<=', $dt_fim)
            ->where('id_tipo_tratamento', 3);

        // Conta todas as presenças de membros no periodo especificado
        $presencasCountMembros = DB::table('presenca_membros as pc')
            ->leftJoin('dias_cronograma as dc', 'pc.id_dias_cronograma', 'dc.id')
            ->where('dc.data', '>=', $dt_inicio)
            ->where('dc.data', '<=', $dt_fim)
            ->groupBy('presenca')
            ->select('presenca', DB::raw("count(*) as total"));

        // Caso o usuário não seja Master Admin, aplique as regras
        if (!in_array(36, session()->get('usuario.acesso'))) {
            $reunioesDirigentes = $reunioesDirigentes->whereIn('cr.id', $cronogramasAutorizados);
            $reunioesPesquisa = $reunioesPesquisa->whereIn('mem.id_cronograma', $cronogramasAutorizados);
            $presencasCountAssistidos = $presencasCountAssistidos->whereIn('dc.id_cronograma', $cronogramasAutorizados);
            $acompanhantes = $acompanhantes->whereIn('id_cronograma', $cronogramasAutorizados);
            $presencasCountPTH = $presencasCountPTH->whereIn('id_cronograma', $cronogramasAutorizados);
            $presencasCountMembros = $presencasCountMembros->whereIn('dc.id_cronograma', $cronogramasAutorizados);
        }

        // Caso algum grupo seja pesquisado
        if ($request->nome_grupo) {
            $reunioesDirigentes = $reunioesDirigentes->where('cr.id', $request->nome_grupo);
            $presencasCountAssistidos = $presencasCountAssistidos->where('dc.id_cronograma', $request->nome_grupo);
            $acompanhantes = $acompanhantes->where('dc.id_cronograma', $request->nome_grupo);
            $presencasCountPTH = $presencasCountPTH->where('dc.id_cronograma', $request->nome_grupo);
            $presencasCountMembros = $presencasCountMembros->where('dc.id_cronograma', $request->nome_grupo);
        }


        $reunioesDirigentes = $reunioesDirigentes->get()->toArray();
        $reunioesPesquisa = $reunioesPesquisa->get();
        $presencasCountAssistidos = $presencasCountAssistidos->get()->toArray();
        $presencasCountMembros = $presencasCountMembros->get()->toArray();
        $acompanhantes = $acompanhantes->sum('nr_acompanhantes');
        $presencasCountPTH = $presencasCountPTH->sum('nr_acompanhantes');

        // Caso exista presença na varíavel, adiciona os PTH a contagem, caso não exista, gera uma com PTH
        if (array_search(true, array_column($presencasCountAssistidos, 'presenca'))) {
            $presencasCountAssistidos[array_search(true, array_column($presencasCountAssistidos, 'presenca'))]->total += $presencasCountPTH;
        } else {
            $presencasCountAssistidos[] = (object) ['presenca' => true, 'total' => $presencasCountPTH];
        }

        // Caso a varíavel saia 100% nula, plota o grafico vazio
        if ($presencasCountAssistidos == []) {
            $presencasCountAssistidos[0] = 0;
            $presencasCountAssistidos[1] = 0;

            // Caso tenha apenas falta, desloca a presença para a segunda posição, e deixa a primeira vazia
        } elseif (!in_array(false, array_values(array_column($presencasCountAssistidos, 'presenca')))) {
            $presencasCountAssistidos[1] = $presencasCountAssistidos[0]->total;
            $presencasCountAssistidos[0] = 0;

            // Caso não tenha presenca, cria um espaço falso na segunda posição para plotar o gráfico
        } elseif (!in_array(true, array_values(array_column($presencasCountAssistidos, 'presenca')))) {
            $presencasCountAssistidos[0] = $presencasCountAssistidos[0]->total;
            $presencasCountAssistidos[1] = 0;

            // Caso todo os dados estejam de acordo, plota o gráfico com os dados exatamente como pensado
        } else {
            $presencasCountAssistidos[0] = $presencasCountAssistidos[0]->total;
            $presencasCountAssistidos[1] = $presencasCountAssistidos[1]->total;
        }
        $presencasCountAssistidos[2] =  $acompanhantes;

        // Caso a varíavel saia 100% nula, plota o grafico vazio
        if ($presencasCountMembros == []) {
            $presencasCountMembros[0] = 0;
            $presencasCountMembros[1] = 0;

            // Caso tenha apenas falta, desloca a presença para a segunda posição, e deixa a primeira vazia
        } elseif (!in_array(false, array_values(array_column($presencasCountMembros, 'presenca')))) {
            $presencasCountMembros[1] = $presencasCountMembros[0]->total;
            $presencasCountMembros[0] = 0;

            // Caso não tenha presenca, cria um espaço falso na segunda posição para plotar o gráfico
        } elseif (!in_array(true, array_values(array_column($presencasCountMembros, 'presenca')))) {
            $presencasCountMembros[0] = $presencasCountMembros[0]->total;
            $presencasCountMembros[1] = 0;

            // Caso todo os dados estejam de acordo, plota o gráfico com os dados exatamente como pensado
        } else {
            $presencasCountMembros[0] = $presencasCountMembros[0]->total;
            $presencasCountMembros[1] = $presencasCountMembros[1]->total;
        }

        //
        $presencasCountMembros[2] = 0;

        return view('relatorios.relatorio-assistido-reuniao', compact('reunioesDirigentes', 'presencasCountAssistidos', 'presencasCountMembros', 'reunioesPesquisa', 'dt_inicio', 'dt_fim'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function visualizarReuniao(Request $request, string $id)
    {

        $dt_inicio = $request->dt_inicio == null ? (Carbon::now()->subMonth()->firstOfMonth()->format('Y-m-d')) : $request->dt_inicio;
        $dt_fim =  $request->dt_fim == null ? Carbon::today()->format('Y-m-d') : $request->dt_fim;

        //Traz todas as reuniões onde a pessoa logada é Dirigente ou Sub-dirigente

        $grupo = DB::table('cronograma as cr')
            ->leftJoin('grupo as gr', 'cr.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as td', 'cr.dia_semana', 'td.id')
            ->select('gr.nome', 'td.nome as dia')
            ->where('cr.id', $id)
            ->first();

        $presencasCountAssistidos = DB::table('presenca_cronograma as pc')
            ->leftJoin('dias_cronograma as dc', 'pc.id_dias_cronograma', 'dc.id')
            ->where('dc.data', '>=', $dt_inicio)
            ->where('dc.data', '<', $dt_fim)
            ->where('id_cronograma', $id)
            ->groupBy('presenca')
            ->select('presenca', DB::raw("count(*) as total"));

        $acompanhantes = DB::table('dias_cronograma')->where('id_cronograma', $id);

        $presencasCountMembros = DB::table('presenca_membros as pc')
            ->leftJoin('dias_cronograma as dc', 'pc.id_dias_cronograma', 'dc.id')
            ->where('dc.data', '>=', $dt_inicio)
            ->where('dc.data', '<', $dt_fim)
            ->where('id_cronograma', $id)
            ->groupBy('presenca')
            ->select('presenca', DB::raw("count(*) as total"));





        $presencasAssistidos = DB::table('tratamento as tr')
            ->leftJoin('tipo_status_tratamento as tst', 'tr.status', 'tst.id')
            ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftJoin('presenca_cronograma as pc', 'tr.id', 'pc.id_tratamento')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->leftJoin('pessoas as p', 'at.id_assistido', 'p.id')
            ->leftJoin('dias_cronograma as dc', 'pc.id_dias_cronograma', 'dc.id')
            ->leftJoin('cronograma as cro', 'dc.id_cronograma', 'cro.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->where('enc.dh_enc', '<=', $dt_fim)
            ->where('id_reuniao', $id)
            ->select('tr.id', 'p.nome_completo', 'tst.nome as status', 'dc.data', 'gr.nome as grupo', 'pc.presenca',)
            ->orderBy('p.nome_completo')
            ->get();

        $presencasMembros = DB::table('membro as m')
            ->leftJoin('associado as ass', 'm.id_associado', 'ass.id')
            ->leftJoin('pessoas as p', 'ass.id_pessoa', 'p.id')
            ->leftJoin('presenca_membros as pm', 'm.id', 'pm.id_membro')
            ->leftJoin('dias_cronograma as dc', 'pm.id_dias_cronograma', 'dc.id')
            ->where(function ($query) use ($dt_fim) {
                $query->where('m.dt_fim', '<', $dt_fim);
                $query->orWhere('m.dt_fim', NULL);
            })
            ->where('m.id_cronograma', $id)
            ->select('m.id', 'p.nome_completo', 'dc.data', 'pm.presenca')
            ->get();



        $presencasCountAssistidos = $presencasCountAssistidos->get();
        $presencasCountAssistidos = json_decode(json_encode($presencasCountAssistidos));

        $acompanhantes = $acompanhantes->sum('nr_acompanhantes');


        $presencasCountMembros = $presencasCountMembros->get();
        $presencasCountMembros = json_decode(json_encode($presencasCountMembros));

        $presencasMembrosArray = array();
        foreach ($presencasMembros as $element) {
            $presencasMembrosArray["$element->nome_completo"][] = $element;
        }

        $presencasAssistidosArray = array();
        foreach ($presencasAssistidos as $element) {
            $presencasAssistidosArray["$element->nome_completo - $element->status"][] = $element;
        }


        if ($presencasCountAssistidos == []) {
            $presencasCountAssistidos[0] = 0;
            $presencasCountAssistidos[1] = 0;
        } elseif (!in_array(false, array_values(array_column($presencasCountAssistidos, 'presenca')))) {
            $presencasCountAssistidos[1] = $presencasCountAssistidos[0]->total;
            $presencasCountAssistidos[0] = 0;
        } elseif (!in_array(true, array_values(array_column($presencasCountAssistidos, 'presenca')))) {
            $presencasCountAssistidos[0] = $presencasCountAssistidos[0]->total;
            $presencasCountAssistidos[1] = 0;
        } else {
            $presencasCountAssistidos[0] = $presencasCountAssistidos[0]->total;
            $presencasCountAssistidos[1] = $presencasCountAssistidos[1]->total;
        }
        $presencasCountAssistidos[2] =  $acompanhantes;

        if ($presencasCountMembros == []) {
            $presencasCountMembros[0] = 0;
            $presencasCountMembros[1] = 0;
        } elseif (!in_array(false, array_values(array_column($presencasCountMembros, 'presenca')))) {
            $presencasCountMembros[1] = $presencasCountMembros[0]->total;
            $presencasCountMembros[0] = 0;
        } elseif (!in_array(true, array_values(array_column($presencasCountMembros, 'presenca')))) {
            $presencasCountMembros[0] = $presencasCountMembros[0]->total;
            $presencasCountMembros[1] = 0;
        } else {
            $presencasCountMembros[0] = $presencasCountMembros[0]->total;
            $presencasCountMembros[1] = $presencasCountMembros[1]->total;
        }
        $presencasCountMembros[2] = 0;

        return view('relatorios.visualizar-assistido-reuniao', compact('id', 'presencasAssistidosArray', 'presencasMembrosArray', 'presencasCountAssistidos', 'presencasCountMembros', 'dt_inicio', 'dt_fim', 'grupo'));
    }
    public function vagasGrupos(Request $request)
    {
        $now = Carbon::now()->format('Y-m-d');
        // Iniciar a consulta
        $grupos = DB::table('cronograma as cro')
            ->leftJoin('tipo_tratamento as t', 'cro.id_tipo_tratamento', 't.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
            ->leftJoin('setor as st', 'gr.id_setor', 'st.id')
            ->where(function ($query) use ($now) {
                $query->where('cro.data_fim', '>', $now);
                $query->orWhere('cro.data_fim', null);
            })
            ->select(
                DB::raw('
                (select count(*) from tratamento tr where tr.id_reuniao = cro.id and tr.status < 3) as trat'),
                't.id',
                't.descricao',
                'cro.id',
                'gr.nome as nome',
                'td.nome as dia',
                'cro.h_inicio',
                'cro.h_fim',
                'st.sigla as setor',
                'st.id as id_setor',
                'cro.max_atend'
            )
            ->orderBy('gr.nome');
        // Consultar setores para o filtro
        $setores = DB::table('setor')
            ->orderBy('nome');


        // Consultar grupos
        $grupo2 = DB::table('cronograma as cro')
            ->leftJoin('tipo_tratamento as t', 'cro.id_tipo_tratamento', 't.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
            ->leftJoin('setor as st', 'gr.id_setor', 'st.id')
            ->where(function ($query) use ($now) {
                $query->where('cro.data_fim', '>', $now);
                $query->orWhere('cro.data_fim', null);
            })
            ->select('t.id', 't.descricao', 'cro.id', 'gr.nome', 'cro.h_inicio', 'cro.h_fim', 'st.sigla as setor', 'td.nome as dia_semana');



        // Consultar tratamentos
        $tratamento = DB::table('tipo_tratamento')->get();

        // Filtros
        if ($request->grupo != null) {
            $grupos = $grupos->where('cro.id', $request->grupo);
        }

        if ($request->setor) {
            $grupos = $grupos->where('gr.id_setor', $request->setor);
        }

        if ($request->tratamento) {
            $grupos = $grupos->where('t.id', $request->tratamento);
            $grupo2 = $grupo2->where('t.id', $request->tratamento);
            $setores->where('id', $grupos->pluck('id_setor')->toArray());
        }
        $grupo2 = $grupo2->get();
        $setores = $setores->get();
        // Paginação dos grupos
        $grupos = $grupos->paginate(30)->appends([
            'grupo' => $request->grupo,
            'setor' => $request->setor,
            'tratamento' => $request->tratamento,
        ]);

        // Calcular a quantidade de vagas por tratamento (total)
        $quantidade_vagas_tipo_tratamento = 0;
        $tipo_de_tratamento = null;
        if ($request->tratamento) {
            // Somando as vagas de cada grupo conforme o tratamento selecionado
            foreach ($grupos as $grupo) {
                $quantidade_vagas_tipo_tratamento += $grupo->max_atend - $grupo->trat;
            }
            $tipo_de_tratamento = DB::table('tipo_tratamento')->where('id', '=', $request->input('tratamento'))->first();
            // dd($tipo_de_tratamento);
        }


        // Retornar a view com os dados
        return view('relatorios.vagas-grupos', compact('setores', 'grupos', 'grupo2', 'tratamento', 'quantidade_vagas_tipo_tratamento', 'tipo_de_tratamento'));
    }

    public function AtendimentosRel(Request $request)
    {

        $now = Carbon::now()->format('Y-m-d');
        $dt_inicio = $request->dt_inicio == null ? (Carbon::now()->subMonth()->firstOfMonth()->format('Y-m-d')) : $request->dt_inicio;
        $dt_fim =  $request->dt_fim == null ? Carbon::today()->format('Y-m-d') : $request->dt_fim;
        // Iniciar a consulta
        $grupos = DB::table('cronograma as cro')
            ->leftJoin('tratamento as tr', 'cro.id', 'tr.id')
            ->leftJoin('tipo_tratamento as t', 'cro.id_tipo_tratamento', 't.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
            ->leftJoin('setor as st', 'gr.id_setor', 'st.id')
            ->whereIn('t.id', [1, 2, 3, 4, 6])
            ->select(
                'cro.id',
                'cro.h_inicio',
                'cro.h_fim',
                't.descricao',
                't.id as id_tp_tratamento',
                'tr.status',
                'cro.max_atend',
                'gr.nome as nome',
                'td.nome as dia',
                't.sigla',
                'st.sigla as setor',
                'st.id as id_setor',
            )
            ->orderBy('gr.nome');

        // Consultar setores para o filtro
        $setores = DB::table('setor')
            ->whereIn('id', [48, 50, 46, 72])
            ->orderBy('nome');

        // Consultar grupos
        $grupo2 =  DB::table('cronograma as cro')
            ->leftJoin('tratamento as tr', 'cro.id', 'tr.id')
            ->leftJoin('tipo_tratamento as t', 'cro.id_tipo_tratamento', 't.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
            ->leftJoin('setor as st', 'gr.id_setor', 'st.id')
            ->whereIn('t.id', [1, 2, 3, 4, 6])
            ->whereIn('tr.status', [2, 3, 4])
            ->select(
                'cro.id',
                'cro.h_inicio',
                'cro.h_fim',
                'gr.nome as nome',
                'td.nome as dia_semana',
                't.sigla',
                'st.sigla as setor',
                'st.id as id_setor',
            )
            ->orderBy('gr.nome');


        // Consultar tratamentos
        $tratamento = DB::table('tipo_tratamento')->whereIn('id', [1, 2, 3, 4, 6])->get();

        // Filtros
        if ($request->setor) {
            $grupos = $grupos->where('gr.id_setor', $request->setor);
        }

        if ($request->tratamento) {
            $grupos = $grupos->where('t.id', $request->tratamento);
            $grupo2 = $grupo2->where('t.id', $request->tratamento);
            $setores->where('id', $grupos->pluck('id_setor')->toArray());
        }
        $grupos = $grupos->get()->toArray();
        $grupo2 = $grupo2->get();
        $setores = $setores->get();



        // Retorna todos os tratamentos ativos, por grupo
        $tratamentosAtivos = DB::table('tratamento as tra')
            ->select('tra.id_reuniao', DB::raw("COUNT('tra.id')"))
            ->leftJoin('encaminhamento as enc', 'tra.id', 'enc.id')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->groupBy('tra.id_reuniao')
            ->where(function ($query) use ($dt_inicio, $dt_fim) {

                // Data Inicio Tratamento
                $query->where(function ($subQuery) use ($dt_inicio, $dt_fim) {
                    $subQuery->where(function ($innerQuery) use ($dt_inicio, $dt_fim) {
                        $innerQuery->where('tra.dt_inicio', '>=', $dt_inicio)->where('tra.dt_inicio', '<=', $dt_fim);
                    });
                    $subQuery->orWhere('tra.dt_inicio', '<=', $dt_inicio);
                });

                // Data Fim Tratamento
                $query->where(function ($subQuery) use ($dt_inicio, $dt_fim) {
                    $subQuery->where(function ($innerQuery) use ($dt_inicio, $dt_fim) {
                        $innerQuery->where('tra.dt_fim', '>=', $dt_inicio)->where('tra.dt_inicio', '<=', $dt_fim);
                    });
                    $subQuery->orWhere('tra.dt_fim', '>=', $dt_fim);
                    $subQuery->orWhere('tra.dt_fim', NULL);
                });
            })
            ->get()
            ->toArray();

        // Retorna os números de acompanhantes, por grupo
        $acomp = DB::table('dias_cronograma as dc')
            //->leftJoin('presenca_cronograma as pc', 'dc.id', 'pc.id_dias_cronograma')
            ->where('data', '>=', $dt_inicio)
            ->where('data', '<=', $dt_fim)
            ->groupBy('id_cronograma')
            ->select(
                'id_cronograma',
                DB::raw('SUM(dc.nr_acompanhantes) as acomp'),
                //  DB::raw('SUM(CASE WHEN pc.presenca = TRUE THEN 1 ELSE 0 END) as assist')
            )->get()
            ->toArray();

        // Retorna os números de passes reais, por grupo
        $passes = DB::table('dias_cronograma as dc')
            ->leftJoin('presenca_cronograma as pc', 'dc.id', 'pc.id_dias_cronograma')
            ->where('data', '>=', $dt_inicio)
            ->where('data', '<=', $dt_fim)
            ->groupBy('id_cronograma')
            ->select(
                'id_cronograma',
                DB::raw('SUM(CASE WHEN pc.presenca = TRUE THEN 1 ELSE 0 END) as assist')
            )->get()
            ->toArray();

        // dd($passes);


        // if ($request->tipo_visualizacao == 2) {
        //     Carbon::setlocale(config('app.locale'));
        //     $meses = CarbonPeriod::create($dt_inicio, $dt_fim)->month()->toArray();

        //     foreach ($meses as $mes) {

        //         $tratamentosAtivos
        //         $acomp
        //         $passes
        //     }
        // } else {
        // Insere os atendimentos
        foreach ($grupos as $key => $grupo) {


            $tratamentosAtivosForeach = (clone $tratamentosAtivos[array_search($grupo->id, array_column($tratamentosAtivos, 'id_reuniao'))])->count;
            $acompForeach = array_search($grupo->id, array_column($acomp, 'id_cronograma')) !== false ? (clone $acomp[array_search($grupo->id, array_column($acomp, 'id_cronograma'))])->acomp : null;
            $passesForeach = array_search($grupo->id, array_column($passes, 'id_cronograma')) !== false ? (clone $passes[array_search($grupo->id, array_column($passes, 'id_cronograma'))])->assist : null;

            if ($grupo->id_tp_tratamento == 3) { // Caso seja um grupo de PTH, conta os assistidos
                $grupos[$key]->passes =  $acompForeach;
                // $grupos[$key]->acompanhantes =  '-';
            } else {
                $grupos[$key]->atendimentos =  $tratamentosAtivosForeach;
                $grupos[$key]->acompanhantes =  $acompForeach;
                $grupos[$key]->passes =  $passesForeach;
            }
        }

        // Pesquisa de grupos
        if ($request->grupo != null) {

            $buffer = array();
            foreach ($grupos as $grupo) {
                if (in_array($grupo->id, $request->grupo)) {
                    $buffer[$grupo->id]['descricao'] = $grupo->descricao;
                    $buffer[$grupo->id]['nome'] = $grupo->nome;
                    $buffer[$grupo->id]['sigla'] =  $grupo->sigla;
                    $buffer[$grupo->id]['dia_semana'] = $grupo->dia;
                    $buffer[$grupo->id]['h_inicio'] = $grupo->h_inicio;
                    $buffer[$grupo->id]['h_fim'] = $grupo->h_fim;
                    $buffer[$grupo->id]['id_tp_tratamento'] = $grupo->id_tp_tratamento;

                    isset($grupo->atendimentos) ? $buffer[$grupo->id]['atendimentos'] = $grupo->atendimentos : null;
                    isset($grupo->acompanhantes) ? $buffer[$grupo->id]['acompanhantes'] = $grupo->acompanhantes : null;
                    isset($grupo->passes) ? $buffer[$grupo->id]['passes'] = $grupo->passes : null;
                }
            }
            $grupos = $buffer;
        } else {
            $buffer = array();
            foreach ($grupos as $grupo) {
                $buffer[$grupo->id_tp_tratamento]['descricao'] = $grupo->descricao;
                $buffer[$grupo->id_tp_tratamento]['sigla'] =  $grupo->sigla;
                $buffer[$grupo->id_tp_tratamento]['id'] =  $grupo->id;

                if (isset($grupo->atendimentos)) {
                    array_key_exists("atendimentos", $buffer[$grupo->id_tp_tratamento]) ?
                        $buffer[$grupo->id_tp_tratamento]['atendimentos'] += $grupo->atendimentos :
                        $buffer[$grupo->id_tp_tratamento]['atendimentos'] = $grupo->atendimentos;
                }

                if (isset($grupo->passes)) {
                    array_key_exists("passes", $buffer[$grupo->id_tp_tratamento]) ?
                        $buffer[$grupo->id_tp_tratamento]['passes'] += $grupo->passes :
                        $buffer[$grupo->id_tp_tratamento]['passes'] = $grupo->passes;
                }

                if (isset($grupo->acompanhantes)) {
                    array_key_exists("acompanhantes", $buffer[$grupo->id_tp_tratamento]) ?
                        $buffer[$grupo->id_tp_tratamento]['acompanhantes'] += $grupo->acompanhantes :
                        $buffer[$grupo->id_tp_tratamento]['acompanhantes'] = $grupo->acompanhantes;
                }

                $grupos = $buffer;
            }

            // Retornar a view com os dados
        }
        //  }




        return view('relatorios.gerenciar-relatorio-tratamento', compact('setores', 'grupos', 'grupo2', 'tratamento', 'dt_inicio', 'dt_fim'));
    }

    public function Atendimentos(Request $request)
    {
        $now = Carbon::now()->format('Y-m-d');
        $dt_inicio = $request->dt_inicio == null ? Carbon::now()->subMonth()->firstOfMonth()->format('Y-m-d') : $request->dt_inicio;
        $dt_fim = $request->dt_fim == null ? Carbon::today()->format('Y-m-d') : $request->dt_fim;



        $atendimentos = DB::table('atendimentos as at')
            ->leftJoin('pessoas as p', 'at.id_assistido', 'p.id')
            ->where('at.dh_chegada', '>=', $dt_inicio)
            ->where('at.dh_chegada', '<', $dt_fim);


        if ($request->tipo_visualizacao == 2) {
            Carbon::setlocale(config('app.locale'));
            $meses = CarbonPeriod::create($dt_inicio, $dt_fim)->month()->toArray();

            foreach ($meses as $mes) {

                if ($request->status_atendimento == 1) {
                    $nomeStatus = DB::table('tipo_status_atendimento')->where('id', $request->status_atendimento)->first();
                    $dadosChart[ucfirst($mes->locale('pt-br')->translatedFormat('F'))] = [
                        'Finalizados' => (clone $atendimentos)->where('at.status_atendimento', 6)->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                        'Cancelados' => (clone $atendimentos)->where('at.status_atendimento', 7)->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                        'Menores 18' => (clone $atendimentos)->where('at.menor_auto', true)->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                    ];
                } else if ($request->status_atendimento == 2) {
                    $dadosChart[ucfirst($mes->locale('pt-br')->translatedFormat('F'))] = [
                        'Homens' => (clone $atendimentos)->where('p.sexo', 1)->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                        'Mulheres' => (clone $atendimentos)->where('p.sexo', 2)->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                    ];
                } else if ($request->status_atendimento == 3) {
                    $dadosChart[ucfirst($mes->locale('pt-br')->translatedFormat('F'))] = [
                        'Domingo' => (clone $atendimentos)->whereRaw('EXTRACT(DOW FROM dh_chegada) = 0')->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                        'Segunda' => (clone $atendimentos)->whereRaw('EXTRACT(DOW FROM dh_chegada) = 1')->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                        'Terça' => (clone $atendimentos)->whereRaw('EXTRACT(DOW FROM dh_chegada) = 2')->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                        'Quarta' => (clone $atendimentos)->whereRaw('EXTRACT(DOW FROM dh_chegada) = 3')->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                        'Quinta' => (clone $atendimentos)->whereRaw('EXTRACT(DOW FROM dh_chegada) = 4')->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                        'Sexta' => (clone $atendimentos)->whereRaw('EXTRACT(DOW FROM dh_chegada) = 5')->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                        'Sábado' => (clone $atendimentos)->whereRaw('EXTRACT(DOW FROM dh_chegada) = 6')->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),

                    ];
                } else if ($request->status_atendimento == 4) {
                    $dadosChart[ucfirst($mes->locale('pt-br')->translatedFormat('F'))] = [
                        'Manhã' => (clone $atendimentos)->whereTime('dh_chegada', '>=', '07:00:00')->whereTime('dh_chegada', '<', '12:30:00')->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                        'Tarde' => (clone $atendimentos)->whereTime('dh_chegada', '>=', '12:30:00')->whereTime('dh_chegada', '<', '17:30:00')->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                        'Noite' => (clone $atendimentos)->whereTime('dh_chegada', '>=', '17:30:00')->whereTime('dh_chegada', '<', '23:30:00')->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),

                    ];
                } else {
                    $dadosChart[ucfirst($mes->locale('pt-br')->translatedFormat('F'))] = [
                        'Finalizados' => (clone $atendimentos)->where('at.status_atendimento', 6)->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                        'Cancelados' => (clone $atendimentos)->where('at.status_atendimento', 7)->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                        'Menores 18' => (clone $atendimentos)->where('at.menor_auto', true)->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                    ];
                }
            }
        } else {

            if ($request->status_atendimento == 1) {
                $nomeStatus = DB::table('tipo_status_atendimento')->where('id', $request->status_atendimento)->first();
                $dadosChart = [
                    'Finalizados' => (clone $atendimentos)->where('at.status_atendimento', 6)->count(),
                    'Cancelados' => (clone $atendimentos)->where('at.status_atendimento', 7)->count(),
                    'Menores 18' => (clone $atendimentos)->where('at.menor_auto', true)->count(),
                ];
            } else if ($request->status_atendimento == 2) {
                $dadosChart = [
                    'Homens' => (clone $atendimentos)->where('p.sexo', 1)->count(),
                    'Mulheres' => (clone $atendimentos)->where('p.sexo', 2)->count(),
                ];
            } else if ($request->status_atendimento == 3) {
                $dadosChart = [
                    'Domingo' => (clone $atendimentos)->whereRaw('EXTRACT(DOW FROM dh_chegada) = 0')->count(),
                    'Segunda' => (clone $atendimentos)->whereRaw('EXTRACT(DOW FROM dh_chegada) = 1')->count(),
                    'Terça' => (clone $atendimentos)->whereRaw('EXTRACT(DOW FROM dh_chegada) = 2')->count(),
                    'Quarta' => (clone $atendimentos)->whereRaw('EXTRACT(DOW FROM dh_chegada) = 3')->count(),
                    'Quinta' => (clone $atendimentos)->whereRaw('EXTRACT(DOW FROM dh_chegada) = 4')->count(),
                    'Sexta' => (clone $atendimentos)->whereRaw('EXTRACT(DOW FROM dh_chegada) = 5')->count(),
                    'Sábado' => (clone $atendimentos)->whereRaw('EXTRACT(DOW FROM dh_chegada) = 6')->count(),

                ];
            } else if ($request->status_atendimento == 4) {
                $dadosChart = [
                    'Manhã' => (clone $atendimentos)->whereTime('dh_chegada', '>=', '07:00:00')->whereTime('dh_chegada', '<', '12:30:00')->count(),
                    'Tarde' => (clone $atendimentos)->whereTime('dh_chegada', '>=', '12:30:00')->whereTime('dh_chegada', '<', '17:30:00')->count(),
                    'Noite' => (clone $atendimentos)->whereTime('dh_chegada', '>=', '17:30:00')->whereTime('dh_chegada', '<', '23:30:00')->count(),

                ];
            } else {
                $dadosChart = [
                    'Finalizados' => (clone $atendimentos)->where('at.status_atendimento', 6)->count(),
                    'Cancelados' => (clone $atendimentos)->where('at.status_atendimento', 7)->count(),
                    'Menores 18' => (clone $atendimentos)->where('at.menor_auto', true)->count(),
                ];
            }
        }
        //  dd($dadosChart);
        return view('relatorios.gerenciar-relatorio-atendimento', compact('dt_inicio', 'dt_fim', 'dadosChart'));
    }
    public function BalancoVoluntarios(Request $request)
    {
        $now = Carbon::now()->format('Y-m-d');
        $dt_inicio = $request->dt_inicio == null ? Carbon::now()->subMonth()->firstOfMonth()->format('Y-m-d') : $request->dt_inicio;
        $dt_fim = $request->dt_fim == null ? Carbon::today()->format('Y-m-d') : $request->dt_fim;



        $membros = DB::table('membro as m');
        $cronogramas = DB::table('cronograma as c');
        $dadosMembro = array();
        $dadosCronograma = array();
        $dadosChart = array();

        $grupos = DB::table('cronograma as cro')
            ->leftJoin('grupo AS g', 'cro.id_grupo', '=', 'g.id')
            ->leftJoin('setor AS s', 'g.id_setor', 's.id')
            ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
            ->leftJoin('salas as sl', 'cro.id_sala', 'sl.id')
            ->leftJoin('tipo_status_grupo AS ts', 'g.status_grupo', 'ts.id')
            ->select(
                'cro.id AS idc',
                'g.nome AS nomeg',
                's.sigla',
                'cro.h_inicio',
                'cro.h_fim',
                'sl.numero as sala',
                'td.nome as dia_semana',
                'ts.descricao AS descricao_status',
                DB::raw("(CASE WHEN cro.data_fim IS NOT NULL THEN 'Inativo' ELSE 'Ativo' END) AS status")
            )
            ->orderBy('g.nome', 'asc')
            ->get();

        if ($request->cronograma) {
            $membros = $membros->where('id_cronograma', $request->cronograma);
        }


        if ($request->tipo_visualizacao == 2) {
            Carbon::setlocale(config('app.locale'));
            $meses = CarbonPeriod::create($dt_inicio, $dt_fim)->toArray();

            foreach ($meses as $mes) {

                $mes = (clone $mes)->month == Carbon::parse($dt_inicio)->month ? Carbon::parse($dt_inicio) : (clone $mes)->firstOfMonth();
                $mesNext = (clone $mes)->addMonth(1)->month == Carbon::parse($dt_fim)->month ? Carbon::parse($dt_fim) : (clone $mes)->firstOfMonth()->addMonth(1);

                $dadosMembro = [
                    'Membros Ativos' => (clone $membros)
                        ->where(function ($where) use ($mes, $mesNext) {
                            $where->where(function ($query) use ($mes, $mesNext) {
                                $query->where('m.dt_inicio', '<=', $mes);
                                $query->where('m.dt_fim', '<=', $mesNext);
                                $query->where('m.dt_fim', '>=', $mes);
                            });
                            $where->orWhere(function ($query) use ($mesNext) {
                                $query->where('m.dt_inicio', '<=', $mesNext);
                                $query->where(function ($innerQuery) use ($mesNext) {
                                    $innerQuery->where('m.dt_fim', '>=', $mesNext);
                                    $innerQuery->orWhereNull('m.dt_fim');
                                });
                            });
                            $where->orWhere(function ($query) use ($mes, $mesNext) {
                                $query->where('m.dt_inicio', '>=', $mes);
                                $query->where('m.dt_fim', '<=', $mesNext);
                            });
                        })->count(),

                    'Membros Criados' => (clone $membros)
                        ->where('m.dt_inicio', '>=', $dt_inicio)
                        ->where('m.dt_inicio', '<=', $dt_fim)
                        ->whereMonth('dt_inicio', $mes->month)
                        ->whereYear('dt_inicio', $mes->year)
                        ->count(),

                    'Membros Inativados' => (clone $membros)
                        ->where('m.dt_fim', '>=', $dt_inicio)
                        ->where('m.dt_fim', '<=', $dt_fim)
                        ->whereMonth('dt_fim', $mes->month)
                        ->whereYear('dt_fim', $mes->year)
                        ->count()
                ];

                $dadosCronograma = [
                    'Cronogramas Ativos' => (clone $cronogramas)
                        ->where(function ($query) use ($mes, $mesNext) {
                            $query->where('c.data_inicio', '<=', $mes);
                            $query->where('c.data_fim', '<=', $mesNext);
                            $query->where('c.data_fim', '>=', $mes);
                        })
                        ->orWhere(function ($query) use ($mesNext) {
                            $query->where('c.data_inicio', '<=', $mesNext);
                            $query->where(function ($innerQuery) use ($mesNext) {
                                $innerQuery->where('c.data_fim', '>=', $mesNext);
                                $innerQuery->orWhereNull('c.data_fim');
                            });
                        })
                        ->orWhere(function ($query) use ($mes, $mesNext) {
                            $query->where('c.data_inicio', '>=', $mes);
                            $query->where('c.data_fim', '<=', $mesNext);
                        })
                        ->count(),

                    'Conogramas Criados' => (clone $cronogramas)
                        ->where('c.data_inicio', '>=', $dt_inicio)
                        ->where('c.data_inicio', '<=', $dt_fim)
                        ->whereMonth('data_inicio', $mes->month)
                        ->whereYear('data_inicio', $mes->year)
                        ->count(),

                    'Cronogramas Inativados' => (clone $cronogramas)
                        ->where('c.data_fim', '>=', $dt_inicio)
                        ->where('c.data_fim', '<=', $dt_fim)
                        ->whereMonth('data_fim', $mes->month)
                        ->whereYear('data_fim', $mes->year)
                        ->count()
                ];


                if ($request->tipo_relatorio == 3  or $request->cronograma) {
                    $dadosChart[ucfirst($mes->locale('pt-br')->translatedFormat('F'))] = $dadosMembro;
                } elseif ($request->tipo_relatorio == 2) {
                    $dadosChart[ucfirst($mes->locale('pt-br')->translatedFormat('F'))] = $dadosCronograma;
                } elseif ($request->tipo_relatorio == 1 or $request->tipo_relatorio == null) {
                    $dadosChart[ucfirst($mes->locale('pt-br')->translatedFormat('F'))] = array_merge($dadosCronograma, $dadosMembro);
                }
            }
        } else {
            $dadosMembro = [
                'Membros Ativos' => (clone $membros)
                    ->where(function ($where) use ($dt_inicio, $dt_fim) {
                        $where->where(function ($query) use ($dt_inicio, $dt_fim) {
                            $query->where('m.dt_inicio', '<', $dt_inicio);
                            $query->where('m.dt_fim', '<', $dt_fim);
                            $query->where('m.dt_fim', '>', $dt_inicio);
                        });
                        $where->orWhere(function ($query) use ($dt_fim) {
                            $query->where('m.dt_inicio', '<', $dt_fim);
                            $query->where(function ($innerQuery) use ($dt_fim) {
                                $innerQuery->where('m.dt_fim', '>', $dt_fim);
                                $innerQuery->orWhereNull('m.dt_fim');
                            });
                        });
                        $where->orWhere(function ($query) use ($dt_inicio, $dt_fim) {
                            $query->where('m.dt_inicio', '>', $dt_inicio);
                            $query->where('m.dt_fim', '<', $dt_fim);
                        });
                    })
                    ->count(),

                'Membros Criados' => (clone $membros)
                    ->where('m.dt_inicio', '>', $dt_inicio)
                    ->where('m.dt_inicio', '<', $dt_fim)
                    ->count(),

                'Membros Inativados' => (clone $membros)
                    ->where('m.dt_fim', '>', $dt_inicio)
                    ->where('m.dt_fim', '<', $dt_fim)
                    ->count()
            ];
            $dadosCronograma = [
                'Cronogramas Ativos' => (clone $cronogramas)
                    ->where(function ($query) use ($dt_inicio, $dt_fim) {
                        $query->where('c.data_inicio', '<', $dt_inicio);
                        $query->where('c.data_fim', '<', $dt_fim);
                        $query->where('c.data_fim', '>', $dt_inicio);
                    })
                    ->orWhere(function ($query) use ($dt_fim) {
                        $query->where('c.data_inicio', '<', $dt_fim);
                        $query->where(function ($innerQuery) use ($dt_fim) {
                            $innerQuery->where('c.data_fim', '>', $dt_fim);
                            $innerQuery->orWhereNull('c.data_fim');
                        });
                    })
                    ->orWhere(function ($query) use ($dt_inicio, $dt_fim) {
                        $query->where('c.data_inicio', '>', $dt_inicio);
                        $query->where('c.data_fim', '<', $dt_fim);
                    })
                    ->count(),

                'Conogramas Criados' => (clone $cronogramas)
                    ->where('c.data_inicio', '>', $dt_inicio)
                    ->where('c.data_inicio', '<', $dt_fim)
                    ->count(),

                'Cronogramas Inativados' => (clone $cronogramas)
                    ->where('c.data_fim', '>', $dt_inicio)
                    ->where('c.data_fim', '<', $dt_fim)
                    ->count()
            ];

            if ($request->tipo_relatorio == 3 or $request->cronograma) {
                $dadosChart = $dadosMembro;
            } elseif ($request->tipo_relatorio == 2) {
                $dadosChart = $dadosCronograma;
            } elseif ($request->tipo_relatorio == 1 or $request->tipo_relatorio == null) {
                $dadosChart = array_merge($dadosCronograma, $dadosMembro);
            }
        }

        return view('relatorios.gerenciar-balanco-voluntarios', compact('dt_inicio', 'dt_fim', 'dadosChart', 'grupos'));
    }

    public function trabalhadores(Request $request)
    {


        //RELATÓRIO DE TRABALHADORES COM BASE NO SETOR

        $setoresAutorizado = array();
        foreach (session()->get('acessoInterno') as $perfil) {

            $setoresAutorizado = array_merge($setoresAutorizado, array_column($perfil, 'id_setor'));
        }

        $trabalho = DB::table('grupo as g')
            ->leftJoin('cronograma as c', 'g.id', 'c.id_grupo')
            ->leftJoin('membro as m', 'c.id', 'm.id_cronograma')
            ->leftJoin('associado as a', 'm.id_associado', 'a.id')
            ->leftJoin('pessoas as p', 'a.id_pessoa', 'p.id')
            ->leftJoin('setor as s', 'g.id_setor', 's.id')
            ->leftJoin('setor as s1',  's.setor_pai', 's1.id')
            ->leftJoin('tp_nivel_setor as tn', 's.id_nivel', 'tn.id')
            ->leftJoin('tipo_dia as t', 'c.dia_semana', 't.id')
            ->leftJoin('tipo_funcao as tf', 'm.id_funcao', 'tf.id')
            ->leftJoin('tipo_tratamento as tt', 'c.id_tipo_tratamento', 'tt.id')
            ->whereNull('m.dt_fim')
            ->whereNull('c.data_fim')
            ->whereIn('g.id_setor', $setoresAutorizado)
            ->select('c.id as cid', 'm.id', 'm.id_funcao', 'tn.id', 'tn.nome as n_nome', 'p.id as pid', 'p.nome_completo', 'g.nome as g_nome', 's.nome as setor_nome', 's.setor_pai', 's.sigla as setor_sigla', 't.nome as dia_nome', 'c.h_inicio', 'c.h_fim', 'tf.nome as nome_funcao', 's.nome as sala', 'tt.sigla as t_sigla', DB::raw('count(m.id) as vlr_final'))
            ->groupBy('c.id', 'm.id', 't.id', 'm.id_funcao', 'tn.id', 'tn.nome', 'p.id', 'p.nome_completo', 'g.nome', 's.nome', 's.setor_pai', 's.sigla', 't.nome', 'tf.id', 'tf.nome', 's.nome', 'tt.sigla',);

        // Obter os parâmetros de busca
        $nivelId = $request->nivel;
        $setorId = $request->setor;
        $reuniaoId = $request->reuniao;
        $funcaoId = $request->funcao;
        $membroId = $request->membro;
        $reuniaoId = $request->reuniao;

        //dd($reuniaoId);

        if ($nivelId == 1 && $setorId === null) {
            $trabalho->where('s.id', '>', 0);
        } elseif ($nivelId == 1 && $setorId <> null) {

            $trabalho->where('s.id', $request->setor);
        } elseif ($nivelId > 1 && $setorId === null) {

            app('flasher')->addError('Selecione um setor');
            return redirect()->back();
        } elseif ($nivelId > 2 && $setorId > 0) {

            $trabalho->where(function ($query) use ($setorId) {
                $query->where('s.id', $setorId)
                    ->orWhere('s.setor_pai', $setorId);
            });
        }

        if ($reuniaoId === null) {
            $trabalho->where('c.id', '<>', 0);
        } else {

            $trabalho->whereIn('c.id', $reuniaoId);
        }


        if ($request->funcao) {
            $trabalho->where('m.id_funcao', $request->funcao);
        }

        if ($request->membro) {
            $trabalho->where('p.id', $request->membro);
        }

        $totmem = $trabalho->get()->sum('vlr_final');


        // Paginar os resultados
        $trabalho = $trabalho->orderBy('g.nome', 'asc')->orderBy('c.id', 'asc')->orderBy('t.id', 'asc')->orderBy('tf.id', 'asc')->orderBy('p.nome_completo', 'asc')->paginate(50);



        // Obter os níveis
        $nivel = DB::table('tp_nivel_setor as tn')
            ->leftJoin('setor as s', 'tn.id', 's.id_nivel')
            ->select('tn.id', 'tn.nome as s_nome')
            ->whereIn('s.id', $setoresAutorizado)
            ->groupBy('tn.id')
            ->orderBy('tn.id', 'asc')
            ->get();


        // Obter os setores
        $setor = DB::table('setor as s')
            ->select('s.id as sid', 's.nome', 's.sigla')
            ->whereIn('id', $setoresAutorizado)
            ->orderBy('nome', 'asc')
            ->get();


        // Obter os grupos
        $grupo = DB::table('grupo as g')
            ->leftJoin('cronograma as c', 'g.id', 'c.id_grupo')
            ->leftJoin('setor as s', 'g.id_setor', 's.id')
            ->leftJoin('tipo_dia as t', 'c.dia_semana', 't.id')
            ->leftJoin('tipo_tratamento as tt', 'c.id_tipo_tratamento', 'tt.id')
            ->select('g.nome as g_nome', 'c.h_inicio', 'c.id as cid', 't.sigla as d_sigla', 's.sigla as s_sigla', 'tt.sigla as t_sigla')
            ->whereNull('c.data_fim')
            ->whereIn('s.id', $setoresAutorizado)
            ->get();

        $funcao = DB::table('tipo_funcao')->orderBy('nome')->get();

        $membro = DB::table('membro as m')
            ->leftJoin('associado as a', 'm.id_associado', 'a.id')
            ->leftJoin('pessoas as p', 'a.id_pessoa', 'p.id')
            ->select('m.id', 'p.id as pid', 'p.nome_completo')
            ->distinct('p.nome_completo')
            ->orderBy('p.nome_completo', 'asc')
            ->get();


        return view('relatorios.setores-trabalhadores', compact('trabalho', 'nivel', 'setor', 'grupo', 'funcao', 'membro', 'totmem'));
    }
    public function curriculo(String $id)
    {
        session()->flash('usuario.url', str_replace(url('/'), '', url()->previous())); // Salva o caminho de entrada desta view
        session()->reflash(); // Permite um acesso temporário para inclusão

        $now = Carbon::today();

        // Retorna a view com os dados
        $dadosP = DB::table('pessoas as p')
            ->select(
                'p.nome_completo',
                'p.celular',
                'd.descricao',
                'p.dt_nascimento',
                'a.nr_associado',
                'a.id'

            )
            ->leftJoin('associado as a', 'p.id', 'a.id_pessoa')
            ->leftJoin('membro as m', 'a.id', 'm.id_associado')
            ->leftJoin('tp_ddd as d', 'p.ddd', '=', 'd.id')
            ->where('m.id', $id)
            ->first();

        // Obtém o membro e suas informações relacionadas
        $membros = DB::table('membro as m')
            ->select(
                'gr.nome as nome_grupo',
                'td.nome as dia',
                'cro.h_inicio',
                'cro.h_fim',
                'sl.numero as sala',
                's.nome as nome_setor',
                's.sigla',
                'tf.nome as nome_funcao',
                'm.dt_inicio',
                'm.dt_fim',
                'tt.descricao as trabalho',
                DB::raw("(CASE WHEN m.dt_fim > '1969-06-12' THEN 'Inativo' ELSE 'Ativo' END) as status_membro"),
                DB::raw("(CASE WHEN cro.modificador = 3 THEN 'Experimental' WHEN cro.modificador = 4 THEN 'Em Férias' WHEN cro.data_fim <= '$now' THEN 'Inativo' ELSE 'Ativo' END) as status"),

            )
            ->leftJoin('cronograma as cro', 'm.id_cronograma', 'cro.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('setor as s', 'gr.id_setor', 's.id')
            ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
            ->leftJoin('salas as sl', 'cro.id_sala', 'sl.id')
            ->leftJoin('tipo_status_grupo as tpg', 'cro.modificador', 'tpg.id')
            ->leftJoin('associado as a', 'm.id_associado', 'a.id')
            ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')
            ->leftJoin('tipo_tratamento as tt', 'cro.id_tipo_tratamento', 'tt.id')
            ->where('a.id', $dadosP->id)
            ->get()
            ->toArray();

        $bufferMembros = array();
        foreach ($membros as $membro) {
            $bufferMembros["$membro->nome_grupo($membro->sigla)-$membro->dia | $membro->h_inicio/$membro->h_fim | Sala $membro->sala | $membro->status"][] = $membro;
        }
        $membros = $bufferMembros;

        return view('relatorios.curriculo-membro', compact('membros', 'dadosP', 'id'));
    }

    function pdfCurriculo(String $id)
    {

        $now = Carbon::today();

        // Retorna a view com os dados
        $dadosP = DB::table('pessoas as p')
            ->select(
                'p.nome_completo',
                'p.celular',
                'd.descricao',
                'p.dt_nascimento',
                'a.nr_associado',
                'a.id'

            )
            ->leftJoin('associado as a', 'p.id', 'a.id_pessoa')
            ->leftJoin('membro as m', 'a.id', 'm.id_associado')
            ->leftJoin('tp_ddd as d', 'p.ddd', '=', 'd.id')
            ->where('m.id', $id)
            ->first();

        // Obtém o membro e suas informações relacionadas
        $membros = DB::table('membro as m')
            ->select(
                'gr.nome as nome_grupo',
                'td.nome as dia',
                'cro.h_inicio',
                'cro.h_fim',
                'sl.numero as sala',
                's.nome as nome_setor',
                's.sigla',
                'tf.nome as nome_funcao',
                'm.dt_inicio',
                'm.dt_fim',
                'tt.descricao as trabalho',
                DB::raw("(CASE WHEN m.dt_fim > '1969-06-12' THEN 'Inativo' ELSE 'Ativo' END) as status_membro"),
                DB::raw("(CASE WHEN cro.modificador = 3 THEN 'Experimental' WHEN cro.modificador = 4 THEN 'Em Férias' WHEN cro.data_fim < '$now' THEN 'Inativo' ELSE 'Ativo' END) as status"),

            )
            ->leftJoin('cronograma as cro', 'm.id_cronograma', 'cro.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('setor as s', 'gr.id_setor', 's.id')
            ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
            ->leftJoin('salas as sl', 'cro.id_sala', 'sl.id')
            ->leftJoin('tipo_status_grupo as tpg', 'cro.modificador', 'tpg.id')
            ->leftJoin('associado as a', 'm.id_associado', 'a.id')
            ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')
            ->leftJoin('tipo_tratamento as tt', 'cro.id_tipo_tratamento', 'tt.id')
            ->where('a.id', $dadosP->id)
            ->get()
            ->toArray();

        //   dd($membros, $dadosP);


        $pdf = Pdf::loadView('relatorios.pdf-curriculo-membro', compact('membros', 'dadosP'))->setPaper('a4', 'landscape');
        return $pdf->download($dadosP->nome_completo . '.pdf');
    }

    public function passes(Request $request)
    {
        $now = Carbon::now()->format('Y-m-d');

        $trata = DB::table('tipo_tratamento')->whereIn('id', [1, 2, 3, 6])->orderBy('descricao')->get();

        $dt_inicio = $request->dt_inicio;
        $dt_fim = $request->dt_fim;
        $tratamento = $request->tratamento;

        // Verifica se as variáveis têm valor, senão define um valor padrão
        $dt_inicio = $dt_inicio ? $dt_inicio : Carbon::now()->startOfMonth()->format('Y-m-d');
        $dt_fim = $dt_fim ? $dt_fim : Carbon::now()->endOfMonth()->format('Y-m-d');

        $passe = DB::table('dias_cronograma as dc')
            ->leftJoin('presenca_cronograma as pc', 'dc.id', 'pc.id_dias_cronograma')
            ->leftJoin('cronograma as c', 'dc.id_cronograma', 'c.id')
            ->leftJoin('tipo_tratamento as t', 'c.id_tipo_tratamento', 't.id')
            ->select(
                't.id as t_id',
                't.sigla as tsigla',
                't.descricao as tnome',
                DB::raw("(DATE_TRUNC('MONTH', dc.data) + INTERVAL '1 MONTH' - INTERVAL '1 DAY') as ultimo_dia_mes"),
                DB::raw('SUM(dc.nr_acompanhantes) as acomp'),
                DB::raw('SUM(CASE WHEN pc.presenca = TRUE THEN 1 ELSE 0 END) as assist')
            );

        if ($request->dt_inicio) {
            $passe->whereDate('dc.data', '>=', $dt_inicio);
        }
        if ($request->dt_fim) {
            $passe->whereDate('dc.data', '<=', $dt_fim);
        }

        if ($tratamento === null) {
            $passe->whereIn('t.id', [1, 2, 3, 6]);
        } else {
            $passe->where('t.id', $tratamento);
        }

        $passe = $passe->groupBy('t.id', 't.sigla', 't.descricao', DB::raw("(DATE_TRUNC('MONTH', dc.data) + INTERVAL '1 MONTH' - INTERVAL '1 DAY')"))
            ->orderBy('t.descricao')
            ->orderBy(DB::raw("(DATE_TRUNC('MONTH', dc.data) + INTERVAL '1 MONTH' - INTERVAL '1 DAY')"))
            ->get();

        // Para exibição, podemos organizar os dados para que a soma apareça antes de iniciar a próxima lista de t.id
        $resultadoAgrupado = [];
        foreach ($passe as $item) {
            $t_id = $item->t_id;

            if (!isset($resultadoAgrupado[$t_id])) {
                $resultadoAgrupado[$t_id] = [
                    'tsigla' => $item->tsigla,
                    'tnome' => $item->tnome,
                    'dados' => [],
                    'total_assist' => 0,
                    'total_acomp' => 0, // Adicionando o total de acompanhantes
                ];
            }

            $resultadoAgrupado[$t_id]['dados'][] = [
                'acomp' => $item->acomp,
                'assist' => $item->assist,
                'ultimo_dia_mes' => $item->ultimo_dia_mes, // Adicionando a chave
            ];

            $resultadoAgrupado[$t_id]['total_assist'] += $item->assist;
            $resultadoAgrupado[$t_id]['total_acomp'] += $item->acomp; // Somando acompanhantes
        }

        $totalGeralAssistidos = array_sum(array_column($resultadoAgrupado, 'total_assist'))
            + array_sum(array_column($resultadoAgrupado, 'total_acomp'));
        //dd($resultadoAgrupado);

        return view('relatorios.passes', [
            'passe' => json_decode(json_encode($resultadoAgrupado), true),
            'dt_inicio' => $dt_inicio,
            'dt_fim' => $dt_fim,
            'tratamento' => $tratamento,
            'trata' => $trata,
            'totalGeralAssistidos' => $totalGeralAssistidos, // Adicionando o total geral
        ]);
    }
    public function AtendimentosGeral(Request $request)
    {
        $now = Carbon::now()->format('Y-m-d');
        $dt_inicio = $request->dt_inicio == null ? Carbon::now()->subMonth()->firstOfMonth() : Carbon::parse($request->dt_inicio);
        $dt_fim = $request->dt_fim == null ? Carbon::today() : Carbon::parse($request->dt_fim);
        $multiplicador = ceil(($dt_fim->diffInDays($dt_inicio) + 1) / 7);

        // Presenças, Faltas,Total, Max_vagas

        // Mês, ano, normal

        $tipo_tratamento = $request->tipo_tratamento;
        $presencas = DB::table('presenca_cronograma as pc')
            ->leftJoin('dias_cronograma as dc', 'pc.id_dias_cronograma', 'dc.id')
            ->leftJoin('tratamento as tr', 'pc.id_tratamento', 'tr.id')
            ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
            ->where('dc.data', '>=', $dt_inicio)
            ->where('dc.data', '<=', $dt_fim)
            ->when($request->tipo_tratamento and $request->tipo_tratamento != 5, function ($query) use ($tipo_tratamento) {
                $query->where('enc.id_tipo_tratamento', $tipo_tratamento);
            });

        $maxAtend = DB::table('cronograma')
            ->where(function ($query) use ($dt_fim) {
                $query->where('data_fim', '>=', $dt_fim);
                $query->orWhereNull('data_fim');
            })
            ->whereIn('id_tipo_tratamento', [1, 2, 4, 6])
            ->when($request->tipo_tratamento and $request->tipo_tratamento != 5, function ($query) use ($tipo_tratamento) {
                $query->where('id_tipo_tratamento', $tipo_tratamento);
            });


        if ($request->tipo_visualizacao == 2) {

            Carbon::setlocale(config('app.locale'));
            $meses = CarbonPeriod::create($dt_inicio, $dt_fim)->month()->toArray();

            foreach ($meses as $mes) {

                $dadosChart[ucfirst($mes->locale('pt-br')->translatedFormat('F')) . ' - ' . $mes->format('Y')] = [
                    'Presenças' => (clone $presencas)->whereMonth('dc.data', $mes->month)->whereYear('dc.data', $mes->year)->where('pc.presenca', true)->count(),
                    'Faltas' => (clone $presencas)->whereMonth('dc.data', $mes->month)->whereYear('dc.data', $mes->year)->where('pc.presenca', false)->count(),
                    'Vagas Disponibilizadas' => (clone $presencas)->whereMonth('dc.data', $mes->month)->whereYear('dc.data', $mes->year)->count(),
                    'Capacidade Máxima' => ($maxAtend->sum('max_atend') * round($multiplicador / count($meses))),
                ];
            }
        } elseif ($request->tipo_visualizacao == 3) {


            $meses = collect(range($dt_inicio->year, $dt_fim->year))
                ->map(fn($ano) => Carbon::create($ano, 1, 1))
                ->toArray();

            foreach ($meses as $mes) {

                $dadosChart[$mes->format('Y')] = [
                    'Presenças' => (clone $presencas)->whereYear('dc.data', $mes->year)->where('pc.presenca', true)->count(),
                    'Faltas' => (clone $presencas)->whereYear('dc.data', $mes->year)->where('pc.presenca', false)->count(),
                    'Vagas Disponibilizadas' => (clone $presencas)->whereYear('dc.data', $mes->year)->count(),
                    'Capacidade Máxima' => ($maxAtend->sum('max_atend') * round($multiplicador / count($meses))),
                ];
            }
        } else {
            $dadosChart = [
                'Presenças' => (clone $presencas)->where('pc.presenca', true)->count(),
                'Faltas' => (clone $presencas)->where('pc.presenca', false)->count(),
                'Vagas Disponibilizadas' => (clone $presencas)->count(),
                'Capacidade Máxima' => ($maxAtend->sum('max_atend') * $multiplicador),
            ];
        }


        return view('relatorios.relatorio-geral-atendimento', compact('dt_inicio', 'dt_fim', 'dadosChart'));
    }

    public function AtendimentosGeral2(Request $request)
    {
        $now = Carbon::now()->format('Y-m-d');
        $ano = $request->input('ano', date('Y'));

        $dt_inicio = date("{$ano}-01-01 00:00:00");
        $dt_fim = date("{$ano}-12-31 23:59:59");


        // Presenças, Faltas, Total, Alta, Transferido, Desistência

        // Mês, ano, normal

        $tipo_tratamento = $request->tipo_tratamento;
        $presencas = DB::table('presenca_cronograma as pc')
            ->leftJoin('dias_cronograma as dc', 'pc.id_dias_cronograma', 'dc.id')
            ->leftJoin('tratamento as tr', 'pc.id_tratamento', 'tr.id')
            ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
            ->where('dc.data', '>=', $dt_inicio)
            ->where('dc.data', '<=', $dt_fim)
            ->whereNot('enc.id_tipo_tratamento', 3)
            ->when($request->tipo_tratamento and !in_array(3, $request->tipo_tratamento) and !in_array(5, $request->tipo_tratamento), function ($query) use ($tipo_tratamento) {
                $query->whereIn('enc.id_tipo_tratamento', $tipo_tratamento);
            });

        $harmonizacao = DB::table('dias_cronograma as dc')
        ->leftJoin('cronograma as cro', 'dc.id_cronograma', 'cro.id')
            ->where('dc.data', '>=', $dt_inicio)
            ->where('dc.data', '<=', $dt_fim)
            ->where('cro.id_tipo_tratamento', 3);

        $alta = DB::table('tratamento as tr')
            ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
            ->where('tr.dt_fim', '>=', $dt_inicio)
            ->where('tr.dt_fim', '<=', $dt_fim)
            ->when($request->tipo_tratamento and !in_array(5, $request->tipo_tratamento), function ($query) use ($tipo_tratamento) {
                $query->whereIn('enc.id_tipo_tratamento', $tipo_tratamento);
            });


        $transferidos = DB::table('tratamento_grupos as tg')
            ->leftJoin('tratamento as tr', 'tg.id_tratamento', '=', 'tr.id')
            ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
            ->where('tg.dt_fim', '>=', $dt_inicio)
            ->where('tg.dt_fim', '<=', $dt_fim)
            ->when($request->tipo_tratamento and !in_array(5, $request->tipo_tratamento), function ($query) use ($tipo_tratamento) {
                $query->whereIn('enc.id_tipo_tratamento', $tipo_tratamento);
            });

        $tratamentos = DB::table('tratamento as tr')
            ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
            ->where('tr.dt_inicio', '>=', $dt_inicio)
            ->where('tr.dt_inicio', '<=', $dt_fim)
            ->when($request->tipo_tratamento and !in_array(5, $request->tipo_tratamento), function ($query) use ($tipo_tratamento) {
                $query->whereIn('enc.id_tipo_tratamento', $tipo_tratamento);
            });

        $maxAtend = DB::table('cronograma')
            ->where(function ($query) use ($dt_fim) {
                $query->where('data_fim', '>=', $dt_fim);
                $query->orWhereNull('data_fim');
            })
            ->when($request->tipo_tratamento and !in_array(5, $request->tipo_tratamento), function ($query) use ($tipo_tratamento) {
                $query->whereIn('id_tipo_tratamento', $tipo_tratamento);
            }, function ($query) {
                $query->whereIn('id_tipo_tratamento', [1, 2, 3, 6]);
            })
            ->select(DB::raw('SUM(max_atend) as max_atend'), DB::raw('SUM(max_trab) as max_trab'))->first();
        Carbon::setlocale(config('app.locale'));
        $meses = CarbonPeriod::create($dt_inicio, $dt_fim)->month()->toArray();



        foreach ($meses as $mes) {

            $pre = (clone $presencas)->whereMonth('dc.data', $mes->month)->whereYear('dc.data', $mes->year)->where('pc.presenca', true)->count();
            $aus =  (clone $presencas)->whereMonth('dc.data', $mes->month)->whereYear('dc.data', $mes->year)->where('pc.presenca', false)->count();
            $trat = (clone $tratamentos)->whereMonth('tr.dt_inicio', $mes->month)->whereYear('tr.dt_inicio', $mes->year)->count();

            $dadosFreq[ucfirst($mes->locale('pt-br')->translatedFormat('F'))] = [
                'Total' => (clone $presencas)->whereMonth('dc.data', $mes->month)->whereYear('dc.data', $mes->year)->count(),
                'Harmonização' => (clone $harmonizacao)->whereMonth('dc.data', $mes->month)->whereYear('dc.data', $mes->year)->sum('dc.nr_acompanhantes'),
                'Presenças' => $pre,
                'PCT Presenças' => $pre ? round(($pre * 100) / ($pre + $aus), 2)  : 0,
                'Ausentes' => $aus,
                'PCT Ausentes' => $aus ? round(($aus * 100) / ($pre + $aus), 2)  : 0,
            ];
            $dadosTrat[ucfirst($mes->locale('pt-br')->translatedFormat('F'))] = [
                'Tratamentos' => $trat,
                'Alta' => (clone $alta)->whereMonth('dt_fim', $mes->month)->whereYear('dt_fim', $mes->year)->where('status', 4)->count(),
                'PCT Alta' => $trat ? round(((clone $alta)->whereMonth('dt_fim', $mes->month)->whereYear('dt_fim', $mes->year)->where('status', 4)->count() * 100) / $trat, 2)  : 0,
                'Transferidos' => (clone $transferidos)->whereMonth('tg.dt_fim', $mes->month)->whereYear('tg.dt_fim', $mes->year)->count(),
                'PCT Transferidos' => $trat ? round(((clone $transferidos)->whereMonth('tg.dt_fim', $mes->month)->whereYear('tg.dt_fim', $mes->year)->count() * 100) / $trat, 2)  : 0,
                'Desistência' => (clone $alta)->whereMonth('tr.dt_fim', $mes->month)->whereYear('tr.dt_fim', $mes->year)->where('status', 5)->count(),
                'PCT Desistência' => $trat ? round(((clone $alta)->whereMonth('tr.dt_fim', $mes->month)->whereYear('tr.dt_fim', $mes->year)->where('status', 5)->count() * 100) / $trat, 2)  : 0,
            ];
        }
        return view('relatorios.relatorio-geral-atendimento2', compact('dt_inicio', 'dt_fim', 'dadosFreq', 'dadosTrat', 'maxAtend'));
    }
}
