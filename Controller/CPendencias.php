<?php
try {
    // Criar uma nova instância da classe de conexão
    $conexao = new CConexao();
    $conn = $conexao->getConnection();

    if ($conn) {
        // Capturar a data atual do usuário
        date_default_timezone_set('America/Fortaleza'); // Configurar o fuso horário
        //$dataAtualUsuario = date("Y-m-d");
        $dataAtualUsuario = date("d/m/Y");
        // Consulta SQL para obter os dados da tabela de empréstimo
        $sql = "SELECT e.DataEmprestimo, e.idEmprestimo, e.StatusEmprestimo,
        DATE_FORMAT(e.DataEmprestimo, '%d/%m/%Y') AS DataEmprestimo,
        DATE_FORMAT(d.DataDevolucao, '%d/%m/%Y') AS Devolucao,
        DATE_FORMAT(d.DataDevolvida, '%d/%m/%Y') AS Devolvido
        FROM emprestimo e LEFT JOIN devolucao d ON e.idEmprestimo = d.emprestimo_idEmprestimo";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($result as $row) {
                $dataEmprestimo = $row['DataEmprestimo'];
                $idEmprestimo = $row['idEmprestimo'];
                $statusEmprestimo = $row['StatusEmprestimo']; 
                $dataDevolucao = $row['Devolucao'];
                $dataDevolvido = $row['Devolvido'];
                

                if (empty($dataDevolvido)) {
                    $sqlUpdate = "UPDATE emprestimo SET StatusEmprestimo = 1 WHERE idEmprestimo = :idEmprestimo";
                } else {
                    $sqlUpdate = "UPDATE emprestimo SET StatusEmprestimo = 2 WHERE idEmprestimo = :idEmprestimo";
                }

                // Preparar e executar a atualização
                $stmtUpdate = $conn->prepare($sqlUpdate);
                if ($stmtUpdate) {
                    $stmtUpdate->bindParam(':idEmprestimo', $idEmprestimo, PDO::PARAM_INT);
                    $stmtUpdate->execute();
                } else {
                    echo "Erro na preparação da atualização.";
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
