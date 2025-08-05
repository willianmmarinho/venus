@extends('layouts.app')

@section('title')
    Gerenciar Assistido
@endsection
@section('content')
    <div class="container-fluid">
        <h4 class="card-title" style="font-size:20px; text-align: start; color: gray; font-family:calibri">GERENCIAR ASSISTIDO
        </h4>

        <div class="row mt-3">





            <div class="col-2">
                <a href="/criar-atendimento" class="btn btn-success btn-sm w-100"
                    style="box-shadow: 1px 2px 5px #000000; margin:5px;">Novo Atendimento</a>
            </div>

            <div class="col-2">
                <a href="/gerenciar-pessoas" class="btn btn-warning btn-sm w-100"
                    style="box-shadow: 1px 2px 5px #000000; margin:5px;">Nova Pessoa</a>
            </div>



            <div class="col-1">
                <a href="/gerenciar-atendente-dia" class="btn btn-warning btn-sm w-100"
                    style="box-shadow: 1px 2px 5px #000000; margin:5px;">Escala AFI</a>
            </div>
            <div class="col-1">
                <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#filtros"
                    style="box-shadow: 1px 2px 5px #000000; margin:5px;">
                    Filtrar <i class="bi bi-funnel"></i>
                </button>
            </div>

            <div class="col d-flex justify-content-end">
                <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal"
                    style="box-shadow: 1px 2px 5px #000000; margin:5px;">
                    Colunas <i class="bi bi-gear"></i>
                </button>
            </div>


            {{-- Filtro Modal --}}
            <div class="modal fade" id="filtros" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color:grey;color:white">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Filtrar Opções</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <center>
                                <div class="col-10">
                                    <div class="col">
                                        <label for="assist">Atendido</label>
                                        <input class="form-control pesquisa" type="text" id="assist" name="assist"
                                            value="{{ $assistido }}">
                                    </div>
                                    <div class="col mt-3">
                                        <label for="assist">Atendente</label>
                                        <input class="form-control pesquisa" type="text" id="idatendente"
                                            name="atendente" value="{{ $atendente }}">
                                    </div>
                                    <div class="col mt-3">
                                        <label for="assist">CPF</label>
                                        <input class="form-control pesquisa" type="text" maxlength="11"
                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                            id="cpf" name="cpf" value="{{ $cpf }}">

                                    </div>
                                    <div class="col mt-3 ">
                                        <label for="status">Status</label>
                                        <select class="form-select pesquisa" id="status" name="status" type="number">

                                            @foreach ($st_atend as $statusz)
                                                <option {{ $situacao == $statusz->id ? 'selected' : '' }}
                                                    value="{{ $statusz->id }}">{{ $statusz->descricao }}</option>
                                            @endforeach
                                        </select>
                                    </div>


                                    <div class="col  mt-3  mb-3">
                                        <label for="dt_ini">Data início</label>
                                        <input class="form-control" type="date" id="dt_ini" name="dt_ini"
                                            value="{{ $data_inicio ?? now()->toDateString() }}">
                                    </div>
                                </div>
                            </center>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                            <button id="limpar" type="button" class="btn btn-secondary pesq"
                                data-bs-dismiss="modal">Limpar</button>
                            <button class="btn btn-primary pesq" id="confirmar" data-bs-dismiss="modal">Confirmar</button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Fim filtro Modal --}}

            {{-- Modal Colunas --}}
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color:grey;color:white">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Colunas Visualizadas</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">



                            <div class="col-10 mx-auto d-block">

                                <div class="col">
                                    <input class="form-check-input coluna" type="checkbox" value=""
                                        id="numeroAtendimento">
                                    <label class="form-check-label" for="flexCheckDefault"> Número do Atendimento </label>
                                </div>

                                <div class="col">
                                    <input class="form-check-input coluna" type="checkbox" value=""
                                        id="atendentePreferido" checked>
                                    <label class="form-check-label" for="flexCheckDefault"> Atendente Preferido </label>
                                </div>

                                <div class="col">
                                    <input class="form-check-input coluna" type="checkbox" value=""
                                        id="tipoAtendente">
                                    <label class="form-check-label" for="flexCheckDefault"> Tipo do Atendente </label>
                                </div>

                                <div class="col">
                                    <input class="form-check-input coluna" type="checkbox" value=""
                                        id="horarioChegada" checked>
                                    <label class="form-check-label" for="flexCheckDefault"> Horário de Chegada </label>
                                </div>

                                <div class="col">
                                    <input class="form-check-input coluna" type="checkbox" value=""
                                        id="prioridade" checked>
                                    <label class="form-check-label" for="flexCheckDefault"> Prioridade </label>
                                </div>

                                <div class="col">
                                    <input class="form-check-input coluna" type="checkbox" value=""
                                        id="atendimento" checked>
                                    <label class="form-check-label" for="flexCheckDefault"> Atendido </label>
                                </div>

                                <div class="col">
                                    <input class="form-check-input coluna" type="checkbox" value=""
                                        id="representante">
                                    <label class="form-check-label" for="flexCheckDefault"> Representante </label>
                                </div>

                                <div class="col">
                                    <input class="form-check-input coluna" type="checkbox" value="" id="atendente"
                                        checked>
                                    <label class="form-check-label" for="flexCheckDefault"> Atendente </label>
                                </div>

                                <div class="col">
                                    <input class="form-check-input coluna" type="checkbox" value="" id="sala"
                                        checked>
                                    <label class="form-check-label" for="flexCheckDefault"> Sala </label>
                                </div>

                                <div class="col">
                                    <input class="form-check-input coluna" type="checkbox" value=""
                                        id="tipoAtendimento" checked>
                                    <label class="form-check-label" for="flexCheckDefault"> Tipo do Atendimento </label>
                                </div>

                                <div class="col mb-3">
                                    <input class="form-check-input coluna" type="checkbox" value=""
                                        id="statusAtendimento" checked>
                                    <label class="form-check-label" for="flexCheckDefault"> Status </label>
                                </div>

                            </div>



                        </div>

                    </div>
                </div>
            </div>
            {{-- Fim modal Colunas --}}



        </div>

        <hr>
        <div class="row">
            <div class="table">
                <div style="display: flex; align-items: center; font-weight: bold;">
                    <div>Total Atendidos: {{ $contar }}</div>
                    <div style="border-inline-start: 2px solid #000; margin: 0 20px;"></div> <!-- Vertical bar -->
                    <div>Fila de Espera: <span id="id_pessoas_para_atender"></span></div>
                    <div style="border-inline-start: 2px solid #000; margin: 0 20px;"></div> <!-- Vertical bar -->
                    <div>Atendentes Disponíveis: <span id="id_atendentes"></span></div>
                </div>
            </div>
            <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                <thead style="text-align: center;">
                    <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                        <th class="col numeroAtendimento">Nr</th>
                        <th class="col atendentePreferido">AFI PREF</th>
                        <th class="col tipoAtendente">TIPO AFI</th>
                        <th class="col horarioChegada">HORÁRIO CHEGADA</th>
                        <th class="col prioridade">PRIOR</th>
                        <th class="col atendimento">ATENDIDO</th>
                        <th class="col representante">REPRESENTANTE</th>
                        <th class="col atendente">ATENDENTE</th>
                        <th class="col sala">SALA</th>
                        <th class="col tipoAtendimento">TIPO</th>
                        <th class="col statusAtendimento">STATUS</th>
                        <th class="col">AÇÕES</th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px; color:#000000; text-align: center;" id="tabelaPrincipal">



                </tbody>
            </table>
        </div class="d-flex justify-content-center">

    </div>
    </div>
    </div>


    <script>
        $(document).ready(function() {



            let atendimentos = @json($lista);
            let motivo = @json($motivo);
            var intervalPesq = 0

            $('#status').prop('selectedIndex', -1)

            function ajax() {

                let assist = $('#assist').val()
                let cpf = $('#cpf').val()
                let status = $('#status').val() 
                let dt_ini = $('#dt_ini').val()
                let atendente = $('#idatendente').val()



                $.ajax({
                    type: "GET",
                    url: "/tabela-atendimentos?assist=" + assist + "&cpf=" + cpf + "&status=" + status + "&dt_ini=" + dt_ini + "&atendente=" + atendente,
                    dataType: "json",
                    success: function(response) {
                        atendimentos = response
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            }

            function colunas() {

                $('#numeroAtendimento').prop('checked') ? $('.numeroAtendimento').show() : $('.numeroAtendimento')
                    .hide()
                $('#atendentePreferido').prop('checked') ? $('.atendentePreferido').show() : $(
                    '.atendentePreferido').hide()
                $('#tipoAtendente').prop('checked') ? $('.tipoAtendente').show() : $('.tipoAtendente').hide()
                $('#horarioChegada').prop('checked') ? $('.horarioChegada').show() : $('.horarioChegada').hide()
                $('#prioridade').prop('checked') ? $('.prioridade').show() : $('.prioridade').hide()
                $('#atendimento').prop('checked') ? $('.atendimento').show() : $('.atendimento').hide()
                $('#representante').prop('checked') ? $('.representante').show() : $('.representante').hide()
                $('#atendente').prop('checked') ? $('.atendente').show() : $('.atendente').hide()
                $('#sala').prop('checked') ? $('.sala').show() : $('.sala').hide()
                $('#tipoAtendimento').prop('checked') ? $('.tipoAtendimento').show() : $('.tipoAtendimento').hide()
                $('#statusAtendimento').prop('checked') ? $('.statusAtendimento').show() : $('.statusAtendimento')
                    .hide()

            }

            function linha(atendimento) {
                let formattedDate = 0;
                        if(atendimento.dh_chegada){
                            const date = Date.parse(atendimento.dh_chegada);
                            const formatter = new Intl.DateTimeFormat('pt-BR', {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit'
                            });
                            formattedDate = formatter.format(date);
                        }

                        let ida = atendimento.ida == null ? '' : atendimento.ida
                        let nm_4 = atendimento.nm_4 == null ? ' ' : atendimento.nm_4
                        let tipo = atendimento.tipo == null ? ' ' : atendimento.tipo
                        let dh_chegada = formattedDate
                        let prdesc = atendimento.prdesc == null ? '' : atendimento.prdesc
                        let nm_1 = atendimento.nm_1 == null ? '' : atendimento.nm_1
                        let nm_2 = atendimento.nm_2 == null ? '' : atendimento.nm_2
                        let nm_3 = atendimento.nm_3 == null ? '' : atendimento.nm_3
                        let nr_sala = atendimento.nr_sala == null ? '' : atendimento.nr_sala
                        let sigla = atendimento.sigla == null ? '' : atendimento.sigla
                        let descricao = atendimento.descricao == null ? '' : atendimento.descricao

                $('#tabelaPrincipal').append(
                    (atendimento.status_atendimento == 1 ? '<tr class="table-danger">' : '<tr>') +
                    
                    //Colunas com informações
                    '<td class="numeroAtendimento">' + ida + '</td>' +
                    '<td class="atendentePreferido">' + nm_4 + '</td>' +
                    '<td class="tipoAtendente">' + tipo + '</td>' +
                    '<td class="horarioChegada">' + dh_chegada + '</td>' +
                    '<td class="prioridade">' + prdesc + '</td>' +
                    '<td class="atendimento">' + nm_1 + '</td>' +
                    '<td class="representante">' + nm_2 + '</td>' +
                    '<td class="atendente">' + nm_3 + '</td>' +
                    '<td class="sala">' + nr_sala + '</td>' +
                    '<td class="tipoAtendimento">' + sigla + '</td>' +
                    '<td class="statusAtendimento" >' + descricao + '</td>' +
                    '<td class="">' +

                    //Botões de ação
                    '<a href="/editar-atendimento/' + ida + '" class="tooltips">' +
                    '<span class="tooltiptext">Editar</span>' +
                    '<button type="button" class="btn btn-outline-warning btn-sm">' +
                    '<i class="bi bi-pencil" style="font-size: 1rem; color:#000;"></i>' +
                    '</button>' +
                    '</a>' +

                    '<a href="/visualizar-atendimentos/' + atendimento.idas +
                    '"class="tooltips">' +
                    '<span class="tooltiptext">Visualizar</span>' +
                    '<button type="button" class="btn btn-outline-primary btn-sm">' +
                    '<i class="bi bi-search" style="font-size: 1rem; color:#000;">' +
                    '</i>' +
                    '</button>' +
                    '</a>' +

                    //botão modal cancelar
                    '<button type="button"class="btn btn-outline-danger btn-sm tooltips" data-bs-toggle="modal" data-bs-target="#modal' +
                    ida + '">' +
                    '<span class="tooltiptext">Cancelar</span>' +
                    '<i class="bi bi-x-circle"style="font-size: 1rem; color:#000;">' +
                    '</i>' +
                    '</button>' +

                    '<form action="/cancelar-atendimento/' + ida + '">' +
                    '<div class="modal fade" id="modal' + ida +
                    '" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">' +
                    '<div class="modal-dialog">' +
                    '<div class="modal-content">' +
                    '<div class="modal-header" style="background-color:#DC4C64;color:white">' +
                    '<h1 class="modal-title fs-5" id="exampleModalLabel">Confirmar Cancelamento</h1>' +
                    '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' +
                    '</div>' +
                    '<div class="modal-body">' +

                    '<label for="recipient-name" class="col-form-label" style="font-size:17px">' +
                    'Tem certeza que deseja inativar:' +
                    '<br />' +
                    '<span style="color:#DC4C64; font-weight: bold;">' + nm_1 + '</span>' +
                    '&#63;' +
                    '</label>' +
                    '<br />' +

                    '<center>' +
                    '<div class="mb-2 col-10">' +
                    '<label class="col-form-label">Insira o motivo da ' +
                    '<span style="color:#DC4C64">inativação:</span></label>' +
                    '<select class="form-select" name="motivo" required>' +
                    '<option value="' + motivo[0].id + '">' + motivo[0].descricao +
                    '</option>' +
                    '<option value="' + motivo[1].id + '">' + motivo[1].descricao +
                    '</option>' +
                    '</select>' +
                    '</div>' +
                    '</center>' +
                    '</div>' +
                    '<div class="modal-footer">' +
                    '<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>' +
                    '<button type="submit" class="btn btn-primary">Confirmar</button>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</form>' +
                    '</td>' +
                    '</tr>'
        )
            }

            function tabelas() {
                if ($('.modal').hasClass('show')) { // Não recarrega com a modal aberta

                } else {
                    $('#tabelaPrincipal').html("")
                    $.each(atendimentos, function() {

                        linha(this)
                       
                    })
                }
            }

            function stopPesquisa() {
                clearInterval(intervalPesq)
            }

            tabelas()
            colunas()
            filaEspera()




            $('.coluna').click(function() {
                colunas();
            })
            $('#limpar').click(function() {
                $('#assist').val("")
                $('#cpf').val("")
                $('#idatendente').val("")
                $('#status').prop('selectedIndex', -1)
                $('#dt_ini').val("{{ $data_inicio ?? now()->toDateString() }}")
            })

            $('.pesq').click(function() {
                ajax()
                intervalPesq = window.setInterval(function() {
                    [tabelas(), colunas()]
                }, 500);


            })

            function filaEspera() {
                $.ajax({
                    type: "GET",
                    url: "/pessoas-para-atender-atendimento",
                    dataType: "JSON",
                    success: function(response) {
                        console.log(response)
                        $('#id_pessoas_para_atender').text(response.atender);
                        $('#id_atendentes').text(response.atendentes);
                    },
                    error: function(error) {
                        console.error('Erro ao buscar dados:', error);
                    }
                });
            }

            var intervalId = window.setInterval(function() {
                [ajax(), tabelas(), colunas(), stopPesquisa(), filaEspera()]
            }, 10000);


        })
    </script>

    <script>
        let hoje = @json($now);
        let assistido = @json($assistido);
        let situacao = @json($situacao);

        if (assistido != null || situacao != null) {
            $('#dt_ini').val("")
        }
        $('.pesquisa').change(function() {
            let assis = $('#assist').val()
            let status = $('#status').prop('selectedIndex')

            if (assis == '' && status == 0) {
                $('#dt_ini').val(hoje)

            } else {
                $('#dt_ini').val("")

            }

        })
    </script>
@endsection
