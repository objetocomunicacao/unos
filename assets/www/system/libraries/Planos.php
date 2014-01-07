<?php
class Planos{
	
	 private $ci;
	 
	 function __construct(){
		$this->ci =& get_instance();
		}
	 
	 function lancar($compra,$beneficios=true){
		  
		$distribuidor = $this->ci->db
                        ->select(array('di_usuario','di_ni_patrocinador'))
                        ->where('di_id',$compra->co_id_distribuidor)
                        ->get('distribuidores')->row();
										
		#obter o plano
		$plano = $this->ci->db
                        ->where('pa_id',$compra->co_id_plano)
                        ->get('planos')->row();
		
		
		#inserindo as vitrines	
		 $this->ci->db->insert('vitrines',array(
			'vt_distribuidor'=>$compra->co_id_distribuidor,
			'vt_compra'=>$compra->co_id,
			'vt_entrada'=>$plano->pa_vitrines,
			'vt_data'=>date('Y-m-d')
			));
		
		
		if($beneficios){
		    #inserindo os Pontos
			$this->ci->db->insert('pontos_distribuidor',array(
			'pd_distribuidor'=>$compra->co_id_distribuidor,
			'pd_compra'=>$compra->co_id,
			'pd_pontos'=>$plano->pa_pontos,
			'pd_tipo'=>1,
			'pd_data'=>date('Y-m-d')
			));
		}
		
										 
		  
	}
		 
	 
	
}