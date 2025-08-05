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
                            @foreach (range(0, 10) as $number)
                                <tr>
                                    
                                    <td>
                                        <p class="placeholder-wave">
                                            <span class="placeholder col-10"></span>
                                        </p>
                                    </td>
                                    <td>
                                        <p class="placeholder-wave">
                                            <span class="placeholder col-12"></span>
                                        </p>
                                    </td>
                                    <td>
                                        <p class="placeholder-wave">
                                            <span class="placeholder col-12"></span>
                                        </p>
                                    </td>
                                    <td>
                                        <p class="placeholder-wave">
                                            <span class="placeholder col-12"></span>
                                        </p>
                                    </td>
                                    <td>
                                        <p class="placeholder-wave">
                                            <span class="placeholder col-12"></span>
                                        </p>
                                    </td>
                                    <td>
                                        <p class="placeholder-wave">
                                            <span class="placeholder col-12"></span>
                                        </p>
                                    </td>
                                    <td>
                                        <p class="placeholder-wave">
                                            <span class="placeholder col-12"></span>
                                        </p>
                                    </td>
                                    <td>
                                        <p class="placeholder-wave">
                                            <span class="placeholder col-12"></span>
                                        </p>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>