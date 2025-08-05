@extends('layouts.app')

@section('title')
     Lista de  Tratamentos
@endsection

@section('content')
    <div class="container-fluid";>
        <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">
            LISTA DE  TRATAMENTOS</h4>
        <div class="col-12">
            <div class="row justify-content-center">
                <div>
                    <form action="{{ route('RI') }}" class="form-horizontal mt-4" method="GET">
                        <div class="row">
                            <div class="modal fade" id="filtros" tabindex="-1" aria-labelledby="exampleModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color:grey;color:white">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Filtrar Opções</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <center>
                                                <div class="row col-10">
                                                    <div class="col-12">Data início
                                                        <input class="form-control pesquisa" type="date" id="dt_enc"
                                                            name="dt_enc" value="{{ $data_enc, old('dt_enc') }}">
                                                    </div>
                                                    <div class="col-12 mt-3">
                                                        Dia
                                                        <select class="form-select teste pesquisa" id=""
                                                            name="dia" type="number">
                                                            @foreach ($dia as $dias)
                                                                <option value="{{ $dias->id }}"
                                                                    {{ $diaP == $dias->id ? 'selected' : '' }}
                                                                    {{ $dias->id == old('dia') ? 'selected' : '' }}>
                                                                    {{ $dias->nome }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-12 mt-3">Assistido
                                                        <input class="form-control pesquisa" type="text" id="3"
                                                            name="assist" value="{{ old('assist') }}">
                                                    </div>
                                                    <div class="col-md-12 mt-3">CPF
                                                        <input class="form-control" type="text" maxlength="11"
                                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                                            id="2" name="cpf" value="{{ $cpf }}">
                                                    </div>
                                                    <div class="col-12 mt-3">Grupo
                                                        <input class="form-control pesquisa" autocomplete="off"
                                                            id="grupo" name="grupo" type="text" list="grupos"
                                                            value="{{ $cron }}">
                                                        <datalist id="grupos">
                                                            @foreach ($cronogramas as $cronograma)
                                                                <option
                                                                    value="{{ $cronograma->id }} - {{ $cronograma->nome }} - {{ $cronograma->dia }} - {{ $cronograma->h_inicio }} - {{ $cronograma->setor }}">
                                                            @endforeach
                                                        </datalist>
                                                    </div>
                                                    <div class="col-12 mt-3">Tratamento
                                                        <select class="form-select pesquisa" id="4" name="tratamento"
                                                            type="number">

                                                            <option value=""></option>
                                                            <option value="1" {{ 1 == request('tratamento') ? 'selected' : '' }}>Passe Tratamento Desobsessivo</option>
                                                            <option value="2" {{ 2 == request('tratamento') ? 'selected' : '' }}>Passe Tratamento Intensivo</option>
                                                            <option value="4" {{ 4 == request('tratamento') ? 'selected' : '' }}>Programa de Apoio a Portadores de Mediunidade Ostensiva</option>
                                                            <option value="6" {{ 6 == request('tratamento') ? 'selected' : '' }}>Tratamento Fluidoterápico Integral</option>

                                                        </select>
                                                    </div>
                                                    <div class="col-12 mt-3">Status
                                                        <select class="form-select teste1" id="4" name="status"
                                                            type="number">
                                                            @foreach ($stat as $status)
                                                                <option value="{{ $status->id }}"
                                                                    {{ $situacao == $status->id ? 'selected' : '' }}
                                                                    {{ old('status') == $status->id ? 'selected' : '' }}>
                                                                    {{ $status->nome }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </center>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger"
                                                data-bs-dismiss="modal">Cancelar</button>
                                            <a class="btn btn-secondary" href="/visualizarRI-tratamento">Limpar</a>
                                            <button type="submit" class="btn btn-primary">Confirmar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex-left">
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#filtros" style="box-shadow: 3px 5px 6px #000000; margin:5px;">
                                    Pesquisar <i class="bi bi-funnel"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <br />

        <div style="text-align:left;">
            <hr />
            Total assistidos: {{ $contar }}
            <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                <thead style="text-align: center;">
                    <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                        <th class="col">ASSISTIDO</th>
                        <th class="col">REPRESENTANTE</th>
                        <th class="col">DIA</th>
                        <th class="col">HORÁRIO</th>
                        <th class="col">TRATAMENTO</th>
                        <th class="col">GRUPO</th>
                        <th class="col">STATUS</th>
                    </tr>
                </thead>
                <tbody style="font-size: 16px; color:#000000; text-align: center;">
                    @foreach ($lista as $listas)
                        <tr>
                            <td>{{ $listas->nm_1 }}</td>
                            <td>{{ $listas->nm_2 }}</td>
                            <td>{{ isset($listas->nomed) ? $listas->nomed : null  }}</td>
                            <td>{{ isset($listas->h_inicio) ? date('H:i', strtotime($listas->h_inicio)) : null }}</td>
                            <td>{{ isset($listas->sigla) ? $listas->sigla : null }}</td>
                            <td>{{ isset($listas->nomeg) ? $listas->nomeg : null }}</td>
                            <td>{{ $listas->tst }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $lista->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <script>
        $(document).ready(function() {
            if ({{ $diaP == null }}) {
                $(".teste").prop("selectedIndex", -1);
            }
        });

        $(document).ready(function() {
            if ({{ $situacao == null }}) {
                $(".teste1").prop("selectedIndex", 1);
            }
            $('.pesquisa').change(function() {
                $(".teste1").prop("selectedIndex", 6);
            });
        });
    </script>
@endsection
