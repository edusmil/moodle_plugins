<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

//$CFG->debug=32767;
//$CFG->debugdisplay=1;

$CFG->dbtype    = 'mysqli';
$CFG->dblibrary = 'native';
$CFG->dbhost    = '10.33.1.891';
$CFG->dbname    = 'moodle1';
$CFG->dbuser    = 'moodle2';
$CFG->dbpass    = 'moodle3';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => 3306,
  'dbsocket' => '',
  'dbcollation' => 'utf8_general_ci',
);

$CFG->wwwroot   = 'http://moodledev.einstein.br2';
$CFG->sslproxy  = '0';
$CFG->dataroot  = '/var/www/html/moodledata1';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;

require_once(__DIR__ . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!

