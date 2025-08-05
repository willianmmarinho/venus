@extends('layouts.app')

@section('title', 'Visualizar Sala')
@section('content')
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        VISUALIZAR SALA
                    </div>
                    <div class="card-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="row">
                                    <div class="col-8">
                                        <br>
                                        <label for="nome" class="form-label">Nome</label>
                                        <input type="text" id="nome" value="{{ $salaEditada->nome }}" class="form-control" placeholder="Nome" disabled>
                                    </div>

                                    <div class="col">
                                        <br>
                                        <label for="status_sala" class="form-label">Status</label>
                                        <select name="status_sala" class="form-control" disabled>
                                            <option value="1" {{ $salaEditada->status_sala == 1 ? 'selected' : '' }}>Ativo</option>
                                            <option value="0" {{ $salaEditada->status_sala == 0 ? 'selected' : '' }}>Inativo</option>
                                        </select>
                                    </div>

                                    <div class="col">
                                        <br>
                                        <label for="id_motivo" class="form-label">Motivo</label>
                                        <select name="id_motivo" class="form-control" required="required" disabled>
                                            <option value="{{ $salas[0]->id_motivo }}">{{ $salas[0]->tipo }}</option>
                                            @foreach ($tipo_motivo as $tipo_motivos)
                                                <option value="{{ $tipo_motivos->id }}">{{ $tipo_motivos->tipo }}</option>
                                            @endforeach
                                        </select>
                                    </div>


                                    <div class="row mt-4">
                                    <div class="col">
                                        <br>
                                        <label for="id_localizacao" class="form-label">Localização</label>
                                        <select name="id_localizacao" class="form-control" required="required" disabled>
                                            @foreach ($tipo_localizacao as $localizacao)
                                                <option value="{{ $localizacao->id }}">{{ $localizacao->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col">
                                        <br>
                                        <label for="id_finalidade" class="form-label">Finalidade Sala</label>
                                        <select name="id_finalidade" class="form-control" required="required" disabled>
                                            @foreach ($tipo_finalidade_sala as $tipo)
                                                <option value="{{ $tipo->id }}">{{ $tipo->descricao }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col">
                                        <br>
                                        <label for="numero" class="form-label">Número</label>
                                        <input type="text" id="numero" value="{{ $salaEditada->numero }}" class="form-control"  disabled>
                                    </div>

                                    <div class="col">
                                        <br>
                                        <label for="tamanho_sala" class="form-label">M² da Sala</label>
                                        <input type="number" id="tamanho_sala" value="{{ $salaEditada->tamanho_sala }}" class="form-control"  disabled>
                                    </div>

                                    <div class="col">
                                        <br>
                                        <label for="nr_lugares" class="form-label">Número de Lugares</label>
                                        <input type="number" id="nr_lugares" value="{{ $salaEditada->nr_lugares }}" class="form-control"  disabled>
                                    </div>
                                </div>
                            </div>

                                <div class="row mt-5">
                                    <div class="col">
                                        <input type="checkbox" name="ar_condicionado" @checked($salaEditada->ar_condicionado)
                                        data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não"
                                        data-onstyle="success" data-offstyle="danger" placeholder="Disabled input"
                                        disabled>
                                        <label for="ar_condicionado">Ar-cond</label>
                                    </div>
                                    <div class="col">

                                        <input type="checkbox" name="armarios" @checked($salaEditada->armarios)
                                            data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não"
                                            data-onstyle="success" data-offstyle="danger" placeholder="Disabled input"
                                            disabled>
                                            <label for="armarios">Armários</label>
                                    </div>
                                    <div class="col">

                                        <input type="checkbox" name="bebedouro" @checked($salaEditada->bebedouro)
                                            data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não"
                                            data-onstyle="success" data-offstyle="danger" placeholder="Disabled input"
                                            disabled>
                                            <label for="bebedouro">Bebedouro</label>
                                    </div>
                                    <div class="col">

                                        <input type="checkbox" name="controle" @checked($salaEditada->controle)
                                            data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não"
                                            data-onstyle="success" data-offstyle="danger" placeholder="Disabled input"
                                            disabled>
                                            <label for="controle">Controle</label>
                                    </div>
                                    <div class="col">

                                        <input type="checkbox" name="computador" @checked($salaEditada->computador)
                                            data-toggle="toggle" data-on="Sim" data-off="Não" data-onstyle="success"
                                            data-offstyle="danger" placeholder="Disabled input" disabled>
                                            <label for="computador">PC</label>
                                    </div>
                                    <div class="col">

                                        <input type="checkbox" name="projetor" @checked($salaEditada->projetor)
                                            data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não"
                                            data-onstyle="success" data-offstyle="danger" placeholder="Disabled input"
                                            disabled>
                                            <label for="projetor">Projetor</label>
                                    </div>
                                    <div class="col">

                                        <input type="checkbox" name="tela_projetor" @checked($salaEditada->tela_projetor)
                                            data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não"
                                            data-onstyle="success" data-offstyle="danger" placeholder="Disabled input"
                                            disabled>
                                            <label for="tela_projetor">Tela_proj</label>
                                    </div>
                                    <div class="col">

                                        <input type="checkbox" @checked($salaEditada->quadro) name="quadro"
                                            data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não"
                                            data-onstyle="success" data-offstyle="danger" placeholder="Disabled input"
                                            disabled>
                                            <label for="quadro">Quadro</label>
                                    </div>
                                    <div class="col">

                                        <input type="checkbox" name="som" @checked($salaEditada->som)
                                            data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não"
                                            data-onstyle="success" data-offstyle="danger" placeholder="Disabled input"
                                            disabled>
                                            <label for="som">Som</label>
                                    </div>
                                    <br>
                                    <div class="col">

                                        <input type="checkbox" name="ventilador" @checked($salaEditada->ventilador)
                                            data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não"
                                            data-onstyle="success" data-offstyle="danger" placeholder="Disabled input"
                                            disabled>
                                            <label for="ventilador">Ventilador</label>
                                    </div>
                                    <div class="col">

                                        <input type="checkbox" name="luz_azul" @checked($salaEditada->luz_azul)
                                            data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não"
                                            data-onstyle="success" data-offstyle="danger" placeholder="Disabled input"
                                            disabled>
                                            <label for="luz_azul">Luz azul</label>
                                    </div>
                                    <div class="col">

                                        <input type="checkbox" name="luz_vermelha" @checked($salaEditada->luz_vermelha)
                                            data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não"
                                            data-onstyle="success" data-offstyle="danger" placeholder="Disabled input"
                                            disabled>
                                            <label for="luz_vermelha">Luz_vermelha</label>
                                </div>
                            </div>
                        </div>
                        <br>

                        <?php $a = 1; $b = 1; $c = 1; $d = 1; $e = 1; ?>
                        @foreach ($salas as $sala)
                            </tbody>
                        @endforeach

                        <div class="row justify-content-center">
                            <div class="d-grid gap-1 col-3 mx-auto">
                                <br>
                                <a class="btn btn-danger" href="/gerenciar-salas" role="button">Fechar</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footerScript')
   
    <script src="{{ URL::asset('/js/pages/mascaras.init.js') }}"></script>
@endsection
