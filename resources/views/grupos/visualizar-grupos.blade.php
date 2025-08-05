@extends('layouts.app')
@section('title', 'Visualizar Grupos')
@section('content')

    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                VISUALIZAR GRUPOS
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="container-fluid">
                            <form class="form-horizontal mt-2" method="post" action="/incluir-grupos">
                                @csrf
                                <div class="row">
                                    <div class="col">
                                        Número
                                        <select name="id" class="form-control" disabled>
                                            <option value="{{ $grupo[0]->id }}"> {{ $grupo[0]->id }}</option>
                                            @foreach ($tipo_motivo as $tipo_motivos)
                                                <option value="{{ $tipo_motivos->id }}"> {{ $tipo_motivos->id }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-5">
                                        Nome
                                        <input type="text" class="form-control" id="nome" name="nome" value="{{ $grupo[0]->nome }}" disabled>
                                    </div>

                                    <div class="col-3">
                                        Status
                                        <select name="status_grupo" class="form-control" disabled>
                                            <option value="{{ $grupo[0]->status_grupo }}"> {{ $grupo[0]->descricao1 }}</option>
                                            @foreach ($tipo_status_grupo as $tipos)
                                                <option value="{{ $tipos->id }}"> {{ $tipos->descricao1 }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-3">
                                        Motivo
                                        <select name="id_motivo_inativacao" class="form-control" disabled>
                                            <option value="{{ $grupo[0]->id_motivo_inativacao }}"> {{ $grupo[0]->descricao }}</option>
                                            @foreach ($tipo_motivo as $tipo_motivos)
                                                <option value="{{ $tipo_motivos->id }}"> {{ $tipo_motivos->descricao }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-3">
                                            <br>
                                            Tipo grupo
                                            <select name="nm_tipo_grupo" class="form-control" disabled>
                                                @foreach ($grupo as $item)
                                                    <option value="{{ $item->id }}" {{ $grupo[0]->nm_tipo_grupo == $item->id ? 'selected' : '' }}>
                                                        {{ $item->nm_tipo_grupo}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                    <div class="col">
                                        <br>
                                        Data início
                                        <input type="date" class="form-control" id="h_inicio" name="data_inicio" value="{{ $grupo[0]->data_inicio}}" disabled>
                                    </div>

                                    <div class="col">
                                        <br>
                                        Data fim
                                        <input type="date" class="form-control" id="h_fim" name="data_fim" value="{{ $grupo[0]->data_fim }}" disabled>
                                    </div>
                                    <div class="col">
                                        <br>
                                        Setor
                                        <select class="form-control" aria-label=".form-select-lg example" name="id_setor" required="required" disabled>
                                            @foreach ($setor as $setores)
                                            <option value="{{ $grupo[0]->id_setor }}" > {{ $grupo[0]->nm_setor}}</option>
                                                <option value="{{ $setores->id}}">{{ $setores->nome}}</option>
                                            @endforeach
                                        </select>

                                </div>
                                <div class="row justify-content-center">
                                    <div class="d-grid gap-1 col-4 mx-auto">
                                        <br>
                                        <a class="btn btn-danger" href="/gerenciar-grupos" role="button">Fechar</a>
                                    </div>

                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
