<?php

include 'config.php';

$sqlAlunos = "SELECT emprestimo.idEmprestimo, aluno.NomeAluno AS Leitor, turma.NomeTurma AS Turma, turma.AnoTurma, livro.NomeLivro AS Livro, emprestimo.StatusEmprestimo AS Estado, IFNULL(DATE_FORMAT(devolucao.DataDevolucao, '%d/%m/%Y'), '-') AS DataDevolucaoFmt
FROM emprestimo
INNER JOIN aluno ON emprestimo.aluno_idAluno = aluno.idAluno
INNER JOIN livro ON emprestimo.livro_idLivro = livro.idLivro
INNER JOIN turma ON aluno.Turma_idTurma = turma.IdTurma
LEFT JOIN devolucao ON emprestimo.idEmprestimo = devolucao.emprestimo_idEmprestimo WHERE emprestimo.StatusEmprestimo = 3";

$sqlProfessores = "SELECT emprestimo.idEmprestimo, prof.NomeProf AS Leitor, 'Professor' AS Turma, '' AS AnoTurma, livro.NomeLivro AS Livro, emprestimo.StatusEmprestimo AS Estado, IFNULL(DATE_FORMAT(devolucao.DataDevolucao, '%d/%m/%Y'), '-') AS DataDevolucaoFmt
FROM emprestimo
INNER JOIN prof ON emprestimo.prof_idProf = prof.idProf
INNER JOIN livro ON emprestimo.livro_idLivro = livro.idLivro
LEFT JOIN devolucao ON emprestimo.idEmprestimo = devolucao.emprestimo_idEmprestimo WHERE emprestimo.StatusEmprestimo = 3";

$sql = "($sqlAlunos) UNION ($sqlProfessores)";
$res = $conn->query($sql);

if ($res && $res->num_rows > 0) {
    $dataHora = date('d/m/Y H:i');
    $css = file_get_contents('../CSS/relatorios.css');

    $html = "<html>
    <head>
        <meta charset='UTF-8'>
        <title>EducaBiblio - Pendências de Empréstimo</title>
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
                        <div class='report-badge-title' style='color: #b91c1c; background-color: #fee2e2; border-color: #fecaca;'>Relatório de Pendências</div>
                        <div class='report-date'>Emitido em: {$dataHora}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class='report-intro' style='border-left-color: #dc2626;'>
            Atenção: Abaixo estão listados todos os <strong>empréstimos em atraso / pendentes de devolução</strong>.
        </div>

        <table class='data-table'>
            <thead>
                <tr style='background-color: #b91c1c;'>
                    <th style='background-color: #b91c1c; border-color: #b91c1c; width: 35%;'>Título do Livro</th>
                    <th style='background-color: #b91c1c; border-color: #b91c1c; width: 28%;'>Leitor (Aluno / Professor)</th>
                    <th style='background-color: #b91c1c; border-color: #b91c1c; width: 17%;'>Turma / Vínculo</th>
                    <th class='center' style='background-color: #b91c1c; border-color: #b91c1c; width: 20%;'>Venceu em</th>
                </tr>
            </thead>
            <tbody>";

    while ($row = $res->fetch_object()) {
        $turma = !empty($row->AnoTurma) ? $row->AnoTurma . 'º ' . $row->Turma : $row->Turma;

        $html .= "<tr>";
        $html .= "<td><strong>" . htmlspecialchars($row->Livro) . "</strong></td>";
        $html .= "<td>" . htmlspecialchars($row->Leitor) . "</td>";
        $html .= "<td>" . htmlspecialchars($turma) . "</td>";
        $html .= "<td class='center'><span class='badge-status badge-pendente'>" . htmlspecialchars($row->DataDevolucaoFmt) . "</span></td>";
        $html .= "</tr>";
    }

    $html .= "</tbody>
        </table>

        <div class='footer-wrapper'>
            <table class='footer-table'>
                <tr>
                    <td>Governo do Estado do Ceará &bull; Secretaria da Educação &bull; EducaBiblio</td>
                    <td class='footer-right'>Relatório Institucional de Pendências</td>
                </tr>
            </table>
        </div>
    </body>
    </html>";
} else {
    $html = "<div style='font-family: Arial; padding: 20px; text-align: center; color: #2e7d32;'><h3>Parabéns! Não há empréstimos pendentes ou em atraso.</h3></div>";
}

use Dompdf\Dompdf;

require_once 'dompdf/autoload.inc.php';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->set_option('defaultFont', 'Helvetica');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Relatorio_Pendencias.pdf", array("Attachment" => false));
