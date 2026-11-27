<?php
require_once __DIR__ . '/CConexao.php';

class LivroController
{
    public function cadastrarLivro($dadosLivro)
    {
        $conn = null;
        try {
            $conexao = new CConexao();
            $conn = $conexao->getConnection();

            // Valida o nome do autor e busca ou cria o registro na tabela autor
            $nomeAutor = isset($dadosLivro['NomeAutor']) ? trim($dadosLivro['NomeAutor']) : '';
            if (empty($nomeAutor)) {
                $nomeAutor = 'Não informado';
            }
            $idAutor = $this->buscarOuCriarAutor($conn, $nomeAutor);

            // Insere os dados do livro na tabela livro com o ID do autor vinculado
            $this->inserirLivro($conn, $dadosLivro, $idAutor);

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['msgAlerta'] = "<div class='alert alert-success' role='alert'>Livro e autor cadastrados com sucesso!</div>";

            return true;
        } catch (PDOException $e) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['msgAlerta'] = "<div class='alert alert-danger' role='alert'>Erro ao cadastrar: " . $e->getMessage() . "</div>";
            return false;
        } catch (Exception $e) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['msgAlerta'] = "<div class='alert alert-danger' role='alert'>Erro: " . $e->getMessage() . "</div>";
            return false;
        }
    }

    public function buscarOuCriarAutor($conn, $nomeAutor)
    {
        $nomeAutor = trim($nomeAutor);
        if (empty($nomeAutor)) {
            $nomeAutor = 'Não informado';
        }

        // Verifica se o autor já existe na tabela autor (busca insensível a maiúsculas/minúsculas)
        $sqlCheckAutor = "SELECT idAutor FROM autor WHERE LOWER(TRIM(NomeAutor)) = LOWER(:NomeAutor) LIMIT 1";
        $stmtCheckAutor = $conn->prepare($sqlCheckAutor);
        $stmtCheckAutor->bindValue(':NomeAutor', $nomeAutor);
        $stmtCheckAutor->execute();
        $autor = $stmtCheckAutor->fetch(PDO::FETCH_ASSOC);

        // Se o autor existir, retorna o ID do autor existente
        if ($autor && !empty($autor['idAutor'])) {
            return (int) $autor['idAutor'];
        }

        // Se o autor não existir, insere o novo autor na tabela autor e retorna o ID gerado
        $sqlNovoAutor = "INSERT INTO autor (NomeAutor) VALUES (:NomeAutor)";
        $stmtNovoAutor = $conn->prepare($sqlNovoAutor);
        $stmtNovoAutor->bindValue(':NomeAutor', $nomeAutor);
        $stmtNovoAutor->execute();

        $novoId = (int) $conn->lastInsertId();
        if ($novoId <= 0) {
            $stmtLast = $conn->prepare("SELECT idAutor FROM autor WHERE NomeAutor = :NomeAutor ORDER BY idAutor DESC LIMIT 1");
            $stmtLast->bindValue(':NomeAutor', $nomeAutor);
            $stmtLast->execute();
            $rowLast = $stmtLast->fetch(PDO::FETCH_ASSOC);
            if ($rowLast && !empty($rowLast['idAutor'])) {
                $novoId = (int) $rowLast['idAutor'];
            }
        }

        return $novoId;
    }

    private function inserirLivro($conn, $dadosLivro, $idAutor)
    {
        // Descobrir as colunas reais da tabela livro no banco de dados para evitar erros de colunas inexistentes
        $colStmt = $conn->query("SHOW COLUMNS FROM livro");
        $colunasExistentes = $colStmt ? $colStmt->fetchAll(PDO::FETCH_COLUMN) : [];
        $colunasMap = [];
        foreach ($colunasExistentes as $col) {
            $colunasMap[strtolower($col)] = $col;
        }

        $nomeLivro = isset($dadosLivro['NomeLivro']) ? trim($dadosLivro['NomeLivro']) : '';
        $tombo = isset($dadosLivro['Tombo']) ? trim($dadosLivro['Tombo']) : '';
        $idGenero = intval($dadosLivro['Genero_idGenero'] ?? 1);
        $idIdioma = intval($dadosLivro['Idioma_idIdioma'] ?? 1);
        $editora = !empty($dadosLivro['EditoraLivro']) ? trim($dadosLivro['EditoraLivro']) : null;
        $edicao = !empty($dadosLivro['EdicaoLivro']) ? trim($dadosLivro['EdicaoLivro']) : null;
        $quantidade = (!empty($dadosLivro['QuantidadeLivros']) && is_numeric($dadosLivro['QuantidadeLivros'])) 
                      ? intval($dadosLivro['QuantidadeLivros']) : 1;
        $ibsm = !empty($dadosLivro['IBSMLivro']) ? trim($dadosLivro['IBSMLivro']) : null;
        $local = !empty($dadosLivro['LocalLivro']) ? trim($dadosLivro['LocalLivro']) : null;
        $prateleira = !empty($dadosLivro['PrateleiraLivro']) ? trim($dadosLivro['PrateleiraLivro']) : null;
        $coluna = !empty($dadosLivro['ColunaLivro']) ? trim($dadosLivro['ColunaLivro']) : null;
        $didatico = !empty($dadosLivro['DidaticoLivro']) ? trim($dadosLivro['DidaticoLivro']) : null;

        $dadosParaInserir = [];

        // Mapear campos para colunas existentes na tabela
        if (isset($colunasMap['nomelivro'])) $dadosParaInserir[$colunasMap['nomelivro']] = $nomeLivro;
        if (isset($colunasMap['tombo'])) $dadosParaInserir[$colunasMap['tombo']] = $tombo;
        if (isset($colunasMap['genero_idgenero'])) $dadosParaInserir[$colunasMap['genero_idgenero']] = $idGenero;
        if (isset($colunasMap['idioma_ididioma'])) $dadosParaInserir[$colunasMap['idioma_ididioma']] = $idIdioma;
        if (isset($colunasMap['editoralivro'])) $dadosParaInserir[$colunasMap['editoralivro']] = $editora;
        if (isset($colunasMap['edicaolivro'])) $dadosParaInserir[$colunasMap['edicaolivro']] = $edicao;

        // Quantidade (pode ser QuantidadeLivros ou Quantidadelivros)
        if (isset($colunasMap['quantidadelivros'])) {
            $dadosParaInserir[$colunasMap['quantidadelivros']] = $quantidade;
        }

        // Autor (pode ser autor_idAutor ou Autor_idAutor)
        if (isset($colunasMap['autor_idautor'])) {
            $dadosParaInserir[$colunasMap['autor_idautor']] = $idAutor;
        }
        // Compatibilidade caso autor_idAutor1 exista no banco
        if (isset($colunasMap['autor_idautor1'])) {
            $dadosParaInserir[$colunasMap['autor_idautor1']] = $idAutor;
        }

        // Campos opcionais
        if (isset($colunasMap['ibsmlivro'])) $dadosParaInserir[$colunasMap['ibsmlivro']] = $ibsm;
        if (isset($colunasMap['locallivro'])) $dadosParaInserir[$colunasMap['locallivro']] = $local;
        if (isset($colunasMap['prateleiralivro'])) $dadosParaInserir[$colunasMap['prateleiralivro']] = $prateleira;
        if (isset($colunasMap['colunalivro'])) $dadosParaInserir[$colunasMap['colunalivro']] = $coluna;
        if (isset($colunasMap['didaticolivro'])) $dadosParaInserir[$colunasMap['didaticolivro']] = $didatico;

        // Montar a query dinamicamente apenas com as colunas válidas
        $campos = [];
        $placeholders = [];
        $valores = [];
        $idx = 0;
        foreach ($dadosParaInserir as $col => $val) {
            $param = ':p_' . $idx;
            $campos[] = "`$col`";
            $placeholders[] = $param;
            $valores[$param] = $val;
            $idx++;
        }

        $sqlLivro = "INSERT INTO livro (" . implode(', ', $campos) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmtLivro = $conn->prepare($sqlLivro);

        foreach ($valores as $param => $val) {
            $stmtLivro->bindValue($param, $val);
        }

        $stmtLivro->execute();
    }
}
