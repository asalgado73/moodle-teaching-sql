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
$consulta=$DB->get_records_sql("select * from mdl_evaluacion where evaluacion_act=1");
$pagina="";
echo "<div style='text-align:center'>
Seleccione evaluacion
<form action='view_a.php'>
<select name='evaluacion' rows='5'>";
foreach($consulta as $aux){
echo "<option value='".$aux->id."'>".$aux->nombre."</option>";
}
echo "</select></div>";
echo '<input type="hidden" name="id" value="' . $id . '">
          '
          . '
          <input type="hidden" name="n" value="' . $n . '">
          '
          . '
          <input type="submit" value="Iniciar" /></form>';

echo $OUTPUT->footer();
?>