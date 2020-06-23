<?php
/*
 * Template Name: Example Template
 * Template Post Type: page
 */
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

require_once( CHILD_THEME_PATH .'/wordyii/controllers/WordyiiExampleController.php' );
require_once( CHILD_THEME_PATH .'/wordyii/models/WordyiiExampleModel.php' );

$controller = new \wordyii\controllers\WordyiiExampleController();
$controller->run();

