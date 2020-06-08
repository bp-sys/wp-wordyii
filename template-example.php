<?php
/*
 * Template Name: Example Template
 * Template Post Type: page
 */
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

require_once( CHILD_THEME_PATH .'/wordyii/controllers/ExampleController.php' );

$controller = new \wordyii\controllers\ExampleController();
$controller->run();

