<?php
require_once("../config/conexion.php");
require_once("validar.php"); 

// 1. ESCUDO: Verificar que el ID viene en la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("<div style='text-align:center; margin-top:50px; font-family:sans-serif;'>
            <h3 style='color:red;'>Error: No se ha seleccionado ninguna obra.</h3>
            <a href='obras.php'>Volver a la lista de obras</a>
         </div>");
}

$id_obra = $_GET['id'];

// 2. Obtener los datos de la base de datos
$sqlObra = $conexion->prepare("SELECT codigo_proy, nombre_obra FROM obras WHERE id_obra = ?");
$sqlObra->execute([$id_obra]);
$obra = $sqlObra->fetch();

// 3. ESCUDO: Verificar que la obra realmente existe en la base de datos
if (!$obra) {
    die("<div style='text-align:center; margin-top:50px; font-family:sans-serif;'>
            <h3 style='color:red;'>Error: La obra con ID {$id_obra} no existe.</h3>
            <a href='obras.php'>Volver a la lista de obras</a>
         </div>");
}

$codigo_proyecto = $obra['codigo_proy'];

// Variables vacías por defecto para el formulario
$fecha_hoy = date('Y-m-d');
$avance_fisico = "0.00";
$avance_financiero = "0.00";
$mensaje_mef = "";

// ... AQUÍ CONTINÚA TU CÓDIGO NORMAL DEL WEB SCRAPING ...
// if (isset($_GET['mef']) && $_GET['mef'] == 1 && !empty($codigo_proyecto)) {

// ==========================================
// INICIO DEL WEB SCRAPING (Si se presionó el botón)
// ==========================================
if (isset($_GET['mef']) && $_GET['mef'] == 1 && !empty($codigo_proyecto)) {
    
    $url = "https://ofi5.mef.gob.pe/ssi/ssi/Index?codigo=" . $codigo_proyecto . "&tipo=1";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/114.0.0.0');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $html = curl_exec($ch);
    curl_close($ch);

    if ($html) {
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        // EXTRAER AVANCE FÍSICO (Ajusta el ID 'lblAvanceFisico' según el HTML real del MEF)
        $nodoFisico = $xpath->query('//span[@id="lblAvanceFisico"]'); 
        if ($nodoFisico->length > 0) {
            // Limpiamos el texto para quedarnos solo con el número (ej. "45.5%" -> "45.5")
            $avance_fisico = preg_replace('/[^0-9.]/', '', $nodoFisico->item(0)->nodeValue);
        }

        // EXTRAER AVANCE FINANCIERO (Ajusta el ID 'lblAvanceFinanciero' según el HTML real del MEF)
        $nodoFinanciero = $xpath->query('//span[@id="lblAvanceFinanciero"]'); 
        if ($nodoFinanciero->length > 0) {
            $avance_financiero = preg_replace('/[^0-9.]/', '', $nodoFinanciero->item(0)->nodeValue);
        }

        $mensaje_mef = "¡Datos extraídos correctamente del MEF!";
    } else {
        $mensaje_mef = "Error al conectar con el MEF.";
    }
}
// ==========================================
// FIN DEL WEB SCRAPING
// ==========================================
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrar Avance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4 mb-5">
    
    <h2>Registrar Avance</h2>
    <h6 class="text-muted"><?= htmlspecialchars($obra['nombre_obra']) ?></h6>
    <hr>

    <div class="alert alert-info d-flex justify-content-between align-items-center">
        <div>
            <strong>¿Quieres autocompletar los datos?</strong><br>
            <small>Presiona el botón para extraer el último avance directamente desde Invierte.pe</small>
        </div>
        <a href="registrar_avance.php?id=<?= $id_obra ?>&mef=1" class="btn btn-warning fw-bold">
            ⚡ Extraer del MEF
        </a>
    </div>

    <?php if(!empty($mensaje_mef)): ?>
        <div class="alert alert-success"><?= $mensaje_mef ?></div>
    <?php endif; ?>

    <form action="guardar_avance.php" method="POST" class="card p-4 shadow-sm">
        
        <input type="hidden" name="id_obra" value="<?= $id_obra ?>">

        <div class="mb-3">
            <label class="fw-bold">Fecha de Avance</label>
            <input type="date" name="fecha_avance" class="form-control" value="<?= $fecha_hoy ?>" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="fw-bold text-primary">Avance Físico (%)</label>
                <input type="number" step="0.01" name="avance_fisico" class="form-control form-control-lg border-primary" value="<?= $avance_fisico ?>" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="fw-bold text-success">Avance Financiero (%)</label>
                <input type="number" step="0.01" name="avance_financiero" class="form-control form-control-lg border-success" value="<?= $avance_financiero ?>" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="fw-bold">Observaciones (Opcional)</label>
            <textarea name="observaciones" class="form-control" rows="3"><?php if(isset($_GET['mef']) && $_GET['mef']==1) echo "Datos actualizados automáticamente desde el SSI (MEF)."; ?></textarea>
        </div>

        <div class="d-flex justify-content-between mt-3">
            <a href="avances.php?id=<?= $id_obra ?>" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary px-5">Guardar Avance en el Visor</button>
        </div>

    </form>
</div>

</body>
</html>