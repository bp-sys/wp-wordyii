<?php
/*
 * Template Name: Example Template
 * Template Post Type: page
 */
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}


$controller = new \Wordyii\Controllers\WordyiiExampleController();
$controller->run();

