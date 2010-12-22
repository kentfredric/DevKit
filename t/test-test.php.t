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
DevKit_ErrorHandler::setup();

$t->pass("A passing test");
# $t->fail("A failing test");
$t->is( 1 , "1", "String 1 is numeric 1");
$t->isnt_exactly( 1, "1", "String 1 isn't exactly numeric 1");
$t->isnt( 1, "2", "String 1 is not numeric 2");
$t->is_exactly( 1, 1, "Numeric 1 is numeric 1!");

class Dummy {
    public function foo(){
        return "Yes";
    }
}

class MoarDummy { }
class MuchMoarDummy {
    public function __construct( $mandatoryarg ) {
    }
}

$instance = $t->new_lives("Can make classes", 'Dummy', array() );
$rval   =   $t->method_call_lives('Can call methods', $instance, 'foo', array( ) );

$t->is( $rval , "Yes" , "value passing works");

$notaninstance = $t->new_dies("Cant make class", 'I_DONT_EXIST', array() );
$t->diag_exception( $notaninstance );
$notaninstance = $t->new_dies("Cant make class", 'MoarDummy', array(1) );
$t->diag_exception( $notaninstance );
$notaninstance = $t->new_dies("Cant make class", 'MuchMoarDummy', array() );
$t->diag_exception( $notaninstance );



$t->done_testing();
