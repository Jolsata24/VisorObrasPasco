<?php

require_once("../config/conexion.php");

$buscar = $_GET['buscar'] ?? '';

$sql = $conexion->prepare("
SELECT *
FROM obras
WHERE nombre_obra LIKE ?
ORDER BY nombre_obra
");

$sql->execute([
    "%$buscar%"
]);

$obras = $sql->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Obras - Pasco Transparente</title>
    
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
        
        <div class="collapse navbar-collapse justify-content-center">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="index.php">Observatorio</a></li>
                <li class="nav-item"><a class="nav-link active" href="obras.php">Proyectos</a></li>
                <li class="nav-item"><a class="nav-link" href="indicadores.php">Reportes</a></li>
            </ul>
        </div>

        <a href="index.php" class="btn btn-outline-light btn-sm px-3 border-opacity-50">Volver al Inicio</a>
    </div>
</nav>

<div class="container mt-5 mb-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title mb-0">🔍 Consulta de Obras Públicas</h2>
        <span class="badge bg-primary text-white fs-6 shadow-sm border border-primary px-3 py-2">
            <?= count($obras) ?> proyectos encontrados
        </span>
    </div>

    <div class="card card-clean p-4 mb-4">
        <form method="GET" class="row g-3 align-items-center">
            <div class="col-md-9 col-lg-10">
                <div class="input-group input-group-lg shadow-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted px-3">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="buscar" class="form-control border-start-0 ps-0 fs-6" 
                           placeholder="Buscar por nombre de la obra, código o distrito..." 
                           value="<?= htmlspecialchars($buscar) ?>">
                </div>
            </div>
            <div class="col-md-3 col-lg-2">
                <button type="submit" class="btn btn-primary-custom btn-lg w-100 fs-6 shadow-sm">Buscar</button>
            </div>
        </form>
    </div>

    <div class="card card-clean overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light border-bottom">
                    <tr>
                        <th class="ps-4 py-3 text-muted small text-uppercase tracking-wider">Nombre de la Inversión</th>
                        <th class="py-3 text-muted small text-uppercase">Estado</th>
                        <th class="py-3 text-muted small text-uppercase">Avance Físico</th>
                        <th class="pe-4 py-3 text-center text-muted small text-uppercase" style="min-width: 140px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($obras) > 0): ?>
                        <?php foreach($obras as $obra): ?>
                        <tr>
                            <td class="ps-4 py-3" style="max-width: 450px;">
                                <div class="fw-bold text-dark lh-sm mb-1" style="font-size: 0.95rem;">
                                    <?= htmlspecialchars($obra['nombre_obra']) ?>
                                </div>
                                <?php if(!empty($obra['codigo_proy'])): ?>
                                    <div class="text-muted small">
                                        <i class="fas fa-hashtag opacity-50 me-1"></i><?= htmlspecialchars($obra['codigo_proy']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <?php 
                                    $badgeColor = 'bg-secondary';
                                    if($obra['estado'] == 'ACTIVO' || $obra['estado'] == 'En Ejecución') $badgeColor = 'bg-primary';
                                    if($obra['estado'] == 'CERRADO' || $obra['estado'] == 'Culminado') $badgeColor = 'bg-success';
                                    if($obra['estado'] == 'Paralizada') $badgeColor = 'bg-danger';
                                ?>
                                <span class="badge <?= $badgeColor ?> px-2 py-1 shadow-sm"><?= htmlspecialchars($obra['estado']) ?></span>
                            </td>
                            
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="fw-bold me-3" style="min-width: 45px;"><?= htmlspecialchars($obra['ultimo_avance_fisico']) ?>%</div>
                                    <div class="progress flex-grow-1 bg-light border shadow-sm" style="height: 8px; max-width: 120px;">
                                        <div class="progress-bar bg-info" style="width: <?= htmlspecialchars($obra['ultimo_avance_fisico']) ?>%"></div>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="pe-4 text-center">
                                <a href="detalle_obra.php?id=<?= $obra['id_obra'] ?>" class="btn btn-primary-custom btn-sm w-100 mb-2 shadow-sm">
                                    <i class="far fa-eye me-1"></i> Detalle
                                </a>
                                
                                <?php if(!empty($obra['codigo_proy'])): ?>
                                <a href="https://ofi5.mef.gob.pe/ssi/ssi/Index?codigo=<?= htmlspecialchars($obra['codigo_proy']) ?>&tipo=1" target="_blank" class="btn btn-outline-secondary btn-sm w-100" style="background: transparent; font-size: 0.75rem;">
                                    SSI MEF <i class="fas fa-external-link-alt ms-1"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="opacity-50 mb-3">
                                    <i class="fas fa-search fa-3x"></i>
                                </div>
                                <h5 class="text-dark fw-bold">No se encontraron resultados</h5>
                                <p class="text-muted">Intenta buscar con otros términos o limpia tu búsqueda actual.</p>
                                <a href="obras.php" class="btn btn-outline-secondary mt-2">Limpiar filtros</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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

</body>
</html>