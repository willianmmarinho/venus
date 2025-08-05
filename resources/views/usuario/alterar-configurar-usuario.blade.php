@extends('layouts.app')

@section('title')
    Alterar usuário
@endsection

@section('content')
    <br>
    <div class="container">
        <form class="form-horizontal mt-4" method="POST" action="/usuario-atualizar/{{ $resultUsuario->id }}">
            @csrf
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card m-1">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    ALTERAR USUÁRIO
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <p>NOME:<strong> {{ $result[0]->nome_completo }}</strong></p>
                                        <p>IDENTIDADE:<strong> {{ $result[0]->idt }}</strong> </p>
                                    </div>
                                    <div class="col">
                                        <p>CPF: <strong> {{ $result[0]->cpf }}</strong> </p>
                                        <p>DATA NASCIMENTO:<strong>
                                                {{ date('d-m-Y', strtotime($result[0]->dt_nascimento)) }}</strong> </p>
                                    </div>
                                    <div class="col">
                                        <p>EMAIL: <strong> {{ $result[0]->email }}</strong> </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card m-1">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    SELECIONAR PERFIS
                                </div>
                            </div>
                        </div>
                        <div class="card-body">

                            <input type="hidden" name="idPessoa" value="{{ $result[0]->id }}">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped mb-0">
                                    <tr>
                                        <td style="text-align:right;">Ativo</td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input " type="checkbox" role="switch"
                                                    id="ativo" name="ativo"
                                                    {{ $resultUsuario->ativo == 1 ? 'checked' : '' }}>

                                                <label for="bloqueado" class="form-check-label"></label>
                                            </div>
                                        </td>
                                        <td style="text-align:right;">Bloqueado</td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input " type="checkbox" role="switch"
                                                    id="bloqueado" name="bloqueado"
                                                    {{ $resultUsuario->bloqueado == 1 ? 'checked' : '' }}>

                                                <label for="bloqueado" class="form-check-label"></label>
                                            </div>

                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <br>

                            <div class="accordion" id="accordionExample">

                                @foreach ($resultPerfil as $resultPerfils)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapseOne{{ $resultPerfils->id }}" aria-expanded="true"
                                                aria-controls="collapseOne" id="collapseButton{{ $resultPerfils->id }}">
                                                {{ $resultPerfils->descricao }}
                                            </button>
                                        </h2>
                                        <div id="collapseOne{{ $resultPerfils->id }}" class="accordion-collapse collapse"
                                            >
                                            <div class="accordion-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped mb-0" id="myTable"
                                                        name="myTable">
                                                        @foreach ($resultSetor as $resultSetors)
                                                            <tr>
                                                                <td>
                                                                    {{ $resultSetors->nome }} - {{ $resultSetors->sigla }}
                                                                </td>
                                                                <td>
                                                                    <div class="form-check form-switch">
                                                                        <input class="form-check-input " type="checkbox"
                                                                            role="switch"
                                                                            id="perfis[{{ $resultPerfils->id }}][{{ $resultSetors->id }}]"
                                                                            name="perfis[{{ $resultPerfils->id }}][{{ $resultSetors->id }}]">
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach



                            </div>




                        </div>
                    </div>
                </div>
            </div>
            <br />
            <div class="card">
                <div class="card-header">
                    <center>
                        <div class="row">
                            <div class="col">
                                <a href="/gerenciar-usuario ">
                                    <input class="btn btn-danger btn-block col-5" type="button" value="Cancelar">
                                </a>
                            </div>
                            <div class="col">
                                <button type="submit" class="btn btn-primary btn-block col-5">Cadastrar</button>
                            </div>
                        </div>
                    </center>
                </div>
            </div>

        </form>
        <br />
        <br />
        <br />
    </div>

    <script>
        $(document).ready(function() {
            let acessosAutorizados = @JSON($acessosAutorizados);
            console.log(acessosAutorizados)
            $.each(acessosAutorizados, function(key, value) {
                if ($("#collapseOne" + value.id_perfil).hasClass('collapse')) {
                    $("#collapseOne" + value.id_perfil).addClass('show')
                    $("#collapseButton" + value.id_perfil).removeClass('collapsed')
                }

               $("#perfis\\["+ value.id_perfil +"\\]\\["+ value.id_setor + "\\]").prop('checked', true);
            });
        });
    </script>
@endsection
