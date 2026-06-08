<?php
require_once("../config/conexion.php");

$id = $_GET['id'];

// 1. OBTENER DATOS DE LA OBRA
$sql = $conexion->prepare("
SELECT *
FROM obras
WHERE id_obra=?
");
$sql->execute([$id]);
$obra = $sql->fetch();

// ==========================================
// INICIO DEL WEB SCRAPING A DEMANDA (MEF)
// ==========================================
// ... justo después de definir $codigo_proyecto ...
$codigo_proyecto = $obra['codigo_proy'];

$mef_fecha_registro = "No disponible";
$mef_situacion = "No disponible";

// CAMBIO AQUÍ: Verifica $codigo_proyecto en lugar de $cui
if (!empty($codigo_proyecto)) {
    $url = "https://ofi5.mef.gob.pe/ssi/ssi/Index?codigo=" . $codigo_proyecto . "&tipo=1";
    // ... el resto del código es igual

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $html = curl_exec($ch);
    curl_close($ch);

    if ($html) {
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $nodoFecha = $xpath->query('//span[@id="lblFechaViabilidad"]'); 
        if ($nodoFecha->length > 0) {
            $mef_fecha_registro = trim($nodoFecha->item(0)->nodeValue);
        }

        $nodoSituacion = $xpath->query('//span[@id="lblSituacion"]'); 
        if ($nodoSituacion->length > 0) {
            $mef_situacion = trim($nodoSituacion->item(0)->nodeValue);
        }
    }
}
// ==========================================
// FIN DEL WEB SCRAPING
// ==========================================

// 2. OBTENER AVANCES
$sqlAvances = $conexion->prepare("
SELECT *
FROM avances
WHERE id_obra=?
ORDER BY fecha_avance DESC
");
$sqlAvances->execute([$id]);
$avances = $sqlAvances->fetchAll();

// 3. OBTENER FOTOS
$sqlFotos = $conexion->prepare("
SELECT *
FROM evidencias
WHERE id_obra=?
ORDER BY fecha_evidencia DESC
");
$sqlFotos->execute([$id]);
$fotos = $sqlFotos->fetchAll();

// AQUÍ CERRAMOS EL CÓDIGO PHP ANTES DE EMPEZAR EL HTML
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($obra['nombre_obra']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4 mb-5">

    <h2><?= htmlspecialchars($obra['nombre_obra']) ?></h2>
    <hr>

    <div class="row">
        <div class="col-md-6">
            <p><strong>Estado:</strong> <?= htmlspecialchars($obra['estado']) ?></p>
            <p><strong>Presupuesto:</strong> S/ <?= number_format($obra['presupuesto_total'], 2) ?></p>
            <p><strong>Avance Actual:</strong> <?= htmlspecialchars($obra['ultimo_avance_fisico']) ?>%</p>
        </div>
    </div>

    <div class="card mt-2 mb-4 border-info">
        <div class="card-header bg-info text-white">
            <strong>Datos extraídos en vivo desde el portal Invierte.pe (MEF)</strong>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Código Único de Inversión (CUI):</strong></p>
                    <p class="text-muted"><?= htmlspecialchars($obra['codigo_unico_inversion']) ?></p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Fecha de Viabilidad / Registro:</strong></p>
                    <p class="text-muted"><?= htmlspecialchars($mef_fecha_registro) ?></p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Situación Actual (MEF):</strong></p>
                    <p class="text-muted"><?= htmlspecialchars($mef_situacion) ?></p>
                </div>
            </div>
            
            <div class="mt-3 text-end">
                <a href="https://ofi5.mef.gob.pe/ssi/ssi/Index?codigo=<?= htmlspecialchars($obra['codigo_unico_inversion']) ?>&tipo=1" target="_blank" class="btn btn-outline-info btn-sm">
                    Ver fuente oficial
                </a>
            </div>
        </div>
    </div>

    <hr>

    <h4>Historial de Avances</h4>
    <table class="table table-bordered">
        <tr class="table-light">
            <th>Fecha</th>
            <th>Físico</th>
            <th>Financiero</th>
        </tr>
        <?php foreach($avances as $a){ ?>
        <tr>
            <td><?= $a['fecha_avance'] ?></td>
            <td><?= $a['avance_fisico'] ?>%</td>
            <td><?= $a['avance_financiero'] ?>%</td>
        </tr>
        <?php } ?>
    </table>

    <hr>

    <h4>Evidencias Fotográficas</h4>
    <div class="row">
        <?php foreach($fotos as $foto){ ?>
        <div class="col-md-4">
            <div class="card mb-3">
                <img src="../uploads/evidencias/<?= htmlspecialchars($foto['foto']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <h6><?= htmlspecialchars($foto['titulo']) ?></h6>
                    <p><?= htmlspecialchars($foto['descripcion']) ?></p>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>

</div>

</body>
</html>