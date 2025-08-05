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

class FimSemanas implements ShouldQueue
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
        $ontem = Carbon::yesterday();

        // Busca os dados dos tratamentos finalizados
        $semanas = DB::table('tratamento as tr')
            ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
            ->whereNot('tr.dt_fim', null) // Retira os infinitos
            ->where('tr.dt_fim', '<=', $ontem) // Tempo finalizado
            ->where('enc.status_encaminhamento', 2) // Apenas agendados
            ->pluck('tr.id_encaminhamento')
            ->toArray();

        // Finaliza os tratamentos com as datas finalizadas
        DB::table('tratamento as tr')
            ->whereIn('id_encaminhamento', $semanas)
            ->update([
                'tr.status' => 4 // Finalizado
            ]);

        // Finaliza os encaminhamentos dos tratamentos finalizados
        DB::table('encaminhamento')
            ->whereIn('id', $semanas)
            ->update([
                'status_encaminhamento' => 3 // Finalizado
            ]);
    }
}
