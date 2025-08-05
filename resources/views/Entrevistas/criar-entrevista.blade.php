@extends('layouts.app')
@section('title', 'Agendar Entrevista')
@section('content')
    <div class="container">
        <br>
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">AGENDAR ENTREVISTA</div>
                </div>
            </div>
            <br>
            <div class="card-body">
                <form class="form-horizontal mt-2" method="post"
                    action="{{ url('/incluir-entrevista/' . $encaminhamento->id) }}">
                    @csrf

                    <div class="row mb-5">
                        <div class="col">
                            <label for="id_encaminhamento_nome" class="form-label">Nome</label>
                            <input class="form-control" id="id_encaminhamento_nome" name="id_encaminhamento_nome"
                                value="{{ $informacoes->nome_pessoa }}" disabled>
                        </div>
                        <div class="col">
                            <label for="id_encaminhamento_telefone" class="form-label">Telefone</label>
                            <input class="form-control" id="id_encaminhamento_telefone" name="id_encaminhamento_telefone"
                                value="{{$informacoes->ddd ? '(' . $informacoes->ddd . ')' : null}} {{ $informacoes->celular }}" disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="data" class="form-label">Data</label>
                            <span class="tooltips">
                                <span class="tooltiptext">Obrigatório</span>
                                <span style="color:red">*</span>
                            </span>
                            <input type="date" class="form-control" id="data" name="data" required>
                        </div>

                        <div class="col">
                            <label for="hora" class="form-label">Hora</label>
                            <span class="tooltips">
                                <span class="tooltiptext">Obrigatório</span>
                                <span style="color:red">*</span>
                            </span>
                            <input type="time" class="form-control" id="hora" name="hora" required>
                        </div>
                    </div>
                    <br>
            </div>
        </div>
        <br />
        <div id="accordion" class="card" {{ $informacoes->id_tipo_entrevista == 3 ? 'hidden' : null  }}>
            <div class="card-header" id="headingOne">
                <siv class="mb-0">SALA</siv>

            </div>
            <div class="card-body">
                <div class="row mb-5">
                    <div class="col">
                        <label for="id_sala" class="form-label">Numero</label>
                        <span class="tooltips">
                            <span class="tooltiptext">Obrigatório</span>
                            <span style="color:red">*</span>
                        </span>
                        <select class="form-select" id="id_sala" name="id_sala" {{ $informacoes->id_tipo_entrevista != 3 ? 'required' : null  }} >
                            <option value=""></option>
                            @foreach ($salas as $sala)
                                <option value="{{ $sala->id }}" data-nome="{{ $sala->nome }}"
                                    data-numero="{{ $sala->numero }}" data-localizacao="{{ $sala->nome_localizacao }}">
                                    {{ $sala->numero }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <label for="numero" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" readonly>
                    </div>
                    <div class="col">
                        <label for="localizacao" class="form-label">Localização</label>
                        <input type="text" class="form-control" id="localizacao" name="localizacao" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <br>
    <br>
    <center>
    <div class="justify-content-center">
    <div class= "col-11 row mt-1 ">
        <div class="d-grid gap-1 col-4 mx-auto">
            <a class="btn btn-danger" href="/gerenciar-entrevistas" role="button">Cancelar</a>
        </div>
        <div class="d-grid gap-1 col-4 mx-auto">
            <button type="submit" class="btn btn-primary">Confirmar</button>
        </div>
    </div>
    </div>
    </center>
    </form>

    <script>
        // Obtém a data atual no formato 'YYYY-MM-DD'
        var dataAtual = new Date().toISOString().split('T')[0];

        // Define a data mínima no campo de entrada
        document.getElementById('data').setAttribute('min', dataAtual);
    </script>
    <script>
        document.getElementById('id_sala').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            document.getElementById('nome').value = selectedOption.getAttribute('data-nome');
            document.getElementById('localizacao').value = selectedOption.getAttribute('data-localizacao');
        });
    </script>

@endsection
