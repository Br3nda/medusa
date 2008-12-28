<?php

class error {
        protected $error = array();

        /**
         * The return code for the error
         */
        function code($code) {
            assert(!is_null($code));
            $this->error['code'] = $code;
        }
        /**
         * The human readable message
         */
        function message($message) {
            assert(!is_null($message));
            $this->error['message'] = $message;
        }

        /**
         * Wrapper for code and message so we can do it on one line
         */
        function set($code, $message) {
            $this->code($code);
            $this->message($message);
        }

        /**
         * So we can append to our error
         */
        function message_append($message) {
            assert(!is_null($message));
            $this->error['message'] .= ' ' . $message;
        }

        /**
         * Render the error, in whichever format we want
         */
        function render($type = 'html') {
            switch ($type) {
                case 'html':
                    return $this->__render_html();
                    break;
                case 'json':
                    return $this->__render_json();
                    break;
                default:
                    echo "Error: Unable to render error";
                    break;
            }
        }
        /*
         * Private functions - we don't want others calling these directly
         * Yay for php5!
         */
        private function __render_html() {
            return htmlentities($this->error['code'] . ": " . $this->error['message']);
        }
        private function __render_json() {
            return json_encode($this->error);
        }
}
