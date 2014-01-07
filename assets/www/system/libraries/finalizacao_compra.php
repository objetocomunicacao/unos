<?php
class Finalizacao_compra{
	
	private $ci;
	
	public function __construct(){
		$this->ci =& get_instance(); 
		}
		
	public function bonus_compra_dd($compra){
		
		 //Verifica se a compra foi maior que 5000 mil e adiciona 10% de bonus quando o administrador informa pagamento.
		 if($compra->co_total_valor >= 5000){
			 
			 $credito = $compra->co_total_valor*0.1;
			 
			 $bonus = array(
			 'cb_distribuidor'=>$compra->co_id_distribuidor,
			 'cb_compra'=>$compra->co_id,
			 'cb_descricao'=>'Bonus DD da compra Nº '.$compra->co_id,
			 'cb_credito'=>$credito,
			 'cb_debito'=>'0.00',
			 'cb_tipo'=>'10' //Pagamento de Compras
			 );
			 
			 $this->ci->db->insert('conta_bonus',$bonus);
			 
		 }
	}
		
}