<?php

require_once("../config/conexion.php");

$sql = $conexion->prepare("
    SELECT
        id_obra,
        nombre_obra,
        estado,
        ultimo_avance_fisico,
        latitud,
        longitud
    FROM obras
    WHERE latitud IS NOT NULL
    AND longitud IS NOT NULL
");

$sql->execute();
$obras = $sql->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa de Obras - Pasco Transparente</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <link href="../css/estilos.css" rel="stylesheet">

    <style>
        /* Ajustes específicos para que el mapa respete los bordes curvos de la tarjeta */
        #mapa {
            height: 100%;
            width: 100%;
            z-index: 1; /* Evita que el mapa se sobreponga al menú superior */
        }

        /* Estilo elegante para los popups del mapa */
        .leaflet-popup-content-wrapper {
            border-radius: 12px !important;
            padding: 2px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
            border: none;
        }
        .leaflet-popup-content {
            margin: 15px;
        }
        .leaflet-popup-content h6 {
            color: #0f172a;
            font-weight: 800;
            margin-bottom: 10px;
            font-size: 0.95rem;
            line-height: 1.3;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="../img/logo_gore.png" alt="Logo GOREPA" class="navbar-logo">
            <span>Pasco Transparente</span>
        </a>
        
        <div class="collapse navbar-collapse justify-content-center">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="index.php">Observatorio</a></li>
                <li class="nav-item"><a class="nav-link active" href="obras.php">Proyectos</a></li>
                <li class="nav-item"><a class="nav-link" href="indicadores.php">Reportes</a></li>
            </ul>
        </div>

        <a href="obras.php" class="btn btn-outline-light btn-sm px-3 border-opacity-50"><i class="fas fa-list me-2"></i>Lista de Obras</a>
    </div>
</nav>

<div class="container-fluid px-4 mt-5 mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title mb-0"><i class="fas fa-map-marked-alt text-primary me-2"></i> Mapa Interactivo de Inversiones</h2>
        <span class="badge bg-light text-dark border shadow-sm px-3 py-2 fs-6">
            <i class="fas fa-map-pin text-danger me-1"></i> <?= count($obras) ?> Obras georreferenciadas
        </span>
    </div>

    <div class="card card-clean overflow-hidden shadow-sm" style="height: 72vh; border-radius: 16px; border: 4px solid #ffffff;">
        <div id="mapa"></div>
    </div>

</div>

<footer class="border-top py-4 mt-auto bg-white">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center">
        <div class="mb-3 mb-md-0 fw-bold" style="color: #006b8f;">
            Gobierno Regional de Pasco
        </div>
        <div class="text-center mb-3 mb-md-0 small text-muted">
            © 2026 Pasco Transparente - Observatorio Ciudadano
        </div>
    </div>
</footer>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
// Centro en Cerro de Pasco
var mapa = L.map('mapa').setView([-10.6833, -76.2567], 9);

// 1. MAPA BASE "VOYAGER": Claro, moderno y colorido
L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; OpenStreetMap &copy; CARTO',
    subdomains: 'abcd',
    maxZoom: 20
}).addTo(mapa);

// ========================================================
// 2. FUNCIÓN PARA GENERAR COLORES ALEGRES POR DISTRITO
// ========================================================
function obtenerColorDistrito(nombre) {
    // Paleta de colores vivos y limpios
    var colores = ['#ff9ff3', '#feca57', '#ffffb2', '#54a0ff', '#00d2d3', '#1dd1a1', '#ff6b6b', '#cca3ff'];
    
    var hash = 0;
    for (var i = 0; i < nombre.length; i++) {
        hash = nombre.charCodeAt(i) + ((hash << 5) - hash);
    }
    var index = Math.abs(hash % colores.length);
    return colores[index];
}

function estiloDistrito(feature) {
    var nombreDistrito = feature.properties.NOMBDIST || feature.properties.name || "Default";
    return {
        fillColor: obtenerColorDistrito(nombreDistrito),
        weight: 2,
        opacity: 1,
        color: '#ffffff', // Borde blanco entre distritos
        dashArray: '3',
        fillOpacity: 0.35 // Transparencia suave
    };
}

// 3. CARGAR EL ARCHIVO GEOJSON DE LOS DISTRITOS
fetch('distritos_pasco.geojson')
    .then(response => response.json())
    .then(data => {
        L.geoJSON(data, {
            style: estiloDistrito,
            onEachFeature: function(feature, layer) {
                var nombre = feature.properties.NOMBDIST || feature.properties.name || "";
                layer.bindTooltip("<strong style='color:#0f172a;'>Distrito de " + nombre + "</strong>", {
                    sticky: true,
                    className: 'shadow-sm border-0 rounded-3 p-2'
                });
            }
        }).addTo(mapa);
    })
    .catch(err => console.log("Nota: Archivo 'distritos_pasco.geojson' no encontrado."));

// ========================================================
// 4. PINES DE LAS OBRAS DE LA BASE DE DATOS
// ========================================================
<?php foreach($obras as $obra){ 
    
    // Asignar color al badge dependiendo del estado
    $badgeColor = 'bg-secondary';
    if($obra['estado'] == 'ACTIVO' || $obra['estado'] == 'En Ejecución') $badgeColor = 'bg-primary';
    if($obra['estado'] == 'CERRADO' || $obra['estado'] == 'Culminado') $badgeColor = 'bg-success';
    if($obra['estado'] == 'Paralizada') $badgeColor = 'bg-danger';
?>

L.marker([
    <?= $obra['latitud'] ?>,
    <?= $obra['longitud'] ?>
])
.addTo(mapa)
.bindPopup(`
    <div style="min-width: 250px;">
        <h6><?= htmlspecialchars(strtoupper($obra['nombre_obra'])) ?></h6>
        <div class="mb-2">
            <span class="badge <?= $badgeColor ?> px-2 py-1 shadow-sm"><?= htmlspecialchars($obra['estado']) ?></span>
        </div>
        <div class="d-flex align-items-center mb-3 mt-2">
            <small class="text-muted fw-bold me-2">AVANCE:</small>
            <div class="progress flex-grow-1 bg-light border" style="height: 6px;">
                <div class="progress-bar bg-info" style="width: <?= htmlspecialchars($obra['ultimo_avance_fisico']) ?>%"></div>
            </div>
            <small class="fw-bold ms-2"><?= htmlspecialchars($obra['ultimo_avance_fisico']) ?>%</small>
        </div>
        <a href="detalle_obra.php?id=<?= $obra['id_obra'] ?>" class="btn btn-primary-custom btn-sm w-100" style="background-color: #006b8f; color: white; border-radius: 6px;">
            <i class="far fa-eye me-1"></i> Ver Expediente
        </a>
    </div>
`); 

<?php } ?>

</script>
</body>
</html>