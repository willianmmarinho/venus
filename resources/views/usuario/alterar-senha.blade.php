
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
                                <a href="/" class="logo logo-admin"><img src="{{ URL::asset('/images/logo150px.ico')}}" height="100" alt="logo"></a>
                            </h3>
                            <div class="p-3">

                                <form method="POST" class="form-horizontal mt-4" action="/usuario/gravaSenha">
                                    @csrf



                                     <input  class="form-control" name="senhaNova"  value="{{session()->get('usuario.nome')}}" disabled>

                                     <br />
                                 @if(session('mensagem'))
                                     <div class="alert alert-success">
                                         <p>{{session('mensagem')}}</p>
                                     </div>
                                 @endif

                                 @if(session('mensagemErro'))
                                     <div class="alert alert-danger">
                                         <p>{{session('mensagemErro')}}</p>
                                     </div>
                                 @endif

                                 <label for="userpassword">Senha Atual</label>
                                 <div class="input-group">
                                     <input id="senhaAtual" type="password" class="form-control @error('password') is-invalid @enderror" name="senhaAtual" required autocomplete="current-password" placeholder="">
                                     @error('password')
                                     <span class="invalid-feedback" role="alert">
                                         <strong>{{ $message }}</strong>
                                     </span>
                                     @enderror
                                     <button class="btn btn-primary bi bi-eye" type="button" id="buttonEye"></button>
                                 </div>
                                 <br>

                                 <label for="userpassword">Nova Senha</label>
                                 <div class="input-group">
                                    <input id="senhaNova" type="password" class="form-control @error('password') is-invalid @enderror" name="senhaNova" required autocomplete="current-password" placeholder="">
                                     @error('password')
                                     <span class="invalid-feedback" role="alert">
                                         <strong>{{ $message }}</strong>
                                     </span>
                                     @enderror
                                     <button class="btn btn-primary bi bi-eye" type="button" id="buttonEye2"></button>
                                 </div>
                                 <br>

                                 <div class="form-group row mt-4">
                                     <div class="d-grid mx-auto">
                                         <button class="btn btn-primary " type="submit">Alterar Senha</button>
                                     </div>
                                 </div>
                                 <!-- <div class="form-group mb-0 row">
                                     <div class="col-12 mt-4">
                                         <a href="{{ route('password.request') }}" class="text-muted"><i class="mdi mdi-lock"></i> Forgot your password?</a>
                                     </div>
                                 </div> -->
                             </form>

                            </div>
                        </div>
                    </div>
                    <div class="mt-5 text-center">
                        
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                function togglePasswordVisibility(buttonId, inputId) {
                                    const button = document.getElementById(buttonId);
                                    const input = document.getElementById(inputId);
                    
                                    button.addEventListener('click', function () {
                                        if (input.type === 'password') {
                                            input.type = 'text';
                                            button.classList.remove('bi-eye');
                                            button.classList.add('bi-eye-slash');
                                        } else {
                                            input.type = 'password';
                                            button.classList.remove('bi-eye-slash');
                                            button.classList.add('bi-eye');
                                        }
                                    });
                                }
                    
                                togglePasswordVisibility('buttonEye', 'senhaAtual');
                                togglePasswordVisibility('buttonEye2', 'senhaNova');
                            });
                        </script>

                         {{-- <p>© {{  date('Y', strtotime('-2 year')) }} - {{  date('Y') }} Comunhão Espírita de Brasília <i class="mdi mdi-heart text-danger"></i></p> --> --}}
                        <p>© Comunhão Espírita de Brasília <i class="mdi mdi-heart text-danger"></i></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

