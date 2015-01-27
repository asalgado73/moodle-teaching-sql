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

$PAGE->set_url('/mod/plugindb/view_1.php', array('id' => $cm->id));
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

$sqlpreguntas = $DB->get_records_sql('SELECT * FROM mdl_pregunta');

//$combobit = "";
//foreach ($sqlpreguntas as $row) {
//    $combobit .=" <option value='" . $row->id . "'>" . $row->texto . "</option>"; //concatenamos el los options para luego ser insertado en el HTML
//}

$combobit = "";
//foreach ($sqlpreguntas as $row) {
//    $combobit .=" <input type='checkbox' value='" . $row->id . "'>".$row->texto."<br>"; //concatenamos el los options para luego ser insertado en el HTML
//}

//  $record1 = new stdClass();
//  $record1->name         = 'overview';
//  $record1->typeid         = 34;
//  $record1->value       = 'overview';
  
//  $lastinsertid = $DB->insert_records('mdl_lti_types_config', $record1,false);
  
//  echo "<table>
//";
//while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
//echo "	<tr>
//";
//foreach ($line as $col_value) {
//echo "	<td>$col_value</td>
//";
//}
//echo "	</tr>
//";
//}
//echo "</table>
//";

// Replace the following lines with you own code.
$aux1=0;
if (isset($_POST['tipo'])) {
    $aux1 = $_POST['select2'];
    $caja=1;
    $aux2=1;
	$id_aux=0;
    $con = mysqli_connect("localhost", "root", "","moodle");
    $res = mysqli_query($con,"select id from mdl_pregunta where escenario_id=".$aux1);
    try{
		mysqli_query($con,"INSERT INTO MDL_EVALUACION(tipo_id,nombre,fecha_inicio,fecha_fin,evaluacion_act)VALUES(".$_POST['tipo'].",'".$_POST['nombre']."','".$_POST['fechai']."','".$_POST['fechai']."',1)");
		$sql1=$DB->get_records_sql('SELECT id FROM mdl_evaluacion ORDER BY id DESC LIMIT 1');
		foreach($sql1 as $a1){
			$id_aux=$a1->id;
		}

		while ($line = mysqli_fetch_array($res)) {
        if(isset($_POST["pregunta".$line['id']])){   
		 mysqli_query($con,"INSERT INTO MDL_PREGUNTA_EVALUACION VALUES(".$line['id'].",$id_aux)");
		}

    }
    }catch(Exception $ex){
        echo "error";
    }
    }


$tipo="";
$escenario="";
$sqlpreguntas = $DB->get_records_sql('SELECT * FROM mdl_tipo');

foreach ($sqlpreguntas as $row) {
    $tipo.=' <option value="'.$row->id.'">'.$row->nombre.'</option>';
}

$sqlpreguntas = $DB->get_records_sql('SELECT * FROM mdl_escenario');
$escenario.='<option value="0">No ha seleccionado Escenario</option>';
foreach ($sqlpreguntas as $row) {
     $escenario.=" <option value='" . $row->id . "'>".$row->nombre."</option>";
    
}

//if(isset($_POST['nombre'])){
//    $con=mysql_connect("localhost","root","123456");
//    mysql_select_db("moodle");
//    mysql_query("insert into evaluacion(nombre,pregunta,t_eva)values('".$_POST['nombre']."','".$_POST['estado']."',1)");
//} 

echo $OUTPUT->heading('
    <form method="POST" action="">
	<table>
    <tr>
	<td>Nombre   :</td>
    <td><input type="Text" name="nombre"></td>  
	</tr>
	<tr>
    <td>Tipo de evaluacion:</td>
	<td>
      <select name="tipo">
      '.$tipo.'
      </select>
	 </td> 
    </tr>
	<tr>
    <td>Escenario:</td>
	<td>
      <select name="select2" onchange="consultar(this.value)">
      '.$escenario.'
      </select>
	  </td>
	  </tr>
	  <tr>
	  <td>Fecha inicio</td>
	  <td><input type="date" name="fechai"></td>
	  </tr>
	  <tr>
	  <td>Fecha Fin</td>
	  <td><input type="date" name="fechaf"></td>
	  </tr>
	  </table>
	  <br>
      '. $combobit .'
  </p>
    
    <p align="center">' 
          . '
          <input type="hidden" name="id" value="' . $id . '">
          '
          . '
          <input type="hidden" name="n" value="' . $n . '">
          '
          . '
          <div id="tabla">
          </div>    
          <input type = "submit" name = "enviar" value = "Crear Actividad">
          <br />
  </p>
</form> 
<form action="view.php">
<p align="center">' 
          . '
          <input type="hidden" name="id" value="' . $id . '">
          '
          . '
          <input type="hidden" name="n" value="' . $n . '">
          '
          . '
          <input type="submit" name="Submit" value="Terminar" /></form>
<script type="text/javascript" src="index.js"></script>
<script type="text/javascript" src="jquery-1.11.1.js"></script>
');


// Finish the page.
echo $OUTPUT->footer();