<?php

class error {
        protected $error = array();

        /*
         * The return code for the error
         */
        function code($code) {
            assert(!is_null($code));
            $this->error['code'] = $code;
        }
        /*
         * The human readable message
         */
        function message($message) {
            assert(!is_null($message));
            $this->error['message'] = $message;
        }

        /*
         * Wrapper for code and message so we can do it on one line
         */
        function set($code, $message) {
            $this->code($code);
            $this->message($message);
        }

        /*
         * So we can append to our error
         */
        function message_append($message) {
            assert(!is_null($message));
            $this->error['message'] .= ' ' . $message;
        }

        /*
         * Render the error, in whichever format we want
         */
        function render($type = 'html') {
            if ($type == 'html') {
                echo $this->error['code'] . ": " . $this->error['message'];
            }
            else {
                echo "Error: Unable to render error";
            }
        }

}
