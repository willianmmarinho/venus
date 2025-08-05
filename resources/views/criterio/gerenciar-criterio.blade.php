@extends('layouts.app')
@section('content')
    <br>
    <div class="container">
        <div class="card" style="font-size:20px; text-align: left; color: gray; font-family:calibri">
            <div class="card-header">
                Gerenciar Critério de Atividade
            </div>
            <div class="card-body">
                <h5 class="card-title">
                    {{-- // Formulário de Pesquisa --}}
                    <div class="row">
                        <div class="col-2 col-form-label">
                            <label for="_idsetor">Setor</label>
                            <select class="form-select" aria-label="Default select example" name="nome_setor" id="id_setor">
                                <option value="">Selecione</option>
                                @foreach ($setores as $setor)
                                    <option value="{{ $setor->ids }}" {{ $setor->ids == $snome ? 'selected' : '' }}>
                                        {{ $setor->sigla }} </option>
                                @endforeach

                            </select>
                        </div>
                    </div>
                </h5>

                <p class="card-text">
                <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                    <thead>
                        <thead style="text-align: center;">
                            <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                                <th class="col-auto">ID</th>
                                <th class="col-auto">ATIVIDADE</th>
                                <th class="col-auto">PRÉ-REQUISITO</th>
                                <th class="col-auto">TIPO</th>
                                <th class="col-auto">INICIO</th>
                                <th class="col-auto">TERMINO</th>
                                <th class="col-auto">SETOR</th>
                                <th class="col-auto">STATUS</th>
                                <th class="col-auto">AÇÕES</th>
                            </tr>
                        </thead>
                    </thead>
                    <tbody></tbody>
                </table>
                </p>
            </div>
        </div>
    </div>
@endsection
