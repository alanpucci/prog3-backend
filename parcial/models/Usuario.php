<?php

class Usuario
{
    public $id;
    public $mail;
    public $tipo;
    public $clave;

    public function crearUsuario(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO parcial_usuarios (mail, tipo, clave) VALUES (:mail, :tipo, :clave)");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':mail', $this->mail, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash, PDO::PARAM_STR);
        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();
    }

    public function validarUsuario(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT tipo, clave FROM parcial_usuarios WHERE mail=:mail");
        $consulta->bindValue(':mail', $this->mail);
        $consulta->execute();
        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);
        if(password_verify($this->clave, $usuario["clave"])){
            return $usuario["tipo"];
        }
    }

    public function obtenerPorCriptomoneda($criptomoneda){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT u.id, u.mail, u.tipo FROM parcial_ventaCriptomonedas v 
                                                        LEFT JOIN parcial_criptomonedas c ON v.criptomoneda_id = c.id
                                                        LEFT JOIN parcial_usuarios u ON v.vendedor_id = u.id
                                                        WHERE c.nombre=:criptomoneda GROUP BY u.id");
        $consulta->bindValue(':criptomoneda', $criptomoneda);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}