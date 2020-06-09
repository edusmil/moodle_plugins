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
 * External Web Service Template
 *
 * @package    localwstemplate
 * @copyright  2011 Moodle Pty Ltd (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . "/externallib.php");
/*require(__DIR__.'config.php');*/

/*defined('MOODLE_INTERNAL') || die();*/

class local_ws_get_final_quiz_results_external extends external_api {
  
    public static function get_quiz_final_results_per_userid_and_courseid_parameters() {
        return new external_function_parameters(
                array('userid' => new external_value(PARAM_INT, 'Please inform the user id'),
                      'courseid' => new external_value(PARAM_INT, 'Please inform the course id'),
                     )
        );
    }

    public static function get_quiz_final_results_per_userid_and_courseid($userid,$courseid) {
        global $USER;
        global $DB;
        global $x;

        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::get_quiz_final_results_per_userid_and_courseid_parameters(),
                array('userid' => $userid, 'courseid' => $courseid)
        );

        //Context validation
        //OPTIONAL but in most web service it should present
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);

        //Capability checking
        //OPTIONAL but in most web service it should present
        if (!has_capability('moodle/user:viewdetails', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }

        $sql = "SELECT mdl_question_attempt_steps.userid, mdl_quiz_attempts.uniqueid, " .
               " SUM( CASE WHEN mdl_question_attempt_steps.state =  'gradedright' THEN 1  ELSE 0 END ) AS total_right, ".
               "                COUNT(DISTINCT questionid) AS total_questions,  ".
               "         mdl_quiz_attempts.state, FROM_UNIXTIME(MAX(mdl_quiz_attempts.timefinish)) AS date_finish, ".
              " mdl_quiz.grade, sum(fraction) right_answers,".
        "       (sum(fraction)/COUNT(DISTINCT questionid))*mdl_quiz.grade as final_grade ".
        "FROM  `mdl_question_attempt_steps`  ".
        "JOIN mdl_question_attempts ON (  `questionattemptid` = mdl_question_attempts.id )  ".
        "JOIN mdl_question ON (  `questionid` = mdl_question.id )  ".
        "JOIN mdl_quiz_attempts ON mdl_quiz_attempts.uniqueid= mdl_question_attempts.questionusageid ".
        "JOIN mdl_question_usages ON mdl_question_usages.id=mdl_question_attempts.questionusageid ".
        "JOIN mdl_quiz ON mdl_quiz.id=mdl_quiz_attempts.quiz  ".
       " WHERE mdl_question_attempt_steps.userid=".$userid." and course=".$courseid.
       " GROUP BY mdl_quiz.grade,mdl_question_attempt_steps.userid,mdl_quiz_attempts.uniqueid,mdl_quiz_attempts.state;";

        $results = $DB->get_records_sql($sql);
        return $results;
    }



 /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_quiz_final_results_per_userid_and_courseid_returns() {

        return new external_multiple_structure(
                new external_single_structure(
                array(
                    'userid' => new external_value(PARAM_INT, 'userid'),
                    'uniqueid' => new external_value(PARAM_INT, 'uniqueid'),
                    'total_questions' => new external_value(PARAM_INT, 'total_questions'),
                    'state' => new external_value(PARAM_TEXT,'state'),
                    'grade' => new external_value(PARAM_NUMBER,'grade'), 
                    'right_answers' => new external_value(PARAM_NUMBER,'right_answers'),
                    'final_grade'  => new external_value(PARAM_NUMBER,'final_grade'),
                    'date_finish' => new external_value(PARAM_TEXT,'date_finish'),
                    )
        )
        );
    }


}



class local_ws_get_quiz_results_external extends external_api {
    
    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_quiz_results_per_userid_and_courseid_parameters() {
        return new external_function_parameters(
                array('userid' => new external_value(PARAM_INT, 'Please inform the user id'),
                      'courseid' => new external_value(PARAM_INT, 'Please inform the course id'),
		     )
        );        
    }

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function get_quiz_results_per_userid_and_courseid($userid,$courseid) {
        global $USER;
        global $DB;
        global $x;
 
        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::get_quiz_results_per_userid_and_courseid_parameters(),
                array('userid' => $userid, 'courseid' => $courseid)
	);                

        //Context validation
        //OPTIONAL but in most web service it should present
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);

        //Capability checking
        //OPTIONAL but in most web service it should present
        if (!has_capability('moodle/user:viewdetails', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }

        $sql = " SELECT questionid, mdl_question_attempt_steps.userid, mdl_quiz_attempts.uniqueid, " .
               " COUNT( CASE WHEN mdl_question_attempt_steps.state =  'gradedright' THEN 1  END ) AS rightanswer, " .
	       " COUNT( CASE WHEN mdl_question_attempt_steps.state =  'gradedwrong' THEN 1  END ) AS wronganswer,  mdl_quiz_attempts.state, " .
	       " mdl_quiz.grade, FROM_UNIXTIME(MAX(mdl_quiz_attempts.timefinish)) AS date_finish ,sum(fraction) total_fraction, sum(mdl_quiz_attempts.sumgrades) as total_grades ".
        "FROM  `mdl_question_attempt_steps`  ".
        "JOIN mdl_question_attempts ON (  `questionattemptid` = mdl_question_attempts.id )  ".
        "JOIN mdl_question ON (  `questionid` = mdl_question.id )  ".
        "JOIN mdl_quiz_attempts ON mdl_quiz_attempts.uniqueid= mdl_question_attempts.questionusageid ".
        "JOIN mdl_question_usages ON mdl_question_usages.id=mdl_question_attempts.questionusageid ".
        "JOIN mdl_quiz ON mdl_quiz.id=mdl_quiz_attempts.quiz  ".
        "WHERE mdl_question_attempt_steps.userid=".$userid." and course=".$courseid." ".
        "GROUP BY mdl_quiz.grade,mdl_question_attempt_steps.userid,mdl_quiz_attempts.uniqueid,questionid,mdl_quiz_attempts.state;";
        
        $results = $DB->get_records_sql($sql);
        return $results;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_quiz_results_per_userid_and_courseid_returns() {

	return new external_multiple_structure(
		new external_single_structure(
                array(
		    'userid' => new external_value(PARAM_INT, 'userid'),
	            'questionid' => new external_value(PARAM_INT, 'questionid'),
                    'uniqueid' => new external_value(PARAM_INT, 'uniqueid'),
                    'rightanswer' => new external_value(PARAM_INT, 'rightanswer'),
                    'wronganswer' => new external_value(PARAM_INT, 'wronganswer'),
                    'state' => new external_value(PARAM_TEXT, 'state'),
                    'grade' => new external_value(PARAM_NUMBER, 'grade'),
                    'total_fraction' => new external_value(PARAM_NUMBER, 'total_fraction'),
                    'total_grades' => new external_value(PARAM_NUMBER, 'total_grades'),
                    'date_finish' => new external_value(PARAM_TEXT,'date_finish'), 
        )
	)
	);
    }



}
