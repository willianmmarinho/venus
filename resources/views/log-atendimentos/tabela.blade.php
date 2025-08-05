
<table class="table table-sm  border-secondary table-hover align-middle">
                        <thead style="text-align: center;">
                            <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                                <th class="col">ID</th>
                                <th class="col">USUARIO</th>
                                <th class="col">ORIGEM</th>
                                <th class="col">REFERENCIA</th>
                                <th class="col">AÇÃO</th>
                                <th class="col">OBS</th>
                                <th class="col">ASSISTIDO</th>
                                <th class="col">DATA</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px; color:#000000; text-align:center;">
                            @foreach($respostaTratada as $dado)

                            <tr style="{{ in_array($dado->id,$erro) ? 'color:red' : null}}">
                                <td>{{ $dado->id }}</td>
                                <td>{{ (in_array($dado->id,$erro)) ? 'ERRO' : $dado->nome_completo_usuario }}</td>
                                <td>{{ (in_array($dado->id,$erro)) ? 'ERRO' : $dado->origem }}</td>
                                <td>{{ (in_array($dado->id,$erro)) ? 'ERRO' : $dado->id_referencia }}</td>
                                <td>{{ (in_array($dado->id,$erro)) ? 'ERRO' : $dado->acao }}</td>
                                <td>{{ (in_array($dado->id,$erro)) ? 'ERRO' : $dado->obs }}</td>
                                <td>{{ (in_array($dado->id,$erro)) ? 'ERRO' : $dado->nome_completo }}</td>
                                <td>{{ (in_array($dado->id,$erro)) ? 'ERRO' : date('d/m/Y G:i:s', strtotime($dado->data_hora)) }}</td>
                            </tr>

                            @endforeach
                        </tbody>
                    </table>
                </div>