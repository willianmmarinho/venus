@extends('layouts/app')
@section('title', 'Encaminhamento Entrevista')
@section('content')

    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                ENCAMINHAR PARA ENTREVISTA
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-2">Nr Atendimento
                                <input class="form-control" type="numeric" name="id" value="{{ $assistido[0]->idat }}"
                                    disabled>
                            </div>
                            <div class="col">Nome Atendido
                                <input class="form-control" type="text" name="nome" value="{{ $assistido[0]->nm_1 }}"
                                    disabled>
                            </div>
                        </div>
                        <form class="form-horizontal mt-4" method="POST"
                            action="/entrevistas/{{ $assistido[0]->idat }}/{{ $assistido[0]->idas }}" id="entr">
                            @csrf
                            <div class="row">
                                <div class="col">
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" id="afe" name="afe" class="form-check-input"
                                            data-size="small" data-toggle="toggle" data-size="sm" data-onstyle="success"
                                            data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                        @if ($atendimento == 2)
                                            <label for="afe" class="form-check-label">Declarar Alta - AFE</label>
                                        @else<label for="afe" class="form-check-label">Atendente Fraterno
                                                Específico -
                                                AFE</label>
                                        @endif
                                    </div>
                                    <br>
                                    <br>
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" id="ame" name="ame" class="form-check-input"
                                            data-size="small" data-toggle="toggle" data-size="sm" data-onstyle="success"
                                            data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                        <label for="ame" class="form-check-label">Assessoria da Medicina Espiritual -
                                            AME</label>
                                    </div>
                                    <br>
                                    <br>
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" id="diamo" name="diamo" class="form-check-input"
                                            data-size="small" data-toggle="toggle" data-size="sm" data-onstyle="success"
                                            data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                        <label for="diamo" class="form-check-label">Divisão de Apoio ao Médium Ostensivo
                                            em Eclosão da Mediunidade - DIAMO</label>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" id="nutres" name="nutres" class="form-check-input"
                                            data-size="small" data-toggle="toggle" data-size="sm" data-onstyle="success"
                                            data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                        <label for="nutres" class="form-check-label">Núcleo de Tratamento Espiritual -
                                            NUTRES</label>
                                    </div>
                                    <br>
                                    <br>
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" id="gel" name="gel" class="form-check-input"
                                            data-size="small" data-toggle="toggle" data-size="sm" data-onstyle="success"
                                            data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não">
                                        <label for="gel" class="form-check-label">Grupo de Evangelho no Lar -
                                            GEL</label>
                                    </div>
                                </div>
                            </div>

                            {{-- Inicio Modal Aviso PROAMO --}}
                            <div class="modal fade" id="avisoProamo" tabindex="-1" aria-labelledby="exampleModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color:rgb(255, 147, 7);color:white;">
                                            <h1 class="modal-title fs-5" id="inativarLabel">
                                                AVISO PROAMO</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <center>
                                            <div class="modal-body col-10">
                                                Este assistido só será chamado para uma entrevista PROAMO após
                                                partipar de um
                                                <br>
                                                <br>

                                                <span style="color:rgb(255, 147, 7); font-weight: bold">Tratamento PTD por
                                                    8 presenças</span>
                                            </div>
                                        </center>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger"
                                                data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary">Confirmar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Fim modal aviso PROAMO --}}

                            <br>
                            <hr>
                            <div class="row">
                                <div class="col" style="text-align: right;">
                                    <a class="btn btn-danger" href="/atendendo"
                                        style="text-align:right; margin-right: 10px" role="button">Cancelar</a>
                                    <button type="button" class="btn" id="sbmt"
                                        style="background-color:#007bff; color:#fff;">Confirmar</button>
                        </form>
                    </div>
                </div>
            </div>
            <div>
            </div>
        </div>
        <script>
            $('#sbmt').click(function() {
                let diamo = $('#diamo').prop('checked')

                if (diamo) {
                    $('.modal').modal('hide')
                    $('#avisoProamo').modal('show')
                } else {
                    $('#entr').submit()
                }
            })
        </script>
    @endsection
