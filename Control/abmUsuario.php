<?php
class abmUsuario {
    
    // --- Funciones complejas para Vistas/Listados ---

    public function listarUsuariosConRoles(){
        $salida = [];
        $listaUsers = $this->buscar(null);
        $abmUsuarioRol = new abmUsuarioRol();

        foreach($listaUsers as $user){
            // Buscar roles de este usuario
            $rolesUser = $abmUsuarioRol->buscar(['idusuario' => $user->getIdUsuario()]);
            $rolesDesc = [];
            foreach($rolesUser as $ur){
                $rolesDesc[] = [
                    'idrol' => $ur->getObjRol()->getIdRol(), 
                    'rodescripcion' => $ur->getObjRol()->getRoDescripcion()
                ];
            }

            $salida[] = [
                'idusuario' => $user->getIdUsuario(),
                'usnombre' => $user->getUsNombre(),
                'usmail' => $user->getUsMail(),
                'roles' => $rolesDesc
            ];
        }
        return $salida;
    }

    public function registrarUsuario($datos){
        $respuesta = ['exito' => false, 'msg' => ''];
        $existe = $this->buscar(['usnombre' => $datos['usnombre']]);
        if(count($existe) > 0){
            $respuesta['msg'] = 'El nombre de usuario ya está en uso.';
            return $respuesta;
        }
        $datos['uspass'] = md5($datos['uspass']); 
        if($this->alta($datos)){
            $nuevoUserList = $this->buscar(['usnombre' => $datos['usnombre']]);
            if(count($nuevoUserList) > 0){
                $objUsuario = $nuevoUserList[0];
                $abmRol = new abmUsuarioRol();
                $objRol = new Rol();
                $objRol->setear(2, null); 
                $datosRol = ['objUsuario' => $objUsuario, 'objRol' => $objRol];
                if($abmRol->alta($datosRol)){
                    $respuesta['exito'] = true;
                    $respuesta['msg'] = 'Registro exitoso.';
                } else {
                    $respuesta['msg'] = 'Usuario creado, fallo rol.';
                }
            }
        } else {
            $respuesta['msg'] = 'Error BD.';
        }
        return $respuesta;
    }

    public function actualizarPerfil($datos, $usuarioActual){
        $respuesta = ['exito' => false, 'msg' => 'Error desconocido'];
        if($datos['idusuario'] != $usuarioActual->getIdUsuario()){
            $respuesta['msg'] = 'No tienes permisos.';
            return $respuesta;
        }
        $passFinal = $usuarioActual->getUsPass();
        if(isset($datos['uspass']) && trim($datos['uspass']) != ""){
            $passFinal = md5($datos['uspass']);
        }
        $param = [
            'idusuario' => $datos['idusuario'],
            'usnombre' => $usuarioActual->getUsNombre(),
            'usmail' => $datos['usmail'],
            'uspass' => $passFinal
        ];
        if($this->modificacion($param)){
            $respuesta = ['exito' => true, 'msg' => 'Perfil actualizado.'];
        } else {
            $respuesta['msg'] = 'Error BD.';
        }
        return $respuesta;
    }

    // --- ABM Básico ---

    public function buscar($param){
        $where = " true ";
        if ($param<>NULL){
            if  (isset($param['idusuario']))
                $where.=" and idusuario =".$param['idusuario'];
            if  (isset($param['usnombre']))
                $where.=" and usnombre ='".$param['usnombre']."'";
            if  (isset($param['usmail']))
                $where.=" and usmail ='".$param['usmail']."'";
            if  (isset($param['uspass']))
                $where.=" and uspass ='".$param['uspass']."'";
        }
        $arreglo = Usuario::listar($where);
        return $arreglo;
    }

    public function alta($param){
        $resp = false;
        $obj = new Usuario();
        $obj->setear(null, $param['usnombre'], $param['uspass'], $param['usmail'], null);
        if ($obj->insertar()){ $resp = true; }
        return $resp;
    }

    public function baja($param){ 
        $resp = false;
        if ($this->seteadosCamposClaves($param)){
            $obj = new Usuario();
            $obj->setear($param['idusuario'], null, null, null, null);
            if ($obj->cargar()){
                $obj->setUsDeshabilitado(date('Y-m-d H:i:s'));
                if($obj->modificar()){ $resp = true; }
            }
        }
        return $resp;
    }

    public function modificacion($param){
        $resp = false;
        if ($this->seteadosCamposClaves($param)){
             $obj = new Usuario();
             $obj->setear($param['idusuario'], $param['usnombre'], $param['uspass'], $param['usmail'], null);
             if($obj->modificar()){ $resp = true; }
        }
        return $resp;
    }

    private function seteadosCamposClaves($param){
        return isset($param['idusuario']);
    }
}
?>