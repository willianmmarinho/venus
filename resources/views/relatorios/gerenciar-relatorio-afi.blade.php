@extends('layouts.app')
@section('title')
    Relatório de Presença AFI
@endsection

@section('content')
    <div class="container-fluid";>
        <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">
            RELATÓRIO DE PRESENÇA AFI
        </h4>
        <div class="col-12">
            <div class="row justify-content-center">
                <div>
                    <br />
                    <form action="/gerenciar-relatorio-afi">
                        <div class="row">
                            <div class="col-5">
                                Nome
                                <select class="form-select select2" id="afi" name="afi">
                                    @foreach ($atendentes as $atendente)
                                        <option value="{{ $atendente->id_associado }}">{{ $atendente->nome_completo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2">
                                Data de Início
                                <input type="date" class="form-control" id="dt_inicio" name="dt_inicio"
                                    value="{{ $dt_inicio }}">
                            </div>
                            <div class="col-2">
                                Data de fim
                                <input type="date" class="form-control" id="dt_fim" name="dt_fim"
                                    value="{{ $dt_fim }}">
                            </div>
                            <div class="col mt-3">
                                <input class="btn btn-light btn-sm me-md-2"
                                    style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit"
                                    value="Pesquisar">
                                <a href="/gerenciar-relatorio-afi"><input class="btn btn-light btn-sm me-md-2"
                                        style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;"
                                        type="button" value="Limpar"></a>
                            </div>
                        </div>
                    </form>
                    <hr />


                </div>
            </div>
        </div>

<table class="table  table-striped table-bordered border-secondary table-hover align-middle">
        <thead style="text-align: center;">
            <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                <th>ID ASSOCIADO</th>
                <th>NOME</th>
                <th>PRESENÇAS</th>
                <th>FALTAS</th>
                <th>AÇÔES</th>
            </tr>
        </thead>
        <tbody style="font-size: 14px; color:#000000; text-align: center;">
            @foreach ($atendentes as $atendente)
            <tr>
                <td> {{$atendente->id_associado}} </td>
                <td> {{$atendente->nome_completo}} </td>
                <td>{{ $atendente->presenca[1] ?? '--' }}</td>
                <td>{{ $atendente->presenca[0] ?? '--' }}</td>
                <td>
                    <a href="/visualizar-presenca-afi?afi={{$atendente->id_associado}}&dt_inicio={{ $dt_inicio }}&dt_fim={{$dt_fim}}" type="button"{{-- botão de histórico --}}
                        class="btn btn-outline-primary btn-sm tooltips">
                        <span class="tooltiptext">Histórico</span>
                        <i class="bi bi-search" style="font-size: 1rem; color:#000;"></i></a>
                </td>

            </tr>
            @endforeach
        </tbody>
    </table>
    </div>

    <script>
        $(document).ready(function() {
            if (@JSON($afiSelecionado) == null) {
                $('#afi').prop('selectedIndex', -1)
            }

        });
    </script>
@endsection
