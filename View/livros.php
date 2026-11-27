<?php

//Incluindo arquivo de conexão ao banco de dados
include('../Controller/CConexao.php');

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
	header("Location: ../View/login.php"); // Redirecionar para a página de login se não estiver logado
	exit();
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="../ArquivosExternos/icons.js"></script>
	<link rel="shortcut icon" href="../img/icon1.png" type="image/x-icon">
	<link rel="stylesheet" href="../CSS/style.css">
	<link rel="stylesheet" href="../lib/Bootstrap 2/bootstrap.min.css">
	<link rel="stylesheet" href="../lib/datatables/dataTables.dataTables.min.css">
	<link rel="stylesheet" href="../CSS/darkPaginacao.css">	

	<style>
		/* Força o cabeçalho e todas as células da tabela a alinharem à esquerda */
		#TableLivros th,
		#TableLivros td {
			text-align: left !important;
			vertical-align: middle !important;
		}
		.pdf-button, #pdf-button, button.pdf-button {
			width: auto !important;
			min-width: fit-content !important;
			white-space: nowrap !important;
			display: inline-flex !important;
			align-items: center !important;
			justify-content: center !important;
			gap: 8px !important;
			padding: 8px 18px !important;
			border-radius: 20px !important;
			height: 38px !important;
			background-color: #28a745 !important;
			color: #fff !important;
			font-size: 14px !important;
			font-weight: 600 !important;
			border: none !important;
			box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
			cursor: pointer !important;
		}
	</style>

	<title>EducaBiblio</title>
</head>

<body>

	<section id="sidebar">
		<a href="#" class="brand">
			<i class="fas fa-book"></i>
			<span class="text">EducaBiblio</span>
		</a>
		<ul class="side-menu top">
			<li>
				<a href="inicio.php">
					<i class='fas fa-home'></i>
					<span class="text">Início</span>
				</a>
			</li>
			<li class="active">
				<a href="livros.php">
					<i class="fas fa-book"></i>
					<span class="text">Livros</span>
				</a>
			</li>
			<li>
				<a href="emprestimos.php">
					<i class="fas fa-undo"></i>
					<span class="text">Empréstimos</span>
				</a>
			</li>
			<li>
				<a href="devolucao.php">
					<i class="fas fa-arrow-left"></i>
					<span class="text">Devoluções</span>
				</a>
			</li>
			<li>
				<a href="aluno.php">
					<i class="fas fa-graduation-cap"></i>
					<span class="text">Alunos</span>
				</a>
			</li>
			<li>
				<a href="prof.php">
					<i class="fas fa-clipboard-list"></i>
					<span class="text">Professores</span>
				</a>
			</li>
			<li>
				<a href="turma.php">
					<i class="fas fa-users"></i>
					<span class="text">Turma</span>
				</a>
			</li>
			<li>
				<a href="recomendacoes.php">
					<i class="fas fa-download"></i>
					<span class="text">Recomendações</span>
				</a>
			</li>
			<li>
				<a href="usuarios.php">
					<i class="fas fa-cogs"></i>
					<span class="text">Usuários</span>
				</a>
			</li>
		</ul>
		<ul class="side-menu">
			<li>
				<a href="../Controller/CLogout.php" class="logout">
					<i class="fas fa-sign-out-alt"></i>
					<span class="text">Deslogar</span>
				</a>
			</li>
		</ul>
	</section>

	<section id="content">
		<nav>
			<i class='fas fa-bars'></i>
			<form action="#"></form>
			<div class="icons">
				<div id="menu-btn" class="fas fa-question" onclick="abrirPDFEmNovaAba()"></div>
			</div>

			<script>
				function abrirPDFEmNovaAba() {
					var urlDoPDF = "../ArquivosExternos/Manual.pdf";
					window.open(urlDoPDF, '_blank');
				}
			</script>
			<input type="checkbox" id="switch-mode" hidden>
			<label for="switch-mode" class="switch-mode"></label>
			<a href="#" class="profile">
				<?php
				require('../Controller/Utils.php');

				$conexao = new CConexao();
				$conn = $conexao->getConnection();

				?>
			</a>
		</nav>
		</head>

		<body>

			<style>
				/* Esconde as setas para campos de entrada numérica */
				input[type=number]::-webkit-inner-spin-button,
				input[type=number]::-webkit-outer-spin-button {
					-webkit-appearance: none;
					margin: 0;
				}

				input[type=number] {
					-moz-appearance: textfield;
				}

				.btn-cadastrar-livro {
					background-color: #3BB143;
					color: #fff;
					border: none;
					padding: 10px 20px;
					border-radius: 8px;
					font-weight: 600;
					font-size: 15px;
					display: inline-flex;
					align-items: center;
					gap: 8px;
					transition: background-color 0.2s;
					cursor: pointer;
				}
				.btn-cadastrar-livro:hover {
					background-color: #2e8b34;
					color: #fff;
				}
			</style>
			<main>
				<div class="table-data">
					<div class="order">
						<div class="head">
							<h3>Tabela de Cadastro Livros</h3>
							<div style="display: flex; align-items: center; gap: 10px; margin-left: auto; flex-wrap: wrap;">
								<button type="button" class="btn-cadastrar-livro" data-bs-toggle="modal" data-bs-target="#cadLivrosModal">
									<i class="fas fa-plus"></i> Cadastrar Livro
								</button>
								<button type="button" class="pdf-button" onclick="abrirAluno()">
									<i class="fas fa-file-pdf"></i> Exportar Relatório PDF
								</button>
							</div>

							<script>
								function abrirAluno() {
									var urlDoPDF = "../pdf/livroPdf.php";
									window.open(urlDoPDF, '_blank');
								}
							</script>

						</div>

						<span id="msgAlerta">
							<?php
							if (isset($_SESSION['msgAlerta'])) {
								echo $_SESSION['msgAlerta'];
								unset($_SESSION['msgAlerta']);
							}
							?>
						</span>

						<table id="TableLivros" style="width:100%">
								<thead>
									<tr>
										<th>ID</th>
										<th>Nome</th>
										<th>Autor</th>
										<th>Editora</th>
										<th>Tombo</th>
										<th>Quantidade</th>
										<th>Gênero</th>
										<th>Idioma</th>
										<th>Visualizar</th>
										<th>Editar</th>
										<th>Excluir</th>
									</tr>
								</thead>
							</table>
					</div>
				</div>

				<!-- Criando Modal Cadastrar Livro -->
				<div class="modal fade" id="cadLivrosModal" tabindex="-1" aria-labelledby="cadLivrosModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="cadLivrosModalLabel">Cadastro de Livros</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 1.2rem; cursor: pointer;">&times;</button>
							</div>
							<div class="modal-body">
								<form action="../Router/livro_rotas.php" method="post" id="form-cad-livros" enctype="multipart/form-data">
									<div class="mb-3">
										<label class="form-label">Nome do Livro:</label>
										<input type="text" placeholder="Nome do Livro" name="NomeLivro" id="cadNomeLivro" required maxlength="100" class="form-control" autocomplete="off">
									</div>

									<div class="mb-3">
										<label class="form-label">Autor:</label>
										<input type="text" placeholder="Nome do Autor" name="NomeAutor" id="cadNomeAutor" required maxlength="100" class="form-control" autocomplete="off">
									</div>

									<div class="mb-3">
										<label class="form-label">Editora:</label>
										<input type="text" placeholder="Editora" name="EditoraLivro" id="cadEditoraLivro" maxlength="50" class="form-control" autocomplete="off">
									</div>

									<div class="mb-3">
										<label class="form-label">Edição:</label>
										<input type="text" placeholder="Edição" name="EdicaoLivro" id="cadEdicaoLivro" maxlength="50" class="form-control" autocomplete="off">
									</div>

									<div class="mb-3">
										<label class="form-label">Tombo:</label>
										<input type="text" placeholder="Tombo" name="Tombo" id="cadTombo" maxlength="50" class="form-control" autocomplete="off">
									</div>

									<div class="mb-3">
										<label class="form-label">Selecione um Gênero:</label>
										<select name="Genero_idGenero" id="cadGenero_idGenero" class="form-control" required>
											<option value="">Selecione um Gênero</option>
											<option value="1">Autoajuda</option>
											<option value="2">Biografia</option>
											<option value="3">Clássico</option>
											<option value="4">Conto</option>
											<option value="5">Fantasia</option>
											<option value="6">Ficção</option>
											<option value="7">Poesia</option>
											<option value="8">Romance</option>
											<option value="9">Outro</option>
											<option value="10">Crônica</option>
											<option value="11">Quadrinhos</option>
											<option value="12">Didático</option>
											<option value="13">Mitologia</option>
											<option value="14">Teatro</option>
										</select>
									</div>

									<div class="mb-3">
										<label class="form-label">Selecione um Idioma:</label>
										<select name="Idioma_idIdioma" id="cadIdioma_idIdioma" class="form-control">
											<option value="1">Português</option>
											<option value="2">Inglês</option>
											<option value="3">Espanhol</option>
										</select>
									</div>

									<div class="mb-3">
										<label class="form-label">Quantidade:</label>
										<input type="number" placeholder="Quantidade" name="QuantidadeLivros" id="cadQuantidadeLivros" class="form-control" min="1" value="1" autocomplete="off">
									</div>
								</form>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
								<button type="submit" form="form-cad-livros" name="action" value="Cadastrar" class="btn btn-success">Cadastrar</button>
							</div>
						</div>
					</div>
				</div>

				<!-- Criando Modal Visualizar -->
				<div class="modal fade" id="visLivrosModal" tabindex="-1" arial-labelledby="visLivrosModalLabel" arial-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="visLivrosModalLabel">Detalhes do Livro</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 1.2rem; cursor: pointer;">&times;</button>
							</div>
							<div class="modal-body">
								<dl class="row">
									<dt class="col-sm-3">ID</dt>
									<dd class="col-sm-9"><span id="idLiv"></span></dd>

									<dt class="col-sm-3">Nome</dt>
									<dd class="col-sm-9"><span id="nomeLivro"></span></dd>

									<dt class="col-sm-3">Autor</dt>
									<dd class="col-sm-9"><span id="autorLivro"></span></dd>

									<dt class="col-sm-3">Editora</dt>
									<dd class="col-sm-9"><span id="editoraLivro"></span></dd>

									<dt class="col-sm-3">Tombo</dt>
									<dd class="col-sm-9"><span id="tomboLivro"></span></dd>

									<dt class="col-sm-3">Quantidade</dt>
									<dd class="col-sm-9"><span id="quantLivro"></span></dd>

									<dt class="col-sm-3">Gênero</dt>
									<dd class="col-sm-9"><span id="geLivro"></span></dd>

									<dt class="col-sm-3">Idioma</dt>
									<dd class="col-sm-9"><span id="idiomaLivro"></span></dd>

								</dl>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
							</div>
						</div>
					</div>
				</div>

				<!-- Criando Modal Editar -->
				<div class="modal fade" id="editLivrosModal" tabindex="-1" arial-labelledby="editLivrosModalLabel" arial-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="editLivrosModalLabel">Editar Livros</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 1.2rem; cursor: pointer;">&times;</button>
							</div>
							<div class="modal-body">
								<span id="msgAlertErroEdit"></span>
								<form method="POST" id="form-edit-livros" action="../Modais/editarLivro.php">
									<div class="mb-3">
										<input type="text" class="form-control" name="idLivro" id="editId" required autocomplete="off" readonly>
									</div>
									<div class="mb-3">
										<label for="form-control">Nome do Livro:</label>
										<input type="text" class="form-control" name="NomeLivro" id="editNome" required autocomplete="off" >
									</div>
									<div class="mb-3">
										<label for="form-control">Autor:</label>
										<input type="text" class="form-control" name="NomeAutor" id="editAutor" autocomplete="off" >
									</div>
									<div class="mb-3">
										<label for="form-control">Editora:</label>
										<input type="text" class="form-control" name="EditoraLivro" id="editEditora" required autocomplete="off" >
									</div>
									<div class="mb-3">
										<label for="form-control">Tombo:</label>
										<input type="text" class="form-control" name="TomboLivro" id="editTombo" required autocomplete="off" >
									</div>
									<div class="mb-3">
										<label for="form-control">Selecione um Gênero:</label>
										<select name="generoLivro" id="editGenero" class="form-control" required>
											
											<option value="">Selecione um Gênero</option>
											<option value="1">Autoajuda</option>
											<option value="2">Biografia</option>
											<option value="3">Clássico</option>
											<option value="4">Conto</option>
											<option value="5">Fantasia</option>
											<option value="6">Ficção</option>
											<option value="7">Poesia</option>
											<option value="8">Romance</option>
											<option value="9">Outro</option>
											<option value="10">Crônica</option>
											<option value="11">Quadrinhos</option>
											<option value="12">Didático</option>
											<option value="13">Mitologia</option>
											<option value="14">Teatro</option>
										</select>
									</div>
									<div class="mb-3">
										<label for="form-control">Selecione um Idioma:</label>
										<select name="idiomaLivro" id="Idioma_idIdioma" class="form-control">
											<option value="1">Português</option>
											<option value="2">Inglês</option>
											<option value="3">Espanhol</option>
										</select>
									</div>
								</form>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
								<button type="submit" class="btn btn-warning" form="form-edit-livros" value="Salvar">Salvar Alterações</button>
							</div>
						</div>
					</div>
				</div>

				<footer class="footer">

					© Copyright 2023 por <span>EducaBiblio</span> | Todos os direitos reservados

				</footer>

			</main>
	</section>

</body>

</html>

<script src="../JS/script.js"></script>
<script src="../lib/jquery/jquery-3.7.1.min.js"></script>
<script src="../lib/datatables/dataTables.js"></script>
<script src="../lib/Bootstrap 2/bootstrap.bundle.min.js"></script>


<!-- Função do Modal Visualizar -->
<script>
	async function visLivro(idLivro) {
		//console.log(idEmprestimo);
		const dados = await fetch('../Modais/visualizarLivros.php?idLivro=' + idLivro);
		const resposta = await dados.json();
		//console.log(resposta);

		if(resposta['status']){
			const viewModal = new bootstrap.Modal(document.getElementById('visLivrosModal'));
			viewModal.show();

			document.getElementById("idLiv").innerHTML = resposta['dados'].idLivro;
			document.getElementById("nomeLivro").innerHTML = resposta['dados'].NomeLivro;
			document.getElementById("autorLivro").innerHTML = resposta['dados'].NomeAutor || 'Não informado';
			document.getElementById("editoraLivro").innerHTML = resposta['dados'].EditoraLivro;
			document.getElementById("tomboLivro").innerHTML = resposta['dados'].Tombo;
			document.getElementById("quantLivro").innerHTML = resposta['dados'].QuantidadeLivros;
			document.getElementById("geLivro").innerHTML = resposta['dados'].GeneroLivro;
			document.getElementById("idiomaLivro").innerHTML = resposta['dados'].IdiomaLivro;
			
		}else{
			alert(resposta['msg']);
		}
	}
</script>

<!-- Função do Modal Editar -->
<script>
	const editModal = new bootstrap.Modal(document.getElementById('editLivrosModal'));
	async function editLivro(idLivro) {
		//console.log(idEmprestimo);
		const dados = await fetch('../Modais/visualizarLivros.php?idLivro=' + idLivro);
		const resposta = await dados.json();
		//console.log(resposta);

		if(resposta['status']){
			document.getElementById("msgAlerta").innerHTML = "";
			editModal.show();

			document.getElementById("editId").value = resposta['dados'].idLivro;
			document.getElementById("editNome").value = resposta['dados'].NomeLivro;
			document.getElementById("editAutor").value = resposta['dados'].NomeAutor || '';
			document.getElementById("editEditora").value = resposta['dados'].EditoraLivro;
			document.getElementById("editTombo").value = resposta['dados'].Tombo;
		}else{
			alert(resposta['msg']);
		}
	}
</script>

<!-- Função do Modal Excluir -->
<script>
	async function excluirLivro(idLivro) {
		//console.log(idLivro);

		const dados = await fetch("../Controller/CExcluir_livros.php?id=" + idLivro);
		var confirmar = confirm("Você realmente quer apagar o registro selecionado?");

		//console.log(resposta);
		if(confirmar){
			document.getElementById("msgAlerta").innerHTML = "<div class='alert alert-success' role='alert'>Registro apagado com sucesso!</div>";

			listarDataTables = $('#TableLivros').DataTable();
			listarDataTables.draw();
		}else{
			document.getElementById("msgAlerta").innerHTML = "<div class='alert alert-danger' role='alert'> Erro: Registro não apagado</div>";
		}
	}
</script>

<!-- Salvando informações no banco de dados -->
<script>
	const formEditLivros = document.getElementById("form-edit-livros");
	if(formEditLivros){
		formEditLivros.addEventListener("submit", async(e) => {
			e.preventDefault();
			const dadosForm = new FormData(formEditLivros);

			const dados = await fetch("../Modais/editarLivro.php", {
				method: "POST",
				body: dadosForm
			});

			const resposta = await dados.json();

			if (resposta['status']){
				//document.getElementById("msgAlertErroEdit").innerHTML = resposta['msg'];

				document.getElementById("msgAlerta").innerHTML = resposta['msg'];
				document.getElementById("msgAlertErroEdit").innerHTML = "";
				formEditLivros.reset();	
				editModal.hide();
				listarDataTables = $('#TableLivros').DataTable();
				listarDataTables.draw();
			}else{
				document.getElementById("msgAlertErroEdit").innerHTML = resposta['msg'];
			}

		});
	}
</script>


<script>
	new DataTable('#TableLivros', {	
		ajax: {
			"url": '../View/listarLivros.php',
			"dataType": 'JSON',
			"error": function () {
				alert('Não há registros para sua pesquisa');
			}
		},
		processing: true,
		serverSide: true,
		autoWidth: false,
		language: {
			url: "../lib/pt_br.json"
		},
		columns: [
			{ visible: false, searchable: false }, // ID (0)
			{ width: "22%", className: "text-start" }, // Nome (1)
			{ width: "18%", className: "text-start" }, // Autor (2)
			{ width: "12%", className: "text-start" }, // Editora (3)
			{ width: "8%",  className: "text-start" }, // Tombo (4)
			{ width: "8%",  className: "text-center" }, // Quantidade (5)
			{ width: "10%", className: "text-start" }, // Gênero (6)
			{ width: "8%",  className: "text-start" }, // Idioma (7)
			{ width: "5%",  className: "text-center" }, // Visualizar (8)
			{ width: "5%",  className: "text-center" }, // Editar (9)
			{ width: "5%",  className: "text-center" }  // Excluir (10)
		],
		columnDefs: [
			{
				"defaultContent": "-",
				"targets": "_all"
			}
		],
		"lengthMenu": [[10, 15, 25, 50, 100, -1], [10, 15, 25, 50, 100, "Tudo"]],
	});
</script>

</body>
</html>