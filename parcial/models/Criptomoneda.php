<?php

class Criptomoneda
{
    public $id;
    public $precio;
    public $nombre;
    public $foto;
    public $nacionalidad;
    public $estado;

    public function crearCriptomoneda(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO parcial_criptomonedas (precio, nombre, foto, nacionalidad, estado) 
                                                        VALUES (:precio, :nombre, :foto, :nacionalidad, :estado)");
        $consulta->bindValue(':precio', $this->precio);
        $consulta->bindValue(':nombre', $this->nombre);
        $consulta->bindValue(':foto', $this->foto["name"]);
        $consulta->bindValue(':nacionalidad', $this->nacionalidad);
        $consulta->bindValue(':estado', 'activo');
        $consulta->execute();
        $ultimoId = $objAccesoDatos->obtenerUltimoId();
        $this->subirImagen($this->foto, $ultimoId);
        return $ultimoId;
    }

    public function obtenerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM parcial_criptomonedas WHERE estado=:estado");
        $consulta->bindValue(':estado', 'activo');
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Criptomoneda');
    }

    public function obtenerPorNacionalidad($nacionalidad){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM parcial_criptomonedas where nacionalidad=:nacionalidad AND estado=:estado");
        $consulta->bindValue(':nacionalidad', $nacionalidad);
        $consulta->bindValue(':estado', 'activo');
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Criptomoneda');
    }

    public function obtenerUna($id){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM parcial_criptomonedas where id=:id");
        $consulta->bindValue(':id', $id);
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public static function borrarUna($id){
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE parcial_criptomonedas SET estado=:estado WHERE id = :id");
        $consulta->bindValue(':id', $id);
        $consulta->bindValue(':estado', 'inactivo');
        $consulta->execute();
    }

    public function modificarCriptomoneda(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE parcial_criptomonedas SET precio=:precio, nombre=:nombre, foto=:foto, nacionalidad=:nacionalidad, estado=:estado WHERE id=:id");
        if(gettype($this->foto) == 'array'){
            if($this->existeImagen($this)){
                $this->backupImagen($this->foto, $this->id);
            }else{
                $this->subirImagen($this->foto, $this->id);
            }
            $consulta->bindValue(':foto', $this->foto["name"]);
        }else{
            $consulta->bindValue(':foto', $this->foto);
        }
        $consulta->bindValue(':id', $this->id);
        $consulta->bindValue(':precio', $this->precio);
        $consulta->bindValue(':nombre', $this->nombre);
        $consulta->bindValue(':nacionalidad', $this->nacionalidad);
        $consulta->bindValue(':estado', $this->estado);
        $consulta->execute();
    }

    public function existeImagen($criptomoneda){
        return file_exists("Cripto/".$criptomoneda->id.".".$criptomoneda->foto["name"]);
    }

    public function backupImagen($foto, $idCripto){
        if(!file_exists("Cripto/Backup")){
            mkdir("Cripto/Backup",0777,true);
        }
        $destino = "Cripto/Backup/".$idCripto.".".$foto["name"];
        move_uploaded_file($foto["tmp_name"], $destino);
    }


    public function subirImagen($foto, $idCripto){
        if(!file_exists("Cripto/")){
            mkdir("Cripto/",0777,true);
        }
        $nombre = $foto["name"];
        $destino = "Cripto/".$idCripto.".".$nombre;
        move_uploaded_file($foto["tmp_name"], $destino);
    }
}