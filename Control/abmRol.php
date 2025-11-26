<?php
class abmRol {
    
    public function listarRolesVista(){
        $salida = [];
        $listaRoles = $this->buscar(null);
        
        foreach($listaRoles as $rol){
            $salida[] = [
                'idrol' => $rol->getIdRol(),
                'rodescripcion' => $rol->getRoDescripcion()
            ];
        }
        return $salida;
    }

    // --- ABM Básico ---

    public function buscar($param){
        $where = " true ";
        if ($param<>NULL){
            if  (isset($param['idrol']))
                $where.=" and idrol =".$param['idrol'];
            if  (isset($param['rodescripcion']))
                $where.=" and rodescripcion ='".$param['rodescripcion']."'";
        }
        $arreglo = Rol::listar($where);
        return $arreglo;
    }

    public function alta($param){
        $resp = false;
        $obj = new Rol();
        $obj->setear($param['idrol'], $param['rodescripcion']); // ID suele ser autoincremental o manual dependiendo tu BD, ajusta si es necesario
        if ($obj->insertar()){ $resp = true; }
        return $resp;
    }

    public function baja($param){
        $resp = false;
        if ($this->seteadosCamposClaves($param)){
            $obj = new Rol();
            $obj->setear($param['idrol'], null);
            if ($obj->eliminar()){ $resp = true; }
        }
        return $resp;
    }

    public function modificacion($param){
        $resp = false;
        if ($this->seteadosCamposClaves($param)){
            $obj = new Rol();
            $obj->setear($param['idrol'], $param['rodescripcion']);
            if ($obj->modificar()){ $resp = true; }
        }
        return $resp;
    }

    private function seteadosCamposClaves($param){
        return isset($param['idrol']);
    }
}
?>