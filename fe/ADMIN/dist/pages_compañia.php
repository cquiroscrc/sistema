<?php require 'inc/_global/config.php'; ?>
<?php require 'inc/backend/config.php'; ?>

<?php require 'inc/_global/views/head_start.php'; ?>
<?php require 'inc/_global/views/head_end.php'; ?>
<?php require 'inc/_global/views/page_start.php';?>
<!-- Page Content -->

<div class="content">
    <!-- Bootstrap Design -->
    <h2 class="content-heading">Administración</h2>
    <!-- Datos de la empresa -->
    <div class="row">
        <div class="col-md-12">
            <div class="block">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Datos de la empresa</h3>
                    <div class="block-options">
                        <button type="button  name="bntActualizarTodo"  onclick="compannyUpdateInformation();"  class="btn btn-alt-primary">
                            <i class="fa fa-save mr-5"></i> Salvar todo
                        </button>
                        <button type="button  name="btnRefreshCP"  onclick="Refrescar('DatosEmpresa');"  class="btn btn-alt-primary">
                        <i class="fa fa-refresh mr-5"></i>
                        </button>
                    </div>
                </div>
                <!-- Mega Form -->

                <div class="block" id="datos-empresa">
                    <div class="block-content">
                            <form action="" method="post" onsubmit="return false;">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <div class="col-5">
                                                <label for="nombre">Nombre</label>
                                                <input type="text" class="form-control  " id="nombre"
                                                       name="nombre" value="">
                                            </div>

                                            <div class="col-5">
                                                <label for="nombre-comercial">Nombre Comercial</label>
                                                <input type="text" class="form-control  " id="nombre-comercial"
                                                       name="nombre-comercial" value="">
                                            </div>
                                            <div class="col-2">
                                                <label for="codigo-pais">Codigo Pais</label>
                                                <input type="text" class="form-control   form-control" id="codigo-pais"
                                                       name="codigo-pais" value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--Fin de primera fila -->

                                <!--Segunda fila -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <div class="col-3">
                                                <label for="telefono">Telefono</label>
                                                <input type="text" class="form-control" id="telefono"
                                                       name="telefono" value="">
                                            </div>
                                            <div class="col-3">
                                                <label for="telefono2">Telefono 2 o Fax</label>
                                                <input type="text" class="form-control  " id="telefono2"
                                                       name="telefono2" value="">
                                            </div>
                                            <div class="col-4">
                                                <label for="correo">Correo</label>
                                                <input type="email" class="form-control " id="correo"
                                                       name="correo" value="">
                                            </div>
                                            <div class="col-2">
                                                <label for="gln">GLN</label>
                                                <input type="text" class="form-control " id="gln" name="gln" value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <div class="col-3">
                                                <label for="tipo-cedula">Tipo de Cedula</label>
                                                <select class="form-control " id="tipo-cedula" name="tipo-cedula">
                                                    <option value="00">Seleccione una opcion</option>
                                                </select>
                                            </div>
                                            <div class="col-3">
                                                <label for="gln">NIT</label>
                                                <input type="text" class="form-control " id="nit" name="nit" value="">
                                            </div>
                                            <div class="col-4">
                                                <label for="correo-envios">Correo Envios</label>
                                                <input type="email" class="form-control " id="correo-envios"
                                                       name="correo-envios" value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                        <!--Fin Tercera fila -->
                        </form>
                    </div>
                </div>
                <!-- END Datos de la empresa -->
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="block">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Datos de Ubicacion</h3>
                    <div class="block-options">
                    </div>
                </div>
                <div class="block">
                    <div class="block-content" id="datos-ubicacion">
                        <form action="" method="post" onsubmit="return false;">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <div class="col-2">
                                            <label for="codigo-pais-iso">Codigo ISO</label>
                                            <input type="text" class="form-control  " id="codigo-pais-iso"
                                                   name="codigo-pais-iso" value="">
                                        </div>

                                        <div class="col-2">
                                            <label for="provincia">Provincia</label>
                                            <select class="form-control " id="provincia" name="provincia">
                                            </select>
                                        </div>
                                        <div class="col-3">
                                            <label for="canton">Canton</label>
                                            <select class="form-control " id="canton" name="canton">
                                            </select>
                                        </div>
                                        <div class="col-2">
                                            <label for="distrito">Distrito</label>
                                            <select class="form-control " id="distrito" name="distrito">
                                            </select>
                                        </div>
                                        <div class="col-3">
                                            <label for="barrio">Barrio</label>
                                            <select class="form-control " id="barrio" name="barrio">
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label for="otras-senas">Otras señas</label>
                                            <input type="text" class="form-control " id="otras-senas" name="otras-senas">
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
            </div>
        </div>
    </div>
</div>

    <div class="row">
        <div class="col-md-12">
            <div class="block">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Datos de Seguridad</h3>
                    <div class="block-options">
                    </div>
                </div>
                <div class="block">
                    <div class="block-content" id="datos-ubicacion">
                          <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <div class="col-3">
                                        <label for="normativa">Normativa</label>
                                        <input type="text" class="form-control " id="normativa" name="normativa">
                                    </div>
                                    <div class="col-3">
                                        <label for="fecha-normativa">Fecha Normativa</label>
                                        <input type="date" class="form-control " id="fecha-normativa"
                                               name="fecha-normativa">
                                    </div>

                                </div>
                            </div>
                          </div>
                </div>
            </div>
        </div>
    </div>



<!-- END Page Content -->

<?php require 'inc/_global/views/page_end.php'; ?>
<?php require 'inc/_global/views/footer_start.php'; ?>



 <script type="application/javascript">

    $(document).ready(function() {


        $('#certificado').fileinput({
            theme: 'fas',
            language: 'es',
            uploadUrl: '#',
            allowedFileExtensions: ['p12'],
            showUpload: false,
            dropZoneEnabled: false,
            showPreview: false
        });

        Refrescar("DatosEmpresa");

        function EjecutarCompanyInf() {
            // second function code here
            Api_getCompannyInformationAdmin();
        }

        $(document).bind('RefrescarCompletado', EjecutarCompanyInf);


        //Con la funcion siguiente se obtienen datos de la empresa y se asignan a la vez los tipos de identificacion


        $("#provincia").change(function () {
            GetCantones($("#provincia").val(),"", 'canton');
        });
        $("#canton").change(function () {
           GetDistritos($("#provincia").val(), $("#canton").val(),"", 'distrito');
        });
        $("#distrito").change(function () {
           GetBarrios($("#provincia").val(), $("#canton").val(), $("#distrito").val(),"", 'barrio');
        });

    });
</script>

<?php require 'inc/_global/views/footer_end.php'; ?>
