<?php
class Pontos extends CI_Controller {
	
	public function ponto() {
		
		$pontos = new Pontos ( get_user () );
		
		if ($pontos) {
			$data = array (
					'pontoDireita' => $pontos->get_pontos_direita_formatado (),
					'pontoEsquerda' => $pontos->get_pontos_esquerda_formatado () 
			);
		} else {
			$data = array ();
		}
		
		if (count ( $data ) > 0) {
			echo json_encode ( array (
					'pontos' => $data,
					'success' => 'ok' 
			) );
			
		} else {
			echo json_encode ( array (
					'Exception' => 'Valor retornou vazio' 
			) );
		}
	}
}
