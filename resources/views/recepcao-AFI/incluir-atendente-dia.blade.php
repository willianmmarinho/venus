@extends('layouts.app')

@section('title')
    Definir AFI
@endsection

@section('content')
    <div class="container";>
        <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">
            DEFINIR AFI/SALA</h4>
        <div class="col-12">
            <div class="row justify-content-center">
                <div>
                    <form class="form-horizontal mt-4" method="GET">
                        <div class="row">
                            {{-- <div class="col-2">Grupo
                       <select class="form-select" id="" name="grupo" type="number">
                            <option value=""></option>
                            @foreach ($grupo as $grupos)
                            <option @if (old('grupo') == $grupos->id) {{'selected="selected"'}} @endif value="{{ $grupos->id }}">{{$grupos->nome}}</option>
                            @endforeach
                        </select>
                    </div> --}}
                            <div class="col-2">Atendente
                                <select class="form-select select2" name="atendente" type="number">
                                    <option value=""></option>
                                    @foreach ($atendentesParaSelect as $atendes)
                                        <option @if (old('atendente') == $atendes->ida) {{ 'selected="selected"' }} @endif
                                            value="{{ $atendes->ida }}">{{ $atendes->nm_4 }}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="col-2">Status
                                <select class="form-select" id="" name="status" type="number">
                                    <option value="">Todos</option>
                                    @foreach ($situacao as $sit)
                                        <option value="{{ $sit->ids }}">{{ $sit->tipo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col"><br>
                                <input type="submit" formaction="{{ route('afisal') }}"
                                    class="btn btn-light btn-sm me-md-2"
                                    style="box-shadow: 1px 2px 5px #000000; margin:5px;" value="Pesquisar">
                                <a href="/definir-sala-atendente"><input class="btn btn-light btn-sm me-md-2"
                                        style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                                        value="Limpar"></a>
                    </form>
                </div>
            </div>
            <br>
        </div style="text-align:right;">
        <hr>
        <div class="col" style="text-align: center;">
            <a href="/gerenciar-atendente-dia"><input class="btn btn-danger btn-sm me-md-2"
                    style="box-shadow: 1px 2px 5px #000000; margin:5px; font-size: 0.9rem;" type="button"
                    value="Retornar principal"></a>
        </div>
        <div class="table"> Total de selecionados: {{ $contar }}


            <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                <thead style="text-align: center;">
                    <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                        <th class="col">NR</th>
                        <th class="col">GRUPO</th>
                        <th class="col">ATENDENTE</th>
                        <th class="col-1">SALA</th>
                        <th class="col">TIPO</th>
                        <th class="col">AÇÕES</th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px; color:#000000; text-align: center;">
                    @foreach ($atende as $atendes)
                        <form class="form-horizontal mt-4" method="POST">
                            @csrf
                            <tr>
                                <td>{{ $atendes->ida }}</td>
                                <td>
                                    <select class="form-select" id="" name="grupo" type="number">

                                        @foreach ($atendes->grup as $results)
                                            <option value="{{ $results->id }}">{{ $results->gnome }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>{{ $atendes->nm_4 }}</td>
                                <td><select class="form-select" id="" name="sala" type="number">
                                        @foreach ($sala as $salas)
                                            <option value="{{ $salas->id }}">{{ $salas->numero }}</option>
                                        @endforeach
                                    </select></td>
                                <td>
                                    <select class="form-select" id="" name="atendimento">
                                        @foreach ($tipoAtendimento as $tipo)
                                            <option value="{{ $tipo->id }}"
                                                {{ (!in_array($atendes->ida, $membros) and $tipo->id == 2) ? 'hidden' : null }}>
                                                {{ $tipo->sigla }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <button type="submit" formaction="/incluir-afi-sala/{{ $atendes->ida }}"
                                        class="btn btn-success btn-sm" style="color:#fff;">Confirmar</button>
                                    <!--<a href="/incluir-afi-sala/{{ $atendes->idat }}"><input class="btn btn-light btn-sm me-md-2" formaction="/incluir-afi-sala/{{ $atendes->idat }}" style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" value="cONFIRMAR"></a>                                                  -->
                                </td>
                            </tr>
                        </form>
                    @endforeach
                </tbody>
            </table>

        </div class="d-flex justify-content-center">
        {{ $atende->links('pagination::bootstrap-5') }}
    </div>
    </div>
    </div>
@endsection
