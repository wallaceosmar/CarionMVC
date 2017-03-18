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

use Core\Load\Psr4Autoloader;

// Require the file for the PSR-4 Autoload Class
require_once ( CORE_CLS_PATH . 'Load' . DS . 'Psr4Autoloader.php' );

/* @var $autoload Core\Load\Psr4Autoloader */
global $autoload;

/**
 *
 * @global Core\Load\Psr4Autoloader $GLOBALS['autoloader']
 * 
 * @name $autoloader 
 */
$GLOBALS['autoload'] = new Psr4Autoloader();

// Seting the default namespaces
$autoload->addNamespaces([
    [ CORE_CLS_PATH , 'Core\\' ],
    [ APP_PATH, 'App\\' ],
    [ CORE_LIB_PATH, 'Lib\\' ]
]);

// Register the autoload
$autoload->register();

// Verify if the vendor for composer exists
if ( file_exists ( BASEPATH . 'vendor' . DS . 'autoload.php' ) ) {
    require_once ( BASEPATH . 'vendor' . DS . 'autoload.php' );
}