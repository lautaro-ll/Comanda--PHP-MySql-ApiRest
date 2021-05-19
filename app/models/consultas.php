<?php

/*
LEMOS LAUTARO LUCAS

Funciones de filtrado:

A. Obtener los detalles completos de todos los usuarios y poder ordenarlos alfabéticamente de forma ascendente o descendente.
SELECT * FROM usuario ORDER BY apellido, nombre ASC
B. Obtener los detalles completos de todos los productos y poder ordenarlos alfabéticamente de forma ascendente y descendente.
SELECT * FROM producto ORDER BY nombre ASC
C. Obtener todas las compras filtradas entre dos cantidades.
SELECT * FROM `venta` WHERE cantidad BETWEEN 2 AND 5
D. Obtener la cantidad total de todos los productos vendidos entre dos fechas.
SELECT cantidad FROM `venta` WHERE fecha_de_venta BETWEEN "2020-01-01" AND "2021-01-01"
E. Mostrar los primeros “N” números de productos que se han enviado.
SELECT * FROM `venta` LIMIT 5
F. Mostrar los nombres del usuario y los nombres de los productos de cada venta.
SELECT producto.nombre,usuario.nombre,usuario.apellido FROM venta INNER JOIN producto ON producto.id=venta.id_producto INNER JOIN usuario ON usuario.id=venta.id_usuario

G. Indicar el monto (cantidad * precio) por cada una de las ventas.
SELECT producto.precio*venta.cantidad FROM venta INNER JOIN producto ON venta.id_producto=producto.id
H. Obtener la cantidad total de un producto (ejemplo:1003) vendido por un usuario (ejemplo: 104).
SELECT SUM(cantidad) FROM venta WHERE id_producto=1003 AND id_usuario=104
I. Obtener todos los números de los productos vendidos por algún usuario filtrado por localidad (ejemplo: ‘Avellaneda’).
SELECT producto.codigo_de_barra,usuario.localidad FROM venta INNER JOIN producto ON venta.id_producto=producto.id INNER JOIN usuario ON venta.id_usuario=usuario.id WHERE usuario.localidad='Avellaneda'; 
J. Obtener los datos completos de los usuarios filtrando por letras en su nombre o apellido.
SELECT * FROM usuario WHERE nombre LIKE '%o%' AND apellido LIKE '%a%';
K. Mostrar las ventas entre dos fechas del año.
SELECT * from `venta` WHERE fecha_de_venta BETWEEN "2020-01-01" AND "2021-01-01"

*/
function obtenerProductosYUsuariosPorVenta()
{
    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
    $consulta =$objetoAccesoDato->RetornarConsulta("SELECT producto.nombre,usuario.nombre,usuario.apellido FROM venta INNER JOIN producto ON producto.id=venta.id_producto INNER JOIN usuario ON usuario.id=venta.id_usuario");
    $consulta->execute();
    $array = $consulta->fetchAll();	//REVISAR
    return $array;
}
function obtenerPrimerosProductos($cantidad)
{
    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
    $consulta =$objetoAccesoDato->RetornarConsulta("SELECT * FROM `venta` LIMIT :c");
    $consulta->bindValue(':c',$cantidad, PDO::PARAM_INT);
    $consulta->execute();
    $array = $consulta->fetchAll(PDO::FETCH_CLASS, "Venta");	
    return $array;
}
function obtenerComprasEntreFechas($fechaUno,$fechaDos)
{
    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
    $consulta =$objetoAccesoDato->RetornarConsulta("SELECT * FROM `venta` WHERE fecha_de_venta BETWEEN :f1 AND :f2");
    $consulta->bindValue(':f1',$fechaUno, PDO::PARAM_STR);
    $consulta->bindValue(':f2',$fechaDos, PDO::PARAM_STR);
    $consulta->execute();
    $array = $consulta->fetchAll(PDO::FETCH_CLASS, "Venta");	
    return $array;
}
function obtenerComprasEntreCantidades($cantidadUno,$catidadDos)
{
    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
    $consulta =$objetoAccesoDato->RetornarConsulta("SELECT * FROM `venta` WHERE cantidad BETWEEN :c1 AND :c2");
    $consulta->bindValue(':c1',$cantidadUno, PDO::PARAM_INT);
    $consulta->bindValue(':c2',$catidadDos, PDO::PARAM_INT);
    $consulta->execute();
    $array = $consulta->fetchAll(PDO::FETCH_CLASS, "Venta");	
    return $array;
}
function obtenerProductoOrdenado($orden)
{
    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
    if($orden == 1) {
        $consulta =$objetoAccesoDato->RetornarConsulta("SELECT * FROM producto ORDER BY nombre ASC");
    }
    if($orden == -1) {
        $consulta =$objetoAccesoDato->RetornarConsulta("SELECT * FROM producto ORDER BY nombre DES");
    }
    $consulta->execute();
    $array = $consulta->fetchAll(PDO::FETCH_CLASS, "Producto");	
    return $array;
}
function obtenerUsuarioOrdenado($orden)
{
    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
    if($orden == 1) {
        $consulta =$objetoAccesoDato->RetornarConsulta("SELECT * FROM usuario ORDER BY apellido, nombre ASC");
    }
    if($orden == -1) {
        $consulta =$objetoAccesoDato->RetornarConsulta("SELECT * FROM usuario ORDER BY apellido, nombre DES");
    }
    $consulta->execute();
    $array = $consulta->fetchAll(PDO::FETCH_CLASS, "Usuario");	
    return $array;
}


require "Usuario.php";
require "Producto.php";
require "Ventas.php";

echo "Funciones de filtrado<br/>";

if(isset($_GET["consulta"]))
{
    $consulta = $_GET["consulta"];
    switch($consulta)
    {
        case "A":
            break;
        case "B":
            break;
        case "C":
            break;
        case "D":
            break;
        case "E":
            break;
        case "F":
            break;
        case "G":
            break;
        case "H":
            break;
        case "I":
            break;
        case "J":
            break;
        case "K":
            break;
        default:
            echo "Consulta incorrecta. <br/>";
            break;
    }
}
else
{
    echo "No se ingresó consulta. <br/>";
}


?>