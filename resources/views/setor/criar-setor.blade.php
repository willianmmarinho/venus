@extends('layouts.app')
@section('title')
    Incluir Setor
@endsection
@section('content')
    <br />
    <div class="container">
        <div class="card">
            <div class="card-header">
                Incluir Setor
            </div>
            <div class="card-body">
                <br>
                <div class="row justify-content-start">
                    <form method="POST" action="/armazenar-setor">
                        @csrf
                        <div class="row col-10 offset-1" style="margin-top:none">
                            <div class="col-12">
                                Setor
                                <span class="tooltips">
                                    <span class="tooltiptext">Obrigatório</span>
                                    <span style="color:red">*</span>
                                </span>
                                <select class="form-select select2" name="setor">
                                    @foreach ($setores as $setor)
                                        <option value="{{ $setor->id }}">{{ $setor->nome }}</option>
                                    @endforeach
                                </select>
                                <br />
                            </div>
                            <div class="col-12">
                                Funcionalidades Autorizadas
                                <span class="tooltips">
                                    <span class="tooltiptext">Obrigatório</span>
                                    <span style="color:red">*</span>
                                </span>
                                <select class="form-select select2" name="rotas[]" multiple>
                                    @foreach ($rotas as $rota)
                                        <option value="{{ $rota->id }}">{{ $rota->nome }}</option>
                                    @endforeach
                                </select>

                            </div>


                            <center>
                                <div class="col-12" style="margin-top: 50px;">
                                    <a href="/gerenciar-setor" class="btn btn-danger col-3">
                                        Cancelar
                                    </a>
                                    <button type = "submit" class="btn btn-primary col-3 offset-3">
                                        Confirmar
                                    </button>
                                </div>
                            </center>
                    </form>
                </div>
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
