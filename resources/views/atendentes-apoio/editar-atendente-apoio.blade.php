@extends('layouts.app')
@section('title')
    Editar Atendentes Apoio
@endsection
@section('content')
    <br />
    <div class="container">
        <div class="card">
            <div class="card-header">
                Editar Atendentes Apoio
            </div>
            <div class="card-body">
                <br>
                <div class="row ">
                    <form method="POST" action="../atualizar-atendentes-apoio/{{ $nomes[0]->id }}">
                        @csrf
                        <div class="row col-10 offset-1" style="margin-top:none">
                            <div class="col-md-6 col-12">
                                <div>Nome
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                </div>
                                <input class="form-control" type="text" value="{{ $nomes[0]->nome_completo }}" Disabled>
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
                                                        <?php
                                                        $isChecked = in_array($diaSemana->id, $checkTheBox) ? 'checked' : '';
                                                        ?>

                                                        <input class="form-check-input checkbox-trigger check_io" type="checkbox"
                                                            name="checkbox[{{ $diaSemana->id }}]" id="{{ $diaSemana->id }}"
                                                            value="{{ $diaSemana->id }}" {{ $isChecked }}>
                                                    </td>
                                                    <td>{{ $diaSemana->nome }}</td>




                                                    <?php $i = 0; ?>

                                                    @foreach ($diasHorarios as $dia)
                                                        @if ($dia->id_dia == $diaSemana->id)
                                                        <?php $i = 1; ?>
                                                            <td>
                                                                <div class="data_io" id="data_inicio_{{ $diaSemana->id }}">
                                                                    <input type="time" class="form-control"
                                                                        aria-label="Sizing example input" name="dhInicio[{{ $diaSemana->id }}]"
                                                                        required="Required"
                                                                        id="data_ini_{{ $diaSemana->id }}"
                                                                        value="{{ $dia->dh_inicio }}" disabled>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="data_io" id="data_inicio_{{ $diaSemana->id }}">
                                                                    <input type="time" class="form-control"
                                                                        aria-label="Sizing example input" name="dhFim[{{ $diaSemana->id }}]"
                                                                        required="Required"
                                                                        id="data_ini_{{ $diaSemana->id }}"
                                                                        value="{{ $dia->dh_fim }}"disabled>
                                                                </div>
                                                            </td>

                                                        @endif
                                                    @endforeach

                                                    @if($i == 0)
                                                    <td>
                                                        <div class="data_io" id="data_inicio_{{ $diaSemana->id }}">
                                                            <input type="time" class="form-control"
                                                                aria-label="Sizing example input" name="dhInicio[{{ $diaSemana->id }}]"
                                                                required="Required"
                                                                id="data_ini_{{ $diaSemana->id }}"
                                                                 disabled>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="data_io" id="data_inicio_{{ $diaSemana->id }}">
                                                            <input type="time" class="form-control"
                                                                aria-label="Sizing example input" name="dhFim[{{ $diaSemana->id }}]"
                                                                required="Required"
                                                                id="data_ini_{{ $diaSemana->id }}"
                                                                disabled>
                                                        </div>
                                                    </td>
                                                    @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </center>
                        <center>
                            <div class="col-12" style="margin-top: 70px;">
                                <a href="/gerenciar-atendentes-apoio" class="btn btn-danger col-3">
                                    Cancelar
                                </a>
                                <button  class="btn btn-primary col-3 offset-3">
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
            $('.checkbox-trigger:checked').change();
        });

    </script>

@endsection
