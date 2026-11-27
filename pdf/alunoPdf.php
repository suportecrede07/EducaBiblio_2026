<?php

include 'config.php';

$sql = "SELECT aluno.NomeAluno,
aluno.idAluno,
aluno.Turma_idTurma,
aluno.EmailAluno,
'aluno' AS tipo,
turma.nomeTurma,
turma.AnoTurma  
FROM aluno
LEFT JOIN turma ON aluno.Turma_idTurma = turma.idTurma ";

$res = $conn->query($sql);

if ($res->num_rows > 0) {
    $dataHora = date('d/m/Y H:i');
    $css = file_get_contents('../CSS/relatorios.css');

    $html = "<html>
    <head>
        <meta charset='UTF-8'>
        <title>EducaBiblio - Relatório de Alunos</title>
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
                        <div class='report-badge-title'>Relatório de Alunos</div>
                        <div class='report-date'>Emitido em: {$dataHora}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class='report-intro'>
            Abaixo estão listados todos os <strong>alunos cadastrados</strong> no sistema, organizados por suas respectivas séries e turmas.
        </div>

        <table class='data-table'>
            <thead>
                <tr>
                    <th>Nome do Aluno</th>
                    <th>Contato / E-mail</th>
                    <th class='center' style='width: 12%;'>Série</th>
                    <th class='center' style='width: 15%;'>Turma</th>
                </tr>
            </thead>
            <tbody>";

    while ($row = $res->fetch_object()) {
        $serie = !empty($row->AnoTurma) ? $row->AnoTurma . 'º Ano' : '-';
        $turma = !empty($row->nomeTurma) ? $row->nomeTurma : '-';
        $email = !empty($row->EmailAluno) ? $row->EmailAluno : '-';

        $html .= "<tr>";
        $html .= "<td><strong>" . htmlspecialchars($row->NomeAluno) . "</strong></td>";
        $html .= "<td>" . htmlspecialchars($email) . "</td>";
        $html .= "<td class='center'>" . htmlspecialchars($serie) . "</td>";
        $html .= "<td class='center'>" . htmlspecialchars($turma) . "</td>";
        $html .= "</tr>";
    }

    $html .= "</tbody>
        </table>

        <div class='footer-wrapper'>
            <table class='footer-table'>
                <tr>
                    <td>Governo do Estado do Ceará &bull; Secretaria da Educação &bull; EducaBiblio</td>
                    <td class='footer-right'>Relatório Institucional de Alunos</td>
                </tr>
            </table>
        </div>
    </body>
    </html>";
} else {
    $html = "<div style='font-family: Arial; padding: 20px; text-align: center; color: #666;'><h3>Não há alunos cadastrados a serem exibidos.</h3></div>";
}

use Dompdf\Dompdf;

require_once 'dompdf/autoload.inc.php';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->set_option('defaultFont', 'Helvetica');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Relatorio_Alunos.pdf", array("Attachment" => false));
