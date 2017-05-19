<?php ! defined('BASEPATH') && exit( 'No direct script access allowed' );

/*
 * The MIT License
 *
 * Copyright 2017 Wallace Osmar.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Carion\Error {
    
    use \Exception;
    
    /**
     * CarionErrorHandler
     * 
     * This class will handlers for errors, exceptions and shutdowns.
     * 
     * ### Uncaught exceptions
     *
     * @author Wallace Osmar <wallace.osmar@hotmail.com>
     * @since 0.0.1
     */
    class CarionErrorHandler {
        
        /**
         *
         * @var \Carion\Http\ServerResponse 
         */
        protected $response;
        
        /**
         *
         * @var bool 
         */
        protected $debug;
        
        public function __construct( $response ) {
            $this->response = $response;
            $this->debug = error_reporting();
        }
        
        /**
         * 
         * @param Exception $exception
         * 
         * @todo Implementig this function
         */
        public function handleException( $exception ) {
            ob_clean();
            if ( $this->debug ) {
                $title = 'Carion Application Error';
                $exception->type = get_class( $exception );
            }
            
            echo <<<HTML
<!DOCTYPE html>
<html>
    <head>
        <title>{$title}</title>
        <style>
            body { margin: 0; padding: 30px; font: 12px/1.5 Helvetica, Arial, Verdana, sans-serif; }
            h1 {margin: 0;font-size: 48px;font-weight: normal;line-height: 48px; }
            strong { display: inline-block; width: 65px; }
        </style>
    </head>

    <body>
        <h1>{$title}</h1>
        <p>The application could not run because of the following error:</p>
        <h2>Details</h2>
        <p><strong>Type:</strong> {$exception->type}</p>
        <p><strong>Code:</strong> {$exception->getCode()}</p>
        <p><strong>Message:</strong> {$exception->getMessage()}</p>
        <p><strong>File:</strong> {$exception->getFile()}</p>
        <p><strong>Line:</strong> {$exception->getLine()}</p>
        <h2>Trace</h2>
        <pre>{$exception->getTraceAsString()}</pre>
    </body>
</html>
HTML;
            
            $this->response->withBody(ob_get_contents());
            ob_end_clean();
            die( $this->response->getBody());
        }
        
        /**
         * 
         * @param int $code Code of error
         * @param string $description Error description
         * @param string $file File on which error occurred
         * @param int $line Line that triggered the error
         * @param array $context Context
         * 
         * @return bool 
         * 
         * @link http://php.net/manual/pt_BR/function.set-error-handler.php#refsect1-function.set-error-handler-parameters  Define uma função do usuário para manipular erros
         * 
         * @todo Implementig this function
         */
        public function handleError($code, $description, $file = null, $line = null, $context = null) {
            
        }
        
        /**
         * 
         * @param int $code
         * @param string $description
         * @param string $file
         * @param int $line
         * 
         * @todo Implementig this function
         */
        public function handleFatalError ( $code = null, $description = null, $file = null, $line = null ) {
            
        }
        
        /**
         * 
         * @param int $code Error code to map
         * 
         * @return array
         */
        public function mapErrorCode( $code ) {
            $error = null;
            switch ( $code ) {
                case E_PARSE:
                case E_ERROR:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                    $error = 'Fatal Error';
                    break;
                case E_WARNING:
                case E_USER_WARNING:
                case E_CORE_WARNING:
                case E_COMPILE_WARNING:
                case E_RECOVERABLE_ERROR:
                    $error = 'Warning';
                    break;
                case E_NOTICE:
                case E_USER_NOTICE:
                    $error = 'Notice';
                    break;
                case E_STRICT:
                    $error = 'Strict';
                case E_DEPRECATED:
                case E_USER_DEPRECATED:
                    $error = 'Deprecated';
                    break;
            }
            return [ $error ];
        }
        
        /**
         * Register the handler for error, exception and shutdown
         * 
         * @todo Implement the shutdow handler function
         */
        public function register() {
            set_error_handler([ $this, 'handleError' ]);
            set_exception_handler([$this, 'handleException']);
        }
        
    }
    
}