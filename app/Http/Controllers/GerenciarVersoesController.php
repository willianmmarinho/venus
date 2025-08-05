<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GerenciarVersoesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {


        $versoes = DB::table('versoes_venus');

        $pesquisaNome = $request->nome;
        if($request->nome){
            $versoes = $versoes->where('versao', 'ilike', "%$request->nome%");
        }

        $versoes = $versoes->orderBy('id', 'DESC')->get();
        return view('versoes.gerenciar-versoes', compact('versoes', 'pesquisaNome'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('versoes.incluir-versoes');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::table('versoes_venus')
        ->where('dt_fim', NULL)
        ->update([
            'dt_fim' => Carbon::yesterday()
        ]);


        DB::table('versoes_venus')->insert([
            'versao' => $request->versao,
            'descricao' => $request->descricao,
            'dt_inicio' => Carbon::today()
        ]);

        return redirect('/gerenciar-versoes');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $versao = DB::table('versoes_venus')->where('id', $id)->first();
        $descricoes = explode("\n", str_replace("\r", "", $versao->descricao));

        return view('versoes.visualizar-versoes', compact('versao', 'descricoes'));


    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        $versao = DB::table('versoes_venus')->where('id', $id)->first();


        return view('versoes.editar-versoes', compact('versao'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        DB::table('versoes_venus')
        ->where('id', $id)
        ->update([
            'versao' => $request->versao,
            'descricao' => $request->descricao,
        ]);

        return redirect('/gerenciar-versoes');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $contaNULL = DB::table('versoes_venus')->where('id', $id)->where('dt_fim', NULL)->count();

        if($contaNULL > 0){
            app('flasher')->addError('Crie uma versão atualizada antes de apagar esta!');
            return redirect('/gerenciar-versoes');
        }
        DB::table('versoes_venus')
        ->where('id', $id)
        ->delete();
        app('flasher')->addSuccess('Versão apagada com sucesso.');
        return redirect('/gerenciar-versoes');
    }
}
