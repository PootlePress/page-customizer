<?php
/**
 * Created by PhpStorm.
 * User: shramee
 * Date: 30/9/15
 * Time: 1:50 PM
 */
global $page_customizer_fields;
$page_customizer_fields = array(

	//Background Controls
	'background-type'             => array(
		'id'      => 'background-type',
		'section' => 'Background',
		'label'   => 'Background',
		'type'    => 'select',
		'choices' => array(
			'color' => 'Color',
			'image' => 'Image',
			'video' => 'Video',
		),
		'default' => '',
	),
	'background-video'            => array(
		'id'      => 'background-video',
		'section' => 'Background',
		'label'   => 'Background Video',
		'type'    => 'upload',
		'mime-type' => 'video',
		'default' => '',
	),
	'background-responsive-image' => array(
		'id'      => 'background-responsive-image',
		'section' => 'Background',
		'label'   => 'Responsive image',
		'type'    => 'image',
		'default' => '',
	),
	'background-image'            => array(
		'id'      => 'background-image',
		'section' => 'Background',
		'label'   => 'Page background image',
		'type'    => 'image',
		'default' => '',
	),
	'background-repeat'           => array(
		'id'      => 'background-repeat',
		'section' => 'Background',
		'label'   => 'Background repeat',
		'type'    => 'radio',
		'default' => 'repeat',
		'choices' => array(
			'no-repeat' => 'No Repeat',
			'repeat'    => 'Tile',
			'repeat-x'  => 'Tile Horizontally',
			'repeat-y'  => 'Tile Vertically',
		)
	),
	'background-position'         => array(
		'id'      => 'background-position',
		'section' => 'Background',
		'label'   => 'Background position',
		'type'    => 'radio',
		'default' => 'center',
		'choices' => array( 'left' => 'Left', 'center' => 'Center', 'right' => 'Right' )
	),
	'background-attachment'       => array(
		'id'      => 'background-attachment',
		'section' => 'Background',
		'label'   => 'Background attachment',
		'type'    => 'radio',
		'default' => 'scroll',
		'choices' => array( 'fixed' => 'Fixed', 'scroll' => 'Scroll' )
	),
	'background-color'            => array(
		'id'      => 'background-color',
		'section' => 'Background',
		'label'   => 'Page background color',
		'type'    => 'color',
		'default' => '',
	),

	//Header Options
	'hide-header'                 => array(
		'id'      => 'hide-header',
		'section' => 'Header',
		'label'   => 'Hide header',
		'type'    => 'checkbox',
		'default' => '',
	),
	'header-background-image'     => array(
		'id'      => 'header-background-image',
		'section' => 'Header',
		'label'   => 'Header background image',
		'type'    => 'image',
		'default' => '',
	),
	'header-background-color'     => array(
		'id'      => 'header-background-color',
		'section' => 'Header',
		'label'   => 'Header background color',
		'type'    => 'color',
		'default' => '',
	),

	//Content
	'hide-breadcrumbs'            => array(
		'id'      => 'hide-breadcrumbs',
		'section' => 'Content',
		'label'   => 'Hide breadcrumbs',
		'type'    => 'checkbox',
		'default' => '',
	),
	'hide-title'                  => array(
		'id'      => 'hide-title',
		'section' => 'Content',
		'label'   => 'Hide title',
		'type'    => 'checkbox',
		'default' => '',
	),

	//Footer
	'hide-footer'                 => array(
		'id'      => 'hide-footer',
		'section' => 'Footer',
		'label'   => 'Hide footer',
		'type'    => 'checkbox',
		'default' => '',
	),

);
