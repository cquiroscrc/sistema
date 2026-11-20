<?php 

?>




<HTML>
	<HEAD>
		<TITLE>Página en blanco</TITLE>
	</HEAD>

	<BODY>
		<div class="content-wrapper">
			
		
		<!--=====================================
		MODAL PREFACTURA
		
		<!-- Modal -->
		  <div class="modal fade" id="myModal" role="dialog">
		    <div class="modal-dialog">
		    
		      <!-- Modal content-->
		      <div class="modal-content">
		        <div class="modal-header">
		          <button type="button" class="close" data-dismiss="modal">&times;</button>
		          <h3 class="modal-title">Prefacturas salvadas</h3>
		        </div>
		        <div class="modal-body">

		          <p><i class="ion ion-ios-paper"></i> Seleccione la prefactura que desee continuar</p>
		              <center>
		              <div class=" input-group">
		              	
		                <table border="1" id="example" class="display" cellspacing="0" cellpadding="2">
					        
					            <tr>
					              <th style="text-align:center;" width="50"> Prefactura </th>
					              <th style="text-align:center;" width="50"> Vendedor </th>
					              <th style="text-align:center;" width="50"> Cliente </th>
					              <th style="text-align:center;" width="220">Fecha</th>
					            </tr>
					        
		                  <?php 
		                  		$valor = "SELECT DISTINCT Factura , fecha, codcliente, vendedor FROM hpregeneral ORDER BY fecha";
								$prefactura = ControladorUtilitaria::ctrMostrarUtilitariaVar($valor);
								$entro=0;
								foreach ($prefactura as $key => $value) {

									echo '<tr>';

						            echo'  <td>
						                      <button class="btn btn-success" id="sel_prefactura" onclick="myPrefactura('."'".$value["Factura"]."'".')">'.$value["Factura"].'</button> 
						                    </td>'; 

						               echo'<td style="text-align:right;">'.$value["vendedor"].'</td>

						                    <td style="text-align:right;">'.$value["codcliente"].'</td>

						                    <td style="text-align:right;">'.$value["fecha"].'</td>

						                  </tr>';

								 
									$entro=1;
								}

							
				            
		                   ?>
		                  </table>
		              

		              </div>
					</center>
		           
		            

		        </div>
		        <div class="modal-footer">
		          <button type="submit" class="btn btn-primary">Cargar</button> 	
		          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
		        </div>
		      </div>
		      
		    </div>
		  </div>
  
</div>

<script>
	$("#myModal").on('hidden.bs.modal', function () {
                window.location.replace('factura');
            });


</script>



	</BODY>
</HTML>

<?php 
if ($entro==1){
	
?>	
	<script>
		$("#myModal").modal("show");
	</script>
<?php 
}else{
?>
	<script> 
		window.location.replace('factura'); 
	</script>
<?php 

}


 ?>

