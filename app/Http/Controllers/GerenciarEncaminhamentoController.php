<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;


class GerenciarEncaminhamentoController extends Controller
{
    public function index(Request $request)
    {
        // Lista de Dados que aparece na view
        $lista = DB::table('encaminhamento AS enc')
            ->select(
                'at.id AS ida',
                'at.id_assistido',
                'at.dh_fim',
                'at.dh_chegada',
                'at.id_representante as idr',
                'enc.id AS ide',
                'enc.id_atendimento',
                'enc.id_tipo_encaminhamento',
                'enc.id_tipo_tratamento AS idtt',
                'enc.status_encaminhamento',
                'id_tipo_entrevista',
                'p1.nome_completo AS nm_1',
                'p1.cpf AS cpf_assistido',
                'p2.nome_completo as nm_2',
                'pr.id AS prid',
                'pr.sigla AS prsigla',
                'tse.descricao AS tsenc',
                'tt.descricao AS desctrat',
                'tt.sigla',
                DB::raw("(CASE WHEN at.emergencia = true THEN 'Emergência' ELSE 'Normal' END) as prdesc"),
            )
            ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
            ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
            ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
            ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
            ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
            ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
            ->leftJoin('tipo_status_encaminhamento AS tse', 'enc.status_encaminhamento', 'tse.id')
            ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
            ->where('enc.id_tipo_encaminhamento', 2); // Tipo de encaminhamento Tratamento

        $tratamentos_permitidos = array(); // Armazenas os tipos de tratamento permitidos pela sessão
        in_array(16, session()->get('usuario.acesso')) ? array_push($tratamentos_permitidos, 1) : 0; // Se tiver permissão PTD, adiciona o ID de PTD
        in_array(22, session()->get('usuario.acesso')) ? array_push($tratamentos_permitidos, 2) : 0; // Se tiver permissão PTI, adiciona o ID de PTI
        in_array(23, session()->get('usuario.acesso')) ? array_push($tratamentos_permitidos, 6) : 0; // Se tiver permissão Integral, adiciona o ID de Integral
        in_array(40, session()->get('usuario.acesso')) ? array_push($tratamentos_permitidos, 4) : 0; // Se tiver permissão PROAMO, adiciona o ID de PROAMO

        $lista = $lista->whereIn('enc.id_tipo_tratamento', $tratamentos_permitidos); // Busca apenas os IDs Permitidos na tela


        $data_enc = $request->dt_enc; // Armazena a pesquisa de Data de Encaminhamento
        $assistido = $request->assist; // Armazena a pesquisa de Nome de Assistido
        $cpf = $request->cpf; // Armazena a pesquisa de CPF
        $situacao = $request->status; // Armazena a pesquisa por Status (Select)

        if ($request->dt_enc) {
            $lista->where('at.dh_chegada', '>=', $request->dt_enc); // Pesquisa qualquer data que seja maior ou igual a pesquisada
        }
        if ($request->assist) {

            $pesquisaNome = array(); // Inicia um array
            $pesquisaNome = explode(' ', $request->assist); // Popula esse array com cada palavra digitada no input
            $margemErro = 0; // Inicializa em 0 uma variável de contagem de erros, usada para validação
            foreach ($pesquisaNome as $itemPesquisa) { // Para cada palavra na pesquisa

                $bufferPessoa = (clone $lista); // Salva o estado anterior antes de pesquisar
                $lista =  $lista->whereRaw("UNACCENT(LOWER(p1.nome_completo)) ILIKE UNACCENT(LOWER(?))", ["%$itemPesquisa%"]); // Pesquisa sem acento e sem case sensitive
                if (count($lista->get()->toArray()) < 1) { // Caso durante o select, o banco não retorne nada
                    $pessoaVazia = (clone $lista); // Guarda em uma variável o que é um estado vazio, para popular a tabela
                    $lista = (clone $bufferPessoa); // Devolve a variável para o estado antes dessa pesquisa
                    $margemErro += 1; // Adiciona 1 na varíavel de contagem para validação

                }
            }
            if ($margemErro == 0) { // Caso não tenha sofrido nenhum erro, passa direto
            } else if ($margemErro < (count($pesquisaNome) / 2)) { // Caso o número de erros seja inferior a 50% dos dados indicados
                app('flasher')->addWarning('Nenhum Item Encontrado. Mostrando Pesquisa Aproximada');
            } else {
                //Transforma a variavel em algo vazio
                $lista = $pessoaVazia;
                app('flasher')->addError('Nenhum Item Encontrado!');
            }
        }
        if ($request->cpf) {

            $lista->where('p1.cpf', $request->cpf); // Pesquisa CPF
        }
        if ($request->status) {
            $lista->where('enc.status_encaminhamento', $request->status); // Pesquisa Status, é um select na view
        }
        if ($request->tratamento) {
            $lista->where('enc.id_tipo_tratamento', $request->tratamento); // Pesquisa os tratamentos, é um select na view
        }

        $contar = (clone $lista)->get()->count('enc.id');
        $lista = $lista
            ->orderby('status_encaminhamento', 'ASC') // Status tem prioridade, Primeiro Agendar, por último cancelado
            ->orderby('at.emergencia', 'DESC') // Emergências ao topo
            ->orderBy('enc.id_tipo_tratamento', 'DESC') // Organizados pelo tipo de tratamento
            ->orderBy('at.dh_inicio') // Por ordem de chegada no atendimento
            ->paginate(50) // Paginate com 50 itens por página
            ->appends([
                'assist' => $assistido, // Caso troque de pagina, mantém a pesquisa de Assisitido
                'tratamento' => $request->tratamento, // Caso troque de pagina, matém a pesquisa de tratamento
                'status' => $situacao, // Caso troque de pagina, matém a pesquisa de  status
                'dt_enc' => $data_enc // Caso troque de pagina, matém a pesquisa de dt_enc
            ]);

        $stat = DB::select("select
        ts.id,
        ts.descricao
        from tipo_status_encaminhamento ts
        ");

        $motivo = DB::table('tipo_motivo')->where('vinculado', 1)->get();

        return view('/recepcao-integrada/gerenciar-encaminhamentos', compact('cpf', 'lista', 'stat', 'contar', 'data_enc', 'assistido', 'situacao', 'motivo'));
    }

    // Função retornada ao cliclar em Agendar, mostra os cards com os dias e grupos
    public function agenda($ide, $idtt)
    {
        //    try {
        $hoje = Carbon::now()->format('Y-m-d'); // Retorna o dia de Hoje no formato de banco de dados
        // Traz todos os dados da pessoa que foi encaminhada, usado na view para visualizar apenas
        $result = DB::table('encaminhamento AS enc')
            ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'dh_enc', 'enc.id_atendimento', 'enc.status_encaminhamento', 'tse.descricao AS tsenc', 'enc.id_tipo_tratamento', 'id_tipo_entrevista', 'at.id AS ida', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante as idr', 'p2.nome_completo as nm_2', 'pa.id AS pid', 'pa.nome', 'pr.id AS prid', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla', 'tt.descricao AS desctrat')
            ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
            ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
            ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
            ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
            ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
            ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
            ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
            ->leftJoin('tipo_status_encaminhamento AS tse', 'enc.status_encaminhamento', 'tse.id')
            ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
            ->where('enc.id', $ide)
            ->first();

        // Traz todos os dados de todos os dias de tratamento
        $dadosDias = DB::table('cronograma AS reu')
            ->select(DB::raw('count(*) as numeroCronograma, sum(max_atend) as maximoVagas'), 'reu.dia_semana as dia', 'td.nome as dia_semana') // Numero de grupos, Numero total de vagas, e dia da semana
            ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as td', 'reu.dia_semana', 'td.id')
            ->where(function ($query) use ($hoje) {
                $query->whereRaw('reu.data_fim > ?', [$hoje])->orWhereNull('reu.data_fim'); // Traz apenas grupos ativos
            })
            ->where(function ($query) {
                $query->where('reu.modificador', NULL); // Sem modificador nenhum
                $query->orWhere('reu.modificador', '<>', 4); // Grupo que não esteja Em Férias
            })
            ->where('reu.id_tipo_tratamento', $idtt)
            ->groupBy('dia', 'td.nome')
            ->orderBy('dia') // Ordena para que os cards saiam certos
            ->get();

        foreach ($dadosDias  as $key => $dadoDia) {

            // Calcula quantos tratamentos ativos tem naquele dia e conta
            $bufferVagas = DB::table('tratamento AS tr')
                ->leftJoin('cronograma AS reu', 'tr.id_reuniao', 'reu.id')
                ->select(DB::raw("($dadoDia->maximovagas - COUNT(CASE WHEN tr.status < 3 THEN tr.id END)) as trat"))
                ->where('reu.id_tipo_tratamento', $idtt)
                ->where('reu.dia_semana', $dadoDia->dia)
                ->pluck('trat')
                ->toArray();

            $dadosDias[$key]->vagas = current($bufferVagas); // Adiciona o valor do buffer para a variavel principal
        }

        return view('recepcao-integrada.agendar-dia', compact('result', 'dadosDias'));
    }

    public function tratamento(Request $request, $ide) //
    {
        $hoje = Carbon::today(); // Data de Hoje
        $dia = intval($request->dia); // Pega o dia do request
        $ide = intval($ide); // Pega o id do encaminhamento passado por método GET

        // Descobre o tipo de tratamento do encaminhamento atual
        $tp_trat = DB::table('encaminhamento AS enc')
            ->select('enc.id_tipo_tratamento')
            ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
            ->where('enc.id', $ide)->value('enc.id_tipo_tratamento');

        // Retorna todos os dados do encaminhamento, para o header com informaçoes para confirmação visual e validação de Observação
        $result = DB::table('encaminhamento AS enc')
            ->select(
                'enc.id AS ide',
                'p1.nome_completo AS nm_1',
                'p2.nome_completo as nm_2',
                'tt.descricao AS desctrat',
                'p1.dt_nascimento',
                'at.id_assistido'
            )
            ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
            ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
            ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
            ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
            ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
            ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
            ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
            ->leftJoin('tipo_status_encaminhamento AS tse', 'enc.status_encaminhamento', 'tse.id')
            ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
            ->where('enc.id', $ide)
            ->first();

        // Usado para validação de observação PROAMO
        $countTratamentos = DB::table('encaminhamento as enc')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->where('enc.id_tipo_encaminhamento', 2) // Encaminhamento de Tratamento
            ->where('at.id_assistido', $result->id_assistido)
            ->where('enc.status_encaminhamento', '<', 3) // 3 => Finalizado, Traz apenas os ativos (Para Agendar, Agendado)
            ->pluck('id_tipo_tratamento')->toArray();


        // Devolve todos os dados dos grupos que podem ser selecionados
        $trata = DB::table('cronograma AS reu')
            ->select(
                DB::raw('(reu.max_atend - (select count(*) from tratamento tr where tr.id_reuniao = reu.id and tr.status < 3)) as trat'),
                'p.nome_completo',
                'reu.id AS idr',
                'gr.nome AS nomeg',
                'reu.dia_semana',
                'reu.id_sala',
                'reu.id_tipo_tratamento',
                'reu.h_inicio',
                'td.nome AS nomed',
                'reu.h_fim',
                'reu.max_atend',
                'gr.status_grupo AS idst',
                'tsg.descricao AS descst',
                'tst.descricao AS tstd',
                'sa.numero',
                'tor.descricao as des'
            )
            ->leftJoin('tratamento AS tr', 'reu.id', 'tr.id_reuniao')
            ->leftJoin('tipo_tratamento AS tst', 'reu.id_tipo_tratamento', 'tst.id')
            ->leftJoin('salas AS sa', 'reu.id_sala', 'sa.id')
            ->leftJoin('tipo_dia AS td', 'reu.dia_semana', 'td.id')
            ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
            ->leftJoin('tipo_status_grupo AS tsg', 'gr.status_grupo', 'tsg.id')
            ->leftJoin('membro AS me', 'reu.id', 'me.id_cronograma')
            ->leftJoin('associado as ass', 'me.id_associado', 'ass.id')
            ->leftJoin('pessoas as p', 'ass.id_pessoa', 'p.id')
            ->leftJoin('tipo_observacao_reuniao as tor', 'reu.observacao', 'tor.id')
            ->where(function ($query) use ($hoje) {
                $query->whereRaw('reu.data_fim > ?', [$hoje])->orWhereNull('reu.data_fim');
            })
            ->where(function ($query) {
                $query->where('reu.modificador', NULL);
                $query->orWhere('reu.modificador', '<>', 4);
            })
            ->where('me.id_funcao', 1) // Busca apenas dirigentes, gera um bug de duplicação caso um grupo tenha mais de um dirigente
            ->whereNull('me.dt_fim')
            ->where('reu.dia_semana', $dia)
            ->where('reu.id_tipo_tratamento', $tp_trat)
            ->orWhere('tr.status', null)
            ->where('tr.status', '<', 3)
            ->groupBy('p.nome_completo', 'reu.h_inicio', 'reu.max_atend', 'reu.id', 'gr.nome', 'td.nome', 'gr.status_grupo', 'tsg.descricao', 'tst.descricao', 'sa.numero', 'tor.descricao')
            ->orderBy('h_inicio');

        // Caso não seja menor de idade, exclua os grupos de criança
        if (Carbon::today()->diffInYears($result->dt_nascimento) > 17) {
            $trata = $trata->whereNot('reu.observacao', 8);
        }
        // Caso não esteja em um PROAMO, exclui os grupos específicos de PROAMO
        if (!in_array(4, $countTratamentos)) {
            $trata = $trata->whereNot('reu.observacao', 9);
        }

        $trata = $trata->get();

        // Validação de erro para dias sem grupo
        if (sizeof($trata) == 0) {
            app('flasher')->addWarning('Não existem grupos para este dia');
            return redirect()->back();
        }

        return view('recepcao-integrada.agendar-tratamento', compact('result', 'trata'));
    }

    public function tratar(Request $request, $ide)
    {

        $reu = intval($request->reuniao); // Guarda numa variavel o ID_cronograma
        $data_atual = Carbon::now(); // Dia de hoje, com dia e horário
        $dia_atual = $data_atual->weekday(); // ID do dia de hoje (Ex.: 0 => Domingo, 3 => Quarta-Feira)

        // Busca o ID dia da semana do cronograma escolhido
        $dia_semana = DB::table('cronograma AS reu')->where('id', $reu)->value('dia_semana');
        $data_antes = Carbon::today()->weekday($dia_semana)->addWeek(1); // Pega a data do tratamento para dias anteriores a hoje
        $data_depois = Carbon::today()->weekday($dia_semana); // Pega a data do tratamento para dias superiores a hoje

        // Conta a quantidade de tratamentos ativos para a reunião escolhida
        $countVagas = DB::table('tratamento')
            ->where('id_reuniao', $reu)
            ->where('status', '<', '3')
            ->count();

        // Traz o cronograma/reunião escolhida, com todos os seus dados
        $maxAtend = DB::table('cronograma')
            ->where('id', $reu)
            ->first();

        // Traz todos os dados do encaminhamento atual
        $tratID = DB::table('encaminhamento')->where('encaminhamento.id', $ide)->leftJoin('atendimentos', 'id_atendimento', 'atendimentos.id')->first();

        // Retorna todos os IDs dos encaminhamentos de tratamento
        $countTratamentos = DB::table('encaminhamento as enc')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->where('enc.id_tipo_encaminhamento', 2) // Encaminhamento de Tratamento
            ->where('at.id_assistido', $tratID->id_assistido)
            ->where('enc.status_encaminhamento', '<', 3) // 3 => Finalizado, Traz apenas os ativos (Para Agendar, Agendado)
            ->pluck('id_tipo_tratamento')->toArray();

        // Retorna todos os IDs dos encaminhamentos de entrevista
        $countEntrevistas = DB::table('encaminhamento as enc')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->where('enc.id_tipo_encaminhamento', 1) // Encaminhamento de Entrevista
            ->where('at.id_assistido', $tratID->id_assistido)
            ->where(function ($query) {
                $query->where(function ($subQuery) { // Caso seja um PROAMO aguardando requsitos
                    $subQuery->where('enc.id_tipo_entrevista', 6); // Entrevista PROAMO
                    $subQuery->where('enc.status_encaminhamento', 5); // Aguardando Requisitos
                });
                $query->orWhere(function ($subQuery) { // Caso seja qualquer outro encaminhamento ativo
                    $subQuery->whereNot('enc.id_tipo_entrevista', 6); // Entrevista PROAMO
                    $subQuery->where('enc.status_encaminhamento', '<', 3); // 3 => Finalizado, Traz apenas os ativos (Para Agendar, Agendado)
                });
            })
            ->pluck('id_tipo_entrevista')->toArray();


        $tfiInfinito = array_search(6, array_column($countTratamentos, 'id_tipo_tratamento')); // Busca, caso exista, a array key dos dados de Integral
        $tfiInfinito = $tfiInfinito ? $countTratamentos[$tfiInfinito] : false; // Caso tenha encontrado, retorna os dados de Integral
        $tfiInfinito = $tfiInfinito ? ($tfiInfinito->dt_fim == null and $tfiInfinito->id != null and in_array(6, array_column($countTratamentos, 'id_tipo_tratamento'))) : false; // Confere se é um Integral Permanente caso os dados existam

        // O encaminhamento não seja PTD (Cujo é permitido exceder as vagas) e o número de Vagas seja menor ou igual ao de tratamentos
        if ($tratID->id_tipo_tratamento != 1 and $countVagas >= $maxAtend->max_atend) {
            app('flasher')->addError('Número de vagas insuficientes');
            return redirect()->back();
        }

        // Caso o dia seja superior ao dia de hoje
        if ($dia_atual < $dia_semana) {

            // Caso seja um tratamento PTD
            if ($tratID->id_tipo_tratamento == 1) {
                //Caso ele tenha uma entrevista ou tratamento PTI ou PROAMO, é criado um tratamento permanente
                if ((in_array(2, $countTratamentos) or in_array(4, $countTratamentos)) or (in_array(4, $countEntrevistas) or (in_array(6, $countEntrevistas))) or $tfiInfinito) {

                    $idPTI = DB::table('tratamento AS tr')->insertGetId([
                        'id_reuniao' => $reu,
                        'id_encaminhamento' => $ide,
                        'status' => 1,
                        'dt_inicio' => $data_depois,
                    ]);

                    // Insere no histórico a criação do atendimento
                    DB::table('log_atendimentos')->insert([
                        'id_referencia' => $idPTI,
                        'id_usuario' => session()->get('usuario.id_usuario'),
                        'id_acao' => 2, // foi criado
                        'id_origem' => 3, // Tratamento
                        'data_hora' => $data_atual
                    ]);
                } else { // Caso seja um PTD normal, ou PTD criado por Integral

                    $idPTI = DB::table('tratamento AS tr')->insertGetId([
                        'id_reuniao' => $reu,
                        'id_encaminhamento' => $ide,
                        'status' => 1,
                        'dt_inicio' => $data_depois,
                        'dt_fim' => $data_depois->copy()->addWeek(7) // Adiciona 7 semanas, pois o PTD tem 8 semanas de duração,
                    ]);

                    // Insere no histórico a criação do atendimento
                    DB::table('log_atendimentos')->insert([
                        'id_referencia' => $idPTI,
                        'id_usuario' => session()->get('usuario.id_usuario'),
                        'id_acao' => 2, // foi criado
                        'id_origem' => 3, // Tratamento
                        'data_hora' => $data_atual
                    ]);
                }
            } elseif ($tratID->id_tipo_tratamento == 2 or $tratID->id_tipo_tratamento == 4) { // PTI ou PROAMO

                // Insere um tratamento infinito
                $idPROAMO = DB::table('tratamento AS tr')->insertGetId([
                    'id_reuniao' => $reu,
                    'id_encaminhamento' => $ide,
                    'status' => 1,
                    'dt_inicio' => $data_depois,
                ]);

                // Insere no histórico a criação do atendimento
                DB::table('log_atendimentos')->insert([
                    'id_referencia' => $idPROAMO,
                    'id_usuario' => session()->get('usuario.id_usuario'),
                    'id_acao' => 2, // foi criado
                    'id_origem' => 3, // Tratamento
                    'data_hora' => $data_atual
                ]);
            } elseif ($tratID->id_tipo_tratamento == 6) { // Tratamento Integral

                // Insere um tratamento com data para ser finalizado
                $idLimite =  DB::table('tratamento AS tr')->insertGetId([
                    'id_reuniao' => $reu,
                    'id_encaminhamento' => $ide,
                    'status' => 1,
                    'dt_inicio' => $data_depois,
                    'dt_fim' => $data_depois->copy()->addWeek(5) // Adiciona 5 semanas, pois o Integral tem 6 semanas de duração
                ]);

                // Insere no histórico a criação do atendimento
                DB::table('log_atendimentos')->insert([
                    'id_referencia' => $idLimite,
                    'id_usuario' => session()->get('usuario.id_usuario'),
                    'id_acao' => 2, // foi criado
                    'id_origem' => 3, // Tratamento
                    'data_hora' => $data_atual
                ]);
            } else {
                app('flasher')->addSuccess('O tratamento foi agendo com sucesso.');
                return redirect('/gerenciar-encaminhamentos');
            }

            // Atualiza o encaminhamento para agendado
            DB::table('encaminhamento AS enc')
                ->where('enc.id', $ide)
                ->update([
                    'status_encaminhamento' => 2,
                ]);

            // Insere no histórico a criação do atendimento
            DB::table('log_atendimentos')->insert([
                'id_referencia' => $ide,
                'id_usuario' => session()->get('usuario.id_usuario'),
                'id_acao' => 1, // mudou de Status para
                'id_observacao' => 2, // Agendado
                'id_origem' => 2, // Encaminhamento
                'data_hora' => $data_atual
            ]);
        } elseif ($dia_atual > $dia_semana or $dia_atual == $dia_semana) { // Caso o dia seja superior ao dia de hoje
            // Caso seja um tratamento PTD
            if ($tratID->id_tipo_tratamento == 1) {
                //Caso ele tenha uma entrevista ou tratamento PTI ou PROAMO, é criado um tratamento permanente
                if ((in_array(2, $countTratamentos) or in_array(4, $countTratamentos)) or (in_array(4, $countEntrevistas) or (in_array(6, $countEntrevistas))) or $tfiInfinito) {

                    $idPermanente = DB::table('tratamento AS tr')->insertGetId([
                        'id_reuniao' => $reu,
                        'id_encaminhamento' => $ide,
                        'status' => 1,
                        'dt_inicio' => $data_antes,
                    ]);

                    // Insere no histórico a criação do atendimento
                    DB::table('log_atendimentos')->insert([
                        'id_referencia' => $idPermanente,
                        'id_usuario' => session()->get('usuario.id_usuario'),
                        'id_acao' => 2, // foi criado
                        'id_origem' => 3, // Tratamento
                        'data_hora' => $data_atual
                    ]);
                } else { // Caso seja um PTD normal, ou PTD criado por Integral
                    $idTempo = DB::table('tratamento AS tr')->insertGetId([
                        'id_reuniao' => $reu,
                        'id_encaminhamento' => $ide,
                        'status' => 1,
                        'dt_inicio' => $data_antes,
                        'dt_fim' => $data_antes->copy()->addWeek(7) // Adiciona 8 semanas, pois o PTD tem 8 semanas de duração,
                    ]);

                    // Insere no histórico a criação do atendimento
                    DB::table('log_atendimentos')->insert([
                        'id_referencia' => $idTempo,
                        'id_usuario' => session()->get('usuario.id_usuario'),
                        'id_acao' => 2, // foi criado
                        'id_origem' => 3, // Tratamento
                        'data_hora' => $data_atual
                    ]);
                }
            } elseif ($tratID->id_tipo_tratamento == 2 or $tratID->id_tipo_tratamento == 4) { // PTI ou PROAMO

                // Insere um tratamento infinito
                $idPTI = DB::table('tratamento AS tr')->insertGetId([
                    'id_reuniao' => $reu,
                    'id_encaminhamento' => $ide,
                    'status' => 1,
                    'dt_inicio' => $data_antes,
                ]);

                // Insere no histórico a criação do atendimento
                DB::table('log_atendimentos')->insert([
                    'id_referencia' => $idPTI,
                    'id_usuario' => session()->get('usuario.id_usuario'),
                    'id_acao' => 2, // foi criado
                    'id_origem' => 3, // Tratamento
                    'data_hora' => $data_atual
                ]);
            } elseif ($tratID->id_tipo_tratamento == 6) { // Tratamento Integral

                // Insere um tratamento com data para ser finalizado
                $idIntegral = DB::table('tratamento AS tr')->insertGetId([
                    'id_reuniao' => $reu,
                    'id_encaminhamento' => $ide,
                    'status' => 1,
                    'dt_inicio' => $data_antes,
                    'dt_fim' => $data_antes->copy()->addWeek(5) // Adiciona 6 semanas, pois o Integral tem 6 semanas de duração
                ]);

                // Insere no histórico a criação do atendimento
                DB::table('log_atendimentos')->insert([
                    'id_referencia' => $idIntegral,
                    'id_usuario' => session()->get('usuario.id_usuario'),
                    'id_acao' => 2, // foi criado
                    'id_origem' => 3, // Tratamento
                    'data_hora' => $data_atual
                ]);
            } else {
                app('flasher')->addSuccess('O tratamento foi agendo com sucesso.');
                return redirect('/gerenciar-encaminhamentos');
            }

            // Atualiza o encaminhamento para agendado
            DB::table('encaminhamento AS enc')
                ->where('enc.id', $ide)
                ->update([
                    'status_encaminhamento' => 2,
                ]);
            // Insere no histórico a criação do atendimento
            DB::table('log_atendimentos')->insert([
                'id_referencia' => $ide,
                'id_usuario' => session()->get('usuario.id_usuario'),
                'id_acao' => 1, // mudou de Status para
                'id_observacao' => 2, // Agendado
                'id_origem' => 2, // Encaminhamento
                'data_hora' => $data_atual
            ]);
        } else {
            app('flasher')->addDanger('Ocorreu um erro inesperado!');
        }

        app('flasher')->addSuccess('O tratamento foi agendo com sucesso.');
        return redirect('/gerenciar-encaminhamentos');
    }

    public function visualizar($ide)
    {

        // Devolve o ID pessoa daquele encaminhamento, para buscar outros encaminhamentos, mesmo que não conectados
        $pessoa = DB::table('encaminhamento')
            ->leftJoin('atendimentos', 'encaminhamento.id_atendimento', 'atendimentos.id')
            ->where('encaminhamento.id', $ide)
            ->first('id_assistido');

        // Traz todas as informações da view exceto o header com nome, e o footer com as faltas
        $result = DB::table('encaminhamento AS enc')
            ->select(
                'at.id AS ida', // ID atendimento, usado em Dados Atendimento Fraterno
                'at.dh_inicio', // Datetime de Inicio do atendimento
                'at.dh_fim', // Datetime de fim do atendimento
                'enc.id AS ide',
                'gr.nome AS nomeg', // Nome do grupo, mostrado em Dados do Encaminhamento
                'p1.dt_nascimento', // Data de Nascimento Assistido usado em header
                'p1.nome_completo AS nm_1', // Nome do Assistido usado em header
                'p2.nome_completo as nm_2', // Nome do representante, usado em Dados do Atendimento Fraterno
                'p4.nome_completo AS nm_4', // Nome do Atendente, usado em Dados do Atendimento Fraterno
                'p1.id as id_pessoa',
                'pa.nome', // Parentesco do representante com o Assistido (Ex.: Pai, Irmão)
                'rm.h_inicio AS rm_inicio', // Inicio do Cronograma do Tratamento Marcado
                'td.nome as nomedia', // Utilizado em Dados Encaminhamento para o Dia do Grupo
                'tsa.descricao AS tst', // Status do atendimento, em String
                'tse.descricao AS tsenc', // Status do encaminhamento, em String
                'tm.tipo AS tpmotivo', // Motivo de cancelamento do encaminhamento
                'tr.id as idt',
                'tr.dt_inicio', // Inicio Real do Tratamento
                'tr.dt_fim as final', // Final do Tratamento
                'tt.descricao AS desctrat', // Tipo de tratamento, usado em Dados do Encaminhamento (Ex.: Passe de Tratamento Desobsessivo)
                'tx.tipo', // Sexo do assistido, usado no header
            )
            ->leftJoin('tipo_status_encaminhamento AS tse', 'enc.status_encaminhamento', 'tse.id')
            ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
            ->leftJoin('tipo_motivo AS tm', 'enc.motivo', 'tm.id')
            ->leftjoin('tratamento AS tr', 'enc.id', 'tr.id_encaminhamento')
            ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
            ->leftjoin('grupo AS gr', 'rm.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as td', 'rm.dia_semana', 'td.id')
            ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
            ->leftJoin('tipo_status_atendimento AS tsa', 'at.status_atendimento', 'tsa.id')
            ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
            ->leftjoin('associado AS ass', 'at.id_atendente', 'ass.id')
            ->leftjoin('pessoas AS p4', 'ass.id_pessoa', 'p4.id')
            ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
            ->leftJoin('tp_sexo AS tx', 'p1.sexo', 'tx.id')
            ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
            ->Where('enc.id', $ide)
            ->first();




        $encaminhamentosAlternativos = DB::table('encaminhamento as enc')
            ->select(
                'enc.id as ide',
                'gr.nome',
                'rm.h_inicio',
                'td.nome as dia',
                'tr.id as idt',
                'tr.dt_inicio',
                'tr.dt_fim',
                'tt.descricao',
                'tse.descricao as status'
            )
            ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
            ->leftJoin('tipo_status_encaminhamento AS tse', 'enc.status_encaminhamento', 'tse.id')
            ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
            ->leftjoin('tratamento AS tr', 'enc.id', 'tr.id_encaminhamento')
            ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
            ->leftjoin('grupo AS gr', 'rm.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as td', 'rm.dia_semana', 'td.id')
            ->where('at.id_assistido', $pessoa->id_assistido) // Todos daquele assistido
            ->where('enc.id_tipo_encaminhamento', 2) // Encaminhamento de Tratamento
            ->whereNot('enc.id_tipo_tratamento', 3) // Remove da lista o PTH (Passe de Tratamento de Harmonização)
            ->where('enc.status_encaminhamento', '<', 3)
            ->whereNot('enc.id', $ide)
            ->get();

        $emergencia = DB::table('presenca_cronograma as dt')
            ->select(
                'dt.id AS idp',
                'dt.presenca',
                'dc.data',
                'gp.nome'
            )
            ->leftJoin('tratamento as tr', 'dt.id_tratamento', 'tr.id')
            ->leftJoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftJoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
            ->leftJoin('dias_cronograma as dc', 'dt.id_dias_cronograma', 'dc.id')
            ->leftJoin('cronograma AS rm1', 'dc.id_cronograma', 'rm1.id')
            ->leftJoin('grupo AS gp', 'rm1.id_grupo', 'gp.id')
            ->where('dt.id_pessoa', '=', $result->id_pessoa)
            ->whereNull('dt.id_tratamento')
            ->get()
            ->toArray();


        // Retorna todos os dados de presença do encaminhamento atual
        $list = DB::table('presenca_cronograma as pc')
            ->select('pc.id as idp', 'dc.data', 'pc.presenca', 'gr.nome')
            ->leftJoin('dias_cronograma as dc', 'id_dias_cronograma', 'dc.id')
            ->leftJoin('cronograma as cr', 'dc.id_cronograma', 'cr.id')
            ->leftJoin('grupo as gr', 'cr.id_grupo', 'gr.id')
            ->leftJoin('tratamento as tr', 'pc.id_tratamento', 'tr.id')
            ->where('tr.id_encaminhamento', $ide)
            ->orderBy('dc.data', 'desc')
            ->get();

        // Conta a quantidade de faltas do encaminhamento atual
        $faul = DB::table('tratamento AS tr')
            ->select('dt.presenca')
            ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
            ->leftJoin('presenca_cronograma AS dt', 'tr.id', 'dt.id_tratamento')
            ->where('enc.id', $ide)
            ->where('dt.presenca', 0)
            ->count();


        return view('recepcao-integrada.historico-encaminhamento', compact('emergencia', 'result', 'list', 'faul', 'encaminhamentosAlternativos'));
    }
    public function inative(Request $request, $ide)
    {

        $dt_hora = Carbon::now();
        $today = Carbon::today()->format('Y-m-d');

        $idAssistido = DB::table('encaminhamento')->where('encaminhamento.id', $ide)
            ->leftJoin('atendimentos', 'encaminhamento.id_atendimento', 'atendimentos.id')
            ->pluck('atendimentos.id_assistido')->toArray();

        // Retorna todos os IDs dos encaminhamentos de tratamento
        $countTratamentos = DB::table('encaminhamento as enc')
            ->select('id_tipo_tratamento', 't.dt_fim', 't.id')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->leftJoin('tratamento as t', 'enc.id', 't.id_encaminhamento')
            ->where('enc.id_tipo_encaminhamento', 2) // Encaminhamento de Tratamento
            ->where('at.id_assistido', $idAssistido)
            ->where('enc.status_encaminhamento', '<', 3) // 3 => Finalizado, Traz apenas os ativos (Para Agendar, Agendado)
            ->whereNot('enc.id', $ide) // Exclui o tratamento de agora
            ->get()->toArray();

        // Retorna todos os IDs dos encaminhamentos de entrevista
        $countEntrevistas = DB::table('encaminhamento as enc')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->where('enc.id_tipo_encaminhamento', 1) // Encaminhamento de Entrevista
            ->where('at.id_assistido', $idAssistido)
            ->where(function ($query) {
                $query->where(function ($subQuery) { // Caso seja um PROAMO aguardando requsitos
                    $subQuery->where('enc.id_tipo_entrevista', 6); // Entrevista PROAMO
                    $subQuery->where('enc.status_encaminhamento', 5); // Aguardando Requisitos
                });
                $query->orWhere(function ($subQuery) { // Caso seja qualquer outro encaminhamento ativo
                    $subQuery->whereNot('enc.id_tipo_entrevista', 6); // Entrevista PROAMO
                    $subQuery->where('enc.status_encaminhamento', '<', 3); // 3 => Finalizado, Traz apenas os ativos (Para Agendar, Agendado)
                });
            })
            ->pluck('id_tipo_entrevista')->toArray();


        $tfiInfinito = array_search(6, array_column($countTratamentos, 'id_tipo_tratamento')); // Busca, caso exista, a array key dos dados de Integral
        $tfiInfinito = $tfiInfinito ? $countTratamentos[$tfiInfinito] : false; // Caso tenha encontrado, retorna os dados de Integral
        $tfiInfinito = $tfiInfinito ? ($tfiInfinito->dt_fim == null and $tfiInfinito->id != null and in_array(6, array_column($countTratamentos, 'id_tipo_tratamento'))) : false; // Confere se é um Integral Permanente caso os dados existam
        // Essa é a clausula para um PTD infinito que está sendo apoiado em outro tratamento
        //      Tratamento PTI                                                         Entrevista NUTRES (PTI)                Tratamento PROAMO                                             Entrevista DIAMO (PROAMO)   Tratamento Integral Permanente
        if (in_array(2, array_column($countTratamentos, 'id_tipo_tratamento')) or in_array(4, $countEntrevistas) or in_array(4, array_column($countTratamentos, 'id_tipo_tratamento')) or in_array(6, $countEntrevistas) or $tfiInfinito) {

            // Não executa nenhum comando especial, apenas o padrão do método

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
            if ($ptdAtivoInfinito) {

                $dataFim = Carbon::today()->weekday($ptdAtivo->dia_semana);

                // Inativa o PTD infinito
                DB::table('tratamento')
                    ->where('id', $ptdAtivo->id)
                    ->update([
                        'dt_fim' => $dataFim,
                        'status' => 6, // Inativado
                    ]);

                // Insere no histórico a criação do atendimento
                DB::table('log_atendimentos')->insert([
                    'id_referencia' => $ptdAtivo->id,
                    'id_usuario' => session()->get('usuario.id_usuario'),
                    'id_acao' => 1, // mudou de Status para
                    'id_origem' => 3, // Tratamento
                    'id_observacao' => 6, // Inativado
                    'data_hora' => $dt_hora
                ]);

                // Inativa o encaminhamento do PTD infinito
                DB::table('encaminhamento')
                    ->where('id', $ptdAtivo->ide)
                    ->update([
                        'status_encaminhamento' => 4 // Inativado
                    ]);

                // Insere no histórico a criação do atendimento
                DB::table('log_atendimentos')->insert([
                    'id_referencia' => $ptdAtivo->ide,
                    'id_usuario' => session()->get('usuario.id_usuario'),
                    'id_acao' => 1, // mudou de Status para
                    'id_origem' => 2, // Encaminhamento
                    'id_observacao' => 4, // Inativado
                    'data_hora' => $dt_hora
                ]);
            }
        }

        DB::table('encaminhamento AS enc') // Atualiza o encaminhamento para cancelado
            ->where('enc.id', $ide)
            ->update([
                'status_encaminhamento' => 4,
                'motivo' => $request->input('motivo'), // Vem de um select na view, os dados vem da variável $motivo do metodo index()
            ]);

        // Insere no histórico a criação do atendimento
        DB::table('log_atendimentos')->insert([
            'id_referencia' => $ide,
            'id_usuario' => session()->get('usuario.id_usuario'),
            'id_acao' => 1, // mudou de Status para
            'id_origem' => 2, // Encaminhamento
            'id_observacao' => 4, // Inativado
            'data_hora' => $dt_hora
        ]);

        // Caso esse encaminhamento tenha um tratamento
        $tratamento = DB::table('tratamento')
            ->where('id_encaminhamento', $ide);


        if ($tratamento && $tratamento->exists()) {
            $firstTratamento = $tratamento->first();

            if ($firstTratamento) {
                $idTratamento = $firstTratamento->id;

                $tratamento->update([
                    'dt_fim' => $today,
                    'status' => 6, // Inativado
                ]);
            }



            // Insere no histórico a criação do atendimento
            DB::table('log_atendimentos')->insert([
                'id_referencia' => $idTratamento,
                'id_usuario' => session()->get('usuario.id_usuario'),
                'id_acao' => 1, // mudou de Status para
                'id_origem' => 3, // Tratamento
                'id_observacao' => 6, // Inativado
                'data_hora' => $dt_hora
            ]);
        }

        app('flasher')->addSuccess('O encaminhamento foi inativado.');

        return redirect('/gerenciar-encaminhamentos');
    }

    // Metodo do botão Alterar Grupo na Index
    public function escolherGrupo($id)
    {
        try {
            $hoje = Carbon::now()->format('Y-m-d'); // Retorna o dia de Hoje no formato de banco de dados

            // Traz todos os dados da pessoa que foi encaminhada, usado na view para visualizar apenas
            $result = DB::table('encaminhamento AS enc')
                ->select(
                    'enc.id AS ide',
                    'enc.id_tipo_encaminhamento',
                    'dh_enc',
                    'enc.id_atendimento',
                    'enc.status_encaminhamento',
                    'enc.id_tipo_tratamento',
                    'tse.descricao AS tsenc',
                    'enc.id_tipo_tratamento',
                    'id_tipo_entrevista',
                    'at.id AS ida',
                    'at.id_assistido',
                    'p1.nome_completo AS nm_1',
                    'at.id_representante as idr',
                    'p2.nome_completo as nm_2',
                    'pa.id AS pid',
                    'pa.nome',
                    'pr.id AS prid',
                    'pr.descricao AS prdesc',
                    'pr.sigla AS prsigla',
                    'tt.descricao AS desctrat'
                )
                ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
                ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
                ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
                ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
                ->leftJoin('tipo_status_encaminhamento AS tse', 'enc.status_encaminhamento', 'tse.id')
                ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
                ->where('enc.id', $id)
                ->first();

            // Traz todos os dados de todos os dias de tratamento
            $dadosDias = DB::table('cronograma AS reu')
                ->select(DB::raw('count(*) as numeroCronograma, sum(max_atend) as maximoVagas'), 'reu.dia_semana as dia', 'td.nome as dia_semana') // Numero de grupos, Numero total de vagas, e dia da semana
                ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                ->leftJoin('tipo_dia as td', 'reu.dia_semana', 'td.id')
                ->where(function ($query) use ($hoje) {
                    $query->whereRaw('reu.data_fim > ?', [$hoje])->orWhereNull('reu.data_fim'); // Traz apenas grupos ativos
                })
                ->where(function ($query) {
                    $query->where('reu.modificador', NULL); // Sem modificador nenhum
                    $query->orWhere('reu.modificador', '<>', 4); // Grupo que não esteja Em Férias
                })
                ->where('reu.id_tipo_tratamento', $result->id_tipo_tratamento)
                ->groupBy('dia', 'td.nome')
                ->orderBy('dia') // Ordena para que os cards saiam certos
                ->get();

            foreach ($dadosDias  as $key => $dadoDia) {

                // Calcula quantos tratamentos ativos tem naquele dia e conta
                $bufferVagas = DB::table('tratamento AS tr')
                    ->leftJoin('cronograma AS reu', 'tr.id_reuniao', 'reu.id')
                    ->select(DB::raw("($dadoDia->maximovagas - COUNT(CASE WHEN tr.status < 3 THEN tr.id END)) as trat"))
                    ->where('reu.id_tipo_tratamento', $result->id_tipo_tratamento)
                    ->where('reu.dia_semana', $dadoDia->dia)
                    ->pluck('trat')
                    ->toArray();

                $dadosDias[$key]->vagas = current($bufferVagas); // Adiciona o valor do buffer para a variavel principal
            }

            return view('recepcao-integrada.agendar-grupo-tratamento', compact('hoje', 'result', 'dadosDias'));
        } catch (\Exception $e) {
            $code = $e->getCode();
            return view('tratamento-erro.erro-inesperado', compact('code'));
        }
    }

    // Escolher o grupo de um dia, Botão de Editar Grupo
    public function escolherHorario(Request $request, $ide)
    {


        try {

            $hoje = Carbon::today(); // Data de Hoje
            $dia = intval($request->dia); // Pega o dia do request
            $ide = intval($ide); // Pega o id do encaminhamento passado por método GET

            // Descobre o tipo de tratamento do encaminhamento atual
            $tp_trat = DB::table('encaminhamento AS enc')
                ->select('enc.id_tipo_tratamento')
                ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
                ->where('enc.id', $ide)->value('enc.id_tipo_tratamento');

            // Retorna todos os dados do encaminhamento, para o header com informaçoes para confirmação visual
            $result = DB::table('encaminhamento AS enc')
                ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'dh_enc', 'enc.id_atendimento', 'enc.status_encaminhamento', 'tse.descricao AS tsenc', 'enc.id_tipo_tratamento', 'id_tipo_entrevista', 'at.id AS ida', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante as idr', 'p2.nome_completo as nm_2', 'pa.id AS pid', 'pa.nome', 'pr.id AS prid', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla', 'tt.descricao AS desctrat')
                ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
                ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
                ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
                ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
                ->leftJoin('tipo_status_encaminhamento AS tse', 'enc.status_encaminhamento', 'tse.id')
                ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
                ->where('enc.id', $ide)
                ->first();

            // Devolve todos os dados dos grupos que podem ser selecionados
            $trata = DB::table('cronograma AS reu')
                ->select(
                    DB::raw('(reu.max_atend - (select count(*) from tratamento tr where tr.id_reuniao = reu.id and tr.status < 3)) as trat'),
                    'p.nome_completo',
                    'reu.id AS idr',
                    'gr.nome AS nomeg',
                    'reu.dia_semana',
                    'reu.id_sala',
                    'reu.id_tipo_tratamento',
                    'reu.h_inicio',
                    'td.nome AS nomed',
                    'reu.h_fim',
                    'reu.max_atend',
                    'gr.status_grupo AS idst',
                    'tsg.descricao AS descst',
                    'tst.descricao AS tstd',
                    'sa.numero',
                    'tor.descricao as des'
                )
                ->leftJoin('tratamento AS tr', 'reu.id', 'tr.id_reuniao')
                ->leftJoin('tipo_tratamento AS tst', 'reu.id_tipo_tratamento', 'tst.id')
                ->leftJoin('salas AS sa', 'reu.id_sala', 'sa.id')
                ->leftJoin('tipo_dia AS td', 'reu.dia_semana', 'td.id')
                ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                ->leftJoin('tipo_status_grupo AS tsg', 'gr.status_grupo', 'tsg.id')
                ->leftJoin('membro AS me', 'reu.id', 'me.id_cronograma')
                ->leftJoin('associado as ass', 'me.id_associado', 'ass.id')
                ->leftJoin('pessoas as p', 'ass.id_pessoa', 'p.id')
                ->leftJoin('tipo_observacao_reuniao as tor', 'reu.observacao', 'tor.id')
                ->where(function ($query) use ($hoje) {
                    $query->whereRaw('reu.data_fim > ?', [$hoje])->orWhereNull('reu.data_fim');
                })
                ->where(function ($query) {
                    $query->where('reu.modificador', NULL);
                    $query->orWhere('reu.modificador', '<>', 4);
                })
                ->where('me.id_funcao', 1) // XXX Busca apenas dirigentes, gera um bug de duplicação caso um grupo tenha mais de um dirigente
                ->whereNull('me.dt_fim')
                ->where('reu.dia_semana', $dia)
                ->where('reu.id_tipo_tratamento', $tp_trat)
                ->orWhere('tr.status', null)
                ->where('tr.status', '<', 3)
                ->groupBy('p.nome_completo', 'reu.h_inicio', 'reu.max_atend', 'reu.id', 'gr.nome', 'td.nome', 'gr.status_grupo', 'tsg.descricao', 'tst.descricao', 'sa.numero', 'tor.descricao')
                ->orderBy('h_inicio')
                ->get();

            // Validação de erro para dias sem grupo
            if (sizeof($trata) == 0) {
                app('flasher')->addWarning('Não existem grupos para este dia');
                return redirect()->back();
            }


            return view('recepcao-integrada.agendar-horario-tratamento', compact('result', 'trata', 'dia'));
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('tratamento-erro.erro-inesperado', compact('code'));
        }
    }
    public function trocarGrupo(Request $request, $ide)
    {


        $reu = intval($request->reuniao); // Guarda em uma variável o ID do cronograma escolhido
        $countVagas = DB::table('tratamento')->where('id_reuniao', "$reu")->where('status', '<', '3')->count(); // Conta a quantidade de tratamentos ativos nessa reunião
        $maxAtend = DB::table('cronograma')->where('id', "$reu")->first(); // Usado para retornar o número máximo de assistidos da reunião
        $tratID = DB::table('encaminhamento')->where('id',  $ide)->first(); // Retorna o tipo de tratamento, usado para validação do número de vagas
        $idt = DB::table('tratamento')->where('id_encaminhamento', $ide)->first(); // Pega os dados do tratamento, usados: DT_FIM e ID

        $data_ontem = Carbon::yesterday();
        $data_hoje = Carbon::today();
        $dt_hora = Carbon::now();

        $dia_inicio = Carbon::createFromFormat('Y-m-d G:i:s', "$idt->dt_inicio 00:00:00"); // Força um campo DATE se transformar em um DATETIME


        if ($data_hoje > (clone $dia_inicio)->weekday($maxAtend->dia_semana)) { // Caso o dia seja menor ou igual o esperado
            $dia_inicio->weekday($maxAtend->dia_semana)->addWeek();
        } elseif ((clone $dia_inicio)->weekday($maxAtend->dia_semana)->subWeek() > $data_hoje) { // Caso o assistido não precisa começar na semana que vem
            $dia_inicio->weekday($maxAtend->dia_semana)->subWeek();
        } else { // Caso não necessite mudar a semana, apenas o dia
            $dia_inicio->weekday($maxAtend->dia_semana);
        }


        // Se o tratamento não for permanente
        if ($idt->dt_fim) {


            $dia_fim = Carbon::createFromFormat('Y-m-d G:i:s', "$idt->dt_fim 00:00:00"); // Força um campo DATE se transformar em um DATETIME
            $dia_fim->weekday($maxAtend->dia_semana); // Descobre o dia na semana da DT_FIM correspondente ao dia da semana selecionado pelo grupo



            // Caso não seja um tratamento PTD e o número de tratamentos seja maior ou igual ao número máximo de assisitidos
            if ($tratID->id_tipo_tratamento != 1 and $countVagas >= $maxAtend->max_atend) {
                app('flasher')->addError('Número de vagas insuficientes');
                return redirect()->back();
            }

            // Caso a data de hoje esteja na mesma semana que a final do tratamento e o dia escolhido seja anterior a hoje
            if ($data_hoje->weekOfYear == $dia_fim->weekOfYear and $data_hoje->diffInDays($dia_fim, false) < 0) {

                app('flasher')->addError('Operação Impossível! Esta é a semana final do assistido');
                return redirect()->back();

                // Caso a data fim seja em um domingo da semana passada (Não reconheço a utilidade, possívelmente inútil)
            } elseif ($data_hoje->weekOfYear == ($dia_fim->weekOfYear + 1) and $data_hoje->diffInDays($dia_fim, false) < 0 and $maxAtend->dia_semana == 0) {
                app('flasher')->addError('Operação Impossível! Esta é a semana final do assistido');
                return redirect()->back();
            }
        }


        $data = date("Y-m-d H:i:s");

        // Atualiza a data fim da última troca de grupo ativa, caso exista
        DB::table('tratamento_grupos')
            ->where('dt_fim', null)
            ->where('id_tratamento', $idt->id)
            ->update([
                'dt_fim' => $data_ontem,
            ]);

        // Existe uma nova troca de grupo ativa
        DB::table('tratamento_grupos')
            ->insert([
                'id_cronograma' => $reu,
                'id_tratamento' => $idt->id,
                'dt_inicio' => $data,
            ]);

        // Atualiza a reunião do tratamento para a nova
        if ($idt->dt_fim) {

            // Busca o Tratamento a ser editado
            $editDtFim = DB::table('tratamento')
                ->where('id_encaminhamento', $ide);

            // Caso algum tratamento seja encontrado
            if ($editDtFim) {

                if ($idt->dt_inicio >= $data_hoje) {
                    $idEditDtFim = $editDtFim->first()->id;
                    $editDtFim->update([
                        'id_reuniao' => $reu,
                        'dt_inicio' => $dia_inicio, // remarca a data inicio
                        'dt_fim' => $dia_fim // Remarca a data fim
                    ]);
                } else {
                    $idEditDtFim = $editDtFim->first()->id;
                    $editDtFim->update([
                        'id_reuniao' => $reu,
                        'dt_fim' => $dia_fim // Remarca a data fim
                    ]);
                }

                // Insere no histórico a criação do atendimento
                DB::table('log_atendimentos')->insert([
                    'id_referencia' => $idEditDtFim,
                    'id_usuario' => session()->get('usuario.id_usuario'),
                    'id_acao' => 9, // mudou de Cronograma
                    'id_origem' => 3, // Tratamento
                    'data_hora' => $dt_hora
                ]);
            }
        } else {
            $editDtFim = DB::table('tratamento')
                ->where('id_encaminhamento', $ide);

            if ($editDtFim) {

                if ($idt->dt_inicio > $data_hoje) {
                    $idEditDtFim = $editDtFim->first()->id;
                    $editDtFim->update([
                        'id_reuniao' => $reu,
                        'dt_inicio' => $dia_inicio, // remarca a data inicio
                    ]);
                } else {
                    $idEditDtFim = $editDtFim->first()->id;
                    $editDtFim->update([
                        'id_reuniao' => $reu,
                        'dt_inicio' => $dia_inicio, // remarca a data inicio
                    ]);
                }


                // Insere no histórico a criação do atendimento
                DB::table('log_atendimentos')->insert([
                    'id_referencia' => $idEditDtFim,
                    'id_usuario' => session()->get('usuario.id_usuario'),
                    'id_acao' => 9, // mudou de Cronograma
                    'id_origem' => 3, // Tratamento
                    'data_hora' => $dt_hora
                ]);
            }
        }

        app('flasher')->addSuccess('Troca efetuada com sucesso!');
        return redirect('/gerenciar-encaminhamentos');
    }
}
