<?php



?>
<style>
  table.dataTable tr.odd { background-color:#3c8dbc; }
  table.dataTable tr.odd { color:white; }

  table.dataTable tr.even { background-color: white; }
</style>

<div class="content-wrapper">

  <section class="content-header">
    
    <h1>
      
      Administrar placas
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      
      <li class="active">Administrar Placas</li>
    
    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">
  
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarPlaca">
          
          Agregar placa

        </button>

      </div>

      <div class="box-body">
        
       <table class="table table-bordered table-striped dt-responsive tablas" width="100%">
         
        <thead>
         
         <tr>
           
           
           <th>Marca</th>
           <th>Tipo</th>
           <th>Año</th>
           <th>Placa</th>
           <th>Vin</th>
           <th>Motor</th>
           <th>Adicional</th>
           

         </tr> 

        </thead>

        <tbody>

        <?php

          $valor = "SELECT * from VAG order by Marca,Tipo, Year,Placa";
          $placas = ControladorUtilitaria::ctrMostrarUtilitariaVar($valor);
          foreach ($placas as $key => $value) {
            

            echo '<tr>

                    <td>'.$value["Marca"].'</td>

                    <td>'.$value["Tipo"].'</td>

                    <td>'.$value["Year"].'</td>

                    <td>'.$value["Placa"].'</td>

                    <td>'.$value["Vin"].'</td>

                    <td>'.$value["Motor"].'</td>

                    <td>'.$value["Tipo2"].'</td>             


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
MODAL AGREGAR PLACA
======================================-->

<div id="modalAgregarPlaca" class="modal fade" role="dialog">
  
  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#3c8dbc; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Agregar Placa</h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">

            <!-- ENTRADA PARA LA PLACA -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-car"></i></span> 

                <input type="text" class="form-control input-lg" name="nuevoPlaca" placeholder="Ingresar la placa" required>

              </div>

            </div>

            <!-- ENTRADA PARA LA MARCA -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-object-group"></i></span> 

                <input type="text" min="0" class="form-control input-lg" name="nuevoMarca" placeholder="Ingresar Marca" required>

              </div>

            </div>

            <!-- ENTRADA PARA EL TIPO -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-random"></i></span> 

                <input type="text" min="0" class="form-control input-lg" name="nuevoTipo" placeholder="Ingresar Tipo" required>

              </div>

            </div>

            <!-- ENTRADA PARA EL AÑO -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 

                <input type="text" class="form-control input-lg" name="nuevoYear" placeholder="Ingresar año" required>

              </div>

            </div>

            <!-- ENTRADA PARA EL VIN -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-credit-card"></i></span> 

                <input type="text" class="form-control input-lg" name="nuevoVin" placeholder="Ingresar número de Vin"  required>

              </div>

            </div>

            <!-- ENTRADA PARA EL MOTOR -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-cogs"></i></span> 

                <input type="text" class="form-control input-lg" name="nuevaMotor" placeholder="Ingresar el Motor" required>

              </div>

            </div>

             <!-- ENTRADA PARA EL ADICIONAL -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-plus"></i></span> 

                <input type="text" class="form-control input-lg" name="nuevoAdicional" placeholder="Ingresar adicional (Cilindrada)" required>

              </div>

            </div>
  
          </div>

        </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-primary">Guardar Placa</button>

        </div>

      </form>

      <?php

        $crearPlaca = new ControladorPlacas();
        $crearPlaca -> ctrCrearPlacas();

      ?>

    </div>

  </div>

</div>

<script type="text/javascript" language="JavaScript">
    document.title = 'Sistema / Placas';
</script>



