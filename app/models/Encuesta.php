<?php

class Encuesta {
    public $id;
    public $codigoMesa;
    public $codigoPedido;
    public $mesa;
    public $resto;
    public $mozo;
    public $cocinero;
    public $experiencia;

    public function crearEncuesta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO `encuesta`(`codigo-mesa`, `codigo-pedido`, `calif-mesa`, `calif-resto`, `calif-mozo`, `calif-cocinero`, `experiencia`) VALUES (:codigoMesa, :codigoPedido, :mesa, :resto, :mozo, :cocinero, :experiencia)");

        $consulta->bindValue(':codigoMesa', $this->codigoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':codigoPedido', $this->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':mesa', $this->mesa, PDO::PARAM_INT);
        $consulta->bindValue(':resto', $this->resto, PDO::PARAM_INT);
        $consulta->bindValue(':mozo', $this->mozo, PDO::PARAM_INT);
        $consulta->bindValue(':cocinero', $this->cocinero, PDO::PARAM_INT);
        $consulta->bindValue(':experiencia', $this->experiencia, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function borrarEncuesta($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM encuesta WHERE id=:id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function obtenerEncuesta($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encuesta WHERE id=:id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encuesta");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }

    public function modificarEncuesta()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET (`codigo-mesa`=:codigoMesa, `codigo-pedido`=:codigoPedido, `calif-mesa`=:mesa, `calif-resto`=:resto, `calif-mozo`=:mozo, `calif-cocinero`=:cocinero, `experiencia`=:experiencia) WHERE id=:id");
        $consulta->bindValue(':codigoMesa', $this->cliente, PDO::PARAM_STR);
        $consulta->bindValue(':codigoPedido', $this->foto, PDO::PARAM_STR);
        $consulta->bindValue(':mesa', $this->codigoPedido, PDO::PARAM_INT);
        $consulta->bindValue(':resto', $this->idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':mozo', $this->idProducto, PDO::PARAM_INT);
        $consulta->bindValue(':cocinero', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':experiencia', $this->idMozo, PDO::PARAM_STR);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
    }

}
?>