@extends('layouts.app')
@section('title', 'Cadastrar Habilidades')
@section('content')


    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        CADASTRAR HABILIDADES
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form class="form-horizontal mt-2" method="post" action="/incluir-habilidade">
                    @csrf

                    <div class="row">
                        <div class="col">
                    Nome
                    <span class="tooltips">
                        <span class="tooltiptext">Obrigatório</span>
                        <span style="color:red">*</span>
                    </span>
                            <select class="form-select select2" aria-label=".form-select-lg example"
                            name="id_pessoa">
                            @foreach ($pessoas as $pessoa)
                                <option value="{{ $pessoa->idp }}">{{ $pessoa->nome_completo }}</option>
                            @endforeach
                        </select>
                        </div>
                        <div class="col">
                        Status
                            <select class="form-control" aria-label=".form-select-lg example"
                                name="tipo_status_pessoa" disabled>
                                @foreach ($tipo_status_pessoa as $tipo)
                                    <option value="{{ $tipo->id }}">{{ $tipo->tipos }}</option>
                                @endforeach
                            </select>
                        </div>
                    <div class="row mt-3">
                        <div class="col">
                            <label for="id_habilidade" class="form-label"></label>
                            <div class="table-responsive">
                                <div class="table">
                                    <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle text-center">
                                        <thead>
                                            <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                                                <th scope="col"></th>
                                                <th scope="col">Tipo de habilidade</th>
                                                <th scope="col">Data que manifestou</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tipo_habilidade as $tipo)
                                                <tr>
                                                    <td>
                                                        <input class="form-check-input" type="checkbox" name="id_tp_habilidade[]"
                                                            value="{{ $tipo->id }}">
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $tipo->tipo }}
                                                    </td>
                                                    <td>
                                                        <div class="form-group data_manifestou" name="id_habilidade_medium"
                                                            id="data_inicio_{{ $tipo->id }}">
                                                            <input type="hidden" name="id_medium" value="{{ $id_habilidade }}">
                                                            @if (old("data_inicio.$tipo->id"))
                                                                @foreach (old("data_inicio.$tipo->id") as $oldDate)
                                                                    <input type="date" class="form-control form-control-sm"
                                                                        name="data_inicio[{{ $tipo->id }}][]"
                                                                        value="{{ $oldDate }}">
                                                                @endforeach
                                                            @else
                                                                <input type="date" class="form-control form-control-sm"
                                                                    name="data_inicio[{{ $tipo->id }}][]">
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <style>
                        /* Estilo mais visível e maior para o checkbox */
                        .table th input[type="checkbox"],
                        .table td input[type="checkbox"] {
                            width: 17px; /* Ajusta a largura do checkbox */
                            height: 17px; /* Ajusta a altura do checkbox */
                            cursor: pointer; /* Adiciona o cursor de ponteiro ao passar sobre o checkbox */
                            border: 2px solid #000; /* Adiciona borda preta ao checkbox */
                        }
                    </style>
                    <div class="row mt-1 justify-content-center">
                        <div class="d-grid gap-1 col-4 mx-auto">
                            <a class="btn btn-danger" href="/gerenciar-habilidade" role="button">Cancelar</a>
                        </div>
                        <div class="d-grid gap-2 col-4 mx-auto">
                            <button type="submit" class="btn btn-primary">Confirmar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Adicione antes do fechamento da tag </body> -->
     <script>
        $(document).ready(function() {
            $('.data_manifestou')
                .hide()
                .find('input[type=date]');


            $('[name^=id_tp_habilidade]').change(function() {
                $('.data_manifestou')
                    .hide()
                    .find('input[type=date]');


                $('[name^=id_tp_habilidade]:checked').each(function() {
                    var tipoId = $(this).val();
                    $('#data_inicio_' + tipoId)
                        .show()
                        .find('input[type=date]');

                });
            });
        });
    </script>
@endsection
