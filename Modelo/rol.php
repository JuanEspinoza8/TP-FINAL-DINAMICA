<?php
class Rol {
    private $idrol;
    private $rodescripcion;
    private $mensajeoperacion;

    public function __construct(){
        $this->idrol="";
        $this->rodescripcion="";
    }
    public function setear($idrol, $rodescripcion){
        $this->idrol = $idrol;
        $this->rodescripcion = $rodescripcion;
    }
    public function getIdRol(){ return $this->idrol; }
    public function getRoDescripcion(){ return $this->rodescripcion; }
    
    public function cargar(){
        $resp = false;
        $base=new BaseDatos();
        $sql="SELECT * FROM rol WHERE idrol = ".$this->idrol;
        if ($base->Iniciar()) {
            if($base->Ejecutar($sql)){
                if($row2=$base->Registro()){
                    $this->setear($row2['idrol'],$row2['rodescripcion']);
                    $resp= true;
                }
            }
        }
        return $resp;
    }

    public static function listar($parametro=""){
        $arreglo = array();
        $base=new BaseDatos();
        $sql="SELECT * FROM rol ";
        if ($parametro!="") { $sql.='WHERE '.$parametro; }
        if ($base->Iniciar()) {
            if ($base->Ejecutar($sql)) {
                while ($row2=$base->Registro()) {
                    $obj=new Rol();
                    $obj->setear($row2['idrol'],$row2['rodescripcion']);
                    array_push($arreglo, $obj);
                }
            }
        }
        return $arreglo;
    }
}
?>