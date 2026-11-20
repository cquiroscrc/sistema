<?php 
session_start();
date_default_timezone_set("America/Costa_Rica"); // pone la hora correctamente
$error = 1;
    if((isset($_GET['error']))&&(!(empty($_GET['error'])))){
        $message = "Datos erroneos, digitelos de nuevo";
        echo "<script type='text/javascript'>alert('$message');</script>";
        // echo "<center><h3><span class='number' style='background-color:red'>!!! </span> Datos erroneos <span class='number' style='background-color:red'>!!!</span></h3></center>";
        //$resultado = $_GET['resultado'];
    }

    if(isset($_SESSION['profile']['nombre'])){
        $admin=true;
    }else {
        header("location: /POS/index.php");
        $admin=false;
    }




include "menu.php";
 ?>
<!--
<div id="fb-root"></div>
<script>(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/es_LA/sdk.js#xfbml=1&version=v2.8&appId=231598086880024";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
-->
 <style>
    body {
        
        background-image: url(img/logos.png);
        
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-size: cover;
    }
    .left{
        float: left;
        width: 300px;
        height: 1000px;
    }
    </style>
    <!--
    <div class="left">
        <div class="fb-page" data-href="https://www.facebook.com/LosPaticas/" data-tabs="timeline" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/LosPaticas/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/LosPaticas/">LOS PATICAS.</a></blockquote></div>
    </div>
    -->

    <div class="container">

        <div class="row">
            <div class="col-lg-12 text-center imagen img-responsive">
            

                <!-- /.Contenido -->
                
            </div>
        </div>
    
    </div>
    <!-- /.container -->

    <!-- jQuery Version 1.11.1 -->
    

    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

</body>

</html>
