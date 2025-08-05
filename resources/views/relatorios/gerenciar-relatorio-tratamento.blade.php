

@extends('layouts.app')
@section('title')
    Relatório de Tratamentos
@endsection
@section('content')
    <div class="container-fluid">
        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family: calibri">
            RELATÓRIO DE TRATAMENTOS
        </h4>
        <br>
        <form action="/gerenciar-relatorio-tratamento" method="GET">
            <div class="row align-items-center">
                <!-- Data de Início -->
                <div class="col">
                    <label for="dt_inicio" class="form-label">Data de Início</label>
                    <input type="date" class="form-control" id="dt_inicio" name="dt_inicio"  value="{{ $dt_inicio }}">
                </div>
                <!-- Data de Fim -->
                <div class="col">
                    <label for="dt_fim" class="form-label">Data de Fim</label>
                    <input type="date" class="form-control" id="dt_fim" name="dt_fim" value="{{ $dt_fim }}">
                </div>
                <!-- Grupos -->
                <div class="col">
                    <label for="grupo" class="form-label">Grupos</label>
                    <select class="form-select select2" id="grupo" name="grupo[]" multiple  data-width="100%">
                        @foreach ($grupo2 as $gruposs)
                            <option value="{{ $gruposs->id }}" {{ request('nome_grupo') == (string) $gruposs->id ? 'selected' : '' }}>
                                {{ $gruposs->nome }} ({{ $gruposs->setor }})-{{ $gruposs->dia_semana }} |
                                {{ date('H:i', strtotime($gruposs->h_inicio)) }}/{{ date('H:i', strtotime($gruposs->h_fim)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- Setor -->
                <div class="col">
                    <label for="setor" class="form-label">Setor</label>
                    <select class="form-select select2" id="setor" name="setor" data-width="100%">
                        <option value="0">Todos</option>
                        @foreach ($setores as $setor)
                            <option value="{{ $setor->id }}" {{ $setor->id == request('setor') ? 'selected' : '' }}>
                                {{ $setor->nome }} - {{ $setor->sigla }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- Tratamento -->
                <div class="col">
                    <label for="tratamento" class="form-label">Tratamento</label>
                    <select class="form-select select2" id="tratamento" name="tratamento" data-width="100%">
                        <option value="0">Todos</option>
                        @foreach ($tratamento as $trat)
                            <option value="{{ $trat->id }}" {{ request('tratamento') == $trat->id ? 'selected' : '' }}>
                                {{ $trat->descricao }} ({{ $trat->sigla }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- Botão Pesquisar -->
                <div class="col">
                    <button type="submit" class="btn btn-light w-100"
                        style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin-top: 27px;">
                        Pesquisar
                    </button>
                </div>
                <!-- Botão Limpar -->
                <div class="col">
                    <a href="/gerenciar-relatorio-tratamento">
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
                <table class="table table-striped table-bordered border-secondary table-hover align-middle">
                    <thead style="text-align: center; background-color: #d6e3ff; font-size: 14px; color: #000000;">
                        <tr>

                            @if (count(current($grupos)) > 6)
                                <th class="col-3">GRUPO</th>
                                <th class="col">DIA</th>
                                <th class="col">INICIO</th>
                                <th class="col">FIM</th>

                            @endif
                            <th class="col-3">TRATAMENTO</th>
                            <th class="col-1">ASSISTIDOS</th>
                            <th class="col-1">PASSES</th>
                            <th class="col-1">ACOMPANHANTES</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color: #000000; text-align: center;">
                        @foreach ($grupos as $trat)
                            <tr>
                                @if (count(current($grupos)) > 6)
                                    <td>{{ $trat['nome']}}</td>
                                    <td>{{ $trat['dia_semana'] }}</td>
                                    <td>{{ $trat['h_inicio'] }}</td>
                                    <td>{{ $trat['h_fim'] }}</td>

                                @endif
                                <td>{{ $trat['descricao'] }} ({{ $trat['sigla'] }})</td>
                                <td>{{ isset($trat['atendimentos']) ?  $trat['atendimentos'] : '-'}}</td>
                                <td>{{ isset($trat['passes']) ?  $trat['passes'] : '-' }}</td>
                                <td>{{ isset($trat['acompanhantes']) ?  $trat['acompanhantes'] : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection


