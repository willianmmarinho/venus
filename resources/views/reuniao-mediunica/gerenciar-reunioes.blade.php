@extends('layouts.app')

@section('title')
    Gerenciar Reuniões
@endsection

@section('content')
    <div class="container-fluid";>
        <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR REUNIÕES </h4>
        <div class="col-12">
            <form action="{{ route('remdex') }}" class="form-horizontal mt-4" method="GET">
            <div class="row justify-content-center">               
                <div class="col-auto">Dia
                    <select class="form-select semana" id="4" name="semana" type="number">
                        <option value="" {{ request('semana') == '' ? 'selected' : '' }}>Todos
                        </option>
                        @foreach ($tpdia as $dias)
                            <option value="{{ $dias->idtd }}"
                                {{ request('semana') == $dias->idtd && request('semana') != '' ? 'selected' : '' }}>
                                {{ $dias->nomed }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-2">Grupo
                    <select class="form-select select2" id="" name="grupo" type="number">
                        <option value="">Selecione</option>
                        Todos</option>
                        @foreach ($grupos as $gruposs)
                            <option value="{{ $gruposs->idg }}"
                                {{ request('grupo') == $gruposs->idg ? 'selected' : '' }}>
                                {{ $gruposs->nomeg }} - {{ $gruposs->sigla }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-3">Atividade
                    <select class="form-select select2" id="tipo_tratamento" name="tipo_tratamento">
                        <option value="">Selecione</option>
                        @foreach ($tipo_tratamento as $tipot)
                            <option value="{{ $tipot->idt }}" {{ request('tipo_tratamento') == $tipot->idt ? 'selected' : '' }}>
                                {{ $tipot->descricao }}-{{ $tipot->tipo }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col">Semestre
                    <select class="form-select select2" id="semestre" name="semestre">
                        <option value="">Selecione</option>
                        @foreach ($tipo_semestre as $tipos)
                            <option value="{{ $tipos->ids }}" {{ request('semestre') == $tipos->ids ? 'selected' : '' }}>
                                {{ $tipos->sigla }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col">Setor
                    <select class="form-select select2" id="" name="setor" type="number">
                        <option value="">Selecione</option>
                        Todos</option>
                        @foreach ($setores as $setoress)
                            <option value="{{ $setoress->id }}"
                                {{ request('setor') == $setoress->id ? 'selected' : '' }}>
                                {{ $setoress->sigla }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">Modalidade
                    <select class="form-select status" id="" name="modalidade" type="number">
                        <option value="" {{ $tmodalidade[0]->id == $modalidade ? 'selected' : '' }}>
                            Todos</option>
                        @foreach ($tmodalidade as $modal)
                            <option value="{{ $modal->id }}" {{ $modal->id == $modalidade ? 'selected' : '' }}>
                                {{ $modal->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">Status
                    <select class="form-select status" id="4" name="status" type="number">
                        <option value="" {{ $situacao[0]->ids == $status ? 'selected' : '' }}>
                            Todos</option>
                        @foreach ($situacao as $situ)
                            <option value="{{ $situ->ids }}" {{ $situ->ids == $status ? 'selected' : '' }}>
                                {{ $situ->descs }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto d-flex align-items-center justify-content-end">
                    <input class="btn btn-light btn-sm me-md-2"
                        style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
                    <a href="/gerenciar-reunioes"><input class="btn btn-light btn-sm me-md-2"
                            style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                            value="Limpar"></a>
                    <a href="/criar-reuniao"><input class="btn btn-success btn-md me-md-2"
                            style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" autofocus
                            value="Nova reunião &plus;"></a>
                
                </div>
                </form>
            </div>
            <hr>
            <div class="row">
            Quantidade de reuniões: {{ $contar }}
            </div>
        <div class="row">
            <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                <thead style="text-align: center;">
                    <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                        <th class="col">Nr</th>
                        <th class="col-2">GRUPO</th>
                        <th class="col">DIA</th>
                        <th class="col">SEMANAS</th>
                        <th class="col">SALA</th>
                        <th class="col">SETOR</th>
                        <th class="col-2">TIPO DE ATIVIDADE</th>
                        <th class="col">SEMESTRE</th>
                        <th class="col">OBSERVAÇÃO</th>
                        <th class="col">H INÍCIO</th>
                        <th class="col">H FIM</th>
                         <th class="col">DT INÍCIO</th>
                        <th class="col">DT FIM</th>
                        <th class="col">MAX A</th>
                        <th class="col">MAX T</th>
                        <th class="col">MODALIDADE</th>
                        <th class="col">STATUS</th>
                        <th class="col">AÇÕES</th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px; color:#000000; text-align: center;">
                    <tr>
                        @foreach ($reuniao as $reuni)
                            <td>{{ $reuni->idr }}</td>
                            <td>{{ $reuni->nomeg }}</td>
                            <td>{{ $reuni->nomed }}</td>
                            <td>{{ $reuni->nsemana }}</td>
                            <td>{{ $reuni->numero }}</td>
                            <td>{{ $reuni->stsigla }}</td>
                            <td>{{ $reuni->trsigla }}-{{ $reuni->trnome }}</td>
                            <td>{{ $reuni->sesigla }}</td>
                            <td>{{ $reuni->descricao}}</td>
                            <td>{{ date('H:i', strtotime($reuni->h_inicio)) }}</td>
                            <td>{{ date('H:i', strtotime($reuni->h_fim)) }}</td>
                            <td>{{ date('d-m-Y', strtotime($reuni->data_inicio)) }}</td>
                            <td>{{ $reuni->data_fim ? date('d-m-Y', strtotime($reuni->data_fim)) : '-' }}</td>
                            <td>{{ $reuni->max_atend }}</td>
                            <td>{{ $reuni->max_trab }}</td>
                            <td>{{ $reuni->nmodal }}</td>
                            <td>{{ $reuni->status }}</td>
                            <td>
                                <a href="/editar-reuniao/{{ $reuni->idr }}"><button type="button"
                                        class="btn btn-outline-warning btn-sm tooltips">
                                        <span class="tooltiptext">Editar</span>
                                        <i class="bi bi-pencil" style="font-size: 1rem; color:#000;"></i></button></a>
                                <a href="/visualizar-reuniao/{{ $reuni->idr }}"><button type="button"
                                        class="btn btn-outline-primary btn-sm tooltips">
                                        <span class="tooltiptext">Visualizar</span>
                                        <i class="bi bi-search" style="font-size: 1rem; color:#000;"></i></button></a>

                                <!-- Botão que aciona o modal -->
                                <button type="button" class="btn btn-outline-danger btn-sm tooltips" data-bs-toggle="modal"
                                    data-bs-target="#inativa{{ $reuni->idr }}">
                                    <!-- Altere o data-bs-target para o ID correto -->
                                    <span class="tooltiptext">Inativar</span>
                                    <i class="bi bi-x-circle" style="font-size: 1rem; color:#000;"></i>
                                </button>
                                 <!-- Botão que aciona o modal -->
                                <button type="button" class="btn btn-outline-danger btn-sm tooltips" data-bs-toggle="modal"
                                    data-bs-target="#excluir{{ $reuni->idr }}">
                                    <!-- Altere o data-bs-target para o ID correto -->
                                    <span class="tooltiptext">Excluir</span>
                                    <i class="bi bi-trash" style="font-size: 1rem; color:#000;"></i>
                                </button>

                                <!-- Modal de confirmação de inativação -->
                                <form action="inativa-reuniao/{{ $reuni->idr }}" method="POST">
                                    @csrf <!-- Adiciona o token CSRF para proteção -->
                                    <div class="modal fade" id="inativa{{ $reuni->idr }}" data-bs-keyboard="false"
                                        tabindex="-1" aria-labelledby="inativarLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header" style="background-color:#DC4C64;color:white">
                                                    <h1 class="modal-title fs-5" id="inativarLabel">Inativação</h1>
                                                    <button data-bs-dismiss="modal" type="button" class="btn-close"
                                                        aria-label="Close"></button>
                                                </div>
                                                <br />
                                                <div class="modal-body">
                                                    <label for="recipient-name" class="col-form-label"
                                                        style="font-size:17px">
                                                        Tem certeza que deseja inativar:<br />
                                                        <span
                                                            style="color:#DC4C64; font-weight: bold;">{{ $reuni->nomeg }} - {{ $reuni->stsigla }}</span>&#63;
                                                    </label>
                                                    <br />

                                                    <center>
                                                        <div class="mb-2 col-10">
                                                            <label class="col-form-label">Insira o motivo da
                                                                <span style="color:#DC4C64">inativação:</span>
                                                            </label>
                                                            <br>
                                                            <select class="form-select teste1" name="motivo" required>
                                                                @foreach ($tipo_motivo as $motivos)
                                                                    <option value="{{ $motivos->id }}">
                                                                        {{ $motivos->descricao }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </center>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" data-bs-dismiss="modal"
                                                        class="btn btn-danger">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary">Confirmar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                 <!-- Modal de confirmação de inativação -->
                                <form action="excluir-reuniao/{{ $reuni->idr }}" method="POST">
                                    @csrf <!-- Adiciona o token CSRF para proteção -->
                                    <div class="modal fade" id="excluir{{ $reuni->idr }}" data-bs-keyboard="false"
                                        tabindex="-1" aria-labelledby="inativarLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header" style="background-color:#DC4C64;color:white">
                                                    <h1 class="modal-title fs-5" id="inativarLabel">Exclusão</h1>
                                                    <button data-bs-dismiss="modal" type="button" class="btn-close"
                                                        aria-label="Close"></button>
                                                </div>
                                                <br />
                                                <div class="modal-body">
                                                    <label for="recipient-name" class="col-form-label"
                                                        style="font-size:17px">
                                                        Tem certeza que deseja excluir:<br />
                                                        <span
                                                            style="color:#DC4C64; font-weight: bold;">{{ $reuni->nomeg }} - {{ $reuni->stsigla }}</span>&#63;
                                                    </label>
                                                    <br />            
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" data-bs-dismiss="modal"
                                                        class="btn btn-danger">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary">Confirmar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>                               

                                <tr>
                                </form>
                        @endforeach
                </tbody>
            </table>
        </div class="d-flex justify-content-center">
        {{ $reuniao->links('pagination::bootstrap-5') }}
    </div>



    <script>
        $(document).ready(function() {
            if (typeof {{ $semana }} === 'undefined') { //Deixa o select status como padrao vazio
                $(".semana").prop("selectedIndex", -1);
            }

            if (typeof {{ $status }} === 'undefined') { //Deixa o select status como padrao vazio
                $(".status").prop("selectedIndex", -1);
            }
        })
    </script>
@endsection
