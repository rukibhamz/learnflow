<?php

/** Install step entry point - ensures /install/application reaches Laravel. */
$boot = require dirname(__DIR__).'/bootstrap.php';
$boot('application');
