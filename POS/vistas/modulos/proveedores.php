<?php

if($_SESSION["perfil"] == "ventas" || $_SESSION["perfil"] == "caja" || $_SESSION["perfil"] == "bodega" || $_SESSION["perfil"] == "mensajeria"){


  echo '<script>

    window.location = "inicio";

  </script>';

  return;

}

?>
<style>
  table.dataTable tr.odd { background-color:#D6D6D6; }
  table.dataTable tr.even { background-color: white; }
</style>


<div class="content-wrapper">

  <section class="content-header">
    
    <h1>
      
      Administrar Proveedores
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      
      <li class="active">Administrar Proveedores</li>
    
    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">
  
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarProveedores">
          
          Agregar Proveedor

        </button>

      </div>

      <div class="box-body">
   
       <table class="table table-bordered table-striped dt-responsive tablasProveedores" width="100%">
         
        <thead>
         
         <tr>
           
           <th>Codigo</th>
           <th>Proveedor</th>
           <th>Corto</th>
           <th>Tipo</th>
           <th>Teléfono</th>
           <th>Dirección</th>
           <th>Acciones</th>

         </tr> 

        </thead>

        <tbody>

        <?php

          $item = null;
          $valor = null;

          $proveedores = ControladorProveedores::ctrMostrarProveedores($item, $valor);

          foreach ($proveedores as $key => $value) {
           
            echo ' <tr>

                    <td>'.$value["codigo"].'</td>

                    <td class="text-uppercase">'.$value["nombre"].'</td>
                    <td class="text-uppercase">'.$value["shortname"].'</td>
                    <td class="text-uppercase">'.$value["tipo"].'</td>
                    <td class="text-uppercase">'.$value["telefono"].'</td>
                    <td class="text-uppercase">'.$value["direccion"].'</td>

                    <td>

                      <div class="btn-group">
                          
                        <button class="btn btn-warning btnEditarProveedores" idProveedores="'.$value["codigo"].'" data-toggle="modal" data-target="#modalEditarProveedores"><i class="fa fa-pencil"></i></button>';

                        if($_SESSION["perfil"] == "total"){

                          echo '<button class="btn btn-danger btnEliminarProveedores" idProveedores="'.$value["codigo"].'"><i class="fa fa-times"></i></button>';

                        }

                      echo '</div>  

                    </td>

                  </tr>';
          }

        ?>

        </tbody>

       </table>

      </div>

    </div>

  </section>

</div>

<!--=====================================
MODAL AGREGAR CATEGORÍA
======================================-->

<div id="modalAgregarProveedores" class="modal fade" role="dialog">
  
  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#3c8dbc; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Agregar Proveedor</h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">
            
            <!-- ENTRADA PARA EL CODIGO -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-truck"></i></span> 

                <input type="text" class="form-control input-lg" name="nuevoCodigo" placeholder="Ingresar Código" required>

              </div>

            </div>
            

            <!-- ENTRADA PARA EL NOMBRE -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="ion ion-android-archive"></i></span> 

                <input type="text" class="form-control input-lg" name="nuevoProveedor" placeholder="Ingresar Nombre" required>

              </div>

            </div>
            
            <!-- ENTRADA PARA DIRECCION -->
            
            <div class="form-group">
              
              <div class="input-group">
                
                <span class="input-group-addon"><i class="fa fa-map-pin"></i></span> 

                <input type="text" class="form-control input-lg" name="nuevoDireccion" placeholder="Ingresar la dirección" required>

              </div>

            </div>
            

            <!-- ENTRADA PARA TELEFONO -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-phone"></i></span> 

                <input type="text" class="form-control input-lg" name="nuevoTelefono" placeholder="Ingresar el teléfono" required>

              </div>

            </div>


            <!-- ENTRADA PARA PAIS -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="ion ion-android-globe"></i></span> 

                <input type="text" class="form-control input-lg" name="nuevoPais" placeholder="Ingresar el país" required>

              </div>

            </div>


            <!-- ENTRADA PARA CREDITO -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="ion ion-android-calendar"></i></span> 

                <input type="text" class="form-control input-lg" name="nuevoCredito" placeholder="Ingresar el crédito" required>

              </div>

            </div>


            <!-- ENTRADA PARA COMENTARIO -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-th"></i></span> 

                <input type="text" class="form-control input-lg" name="nuevoComentarios" placeholder="Ingresar comentario" required>

              </div>

            </div>

            <!-- ENTRADA PARA SHORTNAME -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-cut"></i></span> 

                <input type="text" class="form-control input-lg" name="nuevoShortname" placeholder="Ingresar el nombre corto" required>

              </div>

            </div>
            <!-- ENTRADA PARA SELECCIONAR TIPO -->

            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-globe"></i></span> 

                <select class="form-control input-lg" name="nuevoTipo">
                  
                 <option value="">Selecionar tipo</option>

                  <option value="L">Local</option>

                  <option value="E">Extrangero</option>

                </select>

              </div>

            </div>

            <!-- jhkhd -->
          </div>

        </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-primary">Guardar Proveedor</button>

        </div>

        <?php

          $crearProveedor = new ControladorProveedores();
          $crearProveedor -> ctrCrearProveedores();

        ?>

      </form>

    </div>

  </div>

</div>

<!--=====================================
MODAL EDITAR CATEGORÍA
======================================-->

<div id="modalEditarProveedores" class="modal fade" role="dialog">
  
  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#3c8dbc; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Editar proveedor</h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">

<!-- ENTRADA PARA EL CODIGO -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-truck"></i></span> 

                <input type="text" class="form-control input-lg" id="editarCodigo" name="editarCodigo" readonly>

              </div>

            </div>
            

            <!-- ENTRADA PARA EL NOMBRE -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="ion ion-android-archive"></i></span> 

                <input type="text" class="form-control input-lg" id="editarProveedor" name="editarProveedor" value="" required>

              </div>

            </div>
            
            <!-- ENTRADA PARA DIRECCION -->
            
            <div class="form-group">
              
              <div class="input-group">
                
                <span class="input-group-addon"><i class="fa fa-map-pin"></i></span> 

                <input type="text" class="form-control input-lg" id="editarDireccion" name="editarDireccion" value="" required>

              </div>

            </div>
            

            <!-- ENTRADA PARA TELEFONO -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-phone"></i></span> 

                <input type="text" class="form-control input-lg" id="editarTelefono" name="editarTelefono" value="" required>

              </div>

            </div>


            <!-- ENTRADA PARA PAIS -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="ion ion-android-globe"></i></span> 

                <input type="text" class="form-control input-lg" id="editarPais" name="editarPais" value="" required>

              </div>

            </div>


            <!-- ENTRADA PARA CREDITO -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="ion ion-android-calendar"></i></span> 

                <input type="text" class="form-control input-lg" id="editarCredito" name="editarCredito" value="" required>

              </div>

            </div>


            <!-- ENTRADA PARA COMENTARIO -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-th"></i></span> 

                <input type="text" class="form-control input-lg" id="editarComentarios" name="editarComentarios" value="" required>

              </div>

            </div>

            <!-- ENTRADA PARA SHORTNAME -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-cut"></i></span> 

                <input type="text" class="form-control input-lg" id="editarShortname" name="editarShortname" value="" required>

              </div>

            </div>
            <!-- ENTRADA PARA SELECCIONAR TIPO -->

            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-globe"></i></span> 

                <select class="form-control input-lg" name="editarTipo">
                  
                 <option value="" id="editarTipo"></option>

                  <option value="L">Local</option>

                  <option value="E">Extrangero</option>

                </select>

              </div>

            </div>
          </div>

        </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-primary">Guardar cambios</button>

        </div>

      <?php

          $editarProveedor = new ControladorProveedores();
          $editarProveedor -> ctrEditarProveedores();

        ?> 

      </form>

    </div>

  </div>

</div>

<?php

  $borrarProveedor = new ControladorProveedores();
  $borrarProveedor -> ctrBorrarProveedores();

?>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Proveedores';
</script>

