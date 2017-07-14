<?php

//echo 'hola 1';

switch ($_GET['f']) {
    case 'prueba':
        prueba();
        break;

    case 'insertarAutorizacion':
        insertarAutorizacion();
        break;
        continue;
}

function createConnection($dbengine, $host, $port, $dbname, $user, $pass) {
    $conn = NULL;
    @define("DBENGINE", $dbengine);
    if ($dbengine === "MYSQL") {
        @$conn = mysql_connect($host . $port, $user, $pass);
        mysql_select_db($dbname, $conn);

        executeQuery("SET NAMES 'utf8';", $conn);
        executeUpdate("SET GLOBAL time_zone = '-05:00';", $conn);
        executeUpdate("SET time_zone = '-05:00';", $conn);
        executeUpdate("SET @@session.time_zone = '-5:00';", $conn);
    } else if ($dbengine === "MYSQLI") {
        $conn = mysql_connect($host . $port, $user, $pass);
        mysqli_select_db($conn, $dbname);

        executeQuery("SET NAMES 'utf8';", $conn);
        executeUpdate("SET GLOBAL time_zone = '-05:00';", $conn);
        executeUpdate("SET time_zone = '-05:00';", $conn);
        executeUpdate("SET @@session.time_zone = '-5:00';", $conn);
    } else if ($dbengine === "MYSQL-PDO") {
        $conn = new PDO("mysql:host=" . $host . ";dbname=" . $dbname . ";charset=utf8", $user, $pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"));
    } else if ($dbengine === "POSTGRESQL") {
        $conn = pg_connect("host=" . $host . " port=" . $port . " dbname=" . $dbname
                . " user=" . $user . " password=" . $pass);
    }
    return $conn;
}

function conexionSucursal($sucursal) {

    if ($sucursal === '4') {
        $hostB = "127.0.0.1";
        $portB = ":3306";
        $userB = "root";
        $passB = "";
        $dbnameB = "centroncolbta";
        $fechaActual = date('Y-m-d');
        $conn = mysql_connect($hostB . $portB, $userB, $passB);
        mysql_select_db($dbnameB, $conn);
        return $conn;
    }
    if ($sucursal === '3') {
        $hostB = "127.0.0.1";
        $portB = ":3306";
        $userB = "root";
        $passB = "";
        $dbnameB = "centroncolbta";
        $fechaActual = date('Y-m-d');
        $conn = mysql_connect($hostB . $portB, $userB, $passB);
        mysql_select_db($dbnameB, $conn);
        return $conn;
    }
    if ($sucursal === '8') {
        $hostB = "127.0.0.1";
        $portB = ":3306";
        $userB = "root";
        $passB = "";
        $dbnameB = "centroncolbta";
        $fechaActual = date('Y-m-d');
        $conn = mysql_connect($hostB . $portB, $userB, $passB);
        mysql_select_db($dbnameB, $conn);
        return $conn;
    }
    if ($sucursal === '6') {
        $hostB = "127.0.0.1";
        $portB = ":3306";
        $userB = "root";
        $passB = "";
        $dbnameB = "centroncolbta";
        $fechaActual = date('Y-m-d');
        $conn = mysql_connect($hostB . $portB, $userB, $passB);
        mysql_select_db($dbnameB, $conn);
        return $conn;
    }
    if ($sucursal === '9') {
        $hostB = "127.0.0.1";
        $portB = ":3306";
        $userB = "root";
        $passB = "";
        $dbnameB = "centroncolbta";
        $fechaActual = date('Y-m-d');
        $conn = mysql_connect($hostB . $portB, $userB, $passB);
        mysql_select_db($dbnameB, $conn);
        return $conn;
    }
   
}

function insertarAutorizacion() {

    $cantidad = $_GET['cantidad'];
    $documento = $_GET['documento'];
    $diagnostico = $_GET['diagnostico'];
    $ambulatorio = $_GET['ambulatorio'];
    $contrato = $_GET['contrato'];
    $sucursal = $_GET['sucursal'];
    $medico = $_GET['medico'];
    $cups = $_GET['cups'];
    $nombProd = $_GET['nombreProd'];
    $tarifario = $_GET['tarifario'];
    $fechaActual = date('Y-m-d');
    $conn = conexionSucursal($sucursal);

//    $hostB = "127.0.0.1";
//    $portB = ":3306";
//    $userB = "root";
//    $passB = "";
//    $dbnameB = "centroncolbta";
    
//    $conn = mysql_connect($hostB . $portB, $userB, $passB);
//    mysql_select_db($dbnameB, $conn);

    $selectProducto = mysql_query("SELECT codigo, valor, tiporips, tiposer, nombre FROM valores WHERE codprod=" . $cups . " and nombre LIKE '%" . $nombProd . "%' and tarifario=" . $tarifario, $conn);
    while ($fila = mysql_fetch_assoc($selectProducto)) {
        $idproducto = $fila["codigo"];
        $valor = $fila["valor"];
        $tiporips = $fila["tiporips"];
        $tiposer = $fila["tiposer"];
        $nombreProd = $fila["nombre"];
    }

    $selectCodAuto = mysql_query("select * from configuracion", $conn);
    while ($fila = mysql_fetch_assoc($selectCodAuto)) {
        $codigo_auto = $fila["autoriza"];
    }
    $updateNum = mysql_query("update configuracion set autoriza=" . ($codigo_auto + 1) . " where autoriza='" . $codigo_auto . "'", $conn);


    $selectPaciente = mysql_query("select * from pacientes where documento=" . $documento, $conn);
    while ($fila = mysql_fetch_assoc($selectPaciente)) {
        $paciente = $fila["codigo"];
    }

    $selectMedico = mysql_query("select * from medicos where documento=" . $medico, $conn);
    while ($fila = mysql_fetch_assoc($selectMedico)) {
        $medOrdena = $fila["codigo"];
    }

    //se selecciona el proveedor, si el paciente le habian autorizado antes dicho producto
    $selectProveedor1 = mysql_query("select f.proveedor from facturaauto f join detalleauto d on d.codigoventa=f.codigo and d.idprod=" . $idproducto . " and f.cliente=" . $paciente, $conn);

    while ($fila = mysql_fetch_assoc($selectProveedor1)) {
        $proveedor1 = $fila["proveedor"];
    }

    if (isset($proveedor1)) {
        $proveedor = $proveedor1;
    } else {
        //se selecciona el proveedor dependiendo de el contrato 
        $selectProveedor2 = mysql_query("select a.proveedor from facturaauto a join detalleauto d on d.codigoventa=a.codigo join contratos c on a.contrato=c.codigo 
                                         where d.idprod=" . $idproducto . " and c.codigo=" . $contrato . " and a.valor>0 order by a.valor asc limit 1", $conn);
        while ($fila = mysql_fetch_assoc($selectProveedor2)) {
            $proveedor = $fila["proveedor"];
        }
    }

    $res = mysql_query("INSERT INTO facturaauto (codigo,fecha, valor, cantidad, contado, credito, pagado,cambio, descuento, cliente, "
            . "proveedor, comentario, cajero, anulada, contrato, autorizacion, qanula, manula, observaciones, diasauto, diagnostico, "
            . "medico_ordena, estado, sucursal, ambulatorio, capitada, facturada, num_factura)"
            . " VALUES (" . $codigo_auto . ",'" . $fechaActual . "', " . $valor . ", " . $cantidad . ", 0, 0, 0, 0, 0, " . $paciente . ","
            . " " . $proveedor . ", '', 'HCE3', 0, " . $contrato . ", " . $codigo_auto . ", '', '', '', 0, '" . $diagnostico . "', "
            . "" . $medOrdena . " ,0, " . $sucursal . ", " . $ambulatorio . ", 0, 0, 0);", $conn);


    if ($res == 1) {
        insertarDetalle($conn, $codigo_auto, $cups, $cantidad, $fechaActual, $nombreProd, $idproducto, $tiporips, $valor, $tiposer, $documento);
//        mostrarEncabezado($conn);
    } else {
        echo ' NO INSERTO ' . $res;
        print mysql_error();
    }
}

function insertarDetalle($conn, $codigo_auto, $cups, $cantidad, $fechaActual, $nombreProd, $idproducto, $tiporips, $valor, $tiposer, $documento) {

    $res = mysql_query("INSERT INTO detalleauto (codigoventa, codigo, cantidad, preciocompra,precioventa, "
            . "fecha, nombreprod, idprod, tiporips, tiposer) VALUES (" . $codigo_auto . ", '" . $cups . "', " . $cantidad . ", 0, " . $valor . ","
            . " '" . $fechaActual . "', '" . $nombreProd . "', " . $idproducto . ", " . $tiporips . ", " . $tiposer . ");", $conn);


    if ($res == 1) {
        mostrarAutorizacion($conn, $documento);
    } else {
        echo ' NO INSERTO ' . $res;
        print mysql_error();
    }
}

function mostrarAutorizacion($conn, $documento) {

    $res = mysql_query("select CONCAT(p.pnombre,' ',p.snombre,' ',p.papellido,' ',p.sapellido) as nombrePac, p.documento, e.nombre as eps, diag.numero as diagnostico, c.nombre as ciudadPac,
                    prv.nombre as proveedor, prv.telefono, prv.direccion, prv.ciudad as ciudadPrv, case a.ambulatorio when 1 then 'AMBULATORIO' when 0 then 'HOSPITALARIO' end as servicio,
                    d.codigo as codProd, d.nombreprod, d.cantidad, a.codigo as codAutorizacion, a.observaciones, a.cajero, con.nombre as contrato
                     from pacientes p join facturaauto a on a.cliente=p.codigo 
                     join epss e on e.codigo=p.eps
                     join diagnosticos diag on diag.numero=a.diagnostico
                     join ciudades c on c.cod_muni=p.ciudad
                     join departamentos dep on dep.numero=c.cod_dpto
                     join proveedores prv on prv.codigo=a.proveedor
                     join detalleauto d on d.codigoventa=a.codigo
                     join contratos con on con.codigo=a.contrato
                     where dep.numero=p.departamento and p.documento=" . $documento . " order by a.codigo desc limit 1", $conn);

    $resultado = mysql_fetch_assoc($res);
//    echo 'Autorizacion';
    print_r($resultado);
}

?>