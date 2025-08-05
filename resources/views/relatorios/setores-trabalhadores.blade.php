@extends('layouts.app')

@section('title')
    Relatório de Grupo/Trabalhadores
@endsection

@section('content')
    <div class="container-fluid" >
        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family: calibri">
            RELATÓRIO DE GRUPOS/TRABALHADORES
        </h4>
        <div class="col-12">
            <div class="row justify-content-center">
                <div>
                    <form action="{{route('form.trab')}}" method="GET" id="form_reuniao">
                    <div class="row">
                            <div class="col-1">Nível
                                <select class="form-select" id="nivel" name="nivel">
                                    @foreach ($nivel as $niveis)
                                        <option value="{{ $niveis->id }}" {{ request('nivel') == $niveis->id ? 'selected' : '' }}>
                                            {{ $niveis->s_nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-1">Setor
                                <select class="form-select select2" id="setor" name="setor">
                                    <option value="">Todos</option>
                                    @foreach ($setor as $setores)
                                        <option value="{{ $setores->sid }}" {{ request('setor') == $setores->sid ? 'selected' : '' }}>
                                            {{ $setores->sigla }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3">Reunião
                            <select class="form-select select2" id="reuniao" name="reuniao[]" multiple="multiple">
                                <option value="">Todos</option>
                                @foreach ($grupo as $reuniao)
                                    <option value="{{$reuniao->cid}}" 
                                        {{ in_array($reuniao->cid, request('reuniao', [])) ? 'selected' : '' }}>
                                        {{ $reuniao->g_nome }} ({{ $reuniao->s_sigla }}) | {{ $reuniao->d_sigla }} | {{$reuniao->h_inicio}} | {{ $reuniao->t_sigla }}
                                    </option>
                                @endforeach
                            </select>
                            </div>
                            <div class="col-2">Função
                                <select class="form-select select2" id="funcao" name="funcao">
                                    <option value="">Todos</option>
                                    @foreach ($funcao as $item)
                                        <option value="{{ $item->id }}" {{ request('funcao') == $item->id ? 'selected' : '' }}>
                                            {{ $item->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3">Nome do Membro
                                <select class="form-select select2" id="membro" name="membro">
                                    <option value="">Todos</option>
                                    @foreach ($membro as $mem)
                                        <option value="{{ $mem->pid }}" {{ request('membro') == $mem->pid ? 'selected' : '' }}>
                                            {{ $mem->nome_completo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col mt-3">
                                <input class="btn btn-light btn-sm me-md-2" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
                                <a href="{{ url()->current()  }}" class="btn btn-light btn-sm me-md-2" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;">Limpar</a>
                            </div>
                        </div>
                    </form>
                    <hr />
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table">
                <table class="table table-striped table-bordered border-secondary table-hover align-middle">
    <thead style="text-align: center;">
        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
            <th>NÍVEL</th>
            <th>SETOR</th>    
            <th>GRUPO</th>
            <th>DIA</th>
            <th>HORA INÍCIO</th>
            <th>HORA FIM</th>
            <th>TRATAMENTO</th>
            <th>FUNÇÃO</th>
            <th>TRABALHADOR</th>
        </tr>
    </thead>
    <tbody style="font-size: 14px; color:#000000; text-align: center;">
        @php
            $currentIdc = null; // Variável para controlar o ID atual
            $totalVlrFinal = 0; // Variável para somar vlr_final do ID atual
        @endphp

        @foreach ($trabalho as $trab)
            @if ($currentIdc !== $trab->cid)
                @if ($currentIdc !== null)
                    <!-- Exibe o total do ID anterior antes de mudar -->
                    <tr>
                        <td colspan="7"></td>                    
                        <td style="background-color:paleturquoise;font-size:12px; text-align:right;"><strong>T membros grupo:</strong></td>
                        <td style="background-color:paleturquoise;font-size:12px; text-align:right;"><strong>{{ number_format($totalVlrFinal, 0, '', '.') }}</strong></td>
                    </tr>
                @endif
                @php
                    // Atualiza o ID atual e reseta o total
                    $currentIdc = $trab->cid;
                    $totalVlrFinal = 0; // Reseta o total ao mudar de ID
                @endphp
            @endif

            <!-- Soma o valor ao total do ID atual -->
            @php $totalVlrFinal += $trab->vlr_final; @endphp

            <!-- Exibe os valores de cada registro -->
            <tr>                                        
                <td>{{ $trab->n_nome }}</td>
                <td>{{ $trab->setor_sigla }}</td>
                <td>{{ $trab->g_nome }}</td>
                <td>{{ $trab->dia_nome }}</td>
                <td>{{ $trab->h_inicio }}</td>
                <td>{{ $trab->h_fim }}</td>
                <td>{{ $trab->t_sigla }}</td>
                <td>{{ $trab->nome_funcao }}</td>
                <td>{{ $trab->nome_completo }}</td>
            </tr>
        @endforeach
        
        <!-- Exibe o total do último ID -->
        <tr>
            <td colspan="7"></td>    
            <td style="background-color:paleturquoise;font-size:12px; text-align:right;"><strong>T Membros Grupo:</strong></td>
            <td style="background-color:paleturquoise;font-size:12px; text-align:right;"><strong>{{ number_format($totalVlrFinal, 0, '', '.') }}</strong></td>
        </tr>
    </tbody>
    @if($trabalho->currentPage() === $trabalho->lastPage())
        <tfoot style='background:#ffffff;'>
            <tr>
                <td colspan="7"></td>
                <th style="background-color:yellow;text-align:right;font-size:14px;font-weight: bold;">TOTAL MEMBROS</th>
                <td style="background-color:yellow;text-align:center;font-size:14px;font-weight: bold;"><strong>{{ number_format($totmem, 0, '', '.') }}</strong></td>
            </tr>
        </tfoot>
    @endif
</table>
                </div>
                {{ $trabalho->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div class="d-flex justify-content-center">           
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Inicializa o Select2 com a configuração de busca
            $('#reuniao').select2({
                placeholder: "Selecione a reunião",
                allowClear: true
            });

            // Evento de mudança de seleção
            $('#reuniao').on('change', function() {
                // Captura os valores selecionados
                var selectedValues = $(this).val();
                console.log('Valores selecionados:', selectedValues);

                // Se algum valor for selecionado, submete o formulário automaticamente
                if (selectedValues.length > 0) {
                    $('#form_reuniao').submit();
                }
            });

            // Submissão automática ao pesquisar
            $('#reuniao').on('select2:select', function(e) {
                // Captura os valores filtrados ao selecionar uma opção
                var selectedValues = $(this).val();
                console.log('Valores após seleção:', selectedValues);

                // Submete o formulário
                $('#form_reuniao').submit();
            });
        });
    </script>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        let select = document.getElementById("reuniao");
        let options = Array.from(select.getElementsByTagName("option"));

        let firstOption = options.find(option => option.value === ""); // Encontra a opção "Todos"
        let sortedOptions = options.filter(option => option.value !== "").sort((a, b) => a.text.localeCompare(b.text)); // Ordena as demais

        select.innerHTML = ""; // Limpa o <select>
        if (firstOption) {
            select.appendChild(firstOption); // Adiciona "Todos" primeiro
        }
        sortedOptions.forEach(option => select.appendChild(option)); // Adiciona as opções ordenadas

        // Recarregar o Select2 para aplicar a nova ordenação
        $('#reuniao').select2();
    });

    </script>

    <script>
        $(document).ready(function () {
            if({{request('dia') === null }}){
                $('#dia').prop('selectedIndex', 0);
            }
            
        });
        
    </script>
@endsection