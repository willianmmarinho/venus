@extends('layouts.app')
@section('title', 'Cadastrar Sala')
@section('content')
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                CADASTRAR SALA
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal mt-2" method="post" action="/incluir-salas">
                            @csrf

                            <div class="row">
                                <div class="col-6">
                                    Nome
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <input type="text" class="form-control" id="nome" name="nome" maxlength="30"
                                        required="required" oninput="validarSomenteLetras(this)">
                                </div>



                                <div class="col">
                                    Status
                                    <select class="form-select" aria-label=".form-select-lg example" name="status_sala"
                                        required="required">
                                        <option value="1">Ativo</option>
                                        <option value="2">Inativo</option>
                                    </select>
                                </div>
                                <div class="col">Localização
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <select class="form-select" name="id_localizacao" aria-label=".form-select-lg example">
                                        <option selected></option>
                                        @foreach ($tipo_localizacao as $localizacao)
                                            <option value={{ $localizacao->ids }}>{{ $localizacao->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>


                            </div>

                            <br>
                            <div class="row">
                                <div class="col">
                                    Finalidade sala
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <select class="form-select" aria-label=".form-select-lg example" name="tipo_sala"
                                        required>
                                        <option selected></option>
                                        @foreach ($tipo_finalidade_sala as $tipo)
                                            <option value={{ $tipo->id }}>{{ $tipo->descricao }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">Número

                                    <input type="text" class="form-control" id="numero" maxlength="10"
                                        name="numero">

                                </div>


                                <div class="col">M² da sala
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <input type="number" class="form-control" id="tamanho_sala" name="tamanho_sala"
                                        min="1" max="500"
                                        oninput="javascript: if (this.value.length > 3) this.value = this.value.slice(0, 3); validarNumero(this);"
                                        required="required">
                                </div>
                                <div class="col">Número de lugares
                                    <span class="tooltips">
                                        <span class="tooltiptext">Obrigatório</span>
                                        <span style="color:red">*</span>
                                    </span>
                                    <input type="number" class="form-control" id="nr_lugares" name="nr_lugares"
                                        min="1" max="2000"
                                        oninput="javascript: if (this.value.length > 3) this.value = this.value.slice(0, 3); validarNumero(this);"
                                        required="required">
                                </div>
                            </div>
                            <br>


                            <br>

                            <div class="row justify-content-center mb-4">
                                <div class="col text-center">
                                    <h3 class="fw-bold" style="font-size: 24px; color: #333;">Opcionais da sala</h3>
                                </div>
                            </div>

                            <div class="row align-items-center">

                                <div class="col text-center mb-3">
                                    <label for="ar_condicionado"></label>
                                    <input type="checkbox" name="ar_condicionado" data-toggle="toggle" data-onlabel="Sim"
                                        data-offlabel="Não" data-onstyle="success" data-offstyle="danger">
                                        <label for="ar_condicionado">Ar_cond</label>

                                </div>
                                <div class="col text-center mb-3">
                                    <label for="armarios"></label>
                                    <input type="checkbox" name="armarios" data-toggle="toggle" data-onlabel="Sim"
                                        data-offlabel="Não" data-onstyle="success" data-offstyle="danger">
                                        <label for="armarios">Armários</label>

                                </div>
                                <div class="col text-center mb-3">
                                    <label for="bebedouro"></label>
                                    <input type="checkbox" name="bebedouro" data-toggle="toggle" data-onlabel="Sim"
                                        data-offlabel="Não" data-onstyle="success" data-offstyle="danger">
                                        <label for="bebedouro">Bebedouro</label>

                                </div>
                                <div class="col text-center mb-3">
                                    <label for="controle"></label>
                                    <input type="checkbox" name="controle" data-toggle="toggle" data-onlabel="Sim"
                                        data-offlabel="Não" data-onstyle="success" data-offstyle="danger">
                                        <label for="controle">Controle</label>

                                </div>
                                <div class="col text-center mb-3">
                                    <label for="computador"></label>
                                    <input type="checkbox" name="computador" data-toggle="toggle" data-on="Sim"
                                        data-off="Não" data-onstyle="success" data-offstyle="danger">
                                        <label for="computador">Computador</label>

                                </div>
                                <div class="col text-center mb-3">
                                    <label for="projetor"></label>
                                    <input type="checkbox" name="projetor" data-toggle="toggle" data-onlabel="Sim"
                                        data-offlabel="Não" data-onstyle="success" data-offstyle="danger">
                                        <label for="projetor">Projetor</label>

                                </div>

                                <div class="col text-center mb-3">
                                    <label for="tela_projetor"></label>
                                    <input type="checkbox" name="tela_projetor" data-toggle="toggle" data-onlabel="Sim"
                                        data-offlabel="Não" data-onstyle="success" data-offstyle="danger">
                                        <label for="tela_projetor">Tela_projetor</label>

                                </div>
                                <div class="col text-center mb-3">
                                    <label for="quadro"></label>
                                    <input type="checkbox" name="quadro" data-toggle="toggle" data-onlabel="Sim"
                                        data-offlabel="Não" data-onstyle="success" data-offstyle="danger">
                                        <label for="quadro">Quadro</label>

                                </div>
                                <div class="col text-center mb-3">
                                    <label for="som"></label>
                                    <input type="checkbox" name="som" data-toggle="toggle" data-onlabel="Sim"
                                        data-offlabel="Não" data-onstyle="success" data-offstyle="danger">
                                        <label for="som">Som</label>

                                </div>
                                <div class="col text-center mb-3">
                                    <label for="ventilador"></label>
                                    <input type="checkbox" name="ventilador" data-toggle="toggle" data-onlabel="Sim"
                                        data-offlabel="Não" data-onstyle="success" data-offstyle="danger">
                                        <label for="ventilador">Ventilador</label>

                                </div>
                                <div class="col text-center mb-3">

                                    <input type="checkbox" name="luz_azul" data-toggle="toggle" data-onlabel="Sim"
                                        data-offlabel="Não" data-onstyle="success" data-offstyle="danger">
                                        <label for="luz_azul">Luz_azul</label>
                                </div>
                                <div class="col text-center mb-3">

                                    <input type="checkbox" name="luz_vermelha" data-toggle="toggle" data-onlabel="Sim"
                                        data-offlabel="Não" data-onstyle="success" data-offstyle="danger">
                                        <label for="luz_vermelha">Luz_vermelha</label>

                                </div>

                            </div>



                            <div class="row justify-content-center">
                                <div class="d-grid gap-1 col-4 mx-auto">
                                    <br>
                                    <a class="btn btn-danger" href="/gerenciar-salas" role="button">Cancelar</a>
                                </div>
                                <div class="d-grid gap-2 col-4 mx-auto">
                                    <br>
                                    <button class="btn btn-primary">Confirmar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function validarSomenteLetras(input) {
            // Permite letras, espaços e caracteres especiais
            input.value = input.value.replace(/[^a-zA-Z\u00C0-\u00FF\s]/g, '');
        }
    </script>
@endsection
