<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Prints a particular instance of plugindb
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_plugindb
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Replace plugindb with the name of your module and remove this line.

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // ... plugindb instance ID - it should be named as the first character of the module.

if ($id) {
    $cm         = get_coursemodule_from_id('plugindb', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $plugindb  = $DB->get_record('plugindb', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $plugindb  = $DB->get_record('plugindb', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $plugindb->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('plugindb', $plugindb->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);

$event = \mod_plugindb\event\course_module_viewed::create(array(
    'objectid' => $PAGE->cm->instance,
    'context' => $PAGE->context,
));
$event->add_record_snapshot('course', $PAGE->course);
// In the next line you can use $PAGE->activityrecord if you have set it, or skip this line if you don't have a record.
$event->add_record_snapshot($PAGE->cm->modname, $activityrecord);
$event->trigger();

// Print the page header.

$PAGE->set_url('/mod/plugindb/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($plugindb->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

/*
 * Other things you may want to set - remove if not needed.
 * $PAGE->set_cacheable(false);
 * $PAGE->set_focuscontrol('some-html-id');
 * $PAGE->add_body_class('plugindb-'.$somevar);
 */

// Output starts here.
echo $OUTPUT->header();

// Conditions to show the intro can change to look for own settings or whatever.
if ($plugindb->intro) {
    echo $OUTPUT->box(format_module_intro('plugindb', $plugindb, $cm->id), 'generalbox mod_introbox', 'plugindbintro');
}
global $USER;
$aux1=1;
$evaluacion=0;
$preg=0;
$id_preg=0;
$redireccion=0;
if(isset($_POST['respuesta'])){
	$evaluacion=$_POST['evaluacion'];
	$pregunta=$_POST['pregunta'];
	$nota=0;
	if($_POST['resultado']=='No es correcta la consulta'){
	    $nota=0;
	}else{
		$nota=5;
	}		
	$conecta=mysqli_connect("localhost","root","","moodle");
	mysqli_query($conecta,"UPDATE mdl_user_pregunta_evaluacion SET respuesta='".$_POST['respuesta']."',resultado='".$_POST['resultado']."',nota=".$nota.",pregunta_activa=0
	WHERE evaluacion_id=".$evaluacion." AND pregunta_id=".$pregunta." AND user_id=$USER->id");
$res1=mysqli_query($conecta,"SELECT
mdl_pregunta.id AS pregunta,
mdl_pregunta.texto AS texto,
mdl_evaluacion.id AS evaluacion
FROM
mdl_pregunta,
mdl_evaluacion,
mdl_pregunta_evaluacion,
mdl_user_pregunta_evaluacion
WHERE
mdl_pregunta.id=mdl_pregunta_evaluacion.pregunta_id
AND mdl_evaluacion.id=mdl_pregunta_evaluacion.evaluacion_id
AND mdl_pregunta_evaluacion.pregunta_id=mdl_user_pregunta_evaluacion.pregunta_id
AND mdl_pregunta_evaluacion.evaluacion_id=mdl_user_pregunta_evaluacion.evaluacion_id
AND mdl_evaluacion.id=".$_POST['evaluacion']."
AND pregunta_activa=1
AND mdl_user_pregunta_evaluacion.user_id=$USER->id;");
if(mysqli_num_rows($res1)){
$res2=mysqli_fetch_array($res1);
$preg=$res2['texto'];
$id_preg=$res2['pregunta'];	
}else{
$redireccion=1;
}
}else{
$conexion=mysqli_connect("localhost","root","","moodle");
$res3=mysqli_query($conexion,"
SELECT * FROM mdl_user_evaluacion,mdl_evaluacion
WHERE mdl_user_evaluacion.evaluacion_id=mdl_evaluacion.id
AND evaluacion_act=1 
AND mdl_user_evaluacion.mdl_user_id=$USER->id
AND mdl_evaluacion.id=".$_GET['evaluacion']);
if(mysqli_num_rows($res3)==0){
$sql1=$DB->get_records_sql("select id from mdl_evaluacion where evaluacion_act=1 AND id=".$_GET['evaluacion']);
foreach($sql1 as $aux1){
mysqli_query($conexion,"INSERT INTO mdl_user_evaluacion VALUES($USER->id,$aux1->id)");
}
$sql2=mysqli_query($conexion,"SELECT
mdl_evaluacion.id As evaluacion,
mdl_pregunta.id AS pregunta,
mdl_pregunta.texto AS texto
FROM
mdl_evaluacion,
mdl_pregunta,
mdl_pregunta_evaluacion
WHERE
mdl_evaluacion.id=mdl_pregunta_evaluacion.evaluacion_id
AND mdl_pregunta.id=mdl_pregunta_evaluacion.pregunta_id
AND evaluacion_act=1
AND mdl_evaluacion.id=".$_GET['evaluacion']);
$aux2=0;
$texto=0;
$evaluacion=0;
foreach($sql2 as $aux1){
mysqli_query($conexion,"INSERT INTO mdl_user_pregunta_evaluacion(evaluacion_id,pregunta_id,user_id,nota,pregunta_activa)VALUES(".$aux1['evaluacion'].",".$aux1['pregunta'].",$USER->id,0,1)");
$evaluacion=$aux1['evaluacion'];
}
$sql2=mysqli_query($conexion,"SELECT
mdl_pregunta.id AS pregunta,
mdl_pregunta.texto AS texto
FROM
mdl_evaluacion,
mdl_pregunta,
mdl_pregunta_evaluacion
WHERE
mdl_evaluacion.id=mdl_pregunta_evaluacion.evaluacion_id
AND mdl_pregunta.id=mdl_pregunta_evaluacion.pregunta_id
AND evaluacion_act=1
AND mdl_evaluacion.id=".$_GET['evaluacion']);
$texto=mysqli_fetch_array($sql2);
$preg=$texto['texto'];
$id_preg=$texto['pregunta'];
mysqli_close($conexion);
}else{
$redireccion=1;
}
}
if($redireccion==1){
$conex12=mysqli_connect("localhost","root","","moodle");
$res12=mysqli_query($conexion,"select ROUND((sum(nota)/(count(nota)*5))*100,2) AS nota from mdl_user_pregunta_evaluacion,mdl_user
where mdl_user_pregunta_evaluacion.user_id=mdl_user.id 
AND mdl_user.id=$USER->id AND mdl_user_pregunta_evaluacion.evaluacion_id=".$_GET['evaluacion']);
while($linea12=mysqli_fetch_array($res12)){
	echo "<h1 align='center'>Nota Total ".$linea12['nota']." %</h1>";
}
echo $OUTPUT->heading('<form action="view.php">
<p align="center">' 
          . '
          <input type="hidden" name="id" value="' . $id . '">
          '
          . '
          <input type="hidden" name="n" value="' . $n . '">
          '
          . '
          <input type="submit" name="Submit" value="Terminar" /></form>');
}else{
echo $OUTPUT->heading('<div style="text-align:center">
<img src="imagen.php?id='.$evaluacion.'">
<br>
'.$preg.'
</div>
<form method="post">
<div style="text-align:left">
<table>
<tr>
<td>Respuesta</td>
</tr>
<tr>
<td><textarea name="respuesta" id="respuesta"></textarea></td>
</tr>
<tr>
<td><input type="button" value="consultar" onclick="comprobar('.$id_preg.')"></td>
</tr>
</table>
</div>
<div align="right">
<table>
<tr>
<td>Resultado</td>
</tr>
<tr>
<td><textarea name="resultado" id="resultado"></textarea></td>
</tr>
<tr>
<td><input type="submit" value="siguiente">	</td>
</tr>
</table>
</div>
'. '
    <input type="hidden" name="id" value="' . $id . '">
    '
    . '
    <input type="hidden" name="n" value="' . $n . '">
    '
    . '
	<input type="hidden" name="evaluacion" value="'.$evaluacion.'">
	<input type="hidden" name="pregunta" value="'.$id_preg.'">
</form>
<script type="text/javascript" src="prueba.js"></script>
<script type="text/javascript" src="jquery-1.11.1.js"></script>
');
}

// Finish the page.
echo $OUTPUT->footer();
