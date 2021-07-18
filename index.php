<?php
declare(strict_types=1);
/**
 * Test of the library Eznawca/Comm
 * 
 * @author  Andrzej Mazur
 * @version 0.1
 * @package main
 */
require __DIR__ . '/vendor/autoload.php';

use Eznawca\Comm\Comm;

$testStr = 'To - test examining the str2slug() function';

r(Comm::str2slug($testStr), 'Result fn str2slug()');
