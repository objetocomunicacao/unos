<?php
class Binario{
	
	private $ci;
	private $qtd_esquerda;
	private $qtd_direita;
	private $distribuidor;
	private $binario_ativo;
	private $indicacao_direita;
	private $indicacao_esquerda;
	
	public function __construct($dis){
		 $this->ci =& get_instance();
		 $this->distribuidor = $dis;
		 $this->carrega_qtd_direita();
		 $this->carrega_qtd_esquerda();
		 $this->init_binario_ativo();
		}
	
	public function get_qtd_direita(){
		return $this->qtd_direita;
		}

	public function get_qtd_esquerda(){
		return $this->qtd_esquerda;
		}
	
	public function e_binario(){
		return $this->binario_ativo;
		}	
		
		
	private function carrega_qtd_direita(){
		 $qtd_direita = $this->ci->db->query("
		 SELECT count(di_id) qtd FROM `distribuidor_ligacao` 
		 JOIN distribuidores ON di_id = `li_id_distribuidor`
		 WHERE `li_no` = ".$this->distribuidor->di_direita."
		 ")->row();
		 $this->qtd_direita = (int) $qtd_direita->qtd;
		 
		}	

	private function carrega_qtd_esquerda(){
		 $qtd_esquerda = $this->ci->db->query("
		 SELECT count(di_id) qtd FROM `distribuidor_ligacao` 
		 JOIN distribuidores ON di_id = `li_id_distribuidor`
		 WHERE `li_no` = ".$this->distribuidor->di_esquerda."
		 ")->row();
		 $this->qtd_esquerda = (int)$qtd_esquerda->qtd;
		 
		}	
		
		
	private function init_binario_ativo(){
		 $this->binario_ativo = $this->distribuidor->di_binario;
		}	
		
	
	public function verificar_binario_ativo(){
		 

		
		    $indicacao_esquerda = $this->ci->db->query("
		    	 SELECT di_id
				 FROM `distribuidor_ligacao`
				 JOIN distribuidores dis_interno ON dis_interno.di_id = li_id_distribuidor
				 JOIN compras ON co_id_distribuidor = li_id_distribuidor
				 WHERE `li_no` = ".$this->distribuidor->di_esquerda."
				   AND co_id_plano <> 4
				   AND co_pago_industria = 0
				   AND co_pago = 1
				   AND di_ni_patrocinador = ".$this->distribuidor->di_id.""
				 )->num_rows;	

		     $indicacao_direita = $this->ci->db->query("		     		
		     	 SELECT di_id
				 FROM `distribuidor_ligacao`
				 JOIN distribuidores dis_interno ON dis_interno.di_id = li_id_distribuidor
				 JOIN compras ON co_id_distribuidor = li_id_distribuidor
				 WHERE `li_no` = ".$this->distribuidor->di_direita."
				   AND co_id_plano <> 4
				   AND co_pago_industria = 0
				   AND co_pago = 1
				   AND di_ni_patrocinador = ".$this->distribuidor->di_id."")->num_rows;
	
			 
			 $this->indicacao_esquerda = $indicacao_esquerda;
			 $this->indicacao_direita = $indicacao_direita;
			 
			 
			 $this->binario_ativo();
			 
			 $this->atualizar_binario_distribuidor();
		
		
		    return $this->binario_ativo;
		
		}
		
		//Verifica se a quantidade de indicação na perna direita e na perna esquerda e maior que 1
		private function binario_ativo(){
			 if($this->indicacao_esquerda > 0 && $this->indicacao_direita > 0){
				 $this->binario_ativo = 1;
				 }else{
					 $this->binario_ativo = 0;
					 }
			 	 
			}
	  
	  private function atualizar_binario_distribuidor(){
		   if($this->binario_ativo==1){
			   
			 // Verifica se ele possui outro plano que não seja o Light para poder esta habilitado a receber o bonus			 
			 $naoLight = $this->ci->db
				 ->where('co_id_distribuidor',$this->distribuidor->di_id)
				 ->where('co_eplano',1)
				 ->where('co_id_plano <>',4)
				 ->where('co_pago',1)
				 ->get('compras')->result();
			 
				
					 
					$this->ci->db
					   ->where('di_id',$this->distribuidor->di_id)
					   ->update('distribuidores',array(
						'di_binario'=>1
					));

				 
		   }
		   
		  }			
	
	}