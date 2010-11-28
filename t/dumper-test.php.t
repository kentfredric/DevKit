#!/usr/bin/env php
<?php

require_once(dirname(__FILE__) . '/../lib/DevKit/Autoload.php');

$t = new DevKit_Tester();

$x = array( );
$t->is( DevKit_Dumper::explain( $x ),
    "array:\n  items: 0\n  id: 1\n  data:\n", 'Empty Array Dump' );

$t->done_testing();

#print DevKit_Dumper::explain( $x );

#$y = $x;
#
#$t->is( DevKit_Dumper::explain( $x ), DevKit_Dumper::explain( $y ) );

#$t->done_testing();

#print DevKit_Dumper::explain( $x );
#print var_export( $x );
