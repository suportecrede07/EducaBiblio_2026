<?php
    //Incluindo conexao com banco de dados
    include_once '../Controller/CConexao.php';
    $conexao = new CConexao();
    $conn = $conexao->getConnection();

    //Recebendo os dados da requisição
    $dados_requisicao = $_REQUEST;

    //Obtendo registros
    $query_Quantidade_prof = "SELECT COUNT(idProf) AS Quantidade_prof FROM prof";
    
    $result_Quantidade_prof = $conn->prepare($query_Quantidade_prof);

    $result_Quantidade_prof->execute();
    $row_Quantidade_prof = $result_Quantidade_prof->fetch(PDO::FETCH_ASSOC);


        $sqlProf = "SELECT idProf, NomeProf, EmailProf, MateriaProf from prof";

    if(!empty($dados_requisicao['search']['value'])){
        $sqlProf .= " WHERE NomeProf LIKE :nome ";
    }
    
    $sqlProf .= " LIMIT :inicio , :quantidade";
        
    $result_prof = $conn->prepare($sqlProf);

    $result_prof->bindParam(':inicio',$dados_requisicao['start'],PDO::PARAM_INT);
    $result_prof->bindParam(':quantidade',$dados_requisicao['length'],PDO::PARAM_INT);

    if(!empty($dados_requisicao['search']['value'])){
        $valor_pesq = "%" . $dados_requisicao['search']['value'] . "%";
        $result_prof->bindParam(':nome', $valor_pesq, PDO::PARAM_STR);
    }

    //Executar a Query
    $result_prof->execute();

    while($row_prof = $result_prof->fetch(PDO::FETCH_ASSOC)) {
        //var_dump($row_Emp);
        extract($row_prof);
        $registro = [];
        $registro[] = $idProf;
        $registro[] = $NomeProf;
        $registro[] = $EmailProf;
        $registro[] = $MateriaProf;
        $registro[] = "<a href='../Controller/CExcluir_prof.php?id={$row_prof["idProf"]}'><button class='close-popup-button'><i class='fa fa-trash'></i></button></a>";

        $dados[] = $registro;
    }
    //var_dump($dados);

    //Criando array de informações a serem retornadas para o JavaScript
    $resultado =[
        "draw" => intval($dados_requisicao['draw']),
        "recordsTotal" => intval($row_Quantidade_prof['Quantidade_prof']), //Quantidade de registros
        "recordsFiltered" => intval($row_Quantidade_prof['Quantidade_prof']), //Total de registros quando tiver pesquisa
        "data" => $dados //Array de dados com o registro
    ];
    //var_dump($resultado);

    $var = json_encode($resultado);
    echo $var;
?>