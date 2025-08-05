<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Filament\Support\RawJs;

class PessoaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {



        $ddd = DB::select('select id, descricao from tp_ddd');

        $sexo = DB::select('select id, tipo from tp_sexo');

        $pessoa = DB::table('pessoas AS p')
            ->select('p.id AS idp', 'p.nome_completo', 'p.cpf', 'tps.tipo', 'dt_nascimento', 'sexo', 'email', 'ddd', 'celular', 'tsp.id AS idtps', 'p.status', 'tsp.tipo AS tpsta', 'd.id as did', 'd.descricao as ddesc')
            ->leftjoin('tipo_status_pessoa AS tsp', 'p.status', 'tsp.id')
            ->leftJoin('tp_sexo AS tps', 'p.sexo', 'tps.id')
            ->leftJoin('tp_ddd AS d', 'p.ddd', 'd.id');





        $nome = $request->nome;
        if ($request->nome) {

            $pesquisaNome = array();
            $pesquisaNome = explode(' ', $request->nome);
            $margemErro = 0;
            foreach ($pesquisaNome as $itemPesquisa) {

                $bufferPessoa = (clone $pessoa);
                $pessoa =  $pessoa->whereRaw("UNACCENT(LOWER(p.nome_completo)) ILIKE UNACCENT(LOWER(?))", ["%$itemPesquisa%"]);

                if (count($pessoa->get()->toArray()) < 1) {
                    $pessoaVazia = (clone $pessoa);
                    $pessoa = $bufferPessoa;
                    $margemErro += 1;
                }
            }


            if ($margemErro == 0) {
            } else if ($margemErro < (count($pesquisaNome) / 2)) {
                app('flasher')->addWarning('Nenhum Item Encontrado. Mostrando Pesquisa Aproximada');
            } else {
                //Transforma a variavel em algo vazio
                $pessoa = $pessoaVazia;
                app('flasher')->addError('Nenhum Item Encontrado!');
            }
        }



        $cpf = $request->cpf;

        if ($request->cpf) {
            // Usar LIKE para permitir pesquisa parcial
            $pessoa->whereRaw("LOWER(p.cpf) LIKE LOWER(?)", ["%{$request->cpf}%"]);
        }



        $status = $request->status;
        //


        if ($request->has('status') && $request->status !== "") {
            if ($request->status == "*") {
                $pessoa = $pessoa;
            } else {
                $pessoa = $pessoa->where('p.status', '=', $request->status);
            }
        }

        //Diz método Undefined mas ele funciona
        $pessoa = $pessoa->orderBy('p.status', 'desc')->orderBy('p.nome_completo', 'asc')->paginate(30)
            ->appends([
                'nome' => $nome,
                'cpf' => $cpf,
                'status' => $status
            ]);

        $stap = DB::select("select
                        id as ids,
                        tipo
                        from tipo_status_pessoa t
                        ");

        $soma = $pessoa->count();



        return view('/pessoal/gerenciar-pessoas', compact('pessoa', 'stap', 'soma', 'ddd', 'sexo', 'cpf', 'nome'));
    }

    public function store()
    {
        try {


            $ddd = DB::select('select id, descricao from tp_ddd');

            $sexo = DB::select('select id, tipo from tp_sexo');

            return view('/pessoal/incluir-pessoa', compact('ddd', 'sexo'));
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('administrativo-erro.erro-inesperado', compact('code'));
        }
    }

    public function create(Request $request)
    {
         $now = Carbon::now()->format('Y-m-d');
        $today = Carbon::today()->format('Y-m-d');

        $cpf = $request->cpf;

        $vercpf = DB::table('pessoas')->where('cpf', $cpf)->count();



        try {
            $validated = $request->validate([
                //'telefone' => 'required|telefone',
                'cpf' => 'required|cpf',
                //'cnpj' => 'required|cnpj',
                // outras validações aqui
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            app('flasher')->addError('Este CPF não é válido');

            return redirect()->back()->withInput();
            //dd($e->errors());
        }


        if ($vercpf >= 1) {

            app('flasher')->addError('Existe outro cadastro usando este número de CPF');

            return redirect()->back()->withInput();
        } else {

            DB::table('pessoas')->insert([

                'nome_completo' => str($request->input('nome'))->upper(),
                'cpf' => $request->input('cpf'),
                'dt_nascimento' => $request->input('dt_na'),
                'sexo' => $request->input('sex'),
                'ddd' => $request->input('ddd'),
                'celular' => intval($request->input('celular')),
                'cel_estrangeiro' => $request->input('cel_estrangeiro'),
                'tel_fixo' => $request->input('tel_fixo'),
                'email' => $request->input('email'),
                  'dt_cadastro' => $now,
                'status' => 1


            ]);



            $pessoa = DB::table('pessoas')->max('id');

            DB::table('historico_venus')->insert([
                'id_usuario' => session()->get('usuario.id_usuario'),
                'data' => $today,
                'fato' => 2,
                'pessoa' => $request->input('nome')
            ]);
        }

        app('flasher')->addSuccess('O cadastro foi realizado com sucesso');

        return redirect('/gerenciar-pessoas');
    }




    public function edit($idp)
    {
        try {
            session()->flash('usuario.url', str_replace(url('/'), '', url()->previous())); // Salva o caminho de entrada desta view
            session()->reflash(); // Permite um acesso temporário para inclusão

            $ddd = DB::select('select id, descricao from tp_ddd');
            $sexo = DB::select('select id, tipo from tp_sexo');
            $status_p = DB::select('select id, tipo from tipo_status_pessoa');
            $motivo = DB::select('select id, motivo from tipo_motivo_status_pessoa order by id');


            // Buscando uma única pessoa pelo ID, mas retornando uma coleção
            $lista = DB::table('pessoas as p')
                ->select(
                    'p.id as idp',
                    'p.nome_completo',
                    'p.ddd',
                    'p.dt_nascimento',
                    'p.motivo_status',
                    'p.sexo',
                    'p.status',
                    'tipo_motivo_status_pessoa.id as tipo_motivo_status_pessoa',
                    'tipo_motivo_status_pessoa.motivo as motivo_status_pessoa_tipo_motivo',
                    'tipo_status_pessoa.tipo as tipo_status_pessoa',
                    'p.email',
                    'p.cpf',
                    'p.celular',
                    'p.cel_estrangeiro',
                    'p.tel_fixo',
                    'tps.id AS sexid',
                    'tps.tipo',
                    'd.id AS did',
                    'd.descricao as ddesc'
                )
                ->leftJoin('tp_sexo as tps', 'p.sexo', '=', 'tps.id')
                ->leftJoin('tp_ddd as d', 'p.ddd', '=', 'd.id')
                ->leftJoin('tipo_status_pessoa', 'tipo_status_pessoa.id', '=', 'p.status')
                ->leftJoin('tipo_motivo_status_pessoa', 'tipo_motivo_status_pessoa.id', '=', 'p.motivo_status')
                ->where('p.id', $idp)
                ->get(); // Continuando a usar get()

            // Certifique-se de que a lista não está vazia
            if ($lista->isEmpty()) {
                return redirect()->route('gerenciar-pessoas')->withErrors('Pessoa não encontrada.');
            }

            return view('/pessoal/editar-pessoa', compact('lista', 'sexo', 'ddd', 'status_p', 'motivo'));
        } catch (\Exception $e) {
            $code = $e->getCode();
            return view('administrativo-erro.erro-inesperado', compact('code'));
        }
    }

    public function show($idp)
    {
        try {


            $ddd = DB::select('select id, descricao from tp_ddd');

            $sexo = DB::select('select id, tipo from tp_sexo');

            $status_p = DB::select('select id, tipo from tipo_status_pessoa');

            $motivo = DB::select('select id, motivo from tipo_motivo_status_pessoa order by id');




            $lista = DB::select("select p.id as idp, p.nome_completo, p.ddd, p.dt_nascimento, p.motivo_status,p.sexo, p.status ,tipo_motivo_status_pessoa.id as tipo_motivo_status_pessoa,tipo_motivo_status_pessoa.motivo as motivo_status_pessoa_tipo_motivo, tipo_status_pessoa.tipo as tipo_status_pessoa , p.email, p.cpf, p.celular, tps.id AS sexid, tps.tipo, d.id AS did, d.descricao as ddesc from pessoas p
        left join tp_sexo tps on (p.sexo = tps.id)
        left join tp_ddd d on (p.ddd = d.id)
        left join tipo_status_pessoa on (tipo_status_pessoa.id = p.status )
        left join tipo_motivo_status_pessoa on (tipo_motivo_status_pessoa.id = p.motivo_status )
        where p.id = $idp");




            return view('/pessoal/visualizar-pessoa', compact('lista', 'sexo', 'ddd', 'status_p', 'motivo'));
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('administrativo-erro.erro-inesperado', compact('code'));
        }
    }

    public function update(Request $request, $idp)
    {
        $urlEntrada = session()->get('usuario.url');
        $usuario = session()->get('usuario.id_usuario');

        //dd($usuario);

        $today = Carbon::today()->format('Y-m-d H:m:s');

        $cpf = $request->cpf;

        $vercpf = DB::table('pessoas')->where('cpf', $cpf)->count();


        try {
            $validated = $request->validate([
                //'telefone' => 'required|telefone',
                'cpf' => 'required|cpf',
                //'cnpj' => 'required|cnpj',
                // outras validações aqui
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            app('flasher')->addError('Este CPF não é válido');

            return redirect()->back()->withInput();
            //dd($e->errors());
        }

        if ($vercpf > 1) {


            app('flasher')->addError('Existe outro cadastro usando este número de CPF');

            return redirect()->back()->withInput();
        } else {

            DB::table('pessoas AS p')->where('p.id', $idp)->update([
                'nome_completo' => str($request->input('nome'))->upper(),
                'cpf' => $request->input('cpf'),
                'dt_nascimento' => $request->input('dt_nasc'),
                'sexo' => $request->input('sex'),
                'ddd' => $request->input('ddd'),
                'celular' => $request->input('celular'),
                'cel_estrangeiro' => $request->input('cel_estrangeiro'),
                'tel_fixo' => $request->input('tel_fixo'),
                'email' => $request->input('email'),
                'status' => $request->input('status'),
                'motivo_status' => $request->input('motivo')
            ]);

            //dd($pessoa);
            DB::table('historico_venus')->insert([
                'id_usuario' => $usuario,
                'data' => $today,
                'fato' => 3,
                'pessoa' => $idp
            ]);

            if($urlEntrada == '/criar-atendimento'){
                app('flasher')->addSuccess('O cadastro da pessoa foi alterado e o Atendimento gerado com sucesso');
                return redirect('/gerenciar-atendimentos');
            }
            app('flasher')->addSuccess('O cadastro da pessoa foi alterado com sucesso');
            return redirect('/gerenciar-pessoas');
        }
    }


    public function destroy($idp)
    {

        if (is_null(session()->get('usuario.id_usuario'))) {

            app('flasher')->addError('É necessário fazer Login!');
            return redirect()->route('homeLogin');
        }
        $data = date("Y-m-d H:i:s");

        $pessoa = DB::table('pessoas')->select('nome_completo')->where('id', $idp)->get();

        $funcionario = DB::table('funcionarios')
            ->where('id_pessoa', $idp)
            ->count('id_pessoa');

        $assistido = DB::table('atendimentos')
            ->where('id_assistido', $idp)
            ->count('id_assistido');

        //dd($assistido);

        if ($funcionario > 0) {

            app('flasher')->addError('Essa pessoa não pode ser excluída porque é um funcionário.');
            return redirect('/gerenciar-pessoas');
        }
        if ($assistido > 0) {

            app('flasher')->addError('Essa pessoa não pode ser excluída porque passou por atendimento.');
            return redirect('/gerenciar-pessoas');
        } else {

            // dd($pessoa);
            DB::delete('delete from pessoas where id = ?', [$idp]);

            DB::table('historico_venus')->insert([
                'id_usuario' => session()->get('usuario.id_usuario'),
                'data' => $data,
                'fato' => 1,
                'pessoa' => $pessoa
            ]);


            app('flasher')->addSuccess('O cadastro da pessoa foi excluido com sucesso.');

            return redirect('/gerenciar-pessoas');
        }
    }
}
