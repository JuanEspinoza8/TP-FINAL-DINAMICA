<?php
class abmUsuarioRol {
    
    /**
     * Asigna un rol a un usuario verificando duplicados
     * @param int $idUsuario
     * @param int $idRol
     * @return array ['exito' => bool, 'msg' => string]
     */
    public function agregarRol($idUsuario, $idRol){
        $respuesta = ['exito' => false, 'msg' => ''];
        
        // 1. Verificar si ya tiene ese rol
        $existe = $this->buscar(['idusuario' => $idUsuario, 'idrol' => $idRol]);
        if(count($existe) > 0){
            $respuesta['msg'] = 'El usuario ya posee este rol.';
            return $respuesta;
        }

        // 2. Crear objetos necesarios para el alta
        $objUser = new Usuario();
        $objUser->setear($idUsuario, null, null, null, null);
        
        $objRol = new Rol();
        $objRol->setear($idRol, null);
        
        // 3. Asignar
        if($this->alta(['objUsuario' => $objUser, 'objRol' => $objRol])){
            $respuesta['exito'] = true;
            $respuesta['msg'] = 'Rol asignado correctamente.';
        } else {
            $respuesta['msg'] = 'Error al asignar el rol.';
        }
        
        return $respuesta;
    }

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