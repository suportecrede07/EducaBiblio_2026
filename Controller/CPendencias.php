<?php
try {
    // Criar uma nova instância da classe de conexão
    $conexao = new CConexao();
    $conn = $conexao->getConnection();

    if ($conn) {
        // Capturar a data atual do usuário
        date_default_timezone_set('America/Fortaleza'); // Configurar o fuso horário
        $dataAtualUsuario = date("Y-m-d");

        // Consulta SQL para obter os dados da tabela de empréstimo
        $sql = "SELECT e.DataEmprestimo, e.idEmprestimo, e.StatusEmprestimo
                FROM emprestimo e";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($result as $row) {
                $dataEmprestimo = $row['DataEmprestimo'];
                $idEmprestimo = $row['idEmprestimo'];
                $statusEmprestimo = $row['StatusEmprestimo'];

                // Comparar a data atual do usuário com a DataEmprestimo
                if ($dataAtualUsuario > $dataEmprestimo && ($statusEmprestimo == '0' || $statusEmprestimo == '1')) {
                    // Se a data atual estiver atrasada e o livro não tiver sido devolvido (status 0 ou 1), atualize para 1
                    $sqlUpdate = "UPDATE emprestimo SET StatusEmprestimo = '1' WHERE idEmprestimo = :idEmprestimo";
                    $stmtUpdate = $conn->prepare($sqlUpdate);

                    if ($stmtUpdate) {
                        $stmtUpdate->bindParam(':idEmprestimo', $idEmprestimo, PDO::PARAM_INT);
                        $stmtUpdate->execute();
                    } else {
                        echo "Erro na preparação da atualização.";
                    }
                }
            }
        } else {
            echo "Erro na execução da consulta.";
        }
    } else {
        echo "Falha na conexão com o banco de dados.";
    }
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>
