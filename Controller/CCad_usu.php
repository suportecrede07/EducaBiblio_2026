
<?php
class UsuarioController
{
    private $conexao;

    public function __construct()
    {
        require_once('CConexao.php');

        $conexaoObj = new CConexao();
        $this->conexao = $conexaoObj->getConnection();
    }

    public function salvarUsuario($postData)
    {
        // Extrai os dados do formulário
        $nome = trim($postData['NomeUsuario'] ?? '');
        $usuario = trim($postData['UserUsuario'] ?? '');
        $email = trim($postData['EmailUsuario'] ?? '');
        $senha = $postData['SenhaUsuario'] ?? '';

        // Validação dos campos
        if (empty($nome) || empty($usuario) || empty($email) || empty($senha)) {
            echo "Preencha todos os campos.";
            return;
        }

        // Validação do email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Informe um email válido.";
            return;
        }

        // Verifica se o email já está em uso
        if ($this->emailJaEmUso($email)) {
            echo "Este email já está em uso.";
            return;
        }

        // Gera o hash da senha
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        try {

            // Insere o usuário no banco de dados
            $sql = "INSERT INTO usuario 
                    (UserUsuario, NomeUsuario, EmailUsuario, SenhaUsuario) 
                    VALUES (:usuario, :nome, :email, :senha)";

            $stmt = $this->conexao->prepare($sql);

            $stmt->bindParam(':usuario', $usuario);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':senha', $senhaHash);

            if ($stmt->execute()) {

                header("Location: ../View/inicio.php");
                exit();

            } else {

                echo "Ocorreu um erro ao salvar o usuário.";

            }

        } catch (PDOException $e) {

            error_log("Erro ao cadastrar usuário: " . $e->getMessage());
            echo "Não foi possível cadastrar o usuário.";

        }
    }

    private function emailJaEmUso($email)
    {
        $sql = "SELECT COUNT(*) FROM usuario WHERE EmailUsuario = :email";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $count = $stmt->fetchColumn();

        return ($count > 0);
    }
}
