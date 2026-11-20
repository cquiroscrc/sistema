<?php require 'inc/_global/config.php'; ?>
<?php require 'inc/backend/config.php'; ?>

<?php require 'inc/_global/views/head_start.php'; ?>
<?php require 'inc/_global/views/head_end.php'; ?>
<?php require 'inc/_global/views/page_start.php';?>
<!-- Page Content -->



<div class="content">

    <!-- Datos de la empresa -->
    <div class="row">
        <div class="col-md-12">
            <div class="block">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Pendientes</h3>
                    <div class="block-options">
                        <button type="button  name="bntActualizarTodo"  onclick=""  class="btn btn-alt-primary">
                            <i class="fa fa-save mr-5"></i> Salvar todo
                        </button>
                        <button type="button  name="btnRefreshCP"  onclick=""  class="btn btn-alt-primary">
                        <i class="fa fa-refresh mr-5"></i>Refrescar
                        </button>
                    </div>
                </div>
                <!-- Mega Form -->

                <div class="block" id="datos-proceso">
                    <div class="block-content">
                        <div class="block">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <ul class="nav nav-tabs nav-tabs-block align-items-cente col-12" data-toggle="tabs" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" href="#general">General</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" href="#hacienda">Parametros de Hacienda</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" href="#certificado">Certificado</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" href="#smtp">Serv. Correo</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" href="#docs">Documentos</a>
                                                </li>
                                                <li class="nav-item ml-auto">
                                                    <div class="block-options mr-15">
                                                        <button type="button" class="btn-block-option" data-toggle="block-option" data-action="fullscreen_toggle"></button>
                                                        <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                                                            <i class="si si-refresh"></i>
                                                        </button>
                                                        <button type="button" class="btn-block-option" data-toggle="block-option" data-action="content_toggle"></button>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
                <div class="block-content tab-content">
                    <div class="tab-pane active" id="general" role="tabpanel">
                        <div class="block">
                            <div class="block-content" id="datos-general">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <div class="col-3">
                                                <label for="fecha-inicio">Fecha Inicio</label>
                                                <input type="date" class="form-control  " id="fecha-inicio"
                                                       name="fecha-inicio"" value="">
                                            </div>
                                            <div class="col-3">
                                                <label for="intervalo">Intervalo Envio</label>
                                                <input type="range" class="form-control" id="intervalo"
                                                       name="intervalo" value="">
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                <div class="tab-pane  " id="hacienda" role="tabpanel">
                    <div class="block">
                        <div class="block-content" id="datos-hacienda">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                            <div class="col-3">
                                                <label>Ambiente</label>
                                                <div class="custom-control custom-switch mb-1">
                                                    <input type="checkbox" class="custom-control-input" id="stag" name="stag" checked="">
                                                    <label class="custom-control-label" for="stag">Produccion</label>
                                                </div>
                                            </div>
                                        <div class="col-2">
                                            <label for="fecha-inicio">Codigo Actividad</label>
                                            <input type="number" class="form-control  " id="codigo-actividad"
                                                   name="codigo-actividad" value="">
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="tab-pane  " id="certificado" role="tabpanel">
                    <div class="block">
                        <div class="block-content" id="datos-certificado">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <div class="col-9">

                                                <label for="fecha-inicio">Archivo Certificado(p12)</label>
                                                <input type="file" class="form-control  " id="codigo-actividad"
                                                       name="codigo-actividad" value="">

                                        </div>
                                        <div class="col-3">
                                            <label for="fecha-inicio">PIN</label>
                                            <input type="password" class="form-control  " id="codigo-actividad"
                                                   name="codigo-actividad" value="">
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="tab-pane  " id="smtp" role="tabpanel">
                    <div class="block">
                        <div class="block-content" id="datos-certificado">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <div class="col-4">
                                            <label for="fecha-inicio">Servidor</label>
                                            <input type="server" class="form-control  " id="codigo-actividad"
                                                   name="codigo-actividad" value="">

                                        </div>
                                        <div class="col-3">
                                            <label for="username">Usuario</label>
                                            <input type="text" class="form-control  " id="codigo-actividad"
                                                   name="codigo-actividad" value="">
                                        </div>
                                        <div class="col-3">
                                            <label for="username">Contraseña</label>
                                            <input type="password" class="form-control  " id="codigo-actividad"
                                                   name="codigo-actividad" value="">
                                        </div>
                                        <div class="col-2">
                                            <label for="username">Puerto</label>
                                            <input type="text" class="form-control  " id="codigo-actividad"
                                                   name="codigo-actividad" value="">
                                        </div>

                                    </div>
                                    <div class="form-group row">
                                        <div class="col-3">
                                            <input class="form-check-input" type="checkbox" value="" id="example-checkbox-inline1" name="example-checkbox-inline1">
                                            <label class="form-check-label" for="example-checkbox-inline1">Usar SSL</label>
                                        </div>
                                        <div class="col-3">
                                            <input class="form-check-input" type="checkbox" value="" id="example-checkbox-inline1" name="example-checkbox-inline1">
                                            <label class="form-check-label" for="example-checkbox-inline1">Requiere Autenticacion</label>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="block-options">
                                            <button type="button  name="test"  onclick=""  class="btn btn-alt-primary">
                                            <i class="fa fa-save mr-5"></i> Probar conexion
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>





                        <!--Fin Tercera fila -->

                </div>
                <!-- END Datos de la empresa -->
            </div>
        </div>
    </div>





<!-- END Page Content -->

<?php require 'inc/_global/views/page_end.php'; ?>
<?php require 'inc/_global/views/footer_start.php'; ?>



 <script type="application/javascript">

    $(document).ready(function() {

        //var elems = document.getElementsByClassName("file-caption form-control kv-fileinput-caption");
        //elems[0].style.backgroundColor = "#f2f2f2";

        $("#certificado").fileinput({
            language: 'es',
            showUpload: true,
            dropZoneEnabled: true,
            showPreview: false,
            uploadUrl: "/ADMIN/src/inc/_global/file-upload.php",
            maxFileCount:1,
            allowedFileExtensions: ['p12'],    // allow only images
            showCancel: true,
            initialPreviewAsData: false,
            overwriteInitial: false,
            // initialPreview: [],          // if you have previously uploaded preview files
            // initialPreviewConfig: [],    // if you have previously uploaded preview files
            theme: 'fas',
            deleteUrl: "/ADMIN/src/inc/_global/file-delete.php"
        }).on('fileuploaded', function(event, previewId, index, fileId) {
            console.log('Archivo subido', 'ID: ' + fileId + ', Thumb ID: ' + previewId);
        }).on('fileuploaderror', function(event, data, msg) {
            console.log('File uploaded', msg);
        }).on('filebatchuploadcomplete', function(event, preview, config, tags, extraData) {
            console.log('Archivo por lotes subido', preview, config, tags, extraData);
        });
    });

</script>

<?php require 'inc/_global/views/footer_end.php'; ?>
