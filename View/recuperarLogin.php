<?php
// Inicia a sessão para mensagens
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o e-mail foi validado na sessão ou recebido para redefinição
$emailValidado = $_SESSION['recuperar_email'] ?? '';
$etapa = isset($_SESSION['recuperar_etapa']) ? $_SESSION['recuperar_etapa'] : 'identificacao';

// Se o usuário clicar em reiniciar/voltar
if (isset($_GET['nova_consulta'])) {
    unset($_SESSION['recuperar_email']);
    unset($_SESSION['recuperar_etapa']);
    header("Location: recuperarLogin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="../ArquivosExternos/icons.js"></script>
  <link rel="stylesheet" href="../CSS/login.css?v=<?php echo time(); ?>" />
  <link rel="shortcut icon" href="../img/icon1.png" type="image/x-icon">
  <title>Recuperação de Senha - EducaBiblio</title>

  <style>
    .container::before {
      display: none !important;
    }

    .container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: var(--branco);
      overflow-y: auto;
      padding: 20px 0;
    }

    .forms-container {
      position: relative !important;
      width: 100% !important;
      height: auto !important;
      top: auto !important;
      left: auto !important;
      display: flex !important;
      justify-content: center !important;
      align-items: center !important;
      z-index: 10;
    }

    .recuperar-container {
      width: 100%;
      max-width: 600px;
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 1rem 1.5rem;
      text-align: center;
    }

    .logos-container {
      display: flex;
      align-items: center;
      justify-content: center;
      flex-wrap: nowrap;
      gap: 25px;
      margin-top: 25px;
      margin-bottom: 10px;
      width: 100%;
    }

    .logos-container img {
      max-width: 110px;
      max-height: 65px;
      width: auto;
      height: auto;
      object-fit: contain;
      margin: 0;
    }

    .subtituloRecuperar {
      font-size: 0.95rem;
      color: #666;
      text-align: center;
      margin-bottom: 20px;
      line-height: 1.4;
      max-width: 380px;
    }

    .alert-msg {
      width: 100%;
      max-width: 380px;
      padding: 10px 14px;
      border-radius: 8px;
      margin-bottom: 14px;
      font-size: 0.9rem;
      text-align: center;
      font-weight: 500;
      box-sizing: border-box;
    }

    .alert-success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .alert-danger {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    .info-usuario-encontrado {
      background-color: #e8f4fd;
      border: 1px solid #bee5eb;
      color: #0c5460;
      padding: 10px 14px;
      border-radius: 8px;
      margin-bottom: 15px;
      width: 100%;
      max-width: 380px;
      font-size: 0.9rem;
      box-sizing: border-box;
    }

    .voltar-login {
      margin-top: 15px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      color: #228b22;
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 600;
      transition: color 0.2s ease;
    }

    .voltar-login:hover {
      color: #1b6d1b;
      text-decoration: underline;
    }

    .input-field {
      position: relative;
    }

    .input-field .toggle-password {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #acacac;
    }

    .input-field .toggle-password:hover {
      color: #333;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="forms-container">
      <div class="recuperar-container">
        
        <h2 class="tituloPrincipal">
          <?php echo ($etapa === 'redefinir') ? "Nova Senha" : "Recuperar Senha"; ?>
        </h2>

        <!-- Mensagens de feedback -->
        <?php if (isset($_SESSION['msg_sucesso'])): ?>
          <div class="alert-msg alert-success">
            <?php echo $_SESSION['msg_sucesso']; unset($_SESSION['msg_sucesso']); ?>
          </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['msg_erro'])): ?>
          <div class="alert-msg alert-danger">
            <?php echo $_SESSION['msg_erro']; unset($_SESSION['msg_erro']); ?>
          </div>
        <?php endif; ?>

        <?php if ($etapa !== 'redefinir'): ?>
          <!-- ETAPA 1: Verificação de E-mail no banco -->
          <p class="subtituloRecuperar">
            Digite o e-mail cadastrado na sua conta para verificar os dados no sistema.
          </p>

          <form action="../Router/log_rotas.php" method="POST" style="width: 100%; padding: 0; display: flex; flex-direction: column; align-items: center;">
            <input type="hidden" name="action" value="VerificarEmailRecuperacao" />

            <div class="input-field">
              <i class="fas fa-envelope"></i>
              <input type="email" name="EmailUsuario" id="EmailUsuario" placeholder="E-mail cadastrado" required autocomplete="off" />
            </div>

            <input type="submit" value="Continuar" class="btn solid" style="margin-top: 15px;" />
          </form>

        <?php else: ?>
          <!-- ETAPA 2: Redefinição de Senha -->
          <div class="info-usuario-encontrado">
            Conta encontrada para: <strong><?php echo htmlspecialchars($emailValidado); ?></strong>
            <br>
            <a href="recuperarLogin.php?nova_consulta=1" style="color: #0b5885; font-size: 0.82rem; text-decoration: underline;">Trocar e-mail</a>
          </div>

          <p class="subtituloRecuperar">
            Crie sua nova senha para acessar a plataforma.
          </p>

          <form action="../Router/log_rotas.php" method="POST" id="formRedefinirSenha" style="width: 100%; padding: 0; display: flex; flex-direction: column; align-items: center;">
            <input type="hidden" name="action" value="SalvarNovaSenha" />

            <!-- Campo Nova Senha -->
            <div class="input-field">
              <i class="fas fa-lock"></i>
              <input type="password" name="NovaSenhaUsuario" id="NovaSenhaUsuario" placeholder="Nova Senha" required autocomplete="off" />
              <i class="fas fa-eye toggle-password" onclick="toggleSenha('NovaSenhaUsuario', this)"></i>
            </div>

            <!-- Campo Confirmar Senha -->
            <div class="input-field">
              <i class="fas fa-check-circle"></i>
              <input type="password" name="ConfirmarSenha" id="ConfirmarSenha" placeholder="Confirmar Nova Senha" required autocomplete="off" />
              <i class="fas fa-eye toggle-password" onclick="toggleSenha('ConfirmarSenha', this)"></i>
            </div>

            <input type="submit" value="Salvar Nova Senha" class="btn solid" style="margin-top: 15px;" />
          </form>

          <script>
            function toggleSenha(inputId, icon) {
              const input = document.getElementById(inputId);
              if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
              } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
              }
            }

            document.getElementById('formRedefinirSenha')?.addEventListener('submit', function(e) {
              const senha = document.getElementById('NovaSenhaUsuario').value;
              const confirma = document.getElementById('ConfirmarSenha').value;

              if (senha.length < 4) {
                alert('A senha deve ter pelo menos 4 caracteres.');
                e.preventDefault();
                return;
              }

              if (senha !== confirma) {
                alert('As senhas não coincidem. Digite novamente.');
                e.preventDefault();
                return;
              }
            });
          </script>
        <?php endif; ?>

        <a href="login.php" class="voltar-login">
          <i class="fas fa-arrow-left"></i> Voltar para o Login
        </a>

        <div class="logos-container" style="margin-top: 25px;">
          <img src="../img/crede07.1.png" alt="Logo Crede 07" />
          <img src="../img/logoCE.png" alt="Logo Ceará" />
          <img src="../img/logoCaninde.png" alt="Logo Canindé" />
        </div>

        <p class="direitos">© TODOS OS DIREITOS RESERVADOS - EDUCABIBLIO</p>
      </div>
    </div>
  </div>
</body>

</html>
