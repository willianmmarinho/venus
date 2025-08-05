@extends('layouts.app')

@section('title')
    Reverter Membro
@endsection

@section('content')
    <div class="container">
        <div class="justify-content-center">
            <div class="col-12">
                <br>

                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-12">
                                <div class="col">
                                    REVERTER FALTAS DE MEMBROS
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <form class="form-horizontal mt-2" method="post"
                            action="/remarcar-faltas-membro/{{ $membro->id_cronograma }}">
                            @csrf
                            <!-- Linha 1 -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nome_completo" class="form-label">Nome do médium</label>
                                        <input type="text" class="form-control" name="nome_completo"
                                            value="{{ $membro->nome_completo }}" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="status" class="form-label">Status</label>
                                        <input type="text" class="form-control" name="status"
                                            value="{{ $membro->tipo }}" disabled>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="nr_associado" class="form-label">Número de associado</label>
                                        <input type="text" class="form-control" name="nr_associado"
                                            value="{{ $membro->nr_associado }}" disabled>
                                    </div>
                                </div>
                            </div>
                            <!-- Linha 2 -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="celular" class="form-label">Telefone</label>
                                        <input type="text" class="form-control" name="celular"
                                            value="{{ $membro->descricao ? '(' . $membro->descricao . ')' : '' }} {{ $membro->celular }}"
                                            disabled>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="dt_nascimento" class="form-label">Data de nascimento</label>
                                        <input type="text" class="form-control" name="dt_nascimento"
                                            value="{{ \Carbon\Carbon::parse($membro->dt_nascimento)->format('d/m/Y') }}"
                                            disabled>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="id_funcao" class="form-label">Função</label>
                                        <input type="text" class="form-control" name="id_funcao"
                                            value="{{ $membro->nome_funcao }}" disabled>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
                <br>
                <div class="card">
                    <div class="card-header">
                        Presenças
                    </div>
                    <div class="card-body">

                        <div class="accordion" id="accordionExample">
                            @foreach ($presencas as $key => $presenca)
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed'}}" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse{{ $key }}" aria-expanded="true"
                                            aria-controls="collapse{{ $key }}">
                                            {{ $key }}
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $key }}" class="accordion-collapse collapse  {{ $loop->first ? 'show' : ''}}"
                                        data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <table
                                                class="table table-sm table-striped table-bordered border-secondary table-hover align-middle text-center">
                                                <thead>
                                                    <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                                                        <th>REVERTER</th>
                                                        <th>DATA</th>
                                                        <th>GRUPO</th>
                                                        <th>PRESENÇA</th>
                                                    </tr>
                                                <tbody style="font-size: 14px; color:#000000; text-align:center;">
                                                    <div class="accordion" id="accordionExample">
                                                        @foreach ($presenca as $itemPresenca)
                                                            <tr>
                                                                <td>
                                                                    <input
                                                                        class="form-check-input checkbox-trigger check_io"
                                                                        type="checkbox"
                                                                        name="checkbox[{{ $itemPresenca->id }}]"
                                                                        value="{{ $itemPresenca->presenca }}">

                                                                </td>
                                                                <td>{{ date('d/m/Y', strtotime($itemPresenca->data)) }}
                                                                </td>
                                                                <td>{{ $itemPresenca->nome }}</td>
                                                                @if ($itemPresenca->presenca == 1)
                                                                    <td style="background-color:#90EE90;">Presente</td>
                                                                @else
                                                                    <td style="background-color:#FA8072;">Ausente</td>
                                                                @endif
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

                <!-- Botões -->
                <br>
                <div class="row mt-1 justify-content-center">
                    <div class="d-grid gap-1 col-4 mx-auto">
                        <a class="btn btn-danger" href="/gerenciar-membro/{{ $membro->id_cronograma }}"
                            role="button">Cancelar</a>
                    </div>
                    <div class="d-grid gap-2 col-4 mx-auto">
                        <button type="submit" class="btn btn-primary">Confirmar</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    @endsection
