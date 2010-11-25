#!/usr/bin/env php
<?php

require_once(dirname(__FILE__) . '/../lib/DevKit/Autoload.php');

DevKit_Autoload::setprefix('Example', dirname(__FILE__) . '/elib/Example');
DevKit_Autoload::setprefix('Example_File', dirname(__FILE__) . '/elib/Example/File');

var_dump( DevKit_Autoload::$_prefix );

DevKit_Autoload::discover('foo') ;    # fail
DevKit_Autoload::discover('Example');  # fail
DevKit_Autoload::discover('Example_File'); # success
DevKit_Autoload::discover('Example_File_NotThere'); # fail 
DevKit_Autoload::discover('Exampley');   # fail
DevKit_Autoload::discover('Example_Filey'); #fail

