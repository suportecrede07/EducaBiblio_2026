<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once('../Controller/CConexao.php'); // Verifique o caminho correto do arquivo

    if (isset($_POST['action']) && $_POST['action'] === 'Cadastrar') {
        include('../Controller/CCad_usu.php'); // Verifique o caminho correto do arquivo
        $usuarioController = new UsuarioController();
        $usuarioController->salvarUsuario($_POST);
        header("Location: ../View/login.php");
        exit();
    } elseif (isset($_POST['action']) && $_POST['action'] === 'Entrar') {
        // Obtenha os dados do formulário
        $username = $_POST['UserUsuario'];
        $password = $_POST['SenhaUsuario'];

        $conexaoObj = new CConexao();
        $conn = $conexaoObj->getConnection();

        if ($conn) {
            if (verificarCredenciais($conn, $username, $password)) {
                $_SESSION['usuario_logado'] = true;

                // Recupere o nome do usuário do banco de dados (substitua com sua consulta SQL)
                $nomeDoUsuario = obterNomeDoUsuario($conn, $username);
                $_SESSION['nomeDoUsuario'] = $nomeDoUsuario;

                // Recupere o ID do usuário do banco de dados (substitua com sua consulta SQL)
                $idUsuario = obterIdDoUsuario($conn, $username);
                $_SESSION['id_usuario'] = $idUsuario;

                header("Location: ../View/inicio.php");
                exit();
            } else {
                $_SESSION['erro_login'] = "Nome de usuário ou senha incorretos.";
                header("Location: ../View/login.php?erro=1");
                exit();
            }
        } else {
            echo "Não foi possível conectar ao banco de dados.";
            // Você pode redirecionar para uma página de erro se preferir
            exit();
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'VerificarEmailRecuperacao') {
        $email = trim($_POST['EmailUsuario'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['msg_erro'] = "Por favor, informe um endereço de e-mail válido.";
            header("Location: ../View/recuperarLogin.php");
            exit();
        }

        $conexaoObj = new CConexao();
        $conn = $conexaoObj->getConnection();

        try {
            $sql = "SELECT idUsuario, EmailUsuario FROM usuario WHERE EmailUsuario = :email LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                // E-mail encontrado no banco: avança para a etapa de redefinição
                $_SESSION['recuperar_email'] = $usuario['EmailUsuario'];
                $_SESSION['recuperar_etapa'] = 'redefinir';
                header("Location: ../View/recuperarLogin.php");
                exit();
            } else {
                // E-mail não encontrado no banco: barra e exibe erro
                $_SESSION['msg_erro'] = "O e-mail informado não foi encontrado em nosso sistema.";
                header("Location: ../View/recuperarLogin.php");
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION['msg_erro'] = "Erro ao consultar o banco de dados: " . $e->getMessage();
            header("Location: ../View/recuperarLogin.php");
            exit();
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'SalvarNovaSenha') {
        $email = $_SESSION['recuperar_email'] ?? '';
        $novaSenha = $_POST['NovaSenhaUsuario'] ?? '';
        $confirmaSenha = $_POST['ConfirmarSenha'] ?? '';

        if (empty($email)) {
            $_SESSION['msg_erro'] = "Sessão expirada. Por favor, informe seu e-mail novamente.";
            header("Location: ../View/recuperarLogin.php");
            exit();
        }

        if (empty($novaSenha) || strlen($novaSenha) < 4) {
            $_SESSION['msg_erro'] = "A nova senha deve ter no mínimo 4 caracteres.";
            header("Location: ../View/recuperarLogin.php");
            exit();
        }

        if ($novaSenha !== $confirmaSenha) {
            $_SESSION['msg_erro'] = "As senhas não coincidem.";
            header("Location: ../View/recuperarLogin.php");
            exit();
        }

        $conexaoObj = new CConexao();
        $conn = $conexaoObj->getConnection();

        try {
            // Obtém o nome do usuário antes de atualizar para personalizar o e-mail
            $stmtUser = $conn->prepare("SELECT NomeUsuario, UserUsuario FROM usuario WHERE EmailUsuario = :email LIMIT 1");
            $stmtUser->bindParam(':email', $email);
            $stmtUser->execute();
            $dadosUsuario = $stmtUser->fetch(PDO::FETCH_ASSOC);
            $nomeDestinatario = $dadosUsuario['NomeUsuario'] ?? ($dadosUsuario['UserUsuario'] ?? '');

            $sql = "UPDATE usuario SET SenhaUsuario = :senha WHERE EmailUsuario = :email";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':senha', $novaSenha);
            $stmt->bindParam(':email', $email);

            if ($stmt->execute()) {
                // Envia o e-mail de confirmação usando EnvioEmail.php
                require_once __DIR__ . '/EnvioEmail.php';
                enviarEmailSenhaRedefinida($email, $nomeDestinatario);

                // Limpa os dados de recuperação da sessão
                unset($_SESSION['recuperar_email']);
                unset($_SESSION['recuperar_etapa']);

                $_SESSION['sucesso_login'] = "Senha redefinida com sucesso! Um e-mail de confirmação foi enviado para sua caixa de entrada.";
                header("Location: ../View/login.php?redefinido=1");
                exit();
            } else {
                $_SESSION['msg_erro'] = "Erro ao atualizar a senha. Tente novamente.";
                header("Location: ../View/recuperarLogin.php");
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION['msg_erro'] = "Erro no banco de dados: " . $e->getMessage();
            header("Location: ../View/recuperarLogin.php");
            exit();
        }
    }
}

// Função para verificar as credenciais do usuário (baseada no seu banco de dados)
function verificarCredenciais($conn, $username, $password)
{
    try {
        // Consulta SQL para verificar as credenciais
        $sql = "SELECT SenhaUsuario FROM usuario WHERE UserUsuario = :username";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $storedPassword = $stmt->fetch(PDO::FETCH_COLUMN);

        if ($storedPassword === $password) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        // Erro na conexão ou consulta SQL
        echo "Erro: " . $e->getMessage();
        return false;
    }
}

// Função para obter o nome do usuário a partir do banco de dados
function obterNomeDoUsuario($conn, $username)
{
    try {
        $sql = "SELECT UserUsuario FROM usuario WHERE UserUsuario = :username";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            return $user['UserUsuario'];
        } else {
            return "";
        }
    } catch (PDOException $e) {
        // Erro na conexão ou consulta SQL
        echo "Erro: " . $e->getMessage();
        return "";
    }
}

// Função para obter o ID do usuário a partir do banco de dados
function obterIdDoUsuario($conn, $username)
{
    try {
        $sql = "SELECT idUsuario FROM usuario WHERE UserUsuario = :username";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            return $user['idUsuario'];
        } else {
            return null; // Ou outra indicação de que o usuário não foi encontrado
        }
    } catch (PDOException $e) {
        // Erro na conexão ou consulta SQL
        echo "Erro: " . $e->getMessage();
        return null; // Ou outra indicação de erro, se necessário
    }
}
