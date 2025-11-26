<?php
class abmCompraEstado {
    
    /**
     * Cierra los estados abiertos de una compra y crea uno nuevo.
     * @param int $idCompra
     * @param int $nuevoTipo (ID del tipo de estado: 1:Iniciada, 2:Aceptada, 3:Enviada, 4:Cancelada)
     * @return array ['exito'=>bool, 'msg'=>string]
     */
    public function cambiarEstadoCompra($idCompra, $nuevoTipo){
        $respuesta = ['exito' => false, 'msg' => 'Error desconocido'];
        
        // 1. Buscar estados abiertos (cefechafin null)
        $estadosAbiertos = $this->buscar(['idcompra' => $idCompra, 'cefechafin' => 'null']);
        
        // 2. Cerrar estados anteriores
        $exitoCierre = true;
        foreach($estadosAbiertos as $est){
            $est->setCefechaFin(date('Y-m-d H:i:s'));
            if(!$est->modificar()){
                $exitoCierre = false;
            }
        }

        // 3. Crear nuevo estado
        if($exitoCierre){
            $nuevoEstado = new CompraEstado();
            // setear(id, idcompra, idtipo, fechaini, fechafin)
            $nuevoEstado->setear(null, $idCompra, $nuevoTipo, date('Y-m-d H:i:s'), null);
            
            if($nuevoEstado->insertar()){
                $respuesta = ['exito' => true, 'msg' => 'Estado actualizado correctamente.'];
            } else {
                $respuesta = ['exito' => false, 'msg' => 'Error al crear el nuevo estado.'];
            }
        } else {
            $respuesta = ['exito' => false, 'msg' => 'Error al cerrar el estado anterior.'];
        }
        
        return $respuesta;
    }

    public function buscar($param){
        $where = " true ";
        if ($param<>NULL){
            if  (isset($param['idcompra']))
                $where.=" and idcompra =".$param['idcompra'];
            if  (isset($param['idcompraestadotipo']))
                $where.=" and idcompraestadotipo =".$param['idcompraestadotipo'];
            if  (isset($param['cefechafin'])){
                if($param['cefechafin'] == 'null'){
                    $where.=" and cefechafin IS NULL";
                }
            }
        }
        $arreglo = CompraEstado::listar($where);
        return $arreglo;
    }
}
?>