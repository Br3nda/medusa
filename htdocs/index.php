<?php
/**
 * @mainpage {Medusa} the WRMS API
 *
 * Requirements
 * * Convert existing SQL access, fomr catlayst LAN to Read only - see WRMS/Direct Sql Access 
* Close off psql access from LAN for the general users (that's the one with the super rights) 
* Create a new API to encapsulate the business logic of WRMS, codename = Medusa 
* Convert WRMS web app to use Medusa instead of SQL
* Set up copy if Ned from Egressive, and get working with Medusa
* Get other apps (and teh scripts used by catalysters) to all user Medusa
 *
 *   - @link /api/groups Topics @endlink
 *   - @link /api/constants Constants @endlink
 *   - @link /api/globals Global variables @endlink
*/

require('../lib/request/request.class.php');

$uri = $_SERVER['REQUEST_URI'];
$request = new request($uri);
$method = $request->method();
$params = $request->params();
$formats = $request->format();



//TODO: Work out method
//if (is_callable($method)
//TODO: Check method exists
//TODO: Work out params
//TODO Call method
//TODO Work out form (xml? json? yaml?)
//TODO Encode as requested and ouput


