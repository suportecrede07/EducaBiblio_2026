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
	<link rel="stylesheet" href="../lib/datatables/dataTables.dataTables.min.css">
	<link rel="stylesheet" href="../CSS/popup4.css">
	<link rel="stylesheet" href="../CSS/darkPaginacao.css">
	<style>
		/* Alinhamento à esquerda para cabeçalho e corpo da tabela de turmas */
		#TableTurma th,
		#TableTurma td {
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
			<li class="active">
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
					/* Firefox */
				}

				.btn-cadastrar-turma {
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
				.btn-cadastrar-turma:hover {
					background-color: #2e8b34;
					color: #fff;
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
			<main>
				<div class="table-data">
					<div class="order">
						<div class="head">
							<span id="msgAlerta"></span>
							<h3>Tabela de Cadastro de Turmas</h3>
							<div style="display: flex; align-items: center; gap: 10px; margin-left: auto; flex-wrap: wrap;">
								<button type="button" class="btn-cadastrar-turma" data-bs-toggle="modal" data-bs-target="#cadTurmasModal">
									<i class="fas fa-plus"></i> Cadastrar Turma
								</button>

								<button class="pdf-button" id="pdf-button" aria-label="botão pdf" onclick="abrirAluno()">
									<i class="fas fa-file-pdf"></i> Exportar Relatório PDF
								</button>
							</div>

							<script>
								function abrirAluno() {
									var urlDoPDF = "../pdf/turmaPdf.php";
									window.open(urlDoPDF, '_blank');
								}
							</script>

						</div>
						<span id="msgAlerta"></span>
						<table id="TableTurma" style="width:100%">
								<thead>
									<tr>
										<th>ID</th>
										<th>Ano</th>
										<th>Nome</th>
										<th>Ano de Início</th>
										<th>Editar</th>
										<th>Excluir</th>
									</tr>
								</thead>
							</table>
							
						</table>
						
					</div>

				</div>

				<!-- Criando Modal Cadastrar Turma -->
				<div class="modal fade" id="cadTurmasModal" tabindex="-1" aria-labelledby="cadTurmasModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="cadTurmasModalLabel">Cadastro de Turma</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 1.2rem; cursor: pointer;">&times;</button>
							</div>
							<div class="modal-body">
								<form action="../Router/turma_rotas.php" method="post" id="form-cad-turmas">
									<div class="mb-3">
										<label class="form-label">Série / Ano da Turma:</label>
										<input type="text" placeholder="Ex: 1º, 2º, 3º" name="AnoTurma" id="AnoTurma" class="form-control" autocomplete="off" required>
									</div>

									<div class="mb-3">
										<label class="form-label">Nome da Turma / Curso:</label>
										<input type="text" placeholder="Ex: Informática, Redes, Administração" name="NomeTurma" id="NomeTurma" class="form-control" autocomplete="off" required>
									</div>

									<div class="mb-3">
										<label class="form-label">Ano de Início:</label>
										<input type="number" placeholder="Ano de início (Ex: 2024)" name="AnodeInicio" id="AnodeInicio" class="form-control" autocomplete="off">
									</div>
								</form>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
								<button type="submit" form="form-cad-turmas" name="action" value="Enviar" class="btn btn-success">Cadastrar Turma</button>
							</div>
						</div>
					</div>
				</div>

				<!-- Criando Modal Editar -->
				<div class="modal fade" id="editTurmasModal" tabindex="-1" arial-labelledby="editTurmasModalLabel" arial-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="editTurmasModalLabel">Editar Turmas</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 1.2rem; cursor: pointer;">&times;</button>
							</div>
							<div class="modal-body">
								<span id="msgAlertErroEdit"></span>
								<form method="POST" id="form-edit-turmas" action="../Modais/editarTurma.php">
									<div class="mb-3">
										<input type="text" class="form-control" name="idTurma" id="editIdTurma" required autocomplete="off" readonly>
									</div>
									<div class="mb-3">
										<label for="form-control">Ano Turma:</label>
										<input type="text" class="form-control" name="AnoTurma" id="editAnoTurma" required autocomplete="off" >
									</div>
									<div class="mb-3">
										<label for="form-control">Nome Turma:</label>
										<input type="text" class="form-control" name="NomeTurma" id="editNomeTurma" required autocomplete="off" >
									</div>
									
								</form>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
								<button type="submit" class="btn btn-warning" form="form-edit-turmas" value="Salvar">Salvar Alterações</button>
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

<script>
	$(document).ready(function() {
		// Capturar clique no botão de edição
		$('.edit-button').click(function() {
			// Obter o ID da turma a ser editada
			var id = $(this).closest('tr').find('td:eq(0)').text(); // Considerando que o ID está na primeira coluna

			// Encontrar os dados correspondentes na tabela de turmas e preencher o formulário
			$('table tbody tr').each(function() {
				var rowId = $(this).find('td:eq(0)').text(); // Considerando que o ID está na primeira coluna
				if (rowId == id) {
					var anoInicio = $(this).find('td:eq(3)').text(); // Considerando que o Ano de início está na quarta coluna
					var anoTurma = $(this).find('td:eq(1)').text(); // Considerando que a Série está na segunda coluna
					var nomeTurma = $(this).find('td:eq(2)').text(); // Considerando que o Nome da turma está na terceira coluna

					// Preencher os campos do formulário com os dados obtidos
					$('#IdTurma').val(id);
					$('#AnodeInicio').val(anoInicio);
					$('#AnoTurma').val(anoTurma);
					$('#NomeTurma').val(nomeTurma);

					// Alterar o valor e o nome do botão de enviar para atualizar
					$('form').attr('action', '../Router/turmas2_rotas.php'); // Alterar o action do formulário
					$('input[type="submit"][name="action"]').val('Atualizar');
					$('input[type="submit"][name="action"]').attr('name', 'updateAction');
				}
			});
		});
	});
</script>
<script>
	$(document).ready(function() {
		$('.sortable').click(function() {
			const column = $(this).data('column');
			const order = $(this).hasClass('asc') ? 'desc' : 'asc';

			$('.sortable').removeClass('asc desc');
			$(this).addClass(order);

			sortTable(column, order);
		});
	});

	function sortTable(column, order) {
		const $tbody = $('table tbody');
		const $rows = $tbody.find('tr').get();

		$rows.sort(function(a, b) {
			const keyA = $(a).find(`td[data-column='${column}']`).text();
			const keyB = $(b).find(`td[data-column='${column}']`).text();

			if (order === 'asc') {
				return keyA.localeCompare(keyB);
			} else {
				return keyB.localeCompare(keyA);
			}
		});

		$.each($rows, function(index, row) {
			$tbody.append(row);
		});
	}
</script>

<script>
	$('#searchInput').on('keyup', function() {
		const value = $(this).val().toLowerCase();

		$('table tbody tr').filter(function() {
			$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
		});
	});
</script>

<!-- Função do Modal Editar -->
<script>
	const editModal = new bootstrap.Modal(document.getElementById('editTurmasModal'));
	async function editTurma(idTurma) {
		//console.log(idEmprestimo);
		const dados = await fetch('../Modais/visualizarTurmas.php?idTurma=' + idTurma);
		const resposta = await dados.json();
		//console.log(resposta);

		if(resposta['status']){
			document.getElementById("msgAlerta").innerHTML = "";
			editModal.show();

			document.getElementById("editIdTurma").value = resposta['dados'].idTurma;
			document.getElementById("editAnoTurma").value = resposta['dados'].AnoTurma;
			document.getElementById("editNomeTurma").value = resposta['dados'].NomeTurma;
		}else{
			alert(resposta['msg']);
		}
	}
</script>

<script>
	async function excluirTurma(idTurma) {
		//console.log(idTurma);

		const dados = await fetch("../Controller/CExcluir_turma.php?id=" + idTurma);

		//console.log(resposta);
		var confirmar = confirm("Você realmente quer apagar o registro selecionado?");

		if(confirmar){
			document.getElementById("msgAlerta").innerHTML = "<div class='alert alert-success' role='alert'>Registro apagado com sucesso!</div>";
			listarDataTables = $('#TableTurma').DataTable();
			listarDataTables.draw();
		}else{
			document.getElementById("msgAlerta").innerHTML = "<div class='alert alert-danger' role='alert'> Erro: Registro não apagado</div>";
		}
	}
</script>

<!-- Salvando informações no banco de dados -->
<script>
	const formEditTurmas = document.getElementById("form-edit-turmas");
	if (formEditTurmas) {
		formEditTurmas.addEventListener("submit", async (e) => {
			e.preventDefault();
			const dadosForm = new FormData(formEditTurmas);

			try {
				const dados = await fetch("../Modais/editarTurma.php", {
					method: "POST",
					body: dadosForm
				});

				const resposta = await dados.json();

				if (resposta['status']) {
					document.getElementById("msgAlerta").innerHTML = resposta['msg'];
					document.getElementById("msgAlertErroEdit").innerHTML = "";
					formEditTurmas.reset();
					editModal.hide();
					listarDataTables = $('#TableTurma').DataTable();
					listarDataTables.draw();
				} else {
					document.getElementById("msgAlertErroEdit").innerHTML = resposta['msg'];
				}
			} catch (error) {
				console.error(error);
			}
		});
	}
</script>

<script>
	new DataTable('#TableTurma', {
		ajax: {
			"url": '../View/listarTurma.php',
			"dataType": 'JSON',
			"error": function () {
				alert('Não há registros para sua pesquisa');
			}
		},
		processing: true,
		serverSide: true,
		language: {
			url: "../lib/pt_br.json"
		},
		columnDefs: [{
			"defaultContent": "-",
			"targets": "_all",
			"className": "text-center"
		}],
		"lengthMenu": [[10, 15, 25, 50, 100, -1], [10, 15, 25, 50, 100, "Tudo"]],
	});
</script>
</body>

</html>