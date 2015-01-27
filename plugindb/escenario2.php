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

$sqlpreguntas = $DB->get_records_sql('SELECT * FROM pregunta WHERE id = 2', array(1));

$combobit = "";
foreach ( $sqlpreguntas as $row ) {
        $combobit .=" <option value='". $row->id ."'>". $row->ejercicio ."</option>"; //concatenamos el los options para luego ser insertado en el HTML
}

// Replace the following lines with you own code.
echo $OUTPUT->heading('<div>
<table width="958" height="402" border="0" align="center">
  <tr>
    <td width="727">      <label>Ejercicio: </label>
 </td>
    <td width="221">Resultado</td>
  </tr>
  <tr>
    <td><img src="img/ej2.png" width="650" height="250" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>     <label>'
            . $combobit          
          .'
          </label>
</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><form id="form1" name="form1" method="post" action="">
      <label>
        <textarea name="textarea" ></textarea>
        </label>
    </form>
    </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><form id="form2" name="form2" method="post" action="" >
      <input type="submit" name="Submit" value="Enviar" align=/>
    </form>
    </td>
    <td><form id="form2" name="form2" method="post" action="escenario3.php">
        <p>
          <label></label>
        </p>
        <p align="center">' 
          . '
          <input type="hidden" name="id" value="' . $id . '">
          '
          . '
          <input type="hidden" name="n" value="' . $n . '">
          '
          . '
          <input type="submit" name="Submit" value="Siguiente" />
          <br />
        </p>
      </form>
    </td>
  </tr>
</table>
</div>
');

// Finish the page.
echo $OUTPUT->footer();
