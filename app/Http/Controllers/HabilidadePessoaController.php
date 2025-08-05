<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use function Laravel\Prompts\select;

class HabilidadePessoaController extends Controller
{



    public function index(Request $request)
    {

        $setores = session()->get('acessoInterno')[session()->get('acessoAtual')];
        $setores = array_column($setores, 'id_setor');

        $tipos = DB::table('habilidade_pessoa as hp')
            ->leftJoin('tipo_habilidade as th', 'hp.id_habilidade', 'th.id')
            ->select('hp.id_pessoa')
            ->whereIn('th.id_setor', $setores)
            ->when($request->tipo_habilidade, function ($query, $tipo){
                $query->where('id_habilidade', $tipo);
            })
            ->groupBy('hp.id_pessoa')
            ->get();

        $tiposHabilidade = DB::table('tipo_habilidade')->orderBy('tipo')->get();

        $array = json_decode(json_encode($tipos), true);

        //dd($array);
        $habilidade = DB::table('pessoas AS p')
            ->select('id as idp', 'nome_completo', 'cpf', 'status')
            ->whereIn('id', $array)
            ->orderBy('p.nome_completo', 'ASC');

        $contar = $habilidade->distinct()->count('p.id');
        $nome = $request->nome_pesquisa;
        $cpf = $request->cpf_pesquisa;


        if ($nome) {
            $habilidade = $habilidade->where('nome_completo', 'ilike', "%$nome%");
        }
        if ($cpf) {
            $habilidade = $habilidade->where('cpf', 'ilike', "%$cpf%");
        }

        $contarQuery = clone $habilidade;
        $contar = $contarQuery->distinct('p.id')->count('p.id');


        $habilidade = $habilidade->paginate(50);



        return view('habilidade.gerenciar-habilidades', compact('nome', 'cpf',  'habilidade', 'contar', 'tiposHabilidade'));
    }





    public function create()
    {

        $setores = session()->get('acessoInterno')[session()->get('acessoAtual')];
        $setores = array_column($setores, 'id_setor');

        $id_habilidade = 1;
        $grupo = DB::select('select id, nome from grupo');
        $habilidade = DB::select('select * from habilidade_pessoa');
        $tipo_habilidade = DB::table('tipo_habilidade')->select('id', 'tipo')->whereIn('id_setor', $setores)->orderBy('id_setor')->orderBy('tipo')->get();
        $pessoas = DB::select('SELECT id AS idp, nome_completo, motivo_status, status FROM pessoas ORDER BY nome_completo ASC');
        $tipo_funcao = DB::select('select id as idf, tipo_funcao, nome, sigla from tipo_funcao');
        $habilidade_pessoa = DB::select('select id as idme, data_inicio from habilidade_pessoa');
        $tipo_status_pessoa = DB::select('select id,tipo as tipos from tipo_status_pessoa');


        return view('habilidade.criar-habilidade', compact('tipo_status_pessoa', 'grupo', 'id_habilidade', 'habilidade', 'tipo_habilidade', 'pessoas', 'tipo_funcao', 'habilidade_pessoa'));
    }




    public function store(Request $request)
    {
        try {
            // Obter os dados do formulário
            $id_pessoa = $request->input('id_pessoa');
            $tipo_ids = $request->input('id_tp_habilidade');

            // Inserir dados na tabela 'habilidade_pessoa'
            foreach ($tipo_ids as $tipo_id) {
                $datas_inicio = $request->input("data_inicio.{$tipo_id}");
                // Verifica se já existe uma habilidade desse tipo para essa pessoa
                $existe = DB::table('habilidade_pessoa')
                    ->where('id_pessoa', $id_pessoa)
                    ->where('id_habilidade', $tipo_id)
                    ->exists();

                if ($existe) {
                    // Se já existe, pula para o próximo tipo
                    continue;
                }

                // Insere apenas a primeira data (ou a única, se for o caso)
                $data_inicio = $datas_inicio[0] ?? null;

                foreach ($datas_inicio as $data_inicio) {
                    DB::table('habilidade_pessoa')->insert([
                        'id_pessoa' => $id_pessoa,
                        'id_habilidade' => $tipo_id,
                        'data_inicio' => $data_inicio ? date('Y-m-d', strtotime($data_inicio)) : null,
                    ]);
                }
            }

            // Mensagem de sucesso e redirecionamento
            app('flasher')->addSuccess("Cadastrado com Sucesso");
            return redirect('gerenciar-habilidade');
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('administrativo-erro.erro-inesperado', compact('code'));
        }
    }


    public function edit($id)
    {
        try {

            $setores = session()->get('acessoInterno')[session()->get('acessoAtual')];
            $setores = array_column($setores, 'id_setor');

            $id_habilidade = 1;
            $habilidade = DB::table('habilidade_pessoa AS m')
                ->leftJoin('pessoas AS p', 'm.id_pessoa', '=', 'p.id')
                ->leftJoin('tipo_status_pessoa as tsp', 'p.status', '=', 'tsp.id')
                ->select('p.nome_completo', 'm.id_pessoa', 'm.id_habilidade', 'm.id AS idm', 'm.id_pessoa', 'p.status', 'p.motivo_status', 'm.data_inicio', 'tsp.tipo')
                ->where('m.id_pessoa', $id)
                ->first();

            $tipo_motivo_status_pessoa = DB::select('select id,motivo  from tipo_motivo_status_pessoa');
            $tipo_status_pessoa = DB::select('select id,tipo as tipos from tipo_status_pessoa');
            $pessoas = DB::table('pessoas')->get();
            $tipo_habilidade = DB::table('tipo_habilidade')->select('id', 'tipo')->whereIn('id_setor', $setores)->orderBy('id_setor')->orderBy('tipo')->get();

            $habilidadesIds = DB::table('habilidade_pessoa')->where('id_pessoa', $id)->get();

            $arrayChecked = [];

            foreach ($habilidadesIds as $ids) {
                $arrayChecked[] = $ids->id_habilidade;
            }

            return view('habilidade.editar-habilidade', compact('habilidadesIds', 'arrayChecked', 'id_habilidade', 'habilidade', 'tipo_motivo_status_pessoa', 'tipo_status_pessoa',  'tipo_habilidade', 'pessoas'));
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('administrativo-erro.erro-inesperado', compact('code'));
        }
    }

    public function update(Request $request, string $id)
    {
        try {

            $setores = session()->get('acessoInterno')[session()->get('acessoAtual')];
            $setores = array_column($setores, 'id_setor');

            // Inicia a transação
            DB::beginTransaction();
            // Excluir registros anteriores na tabela 'habilidade_pessoa' para o mesmo id_pessoa
            DB::table('habilidade_pessoa as hp')->leftJoin('tipo_habilidade as th', 'hp.id_habilidade', 'th.id')->where('id_pessoa', $id)->whereIn('id_setor', $setores)->delete();

            // Obter os dados do formulário
            $id_pessoa = $request->input('id_pessoa');
            $tipo_ids = $request->input('id_tp_habilidade');

            //dd($request->all());
            // Certifique-se de que tipo_ids é um array
            if (is_array($tipo_ids)) {
                // Inserir dados na tabela 'habilidade_pessoa'
                foreach ($tipo_ids as $tipo_id) {
                    $datas_inicio = $request->input("data_inicio.{$tipo_id}");

                    // Certifique-se de que datas_inicio é um array
                    if (is_array($datas_inicio)) {
                        foreach ($datas_inicio as $data_inicio) {
                            DB::table('habilidade_pessoa')->insert([
                                'id_pessoa' => $id,
                                'id_habilidade' => $tipo_id,
                                'data_inicio' => $data_inicio ? date('Y-m-d', strtotime($data_inicio)) : null,
                            ]);
                        }
                    }
                }
            }

            // Atualizar o status e motivo na tabela 'pessoas'
            $status = $request->input('tipo_status_pessoa');
            $motivo = $request->input('motivo_status');
            DB::table('pessoas')->where('id', $id)->update(['status' => $status, 'motivo_status' => $motivo]);

            // Gravar no histórico
            $ida = session()->get('usuario.id_pessoa');
            $data = Carbon::today();
            DB::table('historico_venus')->insert([
                'id_usuario' => $ida,
                'data' => $data,
                'fato' => 18,
                'pessoa' => $id,
            ]);

            // Commit da transação
            DB::commit();

            return redirect('gerenciar-habilidade');
        } catch (\Exception $e) {
            // Rollback em caso de erro
            DB::rollBack();

            $code = $e->getCode();
            $message = $e->getMessage();
            return view('administrativo-erro.erro-inesperado', compact('code', 'message'));
        }
    }



    public function show($id)
    {
        try {

            $setores = session()->get('acessoInterno')[session()->get('acessoAtual')];
            $setores = array_column($setores, 'id_setor');

            $id_habilidade = 1;
            $habilidade = DB::table('habilidade_pessoa AS m')
                ->leftJoin('pessoas AS p', 'm.id_pessoa', '=', 'p.id')
                ->leftJoin('tipo_status_pessoa as tsp', 'p.status', '=', 'tsp.id')
                ->select('p.nome_completo', 'm.id_pessoa', 'm.id_habilidade', 'm.id AS idm', 'm.id_pessoa', 'p.status', 'p.motivo_status', 'tsp.tipo')
                ->where('m.id_pessoa', $id)
                ->first();

            $tipo_motivo_status_pessoa = DB::select('select id,motivo  from tipo_motivo_status_pessoa');
            $tipo_status_pessoa = DB::select('select id,tipo as tipos from tipo_status_pessoa');
            $pessoas = DB::table('pessoas')->get();
            $tipo_habilidade = DB::table('tipo_habilidade')->select('id', 'tipo')->whereIn('id_setor', $setores)->orderBy('id_setor')->orderBy('tipo')->get();

            $habilidadesIds = DB::table('habilidade_pessoa')->where('id_pessoa', $id)->get();


            $arrayChecked = [];

            foreach ($habilidadesIds as $ids) {
                $arrayChecked[] = $ids->id_habilidade;
            }
            return view('habilidade.visualizar-habilidade', compact('habilidadesIds', 'arrayChecked', 'id_habilidade', 'habilidade', 'tipo_motivo_status_pessoa', 'tipo_status_pessoa',  'tipo_habilidade', 'pessoas'));
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('administrativo-erro.erro-inesperado', compact('code'));
        }
    }


    public function destroy(string $id)
    {

        $data = date("Y-m-d H:i:s");

        DB::table('historico_venus')->insert([

            'id_usuario' => session()->get('usuario.id_pessoa'),
            'data' => $data,
            'fato' => 9,
            'obs' => $id

        ]);
        $habilidade = DB::table('habilidade_pessoa')->where('id_pessoa', $id)->first();


        if (!$habilidade) {
            app('flasher')->addError('Pessoa não foi encontrada.');
            return redirect('/gerenciar-habilidade');
        }


        DB::table('habilidade_pessoa')->where('id_pessoa', $id)->delete();


        app('flasher')->addError('Excluído com sucesso.');
        return redirect('/gerenciar-habilidade');
    }
}
