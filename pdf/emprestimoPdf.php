<?php

include 'config.php';

$sqlAlunos = "
    SELECT
        livro.NomeLivro AS TituloLivro,
        livro.Tombo,
        genero.NomeGenero,
        emprestimo.idEmprestimo,
        turma.NomeTurma,
        aluno.NomeAluno,
        turma.AnoTurma,
        DATE_FORMAT(emprestimo.DataEmprestimo, '%d/%m/%Y') AS DataEmprestimoFmt,
        IFNULL(DATE_FORMAT(devolucao.DataDevolucao, '%d/%m/%Y'), '-') AS DataDevolucaoFmt,
        emprestimo.Quantidade_emp,
        usuario.UserUsuario
    FROM emprestimo
    LEFT JOIN aluno ON emprestimo.aluno_idAluno = aluno.idAluno
    INNER JOIN livro ON emprestimo.livro_idLivro = livro.idLivro
    INNER JOIN genero ON livro.Genero_idGenero = genero.idGenero
    INNER JOIN turma ON aluno.Turma_idTurma = turma.IdTurma
    INNER JOIN usuario ON emprestimo.usuario_idUsuario = usuario.idUsuario
    LEFT JOIN devolucao ON emprestimo.idEmprestimo = devolucao.emprestimo_idEmprestimo";

$sqlProfessores = "
    SELECT
        livro.NomeLivro AS TituloLivro,
        livro.Tombo,
        genero.NomeGenero,
        emprestimo.idEmprestimo,
        'Professor' AS NomeTurma,
        prof.NomeProf AS NomeAluno,
        '' AS AnoTurma,
        DATE_FORMAT(emprestimo.DataEmprestimo, '%d/%m/%Y') AS DataEmprestimoFmt,
        IFNULL(DATE_FORMAT(devolucao.DataDevolucao, '%d/%m/%Y'), '-') AS DataDevolucaoFmt,
        emprestimo.Quantidade_emp,
        usuario.UserUsuario
    FROM emprestimo
    LEFT JOIN prof ON emprestimo.prof_idProf = prof.idProf
    INNER JOIN livro ON emprestimo.livro_idLivro = livro.idLivro
    INNER JOIN genero ON livro.Genero_idGenero = genero.idGenero
    INNER JOIN usuario ON emprestimo.usuario_idUsuario = usuario.idUsuario
    LEFT JOIN devolucao ON emprestimo.idEmprestimo = devolucao.emprestimo_idEmprestimo
    WHERE prof.idProf IS NOT NULL";

$sql = "($sqlAlunos) UNION ($sqlProfessores) ORDER BY DataEmprestimoFmt DESC";
$res = $conn->query($sql);

if ($res && $res->num_rows > 0) {
    $dataHora = date('d/m/Y H:i');
    $css = file_get_contents('../CSS/relatorios.css');

    $html = "<html>
    <head>
        <meta charset='UTF-8'>
        <title>EducaBiblio - Relatório de Empréstimos</title>
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
                        <div class='report-badge-title'>Registro Geral de Empréstimos</div>
                        <div class='report-date'>Emitido em: {$dataHora}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class='report-intro'>
            Abaixo estão apresentados todos os <strong>empréstimos realizados</strong> para alunos e professores registrados no sistema.
        </div>

        <table class='data-table'>
            <thead>
                <tr>
                    <th style='width: 25%;'>Título do Livro</th>
                    <th class='center' style='width: 10%;'>Tombo</th>
                    <th style='width: 22%;'>Leitor (Aluno / Professor)</th>
                    <th style='width: 15%;'>Turma / Vínculo</th>
                    <th class='center' style='width: 14%;'>Data Empréstimo</th>
                    <th class='center' style='width: 14%;'>Atendente</th>
                </tr>
            </thead>
            <tbody>";

    while ($row = $res->fetch_object()) {
        $turma = !empty($row->AnoTurma) ? $row->AnoTurma . 'º ' . $row->NomeTurma : $row->NomeTurma;
        $tombo = !empty($row->Tombo) ? $row->Tombo : '-';

        $html .= "<tr>";
        $html .= "<td><strong>" . htmlspecialchars($row->TituloLivro) . "</strong></td>";
        $html .= "<td class='center'>" . htmlspecialchars($tombo) . "</td>";
        $html .= "<td>" . htmlspecialchars($row->NomeAluno) . "</td>";
        $html .= "<td>" . htmlspecialchars($turma) . "</td>";
        $html .= "<td class='center'>" . htmlspecialchars($row->DataEmprestimoFmt) . "</td>";
        $html .= "<td class='center'>" . htmlspecialchars($row->UserUsuario) . "</td>";
        $html .= "</tr>";
    }

    $html .= "</tbody>
        </table>

        <div class='footer-wrapper'>
            <table class='footer-table'>
                <tr>
                    <td>Governo do Estado do Ceará &bull; Secretaria da Educação &bull; EducaBiblio</td>
                    <td class='footer-right'>Relatório Institucional de Circulação</td>
                </tr>
            </table>
        </div>
    </body>
    </html>";
} else {
    $html = "<div style='font-family: Arial; padding: 20px; text-align: center; color: #666;'><h3>Não há registros de empréstimos a serem exibidos.</h3></div>";
}

use Dompdf\Dompdf;

require_once 'dompdf/autoload.inc.php';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->set_option('defaultFont', 'Helvetica');
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("Relatorio_Emprestimos.pdf", array("Attachment" => false));
