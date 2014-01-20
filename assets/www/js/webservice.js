function webService() {
	// var token='';

	this.init =function(){
		//carregar a página
		if(localStorage.getItem('pagina') !=null){
			get_home(localStorage.getItem('token'));
		    ativarEpecifico(localStorage.getItem('pagina'));
		}
	
	}
	 
	this.login = function(dados) {

		// verfificando se há conexão com a internet
		if (validarRede()) {
			try {
				try {

					if ($("input[name='login']").val() == "")
						throw "Por favor Informe um usuário.";
					if ($("input[name='senha']").val() == "")
						throw "Por favor Informe uma senha.";

					// carrega o preload
					preload('show', true);

					var request=$.ajax({
								url : 'http://escritoriounos.com.br/mobile_3658/index.php/autenticacao/login',
								dataType : 'json',
								type : 'POST',
								data : dados,
								success : function(usuarios) {
									try {
										

										if (usuarios.success == 'ok') {
											
											//salvando a token no cookie e pagina 
											localStorage.setItem('token',usuarios.token);
											localStorage.setItem('pagina','#home');
											
											
											$('#login').removeClass('current');
											$('#home').addClass('current');
											$("input[name='login']").val('');
											$("input[name='senha']").val('');

											// recuperando os dados do
											// webservice home
											get_home(usuarios.token);

											// verificando se é a primeira vez
											// que faz login, ser for abre o meu
											// lateral
											if (usuarios.status == 0)
												openMenu();

										}else if (usuarios.Exception != '')
											   throw usuarios.Exception;

									} catch (err) {
										navigator.notification.vibrate(100);
										alert(err);
										preload('hide');
										$("input[name='login']").val('');
										$("input[name='senha']").val('');
									}

								}
							});
					
					request.fail(function( jqXHR, statusText ) {
     				   navigator.notification.vibrate(100);
						   if(statusText=="error"){
							   alert('Não foi possível conectar ao servidor.');
							   preload('hide');
						   }
						});
					
				} catch (err) {
					navigator.notification.vibrate(100);
					alert(err);
//					$("input[name='login']").val('');
//					$("input[name='senha']").val('');
					preload('hide');
				}

			} catch (err) {
				 navigator.notification.vibrate(100);
				alert("ocorreu uma exceção não tratada no aplicativo :"
						+ err.message);
				preload('hide');
			}

		}

	}
	
	function get_home(token) {

		// verificanos se há conexão validarRede()
		if (validarRede()) {
			preload('show');
			var request = $.ajax({
				url : 'http://escritoriounos.com.br/mobile_3658/index.php/home',
				dataType : 'json',
				type : 'POST',
				data : {
					token : token
				},
				success : function(data) {
					preload('hide');
					// verificando se o retornou alguma informa��o
					if (data.sucess == "ok") {
						// pegando a quandidade de pontos
						$("#ptQEsquerda").html(data.data.pontos.pontoEsquerda);
						$("#ptQDireita").html(data.data.pontos.pontoDireita);

						// pegando a quantidade de membros
						$('#mbQEsquerda').html(
								data.data.menbros.quantidadeEsquerda);
						$('#mbQDireita').html(
								data.data.menbros.quantidadeDireita);

						// pegando a meta atiginda
						$("#mtAtingida").html(data.data.meta.mentaAtigida);

						// pegando o saldo
						$('#sdAtual').html(data.data.saldo.saldoAtual);
						$('#sdRecebido').html(data.data.saldo.saldoRecebido);

						// carrega a ganbiarra de links
						loadHRF(token);

					}
					// console.debug(data);
				}
			});
			//verifica o erro de conexão com o  servidor remoto
			request.fail(function( jqXHR, statusText ) {
				   navigator.notification.vibrate(100);
					   if(statusText=="error"){
						   alert('Não foi possível conectar ao servidor.');
						   preload('hide');
					   }
					});
		}
	}
	
	function ativarEpecifico(div_id) {
		$('.menu-active').removeClass('menu-active');
		$('.menu-active-ac').removeClass('menu-active-ac');
		$('.current').removeClass('current');
		$(div_id).addClass('current');
	}
	
	// função que implementa a ganbirra de chamadas de links
	function loadHRF(token) {

		// em caso de paginação esse é o valor definido.
		var paginaNacao = 0;

		$('a').each(function(e) {

			if ($(this).attr('href') != "#") {
				$(this).attr('href', $(this).attr('href') + '?token=' + token);

				$(this).click(function() {
					$('.menu-active').removeClass('menu-active');
					$('.menu-active-ac').removeClass('menu-active-ac');
					$('.current').removeClass('current');
					var pagina = $(this).attr('href').split('?');
					$(pagina[0]).addClass('current');

					// verificando qual pagina ta sendo chamanda
					if(pagina[0] == "#login"){
						localStorage.clear();
						ativarEpecifico("#login");

						
					}else if (pagina[0] == '#pedidos') {
						pedidos(token);
					} else if (pagina[0] == "#home") {
						get_home(token);

					} else if (pagina[0] == "#empreededores") {
						binaria(token);

					} else if (pagina[0] == '#transacoes') {

						$('#trans-content').html('');
						ativarEpecifico('#transacoes');
						trasacoes(token, paginaNacao);
						scrollInt(trasacoes, token);
						
					} else if (pagina[0] == '#buscar') {

						$('#trans-content').html('');
						ativarEpecifico('#transacoes');
						trasacoes(token, paginaNacao);
						scrollInt(trasacoes, token);

					} else if (pagina[0] == '#resumoBonus') {
						
						ativarEpecifico('#resumoBonus');
						resulmoBonus(token);
						
					} else if (pagina[0] == "#buscarResumoBonus") {
						
						ativarEpecifico('#resumoBonus');
						resulmoBonus(token);
						
					} else if (pagina[0] == "#relatorioBonus") {

						$('#totalBonusRecebido').html('');
						$('#relatorioBonuscontent').html('');

						ativarEpecifico('#relatorioBonus');
						relatorioBonus(token, paginaNacao);
						scrollInt(relatorioBonus, token);
						
					} else if (pagina[0] == "#buscarRelatorioBonus") {

						$('#totalBonusRecebido').html('');
						$('#relatorioBonuscontent').html('');

						ativarEpecifico('#relatorioBonus');
						relatorioBonus(token, paginaNacao);
						scrollInt(relatorioBonus, token);

					} else if (pagina[0] == "#noticias") {

						$('#noticias-content').html('');
						noticias(token, paginaNacao);
						scrollInt(noticias, token);
					} else if (pagina[0] == "#promocao") {

						$('#promocao-content').append('');
						promocao(token, paginaNacao);
						scrollInt(promocao, token);
					} else if (pagina[0] == "#notificacao") {
						notificacao(token);
						scrollInt(notificacao, token);
					}

				});
			}// fim do if
		});
	}

	// função pegar os pedidos
	function pedidos(token) {

		// verificanos se há conexão
		if (validarRede()) {
			var html = "";
			preload('show');
			var request =$
					.ajax({
						url : 'http://escritoriounos.com.br/mobile_3658/index.php/pedido',
						dataType : 'json',
						type : 'POST',
						
						data : {
							token : token
						},
						success : function(data) {
							preload('hide');
							// verificando se o retornou alguma informa��o
							if (data.sucess == "ok") {
								for ( var x in data.data.pedidos) {

									html += '<table width="100%" border="0" class="table table-bordered table-hover font-style-0">\n\
				    		<tr>\n\
				    	    <td width="25%"><strong>N°: </strong> <br/>'
											+ data.data.pedidos[x].co_id
											+ '</td>\n \
				    	    <td width="25%"><strong>Data:</strong><br/> '
											+ data.data.pedidos[x].co_data_compra
											+ '</td>\n\
				    	    <td width="12%"><strong>pt:</strong> <br/>'
											+ data.data.pedidos[x].co_total_pontos
											+ '</td>\n\
				    	    <td width="38%"><strong>Valor</strong>: <br/> '
											+ data.data.pedidos[x].co_total_valor
											+ '</td>\n\
				    	  </tr>\n\
				    	  <tr>\n\
				    	    <td colspan="4" class="font-style-4 center '
											+ (data.data.pedidos[x].st_descricao == "Pagamento confirmado" ? "text-sucess"
													: "text-alert")
											+ ' "><strong>Situação:</strong>'
											+ data.data.pedidos[x].st_descricao
											+ '</td>\n\
				    	   <tr>\n\
				    	    <td colspan="4"><a href="javascript:visualizar(\''
											+ token
											+ '\',\''
											+ data.data.pedidos[x].co_id
											+ '\');" class="btn btn-primary">Visualizar Pedido</a></td>\n\
				    	  </tr>\n\
				    	</table>';

							

								$('#pd-content').html(html);
							}
								
							}else if(data.Exception == 1){
								validarMultiploLogin();
							}
						}
					});
			//verifica o erro de conexão com o  servidor remoto
			request.fail(function( jqXHR, statusText ) {
				   navigator.notification.vibrate(100);
					   if(statusText=="error"){
						   alert('Não foi possível conectar ao servidor.');
						   preload('hide');
					   }
					});
		}
	}

	// função ver pedido
	this.verPedido = function(token, pedido) {

		// verificanos se há conexão
		if (validarRede()) {
			ativarEpecifico('#verpedido');
			var html = "";
			var total = '';

			preload('show');
			var request = $
					.ajax({
						url : 'http://escritoriounos.com.br/mobile_3658/index.php/pedido/verpedido',
						dataType : 'json',
						type : 'POST',
						data : {
							token : token,
							pedido : pedido
						},
						success : function(data) {
							preload('hide');
							// verificando se o retornou alguma informação
							if (data.sucess == "ok") {
								html = '<table width="100%" border="0" class="table table-bordered table-hover font-style-0">\n\
				    	  <tr>\n\
				    <td colspan="4"><address>\n\
						<strong>'
										+ data.data.pedido.di_nome
										+ '('
										+ data.data.pedido.di_usuario
										+ ')</strong><br>\n\
						'
										+ data.data.pedido.di_endereco
										+ '<br>\n\
						'
										+ data.data.pedido.di_bairro
										+ ', CEP: '
										+ data.data.pedido.di_cep
										+ '<br>\n\
						'
										+ data.data.pedido.ci_nome
										+ ' - '
										+ data.data.pedido.ci_nome
										+ '<br>\n\
						<abbr title="Phone">Fone:</abbr> '
										+ data.data.pedido.di_fone1
										+ '\n\
						</address>\n\
						<address>\n\
						'
										+ data.data.pedido.di_email
										+ '\n\
						</address></td>\n\</td>\n\
				  </tr>\n\
				  <tr>\n\
				    <td colspan="4"><strong>Data:</strong> '
										+ data.data.pedido.co_data_compra
										+ '<br>\n\
		            <strong>Nº Pedido:</strong> '
										+ data.data.pedido.co_id
										+ '<br>\n\
		            <strong>Forma Pag.:</strong> '
										+ data.data.pedido.co_forma_pgt_txt
										+ '<br>\n\
		            <strong>Entrega:</strong>'
										+ (data.data.pedido.co_entrega == 1 ? 'Entregar -'
												+ data.data.pedido.co_frete_tipo
												: 'Retirar no CD')
										+ '<br>\n\
		            <strong>Situação:</strong> '
										+ data.data.pedido.st_descricao
										+ '<br>\n\
		            <strong>Cód. Rastreio:</strong> '
										+ (data.data.pedido.co_frete_codigo == " " ? "Ainda não foi informado"
												: data.data.pedido.co_frete_codigo)
										+ ' <br>\n\
		            <div><strong>Valor:</strong> '
										+ data.data.pedido.co_total_valor
										+ '</div></td>\n\
				  </tr>';

								for ( var x in data.data.produto) {
									// total
									// +=(parseFloat(data.data.produto[x].pm_valor)
									// *
									// parseInt(data.data.produto[x].pm_quantidade))+data.data.produto[x].co_frete_valor;

									html += '<tr>\n\
						    <td>Código:<br/> '
											+ data.data.produto[x].pr_codigo
											+ ' </td>\n\
						    <td>Produdo: <br/>'
											+ data.data.produto[x].co_id
											+ '</td>\n\
						    <td>AQNT:<br/> '
											+ data.data.produto[x].pm_quantidade
											+ '</td>\n\
						    <td>Pt. Unitário: <br/> '
											+ data.data.produto[x].pm_pontos
											+ '</td>\n\
						  </tr>\n\
						  <tr>\n\
						    <td>Vl. unitário: <br/>'
											+ data.data.produto[x].pm_quantidade
											+ '</td>\n\
						    <td>total sem frete: <br/>'
											+ data.data.produto[x].total_sem_frete
											+ '</td>\n\
						    <td>Frete: <br/> '
											+ data.data.produto[x].co_frete_tipo
											+ ' - '
											+ data.data.produto[x].co_frete_valor
											+ '</td>\n\
						    <td>Total: <br/>'
											+ data.data.produto[x].total
											+ '</td>\n\
						  </tr>';
								}

								html += '</table>';

								$('#vpd-content').html(html);
								
							}else if(data.Exception == 1){
								validarMultiploLogin();
							}
						}
					});
			
			//verifica o erro de conexão com o  servidor remoto
			request.fail(function( jqXHR, statusText ) {
				   navigator.notification.vibrate(100);
					   if(statusText=="error"){
						   alert('Não foi possível conectar ao servidor.');
						   preload('hide');
					   }
					});
		}
	}

	// função pegar a rede binária
	function binaria(token) {

		// verificanos se há conexão
		if (validarRede()) {
			preload('show');

			var request = $.ajax({
				url : 'http://escritoriounos.com.br/mobile_3658/index.php/empreederes',
				type : 'POST',
				data : {
					token : token
				},
				success : function(data) {
					preload('hide');
					if(data !=1){
						// verificando se o retornou alguma informação
						$('#empre-content').contents().find('html').html(data);
						
					}else if(data ==1){
						validarMultiploLogin();
					}
				
				}
			});
			
			//verifica o erro de conexão com o  servidor remoto
			request.fail(function( jqXHR, statusText ) {
				   navigator.notification.vibrate(100);
					   if(statusText=="error"){
						   alert('Não foi possível conectar ao servidor.');
						   preload('hide');
					   }
					});
		}
	}
	// função visualizar um no espeficio da arvore

	this.binariaView = function(token, id) {

		// verificanos se há conexão
		if (validarRede()) {
			preload('show');

			var request = $.ajax({
				url : 'http://escritoriounos.com.br/mobile_3658/index.php/empreederes',
				type : 'POST',
				data : {
					token : token,
					id : id
				},
				success : function(data) {
					preload('hide');
					// verificando se o retornou alguma informação
					$('#empre-content').contents().find('html').html(data);
					// $('#empre-content').contents().find('#empre-content').html(data);

				}
			});
			
			//verifica o erro de conexão com o  servidor remoto
			request.fail(function( jqXHR, statusText ) {
				   navigator.notification.vibrate(100);
					   if(statusText=="error"){
						   alert('Não foi possível conectar ao servidor.');
						   preload('hide');
					   }
					});
		}
	}

	// pega o historico das transações bancarias
	function trasacoes(token, pagina) {
		

       // verificanos se há conexão
		if (validarRede()) {
			var html = '';
			preload('show');

			var request = $
					.ajax({
						url : 'http://escritoriounos.com.br/mobile_3658/index.php/financeiro/historico_transacao',
						dataType : 'json',
						type : 'POST',
						data : {
							token : token,
							de : $("input[name='de']").val(),
							ate : $("input[name='ate']").val(),
							pagina : pagina
						},
						success : function(data) {

							preload('hide');
                            if(data.sucess == "ok"){
                            	// verificando se o retornou alguma informação
    							for ( var x in data.data.historicoTransacao) {
    								html += "<tr><td>"
    										+ data.data.historicoTransacao[x].cb_id
    										+ "</td>\n\
    						     <td>"
    										+ data.data.historicoTransacao[x].data
    										+ "</td>\n\
    						     <td>"
    										+ data.data.historicoTransacao[x].descricao
    										+ "</td>\n\
    						     <td><span class='"
    										+ data.data.historicoTransacao[x].css
    										+ "'>"
    										+ data.data.historicoTransacao[x].valor
    										+ " </span></td></tr>";
    							}

    							$('#trans-content').append(html);
                            	
                            }else if(data.Exception == 1){
								validarMultiploLogin();
							}
							
						}
					});
			
			//verifica o erro de conexão com o  servidor remoto
			request.fail(function( jqXHR, statusText ) {
				   navigator.notification.vibrate(100);
					   if(statusText=="error"){
						   alert('Não foi possível conectar ao servidor.');
						   preload('hide');
					   }
					});
		}
	}

	// pegar o resulmo do bonus
	function resulmoBonus(token) {
	
		//verificanos se há conexão 
		 if (validarRede()) {

			preload('show');

		var request = $
					.ajax({
						url : 'http://escritoriounos.com.br/mobile_3658/index.php/financeiro/resumo_bonus',
						type : 'POST',
						data : {
							token : token,
							de : $("input[name='resumo_de']").val(),
							ate : $("input[name='resumo_ate']").val()
						},
						success : function(response) {
							preload('hide');
							if(response !=1){
							   $('#resumoBonuscontente').html(response);
							}if(response ==1){
								validarMultiploLogin();	
							}
							
						}
					});
		
		//verifica o erro de conexão com o  servidor remoto
		request.fail(function( jqXHR, statusText ) {
			   navigator.notification.vibrate(100);
				   if(statusText=="error"){
					   alert('Não foi possível conectar ao servidor.');
					   preload('hide');
				   }
				});
		}
	}

	// Relatório Bônus
	function relatorioBonus(token, pagina) {
		
		//verificanos se há conexão 
		 if (validarRede()) {
			var html = "";

			preload('show');

			var request = $
					.ajax({
						url : 'http://escritoriounos.com.br/mobile_3658/index.php/financeiro/historico_bonus',
						dataType : 'json',
						type : 'POST',
						data : {
							token : token,
							de : $("input[name='relatorio_de']").val(),
							ate : $("input[name='relatorio_ate']").val(),
							pagina : pagina
						},
						success : function(data) {
							preload('hide');

							if (data.sucess == "ok") {
								$('#totalBonusRecebido')
										.html(
												'<tr><td>Saldo Recebido: '
														+ data.data.bonusRecebido.saldo
														+ '</td><td ><span class="label label-info">SALDO ATUAL:  '
														+ data.data.saldo[0].saldo
														+ '</span></td></tr>');

								for ( var x in data.data.movimentacoes) {
									html += '<tr class="row-row">\n\
								      <td width="6%">'
											+ data.data.movimentacoes[x].cb_id
											+ '</td>\n\
								      <td width="70%">\n\
								  	<span style="font-size:11px; display:block;">'
											+ data.data.movimentacoes[x].cb_data_hora
											+ '</span>\n\
								    </td>\n\
								      <td width="24%"><span  class="'
											+ data.data.movimentacoes[x].css
											+ '">'
											+ data.data.movimentacoes[x].valor
											+ '</span></td>\n\
								   </tr>';
								}

								$('#relatorioBonuscontent').append(html);
								
							}else if(data.Exception == 1){
								validarMultiploLogin();
							}
						}
					});
			
			//verifica o erro de conexão com o  servidor remoto
			request.fail(function( jqXHR, statusText ) {
				   navigator.notification.vibrate(100);
					   if(statusText=="error"){
						   alert('Não foi possível conectar ao servidor.');
						   preload('hide');
					   }
					});
		}
	}

	// buscando noticias
	function noticias(token, pagina) {
		

				// verificanos se há conexão
		if (validarRede()) {
			var html = "";
			preload('show');

			var request = $.ajax({
						url : 'http://escritoriounos.com.br/mobile_3658/index.php/noticia',
						dataType : 'json',
						type : 'POST',
						data : {
							token : token,
							pagina : pagina
						},
						success : function(data) {

							preload('hide');
							if (data.sucess == "ok") {
								for ( var x in data.data.noticias) {
									html += '<table width="100%" border="0">\n\
						        <tr>\n\
						  		<td style="color: #0088cc;">'
											+ data.data.noticias[x].titulo
											+ '</td>\n\
							  	</tr>\n\
							  	<tr>\n\
					              <td>'
											+ data.data.noticias[x].texto
											+ '</td>\n\
					            </tr>\n\
					         </table>';
								}
								$('#noticias-content').append(html);
								
							}else if(data.Exception == 1){
								validarMultiploLogin();
							}
							
						}
					});
			
			//verifica o erro de conexão com o  servidor remoto
			request.fail(function( jqXHR, statusText ) {
				   navigator.notification.vibrate(100);
					   if(statusText=="error"){
						   alert('Não foi possível conectar ao servidor.');
						   preload('hide');
					   }
					});
		}
	}

	// buscando promoção
	function promocao(token, pagina) {
	

	    // verificanos se há conexão
		if (validarRede()) {
			var html = "";
			preload('show');

			var request = $.ajax({
						url : 'http://escritoriounos.com.br/mobile_3658/index.php/promocao',
						dataType : 'json',
						type : 'POST',
						data : {
							token : token,
							pagina : pagina
						},
						success : function(data) {

							preload('hide');
							if (data.sucess == "ok") {
								
								for ( var x in data.data.promocoes) {
									html += '<table width="100%" border="0">\n\
						        <tr>\n\
						  		<td style="color: #0088cc;">'
											+ data.data.promocoes[x].titulo
											+ '</td>\n\
							  	</tr>\n\
							  	<tr>\n\
					              <td>'
											+( data.data.promocoes[x].texto!=null? data.data.promocoes[x].texto:'')
											+ '</td>\n\
					            </tr>\n\
					         </table>';
								}
								$('#promocao-content').append(html);
								
							}else if(data.Exception == 1){
								validarMultiploLogin();
							}
						}
					});
			
			//verifica o erro de conexão com o  servidor remoto
			request.fail(function( jqXHR, statusText ) {
				   navigator.notification.vibrate(100);
					   if(statusText=="error"){
						   alert('Não foi possível conectar ao servidor.');
						   preload('hide');
					   }
					});
		}
	}

	// buscando notificação
	function notificacao(token) {
		

				// verificanos se há conexão
		if (validarRede()) {
			var html = "";
			preload('show');

			var request = $
					.ajax({
						url : 'http://escritoriounos.com.br/mobile_3658/index.php/notificacao',
						dataType : 'json',
						type : 'POST',
						data : {
							token : token
						},
						success : function(data) {

							preload('hide');
							if (data.sucess == "ok") {
								
								for ( var x in data.data.notificacoes) {
									html += '<table width="100%" border="0">\n\
						        <tr>\n\
						  		<td style="color: #0088cc;">'
											+ data.data.notificacoes[x].titulo
											+ '</td>\n\
							  	</tr>\n\
							  	<tr>\n\
					              <td>'
											+ data.data.notificacoes[x].texto
											+ '</td>\n\
					            </tr>\n\
					         </table>';
								}
								$('#notificacao-content').html(html);
								
							}else if(data.Exception == 1){
								validarMultiploLogin();
							}
						}
					});
			
			//verifica o erro de conexão com o  servidor remoto
			request.fail(function( jqXHR, statusText ) {
				   navigator.notification.vibrate(100);
					   if(statusText=="error"){
						   alert('Não foi possível conectar ao servidor.');
						   preload('hide');
					   }
					});
		}
	}

	// funçao de scroll infinito
	function scrollInt(funcao, token) {
		$('.proxima').data('pagina', 10);
		inicializa_scroll(funcao, token);
	}

	function inicializa_scroll(funcao, token) {
		$(window).scroll(
				function() {
					if (($(window).scrollTop() + $(window).height() + 20) >= $(
							document).height()) {
						// $(window).unbind('scroll');

						// carregar a função para a função passada
						funcao(token, ($('.proxima').data('pagina') + 10));
						$('.proxima').data('pagina',
								($('.proxima').data('pagina') + 10));
					}
				});
	}

	// função para pegar varivavel na url
	function getUrlVars() {
		var vars = [], hash;
		var hashes = window.location.href.slice(
				window.location.href.indexOf('?') + 1).split('&');

		for ( var i = 0; i < hashes.length; i++) {
			hash = hashes[i].split('=');
			hash[1] = unescape(hash[1]);
			vars.push(hash[0]);
			vars[hash[0]] = hash[1];
		}

		return vars;
	}

	// function ao carregar a pagina
	function onLoadPage() {
		var paginaAtual = window.location.href.slice(
				window.location.href.indexOf('#') + 1).split('&');
		paginaAtual = paginaAtual[0].split('?');
		paginaAtual[0];

		$('.menu-active').removeClass('menu-active');
		$('.menu-active-ac').removeClass('menu-active-ac');
		$('.current').removeClass('current');
		$('#' + paginaAtual[0]).addClass('current');
	}

	// função ativa é desativa o loading da pagina
	function preload(status, mask) {
		if (status == 'show') {
			if (mask)
				$('.mask').fadeIn('show');

			$('.load').fadeIn('show');

		} else if (status == 'hide') {

			$('.mask').fadeOut('show');
			$('.load').fadeOut('show');

		}
	}
	
	function setCookie(variavel,valor)
	{
		document.cookie=""+variavel+"="+valor;
	}

	function getCookie(cname)
	{
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i=0; i<ca.length; i++) 
	  {
	  var c = ca[i].trim();
	  if (c.indexOf(name)==0) return c.substring(name.length,c.length);
	  }
	
	//gravando cooker
	function checkCookie()
	{
		var token =getCookie("token");
		var pagina =getCookie("pagina");
		
		if (pagina!="")
		  {
			 ativarEpecifico(pagina);
		  }else{
			  ativarEpecifico('#login');
		  }
	}
	
	return "";
	}

	/*
	 * validar(false) para produção 
	 * validar(true) para testar em navegadores local 
	 * 
	 */
//validando se tem internet no celular
	function validarRede(debug) {
		//o teebug como true para testar local
		if(!debug){
			if (check_network()) {
				ativarEpecifico('#login');
				return false;
			}else{
				return true;
			}
	     return true;
		}else{
			return true;
		}
//		return true;
	}
	// função abri menu
	function openMenu() {
		jQuery('#jqt').toggleClass('menu-active');
		jQuery('menu').toggleClass('menu-active-ac');
	}
	
	//validar o login
	
	function validarMultiploLogin(){
		   navigator.notification.vibrate(100);
		   alert('Realize novamente o login');
		   preload('hide');
		   ativarEpecifico('#login');
	}

}