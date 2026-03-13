<?php

/** Install step entry point - ensures /install/requirements reaches Laravel. */
$boot = require dirname(__DIR__).'/bootstrap.php';
$boot('requirements');
