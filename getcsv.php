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
 * @package local_configurablereportwebservice
 * @author Andrew Hancox <andrewdchancox@googlemail.com>
 * @author Open Source Learning <enquiries@opensourcelearning.co.uk>
 * @link https://opensourcelearning.co.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2021, Andrew Hancox
 */

require_once('../../config.php');
require_once($CFG->dirroot."/blocks/configurable_reports/locallib.php");
require_once($CFG->dirroot . '/blocks/configurable_reports/report.class.php');
require_once("$CFG->dirroot/blocks/configurable_reports/export/csv/export.php");
require_once("$CFG->dirroot/webservice/lib.php");

$id = required_param('id', PARAM_INT);
$token = required_param('t', PARAM_RAW);

$webservicelib = new webservice();

try {
    $webservicelib->authenticate_user($token);
} catch (\Exception $exception) {
    require_logout();

    throw $exception;
}
if (!$report = $DB->get_record('block_configurable_reports', ['id' => $id])) {
    throw new \moodle_exception('reportdoesnotexists', 'block_configurable_reports');
}

require_once($CFG->dirroot . '/blocks/configurable_reports/reports/' . $report->type . '/report.class.php');

if (!$course = $DB->get_record('course', ['id' => $report->courseid])) {
    throw new \moodle_exception('No such course id');
}

// Force user login in course (SITE or Course).
if ($course->id == SITEID) {
    $context = context_system::instance();
} else {
    $context = context_course::instance($course->id);
}

require_capability('local/configurablereportwebservice:fetchreports', $context);

$reportclassname = 'report_' . $report->type;
$reportclass = new $reportclassname($report);

if (
    !$reportclass->check_permissions($USER->id, $context)
    ||
    strpos($report->export, 'csv' . ',') === false
) {
    throw new \moodle_exception('badpermissions', 'block_configurable_reports');
}

$PAGE->set_context($context);
$PAGE->set_url('/local/configurablereportwebservice/getcsv.php', ['id' => $id]);


if ($report->type == "sql") {
    $reportclass->setForExport(true);
}
$reportclass->create_report();

// Large exports are likely to take their time and memory.
core_php_time_limit::raise();
raise_memory_limit(MEMORY_EXTRA);
export_report($reportclass->finalreport);
