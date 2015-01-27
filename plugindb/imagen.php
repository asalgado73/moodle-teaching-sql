<?php
$conexion=mysqli_connect("localhost","root","","moodle");
$registro=mysqli_query($conexion,"select distinct(mdl_escenario.imagen) from mdl_evaluacion,
mdl_pregunta,mdl_escenario,mdl_pregunta_evaluacion
where
mdl_evaluacion.id=mdl_pregunta_evaluacion.evaluacion_id
and mdl_pregunta_evaluacion.pregunta_id=mdl_pregunta.id
and mdl_pregunta.escenario_id=mdl_escenario.id
and mdl_evaluacion.id=".$_GET['id']);
$reg=mysqli_fetch_array($registro); 

Header("Content-type: image/jpeg");
echo $reg['imagen'];