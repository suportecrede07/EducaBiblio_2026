<?php

include 'config.php';
date_default_timezone_set('America/Fortaleza');

$sql = "SELECT
usuario.NomeUsuario,
usuario.idUsuario,
usuario.UserUsuario,
usuario.EmailUsuario,
usuario.camfoto
FROM usuario";

$res = $conn->query($sql);

if ($res->num_rows > 0) {
    $dataHora = date('d/m/Y H:i');
    $css = file_get_contents('../CSS/relatorios.css');

    $html = "<html>
    <head>
        <meta charset='UTF-8'>
        <title>EducaBiblio - Relatório de Usuários</title>
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
                        <div class='report-badge-title'>Relatório de Usuários</div>
                        <div class='report-date'>Emitido em: {$dataHora}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class='report-intro'>
            Abaixo estão listados todos os <strong>operadores e bibliotecários cadastrados</strong> com acesso ao sistema.
        </div>

        <table class='data-table'>
            <thead>
                <tr>
                    <th>Nome Completo</th>
                    <th style='width: 25%;'>Login / Usuário</th>
                    <th style='width: 35%;'>E-mail de Contato</th>
                </tr>
            </thead>
            <tbody>";

    while ($row = $res->fetch_object()) {
        $email = !empty($row->EmailUsuario) ? $row->EmailUsuario : '-';

        $html .= "<tr>";
        $html .= "<td><strong>" . htmlspecialchars($row->NomeUsuario) . "</strong></td>";
        $html .= "<td>" . htmlspecialchars($row->UserUsuario) . "</td>";
        $html .= "<td>" . htmlspecialchars($email) . "</td>";
        $html .= "</tr>";
    }

    $html .= "</tbody>
        </table>

        <div class='footer-wrapper'>
            <table class='footer-table'>
                <tr>
                    <td>Governo do Estado do Ceará &bull; Secretaria da Educação &bull; EducaBiblio</td>
                    <td class='footer-right'>Relatório Institucional de Usuários</td>
                </tr>
            </table>
        </div>
    </body>
    </html>";
} else {
    $html = "<div style='font-family: Arial; padding: 20px; text-align: center; color: #666;'><h3>Não há usuários cadastrados a serem exibidos.</h3></div>";
}

use Dompdf\Dompdf;

require_once 'dompdf/autoload.inc.php';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->set_option('defaultFont', 'Helvetica');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Relatorio_Usuarios.pdf", array("Attachment" => false));
