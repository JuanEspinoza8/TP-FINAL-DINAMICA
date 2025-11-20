<?php
class abmUsuarioRol {
    public function buscar($param){
        $where = " true ";
        if ($param<>NULL){
            if  (isset($param['idusuario']))
                $where.=" and idusuario =".$param['idusuario'];
            if  (isset($param['idrol']))
                $where.=" and idrol =".$param['idrol'];
        }
        $arreglo = UsuarioRol::listar($where);
        return $arreglo;
    }

    public function alta($param){
       
        $resp = false;
        $obj = new UsuarioRol();
        $obj->setear($param['objUsuario'], $param['objRol']);
        if ($obj->insertar()){ $resp = true; }
        return $resp;
    }
}
?>