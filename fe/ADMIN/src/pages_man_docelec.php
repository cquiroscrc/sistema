<?php require 'inc/_global/config.php'; ?>
<?php require 'inc/backend/config.php'; ?>
<?php require 'inc/_global/views/head_start.php'; ?>
<?php require 'inc/_global/views/head_end.php'; 
 
 
?>
 
 
<!-- Add your custom scripts here -->

<?php require 'inc/_global/views/page_start.php';?>

<!-- Page Content -->
<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="block">
                <div class="block-header block-header-default">
                   
                    <div class="block-options">
                        <div class="btn-toolbar mb-2" role="toolbar" aria-label="Icons Toolbar with button groups">
                            <div class="btn-group mr-2 mb-2" role="group" aria-label="Icons File group">
                                <button type="button" id="btnRefrescar" class="btn btn-primary">
                                    <i class="far fa-database  fa-fw fa-pull-left"></i> Refrescar
                                </button>
                                <button type="button" id="btnEnviar" class="btn btn-primary">
                                    <i class="far fa fa-paper-plane-o fa-pull-left"></i> Reenviar
                                </button>
                            </div>
                            <div class="btn-group mr-2 mb-2" role="group" aria-label="Icons Text group">
                                <button type="button"  id="btnCorreo" class="btn btn-primary">
                                    <i class="far fa fa-envelope-o fa-pull-left"></i> Reenviar correo
                                </button>
                            </div>

                          <div class="btn-group mr-2 mb-2" role="group" aria-label="Icons Text group">
                                <button type="button" class="btn btn-primary">
                                    Desde                                                                        
                                </button>
                                <button type="button" class="btn btn-primary">                                                                    
                                    <input id="fechaInicio"  style="width: 100%;height:50%" />
                                </button>                                
                                <button type="button" class="btn btn-primary">
                                    Hasta                                                                        
                                </button>
                                <button type="button" class="btn btn-primary">                                     
                                    <input id="fechaFinal"  style="width: 100%;height:50%" />
                                </button>
                            </div>
                            
                            
                        </div>
                    </div>
                </div>               
            </div>
            <div id="grid"></div>

             
             </div>
        </div>


        
    </div>

    <script>

     $(document).ready(function() {
            
            showDocElec('empty');

            $("#btnRefrescar").click( function() {
                    showDocElec('refresh');
                });
            
                $("#btnRefrescar").click( function() {
                    sendDocElec();
                });

            $("#fechaInicio").kendoDateTimePicker({
                value: new Date(),
                dateInput: true,
                format: "dd/MM/yyyy hh:mm:ss"                
            });

            $("#fechaFinal").kendoDateTimePicker({
                value: new Date(),
                dateInput: true,
                format: "dd/MM/yyyy hh:mm:ss"            
            });

            
                     
            var datetimepicker = $("#fechaInicio").data("kendoDateTimePicker");
            var prevWeek = moment().add(-7, 'days');
            datetimepicker.value(prevWeek.toDate());

             
        });
    </script>

<!-- END Page Content -->

<?php require 'inc/_global/views/page_end.php'; ?>
<?php require 'inc/_global/views/footer_start.php'; ?>

     
<?php require 'inc/_global/views/footer_end.php'; ?>
