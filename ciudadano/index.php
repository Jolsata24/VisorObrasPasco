<?php

require_once("../config/conexion.php");

// Consultas dinámicas a la base de datos
$totalObras = $conexion->query("SELECT COUNT(*) FROM obras")->fetchColumn();
$ejecucion = $conexion->query("SELECT COUNT(*) FROM obras WHERE estado='ACTIVO'")->fetchColumn();
$culminadas = $conexion->query("SELECT COUNT(*) FROM obras WHERE estado='CERRADO'")->fetchColumn();
$inversion = $conexion->query("SELECT SUM(presupuesto_total) FROM obras")->fetchColumn();

// Formateo de presupuesto (Ej: S/ 845M) para igualar el diseño visual
if ($inversion >= 1000000) {
    $inversion_formateada = "S/ " . number_format($inversion / 1000000, 1) . "M";
} else {
    $inversion_formateada = "S/ " . number_format($inversion, 0);
}

$porcentaje_ejecucion = $totalObras > 0 ? round(($ejecucion / $totalObras) * 100) : 0;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pasco Transparente - Observatorio Regional</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --bg-color: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --primary-color: #006b8f;
            --card-shadow: 0 10px 25px rgba(0, 0, 0, 0.02);
            --border-radius: 16px;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* =========================================
           NAVBAR STICKY Y TRANSPARENTE
           ========================================= */
        .navbar-custom {
            position: fixed; /* Lo pega a la pantalla */
            top: 0;
            width: 100%;
            z-index: 1030; /* Asegura que esté por encima de todo */
            background-color: transparent !important; /* Transparente al inicio */
            padding: 1.2rem 0;
            transition: all 0.4s ease-in-out; /* Animación suave */
        }

        /* Clase añadida por JavaScript al hacer Scroll */
        .navbar-custom.scrolled {
            background-color: rgba(15, 23, 42, 0.95) !important; /* Azul oscuro elegante */
            backdrop-filter: blur(10px); /* Efecto cristal borroso */
            padding: 0.6rem 0; /* Se hace un poco más delgado al bajar */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .navbar-brand { 
            font-weight: 700; 
            color: #ffffff !important;
            font-size: 1.15rem;
            letter-spacing: -0.5px;
        }
        .navbar-logo {
            height: 38px;
            width: auto;
            object-fit: contain;
            margin-right: 12px;
        }
        .nav-link { color: rgba(255, 255, 255, 0.8) !important; font-size: 0.9rem; font-weight: 500; }
        .nav-link:hover, .nav-link.active { color: #fff !important; }
        
        /* BOTONES DEL HERO */
        .btn-primary-custom {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            padding: 0.65rem 1.4rem;
            font-size: 0.9rem;
            transition: background 0.2s ease;
        }
        .btn-primary-custom:hover { background-color: #005673; color: white; }
        
        .btn-outline-custom {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 8px;
            font-weight: 600;
            padding: 0.65rem 1.4rem;
            font-size: 0.9rem;
            backdrop-filter: blur(4px);
            transition: all 0.2s ease;
        }
        .btn-outline-custom:hover { background-color: white; color: var(--text-main); border-color: white; }

        /* HERO SECTION CON IMAGEN DE FONDO COMPLETA */
        .hero-section {
            position: relative;
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.75) 0%, rgba(30, 41, 59, 0.95) 100%), 
                        url('../img/fondo_login.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: scroll;
            color: white;
            /* Incrementamos el padding superior para que el texto no quede escondido detrás del menú fijo */
            padding: 160px 0 100px 0; 
            border-radius: 0 0 24px 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .hero-title {
            font-weight: 800;
            letter-spacing: -1px;
            color: #ffffff;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        /* MÓDULOS Y COMPONENTES */
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            background: #ffffff;
        }
        .card-interactive {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card-interactive:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.06);
        }
        .icon-box {
            width: 46px;
            height: 46px;
            background-color: #e0f2fe;
            color: #0284c7;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 1.15rem;
        }

        /* BORDES DE COLOR EN LOS KPIs */
        .kpi-card { border-top: 4px solid transparent; padding: 24px; }
        .kpi-totales { border-top-color: #3b82f6; }
        .kpi-ejecucion { border-top-color: #f59e0b; }
        .kpi-culminadas { border-top-color: #10b981; }
        .kpi-inversion { border-top-color: #06b6d4; }
        
        .kpi-title { font-size: 0.75rem; font-weight: 700; color: #475569; letter-spacing: 0.5px; }
        .kpi-value { font-size: 2.3rem; font-weight: 800; color: var(--text-main); line-height: 1; margin: 10px 0 6px 0; }
        .kpi-sub { font-size: 0.72rem; color: var(--text-muted); font-weight: 500; }
        
        /* LOWER DASHBOARD BUILD */
        .dashboard-title { font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-bottom: 1.2rem; }
        .map-placeholder {
            background-color: #cbd5e1;
            background-image: linear-gradient(180deg, #e2e8f0 0%, #cbd5e1 100%);
            border-radius: 12px;
            height: 320px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475569;
        }
        .progress-label { font-size: 0.8rem; font-weight: 600; display: flex; justify-content: space-between; margin-bottom: 5px; color: #334155; }
        .progress-bar-custom {
            height: 7px;
            background-color: #e2e8f0;
            border-radius: 4px;
            margin-bottom: 1.1rem;
            overflow: hidden;
        }
        .progress-fill { height: 100%; border-radius: 4px; }
        footer { font-size: 0.8rem; color: var(--text-muted); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom" id="mainNavbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="../img/logo_gore.png" alt="Logo GOREPA" class="navbar-logo">
            <span>Pasco Transparente</span>
        </a>
        
        <div class="collapse navbar-collapse justify-content-center">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link active" href="#">Observatorio</a></li>
                <li class="nav-item"><a class="nav-link" href="obras.php">Proyectos</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Noticias</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Transparencia</a></li>
            </ul>
        </div>

        <a href="obras.php" class="btn btn-primary-custom btn-sm">Consultar Obras</a>
    </div>
</nav>

<div class="hero-section text-center mb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="display-4 hero-title fw-bold mb-3">Observatorio Regional de Pasco</h1>
                <p class="lead mb-4" style="color: #cbd5e1; font-size: 1.15rem; line-height: 1.6; text-shadow: 0 1px 4px rgba(0,0,0,0.4);">
                    Transparencia radical en obras públicas. Transformamos datos regionales complejos en información accesible y confiable para cada ciudadano. Monitoreamos presupuestos, avance físico y aseguramos la integridad institucional en toda la región de Pasco.
                </p>
                <div class="d-flex gap-3 justify-content-center">
                    <a href="obras.php" class="btn btn-primary-custom"><i class="fas fa-search me-2"></i> Explorar Obras</a>
                    <a href="indicadores.php" class="btn btn-outline-custom"><i class="far fa-file-alt me-2"></i> Ver Informes</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mb-5">

    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <a href="mapa.php" class="text-decoration-none">
                <div class="card card-interactive p-2 h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-box me-4"><i class="fas fa-map-marked-alt"></i></div>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-1 text-dark">Mapa Geográfico de Obras</h6>
                            <p class="text-muted small mb-0">Visualiza la distribución territorial de los proyectos públicos en toda la región.</p>
                        </div>
                        <div class="text-muted fs-5"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-md-6">
            <a href="indicadores.php" class="text-decoration-none">
                <div class="card card-interactive p-2 h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-box me-4"><i class="fas fa-chart-pie"></i></div>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-1 text-dark">Panel de Indicadores</h6>
                            <p class="text-muted small mb-0">Análisis detallado del avance físico y financiero de la inversión pública.</p>
                        </div>
                        <div class="text-muted fs-5"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card kpi-card kpi-totales h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="kpi-title text-uppercase">Obras Totales</span>
                    <i class="fas fa-cubes text-primary fs-5"></i>
                </div>
                <div class="kpi-value"><?= number_format($totalObras) ?></div>
                <div class="kpi-sub text-success"><i class="fas fa-sync-alt"></i> Datos en tiempo real</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card kpi-card kpi-ejecucion h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="kpi-title text-uppercase">En Ejecución</span>
                    <i class="fas fa-tools text-warning fs-5"></i>
                </div>
                <div class="kpi-value"><?= number_format($ejecucion) ?></div>
                <div class="kpi-sub"><?= $porcentaje_ejecucion ?>% del total regional</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card kpi-card kpi-culminadas h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="kpi-title text-uppercase">Culminadas</span>
                    <i class="fas fa-chart-line text-success"></i>
                </div>
                <div class="kpi-value"><?= number_format($culminadas) ?></div>
                <div class="kpi-sub">Fase de cierre o entrega</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card kpi-card kpi-inversion h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="kpi-title text-uppercase">Inversión Total</span>
                    <i class="fas fa-wallet text-info fs-5"></i>
                </div>
                <div class="kpi-value"><?= $inversion_formateada ?></div>
                <div class="kpi-sub">Presupuesto consolidado</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        
        <div class="col-lg-8">
            <div class="card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="dashboard-title mb-0">Distribución Geográfica</h5>
                    <a href="mapa.php" class="btn btn-light btn-sm border fw-semibold px-3">Ver Mapa Interactivo</a>
                </div>
                <div class="map-placeholder">
                    <div class="text-center">
                        <i class="fas fa-globe-americas fa-3x mb-3 opacity-50 text-secondary"></i>
                        <h6 class="fw-bold mb-1">Mapeo Georreferenciado Activo</h6>
                        <small class="text-muted">Límites distritales y visualización de pines interactivos</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 d-flex flex-column gap-4">
            
            <div class="card p-4">
                <h5 class="dashboard-title">Obras por Provincia</h5>
                
                <div class="progress-label"><span>Pasco</span> <span class="text-muted">60%</span></div>
                <div class="progress-bar-custom"><div class="progress-fill" style="width: 60%; background-color: #0284c7;"></div></div>

                <div class="progress-label"><span>Daniel Alcides Carrión</span> <span class="text-muted">25%</span></div>
                <div class="progress-bar-custom"><div class="progress-fill" style="width: 25%; background-color: #8b5cf6;"></div></div>

                <div class="progress-label"><span>Oxapampa</span> <span class="text-muted">15%</span></div>
                <div class="progress-bar-custom"><div class="progress-fill" style="width: 15%; background-color: #f59e0b;"></div></div>
            </div>

            <div class="card p-4 flex-grow-1">
                <h5 class="dashboard-title text-center mb-3">Estado Financiero</h5>
                <div style="position: relative; height: 160px; width: 100%;">
                    <canvas id="donutChart"></canvas>
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                        <h4 class="mb-0 fw-bold" style="color: #0f172a;">68%</h4>
                        <small class="text-muted fw-semibold" style="font-size:0.65rem; text-uppercase; letter-spacing:0.3px;">Devengado</small>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<footer class="border-top py-4 mt-5 bg-white">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center">
        <div class="mb-3 mb-md-0 fw-bold" style="color: var(--primary-color);">
            Gobierno Regional de Pasco
        </div>
        <div class="text-center mb-3 mb-md-0 small text-muted">
            © 2026 Pasco Transparente - Observatorio Ciudadano de Inversión Pública
        </div>
        <div class="d-flex gap-3">
            <a href="#" class="text-decoration-none text-muted small">Privacidad</a>
            <a href="#" class="text-decoration-none text-muted small">Términos</a>
            <a href="#" class="text-decoration-none text-muted small">Portal MEF</a>
        </div>
    </div>
</footer>

<script>
    // -------------------------------------------------------------------
    // SCRIPT MÁGICO PARA EL NAVBAR STICKY
    // -------------------------------------------------------------------
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('mainNavbar');
        // Si el usuario hace scroll hacia abajo más de 50 píxeles, aplica la clase
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            // Si regresa al inicio, la quita y lo vuelve transparente
            navbar.classList.remove('scrolled');
        }
    });

    // -------------------------------------------------------------------
    // Gráfico Donut
    // -------------------------------------------------------------------
    const ctx = document.getElementById('donutChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Devengado', 'Saldo'],
            datasets: [{
                data: [68, 32],
                backgroundColor: ['#10b981', '#f1f5f9'],
                borderWidth: 0,
                cutout: '76%',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false }
            }
        }
    });
</script>

</body>
</html>