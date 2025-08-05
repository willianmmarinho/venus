@extends('auth.app')

@section('title') Vênus @endsection

@section('content')
    <div class="account-pages my-4 pt-sm-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        <div class="card-body pt-0">
                            <h3 class="text-center mt-4">
                                <a href="/" class="logo logo-admin"><img src="{{ URL::asset('/images/logo150px.ico') }}"
                                        height="100" alt="logo"></a>
                            </h3>
                            <div class="p-3">
                                <form  id="form" name="form" class="form-horizontal mt-4" method="POST" action="/login/home" >
                                    @csrf
                                    <div class="form-group">
                                        <label for="username">CPF</label>
                                        <!--<input id="cpf" type="numeric" class="form-control @error('cpf') is-invalid @enderror" name="cpf" value="{{ old('cpf') }}" required autocomplete="cpf" autofocus placeholder="">-->
                                        <input id="cpf" type="text" maxlength="14"
                                            class="form-control mascara_cpf  @if ($errors->first() == 'cpf' or $errors->first() == 'user') is-invalid @endif"
                                            name="cpf" placeholder="Ex.: 00000000000" value="{{ old('cpf') }}" required>

                                        @if ($errors->any())
                                            @switch($errors->first())
                                                @case('cpf')
                                                    <label style="font-size: 14px; color:red">
                                                        *CPF inválido.
                                                    </label>
                                                @break

                                                @case('user')
                                                    <label style="font-size: 14px; color:red">
                                                        *Usuário não cadastrado no sistema.
                                                    </label>
                                                @break

                                                @default
                                            @endswitch
                                        @endif

                                        <br>

                                    </div>
                                    <label for="userpassword">Senha</label>
                                    <div class="input-group">
                                        <input id="senha" type="password"
                                            class="form-control @if ($errors->first() == 'psswd') is-invalid @endif testee"
                                            name="senha" required autocomplete="current-password" placeholder="">

                                        <button class="btn btn-primary" type="button" id="buttonEye"><i
                                                class="bi bi-eye"></i></button>
                                    </div>
                                    <div class="form-group row">
                                        @if ($errors->any())
                                            @if ($errors->first() == 'psswd')
                                                <label style="font-size: 14px; color:red">
                                                    *Senha incorreta.
                                            @endif
                                        @endif
                                    </div>
                                    <br>
                                    <div class="form-group row mt-4">
                                        <div class="col-6">
                                            <!-- <div class="custom-control custom-checkbox">
                                                                    <input type="checkbox" class="custom-control-input" name="remember" id="customControlInline" {{ old('remember') ? 'checked' : '' }}>
                                                            <label class="custom-control-label" for="customControlInline">{{ __('Remember Me') }}</label>
                                                                </div> -->
                                        </div>
                                        <div class="col-12 d-grid gap-2" style="text-align:center;">
                                            <button class="btn btn-primary " type="submit" id="submit">Entrar</button>
                                        </div>
                                        <!--<div class="form-group mb-0 row">
                                                                <div class="col-12 mt-3">
                                                                    <a href="#" class="text-danger" type="button"><i class="mdi mdi-lock"></i>Esqueci minha senha</a>
                                                                </div>
                                                            </div>-->
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 text-center">

                        <script>
                            document.getElementById('buttonEye').addEventListener('click', function() {
                                const passwordInput = document.getElementById('senha');
                                const eyeIcon = this.querySelector('i');

                                if (passwordInput.type === 'password') {
                                    passwordInput.type = 'text';
                                    eyeIcon.classList.remove('bi-eye');
                                    eyeIcon.classList.add('bi-eye-slash');
                                } else {
                                    passwordInput.type = 'password';
                                    eyeIcon.classList.remove('bi-eye-slash');
                                    eyeIcon.classList.add('bi-eye');
                                }
                            });
                        </script>
                        {{-- <p>Don't have an account ? <a href="/register" class="text-primary"> Signup Now </a></p> --> --}}
                        {{-- <p>© {{  date('Y', strtotime('-2 year')) }} - {{  date('Y') }} Comunhão Espírita de Brasília <i class="mdi mdi-heart text-danger"></i></p> --> --}}
                        <p>© Comunhão Espírita de Brasília <i class="fa-solid fa-heart" style="color: #74C0FC;"></i></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
     {{-- Necessário para o jQuery --}}
    <script src="{{ asset('js/jquery.js') }}"></script>
    <script>
        $(document).ready(function() {

            let novoConteudo
            $('#cpf').on('input', function() {

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

            // ---- Parte de Formulário ---- //
            $('#submit').click(function(event) {
             //   event.preventDefault(); // Previne que o formulário seja enviado de uma vez
                novoConteudo = $('#cpf').val().replace(/\D/g, ''); // Remove a formatação do texto
                $('#cpf').val(novoConteudo);
            })

        });
    </script>

@stop
