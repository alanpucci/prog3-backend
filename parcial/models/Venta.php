<?php

class Venta
{
    public $id;
    public $fecha;
    public $cantidad;
    public $foto;
    public $usuario;

    public function crearVenta(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta_usuario = $objAccesoDatos->prepararConsulta("SELECT * FROM parcial_usuarios where mail=:mail");
        $consulta_usuario->bindValue(':mail', $this->vendedor);
        $consulta_usuario->execute();
        $vendedor = $consulta_usuario->fetch(PDO::FETCH_ASSOC);
        if($vendedor && $vendedor["id"]){
            $consulta_criptomoneda = $objAccesoDatos->prepararConsulta("SELECT * FROM parcial_criptomonedas where nombre=:nombre");
            $consulta_criptomoneda->bindValue(':nombre', $this->criptomoneda);
            $consulta_criptomoneda->execute();
            $criptomoneda = $consulta_criptomoneda->fetch(PDO::FETCH_ASSOC);
            if($criptomoneda && $criptomoneda["id"]){
                $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO parcial_ventaCriptomonedas (fecha, cantidad, criptomoneda_id, foto, vendedor_id) 
                                                                VALUES (:fecha, :cantidad, :criptomoneda_id, :foto, :vendedor)");
                $date = new DateTime("now");
                $fechaCreacion = $date->format('Y-m-d H:i:s');
                $this->fecha = $fechaCreacion;
                $nombreFoto = $this->subirImagen($this);
                $consulta->bindValue(':fecha',$this->fecha);
                $consulta->bindValue(':cantidad', $this->cantidad);
                $consulta->bindValue(':criptomoneda_id', $criptomoneda["id"]);
                $consulta->bindValue(':foto', $nombreFoto);
                $consulta->bindValue(':vendedor', $vendedor["id"]);
                $consulta->execute();
                $ultimoId = $objAccesoDatos->obtenerUltimoId();
                return $ultimoId;
            }else{
                throw new Exception("Criptomoneda no existente");
            }
        }else{
            throw new Exception("Usuario no existente");
        }
    }

    public function obtenerTodosEntreFechas(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT v.id, v.fecha, v.cantidad, v.foto, c.precio, c.nombre as criptomoneda, c.nacionalidad, u.mail, u.tipo FROM parcial_ventaCriptomonedas v 
                                                        LEFT JOIN parcial_criptomonedas c ON v.criptomoneda_id = c.id
                                                        LEFT JOIN parcial_usuarios u ON v.vendedor_id = u.id
                                                        WHERE nacionalidad=:nacionalidad AND CAST(fecha as DATE) BETWEEN :fechaDesde AND :fechaHasta");
        $consulta->bindValue(':nacionalidad', "Alemana");
        $consulta->bindValue(':fechaDesde', "2021-06-10");
        $consulta->bindValue(':fechaHasta', "2021-06-13");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT v.id, v.fecha, v.cantidad, v.foto, c.precio, c.nombre as criptomoneda, c.nacionalidad, u.mail, u.tipo FROM parcial_ventaCriptomonedas v 
                                                        LEFT JOIN parcial_criptomonedas c ON v.criptomoneda_id = c.id
                                                        LEFT JOIN parcial_usuarios u ON v.vendedor_id = u.id");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function subirImagen($venta){
        if(!file_exists("FotosCripto/")){
            mkdir("FotosCripto/",0777,true);
        }
        $extension = explode(".",$venta->foto["name"])[1];
        $fechaFormateada = explode(" ",$venta->fecha)[0];
        $vendedor = explode("@",$venta->vendedor)[0];
        $nombreArchivo = $venta->criptomoneda."+".$vendedor."+".$fechaFormateada.".".$extension;
        $destino = "FotosCripto/".$nombreArchivo;
        move_uploaded_file($venta->foto["tmp_name"], $destino);
        return $nombreArchivo;
    }

}