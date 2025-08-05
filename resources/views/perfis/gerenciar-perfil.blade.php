@extends('layouts.app')

@section('title') Gerenciar Perfis @endsection

@section('content')


<div class="container-fluid">
    <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR PERFIS</h4>
    <div class="col-12">
        <div class="row justify-content-center">
            <form action="/gerenciar-perfis" class="form-horizontal mt-4" method="GET">
                <div class="row">
                    <div class="col-3">Nome
                        <input class="form-control" type="text" id="nome_pesquisa" name="nome_pesquisa" placeholder="Pesquisar nome" value="{{ request('nome_pesquisa') }}">
                    </div>
                    <div class="col"><br>
                        <input class="btn btn-light btn-sm me-md-2" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
                        <a href="/gerenciar-perfis"><input class="btn btn-light btn-sm me-md-2" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" value="Limpar"></a>
                    </form>
                    <a href="/criar-perfis"><input class="btn btn-success btn-sm me-md-2" style="font-size: 0.9rem;" type="button" value="Novo Perfil +"></a>

                    </div>
                </div>
        </div>


    <hr>

    <div class="row" style="text-align:center;">
        <div class="col">
            <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                <thead style="text-align: center;">
                    <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                        <th class="col-3 ">ID</th>
                        <th class="col"> NOME</th>

                        <th class="col">AÇÕES</th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px; color:#000000; text-align:center;">
                    @foreach ($perfis as $perfil)
                    <tr>
                        <td>{{ $perfil->id }}</td>
                        <td>{{ $perfil->descricao }}</td>
                        <td>
                            <a href="/editar-perfis/{{ $perfil->id }}" type="button" class="btn btn-outline-warning btn-sm tooltips">
                                <span class="tooltiptext">Editar</span>
                                <i class="bi bi-pencil"style="font-size: 1rem; color:#000;"></i>
                            </a>
                            <a href="/visualizar-perfis/{{ $perfil->id }}" type="button" class="btn btn-outline-primary btn-sm tooltips">
                                <span class="tooltiptext">Visualizar</span>
                                <i class="bi bi-search" style="font-size: 1rem; color:#000;" data-bs-target="#pessoa"></i>
                            </a>
                            <a href="#" class="btn btn-outline-danger btn-sm tooltips" data-bs-toggle="modal" data-bs-target="#modal{{ $perfil->id }}">
                                <span class="tooltiptext">Deletar</span>
                                <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i>
                            </a>
                            {{--  Modal de Exclusao --}}
                            <div class="modal fade" id="modal{{ $perfil->id }}" tabindex="-1"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color:#DC4C64">
                                            <h5 class="modal-title" id="exampleModalLabel" style="color:white">Exclusão de Perfil </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body" style="text-align: center; ">
                                            Tem certeza que deseja excluir o grupo<br /><span style="color:#DC4C64; font-weight: bold;">{{ $perfil->descricao }}</span>&#63;
                                        </div>
                                        <div class="modal-footer mt-3">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                                            <a type="button" class="btn btn-primary" href="/excluir-perfis/{{ $perfil->id }}">Confirmar</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        {{--  Fim do modal de Exclusao --}}
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>



<script src="caminho/para/bootstrap/js/bootstrap.bundle.min.js" async defer></script>
<link href="caminho/para/bootstrap/css/bootstrap.min.css" rel="stylesheet">


<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-tt="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })






</script>
@endsection
