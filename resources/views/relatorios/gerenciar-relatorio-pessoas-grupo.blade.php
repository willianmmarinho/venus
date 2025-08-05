@extends('layouts.app')

@section('title')
    histórico de membros
@endsection
@section('content')
    <div class="container-fluid">
        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family: calibri">
            HISTÓRICO DE MEMBROS
        </h4>
        <div class="col-12">
            <div class="row justify-content-center">
                <div>
                    <br />
                    <form action="{{ url('/gerenciar-relatorio-pessoas-grupo') }}" method="get">
                        <div class="row align-items-end">
                            <div class="col-1">
                                Dia
                                <select class="form-select select2" id="dia" name="dia" data-width="100%">
                                    <option value="">Todos</option>
                                    @foreach ($dias as $dia)
                                        <option value="{{ $dia->id }}"
                                            {{ request('dia') == $dia->id ? 'selected' : '' }}>
                                            {{ $dia->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2">
                                Setor
                                <select class="form-select select2" id="setor" name="setor" data-width="100%">
                                    <option value="">Todos</option>
                                    @foreach ($setor as $setores)
                                        <option value="{{ $setores->id }}"
                                            {{ request('setor') == $setores->id ? 'selected' : '' }}>
                                            {{ $setores->nome }} - {{ $setores->sigla }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2">
                                Grupo
                                <select class="form-select select2" id="grupo" name="grupo" data-width="100%">
                                    <option value="">Todos</option>
                                    @foreach ($grupo as $grupos)
                                        <option value="{{ $grupos->id }}"
                                            {{ request('grupo') == $grupos->id ? 'selected' : '' }}>
                                            {{ $grupos->nome_grupo }} - {{ $grupos->sigla }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2">
                                Função
                                <select class="form-select select2" id="funcao" name="funcao" data-width="100%">
                                    <option value="">Todos</option>
                                    @foreach ($funcao as $item)
                                        <option value="{{ $item->id }}"
                                            {{ request('funcao') == $item->id ? 'selected' : '' }}>
                                            {{ $item->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-1">
                                <label for="status">Status</label>
                                <select class="form-select select2" id="status" name="status" data-width="100%">
                                    <option value="">Todos</option>
                                    @foreach ($statu as $status)
                                        <option value="{{ $status->nome }}"
                                            {{ request('status') == $status->nome ? 'selected' : '' }}>
                                            {{ $status->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2">
                                Membro
                                <select class="form-select select2" id="nome" name="nome" data-width="100%">
                                    <option value="">Todos</option>
                                    @foreach ($atendentesParaSelect as $atendente)
                                        <option value="{{ $atendente->ida }}"
                                            {{ request('nome') == $atendente->ida ? 'selected' : '' }}>
                                            {{ $atendente->nm_4 }} - {{ $atendente->nr_associado }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto">
                                <input class="btn btn-light btn-sm me-md-2"
                                    style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit"
                                    value="Pesquisar">
                                <a href="{{ url('/gerenciar-relatorio-pessoas-grupo') }}"
                                    class="btn btn-light btn-sm me-md-2"
                                    style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;">Limpar</a>
                            </div>
                        </div>
                    </form>
                    <hr />
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div div class="accordion" id="accordionExample">

                        @foreach ($result as $key => $membro)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ reset($membro)->id }}" aria-expanded="false"
                                        aria-controls="collapseOne">
                                        {{ $key }}
                                    </button>
                                </h2>
                                {{-- Membros Ativos: {{ $result}} --}}
                                <div id="collapse{{ reset($membro)->id }}" class="accordion-collapse collapse "
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <table class="table table-striped table-bordered border-secondary table-hover align-middle">
                                            <thead style="text-align: center;">
                                                <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                                                    <th>GRUPO</th>
                                                    <th>DT_INICIO</th>
                                                    <th>DT_FIM</th>
                                                    <th>STATUS</th>
                                                    <th>FUNÇÃO</th>
                                                    <th>DIA</th>
                                                    <th>HORA INÍCIO</th>
                                                    <th>HORA FIM</th>
                                                    <th>SETOR</th>
                                                    <th>CURRÍCULO</th> <!-- Corrigido aqui -->
                                                </tr>
                                            </thead>
                                            <tbody style="font-size: 14px; color:#000000; text-align: center;">
                                                @foreach ($membro as $dado)
                                                    <tr>
                                                        <td>{{ $dado->grupo_nome }}</td>
                                                        <td>{{ $dado->dt_inicio ? date('d/m/Y', strtotime($dado->dt_inicio)) : '-' }}</td>
                                                        <td>{{ $dado->dt_fim ? date('d/m/Y', strtotime($dado->dt_fim)) : '-' }}</td>
                                                        <td>{{ $dado->status }}</td>
                                                        <td>{{ $dado->nome_funcao }}</td>
                                                        <td>{{ $dado->dia_nome }}</td>
                                                        <td>{{ $dado->h_inicio }}</td>
                                                        <td>{{ $dado->h_fim }}</td>
                                                        <td>{{ $dado->setor_sigla }}</td>
                                                        <td>
                                                            <a href="/curriculo-medium/{{ $dado->id ?? '' }}" class="btn btn-outline-primary btn-sm tooltips">
                                                                <span class="tooltiptext">Currículo</span>
                                                                <i class="bi bi-newspaper" style="font-size: 1rem; color:#000;"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <br />
            {{ $result->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div class="d-flex justify-content-center">

    </div>
    </div>
    <script>
        $(document).ready(function() {
            if ({{ request('dia') === null }}) {
                $('#dia').prop('selectedIndex', 0);
            }

        });
    </script>
@endsection
