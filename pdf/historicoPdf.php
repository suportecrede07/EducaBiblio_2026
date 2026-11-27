<?php

include 'config.php';
date_default_timezone_set('America/Fortaleza');

$sql = "SELECT
emprestimo.idEmprestimo,
aluno.NomeAluno AS Estudante,
prof.NomeProf AS Professor,
DATE_FORMAT(emprestimo.DataEmprestimo, '%d/%m/%Y') AS DataEmprestimoFormatada,
IFNULL(DATE_FORMAT(devolucao.DataDevolucao, '%d/%m/%Y'), '-') AS DataDevolucaoFormatada,
IFNULL(DATE_FORMAT(devolucao.DataDevolvida, '%d/%m/%Y'), '-') AS DataDevolvidaFormatada,
CASE
    WHEN emprestimo.StatusEmprestimo = 1 THEN 'A prazo'
    WHEN emprestimo.StatusEmprestimo = 3 THEN 'Pendente'
    WHEN emprestimo.StatusEmprestimo = 2 THEN 'Devolvido'
    ELSE 'Não definido'
END AS Estado,
livro.NomeLivro
FROM emprestimo
LEFT JOIN aluno ON emprestimo.aluno_idAluno = aluno.idAluno
LEFT JOIN prof ON emprestimo.prof_idProf = prof.idProf
LEFT JOIN devolucao ON emprestimo.idEmprestimo = devolucao.emprestimo_idEmprestimo
LEFT JOIN livro ON emprestimo.livro_idLivro = livro.idLivro
ORDER BY emprestimo.DataEmprestimo DESC";

$res = $conn->query($sql);

if ($res && $res->num_rows > 0) {
    $dataHora = date('d/m/Y H:i');
    $css = file_get_contents('../CSS/relatorios.css');

    $html = "<html>
    <head>
        <meta charset='UTF-8'>
        <title>EducaBiblio - Histórico de Empréstimos</title>
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
                        <div class='report-badge-title'>Histórico de Circulação</div>
                        <div class='report-date'>Emitido em: {$dataHora}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class='report-intro'>
            Histórico completo de <strong>todas as movimentações e devoluções</strong> efetuadas na biblioteca.
        </div>

        <table class='data-table'>
            <thead>
                <tr>
                    <th style='width: 25%;'>Leitor</th>
                    <th style='width: 30%;'>Livro</th>
                    <th class='center' style='width: 15%;'>Empréstimo</th>
                    <th class='center' style='width: 15%;'>Devolução Real</th>
                    <th class='center' style='width: 15%;'>Situação</th>
                </tr>
            </thead>
            <tbody>";

    while ($row = $res->fetch_object()) {
        $leitor = !empty($row->Estudante) ? $row->Estudante : (!empty($row->Professor) ? $row->Professor : '-');
        $badgeClass = 'badge-prazo';
        if ($row->Estado === 'Devolvido') $badgeClass = 'badge-devolvido';
        if ($row->Estado === 'Pendente') $badgeClass = 'badge-pendente';

        $html .= "<tr>";
        $html .= "<td>" . htmlspecialchars($leitor) . "</td>";
        $html .= "<td><strong>" . htmlspecialchars($row->NomeLivro) . "</strong></td>";
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
                    <td class='footer-right'>Relatório Institucional de Histórico</td>
                </tr>
            </table>
        </div>
    </body>
    </html>";
} else {
    $html = "<div style='font-family: Arial; padding: 20px; text-align: center; color: #666;'><h3>Não há histórico a ser exibido.</h3></div>";
}

use Dompdf\Dompdf;

require_once 'dompdf/autoload.inc.php';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->set_option('defaultFont', 'Helvetica');
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("Historico_Emprestimos.pdf", array("Attachment" => false));
