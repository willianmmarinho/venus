@extends('layouts.app')
@section('title', 'Editar Entrevista')
@section('content')
    <div class="container">
        <br>
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">EDITAR ENTREVISTA</div>
                </div>
            </div>
            <br>
            <div class="card-body">
                <form class="form-horizontal mt-2" method="post" action="/atualizar-entrevista/{{ $id }}">
                    @csrf

                    <div class="row mb-5">
                        <div class="col">
                            <label for="id_encaminhamento" class="form-label">Nome do assistido</label>
                            <select class="form-select" id="id_encaminhamento" name="id_encaminhamento"disabled>
                                <option value="{{ $id }}">{{ $entrevistas->nome_completo }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-5">
                        <div class="col">
                            <label for="id_entrevistador" class="form-label">Entrevistador</label>
                            <span class="tooltips">
                                <span class="tooltiptext">Obrigatório</span>
                                <span style="color:red">*</span>
                            </span>
                            <select class="form-select select2 teste" id="id_entrevistador" name="entrevistador" {{ $entrevistas->id_entrevistador == null ? 'disabled' : '' }}>
                                 @foreach ($membros as $membro )
                                 <option value="{{ $membro->id_associado }}" {{$membro->id_associado == $entrevistas->id_entrevistador ? 'selected' : '' }}>{{ $membro->nome_entrevistador }}</option>
                                 @endforeach
                            </select>
                        </div>
                    </div>
                    <fieldset>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="data" class="form-label">Data</label>
                                <span class="tooltips">
                                    <span class="tooltiptext">Obrigatório</span>
                                    <span style="color:red">*</span>
                                </span>
                                <input type="date" class="form-select" id="data" name="data"
                                    value="{{ $entrevistas->data }}">
                            </div>
                            <div class="col">
                                <label for="hora" class="form-label">Hora</label>
                                <span class="tooltips">
                                    <span class="tooltiptext">Obrigatório</span>
                                    <span style="color:red">*</span>
                                </span>
                                <input type="time" class="form-select" id="hora" name="hora"
                                    value="{{ $entrevistas->hora }}">
                            </div>
                        </div>
                    </div>
                </div>

                        <br>
                        <div class="form-group row">
                            <div class="col">
                                <div id="accordion" class="card" {{ $entrevistas->id_tipo_entrevista == 3 ? 'hidden' : null  }}>
                                    <div class="card-header" id="headingOne">
                                        <h5 class="mb-0">
                                            Sala
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col">
                                                <label for="numero_sala" class="form-label">Número</label>
                                                <span class="tooltips">
                                                    <span class="tooltiptext">Obrigatório</span>
                                                    <span style="color:red">*</span>
                                                </span>
                                                <select class="form-select" id="numero_sala" name="numero_sala" {{ $entrevistas->id_tipo_entrevista != 3 ? 'required' : null  }}>
                                                    <option value="{{ $entrevistas->sala_id }}">{{ $entrevistas ? $entrevistas->numero : '' }}</option>
                                                    @foreach ($salas as $sala)
                                                        <option value="{{ $sala->id }}" data-nome="{{ $sala->nome }}"
                                                            data-localizacao="{{ $sala->nome_localizacao }}">
                                                            {{ $sala->numero }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col">
                                                <label for="nome" class="form-label">Nome</label>
                                                <input type="text" class="form-control" id="nome" name="nome"
                                                    value="{{ $entrevistas ? $entrevistas->nome : '' }}" readonly>
                                            </div>
                                            <div class="col">
                                                <label for="localizacao" class="form-label">Localização</label>
                                                <input type="text" class="form-control" id="localizacao"
                                                    name="localizacao"
                                                    value="{{ $entrevistas ? $entrevistas->local : '' }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <br>

                    <br>
                    <div class="row mt-4 justify-content-center">
                        <div class="d-grid gap-1 col-4 mx-auto">
                            <a class="btn btn-danger" href="/gerenciar-entrevistas" role="button">Cancelar</a>
                        </div>
                        <div class="d-grid gap-1 col-4 mx-auto">
                            <button type="submit" class="btn btn-primary">Confirmar</button>
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

    <script>
        // Obtém a data atual no formato 'YYYY-MM-DD'
        var dataAtual = new Date().toISOString().split('T')[0];

        // Define a data mínima no campo de entrada
        document.getElementById('data').setAttribute('min', dataAtual);
    </script>

    <script>
        document.getElementById('numero_sala').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            document.getElementById('nome').value = selectedOption.getAttribute('data-nome');
            document.getElementById('localizacao').value = selectedOption.getAttribute('data-localizacao');
        });

        if ( {{ $entrevistas->id_entrevistador == null ? 1 : 0}} ) { //Deixa o select status como padrao vazio
            $(".teste").prop("selectedIndex", -1);
        }
    </script>
@endsection
