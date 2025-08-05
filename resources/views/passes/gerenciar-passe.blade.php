@extends('layouts.app')

@section('title')
    Gerenciar Passes
@endsection

@section('content')
    <div class="container-fluid">
        <h4 class="card-title" style="font-size:20px; text-align:left; color:gray; font-family:calibri">
            GERENCIAR PASSES
        </h4>
        <br>
            <form method="GET" action="/gerenciar-passe">
            <div class="row">
                    <div class="col-lg-4 col-12">
                        Grupo
                        <select class="form-select select2" name="grupo"  data-width="100%">>
                            <option value="">Selecione</option>
                            @foreach ($grupos as $gruposs)
                                <option value="{{ $gruposs->idg }}"
                                    {{ request('nome_grupo') == $gruposs->idg ? 'selected' : '' }}>
                                    {{ $gruposs->nomeg }} ({{ $gruposs->sigla }})-{{ $gruposs->dia_semana }}
                                    |
                                    {{ date('H:i', strtotime($gruposs->h_inicio)) }}/{{ date('H:i', strtotime($gruposs->h_fim)) }}
                                    | Sala {{ $gruposs->sala }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Pesquisar Button -->
                    <div class="col-lg-2 col-md-12">
                        <br />
                        <center>
                            <button class="btn btn-light btn-sm col-6" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000;"
                                type="submit">Pesquisar</button>
                                <a href="/gerenciar-passe">
                                    <button class="btn btn-light btn-sm col-5" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000;"
                                        type="button">Limpar</button>
                                </a>
                        </center>
                    </div>
                </div>
            </form>
        <br>
        <div class="table">
            <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                <thead style="text-align: center;">
                    <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th>GRUPO</th>
                            <th>DIA</th>
                            <th>INÍCIO</th>
                            <th>FIM</th>
                            <th>AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color:#000000; text-align: center;">
                        @foreach ($reuniao as $reuni)
                            <tr>
                                <td>{{ $reuni->nomeg }}</td>
                                <td>{{ $reuni->nomed }}</td>
                                <td>{{ date('H:i:s', strtotime($reuni->h_inicio)) }}</td>
                                <td>{{ date('H:i:s', strtotime($reuni->h_fim)) }}</td>
                                <td>
                                    <!-- Botão para abrir o modal de presença -->
                                    <button type="button" class="btn btn-outline-success btn-sm tooltips"
                                        data-bs-toggle="modal" data-bs-target="#presenca{{ $reuni->idr }}">
                                        <span class="tooltiptext">Novo passe</span>
                                        <i class="bi bi-clipboard-check" style="font-size: 1rem; color:#000;"></i>
                                    </button>
                                    {{-- Modal de presença --}}
                                    <div class="modal fade" id="presenca{{ $reuni->idr }}" tabindex="-1"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <form method="post" action="/incluir-passe/{{ $reuni->idr }}">
                                            @csrf
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header" style="background-color:#198754;color:white">
                                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Quantidade de
                                                            passes
                                                        </h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="recipient-name" class="col-form-label"
                                                                style="font-size:14px">
                                                                Registrar de quantidade de passes no grupo
                                                                <br />
                                                                <span style="color:#198754">{{ $reuni->nomeg }}</span>&#63;
                                                            </label>
                                                        </div>
                                                        <center>
                                                            <div class="mb-2 col-10">
                                                                <label class="col-form-label">Insira o
                                                                    <span style="color:#198754">número de passes:</span>
                                                                </label>
                                                                <input type="number" class="form-control" name="acompanhantes"
                                                                    placeholder="0" min="1" max="500" required>
                                                            </div>
                                                        </center>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger"
                                                            data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-primary">Confirmar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <a href="/editar-passe/{{ $reuni->idr }}"
                                            class="btn btn-outline-warning btn-sm tooltips">
                                            <span class="tooltiptext" style="z-index:10000">Editar</span>
                                            <i class="bi bi-pencil" style="font-size: 1rem; color:#000;"></i>
                                        </a>
                                    <a href="/visualizar-passe/{{ $reuni->idr }}"
                                        class="btn btn-outline-primary btn-sm tooltips">
                                        <span class="tooltiptext" style="z-index:10000">Visualizar</span>
                                        <i class="bi bi-search" style="font-size: 1rem; color:#000;"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
    </div>
@endsection
