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
require_once "{$CFG->libdir}/clilib.php";
require_once "{$CFG->libdir}/datalib.php";
require_once "{$CFG->dirroot}/backup/util/includes/backup_includes.php";

list($params, $unrecognized) = cli_get_params([
    'file' => '',
    'type' => '',
    'id' => 0,
    'samesite' => false,
    'help' => false,
], [
    'h' => 'help',
]);

if ($params['help']) {
    $help = <<<HELP
Backup CLI

Options:
    --file=PATH           Output file to write the backup to
    --type=TYPE           Object type (course, section or activity)
    --id=ID               Object ID
    --samesite            Generate the backup for use on the same site (excludes files, faster)
    -h, --help            This message
HELP;
    mtrace($help);
    exit;
}

if (!$params['file']) {
    cli_error('--file is required');
}
if (!$params['type'] || !in_array($params['type'], ['course', 'section', 'activity'])) {
    cli_error('--type must be one of course, section or activity');
}
if (!$params['id']) {
    cli_error('--id is required');
}

$mode = $params['samesite'] ? backup::MODE_SAMESITE : backup::MODE_GENERAL;
$admin = get_admin();

$controller = new backup_controller(
        $params['type'], $params['id'], backup::FORMAT_MOODLE,
        backup::INTERACTIVE_NO, $mode, $admin->id);
$controller->execute_plan();
$results = $controller->get_results();
$results['backup_destination']->copy_content_to($params['file']);
$results['backup_destination']->delete();
$controller->destroy();
