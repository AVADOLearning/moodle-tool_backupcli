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
 * Backup and restore CLI.
 *
 * @package    tool_backupcli
 * @copyright  2019 Luke Carrier <luke@carrier.im>
 * @author     Luke Carrier <luke@carrier.im>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);
require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/config.php';
/** @var moodle_page $PAGE */
require_once "{$CFG->libdir}/clilib.php";
require_once "{$CFG->libdir}/datalib.php";
require_once "{$CFG->dirroot}/backup/util/includes/restore_includes.php";

list($params, $unrecognized) = cli_get_params([
    'file' => '',
    'courseid' => 0,
    'categoryid' => 0,
    'samesite' => false,
    'help' => false,
], [
    'h' => 'help',
]);

if ($params['help']) {
    $help = <<<HELP
Restore CLI

Options:
    --file=PATH           Input file to restore from
    --courseid=ID         Course ID, if overwriting existing course
    --categoryid=ID       Parent category ID, if creating new course
    --samesite            The backup was generated for use on the same site (excludes files, faster)
    -h, --help            This message
HELP;
    mtrace($help);
    exit;
}

if (!$params['file']) {
    cli_error('--file is required');
}
if (!$params['courseid'] && !$params['categoryid']) {
    cli_error('Either --courseid or --categoryid is required');
}
if ($params['courseid'] && $params['categoryid']) {
    cli_error('Only one of --courseid or --categoryid is allowed');
}

if ($params['categoryid']) {
    $course = (object) [
        'shortname' => 'Restoring course...',
        'fullname' => 'Restoring course...',
        'summary' => '',
        'category' => $params['categoryid'],
    ];
    $course = create_course($course);
} else {
    $course = get_course($params['courseid']);
}

$mode = $params['samesite'] ? backup::MODE_SAMESITE : backup_controller::MODE_GENERAL;
$admin = get_admin();
$tempdir = restore_controller::get_tempdir_name($course->id, $admin->id);
$qualifiedtempdir = make_temp_directory(sprintf('/backup/%s', $tempdir));
$target = $params['categoryid']
        ? backup::TARGET_NEW_COURSE : backup::TARGET_EXISTING_DELETING;

$packer = get_file_packer('application/vnd.moodle.backup');
$packer->extract_to_pathname($params['file'], $qualifiedtempdir);

$controller = new restore_controller(
        $tempdir, $course->id, backup::INTERACTIVE_NO, $mode, $admin->id,
        $target);
if (!$controller->execute_precheck()) {
    $results = $controller->get_precheck_results();
    if (!empty($results['errors'])) {
        $renderer = $PAGE->get_renderer('core', 'backup');
        /** @var core_backup_renderer $renderer */
        echo $renderer->precheck_notices($results);
        delete_course($course);
        cli_error('Aborting restore due to precheck failure');
    }
}
if ($target == backup::TARGET_EXISTING_DELETING) {
    restore_dbops::delete_course_content($course->id);
}
$controller->execute_plan();
$controller->destroy();
