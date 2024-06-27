<?php
header('Content-Type: application/json');
// Incluir el archivo de conexión
include 'conn.php';
//$jsondata = array();

try {
    // Consulta SQL para obtener todos los productos
    $sql = "SELECT tbadmproducto.codigo,tbadmproducto.descripcion,tbadmproducto.precio,tbadmproducto.preciomayor,
    ifnull((sum(tbadmalmexterno.stock) + sum(tbadmalmexterno.unds)),0) as stock 
    FROM tbadmproducto left join tbadmalmexterno ON tbadmproducto.codigo=tbadmalmexterno.codigo 
    where tbadmproducto.vista>0 
    group by tbadmproducto.codigo 
    order by tbadmproducto.descripcion";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $productos_disponibles = array();

    foreach ($results as $producto) {
        if ($producto['stock']>0) {
            $productos_disponibles[]=$producto;
        }
    }

    // Devolver los resultados en formato JSON
    echo json_encode($productos_disponibles);

} catch (PDOException $e) {
    // Manejar los errores de la consulta
    die("Error al obtener productos: " . $e->getMessage());
}

?>
