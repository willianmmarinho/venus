@extends('layouts.app')

@section('title')
    Gerenciar Versões
@endsection

@section('content')

    <div class="container-fluid";>

        <div class="container-fluid";>

            <h4 class="card-title" class="card-title"
                style="font-size:20px; text-align: left; color: gray; font-family:calibri">
                GERENCIAR VERSÕES</h4>
            <div class="col-12">
                <div class="row justify-content-center">
                    <form action="" class="form-horizontal mt-4" method="GET">
                        <div class="row">
                            <div class="col-4">Nome
                                <input class="form-control" type="text" maxlength="45" id="1" name="nome" value="{{ $pesquisaNome }}">
                            </div>

                            <div class="col"><br>
                                <input class="btn btn-light btn-sm me-md-2"
                                    style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit"
                                    value="Pesquisar">
                                <a href="/gerenciar-versoes"><input class="btn btn-light btn-sm me-md-2"
                                        style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;"
                                        type="button" value="Limpar"></a>
                    </form>
                    <a href="/incluir-versoes"><input class="btn btn-success btn-sm me-md-2" style="font-size: 0.9rem;"
                            type="button" value="Nova Versão +"></a>
                </div>
            </div>
        </div>
        <hr>


        <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
            <thead style="text-align: center;">
                <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                    <th class="col">ID</th>
                    <th class="col-6">VERSÃO</th>
                    <th class="col">AÇÕES</th>
                </tr>
            </thead>
            <tbody style="font-size: 14px; color:#000000; text-align:center;">
                @foreach ($versoes as $versao)
                    <tr>

                        <td>{{ $versao->id }}</td>
                        <td>{{ $versao->versao }}</td>
                        <td>
                            <a href="/editar-versoes/{{ $versao->id }}" type="button"
                                class="btn btn-outline-warning btn-sm tooltips">
                                <span class="tooltiptext">Editar</span>
                                <i class="bi bi-pencil" style="font-size: 1.1rem; color:#000;"></i>
                            </a>
                            <a href="/visualizar-versoes/{{ $versao->id }}" type="button"
                                class="btn btn-outline-primary btn-sm tooltips">
                                <span class="tooltiptext">Visualizar</span>
                                <i class="bi bi-search" style="font-size: 1.1rem; color:#000;"></i>
                            </a>


                            <a type="button" class="btn btn-outline-danger btn-sm tooltips"
                                data-bs-target="#inativa{{ $versao->id }}" data-bs-toggle="modal">
                                <span class="tooltiptext">Excluir</span>
                                <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i></a>

                            {{-- modal de inativação --}}
                            <div class="modal fade" id="inativa{{ $versao->id }}" data-bs-keyboard="false" tabindex="-1"
                                aria-labelledby="inativarLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color:#DC4C64;color:white">
                                            <h1 class="modal-title fs-5" id="inativarLabel">Inativação</h1>
                                            <button data-bs-dismiss="modal" type="button" class="btn-close"
                                                aria-label="Close"></button>
                                        </div>
                                        <br />
                                        <div class="modal-body">
                                            <label for="recipient-name" class="col-form-label" style="font-size:17px">Tem
                                                certeza que deseja inativar:<br /><span
                                                    style="color:#DC4C64; font-weight: bold;">{{ $versao->versao }}</span>&#63;</label>
                                            <br />

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" data-bs-dismiss="modal"
                                                class="btn btn-danger">Cancelar</button>
                                            <a href="/excluir-versoes/{{ $versao->id }}" class="btn btn-primary">Confirmar</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- fim modal de inativação --}}
                            </form>


                        </td>
                    </tr>
                @endforeach


            </tbody>
        </table>
    </div>
@endsection
