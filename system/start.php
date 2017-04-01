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

use CarionMVC\Core\App;

/* @var $app CarionMVC\Core\App */
global $app;

// Require the constant of the project
require_once ( dirname( __FILE__ ) . DS . 'include' . DS . 'default-constants.php' );

// Seting the autoloader
require_once ( CORE_INC_PATH . 'inc.autoload.php' );

// Adding common functions
require_once ( CORE_FNC_PATH . 'common.php' );

$app = App::singleton();

// Initialize error
require_once ( CORE_INC_PATH . 'inc.error.php' );

$app->run();