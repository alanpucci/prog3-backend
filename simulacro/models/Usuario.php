<?php

class Usuario
{
    public $id;
    public $mail;
    public $tipo;
    public $clave;

    public function crearUsuario(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (mail, tipo, clave) VALUES (:mail, :tipo, :clave)");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':mail', $this->mail, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash, PDO::PARAM_STR);
        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();
    }

    public function validarUsuario(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT tipo, clave FROM usuarios WHERE mail=:mail");
        $consulta->bindValue(':mail', $this->mail);
        $consulta->execute();
        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);
        if(password_verify($this->clave, $usuario["clave"])){
            return $usuario["tipo"];
        }
    }

    public function obtenerTodos($parametro){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT u.mail, u.tipo FROM usuarios u
                                                        LEFT JOIN ventaHortaliza v ON v.vendedor_id=u.id
                                                        LEFT JOIN hortalizas h ON v.hortaliza_id=h.id
                                                        WHERE h.nombre =:nombre_hortaliza");
        $consulta->bindValue(':nombre_hortaliza', $parametro);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}