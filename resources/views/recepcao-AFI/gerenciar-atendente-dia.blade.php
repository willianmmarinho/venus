@extends('layouts.app')

@section('title') Gerenciar Atendente dia @endsection

@section('content')


<div class="container-fluid";>
<h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR ATENDENTES DO DIA</h4>
    <div class="col-12">
        <div class="row justify-content-center">
            <div>
                <form action="{{route('afidia')}}" class="form-horizontal mt-4" method="GET" >
                <div class="row">
                    <div class ="col">Data
                        <input class="form-control" type="date" id="data" name="data" value="{{$data}}">
                    </div>
                    <div class="col">Grupo
                        <select class="form-select pesquisa" id="" name="grupo" type="number">
                            <option value=""></option>
                            @foreach ($grupo as $grupos)
                            {{--<option value="{{ old('grupo', $grupos->id) }}" selected="{{ old('grupo') == $grupos->id ? 'selected' : '' }}">{{$grupos->nome}}</option>--}}
                            <option @if(old('grupo') == $grupos->id) {{'selected="selected"'}} @endif value="{{ $grupos->id }}">{{$grupos->nome}}</option>
                            @endforeach

                        </select>
                    </div>
                    <div class="col">Atendente
                        <input class="form-control pesquisa" type="text" id="" name="atendente" value="{{$atendente}}">
                    </div>
                    <div class="col">Status
                        <select class="form-select pesquisa" id="" name="status" type="number">
                            <option value="Ativo" {{ old('status') == 'Ativo' ? 'selected' : '' }}>Ativo</option>
                            <option value="Inativo" {{ old('status') == 'Inativo' ? 'selected' : '' }}>Inativo</option>
                            <option value="" {{ old('status') == '' ? 'selected' : '' }}>Todos</option>
                        </select>
                    </div>
                        <div class="col-5"><br>
                            <input class="btn btn-light btn-sm me-md-2" style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
                            <a href="/gerenciar-atendente-dia"><input class="btn btn-light btn-sm me-md-2"  style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" value="Limpar"></a>
                            <a href="/definir-sala-atendente"><input class="btn btn-success btn-sm me-md-2" autofocus type="button" value="Atendente / Sala"></a>
                    </form>
                    <a href="/gerenciar-atendimentos"><input class="btn btn-danger btn-sm me-md-2" style="font-size: 0.9rem;" type="button" value="Retornar principal"></a>
                        </div>
                </div>
                <br>
            </div style="text-align:right;">
            <hr>
            <div class="table">Total atendentes:
                <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                    <thead style="text-align: center;">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th class="col">Nr</th>
                            <th class="col">INÍCIO</th>
                            <th class="col">FIM</th>
                            <th class="col">GRUPO</th>
                            <th class="col">ATENDENTE</th>
                            <th class="col">SALA</th>
                            <th class="col">STATUS ATENDENTE</th>
                            <th class="col">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color:#000000; text-align: center;">
                        <tr>
                        @foreach($atende as $atendes)
                            <td scope="">{{$atendes->nr}}</td>
                            <td scope="">{{date( 'd/m/Y G:i', strtotime($atendes->dh_inicio))}}</td>
                            <td scope="">{{$atendes->dh_fim ? date( 'G:i', strtotime($atendes->dh_fim)) : '--'}}</td>
                            <td scope="">{{$atendes->nomeg}}</td>
                            <td scope="">{{$atendes->nm_4}}</td>
                            <td scope="">{{$atendes->nm_sala}}</td>
                            <td scope="">{{$atendes->status}}</td>
                            <td scope="">
                                @if ($atendes->dh_fim)
                                    <button disabled type="button" class="btn btn-outline-warning btn-sm" data-tt="tooltip" data-placement="top" title="Editar"><i class="bi bi-pen" style="font-size: 1rem; color:#000;"></i></button>
                                    <button  type="button" class="btn btn-outline-primary btn-sm" data-tt="tooltip" data-placement="top" title="Finalizar" disabled><i class="bi bi-calendar2-check" style="font-size: 1rem; color:#000;"></i></button>

                                @else
                                    <a href="/editar-atendente-dia/{{$atendes->idatd}}"><button  type="button" class="btn btn-outline-warning btn-sm tooltips"><span class="tooltiptext">Editar</span><i class="bi bi-pencil" style="font-size: 1rem; color:#000;"></i></button></a>
                                    <a href="/finalizar-atendente-dia/{{$atendes->idatd}}"><button  type="button" class="btn btn-outline-primary btn-sm tooltips"><span class="tooltiptext">Finalizar</span><i class="bi bi-calendar2-check" style="font-size: 1rem; color:#000;"></i></button></a>

                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div class="d-flex justify-content-center">
        </div>
    </div>
</div>




<script>


 $('.pesquisa').change(function () {
    $('#data').val("")
 })



</script>

@endsection

@section('footerScript')


@endsection
