<?php
class abmCompra {

    public function listarVentas(){
        $salida = [];
        $listaCompras = $this->buscar(null);
        
        $abmCE = new abmCompraEstado();
        $abmUsuario = new abmUsuario(); 

        foreach($listaCompras as $compra){
            // Buscar estado actual (el que tiene fecha fin null)
            $estados = $abmCE->buscar(['idcompra' => $compra->getIdCompra(), 'cefechafin' => 'null']);
            
            if(count($estados) > 0){
                $estadoActual = $estados[0];
                $tipo = $estadoActual->getIdCompraEstadoTipo();
                
                // Filtramos: Si es tipo 5 (Carrito) NO lo mostramos en la lista de ventas
                if($tipo != 5){
                    
                    $usuario = $abmUsuario->buscar(['idusuario' => $compra->getIdUsuario()]);
                    $nombreUser = (count($usuario) > 0) ? $usuario[0]->getUsNombre() : "Desconocido";
                    
                    // Mapeo de estados (esto podría venir de base de datos también)
                    $descEstado = "Desconocido";
                    switch($tipo){
                        case 1: $descEstado = "Iniciada"; break;
                        case 2: $descEstado = "Aceptada"; break;
                        case 3: $descEstado = "Enviada"; break;
                        case 4: $descEstado = "Cancelada"; break;
                    }

                    $salida[] = [
                        'idcompra' => $compra->getIdCompra(),
                        'usnombre' => $nombreUser,
                        'fecha' => $compra->getCoFecha(),
                        'idestado' => $tipo,
                        'estado_desc' => $descEstado
                    ];
                }
            }
        }
        return $salida;
    }


    public function buscarCarrito($idUsuario){
        $objCompra = null;
        $listaCompras = $this->buscar(['idusuario'=>$idUsuario]);
        foreach($listaCompras as $compra){
            $abmCE = new abmCompraEstado();
            $listaCE = $abmCE->buscar(['idcompra'=>$compra->getIdCompra(), 'cefechafin'=>'null']);
            if(count($listaCE) > 0){
                if($listaCE[0]->getIdCompraEstadoTipo() == 5){
                    $objCompra = $compra;
                    break;
                }
            }
        }
        return $objCompra;
    }

    public function iniciarCompra($idUsuario){
        $objC = new Compra();
        $objC->setear(null, date('Y-m-d H:i:s'), $idUsuario); 
        
        if($objC->insertar()){
            $idCompra = $objC->getIdCompra();
            $objCE = new CompraEstado();
            $objCE->setear(null, $idCompra, 5, date('Y-m-d H:i:s'), null);
            
            if($objCE->insertar()){
                return $objC;
            }
        }
        return null;
    }
    
    public function finalizarCompra($idUsuario){
        $respuesta = ['exito' => false, 'msg' => ''];
        
        $compraCarrito = $this->buscarCarrito($idUsuario);
        
        if($compraCarrito != null){
            $abmItem = new abmCompraItem(); 
            $itemsCarrito = $abmItem->buscar(['idcompra' => $compraCarrito->getIdCompra()]);
            
            if(count($itemsCarrito) == 0){
                return ['exito' => false, 'msg' => 'El carrito está vacío.'];
            }

            $stockOk = true;
            foreach($itemsCarrito as $item){
                $prod = $item->getObjProducto();
                if($prod->getProCantStock() < $item->getCiCantidad()){
                    $stockOk = false;
                    $respuesta['msg'] = "Stock insuficiente para: " . $prod->getProNombre();
                    break;
                }
            }

            if($stockOk){
                foreach($itemsCarrito as $item){
                    $prod = $item->getObjProducto();
                    $nuevoStock = $prod->getProCantStock() - $item->getCiCantidad();
                    $prod->setProCantStock($nuevoStock);
                    $prod->modificar(); 
                }

                $abmCE = new abmCompraEstado();
                $listaCE = $abmCE->buscar(['idcompra' => $compraCarrito->getIdCompra(), 'cefechafin' => 'null']);
                
                $exitoCierre = true;
                foreach($listaCE as $estado){
                     $estado->setCefechaFin(date('Y-m-d H:i:s'));
                     if(!$estado->modificar()){ 
                         $exitoCierre = false; 
                     }
                }
                
                if($exitoCierre){
                    $nuevoEstado = new CompraEstado();
                    $nuevoEstado->setear(null, $compraCarrito->getIdCompra(), 1, date('Y-m-d H:i:s'), null);
                    
                    if($nuevoEstado->insertar()){
                        $respuesta = ['exito' => true, 'msg' => 'Compra finalizada con éxito.'];
                    } else {
                        $respuesta = ['exito' => false, 'msg' => 'Error al crear el estado de compra.'];
                    }
                } else {
                    $respuesta = ['exito' => false, 'msg' => 'Error al cerrar el carrito.'];
                }

            } 
            
        } else {
            $respuesta = ['exito' => false, 'msg' => 'No se encontró el carrito activo.'];
        }

        return $respuesta;
    }

    public function agregarProductoAlCarrito($idUsuario, $idProducto, $cantidad){
        $respuesta = ['exito' => false, 'msg' => 'Error desconocido'];
        
        if($cantidad <= 0) {
            return ['exito' => false, 'msg' => 'La cantidad debe ser mayor a 0.'];
        }

        $objProducto = new Producto();
        $objProducto->setear($idProducto, null, null, null, null, null);
        if(!$objProducto->cargar()){
            return ['exito' => false, 'msg' => 'El producto no existe.'];
        }
        $stockActual = $objProducto->getProCantStock();

        $compra = $this->buscarCarrito($idUsuario);
        if($compra == null){
            $compra = $this->iniciarCompra($idUsuario);
            if($compra == null){
                return ['exito' => false, 'msg' => 'No se pudo crear el carrito.'];
            }
        }

        $abmItem = new abmCompraItem();
        $listaItems = $abmItem->buscar([
            'idcompra' => $compra->getIdCompra(), 
            'idproducto' => $idProducto
        ]);
        
        if(count($listaItems) > 0){
            $itemExistente = $listaItems[0];
            $cantidadNuevaTotal = $itemExistente->getCiCantidad() + $cantidad;
            
            if($cantidadNuevaTotal > $stockActual){
                return ['exito' => false, 'msg' => "No hay stock suficiente. Tienes {$itemExistente->getCiCantidad()} en carrito y el stock total es $stockActual."];
            }

            $paramModificacion = [
                'idcompraitem' => $itemExistente->getIdCompraItem(),
                'cicantidad' => $cantidadNuevaTotal
            ];
            if($abmItem->modificacion($paramModificacion)){
                $respuesta = ['exito' => true, 'msg' => 'Cantidad actualizada en el carrito.'];
            } else {
                $respuesta = ['exito' => false, 'msg' => 'Error al actualizar la cantidad.'];
            }

        } else {
            if($cantidad > $stockActual){
                return ['exito' => false, 'msg' => "Stock insuficiente. Solo quedan $stockActual unidades."];
            }

            $paramAlta = [
                'objProducto' => $objProducto,
                'objCompra' => $compra,
                'cicantidad' => $cantidad
            ];
            
            if($abmItem->alta($paramAlta)){
                $respuesta = ['exito' => true, 'msg' => 'Producto agregado al carrito.'];
            } else {
                $respuesta = ['exito' => false, 'msg' => 'Error al agregar el producto.'];
            }
        }

        return $respuesta;
    }

    public function quitarProducto($idCompraItem){
        $respuesta = ['exito' => false, 'msg' => 'Error desconocido'];
        $abmItem = new abmCompraItem();
        
        if($abmItem->baja(['idcompraitem' => $idCompraItem])){
             $respuesta = ['exito' => true, 'msg' => 'Producto eliminado del carrito.'];
        } else {
             $respuesta = ['exito' => false, 'msg' => 'No se pudo eliminar el producto.'];
        }
        return $respuesta;
    }

    public function vaciarCarrito($idUsuario){
        $respuesta = ['exito' => false, 'msg' => 'No se encontró carrito activo.'];
        $compra = $this->buscarCarrito($idUsuario);
        
        if($compra != null){
            $abmItem = new abmCompraItem();
            $listaItems = $abmItem->buscar(['idcompra' => $compra->getIdCompra()]);
            
            $exito = true;
            if(count($listaItems) > 0){
                foreach($listaItems as $item){
                    if(!$abmItem->baja(['idcompraitem' => $item->getIdCompraItem()])){
                        $exito = false;
                        break;
                    }
                }
            }
            
            if($exito){
                $respuesta = ['exito' => true, 'msg' => 'Carrito vaciado con éxito.'];
            } else {
                $respuesta = ['exito' => false, 'msg' => 'Error al vaciar algunos productos.'];
            }
        }
        
        return $respuesta;
    }

    public function buscar($param){
        $where = " true ";
        if ($param<>NULL){
            if  (isset($param['idcompra']))
                $where.=" and idcompra =".$param['idcompra'];
            if  (isset($param['idusuario']))
                $where.=" and idusuario =".$param['idusuario'];
        }
        $arreglo = Compra::listar($where);
        return $arreglo;
    }
    public function obtenerItemsDeCompra($idCompra){
        $resultado = [];
        $abmItem = new abmCompraItem();
        
        $listaItems = $abmItem->buscar(['idcompra' => $idCompra]);
        
        foreach($listaItems as $item){
            $prod = $item->getObjProducto();
            $resultado[] = [
                'pronombre' => $prod->getProDetalle(), // Usamos detalle como nombre visible
                'cantidad' => $item->getCiCantidad(),
                'precio' => $prod->getProPrecio(), 
                'total' => $item->getCiCantidad() * $prod->getProPrecio() 
            ];
        }
        return $resultado;
    }

    public function obtenerListadoCarrito($idUsuario){
        $resultado = [];
        
        // 1. Buscamos la compra que sea carrito (estado 5)
        $compra = $this->buscarCarrito($idUsuario);
        
        if($compra != null){
            $abmItem = new abmCompraItem();
            $listaItems = $abmItem->buscar(['idcompra' => $compra->getIdCompra()]);
            
            foreach($listaItems as $item){
                $prod = $item->getObjProducto();
                
                // Validamos imagen
                $img = $prod->getProImagen();
                if($img == null || $img == "") {
                    $img = "https://placehold.co/600x400?text=" . urlencode($prod->getProNombre());
                }

                $resultado[] = [
                    'idcompraitem' => $item->getIdCompraItem(),
                    'idproducto' => $prod->getIdProducto(),
                    'pronombre' => $prod->getProNombre(),
                    'prodetalle' => $prod->getProDetalle(),
                    'precio' => $prod->getProPrecio(),
                    'cantidad' => $item->getCiCantidad(),
                    'total' => $item->getCiCantidad() * $prod->getProPrecio(),
                    'imagen' => $img
                ];
            }
        }
        return $resultado;
    }
    public function obtenerDatosParaPDF($idCompra){
        $datos = null;
        
        // 1. Buscar la compra
        $listaCompras = $this->buscar(['idcompra' => $idCompra]);
        
        if(count($listaCompras) > 0){
            $objCompra = $listaCompras[0];
            
            // 2. Buscar Usuario
            $abmUsuario = new abmUsuario();
            $listaUsers = $abmUsuario->buscar(['idusuario' => $objCompra->getIdUsuario()]);
            $objUsuario = (count($listaUsers) > 0) ? $listaUsers[0] : null;
            
            // 3. Buscar Items y Calcular Totales
            $abmItem = new abmCompraItem();
            $listaItems = $abmItem->buscar(['idcompra' => $idCompra]);
            $itemsFormateados = [];
            $totalCompra = 0;
            
            foreach($listaItems as $item){
                $prod = $item->getObjProducto();
                $cantidad = $item->getCiCantidad();
                $precio = $prod->getProPrecio();
                $subtotal = $cantidad * $precio;
                $totalCompra += $subtotal;
                
                $itemsFormateados[] = [
                    'producto' => $prod->getProNombre(),
                    'detalle' => $prod->getProDetalle(),
                    'cantidad' => $cantidad,
                    'precio' => $precio,
                    'subtotal' => $subtotal
                ];
            }

            // 4. Buscar Estado Actual
            $abmCE = new abmCompraEstado();
            $listaEstados = $abmCE->buscar(['idcompra' => $idCompra, 'cefechafin' => 'null']);
            $estadoDesc = "Desconocido";
            if(count($listaEstados) > 0){
                // Aquí podrías hacer un switch según el tipo si quieres el nombre exacto
                // Por ahora devolvemos el ID o podrías buscar el objeto TipoEstado
                $estadoDesc = $listaEstados[0]->getIdCompraEstadoTipo(); 
            }

            $datos = [
                'idcompra' => $objCompra->getIdCompra(),
                'fecha' => $objCompra->getCoFecha(),
                'cliente_nombre' => ($objUsuario ? $objUsuario->getUsNombre() : 'Eliminado'),
                'cliente_mail' => ($objUsuario ? $objUsuario->getUsMail() : '-'),
                'estado' => $estadoDesc,
                'items' => $itemsFormateados,
                'total' => $totalCompra
            ];
        }
        return $datos;
    }

}

?>