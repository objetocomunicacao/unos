<?php 
class Ativacao{
	private $distribuidor;
	private $ci;
	
	public function __construct($distribuidor){
		$this->distribuidor = $distribuidor;
		
		$this->ci =& get_instance();
	}	
	
	// Verifica se esta ativo e atualiza a coluna di_ativo da tabela distribuidores
	public function esta_ativo(){
		
		//Primeiro busco qualquer compra de ativação por valor do distribuidor
		$ativacaoPorValor = $this->ci->db
		->where('co_id_distribuidor',$this->distribuidor)
		->where('co_eativo',1)
		->where('co_pago',1)
		->get('compras')->result();

############### Ativação por 99.99	
		if(sizeof($ativacaoPorValor)){
			//Não pode mais ser ativo por produtos, busca compra de ativação no mês atual
			$ativo = $this->ci->db
			->where('co_data_insert >=',date('Y-m-01'))
			->where('co_id_distribuidor',$this->distribuidor)
			->where('co_eativo',1)
			->where('co_pago',1)
			->get('compras')->row();
			
			if(sizeof($ativo)){
				//Ativa o cadastro
				$this->ci->db->where('di_id',$this->distribuidor)->update('distribuidores',array(
				  'di_ativo'=>'1', 
				 ));
				 $this->registra_ativacao(1,$ativo->co_id,'99.99');
				 return true;
			}else{
				//Desativa o cadastro
				$this->ci->db->where('di_id',$this->distribuidor)->update('distribuidores',array(
				  'di_ativo'=>'0', 
				 ));
				 return false;
			}
############## Ativação por compra acima de 50 Pontos				
		}else{
			//Ainda pode ser ativo por produtos, busca compra acima de 50 pontos
			$ativo = $this->ci->db
			->where('co_data_compra >=',date('Y-m-01'))
			->where('co_id_distribuidor',$this->distribuidor)
			->where('co_total_pontos >=',50)
			->where('co_pago',1)
			->get('compras')->row();
			
			if(sizeof($ativo)){
				//Ativa o cadastro
				$this->ci->db->where('di_id',$this->distribuidor)->update('distribuidores',array(
				  'di_ativo'=>'1', 
				 ));
				 $this->registra_ativacao(2,$ativo->co_id,$ativo->co_total_pontos);
				 return true;
			}else{
				//Desativa o cadastro
				$this->ci->db->where('di_id',$this->distribuidor)->update('distribuidores',array(
				  'di_ativo'=>'0', 
				 ));
				 return false;
			}
			
		}		
		
	}
	
	public function receber_bonus($distribuidor){
		  
		if($distribuidor->di_ativo==1){
			return true;
		}else{
			// checaCarencia() esta na Helper
			if(checaCarencia()){
				return true;
			}else{
				return false;
			}
		}
	}
	
	// Registra na auditoria informações de ativação em qualquer ativação no sistema
	// (int $tipo,int $id_compra) -> tipo 1 = ativação por unos vantagens - tipo 2 = ativação por pontos de compra
	public function registra_ativacao($tipo,$id_compra,$valorOuPontos){
		
		//Verifica se já possui registro de ativação no mês atual
			$registro = $this->ci->db
			->where('aua_data >=',date('Y-m-01'))
			->where('aua_distribuidor',$this->distribuidor)
			->get('auditoria_ativacao')->row();
		
		if(!sizeof($registro)){
			##Registra na auditória o tipo de ativação
			if($tipo == 1){
				 $this->ci->db->insert('auditoria_ativacao',array(
				 'aua_distribuidor'=>$this->distribuidor,
				 'aua_compra'=>$id_compra,
				 'aua_valor'=>$valorOuPontos,
				 'aua_pontos'=>0,
				 'aua_tipo'=>1,
				 'aua_data'=>date('Y-m-d')
				 ));	
			}else{
				$this->ci->db->insert('auditoria_ativacao',array(
				 'aua_distribuidor'=>$this->distribuidor,
				 'aua_compra'=>$id_compra,
				 'aua_valor'=>0,
				 'aua_pontos'=>$valorOuPontos,
				 'aua_tipo'=>2,
				 'aua_data'=>date('Y-m-d H:i:s')
				 ));
			}
		}
	}
}

?>