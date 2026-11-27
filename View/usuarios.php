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
	<link rel="stylesheet" href="../CSS/popup.css">
	<link rel="stylesheet" href="../lib/Bootstrap 2/bootstrap.min.css">
	<link rel="stylesheet" href="../CSS/darkPaginacao.css">
	<style>
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

		/* Padronização do botão de editar em Usuários */
		.btn-outline-warning,
		.edit-button {
			width: 36px !important;
			height: 36px !important;
			min-width: 36px !important;
			max-width: 36px !important;
			padding: 0 !important;
			background-color: transparent !important;
			background-image: none !important;
			border: 1.5px solid #f59e0b !important;
			border-radius: 8px !important;
			color: #f59e0b !important;
			cursor: pointer !important;
			display: inline-flex !important;
			justify-content: center !important;
			align-items: center !important;
			font-size: 14px !important;
			font-weight: 600 !important;
			text-align: center !important;
			margin: 0 4px !important;
			box-shadow: 0 1px 3px rgba(245, 158, 11, 0.1) !important;
			transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
			box-sizing: border-box !important;
		}

		.btn-outline-warning i,
		.btn-outline-warning .fas,
		.edit-button i,
		.edit-button .fas {
			color: #f59e0b !important;
			font-size: 14px !important;
		}

		.btn-outline-warning:hover,
		.edit-button:hover {
			background-color: #f59e0b !important;
			border-color: #f59e0b !important;
			color: #ffffff !important;
			transform: translateY(-2px) !important;
			box-shadow: 0 4px 8px rgba(245, 158, 11, 0.28) !important;
		}

		.btn-outline-warning:hover i,
		.btn-outline-warning:hover .fas,
		.edit-button:hover i,
		.edit-button:hover .fas {
			color: #ffffff !important;
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
			<li class="active">
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
					/* Firefox */
				}

				.searchInput {
					width: 220px !important;
					height: 38px;
					background-color: #f2f2f2;
					border: 1px solid #ccc;
					border-radius: 6px;
					padding: 6px 12px;
					margin: 0;
				}

				.btn-cadastrar-usuario {
					background-color: #28a745;
					color: #fff;
					border: none;
					height: 38px;
					padding: 0 16px;
					border-radius: 6px;
					cursor: pointer;
					font-weight: 600;
					display: inline-flex;
					align-items: center;
					gap: 6px;
					transition: background 0.2s ease;
				}

				.btn-cadastrar-usuario:hover {
					background-color: #218838;
					color: #fff;
				}

				.head-acoes {
					display: flex;
					align-items: center;
					gap: 12px;
					flex-wrap: wrap;
				}

				.order table th,
				.order table td {
					text-align: left !important;
					vertical-align: middle !important;
				}
			</style>
			<main>
				<div class="table-data">
					<div class="order">
						<div class="head">
							<h3>Tabela de Cadastro de Usuários</h3>
							<div class="head-acoes">
								<button type="button" class="btn-cadastrar-usuario" onclick="abrirNovoUsuario()">
									<i class="fas fa-plus"></i> Cadastrar Usuário
								</button>
								<input type="text" id="searchInput" class="searchInput" placeholder="Pesquisar...">
								<button class="pdf-button" id="pdf-button" aria-label="botão pdf" onclick="abrirAluno()">
									<i class="fas fa-file-pdf"></i> Exportar Relatório PDF
								</button>
							</div>

							<script>
								function abrirAluno() {
									var urlDoPDF = "../pdf/usuarioPdf.php";
									window.open(urlDoPDF, '_blank');
								}
							</script>

						</div>
						<table>

							<?php
							$conexao = new CConexao();
							$conn = $conexao->getConnection();

							// Consulta para obter os dados da tabela de usuários
							$sql = "SELECT
					usuario.NomeUsuario,
					usuario.idUsuario,
					usuario.UserUsuario,
					usuario.EmailUsuario,
					usuario.camfoto
				FROM usuario";

							$result = $conn->query($sql);

							if ($result === false) {
								// Use errorInfo para obter informações sobre o erro
								$errorInfo = $conn->errorInfo();
								echo "Erro na consulta SQL: " . $errorInfo[2];
							} else {
								if ($result->rowCount() > 0) {
									$user = $result->fetchAll(PDO::FETCH_ASSOC);
									$UsuarioPorPagina = 5;
									$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
									$indiceInicial = ($paginaAtual - 1) * $UsuarioPorPagina;
									$UsuarioExibidos = array_slice($user, $indiceInicial, $UsuarioPorPagina);

									// Exibir a tabela de usuários
									echo "<table>";
									echo "<thead>";
									echo "<tr>";
									echo "<th>Nome</th>";
									echo "<th>ID</th>";
									echo "<th>Usuário</th>";
									echo "<th>E-mail</th>";
									echo "<th>Perfil</th>";
									echo "<th>Editar</th>";
									echo "<th>Excluir</th>";
									echo "</tr>";
									echo "</thead>";
									echo "<tbody>";

									foreach ($UsuarioExibidos as $row) {
										echo "<tr>";
										echo "<td>" . htmlspecialchars($row["NomeUsuario"]) . "</td>";
										echo "<td>" . htmlspecialchars($row["idUsuario"]) . "</td>";
										echo "<td>" . htmlspecialchars($row["UserUsuario"]) . "</td>";
										echo "<td>" . htmlspecialchars($row["EmailUsuario"]) . "</td>";
										echo "<td><img src='" . htmlspecialchars($row["camfoto"]) . "' alt='Imagem do usuário' /></td>";
										echo "<td><button class='btn btn-outline-warning edit-button' data-id='$row[idUsuario]'><i class='fas fa-pen'></i></button></td>";

										echo "<td><div class='container' style='display:inline-block;'><button class='delete-button' type='button' onclick='handlePopup(true)' aria-label='botão excluir'><i class='fas fa-trash-alt'></i></button><div class='popup' id='popup'><img src='../img/decisao.png' aria-label='popup decisão'><h2 class='title'>Aviso!</h2><p class='desc'>Deseja mesmo excluir?</p><button class='close-popup-button' type='button' onclick='handlePopup(false)'>Fechar</button><a href='../Controller/CExcluir_usuario.php?id={$row["idUsuario"]}'><button class='close-popup-button'>Excluir</button></a></div></div></td>";
										echo "</tr>";
									}

									echo "</tbody>";
									echo "</table>";

									// Adiciona links de paginação
									echo "<div class='pagination'>";
									$totalUser = count($user);
									$totalPaginas = ceil($totalUser / $UsuarioPorPagina);
									for ($i = 1; $i <= $totalPaginas; $i++) {
										$classeAtiva = ($i === $paginaAtual) ? "active" : "";
										echo "<a class='page-link $classeAtiva' href='usuarios.php?pagina=$i'>$i</a>";
									}
									echo "</div>";

									// Botão Fechar do popup fora da tabela

								} else {
									echo "<p><center>enhum usuário encontrado.</center></p>";
								}
							}

							$conn = null; // Fecha a conexão
							?>

						</table>
					</div>

				</div>

				<footer class="footer">

					© Copyright 2023 por <span>EducaBiblio</span> | Todos os direitos reservados

				</footer>

			</main>
	</section>

	<!-- Modal de Cadastro / Edição de Usuário (Vertical) -->
	<div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalUsuarioLabel">Cadastrar Usuário</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 1.2rem; cursor: pointer;">&times;</button>
				</div>
				<form id="formUsuario" action="../Router/cad_rotas.php" method="post" enctype="multipart/form-data">
					<div class="modal-body">
						<input type="hidden" name="idUsuario" id="idUsuario">

						<div class="mb-3">
							<label for="NomeUsuario" class="form-label">Nome Completo</label>
							<input type="text" class="form-control" name="NomeUsuario" id="NomeUsuario" placeholder="Digite o nome completo" required autocomplete="off">
						</div>

						<div class="mb-3">
							<label for="UserUsuario" class="form-label">Nome de Usuário (Login)</label>
							<input type="text" class="form-control" name="UserUsuario" id="UserUsuario" placeholder="Digite o usuário de login" required autocomplete="off">
						</div>

						<div class="mb-3">
							<label for="SenhaUsuario" class="form-label">Senha</label>
							<input type="password" class="form-control" name="SenhaUsuario" id="SenhaUsuario" placeholder="Digite a senha" required autocomplete="off">
							<small id="senhaHelp" class="text-muted" style="display:none; font-size: 12px;">Preencha a senha para atualizar.</small>
						</div>

						<div class="mb-3">
							<label for="EmailUsuario" class="form-label">E-mail</label>
							<input type="email" class="form-control" name="EmailUsuario" id="EmailUsuario" placeholder="Digite o e-mail" required autocomplete="off">
						</div>

						<div class="mb-3">
							<label for="FotoUsuario" class="form-label">Foto de Perfil</label>
							<input type="file" class="form-control" name="FotoUsuario" id="FotoUsuario">
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
						<button type="submit" id="btnSalvarUsuario" class="btn btn-success" name="action" value="Cadastrar">Cadastrar</button>
					</div>
				</form>
			</div>
		</div>
	</div>

</body>

<script src="../lib/jquery/jquery-3.7.1.min.js"></script>
<script src="../lib/Bootstrap 2/bootstrap.bundle.min.js"></script>
<script src="../JS/script.js"></script>
<script src="../JS/popup.js"></script>

<script>
	var modalUsuarioInstancia = null;

	function getModalUsuario() {
		if (!modalUsuarioInstancia && typeof bootstrap !== 'undefined') {
			var modalEl = document.getElementById('modalUsuario');
			modalUsuarioInstancia = new bootstrap.Modal(modalEl);
		}
		return modalUsuarioInstancia;
	}

	function abrirNovoUsuario() {
		$('#modalUsuarioLabel').text('Cadastrar Usuário');
		$('#formUsuario').attr('action', '../Router/cad_rotas.php');
		$('#idUsuario').val('');
		$('#NomeUsuario').val('');
		$('#UserUsuario').val('');
		$('#SenhaUsuario').val('').attr('required', true);
		$('#senhaHelp').hide();
		$('#EmailUsuario').val('');
		$('#FotoUsuario').val('');
		$('#btnSalvarUsuario').attr('name', 'action').val('Cadastrar').text('Cadastrar').removeClass('btn-primary').addClass('btn-success');
		
		var modal = getModalUsuario();
		if (modal) {
			modal.show();
		} else {
			$('#modalUsuario').modal('show');
		}
	}

	$(document).ready(function() {
		// Capturar clique no botão de exclusão
		$('.delete-button').click(function() {
			var id = $(this).closest('tr').find('td:eq(1)').text().trim();
			handlePopup(true);
			var linkExclusao = '../Controller/CExcluir_usuario.php?id=' + id;
			$('#popup a').attr('href', linkExclusao);
		});

		// Capturar clique no botão de edição
		$('.edit-button').click(function() {
			var id = $(this).data('id');
			var $linha = $(this).closest('tr');
			var nome = $linha.find('td:eq(0)').text().trim();
			var usuario = $linha.find('td:eq(2)').text().trim();
			var email = $linha.find('td:eq(3)').text().trim();

			$('#modalUsuarioLabel').text('Editar Usuário');
			$('#formUsuario').attr('action', '../Router/usu_rotas.php');
			$('#idUsuario').val(id);
			$('#NomeUsuario').val(nome);
			$('#UserUsuario').val(usuario);
			$('#EmailUsuario').val(email);
			$('#SenhaUsuario').val('').attr('required', true);
			$('#senhaHelp').show();
			$('#FotoUsuario').val('');
			$('#btnSalvarUsuario').attr('name', 'Editar').val('Editar').text('Salvar Alterações').removeClass('btn-success').addClass('btn-primary');

			var modal = getModalUsuario();
			if (modal) {
				modal.show();
			} else {
				$('#modalUsuario').modal('show');
			}
		});

		// Pesquisa na tabela
		$('#searchInput').on('keyup', function() {
			const value = $(this).val().toLowerCase();
			$('table tbody tr').filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
			});
		});
	});
</script>

</html>