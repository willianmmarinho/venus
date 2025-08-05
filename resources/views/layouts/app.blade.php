<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Ícone do sistema e títulos --}}
    <link rel="icon" href="{{ URL::asset('/images/Venus2.png') }}" type="image/icon type">
    <title>@yield('title')</title>



    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js'])



    {{--
 * Este regiao abaixo é dedicada aos imports do sistema
 * Ela foi criada para substituir os Links CDN e só existe devido à um bug em Node_Modules
 * Para encontrar esses arquivos vá em Public/js
 --}}

    {{-- CDN Jquery --}}
    {{-- <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script> --}}

    {{-- Import Jquery --}}
    <script src="{{ asset('js/jquery.js') }}"></script>

    {{-- Import Bootstrap, colocado dessa maneira devido a um Bug de comando $.modal() --}}
    <script src="{{ asset('js/bootstrap/bootstrap.bundle.js') }}"></script>

    {{-- Import CSS Tooltips --}}
    <link href="{{ asset('css/tooltips.css') }}" rel="stylesheet">

    {{-- Import Dos Gráficos --}}
    <script src="{{ asset('js/chart.js') }}"></script>

    {{-- Import do Calendário --}}
    <script src="{{ asset('js/fullcalendar.js') }}"></script>

    {{-- Import do Bootstrap necessário para o Calendário --}}
    <script src="{{ asset('js/bootstrap5/index.global.min.js') }}"></script>


    <link rel="stylesheet" href="{{ asset('css/font/bootstrap-icons.css') }}">

    <!-- Outros links de head aqui -->

    <!-- Font Awesome manualmente -->
    <link rel="stylesheet" href="{{ asset('fontsawesome/css/all.min.css') }}">










</head>

<body>

    @include('layouts/sidebar')
    @yield('content')

    <div class="modal fade" id="modalLogin" data-bs-keyboard="false" tabindex="-1" aria-labelledby="inativarLabel"
        aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#DC4C64;color:white">
                    <h1 class="modal-title fs-5" id="inativarLabel">Alerta de inatividade</h1>
                </div>
                <br />
                <div class="modal-body">
                    <center>
                        <span>Atenção, clique em ESTOU AQUI! para não ser deslogado</span><br />
                        <span id="tempoLogout"style="color:#DC4C64; font-weight: bold;"></span>
                    </center>
                </div>
                <div class="modal-footer">
                    <button type="button" id="confirm" class="btn btn-primary" data-bs-dismiss="modal">Estou
                        Aqui!</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        html,
        body {}
    </style>

    <script>
        let session
        let redirect

        function toast() {
            $('.modal').modal('hide');
            $('#modalLogin').modal('show');
        }

        function checkSession() {
            $.ajax({
                type: "GET",
                url: "/usuario/sessao",
                dataType: "json",
                success: function(response) {

                    session = response

                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }


        function time() {
            checkSession()
            total -= 1
            $('#tempoLogout').html(total + 's')
            if (total < 1 && redirect == 0) {
                if (session == 0) {
                    window.location.replace("/login/valida")
                } else {
                    document.getElementById('logout-form').submit();
                    redirect = 1
                }

            }
        }

        function setTime() {
            total = 60
            redirect = 0
            $('#tempoLogout').html(total + 's')
            var intervalIdTime = window.setInterval(function() {
                [time()]
            }, 1000);





            $('#confirm').click(function() {
                clearInterval(intervalIdTime)
            })

        }

        var intervalId = window.setInterval(function() {
            [toast(), setTime()]
        }, 3600000);
    </script>
    <script>
        $("body").on("submit", "form", function() {
            $(this).submit(function() {
                return false;
            });
            $(':submit').html(
                '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Carregando...')
            return true;
        });
    </script>


</html>
