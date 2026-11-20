<?php require 'inc/_global/config.php'; ?>
<?php require 'inc/backend/config.php'; ?>
<?php require 'inc/_global/views/head_start.php'; ?>
<?php require 'inc/_global/views/head_end.php'; 
 
 
?>
 
 
<!-- Add your custom scripts here -->

<?php require 'inc/_global/views/page_start.php';?>
<!-- Page Content -->



<div class="content">

    <!-- Datos de la empresa -->
    <div class="row">
        <div class="col-md-12">
            <div class="block">
                <div class="block-header block-header-default">
                   
                    <div class="block-options">
                        <div class="btn-toolbar mb-2" role="toolbar" aria-label="Icons Toolbar with button groups">
                            <div class="btn-group mr-2 mb-2" role="group" aria-label="Icons File group">
                                <button type="button" class="btn btn-primary">
                                    <i class="far fa-database  fa-fw fa-pull-left"></i> Refrescar
                                </button>
                                <button type="button" class="btn btn-primary">
                                    <i class="far fa fa-paper-plane-o fa-pull-left"></i> Reenviar
                                </button>
                            </div>
                            <div class="btn-group mr-2 mb-2" role="group" aria-label="Icons Text group">
                                <button type="button" class="btn btn-primary">
                                    <i class="far fa fa-envelope-o fa-pull-left"></i> Reenvia correo
                                </button>
                            </div>

                          <div class="btn-group mr-2 mb-2" role="group" aria-label="Icons Text group">
                                <button type="button" class="btn btn-primary">
                                    <i class="far fa fa-calendar-alt fa-pull-left"></i> Desde                                    
                                </button>
                                <input type="text" class="js-flatpickr  bg-white js-flatpickr-enabled 
                                flatpickr-input " id="dtDesde" name="dtDesde" 
                                placeholder="d-m-Y" data-date-format="d-m-Y"  >
                               
                                <button type="button" class="btn btn-primary">
                                    <i class="fa fa fa-calendar-alt fa-pull-left"></i> Hasta
                                </button>
                                <div class="input-group date">
                                    <input type="date" class="form-control" value="12-02-2012">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                </div>

                                

                            </div>
                            
                        </div>
                    </div>
                </div>               
            </div>
        </div>

        <?php
         
        ?>
 
             
        </div>
    </div>


 

<!-- END Page Content -->

<?php require 'inc/_global/views/page_end.php'; ?>
<?php require 'inc/_global/views/footer_start.php'; ?>


<?php require 'inc/_global/views/footer_end.php'; ?>
