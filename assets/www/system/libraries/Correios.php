<?php

class Correios{
	
 private $ci;
 private $cep_origem;
 private $cep_destino;
 private $largura;
 private $altura;
 private $comprimento;
 private $peso;
 private $resultado;
 
 public function __construct(){
	 $this->ci =& get_instance(); 
	  $this->peso = 1;
	  $this->comprimento = 16;
	  $this->largura = 16;
	  $this->altura = 16;
	  $this->resultado = array();
	 }
	 
	 	
 private function set_cep_origem_default(){
	  $fabrica = $this->ci->db->get('fabricas',1)->result();
	  $this->cep_origem = $fabrica[0]->fa_cep;
	 }	
	 
 public function calcular_frete($cep_destino,$peso=1,$cep_origem=0){
	  $this->resultado = array();
	  $this->cep_destino = $cep_destino;
	  $this->peso = $peso;
	  
	  if($cep_origem==0)
	   {
	   $this->set_cep_origem_default();
	   }else{
	   $this->cep_origem = $cep_origem;
	   }
	   
	   $this->tratar_cep();
	   
	   
	  
	   
	   //Calcula valor PAC
	   $pac = @simplexml_load_file($this->get_url(41106));
	   
	   
	   if($pac && $pac->cServico->Erro==0){
		   $this->resultado['pac'] = array(
		   'valor'=>$pac->cServico->Valor[0],
		   'prazo'=>$pac->cServico->PrazoEntrega[0],
		   );
	   }
	   
	   
	   $sedex =  @simplexml_load_file($this->get_url(40010));
	    if($pac && $sedex->cServico->Erro==0){
		   $this->resultado['sedex'] = array(
		   'valor'=>$sedex->cServico->Valor,
		   'prazo'=>$sedex->cServico->PrazoEntrega,
		   );
	   }
	   	
		return $this->resultado;  
	  }
	
	
	
public function get_url($cod_servico){
	return "http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa=&sDsSenha=&sCepOrigem=".$this->cep_origem."&sCepDestino=".$this->cep_destino."&nVlPeso=".$this->peso."&nCdFormato=1&nVlComprimento=".$this->comprimento."&nVlAltura=".$this->altura."&nVlLargura=".$this->largura."&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico=".$cod_servico."&nVlDiametro=0&StrRetorno=xml";
	}	  

public function tratar_cep(){
	$this->cep_destino = trim(str_ireplace('-','',$this->cep_destino));
	$this->cep_origem = trim(str_ireplace('-','',$this->cep_origem));
	}

public function codigo_rastreio($compra,$codigo_ras){
	
	// Verifica se esta cadastrando o Código e rastreio dos Correios
		
			// Cadastro de código de rastreio
			if($this->ci->db->where('co_id',$compra->co_id)->update('compras',array(
			  'co_frete_codigo'=>$codigo_ras
			 ))){
			     
				 $distribuidor = $this->ci->db
				 ->where('di_id',$compra->co_id_distribuidor)
				 ->get('distribuidores')->row();
				 
				 $fabrica = $this->ci->db->get('fabricas')->row();
				 
				 // Manda um email de novo código.
				  $mensagem = "Unos Empreendedores<br><br>
				  <strong>Olá ".$distribuidor->di_nome."</strong><br>
				 O status do seu pedido de nº ".$compra->co_id."  foi atualizado!<br><br>
				 Seu pedido possui um código de rastreio dos Correios:<br>
				 CÓDIGO DE RASTREIO: <strong>".$codigo_ras."</strong><br><br>
				 Acesse seu painel para mais informações sobre o pedido.<br>
				 Qualquer dúvida entre em contato através do email  <a href='mailto:".$fabrica->fa_email."'>".$fabrica->fa_email."</a>";
				 
				 mailSend($distribuidor->di_email,$mensagem,"Status do Pedido - ".$fabrica->fa_nome,$fabrica->fa_email);
				 
				 set_notificacao(1,"O pedido de nº ".$compra->co_id." foi atualizado com sucesso!");
			 }else{
				 set_notificacao(0,"Houve uma falha na atualização do pedido de nº ".$compra->co_id.", tente novamente!(email novo)");
			 }
			 
			 
		
	
	}

//	
public function codigo_rastreio_fabrica($id_compra,$codigo_ras,$compra){
	   // Verifica se esta cadastrando o Código e rastreio dos Correios
		if(empty($compra->cr_frete_codigo) && !empty($codigo_ras)){
			// Cadastro de código de rastreio
			if($this->ci->db->where('cr_id',$id_compra)->update('compras_fabrica',array(
			  'cr_frete_codigo'=>$codigo_ras
			 ))){
			 
				 // Manda um email de novo código.
				  $mensagem = "Unos Empreendedores<br><br>
				  <strong>Olá ".$compra[0]->cd_nome."</strong><br>
				 O status do seu pedido de nº $id_compra foi atualizado!<br><br>
				 Seu pedido possui um código de rastreio dos Correios:<br>
				 CÓDIGO DE RASTREIO: <strong>$codigo_ras</strong><br><br>
				 Acesse seu painel para mais informações sobre o pedido.<br>
				 Qualquer dúvida entre em contato através do email  <a href='mailto:relacionamento@unosempreendedores.com.br'>relacionamento@unosempreendedores.com.br</a>";
				 
				 mailSend($compra[0]->cd_email,$mensagem,"Status do Pedido - Unos Empreendedores","relacionamento@unosempreendedores.com.br");
				 
				 set_notificacao(1,"O pedido de nº $id_compra foi atualizado com sucesso!");
			 }else{
				 set_notificacao(0,"Houve uma falha na atualização do pedido de nº $id_compra, tente novamente!(email novo)");
			 }
			 
		}elseif(!empty($compra->cr_frete_codigo) && !empty($codigo_ras)){
			
			// Atualização de código de rastreio
			if($this->ci->db->where('cr_id',$id_compra)->update('compras',array(
			  'cr_frete_codigo'=>$codigo_ras
			 ))){
			 
				 // Manda um email de atualização de código.
				  $mensagem = "Unos Empreendedores<br><br>
				  <strong>Olá ".$compra[0]->cd_nome."</strong><br>
				 O status do seu pedido de nº $id_compra foi atualizado!<br><br>
				 O código de rastreio dos Correios em seu pedido foi atualizado para:<br>
				 CÓDIGO DE RASTREIO: <strong>$codigo_ras</strong><br><br>
				 Acesse seu painel para mais informações sobre o pedido.<br>
				 Qualquer dúvida entre em contato através do email  <a href='mailto:relacionamento@unosempreendedores.com.br'>relacionamento@unosempreendedores.com.br</a>";
				 
				 mailSend($compra[0]->cd_email,$mensagem,"Status do Pedido - Unos Empreendedores","relacionamento@unosempreendedores.com.br");
				 
				 set_notificacao(1,"O pedido de nº $id_compra foi atualizado com sucesso!");
			 }else{
				 set_notificacao(0,"Houve uma falha na atualização do pedido de nº $id_compra, tente novamente!(email atualiz)");
			 }
			 
		}else{
			// Nenhuma alteração relacionada ao Código de rastreio	
		}	
  }
 	 
}