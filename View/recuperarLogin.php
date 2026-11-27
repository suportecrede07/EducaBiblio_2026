<?php
// Inicia a sessão para mensagens
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se recebeu o token de recuperação pela URL
$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$modoRedefinicao = !empty($token);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="../ArquivosExternos/icons.js"></script>
  <link rel="stylesheet" href="../CSS/login.css?v=<?php echo time(); ?>" />
  <link rel="shortcut icon" href="../img/icon1.png" type="image/x-icon">
  <title>Recuperação de Acesso - EducaBiblio</title>

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

    .opcoes-alterar {
      width: 100%;
      max-width: 380px;
      display: flex;
      flex-direction: column;
      gap: 8px;
      margin-bottom: 12px;
      text-align: left;
    }

    .opcoes-alterar label {
      font-size: 0.9rem;
      color: #494949;
      font-weight: 500;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .campo-bloco {
      width: 100%;
      max-width: 380px;
      transition: all 0.3s ease;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="forms-container" style="display: flex; justify-content: center; align-items: center;">
      <div class="recuperar-container">
        
        <h2 class="tituloPrincipal">
          <?php echo $modoRedefinicao ? "Redefinir Acesso" : "Recuperar Conta"; ?>
        </h2>

        <!-- Mensagens do backend -->
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

        <?php if (!$modoRedefinicao): ?>
          <!-- 1. Enviar link para o e-mail -->
          <p class="subtituloRecuperar">
            Informe seu e-mail para receber o link de recuperação de usuário e senha.
          </p>

          <form action="../Router/log_rotas.php" method="POST" style="width: 100%; padding: 0;">
            <input type="hidden" name="action" value="SolicitarRecuperacao" />

            <div class="input-field">
              <i class="fas fa-envelope"></i>
              <input type="email" name="EmailUsuario" id="EmailUsuario" placeholder="E-mail cadastrado" required autocomplete="off" />
            </div>

            <input type="submit" value="Enviar Link" class="btn solid" style="margin-top: 15px;" />
          </form>

        <?php else: ?>
          <!-- 2. Alterar usuário e/ou senha -->
          <p class="subtituloRecuperar">
            Selecione o que deseja alterar:
          </p>

          <form action="../Router/log_rotas.php" method="POST" id="formRedefinicao" style="width: 100%; padding: 0;">
            <input type="hidden" name="action" value="RedefinirAcesso" />
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>" />

            <div class="opcoes-alterar">
              <label>
                <input type="checkbox" id="chkAlterarUsuario" onchange="alternarCampos()" checked />
                Alterar Usuário
              </label>
              <label>
                <input type="checkbox" id="chkAlterarSenha" onchange="alternarCampos()" checked />
                Alterar Senha
              </label>
            </div>

            <!-- Campo Novo Usuário -->
            <div class="campo-bloco" id="blocoUsuario">
              <div class="input-field">
                <i class="fas fa-user-alt"></i>
                <input type="text" name="NovoUserUsuario" id="NovoUserUsuario" placeholder="Novo Usuário" autocomplete="off" />
              </div>
            </div>

            <!-- Campos Nova Senha -->
            <div class="campo-bloco" id="blocoSenha">
              <div class="input-field">
                <i class="fas fa-lock"></i>
                <input type="password" name="NovaSenhaUsuario" id="NovaSenhaUsuario" placeholder="Nova Senha" autocomplete="off" />
              </div>

              <div class="input-field">
                <i class="fas fa-check-circle"></i>
                <input type="password" name="ConfirmarSenha" id="ConfirmarSenha" placeholder="Confirmar Senha" autocomplete="off" />
              </div>
            </div>

            <input type="submit" value="Salvar" class="btn solid" style="margin-top: 15px;" />
          </form>

          <script>
            function alternarCampos() {
              const chkUser = document.getElementById('chkAlterarUsuario');
              const chkSenha = document.getElementById('chkAlterarSenha');
              document.getElementById('blocoUsuario').style.display = chkUser.checked ? 'block' : 'none';
              document.getElementById('blocoSenha').style.display = chkSenha.checked ? 'block' : 'none';
            }

            document.getElementById('formRedefinicao')?.addEventListener('submit', function(e) {
              const chkSenha = document.getElementById('chkAlterarSenha');
              if (chkSenha.checked) {
                const senha = document.getElementById('NovaSenhaUsuario').value;
                const confirma = document.getElementById('ConfirmarSenha').value;
                if (!senha || senha.length < 4) {
                  alert('A senha deve ter no mínimo 4 caracteres.');
                  e.preventDefault();
                  return;
                }
                if (senha !== confirma) {
                  alert('As senhas não coincidem.');
                  e.preventDefault();
                  return;
                }
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
