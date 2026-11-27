<?php
    //Incluindo conexao com banco de dados
    include_once '../Controller/CConexao.php';
    $conexao = new CConexao();
    $conn = $conexao->getConnection();

    //Recebendo os dados da requisição
    $dados_requisicao = $_REQUEST;

    //Obtendo registros totais
    $query_Quantidade_Livros = "SELECT COUNT(idLivro) AS Quantidade_li FROM livro";
    $result_Quantidade_Livros = $conn->prepare($query_Quantidade_Livros);
    $result_Quantidade_Livros->execute();
    $row_Quantidade_Livros = $result_Quantidade_Livros->fetch(PDO::FETCH_ASSOC);

    $sqlLivros = "SELECT
        livro.idLivro,
        livro.NomeLivro,
        livro.EditoraLivro,
        livro.Tombo,
        livro.IBSMLivro,
        livro.QuantidadeLivros,
        genero.NomeGenero AS GeneroLivro,
        idioma.Idioma AS IdiomaLivro,
        autor.NomeAutor
    FROM
        livro
    LEFT JOIN
         genero ON livro.Genero_idGenero = genero.idGenero
    LEFT JOIN
        autor ON (livro.autor_idAutor = autor.idAutor)
    LEFT JOIN
        idioma ON livro.Idioma_idIdioma = idioma.idIdioma";

    if(!empty($dados_requisicao['search']['value'])){
        $sqlLivros .= " WHERE NomeLivro LIKE :NomeLivro ";
        $sqlLivros .= " OR Tombo LIKE :Tombo ";
        $sqlLivros .= " OR genero.NomeGenero LIKE :genero ";
        $sqlLivros .= " OR idioma.Idioma LIKE :idioma ";
        $sqlLivros .= " OR autor.NomeAutor LIKE :autor ";
    }
    
    $sqlLivros .= " LIMIT :inicio , :quantidade";
        
    $result_Livros = $conn->prepare($sqlLivros);

    $result_Livros->bindParam(':inicio',$dados_requisicao['start'],PDO::PARAM_INT); 
    $result_Livros->bindParam(':quantidade',$dados_requisicao['length'],PDO::PARAM_INT);

    if(!empty($dados_requisicao['search']['value'])){
        $valor_pesq = "%" . $dados_requisicao['search']['value'] . "%";
        $result_Livros->bindParam(':NomeLivro', $valor_pesq, PDO::PARAM_STR);
        $result_Livros->bindParam(':Tombo', $valor_pesq, PDO::PARAM_STR);
        $result_Livros->bindParam(':genero', $valor_pesq, PDO::PARAM_STR);
        $result_Livros->bindParam(':idioma', $valor_pesq, PDO::PARAM_STR);
        $result_Livros->bindParam(':autor', $valor_pesq, PDO::PARAM_STR);
    }

    //Executar a Query
    $result_Livros->execute();

    $dados = [];
    while($row_Li = $result_Livros->fetch(PDO::FETCH_ASSOC)) {
        extract($row_Li);
        $registro = [];
        $registro[] = $idLivro;
        $registro[] = $NomeLivro;
        $registro[] = !empty($NomeAutor) ? $NomeAutor : '-';
        $registro[] = $EditoraLivro;
        $registro[] = $Tombo;
        $registro[] = $QuantidadeLivros;
        $registro[] = $GeneroLivro;
        $registro[] = $IdiomaLivro;
        $registro[] = "<button type='button' class='btn btn-outline-primary' id='$idLivro' onclick='visLivro($idLivro)'><i class='fas fa-eye'></i></button>";
        $registro[] = "<button type='button' class='btn btn-outline-warning' id='$idLivro' onclick='editLivro($idLivro)'><i class='fas fa-pen'></i></button>";
        $registro[] = "<button type='button' class='btn btn-outline-danger' id='$idLivro' onclick='excluirLivro($idLivro)'><i class='fas fa-trash-alt'></i></button>";

        $dados[] = $registro;
    }

    //Criando array de informações a serem retornadas para o JavaScript
    $resultado =[
        "draw" => intval($dados_requisicao['draw'] ?? 1),
        "recordsTotal" => intval($row_Quantidade_Livros['Quantidade_li'] ?? 0),
        "recordsFiltered" => intval($row_Quantidade_Livros['Quantidade_li'] ?? 0),
        "data" => $dados
    ];

    echo json_encode($resultado);
?>
