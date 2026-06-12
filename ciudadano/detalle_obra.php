<?php
require_once("../config/conexion.php");

// Verificación de seguridad básica
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: obras.php");
    exit;
}

$id = $_GET['id'];

// 1. OBTENER DATOS DE LA OBRA
$sql = $conexion->prepare("SELECT * FROM obras WHERE id_obra=?");
$sql->execute([$id]);
$obra = $sql->fetch();

if (!$obra) {
    header("Location: obras.php");
    exit;
}

// 2. OBTENER HISTORIAL DE AVANCES
$sqlAvances = $conexion->prepare("SELECT * FROM avances WHERE id_obra=? ORDER BY fecha_avance DESC");
$sqlAvances->execute([$id]);
$avances = $sqlAvances->fetchAll();

// 3. OBTENER EVIDENCIAS FOTOGRÁFICAS
$sqlFotos = $conexion->prepare("SELECT * FROM evidencias WHERE id_obra=? ORDER BY fecha_evidencia DESC");
$sqlFotos->execute([$id]);
$fotos = $sqlFotos->fetchAll();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Obra - Pasco Transparente</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../css/estilos.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="../img/logo_gore.png" alt="Logo GOREPA" class="navbar-logo">
            <span>Pasco Transparente</span>
        </a>
        <a href="obras.php" class="btn btn-outline-light btn-sm px-3"><i class="fas fa-arrow-left me-2"></i>Volver a la lista</a>
    </div>
</nav>

<div class="container mt-4 mb-5">
    
    <div class="d-flex align-items-center mb-4">
        <div class="icon-box me-3 bg-primary text-white shadow-sm" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
            <i class="fas fa-hard-hat"></i>
        </div>
        <h2 class="section-title mb-0 fs-4" style="max-width: 900px; line-height: 1.3;">
            <?= htmlspecialchars($obra['nombre_obra']) ?>
        </h2>
    </div>
    
    <div class="row g-4 mb-4">
        
        <div class="col-lg-4 d-flex flex-column gap-4">
            
            <div class="card card-clean p-4">
                <h6 class="fw-bold text-uppercase text-muted mb-3 border-bottom pb-2">Resumen Ejecutivo</h6>
                
                <p class="mb-1 text-muted small"><i class="fas fa-hashtag me-2"></i>CÓDIGO SNIP / CUI</p>
                <p class="fw-bold text-dark mb-3"><?= htmlspecialchars($obra['codigo_proy']) ?></p>
                
                <p class="mb-1 text-muted small"><i class="fas fa-info-circle me-2"></i>ESTADO ACTUAL</p>
                <p class="mb-3">
                    <?php 
                        $badgeColor = 'bg-secondary';
                        if($obra['estado'] == 'ACTIVO') $badgeColor = 'bg-primary';
                        if($obra['estado'] == 'CERRADO') $badgeColor = 'bg-success';
                    ?>
                    <span class="badge <?= $badgeColor ?> shadow-sm px-3 py-2"><?= htmlspecialchars($obra['estado']) ?></span>
                </p>
                
                <p class="mb-1 text-muted small"><i class="fas fa-money-bill-wave me-2"></i>PRESUPUESTO TOTAL</p>
                <p class="fw-bold text-success fs-3 mb-3">S/ <?= number_format($obra['presupuesto_total'], 2) ?></p>

                <p class="mb-1 text-muted small"><i class="fas fa-chart-line me-2"></i>AVANCE FÍSICO (Local)</p>
                <div class="d-flex align-items-center mb-2">
                    <div class="fw-bold fs-5 me-3"><?= htmlspecialchars($obra['ultimo_avance_fisico']) ?>%</div>
                    <div class="progress flex-grow-1 bg-light border shadow-sm" style="height: 10px;">
                        <div class="progress-bar bg-info" style="width: <?= htmlspecialchars($obra['ultimo_avance_fisico']) ?>%"></div>
                    </div>
                </div>
            </div>

            <div class="card card-clean p-4 flex-grow-1">
                <h6 class="fw-bold text-uppercase text-muted mb-3 border-bottom pb-2">Historial de Avances</h6>
                <?php if(count($avances) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless align-middle">
                            <thead class="border-bottom text-muted small">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Físico</th>
                                    <th>Financ.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($avances as $a): ?>
                                <tr>
                                    <td class="text-secondary small fw-medium"><?= date('d/m/Y', strtotime($a['fecha_avance'])) ?></td>
                                    <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle"><?= $a['avance_fisico'] ?>%</span></td>
                                    <td><span class="badge bg-success bg-opacity-10 text-success border border-success-subtle"><?= $a['avance_financiero'] ?>%</span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted small text-center my-4"><i class="fas fa-history fa-2x mb-2 opacity-25 d-block"></i> No hay registros de avances locales.</p>
                <?php endif; ?>
            </div>

        </div>

        <div class="col-lg-8">
            <div class="card card-clean overflow-hidden h-100">
                <div class="mef-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 1rem 1.5rem;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-landmark fs-4 me-3 text-info"></i>
                        <div>
                            <span class="d-block small text-info fw-bold" style="letter-spacing: 0.5px;">PORTAL DE TRANSPARENCIA ESTÁNDAR</span>
                            <strong class="fs-6">Ficha Oficial SSI - INVIERTE.PE</strong>
                        </div>
                    </div>
                    <?php if(!empty($obra['codigo_proy'])): ?>
                    <a href="https://ofi5.mef.gob.pe/ssi/ssi/Index?codigo=<?= $obra['codigo_proy'] ?>&tipo=1" target="_blank" class="btn btn-outline-light btn-sm px-3" style="backdrop-filter: blur(4px);">
                        Abrir en el MEF <i class="fas fa-external-link-alt ms-1"></i>
                    </a>
                    <?php endif; ?>
                </div>
                
                <div class="p-0 h-100 bg-light" style="min-height: 600px;">
                    <?php if(!empty($obra['codigo_proy'])): ?>
                        <iframe src="https://ofi5.mef.gob.pe/ssi/ssi/Index?codigo=<?= $obra['codigo_proy'] ?>&tipo=1" width="100%" height="100%" style="border:none; min-height: 600px; display: block;"></iframe>
                    <?php else: ?>
                        <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted opacity-50 pt-5">
                            <i class="fas fa-exclamation-triangle fa-4x mb-3"></i>
                            <h5>Código de proyecto no disponible</h5>
                            <p>No se puede cargar la ficha oficial del MEF.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

    <h4 class="section-title mt-5 mb-4"><i class="fas fa-camera-retro me-2 text-primary"></i> Galería de Evidencias de Campo</h4>
    
    <div class="row g-4">
        <?php if(count($fotos) > 0): ?>
            <?php foreach($fotos as $foto): ?>
            <div class="col-md-4 col-lg-3">
                <div class="card card-clean card-interactive h-100 overflow-hidden">
                    <img src="../uploads/evidencias/<?= htmlspecialchars($foto['foto']) ?>" class="card-img-top" style="height: 220px; object-fit: cover;" alt="<?= htmlspecialchars($foto['titulo']) ?>">
                    <div class="card-body">
                        <h6 class="fw-bold mb-2 text-dark"><?= htmlspecialchars($foto['titulo']) ?></h6>
                        <p class="text-muted small mb-3" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                            <?= htmlspecialchars($foto['descripcion']) ?>
                        </p>
                        <div class="text-secondary small fw-medium">
                            <i class="far fa-calendar-alt me-1"></i> <?= date('d/m/Y', strtotime($foto['fecha_evidencia'])) ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="card card-clean p-5 text-center text-muted">
                    <i class="fas fa-images fa-3x mb-3 opacity-25"></i>
                    <h5>No hay fotografías registradas</h5>
                    <p>Las evidencias de campo se publicarán conforme avance la obra.</p>
                </div>
            </div>
        <?php endif; ?>
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

</body>
</html>