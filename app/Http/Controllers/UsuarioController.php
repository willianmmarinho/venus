<?php

namespace App\Http\Controllers;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ModelUsuario;
use App\Models\ModelPessoa;
use Illuminate\Support\Facades\Hash;
use App\Notifications\ResetPassword;
use Illuminate\Notifications\Notifiable;

class UsuarioController extends Controller
{
    use Notifiable;

    // Enviar email traduziado
    public function sendPasswordResetNotification($token)
    {
        try {
            $this->notify(new ResetPassword($token));
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('administrativo-erro.erro-inesperado', compact('code'));
        }
    }
    private $objUsuario;

    public function __construct()
    {
        try {
            $this->objUsuario = new ModelUsuario();
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('administrativo-erro.erro-inesperado', compact('code'));
        }
    }

    public function getUsuarios()
    {
        try {
            $result = DB::table('usuario as u')->select('u.id', 'u.id_pessoa', 'p.cpf', 'p.nome_completo', 'u.ativo', 'u.bloqueado', 'u.data_ativacao')->leftJoin('pessoas as p', 'u.id_pessoa', 'p.id');

            return $result;
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('administrativo-erro.erro-inesperado', compact('code'));
        }
    }

    public function index(Request $request)
    {

        // Supondo que getUsuarios retorne um query builder
        $query = $this->getUsuarios();

        // Aplicar filtros com base na requisição
        if ($request->nome) {
            $query->whereRaw('unaccent(lower(p.nome_completo)) ILIKE ?', ['%' . strtolower($request->nome) . '%']);
        }
        if ($request->cpf) {
            $query->whereRaw('unaccent(lower(p.cpf)) ILIKE ?', ['%' . strtolower($request->cpf) . '%']);
        }

        // Contar o número total de registros que atendem aos critérios de pesquisa
        $contar = $query->distinct()->count('p.id');

        // Aplicar ordenação e paginação na consulta
        $result = $query->orderBy('p.nome_completo', 'ASC')
            ->paginate(50);

        // Retornar a view com as variáveis necessárias
        return view('usuario/gerenciar-usuario', compact('result', 'contar'));
    }


    public function create(Request $request)
    {
        try {
            $pessoa = new ModelPessoa();
            $result = $pessoa;

            // Conta os registros distintos de 'id'
            $contar = $result->distinct()->count('id');

            if ($request->nome) {
                $result = $result->whereRaw("UNACCENT(LOWER(nome_completo)) ILIKE UNACCENT(LOWER(?))", ["%{$request->nome}%"]);
            }
            if ($request->cpf) {
                $result = $result->whereRaw("UNACCENT(LOWER(cpf)) ILIKE UNACCENT(LOWER(?))", ["%{$request->cpf}%"]);
            }

            $result = $result->orderBy('nome_completo', 'ASC')
                ->paginate(50);

            return view('usuario/incluir-usuario', compact('result', 'contar'));
        } catch (\Exception $e) {
            $code = $e->getCode();
            return view('administrativo-erro.erro-inesperado', compact('code', 'contar'));
        }
    }



    public function store(Request $request)
    {


        $senha_inicial = $this->gerarSenhaInicial($request->input('idPessoa'));
        $contaUsuarios = DB::table('usuario')->where('id_pessoa', $request->input('idPessoa'))->count();



        if ($contaUsuarios < 1) {
            $this->inserirUsuario($request, $senha_inicial);
            app('flasher')->addSuccess('O usuário foi criado com sucesso.');
        } else {
            app('flasher')->addWarning('Usuário já inserido. Dados Atualizados!');
        }



        $this->excluirUsuarioPerfis($request->input('idPessoa'));

        $this->inserirperfilUsuario($request->perfis, $request->input('idPessoa'));

        return Redirect('/gerenciar-usuario');

        //return view('usuario/gerenciar-usuario', compact('result'));
    }
    // catch(\Exception $e){

    //     $code = $e->getCode( );
    //     return view('administrativo-erro.erro-inesperado', compact('code'));
    //         }
    //     }
    public function show($id)
    {
        //
    }

    public function edit($idUsuario)
    {
        $resultUsuario = DB::table('usuario')->where('id', $idUsuario)->first();

        $result = DB::table('pessoas')
            ->where('id', $resultUsuario->id_pessoa)
            ->get();

        $resultPerfil = DB::table('perfil')->get();

        $resultSetor = DB::table('rotas_setor')->leftJoin('setor', 'rotas_setor.id_setor', 'setor.id')->distinct('id_setor')->get();

        $acessosAutorizados = DB::table('usuario_acesso')->select('id_setor', 'id_perfil')->where('id_usuario', $idUsuario)->get()->toArray();
        $acessosAutorizados = array_intersect_key($acessosAutorizados, array_unique(array_map('serialize', $acessosAutorizados)));


        return view('/usuario/alterar-configurar-usuario', compact('result', 'resultUsuario', 'resultPerfil', 'resultSetor', 'acessosAutorizados'));
    }


    public function update(Request $request, $id)
    {
        //try {

        $ativo = isset($request->ativo) ? 1 : 0;
        $bloqueado = isset($request->bloqueado) ? 1 : 0;
        // echo $id;
        // exit();

        DB::table('usuario')
            ->where('id', $id)
            ->update([
                'ativo' => $ativo,
                'bloqueado' => $bloqueado,
            ]);



        $this->excluirUsuarioPerfis($request->input('idPessoa'));

        $this->inserirPerfilUsuario($request->perfis, $request->input('idPessoa'));
        app('flasher')->addSuccess('Usuário alterado com sucesso!');
        return redirect('gerenciar-usuario');
        // } catch (\Exception $e) {

        //     $code = $e->getCode();
        //     return view('administrativo-erro.erro-inesperado', compact('code'));
        // }
    }

    public function regenerarAcessos(){

        $counter = 0;
        $acessos = DB::table('usuario_acesso')->get();

        $perfis = array();
        foreach ($acessos as $element) {
            $perfis[$element->id_usuario][$element->id_perfil][$element->id_setor] = 'on';
        }

        foreach($perfis as $key => $usuario){
            $id_pessoa = DB::table('usuario')->where('id', $key)->pluck('id_pessoa')->toArray();
            $this->inserirPerfilUsuario($usuario, current($id_pessoa));

            $counter++;
        }

        app('flasher')->addSuccess("Acessos de $counter usuários atualizados com sucesso!");
        return redirect()->back();
    }

    public function destroy($id)
    {
       // try {

            DB::delete('delete from usuario_acesso where id_usuario =?', [$id]);
            $deleted = DB::delete('delete from usuario where id =?', [$id]);

            $result = $this->getUsuarios();

            app('flasher')->addSuccess('O usuário foi excluido com sucesso.');

            return Redirect('/gerenciar-usuario');
            //return view('usuario/gerenciar-usuario', compact('result'));
        // } catch (\Exception $e) {

        //     $code = $e->getCode();
        //     return view('administrativo-erro.erro-inesperado', compact('code'));
        // }
    }


    public function configurarUsuario($id)
    {
        //  try{
        $resultPerfil = DB::table('perfil')->get();


        $resultSetor = DB::table('rotas_setor')->leftJoin('setor', 'rotas_setor.id_setor', 'setor.id')->distinct('setor.nome')->orderBy('setor.nome')->get();

        $result = DB::table('pessoas')->where('id', $id)->get();

        return view('/usuario/configurar-usuario', compact('result', 'resultPerfil', 'resultSetor'));
    }
    // catch(\Exception $e){

    //     $code = $e->getCode( );
    //     return view('administrativo-erro.erro-inesperado', compact('code'));
    //         }
    //     }

    public function inserirUsuario($request, $senha_inicial)
    {
        try {
            $ativo = isset($request->ativo) ? 1 : 0;
            $bloqueado = isset($request->bloqueado) ? 1 : 0;

            DB::table('usuario')->insert([
                'id_pessoa' => $request->input('idPessoa'),
                'ativo' => $ativo,
                'data_criacao' => date('Y-m-d'),
                'data_ativacao' => date('Y-m-d'),
                'bloqueado' => $bloqueado,
                'hash_senha' => $senha_inicial,
            ]);
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('administrativo-erro.erro-inesperado', compact('code'));
        }
    }

    public function excluirUsuarioPerfis($idPessoa)
    {
        try {
            $idUsuario = DB::select('select id from usuario where id_pessoa =' . $idPessoa);
            DB::delete('delete from usuario_acesso where id_usuario =?', [$idUsuario[0]->id]);
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('administrativo-erro.erro-inesperado', compact('code'));
        }
    }

    public function inserirPerfilUsuario($perfis, $idPessoa)
    {
       
        //  try {
        $idUsuario = DB::select('select id from usuario where id_pessoa =' . $idPessoa);

        if ($perfis) {
            foreach ($perfis as $keyPerfil => $perfil) {

                $acessoPerfil = DB::table('rotas_perfil')
                    ->where('id_perfil', $keyPerfil)
                    ->pluck('id_rotas')->toArray();

                foreach ($perfil as $keySetor => $setor) {

                    $setores = DB::table('setor as st')
                        ->leftJoin('setor as stf', 'st.id', 'stf.setor_pai')
                        ->leftJoin('setor as stn', 'stf.id', 'stn.setor_pai')
                        ->select('st.id as ids', 'stf.id as idf', 'stn.id as idn')
                        ->where('st.id', $keySetor)
                        ->get()
                        ->toArray();

                    $setores = (array_unique(array_merge(array_column($setores, 'ids'), array_column($setores, 'idf'), array_column($setores, 'idn'))));

                    foreach ($setores as $setor) {
                        $acessoSetor = DB::table('rotas_setor')
                            ->where('id_setor', $setor)
                            ->pluck('id_rotas')
                            ->toArray();

                        $acessoTotal = array_intersect($acessoPerfil, $acessoSetor);

                        foreach ($acessoTotal as $acesso) {

                            $countInserido = DB::table('usuario_acesso')
                            ->where('id_usuario',$idUsuario[0]->id)
                            ->where('id_setor',$setor)
                            ->where('id_perfil',$keyPerfil)
                            ->where('id_acesso',$acesso)
                            ->count();

                            if(!$countInserido){
                                DB::table('usuario_acesso')->insert([
                                    'id_usuario' => $idUsuario[0]->id,
                                    'id_setor' => $setor,
                                    'id_perfil' => $keyPerfil,
                                    'id_acesso' => $acesso
                                ]);
                            }
                           
                        }
                    }
                }
            }
        }
        // } catch (\Exception $e) {

        //     $code = $e->getCode();
        //     return view('administrativo-erro.erro-inesperado', compact('code'));
        // }
    }


    public function gerarSenhaInicial($id_pessoa)
    {
        $resultPessoa = DB::select("select cpf, id from pessoas where id =$id_pessoa");

        //dd($resultPessoa[0]->cpf);

        return Hash::make($resultPessoa[0]->cpf);
    }

    public function alteraSenha()
    {
        return view('usuario.alterar-senha');
    }

    public function gravaSenha(Request $request)
    {
        try {
            //dd($request);
            $id_usuario = session()->get('usuario.id_usuario');
            $senhaAtual = $request->input('senhaAtual');
            $resultSenhaAtualHash = DB::select("select hash_senha from usuario where id = $id_usuario");

            if (Hash::check($senhaAtual, $resultSenhaAtualHash[0]->hash_senha)) {
                if ($request->input('senhaNova') == $request->input('senhaAtual')) {
                    app('flasher')->addError('Sua nova senha não pode ser igual a antiga!');
                    return redirect()->back();
                }
                $senha_nova = Hash::make($request->input('senhaNova'));

                DB::table('usuario')
                    ->where('id', $id_usuario)
                    ->update([
                        'hash_senha' => $senha_nova,
                    ]);

                //return view('login.alterar-senha')->with('mensagem', 'Senha Alterada com sucesso!');

                app('flasher')->addSuccess('Senha Alterada com sucesso!');

                return redirect('/login/valida');
            }
            return redirect()->back()->with('mensagemErro', 'Senha atual incorreta!');
            //return view('login.alterar-senha')->withErrors(['Senha atual incorreta']);
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('administrativo-erro.erro-inesperado', compact('code'));
        }
    }

    public function gerarSenha($id_pessoa)
    {

        $senha = $this->gerarSenhaInicial($id_pessoa);

        DB::table('usuario')
            ->where('id_pessoa', $id_pessoa)
            ->update([
                'hash_senha' => $senha,
            ]);
        return redirect('gerenciar-usuario')->with('mensagem', 'Senha gerada com sucesso!');
    }
}
