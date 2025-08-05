<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use PhpParser\Node\Expr\Cast\String_;

use function Laravel\Prompts\select;

class MembroController extends Controller
{
    public function grupos(Request $request)
    {


        $now = Carbon::now()->format('Y-m-d');

        // Proteção por setor e perfil

        // Devolve todos os Perfis e Setores dessa tela para o Usuário Logado
        $acessos = DB::table('usuario_acesso')->where('id_usuario', session()->get('usuario.id_usuario'))->where('id_acesso', session()->get('acessoAtual'))->get()->toArray();

        // Gera um array organizado por perfis e seus respectivos setores
        $arraySetores = array();
        foreach ($acessos as $element) {
            $arraySetores[$element->id_perfil][] = $element->id_setor;
        }

        // Cria um array de armazenagem para os IDs
        $cronogramasLogin = array();
        foreach ($arraySetores as $perfil => $setores) {
            // Checka se o perfil utilizado tem master admin
            $master = DB::table('usuario_acesso')->where('id_usuario', session()->get('usuario.id_usuario'))->where('id_perfil', $perfil)->pluck('id_acesso')->toArray();


            // Caso não tenha Master admin, checka ID função
            if (!in_array(36, $master)) {
                $cronogramasAcesso = DB::table('membro AS m')
                    ->leftJoin('associado', 'associado.id', '=', 'm.id_associado')
                    ->join('pessoas AS p', 'associado.id_pessoa', '=', 'p.id')
                    ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')
                    ->leftJoin('cronograma as cro', 'm.id_cronograma', 'cro.id')
                    ->leftJoin('grupo AS g', 'cro.id_grupo', '=', 'g.id')
                    ->where('id_associado', session()->get('usuario.id_associado'))
                    ->whereIn('m.id_funcao', [1, 2])
                    ->whereIn('g.id_setor', $setores)
                    ->distinct('id_cronograma')
                    ->pluck('id_cronograma')
                    ->toArray();

                $cronogramasLogin = array_merge($cronogramasLogin, $cronogramasAcesso);

                // Caso seja master admin, só checka os setores
            } else {

                $cronogramasAcesso = DB::table('membro AS m')
                    ->leftJoin('associado', 'associado.id', '=', 'm.id_associado')
                    ->join('pessoas AS p', 'associado.id_pessoa', '=', 'p.id')
                    ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')
                    ->leftJoin('cronograma as cro', 'm.id_cronograma', 'cro.id')
                    ->leftJoin('grupo AS g', 'cro.id_grupo', '=', 'g.id')
                    ->whereIn('g.id_setor', $setores)
                    ->distinct('id_cronograma')
                    ->pluck('id_cronograma')
                    ->toArray();

                $cronogramasLogin = array_merge($cronogramasLogin, $cronogramasAcesso);
            }
        }

        $cronogramas = $cronogramasLogin;
        //dd($cronogramas, session()->get('usuario.id_associado'));

        if ($request->nome_membro) {
            $cronogramasPesquisa = DB::table('membro AS m')
                ->leftJoin('associado', 'associado.id', '=', 'm.id_associado')
                ->join('pessoas AS p', 'associado.id_pessoa', '=', 'p.id')
                ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')
                ->leftJoin('grupo AS g', 'm.id_cronograma', '=', 'g.id')
                ->where('id_associado', $request->nome_membro)
                ->whereNull('m.dt_fim')
                ->pluck('m.id_cronograma');

            $cronogramasPesquisa = json_decode(json_encode($cronogramasPesquisa), true);
            $cronogramas = array_intersect($cronogramasLogin, $cronogramasPesquisa);
        }



        $membro_cronograma = DB::table('cronograma as cro')

            ->select(
                'cro.id',
                'gr.nome as nome_grupo',
                'td.nome as dia',
                'cro.h_inicio',
                'cro.h_fim',
                'sl.numero as sala',
                'tpg.descricao',
                's.nome as nome_setor',
                's.sigla',
                DB::raw("
            (CASE
             WHEN cro.modificador = 3  THEN 'Experimental'
             WHEN cro.modificador = 4   THEN 'Em Férias'
             WHEN cro.data_fim < '$now' THEN 'Inativo'
             ELSE 'Ativo' END)
             as status"),
            )
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('setor as s', 'gr.id_setor', 's.id')
            ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
            ->leftJoin('salas as sl', 'cro.id_sala', 'sl.id')
            ->leftJoin('tipo_status_grupo as tpg', 'cro.modificador', 'tpg.id')
            ->whereIn('cro.id', $cronogramas);

        $membro = DB::table('membro AS m')
            ->leftJoin('associado', 'associado.id', 'm.id_associado')
            ->join('pessoas AS p', 'associado.id_pessoa', 'p.id')
            ->leftJoin('tipo_funcao AS tf', 'm.id_funcao',  'tf.id')
            ->leftJoin('cronograma as cro', 'm.id_cronograma', 'cro.id')
            ->leftJoin('grupo AS g', 'cro.id_grupo', 'g.id')
            ->select('p.nome_completo', 'm.id_associado', 'associado.nr_associado')
            ->whereIn('m.id_cronograma', $cronogramasLogin)
            ->distinct()
            ->get();

        if ($request->nome_grupo) {
            $membro_cronograma = $membro_cronograma->where('cro.id', $request->nome_grupo);
        }

        $membro_cronograma = $membro_cronograma->orderBy('status')
            ->orderBy('nome_grupo')
            ->paginate(50);


        $nome = $request->nome_grupo;
        $membroPesquisa = $request->nome_membro;
        $contar = $membro_cronograma->total();
        $grupos2 = DB::table('grupo AS g')
            ->leftJoin('setor AS s', 'g.id_setor', 's.id')
            ->leftJoin('cronograma as cro', 'g.id', '=', 'cro.id_grupo')
            ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
            ->leftJoin('salas as sl', 'cro.id_sala', 'sl.id')
            ->leftJoin('tipo_status_grupo AS ts', 'g.status_grupo', 'ts.id')
            ->select(
                'cro.id AS idg',
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
            ->whereIn('cro.id', $cronogramas)
            ->get();


        return view('membro.listar-grupos-membro', compact('grupos2', 'membro_cronograma', 'contar', 'nome', 'membro', 'membroPesquisa'));
    }

    public function createGrupo(Request $request, string $id)
    {
        try {

            $grupo = DB::table('cronograma as cro')
                ->select('cro.id', 'gr.nome', 'cro.h_inicio', 'cro.h_fim', 'sa.numero', 'td.nome as dia', 's.sigla as nsigla')
                ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
                ->leftJoin('salas as sa', 'cro.id_sala', 'sa.id')
                ->leftJoin('setor as s', 'gr.id_setor', 's.id')
                ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
                ->where('cro.id', $id)
                ->first();


            $membro = DB::select('select * from membro');
            $pessoas = DB::select('select id , nome_completo, motivo_status, status from pessoas order by nome_completo asc');
            $tipo_funcao = DB::select('select id as idf, tipo_funcao, nome, sigla from tipo_funcao order by nome asc');
            $tipo_status_pessoa = DB::select('select id,tipo as tipos from tipo_status_pessoa');
            $associado = DB::table('associado')->leftJoin('pessoas', 'pessoas.id', '=', 'associado.id_pessoa', 'associado.nr_associado')->select('pessoas.nome_completo', 'associado.id', 'associado.nr_associado')->orderBy('pessoas.nome_completo', 'asc')->get();

            return view('membro/criar-membro-grupo', compact('associado', 'tipo_status_pessoa', 'grupo', 'membro', 'pessoas', 'tipo_funcao', 'id'));
        } catch (\Exception $e) {
            $code = $e->getCode();
            return view('administrativo-erro.erro-inesperado', compact('code'));
        }
    }

    public function storeGrupo(Request $request, string $id)
    {


        $now = Carbon::now()->format('Y-m-d');
        $seletedCronograma = DB::table('cronograma as cro')->where('id', $id)->first();
        $cronogramasPessoa = DB::table('membro')->whereNull('dt_fim')->where('id_associado', $request->input('id_associado'))->pluck('id_cronograma');
        $data =  $request->input('dt_inicio') ?  $request->input('dt_inicio') : $now;

        $repeat = DB::table('cronograma AS rm')
            ->leftJoin('grupo AS g', 'rm.id_grupo', 'g.id')
            ->where('rm.dia_semana', $seletedCronograma->dia_semana)
            ->whereIn('rm.id', $cronogramasPessoa)
            ->whereNot('rm.id', $seletedCronograma->id)
            ->where(function ($query) use ($now) {
                $query->where('rm.data_fim', '>', $now);
                $query->orWhere('rm.data_fim', null);
            })
            ->where(function ($query) use ($seletedCronograma) {
                $query->where(function ($hour) use ($seletedCronograma) {
                    $hour->where('rm.h_inicio', '<', $seletedCronograma->h_inicio);
                    $hour->where('rm.h_fim', '>', $seletedCronograma->h_inicio);
                });
                $query->orWhere(function ($hour) use ($seletedCronograma) {
                    $hour->where('rm.h_inicio', '<', $seletedCronograma->h_fim);
                    $hour->where('rm.h_fim', '>', $seletedCronograma->h_fim);
                });
            })
            ->first();

        if ($repeat != null) {
            app('flasher')->addError("Este membro faz parte de $repeat->nome neste horário");
            return redirect()->back()->withInput();
        }
        $repetfuncao = DB::table('membro AS m')
            ->join('cronograma AS c', 'm.id_cronograma', '=', 'c.id')
            ->whereNull('m.dt_fim')
            ->where('m.id_associado', $request->input('id_associado'))
            ->where('m.id_funcao', $request->input('id_funcao'))
            ->where('c.id', $id)
            ->exists();

        // Se o membro já estiver registrado na mesma função e grupo, bloquear o cadastro
        if ($repetfuncao) {
            app('flasher')->addError('Este membro já está cadastrado nesta função para o mesmo grupo.');
            return redirect()->back()->withInput();
        }


        $data = date('Y-m-d H:i:s');
        DB::table('membro')->insert([
            'id_associado' => $request->input('id_associado'),
            'id_funcao' => $request->input('id_funcao'),
            'id_cronograma' => $id,
            'dt_inicio' => $data,
        ]);

        $nomePessoa = DB::table('pessoas')
            ->where('id', session()->get('usuario.id_pessoa'))
            ->value('nome_completo');

        DB::table('historico_venus')->insert([
            'id_usuario' => session()->get('usuario.id_usuario'),
            'data' => $data,
            'pessoa' => $nomePessoa,
            'obs' => 'Incluiu Membro dentro do Grupo',
            'id_ref' => $id,
            'fato' => 30,
        ]);

        app('flasher')->addSuccess('Cadastrado com Sucesso');
        return redirect("gerenciar-membro/$id");
    }

    public function index(Request $request, string $id)
    {

        // Busca os detalhes do grupo
        $grupo = DB::table('cronograma as cro')
            ->select('cro.id', 'gr.id as idg', 'gr.nome', 'cro.h_inicio', 'cro.h_fim', 'sa.numero', 'td.nome as dia', 'cro.modificador', 's.sigla as nsigla')
            ->leftJoin('salas as sa', 'cro.id_sala', 'sa.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('setor as s', 'gr.id_setor', 's.id')
            ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
            ->where('cro.id', $id)
            ->first();

        // Montagem da query para membros
        $membroQuery = DB::table('membro AS m')
            ->leftJoin('associado', 'associado.id', '=', 'm.id_associado')
            ->join('pessoas AS p', 'associado.id_pessoa', '=', 'p.id')
            ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')
            ->leftJoin('cronograma AS cro', 'm.id_cronograma', '=', 'cro.id')
            ->leftJoin('grupo AS g', 'cro.id_grupo', '=', 'g.id')
            ->where('m.id_cronograma', $id)
            ->select(
                'associado.id as ida',
                'associado.nr_associado',
                'p.nome_completo',
                'm.id AS idm',
                'm.id_associado',
                'm.id_funcao',
                'm.id_cronograma',
                'p.cpf',
                'p.motivo_status',
                'tf.nome as nome_funcao',
                'm.id_cronograma',
                'g.nome as nome_grupo',
                DB::raw("(CASE WHEN m.dt_fim > '1969-06-12' THEN 'Inativo' ELSE 'Ativo' END) as status")
            )
            ->orderBy('status')
            ->orderBy('id_funcao')
            ->orderBy('p.nome_completo', 'ASC');


        // Filtros
        $nome = $request->nome_pesquisa;
        $status = $request->status ?? null; // Define "Ativo" como valor padrão se não for informado
        $cpf = $request->cpf_pesquisa;
        $grupoPesquisa = $request->grupo_pesquisa;

        // Array de status
        $statu = [
            (object) ['nome' => 'Ativo'],
            (object) ['nome' => 'Inativo'],
            (object) ['nome' => 'Todos']
        ];


        // Carregar lista de grupos
        $grupos = DB::table('grupo')->pluck('nome', 'id');

        // Aplicação dos filtros
        if ($nome || $cpf || $grupoPesquisa) {
            $membroQuery->where(function ($query) use ($nome, $cpf, $grupoPesquisa) {
                if ($nome) {
                    $query->where(DB::raw('unaccent(lower(p.nome_completo))'), 'ilike', DB::raw("unaccent(lower('%{$nome}%'))"))
                        ->orWhere('p.cpf', 'ilike', "%$nome%");
                }

                if ($cpf) {
                    $query->orWhere('p.cpf', 'ilike', "%$cpf%");
                }

                if ($grupoPesquisa) {
                    $query->orWhere('g.id', '=', $grupoPesquisa);
                }
            });
        }

        // Filtro de status
        if ($status && $status != 'Todos') {
            $membroQuery->where(DB::raw("(CASE WHEN m.dt_fim > '1969-06-12' THEN 'Inativo' ELSE 'Ativo' END)"), '=', $status);
        } else if ($status == 'Todos') {
        } else {
            $membroQuery->where('m.dt_fim', NULL);
        }

        // Paginação dos resultados
        $membro = $membroQuery->orderBy('status', 'asc')->orderBy('p.nome_completo', 'asc')->paginate(50);

        // Contagem total de membros sem considerar dt_fim
        $contar = $membroQuery->whereNull('m.dt_fim')->count(); // Co


        /* VALIDAÇÃO DE OUTROS GRUPOS */

        // Recupera todos os cronogramas que o usuario logado é dirigente ou subdirigente
        $id_cronogramas_usuario = DB::table('membro')
            ->where('id_associado', session()->get('usuario.id_associado'))
            ->whereIn('id_funcao', [1, 2])
            ->whereNull('dt_fim')
            ->pluck('id_cronograma')
            ->toArray();

        // Retorna todos os membros nesse mesmo grupo que o usuário logado é dirigente
        $id_membros = DB::table('membro as m')
            ->leftJoin('cronograma as cro', 'm.id_cronograma', 'cro.id')
            ->where('cro.id_grupo', $grupo->idg)
            ->whereIn('m.id_cronograma', $id_cronogramas_usuario)
            ->whereNot('m.id_cronograma', $id)
            ->whereNull('m.dt_fim')
            ->pluck('m.id_associado')
            ->toArray();


        // Retorno da view com os dados
        return view('membro.gerenciar-membro', compact('contar', 'membro', 'id', 'grupo', 'status', 'statu', 'grupos', 'id_membros'));
    }

    public function create()
    {



        $grupo = DB::table('cronograma as cro')->select('cro.id', 'gr.nome', 'cro.h_inicio', 'cro.h_fim', 'sa.numero', 'td.nome as dia', 's.sigla as nsigla')
            ->leftJoin('salas as sa', 'cro.id_sala', 'sa.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('setor as s', 'gr.id_setor', 's.id')
            ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')->get();

        $membro = DB::select('select * from membro');
        $pessoas = DB::select('select id , nome_completo, motivo_status, status from pessoas order by nome_completo asc');
        $tipo_funcao = DB::select('select id as idf, tipo_funcao, nome, sigla from tipo_funcao order by nome asc');
        $tipo_status_pessoa = DB::select('select id,tipo as tipos from tipo_status_pessoa');
        $associado = DB::table('associado')->leftJoin('pessoas', 'pessoas.id', '=', 'associado.id_pessoa')->select('pessoas.nome_completo', 'associado.id', 'associado.nr_associado')->orderBy('pessoas.nome_completo', 'asc')->get();



        return view('membro/criar-membro', compact('associado', 'tipo_status_pessoa', 'grupo', 'membro', 'pessoas', 'tipo_funcao'));
    }

    public function store(Request $request)
    {


        $now = Carbon::now()->format('Y-m-d');
        $seletedCronograma = DB::table('cronograma as cro')->where('id', $request->input('id_reuniao'))->first();

        $cronogramasPessoa = DB::table('membro')
            ->where('id_associado', $request->input('id_associado'))
            ->whereNull('dt_fim')
            ->pluck('id_cronograma');

        $repeat = DB::table('cronograma AS rm')
            ->leftJoin('grupo AS g', 'rm.id_grupo', 'g.id')
            ->whereIn('rm.id', $cronogramasPessoa)
            ->where('rm.dia_semana', $seletedCronograma->dia_semana)
            ->whereNot('rm.id', $seletedCronograma->id)
            ->where(function ($query) use ($now) {
                $query->where('rm.data_fim', '>', $now);
                $query->orWhere('rm.data_fim', null);
            })
            ->where(function ($query) use ($seletedCronograma) {
                $query->where(function ($hour) use ($seletedCronograma) {
                    $hour->where('rm.h_inicio', '<', $seletedCronograma->h_inicio);
                    $hour->where('rm.h_fim', '>', $seletedCronograma->h_inicio);
                });
                $query->orWhere(function ($hour) use ($seletedCronograma) {
                    $hour->where('rm.h_inicio', '<', $seletedCronograma->h_fim);
                    $hour->where('rm.h_fim', '>', $seletedCronograma->h_fim);
                });
            })
            ->first();

        if ($repeat != null) {
            app('flasher')->addError("Este membro faz parte de $repeat->nome neste horário");
            return redirect()->back()->withInput();
        }

        $repetfuncao = DB::table('membro AS m')
            ->join('cronograma AS c', 'm.id_cronograma', '=', 'c.id')
            ->where('m.id_associado', $request->input('id_associado'))
            ->where('m.id_funcao', $request->input('id_funcao'))
            ->where('c.id', $request->input('id_reuniao'))
            ->exists();

        // Se o membro já estiver registrado na mesma função e grupo, bloquear o cadastro
        if ($repetfuncao) {
            app('flasher')->addError('Este membro já está cadastrado nesta função para o mesmo grupo.');
            return redirect()->back()->withInput();
        }

        $data = date('Y-m-d H:i:s');
        DB::table('membro')->insert([
            'id_associado' => $request->input('id_associado'),
            'id_funcao' => $request->input('id_funcao'),
            'id_cronograma' => $request->input('id_reuniao'),
            'dt_inicio' => $request->input('dt_inicio'),
        ]);

        $nomePessoa = DB::table('pessoas')
            ->where('id', session()->get('usuario.id_pessoa'))
            ->value('nome_completo');

        DB::table('historico_venus')->insert([
            'id_usuario' => session()->get('usuario.id_usuario'),
            'data' => $data,
            'pessoa' => $nomePessoa,
            'obs' => 'Incluiu Membro',
            'id_ref' => $request->input('id_reuniao'),
            'fato' => 21,
        ]);
        app('flasher')->addSuccess('Cadastrado com Sucesso');
        return redirect('gerenciar-grupos-membro');
    }

    public function edit(string $idcro, string $id)
    {
        try {
            $grupo = DB::table('cronograma as cro')->select('cro.id', 'gr.nome', 'cro.h_inicio', 'cro.h_fim', 'sa.numero', 'td.nome as dia')->leftJoin('salas as sa', 'cro.id_sala', 'sa.id')->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')->get();

            $membro = DB::table('membro AS m')->leftJoin('associado AS a', 'a.id', '=', 'm.id_associado')->leftJoin('pessoas AS p', 'a.id_pessoa', '=', 'p.id')->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')->leftJoin('grupo AS g', 'm.id_cronograma', '=', 'g.id')->leftJoin('pessoas', 'p.id', '=', 'a.id_pessoa')->select('p.nome_completo', 'm.id AS idm', 'm.id_associado', 'm.id_funcao', 'p.cpf', 'p.status', 'p.motivo_status', 'tf.nome as nome_funcao', 'm.id_cronograma', 'g.nome as nome_grupo')->where('m.id', $id)->first();

            $tipo_status_pessoa = DB::table('tipo_status_pessoa')->select('id', 'tipo as tipos')->get();
            $tipo_motivo_status_pessoa = DB::table('tipo_motivo_status_pessoa')->select('id', 'motivo')->get();
            $pessoas = DB::table('pessoas')->get();
            $tipo_funcao = DB::table('tipo_funcao')->get();
            $associado = DB::table('associado')->leftJoin('pessoas', 'pessoas.id', '=', 'associado.id_pessoa')->select('associado.id', 'pessoas.nome_completo', 'associado.nr_associado')->orderBy('pessoas.nome_completo', 'asc')->get();

            return view('membro.editar-membro', compact('associado', 'membro', 'tipo_status_pessoa', 'tipo_motivo_status_pessoa', 'grupo', 'pessoas', 'tipo_funcao', 'idcro'));
        } catch (\Exception $e) {
            $code = $e->getCode();
            return view('administrativo-erro.erro-inesperado', compact('code'));
        }
    }

    public function update(Request $request, string $idcro, string $id)
    {
        try {
            DB::table('membro')
                ->where('id', $id)
                ->update([
                    'id_funcao' => $request->input('id_funcao'),
                    'id_cronograma' => $idcro,
                ]);

            app('flasher')->addSuccess('Alterado com Sucesso');

            return redirect("gerenciar-membro/$idcro");
        } catch (\Exception $e) {
            $code = $e->getCode();
            return view('administrativo-erro.erro-inesperado', compact('code'));
        }
    }

    public function show(String $id, Request $request)
    {
        // Obter o registro do membro com base no ID
        $membro = DB::table('membro AS m')
            ->select(
                'm.id',
                'm.id_cronograma',
                'p.nome_completo',
                'a.nr_associado',
                'p.dt_nascimento',
                'p.ddd',
                'p.celular',
                'tf.nome as nome_funcao',
                'd.descricao',
                'm.dt_inicio',
                'm.dt_fim',
                'tp.tipo'
            )
            ->leftJoin('associado as a', 'm.id_associado', 'a.id')
            ->join('pessoas AS p', 'a.id_pessoa', '=', 'p.id')
            ->leftJoin('tipo_status_pessoa as tp', 'p.status', 'tp.id')
            ->leftJoin('tp_ddd as d', 'p.ddd', '=', 'd.id')
            ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')
            ->where('m.id', $id)
            ->first();

        // Usar o id_associado para buscar todas as presenças e informações relacionadas
        $presencas = DB::table('presenca_membros as pm')
            ->select('dc.data', 'pm.presenca', 'g.nome')
            ->leftJoin('membro as m', 'pm.id_membro', 'm.id')
            ->leftJoin('associado as a', 'm.id_associado', 'a.id')
            ->leftJoin('dias_cronograma as dc', 'pm.id_dias_cronograma', 'dc.id')
            ->leftJoin('cronograma as cro', 'dc.id_cronograma', 'cro.id')
            ->leftJoin('grupo as g', 'cro.id_grupo', 'g.id')
            ->where('m.id', $membro->id) // Filtro baseado no associado
            ->orderBy('dc.data', 'desc')
            ->get();

        // Agrupar presenças por ano
        $arrayPresencas = [];
        foreach ($presencas as $presenca) {
            $arrayPresencas[date('Y', strtotime($presenca->data))][] = $presenca;
        }

        $presencas = $arrayPresencas;

        // Retornar a view com os dados
        return view('membro.visualizar-membro', compact('membro', 'presencas'));
    }

    public function faltas(String $id, Request $request)
    {

        $membro = DB::table('membro AS m')
            ->select(
                'm.id',
                'm.id_cronograma',
                'p.nome_completo',
                'a.nr_associado',
                'p.dt_nascimento',
                'p.ddd',
                'p.celular',
                'tf.nome as nome_funcao',
                'd.descricao',
                'm.dt_inicio',
                'm.dt_fim',
                'tp.tipo',
            )
            ->leftJoin('associado as a', 'm.id_associado',  'a.id')
            ->join('pessoas AS p', 'a.id_pessoa', '=', 'p.id')
            ->leftJoin('tipo_status_pessoa as tp', 'p.status', 'tp.id')
            ->leftJoin('tp_ddd as d', 'p.ddd', '=', 'd.id')
            ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')
            ->where('m.id', $id)
            ->first();

        $presencas = DB::table('presenca_membros as pm')
            ->select('dc.data', 'pm.presenca', 'g.nome', 'pm.id')
            ->leftJoin('membro as m', 'pm.id_membro', 'm.id')
            ->leftJoin('associado as a', 'm.id_associado', 'a.id')
            ->leftJoin('dias_cronograma as dc', 'pm.id_dias_cronograma', 'dc.id')
            ->leftJoin('cronograma as cro', 'dc.id_cronograma', 'cro.id')
            ->leftJoin('grupo as g', 'cro.id_grupo', 'g.id')
            ->where('m.id', $id)
            ->orderBy('dc.data', 'desc')
            ->get();

        $arrayPresencas = [];
        foreach ($presencas as $presenca) {

            $arrayPresencas[date('Y', strtotime($presenca->data))][] = $presenca;
        }

        $presencas = $arrayPresencas;




        return view('membro.reverter-faltas-membro', compact('membro', 'presencas'));
    }

    public function remarcar(Request $request, String $id)
    {
        $data_atual = Carbon::now();
        if ($request->checkbox) {
            foreach ($request->checkbox as $key => $presenca) {

                $booleanPresenca = $presenca ?? false;

                DB::table('presenca_membros')
                    ->where('id', $key)
                    ->update([
                        'presenca' => !$booleanPresenca
                    ]);

                $nomePessoa = DB::table('pessoas')
                    ->where('id', session()->get('usuario.id_pessoa'))
                    ->value('nome_completo');

                // Realiza a inserção na tabela 'historico_venus'
                DB::table('historico_venus')->insert([
                    'id_usuario' => session()->get('usuario.id_usuario'),
                    'data' => $data_atual,
                    'fato' => 26,
                    'obs' => 'alterou a presença/falta do membro',
                    'pessoa' => $nomePessoa,
                    'id_ref' => $key,
                ]);
            }
            app('flasher')->addSuccess('Presença alterada com sucesso.');
        } else {
            app('flasher')->addError('Nenhum item selecionada.');
        }


        return redirect("/gerenciar-membro/$id");
    }

    public function destroy(string $idcro, string $id)
    {
        $data = date('Y-m-d H:i:s');

        // Obtém o nome do usuário da tabela 'pessoas'
        $nomeUsuario = DB::table('pessoas')
            ->where('id', session()->get('usuario.id_usuario'))
            ->value('nome_completo'); //

        // Insere o histórico
        DB::table('historico_venus')->insert([
            'id_usuario' => session()->get('usuario.id_usuario'),
            'data' => $data,
            'fato' => 7, //
            'obs' => 'Membro deletado',
            'pessoa' => $nomeUsuario
        ]);

        // Verifica se o membro existe
        $membro = DB::table('membro')->where('id', $id)->first();

        if (!$membro) {
            app('flasher')->addError('O membro não foi encontrado.');
            return redirect("/gerenciar-membro/$idcro");
        }

        // Exclui as presenças do membro antes de deletá-lo
        DB::table('presenca_membros')->where('id_membro', $id)->delete();

        // Deleta o membro
        DB::table('membro')->where('id', $id)->delete();

        app('flasher')->addSuccess('Membro deletado com sucesso.');


        return redirect("/gerenciar-membro/$idcro");
    }



    public function inactivate(string $idcro, string $id, Request $request)
    {
        $data = date('Y-m-d H:i:s');

        // Obtém o nome do usuário da tabela 'pessoas'
        $nomeUsuario = DB::table('pessoas')
            ->where('id', session()->get('usuario.id_usuario'))
            ->value('nome_completo'); //

        // Insere o histórico
        DB::table('historico_venus')->insert([
            'id_usuario' => session()->get('usuario.id_usuario'),
            'data' => $data,
            'fato' => 6,
            'obs' => 'Membro inativado',
            'pessoa' => $nomeUsuario
        ]);



        // Verifica se o membro existe
        $membro = DB::table('membro as m')->select('m.id_associado', 'cro.id_grupo as idg')->where('m.id', $id)->leftJoin('cronograma as cro', 'm.id_cronograma', 'cro.id')->first();

        // Busca os cronogramas que eu sou dirigente ou sub-dirigente
        $id_cronogramas_usuario = DB::table('membro')
            ->where('id_associado', session()->get('usuario.id_associado'))
            ->whereIn('id_funcao', [1, 2])
            ->whereNull('dt_fim')
            ->pluck('id_cronograma')
            ->toArray();

        if (!$membro) {
            app('flasher')->addError('O membro não foi encontrado.');
            return redirect("/gerenciar-membro/$idcro");
        }

        // Obtenha a data de inativação do request
        $dataInativacao = $request->input('data_inativacao');

        // Se a data de inativação não for fornecida, use a data atual como fallback
        if (empty($dataInativacao)) {
            $dataInativacao = Carbon::today()->toDateString(); // Formato Y-m-d
        }

        if ($request->escolha == 1) {
            // Retorna todos os membros nesse mesmo grupo que o usuário logado é dirigente
            DB::table('membro as m')
                ->leftJoin('cronograma as cro', 'm.id_cronograma', 'cro.id')
                ->where('m.id_associado', $membro->id_associado)
                ->whereNull('m.dt_fim')
                ->whereIn('m.id_cronograma', $id_cronogramas_usuario)
                ->where('cro.id_grupo', $membro->idg)
                ->update([
                    'dt_fim' => $dataInativacao,
                ]);
        } else {
            // Atualiza a data de término e o status para "Inativo"
            DB::table('membro')
                ->where('id', $id)
                ->update([
                    'dt_fim' => $dataInativacao,
                ]);
        }

        app('flasher')->addSuccess('Membro inativado com sucesso.');
        return redirect("/gerenciar-membro/$idcro");
    }




    public function ferias(string $id, string $tp)
    {
        try {
            $now = Carbon::now()->format('Y-m-d');
            $tratamentosPTI = DB::table('tratamento as tr')->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')->where('id_reuniao', $id)->where('tr.status', '<', 3)->get();

            if ($tp == 1) {
                DB::table('cronograma')
                    ->where('id', $id)
                    ->update([
                        'modificador' => 4,
                    ]);

                foreach ($tratamentosPTI as $tratamento) {
                    if ($tratamento->id_tipo_tratamento == 2) {
                        DB::table('encaminhamento AS enc')->insert([
                            'dh_enc' => $now,
                            'id_usuario' => session()->get('usuario.id_pessoa'),
                            'id_tipo_encaminhamento' => 2,
                            'id_atendimento' => $tratamento->id_atendimento,
                            'id_tipo_tratamento' => 1,
                            'status_encaminhamento' => 2,
                        ]);
                    }
                }
            } elseif ($tp == 2) {
                DB::table('cronograma')
                    ->where('id', $id)
                    ->update([
                        'modificador' => null,
                    ]);

                foreach ($tratamentosPTI as $tratamento) {
                    if ($tratamento->id_tipo_tratamento == 2) {
                        DB::table('encaminhamento AS enc')
                            ->where('enc.id_atendimento', $tratamento->id_atendimento)
                            ->update([
                                'status_encaminhamento' => 5,
                            ]);
                    }
                }
            }

            $nomePessoa = DB::table('pessoas')
                ->where('id', session()->get('usuario.id_usuario'))
                ->value('nome_completo');

            // Realiza a inserção na tabela 'historico_venus'
            DB::table('historico_venus')->insert([
                'id_usuario' => session()->get('usuario.id_usuario'),
                'data' => $now,
                'fato' => 29,
                'obs' => 'Declarou férias',
                'pessoa' => $nomePessoa,
                'id_ref' => $id,
            ]);
            return redirect()->back();
        } catch (\Exception $e) {
            $code = $e->getCode();
            return view('administrativo-erro.erro-inesperado', compact('code'));
        }
    }

    public function selecionar(String $id)
    {
        $membros = DB::table('membro as m')
            ->select('m.id', 'ass.nr_associado', 'p.nome_completo', 'tf.nome')
            ->leftJoin('associado as ass', 'm.id_associado', 'ass.id')
            ->leftJoin('pessoas as p', 'ass.id_pessoa', 'p.id')
            ->leftJoin('tipo_funcao as tf', 'm.id_funcao', 'tf.id')
            ->where('m.dt_fim', null)
            ->where('m.id_cronograma', $id)
            ->get();


        $grupos = DB::table('grupo AS g')
            ->leftJoin('setor AS s', 'g.id_setor', 's.id')
            ->leftJoin('cronograma as cro', 'g.id', '=', 'cro.id_grupo')
            ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
            ->leftJoin('salas as sl', 'cro.id_sala', 'sl.id')
            ->leftJoin('tipo_status_grupo AS ts', 'g.status_grupo', 'ts.id')
            ->select(
                'cro.id AS idg',
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


        return view('membro.transferir', compact('membros', 'grupos', 'id'));
    }

    public function transferir(Request $request, String $id)
    {

        foreach ($request->check as $membro) {

            $data_atual = Carbon::now();
            $ontem = Carbon::yesterday();
            $hoje = Carbon::today();

            $membroAtual = DB::table('membro as m')
                ->select('ass.id', 'm.id_funcao')
                ->leftJoin('associado as ass', 'm.id_associado', 'ass.id')
                ->leftJoin('pessoas as p', 'ass.id_pessoa', 'p.id')
                ->leftJoin('tipo_funcao as tf', 'm.id_funcao', 'tf.id')
                ->where('m.dt_fim', null)
                ->where('m.id', $membro)
                ->first();
            $nomePessoa = DB::table('pessoas')
                ->where('id', session()->get('usuario.id_usuario'))
                ->value('nome_completo');

            // Realiza a inserção na tabela 'historico_venus'
            DB::table('historico_venus')->insert([
                'id_usuario' => session()->get('usuario.id_usuario'),
                'data' => $data_atual,
                'fato' => 28,
                'obs' => 'Transferiu Membro',
                'pessoa' => $nomePessoa,
                'id_ref' => $membro,
            ]);

            DB::table('membro')
                ->where('id', $membro)
                ->update([
                    'dt_fim' => $ontem
                ]);

            DB::table('membro')->insert([
                'id_associado' => $membroAtual->id,
                'id_cronograma' => $request->nome_grupo,
                'id_funcao' => $membroAtual->id_funcao,
                'dt_inicio' => $hoje
            ]);
        }



        return redirect("/gerenciar-membro/$id");
    }

    public function editLimiteCronograma(String $id)
    {


        $cronograma = DB::table('cronograma as cro')
            ->select(
                'cro.id',
                'gr.nome',
                's.sigla as setor',
                'td.nome as dia',
                'cro.h_inicio',
                'cro.h_fim',
                'sl.numero',
                'cro.max_atend',
                'cro.max_trab'

            )
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('setor as s', 'gr.id_setor', 's.id')
            ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
            ->leftJoin('salas as sl', 'cro.id_sala', 'sl.id')
            ->where('cro.id', $id)
            ->first();

        return view('membro.editar-limite', compact('cronograma'));
    }

    public function updateLimiteCronograma(Request $request, String $id)
    {


        DB::table('cronograma')
        ->where('id', $id)
        ->update([
            'max_atend' => $request->max_atend,
            'max_trab' => $request->max_trab
        ]);

        return redirect("/gerenciar-grupos-membro");;
    }

    public function transferirLote(Request $request) {}
}
