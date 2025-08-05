

@extends('layouts.app')
@section('title')
    Relatório de Passes
@endsection
@section('content')
<div class="container-fluid">
    <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family: calibri">
        RELATÓRIO DE PASSES PPH-PTD-PTI-TFI
    </h4>
    <br>
    <form action="/relatorio-passes" method="GET">
        <div class="row align-items-center">
            <!-- Data de Início -->
            <div class="col-md-2 mb-2">
                <label for="dt_inicio" class="form-label">Data de Início</label>
                <input type="date" class="form-control" id="dt_inicio" name="dt_inicio" value="{{ old('dt_inicio', $dt_inicio) }}">
            </div>
            <!-- Data de Fim -->
            <div class="col-md-2 mb-2">
                <label for="dt_fim" class="form-label">Data de Fim</label>
                <input type="date" class="form-control" id="dt_fim" name="dt_fim" value="{{ old('dt_fim', $dt_fim) }}">
            </div>
            <!-- Tratamento -->
            <div class="col-md-2 mb-2">
                <label for="tratamento" class="form-label">Tratamento</label>
                <select class="form-select select2" id="tratamento" name="tratamento" data-width="100%">
                    <option value="">Todos</option>
                    @foreach ($trata as $tratas)
                        <option value="{{ $tratas->id }}" {{ $tratamento == $tratas->id ? 'selected' : '' }}>
                            {{ $tratas->sigla }}
                        </option>
                    @endforeach
                </select>
            </div>
            <!-- Botão Pesquisar -->
            <div class="col-md-1 mb-2">
                <button type="submit" class="btn btn-light w-100"
                    style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin-top: 27px;">
                    Pesquisar
                </button>
            </div>
            <!-- Botão Limpar -->
            <div class="col-md-1 mb-2">
                <a href="/relatorio-passes">
                    <button type="button" class="btn btn-light w-100"
                        style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin-top: 27px;">
                        Limpar
                    </button>
                </a>
            </div>
        </div>
    </form>
    <br />

    <div class="card">
        <div class="card-body">
            <table class="table table-striped table-bordered border-secondary align-middle">
                <thead style="text-align: center; background-color: #d6e3ff; font-size: 14px; color: #000000;">
                    <tr>
                        <th class="col-8">MÊS/ANO</th>
                        <th class="col-2">ASSISTIDOS</th>
                        <th class="col-2">ACOMPANHANTES</th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px; color: #000000; text-align: center;">
                        @foreach ($passe as $t_id => $tratamento)
                        <!-- Exibir o nome do tratamento uma única vez -->
                        <tr style="background-color: #f2f2f2; font-weight: bold; text-align: right;">
                            <td colspan="3">{{ $tratamento['tnome'] }} ({{ $tratamento['tsigla'] }})</td>
                        </tr>

                        @foreach ($tratamento['dados'] as $dados)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($dados['ultimo_dia_mes'])->format('m/Y') }}</td>
                                <td>{{ number_format($dados['assist'],0,'','.' ) }}</td>
                                <td>{{ number_format($dados['acomp'],0,'','.' ) ?? 0 }}</td>
                            </tr>
                        @endforeach

                        <!-- Totalizadores do tratamento -->
                        <tr style="font-weight: bold; background-color: #d6e3ff; text-align: right;">
                            <td colspan="1">TOTAL DE PASSES POR TIPO DE TRATAMENTO</td>
                            <td colspan="2" style="background-color:rgba(223, 209, 15, 0.68);">{{ number_format($tratamento['total_assist'] + $tratamento['total_acomp'],0,'','.' ) }}</td>
                        </tr>
                        @endforeach
                </tbody>
                <tfoot>
                    <tr style="font-weight: bold;  text-align: right;">
                        <td colspan="1">TOTAL GERAL DE PASSES</td>
                        <td colspan="2" style="background-color:rgb(220, 223, 15); " >{{ number_format($totalGeralAssistidos,0,'','.' )}}</td>
                    </tr>  
                </tfoot>
            </table>
        </div>
    </div>
</div>



@endsection


