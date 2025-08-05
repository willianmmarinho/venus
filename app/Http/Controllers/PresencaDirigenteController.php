<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Tipo_fato;
use Illuminate\Database\DBAL\TimestampType;
use Illuminate\Support\Carbon;


class PresencaDirigenteController extends Controller
{
    // Exibe a tela de dar presença
    public function index(Request $request)
    {

        $hoje = Carbon::today();

        //Traz todas as reuniões onde a pessoa logada é Dirigente ou Sub-dirigente
        $reunioesDirigentes = DB::table('cronograma as cr')
            ->select('gr.nome', 'cr.id', 'cr.h_inicio', 'cr.h_fim', 'd.nome as dia', 'sl.numero', 's.sigla')
            ->leftJoin('salas as sl', 'cr.id_sala', 'sl.id')
            ->leftJoin('grupo as gr', 'cr.id_grupo', 'gr.id')
            ->leftJoin('setor AS s', 'gr.id_setor', 's.id')
            ->leftJoin('tipo_dia as d', 'cr.dia_semana', 'd.id')
            ->leftJoin('membro as m', 'cr.id', 'm.id_cronograma')
            ->leftJoin('associado as ass', 'm.id_associado', 'ass.id')
            ->whereNull('cr.data_fim')
            ->whereNull('m.dt_fim')
            ->groupBy('gr.nome', 'cr.h_inicio', 'cr.h_fim', 'd.nome', 's.sigla', 'sl.numero', 'cr.id')->orderBy('gr.nome', 'asc');



        if (in_array(36, session()->get('usuario.acesso'))) {
        } elseif (in_array(37, session()->get('usuario.acesso'))) {
            $reunioesDirigentes = $reunioesDirigentes->whereIn('gr.id_setor', session()->get('usuario.setor'));
        } else {
            $reunioesDirigentes = $reunioesDirigentes->where('ass.id_pessoa', session()->get('usuario.id_pessoa'))
                ->where('id_funcao', '<', 3);
        }


        //Salva esse select completo em uma variável separada
        $reunioes = $reunioesDirigentes->get();
        //Caso nenhum, grupo seja pesquisado, traz o primeiro da lista como padrão, senão o pesquisado
        if ($request->grupo == null) {

            $reunioesDirigentes = $reunioesDirigentes->pluck('id');
        } else {
            $reunioesDirigentes = $reunioesDirigentes->where('cr.id', $request->grupo)->pluck('id');
        }

        //Traz todos os membros do grupo selecionado
        $query = DB::table('membro as m')
            ->select('m.id', 'm.id_cronograma', 'p.nome_completo', 'tf.nome')
            ->leftJoin('associado as ass', 'm.id_associado', 'ass.id')
            ->leftJoin('pessoas as p', 'ass.id_pessoa', 'p.id')
            ->leftJoin('tipo_funcao as tf', 'm.id_funcao', 'tf.id')
            ->where('m.dt_fim', null)
            ->where('m.id_cronograma', $reunioesDirigentes[0])
            ->whereNot('m.id_funcao',  6) // Exclui id_funcao e 6
            ->orderBy('m.id_funcao', 'ASC', 'p.nome_completo');
        // Filtra pelo nome do setor se estiver presente na requisição
        if ($request->nome_setor) {
            $query->where('m.id', $request->nome_setor);
        }

        $membros = $query->get();

        //Checa pelo ID da reunião selecionado para ver o seu cronograma naquele dia
        $dias_cronograma_selecionada = DB::table('dias_cronograma')
            ->where('data', $hoje)
            ->where('id_cronograma', $reunioesDirigentes[0])
            ->pluck('id');

        // Gera uma variável, checa se todos os requisitos para ela são encontrador e marca o ID de todos os membros já presentes
        $presencas = [];
        if (count($dias_cronograma_selecionada) > 0) {
            $presencas =  DB::table('presenca_membros')
                ->where('id_dias_cronograma', $dias_cronograma_selecionada)
                ->pluck('id_membro');

            //Transforma essa variável de STDClass pra Array
            $presencas = json_decode(json_encode($presencas), true);
        }

        return view('presenca-dirigente.gerenciar-presenca-dirigente', compact('reunioes', 'reunioesDirigentes', 'membros', 'presencas'));
    }




    // Método para marcar a presença
    public function marcarPresenca($id, $idg)
    {
        //$id = id_membro, $idg = id_reuniao

        $hoje = Carbon::today();

        //Confere o cronograma do dia de hoje para o grupo selecionado
        $dias_cronograma_selecionada = DB::table('dias_cronograma')
            ->where('data', $hoje)
            ->where('id_cronograma', $idg)
            ->pluck('id');

        // Caso nenhum cronograma seja encontrado ao dar a presença, retorna um erro
        if (count($dias_cronograma_selecionada) == 0) {
            app('flasher')->addError('Esta reunião não está agendada para hoje nem para este horário!');
            return redirect()->back();
        }

        //Checa as presenças daquele membro naquele cronograma
        $presencas =  DB::table('presenca_membros')->where('id_membro', $id)->where('id_dias_cronograma', $dias_cronograma_selecionada[0])->first();

        //Caso ele já tenha presença, retorna um aviso e não possibilita novas presenças
        if ($presencas) {
            app('flasher')->addWarning('Presença já registrada!');
            return redirect()->back();
        }

        //Caso todos os requisitos acima sejam aceitos, gera a presença para o membro
        DB::table('presenca_membros')->insert([
            'presenca' => true,
            'id_membro' => $id,
            'id_dias_cronograma' => $dias_cronograma_selecionada[0]
        ]);

        $nomePessoa = DB::table('pessoas')
            ->where('id', session()->get('usuario.id_pessoa'))
            ->value('nome_completo');

        DB::table('historico_venus')->insert([
            'id_usuario' => session()->get('usuario.id_usuario'),
            'data' => $hoje,
            'pessoa' => $nomePessoa,
            'obs' => 'Marcou Presença',
            'id_ref' => $id,
            'fato' => 31,
        ]);

        app('flasher')->addSuccess('Presença salva com sucesso!');
        return redirect()->back();
    }

    // Método para cancelar a presença
    public function cancelarPresenca($id, $idg)
    {

        $hoje = Carbon::today();

        //Encontra o cronograma daquele grupo na data de hoje
        $dias_cronograma_selecionada = DB::table('dias_cronograma')
            ->where('data', $hoje)
            ->where('id_cronograma', $idg)
            ->pluck('id');

            $nomePessoa = DB::table('pessoas')
            ->where('id', session()->get('usuario.id_pessoa'))
            ->value('nome_completo');

        DB::table('historico_venus')->insert([
            'id_usuario' => session()->get('usuario.id_usuario'),
            'data' => $hoje,
            'pessoa' => $nomePessoa,
            'obs' => 'Cancelou Presença',
            'id_ref' => $idg,
            'fato' => 32,
        ]);

        //Deleta a presença do membro selecionado no dia de hoje para aquele grupo
        DB::table('presenca_membros')->where('id_membro', $id)->where('id_dias_cronograma', $dias_cronograma_selecionada[0])->delete();

        app('flasher')->addSuccess('Presença cancelada com sucesso!');
        return redirect()->back();
    }
}
