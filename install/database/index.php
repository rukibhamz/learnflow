<?php

/** Install step entry point - ensures /install/database reaches Laravel. */
$boot = require dirname(__DIR__).'/bootstrap.php';
$boot('database');
