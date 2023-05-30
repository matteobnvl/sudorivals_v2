<?php

use App\Command\MigrateCommand;

require './app/Command/MigrateCommand.php';

$command = new MigrateCommand();

$script = isset($argv[1]) ? $argv[1] : null;

$command->execute($script);

