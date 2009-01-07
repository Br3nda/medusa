<?php

/**
 * Base object for all wrms objects, such as work requests, time sheet entries, users, etc
 */
abstract class WrmsBase
{
    // Force Extending class to define this method
    abstract public function populate($row);
}

?>
