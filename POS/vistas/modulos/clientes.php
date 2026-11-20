<?php

if($_SESSION["perfil"] == "ventas" || $_SESSION["perfil"] == "bodega" || $_SESSION["perfil"] == "mensajeria"){


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
      
      Administrar clientes
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      
      <li class="active">Administrar clientes</li>
    
    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">
  
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarCliente">
          
          Agregar cliente

        </button>

      </div>

      <div class="box-body">
        
       <table class="table table-bordered table-striped dt-responsive tablas" width="100%">
         
        <thead>
         
         <tr>
           
           
           <th>Codigo</th>
           <th>Nombre</th>
           <th>Cedula</th>
           <th>Email</th>
           <th>Teléfono</th>
           <th>Ingreso al sistema</th>
           <th>Actividad Comercial</th>
           <th>Acciones</th>

         </tr> 

        </thead>

        <tbody>

        <?php

          $item = null;
          $valor = null;

          $clientes = ControladorClientes::ctrMostrarClientes($item, $valor);

          foreach ($clientes as $key => $value) {
            
            if ($value["comentario"]=="**DESACTIVADO**") {
              echo '<tr style="color:red;">';
            }else{
            
              echo '<tr>';
             } 
                  echo'
                    <td>'.$value["codigo"].'</td>

                    <td>'.$value["nombre"].'</td>

                    <td>'.$value["cedula"].'</td>

                    <td>'.$value["correo"].'</td>

                    <td>'.$value["telefono"].'</td>

                    <td>'.$value["afiliacion"].'</td> 

                    <td>'.$value["actividad_comercial"].'</td>            

                    

                    <td>

                      <div class="btn-group">
                          
                        <button class="btn btn-warning btnEditarCliente" data-toggle="modal" data-target="#modalEditarCliente" idCliente="'.$value["codigo"].'"><i class="fa fa-pencil"></i></button>';

                      if($_SESSION["perfil"] == "total" or $_SESSION["perfil"] == "parcial"){

                          echo '<button class="btn btn-danger btnEliminarCliente" idCliente="'.$value["codigo"].'"><i class="fa fa-times"></i></button>';

                      }
                        echo '<button class="btn btn-info btnSaldoCliente" idCliente="'.$value["codigo"].'"><i class="fa fa-balance-scale"></i></button>';
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
MODAL SALDO
======================================-->

<div id="modalSaldo" class="modal fade" tabindex="-1" role="dialog">

    <div class="modal-dialog modal-lg" >

        <div class="modal-content">



            <!--=====================================
            CABEZA DEL MODAL SALDO
            ======================================-->

            <div class="modal-header" style="background:#3c8dbc; color:white">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h4 class="modal-title">Saldo Cliente</h4>

            </div>

            <!--=====================================
            CUERPO DEL MODAL SALDO
            ======================================-->

            <div class="modal-body">
                <div class="modal-dialog modal-dialog-scrollable" role="document">

                    <div class="box-body">



                        <div class="form-group">
                            <center>
                                <div id="resultadoSaldo">

                                    <!-- AQUI VA EL AJAX -->
                                </div>
                            </center>
                        </div>
                    </div>
                </div>




                <!--=====================================
                PIE DEL MODAL SALDO
                ======================================-->

                <div class="modal-footer">

                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

                </div>

            </div>
        </div>
    </div>
</div>
<!--=====================================
MODAL AGREGAR CLIENTE
======================================-->

<div id="modalAgregarCliente" class="modal fade" role="dialog">
  
  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#3c8dbc; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Agregar cliente</h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">

            <!-- ENTRADA PARA EL NOMBRE -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-user"></i></span> 

                <input type="text" class="form-control input-lg" name="nuevoCliente" placeholder="Ingresar nombre" required>

              </div>

            </div>

            <!-- ENTRADA PARA EL DOCUMENTO ID -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-key"></i></span> 

                <input type="number" min="0" class="form-control input-lg" name="nuevoCedula" placeholder="Ingresar # Identificación" required>

              </div>

            </div>

            <!-- ENTRADA PARA EL EMAIL -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span> 

                <input type="email" class="form-control input-lg" name="nuevoCorreo" placeholder="Ingresar email" required>

              </div>

            </div>

            <!-- ENTRADA PARA EL TELÉFONO -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-phone"></i></span> 

                <input type="text" class="form-control input-lg" name="nuevoTelefono" placeholder="Ingresar teléfono"  required>

              </div>

            </div>

            <!-- ENTRADA PARA LA DIRECCIÓN -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-phone"></i></span> 

                <input type="text" class="form-control input-lg" name="nuevaCelular" placeholder="Ingresar Celular"  required>

              </div>

            </div>

             <!-- ENTRADA PARA TIPO IDENTIFICACION -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-globe"></i></span> 
                <select class="form-control input-lg" id="tipoCli" name="tipoCli">
                    <optgroup >
                      <option value="01">01- Cédula Física</option>
                      <option value="02">02- Cédula Jurídica</option>
                      <option value="03">03- DIMEX</option>
                      <option value="04">04- NITE</option>
                    </optgroup>
                </select>

                

              </div>

            </div>

             <!-- ENTRADA PARA ACTIVIDAD COMERCIAL -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-user"></i></span> 

                <input type="text" class="form-control input-lg" name="nuevaActividad" placeholder="Ingresar Actividad Comercial"  required>

              </div>

            </div>
  
          </div>

        </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-primary">Guardar cliente</button>

        </div>

      </form>

      <?php

        $crearCliente = new ControladorClientes();
        $crearCliente -> ctrCrearCliente();

      ?>

    </div>

  </div>

</div>

<!--=====================================
MODAL EDITAR CLIENTE
======================================-->

<div id="modalEditarCliente" class="modal fade" role="dialog">
  
  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#3c8dbc; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Editar cliente</h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">

            <!-- ENTRADA PARA EL NOMBRE -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-user"></i></span> 

                <input type="text" class="form-control input-lg" name="editarCliente" id="editarCliente" required>
                <input type="hidden" id="idCliente" name="idCliente">
              </div>

            </div>

            <!-- ENTRADA PARA EL DOCUMENTO ID -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-key"></i></span> 

                <input type="number" min="0" class="form-control input-lg" name="editarDocumentoId" id="editarDocumentoId" required>

              </div>

            </div>

            <!-- ENTRADA PARA EL EMAIL -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span> 

                <input type="text" class="form-control input-lg" name="editarEmail" id="editarEmail" required>

              </div>

            </div>

            <!-- ENTRADA PARA EL TELÉFONO -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-phone"></i></span> 

                <input type="text" class="form-control input-lg" name="editarTelefono" id="editarTelefono"  required>

              </div>

            </div>

            <!-- ENTRADA PARA CELULAR -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-phone"></i></span> 

                <input type="text" class="form-control input-lg" name="editarCelular" id="editarCelular"   required>

              </div>

            </div>

             <!-- ENTRADA PARA COMENTARIO -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 

                <input type="text" class="form-control input-lg" name="editarComentario" id="editarComentario"   data-mask required>

              </div>

            </div>

              <!-- ENTRADA PARA ACTIVIDAD COMERCIAL -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-user"></i></span> 

                <input type="text" class="form-control input-lg" name="editarActividad" id="editarActividad"   data-mask required>

              </div>

            </div>
              <!-- ENTRADA PARA ACTIVAR -->

              <div class="form-group">
                  <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-pencil-square-o"></i></span>
                      <select class="form-control input-lg" name="Activo" onkeydown="uniKeyCodeTE(event)" onchange="ShowSelected()" id="Activo">
                          <option value="Activado">Activado</option>';
                          <option value="Desactivado">Desactivado</option>';
                      </select>
                  </div>
              </div>
<!--           fin   -->
  
          </div>

        </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-primary">Guardar cambios</button>

        </div>

      </form>

      <?php

        $editarCliente = new ControladorClientes();
        $editarCliente -> ctrEditarCliente();

      ?>

    

    </div>

  </div>

</div>

<?php

  $eliminarCliente = new ControladorClientes();
  $eliminarCliente -> ctrEliminarCliente();

?>
<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Clientes';
</script>

