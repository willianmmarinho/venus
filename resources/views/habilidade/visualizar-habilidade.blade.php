@extends('layouts.app')
@section('title', 'Visualizar Habilidades')
@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col">
                    VISUALIZAR HABLIDADES
                </div>
            </div>
        </div>

        <div class="card-body">
            <form class="form-horizontal mt-2" method="post" action="/atualizar-habilidade/{{ $habilidade->id_pessoa}}/" disabled>
                @csrf

                <div class="row mt-3">
                    <div class="col-5">
                        <label for="id_pessoa" class="form-label">Nome</label>
                        <span class="tooltips">
                            <span class="tooltiptext">Obrigatório</span>
                            <span style="color:red">*</span>
                        </span>
                        <select name="id_pessoa" class="form-control" disabled>
                            <option value="{{ $habilidade->idm }}"> {{ $habilidade->nome_completo }} </option>
                            @foreach ($pessoas as $pessoa)
                                <option value="{{ $pessoa->id }}"> {{ $pessoa->nome_completo }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <label for="tipo_status_pessoa" class="form-label">Status</label>
                        <select class="form-control status" aria-label=".form-control" name="tipo_status_pessoa" disabled>
                            @foreach ($tipo_status_pessoa as $tipo)
                                <option value="{{ $tipo->id }}" {{ $habilidade->tipo == $tipo->tipos ? 'selected' : '' }}>{{ $tipo->tipos }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col">
                        <label for="motivo_status" class="form-label">Motivo status</label>
                        <select class="form-control motivo" aria-label=".form-select-lg example" name="motivo_status" id="motivo_status" disabled>
                            @if ($habilidade->motivo_status)
                                @foreach ($tipo_motivo_status_pessoa as $motivo)
                                    <option value="{{ $motivo->id }}" {{ $motivo->id == $habilidade->motivo_status ? 'selected' : '' }}>{{ $motivo->motivo }}</option>
                                @endforeach
                            @else
                                <option value=""></option>
                            @endif
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col">
                        <label for="id_habilidade" class="form-label"></label>
                    </div>
                    <div class="col">
                        <label for="data_inicio" class="form-label"></label>
                    </div>
                </div>

                {{-- Table --}}
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
                                            @php
                                                $checked = in_array($tipo->id, $arrayChecked);
                                                $data_inicio = $habilidadesIds->firstWhere('id_habilidade', $tipo->id)->data_inicio ?? '';
                                            @endphp
                                            @if ($checked)
                                                <tr class="habilidade-row" data-id="{{ $tipo->id }}">
                                                    <td>
                                                        <input class="form-check-input" type="checkbox" name="id_tp_habilidade[]" value="{{ $tipo->id }}" {{ $checked ? 'checked' : '' }} disabled>
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $tipo->tipo }}
                                                    </td>
                                                    <td>
                                                        <div class="form-group data_manifestou" name="id_habilidade" id="data_inicio_{{ $tipo->id }}">
                                                            <input type="date" class="form-control form-control-sm" name="data_inicio[{{ $tipo->id }}][]" value="{{ $data_inicio }}" disabled>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
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
                        <a class="btn btn-danger" href="/gerenciar-habilidade" role="button">Fechar</a>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

@endsection
