<?php
class abmMenu {
    
    // --- Lógica de Negocio Compleja ---

    public function listarMenuesConRoles(){
        $salida = [];
        $menus = $this->buscar(null);
        $abmMenuRol = new abmMenuRol();

        foreach($menus as $m){
            $rolesAsignados = $abmMenuRol->buscar(['idmenu' => $m->getIdMenu()]);
            $listaRolesID = [];
            
            foreach($rolesAsignados as $mr){
                $listaRolesID[] = $mr->getObjRol()->getIdRol();
            }

            $salida[] = [
                'idmenu' => $m->getIdMenu(),
                'menombre' => $m->getMeNombre(),
                'medescripcion' => $m->getMeDescripcion(),
                'idpadre' => $m->getIdPadre(),
                'medeshabilitado' => $m->getMeDeshabilitado(),
                'roles' => $listaRolesID 
            ];
        }
        return $salida;
    }

    public function obtenerMenuesFormateadosPorRol($idRol){
        $listaSalida = [];
        // Reutilizamos el método que filtra por rol
        $listaMenues = $this->obtenerMenuPorRol($idRol);
        
        foreach($listaMenues as $menu){
            $listaSalida[] = [
                'idmenu' => $menu->getIdMenu(),
                'menombre' => $menu->getMeNombre(),
                'medescripcion' => $menu->getMeDescripcion(),
                'idpadre' => $menu->getIdPadre()
            ];
        }
        return $listaSalida;
    }

    public function guardarMenuConRoles($datos){
        $respuesta = ['exito' => false, 'msg' => 'Error desconocido'];
        $idMenu = null;
        $exitoMenu = false;

        if(isset($datos['accion']) && $datos['accion'] == 'nuevo'){
            if($this->alta($datos)){
                $busqueda = $this->buscar(['menombre'=>$datos['menombre'], 'medescripcion'=>$datos['medescripcion']]);
                if(count($busqueda) > 0){
                    $nuevoMenu = end($busqueda);
                    $idMenu = $nuevoMenu->getIdMenu();
                    $exitoMenu = true;
                }
            }
        } elseif(isset($datos['accion']) && $datos['accion'] == 'editar'){
            if($this->modificacion($datos)){
                $idMenu = $datos['idmenu'];
                $exitoMenu = true;
            }
        }

        if($exitoMenu && $idMenu != null){
            $abmMenuRol = new abmMenuRol();
            
            $rolesActuales = $abmMenuRol->buscar(['idmenu' => $idMenu]);
            foreach($rolesActuales as $mr){
                $mr->eliminar(); 
            }

            if(isset($datos['roles']) && is_array($datos['roles'])){
                foreach($datos['roles'] as $idRol){
                    $objMenu = new Menu();
                    $objMenu->setear($idMenu, null, null, null, null);
                    
                    $objRol = new Rol();
                    $objRol->setear($idRol, null);
                    
                    $abmMenuRol->alta(['objMenu' => $objMenu, 'objRol' => $objRol]);
                }
            }
            $respuesta = ['exito' => true, 'msg' => 'Menú guardado correctamente.'];
        } else {
            $respuesta['msg'] = 'No se pudo guardar el menú base.';
        }

        return $respuesta;
    }

    public function cambiarEstadoMenu($datos){
        $res = false;
        if(isset($datos['accion']) && isset($datos['idmenu'])){
            if($datos['accion'] == 'baja'){
                $res = $this->baja($datos);
            } elseif($datos['accion'] == 'alta'){
                $res = $this->habilitar($datos);
            }
        }
        return $res;
    }

    // --- ABM Básico ---

    public function buscar($param){
        $where = " true ";
        if ($param<>NULL){
            if  (isset($param['idmenu']))
                $where.=" and idmenu =".$param['idmenu'];
            if  (isset($param['menombre']))
                $where.=" and menombre ='".$param['menombre']."'";
            if  (isset($param['medeshabilitado']))
                $where.=" and medeshabilitado IS NULL";
        }
        $arreglo = Menu::listar($where);
        return $arreglo;
    }

    public function alta($param){
        $resp = false;
        $obj = new Menu();
        $padre = isset($param['idpadre']) && $param['idpadre'] != "" ? $param['idpadre'] : null;
        $obj->setear(null, $param['menombre'], $param['medescripcion'], $padre, null);
        if ($obj->insertar()){ $resp = true; }
        return $resp;
    }

    public function modificacion($param){
        $resp = false;
        if (isset($param['idmenu'])){
            $obj = new Menu();
            $obj->setear($param['idmenu'], null, null, null, null);
            if($obj->cargar()){
                $padre = isset($param['idpadre']) && $param['idpadre'] != "" ? $param['idpadre'] : $obj->getIdPadre();
                $nombre = isset($param['menombre']) ? $param['menombre'] : $obj->getMeNombre();
                $desc = isset($param['medescripcion']) ? $param['medescripcion'] : $obj->getMeDescripcion();
                $deshab = $obj->getMeDeshabilitado();
                
                $obj->setear($param['idmenu'], $nombre, $desc, $padre, $deshab);
                if($obj->modificar()){ $resp = true; }
            }
        }
        return $resp;
    }

    public function baja($param){
        $resp = false;
        if (isset($param['idmenu'])){
            $obj = new Menu();
            $obj->setear($param['idmenu'], null, null, null, null);
            if($obj->cargar()){
                $obj->setMeDeshabilitado(date('Y-m-d H:i:s'));
                if($obj->modificar()){ $resp = true; }
            }
        }
        return $resp;
    }
    
    public function habilitar($param){
        $resp = false;
        if (isset($param['idmenu'])){
            $obj = new Menu();
            $obj->setear($param['idmenu'], null, null, null, null);
            if($obj->cargar()){
                $obj->setMeDeshabilitado(null);
                if($obj->modificar()){ $resp = true; }
            }
        }
        return $resp;
    }
    
    public function obtenerMenuPorRol($idRol){
        $menus = [];
        $base = new BaseDatos();
        $sql = "SELECT m.* FROM menu m INNER JOIN menurol mr ON m.idmenu = mr.idmenu WHERE mr.idrol = ".$idRol." AND m.medeshabilitado IS NULL";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($sql)) {
                while ($row2=$base->Registro()) {
                    $obj=new Menu();
                    $obj->setear($row2['idmenu'],$row2['menombre'],$row2['medescripcion'],$row2['idpadre'],$row2['medeshabilitado']);
                    array_push($menus, $obj);
                }
            }
        }
        return $menus;
    }
}
?>