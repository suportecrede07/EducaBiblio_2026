<?php
    //Incluindo conexao com banco de dados
    include_once '../Controller/CConexao.php';
    $conexao = new CConexao();
    $conn = $conexao->getConnection();

    //Recebendo os dados da requisição
    $dados_requisicao = $_REQUEST;

    //Obtendo registros
    $query_Quantidade_Emp = "SELECT COUNT(idEmprestimo) AS Quantidade_emp FROM emprestimo";
    $result_Quantidade_Emp = $conn->prepare($query_Quantidade_Emp);
    $result_Quantidade_Emp->execute();
    $row_Quantidade_Emp = $result_Quantidade_Emp->fetch(PDO::FETCH_ASSOC);
    //var_dump($row_Quantidade_Emp); 
    
    
    $sqlAlunos = "
    SELECT
        livro.NomeLivro AS TituloLivro,
        genero.NomeGenero,
        emprestimo.idEmprestimo,
        turma.NomeTurma,
        aluno.NomeAluno,
        turma.AnoTurma,
        emprestimo.DataEmprestimo,
        devolucao.DataDevolucao,
        emprestimo.Quantidade_emp
    FROM emprestimo
    LEFT JOIN aluno ON emprestimo.aluno_idAluno = aluno.idAluno
    INNER JOIN turma ON aluno.Turma_idTurma = turma.IdTurma
    LEFT JOIN devolucao ON emprestimo.idEmprestimo = devolucao.emprestimo_idEmprestimo
    INNER JOIN livro ON emprestimo.livro_idLivro = livro.idLivro
    INNER JOIN genero ON livro.Genero_idGenero = genero.idGenero
    ";

    $result_Emprestimos = $conn->prepare($sqlAlunos);
    $result_Emprestimos->execute();

    while($row_Emp = $result_Emprestimos->fetch(PDO::FETCH_ASSOC)) {
        //var_dump($row_Emp);
        extract($row_Emp);
        $registro = [];
        $registro[] = $TituloLivro;
        $registro[] = $NomeGenero;
        $registro[] = $idEmprestimo;
        $registro[] = $AnoTurma;
        $registro[] = $NomeTurma;
        $registro[] = $NomeAluno;
        $registro[] = $DataEmprestimo;
        $registro[] = $DataDevolucao;
        $registro[] = $Quantidade_emp;

        $dados[] = $registro;
    }
    //var_dump($dados);

    //Criando array de informações a serem retornadas para o JavaScript
    $resultado =[
        "draw" => intval($dados_requisicao['draw']),
        "recordsTotal" => intval($row_Quantidade_Emp['Quantidade_emp']), //Quantidade de registros
        "recordsFiltered" => intval($row_Quantidade_Emp['Quantidade_emp']), //Total de registros quando tiver pesquisa
        "data" => $dados //Array de dados com o registro
    ];
    //var_dump($resultado);

    $var = json_encode($resultado);
    echo $var;
?>