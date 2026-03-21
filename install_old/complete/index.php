<?php

/** Install step entry point - ensures /install/complete reaches Laravel. */
$boot = require dirname(__DIR__).'/bootstrap.php';
$boot('complete');
