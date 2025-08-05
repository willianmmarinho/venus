@extends('layouts.app')
@section('title', 'Transferir Membros')
@section('content')

    <br>
    <div class="container">

        <form action="/transferir-membro/{{$id}}">

            <div class="row">
                <div class="col-4">
                    Nome do Medium
                    <input type="text" class="form-control" id="nome_pesquisa" placeholder="Pesquisar Nome...">
                </div>
                <div class="col-5">
                    Grupo de Destino
                        <select class="form-select select2 grupo" id="nome_grupo" name="nome_grupo" required>
                            <option value=""></option>
                            @foreach ($grupos as $gr)
                                <option value="{{ $gr->idg }}"
                                    {{ request('nome_grupo') == $gr->idg ? 'selected' : '' }}>
                                    {{ $gr->nomeg }} ({{ $gr->sigla }})-{{ $gr->dia_semana }}
                                    | {{ date('H:i', strtotime($gr->h_inicio)) }}/{{ date('H:i', strtotime($gr->h_fim)) }}
                                    | Sala {{ $gr->sala }}
                                    | {{ $gr->status == 'Inativo' ? 'Inativo' : $gr->descricao_status }}
                                </option>
                            @endforeach
                        </select>
                </div>
                <div class="col-3 mt-4">
                    <button type="submit" class="btn btn-primary">Confirmar Transferencia</button>

                </div>
            </div>

            <br>
            <div class="card">
                <div class="card-header">
                    Transferir Membros
                </div>
                <div class="card-body">
                    <table
                        class="table table-sm table-striped table-bordered border-secondary table-hover align-middle text-center" id="myTable">
                        <thead>
                            <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                                <th>NºASSOCIADO</th>
                                <th>NOME DO MÉDIUM</th>
                                <th>FUNÇÃO</th>
                                <th>
                                    <input type="checkbox" class="btn-check " id="btn-check-4" autocomplete="off">
                                    <label class="btn btn-outline-success btn-sm" for="btn-check-4">Todos</label>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($membros as $membro)
                                <tr>
                                    <td> {{ $membro->nr_associado }} </td>
                                    <td> {{ $membro->nome_completo }} </td>
                                    <td> {{ $membro->nome }} </td>
                                    <td> <input class="form-check-input check" type="checkbox" name="check[]" value="{{ $membro->id }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
        </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {

            $('#nome_grupo').prop('selectedIndex', -1)

            $('#btn-check-4').change(function() {
                if ($('#btn-check-4').prop('checked') == true) {
                    $('.check').prop('checked', true)
                } else {
                    $('.check').prop('checked', false)
                }
            })
        });
    </script>
     <script>
        $(document).ready(function() {
            $("#nome_pesquisa").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                console.log(value)
                $("#myTable tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>






@endsection
