<?php
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

	<title>EducaBiblio</title>
</head>
<style>
	.pagination {
		text-align: center;
		margin-top: 15px;

	}

	.page-link {
		display: inline-block;
		padding: 5px 10px;
		margin: 2px;
		border: 1px solid #333;
		background-color: #fff;
		color: #333;
		text-decoration: none;
		border-radius: 5px;
		transition: background-color 0.3s, color 0.3s;
	}

	.page-link.active {
		background-color: #333;
		color: #fff;
	}

	.page-link:hover {
		background-color: #333;
		color: #fff;
	}
</style>

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
			<li>
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
			<li class="active">
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
				/* Alinhamento à esquerda para a tabela de recomendações */
				#content main .table-data .order table th,
				#content main .table-data .order table td {
					text-align: left !important;
					vertical-align: middle !important;
				}

				#content main .table-data .head h3 {
					color: var(--cinzaEscuro);
					margin-right: auto !important;
					margin-left: auto;
					font-size: 2rem;
					font-family: 'Mulish', sans-serif;
					font-weight: bolder;
					margin-top: -15px;
				}

				.btn-editar-rec-topo {
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
					margin-bottom: 15px;
				}
				.btn-editar-rec-topo:hover {
					background-color: #2e8b34;
					color: #fff;
				}
			</style>
			<main>
				<div class="table-data">
					<div class="order">
						<div class="head">
							<h3>Tabela de Recomendações</h3>
							<button type="button" class="btn-editar-rec-topo" onclick="abrirNovaRec()">
								<i class="fas fa-plus"></i> Nova Recomendação
							</button>
						</div>
						<table>

							<?php
							$conexao = new CConexao();
							$conn = $conexao->getConnection();

							// Consulta para obter os dados da tabela de usuários
							$sql = "SELECT
                recomendacao.LivroRec,
                recomendacao.idRec,
                recomendacao.AutorRec,
                recomendacao.CatRec,
                recomendacao.CamRec
            FROM recomendacao";

							$result = $conn->query($sql);

							if ($result === false) {
								// Use errorInfo para obter informações sobre o erro
								$errorInfo = $conn->errorInfo();
								echo "Erro na consulta SQL: " . $errorInfo[2];
							} else {
								if ($result->rowCount() > 0) {
									$rec = $result->fetchAll(PDO::FETCH_ASSOC);
									$RecPorPagina = 4;
									$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
									$indiceInicial = ($paginaAtual - 1) * $RecPorPagina;
									$RecExibidos = array_slice($rec, $indiceInicial, $RecPorPagina);

									// Exibir a tabela de usuários
									echo "<table>";
									echo "<thead>";
									echo "<tr>";
									echo "<th>Livro</th>";
									echo "<th>ID</th>";
									echo "<th>Autor</th>";
									echo "<th>Categoria</th>";
									echo "<th>Imagem</th>";
									echo "<th>Editar</th>";
									echo "</tr>";
									echo "</thead>";
									echo "<tbody>";

									foreach ($RecExibidos as $row) {
										echo "<tr>";
										echo "<td>" . $row["LivroRec"] . "</td>";
										echo "<td>" . $row["idRec"] . "</td>";
										echo "<td>" . $row["AutorRec"] . "</td>";
										echo "<td>" . $row["CatRec"] . "</td>";
										echo "<td><img src='" . $row["CamRec"] . "' alt='Imagem do Livro' /></td>";
										echo "<td><button class='edit-button' onclick='editarRec(" . $row["idRec"] . ")'><i class='fas fa-pencil-alt'></i></button></td>";
										echo "</tr>";
									}

									echo "</tbody>";
									echo "</table>";

									

									// Botão Fechar do popup fora da tabela

								} else {
									echo "<p>Nenhum usuário encontrado.</p>";
								}
							}

							$conn = null; // Fecha a conexão
							?>
					</div>

				</div>

				<!-- Criando Modal Editar Recomendação -->
				<div class="modal fade" id="modalRec" tabindex="-1" aria-labelledby="modalRecLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="modalRecLabel">Editar Recomendação</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 1.2rem; cursor: pointer;">&times;</button>
							</div>
							<div class="modal-body">
								<form action="../Router/Rec_rotas.php" method="post" id="form-rec" enctype="multipart/form-data">
									<div class="mb-3">
										<label class="form-label">ID da Recomendação:</label>
										<input type="text" id="idRec" name="idRec" class="form-control" autocomplete="off" readonly>
									</div>

									<div class="mb-3">
										<label class="form-label">Nome do Livro:</label>
										<input type="text" placeholder="Nome do Livro" name="LivroRec" id="LivroRec" class="form-control" autocomplete="off" required>
									</div>

									<div class="mb-3">
										<label class="form-label">Autor:</label>
										<input type="text" placeholder="Nome do Autor" name="AutorRec" id="AutorRec" class="form-control" autocomplete="off" required>
									</div>

									<div class="mb-3">
										<label class="form-label">Categoria:</label>
										<input type="text" placeholder="Categoria" name="CatRec" id="CatRec" class="form-control" autocomplete="off" required>
									</div>

									<div class="mb-3">
										<label class="form-label">Imagem de Capa:</label>
										<input type="file" name="ImgRec" id="ImgRec" class="form-control">
									</div>
								</form>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
								<button type="submit" form="form-rec" name="atualizar" value="Enviar" class="btn btn-success">Salvar Recomendação</button>
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
<script src="../lib/Bootstrap 2/bootstrap.bundle.min.js"></script>
<script>
	let modalRecObj;

	function abrirNovaRec() {
		if (!modalRecObj) {
			modalRecObj = new bootstrap.Modal(document.getElementById('modalRec'));
		}
		$('#modalRecLabel').text('Nova Recomendação');
		$('#idRec').val('');
		$('#LivroRec').val('');
		$('#AutorRec').val('');
		$('#CatRec').val('');
		$('#ImgRec').val('');
		modalRecObj.show();
	}

	function editarRec(id) {
		if (!modalRecObj) {
			modalRecObj = new bootstrap.Modal(document.getElementById('modalRec'));
		}
		$('#modalRecLabel').text('Editar Recomendação');

		$('table tbody tr').each(function() {
			var rowId = $(this).find('td:eq(1)').text().trim();
			if (rowId == id) {
				var livro = $(this).find('td:eq(0)').text().trim();
				var autor = $(this).find('td:eq(2)').text().trim();
				var categoria = $(this).find('td:eq(3)').text().trim();

				$('#idRec').val(id);
				$('#LivroRec').val(livro);
				$('#AutorRec').val(autor);
				$('#CatRec').val(categoria);

				modalRecObj.show();
			}
		});
	}
</script>
</body>

</html>