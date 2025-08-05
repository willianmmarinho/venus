@extends('layouts.app')
@section('title', 'Cadastrar Membro')
@section('content')

    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <h4 class="card-title" style="font-size: 20px; text-align: left; color: gray; font-family: Calibri;">
                            CADASTRAR MEMBRO -
                            {{ Str::upper($grupo->nome) }} ({{ $grupo->nsigla }}) - {{ Str::upper($grupo->dia) }}
                            {{ date('H:i', strtotime($grupo->h_inicio)) }}/{{ date('H:i', strtotime($grupo->h_fim)) }}
                        </h4>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form class="form-horizontal mt-2" method="post" action="/incluir-membro-grupo/{{ $id }}">
                    @csrf

                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="id_associado" class="form-label">Nome</label>
                                <span class="tooltips">
                                    <span class="tooltiptext">Obrigatório</span>
                                    <span style="color:red">*</span>
                                </span>
                                <select class="form-select select2" aria-label=".form-select-lg example"
                                    name="id_associado">
                                    @foreach ($associado as $associados)
                                        <option value="{{ $associados->id }}">{{ $associados->nome_completo }} -
                                            {{ $associados->nr_associado }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col">
                            <div class="form-group">

                                <label for="tipo_status_pessoa" class="form-label">Status</label>
                                <span class="tooltips">
                                    <span class="tooltiptext">Obrigatório</span>
                                    <span style="color:red">*</span>
                                </span>
                                <select class="form-select" aria-label=".form-select-lg example" name="tipo_status_pessoa">
                                    @foreach ($tipo_status_pessoa as $tipo)
                                        <option value="{{ $tipo->id }}">{{ $tipo->tipos }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="id_funcao" class="form-label">Função</label>
                                <span class="tooltips">
                                    <span class="tooltiptext">Obrigatório</span>
                                    <span style="color:red">*</span>
                                </span>
                                <select class="form-select" aria-label=".form-select-lg example" name="id_funcao">
                                    @foreach ($tipo_funcao as $funcao)
                                        <option value="{{ $funcao->idf }}">{{ $funcao->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="dt_inicio" class="form-label">Data de Início</label>
                                <input type="date" class="form-control" name="dt_inicio" id="dt_inicio">
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row mt-1 justify-content-center">
                        <div class="d-grid gap-1 col-4 mx-auto">
                            <a class="btn btn-danger" href="/gerenciar-membro/{{ $id }}"
                                role="button">Cancelar</a>
                        </div>
                        <div class="d-grid gap-2 col-4 mx-auto">
                            <button type="submit" class="btn btn-primary">Confirmar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5'
            });
        });
    </script>
@endsection
