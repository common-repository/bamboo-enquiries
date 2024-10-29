<?php
/*****************************************************************************/
/*
Plugin Name: Bamboo Enquiries
Plugin URI:  https://www.bamboomanchester.uk/wordpress/bamboo-enquiries
Author:      Bamboo Mcr
Author URI:  https://www.bamboomanchester.uk
Version:     1.9.3
Description: Turn any web form into a flexible enquiry form, enabling you to have multiple enquiry forms throughout your website.
*/
/*****************************************************************************/

	// Exit if called directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

/*****************************************************************************/

	include 'classes/bamboo-enquiries.php';
	$bamboo_enquiries = new BambooEnquiries;

	include 'classes/bamboo-enquiries-shortcodes.php';
	$bamboo_enquiries_shortcodes = new BambooEnquiriesShortcodes;

/*****************************************************************************/
?>
