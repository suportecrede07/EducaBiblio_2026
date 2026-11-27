<?php

include 'config.php';

$sql = "SELECT
            livro.idLivro,
            livro.Tombo,
            livro.NomeLivro,
            livro.EditoraLivro,
            livro.IBSMLivro,
            livro.QuantidadeLivros,
            genero.NomeGenero AS GeneroLivro,
            idioma.Idioma AS IdiomaLivro,
            livro.FotoLivro,
            livro.CaminhoFotoLivro,
            livro.LocalLivro,
            livro.PrateleiraLivro,
            livro.ColunaLivro,
            autor.NomeAutor
        FROM
            livro
        LEFT JOIN
            genero ON livro.Genero_idGenero = genero.idGenero
        LEFT JOIN
            autor ON livro.autor_idAutor = autor.idAutor
        LEFT JOIN
            idioma ON livro.Idioma_idIdioma = idioma.idIdioma";

$res = $conn->query($sql);

if ($res->num_rows > 0) {
    $dataHora = date('d/m/Y H:i');
    $css = file_get_contents('../CSS/relatorios.css');

    $html = "<html>
    <head>
        <meta charset='UTF-8'>
        <title>EducaBiblio - Catálogo de Livros</title>
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
                        <div class='report-badge-title'>Acervo Geral de Livros</div>
                        <div class='report-date'>Emitido em: {$dataHora}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class='report-intro'>
            Abaixo estão listadas todas as <strong>obras cadastradas no acervo</strong>, com identificação de autor, tombo, gênero, editora e quantidade disponível.
        </div>

        <table class='data-table'>
            <thead>
                <tr>
                    <th style='width: 25%;'>Título da Obra</th>
                    <th style='width: 18%;'>Autor</th>
                    <th class='center' style='width: 10%;'>Tombo</th>
                    <th style='width: 15%;'>Editora</th>
                    <th style='width: 12%;'>ISBN / CDD</th>
                    <th style='width: 12%;'>Gênero</th>
                    <th class='center' style='width: 8%;'>Qtd</th>
                </tr>
            </thead>
            <tbody>";

    while ($row = $res->fetch_object()) {
        $autor = !empty($row->NomeAutor) ? $row->NomeAutor : '-';
        $tombo = !empty($row->Tombo) ? $row->Tombo : '-';
        $editora = !empty($row->EditoraLivro) ? $row->EditoraLivro : '-';
        $isbn = !empty($row->IBSMLivro) ? $row->IBSMLivro : '-';
        $genero = !empty($row->GeneroLivro) ? $row->GeneroLivro : '-';
        $qtd = isset($row->QuantidadeLivros) ? $row->QuantidadeLivros : '0';

        $html .= "<tr>";
        $html .= "<td><strong>" . htmlspecialchars($row->NomeLivro) . "</strong></td>";
        $html .= "<td>" . htmlspecialchars($autor) . "</td>";
        $html .= "<td class='center'>" . htmlspecialchars($tombo) . "</td>";
        $html .= "<td>" . htmlspecialchars($editora) . "</td>";
        $html .= "<td>" . htmlspecialchars($isbn) . "</td>";
        $html .= "<td>" . htmlspecialchars($genero) . "</td>";
        $html .= "<td class='center'><strong>" . htmlspecialchars($qtd) . "</strong></td>";
        $html .= "</tr>";
    }

    $html .= "</tbody>
        </table>

        <div class='footer-wrapper'>
            <table class='footer-table'>
                <tr>
                    <td>Governo do Estado do Ceará &bull; Secretaria da Educação &bull; EducaBiblio</td>
                    <td class='footer-right'>Relatório Institucional de Acervo</td>
                </tr>
            </table>
        </div>
    </body>
    </html>";
} else {
    $html = "<div style='font-family: Arial; padding: 20px; text-align: center; color: #666;'><h3>Não há livros cadastrados a serem exibidos.</h3></div>";
}

use Dompdf\Dompdf;

require_once 'dompdf/autoload.inc.php';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->set_option('defaultFont', 'Helvetica');
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("Relatorio_Livros.pdf", array("Attachment" => false));
