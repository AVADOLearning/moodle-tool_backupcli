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

defined('MOODLE_INTERNAL') || die;
/** @var stdClass $plugin */

$plugin->component = 'tool_backupcli';

$plugin->version = 2019030600;
$plugin->release = '0.1.0';
$plugin->maturity = MATURITY_ALPHA;
