<?php

    /*
     * Various 'magic' numbers that WRMS uses for work request trickery
     * and aren't enforced in the database so we have to code them in :/
     */

    static $WRMS_WR_URGENCY_CODES = array(0, 10, 20, 30, 40, 50, 60);
    static $WRMS_WR_TYPE_CODES = array(0, 1, 10, 20, 30, 40, 50, 60, 70, 80, 90);
    static $WRMS_WR_IMPORTANCE_CODES = array(0, 10, 20, 30);
    static $WRMS_WR_STATUS_CODES = array('N', 'R', 'H', 'C', 'I', 'L', 'F', 'K', 'T', 'Q', 'A', 'D', 'S', 'P', 'Z', 'U', 'V', 'W', 'B', 'O');

?>
