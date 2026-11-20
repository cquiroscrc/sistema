<?php

if(isset($_REQUEST['canal']) && !empty($_REQUEST['canal'])){
  $canal =$_REQUEST['canal']; 
}


if ($canal=='BADBOYS') {
	$direccion="https://mega.nz/embed/8V0E1TBC#MqE9dNZ3pz2WFaVSPJE6eTrRzcY_qi2v1yTee41g4aI";
}

if ($canal=='HOBBS') {
	$direccion="https://mega.nz/embed/hV023T6D#FS_t_-iEbd8vrsj0TkAWv3zasTQe-Ag4VG3GXIrG0Og";
}

if ($canal=='JUNGLE') {
	$direccion="https://mega.nz/embed/VItF1aoR#G5iuIWHIsixu82NlrSr4QCKs3LBX_rV6au710WjoP6o";
}

if ($canal=='TERRA') {
	$direccion="https://mega.nz/embed/RA03gKSI#ImHDPyW1IVYOdUnBFeBG8Z73cX232nDVijjrMkZAVss";
}

if ($canal=='WILLOU') {
	$direccion="https://mega.nz/embed/kZ8iWBgb#boH2AIN-KAjUgaxK9eWi3-P9vhkNuEu8VcprlhRGHI0";
}




?>
<br>
<iframe width="640" height="360" frameborder="0" src=<?php echo $direccion; ?> allowfullscreen allow="autoplay;"></iframe>



  
