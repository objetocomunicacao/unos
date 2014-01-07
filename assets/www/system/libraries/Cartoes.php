<?php
class Cartoes{
	
	private $ci;
	
	public function __construct(){
		$this->ci =& get_instance();
		}
	
	/*
	*Função raliza o pagamento do bônus e pontos que o distribuidor
	*ganha quando compra qualquer cartão.
	*Essa função deve ser chamada toda vez que um cartão for  pago
	*/
	public function lancar($compra){
		 
		 //Obter dados do cartão
		 $cartao = $this->ci->db
		 ->where('ca_id',$compra->co_idcartao)
		 ->get('cartoes')->row();
		 
		 //O Cliente que comprou o cartão
		 $clienteCartao = $this->ci->db
		 ->where('cc_id',$compra->co_id_cliente_cartao)
		 ->get('cliente_cartao')->row();
		 
		 if(count($cartao) > 0 && count($clienteCartao) >0 ){
		  
		  //Depositar o bônus para o distribuidor que vendeu o carão
		   $this->ci->db->insert('conta_bonus',array(
		    'cb_distribuidor'=>$compra->co_id_distribuidor,
			'cb_compra'=>$compra->co_id,
			'cb_descricao'=>'Bônus de venda direta Nº '.$compra->co_id,
			'cb_credito'=>$cartao->ca_bonus,
			'cb_tipo'=>7
		   ));
		   
		  //Registrar os pontos do distribuidor
		  $this->ci->db->insert('pontos_distribuidor',array(
		   'pd_distribuidor'=>$compra->co_id_distribuidor,
		   'pd_pontos'=>$cartao->ca_pontos,
		   'pd_tipo'=>3,
		   'pd_data'=>date('Y-m-d')
		  ));  
		   
		 }
		 
		}
	
}