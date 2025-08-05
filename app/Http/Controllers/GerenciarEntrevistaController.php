<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;


class GerenciarEntrevistaController extends Controller
{

    // Função de Paginate que aceita Array
    public function paginate($items, $perPage = 5, $page = null)
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $total = count($items);
        $currentpage = $page;
        $offset = ($currentpage * $perPage) - $perPage;
        $itemstoshow = array_slice($items, $offset, $perPage);
        return new LengthAwarePaginator($itemstoshow, $total, $perPage);
    }

    public function index(Request $request)
    {
        // FIX existe um bug que ocorre quando um PROAMO tem seu PTD encerrado por faltas
        $setores = array(); // Inicializa um Array
        foreach (session()->get('acessoInterno') as $perfil) {

            // Pega todos os dados de acesso e salva os setores em uma varíavel
            $setores = array_merge($setores, array_column($perfil, 'id_setor'));
        }

        // Tras todos os dados para as tebelas e validações IF e ELSE
        $informacoes = DB::table('encaminhamento')
            ->select(
                DB::raw("CASE WHEN entrevistas.status IS NULL THEN 1 ELSE entrevistas.status END as status"), // Caso não tenha nenhum Status, Status 1 (Aguardando Agendamento -> Encaminhamento)
                DB::raw("(CASE WHEN atendimentos.emergencia = true THEN 'Emergência' ELSE 'Normal' END) as emergencia"), // Faz com que um atendimento de emergencia apareça (Não existe uma domínio para traduzir o Boolean)
                'tse.descricao as d1', // Status Entrevista, todos menos Aguardando Agendamento
                'encaminhamento.id as ide', // ID encaminhamento, utilizado na view para o botão Agendar
                'pessoa_pessoa.nome_completo as nome_pessoa', // Nome do Assistido
                'pessoa_entrevistador.nome_completo as nome_entrevistador', // Nome do Entrevistador
                'tipo_entrevista.id as id_tipo_entrevista', // ID Tipo Entrevista, usado como value da pesquisa de Tipo Entrevista
                'tipo_entrevista.sigla as entrevista_sigla', // Tipo Entrevista (Ex.: DIAMO, AFE )
                's.numero', // Traz o suposto número da sala (excessões PREP, CX, AL)
                'encaminhamento.status_encaminhamento as status_encaminhamento_id', // Status do Encaminhamento, usado para validar se está ativa a entrevista
                'atendimentos.dh_inicio as inicio', // DateTime do atendimento
                'entrevistas.id as ident', // ID entrevista, usado na view na tabela
                'pessoa_pessoa.celular',
                'pessoa_pessoa.cpf',
                'pessoa_pessoa.id as id_pessoa',
                'ddd.descricao as ddd',
                'encaminhamento.id_tipo_entrevista',
                'encaminhamento.status_encaminhamento'

            )
            ->leftJoin('atendimentos', 'encaminhamento.id_atendimento', 'atendimentos.id')
            ->leftJoin('entrevistas', 'encaminhamento.id', 'entrevistas.id_encaminhamento')
            ->leftJoin('salas AS s', 'entrevistas.id_sala', 's.id')
            ->leftJoin('pessoas as pessoa_pessoa', 'atendimentos.id_assistido', 'pessoa_pessoa.id')
            ->leftJoin('tp_ddd as ddd', 'pessoa_pessoa.ddd', 'ddd.id')
            ->leftJoin('tipo_entrevista', 'encaminhamento.id_tipo_entrevista', 'tipo_entrevista.id')
            ->leftJoin('tipo_encaminhamento', 'encaminhamento.id_tipo_encaminhamento', 'tipo_encaminhamento.id')
            ->leftJoin('associado', 'entrevistas.id_entrevistador', 'associado.id')
            ->leftJoin('pessoas as pessoa_entrevistador', 'associado.id_pessoa', 'pessoa_entrevistador.id')
            ->leftJoin('tipo_status_entrevista as tse', 'entrevistas.status', 'tse.id')
            ->where('encaminhamento.id_tipo_encaminhamento', 1) // Tipo Entrevista
            ->whereNot('tipo_entrevista.id', 8) // Exclui o tipo de entrevista 8 (Evangelho no Lar)
            ->whereIn('tipo_entrevista.id_setor', $setores);

        $i = 0;
        $pesquisaValue = $request->status == null ? 'limpo' : $request->status; // XXX Se remover o 'limpo' o sistema quebra, por algum motivo

        if ($request->nome_pesquisa) {
            $informacoes->whereRaw("UNACCENT(LOWER(pessoa_pessoa.nome_completo)) ILIKE UNACCENT(LOWER(?))", ["%{$request->nome_pesquisa}%"]);
        }
        if ($request->cpf) {
            $cpfLimpo = preg_replace('/[^0-9]/', '', $request->cpf); // Remove pontos e traços

            $informacoes->whereRaw(
                "REGEXP_REPLACE(pessoa_pessoa.cpf, '[^0-9]', '', 'g') ILIKE ?",
                ["%$cpfLimpo%"]
            );
        }


        if ($request->tipo_entrevista) { // Ex.: DIAMO, NUTRES
            $informacoes->where('tipo_entrevista.id', $request->tipo_entrevista);
        }

        // Caso não seja Aguardando Atendimento, ou Inativado, faz uma pesquisa comum
        if ($request->status != 1 and $request->status < 7  and $pesquisaValue != 'limpo') {
            $informacoes->where('entrevistas.status', $pesquisaValue);
        }
        // Caso nenhuma pesquisa seja feita, traz apenas os status ativos
        if ($pesquisaValue == 'limpo' and !$request->nome_pesquisa and !$request->tipo_entrevista) {
            $informacoes->whereNot('encaminhamento.status_encaminhamento', 6) // Entrevistas Canceladas Antes de Serem Agendadas
                ->where(function ($query) {
                    $query->whereNotIn('entrevistas.status', [5, 6, 7]); // Finalizadas e Canceladas
                    $query->orWhere('encaminhamento.status_encaminhamento', 1); // No caso de antes de Agendar
                });
        }

        // Aplica a ordem de vizualização da pagina
        $informacoes = $informacoes
            ->orderBy('encaminhamento.status_encaminhamento')
            ->orderBy('entrevistas.status', 'ASC')
            ->orderBy('atendimentos.emergencia', 'DESC')
            ->orderBy('atendimentos.dh_inicio')
            ->get()->toArray();

        // Confere se a pessoa tem um tratamento PTD ativo
        $ptdAtivos = DB::table('tratamento as tr')
            ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->whereIn('at.id_assistido', array_column($informacoes, 'id_pessoa'))
            ->where('enc.id_tipo_tratamento', 1) // PTD
            ->where('tr.status', '<', 3) // Ativo
            ->pluck('id_assistido')->toArray();


        foreach ($ptdAtivos as $ptd) {
            foreach (array_keys(array_column($informacoes, 'id_pessoa'), $ptd) as $info) {
                $informacoes[$info]->ptd = true;
            }
        }


        // Caso seja pesquisado Aguardando Agendamento
        if ($request->status == 1) {
            $info = [];

            // Popula um array com todas os itens da Variável
            foreach ($informacoes as $dia) {
                $info[] = $dia;
            }

            // Para cada item do novo array populado
            foreach ($info as $check) {

                // Caso o status não seja Aguardando Agendamento, retire do array
                if ($check->status != 1 or $check->status_encaminhamento_id != 1) {
                    unset($info[$i]);
                }
                $i = $i + 1;
            }

            $informacoes = $info; // Repopula a Variavel inicial com o array "pesquisado"
            $pesquisaValue = 1; // Envia para a view qual item foi selecionado anteriormente
        }

        // Caso seja pesquisado Inativado
        if ($request->status == 7) {
            $info = [];
            foreach ($informacoes as $dia) {
                $info[] = $dia;
            }

            foreach ($info as $check) {

                // Caso o Status não seja Cancelado, retire do array
                if ($check->status != 1 or $check->status_encaminhamento_id != 4) {
                    unset($info[$i]);
                }
                $i = $i + 1;
            }

            $informacoes = $info; // Repopula a Variavel inicial com o array "pesquisado"
            $pesquisaValue = 7; // Envia para a view qual item foi selecionado anteriormente
        }
        // Caso seja pesquisado Aguardando Requisitos
        if ($request->status == 8) {
            $info = [];
            foreach ($informacoes as $dia) {
                $info[] = $dia;
            }
            foreach ($info as $check) {

                // Caso o Status não seja Cancelado, retire do array
                if ($check->status != 1 or $check->status_encaminhamento_id != 5) {
                    unset($info[$i]);
                }
                $i = $i + 1;
            }

            $informacoes = $info; // Repopula a Variavel inicial com o array "pesquisado"
            $pesquisaValue = 8; // Envia para a view qual item foi selecionado anteriormente
        }
        // Caso seja pesquisado Aguardando Manutenção
        if ($request->status == 9) {
            $info = [];
            foreach ($informacoes as $dia) {
                $info[] = $dia;
            }
            foreach ($info as $check) {

                // Caso o Status não seja Cancelado, retire do array
                if ($check->status != 1 or $check->status_encaminhamento_id != 6) {
                    unset($info[$i]);
                }
                $i = $i + 1;
            }

            $informacoes = $info; // Repopula a Variavel inicial com o array "pesquisado"
            $pesquisaValue = 8; // Envia para a view qual item foi selecionado anteriormente
        }


        $totalAssistidos = count($informacoes); // Guarda o total de itens na variável

        // Traz as entrevistar para o Select de Pesquisa de Status
        $tipo_entrevista = DB::table('tipo_entrevista')
            ->whereIn('id', [3, 4, 5, 6]) // AME, AFE, DIAMO, NUTRES
            ->select('id as id_ent', 'sigla as ent_desc')
            ->orderby('descricao', 'asc')
            ->get();

        // dd($informacoes); // Debug the fetched data

        $status = DB::table('tipo_status_entrevista')->orderBy('id', 'ASC')->get(); // Traz os itens para pesquisa de Status
        $motivo = DB::table('tipo_motivo')->where('vinculado', 3)->orderBy('tipo')->get(); // Usado no Select de Motivo no Modal de Inativação

        $informacoes = $this->paginate($informacoes, 50); // Pagina o Array
        $informacoes->withPath('')->appends(
            [
                'status' => $request->status,
                'tipo_entrevista' => $request->tipo_entrevista,
                'nome_pesquisa' => $request->nome_pesquisa
            ]
        ); // Usado para que ao trocar de página, as pesquisas se mantenham
        return view('Entrevistas.gerenciar-entrevistas', compact('tipo_entrevista', 'totalAssistidos', 'informacoes', 'pesquisaValue', 'status', 'motivo'));
    }



    // Botão de Agendar
    public function create($id)
    {
        try {
            $encaminhamento = DB::table('encaminhamento')->where('id', $id)->first(); // Usado para validar e pra Mandar o ID via GET para o método de INCLUIR

            // Retorna todas as salas ativas, ordenadas por número
            $salas = DB::table('salas')
                ->join('tipo_localizacao', 'salas.id_localizacao', 'tipo_localizacao.id')
                ->select('salas.*', 'tipo_localizacao.nome AS nome_localizacao')
                ->where('status_sala', 1)
                ->orderBy('numero');

            // Caso seja AFE, mostra apenas as salas de Atendimento Fraterno
            if ($encaminhamento->id_tipo_entrevista == 3) {
                $salas = $salas->where('id_finalidade', 2);
            }
            $salas = $salas->get();

            // Retorna todas as informações do Assistido
            $informacoes = DB::table('encaminhamento')
                ->leftJoin('atendimentos', 'encaminhamento.id_atendimento', 'atendimentos.id')
                ->leftJoin('pessoas AS pessoa_pessoa', 'atendimentos.id_assistido', 'pessoa_pessoa.id')
                ->leftJoin('tp_ddd as ddd', 'pessoa_pessoa.ddd', 'ddd.id')
                ->select(
                    'pessoa_pessoa.nome_completo AS nome_pessoa',
                    'pessoa_pessoa.celular',
                    'ddd.descricao as ddd',
                    'encaminhamento.id_tipo_entrevista'
                )
                ->where('encaminhamento.id', $id)
                ->first();

            return view('Entrevistas.criar-entrevista', compact('salas',  'encaminhamento', 'informacoes'));
        } catch (\Exception $e) {
            app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
            return redirect()->back();
        }
    }

    // Submit do formulário do Botão Agendar
    public function store(Request $request, $id)
    {
        try {

            $dt_hora = Carbon::now();

            $entrevista = DB::table('entrevistas')->insertGetId([
                'id_encaminhamento' => $id,
                'id_sala' => $request->id_sala,
                'data' => $request->data,
                'hora' => $request->hora,
                'status' => 2, // Confirmar Atendente
            ]);

            // Insere no histórico a criação do atendimento
            DB::table('log_atendimentos')->insert([
                'id_referencia' => $entrevista,
                'id_usuario' => session()->get('usuario.id_usuario'),
                'id_acao' => 2, // foi criado
                'id_origem' => 4, // Entrevista
                'data_hora' => $dt_hora
            ]);

            return redirect()->route('gerenciamento')->with('success', 'Entrevista criada com sucesso!');
        } catch (\Exception $e) {
            app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
            DB::rollBack();
            return redirect()->back();
        }
    }

    // Botão Confirmar Entrevistador
    public function criar($id)
    {
        try {

            $entrevistas = DB::table('entrevistas AS entre')
                ->leftJoin('salas AS s', 'entre.id_sala', 's.id')
                ->leftJoin('pessoas as pessoa_entrevistador', 'entre.id_entrevistador', 'pessoa_entrevistador.id')
                ->leftJoin('tipo_localizacao as tpl', 's.id_localizacao', 'tpl.id')
                ->leftJoin('encaminhamento AS enc', 'entre.id_encaminhamento', 'enc.id')
                ->leftJoin('tipo_entrevista as te', 'enc.id_tipo_entrevista', 'te.id')
                ->leftJoin('atendimentos as atd', 'enc.id_atendimento', 'atd.id')
                ->leftJoin('pessoas AS pessoa_assitido', 'atd.id_assistido', 'pessoa_assitido.id')
                ->select(
                    'pessoa_assitido.nome_completo',
                    's.nome',
                    's.numero',
                    'tpl.nome as local',
                    'enc.id',
                    'entre.id',
                    'entre.id_entrevistador',
                    'entre.data',
                    'entre.hora',
                    'pessoa_entrevistador.nome_completo as nome_completo_pessoa_entrevistador',
                    'te.id_setor'
                )
                ->where('entre.id_encaminhamento', $id)
                ->first();


            $usuarios = DB::table('usuario as u')
                ->leftJoin('usuario_acesso as ua', 'u.id', 'ua.id_usuario')
                ->where('id_acesso', 9)
                ->where('id_setor', $entrevistas->id_setor)
                ->pluck('u.id_pessoa')
                ->toArray();



            $salas = DB::table('salas')->get();
            $encaminhamento = DB::table('encaminhamento')->find($id);
            $pessoas = DB::table('pessoas')->get();


            $membros = DB::table('membro')
                ->rightJoin('associado', 'membro.id_associado', 'associado.id')
                ->join('pessoas', 'associado.id_pessoa', 'pessoas.id')
                ->leftJoin('cronograma as cro', 'membro.id_cronograma', 'cro.id')
                ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
                ->select('membro.*', 'pessoas.nome_completo', 'gr.id_setor')
                ->where('gr.id_setor', $entrevistas->id_setor)
                ->whereIn('associado.id_pessoa', $usuarios)
                ->whereIn('membro.id_funcao', [1, 2])
                ->distinct('membro.id_associado')
                ->get();
            // dd($membros);


            $encaminhamento = DB::table('encaminhamento')->find($id);

            // Verificando se o tipo de entrevista é 3 (tipo_entrevista 3, afe)
            if ($encaminhamento && $encaminhamento->id_tipo_entrevista === 3) {
                // Obtendo informações dos atendentes (caso o tipo de entrevista seja afe)
                $membros = DB::table('membro')
                    ->join('associado', 'membro.id_associado', 'associado.id')
                    ->join('pessoas', 'associado.id_pessoa', 'pessoas.id')
                    ->select('membro.*', 'pessoas.nome_completo')
                    ->distinct('membro.id_associado')
                    ->where('membro.id_funcao', 5)
                    ->get();
            }
            return view('Entrevistas.agendar-entrevistador', compact('membros', 'entrevistas', 'encaminhamento', 'pessoas', 'salas'));
        } catch (\Exception $e) {

            app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
            return redirect()->back();
        }
    }

    // Store de Confirmar Entrevistador
    public function incluir(Request $request, string $id)
    {
        try {

            $dt_hora = Carbon::now();

            // Atualiza os dados da tebela de entrevista com o Entrevistador
            $entrevista = DB::table('entrevistas')->where('id_encaminhamento', $id);
            $idEntrevista = $entrevista->first()->id;
            $entrevista->update([
                'id_entrevistador' => $request->input('id_entrevistador'),
                'status' => 3, // Aguardando Atendimento
            ]);

            // Insere no histórico a criação do atendimento
            DB::table('log_atendimentos')->insert([
                'id_referencia' => $idEntrevista,
                'id_usuario' => session()->get('usuario.id_usuario'),
                'id_acao' => 1, // mudou de Status para
                'id_origem' => 4, // Entrevista
                'id_observacao' => 3, // // Aguardando Atendimento
                'data_hora' => $dt_hora
            ]);

            return redirect()->route('gerenciamento')->with('success', 'O cadastro foi realizado com sucesso!');
        } catch (\Exception $e) {
            app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
            DB::rollBack();
            return redirect()->back();
        }
    }


    public function show($id)
    {
        //  try {

        // Traz todos os dados da VIEW
        $entrevistas = DB::table('encaminhamento AS enc')
            ->select(
                'p.nome_completo',
                'p.celular',
                'p.id as id_assistido',
                'ddd.descricao as ddd',
                's.nome',
                's.numero',
                'tpl.nome as local',
                'enc.id',
                'enc.id_tipo_entrevista',
                'entre.id',
                'entre.id_entrevistador',
                'entre.data',
                'entre.hora',
                'pessoas.nome_completo as entrevistador'
            )
            ->leftJoin('entrevistas AS entre', 'enc.id', 'entre.id_encaminhamento')
            ->leftJoin('salas AS s', 'entre.id_sala', 's.id')
            ->leftJoin('tipo_localizacao as tpl', 's.id_localizacao', 'tpl.id')
            ->leftJoin('atendimentos as atd', 'enc.id_atendimento', 'atd.id')
            ->leftJoin('pessoas AS p', 'atd.id_assistido', 'p.id')
            ->leftJoin('tp_ddd as ddd', 'p.ddd', 'ddd.id')
            ->leftJoin('associado', 'entre.id_entrevistador', 'associado.id')
            ->leftJoin('pessoas', 'associado.id_pessoa', 'pessoas.id')
            ->where('enc.id', $id)
            ->first();

        // XXX Uma gambiarra pra resolver tradução entre tratamentos e entrevistas
        $tradutor = [
            2 => 4,
            5 => 6,
            6 => 4
        ];

        $tradutor = isset($tradutor[$entrevistas->id_tipo_entrevista]) ?  $tradutor[$entrevistas->id_tipo_entrevista] : 0;


        $tratamento = DB::table('encaminhamento as enc')
            ->select(
                'enc.id as ide',
                'gr.nome',
                'rm.h_inicio',
                'td.nome as dia',
                'tr.id as idt',
                'tr.dt_inicio',
                'tr.dt_fim',
                'tt.descricao',
                'tse.nome as status',
                'enc.id_tipo_tratamento',
                'at.id_assistido'
            )
            ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
            ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
            ->leftjoin('tratamento AS tr', 'enc.id', 'tr.id_encaminhamento')
            ->leftJoin('tipo_status_tratamento AS tse', 'tr.status', 'tse.id')
            ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
            ->leftjoin('grupo AS gr', 'rm.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as td', 'rm.dia_semana', 'td.id')
            ->where('at.id_assistido', $entrevistas->id_assistido) // Todos daquele assistido
            ->where('enc.id_tipo_encaminhamento', 2) // Encaminhamento de Tratamento
           ->where('enc.id_tipo_tratamento', $tradutor)
           ->orderBy('tr.dt_fim', 'DESC')
           ->orderBy('tr.dt_inicio', 'DESC')
           ->first();

        $presencas = DB::table('presenca_cronograma as pc')
            ->select('enc.id_tipo_tratamento', 'dc.data', 'pc.presenca', 'gr.nome')
            ->leftJoin('tratamento as tr', 'pc.id_tratamento', 'tr.id')
            ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->leftJoin('dias_cronograma as dc', 'pc.id_dias_cronograma', 'dc.id')
            ->leftJoin('cronograma as cro', 'tr.id_reuniao', 'cro.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->where('at.id_assistido', $entrevistas->id_assistido)
            ->whereIn('enc.id_tipo_tratamento', [1, 2])
            ->where('enc.status_encaminhamento', '<', 3)
            ->orderBy('dc.data', 'DESC')
            ->get();


        return view('Entrevistas.visualizar-entrevista', compact('entrevistas', 'id', 'presencas', 'tratamento'));
        // } catch (\Exception $e) {

        //     app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
        //     return redirect()->back();
        // }
    }



    public function edit($id)
    {
        try {

            $entrevistas = DB::table('entrevistas AS entre')
                ->leftJoin('salas AS s', 'entre.id_sala', 's.id')
                ->leftJoin('pessoas as pessoa_entrevistador', 'entre.id_entrevistador', 'pessoa_entrevistador.id')
                ->leftJoin('tipo_localizacao as tpl', 's.id_localizacao', 'tpl.id')
                ->leftJoin('encaminhamento AS enc', 'entre.id_encaminhamento', 'enc.id')
                ->leftJoin('tipo_entrevista as te', 'enc.id_tipo_entrevista', 'te.id')
                ->leftJoin('atendimentos as atd', 'enc.id_atendimento', 'atd.id')
                ->leftJoin('pessoas AS pessoa_assitido', 'atd.id_assistido', 'pessoa_assitido.id')
                ->select(
                    'pessoa_assitido.nome_completo',
                    's.nome',
                    's.numero',
                    'tpl.nome as local',
                    'enc.id',
                    'entre.id',
                    'entre.id_entrevistador',
                    'entre.data',
                    'entre.hora',
                    'pessoa_entrevistador.nome_completo as nome_completo_pessoa_entrevistador',
                    'te.id_setor',
                    'enc.id_tipo_entrevista',
                    's.id as sala_id',
                )
                ->where('entre.id_encaminhamento', $id)
                ->first();

            // Retorna todas as salas ativas
            $salas = DB::table('salas')
                ->join('tipo_localizacao', 'salas.id_localizacao', 'tipo_localizacao.id')
                ->select('salas.*', 'tipo_localizacao.nome AS nome_localizacao')
                ->where('status_sala', 1)
                ->orderBy('numero');

            // Caso seja AFE, retorna apenas as salas de atendimento
            if ($entrevistas->id_tipo_entrevista == 3) {
                $salas = $salas->where('id_finalidade', 2);
            }
            $salas = $salas->get();


            $usuarios = DB::table('usuario as u')
                ->leftJoin('usuario_acesso as ua', 'u.id', 'ua.id_usuario')
                ->where('id_acesso', 9)
                ->where('id_setor', $entrevistas->id_setor)
                ->pluck('u.id_pessoa')
                ->toArray();

            // Caso padrão, traz todos os entrevistadores
            $membros = DB::table('membro')
                ->leftJoin('associado', 'membro.id_associado', 'associado.id')
                ->leftJoin('pessoas', 'associado.id_pessoa', 'pessoas.id')
                ->leftJoin('cronograma as cro', 'membro.id_cronograma', 'cro.id')
                ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
                ->select('membro.*', 'pessoas.nome_completo', 'gr.id_setor', 'pessoas.nome_completo as nome_entrevistador')
                ->where('gr.id_setor', $entrevistas->id_setor)
                ->whereIn('associado.id_pessoa', $usuarios)
                ->whereIn('membro.id_funcao', [1, 2]) // Dirigente ou Subdirigente
                ->distinct('membro.id_associado')
                ->get();

            // Verificando se o tipo de entrevista é 3 (tipo_entrevista 3, afe)
            if ($entrevistas->id_tipo_entrevista === 3) {
                // Obtendo informações dos atendentes (caso o tipo de entrevista seja afe)
                $membros = DB::table('membro')
                    ->join('associado', 'membro.id_associado', 'associado.id')
                    ->join('pessoas', 'associado.id_pessoa', 'pessoas.id')
                    ->select('membro.*', 'pessoas.nome_completo as nome_entrevistador')
                    ->distinct('membro.id_associado')
                    ->where('membro.id_funcao', 5)
                    ->get();
            }


            return view('Entrevistas.editar-entrevista', compact('membros', 'entrevistas', 'id', 'salas'));
        } catch (\Exception $e) {

            app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
            return redirect()->back();
        }
    }

    public function update(Request $request, $id)
    {


        $dt_hora = Carbon::now();

        // Traz os dados da entrevista gerada
        $entrevista = DB::table('entrevistas as ent')->where('id_encaminhamento', $id)
            ->select('at.id_assistido', 'ent.data', 'ent.hora', 'enc.id_tipo_entrevista', 'enc.id', 'ent.id as ide', 'ent.id_sala', 'ent.id_entrevistador', 'ent.status')
            ->leftJoin('encaminhamento as enc', 'ent.id_encaminhamento', 'enc.id')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id');

        $idEntrevista = $entrevista->first();

        // Força uma variável DATE e uma TIME a forçarem uma única DATETIME
        $dt = Carbon::createFromFormat('Y-m-d H:i:s', $idEntrevista->data . ' ' . $idEntrevista->hora);
        $dt_new = Carbon::createFromFormat('Y-m-d H:i:s', $request->input('data') . ' ' . $request->input('hora'));


        $entrevista->update([
            'id_entrevistador' => $request->input('entrevistador'),
            'data' => $request->input('data'),
            'hora' => $request->input('hora'),
            'id_sala' => $request->input('numero_sala'),
        ]);

        // Insere no histórico a criação do atendimento
        DB::table('log_atendimentos')->insert([
            'id_referencia' => $idEntrevista->ide,
            'id_usuario' => session()->get('usuario.id_usuario'),
            'id_acao' => 3, // foi editado
            'id_origem' => 4, // Entrevista
            'data_hora' => $dt_hora
        ]);

        // Caso seja uma entrevista do tipo AFE
        if ($idEntrevista->id_tipo_entrevista == 3 and $idEntrevista->status == 4) {

            // Busca um atendimento com especificações iguais a da entrevista
            $afe = DB::table('atendimentos')
                ->where('dh_marcada', $dt)
                ->where('id_assistido', $idEntrevista->id_assistido)
                ->where('id_atendente', $idEntrevista->id_entrevistador)
                ->where('id_tipo_atendimento', 2)
                ->where('status_atendimento', 3);

            if ($afe->first()) {
                // Insere no histórico a criação do atendimento
                DB::table('log_atendimentos')->insert([
                    'id_referencia' => $afe->first()->id,
                    'id_usuario' => session()->get('usuario.id_usuario'),
                    'id_acao' => 3, // foi editado
                    'id_origem' => 1, // Atendimento
                    'data_hora' => $dt_hora
                ]);

                $afe->update([
                    'id_atendente' => $request->input('entrevistador'),
                    'dh_marcada' => $dt_new,
                ]);
            }
        }

        app('flasher')->addSuccess("Entrevista atualizada com sucesso");
        return redirect('gerenciar-entrevistas');
    }

    public function finalizar($id)
    {
        //  try {

        $data = Carbon::now(); // Usado para a tabela de Audit
        $data_enc = Carbon::today();
        $id_usuario = session()->get('usuario.id_usuario');
        $encaminhamento = DB::table('encaminhamento')->where('id', $id)->first(); // Usado em Validação de Tipo de Entrevista

        // Traz os dados da entrevista gerada
        $entrevista = DB::table('entrevistas as ent')->where('id_encaminhamento', $id)
            ->select('at.id_assistido', 'ent.data', 'ent.hora', 'enc.id_tipo_entrevista', 'enc.id', 'ent.id_sala', 'ent.id_entrevistador')
            ->leftJoin('encaminhamento as enc', 'ent.id_encaminhamento', 'enc.id')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->first();

        // Força uma variável DATE e uma TIME a forçarem uma única DATETIME
        $dt = Carbon::createFromFormat('Y-m-d H:i:s', $entrevista->data . ' ' . $entrevista->hora);

        // A tabela Atendimentos pede o ID associado, logo, é necessária busca em banco desse dado
        $id_entrevistador = DB::table('membro')->where('id_associado', $entrevista->id_entrevistador)->select('id_associado')->first();

        // Caso seja uma entrevista do tipo AFE
        if ($encaminhamento->id_tipo_entrevista == 3) {

            // Cria um Atendimento do tipo AFE
            $afe = DB::table('atendimentos')->insertGetId([
                'dh_marcada' => $dt,
                'id_assistido' => $entrevista->id_assistido,
                'id_atendente' => $id_entrevistador->id_associado,
                'id_usuario' => session()->get('usuario.id_usuario'),
                'id_sala' => $entrevista->id_sala,
                'id_tipo_atendimento' => 2,
                'status_atendimento' => 3,
                'id_prioridade' => 3
            ]);


            // Insere no histórico a criação do atendimento
            DB::table('log_atendimentos')->insert([
                'id_referencia' => $afe,
                'id_usuario' => session()->get('usuario.id_usuario'),
                'id_acao' => 2, // foi criado
                'id_origem' => 1, // Atendimento
                'data_hora' => $data
            ]);


            // Atualiza a entrevista
            DB::table('entrevistas')->where('id_encaminhamento', $id)->update(['status' => 4]); // Agendado


        } else {

            // Insere um novo encaminhamento de tratamento para a Entrevista Aprovada
            $nova = DB::table('encaminhamento')->insertGetId([
                'dh_enc' => $data_enc,
                'id_usuario' => $id_usuario,
                'id_tipo_encaminhamento' => 2, // Tipo Tratamento
                'id_atendimento' => $encaminhamento->id_atendimento,
                'status_encaminhamento' => 1, // Aguardando Agendamento
            ]);


            if ($encaminhamento->id_tipo_entrevista == 4) { // Caso seja tipo NUTRES
                DB::table('encaminhamento')->where('id', $nova)->update(['id_tipo_tratamento' => 2,]); // Passe Tratamento Intensivo (PTI)
            } else if ($encaminhamento->id_tipo_entrevista == 5) { // Seja seja AME
                DB::table('encaminhamento')->where('id', $nova)->update(['id_tipo_tratamento' => 6,]); // Tratamento Fluidoterápico Integral (TFI)
            } else if ($encaminhamento->id_tipo_entrevista == 6) { // Caso seja DIAMO
                DB::table('encaminhamento')->where('id', $nova)->update(['id_tipo_tratamento' => 4,]); //  Programa de Apoio a Portadores de Mediunidade Ostensiva (PROAMO)
            }

            // Insere no histórico a criação do atendimento
            DB::table('log_atendimentos')->insert([
                'id_referencia' => $nova,
                'id_usuario' => session()->get('usuario.id_usuario'),
                'id_acao' => 2, // foi criado
                'id_origem' => 2, // Encaminhamento
                'data_hora' => $data
            ]);


            // Atualizar o status da entrevista para 'Entrevistado'
            $entrevista = DB::table('entrevistas')
                ->where('id_encaminhamento', $id);
            $idEntrevista = $entrevista->first()->id;
            $entrevista->update(['status' => 5,]);

            // Insere no histórico a criação do atendimento
            DB::table('log_atendimentos')->insert([
                'id_referencia' => $idEntrevista,
                'id_usuario' => session()->get('usuario.id_usuario'),
                'id_acao' => 1, // mudou de Status para
                'id_origem' => 4, // Entrevista
                'id_observacao' => 5, // Entrevista Finalizada
                'data_hora' => $data
            ]);
            // Atualiza o status do Encaminhamento para Finalizado
            DB::table('encaminhamento')->where('id', $id)->update(['status_encaminhamento' => 3]);
        }

        return redirect()->route('gerenciamento')->with('success', 'Entrevista finalizada com sucesso!');
        // } catch (\Exception $e) {

        //     app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
        //     DB::rollBack();
        //     return redirect()->back();
        // }
    }

    // Cancelar Entrevista
    public function inativar(Request $request, String $id)
    {

        $data = date("Y-m-d");
        $dt_hora = Carbon::now();

        // Insere o fato de Cancelamento de Entrevista

        // Usado para retornar o ID assistido para validações
        $idAssistido = DB::table('encaminhamento')->where('encaminhamento.id', $id)
            ->leftJoin('atendimentos', 'encaminhamento.id_atendimento', 'atendimentos.id')
            ->pluck('atendimentos.id_assistido')->toArray();


        $motivo_entrevista = $request->input('motivo_entrevista'); // Salva em uma Variável o Id_motivo do select

        // Retorna todos os IDs dos encaminhamentos de tratamento
        $countTratamentos = DB::table('encaminhamento as enc')
            ->select('id_tipo_tratamento', 't.dt_fim', 't.id')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->leftJoin('tratamento as t', 'enc.id', 't.id_encaminhamento')
            ->where('enc.id_tipo_encaminhamento', 2) // Encaminhamento de Tratamento
            ->where('at.id_assistido', $idAssistido)
            ->where('enc.status_encaminhamento', '<', 3) // 3 => Finalizado, Traz apenas os ativos (Para Agendar, Agendado)
            ->get()->toArray();

        // Retorna todos os IDs dos encaminhamentos de entrevista
        $countEntrevistas = DB::table('encaminhamento as enc')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->where('enc.id_tipo_encaminhamento', 1) // Encaminhamento de Entrevista
            ->where('at.id_assistido', $idAssistido)
            ->where('enc.status_encaminhamento', '<', 3) // 3 => Finalizado, Traz apenas os ativos (Para Agendar, Agendado)
            ->whereNot('enc.id', $id) // Exclui a entrevista de agora
            ->pluck('id_tipo_entrevista')->toArray();


        // Traz os dados da entrevista gerada
        $idEntrevista = DB::table('entrevistas as ent')->where('id_encaminhamento', $id)
            ->select('at.id_assistido', 'ent.data', 'ent.hora', 'enc.id_tipo_entrevista', 'enc.id', 'ent.id_sala', 'ent.id_entrevistador', 'ent.status')
            ->leftJoin('encaminhamento as enc', 'ent.id_encaminhamento', 'enc.id')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->first();


        // Caso seja uma entrevista do tipo AFE
        if ($idEntrevista and $idEntrevista->id_tipo_entrevista == 3) {
            // Força uma variável DATE e uma TIME a forçarem uma única DATETIME
            $dt = Carbon::createFromFormat('Y-m-d H:i:s', $idEntrevista->data . ' ' . $idEntrevista->hora);

            // Busca um atendimento com especificações iguais a da entrevista
            $afe = DB::table('atendimentos')
                ->where('dh_marcada', $dt)
                ->where('id_assistido', $idEntrevista->id_assistido)
                ->where('id_atendente', $idEntrevista->id_entrevistador)
                ->where('id_tipo_atendimento', 2)
                ->where('status_atendimento', 3);

            if ($afe->first()) {
                // Insere no histórico a criação do atendimento
                DB::table('log_atendimentos')->insert([
                    'id_referencia' => $afe->first()->id,
                    'id_usuario' => session()->get('usuario.id_usuario'),
                    'id_acao' => 3, // foi editado
                    'id_origem' => 1, // Atendimento
                    'data_hora' => $dt_hora
                ]);

                $afe->update([
                    'status_atendimento' => 7,
                    'motivo' => 5
                ]);
            }
        }

        $tfiInfinito = array_search(6, array_column($countTratamentos, 'id_tipo_tratamento')); // Busca, caso exista, a array key dos dados de Integral
        $tfiInfinito = $tfiInfinito ? $countTratamentos[$tfiInfinito] : false; // Caso tenha encontrado, retorna os dados de Integral
        $tfiInfinito = $tfiInfinito ? ($tfiInfinito->dt_fim == null and $tfiInfinito->id != null and in_array(6, array_column($countTratamentos, 'id_tipo_tratamento'))) : false; // Confere se é um Integral Permanente caso os dados existam
        // Essa é a clausula para um PTD infinito que está sendo apoiado em outro tratamento
        //      Tratamento PTI                                                         Entrevista NUTRES (PTI)                Tratamento PROAMO                                             Entrevista DIAMO (PROAMO)   Tratamento Integral Permanente
        if (in_array(2, array_column($countTratamentos, 'id_tipo_tratamento')) or in_array(4, $countEntrevistas) or in_array(4, array_column($countTratamentos, 'id_tipo_tratamento')) or in_array(6, $countEntrevistas) or $tfiInfinito) {

            // Cancela o encaminhamento da entrevista selecionada
            $encaminhamento = DB::table('encaminhamento')
                ->where('id', $id);
            $id_encaminhamento = $encaminhamento->first()->id;
            $encaminhamento->update([
                'status_encaminhamento' => 4, // Inativado
                'motivo' => $motivo_entrevista
            ]);

            // Insere no histórico a criação do atendimento
            DB::table('log_atendimentos')->insert([
                'id_referencia' => $id_encaminhamento,
                'id_usuario' => session()->get('usuario.id_usuario'),
                'id_acao' => 1, // mudou de Status para
                'id_origem' => 2, // Entrevista
                'id_observacao' => 4, // Entrevista Finalizada
                'data_hora' => $dt_hora
            ]);

            // Inativa a entrevista caso encontre alguma
            $entrevista = DB::table('entrevistas')
                ->where('id_encaminhamento', $id);
            $idEntrevista = $entrevista->first()->id;
            $entrevista->update(['status' => 6]); // Entrevista Cancelada

            // Insere no histórico a criação do atendimento
            DB::table('log_atendimentos')->insert([
                'id_referencia' => $idEntrevista,
                'id_usuario' => session()->get('usuario.id_usuario'),
                'id_acao' => 1, // mudou de Status para
                'id_origem' => 4, // Entrevista
                'id_observacao' => 6, // Entrevista Finalizada
                'data_hora' => $dt_hora
            ]);
        } else {

            $ptdAtivo = DB::table('tratamento as t')
                ->select('t.id', 'e.id as ide', 't.dt_fim', 'c.dia_semana')
                ->leftJoin('encaminhamento as e', 't.id_encaminhamento', 'e.id')
                ->leftJoin('atendimentos as a', 'e.id_atendimento', 'a.id')
                ->leftJoin('cronograma as c', 't.id_reuniao', 'c.id')
                ->where('a.id_assistido', $idAssistido)
                ->where('t.status', '<', 3)
                ->where('e.id_tipo_tratamento', 1)
                ->first();

            // Caso aquela entrevista tenha um PTD marcado, e ele seja infinito, e o motivo do cancelamento foi alta da avaliação, tire de infinito
            $ptdAtivoInfinito = $ptdAtivo ? $ptdAtivo->dt_fim == null : false; //
            if ($ptdAtivoInfinito and $motivo_entrevista == 11) {
                $dataFim = Carbon::today()->weekday($ptdAtivo->dia_semana);

                // Caso o tratamento seja num dia da semana anterior ou igual a hoje
                if ($data < $dataFim or $data == $dataFim) {
                    $PTDInfinito = DB::table('tratamento')
                        ->where('id', $ptdAtivo->id);
                    $idPTDInfinito = $PTDInfinito->first()->id;
                    $PTDInfinito->update([
                        'dt_fim' => $dataFim->addWeek(8)
                    ]);

                    DB::table('log_atendimentos')->insert([
                        'id_referencia' => $idPTDInfinito,
                        'id_usuario' => session()->get('usuario.id_usuario'),
                        'id_acao' => 10, // deixou de ser Permanente
                        'id_origem' => 3, // Tratamento
                        'data_hora' => $dt_hora
                    ]);
                } else { // Caso seja num dia da semana superior a hoje
                    $PTDInfinito = DB::table('tratamento')
                        ->where('id', $ptdAtivo->id);
                    $idPTDInfinito = $PTDInfinito->first()->id;
                    $PTDInfinito->update([
                        'dt_fim' => $dataFim->addWeek(7)
                    ]);

                    DB::table('log_atendimentos')->insert([
                        'id_referencia' => $idPTDInfinito,
                        'id_usuario' => session()->get('usuario.id_usuario'),
                        'id_acao' => 10, // deixou de ser Permanente
                        'id_origem' => 3, // Tratamento
                        'data_hora' => $dt_hora
                    ]);
                }
            } else if ($ptdAtivoInfinito) { // Caso não seja Alta, Cancela o PTD Infinito junto com a entrevista
                $dataFim = Carbon::today()->weekday($ptdAtivo->dia_semana);

                $inativPTD = DB::table('tratamento')
                    ->where('id', $ptdAtivo->id);
                $idInativPTD = $inativPTD->first()->id;
                $inativPTD->update([
                    'dt_fim' => $dataFim,
                    'status' => 6, // Inativado
                ]);


                // Insere no histórico a criação do atendimento
                DB::table('log_atendimentos')->insert([
                    'id_referencia' => $idInativPTD,
                    'id_usuario' => session()->get('usuario.id_usuario'),
                    'id_acao' => 1, // mudou de Status para
                    'id_origem' => 3, // Tratamento
                    'id_observacao' => 6, // Inativado
                    'data_hora' => $dt_hora
                ]);

                $inativEncPTD = DB::table('encaminhamento')
                    ->where('id', $ptdAtivo->ide);
                $idInativEncPTD = $inativEncPTD->first()->id;
                $inativEncPTD->update([
                    'status_encaminhamento' => 4 // Inativado
                ]);


                // Insere no histórico a criação do atendimento
                DB::table('log_atendimentos')->insert([
                    'id_referencia' => $idInativEncPTD,
                    'id_usuario' => session()->get('usuario.id_usuario'),
                    'id_acao' => 1, // mudou de Status para
                    'id_origem' => 2, // Encaminhamento
                    'id_observacao' => 4, // Inativado
                    'data_hora' => $dt_hora
                ]);
            } // Caso não seja alta da avaliação, finaliza o tratamento ptd

            // Cancela o encaminhamento da entrevista selecionada
            $encEntrevista = DB::table('encaminhamento')
                ->where('id', $id);
            $idEncEntrevista = $encEntrevista->first()->id;
            $encEntrevista->update([
                'status_encaminhamento' => 4, // Inativado
                'motivo' => $motivo_entrevista
            ]);


            // Insere no histórico a criação do atendimento
            DB::table('log_atendimentos')->insert([
                'id_referencia' => $idEncEntrevista,
                'id_usuario' => session()->get('usuario.id_usuario'),
                'id_acao' => 1, // mudou de Status para
                'id_origem' => 2, // Encaminhamento
                'id_observacao' => 4, // Inativado
                'data_hora' => $dt_hora
            ]);

            // Inativa a entrevista caso encontre alguma
            $inativEntrevista = DB::table('entrevistas')
                ->where('id_encaminhamento', $id);
            if ($inativEntrevista->first()) {
                $idInativEntrevista = $inativEntrevista->first()->id;
                $inativEntrevista->update(['status' => 6]); // Entrevista Cancelada
                // Insere no histórico a criação do atendimento
                DB::table('log_atendimentos')->insert([
                    'id_referencia' => $idInativEntrevista,
                    'id_usuario' => session()->get('usuario.id_usuario'),
                    'id_acao' => 1, // mudou de Status para
                    'id_origem' => 4, // Entrevista
                    'id_observacao' => 6, // Entrevista Cancelada
                    'data_hora' => $dt_hora
                ]);
            }
        }

        return redirect()->route('gerenciamento')->with('success', 'Entrevista Cancelada com Sucesso!');
    }
}
