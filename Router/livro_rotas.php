<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'Cadastrar') {
        require_once '../Controller/CCad_livro.php';
        $livroController = new LivroController();
        $livroController->cadastrarLivro($_POST);
    }
}

// Redirecionamento após o cadastro ou caso a condição não seja atendida
header("Location: ../View/livros.php");
exit();
