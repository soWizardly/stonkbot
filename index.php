<?php
require 'bootstrap/bootstrap.php';

$container[\App\Communication\ConnectionManager::class]->run();
