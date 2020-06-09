<?php

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
 * Web service local plugin template external functions and service definitions.
 *
 * @package    localwstemplate
 * @copyright  2011 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// We defined the web service functions to install.
$functions = array(
        'local_ws_get_quiz_results' => array(
                'classname'   => 'local_ws_get_quiz_results_external',
                'methodname'  => 'get_quiz_results_per_userid_and_courseid',
                'classpath'   => 'local/get_quiz_results/externallib.php',
                'description' => 'Return quiz result by user and course',
                'type'        => 'read',
		'services' => array('Results')
        ),
        'local_ws_get_final_quiz_results' => array(
                'classname'   => 'local_ws_get_final_quiz_results_external',
                'methodname'  => 'get_quiz_final_results_per_userid_and_courseid',
                'classpath'   => 'local/get_quiz_results/externallib.php',
                'description' => 'Return final quiz result by user and course',
                'type'        => 'read',
                'services' => array('Results')
        ),

);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
        'Results' => array(
                'functions' => array ('local_ws_get_quiz_results','local_ws_get_final_quiz_results'),
                'restrictedusers' => 0,
                'enabled'=>1,
        )
);

