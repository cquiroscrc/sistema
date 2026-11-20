<?php
/*
 * Copyright (C) 2017-2020 CRLibre <https://crlibre.org>
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

/**
 * Boot up procedure
 */
function callback_bootMeUp()
{
    // Just booting up
}

/**
 * Init function
 */
 function callback_init()
 {
    $paths = array(
        array(
            'r'         => 'callback',
            'action'    => 'api_callback',
            'access'    => 'users_openAccess',
            'file'      => 'callBack.php'
        )
    );

    return $paths;
}

/* * *********************************************** */
//In the access you can use users_openAccess if you want anyone can use the function
// or users_loggedIn if the user must be logged in
/* * *********************************************** */

/**
 * Get the perms for this module
 */
function callback_init_access()
{
    $perms = array(
        array(
            # A human readable name
            'name'          => 'callback_init_access',
            # Something to remember what it is for
            'description'   => 'Obtiene la respuesta de hacienda de una consulta en modo callbackn',
            # Internal machine name, no spaces, no funny symbols, same rules as a variable
            # Use yourmodule_ prefix
            'code'          => 'callback_init',
            # Default value in case it is not set
            'def'           => false, //Or true, you decide
        ),
    );
}

/**@}*/
/** @}*/
