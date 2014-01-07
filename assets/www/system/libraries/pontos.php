<?php 
class Pontos{
	 private $distribuidor;
	 private $pontos_esquerda;
	 private $pontos_direita;
	 private $pontos_perna_menor;
	 private $total_pontos_pagos;
	 private $pontos_pagos_array;
	 private $pontos_direita_hoje;
	 private $pontos_esquerda_hoje;
	 private $ci;
	 
	/**
	* Responsável por executar todos os metodos que garante que o objeto da class terá as informações necessaria.
	* @access public
	* @param $distribuidor deve ser infromado um objeto de distribuidor
	* @return void
	*/		
	 public function __construct($distribuidor){
		   $this->ci =& get_instance();
		   $this->distribuidor = $distribuidor;
		   
		   $this->verifica_distribuidor();
		   
		   $this->carrega_esquerda();
		   $this->carrega_direita();
		   $this->carrega_perna_menor();
		   $this->total_pontos_pagos();
		   
		   $this->pontos_direita_hoje = NULL;
		   $this->pontos_esquerda_hoje = NULL;
		 }
   
   
   public function verifica_distribuidor(){
	    if(
		!isset($this->distribuidor->di_id)
		|| !isset($this->distribuidor->di_direita)
		|| !isset($this->distribuidor->di_esquerda)
		){
		exit("Informe um distribuidor para que o objeto funcione. User $obj->carregar_distribuidor()");
		}
	   }
   
   public function get_pontos_esquerda(){
	   return $this->pontos_esquerda;
	   }

   public function get_pontos_direita(){
	   return $this->pontos_direita;
	   }

   public function get_pontos_esquerda_formatado(){
	   return number_format($this->pontos_esquerda,0,',','.');
	   }

   public function get_pontos_direita_formatado(){
	   return number_format($this->pontos_direita,0,',','.');
	   }

   public function get_pontos_perna_menor(){
	   return $this->pontos_perna_menor;
	   }

   public function get_pontos_perna_menor_formatado(){
	   return number_format($this->pontos_perna_menor,0,',','.');
	   }

   public function get_pontos_pagos(){
	   return $this->total_pontos_pagos;
	   }

   public function get_pontos_pagos_formatado(){
	   return number_format($this->total_pontos_pagos,0,',','.');
	   }	   	   

   
   public function pontos_a_pagar(){
	    return $this->pontos_perna_menor - $this->total_pontos_pagos;
	   }
    
	public function direita_hoje(){
		if($this->pontos_direita_hoje === NULL){
			 $this->carrega_direita_hoje();
			}
		return 	$this->pontos_direita_hoje;
		}

	public function esquerda_hoje(){
		if($this->pontos_esquerda_hoje === NULL){
			 $this->carrega_esquerda_hoje();
			}
		return 	$this->pontos_esquerda_hoje;
		}

	private function carrega_esquerda_hoje(){
		      
		     if($this->distribuidor->di_esquerda > 0){
				$pontos_esquerda_hoje = $this->ci->db->query("
				 SELECT SUM(pd_pontos) pontos FROM `distribuidor_ligacao` 
				 JOIN distribuidores ON di_id = `li_id_distribuidor`
				JOIN pontos_distribuidor ON di_id = `pd_distribuidor`
				 WHERE `li_no` =  ".$this->distribuidor->di_esquerda."
				 AND pd_data = '".date('Y-m-d')."'
				")->row();
				$this->pontos_esquerda_hoje = (int)$pontos_esquerda_hoje->pontos;
			 }else{
				 $this->pontos_esquerda_hoje = 0;
				 }
		 
		}					 

	public function carrega_direita_hoje(){
		 
		 if($this->distribuidor->di_direita > 0){
			$pontos_direita_hoje = $this->ci->db->query("
			 SELECT SUM(pd_pontos) pontos FROM `distribuidor_ligacao` 
			 JOIN distribuidores ON di_id = `li_id_distribuidor`
			JOIN pontos_distribuidor ON di_id = `pd_distribuidor`
			 WHERE `li_no` =  ".$this->distribuidor->di_direita."
			 AND pd_data = '".date('Y-m-d')."'
			")->row();
			
			$this->pontos_direita_hoje = (int) $pontos_direita_hoje->pontos;
		 }else{
			 $this->pontos_direita_hoje = 0;
			}
		 
		}	

	/**
	* Carregando o valor do atributo pontos_esquerda
	* @access private
	* @param $id_distribuidor_esquerda Variavel Int que recebe o id do usuario a direita do distribuidor
	* @return void
	*/		
	 public function carrega_esquerda(){
		     if($this->distribuidor->di_esquerda > 0){
				$pontos_esquerda = $this->ci->db->query("
				 SELECT SUM(pd_pontos) pontos FROM `distribuidor_ligacao` 
				 JOIN distribuidores ON di_id = `li_id_distribuidor`
				JOIN pontos_distribuidor ON di_id = `pd_distribuidor`
				 WHERE `li_no` =  ".$this->distribuidor->di_esquerda."
				")->row();
			
			   $this->pontos_esquerda =  (int) $pontos_esquerda->pontos;
			 }else{
				  $this->pontos_esquerda = 0;
				 }
		 }
	
	/**
	* Carregando o valor do atributo pontos_direita
	* @access private
	* @param $id_distribuidor_direita Variavel Int que recebe o id do usuario a direita do distribuidor
	* @return void
	*/	
	private function carrega_direita(){
		if($this->distribuidor->di_direita > 0){
			$pontos_direita = $this->ci->db->query("
			 SELECT SUM(pd_pontos) pontos FROM `distribuidor_ligacao` 
			 JOIN distribuidores ON di_id = `li_id_distribuidor`
			JOIN pontos_distribuidor ON di_id = `pd_distribuidor`
			 WHERE `li_no` =  ".$this->distribuidor->di_direita."
			")->row();
			
			$this->pontos_direita = (int) $pontos_direita->pontos;
		 }else{
			 $this->pontos_direita = 0;
			}
		}
	
	
	/**
	* Funcção que informa a class qual a perna com menor quantidades de pontos
	* @access private
	* @return void
	*/
	private function carrega_perna_menor(){
		 if($this->pontos_esquerda<$this->pontos_direita){
			 $this->pontos_perna_menor = $this->pontos_esquerda;
			 }else{
				 $this->pontos_perna_menor = $this->pontos_direita;
				 }
		}
		
	
	/**
	* Funcção que informa o atributo total_pontos_pagos qual o total de pontos pagos
	* @access private
	* @return void
	*/
	private function total_pontos_pagos(){
		  $p_pagos = $this->ci->db
		  ->select('SUM(pg_pontos) as pontos')
		  ->where('pg_distribuidor',$this->distribuidor->di_id)
		  ->get('pontos_pagos')->row();
		  
		  $this->total_pontos_pagos = (int) $p_pagos->pontos;
		}		

	/**
	* Funcção que informa o atributo total_pontos_pagos qual o total de pontos pagos
	* @access private
	* @return void
	*/
	private function pontos_pagos_array($id_distribuidor){
		  $p_pagos = $this->ci->db
		  ->where('pg_distribuidor',$this->distribuidor->di_id)
		  ->get('pontos_pagos')->result();
		  $this->pontos_pagos_array = $p_pagos;
		}

	
	}