<?php

class Rede {

    private $ci;
    private $nos_ids;
    private $ids_rede = array();
    private $cache_no_inserir = 0;

    function __construct() {
        $this->ci = & get_instance();
    }

    private function caminho_distribuidor($patrocinador) {

        $dis = $this->ci->db
                        ->select(array('di_id'))
                        ->where('di_direita', $patrocinador)
                        ->or_where('di_esquerda', $patrocinador)
                        ->get('distribuidores')->row();

        if (count($dis) > 0) {
            $this->nos_ids[] = $dis->di_id;
            $this->caminho_distribuidor($dis->di_id);
        }
    }

    /**
     * Inserir o nó do patrocinador
     * @param type $patrocinador
     * @param type $lado_rede
     * @return \stdClass
     */
    private function inserir_no_patrocinador($patrocinador, $lado_rede) {
        $no = new stdClass;
        $no->id = $patrocinador->di_id;
        $no->lado = $lado_rede;
        if ($lado_rede == false) {
            if ($patrocinador->di_esquerda == 0) {
                $no->lado = 'e';
            } else {
                $no->lado = 'd';
            }
        }
        return $no;
    }

    private function inserir_na_rede($dis, $lado) {

        $no = new stdClass;
        $no->id = 0;
        $no->lado = $lado;

        self::ultimo_no_extremidade($dis, $lado);

        $no->id = $this->cache_no_inserir->di_id;

        return $no;
    }

    public function ultimo_no_extremidade($no_parameter, $lado) {
        $fild_lado = $lado == 'e' ? 'di_esquerda' : 'di_direita';
        $no = $this->ci->db
                        ->where('di_id', $no_parameter->{$fild_lado})
                        ->select(array('di_id', 'di_esquerda', 'di_direita'))
                        ->get('distribuidores')->row();
        if (count($no) > 0) {
            self::ultimo_no_extremidade($no, $lado);
        } else {
            $this->cache_no_inserir = 0;
            $this->cache_no_inserir = $no_parameter;
        }
    }

    function perna_menor($dis_id, $esquerda, $direita) {

        $qtd_esquerda = $this->ci->db->query("
		 SELECT SUM(pd_pontos) pontos FROM `distribuidor_ligacao` 
		 JOIN distribuidores ON di_id = `li_id_distribuidor`
		 JOIN pontos_distribuidor ON di_id = `pd_distribuidor`
		 WHERE `li_no` =  {$esquerda}
		")->row();
        $qtd_esquerda = $qtd_esquerda->pontos;


        $qtd_direita = $this->ci->db->query("
		 SELECT SUM(pd_pontos) pontos FROM `distribuidor_ligacao` 
		 JOIN distribuidores ON di_id = `li_id_distribuidor`
		JOIN pontos_distribuidor ON di_id = `pd_distribuidor`
		 WHERE `li_no` =  {$direita}
		")->row();
        $qtd_direita = $qtd_direita->pontos;



        if ($qtd_esquerda <= $qtd_direita) {
            return 'e';
        } else {
            return 'd';
        }
    }

    function alocar($di_id) {
        //Verificar se o distribuidor já foi alocado
        $alocado = $this->ci->db
                        ->select('di_id')
                        ->where('di_direita', $di_id)
                        ->or_where('di_esquerda', $di_id)
                        ->get('distribuidores')->row();

        $na_rede = $this->ci->db->where('li_id_distribuidor', $di_id)
                        ->get('distribuidor_ligacao')->num_rows;


        if (count($alocado) == 0 && $na_rede == 0) {

            $distribuidor = $this->ci->db
                            ->join('cidades', 'ci_id = di_cidade')
                            ->where('di_id', $di_id)
                            ->get('distribuidores')->row();

            //é o patrocinador do usuário que está cadastrando
            $pat = $this->ci->db
                            ->join('cidades', 'ci_id = di_cidade')
                            ->where('di_id', $distribuidor->di_ni_patrocinador)
                            ->get('distribuidores')->row();

            if ($distribuidor->di_preferencia_indicador != 0) {
                $pat->di_lado_preferencial = $distribuidor->di_preferencia_indicador;
            }


            $no = false;
            if ($pat->di_lado_preferencial == 1 && $pat->di_esquerda == 0) {
                $no = self::inserir_no_patrocinador($pat, 'e');
            }

            if ($pat->di_lado_preferencial == 2 && $pat->di_direita == 0 && $no == false) {
                $no = self::inserir_no_patrocinador($pat, 'd');
            }

            if ($pat->di_lado_preferencial == 3 && ($pat->di_esquerda == 0 || $pat->di_direita == 0) && $no == false) {
                $no = self::inserir_no_patrocinador($pat, false);
            }

            if ($pat->di_lado_preferencial == 1 && $pat->di_esquerda != 0 && $no == false) {
                $no = self::inserir_na_rede($pat, 'e');
            }

            if ($pat->di_lado_preferencial == 2 && $pat->di_direita != 0 && $no == false) {
                $no = self::inserir_na_rede($pat, 'd');
            }

            if ($pat->di_lado_preferencial == 3 && $pat->di_direita != 0 && $pat->di_esquerda != 0 && $no == false) {
                //Verificar perna menor
                $lado_menor = self::perna_menor($pat->di_id, $pat->di_esquerda, $pat->di_direita);

                $no = self::inserir_na_rede($pat, $lado_menor);
            }



            //Atualizo o nó
            if ($no) {

                if ($no->lado == 'e') {
                    $this->ci->db->select(array('di_id', 'di_esquerda', 'di_direita'))->where('di_id', $no->id)
                            ->update('distribuidores', array(
                                'di_esquerda' => $distribuidor->di_id
                            ));
                } elseif ($no->lado == 'd') {
                    $this->ci->db->select(array('di_id', 'di_esquerda', 'di_direita'))->where('di_id', $no->id)
                            ->update('distribuidores', array(
                                'di_direita' => $distribuidor->di_id
                            ));
                }
            }


            //notificacao_cadastro($distribuidor[0],$patrocinador[0]);

            $this->nos_ids = array($distribuidor->di_id);
            self::caminho_distribuidor($distribuidor->di_id);
            $caminho = array_reverse($this->nos_ids);




            foreach ($caminho as $k => $c) {
                $this->ci->db->insert('distribuidor_ligacao', array(
                    'li_id_distribuidor' => $di_id,
                    'li_posicao' => ($k + 1),
                    'li_no' => $c
                ));
            }
        }
    }

}