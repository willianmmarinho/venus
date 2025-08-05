@extends('layouts.app')

@section('title')
    Editar Pessoa
@endsection

@section('content')
    <div class="container">
        <div class="justify-content-center">
            <div class="col-12">
                <br>

                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-12">
                                <div class="col">
                                    EDITAR PESSOA
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal mt-4" method="post" action="/executa-edicao/{{ $lista[0]->idp }}">
                            @csrf
                            <div class="row">
                                <div class="col">
                                    <div class="mb-4" style="text-align:left;">
                                        <label for="validationCustom01" class="form-label">Nome completo</label>
                                        </span>
                                        <input class="form-control" type="text" maxlength="80" id=""
                                            name="nome" value="{{ $lista[0]->nome_completo }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="mb-4" style="text-align:left;">
                                        <label for="validationCustom02" class="form-label">CPF</label>
                                        <input class="form-control" type="text" maxlength="11"
                                            value="{{ $lista[0]->cpf }}" id="" name="cpf"
                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-4" style="text-align:left;">
                                        <label for="validationCustom04" class="form-label">Sexo</label>
                                        <select class="form-select" id="" name="sex">
                                            <option value="{{ $lista[0]->sexo }}">{{ $lista[0]->tipo }}</option>
                                            @foreach ($sexo as $sexos)
                                                <option @if (old('sex') == $sexos->id) {{ 'selected="selected"' }} @endif
                                                    value="{{ $sexos->id }}">{{ $sexos->tipo }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-4" style="text-align:left;">
                                        <label for="validationCustom03" class="form-label">Data Nascimento</label>
                                        <input class="form-control" type="date" value="{{ $lista[0]->dt_nascimento }}"
                                            id="" name="dt_nasc">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="mb-4" style="text-align:left;">
                                        <label for="validationCustom05" class="form-label">Status</label>
                                        <select class="form-select" id="status_pessoa" name="status">
                                            <option value="{{ $lista[0]->status }}" selected>
                                                {{ $lista[0]->tipo_status_pessoa }}</option>
                                            <option value="1">Ativo</option>
                                            <option value="0">Inativo</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-4" style="text-align:left;">
                                        <label for="validationCustom06" class="form-label">Motivo</label>
                                        <select class="form-select" id="tp_motivo" name="motivo">
                                            <option value="{{ $lista[0]->tipo_motivo_status_pessoa }}">
                                                {{ $lista[0]->motivo_status_pessoa_tipo_motivo }}</option>
                                            @foreach ($motivo as $motivos)
                                                <option @if (old('motivo') == $motivos->id) selected @endif
                                                    value="{{ $motivos->id }}">{{ $motivos->motivo }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-2">
                                    <div class="mb-4 text-start">
                                        <label for="validationCustom05" class="form-label">DDD</label>
                                        <select class="form-select" id="validationCustom05" name="ddd">
                                            <option value="" disabled selected>Selecione o DDD</option>
                                            @foreach ($ddd as $ddds)
                                                <option value="{{ $ddds->id }}"
                                                    @if (old('ddd', $lista[0]->ddd) == $ddds->id) selected @endif>
                                                    {{ $ddds->descricao }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="mb-5 text-start">
                                        <label for="validationCustom06" class="form-label">Nr Celular</label>
                                        <input class="form-control" maxlength="11" type="text" name="celular"
                                            value="{{ old('celular', $lista[0]->celular) }}"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                    </div>
                                </div>
                                <div class="row mb-5">
                                    <div class="col">
                                        <label for="validationCustom06" class="form-label">Telefone Estrangeiro</label>
                                        <input class="form-control"  type="text" name="cel_estrangeiro"
                                            value="{{ old('cel_estrangeiro', $lista[0]->cel_estrangeiro) }}"
                                            oninput="this.value = this.value.replace(/[^+\d\(\)\-\s]/g, '').slice(0, 15);"
                                        placeholder="Ex: +1 (415) 555-1234" pattern="[\+]?[0-15]{1,3}[\s]?[(]?[0-9]{1,3}[)]?[\s]?[0-9]{3}[\-]?[0-9]{4}">
                                    </div>
                                    <div class="col">
                                        <label for="validationCustom06" class="form-label">Telefone Alternativo</label>
                                        <input class="form-control" maxlength="11" type="text" name="tel_fixo"
                                            value="{{ old('tel_fixo', $lista[0]->tel_fixo) }}"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                    </div>
                                </div>

                                <br>
                                <div class="row mt-2 justify-content-center">
                                    <div class="d-grid gap-1 col-4 mx-auto">
                                        <a class="btn btn-danger" href="/gerenciar-pessoas" role="button">Cancelar</a>
                                    </div>
                                    <div class="d-grid gap-2 col-4 mx-auto">
                                        <button type="submit" class="btn btn-primary">Confirmar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                </fieldset>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Função para verificar e habilitar/desabilitar o campo "Motivo"
            function verificarStatusMotivo() {
                var statusPessoa = document.getElementById('status_pessoa');
                var motivo = document.getElementById('tp_motivo');

                // Se o status for "Inativo", habilitar o campo "Motivo", caso contrário, desabilitar
                motivo.disabled = statusPessoa.value !== '0';
            }

            // Adicionar um ouvinte de eventos ao campo "Status" para verificar mudanças
            var statusPessoa = document.getElementById('status_pessoa');
            statusPessoa.addEventListener('change', verificarStatusMotivo);

            // Chamar a função inicialmente para configurar o estado inicial
            verificarStatusMotivo();
        });
    </script>
@endsection
