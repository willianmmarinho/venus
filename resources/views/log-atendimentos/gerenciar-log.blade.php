@extends('layouts.app')
@section('title', 'Gerenciar Log de Atendimentos')
@section('content')

    <div class="container" id="a">

        <br />
        <br />

        <button type="button" class="btn btn-danger btn-floating btn-lg" id="btn-back-to-top">
            <i class="bi bi-arrow-up"></i>
        </button>

        <div class="row">


            {{-- Início da zona de Pesquisa --}}
            <div class="col-10">

                <div class="input-group mb-3">

                    {{-- Inicio do Dropdown de Seleção de tipo de Pesquisa --}}
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false" id="btnDrop"></button>
                    <ul class="dropdown-menu">
                        <li><button class="dropdown-item drop" value="2" tipo="1">Id de Referência</button></li>
                        <li><button class="dropdown-item drop" value="3" tipo="1">Id do Assistido</button></li>
                        <li><button class="dropdown-item drop" value="4" tipo="3">CPF do Assistido</button></li>
                        <li><button class="dropdown-item drop" value="5" tipo="2">Nome do Assistido</button></li>
                        <li><button class="dropdown-item drop" value="6" tipo="1">Id do Usuário</button></li>
                        <li><button class="dropdown-item drop" value="7" tipo="3">CPF do Usuário</button></li>
                        <li><button class="dropdown-item drop" value="8" tipo="2">Nome do Usuário</button></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><button class="dropdown-item drop" value="1">Todos</button></li>
                    </ul>
                    {{-- Fim do Dropdown de Seleção de Tipo de Pesquisa --}}


                    <input id="inputGeral" type="text" class="form-control" maxlength="100">{{-- Input de Pesquisa --}}


                    <button id="btnClear" class="btn btn-outline-secondary">Limpar</button>
                    <button class="btn btn-secondary btnPesquisa">Pesquisar</button>
                    <div class="invalid-feedback">
                        É necessário pesquisar algo para iniciar a pesquisa!
                    </div>
                </div>

            </div>
            <div class="col-2">
                <button class="btn btn-secondary col-12" data-bs-toggle="modal" data-bs-target="#modalFiltro">Filtro <i
                        class="fa-solid fa-filter"></i></button>
                {{-- Botão para Modal de Filtros --}}

                <div class="modal fade" id="modalFiltro" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color:#5a5858;color:white">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Definições de Pesquisa</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">

                                <center>
                                    <div class="col-10">
                                        Tipo de Origem
                                        <select class="form-select mb-3" id="id_origem" name="id_origem" type="number">
                                            @foreach ($origens as $origem)
                                                <option value="{{ $origem->id }}"> {{ $origem->descricao }} </option>
                                            @endforeach
                                            <option value="0" selected>Todos</option>
                                        </select>

                                        Tipo de Ação
                                        <select class="form-select mb-3" id="id_acao" name="id_acao" type="number">
                                            @foreach ($acoes as $acao)
                                                <option value="{{ $acao->id }}"> {{ $acao->descricao }} </option>
                                            @endforeach
                                            <option value="0" selected>Todos</option>
                                        </select>

                                        Id de Observação
                                        <input id="inputObs" type="text" class="form-control mb-3">

                                        Data de Início
                                        <input id="dt_inicio" type="date" class="form-control mb-3">

                                        Data de Fim
                                        <input id="dt_fim" type="date" class="form-control mb-3">
                                    </div>
                                </center>
                                <div class="modal-footer">
                                    <button id="btnLimparModal"type="button" class="btn btn-secondary">Limpar</button>
                                    <button class="btn btn-primary btnPesquisa" data-bs-dismiss="modal">Pesquisar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>




        <div class="card">
            <div class="card-header">
                GERENCIAR LOG DE ATENDIMENTOS
            </div>
            <div class="card-body">

                <div id="placeholder" hidden>
                </div>
                <div id="tabela" hidden>
                </div>

            </div>
        </div>











    </div>
    <br />
    <br />
    <br />

    <script>
        $(document).ready(function() {

            // Carregados no Inicio da Página
            let tipo = 0;
            let value = 1;
            $('#btnDrop').html('Todos')
            $('#inputGeral').attr("placeholder", "Digite um valor padrão para a Pesquisa Global...")



            function ajaxPesquisa() {

                let placeholderTimeout;
                const realTableLoad = $.Deferred();
                const regexTemLetra = /[A-Za-zÀ-ÿ]/;

                let pesquisaGeral = $('#inputGeral').val()
                let id_origem = $('#id_origem option:selected').val()
                let id_acao = $('#id_acao option:selected').val()
                let inputObs = $('#inputObs').val()
                let dt_inicio = $('#dt_inicio').val()
                let dt_fim = $('#dt_fim').val()


                if ((value == 4 || value == 7 || value == 1) && !regexTemLetra.test(pesquisaGeral)) {
                    pesquisaGeral = pesquisaGeral.replace(/\D/g, '')
                } else if (value == 5 || value == 8 || value == 1) {
                    pesquisaGeral = pesquisaGeral.replace(/ /g, "+")
                }

                if (pesquisaGeral === '') {
                    $('#inputGeral').addClass('is-invalid')
                } else {
                    $('#tabela').load('/tabela-log-atendimentos?' +
                        'pesquisaGeral=' + pesquisaGeral +
                        '&value=' + value +
                        '&id_origem=' + id_origem +
                        '&id_acao=' + id_acao +
                        '&inputObs=' + inputObs +
                        '&dt_inicio=' + dt_inicio +
                        '&dt_fim=' + dt_fim,
                        function() {
                            realTableLoad.resolve()
                        })


                    $('#tabela').prop('hidden', true)
                    placeholderTimeout = setTimeout(function() {
                        $('#placeholder').load('/placeholder-log-atendimentos')
                        $('#placeholder').prop('hidden', false)
                    }, 200);

                    realTableLoad.done(function() {
                        clearTimeout(placeholderTimeout); // Cancel placeholder if it's pending
                        $('#placeholder').prop('hidden', true)
                        $('#tabela').prop('hidden', false)
                    });
                }



            }

            // DropDown de Seleção de Tipo de Pesquisa
            $('.drop').click(function() {
                tipo = $(this).attr("tipo")
                value = $(this).attr("value")


                $('#btnDrop').html(this.innerText)
                $('#inputSelect').val($(this).attr("value"))
                $('#inputGeral').val('')
                $('#inputGeral').removeClass('is-invalid')

                if ($(this).attr("value") == 1) {
                    $('#inputGeral').attr("placeholder", "Digite um valor padrão para a Pesquisa Global...")
                } else {
                    $('#inputGeral').attr("placeholder", "Digite o " + this.innerText + "...")
                }

            })

            // Validação de Apenas Números nas Pesquisas de ID
            $('#inputGeral').on('input', function() {

                if (tipo == 1) {
                    $(this).attr('maxlength', 9);
                    novoConteudo = $(this).val().replace(/\D/g, '')
                } else if (tipo == 3) {
                    $(this).attr('maxlength', 14);

                    // ---- Validação de letras ---- //
                    novoConteudo = $(this).val().replace(/\D/g, '')
                    $(this).val(novoConteudo)

                    let numeros = ($(this).val().match(/\d/g) || [])
                        .length; // Conta a quantidade de números no input

                    // ---- Máscara de CPF ---- //

                    if (numeros > 3) {
                        novoConteudo = $(this).val().slice(0, 3) + '.' + novoConteudo.slice(
                            3, ) // Separa os números e adiciona um ponto
                        $(this).val(novoConteudo)
                    }
                    if (numeros > 6) {
                        novoConteudo = $(this).val().slice(0, 7) + '.' + novoConteudo.slice(
                            7, ) // Separa os números e adiciona um ponto
                        $(this).val(novoConteudo)
                    }
                    if (numeros > 9) {
                        novoConteudo = $(this).val().slice(0, 11) + '-' + novoConteudo.slice(
                            11, ) // Separa os números e adiciona um hífen
                        $(this).val(novoConteudo)
                    }

                } else {
                    $(this).attr('maxlength', 100);
                    novoConteudo = $(this).val()
                }


                $('#inputGeral').removeClass('is-invalid')
                $(this).val(novoConteudo)
            })

            // Validação de Apenas Números da Pesquisa de Obs
            $('#inputObs').on('input', function() {
                novoConteudo = $(this).val().replace(/\D/g, '')
                $(this).val(novoConteudo)
            })

            // Botão de Limpar
            $('#btnClear').click(function() {
                $('#inputGeral').val('')
                $('#inputGeral').removeClass('is-invalid')
            })

            // Botão de Limpar da Modal
            $('#btnLimparModal').click(function() {
                $('#id_origem option[value="0"]').prop('selected', true)
                $('#id_acao option[value="0"]').prop('selected', true)
                $('#inputObs').val('')
                $('#dt_inicio').val('')
                $('#dt_fim').val('')
            })

            // Pesquisa com Enter
            $("#inputGeral").keydown(function(event) {
                if (event.key === "Enter") {
                    event.preventDefault(); // Prevent form submission
                    ajaxPesquisa()
                }
            });

            // Botão de Pesquisar
            $('.btnPesquisa').click(function() {
                ajaxPesquisa()
            })




        });
    </script>


    <style>
        #btn-back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: none;
        }
    </style>
    <script>
        //Get the button
        let mybutton = document.getElementById("btn-back-to-top");

        // When the user scrolls down 20px from the top of the document, show the button
        window.onscroll = function() {
            scrollFunction();
        };

        function scrollFunction() {
            if (
                document.body.scrollTop > 20 ||
                document.documentElement.scrollTop > 20
            ) {
                mybutton.style.display = "block";
            } else {
                mybutton.style.display = "none";
            }
        }
        // When the user clicks on the button, scroll to the top of the document
        mybutton.addEventListener("click", backToTop);

        function backToTop() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        }
    </script>

@endsection
