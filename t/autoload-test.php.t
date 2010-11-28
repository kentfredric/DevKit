#!/usr/bin/env php
<?php

require_once(dirname(__FILE__) . '/../lib/DevKit/Autoload.php');

$t = new DevKit_Tester();

DevKit_Autoload::setprefix('Example', dirname(__FILE__) . '/tlib/Example');
DevKit_Autoload::setprefix('Example_File', dirname(__FILE__) . '/tlib/Example/File');

$t->is_exactly(   DevKit_Autoload::discover('foo'),                   false, 'Module not defined anywhere in system tree cannot be loaded' );
$t->is_exactly(   DevKit_Autoload::discover('Example'),               false, 'Module that is missing, but has a matching prefix rule cannot be loaded');
$t->isnt_exactly( DevKit_Autoload::discover('Example_File'),          false, 'Module that is the base of a prefix should be loadable');
$t->is_exactly(   DevKit_Autoload::discover('Example_File_NotThere'), false, 'File in a subdir is missing');
$t->is_exactly(   DevKit_Autoload::discover('Exampley'),              false, 'Prefixy things do not match');
$t->is_exactly(   DevKit_Autoload::discover('Example_Filey'),         false, 'More prefixy things do not match');

$t->done_testing();
