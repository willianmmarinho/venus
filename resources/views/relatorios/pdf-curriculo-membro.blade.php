<?php 
// "date_default_timezone_set" may be required by your server
date_default_timezone_set( 'America/Sao_Paulo' );

// make a DateTime object 
// the "now" parameter is for get the current date, 
// but that work with a date recived from a database 
// ex. replace "now" by '2022-04-04 05:05:05'
$dateTimeObj = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));

// format the date according to your preferences
// the 3 params are [ DateTime object, ICU date scheme, string locale ]
$dateFormatted = 
IntlDateFormatter::formatObject( 
  $dateTimeObj, 
  "eeee, d 'de' MMMM 'de' y 'às' HH:mm", 
  'pt' 
);

function customUcwords($string, $ignoreWords = ['de', 's']) {
    return preg_replace_callback('/\b\w+\b/', function ($matches) use ($ignoreWords) {
        $word = $matches[0];
        return in_array(strtolower($word), $ignoreWords) ? $word : ucfirst($word);
    }, $string);
}

?>
    <center>
        <h1>
            {{ $dadosP->nome_completo }}
        </h1>
        <p>Associado(a) Nº {{ $dadosP->nr_associado }}</p>
    </center>


<br />

<table style="font-size: 12px; border-collapse: collapse; width: 100%; border: 1px solid rgb(247, 244, 244);">
    <thead>
        <tr>
            <th style="border: 1px solid black; padding: 5px;">GRUPO</th>
            <th style="border: 1px solid black; padding: 5px;">TRABALHO</th>
            <th style="border: 1px solid black; padding: 5px;">DIA</th>
            <th style="border: 1px solid black; padding: 5px;">INÍCIO</th>
            <th style="border: 1px solid black; padding: 5px;">FIM</th>
            <th style="border: 1px solid black; padding: 5px;">SALA</th>
            <th style="border: 1px solid black; padding: 5px;">SETOR</th>
            <th style="border: 1px solid black; padding: 5px;">FUNÇÃO</th>
            <th style="border: 1px solid black; padding: 5px;">ENTRADA</th>
            <th style="border: 1px solid black; padding: 5px;">SAÍDA</th>
            <th style="border: 1px solid black; padding: 5px;">STATUS</th>
        </tr>
    </thead>
    <tbody style="text-align: center">
        @foreach($membros as $membro)
            <tr>
                <td style="border: 1px solid black; padding: 5px;">{{ $membro->nome_grupo }}</td>
                <td style="border: 1px solid black; padding: 5px;">{{ $membro->trabalho }}</td>
                <td style="border: 1px solid black; padding: 5px;">{{ $membro->dia }}</td>
                <td style="border: 1px solid black; padding: 5px;">{{ date('H:i', strtotime($membro->h_inicio)) }}</td>
                <td style="border: 1px solid black; padding: 5px;">{{ date('H:i', strtotime($membro->h_fim)) }}</td>
                <td style="border: 1px solid black; padding: 5px;">{{ $membro->sala }}</td>
                <td style="border: 1px solid black; padding: 5px;">{{ $membro->sigla }}</td>
                <td style="border: 1px solid black; padding: 5px;">{{ $membro->nome_funcao }}</td>
                <td style="border: 1px solid black; padding: 5px;">{{ date('d/m/Y', strtotime($membro->dt_inicio)) }}</td> 
                <td style="border: 1px solid black; padding: 5px;">{{ $membro->dt_fim ? date('d/m/Y', strtotime($membro->dt_fim)) : '-' }}</td>                
                <td style="border: 1px solid black; padding: 5px;">{{ $membro->status_membro }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
    <div class="footer">
        <p>Brasília, DF - {{  customUcwords($dateFormatted) }}</p>
      </div>
    <style>
        .footer {
          position: fixed;
          left: 0;
          bottom: 0;
          width: 100%;
          text-align: center;
        }

        table,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 5px;
        }

        tr:nth-child(odd) {
            background-color: #e4e4e4;
        }
        </style>