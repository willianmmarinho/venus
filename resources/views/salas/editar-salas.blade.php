@extends('layouts.app')
@section('title', 'Editar Sala')
@section('content')

<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            EDITAR SALA
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form class="form-horizontal mt-2" method="post" action="/atualizar-salas/{{$salaEditada->id}}">
                        @csrf
                        <div class="row">
                            <div class="col-6">
                                Nome
                                <span class="tooltips">
                                    <span class="tooltiptext">Obrigatório</span>
                                    <span style="color:red">*</span>
                                </span>
                                <input type="text" maxlength="50" class="form-control" id="nome" name="nome" value="{{ $salaEditada->nome }}" required="required" oninput="validarSomenteLetras(this)">
                            </div>



                            <div class="col">
                                <label for="status_sala">Status</label>
                                <select class="form-select" name="status_sala" id="status_sala" class="form-control">
                                    <option value="1" {{ $salaEditada->status_sala == 1 ? 'selected' : '' }}>Ativo</option>
                                    <option value="0" {{ $salaEditada->status_sala == 0 ? 'selected' : '' }}>Inativo</option>
                                </select>
                            </div>
                            <div class="col">
                                Motivo
                                <select class="form-select" aria-label=".form-select-lg example" name="id_motivo" id="tipo_motivo" disabled>
                                    <option value="{{ $salas[0]->id_motivo }}"> {{ $salas[0]->tipo }}</option>
                                    @foreach ($tipo_motivo as $tipo_motivos)
                                        <option value="{{ $tipo_motivos->id }}"> {{ $tipo_motivos->tipo }} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row mt-5">
                            <div class="col">
                                <label for="id_localizacao">Localização</label>
                                <span class="tooltips">
                                    <span class="tooltiptext">Obrigatório</span>
                                    <span style="color:red">*</span>
                                </span>
                                <select class="form-select" name="id_localizacao" aria-label="form-select-lg example" value={{$salaEditada->id_localizacao}}>
                                    <option value="{{ $salas[0]->id_localizacao }}"> {{ $salas[0]->nome }}</option>
                                    @foreach ($tipo_localizacao as $localizacao )
                                    <option value={{$localizacao->id}}>{{$localizacao->nome}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <br>


                            <div class="col-4">
                                Finalidade sala
                                <span class="tooltips">
                                    <span class="tooltiptext">Obrigatório</span>
                                    <span style="color:red">*</span>
                                </span>
                                <select class="form-select" aria-label=".form-select-lg example" name="id_finalidade">
                                    <option value="{{ $salas[0]->id_finalidade }}"> {{ $salas[0]->descricao }}</option>
                                    @foreach ($tipo_finalidade_sala as $tipo)
                                    <option value={{$tipo->id}}>{{$tipo->descricao}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">Número
                                <input type="text" class="form-control" id="numero" maxlength="10" name="numero" value={{$salaEditada->numero}} >

                            </div>
                            <div class="col">M² da sala
                                <span class="tooltips">
                                    <span class="tooltiptext">Obrigatório</span>
                                    <span style="color:red">*</span>
                                </span>
                                <input type="number" class="form-control" id="tamanho_sala" name="tamanho_sala"  min="1" max="500" oninput="javascript: if (this.value.length > 3) this.value = this.value.slice(0, 3);" value={{$salaEditada->tamanho_sala}} required="required">
                            </div>
                            <div class="col">Número de lugares
                                <span class="tooltips">
                                    <span class="tooltiptext">Obrigatório</span>
                                    <span style="color:red">*</span>
                                </span>
                                <input type="number" class="form-control" id="nr_lugares" name="nr_lugares"  min="1" max="2000"  oninput="javascript: if (this.value.length > 3) this.value = this.value.slice(0, 3);" value={{$salaEditada->nr_lugares}} required="required">
                            </div>
                        </div>
                    </div>
                        <br>

                        <div class="row justify-content-center mb-4">
                            <div class="col text-center">
                                <h3 class="fw-bold" style="font-size: 24px; color: #333;">Opcionais da sala</h3>
                            </div>
                        </div>

                        <div class="row mt-3 form-group">
                            <div class="col text-center mb-3">
                                <label for="ar_condicionado"></label>
                                <input type="checkbox" name="ar_condicionado" data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" {{$salaEditada->ar_condicionado ? 'checked' : ''}}>
                                <label for="ar_condicionado">Ar_cond</label>
                            </div>
                            <div class="col text-center mb-3">
                                <label for="armarios"></label>
                                <input type="checkbox" name="armarios" data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" {{$salaEditada->armarios ? 'checked' : ''}}>
                                <label for="armarios">Armários</label>
                            </div>
                            <div class="col text-center mb-3">
                                <label for="bebedouro"></label>
                                <input type="checkbox" name="bebedouro" data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" {{$salaEditada->bebedouro ? 'checked' : ''}}>
                                <label for="bebedouro">Bebedouro</label>
                            </div>
                            <div class="col text-center mb-3">
                                <label for="controle"></label>
                                <input type="checkbox" name="controle" data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" {{$salaEditada->controle ? 'checked' : ''}}>
                                <label for="controle">Controle</label>
                            </div>
                            <div class="col text-center mb-3">
                                <label for="computador"></label>
                                <input type="checkbox" name="computador" data-toggle="toggle" data-on="Sim" data-off="Não" data-onstyle="success" data-offstyle="danger" {{$salaEditada->computador ? 'checked' : ''}}>
                                <label for="computador">Computador</label>
                            </div>
                            <div class="col text-center mb-3">
                                <label for="projetor"></label>
                                <input type="checkbox" name="projetor" data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" {{$salaEditada->projetor ? 'checked' : ''}}>
                                <label for="projetor">Projetor</label>
                            </div>
                            <div class="col text-center mb-3">
                                <label for="tela_projetor"></label>
                                <input type="checkbox" name="tela_projetor" data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" {{$salaEditada->tela_projetor ? 'checked' : ''}}>
                                <label for="tela_projetor">Tela_projetor</label>
                            </div>
                            <div class="col text-center mb-3">
                                <label for="quadro"></label>
                                <input type="checkbox" name="quadro" data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" {{$salaEditada->quadro ? 'checked' : ''}}>
                                <label for="quadro">Quadro</label>
                            </div>
                            <div class="col text-center mb-3">
                                <label for="som"></label>
                                <input type="checkbox" name="som" data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" {{$salaEditada->som ? 'checked' : ''}}>
                                <label for="som">Som</label>
                            </div>
                            <br>
                            <div class="col text-center mb-3">
                                <label for="ventilador"></label>
                                <input type="checkbox" name="ventilador" data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" {{$salaEditada->ventilador ? 'checked' : ''}}>
                                <label for="ventilador">Ventilador</label>
                            </div>
                            <div class="col text-center mb-3">
                                <label for="luz_azul"></label>
                                <input type="checkbox" name="luz_azul" data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" {{$salaEditada->luz_azul ? 'checked' : ''}}>
                                <label for="luz_azul">Luz_azul</label>
                            </div>
                            <div class="col text-center mb-3">
                                <label for="luz_vermelha"></label>
                                <input type="checkbox" name="luz_vermelha" data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" {{$salaEditada->luz_azul ? 'checked' : ''}}>
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
                                <button class="btn btn-primary" type="submit">Confirmar</button>
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
        // Permite letras e alguns caracteres especiais comuns, excluindo números
        input.value = input.value.replace(/[^a-zA-Z\u00C0-\u00FF\s!"#$%&'()*+,-./:;<=>?@[\\\]^_`{|}~]/g, '');
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Função para verificar e habilitar/desabilitar o campo "Motivo"
        function verificarStatusMotivo() {
            var statusSala = document.getElementById('status_sala');
            var motivo = document.getElementById('tipo_motivo');

            // Se o status for "Inativo", habilitar o campo "Motivo", caso contrário, desabilitar
            motivo.disabled = statusSala.value !== '0';
        }

        // Adicionar um ouvinte de eventos ao campo "Status" para verificar mudanças
        var statusSala = document.getElementById('status_sala');
        statusSala.addEventListener('change', verificarStatusMotivo);

        // Chamar a função inicialmente para configurar o estado inicial
        verificarStatusMotivo();
    });
    function validarNumero(input) {
        var numeroSelecionado = parseInt(input.value, 10);
        var numerosExistem = {!! json_encode($numerosExistem) !!};

        if (numeroSelecionado < 1 || numeroSelecionado < 500 || numerosExistem.includes(numeroSelecionado)) {
            document.getElementById('numeroError').innerText = 'Existe uma sala registrada com esse número.';
            input.value = '';
        } else {
            document.getElementById('numeroError').innerText = '';
        }
    }
</script>
@endsection
