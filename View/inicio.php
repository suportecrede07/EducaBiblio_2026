<?php
include('../Controller/CConexao.php');
include('../Controller/CLog_usu.php');
require '../Controller/CGet_rec.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
	header("Location: ../login.php"); // Redirecionar para a página de login se não estiver logado
	exit();
}


$conexao = new CConexao();
$conn = $conexao->getConnection();
$getlivro = new GetLivro($conn);
$livrosRecomendados = $getlivro->obterLivrosRecomendados();
include('../Controller/CPendencias.php'); // Inclua o arquivo CPendencias.php aqui
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
	<title>EducaBiblio</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="../ArquivosExternos/icons.js"></script>
	<link rel="shortcut icon" href="../img/icon1.png" type="image/x-icon">
	<link rel="stylesheet" href="../CSS/style.css">
	<link rel="stylesheet" href="../lib/datatables/dataTables.dataTables.min.css">
	<link rel="stylesheet" href="../CSS/darkPaginacao.css">
	<style>
		/* Alinhamento à esquerda e espaçamento harmonioso sem linhas visíveis */
		#TableHistorico th,
		#TableHistorico td {
			text-align: left !important;
			vertical-align: middle !important;
			padding: 10px 14px !important;
			border: none !important;
		}
		#TableHistorico thead th {
			text-align: left !important;
		}
		#TableHistorico tbody tr {
			border: none !important;
		}
		#TableHistorico tbody tr:hover {
			background-color: transparent !important;
		}

		/* Garantia do botão PDF no formato pílula verde na ponta direita */
		.pdf-button, #pdf-button, button.pdf-button {
			margin-left: auto !important;
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
		.pdf-button:hover, #pdf-button:hover, button.pdf-button:hover {
			background-color: #218838 !important;
			color: #fff !important;
		}
	</style>
</head>

<body>
	<section id="sidebar" class="page-transition">
		<a href="#" class="brand">
			<i class="fas fa-book"></i>
			<span class="text">EducaBiblio</span>
		</a>
		<ul class="side-menu top">
			<li class="active">
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
			<li id='li-toggle-sidebar'>
				<a href='#' id='btn-toggle-sidebar' style='cursor: pointer;'>
					<i class='fas fa-chevron-left'></i>
					<span class='text'>Recolher menu</span>
				</a>
			</li>
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
		<main>
			<div class="head-title">
				<div class="left">
					<h1>Olá, <b><?php
								if (isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true) {
									$nomeDoUsuario = $_SESSION['nomeDoUsuario'];
									echo $nomeDoUsuario; // Exibe o nome do usuário
								} else {
									// Usuário não está logado, redireciona ou exibe uma mensagem de erro
								}
								?> 👋</b>!</h1>
					<style type="text/css">
						b {
							color: #32CD32;
						}
					</style>
					<p>Seja bem-vindo(a) ao EducaBiblio!</p>
				</div>
			</div>

			<ul class="box-info">
				<li class="card-alunos">
					<div class="card-top">
						<div class="card-title-group">
							<span class="card-label">ALUNOS</span>
							<h3 class="card-number">286</h3>
						</div>
						<div class="card-icon card-icon-green">
							<i class="fas fa-graduation-cap"></i>
						</div>
					</div>
					<div class="card-bottom">
						<span class="card-tag tag-green">↑ +12%</span>
						<span class="card-subtext">neste mês</span>
					</div>
				</li>
				<li class="card-emprestimos">
					<div class="card-top">
						<div class="card-title-group">
							<span class="card-label">EMPRÉSTIMOS</span>
							<h3 class="card-number">1707</h3>
						</div>
						<div class="card-icon card-icon-yellow">
							<i class="fas fa-address-book"></i>
						</div>
					</div>
					<div class="card-bottom">
						<span class="card-tag tag-green">↑ +8.4%</span>
						<span class="card-subtext">vs mês anterior</span>
					</div>
				</li>
				<li class="card-pendencias">
					<div class="card-top">
						<div class="card-title-group">
							<span class="card-label">PENDÊNCIAS</span>
							<h3 class="card-number">58</h3>
						</div>
						<div class="card-icon card-icon-orange">
							<i class="fas fa-users"></i>
						</div>
					</div>
					<div class="card-bottom">
						<span class="card-tag tag-orange">4 críticas</span>
						<span class="card-subtext">necessitam atenção</span>
					</div>
				</li>
				<li class="card-acervo">
					<div class="card-top">
						<div class="card-title-group">
							<span class="card-label">ACERVO</span>
							<h3 class="card-number">3579</h3>
						</div>
						<div class="card-icon card-icon-purple">
							<i class="fas fa-book"></i>
						</div>
					</div>
					<div class="card-bottom">
						<span class="card-tag tag-purple">+142</span>
						<span class="card-subtext">novos títulos</span>
					</div>
				</li>
			</ul>
			<br>
			<br>

			<style>
				/* Esconde as setas para campos de entrada numérica */
				input[type=number]::-webkit-inner-spin-button,
				input[type=number]::-webkit-outer-spin-button {
					-webkit-appearance: none;
					margin: 0;
				}

				input[type=number] {
					-moz-appearance: textfield;
					/* Firefox */
				}



				.searchInput {
					width: 20% !important;
					height: 30px;
					background-color: #f2f2f2;
					border: 1px solid #ccc;
					border-radius: 5px;
					padding: 5px;
				}
			</style>
			<div class="table-data">
				<div class="order">
					<div class="head">
						<h3>Histórico de Empréstimos</h3>

						<button class="pdf-button" id="pdf-button" aria-label="botão pdf" onclick="abrirAluno()">
							<i class="fas fa-file-pdf"></i> Exportar Relatório PDF
						</button>

						<script>
							function abrirAluno() {
								var urlDoPDF = "../pdf/historicoPdf.php";
								window.open(urlDoPDF, '_blank');
							}
						</script>

					</div>
					<table id="TableHistorico" style="width:100%">
						<thead>
							<tr>
								<th>Id</th>
								<th>Livro</th>
								<th>Nome</th>
								<th>Data de empréstimo</th>
								<th>Data de devolução</th>
								<th>Estado</th>
							</tr>
						</thead>
					</table>
					
					</div>

			</div>
			<footer class="footer">

				© Copyright 2023 por <span>EducaBiblio</span> | Todos os direitos reservados

			</footer>
		</main>
	</section>

	<script src="../JS/script.js"></script>
	<script src="../lib/jquery/jquery-3.7.1.min.js"></script>
	<script src="../lib/datatables/dataTables.js"></script>
	<script src="../lib/Bootstrap 2/bootstrap.bundle.min.js"></script>


	<script>
		new DataTable('#TableHistorico', {
			ajax: {
				"url": '../View/listarHistorico.php',
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
				{ visible: false },                       // Id (oculto)
				{ width: "30%", className: "text-start" }, // Livro
				{ width: "26%", className: "text-start" }, // Nome do Leitor
				{ width: "16%", className: "text-start" }, // Data de empréstimo
				{ width: "16%", className: "text-start" }, // Data de devolução
				{ width: "12%", className: "text-start" }  // Estado
			],
			columnDefs: [{
				"defaultContent": "-",
				"targets": "_all"
			}],
			"lengthMenu": [[10, 15, 25, 50, 100, -1], [10, 15, 25, 50, 100, "Tudo"]],
		});
	</script>
	<script>
		$('#searchInput').on('keyup', function() {
			const value = $(this).val().toLowerCase();

			$('table tbody tr').filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
			});
		});
	</script>
</body>

</html>