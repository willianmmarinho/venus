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

class DiasCronogramaOntem implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $dia_hoje = Carbon::yesterday();
        $dia_semana_hoje = $dia_hoje->weekday();

        // Traz todos os crnogramas reunidos na data de hoje, usado para validação
        $incluidos = DB::table('dias_cronograma')->where('data', $dia_hoje)->pluck('id_cronograma')->toArray();

        // Retorna todos os cronogramas de hoje, que não tenham sido incluidos ainda, e estejam ativos
        $reunioes_hoje = DB::table('cronograma')->where('dia_semana', $dia_semana_hoje)
        ->whereNotIn('id', $incluidos)
        ->where(function($query) use ($dia_hoje) { // Cronogramas ativos
            $query->whereRaw("data_fim > ?", [$dia_hoje]) 
                  ->orWhereNull('data_fim');
        })
        ->get();

        // Insere todas as reuniões encontradas
        foreach($reunioes_hoje as $reuniao){
            DB::table('dias_cronograma')
            ->insert([
                'data' => $dia_hoje,
                'id_cronograma' => $reuniao->id,
            ]);
        }
    }
}
