<?php


namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Sala;
use Doctrine\DBAL\Driver\Mysqli\Exception\InvalidOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Spatie\FlareClient\Http\Exceptions\InvalidData;



class GrupoController extends Controller
{

    public function index(Request $request)
    {


        // Carregar a lista de setores para o Select2
        $setor = DB::table('setor')->orderBy('nome', 'asc')->get();

       

       

        // Carregar a lista de grupos para o Select2
        $grupo = DB::table('grupo AS g')->leftJoin('setor AS s', 'g.id_setor', 's.id')->select('g.id AS idg', 'g.nome AS nomeg', 's.sigla')->orderBy('g.nome', 'asc')->get();


        $lista = DB::table('grupo AS g')
            ->leftJoin('tipo_grupo AS tg', 'g.id_tipo_grupo', 'tg.id')
            ->leftJoin('tipo_status_grupo AS ts', 'g.status_grupo', 'ts.id')
            ->leftJoin('tipo_mot_inat_gr_reu as tm', 'g.id_motivo_inativacao', 'tm.id')
            ->leftJoin('setor AS st', 'g.id_setor', 'st.id')
            ->select(
                'g.id',
                'g.nome',
                'g.data_inicio',
                'g.data_fim',
                'g.status_grupo',
                'g.id_motivo_inativacao',
                'tg.nm_tipo_grupo',
                'tg.id AS idg',
                'ts.descricao as descricao1',
                'tm.descricao',
                'g.id_setor',
                'st.nome AS nm_setor',
                'st.sigla AS sigset'
            );

        $nomeg = $request->nome_grupo;
        if ($nomeg) {
            $lista->where('g.id', $nomeg);
        }

        $nomes = $request->nome_setor;
        if ($nomes) {
            $lista->where('st.id', $nomes);
        }

        if ($request->tipo_status_grupo) {
            $lista->where('ts.id', $request->tipo_status_grupo);
        }


        $lista = $lista->orderBy('g.status_grupo', 'ASC')
            ->orderBy('g.nome', 'ASC')
            ->paginate(50)->appends([
                'nome_grupo' => $request->nome_grupo,
                'nome_setor' => $request->nome_setor,
                'status' => $request->tipo_status_grupo,
            ]);

        $tipo_status_grupo = DB::table('tipo_status_grupo')->get();
        $tipo_motivo = DB::table('tipo_mot_inat_gr_reu')->get();
        $contar = $lista->total();


        return view('grupos.gerenciar-grupos', compact('tipo_motivo','tipo_status_grupo', 'lista', 'grupo', 'setor', 'contar', 'nomeg', 'nomes'));
    }








    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $grupos = DB::select('select * from grupo');
        $tipo_grupo = DB::select('select id as idg,nm_tipo_grupo from tipo_grupo order by nm_tipo_grupo asc');
        $tipo_status_grupo = DB::select('select id as ids, descricao as descricao from tipo_status_grupo');
        $tipo_motivo = DB::select('select id ,descricao from tipo_mot_inat_gr_reu');
        $setor = DB::select('select id, nome as nm_setor from setor order by nome asc');




        return view('grupos/criar-grupos', compact('grupos', 'tipo_grupo', 'tipo_status_grupo', 'tipo_motivo', 'setor'));
    }




    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = date("Y-m-d H:i:s");
            DB::table('grupo')->insert([
                'status_grupo' => $request->input('status_grupo'),
                'nome' => str($request->input('nome'))->upper(),
                'data_inicio' => $data,
                'id_tipo_grupo' => $request->input('id_tipo_grupo'),
                'id_motivo_inativacao' => $request->input('id_motivo_inativacao'),
                'id_setor' => $request->input('id_setor'),

            ]);


            app('flasher')->addSuccess('O cadastro foi realizado com sucesso.');





            return redirect('gerenciar-grupos');
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('administrativo-erro.erro-inesperado', compact('code'));
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $grupo = DB::table('grupo AS g')
            ->leftJoin('tipo_grupo AS tg', 'g.id_tipo_grupo', 'tg.id')
            ->leftJoin('tipo_status_grupo AS ts', 'g.status_grupo', 'ts.id')
            ->leftJoin('tipo_mot_inat_gr_reu AS tm', 'g.id_motivo_inativacao', 'tm.id')
            ->leftJoin('setor AS st', 'g.id_setor', 'st.id')
            ->select('g.id', 'g.nome', 'g.data_inicio', 'g.data_fim', 'g.status_grupo', 'g.id_motivo_inativacao', 'tg.nm_tipo_grupo', 'ts.descricao as descricao1', 'tm.descricao', 'g.id_setor', 'st.nome AS nm_setor')->where('g.id', $id)
            ->get();
        $tipo_grupo = DB::table('tipo_grupo')->get();
        $tipo_status_grupo = DB::table('tipo_status_grupo')->select('descricao as descricao1', 'id')->get();
        $tipo_motivo = DB::table('tipo_mot_inat_gr_reu')->get();
        $setor = DB::table('setor')->get();

        return view('grupos/visualizar-grupos', compact('setor', 'grupo', 'tipo_grupo', 'tipo_status_grupo', 'tipo_motivo'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {


            $grupo = DB::table('grupo AS g')
                ->leftJoin('tipo_grupo AS tg', 'g.id_tipo_grupo', 'tg.id')
                ->leftJoin('tipo_status_grupo AS ts', 'g.status_grupo', 'ts.id')
                ->leftJoin('tipo_mot_inat_gr_reu AS tm', 'g.id_motivo_inativacao', 'tm.id')
                ->leftJoin('setor AS st', 'g.id_setor', 'st.id')
                ->select('g.id', 'g.nome', 'g.data_inicio', 'g.data_fim', 'g.status_grupo', 'tg.nm_tipo_grupo as nmg', 'ts.descricao as descricao1', 'g.id_tipo_grupo', 'g.status_grupo', 'g.id_motivo_inativacao', 'tm.descricao', 'g.id_setor', 'st.nome AS nm_setor')->where('g.id', $id)
                ->get();
            $tipo_grupo = DB::table('tipo_grupo')->get();
            $tipo_status_grupo = DB::table('tipo_status_grupo')->select('descricao as descricao1', 'id')->get();
            $tipo_motivo = DB::table('tipo_mot_inat_gr_reu')->get();
            $setor = DB::table('setor')->get();



            return view('grupos/editar-grupos', compact('setor', 'grupo', 'tipo_grupo', 'tipo_status_grupo', 'tipo_motivo'));
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('administrativo-erro.erro-inesperado', compact('code'));
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {


        // Obter a data atual
        $now = Carbon::now()->format('Y-m-d');

        // Atualizar o registro do grupo
        DB::table('grupo')->where('id', $id)->update([
            'nome' => str($request->input('nome'))->upper(),
            'data_inicio' => $request->input('data_inicio'),
            'data_fim' => $request->input('data_fim'),
            'id_tipo_grupo' => $request->input('id_tipo_grupo'),
            'status_grupo' => $request->input('status_grupo'),
            'id_motivo_inativacao' => $request->input('id_motivo_inativacao'),
            'id_setor' => $request->input('id_setor')
        ]);

        // Verificar se o status do grupo foi alterado para inativo
        // if ($request->input('status_grupo') == 2) {
        //     // Atualizar o cronograma com o status de reunião inativo e a data de término
        //     DB::table('cronograma as cro')
        //         ->where('cro.id_grupo', $id)
        //         ->update([
        //             'status_reuniao' => 2,
        //             'data_fim' => $now
        //         ]);
        // }

        app('flasher')->addSuccess("Alterado com Sucesso");

        return redirect('gerenciar-grupos');
    }





    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,string $id)

    {
       // try {

            $ids = DB::table('grupo')->select('nome')->where('id', $id)->get();
            $teste = session()->get('usuario');

            $verifica = DB::table('historico_venus')->where('fato', $id)->count('fato');

            $data = date("Y-m-d H:i:s");
            $hoje = Carbon::today();

            DB::table('historico_venus')->insert([

                'id_usuario' => session()->get('usuario.id_usuario'),
                'data' => $data,
                'fato' => 8,
                'obs' => $id

            ]);
            DB::table('cronograma')->where('id_grupo', $id)->update([
                'modificador' => 2,
                'data_fim' => $hoje,
               
            ]);

            DB::table('grupo')->where('id', $id)->update([
                'status_grupo' => 2,
                'data_fim' => $hoje,
                'id_motivo_inativacao' => $request->motivo
            ]);

            app('flasher')->addSuccess('Inativado com sucesso.');
            return redirect('/gerenciar-grupos');
        // } catch (\Exception $e) {

        //     app('flasher')->addError('Este grupo está sendo utilizado em outro lugar.');
        //     return redirect('/gerenciar-grupos');
        // }
    }
}
