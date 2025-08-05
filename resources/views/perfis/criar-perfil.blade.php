@extends('layouts.app')
@section('title')
   Incluir Perfil
@endsection
@section('content')
    <br />
    <div class="container">
        <div class="card">
            <div class="card-header">
                Incluir Perfil
            </div>
            <div class="card-body">
                <br>
                <div class="row justify-content-start">
                    <form method="POST" action="/armazenar-perfis">
                        @csrf
                        <div class="row col-10 offset-1" style="margin-top:none">
                            <div class="col-12">
                                Nome
                                <span class="tooltips">
                                    <span class="tooltiptext">Obrigatório</span>
                                    <span style="color:red">*</span>
                                </span>
                                <input type="text" class="form-control" id="nome" name="nome" maxlength="40"
                                    required="required">
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
                                    <a href="/gerenciar-perfis" class="btn btn-danger col-3">
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
