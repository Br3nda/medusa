<?php
/**
 * @file
 Errors
 * @ingroup Error
 */


class response {
        protected $reponse = array();

        /**
         * The return code for the response
         */
        function code($code) {
            assert(!is_null($code));
            $this->response['code'] = $code;
        }
        /**
         * The human readable message
         */
        function message($message) {
            assert(!is_null($message));
            $this->response['message'] = $message;
        }

        /**
         * Wrapper for code and message so we can do it on one line
         */
        function set($code, $message) {
            $this->code($code);
            $this->message($message);
        }

        /**
         * So we can append to our response
         */
        function message_append($message) {
            assert(!is_null($message));
            $this->response['message'] .= ' ' . $message;
        }

        /**
         * Render the response, in whichever format we want
         */
        function render($type = 'html') {

            switch (strtolower($type)) {
                case 'html':
                    return $this->__render_html();
                    break;
                case 'json':
                    return $this->__render_json();
                    break;
                default:
                    return $this->__render_html();
                    break;
            }
        }
        /*
         * Private functions - we don't want others calling these directly
         * Yay for php5!
         */
        private function __render_html() {
            return htmlentities($this->response['code'] . ": " . $this->response['message']);
        }
        private function __render_json() {
            return json_encode($this->response);
        }
}
