<?php
/*
 * Copyright (C) 2018 CRLibre <https://crlibre.org>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

global $config;


# A private token in order to run cron, but I don't think this will be needed
# Not in use yet, you may ignore this
$config['cron']['cronToken'] = 'ItIsGoodIfThisIsBigAndHasW3irDLeeT3rsAnd$ymb0lz.IniT';

# List of core modules
$config['modules']['core']     = array('cala', 'db', 'users', 'files', 'geoloc', 'wirez','crypto');
# List of core modules to load always, you can overide this list
$config['modules']['coreLoad'] = array('cala', 'db', 'users','crypto');

# Am I running in CLI mode?
$config['core']['cli'] = false;

#####################################################################################
#
# Grace: Only touch if you need to debug
#
#####################################################################################
# Where do you want me to store the logs? USE TRAILING SLASH!
# If false I will not store them
$config['grace']['logPath'] = false;// $config['modules']['coreInstall'] . "logs/";

#####################################################################################
#
# Files, Uploads and such
#
#####################################################################################

# Location to upload files, USE TRAILING SLASH!!
# Each user will have its own directory within this path
#$config['files']['basePath']      = $config['modules']['coreInstall'] . 'files/';
# Maximum upload size in Mb
$config['files']['maxUploadSize'] = 2;
# Default allowed extensions
$config['files']['allowedExt'] = 'jpg,png,p12,P12,.p12,.P12';

