@extends('layouts.app')

@section('title')
    Editar Atendimento
@endsection

@section('content')
    <div class="container">

        {{-- Card de assistidos --}}
        <br />
        <div class="card">
            <div class="card-header">
                Editar Atendimento
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <span>
                            Buscar Pessoa
                            <span class="tooltips">
                                <span class="tooltiptext">Obrigatório</span>
                                <span style="color:red">*</span>
                            </span>
                        </span>
                        <div class="input-group mt-1">
                            <input type="text" class="form-control " placeholder="" aria-label="Recipient's username"
                                aria-describedby="button-addon2" id="cpfAssistido" maxlength="100" disabled>
                            <button class="btn btn-outline-primary" type="button" id="bCpfAssistido" disabled>
                                Buscar <i class="bi bi-search"></i>
                            </button>
                            <button href="/dados-pessoa" type="button" class="btn btn-outline-success" disabled>
                                Inserir Nova Pessoa
                            </button>
                        </div>

                        <label id="labelNumeroCpfAssistido" style="font-size: 14px; color:red" hidden>
                            *Número insuficiente de caracteres.
                        </label>
                        <label id="labelCpfAssistido" style="font-size: 14px; color:red" hidden>
                            *Nenhuma pessoa encontrada.
                        </label>
                    </div>
                </div>
                <div class="row">

                    <form class="form-horizontal mt-3" method="post" action="/grava-atualizacao/{{ $result->ida }}">
                        @csrf

                        <div class="input-group row">
                            <div class="col-3">Tipo Prioridade
                                <span class="tooltips">
                                    <span class="tooltiptext">Obrigatório</span>
                                    <span style="color:red">*</span>
                                </span>
                                <select class="form-select" id="" name="priori" required="required">
                                    @foreach ($priori as $prioris)
                                        <option value="{{ $prioris->prid }}"
                                            {{ $result->prid == $prioris->prid ? 'selected' : '' }}>{{ $prioris->prdesc }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">Atendido
                                <span class="tooltips">
                                    <span class="tooltiptext">Obrigatório</span>
                                    <span style="color:red">*</span>
                                </span>
                                <select class="form-control" id="assist" name="assist" required="required" disabled>
                                    <option>{{ $result->nm_1 }}</option>
                                </select>
                            </div>
                        </div>
                        <center>
                            <div class="row mt-3">


                                <div class="col">
                                    <div class="col">Menor de 18 anos</div>
                                    <div class="col-1"><input id="menor" type="checkbox" name="menor"
                                            data-size="small" data-toggle="toggle" data-onstyle="success"
                                            data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não"
                                            {{ $result->menor == true ? 'checked' : '' }}></div>
                                </div>
                                <div class="col">
                                    <div class="col">Representante</div>
                                    <div class="col-1"><input id="representante" class="checkboxes" type="checkbox"
                                            name="representante" data-size="small" data-toggle="toggle"
                                            data-onstyle="success" data-offstyle="danger" data-onlabel="Sim"
                                            data-offlabel="Não" {{ $result->nm_2 == null ? '' : 'checked' }}></div>
                                </div>
                                <div class="col">
                                    <div class="col">Pedido Especial</div>
                                    <div class="col-1"><input id="pEspecial" class="checkboxes" type="checkbox"
                                            name="pEspecial" data-size="small" data-toggle="toggle" data-onstyle="success"
                                            data-offstyle="danger" data-onlabel="Sim" data-offlabel="Não"
                                            {{ $result->idsx != null ? 'checked' : '' }}
                                            {{ $result->iap != null ? 'checked' : '' }}{{ $result->tpat != 1 ? 'checked' : '' }}>
                                    </div>
                                </div>


                            </div>
                        </center>
                </div>
            </div>
        </div>
        {{-- Fim do card de Assistido --}}

        {{-- Card Representante --}}
        <div class="card mt-4" id="represent" hidden>

            <div class="card-header">
                Incluir Representante/Responsável
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <span>
                            Buscar Pessoa
                        </span>
                        <div class="input-group mt-1">
                            <input type="text" class="form-control " placeholder="Nome..."
                                aria-label="Recipient's username" aria-describedby="button-addon2" id="cpfResponsavel"
                                maxlength="100">
                            <button class="btn btn-outline-primary" type="button" id="bCpfResponsavel">
                                Buscar <i class="bi bi-search"></i>
                            </button>
                            <a href="/dados-pessoa" type="button" class="btn btn-outline-success">
                                Inserir Nova Pessoa
                            </a>
                        </div>
                        <label id="labelNumeroCpfResponsavel" style="font-size: 14px; color:red" hidden>
                            *Número insuficiente de caracteres.
                        </label>
                        <label id="labelCpfResponsavel" style="font-size: 14px; color:red" hidden>
                            *Nenhuma pessoa encontrada.
                        </label>

                    </div>
                </div>
                <div class="row">
                    <div class="input-group row mt-3">
                        <div class="col-3">Parentesco
                            <select class="form-select" id="parent" name="parent">
                                @foreach ($pare as $parentess)
                                    <option value="{{ $parentess->idp }}"
                                        {{ $result->idp == $parentess->idp ? 'selected' : '' }}>{{ $parentess->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">Representante/Responsável
                            <select class="form-select lista" id="repres" name="repres">
                                <option value="{{ $result->idr }}">{{ $result->nm_2 }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Fim card Representante --}}

        {{-- Card Pedido Especial --}}
        <div class="card mt-4" id="pedidoEspecial" hidden>
            <div class="card-header">
                Incluir Pedido Especial
            </div>
            <div class="card-body">
                <div class="row">


                    <div class="col">Tipo AFI
                        <select class="form-select pedido" id="tipo_afi" name="tipo_afi">
                            <option></option>
                            @foreach ($sexo as $sexos)
                                <option value="{{ $sexos->idsx }}"
                                    {{ $result->idsx == $sexos->idsx ? 'selected' : '' }}>{{ $sexos->tipo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-3">Tipo Atendimento
                        <select class="form-select pedido" id="tipo_atendimento" name="tipo_atendimento">
                            @foreach ($tipoAtendimento as $tipo)
                                <option value="{{ $tipo->id }}" {{ $result->tpat == $tipo->id ? 'selected' : '' }}>
                                    {{ $tipo->sigla }}</option>
                            @endforeach
                        </select>
                    </div>





                    <div class="col" id="hiddenField">AFI preferido
                        <select class="form-select pedido" id="afi_p" name="afi_p">
                            <option></option>
                            @foreach ($afi as $afis)
                                <option value="{{ $afis->iaf }}"
                                    {{ $afiSelecionado == $afis->iaf ? 'selected' : '' }}> {{ $afis->nm_afi }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                </div>
            </div>
        </div>
        {{-- Fim Card Pedido Especial --}}



        <center>
            <br>
            <div class="row col-10">
                <div class="d-grid gap-1 col mx-auto">
                    <a class="btn btn-danger" href="/gerenciar-atendimentos" role="button">Cancelar</a>
                </div>

                <div class="d-grid gap-2 col mx-auto">
                    <button type="submit" class="btn btn-primary" style="color:#fff;">Confirmar</button>
                </div>
                </form>

            </div>
        </center>
    </div>






    <script>
        $(document).ready(function() {



            function cards() {
                if ($('#representante').prop('checked')) {
                    $('#represent').prop('hidden', false)
                } else {
                    $('#repres').html('')
                    //   $('#repres').prop('selectedIndex', -1)
                    //   $('#parent').prop('selectedIndex', -1)
                    $('#represent').prop('hidden', true)
                }
                if ($('#pEspecial').prop('checked')) {
                    $('#pedidoEspecial').prop('hidden', false)
                } else {

                    $('#pedidoEspecial').prop('hidden', true)
                }
            }

            function pedidos() {
                if ($('#tipo_afi').prop('selectedIndex') != 0) {
                    $('#afi_p').prop('disabled', true)
                    $('#afi_p').prop('selectedIndex', 0)
                } else {
                    $('#afi_p').prop('disabled', false)
                }

                if ($('#afi_p').prop('selectedIndex') != 0) {
                    $('#tipo_afi').prop('disabled', true)
                    $('#tipo_afi').prop('selectedIndex', 0)
                } else {
                    $('#tipo_afi').prop('disabled', false)
                }
            }

            function ajaxAssistido() {
                nome = $('#cpfAssistido').val()
                $('#cpfAssistido').removeClass('is-invalid')
                $('#labelNumeroCpfAssistido').prop('hidden', true)
                $('#labelCpfAssistido').prop('hidden', true)
                $('#assist').html('')
                $('#assist').prop('selectedIndex', -1)
                if (nome.length < 1) {
                    $('#cpfAssistido').addClass('is-invalid')
                    $('#labelNumeroCpfAssistido').prop('hidden', false)
                } else {
                    $.ajax({
                        type: "GET",
                        url: "/ajaxCRUD?nome=" + nome,
                        dataType: "json",
                        success: function(response) {
                            console.log(response)
                            if (response.length == 0) {
                                $('#cpfAssistido').addClass('is-invalid')
                                $('#labelCpfAssistido').prop('hidden', false)
                            } else {
                                $.each(response, function() {

                                    $('#assist').append([
                                        '<option value="' + this.id + '">' +
                                        this.nome_completo +
                                        '</option>'
                                    ])
                                })
                            }


                        },
                        error: function(xhr) {
                            console.log(xhr.responseText)
                            $('#cpfAssistido').addClass('is-invalid')
                            $('#labelCpfAssistido').prop('hidden', false)

                        }
                    });
                }
            }

            function ajaxResponsavel() {
                nome = $('#cpfResponsavel').val()
                $('#cpfResponsavel').removeClass('is-invalid')
                $('#labelNumeroCpfResponsavel').prop('hidden', true)
                $('#labelCpfResponsavel').prop('hidden', true)
                $('#repres').html('')
                $('#repres').prop('selectedIndex', -1)
                $('#parent').prop('selectedIndex', -1)
                if (nome.length < 1) {
                    $('#cpfResponsavel').addClass('is-invalid')
                    $('#labelNumeroCpfResponsavel').prop('hidden', false)
                } else {
                    $.ajax({
                        type: "GET",
                        url: "/ajaxCRUD?nome=" + nome,
                        dataType: "json",
                        success: function(response) {

                            if (response.length == 0) {
                                $('#cpfResponsavel').addClass('is-invalid')
                                $('#labelCpfResponsavel').prop('hidden', false)
                            } else {
                                $.each(response, function() {

                                    $('#repres').append([
                                        '<option value="' + this.id + '">' +
                                        this.nome_completo +
                                        '</option>'
                                    ])
                                })
                            }



                        },
                        error: function(xhr) {

                            $('#cpfResponsavel').addClass('is-invalid')
                            $('#labelCpfResponsavel').prop('hidden', false)

                        }
                    });
                }
            }




            $('#bCpfAssistido').click(function() {
                ajaxAssistido();
            })
            $('#bCpfResponsavel').click(function() {
                ajaxResponsavel();
            })

            cards()
            $('.checkboxes').change(function() {
                cards()
            })

            pedidos()
            $('.pedido').change(function() {
                pedidos()
            })

        })
    </script>
@endsection
