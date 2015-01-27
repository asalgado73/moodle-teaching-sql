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

$sqlpreguntas = $DB->get_records_sql('SELECT * FROM mdl_nivel');

$combobit = "";
foreach ( $sqlpreguntas as $row ) {
        $combobit.=' <option value="'.$row->id.'">'.$row->nombre.'</option>'; //concatenamos el los options para luego ser insertado en el HTML
}
$escen=0;
$pre=1;
$nom_e="";
if (isset($_POST['textfield'])) {
    $conexion = mysqli_connect("localhost", "root", "","moodle");
	$imagen=$_FILES['imagen']['tmp_name'];
	$tamimagen=$_FILES['imagen']['size'];
	$fp=fopen($imagen,'rb'); //abrimos el archivo binario "imagen" en modo lectura
	$contenido=fread($fp,$tamimagen);//lee el archivo hasta el tamaño de la imagen
	$contenido=addslashes($contenido);//Añadimos caracteres de escape
	fclose($fp);
    mysqli_query($conexion,"INSERT INTO MDL_ESCENARIO(NOMBRE,IMAGEN,SCRIPT)VALUES('". $_POST['textfield'] ."','".$contenido."','". $_POST['textarea'] ."')");
    mysqli_close($conexion);
    $sqlpreguntas = $DB->get_records_sql('SELECT id,nombre FROM mdl_escenario where nombre=?',array($_POST['textfield']));
    //$sqlpreguntas = $DB->get_records_sql('SELECT id,nombre FROM mdl_escenario');
	foreach ( $sqlpreguntas as $row ) {
		echo $row->id;
		echo $row->nombre;
		$escen=$row->id;
		$nom_e=$row->nombre;
	}
}

if(isset($_POST['texto1'])){
    $escen=$_POST['escenario'];
    $pre=$_POST['preg']+1;
    $nom_e=$_POST['nome'];
    $conexion = mysqli_connect("localhost", "root", "","moodle");
    mysqli_query($conexion,"INSERT INTO MDL_PREGUNTA(NIVEL_ID,ESCENARIO_ID,TEXTO,RESULTADO)VALUES(".$_POST['nivel'].",".$_POST['escenario'].",'".$_POST['texto1']."','".$_POST['texto2']."')");
    }
// Replace the following lines with you own code.
echo $OUTPUT->heading('<div>
  <table width="770" border="0" cellspacing="2" align="center">
    <tr>
      <td width="341" align="left"><strong>Escenario '.$nom_e.'</strong></td>
      <td width="229" align="right"><strong>Pregunta '.$pre.'</strong></td>
    </tr>
    <tr>
      <td align="left"><strong>Pregunta:</strong></td>
      <td><form id="form2" name="form2" method="post" action="">
        <input type="text" name="texto1" required="required" />
      </td>
    </tr>
    <tr>
      <td align="left"><strong>Respuesta:</strong></td>
      <td><input type="text" name="texto2" required="required"/>
      <input type="hidden" name="escenario" value="'.$escen.'">
      <input type="hidden" name="preg" value="'.$pre.'"></td>
      <input type="hidden" name="nome" value="'.$nom_e.'"></td>    
    </tr>
    <tr>
      <td align="left"><strong>Nivel</strong>:</td>
      <td>
        <select name="nivel">
        '.$combobit.'
        </select>
            </td>
    </tr>
    <tr>
      <th scope="row">&nbsp;</th>
         <td>'
        . '
          <input type="hidden" name="id" value="' . $id . '">
          '
          . '
          <input type="hidden" name="n" value="' . $n . '">
          '
          . '
        <input type="submit" name="Submit" value="Crear" />
       </form></td>
    </tr>
    <tr>
      <th scope="row">&nbsp;</th><form id="form4" name="form4" method="post" action="view.php">
      '
        . '
          <input type="hidden" name="id" value="' . $id . '">
          '
          . '
          <input type="hidden" name="n" value="' . $n . '">
          '
          . '
    <td align="right"><input alin type="submit" name="Submit2" value="Finalizar" /></td>
   </form>     
   </tr>
  </table>
</div>
');




// Finish the page.
echo $OUTPUT->footer();
