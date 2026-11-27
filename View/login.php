<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="../ArquivosExternos/icons.js"></script>
  <link rel="stylesheet" href="../CSS/login.css?v=<?php echo time(); ?>" />
  <link rel="shortcut icon" href="../img/icon1.png" type="image/x-icon">
  <title>EducaBiblio</title>
</head>
<style>
  .error-message {
    color: red;
    font-weight: bold;
  }
  .esqueci-senha {
    width: 100% !important;
    max-width: 380px !important;
    margin: 4px 0 12px 0 !important;
    display: flex !important;
    justify-content: flex-end !important;
    text-align: right !important;
  }
  .esqueci-senha a,
  .link-senha {
    color: #228b22 !important;
    text-decoration: none !important;
    font-size: 0.85rem !important;
    font-weight: 500 !important;
    cursor: pointer !important;
  }
  .esqueci-senha a:hover,
  .link-senha:hover {
    color: #1b6d1b !important;
    text-decoration: underline !important;
  }
</style>

<body>
  <div class="container">
    <div class="forms-container">
      <div class="signin-signup">
        <form action="../Router/log_rotas.php" method="post" class="sign-in-form">
          <br><br>
          <h2 class="tituloPrincipal">Login</h2>
          <?php
          if (isset($_GET['erro']) && $_GET['erro'] == 1) {
            echo '<div class="error-message">Nome de usuário ou senha incorretos.</div>';
          }
          if (isset($_GET['redefinido']) && $_GET['redefinido'] == 1) {
            $msgSucesso = $_SESSION['sucesso_login'] ?? 'Senha redefinida com sucesso! Faça login com a nova senha.';
            unset($_SESSION['sucesso_login']);
            echo '<div style="color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-weight: 500; text-align: center; max-width: 380px; width: 100%; box-sizing: border-box;">' . htmlspecialchars($msgSucesso) . '</div>';
          }
          ?>

          <div class="input-field">
            <i class="fas fa-user-alt"></i>
            <input type="text" name="UserUsuario" id="UserUsuario" placeholder="Usuário" autocomplete="off" />
          </div>
          <div class="input-field">
            <i class="fas fa-lock"></i>
            <input type="password" name="SenhaUsuario" id="SenhaUsuario" placeholder="Senha" autocomplete="off" />
            <i class="fas fa-eye toggle-password-icon" id="togglePassword"></i>
          
          </div>
         <p class="esqueci-senha">
  <a href="recuperarLogin.php" class="link-senha">Esqueci minha senha ou usuário</a>
</p>

          <input type="submit" name="action" value="Entrar" class="btn solid" />

          <div class="logos-container">
            <img src="../img/crede07.1.png" alt="Logo 1" />
            <img src="../img/logoCE.png" alt="Logo 2" />
            <img src="../img/logoCaninde.png" alt="Logo 3" />
          </div>
          <p class="direitos"> © TODOS OS DIREITOS RESERVADOS - EDUCABIBLIO </p>
        </form>

        <form action="../Router/log_rotas.php" method="post" class="sign-up-form">
          <br><br>
          <h2 class="tituloPrincipal">Cadastro</h2>
          <div class="input-field">
            <i class="fas fa-user-friends"></i>
            <input type="text" id="NomeUsuario" name="NomeUsuario" placeholder="Nome" autocomplete="off" />
          </div>
          <div class="input-field">
            <i class="fas fa-user-alt"></i>
            <input type="text" id="UserUsuario" name="UserUsuario" placeholder="Usuário" autocomplete="off" />
          </div>
          <div class="input-field">
            <i class="fas fa-comments"></i>
            <input type="email" id="EmailUsuario" name="EmailUsuario" placeholder="E-mail" autocomplete="off" />
          </div>
          <div class="input-field">
            <i class="fas fa-lock"></i>
            <input type="password" id="SenhaUsuario" name="SenhaUsuario" placeholder="Senha" autocomplete="off" />
          </div>
          <input type="submit" name="action" value="Cadastrar" class="btn" />


          <div class="logos-container">
            <img src="../img/crede07.1.png" alt="Logo 1" />
            <img src="../img/logoCE.png" alt="Logo 2" />
            <img src="../img/logoCaninde.png" alt="Logo 3" />
          </div>

          <p class="direitos">© TODOS OS DIREITOS RESERVADOS - EDUCABIBLIO</p>
        </form>


      </div>
    </div>

    <div class="panels-container">
      <div class="panel left-panel">
        <div class="content">
          <h3>Não possui um cadastro?</h3>
          <p>Seja muito bem-vindo ao EducaBiblio, um sistema de biblioteca. Caso ainda não tenha uma conta, clique
            aqui para realizar o seu cadastro.</p>
          <button class="btn transparent" id="sign-up-btn">
            Cadastrar
          </button>
        </div>
        <img src="../img/livrosLogin.png" class="image" alt="Imagem de livros em uma celular na página de login" />
      </div>
      <div class="panel right-panel">
        <div class="content">
          <h3>Já possui um cadastro?</h3>
          <p>
            Se você é o bibliotecário responsável pelo sistema, clique no botão abaixo para acessar sua conta de
            administração.
          </p>
          <button class="btn transparent" id="sign-in-btn">
            Entrar
          </button>
        </div>
        <img src="../img/livrosLogin2.png" class="image" alt="Imagem de livros em um celular na página de login" />
      </div>
    </div>
  </div>
  <script src="../JS/login.js"></script>
  <script>
    // Função para esconder a mensagem de erro após 5 segundos
    setTimeout(function() {
      var errorMessage = document.querySelector('.error-message');
      if (errorMessage) {
        errorMessage.style.display = 'none';
      }
    }, 5000); // Tempo em milissegundos (5 segundos)
  </script>
</body>

</html>