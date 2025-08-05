<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Tipo_fato;
use Illuminate\Database\DBAL\TimestampType;
use Illuminate\Support\Carbon;

class PresencaController extends Controller
{

    public function index(Request $request)
    {

        try {

            // Busca os atendimentos aguardando para a lista
            $lista = DB::table('atendimentos as atd')
                ->select('p.nome_completo', 'p.cpf', 'atd.id', 'atd.dh_marcada')
                ->leftJoin('pessoas as p', 'atd.id_assistido', 'p.id')
                ->where('status_atendimento', 3)
                ->where('id_tipo_atendimento', 2)
                ->when($request->nome_pesquisa, function ($query) use ($request) { // Caso um nome seja pesquisado, busca no banco de dados
                    $query->where('nome_completo', 'ilike', "%$request->nome_pesquisa%");
                })
                ->when($request->data, function ($query) use ($request) { // Caso seja pesquisado, retorna todos os atendimentos daquele dia
                    $query->where('atd.dh_marcada', '<', Carbon::parse($request->data)->addDay(1))->where('atd.dh_marcada', '>', $request->data);
                }, function ($query) use ($request) { // Caso não haja pesquisa, mostra todos os atendimentos de hoje e os atrasados (Para facilitar quem precisa de remarcação)
                    $query->where('atd.dh_marcada', '<', Carbon::parse($request->data)->addDay(1));
                })->orderBy('atd.dh_marcada', 'DESC')
                ->get();


            return view('presenças.gerenciar-presenca', compact('lista'));
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('gerenciar-presenca erro.erro-inesperado', compact('code'));
        }
    }

    public function criar(Request $request, string $idtr)
    {

       

            $dt_hora = Carbon::now();
            $now = Carbon::now();

            DB::table('atendimentos')
                ->where('atendimentos.id', $idtr)
                ->update([
                    'dh_chegada' =>  $now,
                    'status_atendimento' => 2, // Aguardando Atendimento
                ]);


            // Insere no histórico a criação do atendimento
            DB::table('log_atendimentos')->insert([
                'id_referencia' => $idtr,
                'id_usuario' => session()->get('usuario.id_usuario'),
                'id_acao' => 1, // mudou de Status para
                'id_observacao' => 2, // Aguardando Atendimento
                'id_origem' => 1, // Atendimento
                'data_hora' => $dt_hora
            ]);

            app('flasher')->addSuccess('Foi registrada a presença com sucesso.');

            return redirect('/gerenciar-presenca');
        
    }


    public function destroy($id)
    {

        $deletar = DB::table('atendimentos')->where('id', $id)->get();
        $teste = session()->get('usuario');

        $verifica = DB::table('historico_venus')->where('fato', $id)->count('fato');


        $data = date("Y-m-d H:i:s");





        DB::table('historico_venus')->insert([

            'id_usuario' => session()->get('usuario.id_usuario'),
            'data' => $data,
            'fato' => 10,
            'obs' => $id

        ]);


        DB::table('atendimentos')->where('id', $id)->delete();


        app('flasher')->addError('Excluido com sucesso.');


        return redirect('/gerenciar-presenca');
    }
}
