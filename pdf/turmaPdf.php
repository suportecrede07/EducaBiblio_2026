<?php

include 'config.php';
date_default_timezone_set('America/Fortaleza');

$sql = "SELECT
            turma.AnodeInicio,
            turma.AnoTurma,
            turma.NomeTurma,
            turma.IdTurma
        FROM turma";

$res = $conn->query($sql);

if ($res->num_rows > 0) {
    $dataHora = date('d/m/Y H:i');
    $css = file_get_contents('../CSS/relatorios.css');

    $html = "<html>
    <head>
        <meta charset='UTF-8'>
        <title>EducaBiblio - Relatório de Turmas</title>
        <style>
            $css
        </style>
    </head>
    <body>
        <div class='header-wrapper'>
            <table class='header-table'>
                <tr>
                    <td class='header-title-box'>
                        <div class='header-system-name'>EducaBiblio</div>
                        <div class='header-subtitle'>Sistema de Gestão Bibliotecária e Leitura</div>
                    </td>
                    <td class='header-report-badge'>
                        <div class='report-badge-title'>Relatório de Turmas</div>
                        <div class='report-date'>Emitido em: {$dataHora}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class='report-intro'>
            Abaixo estão listadas todas as <strong>turmas cadastradas</strong> na instituição, divididas por ano e período de início.
        </div>

        <table class='data-table'>
            <thead>
                <tr>
                    <th class='center' style='width: 20%;'>Série / Ano</th>
                    <th>Identificação da Turma</th>
                    <th class='center' style='width: 25%;'>Ano de Início</th>
                </tr>
            </thead>
            <tbody>";

    while ($row = $res->fetch_object()) {
        $serie = !empty($row->AnoTurma) ? $row->AnoTurma . 'º Ano' : '-';
        $inicio = !empty($row->AnodeInicio) ? $row->AnodeInicio : '-';

        $html .= "<tr>";
        $html .= "<td class='center'><strong>" . htmlspecialchars($serie) . "</strong></td>";
        $html .= "<td>" . htmlspecialchars($row->NomeTurma) . "</td>";
        $html .= "<td class='center'>" . htmlspecialchars($inicio) . "</td>";
        $html .= "</tr>";
    }

    $html .= "</tbody>
        </table>

        <div class='footer-wrapper'>
            <table class='footer-table'>
                <tr>
                    <td>Governo do Estado do Ceará &bull; Secretaria da Educação &bull; EducaBiblio</td>
                    <td class='footer-right'>Relatório Institucional de Turmas</td>
                </tr>
            </table>
        </div>
    </body>
    </html>";
} else {
    $html = "<div style='font-family: Arial; padding: 20px; text-align: center; color: #666;'><h3>Não há turmas cadastradas a serem exibidas.</h3></div>";
}

use Dompdf\Dompdf;

require_once 'dompdf/autoload.inc.php';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->set_option('defaultFont', 'Helvetica');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Relatorio_Turmas.pdf", array("Attachment" => false));
