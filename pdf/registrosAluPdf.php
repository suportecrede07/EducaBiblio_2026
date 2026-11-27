<?php

include 'config.php';
date_default_timezone_set('America/Fortaleza');

$idAluno = isset($_GET['idAluno']) ? intval($_GET['idAluno']) : 0;
$where = $idAluno > 0 ? "WHERE aluno.idAluno = $idAluno" : "";

$sql = "SELECT
    emprestimo.idEmprestimo,
    aluno.NomeAluno AS Estudante,
    DATE_FORMAT(emprestimo.DataEmprestimo, '%d/%m/%Y') AS DataEmprestimoFormatada,
    IFNULL(DATE_FORMAT(devolucao.DataDevolucao, '%d/%m/%Y'), '-') AS DataDevolucaoFormatada,
    IFNULL(DATE_FORMAT(devolucao.DataDevolvida, '%d/%m/%Y'), '-') AS DataDevolvidaFormatada,
    CASE
        WHEN emprestimo.StatusEmprestimo = 1 THEN 'A prazo'
        WHEN emprestimo.StatusEmprestimo = 2 THEN 'Devolvido'
        WHEN emprestimo.StatusEmprestimo = 3 THEN 'Pendente'
        ELSE 'Não definido'
    END AS Estado,
    livro.NomeLivro,
    livro.Tombo
FROM emprestimo
LEFT JOIN aluno ON emprestimo.aluno_idAluno = aluno.idAluno
LEFT JOIN devolucao ON emprestimo.idEmprestimo = devolucao.emprestimo_idEmprestimo
LEFT JOIN livro ON emprestimo.livro_idLivro = livro.idLivro
$where
ORDER BY emprestimo.DataEmprestimo DESC";

$res = $conn->query($sql);

if ($res && $res->num_rows > 0) {
    $dataHora = date('d/m/Y H:i');
    $css = file_get_contents('../CSS/relatorios.css');

    // Identificar nome do aluno
    $firstRow = $res->fetch_object();
    $nomeAluno = !empty($firstRow->Estudante) ? $firstRow->Estudante : 'Todos os Alunos';
    $res->data_seek(0);

    $html = "<html>
    <head>
        <meta charset='UTF-8'>
        <title>EducaBiblio - Histórico do Aluno</title>
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
                        <div class='report-badge-title'>Histórico de Leitura</div>
                        <div class='report-date'>Emitido em: {$dataHora}</div>
                    </td>
                </tr>
            </table>
        </div>";

    if ($idAluno > 0) {
        $html .= "<div class='user-card'>
            Aluno: <span>" . htmlspecialchars($nomeAluno) . "</span>
        </div>";
    }

    $html .= "
        <table class='data-table'>
            <thead>
                <tr>
                    <th style='width: 32%;'>Título da Obra</th>
                    <th class='center' style='width: 12%;'>Tombo</th>
                    <th class='center' style='width: 18%;'>Data Empréstimo</th>
                    <th class='center' style='width: 18%;'>Data Devolução</th>
                    <th class='center' style='width: 20%;'>Situação</th>
                </tr>
            </thead>
            <tbody>";

    while ($row = $res->fetch_object()) {
        $tombo = !empty($row->Tombo) ? $row->Tombo : '-';
        $badgeClass = 'badge-prazo';
        if ($row->Estado === 'Devolvido') $badgeClass = 'badge-devolvido';
        if ($row->Estado === 'Pendente') $badgeClass = 'badge-pendente';

        $html .= "<tr>";
        $html .= "<td><strong>" . htmlspecialchars($row->NomeLivro) . "</strong></td>";
        $html .= "<td class='center'>" . htmlspecialchars($tombo) . "</td>";
        $html .= "<td class='center'>" . htmlspecialchars($row->DataEmprestimoFormatada) . "</td>";
        $html .= "<td class='center'>" . htmlspecialchars($row->DataDevolvidaFormatada) . "</td>";
        $html .= "<td class='center'><span class='badge-status {$badgeClass}'>" . htmlspecialchars($row->Estado) . "</span></td>";
        $html .= "</tr>";
    }

    $html .= "</tbody>
        </table>

        <div class='footer-wrapper'>
            <table class='footer-table'>
                <tr>
                    <td>Governo do Estado do Ceará &bull; Secretaria da Educação &bull; EducaBiblio</td>
                    <td class='footer-right'>Ficha Individual de Empréstimos</td>
                </tr>
            </table>
        </div>
    </body>
    </html>";
} else {
    $html = "<div style='font-family: Arial; padding: 20px; text-align: center; color: #666;'><h3>Não há registros de empréstimos para este leitor.</h3></div>";
}

use Dompdf\Dompdf;

require_once 'dompdf/autoload.inc.php';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->set_option('defaultFont', 'Helvetica');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Historico_Aluno.pdf", array("Attachment" => false));
