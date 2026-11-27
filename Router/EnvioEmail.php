<?php

require_once __DIR__ . '/../lib/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer-master/src/SMTP.php';
require_once __DIR__ . '/../lib/PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Função responsável por enviar o e-mail de confirmação de senha redefinida.
 *
 * @param string $destinatarioEmail E-mail do usuário
 * @param string $destinatarioNome Nome ou identificador do usuário (opcional)
 * @return array ['sucesso' => bool, 'erro' => string|null]
 */
function enviarEmailSenhaRedefinida($destinatarioEmail, $destinatarioNome = '')
{
    if (!filter_var($destinatarioEmail, FILTER_VALIDATE_EMAIL)) {
        return ['sucesso' => false, 'erro' => 'E-mail inválido.'];
    }

    $mail = new PHPMailer(true);

    try {
        // Configuração SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp-relay.brevo.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'c56fd3001@smtp-brevo.com';
        $config = require __DIR__ . '/../config/segredos.php';
        $mail->Password = $config['brevo_smtp_password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->SMTPDebug  = 0; // Desativa debug visual para não quebrar redirecionamento

        // Configurações de remetente e destinatário
        $mail->CharSet = 'UTF-8';
        $mail->setFrom('admineducbibriosuporte@gmail.com', 'EducaBiblio');
        $mail->addAddress($destinatarioEmail, $destinatarioNome);

        // Conteúdo da mensagem
        $mail->isHTML(true);
        $mail->Subject = 'Sua senha foi redefinida com sucesso - EducaBiblio';

        $cumprimento = !empty($destinatarioNome) ? htmlspecialchars($destinatarioNome) : 'Usuário';

        $mail->Body = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px;">
                <div style="text-align: center; margin-bottom: 20px;">
                    <h2 style="color: #228b22; margin: 0;">EducaBiblio</h2>
                    <p style="color: #666; font-size: 14px;">Sistema de Gestão Bibliotecária</p>
                </div>
                <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
                <p style="font-size: 16px; color: #333;">Olá, <strong>' . $cumprimento . '</strong>!</p>
                <p style="font-size: 15px; color: #555; line-height: 1.5;">
                    Informamos que a senha da sua conta no <strong>EducaBiblio</strong> foi redefinida recentemente com sucesso.
                </p>
                <div style="background-color: #f7f9fa; border-left: 4px solid #228b22; padding: 12px 16px; margin: 20px 0; font-size: 14px; color: #555;">
                    Se foi você quem fez essa alteração, nenhuma ação adicional é necessária. Você já pode acessar o sistema normalmente com sua nova senha.
                </div>
                <p style="font-size: 14px; color: #888; margin-top: 25px;">
                    Caso você não tenha solicitado essa alteração, entre em contato imediatamente com a administração da biblioteca.
                </p>
                <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
                <p style="font-size: 12px; color: #aaa; text-align: center;">
                    © ' . date('Y') . ' EducaBiblio - Todos os direitos reservados.
                </p>
            </div>
        ';

        $mail->AltBody = "Olá!\n\nA senha da sua conta no EducaBiblio foi redefinida com sucesso.\nSe foi você quem fez a alteração, nenhuma ação adicional é necessária.\nCaso não tenha sido você, entre em contato imediatamente com a administração.";

        $mail->send();
        return ['sucesso' => true, 'erro' => null];
    } catch (Exception $e) {
        error_log("Erro ao enviar email pelo EnvioEmail.php: " . $mail->ErrorInfo);
        return ['sucesso' => false, 'erro' => $mail->ErrorInfo];
    }
}

// Suporte para requisição direta via POST (se chamado diretamente pelo formulário de solicitação)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'SolicitarRecuperacao') {
    $EmailUser = trim($_POST['EmailUsuario'] ?? '');
    $res = enviarEmailSenhaRedefinida($EmailUser);

    if ($res['sucesso']) {
        echo '<h3>E-mail enviado com sucesso!</h3>';
    } else {
        echo '<h3>Erro ao enviar e-mail:</h3>';
        echo htmlspecialchars($res['erro']);
    }
}
