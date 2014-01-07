<?php
class CalculaImposto{
	
		static function impostoRenda($valor,$inss,$dependentes){
		 $base_calculo = ($valor-$inss)-($dependentes*171.97);
		 
		 $imposto = 0;
		 
		 if($base_calculo >= 1710.79 && $base_calculo <= 2563.91 ){
			 $imposto = ($base_calculo*(7.5/100))-128.31;
			 }else if($base_calculo >= 2563.92 && $base_calculo <= 3418.59){
				 $imposto = ($base_calculo*(15/100))-320.60;
				 }else if($base_calculo >= 3418.60 && $base_calculo <= 4271.59){
					 $imposto = ($base_calculo*(22.5/100))-577.00;
					 }else if($base_calculo >= 4271.59){
						  $imposto = ($base_calculo*(27.5/100))-790.58;
						 }
						 
		 return $imposto;				 
		 
		}	
		
	
	function INSS($valor){
	 $inss = 0;
	 
	 if($valor < 1247.70){
		  $inss = $valor*(8/100);
		 }else if($valor>=1247.71 && $valor <=2079.50){
			 $inss = $valor*(9/100);
			 }else if($valor>=2079.51){
				  $new_valor = $valor>4159.00?4159.00:$valor;
				  $inss = $new_valor*(11/100); 
				 }
	 return $inss;
	}
		
		
	
	}