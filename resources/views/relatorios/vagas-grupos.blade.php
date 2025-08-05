@extends('layouts.app')
@section('title')
    Relatório de Vagas em Grupos
@endsection
@section('content')
    <div class="container-fluid">
        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family: calibri">
            RELATÓRIO DE VAGAS
        </h4>
        <br>
        <div class="card">
            <div class="card-header">
                <form action="/relatorio-vagas-grupos">
                    <div class="row">
                        <div class="col">
                            Grupos
                            <select class="form-select select2" id="grupo" name="grupo">
                                <option value="">Todos</option>
                                @foreach ($grupo2 as $gruposs)
                                    <option value="{{ $gruposs->id }}"
                                        {{ request('nome_grupo') == (string) $gruposs->id ? 'selected' : '' }}>
                                        {{ $gruposs->nome }} ({{ $gruposs->setor }})-{{ $gruposs->dia_semana }}
                                        |
                                        {{ date('H:i', strtotime($gruposs->h_inicio)) }}/{{ date('H:i', strtotime($gruposs->h_fim)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            Setor
                            <select class="form-select select2" id="setor" name="setor">
                                <option value="0">Todos</option>
                                @foreach ($setores as $setor)
                                    <option value="{{ $setor->id }}"
                                        {{ $setor->id == request('setor') ? 'selected' : '' }}>{{ $setor->nome }} -
                                        {{ $setor->sigla }} </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            Tratamento
                            <select class="form-select select2" id="tratamento" name="tratamento">
                                <option value="0">Todos</option>
                                @foreach ($tratamento as $trat)
                                    <option value="{{ $trat->id }}"
                                        {{ request('tratamento') == $trat->id ? 'selected' : '' }}>
                                        {{ $trat->descricao }} ({{ $trat->sigla }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-1">
                            <br />
                            <input class="btn btn-light btn-sm me-md-2 col-6 col-12"
                                style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit"
                                value="Pesquisar">
                        </div>
                        <div class="col-1">
                            <br />
                            <a href="/relatorio-vagas-grupos">
                                <input class="btn btn-light btn-sm me-md-2 col-12"
                                    style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                                    value="Limpar">
                            </a>
                        </div>

                        <div class="col d-flex justify-content-end mt-3">

                        </div>

                    </div>
                </form>
            </div>

            <div class="card-body">

                @if ($quantidade_vagas_tipo_tratamento != 0 and $tipo_de_tratamento != null)
                    <div class="d-flex align-items-center">
                        <!-- Nome do Tratamento -->
                        <p class="mb-0 text-muted fs-5 me-3">{{ $tipo_de_tratamento->descricao }}:</p>

                        <!-- Badge de Vagas -->
                        <span class="badge bg-success fs-6">{{ $quantidade_vagas_tipo_tratamento }} vagas</span>
                    </div>
                @endif


                <table class="table table-striped table-bordered border-secondary table-hover align-middle">
                    <thead style="text-align: center; background-color: #d6e3ff; font-size: 14px; color: #000000;">
                        <tr>
                            <th class="col-3">GRUPO</th>
                            <th class="col-auto">DIA</th>
                            <th class="col-auto">HORÁRIO</th>
                            <th class="col-auto">TRABALHO</th>
                            <th class="col-auto">VAGAS DISPONIVEIS</th>
                            <th class="col-auto">VAGAS OCUPADAS</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color: #000000; text-align: center;">
                        @foreach ($grupos as $grupo)
                            <tr>
                                <!-- Grupo: Nome e Setor -->
                                <td style="padding: 10px;">{{ $grupo->nome }} ({{ $grupo->setor }})
                                </td>

                                <!-- Dia -->
                                <td style="padding: 10px;">{{ $grupo->dia }}</td>

                                <!-- Horário: Formatado para horas -->
                                <td style="padding: 10px;">
                                    {{ date('H:i', strtotime($grupo->h_inicio)) }} -
                                    {{ date('H:i', strtotime($grupo->h_fim)) }}
                                </td>

                                <!-- Tratamento: Exibindo descrição com corte se necessário -->
                                <td style="padding: 10px; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                                    title="{{ $grupo->descricao }}">
                                    {{ substr($grupo->descricao, 0, 50) }}{{ strlen($grupo->descricao) > 50 ? '...' : '' }}
                                </td>

                                <!-- Vagas restantes: Verificando a cor com base na quantidade de vagas -->
                                @if ($grupo->max_atend - $grupo->trat < $grupo->max_atend * 0.1)
                                    <td style="padding: 10px; color: red; font-weight: bold;">
                                        {{ $grupo->max_atend - $grupo->trat }}</td>
                                @else
                                    <td style="padding: 10px; color: green; font-weight: bold;">
                                        {{ $grupo->max_atend - $grupo->trat }}</td>
                                @endif
                                @php
                                    $ocupadas = $grupo->trat;
                                    $total = $grupo->max_atend;
                                    $percentual = $total > 0 ? $ocupadas / $total : 0;
                                @endphp

                                @if ($percentual > 0.9)
                                    <td style="padding: 10px; color: red; font-weight: bold;">
                                        {{ $ocupadas }}
                                    </td>
                                @else
                                    <td style="padding: 10px; color: green; font-weight: bold;">
                                        {{ $ocupadas }}
                                    </td>
                                @endif

                            </tr>
                        @endforeach
                        </tr>
                    </tbody>
                </table>


                {{ $grupos->links('pagination::bootstrap-5') }}
            </div>
        </div>
        <br />
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#tratamento').change(function(e) {
                    e.preventDefault();

                    var idtratamento = $(this).val(); // Pega o valor do tratamento selecionado

                    // Faz a requisição AJAX para buscar os grupos filtrados pelo tratamento
                    $.ajax({
                        type: "GET",
                        url: "/vagasGruposAjax/" + idtratamento, // Chama a URL com o ID do tratamento
                        dataType: "json", // Espera uma resposta no formato JSON
                        success: function(response) {
                            // Limpa o conteúdo atual do select de grupos
                            $('#grupo').empty();

                            // Adiciona a opção "Todos" no topo
                            $('#grupo').append('<option value="">Todos</option>');

                            // Verifica se a resposta contém grupos e os adiciona no select
                            if (response.length > 0) {
                                response.forEach(function(grupo) {
                                    // Adiciona as opções ao select de grupos
                                    $('#grupo').append('<option value="' + grupo.id + '">' +
                                        grupo.nome + ' (' + grupo.setor + ') - ' + grupo
                                        .dia_semana + ' | ' + grupo.h_inicio + '/' +
                                        grupo.h_fim + '</option>');
                                });
                            } else {
                                // Caso não haja resultados, pode adicionar uma mensagem opcional ou apenas deixar "Todos"
                                console.log("Nenhum grupo encontrado para este tratamento.");
                            }

                            // Reaplica o select2 (caso esteja usando o plugin Select2)
                            $('#grupo').trigger('change');
                        },
                        error: function(xhr, status, error) {
                            // Caso a requisição falhe, exibe um erro
                            console.log("Erro na requisição: " + error);
                        }
                    });
                });
            });
        </script>
    @endsection
