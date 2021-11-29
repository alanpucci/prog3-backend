<?php

class Venta
{
    public $id;
    public $fecha;
    public $cantidad;
    public $tipoUnidad;
    public $foto;
    public $vendedor;

    public function crearVenta(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta_usuario = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios where mail=:mail");
        $consulta_usuario->bindValue(':mail', $this->vendedor);
        $consulta_usuario->execute();
        $vendedor = $consulta_usuario->fetch(PDO::FETCH_ASSOC);
        $consulta_hortaliza = $objAccesoDatos->prepararConsulta("SELECT * FROM hortalizas where nombre=:nombre");
        $consulta_hortaliza->bindValue(':nombre', $this->hortaliza);
        $consulta_hortaliza->execute();
        $hortaliza = $consulta_hortaliza->fetch(PDO::FETCH_ASSOC);
        if($hortaliza && $hortaliza["id"]){
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO ventaHortaliza (fecha, cantidad, hortaliza_id, foto, vendedor_id) 
                                                            VALUES (:fecha, :cantidad, :hortaliza_id, :foto, :vendedor)");
            $date = new DateTime("now");
            $fechaCreacion = $date->format('Y-m-d H:i:s');
            $this->fecha = $fechaCreacion;
            $nombreFoto = $this->subirImagen($this);
            $consulta->bindValue(':fecha',$this->fecha);
            $consulta->bindValue(':cantidad', $this->cantidad);
            $consulta->bindValue(':hortaliza_id', $hortaliza["id"]);
            $consulta->bindValue(':foto', $nombreFoto);
            $consulta->bindValue(':vendedor', $vendedor["id"]);
            $consulta->execute();
            $ultimoId = $objAccesoDatos->obtenerUltimoId();
            return $ultimoId;
        }else{
            throw new Exception("Hortaliza no existente");
            
        }
    }

    public function obtenerTodosEntreFechas(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ventaHortaliza v 
                                                        LEFT JOIN hortalizas h ON v.hortaliza_id = h.id
                                                        WHERE clima=:clima AND CAST(fecha as DATE) BETWEEN :fecha1 AND :fecha2");
        $consulta->bindValue(':clima', "seco");
        $consulta->bindValue(':fecha1', "2021-06-10");
        $consulta->bindValue(':fecha2', "2021-06-13");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Venta');
    }

    public static function obtenerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT v.fecha, v.cantidad, u.mail, h.nombre, h.precio, h.clima, h.tipoUnidad, v.foto FROM ventaHortaliza v 
                                                        LEFT JOIN hortalizas h ON v.hortaliza_id = h.id
                                                        LEFT JOIN usuarios u ON v.vendedor_id = u.id");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function subirImagen($venta){
        if(!file_exists("FotosHortalizas/")){
            mkdir("FotosHortalizas/",0777,true);
        }
        $extension = explode(".",$venta->foto["name"])[1];
        $fechaFormateada = explode(" ",$venta->fecha)[0];
        $vendedor = explode("@",$venta->vendedor)[0];
        $nombreArchivo = $venta->hortaliza."+".$fechaFormateada."+".$vendedor.".".$extension;
        $destino = "FotosHortalizas/".$nombreArchivo;
        move_uploaded_file($venta->foto["tmp_name"], $destino);
        return $nombreArchivo;
    }

}