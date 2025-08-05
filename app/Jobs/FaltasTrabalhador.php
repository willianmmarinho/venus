<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use function PHPUnit\Framework\isEmpty;

class FaltasTrabalhador implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $info;
    /**
     * Create a new job instance.
     */

    public function __construct() {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $data_atual = Carbon::yesterday();
        $dia_atual = $data_atual->weekday();

        // Retorna todas as presencas de membros já incluidas
        $inseridos = DB::table('presenca_membros as pm')
            ->leftJoin('dias_cronograma as dc', 'pm.id_dias_cronograma', 'dc.id')
            ->where('dc.data', $data_atual)
            ->pluck('id_membro')
            ->toArray();

        // Retorna todos os membros que não foram incluidos de um cronograma do dia de hoje
        $lista = DB::table('membro as m')
            ->leftJoin('cronograma as cro', 'm.id_cronograma', 'cro.id')
            ->where('cro.dia_semana', $dia_atual)
            ->where('dt_fim', null) // Apenas membros ativos
            ->where(function($query) use ($data_atual) { // Cronogramas ativos
                $query->whereRaw("data_fim > ?", [$data_atual]) 
                      ->orWhereNull('data_fim');
            })
            ->whereNotIn('m.id', $inseridos)
            ->select('m.id', 'm.id_cronograma')
            ->get();


        foreach ($lista as $item) {

            // Descobre o cronograma que se reune hoje correspondente
            $id_dia_cronograma =  DB::table('dias_cronograma')
                ->select('id')
                ->where('data', $data_atual)
                ->where('id_cronograma', $item->id_cronograma)
                ->first();

            // Insere a falta de acordo com os dados
            DB::table('presenca_membros')
                ->insert([
                    'id_dias_cronograma' => $id_dia_cronograma->id,
                    'id_membro' => $item->id,
                    'presenca' => false
                ]);
        }

        return;
    }
}
