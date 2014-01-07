<?php
class Financeiro extends CI_Controller{
	 
	 public function historico_bonus(){
		  
		  $bonus = $this->db
		  ->select(array("cb_descricao as descricao", "DATE_FORMAT(cb_data_hora,'%Y-%m-%d') as data"))
		  ->join('bonus_tipo','cb_tipo=tb_id')
		  ->where('cb_distribuidor',$this->input->post('id'))
		  ->order_by('cb_data_hora','DESC')
		  ->get('conta_bonus',20)->result();
		  
		  echo json_encode($bonus);
		  
		 }
		
		 
	 public function historico_transacao(){
		  
		  $bonus = $this->db
		  ->select(array("cb_descricao as descricao", "DATE_FORMAT(cb_data_hora,'%Y-%m-%d') as data"))
		  ->join('bonus_tipo','cb_tipo=tb_id')
		  ->where('cb_distribuidor',$this->input->post('id'))
		  ->order_by('cb_data_hora','DESC')
		  ->get('conta_bonus',20)->result();
		  
		  echo json_encode($bonus);
		  
		 }		 

	 public function resumo_bonus(){
		  
		  $bonus = $this->db
		  ->select('tb_descricao as bonus, SUM(cb_credito) as valor')
		  ->join('conta_bonus','cb_tipo=tb_id')
		  ->where('cb_distribuidor',$this->input->post('id'))
		  ->group_by('cb_tipo')
		  ->get('bonus_tipo')->result();
		  
		  echo json_encode($bonus);
		  
		 }	
	
	
	}