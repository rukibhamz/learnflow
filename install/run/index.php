<?php

/** Install step entry point - ensures /install/run reaches Laravel. */
$boot = require dirname(__DIR__).'/bootstrap.php';
$boot('run');
