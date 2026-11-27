<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';

$aluno_id = isset($_GET['aluno']) ? intval($_GET['aluno']) : 0;
$sql = "
SELECT
    a.NomeAluno AS Estudante,
    l.NomeLivro,
    l.EditoraLivro,
    l.Tombo,
    l.IBSMLivro,
    g.NomeGenero AS GeneroLivro,
    i.Idioma AS IdiomaLivro,
    au.NomeAutor,
    DATE_FORMAT(e.DataEmprestimo, '%d/%m/%Y') AS DataEmprestimoFormatada,
    IFNULL(DATE_FORMAT(d.DataDevolvida, '%d/%m/%Y'), '-') AS DataDevolvidaFormatada,
    CASE
        WHEN e.StatusEmprestimo = 1 THEN 'A prazo'
        WHEN e.StatusEmprestimo = 2 THEN 'Devolvido'
        WHEN e.StatusEmprestimo = 3 THEN 'Pendente'
        ELSE 'Não definido'
    END AS Estado
FROM emprestimo e
JOIN aluno a ON e.aluno_idAluno = a.idAluno
JOIN livro l ON e.livro_idLivro = l.idLivro
LEFT JOIN devolucao d ON e.idEmprestimo = d.emprestimo_idEmprestimo
LEFT JOIN genero g ON l.Genero_idGenero = g.idGenero
LEFT JOIN autor au ON l.autor_idAutor = au.idAutor
LEFT JOIN idioma i ON l.Idioma_idIdioma = i.idIdioma
WHERE e.aluno_idAluno = $aluno_id
ORDER BY e.DataEmprestimo DESC
";

$res = $conn->query($sql);

if ($res && $res->num_rows > 0) {
    $dadosAluno = $res->fetch_object();
    $nomeAluno = $dadosAluno->Estudante;
    $res->data_seek(0);

    $dataHora = date('d/m/Y H:i');
    $css = file_get_contents('../CSS/relatorios.css');

    $html = "<html>
    <head>
        <meta charset='UTF-8'>
        <title>EducaBiblio - Histórico de Leitura</title>
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
                        <div class='report-badge-title'>Histórico Individual</div>
                        <div class='report-date'>Emitido em: {$dataHora}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class='user-card'>
            Aluno(a): <span>" . htmlspecialchars($nomeAluno) . "</span>
        </div>

        <table class='data-table'>
            <thead>
                <tr>
                    <th style='width: 30%;'>Livro</th>
                    <th style='width: 20%;'>Autor</th>
                    <th style='width: 15%;'>Gênero</th>
                    <th class='center' style='width: 12%;'>Empréstimo</th>
                    <th class='center' style='width: 12%;'>Devolvido</th>
                    <th class='center' style='width: 11%;'>Status</th>
                </tr>
            </thead>
            <tbody>";

    while ($row = $res->fetch_object()) {
        $autor = !empty($row->NomeAutor) ? $row->NomeAutor : '-';
        $genero = !empty($row->GeneroLivro) ? $row->GeneroLivro : '-';
        $badgeClass = 'badge-prazo';
        if ($row->Estado === 'Devolvido') $badgeClass = 'badge-devolvido';
        if ($row->Estado === 'Pendente') $badgeClass = 'badge-pendente';

        $html .= "<tr>";
        $html .= "<td><strong>" . htmlspecialchars($row->NomeLivro) . "</strong></td>";
        $html .= "<td>" . htmlspecialchars($autor) . "</td>";
        $html .= "<td>" . htmlspecialchars($genero) . "</td>";
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
                    <td class='footer-right'>Ficha Individual de Leitura</td>
                </tr>
            </table>
        </div>
    </body>
    </html>";
} else {
    $html = "<div style='font-family: Arial; padding: 20px; text-align: center; color: #666;'><h3>Não há histórico de leitura registrado para este aluno.</h3></div>";
}

use Dompdf\Dompdf;

require_once 'dompdf/autoload.inc.php';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->set_option('defaultFont', 'Helvetica');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Historico_Leitura.pdf", array("Attachment" => false));
