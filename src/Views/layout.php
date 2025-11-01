<!-- index.php en src/Views/ -->
 <!-- template base para todas las vistas -->

 <?php require_once __DIR__ . '/shared/header.php'; ?>
    <div class="contenedor-app">
        <div class="app">
            <?php echo $contenido; ?>
        </div>
    </div>

<?php require_once __DIR__ . '/shared/footer.php'; ?>

  