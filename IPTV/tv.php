<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title></title>
  

</head>
<body>
	<hr>
	<button onclick="window.location.href='https://mega.nz'" target="_blank">Logear Mega</button>
	<button onclick="window.location.href='Peliculas HH.apk'" target="_blank">App</button>	
	<hr>
	<button  onclick="cargar('BADBOYS')"><img src="img/badboys.jpg" style="width:128px;height:128px;" />Bad Boys</button>
	<button onclick="cargar('HOBBS')"><img src="img/hobbs.png" style="width:128px;height:128px;" />Hobbs and Shaw</button>
	<hr>
	<button onclick="cargar('JUNGLE')"><img src="img/jungle.png" style="width:128px;height:128px;" />Jungle beat</button>
	<button onclick="cargar('TERRA')"><img src="img/terra.png" style="width:128px;height:128px;" />Terra Willy</button>
	<button onclick="cargar('WILLOU')"><img src="img/Willoughby.png" style="width:128px;height:128px;" />The Willoughbys</button>

	<hr>
	<iframe src="https://onedrive.live.com/embed?id=F3A7FF6144A35AE2&resid=F3A7FF6144A35AE2%219187&authkey=ANUY6ECjiQGQVGk" width="320" height="240" frameborder="0" scrolling="no" allowfullscreen>Aqui Va</iframe>
	<div id="contenido">
					
					
	</div>
	
	 
	  <script src="src/jquery.min.js"></script>
	  <script src="src/bootstrap.min.js"></script>
	  <script type='text/javascript' src='http://www.televisiongratishd.com/embed.js'></script>
</body>
</html>

<script>
	    function cargar(canal){
	    	$('#contenido').html('<div class="loading"><img src="src/pageLoader.gif" alt="loading" /><br/>Un momento, por favor...</div>');
		    	$.ajax('src/cargar.php?canal='+canal)
				  .done(function(response){
				    $('#contenido').html(response);
				  });	

			}

	   
</script>