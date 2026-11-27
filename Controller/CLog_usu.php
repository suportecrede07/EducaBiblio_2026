
<?php

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['action']) && $_POST['action'] === 'Entrar') {

        $username = $_POST['username'];
        $password = $_POST['password'];

        include('CConexao.php');

        $conexaoObj = new CConexao();
        $conn = $conexaoObj->getConnection();

        // Verificar credenciais e recuperar os dados do usuário
        $usuario = verificarCredenciais($conn, $username, $password);

        if ($usuario) {

            // Renovar o ID da sessão
            session_regenerate_id(true);

            // Guardar informações do usuário na sessão
            $_SESSION['usuario_logado'] = true;
            $_SESSION['idUsuario'] = $usuario['idUsuario'];
            $_SESSION['nomeDoUsuario'] = $usuario['UserUsuario'];

            // Guardar imagem do usuário
            $_SESSION['caminhoImagemUsuario'] =
                !empty($usuario['CamFoto'])
                ? $usuario['CamFoto']
                : "../img/adm.png";

            // Redirecionar para página inicial
            header("Location: ../View/inicio.php");
            exit();
        } else {

            echo "Nome de usuário ou senha incorretos.";
        }
    }
}

// Função para verificar as credenciais
function verificarCredenciais($conn, $username, $password)
{
    try {

        $sql = "SELECT * FROM usuario WHERE UserUsuario = :username";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['SenhaUsuario'])) {

            // Retornar todos os dados do usuário encontrado
            return $user;
        }

        return false;
    } catch (PDOException $e) {

        echo "Erro ao verificar credenciais.";
        return false;
    }
}
