<?php
$promociones = [
    [
        'imagen' => 'promo/8820.png' // PROMOCION DE CHAO x2 MORAZUL
    ],
    
    [
        'imagen' => 'promo/8832.png' // PROMOCION DE MENTA HELADA
    ],

    // Agrega más promociones aquí
];

// Devolver las promociones como JSON
header('Content-Type: application/json');
echo json_encode($promociones);
?>
