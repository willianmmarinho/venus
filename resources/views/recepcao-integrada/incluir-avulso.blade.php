@extends('layouts.app')

@section('title')
    Incluir Avulso
@endsection

@section('content')
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <form class="form-horizontal" method="post" action="/armazenar-avulso">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    INCLUIR ATENDIMENTO DE EMERGÊNCIA
                                </div>
                            </div>
                        </div>
                        <div class="card-body ">

                            <div class="mt-3 row">


                                <div class="col-12 mb-3">
                                    <span>
                                        Buscar Pessoa
                                        <span class="tooltips">
                                            <span class="tooltiptext">Obrigatório</span>
                                            <span style="color:red">*</span>
                                        </span>
                                    </span>
                                    <div class="input-group mt-1">
                                        <input type="text" class="form-control assistido" placeholder="Nome..."
                                            aria-label="Recipient's username" aria-describedby="button-addon2"
                                            id="nomeAssistido" maxlength="100">
                                        <input type="text" class="form-control assistido" placeholder="CPF..."
                                            aria-label="Recipient's username" aria-describedby="button-addon2"
                                            id="cpfAssistido" maxlength="14">
                                        <button class="btn btn-outline-primary" type="button" id="bNomeAssistido">
                                            Buscar <i class="bi bi-search"></i>
                                        </button>
                                    </div>

                                    <label id="labelNumeroNomeAssistido" style="font-size: 14px; color:red" hidden>
                                        *Número insuficiente de caracteres.
                                    </label>
                                    <label id="labelNomeAssistido" style="font-size: 14px; color:red" hidden>
                                        *Nenhuma pessoa encontrada.
                                    </label>
                                </div>

                                <div class="mb-3 col-3">
                                    Motivo
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <select class="form-select" aria-label="Default select example" name="motivo" required>
                                        @foreach ($motivo as $motivos)
                                            <option value="{{ $motivos->id }}">{{ $motivos->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-9">Atendido
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <select class="form-select" id="assist" name="assist" required="required">
                                    </select>
                                </div>


                                <div class=" mb-3 col-3">
                                    Número de Acompanhantes
                                    <input type="number" class="form-control" name="acompanhantes" placeholder="0">
                                </div>
                                <div class=" mb-3 col-9">
                                    Reunião Mediúnica
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <select class="form-select select2" aria-label="Default select example" name="reuniao"
                                        required>
                                        @foreach ($reuniao as $reunioes)
                                            <option value="{{ $reunioes->id }}">{{ $reunioes->nome }} -
                                                {{ $reunioes->nomedia }} -
                                                {{ date('H:i', strtotime($reunioes->h_inicio)) }}/{{ date('H:i', strtotime($reunioes->h_fim)) }}
                                                - Sala {{ $reunioes->sala }}</option>
                                        @endforeach
                                    </select>
                                </div>


                            </div>


                        </div>
                        <div class="row mb-3">
                            <div class="d-grid gap-1 col-4 mx-auto">
                                <a class="btn btn-danger" href="/gerenciar-tratamentos" role="button">Cancelar</a>
                            </div>
                            <div class="d-grid gap-2 col-4 mx-auto">
                                <button type="submit" class="btn btn-primary" style="color:#fff;">Confirmar</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#assist').prop('selectedIndex', -1);


            function ajaxAssistido() {
                var nome = $('#nomeAssistido').val();
                var cpf = $('#cpfAssistido').val().replace(/\D/g, '');

                $('#nomeAssistido').removeClass('is-invalid');
                $('#cpfAssistido').removeClass('is-invalid');
                $('#labelNumeroNomeAssistido').prop('hidden', true);
                $('#labelNomeAssistido').prop('hidden', true);
                $('#assist').html('');
                $('#assist').prop('selectedIndex', -1);

                if (nome.length < 1 && cpf.length < 1) {
                    $('#nomeAssistido').addClass('is-invalid');
                    $('#cpfAssistido').addClass('is-invalid');
                    $('#labelNumeroNomeAssistido').prop('hidden', false);
                } else {
                    $.ajax({
                        type: "GET",
                        url: "/ajax-avulso?nome=" + nome + '&cpf=' + cpf,
                        dataType: "json",
                        success: function(response) {
                            console.log(response);
                            if (response.length === 0) {
                                $('#nomeAssistido').addClass('is-invalid');
                                $('#labelNomeAssistido').prop('hidden', false);
                            } else {
                                $.each(response, function() {
                                    $('#assist').append('<option value="' + this.id + '">' +
                                        this.nome_completo + '</option>');
                                });
                            }
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText);
                            $('#NomeAssistido').addClass('is-invalid');
                            $('#labelNomeAssistido').prop('hidden', false);
                        }
                    });
                }
            }


            // Clicar no Botão Buscar
            $('#bNomeAssistido').click(function() {
                ajaxAssistido();
            });

            // Modificar o campo
            $('.assistido').change(function() {
                ajaxAssistido();
            });

            // Mascara de CPF do Assistido
            $('#cpfAssistido').on('input', function() {

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


            })

            // Apertar enter ao digitar no input de representante, previne o submit do formulário
            $("#cpfResponsavel").keydown(function(event) {
                if (event.key === "Enter") {
                    event.preventDefault(); // Prevent form submission
                    ajaxResponsavel();
                }
            });


        });
    </script>
@endsection
