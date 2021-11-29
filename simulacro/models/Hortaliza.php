<?php

class Hortaliza
{
    public $id;
    public $precio;
    public $nombre;
    public $foto;
    public $clima;
    public $tipoUnidad;
    public $estado;

    public function crearHortaliza(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO hortalizas (precio, nombre, foto, clima, tipoUnidad) 
                                                        VALUES (:precio, :nombre, :foto, :clima, :tipoUnidad)");
        $consulta->bindValue(':precio', $this->precio);
        $consulta->bindValue(':nombre', $this->nombre);
        $consulta->bindValue(':foto', $this->foto["name"]);
        $consulta->bindValue(':clima', $this->clima);
        $consulta->bindValue(':tipoUnidad', $this->tipoUnidad);
        $consulta->execute();
        $ultimoId = $objAccesoDatos->obtenerUltimoId();
        $this->subirImagen($this->foto, $ultimoId);
        return $ultimoId;
    }

    public function obtenerTodos($parametro, $valor){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM hortalizas WHERE ".$parametro." =:valor");
        $consulta->bindValue(':valor', $valor);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Hortaliza');
    }

    public function subirImagen($foto, $idHortaliza){
        if(!file_exists("ImagenesHortalizas/")){
            mkdir("ImagenesHortalizas/",0777,true);
        }
        $nombre = $foto["name"];
        $destino = "ImagenesHortalizas/".$idHortaliza.".".$nombre;
        move_uploaded_file($foto["tmp_name"], $destino);
    }

    public function obtenerUna($id){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM hortalizas WHERE id=:id");
        $consulta->bindValue(':id', $id);
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public static function borrarUna($id){
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE hortalizas SET estado='inactivo' WHERE id = :id");
        $consulta->bindValue(':id', $id);
        $consulta->execute();
    }

    public function modificarHortaliza(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE hortalizas SET precio=:precio, nombre=:nombre, foto=:foto, clima=:clima, tipoUnidad=:tipoUnidad, estado=:estado WHERE id=:id");
        if(gettype($this->foto) == 'array'){
            if($this->existeImagen($this)){
                $this->backupImagen($this->foto, $this->id);
            }else{
                $this->subirImagen($this->foto, $this->id);
            }
        }
        $consulta->bindValue(':foto', $this->foto);
        $consulta->bindValue(':id', $this->id);
        $consulta->bindValue(':precio', $this->precio);
        $consulta->bindValue(':nombre', $this->nombre);
        $consulta->bindValue(':clima', $this->clima);
        $consulta->bindValue(':tipoUnidad', $this->tipoUnidad);
        $consulta->bindValue(':estado', $this->estado);
        $consulta->execute();
    }

    public function existeImagen($hortaliza){
        return file_exists("ImagenesHortalizas/".$hortaliza->id.".".$hortaliza->foto["name"]);
    }

    public function backupImagen($foto, $idHortaliza){
        if(!file_exists("ImagenesHortalizas/Backup")){
            mkdir("ImagenesHortalizas/Backup",0777,true);
        }
        $destino = "ImagenesHortalizas/Backup/".$idHortaliza.".".$foto["name"];
        move_uploaded_file($foto["tmp_name"], $destino);
    }

}