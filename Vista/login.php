<?php include_once('Estructura/cabecera.php'); ?>
<div class="d-flex justify-content-center align-items-center" style="height: 70vh;">
    <div class="card shadow p-4" style="width: 400px;">
        <h3 class="text-center mb-3">Iniciar Sesión</h3>
        <form action="../Acciones/login/accionLogin.php" method="post">
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input type="text" name="usnombre" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="uspass" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
        </form>
        <?php 
        if(isset($_GET['error'])){
            echo '<div class="alert alert-danger mt-3">Usuario o contraseña incorrectos</div>';
        }
        ?>
    </div>
</div>
<?php include_once('Estructura/pie.php'); ?>