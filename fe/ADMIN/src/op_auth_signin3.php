<?php require 'inc/_global/config.php';?>
<?php require 'inc/_global/views/head_start.php';?>
<?php require 'inc/_global/views/head_end.php';?>
<?php require 'inc/_global/views/page_start.php';?>
<?php require_once 'inc/_classes/redis.php'; ?>
<?php require 'inc/_classes/pdo.php';?>
<?php require 'inc/_classes/auth.php';?>


<?php

use App\Utils\RedisUtils;


$redis=new App\Utils\RedisUtils();


if (!empty($_POST['login-username']) && !empty($_POST['login-password'])) {
    $cn =  new \App\Data\pdoCN();
    $user= new Login($cn->pdo);
    if ($user->checkCredentials($_POST['login-username'], $_POST['login-password'])) {
        //Guarda el usuario para uso posterior
        $redis->setKey('login-username',$_POST['login-username']);
        $_SESSION['login-username'] = $_POST['login-username'];
        header('Location:pages_dashboard_api.php');
        exit();
    }
    else{
        $_SESSION['usuario'] = FALSE;
        $_SESSION['password'] = FALSE;
        modalWindow("Error, su usuario o contrasena son invalidos");
    }

}

function modalWindow($message){
    echo "
    <!-- Modal para el error de conexion -->
    <div class='modal fade' id='modal-conn' tabindex='-1' role='dialog' aria-labelledby='modal-conn' aria-hidden='true'>
        <div class='modal-dialog' role='document'>
            <div class='modal-content'>
                <div class='block block-themed block-transparent mb-0'>
                    <div class='block-header bg-primary-dark'>
                        <h3 class='block-title'>Mensaje del Sistema</h3>
                        <div class='block-options'>
                            <button type='button' class='btn-block-option' data-dismiss='modal' aria-label='Close'>
                                <i class='si si-close'></i>
                            </button>
                        </div>
                    </div>
                    <div class='block-content'>
                        <p>{$message}</p>
                    </div>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-alt-secondary' data-dismiss='modal'>Aceptar</button>
                    
                </div>
            </div>
        </div>
    </div>
    <!-- END Fade In Modal -->
    ";

}
?>
<!-- Page Content -->
<div class="bg-body-dark bg-pattern" style="background-image: url('<?php echo $cb->assets_folder; ?>/media/various/bg-pattern-inverse.png');">
    <div class="row mx-0 justify-content-center">
        <div class="hero-static col-lg-6 col-xl-4">
            <div class="content content-full overflow-hidden">
                <!-- Header -->
                <div class="py-30 text-center">
                    <a class="link-effect font-w700" href="index.php">
                        <i class="si si-fire"></i>
                        <span class="font-size-xl text-primary-dark">Pati</span><span class="font-size-xl">cas</span>
                    </a>
                    <h1 class="h4 font-w700 mt-30 mb-10">Bienvenido</h1>
                
                </div>
                <!-- END Header -->

                <!-- Sign In Form -->
                <!-- jQuery Validation functionality is initialized with .js-validation-signin class in js/pages/op_auth_signin.min.js which was auto compiled from _es6/pages/op_auth_signin.js -->
                <!-- For more examples you can check out https://github.com/jzaefferer/jquery-validation -->
                <form class="js-validation-signin" action="" method="post">
                    <div class="block block-themed block-rounded block-shadow">
                        <div class="block-header bg-gd-dusk">
                            <h3 class="block-title">Por favor ingrese</h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option">
                                    <i class="si si-wrench"></i>
                                </button>
                            </div>
                        </div>
                        <div class="block-content">
                            <div class="form-group row">
                                <div class="col-12">
                                    <label for="login-username">Usuario</label>
                                    <input type="text" class="form-control" id="login-username" name="login-username">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-12">
                                    <label for="login-password">Contraseña</label>
                                    <input type="password" class="form-control" id="login-password" name="login-password">
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                
                                <div class="col-sm-12 text-sm-right push">
                                    <button type="submit" class="btn btn-alt-primary">
                                        <i class="si si-login mr-10"></i> Ingresar
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="block-content bg-body-light">
                            <div class="form-group text-center">
                                <a class="link-effect text-muted mr-10 mb-5 d-inline-block" href="original/op_auth_signup3.php">
                                    <i class="fa fa-plus mr-5"></i> Crear usuario
                                </a>
                                <a class="link-effect text-muted mr-10 mb-5 d-inline-block" href="original/op_auth_reminder3.php">
                                    <i class="fa fa-warning mr-5"></i> Olvide mi clave
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- END Sign In Form -->
            </div>
        </div>
    </div>
</div>

<!-- END Page Content -->

<?php require 'inc/_global/views/page_end.php'; ?>
<?php require 'inc/_global/views/footer_start.php'; ?>

<!-- Page JS Plugins -->
<?php $cb->get_js('js/plugins/jquery-validation/jquery.validate.min.js'); ?>

<!-- Page JS Code -->
<?php $cb->get_js('js/pages/op_auth_signin.min.js'); ?>

<?php require 'inc/_global/views/footer_end.php'; ?>
