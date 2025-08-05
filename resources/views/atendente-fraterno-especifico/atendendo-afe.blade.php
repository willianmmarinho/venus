@extends('layouts.app')

@section('title') Atendimento Fraterno Especifico @endsection

@section('content')




<?php
//echo "<meta HTTP-EQUIV='refresh' CONTENT='30;URL=gerenciar-atendimentos'>";
?>

<div class="container-xxl" ;>
    <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">ATENDIMENTO FRATERNO ESPECIFICO</h4>
    <div class="col-12">
        <hr>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-2">Data
                        <input class="form-control" style="font-weight:bold; background: #f3f3f3; color: rgb(0, 0, 0);" value="{{date( 'd/m/Y' , strtotime ($now))}}" type="text" name="data" id="" disabled>
                    </div>
                    <div class="col-3">Grupo
                        <input class="form-control" style="text-align:left; font-weight:bold; background: #f3f3f3; color: rgb(0, 0, 0);" value="{{$grupo}}" name="nome" id="" type="text" disabled>
                    </div>

                    <div class="col-2">Código Atendente
                        <input class="form-control" style="font-weight:bold; background:#f3f3f3; color:#000;" type="text" name="id_atendene" id="" value="{{$atendente}}" disabled>
                    </div>

                    <div class="col-5">Nome do Atendente
                        <input class="form-control" style="font-weight:bold; background: #f3f3f3; color: rgb(0, 0, 0);" value="{{$nome}}" name="nome_usuario" id="" type="text" disabled>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row" style="text-align:right;">
            <div class="col-6">
                <a href="/meus-atendimentos-afe"><input class="btn btn-light btn-sm me-md-2" style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" value="Meus atendimentos"></a>
            </div>
            <div class="col-6">
                <a href="{{ route('Atender-afe') }}"><input class="btn btn-success btn-sm me-md-2" type="button" value="Atender agora"></a>
            </div>
        </div>
        <br>
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="table">
                        <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                            <thead style="text-align: center;">
                                <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                                    <th class="col">NR</th>
                                    <th class="col-2">ATENDENTE PREFERIDO</th>
                                    <th class="col-1">TIPO AF</th>
                                    <th class="col-1">HORÁRIO CHEGADA</th>
                                    <th class="col">PRIORIDADE</th>
                                    <th class="col-2">ATENDIDO</th>
                                    <th class="col-2">REPRESENTANTE</th>
                                    <th class="col-1">STATUS</th>
                                    <th class="col">AÇÕES</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 14px; color:#000000; text-align:center;">
                                @foreach($assistido as $assistidos)
                                <tr>
                                    <td scope="">{{$assistidos->idat}}</td>
                                    <td scope="">{{$assistidos->nm_3}}</td>
                                    <td scope="">{{$assistidos->tipo}}</td>
                                    <td scope="">{{date( 'd/m/Y H:i', strtotime($assistidos->dh_chegada))}}</td>
                                    <td scope="">{{$assistidos->prdesc}}</td>
                                    <td scope="">{{$assistidos->nm_1}}</td>
                                    <td scope="">{{$assistidos->nm_2}}</td>
                                    <td scope="">{{$assistidos->descricao}}</td>
                                    <td scope="">
                                        <a href="/historico-afe/{{$assistidos->idat}}/{{$assistidos->idas}}"><button type="button" class="btn btn-outline-primary btn-sm tooltips" ><span class="tooltiptext">Analisar</span><i class="bi bi-search" style="font-size: 1rem; color:#000;"></i></button></a>
                                        <a href="/fim-analise-afe/{{$assistidos->idat}}"><button type="button" class="btn btn-outline-warning btn-sm tooltips"><span class="tooltiptext" style="width:150px; margin-left:-75px">Chamar Assistido</span><i class="bi bi-bell" style="font-size: 1rem; color:#000;"></i></button></a>
                                        <a href="/iniciar-atendimento-afe/{{$assistidos->idat}}"><button type="button" class="btn btn-outline-success btn-sm tooltips"><span class="tooltiptext">Iniciar</span><i class="bi bi-check-circle" style="font-size: 1rem; color:#000;"></i></button></a>
                                        <a href="/tratar-afe/{{$assistidos->idat}}/{{$assistidos->idas}}"><button type="button" class="btn btn-outline-warning btn-sm tooltips"><span class="tooltiptext">Tratamento</span><i class="bi bi-bandaid" style="font-size: 1rem; color:#000;"></i></button></a>
                                        <a href="/entrevistar-afe/{{$assistidos->idat}}/{{$assistidos->idas}}"><button type="button" class="btn btn-outline-warning btn-sm tooltips" ><span class="tooltiptext">Entrevista</span><i class="bi bi-mic" style="font-size: 1rem; color:#000;"></i></button></a>
                                        <a href="/temas-afe/{{$assistidos->idat}}"><button type="button" class="btn btn-outline-warning btn-sm tooltips"><span class="tooltiptext">Temática</span><i class="bi bi-journal-bookmark-fill" style="font-size: 1rem; color:#000;"></i></button></a>
                                        <button type="button" class="btn btn-outline-danger btn-sm tooltips"
                                        ><span class="tooltiptext">Reset</span><i
                                            class="bi bi-arrow-repeat" style="font-size: 1rem; color:#000;"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalRel{{ $assistidos->idat }}"></i></button>
                                    <button type="button" class="btn btn-outline-danger btn-sm tooltips"

                                        data-bs-toggle="modal"
                                        data-bs-target="#modalF{{ $assistidos->idat }}"><span class="tooltiptext">Finalizar</span><i
                                            class="bi bi-door-open"
                                            style="font-size: 1rem; color:#000;"></i></button>


                                    {{-- Modal de Reset --}}
                                    <div class="modal fade" id="modalRel{{ $assistidos->idat }}" tabindex="-1"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header"
                                                    style="background-color:rgb(196, 27, 27);">
                                                    <h5 class="modal-title" id="exampleModalLabel"
                                                        style=" color:white">Reiniciar</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Tem certeza que deseja resetar? <br /><span
                                                        style="color:rgb(196, 27, 27);">Todo o progresso feito
                                                        até aqui será apagado!</span>&#63;

                                                </div>
                                                <div class="modal-footer mt-2">
                                                    <button type="button" class="btn btn-danger"
                                                        data-bs-dismiss="modal">Cancelar</button>
                                                    <a type="button" class="btn btn-primary"
                                                        href="/reset/{{ $assistidos->idat }}">Confirmar
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Modal de Reset Fim --}}


                                    {{-- Modal de Finalizar --}}
                                    <form action="/finalizar-afe/{{ $assistidos->idat }}" method="POST">
                                        @csrf
                                        <div class="modal fade" id="modalF{{ $assistidos->idat }}"
                                            tabindex="-1" aria-labelledby="exampleModalLabel"
                                            aria-hidden="true">
                                            <div class="modal-dialog">

                                                <div class="modal-content">
                                                    <div class="modal-header"
                                                        style="background-color:rgb(196, 27, 27);">
                                                        <h5 class="modal-title" id="exampleModalLabel"
                                                            style=" color:white">Finalizar Atendimento</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                Tem certeza que deseja finalizar o atendimento
                                                                de:
                                                                <br /><span
                                                                    style="color:rgb(196, 27, 27);">{{ $assistidos->nm_1 }}</span>&#63;


                                                            </div>
                                                            <center>
                                                                <div class="col-9 mt-5">
                                                                    <span
                                                                    
                                                                        style="color:rgb(255, 147, 7); font-weight: bold">*</span>
                                                                    Não esqueça de conferir se os seus encaminhamentos foram registrados corretamentes,
                                                                    Utilizando para isso a ação de visualizar.
                                                                </div>
                                                            </center>





                                                        </div>
                                                    </div>

                                                    <div class="modal-footer mt-2">
                                                        <button type="button" class="btn btn-danger"
                                                            data-bs-dismiss="modal">Cancelar</button>

                                                        <button type="submit" class="btn btn-primary">Confirmar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    {{-- Modal de Finalizar Fim --}}





                                    <!--<button class="btn btn-outline-warning btn-sm" type="button" id="" data-bs-toggle="modal" data-bs-target="#tratamento{{ $assistidos->idat }}" data-toggle="tooltip" data-placement="top" title="Tratamentos"><i class="bi bi bi-bandaid" style="font-size: 1rem; color:#000;"></i></button>
                                        <button class="btn btn-outline-warning btn-sm" type="button" id="" data-bs-toggle="modal" data-bs-target="#entrevista{{ $assistidos->idat }}" data-toggle="tooltip" data-placement="top" title="Entrevistas"><i class="bi bi bi-mic" style="font-size: 1rem; color:#000;"></i></button>
                                        <button class="btn btn-outline-warning btn-sm" type="button" id="" data-bs-toggle="modal" data-bs-target="#anotacoes{{ $assistidos->idat }}" data-toggle="tooltip" data-placement="top" title="Entrevistas"><i class="bi bi-journal-bookmark-fill" style="font-size: 1rem; color:#000;"></i></button>
                                        <button class="btn btn-outline-danger btn-sm" type="button" id="" data-bs-toggle="modal" data-bs-target="#finalizar{{ $assistidos->idat }}" data-toggle="tooltip" data-placement="top" title="Finalizar"><i class="bi bi-door-open" style="font-size: 1rem; color:#000;"></i></button>-->
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>
</div>
</div>

<style>
.emergencia {
opacity: 50%;
}
</style>



@endsection
