<?php
// Configuración de la conexión a la base de datos
$servername = '192.168.0.101';
$port = '3306';
$database = 'dbnegocio';
$username = 'usrContingencia';
$password = 'R0r4y2023.$';

// Intentar conectarse a la base de datos
try {
    $pdo = new PDO("mysql:host=$servername;port=$port;dbname=$database;charset=utf8mb4", $username, $password);
    // Establecer el modo de error PDO a excepción
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Conexión exitosa a la base de datos";
} catch (PDOException $e) {
    die("La conexión a la base de datos falló: " . $e->getMessage());
}
?>
