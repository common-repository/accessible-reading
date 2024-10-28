<?php

/**
 * Accessible Reading Plugin
 *
 * @package AuRise\Plugin\AccessibleReading
 * @copyright Copyright (c) 2022, AuRise Creative - support@aurisecreative.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 *
 * Plugin Name: Accessible Reading
 * Plugin URI: https://aurisecreative.com/accessible-reading/
 * Description: Make your website more accessible by offering a reading accommodation to your disabled and neurodivergent readers by adding Bionic Reading® as a font choice!
 * Version: 2.1.1
 * Author: AuRise Creative
 * Author URI: https://aurisecreative.com/
 * License: GPL v3
 * Requires at least: 5.8
 * Requires PHP: 5.6.20
 * Text Domain: accessible-reading
 * Domain Path: /languages/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Define root file
defined('ACCESSIBLEREADING_FILE') || define('ACCESSIBLEREADING_FILE', __FILE__);

defined('ACCESSIBLEREADING_VERSION') || define('ACCESSIBLEREADING_VERSION', '2.1.1');

// Load the utilities class: AuRise\Plugin\AccessibleReading\Utilities
require_once('includes/class-utilities.php');

// Load the settings class: AuRise\Plugin\AccessibleReading\Settings
require_once('includes/class-settings.php');

// Load the frontend class: AuRise\Plugin\AccessibleReading\Frontend
require_once('includes/class-frontend.php');

// Load the main plugin class: AuRise\Plugin\AccessibleReading\AccessibleReading
require_once('includes/class-main.php');
