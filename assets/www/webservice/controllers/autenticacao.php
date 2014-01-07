<?php
class Autenticacao extends CI_Controller{
	
	
	public function login(){
		 
		 $login = $this->input->post('login');
		 $senha = sha1($this->input->post('senha'));
		var_dump($senha);
		exit; 
		 $user = $this->db
		 ->select('di_usuario,di_nome,di_id,di_usuario')
		 ->where('di_usuario',$login)
		 ->where('di_senha',$senha)
		 ->get('distribuidores')
		 ->row();

		 $token= $this->db
		 ->select('*')
		 ->where('tk_di_id',$user->di_id)
		 ->get('token_api')
		 ->row();
		 
		 $hash=md5($user->di_usuario);
		 $token_key= base64_encode($hash).base64_encode(sha1(date('d-m-Y H-m-s')));
		 
		 $data = array(
		 		'tk_di_id' =>  $user->di_id,
		 		'tk_token' => $token_key ,
		 		'tk_data' => date('Y-m-d H-m-s')
		 );
		 
		 if(count($token)>0)
		 {		 	
			$this->db->where('tk_di_id', $user->di_id);
			$this->db->update('token_api', $data); 
			
		 }else{
		 	$this->db->insert('token_api', $data);
		 }
	
		 if($user){
			  echo json_encode(array('token'=>$token_key,'success'=>'ok'));
			 }else{ 
				 echo json_encode(array('Exception'=>'usuário ou senha são invalido'));
			  }
		 
		}
		
		
	
	}