@extends('layouts.app')
@section('title')
    Relatório de Disponibilidade de Vagas
@endsection
@section('content')
    <br />
    <button type="button" class="btn btn-danger btn-floating btn-lg" id="btn-back-to-top">
        <i class="bi bi-arrow-up"></i>
    </button>
    <div class="container">
        <form method="GET" action="{{ url('/relatorio-geral-atendimento2') }}">
            <div class="row align-items-end mb-4">
                <div class="col-md-2">
                    <label for="ano" class="form-label">Ano</label>
                    <select class="form-select" id="ano" name="ano">
                        @php
                            $anoAtual = now()->year;
                            $anoInicio = 2024;
                        @endphp
                        @for ($ano = $anoAtual; $ano >= $anoInicio; $ano--)
                            <option value="{{ $ano }}" @if (request('ano') == $ano) selected @endif>
                                {{ $ano }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="tipo_tratamento" class="form-label">Tipo Tratamento</label>
                    <select class="form-select select2" id="tipo_tratamento" name="tipo_tratamento[]" multiple>
                        <option value="1" @if (request('tipo_tratamento') == 1) selected @endif>PTD</option>
                        <option value="2" @if (request('tipo_tratamento') == 2) selected @endif>PTI</option>
                        <option value="3" @if (request('tipo_tratamento') == 3) selected @endif>PPH</option>
                        <option value="6" @if (request('tipo_tratamento') == 6) selected @endif>Integral</option>
                        <option value="5" @if (request('tipo_tratamento') == 5) selected @endif>Todos</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-light w-100 me-2"
                        style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000;">
                        Pesquisar
                    </button>
                    <a href="{{ url('/relatorio-geral-atendimento2') }}" class="btn btn-light w-100"
                        style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000;">
                        Limpar
                    </a>
                </div>
            </div>
        </form>


        <br />
        <div class="card">
            @php
                $tiposTratamento = [
                    1 => 'PTD',
                    2 => 'PTI',
                    3 => 'PPH',
                    6 => 'Integral',
                    5 => 'Todos',
                ];

                $selecionados = request('tipo_tratamento', []);
                $nomesSelecionados = [];

                if (is_array($selecionados)) {
                    foreach ($selecionados as $id) {
                        if (isset($tiposTratamento[$id])) {
                            $nomesSelecionados[] = $tiposTratamento[$id];
                        }
                    }
                }
            @endphp

            <div class="card-header">
                ESTATÍSTICA FREQUÊNCIA

                @if (count($nomesSelecionados))
                    <small class="text-muted">| TIPO DE TRATAMENTO: {{ implode(', ', $nomesSelecionados) }}</small>
                @endif
            </div>
            <div class="card-body" id="printTable">
                <table class="table table-striped table-bordered border-secondary table-hover align-middle">
                    <thead class="text-center">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th>TIPOS</th>
                            @foreach (array_keys($dadosFreq) as $mes)
                                <th>{{ $mes }}</th>
                            @endforeach
                            <th>MÉDIA</th>
                            <th>TOTAL</th>
                        </tr>
                    </thead>
                    <tbody class="text-center" style="font-size: 14px; color:#000000;">
                        <tr>
                            <td>TOTAL</td>
                            @foreach ($dadosFreq as $dado)
                                <td>{{ number_format($dado['Total'], 0, ',', '.') ?? '--' }}</td>
                            @endforeach
                            @php
                                $valores = array_filter(
                                    array_column($dadosFreq, 'Total'),
                                    fn($a) => $a !== 0 && $a !== null,
                                );
                            @endphp
                            <td>{{ count($valores) > 0 ? number_format( round(array_sum($valores) / count($valores)) , 0, ',', '.')  : '--' }}</td>
                            <td>{{number_format( round(array_sum(array_column($dadosFreq, 'Total'))) , 0, ',', '.')  }}</td>
                        </tr>
                        @if (!request('tipo_tratamento') or in_array(3, request('tipo_tratamento')))
                        <tr>
                            <td>HARMONIZAÇÃO</td>
                            @foreach ($dadosFreq as $dado)
                                <td>{{ number_format($dado['Harmonização'], 0, ',', '.') ?? '--' }}</td>
                            @endforeach
                            @php
                                $valores = array_filter(
                                    array_column($dadosFreq, 'Harmonização'),
                                    fn($a) => $a !== 0 && $a !== null,
                                );
                            @endphp
                            <td>{{ count($valores) > 0 ? number_format( round(array_sum($valores) / count($valores)) , 0, ',', '.')  : '--' }}</td>
                            <td>{{number_format( round(array_sum(array_column($dadosFreq, 'Harmonização'))) , 0, ',', '.')  }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td rowspan="2">AUSENTES</td>
                            @foreach ($dadosFreq as $dado)
                                <td>{{ number_format( $dado['Ausentes'] , 0, ',', '.')  ?? '--' }}</td>
                            @endforeach
                            @php
                                $valores = array_filter(
                                    array_column($dadosFreq, 'Ausentes'),
                                    fn($a) => $a !== 0 && $a !== null,
                                );
                            @endphp
                            <td>{{ count($valores) > 0 ? number_format( round(array_sum($valores) / count($valores)) , 0, ',', '.') : '--' }}</td>
                            <td>{{ number_format( round(array_sum(array_column($dadosFreq, 'Ausentes'))) , 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            @foreach ($dadosFreq as $dado)
                                <td>{{ isset($dado['PCT Ausentes']) ? $dado['PCT Ausentes'] . '%' : '--' }}</td>
                            @endforeach
                            @php
                                $valores = array_filter(
                                    array_column($dadosFreq, 'PCT Ausentes'),
                                    fn($a) => $a !== 0 && $a !== null,
                                );
                            @endphp
                            <td>{{ count($valores) > 0 ? round(array_sum($valores) / count($valores), 2) . '%' : '--' }}
                            </td>
                            <td>--</td>
                        </tr>

                        <tr>
                            <td rowspan="2">PRESENTES</td>
                            @foreach ($dadosFreq as $dado)
                              <td>{{ number_format($dado['Presenças'], 0, ',', '.') ?? '--' }}</td>
                            @endforeach
                            @php
                                $valores = array_filter(
                                    array_column($dadosFreq, 'Presenças'),
                                    fn($a) => $a !== 0 && $a !== null,
                                );
                            @endphp
                            <td>{{ count($valores) > 0 ? number_format( round(array_sum($valores) / count($valores)) , 0, ',', '.') : '--' }}</td>
                            <td>{{ number_format( round(array_sum(array_column($dadosFreq, 'Presenças')))  , 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            @foreach ($dadosFreq as $dado)
                                <td>{{ isset($dado['PCT Presenças']) ? $dado['PCT Presenças'] . '%' : '--' }}</td>
                            @endforeach
                            @php
                                $valores = array_filter(
                                    array_column($dadosFreq, 'PCT Presenças'),
                                    fn($a) => $a !== 0 && $a !== null,
                                );
                            @endphp
                            <td>{{ count($valores) > 0 ? round(array_sum($valores) / count($valores), 2) . '%' : '--' }}
                            </td>
                            <td>--</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <br />
        <br />

        <div class="card">
            <div class="card-header">
                ESTATÍSTICA TRATAMENTOS

                @if (count($nomesSelecionados))
                    <small class="text-muted">| TIPO DE TRATAMENTO: {{ implode(', ', $nomesSelecionados) }}</small>
                @endif
            </div>
            <div class="card-body" id="printTable">
                <table class="table table-striped table-bordered border-secondary table-hover align-middle">
                    <thead class="text-center">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th>TIPOS</th>
                            @foreach (array_keys($dadosTrat) as $mes)
                                <th>{{ $mes }}</th>
                            @endforeach
                            <th>MÉDIA</th>
                            <th>TOTAL</th>
                        </tr>
                    </thead>
                    <tbody class="text-center" style="font-size: 14px; color:#000000;">
                         <tr>
                            <td>TRATAMENTOS</td>
                            @foreach ($dadosTrat as $dado)
                                <td>{{ number_format( $dado['Tratamentos'] , 0, ',', '.') ?? '--' }}</td>
                            @endforeach
                            @php
                                $valores = array_filter(
                                    array_column($dadosTrat, 'Tratamentos'),
                                    fn($a) => $a !== 0 && $a !== null,
                                );
                            @endphp
                            <td>{{ count($valores) > 0 ? number_format( round(array_sum($valores) / count($valores)) , 0, ',', '.')  : '--' }}</td>
                            <td>{{number_format( round(array_sum(array_column($dadosTrat, 'Tratamentos'))) , 0, ',', '.')  }}</td>
                        </tr>
                        <tr>
                            <td rowspan="2">ALTA</td>
                            @foreach ($dadosTrat as $dado)
                                <td>{{number_format( $dado['Alta'] , 0, ',', '.')  ?? '--' }}</td>
                            @endforeach
                            @php
                                $valores = array_filter(
                                    array_column($dadosTrat, 'Alta'),
                                    fn($a) => $a !== 0 && $a !== null,
                                );
                            @endphp
                            <td>{{ count($valores) > 0 ? number_format( round(array_sum($valores) / count($valores)) , 0, ',', '.')  : '--' }}</td>
                            <td>{{ number_format( round(array_sum(array_column($dadosTrat, 'Alta'))) , 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            @foreach ($dadosTrat as $dado)
                                <td>{{ isset($dado['PCT Alta']) ? $dado['PCT Alta'] . '%' : '--' }}</td>
                            @endforeach
                            @php
                                $valores = array_filter(
                                    array_column($dadosTrat, 'PCT Alta'),
                                    fn($a) => $a !== 0 && $a !== null,
                                );
                            @endphp
                            <td>{{ count($valores) > 0 ? round(array_sum($valores) / count($valores), 2) . '%' : '--' }}
                            </td>
                            <td>--</td>
                        </tr>

                        {{-- TRANSFERIDOS --}}
                        <tr>
                            <td rowspan="2">TRANSFERIDOS</td>
                            @foreach ($dadosTrat as $dado)
                                <td>{{ number_format( $dado['Transferidos'] , 0, ',', '.')  ?? '--' }}</td>
                            @endforeach
                            @php
                                $valores = array_filter(
                                    array_column($dadosTrat, 'Transferidos'),
                                    fn($a) => $a !== 0 && $a !== null,
                                );
                            @endphp
                            <td>{{ count($valores) > 0 ? number_format( round(array_sum($valores) / count($valores)) , 0, ',', '.')  : '--' }}</td>
                            <td>{{ number_format( round(array_sum(array_column($dadosTrat, 'Transferidos')))  , 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            @foreach ($dadosTrat as $dado)
                                <td>{{ isset($dado['PCT Transferidos']) ? $dado['PCT Transferidos'] . '%' : '--' }}</td>
                            @endforeach
                            @php
                                $valores = array_filter(
                                    array_column($dadosTrat, 'PCT Transferidos'),
                                    fn($a) => $a !== 0 && $a !== null,
                                );
                            @endphp
                            <td>{{ count($valores) > 0 ? round(array_sum($valores) / count($valores), 2) . '%' : '--' }}
                            </td>
                            <td>--</td>
                        </tr>

                        {{-- DESISTÊNCIAS --}}
                        <tr>
                            <td rowspan="2">DESISTÊNCIAS</td>
                            @foreach ($dadosTrat as $dado)
                                <td>{{number_format( $dado['Desistência'] , 0, ',', '.')  ?? '--' }}</td>
                            @endforeach
                            @php
                                $valores = array_filter(
                                    array_column($dadosTrat, 'Desistência'),
                                    fn($a) => $a !== 0 && $a !== null,
                                );
                            @endphp
                            <td>{{ count($valores) > 0 ? number_format(round(array_sum($valores) / count($valores)) , 0, ',', '.')  : '--' }}</td>
                            <td>{{ number_format( round(array_sum(array_column($dadosTrat, 'Desistência'))) , 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            @foreach ($dadosTrat as $dado)
                                <td>{{ isset($dado['PCT Desistência']) ? $dado['PCT Desistência'] . '%' : '--' }}</td>
                            @endforeach
                            @php
                                $valores = array_filter(
                                    array_column($dadosTrat, 'PCT Desistência'),
                                    fn($a) => $a !== 0 && $a !== null,
                                );
                            @endphp
                            <td>{{ count($valores) > 0 ? round(array_sum($valores) / count($valores), 2) . '%' : '--' }}
                            </td>
                            <td>--</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card mt-5">
            <div>
                <div class="card-header">
                    CAPACIDADE E VAGAS
                @if (count($nomesSelecionados))
                       <small class="text-muted">| TIPO DE TRATAMENTO: {{ implode(', ', $nomesSelecionados) }}</small>
                   @endif
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered border-secondary table-hover align-middle mt-3">
                    <thead class="text-center">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th>VAGAS ASSISTIDOS</th>
                            <th>VAGAS TRABALHADORES</th>
                        </tr>
                    </thead>
                    <tbody class="text-center" style="font-size: 14px; color:#000000;">
                        <tr>
                            <TD>
                                {{number_format( $maxAtend->max_atend , 0, ',', '.')  }}
                            </TD>
                            <td>
                                {{number_format( $maxAtend->max_trab , 0, ',', '.')  }}
                            </td>

                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <br /><br /><br /><br />

    <style>
        #btn-back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: none;
        }
    </style>

    {{-- <script>
        $(document).ready(function() {
            let tratamento = @JSON(request('tipo_tratamento'));

            if (tratamento === null) {
                $('#tipo_tratamento').prop('selectedIndex', 4)
            }

        });
    </script> --}}
@endsection
