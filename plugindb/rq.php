<?php

if (isset($_REQUEST["numero"])) {
	cargarTabla($_REQUEST["numero"]);
}
if(isset($_REQUEST["comprobar"])){
   comprobar($_REQUEST["comprobar"],$_REQUEST["pregunta"]);
}

function cargarTabla($val) {

	//$value=array();
	$resultado = array();
	$con=mysqli_connect("localhost","root","","moodle");
	$res=mysqli_query($con,"select * from mdl_pregunta where escenario_id=".$val);
	if(mysqli_num_rows($res)){
	$aux=1;
	while($linea=mysqli_fetch_array($res)){
		$resultado[''.$aux.'']='<input type="checkbox" name="pregunta'.$linea['id'].'" value="'.$linea['id'].'">'.$linea['texto'];
		$aux++;
	}
	}else{
		$resultado['1']="ninguna";
	}
    echo json_encode($resultado);
}

function comprobar($val,$preg){
$resultado="";
$con=mysqli_connect("localhost","root","","moodle");
$res=mysqli_query($con,"select * from mdl_pregunta where id=$preg and resultado='".$val."'");
if(mysqli_num_rows($res)){
$aux=mysqli_fetch_array($res);
$res=mysqli_query($con,$aux['resultado']);
while($aux=mysqli_fetch_array($res,MYSQLI_NUM)){
	foreach($aux as $a){
		$resultado.=$a;
	}
}
}else{
$resultado="No es correcta la consulta";
}
$value=array(
"prueba"=>$resultado
);
echo json_encode($value);
}

