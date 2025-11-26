<?php
class abmProducto {

    // Sube la imagen y devuelve la ruta
    public function subirImagen($imagen){
        $dir = '../../Vista/img/productos/';
        $rutaRelativa = 'img/productos/';
        
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        
        if ($imagen['error'] <= 0) {
            $nombre = $imagen['name'];
            $rutaDestino = $dir . $nombre;
            
            if (move_uploaded_file($imagen['tmp_name'], $rutaDestino)) {
                return $rutaRelativa . $nombre; 
            }
        }
        return null;
    }

    public function agregarNuevoProducto($datos, $archivos){
        $respuesta = ['exito' => false, 'msg' => 'Error desconocido'];
        
        // Validamos que lleguen los datos mínimos (ajustado a proprecio)
        if(isset($datos['pronombre']) && isset($datos['prodetalle']) && isset($datos['procantstock']) && isset($datos['proprecio'])){
            
            // Manejo de Imagen (Prioridad: Archivo > Texto > Vacío)
            $rutaImagen = isset($datos['proimagen']) ? $datos['proimagen'] : ''; 
            
            if(isset($archivos['imagen']) && $archivos['imagen']['error'] === 0){
                $rutaSubida = $this->subirImagen($archivos['imagen']);
                if($rutaSubida != null){
                    $rutaImagen = $rutaSubida;
                }
            }
            $datos['proimagen'] = $rutaImagen;

            if($this->alta($datos)){
                $respuesta = ['exito' => true, 'msg' => 'Producto creado con éxito.'];
            } else {
                $respuesta = ['exito' => false, 'msg' => 'Error al insertar en base de datos.'];
            }

        } else {
            $respuesta['msg'] = 'Faltan datos obligatorios.';
        }

        return $respuesta;
    }

    public function actualizarDatosProducto($datos, $archivos){
        $respuesta = ['exito' => false, 'msg' => 'Error desconocido'];
        
        if(isset($datos['idproducto'])){
            
            // 1. Determinar imagen a usar
            // Por defecto usamos la que viene del formulario (texto) o la que ya tenía el objeto si no mandan nada
            $imagenAUsar = isset($datos['proimagen']) ? $datos['proimagen'] : ''; 
            
            // Si el usuario NO mandó texto nuevo, buscamos la anterior para no perderla (opcional, depende tu lógica de vista)
            if($imagenAUsar == ''){
                 $productoActual = new Producto();
                 $productoActual->setear($datos['idproducto'], null, null, null, null, null);
                 if($productoActual->cargar()){
                     $imagenAUsar = $productoActual->getProImagen();
                 }
            }

            // 2. Si mandaron ARCHIVO nuevo, este tiene prioridad absoluta
            if(isset($archivos['imagen']) && $archivos['imagen']['error'] === 0){
                $nuevaRuta = $this->subirImagen($archivos['imagen']);
                if($nuevaRuta != null){
                    $imagenAUsar = $nuevaRuta;
                }
            }
            
            $datos['proimagen'] = $imagenAUsar;

            // 3. Ejecutamos la modificación
            if($this->modificacion($datos)){
                $respuesta = ['exito' => true, 'msg' => 'Producto actualizado correctamente.'];
            } else {
                $respuesta = ['exito' => false, 'msg' => 'Error al modificar en base de datos.'];
            }

        } else {
            $respuesta['msg'] = 'Falta el ID del producto.';
        }

        return $respuesta;
    }
    
    public function listarProductosTienda($datos){
        $arregloSalida = [];
        $lista = $this->buscar(null);
        
        foreach($lista as $prod){
            $stock = (int)$prod->getProCantStock();

            if(isset($datos['soloStock']) && $datos['soloStock'] == 'true'){
                if($stock <= 0) { continue; }
            }

            $img = $prod->getProImagen();
            if($img == null || $img == "") {
                $img = "https://placehold.co/600x400?text=" . urlencode($prod->getProNombre());
            }

            $arregloSalida[] = [
                'idproducto' => $prod->getIdProducto(),
                'pronombre' => $prod->getProNombre(),
                'prodetalle' => $prod->getProDetalle(),
                'procantstock' => $stock,
                'precio' => $prod->getProPrecio(),
                'imagen' => $img
            ];
        }
        return $arregloSalida;
    }

    // --- MÉTODOS BASE ---

    public function alta($param){
        $resp = false;
        $elObjtTabla = $this->cargarObjeto($param);
        if ($elObjtTabla!=null and $elObjtTabla->insertar()){
            $resp = true;
        }
        return $resp;
    }

    public function baja($param){
        $resp = false;
        if ($this->eseteadosCamposClaves($param)){
            $elObjtTabla = new Producto();
            $elObjtTabla->setear($param['idproducto'], null, null, null, null, null);
            if ($elObjtTabla->eliminar()){
                $resp = true;
            }
        }
        return $resp;
    }

    public function modificacion($param){
        $resp = false;
        if ($this->eseteadosCamposClaves($param)){
            $elObjtTabla = $this->cargarObjeto($param);
            if($elObjtTabla != null and $elObjtTabla->modificar()){
                $resp = true;
            }
        }
        return $resp;
    }

    public function buscar($param){
        $where = " true ";
        if ($param<>NULL){
            if  (isset($param['idproducto']))
                $where.=" and idproducto =".$param['idproducto'];
            if  (isset($param['pronombre']))
                $where.=" and pronombre ='".$param['pronombre']."'";
        }
        $arreglo = Producto::listar($where);
        return $arreglo;
    }

    private function cargarObjeto($param){
        $obj = null;
        // CORREGIDO: Ahora buscamos 'proprecio' que es como viene de la vista
        if( array_key_exists('pronombre',$param) && array_key_exists('prodetalle',$param) && array_key_exists('procantstock',$param) && array_key_exists('proprecio',$param)){
            $obj = new Producto();
            
            $id = isset($param['idproducto']) ? $param['idproducto'] : null;
            $imagen = isset($param['proimagen']) ? $param['proimagen'] : '';
            
            $obj->setear(
                $id, 
                $param['pronombre'], 
                $param['prodetalle'], 
                $param['procantstock'], 
                $param['proprecio'], // Corregido aquí también
                $imagen 
            );
        }
        return $obj;
    }
    
    private function eseteadosCamposClaves($param){
        $resp = false;
        if (isset($param['idproducto']))
            $resp = true;
        return $resp;
    }
}
?>