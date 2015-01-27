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
$aux=0;
$conexion=mysqli_connect("localhost","root","","moodle");
$consulta=mysqli_query($conexion,"select mdl_evaluacion.id AS id,nombre from mdl_evaluacion,
mdl_user_evaluacion where mdl_evaluacion.id=mdl_user_evaluacion.evaluacion_id
AND mdl_user_evaluacion.mdl_user_id=".$_GET['estudiante']);
if(mysqli_num_rows($consulta)>0){
echo "<div style='text-align:center'>
Seleccione evaluacion
<form action='view_2.php'>
<select name='evaluacion' rows='5'>";
while($aux=mysqli_fetch_array($consulta)){
echo "<option value='".$aux['id']."'>".$aux['nombre']."</option>";
}
echo "</select></div>";
echo '<input type="hidden" name="id" value="' . $id . '">
          '
          . '
          <input type="hidden" name="n" value="' . $n . '">
          '
          . '
		  <input type="hidden" name="estudiante" value="'.$_GET['estudiante'].'">
          <input type="submit" value="Respuestas" />
		  </form>';
}else{
echo "NO ha realizado ninguna evaluacion";
echo '<form action="view.php">
<input type="hidden" name="id" value="' . $id . '">
          '
          . '
          <input type="hidden" name="n" value="' . $n . '">
          '
          . '
          <input type="submit" value="Terminar" />
</form>';
}		  
echo $OUTPUT->footer();
?>