@extends('layouts.app')
@section('title')
   Incluir Plantonistas
@endsection
@section('content')
    <br />
    <div class="container">
        <div class="card">
            <div class="card-header">
                Incluir Plantonistas
            </div>
            <div class="card-body">
                <br>
                <div class="row justify-content-start">
                    <form method="POST" action="/armazenar-atendentes-plantonistas">
                        @csrf
                        <div class="row col-10 offset-1" style="margin-top:none">
                            <div class="col-md-6 col-12">
                                <div>Nome
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                </div>
                                <select class="form-select select2" aria-label="Default select example" required
                                    name="nome">
                                    <option value=""></option>
                                    @foreach ($nomes as $nome)
                                        <option value="{{ $nome->id }}">{{ $nome->nome_completo }}</option>
                                    @endforeach
                                </select>
                            </div>





                        </div>
                        <br />
                        <center>
                            <div class="table-responsive col-10">
                                <div class="table">
                                    <table
                                        class="table table-sm table-striped table-bordered border-secondary table-hover align-middle text-center">
                                        <thead>
                                            <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                                                <th scope="col"></th>
                                                <th scope="col">Dia da Semana</th>
                                                <th scope="col">Inicio</th>
                                                <th scope="col">Final</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach ($dias as $diaSemana)
                                                <tr>
                                                    <td>
                                                        <input class="form-check-input checkbox-trigger" type="checkbox"
                                                            name="checkbox[]" id="{{ $diaSemana->id }}"
                                                            value="{{ $diaSemana->id }}">
                                                    </td>
                                                    <td>{{ $diaSemana->nome }}</td>
                                                    <td>
                                                        <div class="data_io" id="data_inicio_{{ $diaSemana->id }}">
                                                            <input type="time" class="form-control"
                                                                aria-label="Sizing example input" name="dhInicio[]"
                                                                required="Required" id="data_ini_{{ $diaSemana->id }}"
                                                                disabled>
                                                        </div>
                                                    </td>

                                                    <td>

                                                        <div class="data_io" id="data_inicio_{{ $diaSemana->id }}">
                                                            <input type="time" class="form-control"
                                                                aria-label="Sizing example input" name="dhFim[]"
                                                                required="Required" id="data_ini_{{ $diaSemana->id }}"
                                                                disabled>
                                                        </div>

                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </center>
                        <center>
                            <div class="col-12" style="margin-top: 70px;">
                                <a href="/gerenciar-atendentes-plantonistas" class="btn btn-danger col-3">
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
            $('.checkbox-trigger').change(function() {
                var isChecked = $(this).prop('checked');
                var id = $(this).attr('id');

                if (isChecked) {
                    $('#data_inicio_' + id + ' input').prop('disabled', false);
                } else {
                    $('#data_inicio_' + id + ' input').prop('disabled', true);
                }
            });
        });

        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5'
            });
        });
    </script>
@endsection
