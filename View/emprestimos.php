<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once '../Controller/CConexao.php';

if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
    header("Location: ../View/login.php");
    exit();
}

$conexao = new CConexao();
$conn = $conexao->getConnection();

if (isset($_GET['msg'])) {
	$mensagens = [
		'sucesso' => "✅ Empréstimo realizado com sucesso!",
		'emprestimo_ativo_bloqueado' => "🚫 Este aluno já possui um empréstimo ativo. Não é permitido fazer outro.",
		'erro' => "❌ Ocorreu um erro ao registrar o empréstimo."
	];

	if (array_key_exists($_GET['msg'], $mensagens)) {
		echo "<p style='color: red; font-weight: bold; text-align: center;'>{$mensagens[$_GET['msg']]}</p>";
	}
}

// Inicialize a instância da classe de conexão.
$conexao = new CConexao();
$conn = $conexao->getConnection();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="../ArquivosExternos/icons.js"></script>
	<link rel="shortcut icon" href="../img/icon1.png" type="image/x-icon">
	<link href="../lib/select2/select2.min.css" rel="stylesheet" id="select2" />
	<link rel="stylesheet" href="../CSS/style.css">
	<link rel="stylesheet" href="../lib/Bootstrap 2/bootstrap.min.css">
	<link rel="stylesheet" href="../lib/datatables/dataTables.dataTables.min.css">
	<link rel="stylesheet" href="../CSS/darkPaginacao.css">
	<style>
		/* Alinhamento à esquerda para tabelas de empréstimos */
		#TableEmprestimos th,
		#TableEmprestimos td {
			text-align: left !important;
			vertical-align: middle !important;
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
			<li class="active">
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

				.searchInput {
					width: 20% !important;
					height: 30px;
					background-color: #f2f2f2;
					border: 1px solid #ccc;
					border-radius: 5px;
					padding: 5px;
				}

				.btn-cadastrar-emp {
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

				.btn-cadastrar-emp:hover {
					background-color: #2e8b34;
					color: #fff;
				}
			</style>
			<main>
				<div class="table-data">
					<div class="order">
						<div class="head">
							<h3>Tabela de Empréstimos</h3>
							<button type="button" class="btn-cadastrar-emp" data-bs-toggle="modal" data-bs-target="#cadEmpModal">
								<i class="fas fa-plus"></i> Novo Empréstimo
							</button>
						</div>

						<span id="msgAlerta"></span>

						<table id="TableEmprestimos" class="tabelaEmp">
								<thead>
									<tr>
										<th>Livro</th>
										<th>Gênero</th>
										<th>Id</th>
										<th>Ano</th>
										<th>Turma</th>
										<th>Leitor</th>
										<th>Data de Empréstimo</th>
										<th>Data para Devolução</th>
										<th>Visualizar</th>
										<th>Editar</th>
										<th>Renovar</th>
										<th>Excluir</th>
									</tr>
								</thead>
							</table>
					</div>
				</div>

				<!-- Criando Modal Cadastrar Empréstimo -->
				<div class="modal fade" id="cadEmpModal" tabindex="-1" aria-labelledby="cadEmpModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="cadEmpModalLabel">Novo Empréstimo</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 1.2rem; cursor: pointer;">&times;</button>
							</div>
							<div class="modal-body">
								<form action="../Router/emp_rotas.php" method="post" id="form-cad-emp">
									<div class="mb-3">
										<label class="form-label">Gênero:</label>
										<select id="Genero_idGenero" name="genero_idGenero" class="form-control" required>
											<option value="">Selecione um gênero</option>
											<?php
											$query = "SELECT idGenero, NomeGenero FROM genero";
											$stmt = $conn->query($query);
											while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
												echo "<option value='" . $row['idGenero'] . "'>" . $row['NomeGenero'] . "</option>";
											}
											?>
										</select>
									</div>

									<div class="mb-3">
										<label class="form-label">Livro:</label>
										<select id="livro_idLivro" name="livro_idLivro" class="form-control" style="text-align: left; width: 100%;" required>
											<option value="">Selecione um livro</option>
										</select>
									</div>

									<div class="mb-3">
										<label class="form-label">Turma / Tipo:</label>
										<select id="Turma_idTurma" name="Turma_idTurma" class="form-control" required>
											<option value="">Selecione a Turma</option>
											<option value="0">Professor</option>
											<?php
											$query = "SELECT IdTurma, AnodeInicio, AnoTurma, NomeTurma FROM turma";
											$stmt = $conn->query($query);
											while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
												echo "<option value='" . $row['IdTurma'] . "'>" . $row['AnoTurma'] . 'º ' . $row['NomeTurma'] . "</option>";
											}
											?>
										</select>
									</div>

									<div class="mb-3" id="div_cad_prof" style="display: none;">
										<label class="form-label">Professor:</label>
										<select id="prof_idProf" name="prof_idProf" class="form-control">
											<option value="">Selecione um professor</option>
										</select>
									</div>

									<div class="mb-3" id="div_cad_aluno" style="display: none;">
										<label class="form-label">Aluno:</label>
										<select id="aluno_idAluno" name="aluno_idAluno" class="form-control">
											<option value="">Selecione um aluno</option>
										</select>
									</div>

									<div class="mb-3">
										<label class="form-label">Quantidade:</label>
										<input type="number" placeholder="Quantidade" name="quantidade" id="cadQuantidade" class="form-control" value="1" min="1" required autocomplete="off">
									</div>

									<div class="mb-3">
										<label class="form-label">Data de Empréstimo:</label>
										<input type="date" name="DataEmprestimo" id="DataEmprestimo" class="form-control" required autocomplete="off">
									</div>

									<div class="mb-3">
										<label class="form-label">Data de Devolução:</label>
										<input type="date" name="DataDevolucao" id="DataDevolucao" class="form-control" required autocomplete="off">
									</div>

									<div class="mb-3">
    <label class="form-label">Responsável pelo Empréstimo:</label>

    <select id="usuario_idUsuario"
            name="usuario_idUsuario"
            class="form-control"
            required>

        <?php

        $username = $_SESSION['nomeDoUsuario'] ?? '';

        $query = "SELECT idUsuario, UserUsuario
                  FROM usuario
                  WHERE UserUsuario = :username";

        $stmt = $conn->prepare($query);
        $stmt->bindValue(':username', $username);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            echo '<option value="' . htmlspecialchars($usuario['idUsuario']) . '" selected>';
            echo htmlspecialchars($usuario['UserUsuario']);
            echo '</option>';
        } else {
            echo '<option value="">Usuário não encontrado</option>';
        }

        ?>

    </select>
</div>
								</form>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
								<button type="submit" form="form-cad-emp" name="emprestar" value="Enviar" class="btn btn-success">Salvar Empréstimo</button>
							</div>
						</div>
					</div>
				</div>

				<!-- Criando Modal Visualizar -->
				<div class="modal fade" id="visEmpModal" tabindex="-1" arial-labelledby="visEmpModalLabel" arial-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="visEmpModalLabel">Detalhes do Emprestimo</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 1.2rem; cursor: pointer;">&times;</button>
							</div>
							<div class="modal-body">
								<dl class="row">
									<dt class="col-sm-3">ID</dt>
									<dd class="col-sm-9"><span id="idEmp"></span></dd>

									<dt class="col-sm-3">Livro</dt>
									<dd class="col-sm-9"><span id="nomeLivro"></span></dd>

									<dt class="col-sm-3">Gênero</dt>
									<dd class="col-sm-9"><span id="GeLivro"></span></dd>

									<dt class="col-sm-3">Leitor</dt>
									<dd class="col-sm-9"><span id="NomeLeitor"></span></dd>

									<dt class="col-sm-3">Data de Empréstimos</dt>
									<dd class="col-sm-9"><span id="dataEmprestimo"></span></dd>

									<dt class="col-sm-3">Data para Devolução</dt>
									<dd class="col-sm-9"><span id="dataDevolucao"></span></dd>
								</dl>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
							</div>
						</div>
					</div>
				</div>

				<!-- Criando Modal Editar -->
				<div class="modal fade" id="editEmpModal" tabindex="-1" arial-labelledby="editEmpModalLabel" arial-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="editEmpModalLabel">Editar Empréstimo</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 1.2rem; cursor: pointer;">&times;</button>
							</div>
							<div class="modal-body">
								<span id="msgAlertErroEdit"></span>
								<form method="POST" id="form-edit-emp" action="../Modais/editarEmp.php">
									<div class="mb-3">
										<input type="text" class="form-control" name="idEmprestimo" id="editId" required autocomplete="off" readonly>
									</div>
									<div class="mb-3">
										<label for="form-control">Selecione um Gênero:</label>
										<select id="Genero_editGenero" name="generoEmp" class="form-control" required>
											<option value="">Selecione um gênero</option>
											<?php
											// Preencha as opções de gênero a partir do banco de dados.
											$query = "SELECT idGenero, NomeGenero FROM genero";
											$stmt = $conn->query($query);
											while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
												echo "<option value='" . $row['idGenero'] . "'>" . $row['NomeGenero'] . "</option>";
											}
											?>
										</select>
									</div>
									<div class="mb-3">
										<label for="form-control">Selecione um Livro:</label>
										<select id="livro_editLivro" name="livroEmp" class="form-control">
											<option value=''>Selecione um livro</option> <!-- Opções de livros serão preenchidas dinamicamente com JavaScript -->
										</select>
									</div>
									<div class="mb-3">
										<label for="form-control">Selecione uma Turma:</label>
										<select id="Turma_editTurma" name="turmaEmp" class="form-control" required>
											<option value="">Turma</option>
											<option value="0">Professor</option>
											<?php
											// Preencha as opções de turma a partir do banco de dados.
											$query = "SELECT IdTurma, AnodeInicio, AnoTurma, NomeTurma FROM turma";
											$stmt = $conn->query($query);
											while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
												echo "<option value='" . $row['IdTurma'] . "'>" . $row['AnoTurma'] . 'º ' . $row['NomeTurma'] . "</option>";
											}
											?>
										</select>
									</div>
									<div class="mb-3">
										<label for="form-control">Selecione um Aluno:</label>
										<select id="aluno_editAluno" name="alunoEmp" class="form-control">
											<option value="">Selecione um aluno</option>
											<!-- Opções de alunos serão preenchidas dinamicamente com JavaScript -->
										</select>
									</div>
								</form>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
								<button type="submit" class="btn btn-warning" form="form-edit-emp" value="Salvar">Salvar Alterações</button>
							</div>
						</div>
					</div>
				</div>

				<!-- Criando Modal Renovar -->
				<div class="modal fade" id="renovarEmpModal" tabindex="-1" arial-labelledby="renovarEmpModalLabel" arial-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="renovarEmpModalLabel">Renovar Empréstimo</h5>
							</div>
							<div class="modal-body">
								<form method="POST" id="form-renovar-emp">
									<div class="mb-3">
										<label for="form-control" class="datas">Data Atual:</label>
										<div class="form-control" readonly><span id="dataAtual"></span></div>
									</div>
									<div class="mb-3">
										<label for="form-control" class="datas">Data de Devolução Atual:</label>
										<div class="form-control" readonly><span id="viewDataDev"></span></div>
									</div>

									<div class="mb-3">
										<label for="form-control">Adicione um novo prazo para Devolução:</label>
										<input type="date" placeholder="DD/MM/AAAA" name="DataRenovada" id="DataDev" required class="form-control" autocomplete="off" required>
									</div>

								</form>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
								<button type="button" class="btn btn-info" data-bs-dismiss="modal" id="btnRenovar">Renovar</button>
							</div>
						</div>
					</div>
				</div>

				<!-- Criando Modal Excluir -->
				<div class="modal fade" id="excluirEmpModal" tabindex="-1" arial-labelledby="excluirEmpModalLabel" arial-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="excluirEmpModalLabel">Excluir Empréstimo</h5>
							</div>
							<div class="modal-body">
								<div class="alert alert-danger" role="alert">
									Deseja mesmo excluir este empréstimo?
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
								<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Excluir</button>
							</div>
						</div>
					</div>
				</div>



				<div class="footer">
					<footer>

						© Copyright 2023 por <span>EducaBiblio</span> | Todos os direitos reservados

					</footer>
				</div>

			</main>
	</section>

</body>

</html>


<script src="../JS/script.js"></script>
<script src="../ArquivosExternos/Jquery.js"></script>
<script src="../lib/datatables/dataTables.js"></script>
<script src="../lib/Bootstrap 2/bootstrap.bundle.min.js"></script>
<script src="../lib/select2/select2.min.js"></script>


<script>
	// Script para inicializar e calcular devolução automática de empréstimo
	$(document).ready(function() {
		const dataEmprestimo = document.getElementById("DataEmprestimo");
		const dataDevolucao = document.getElementById("DataDevolucao");

		function formatarDataInput(data) {
			const ano = data.getFullYear();
			const mes = String(data.getMonth() + 1).padStart(2, '0');
			const dia = String(data.getDate()).padStart(2, '0');
			return `${ano}-${mes}-${dia}`;
		}

		function calcularDevolucao() {
			if (!dataEmprestimo || !dataEmprestimo.value) return;
			const [ano, mes, dia] = dataEmprestimo.value.split('-').map(Number);
			const data = new Date(ano, mes - 1, dia);
			data.setDate(data.getDate() + 7);
			if (dataDevolucao) {
				dataDevolucao.value = formatarDataInput(data);
			}
		}

		if (dataEmprestimo && dataDevolucao) {
			const hoje = new Date();
			dataEmprestimo.value = formatarDataInput(hoje);
			calcularDevolucao();
			dataEmprestimo.addEventListener("change", calcularDevolucao);
		}
	});
</script>

<script>
	window.addEventListener('load', () => {
		if (window.history.replaceState) {
			const urlLimpa = window.location.origin + window.location.pathname;
			window.history.replaceState(null, '', urlLimpa);
		}
	});
	$(document).ready(function() {
		// Capturar clique no botão de edição
		$('editEmp()').click(function() {
			var idEmprestimo = $(this).closest('tr').find('td:eq(2)').text();
			/*var DataEmprestimo = $(this).closest('tr').find('td:eq(5)').text(); // Nome do livro na primeira coluna, ajuste conforme a estrutura real da sua tabela
        var DataDevolucao = $(this).closest('tr').find('td:eq(6)').text(); // Editora na terceira coluna, ajuste conforme a estrutura real da sua tabela*/

			preencherFormulario(idEmprestimo);

			// Alterar o modo de ação para editar
			$('#modoAcao').val('editar');
			// Alterar o valor do botão para refletir a ação de edição
			$('input[type="submit"]').val('Editar');
			// Modificar o action do formulário para o script responsável pela atualização
			$('form').attr('action', '../Router/Atualizar_emprotas.php');
			// Modificar o texto do botão de envio para "Atualizar"
			$('input[type="submit"]').attr('name', 'Editar');
		});
	});

	// Função para preencher o formulário com o ID do empréstimo ao clicar no botão de edição
	function preencherFormulario(idEmprestimo) {
		$('#idEmprestimo').val(idEmprestimo);
	}
</script>




<script>
	$(document).ready(function() {
		// Função para preencher os livros com base no gênero selecionado
		$("#Genero_idGenero").change(function() {
			var generoId = $(this).val();
			var livroSelect = $("#livro_idLivro");

			if (generoId) {
				$.ajax({
					type: "GET",
					url: "../Controller/CBuscar_livros.php",
					data: {
						generoId: generoId
					},
					success: function(data) {
						livroSelect.html(data);
					},
					error: function() {
						livroSelect.html("<option value=''>Erro ao carregar livros</option>");
					}
				});
			} else {
				livroSelect.html("<option value=''>Selecione um livro</option>");
			}
		});
	});
</script>

<script>
	$(document).ready(function() {
		// Função para preencher os livros com base no gênero selecionado
		$("#Genero_editGenero").change(function() {
			var generoId = $(this).val();
			var livroSelect = $("#livro_editLivro");

			if (generoId) {
				$.ajax({
					type: "GET",
					url: "../Controller/CBuscar_livros.php",
					data: {
						generoId: generoId
					},
					success: function(data) {
						livroSelect.html(data);
					},
					error: function() {
						livroSelect.html("<option value=''>Erro ao carregar livros</option>");
					}
				});
			} else {
				livroSelect.html("<option value=''>Selecione um livro</option>");
			}
		});
	});
</script>


<!-- Função do Modal Visualizar -->
<script>
	function formatarData(data) {
		const dataObj = new Date(data);
		dataObj.setDate(dataObj.getDate() + 1);
		const dia = String(dataObj.getDate()).padStart(2, '0');
		const mes = String(dataObj.getMonth() + 1).padStart(2, '0'); // Meses começam do zero
		const ano = dataObj.getFullYear();
		return `${dia}/${mes}/${ano}`; // Formato DD/MM/YYYY
	}

	async function visEmp(idEmprestimo) {
		//console.log(idEmprestimo);
		const dados = await fetch('../Modais/visualizarEmp.php?idEmprestimo=' + idEmprestimo);
		const resposta = await dados.json();
		//console.log(resposta);

		if (resposta['status']) {
			const viewModal = new bootstrap.Modal(document.getElementById('visEmpModal'));
			viewModal.show();

			document.getElementById("idEmp").innerHTML = resposta['dados'].idEmprestimo;
			document.getElementById("nomeLivro").innerHTML = resposta['dados'].TituloLivro;
			document.getElementById("GeLivro").innerHTML = resposta['dados'].NomeGenero;
			document.getElementById("NomeLeitor").innerHTML = resposta['dados'].NomeAluno;
			const dataEmprestimoFormatada = formatarData(resposta['dados'].DataEmprestimo);
			const dataDevolucaoFormatada = formatarData(resposta['dados'].DataDevolucao);
			document.getElementById("dataEmprestimo").innerHTML = dataEmprestimoFormatada;
			document.getElementById("dataDevolucao").innerHTML = dataDevolucaoFormatada;
		} else {
			alert(resposta['msg']);
		}
	}
</script>

<!-- Função do Modal Editar -->
<script>
	const editModal = new bootstrap.Modal(document.getElementById('editEmpModal'));
	async function editEmp(idEmprestimo) {
		//console.log(idEmprestimo);
		const dados = await fetch('../Modais/visualizarEmp.php?idEmprestimo=' + idEmprestimo);
		const resposta = await dados.json();
		//console.log(resposta);

		if (resposta['status']) {
			editModal.show();

			document.getElementById("editId").value = resposta['dados'].idEmprestimo;
			document.getElementById("Genero_idGenero").value = resposta['dados'].idGenero;
			/*document.getElementById("Turma_idTurma").value = resposta['dados'].turmaId;
			document.getElementById("aluno_idAluno").value = resposta['dados'].NomeAluno;*/
		} else {
			alert(resposta['msg']);
		}
	}
</script>

<!-- Função do Modal Renovar -->
<script>
	const renovarModal = new bootstrap.Modal(document.getElementById('renovarEmpModal'));

	function obterDataAtual() {
		const dataAtual = new Date();
		const ano = dataAtual.getFullYear();
		const mes = String(dataAtual.getMonth() + 1).padStart(2, '0');
		const dia = String(dataAtual.getDate()).padStart(2, '0');
		return `${dia}/${mes}/${ano}`; // Formato DD/MM/YYYY
	}

	function formatarData(data) {
		const dataObj = new Date(data);
		dataObj.setDate(dataObj.getDate() + 1);
		const dia = String(dataObj.getDate()).padStart(2, '0');
		const mes = String(dataObj.getMonth() + 1).padStart(2, '0'); // Meses começam do zero
		const ano = dataObj.getFullYear();
		return `${dia}/${mes}/${ano}`; // Formato DD/MM/YYYY
	}

	async function renovarEmp(idEmprestimo) {
		//console.log(idEmprestimo);
		const dados = await fetch('../Modais/visualizarEmp.php?idEmprestimo=' + idEmprestimo);
		const resposta = await dados.json();
		//console.log(resposta);

		if (resposta['status']) {
			renovarModal.show();
			//console.log(obterDataAtual());
			document.getElementById("dataAtual").innerText = obterDataAtual();
			const dataDevolucaoFormatada = formatarData(resposta['dados'].DataDevolucao);
			document.getElementById("viewDataDev").innerHTML = dataDevolucaoFormatada;
		} else {
			alert(resposta['msg']);
		}
	}
	async function renovar(idEmprestimo) {
		const id = idEmprestimo;
		const botao = document.getElementById("btnRenovar");
		const msgAlertaElement = document.getElementById("msgAlerta");

		botao.addEventListener('click', async () => {
			if (confirm("Você realmente quer editar o registro selecionado?")) {
				try {
					var dataNovaInput = document.getElementById("DataDev").value;
					const response = await fetch(`../Controller/CRenovar.php?id=${id}&data=${dataNovaInput}`);
					const dados = await response.json();
					setTimeout(function() {
						location.reload();
					}, 250);
					msgAlertaElement.innerHTML = `<div class='alert alert-success' role='alert'>Registro alterado com sucesso</div>`;

				} catch (error) {
					console.log(error);
				}
			} else {
				setTimeout(function() {
					location.reload();
				}, 250);
				msgAlertaElement.innerHTML = `<div class='alert alert-danger' role='alert'> Erro: Registro não alterado</div>`;
			}
		});
	}
</script>

<script>
	async function excluirEmp(idEmprestimo) {
		//console.log(idEmprestimo);	
		const dados = await fetch("../Modais/apagarEmp.php?idEmprestimo=" + idEmprestimo);
		const resposta = await dados.json();
		var confirmar = confirm("Você realmente quer apagar o registro selecionado?");

		//console.log(resposta);
		if (confirmar) {
			if (resposta['status']) {
				document.getElementById("msgAlerta").innerHTML = resposta['msg'];

				listarDataTables = $('#TableEmprestimos').DataTable();
				listarDataTables.draw();
			} else {
				document.getElementById("msgAlerta").innerHTML = resposta['msg'];
			}
		}
	}
</script>

<!-- Salvando informações no banco de dados -->
<script>
	const formEditEmp = document.getElementById("form-edit-emp");
	if (formEditEmp) {
		formEditEmp.addEventListener("submit", async (e) => {
			e.preventDefault();
			const dadosForm = new FormData(formEditEmp);

			const dados = await fetch("../Modais/editarEmp.php", {
				method: "POST",
				body: dadosForm
			});

			const resposta = await dados.json();

			if (resposta['status']) {
				//document.getElementById("msgAlertErroEdit").innerHTML = resposta['msg'];

				document.getElementById("msgAlerta").innerHTML = resposta['msg'];
				document.getElementById("msgAlertErroEdit").innerHTML = "";
				formEditEmp.reset();
				editModal.hide();
				listarDataTables = $('#TableEmprestimos').DataTable();
				listarDataTables.draw();
			} else {
				document.getElementById("msgAlertErroEdit").innerHTML = resposta['msg'];
			}

		});
	}
</script>


<!-- Função 1: select dinamico dentro da tabela empréstimos -->
<script>
	$(document).ready(function() {
		function toggleSelects() {
			var turmaId = $("#Turma_idTurma").val();
			var divAluno = $("#div_cad_aluno");
			var divProf = $("#div_cad_prof");
			var alunoSelect = $("#aluno_idAluno");
			var profSelect = $("#prof_idProf");

			if (turmaId !== null && turmaId !== '') {
				var url = "../Controller/CBuscar_AlunoProf.php";
				var requestData = {
					turmaId: turmaId
				};

				$.ajax({
					type: "GET",
					url: url,
					data: requestData,
					success: function(data) {
						if (turmaId === '0') {
							divAluno.hide();
							alunoSelect.prop("required", false).html('');
							profSelect.html(data).prop("required", true);
							divProf.show();
						} else {
							divProf.hide();
							profSelect.prop("required", false).html('');
							alunoSelect.html(data).prop("required", true);
							divAluno.show();
						}
					},
					error: function() {
						if (turmaId === '0') {
							profSelect.html("<option value=''>Erro ao carregar professores</option>");
							divProf.show();
							divAluno.hide();
						} else {
							alunoSelect.html("<option value=''>Erro ao carregar alunos</option>");
							divAluno.show();
							divProf.hide();
						}
					}
				});
			} else {
				divAluno.hide();
				divProf.hide();
				alunoSelect.prop("required", false).html('<option value="">Selecione um aluno</option>');
				profSelect.prop("required", false).html('<option value="">Selecione um professor</option>');
			}
		}

		// Ao abrir o modal, reseta os selects
		$('#cadEmpModal').on('shown.bs.modal', function () {
			toggleSelects();
		});

		// Executa ao carregar a página
		toggleSelects();

		// Ao mudar o valor da turma
		$("#Turma_idTurma").change(function() {
			toggleSelects();
		});
	});
</script>

<!-- Função 2: select dinamico dentro do modal editar -->
<script>
	$(document).ready(function() {
		function toggleSelects() {
			var turmaId = $("#Turma_editTurma").val();
			var alunoSelect = $("#aluno_editAluno");
			var profSelect = $("#prof_editProf");

			if (turmaId !== null && turmaId !== '') {
				var url = "../Controller/CBuscar_AlunoProf.php";
				var requestData = {
					turmaId: turmaId
				};

				$.ajax({
					type: "GET",
					url: url,
					data: requestData,
					success: function(data) {
						if (turmaId === '0') {
							alunoSelect.hide().html('');
							profSelect.html(data).show();
						} else {
							profSelect.hide().html('');
							alunoSelect.html(data).show();
						}
					},
					error: function() {
						alunoSelect.html("<option value=''>Erro ao carregar alunos</option>").show();
						profSelect.html("<option value=''>Erro ao carregar professores</option>").show();
					}
				});
			} else {
				alunoSelect.show();
				profSelect.hide().html('');
			}
		}

		// Verifica o valor da turma ao carregar a página
		toggleSelects();

		// Ao mudar o valor da turma
		$("#Turma_editTurma").change(function() {
			toggleSelects();
		});
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

<script>
	$(document).ready(function() {
		// Inicializando o Select2 para todos os selects
		$('.select2').select2({
			placeholder: "Selecione uma opção",
			allowClear: true
		}).on('select2:open', function() {
			// Adiciona a classe de dark mode quando o Select2 é aberto
			$('.select2-container--default .select2-selection--single').addClass('select-dark-mode');
			$('.select2-container--default .select2-selection--multiple').addClass('select-dark-mode');
		});
	});
</script>


<script>
	new DataTable('#TableEmprestimos', {
		ajax: {
			'url': '../View/listarEmprestimos.php',
			'dataType': 'JSON',
			'error': function() {
				alert('Não há registros para sua pesquisa');
			}
		},

		processing: true,
		serverSide: true,
		autoWidth: false,
		language: {
			url: "../lib/pt_br.json"
		},
		columns: [{
				width: "22%",
				className: "text-start"
			}, // Livro (0)
			{
				width: "10%",
				className: "text-start"
			}, // Gênero (1)
			{
				visible: false,
				searchable: false
			}, // Id (2)
			{
				width: "6%",
				className: "text-start"
			}, // Ano (3)
			{
				width: "8%",
				className: "text-start"
			}, // Turma (4)
			{
				width: "16%",
				className: "text-start"
			}, // Leitor (5)
			{
				width: "11%",
				className: "text-start"
			}, // Data Empréstimo (6)
			{
				width: "11%",
				className: "text-start"
			}, // Data Devolução (7)
			{
				width: "4%",
				className: "text-center"
			}, // Visualizar (8)
			{
				width: "4%",
				className: "text-center"
			}, // Editar (9)
			{
				width: "4%",
				className: "text-center"
			}, // Renovar (10)
			{
				width: "4%",
				className: "text-center"
			} // Excluir (11)
		],
		columnDefs: [{
			"defaultContent": "-",
			"targets": "_all"
		}],
		"lengthMenu": [
			[10, 15, 25, 50, 100, -1],
			[10, 15, 25, 50, 100, "Tudo"]
		],

	});
</script>

<style>
	.select-dark-mode {
		background-color: orange;
		/* Cor de fundo escura */
		color: #fff;
		/* Cor do texto */
		border: 1px solid #555;
		/* Cor da borda */
	}

	.select-dark-mode .select2-selection__rendered {
		color: #fff;
		/* Cor do texto selecionado */
	}

	.select-dark-mode .select2-selection__arrow {
		border-color: #555;
		/* Cor da seta */
	}

	.select-dark-mode .select2-selection--single .select2-selection__placeholder {
		color: #aaa;
		/* Cor do placeholder */
	}

	.select-dark-mode .select2-selection--multiple .select2-selection__choice {
		background-color: green;
		/* Cor de fundo das escolhas */
		color: #fff;
		/* Cor do texto das escolhas */
	}
</style>
</body>

</html>