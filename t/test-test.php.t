#!/usr/bin/env php
<?php

/* For extra fun, run this with 
 *
 * prove ./scripts/testtest.php 
 * and 
 * prove -v ./scripts/testtest.php
 */

require_once(dirname(__FILE__) . '/../lib/DevKit/Autoload.php');

$t = new DevKit_Tester();
$t->pass("A passing test");
# $t->fail("A failing test");
$t->is( 1 , "1", "String 1 is numeric 1");
$t->isnt_exactly( 1, "1", "String 1 isn't exactly numeric 1");
$t->isnt( 1, "2", "String 1 is not numeric 2");
$t->is_exactly( 1, 1, "Numeric 1 is numeric 1!");
$t->done_testing();
