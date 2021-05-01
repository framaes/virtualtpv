<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Title</title>
    </head>
    <body>
<?php
$mysqli = new mysqli("localhost", "root", "", "itramit_pagos");

if ($mysqli->connect_errno) {
    printf("Falló la conexión: %s\n", $mysqli->connect_error);
    exit();
}

echo "<table border='1px solid black'><tr><th>PK_FACTURA</th><th>CANTIDAD</th><th>F_EMISION</th><th>DESCRIPCION</th><th>PAGOS</th></tr>";

$consulta = "SELECT * FROM facturas"; // where PK_FACTURA='3257563'

if ($resultado = $mysqli->query($consulta)) {

    while ($fila = $resultado->fetch_assoc()) {
        $pkfactura = $fila['PK_FACTURA'];
        $cantidad = $fila['I_TOTAL'];
        $fechaemision = $fila['F_EMISION'];
        $descripcion = $fila['T_OBSERVACIONES'];
        echo "<tr><td>$pkfactura</td><td>$cantidad</td><td>$fechaemision</td><td>$descripcion</td>";
        echo "<td><form action='transactionForm.php' method='post'><input type='hidden' id='idFactura' name='idFactura' value='$pkfactura'><input type='submit' value='Pagar'></form></td><tr>";
    }
}

echo "</table>";

/* cerrar la conexión */
$mysqli->close();

?>

    </body>
</html>

