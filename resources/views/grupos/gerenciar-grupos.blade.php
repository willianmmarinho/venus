@extends('layouts.app')

@section('title')
    Gerenciar Grupos
@endsection

@section('content')
    <div class="container-fluid">
        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">
            GERENCIAR GRUPOS
        </h4>
        <form action="{{ route('nomes') }}" class="form-horizontal mt-4" method="GET">
            <div class="row justify-content-center">
                <div class="col-3">Nome
                    <select class="form-select select2" name="nome_grupo">
                        <option value="">Selecione o Grupo</option>
                        @foreach ($grupo as $grupos)
                            <option value="{{ $grupos->idg }}"
                                {{ request('nome_grupo') == $grupos->idg ? 'selected' : '' }}>
                                {{ $grupos->nomeg }} - {{ $grupos->sigla }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-3">Setor
                    <select class="form-select select2" name="nome_setor">
                        <option value="">Selecione o Setor</option>
                        @foreach ($setor as $setores)
                            <option value="{{ $setores->id }}"
                                {{ request('nome_setor') == $setores->id ? 'selected' : '' }}>
                                {{ $setores->nome }}-{{ $setores->sigla }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-2">Status
                    <select class="form-select select2" name="tipo_status_grupo">
                        <option value="">Selecione o Status</option>
                        @foreach ($tipo_status_grupo as $tipos)
                            <option value="{{ $tipos->id }}"
                                {{ request('tipo_status_grupo') == $tipos->id ? 'selected' : '' }}>
                                {{ $tipos->descricao }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col"><br />
                    <input class="btn btn-light btn-sm me-md-2"
                        style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit"
                        value="Pesquisar">
                    <a href="/gerenciar-grupos"><input class="btn btn-light btn-sm me-md-2"
                            style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                            value="Limpar"></a>
                </div>
                <div class="col"><br />
                    <a href="/criar-grupos"><input class="btn btn-success btn-sm me-md-2" style="font-size: 0.9rem;"
                            type="button" value="Novo grupo +"></a>
                </div>
            </div>
        </form>
        <hr>
        Quantidade de grupos: {{ $contar }}
    </div>

    <div class="row" style="text-align:center;">
        <div class="col">
            <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                <thead style="text-align: center;">
                    <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                        <th class="col">Nr</th>
                        <th class="col-3">NOME</th>
                        <th class="col">DATA INÍCIO</th>
                        <th class="col">DATA FIM</th>
                        <th class="col">TIPO GRUPO</th>
                        <th class="col">SETOR</th>
                        <th class="col">STATUS GRUPO</th>
                        <th class="col">AÇÕES</th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px; color:#000000; text-align:center;">
                    @foreach ($lista as $listas)
                        <tr>
                            <td> {{ $listas->id }} </td>
                            <td> {{ $listas->nome }} </td>
                            <td> {{ date('d/m/Y', strtotime($listas->data_inicio)) }} </td>
                            <td> {{ $listas->data_fim }} </td>
                            <td> {{ $listas->nm_tipo_grupo }} </td>
                            <td> {{ $listas->nm_setor }} - {{ $listas->sigset }} </td>
                            <td> {{ $listas->descricao1 }} </td>
                            <td>
                                <a href="/editar-grupos/{{ $listas->id }}" type="button"
                                    class="btn btn-outline-warning btn-sm tooltips">
                                    <span class="tooltiptext">Editar</span>
                                    <i class="bi bi-pencil" style="font-size: 1rem; color:#000;"></i>
                                </a>
                                <a href="/visualizar-grupos/{{ $listas->id }}" type="button"
                                    class="btn btn-outline-primary btn-sm tooltips">
                                    <span class="tooltiptext">Visualizar</span>
                                    <i class="bi bi-search" style="font-size: 1rem; color:#000;"
                                        data-bs-target="#pessoa"></i>
                                </a>

                                <!-- Botão que aciona o modal -->
                                <button type="button" class="btn btn-outline-danger btn-sm tooltips" data-bs-toggle="modal"
                                    data-bs-target="#inativa{{ $listas->id }}">
                                    <span class="tooltiptext">Inativar</span>
                                    <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i>
                                </button>

                                <!-- Modal de confirmação de  -->
                                <form action="deletar-grupos/{{ $listas->id }}" method="POST">
                                    @csrf <!-- Adiciona o token CSRF para proteção -->
                                    <div class="modal fade" id="inativa{{ $listas->id }}" data-bs-keyboard="false"
                                        tabindex="-1" aria-labelledby="inativarLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header" style="background-color:#DC4C64;color:white">
                                                    <h1 class="modal-title fs-5" id="inativarLabel">Inativação</h1>
                                                    <button data-bs-dismiss="modal" type="button" class="btn-close"
                                                        aria-label="Close"></button>
                                                </div>
                                                <br />
                                                <div class="modal-body">
                                                    <label for="recipient-name" class="col-form-label"
                                                        style="font-size:17px">
                                                        Tem certeza que deseja inativar:<br />
                                                        <span style="color:#DC4C64; font-weight: bold;">{{ $listas->nome }}
                                                            - {{ $listas->sigset }}</span>&#63;
                                                    </label>
                                                    <br />

                                                    <center>
                                                        <div class="mb-2 col-10">
                                                            <label class="col-form-label">Insira o motivo da
                                                                <span style="color:#DC4C64">inativação:</span>
                                                            </label>
                                                            <br>
                                                            <select class="form-select teste1" name="motivo" required>
                                                                @foreach ($tipo_motivo as $motivos)
                                                                    <option value="{{ $motivos->id }}">
                                                                        {{ $motivos->descricao }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </center>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" data-bs-dismiss="modal"
                                                        class="btn btn-danger">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary">Confirmar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div class="d-flex justify-content-center">
        {{ $lista->links('pagination::bootstrap-5') }}
    </div>
@endsection
