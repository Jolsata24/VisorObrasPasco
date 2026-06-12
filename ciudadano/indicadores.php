<?php

require_once("../config/conexion.php");

/*
------------------------------------
OBRAS POR ESTADO
------------------------------------
*/
$sqlEstados = $conexion->query("
SELECT estado, COUNT(*) cantidad
FROM obras
GROUP BY estado
");

$estados = [];
$cantidadesEstados = [];

while($fila = $sqlEstados->fetch(PDO::FETCH_ASSOC)){
    $estados[] = $fila['estado'];
    $cantidadesEstados[] = $fila['cantidad'];
}

/*
------------------------------------
INVERSIÓN POR ESTADO
------------------------------------
*/
$sqlInversion = $conexion->query("
SELECT estado,
SUM(presupuesto_total) total
FROM obras
GROUP BY estado
");

$estadosInv = [];
$montosInv = [];

while($fila = $sqlInversion->fetch(PDO::FETCH_ASSOC)){
    $estadosInv[] = $fila['estado'];
    $montosInv[] = $fila['total'];
}

/*
------------------------------------
TOP 10 OBRAS
------------------------------------
*/
$sqlTop = $conexion->query("
SELECT
nombre_obra,
presupuesto_total
FROM obras
ORDER BY presupuesto_total DESC
LIMIT 10
");

$nombresTop = [];
$montosTop = [];

while($fila = $sqlTop->fetch(PDO::FETCH_ASSOC)){
    // Acortamos nombres muy largos para que no rompan el diseño del gráfico
    $nombreCorto = strlen($fila['nombre_obra']) > 45 ? substr($fila['nombre_obra'], 0, 42) . '...' : $fila['nombre_obra'];
    $nombresTop[] = $nombreCorto;
    $montosTop[] = $fila['presupuesto_total'];
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indicadores Ciudadanos - Pasco Transparente</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../css/estilos.css" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* Ajuste específico para centrar el gráfico circular sin deformarse */
        .chart-container-pie {
            position: relative;
            max-height: 320px;
            margin: 0 auto;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="../img/logo_gore.png" alt="Logo GOREPA" class="navbar-logo">
            <span class="text-white fw-bold">Pasco Transparente</span>
        </a>
        
        <div class="collapse navbar-collapse justify-content-center">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="index.php">Observatorio</a></li>
                <li class="nav-item"><a class="nav-link" href="obras.php">Proyectos</a></li>
                <li class="nav-item"><a class="nav-link active" href="indicadores.php">Reportes</a></li>
            </ul>
        </div>

        <a href="obras.php" class="btn btn-outline-light btn-sm px-3 border-opacity-50"><i class="fas fa-list me-2"></i>Lista de Obras</a>
    </div>
</nav>

<div class="container mt-5 mb-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="section-title mb-1"><i class="fas fa-chart-pie text-primary me-2"></i> Panel de Analítica Financiera</h2>
            <p class="text-muted mb-0">Resumen estadístico del presupuesto analizado dinámicamente desde la base de datos regional.</p>
        </div>
        <span class="badge bg-light text-dark border shadow-sm px-3 py-2 fs-6 d-none d-md-block">
            <i class="fas fa-sync-alt text-success me-1"></i> Datos en tiempo real
        </span>
    </div>

    <div class="row g-4">
        
        <div class="col-lg-5">
            <div class="card card-clean h-100 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-cubes text-primary me-2"></i> Cantidad de Obras según Estado</h6>
                </div>
                <div class="card-body d-flex align-items-center p-4">
                    <div class="chart-container-pie w-100">
                        <canvas id="graficoEstados"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card card-clean h-100 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-money-check-alt text-success me-2"></i> Monto de Inversión Total por Estado (S/)</h6>
                </div>
                <div class="card-body p-4">
                    <canvas id="graficoInversion" style="max-height: 320px;"></canvas>
                </div>
            </div>
        </div>

    </div>

    <div class="row mt-2 mb-5">
        <div class="col-12">
            <div class="card card-clean overflow-hidden">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-trophy text-warning me-2"></i> Top 10 Obras Públicas con Mayor Presupuesto Asignado</h6>
                </div>
                <div class="card-body p-4">
                    <canvas id="graficoTop" style="max-height: 450px;"></canvas>
                </div>
            </div>
        </div>
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

<script>
    // Configuración global de fuentes en Chart.js
    Chart.defaults.font.family = "'Segoe UI', system-ui, sans-serif";
    Chart.defaults.font.color = "#64748b";

    // 1. GRÁFICO: OBRAS POR ESTADO (Torta)
    new Chart(document.getElementById('graficoEstados'), {
        type: 'pie',
        data: {
            labels: <?= json_encode($estados) ?>,
            datasets: [{
                data: <?= json_encode($cantidadesEstados) ?>,
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#64748b'], // Paleta moderna
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 15, padding: 15 } }
            }
        }
    });

    // 2. GRÁFICO: INVERSIÓN POR ESTADO (Barras Verticales)
    new Chart(document.getElementById('graficoInversion'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($estadosInv) ?>,
            datasets: [{
                label: 'Presupuesto Acumulado (S/) ',
                data: <?= json_encode($montosInv) ?>,
                backgroundColor: 'rgba(14, 165, 233, 0.85)', // Celeste elegante institucional
                borderColor: '#0ea5e9',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });

    // 3. GRÁFICO: TOP 10 OBRAS (Barras Horizontales)
    new Chart(document.getElementById('graficoTop'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($nombresTop) ?>,
            datasets: [{
                label: 'Presupuesto Total Asignado ',
                data: <?= json_encode($montosTop) ?>,
                backgroundColor: 'rgba(79, 70, 229, 0.85)', // Indigo/Azul profundo elegante
                borderColor: '#4f46e5',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                y: { grid: { display: false }, labels: { font: { size: 11 } } }
            }
        }
    });
</script>

</body>
</html>