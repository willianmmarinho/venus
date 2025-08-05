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

class Faltas implements ShouldQueue
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

        // Retorna todos os tratamentos que já tomaram presença hoje
        $inseridos = DB::table('presenca_cronograma as pc')
            ->leftJoin('dias_cronograma as dc', 'pc.id_dias_cronograma', 'dc.id')
            ->whereNot('id_tratamento', null)
            ->where('dc.data', $data_atual)
            ->pluck('id_tratamento')
            ->toArray();

        // Traz todos os tratamentos que trocaram de grupo hoje, usado para proteger contra faltas desnecessárias
        $idsTrocaDeGrupo = DB::table('tratamento_grupos')
            ->leftJoin('cronograma', 'tratamento_grupos.id_cronograma', 'cronograma.id')
            ->where('tratamento_grupos.dt_inicio', $data_atual)
            ->where('dia_semana', $dia_atual)
            ->pluck('id_tratamento');

        // Retorna todos os tratamentos ativos, iniciados, do dia de hoje, ativas, que não estejam de férias, inseridos ou que trocaram de grupo hoje
        $lista = DB::table('tratamento AS tr')
            ->select('tr.id', 'tr.id_reuniao', 'enc.id_tipo_tratamento', 'tr.dt_fim')
            ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
            ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
            ->where('tr.dt_inicio', '<=', $data_atual) // Iniciados
            ->where('tr.status', '<', 3) //Apenas ativos
            ->where('rm.dia_semana', $dia_atual)
            ->where(function ($query) use ($data_atual) { // Cronogramas ativos
                $query->whereRaw("data_fim > ?", [$data_atual])
                    ->orWhereNull('data_fim');
            })
            ->where(function ($query) {
                $query->where('rm.modificador', NULL); // Sem modificador algum
                $query->orWhere('rm.modificador', '<>', 4); // Em férias
            })
            ->whereNotIn('tr.id', $inseridos) // Inseridos de hoje
            ->whereNotIn('tr.id', $idsTrocaDeGrupo) // Que trocaram de grupo hoje
            ->get()
            ->toArray();

        // Busca a variável das faltas, e retorna os IDs de todos os tratamentos integral
        $idsIntegral =  array_filter(array_column($lista, 'id'), fn($k) => in_array($k, array_keys(array_column($lista, 'id_tipo_tratamento'), 6)), ARRAY_FILTER_USE_KEY);

        // Conta quantas faltas cada tratamento tem com base no id_tratamentoc
        $faltasIntegral = DB::table('presenca_cronograma')
            ->select('id_tratamento', DB::raw('COUNT(presenca)'))
            ->whereIn('id_tratamento', $idsIntegral)
            ->where('presenca', false)
            ->groupBy('id_tratamento')
            ->get()
            ->toArray();

        foreach ($lista as $item) {



            DB::beginTransaction();
            try {

                // Caso o tratamento seja Integral e não seja infinito, usado para a extensão de tempo por falta
                if($item->id_tipo_tratamento == 6 and $item->dt_fim != null){
            
                    // Caso o tratamento esteja levando da primeira à terceira falta, excluindo todas as adiante
                    if(   !in_array($item->id,array_column($faltasIntegral, 'id_tratamento')) or $faltasIntegral[array_search($item->id, array_column($faltasIntegral, 'id_tratamento'))]->count < 3   ){

                        // Adiciona uma semana a data fim e insere na tabela
                        $novaData = Carbon::parse($item->dt_fim)->addWeek();
                        DB::table('tratamento')
                        ->where('id', $item->id)
                        ->update([
                            'dt_fim' => $novaData
                        ]);
    
                    }

                }

                // Descobre o cronograma que se reune hoje correspondente
                $id_dia_cronograma =  DB::table('dias_cronograma')
                    ->select('id')
                    ->where('data', $data_atual)
                    ->where('id_cronograma', $item->id_reuniao)
                    ->first();
    
                // Insere a falta de acordo com os dados
                DB::table('presenca_cronograma AS dt')
                    ->leftJoin('tratamento AS tr', 'dt.id', 'dt.id_tratamento')
                    ->insert([
                        'id_dias_cronograma' => $id_dia_cronograma->id,
                        'id_tratamento' => $item->id,
                        'presenca' => false
                    ]);


                DB::commit();
            } catch (\Exception $e) {

                echo "Erro no tratamento: " . $item->id . " Codigo: " . $e->getCode() . "\n";
                DB::rollBack();
            }



        }
    }
}
