<aside class="main-sidebar">

    <section class="sidebar">

        <ul style="font-size: 18px;" class="sidebar-menu">

            <?php


            //======== Inicio
            echo '<li>
				<a href="inicio">
					<img src="vistas/img/plantilla/home.png" style="width:24px;height:24px;">
					<span>Inicio</span>
						<i class="fa fa-angle-left pull-right"></i>
				</a>
			</li>';


            if ($_SESSION["perfil"] == "total" || $_SESSION["perfil"] == "ventas" || $_SESSION["perfil"] == "parcial" || $_SESSION["perfil"] == "bodega" || $_SESSION["perfil"] == "especial" || $_SESSION["perfil"] == "caja") {
                //***** FACTURACION ****
                echo '<li>
				<a href="factura">
					<img src="vistas/img/plantilla/cart.png" style="width:24px;height:24px;">
					<span>Facturar</span>

				</a>
			</li>';
                //***** ARCHIVOS ****
                echo '<li class="treeview">
				<a href="#">						
					<img src="vistas/img/plantilla/folder.png" style="width:24px;height:24px;">
					<span>Archivos</span>
						<i class="fa fa-angle-left pull-right"></i>
				</a>
				<ul class="treeview-menu">';
//                Archivos
                echo '<li>
                    <a href="archivos">
                        <i class="fa fa-folder-open-o"></i>
                        <span>Proformas/Ordenes</span>
                    </a>
			    </li>';
//               Ventas perdidas
                echo '<li>
                    <a href="perdidas">
                        <i class="fa fa-map-pin"></i>
                        <span>Ventas Perdidas</span>
                    </a>
			    </li>';
                //======== Cierra menu
                echo '</ul>
				</li>';

            }

            if ($_SESSION["perfil"] == "total" || $_SESSION["perfil"] == "parcial") {
                //***** MENSAJERIA ****
                echo '<li>
					<a href="mensajeria">
						<img src="vistas/img/plantilla/mensajeria.png" style="width:24px;height:24px;">
						<span>Mensajería</span>
					</a>
				</li>';
            }
            if ($_SESSION["perfil"] == "total" || $_SESSION["perfil"] == "parcial" || $_SESSION["perfil"] == "bodega" ||  $_SESSION["perfil"] == "caja" ||  $_SESSION["perfil"] == "ventas") {
                //***** BODEGA ****
                echo '<li class="treeview">
				<a href="#">						
					<img src="vistas/img/plantilla/bodega.png" style="width:24px;height:24px;">
					<span>Bodega</span>
						<i class="fa fa-angle-left pull-right"></i>
				</a>
				<ul class="treeview-menu">';
                //Ajustes
                echo '<li>
					<a href="ajustes">
						<i class="ion ion-android-options"></i>
						<span>Ajustes</span>
					</a>
				</li>';
                //Etiquetas
                echo '<li>
					<a href="etiqueta">
						<i class="ion ion-ios-pricetag"></i>
						<span>Etiquetas</span>
					</a>
				</li>';
                //Sucursal
                echo '<li>
					<a href="sucursal">
						<i class="ion ion-social-dropbox"></i>
						<span>Sucursal</span>
					</a>
				</li>';
                //Bodega
                echo '<li>
					<a href="bodega">
						<i class="ion ion-android-exit"></i>
						<span>Bodega</span>
					</a>
				</li>';
                //Ubicaciones
                echo '<li>
					<a href="ubicaciones">
						<i class="ion ion-location"></i>
						<span>Ubicaciones</span>
					</a>
				</li>';
                //Ventas diarias
                echo '<li>
					<a href="diarias">
						<i class="ion ion-ios-calendar"></i>
						<span>Ventas Diarias</span>
					</a>
				</li>';


                //======== Cierra menu
                echo '</ul></li>';
            }

            if ($_SESSION["perfil"] == "total" || $_SESSION["perfil"] == "parcial" || $_SESSION["perfil"] == "caja" || $_SESSION["perfil"] == "especial" ) {
                //***** ADMINISTRATIVO ****
                echo '<li class="treeview">
				<a href="#">						
					<img src="vistas/img/plantilla/caja.png" style="width:24px;height:24px;">
					<span>Caja</span>
						<i class="fa fa-angle-left pull-right"></i>
				</a>
				<ul class="treeview-menu">';
                //Administrar ventas
            if ($_SESSION["perfil"] == "total" || $_SESSION["perfil"] == "parcial" || $_SESSION["perfil"] == "caja" ) {
                echo '<li>
					<a href="ventas">	
						<i class="ion ion-clipboard"></i>
						<span>Administrar ventas</span>
					</a>
				</li>';
                //Cierre de caja
                echo '<li>
					<a href="caja">
						<i class="ion ion-card"></i>
						<span>Cierre de caja</span>
					</a>
				</li>';
                }}
            if ($_SESSION["perfil"] == "total" || $_SESSION["perfil"] == "parcial" || $_SESSION["perfil"] == "especial" || $_SESSION["perfil"] == "bodega" ) {
                //Promedio
                echo '<li>
					<a href="promedio">
						<i class="fa fa-crosshairs"></i>
						<span>Promedio Pedidos</span>
					</a>
				</li>';
            }
            if ($_SESSION["perfil"] == "total" || $_SESSION["perfil"] == "parcial" || $_SESSION["perfil"] == "caja"  ) {

                //Reporte ventas
                echo '<li>
					<a href="reportes">
						<img src="vistas/img/plantilla/reportes.png" style="width:24px;height:24px;">
						<span>Reportes</span>
					</a>
				</li>';

            }

                //======== Cierra menu
            if ($_SESSION["perfil"] == "total" || $_SESSION["perfil"] == "parcial" || $_SESSION["perfil"] == "caja" || $_SESSION["perfil"] == "especial") {
                echo '</ul>
                        
			</li>';
            }
                //===== Compras ******************
            if ($_SESSION["perfil"] == "total" || $_SESSION["perfil"] == "parcial" ||  $_SESSION["perfil"] == "bodega"  || $_SESSION["perfil"] == "caja" || $_SESSION["perfil"] == "caja" ) {
                echo '<li class="treeview">
				<a href="#">						
					<img src="vistas/img/plantilla/compras.png" style="width:24px;height:24px;">
					<span>Compras</span>					
						<i class="fa fa-angle-left pull-right"></i>
				</a>
				<ul class="treeview-menu">';
                // Hasta aqui menú
                //Compras
            if ($_SESSION["perfil"] == "total" || $_SESSION["perfil"] == "parcial" || $_SESSION["perfil"] == "caja" || $_SESSION["perfil"] == "bodega") {
                echo '<li>
					<a href="compras">
						<i class="glyphicon glyphicon-import"></i>
						<span>Compras</span>
					</a>
				</li>';
                //Pagos
            }
                echo '<li>
					<a href="pedidos">
						<i class="fa fa-ship"></i>
						<span>Pedidos</span>
					</a>
				</li>';
                //Pagos
            if ($_SESSION["perfil"] == "total" || $_SESSION["perfil"] == "parcial" || $_SESSION["perfil"] == "caja") {
                echo '<li>
					<a href="pagos">
						<i class="fa fa-credit-card-alt"></i>
						<span>Pagos</span>
					</a>
				</li>';

            }
                //======== Cierra menu
                echo '</ul></li>';

            }
            if ($_SESSION["perfil"] == "total" || $_SESSION["perfil"] == "parcial" || $_SESSION["perfil"] == "caja"  ) {
                //===== Mantenimiento ******************

                echo '<li class="treeview">
				<a href="#">						
					<img src="vistas/img/plantilla/tools.png" style="width:24px;height:24px;">
					<span>Mantenimiento</span>
						<i class="fa fa-angle-left pull-right"></i>
				</a>
				<ul class="treeview-menu">';
                // Hasta aqui menú
            if ($_SESSION["perfil"] == "total" || $_SESSION["perfil"] == "parcial" || $_SESSION["perfil"] == "caja" ) {
                //Bancos
                echo '<li>
					<a href="bancos">
						<i class="fa fa-bank"></i>
						<span>Bancos</span>
					</a>
				</li>';
                //Consulta
                echo '<li>
					<a href="consulta">
						<i class="fa fa-search"></i>
						<span>Consultas</span>
					</a>
				</li>';
                //Clientes
                echo '<li>
					<a href="clientes">
						<i class="fa fa-users"></i>
						<span>Clientes</span>
					</a>
				</li>';
                //Sin movimiento
                echo '<li>
					<a href="movimientos">
						<i class="fa fa-bullseye"></i>
						<span>Sin movimiento</span>
					</a>
				</li>';
                }
                //Productos
                echo '<li>
					<a href="productos">
						<i class="fa fa-cubes"></i>
						<span>Productos</span>
					</a>
				</li>';
            if ($_SESSION["perfil"] == "total" || $_SESSION["perfil"] == "parcial" || $_SESSION["perfil"] == "caja" ) {
                //Proveedores
                echo '<li>
					<a href="proveedores">
						<i class="fa fa-truck"></i>
						<span>Proveedores</span>
					</a>
				</li>';

                //Usuarios
                echo '<li>
					<a href="usuarios">
						<i class="fa fa-user"></i>
						<span>Usuarios</span>
					</a>
				</li>';
                //Utilidades
                echo '<li>
					<a href="utilidades">
						<i class="ion ion-android-options"></i>
						<span>Utilidades</span>
					</a>
				</li>';
                if ($_SESSION["perfil"] == "total") {
                    //Planilla
                    echo '<li>
					<a href="planilla">
						<i class="fa fa-address-card"></i>
						<span>Planilla</span>
					</a>
				</li>';
                }
                if ($_SESSION["perfil"] == "total" || $_SESSION["perfil"] == "parcial"  ) {
                
                //Sistema Viejo
                echo '<li>
					<a href="../index.php" target="_blank">
						<img src="vistas/img/plantilla/logomini.png" style="width:24px;height:24px;">
						<span>Sistema Viejo</span>
					</a>
				</li>
				<li>';
                
                }
            }

                //======== Cierra menu
                echo '</ul></li>';
            }

            //if ($_SESSION["perfil"] == "total" || $_SESSION["perfil"] == "ventas" || $_SESSION["perfil"] == "parcial" || $_SESSION["perfil"] == "bodega" || $_SESSION["perfil"] == "especial") {

                // ***** Inicio Catalogos
                echo '<li class="treeview">
				<a href="#">	
					<img src="vistas/img/plantilla/world.png" style="width:24px;height:24px;">
					<span>Catalogos</span>
						<i class="fa fa-angle-left pull-right"></i>
				</a>';
                //======== Menu de ventas
                echo '<ul class="treeview-menu">';
                //Listados
                
                echo '<li>	
						<a  href="https://www.lospaticas.com/correo" target="_blank">
							<img src="vistas/img/plantilla/correo.png" style="width:24px;height:24px;">
							<span>Correo</span>
						</a>
					</li>';
                echo '<li>	
						<a  href="https://web.whatsapp.com/" target="_blank">
							<img src="vistas/img/plantilla/whatsapp.png" style="width:24px;height:24px;">
							<span>WhatsApp Web</span>
						</a>
					</li>';
                //Registro	Usuario info@lospaticas.com // Paticas1
                echo '<li>		
						<a  href="https://www.rnpdigital.com/shopping/login.jspx" target="_blank">	
							<img src="vistas/img/plantilla/RN.png" style="width:24px;height:24px;">
							<span>Registro Nacional</span>
						</a>
					</li>';
                echo '<li>
						<a href="placas"  target="_blank">	
							<img src="vistas/img/plantilla/Vin.png" style="width:24px;height:24px;">
							<span>Listado Carros</span>
						</a>
					</li>';
                //Paginas de Catalogos
                echo '<li>
							<a  href="#">			
							<img src="vistas/img/plantilla/cat.png" style="width:24px;height:24px;">
							<span>Paginas Catalogos</span>
							</a>
						<ul>
							<li>						
								<a  href="https://ifinterface.com/page/page3.php?langid=2" target="_blank">						
									<img src="vistas/img/plantilla/item.png" style="width:24px;height:24px;">
									<span>Etka</span>
								</a>
							</li>
							<li>							
								<a  href="catalogo7zap"  target="_blank">								
									<img src="vistas/img/plantilla/item.png" style="width:24px;height:24px;">
									<span>7 Zap</span>
								</a>
							</li>
							<li>							
								<a  href="catalogoppi"  target="_blank">							
									<img src="vistas/img/plantilla/item.png" style="width:24px;height:24px;">
									<span>Pacific Parts</span>
								</a>
							</li>
							<li>
								<a  href="http://v1.empius.com/vwcatalogsite/search.html" target="_blank">	
									<img src="vistas/img/plantilla/item.png" style="width:24px;height:24px;">
									<span>Empi</span>
								</a>
							</li>
							<li>
								<a  href="catalogooems"  target="_blank">	
									<img src="vistas/img/plantilla/item.png" style="width:24px;height:24px;">
									<span>OEMShop</span>
								</a>
							</li>
						</ul>
					</li>';
                //Aceites
                echo '<li>
						<a  href="#">	
							<img src="vistas/img/plantilla/aceite.png" style="width:24px;height:24px;">
							<span>Aceites</span>
						</a>
						<ul>
						    
							<li>		
								<a  href="catalogolqm"  target="_blank">	
									<img src="vistas/img/plantilla/item.png" style="width:24px;height:24px;">
									<span>Liqui Moly</span>
								</a>
							</li>
							
						</ul>
					</li>';
                // Bujias
                echo '<li>
						<a  href="#">	
							<img src="vistas/img/plantilla/bujias.png" style="width:24px;height:24px;">
							<span>Bujias</span>
						</a>
						<ul>
							<li>
								<a  href="https://ngksparkplugs.com/en/part-finder" target="_blank">
									<img src="vistas/img/plantilla/item.png" style="width:24px;height:24px;">
									<span>NGK</span>
								</a>
							</li>
							
						</ul>
					</li>';
                //cr-415769 / admin / paticas2020cat* / pedidos /pEdidos* / ventas / ventas_paticas
                echo '<li>	 
						<a href="../App/Sistema Los Paticas_2_1.0.apk" target="_blank" >	 
							<img src="vistas/img/plantilla/android.png" style="width:24px;height:24px;">
							<span>App</span>
						</a>
					</li>';
                
                echo '<li> 
						<a href="catalogopl24" target="_blank" >	 
							<img src="vistas/img/plantilla/vw.png" style="width:24px;height:24px;">
							<span>PartLink24</span>
						</a>
					</li>';
                //======== Cierra menu
                echo '</ul></li>';
//            }
            ?>
        </ul>
    </section>
</aside>