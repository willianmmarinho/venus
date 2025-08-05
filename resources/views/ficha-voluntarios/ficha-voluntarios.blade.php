@extends('layouts.app')
@section('head')
    <title>Editar Ficha Voluntário</title>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <div id="sucesso" class="toast align-items-center text-bg-success border-0 top-30 start-50 translate-middle-x"
        role="alert" aria-live="assertive" aria-atomic="true" style="position: absolute; z-index: 1000">
        <div class="d-flex">
            <div class="toast-body">
                Imagem atualizada com Sucesso!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div>

    <div id="erro" class="toast align-items-center text-bg-danger border-0  top-30 start-50 translate-middle-x"
        role="alert" aria-live="assertive" aria-atomic="true" style="position: absolute; z-index: 1000">
        <div class="d-flex">
            <div class="toast-body">
                Erro inesperado!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div>




    <button type="button" id="foto-voluntario" class="btn btn-warning btn-floating btn-lg" data-bs-toggle="offcanvas"
        data-bs-target="#offcanvasExample" aria-controls="offcanvasExample" style="z-index:100">
        <i class="fa-solid fa-user"></i>
    </button>





    <div class="offcanvas offcanvas-start mt-5" style="border-radius: 0px 30px 30px 0px; width: 300px; height: 490px"
        tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
        <div class="offcanvas-header" style="background-color:#DC4C64;color:white;">
            <h5 class="offcanvas-title" id="offcanvasExampleLabel">Imagem Voluntário</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <center>
                <div id="erroWebcam" class="caixa placeholder-glow" hidden>
                    <i id="webcam" class="bi bi-webcam"></i>
                    <div id="mensagemErro" class="mensagem-erro">Webcam não Encontrada</div>
                </div>

                <canvas id="canvas"></canvas>
                <video id="video" autoplay playsinline style="display: none;" hidden></video>
                <canvas id="canvasPreview" hidden></canvas>

                <hr />

                <button id="atualizar-foto" class="btn btn-warning btn-sm col-10" type="button">
                    Atualizar foto
                </button>
                <button id="cancelar" class="btn btn-danger btn-sm col-5" type="button" hidden>
                    Cancelar
                </button>
                <button id="tirar-foto" class="btn btn-primary btn-sm col-5" type="button" hidden>
                    Tirar Foto
                </button>
                <button id="atualizar-imagem" class="btn btn-warning btn-sm col-5" type="button" hidden>
                    Atualizar Foto
                </button>

            </center>


        </div>

    </div>

    <div class="container"> {{-- Container completo da página  --}}
        <div class="justify-content-center">
            <div class="col-12">
                <br>


                <div class="card">
                    <div class="card-header">
                        DADOS PESSOAIS
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal mt-2" method='POST'
                            action="/atualizar-ficha-voluntario/{{ $edit_associado->ida }}/{{ $edit_associado->idp }}">
                            @csrf
                            <div class="container-fluid">
                                <div class="row g-3 d-flex justify-content-around">
                                    <div class="col-xl-6 col-md-6 col-sm-12">Nome Completo
                                        <input type="text" class="form-control" name="nome_completo" maxlength="45"
                                            oninput="this.value = this.value.replace(/[0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                            value="{{ $edit_associado->nome_completo }}" disabled>
                                    </div>
                                    <div class="col-xl-2 col-md-3 col-sm-12">CPF
                                        <input type="text" class="form-control" id="cpf" name="cpf"
                                            maxlength="11" value="{{ $edit_associado->cpf }}" disabled>
                                    </div>
                                    <div class="col-xl-2 col-md-3 col-sm-12">
                                        <label for="2">N.º Associado</label>
                                        <input type="text" class="form-control" id="nrassociado" name="nrassociado"
                                            maxlength="11" value="{{ $edit_associado->nr_associado }}" disabled>
                                    </div>
                                    <div class="col-lg-2 col-md-4 col-sm-12">Identidade
                                        <input type="text" class="form-control" name="idt" maxlength="9"
                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                            value="{{ $edit_associado->idt }}" required>
                                    </div>
                                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-12">Data de Nascimento
                                        <input type="date" class="form-control" name="dt_nascimento" id="3"
                                            value="{{ $edit_associado->dt_nascimento }}" required="required">
                                    </div>
                                    <div class="col-lg-2 col-md-4 col-sm-12">Sexo
                                        <select id="sexo" class="form-select" name="sexo" required>
                                            @foreach ($tpsexo as $tpsexos)
                                                <option value="{{ $tpsexos->id }}"
                                                    {{ $edit_associado->id_sexo == $tpsexos->id ? 'selected' : null }}>
                                                    {{ $tpsexos->tipo }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-xl-1 col-lg-2 col-md-4 col-sm-12">
                                        <label for="3">DDD</label>
                                        <select id="ddd" class="form-select" name="ddd">
                                            </option>
                                            @foreach ($tpddd as $tpddds)
                                                <option value="{{ $tpddds->id }}"
                                                    {{ $tpddds->id == $edit_associado->ddd ? 'selected' : null }}>
                                                    {{ $tpddds->descricao }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-xl-2 col-lg-3 col-md-8 col-sm-12">
                                        <label for="2">Celular</label>
                                        <input type="text" class="form-control" id="2" maxlength="9"
                                            name="telefone" value="{{ $edit_associado->celular }}" required>
                                    </div>
                                    <div class="col-md-12 col-xl-5">
                                        <label for="2">Email</label>
                                        <input type="text" class="form-control" id="2" maxlength="100"
                                            name="email" value="{{ $edit_associado->email }}" required>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
        <br>

        <div class="card">
            <div class="card-header">
                DADOS RESIDENCIAIS
            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <div class="row g-3 d-flex justify-content-around">
                        <div class="col-xl-2 col-md-3 col-sm-12">CEP
                            <div class=" input-group has-validation">
                                <input type="text" class="form-control" id="cep" name="cep" maxlength="8"
                                    value="{{ $edit_associado->cep }}" required>
                                <div class="invalid-tooltip">
                                    CEP Inválido!
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-1 col-md-2 col-sm-12">UF
                            <select class="form-select" id="uf2" name="uf_end">
                                @foreach ($tpufidt as $tp_ufes)
                                    <option value="{{ $tp_ufes->id }}"
                                        {{ $tp_ufes->id == $edit_associado->id_uf ? 'selected' : null }}>
                                        {{ $tp_ufes->sigla }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-4 col-md-7 col-sm-12">Cidade
                            <select class="form-select" id="cidade2" name="cidade">

                            </select>
                        </div>
                        <div class="col-xl-5 col-lg-6 col-md-12">Logradouro
                            <input type="text" class="form-control" id="logradouro" name="logradouro" maxlength="50"
                                value="{{ $edit_associado->logradouro }}" required>
                        </div>

                        <div class="col-xl-4 col-lg-6 col--12">Complemento
                            <input type="text" class="form-control" id="complemento" name="complemento"
                                maxlength="50" value="{{ $edit_associado->complemento }}" required>
                        </div>
                        <div class="col-xl-4 col-md-4 col-sm-12">Número
                            <input type="text" class="form-control" id="numero" name="numero" maxlength="10"
                                value="{{ $edit_associado->numero }}" required>
                        </div>
                        <div class="col-xl-4 col-md-8 col-sm-12">Bairro
                            <input type="text" class="form-control" id="bairro" name="bairro" maxlength="50"
                                value="{{ $edit_associado->bairro }}" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-5 mt-5">
            <a class="btn btn-danger col-md-3 col-sm-12 col-2 mt-3 offset-md-2" href="/gerenciar-pessoas"
                class="btn btn-danger">Cancelar</a>
            <button type="submit" class="btn btn-primary col-md-3 col-sm-12 col-2 mt-3 offset-md-2">Confirmar</button>
            </form>
        </div>
    </div>











    <style>
        #foto-voluntario {
            position: fixed;
            bottom: 50px;
            right: 20px;
        }

        #webcam {
            z-index: 100;
            font-size: 50px;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #333;
            position: absolute;
            overflow: hidden;
        }

        #mensagemErro {
            z-index: 100;
            font-size: 16px;
            top: 60%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #333;
            position: absolute;
            font-family: sans-serif;
        }

        .caixa {
            width: 240px;
            height: 320px;
            background-color: #e0e0e0;
            border-radius: 6px;
            position: relative;
            overflow: hidden;
            font-family: sans-serif;
        }

        .placeholder-glow::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            height: 100%;
            width: 100%;
            background: linear-gradient(90deg,
                    rgba(224, 224, 224, 0) 0%,
                    rgba(255, 255, 255, 0.6) 50%,
                    rgba(224, 224, 224, 0) 100%);
            animation: shimmer 1.5s infinite;
        }

        @keyframes shimmer {
            0% {
                left: -100%;
            }

            100% {
                left: 100%;
            }
        }
    </style>


    <script>
        $(document).ready(function() {

            $('#cpf').val($('#cpf').val().replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, '***.$2.$3-**'));
            let cep = $('#cep').val().replace(/\D/g, '');
            if (cep.length === 8) {
                populateCities($('#cidade2'), @JSON($edit_associado->id_uf), @JSON($edit_associado->nat))
            }


            if (!@JSON($edit_associado->id_sexo)) $('#sexo').prop('selectedIndex', -1)
            if (!@JSON($edit_associado->ddd)) $('#ddd').prop('selectedIndex', -1)



            function populateCities(selectElement, uf, cidadeNome) {
                $.ajax({
                    type: "GET",
                    url: "/retorna-cidades/" + uf,
                    dataType: "JSON",
                    success: function(response) {
                        selectElement.empty();
                        selectElement.removeAttr('disabled');

                        let cidadeSelecionada = null;

                        $.each(response, function(indexInArray, item) {
                            selectElement.append('<option value="' + item.id_cidade + '">' +
                                item.descricao + '</option>');

                            // Verifica se o nome da cidade retornado pelo ViaCEP é igual ao da lista
                            if (item.descricao.toLowerCase() == cidadeNome.normalize("NFD")
                                .replace(/[\u0300-\u036f]/g, "").toLowerCase()) {
                                cidadeSelecionada = item.id_cidade;
                            }
                        });

                        // Se encontramos a cidade pelo nome, selecionamos ela
                        if (cidadeSelecionada) {
                            selectElement.val(cidadeSelecionada).trigger('change');
                        }
                    }
                });
            }

            function retornaCEP(cep) {
                let estados = @JSON($tpufidt);

                $.ajax({
                    type: "GET",
                    url: 'https://viacep.com.br/ws/' + cep + '/json/',
                    dataType: "json",
                    success: function(response) {
                        console.log(response);

                        if (response.erro) {
                            $('#cep').addClass('is-invalid')
                            return;
                        }


                        // Preenchendo os campos automaticamente
                        $('#logradouro').val(response.logradouro);
                        $('#bairro').val(response.bairro);
                        $('#complemento').val(response.complemento);
                        $('#numero').val('');

                        // Encontrando o estado correspondente
                        let estadoEncontrado = estados.find(estado => estado.sigla ===
                            response.uf);

                        if (estadoEncontrado) {
                            $('#uf2').val(estadoEncontrado.id).trigger('change');

                            // Buscar cidades automaticamente e selecionar pelo nome
                            populateCities($('#cidade2'), estadoEncontrado.id, response.localidade);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Erro ao buscar o CEP:", error);
                    }
                });
            }

            $('#cep').on('input', function() {
                let cep = $(this).val().replace(/\D/g, '');

                if (cep.length === 8) {
                    retornaCEP(cep)
                }
            });
        });
    </script>
    <script>
        const video = document.getElementById('video'); // oculto
        const canvas = document.getElementById('canvas'); // foto final
        const preview = document.getElementById('canvasPreview'); // visualização ao vivo
        const context = canvas.getContext('2d');
        const previewCtx = preview.getContext('2d');
        const id = {{ $edit_associado->ida }};
        let myStream, intervalo
        let contador = 0;
        let stop = 0;

        // Define resoluções
        video.width = 640;
        video.height = 480;
        canvas.width = 240;
        canvas.height = 320;
        preview.width = 240;
        preview.height = 320;


        function iniciarCamera() {
            // Inicia a câmera
            navigator.mediaDevices.getUserMedia({
                    video: {
                        width: 640,
                        height: 480
                    }
                })
                .then(stream => {
                    video.srcObject = stream;
                    video.play();
                    myStream = stream;

                    $('#erroWebcam').prop('hidden', true)
                    $('#video').prop('hidden', false)
                    $('#canvasPreview').prop('hidden', false)

                    // Limpa o intervalo de tentativa, se estava ativo
                    if (intervalo) {
                        clearInterval(intervalo);
                        intervalo = null;
                        contador = 0;
                    }
                    requestAnimationFrame(atualizarPreview);

                })
                .catch(err => {
                    console.error("Erro ao acessar a câmera:", err);

                    $('#erroWebcam').prop('hidden', false)
                    $('#video').prop('hidden', true)
                    $('#canvasPreview').prop('hidden', true)

                    console.log(stop)
                    console.log(intervalo)

                    // Inicia o loop de tentativas apenas se ainda não estiver em execução
                    if (!intervalo) {
                        intervalo = setInterval(() => {
                            if (!stop) {

                                contador++;
                                console.log(`Tentando novamente... (${contador})`);
                                iniciarCamera();

                                if (contador >= 30) {
                                    clearInterval(intervalo);
                                    intervalo = null;
                                    contador = 0;
                                    $('#cancelar').click();
                                }
                            } else {
                                clearInterval(intervalo);
                                intervalo = null;
                                contador = 0;

                            }
                        }, 1000);
                    }
                });
        }

        // Atualiza o canvasPreview em tempo real com corte 3x4
        function atualizarPreview() {
            // Define área de crop central (360x480 = 3:4)
            const cropWidth = 360;
            const cropHeight = 480;
            const cropX = (640 - cropWidth) / 2;
            const cropY = 0;

            // Desenha o corte no preview
            previewCtx.clearRect(0, 0, preview.width, preview.height);
            previewCtx.drawImage(
                video,
                cropX, cropY, cropWidth, cropHeight,
                0, 0, preview.width, preview.height
            );

            if (stop) {

                video.pause()
                video.srcObject = null;
                var tracks = myStream.getTracks();
                tracks.forEach(function(track) {
                    track.stop();
                });

                return
            }
            requestAnimationFrame(atualizarPreview); // loop contínuo
        }

        // Captura a foto cortada
        function tirarFoto() {
            const cropWidth = 360;
            const cropHeight = 480;
            const cropX = (640 - cropWidth) / 2;
            const cropY = 0;

            context.clearRect(0, 0, canvas.width, canvas.height);
            context.drawImage(
                video,
                cropX, cropY, cropWidth, cropHeight,
                0, 0, canvas.width, canvas.height
            );
        }


        function enviarImagem() {
            const imagemBase64 = canvas.toDataURL('image/png');
            fetch('/salvar-foto', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        imagem: imagemBase64,
                        associado: id
                    })
                })
                .then(async response => {
                    const text = await response.text(); // lê como texto
                    console.log('Resposta bruta do servidor:', text);

                    try {
                        const json = JSON.parse(text); // tenta transformar em JSON
                        $('#sucesso').toast('show');
                    } catch (e) {
                        console.error('Resposta não é JSON:', e);
                        $('#erro').toast('show');
                    }
                })
                .catch(error => {
                    console.error('Erro ao enviar imagem:', error);
                    $('#erro').toast('show');
                });
        }

        function buscaImagem() {
            $.ajax({
                url: '/retorna-foto?associado=' + id, // Rota que busca a imagem aleatória
                method: 'GET',
                success: function(response) {
                    // Cria nova imagem com a resposta base64
                    let img = new Image();
                    img.onload = function() {
                        const canvas = document.getElementById('canvas');
                        const ctx = canvas.getContext('2d');
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                    };
                    img.src = 'data:image/jpeg;base64,' + response.base64; // ou image/png se for o caso
                },
                error: function(xhr) {
                    console.error('Erro ao buscar imagem:', xhr.responseText);
                }
            });
        }


        $('#atualizar-foto').click(function() {

            stop = 0;
            iniciarCamera();

            $('#video').prop('hidden', false)
            $('#canvasPreview').prop('hidden', false)
            $('#canvas').prop('hidden', true)

            $('#atualizar-foto').prop('hidden', true)
            $('#cancelar').prop('hidden', false)
            $('#tirar-foto').prop('hidden', false)

        })

        $('#cancelar').click(function() {

            stop = 1;

            buscaImagem();

            $('#erroWebcam').prop('hidden', true)
            $('#video').prop('hidden', true)
            $('#canvasPreview').prop('hidden', true)
            $('#canvas').prop('hidden', false)

            $('#atualizar-foto').prop('hidden', false)
            $('#cancelar').prop('hidden', true)
            $('#tirar-foto').prop('hidden', true)
            $('#atualizar-imagem').prop('hidden', true)

        })

        $('#tirar-foto').click(function() {

            tirarFoto();
            stop = 1;

            $('#video').prop('hidden', true)
            $('#canvasPreview').prop('hidden', true)
            $('#canvas').prop('hidden', false)

            $('#tirar-foto').prop('hidden', true)
            $('#atualizar-imagem').prop('hidden', false)
        })

        $('#atualizar-imagem').click(function() {

            enviarImagem()

            $('#video').prop('hidden', true)
            $('#canvasPreview').prop('hidden', true)
            $('#canvas').prop('hidden', false)

            $('#atualizar-foto').prop('hidden', false)
            $('#cancelar').prop('hidden', true)
            $('#tirar-foto').prop('hidden', true)
            $('#atualizar-imagem').prop('hidden', true)


        })

        buscaImagem()
    </script>
@endsection
