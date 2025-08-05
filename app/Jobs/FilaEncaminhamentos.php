<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FilaEncaminhamentos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    // Cancelar Entrevista
    public function inativar(String $id)
    {

        $data = date("Y-m-d");
        $dt_hora = Carbon::now();

        // Insere o fato de Cancelamento de Entrevista

        // Usado para retornar o ID assistido para validações
        $idAssistido = DB::table('encaminhamento')->where('encaminhamento.id', $id)
            ->leftJoin('atendimentos', 'encaminhamento.id_atendimento', 'atendimentos.id')
            ->pluck('atendimentos.id_assistido')->toArray();


        $motivo_entrevista = 13; // Salva em uma Variável o Id_motivo Excedeu Espera

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
                'id_origem' => 2, // Encaminhamento
                'id_observacao' => 4, // Entrevista Finalizada
                'data_hora' => $dt_hora
            ]);

            // Inativa a entrevista caso encontre alguma
            $entrevista = DB::table('entrevistas')
                ->where('id_encaminhamento', $id);

            if ((clone $entrevista)->first()) {
                $idEntrevista = (clone $entrevista)->first()->id;
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
            }
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
            if ((clone $inativEntrevista)->first()) {
                $idInativEntrevista = (clone $inativEntrevista)->first()->id;
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
    }


    public function inative(String $ide)
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
            ->where('enc.status_encaminhamento', '<', 3) // 3 => Finalizado, Traz apenas os ativos (Para Agendar, Agendado)
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
                'motivo' => 13, // Vem de um select na view, os dados vem da variável $motivo do metodo index()
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


        if ((clone $tratamento)->first()) {
            $idTratamento = (clone $tratamento)->first()->id;

            $tratamento->update([
                'dt_fim' => $today,
                'status' => 6, // Inativado
            ]);

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

        //   app('flasher')->addSuccess('O encaminhamento foi inativado.');

        return redirect('/gerenciar-encaminhamentos');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = Carbon::today()->subDay(15);


        // Encontra todas as entrevistas PROAMO aguardando requisitos
        $proamoRequisitos = DB::table('encaminhamento as enc')
            ->select('at.dh_chegada', 'at.id_assistido', 'enc.id')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->where('enc.id_tipo_entrevista', 6)
            ->where('enc.status_encaminhamento', 5)
            ->get()
            ->toArray();

        // Encontra todos os tratamentos atrelados a essas entrevistas
        $tratamentos = DB::table('tratamento as tr')
            ->select('tr.id', 'tr.dt_fim', 'at.id_assistido')
            ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->where('enc.id_tipo_tratamento', 1)
            ->where('tr.status', '<', 3)
            ->whereIn('at.id_assistido', array_column($proamoRequisitos, 'id_assistido'))
            ->orderBy('at.id_assistido')
            ->get()
            ->toArray();

        // Encontra todas os Encaminhamentos PTD Aguardando Agendamento
        $encaminhamentoPTD = DB::table('encaminhamento as enc')
            ->select('at.dh_chegada', 'at.id_assistido', 'enc.id')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->where('enc.id_tipo_tratamento', 1)
            ->where('enc.status_encaminhamento', 1)
            ->where('at.dh_chegada', '<', $data)
            ->get()
            ->toArray();

        // // Encontra todas as entrevistas exceto PROAMO,PTI E INTEGRAL
        // $encaminhamentoEntrevista = DB::table('encaminhamento as enc')
        //     ->select('at.dh_chegada', 'at.id_assistido', 'enc.id', 'enc.id_tipo_tratamento')
        //     ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
        //     ->where('id_tipo_encaminhamento', 1)
        //     ->whereNotIn('enc.id_tipo_entrevista', [6, 4, 5])
        //     ->where('enc.status_encaminhamento', 1)
        //     ->where('at.dh_chegada', '<', $data)
        //     ->get()
        //     ->toArray();


        foreach ($encaminhamentoPTD as $ptd) {
            $this->inative($ptd->id);
        }

        // foreach ($encaminhamentoEntrevista as $entrevista) {
        //     $this->inativar($entrevista->id);
        // }

        // foreach ($proamoRequisitos as $proamo) {
        //     $dh_chegada = Carbon::parse($proamo->dh_chegada)->format('Y-m-d');

        //     // Caso o assistido não tenha um tratamento
        //     if (!array_search($proamo->id_assistido, array_column($tratamentos, 'id_assistido'))) {

        //         // Confere se a dh_chegada da entrevista foi a mais de X tempo
        //         if ($dh_chegada < $data) {
        //             $this->inativar($proamo->id);
        //         }
        //     } else {

        //         Confere se a dt_fim do tratamento faz mais de X dias e a dh_chegada da entrevista faz mais de X dias
        //         if ($dh_chegada < $data and $tratamentos[array_search($proamo->id_assistido, array_column($tratamentos, 'id_assistido'))]->dt_fim < $data and $tratamentos[array_search($proamo->id_assistido, array_column($tratamentos, 'id_assistido'))]->dt_fim != null) {
        //             $this->inativar($proamo->id);
        //         }
        //    }
        // }
    }
}
