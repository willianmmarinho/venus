@extends('layouts.app')
@section('title', 'Agendar Entrevistador')
@section('content')
    <div class="container">
        <br>
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">AGENDAR ENTREVISTADOR</div>
                </div>
            </div>
            <br>
            <div class="card-body">
                <form class="form-horizontal mt-2" method="post" action="/incluir-entrevistador/{{ $encaminhamento->id }}">
                    @csrf
                    <div class="row mb-5">
                        <div class="col">
                            <label for="id_encaminhamento" class="form-label">Nome</label>
                            <input class="form-control" id="id_encaminhamento" name="id_encaminhamento"
                                value="{{ $entrevistas->nome_completo }}" disabled>
                        </div>
                    </div>

                    <div class="row mb-5">
                        <div class="col">
                            <label for="id_entrevistador" class="form-label">Entrevistador</label>
                            <span class="tooltips">
                                <span class="tooltiptext">Obrigatório</span>
                                <span style="color:red">*</span>
                            </span>
                            <select class="form-select select2" id="id_entrevistador" name="id_entrevistador" required>
                                @if (!empty($entrevistas->id_entrevistador))
                                    <option value="{{ $entrevistas->id_entrevistador }}">
                                        {{ $entrevistas->nome_completo_pessoa_entrevistador }}</option>
                                @endif
                                @foreach ($membros as $membro)
                                    <option value="{{ $membro->id_associado }}">{{ $membro->nome_completo }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label for="data" class="form-label">Data</label>
                            <input type="date" class="form-control" id="data" name="data"
                                value="{{ $entrevistas->data }}" disabled>
                        </div>
                        <div class="col">
                            <label for="hora" class="form-label">Hora</label>
                            <input type="time" class="form-control" id="hora" name="hora"
                                value="{{ $entrevistas->hora }}" disabled>
                        </div>
                    </div>
            </div>
        </div>
        <br>
        <div class="form-group row">
            <div class="col">
                <div id="accordion" class="card">
                    <div class="card-header" id="headingOne">
                        <h5 class="mb-0">Sala</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-5">
                            <div class="col">
                                <label for="id_sala" class="form-label">Sala</label>
                                <select class="form-control" id="id_sala" name="id_sala" disabled>
                                    <option>{{ $entrevistas->nome }}</option>
                                </select>
                            </div>
                            <div class="col">
                                <label for="numero" class="form-label">Número</label>
                                <input type="text" class="form-control" id="numero" name="numero"
                                    value="{{ $entrevistas ? $entrevistas->numero : '' }}" readonly disabled>
                            </div>
                            <div class="col">
                                <label for="localizacao" class="form-label">Localização</label>
                                <input type="text" class="form-control" id="localizacao" name="localizacao"
                                    value="{{ $entrevistas ? $entrevistas->local : '' }}" readonly disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row mt-4 justify-content-center">
            <div class="d-grid gap-1 col-4 mx-auto">
                <a class="btn btn-danger" href="/gerenciar-entrevistas" role="button">Cancelar</a>
            </div>
            <div class="d-grid gap-1 col-4 mx-auto">
                <button type="submit" class="btn btn-primary">Confirmar</button>
            </div>
        </div>
        </form>
    </div>
    </div>
    </div>

    <script>
        // Obtém a data atual no formato 'YYYY-MM-DD'
        var dataAtual = new Date().toISOString().split('T')[0];

        // Define a data mínima no campo de entrada
        document.getElementById('data').setAttribute('min', dataAtual);
    </script>

@endsection
