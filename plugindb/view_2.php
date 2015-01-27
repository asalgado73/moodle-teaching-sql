<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n = optional_param('n', 0, PARAM_INT);  // ... plugindb instance ID - it should be named as the first character of the module.


if ($id) {
    $cm = get_coursemodule_from_id('plugindb', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $plugindb = $DB->get_record('plugindb', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $plugindb = $DB->get_record('plugindb', array('id' => $n), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $plugindb->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('plugindb', $plugindb->id, $course->id, false, MUST_EXIST);
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

$PAGE->set_url('/mod/plugindb/view_2.php', array('id' => $cm->id));
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
$conexion=mysqli_connect("localhost","root","","moodle");
if(isset($_GET['estudiante'])){
$res3=mysqli_query($conexion,"SELECT pregunta_id from mdl_user_pregunta_evaluacion
WHERE user_id=".$_GET['estudiante']." AND evaluacion_id=".$_GET['evaluacion']);
while($linea1=mysqli_fetch_array($res3)){
if(isset($_GET['pregunta'.$linea1['pregunta_id']])){
mysqli_query($conexion,"UPDATE mdl_user_pregunta_evaluacion SET nota=".$_GET['nota'.$linea1['pregunta_id']]." WHERE user_id=".$_GET['estudiante']." AND evaluacion_id=".$_GET['evaluacion']."
AND pregunta_id=".$_GET['pregunta'.$linea1['pregunta_id']]);
}
}
}
$res=mysqli_query($conexion,"select pregunta_id,username,respuesta,resultado,nota from mdl_user_pregunta_evaluacion,mdl_user
where mdl_user_pregunta_evaluacion.user_id=mdl_user.id 
AND mdl_user.id=".$_GET['estudiante']." AND mdl_user_pregunta_evaluacion.evaluacion_id=".$_GET['evaluacion']);
if(mysqli_num_rows($res)>0){
echo "<form><table border='1'>";
echo "<tr><th>Op</th><th>ESDIANTE</th><th>RESPUESTA</th><th>RESULTADO</th><th>NOTA</th></tr>";
foreach($res as $linea){
    echo "<tr>";
	echo "<td><input type='checkbox' name='pregunta".$linea['pregunta_id']."' value='".$linea['pregunta_id']."'></td>";
	echo "<td>".$linea['username']."</td>";
	echo "<td>".$linea['respuesta']."</td>";
	echo "<td>".$linea['resultado']."</td>";
	echo "<td><input type='text' name='nota".$linea['pregunta_id']."' value='".$linea['nota']."'></td>";
	echo "</tr>";
}
echo '</table>'. '
          <input type="hidden" name="id" value="' . $id . '">
          '
          . '
          <input type="hidden" name="n" value="' . $n . '">
          '
          . '
		  <input type="hidden" name="estudiante" value="'.$_GET['estudiante'].'">
		  <input type="hidden" name="evaluacion" value="'.$_GET['evaluacion'].'">
          <input type="submit" name="Submit" value="Actualizar" /></form>';
$res=mysqli_query($conexion,"select ROUND((sum(nota)/(count(nota)*5))*100,2) AS nota from mdl_user_pregunta_evaluacion,mdl_user
where mdl_user_pregunta_evaluacion.user_id=mdl_user.id 
AND mdl_user.id=".$_GET['estudiante']." AND mdl_user_pregunta_evaluacion.evaluacion_id=".$_GET['evaluacion']);
while($linea=mysqli_fetch_array($res)){
	echo "<h1 align='center'>Nota Total ".$linea['nota']." %</h1>";
}
}else{
echo "<div align='center'>No ha presentado examenes</div>";
}
echo '<br><br><form action="view.php">
<p align="center">' 
          . '
          <input type="hidden" name="id" value="' . $id . '">
          '
          . '
          <input type="hidden" name="n" value="' . $n . '">
          '
          . '
          <input type="submit" name="Submit" value="Terminar" /></form>';		  
echo $OUTPUT->footer();
?>