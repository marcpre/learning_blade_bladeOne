<?php
require "vendor/autoload.php";

use eftec\bladeone;

$views = __DIR__ . '/views';
$cache = __DIR__ . '/cache';
define("BLADEONE_MODE", 1); // (optional) 1=forced (test),2=run fast (production), 0=automatic, default value.
$blade = new bladeone\BladeOne($views,$cache);

