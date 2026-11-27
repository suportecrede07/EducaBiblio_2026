<?php

include 'config.php';
date_default_timezone_set('America/Fortaleza');

$sql = "SELECT prof.idProf,
prof.NomeProf,
prof.EmailProf, 
prof.MateriaProf
FROM prof";

$res = $conn->query($sql);

if ($res->num_rows > 0) {
    $dataHora = date('d/m/Y H:i');
    $css = file_get_contents('../CSS/relatorios.css');

    $html = "<html>
    <head>
        <meta charset='UTF-8'>
        <title>EducaBiblio - Relatório de Professores</title>
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
                        <div class='report-badge-title'>Relatório de Professores</div>
                        <div class='report-date'>Emitido em: {$dataHora}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class='report-intro'>
            Abaixo estão listados todos os <strong>professores cadastrados</strong> no sistema e suas respectivas disciplinas de atuação.
        </div>

        <table class='data-table'>
            <thead>
                <tr>
                    <th style='width: 40%;'>Nome do Professor</th>
                    <th style='width: 35%;'>Contato / E-mail</th>
                    <th style='width: 25%;'>Disciplina</th>
                </tr>
            </thead>
            <tbody>";

    while ($row = $res->fetch_object()) {
        $email = !empty($row->EmailProf) ? $row->EmailProf : '-';
        $materia = !empty($row->MateriaProf) ? $row->MateriaProf : '-';

        $html .= "<tr>";
        $html .= "<td><strong>" . htmlspecialchars($row->NomeProf) . "</strong></td>";
        $html .= "<td>" . htmlspecialchars($email) . "</td>";
        $html .= "<td>" . htmlspecialchars($materia) . "</td>";
        $html .= "</tr>";
    }

    $html .= "</tbody>
        </table>

        <div class='footer-wrapper'>
            <table class='footer-table'>
                <tr>
                    <td>Governo do Estado do Ceará &bull; Secretaria da Educação &bull; EducaBiblio</td>
                    <td class='footer-right'>Relatório Institucional de Professores</td>
                </tr>
            </table>
        </div>
    </body>
    </html>";
} else {
    $html = "<div style='font-family: Arial; padding: 20px; text-align: center; color: #666;'><h3>Não há professores cadastrados a serem exibidos.</h3></div>";
}

use Dompdf\Dompdf;

require_once 'dompdf/autoload.inc.php';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->set_option('defaultFont', 'Helvetica');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Relatorio_Professores.pdf", array("Attachment" => false));
