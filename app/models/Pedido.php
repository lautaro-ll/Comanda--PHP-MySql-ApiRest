<?php

class Pedido {
    public $id;
    public $cliente;
    public $foto;
    public $codigoPedido;
    public $idMesa;
    public $idProducto;
    public $precio;
    public $idMozo;
    public $idUsuario;
    public $estado;
    public $tiempoEstimado;
    public $tiempoFinalizado;
    public $tiempoEntregado;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (`cliente`, `foto`, `codigo-pedido`, `idmesa`, `idproducto`, `precio`, `idmozo`, `estado`) VALUES (:cliente, :foto, :codigoPedido, :idMesa, :idProducto, :precio, :idMozo, :estado)");
        $consulta->bindValue(':cliente', $this->cliente, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
        $consulta->bindValue(':codigoPedido', $this->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':idMozo', $this->idMozo, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPorCargo($cargo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidos.id, pedidos.cliente, pedidos.foto, pedidos.`codigo-pedido` AS codigoPedido, pedidos.idmesa, pedidos.idproducto, pedidos.precio, pedidos.idmozo, pedidos.idusuario, pedidos.estado, pedidos.`tiempo-pedido` AS tiempoPedido, pedidos.`tiempo-estimado` AS tiempoEstimado, pedidos.`tiempo-finalizado` AS tiempoFinalizado, pedidos.`tiempo-entregado` FROM pedidos INNER JOIN usuarios ON pedidos.idusuario=usuarios.id WHERE usuarios.cargo=:cargo");
        $consulta->bindValue(':cargo', $cargo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPorEstado($estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE estado=:estado");
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function cambiarEstado($id, $estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos SET estado=:estado WHERE id=:id");
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedido($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE id=:id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public function modificarPedido()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET (cliente=:cliente, foto=:foto, `codigo-pedido`=:codigoPedido, idmesa=:idMesa, idproducto=:idProducto, precio=:precio, idmozo=:idMozo, idusuario=:idUsuario, estado=:estado, `tiempo-estimado`=:tiempoEstimado, `tiempo-finalizado`=:tiempoFinalizado, `tiempo-entregado`=:tiempoEntregado) WHERE id=:id");
        $consulta->bindValue(':cliente', $this->cliente, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
        $consulta->bindValue(':codigoPedido', $this->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':idMozo', $this->idMozo, PDO::PARAM_INT);
        $consulta->bindValue(':idUsuario', $this->idUsuario, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoEstimado', $this->tiempoEstimado, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoFinalizado', $this->tiempoFinalizado, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoEntregado', $this->tiempoEntregado, PDO::PARAM_STR);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function borrarPedido($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM pedidos WHERE id=:id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }
}
