<?php
/*****************************************************************************/

	// Exit if called directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

/*****************************************************************************/

	class BambooEnquiries {

/*****************************************************************************/

		public function __construct() {

			add_action(
				'wp_enqueue_scripts',
				array( $this, 'enqueue_scripts' )
			);

			add_action(
				'wp_enqueue_scripts',
				array( $this, 'enqueue_styles' )
			);

		}

/*****************************************************************************/

		public function enqueue_styles() {

			$path = plugins_url( '', __FILE__ ) . '/../css';

			wp_enqueue_style(
				'bamboo-enquiries',
				$path . '/bamboo-enquiries.css',
				array(),
				null
			);

		}

/*****************************************************************************/

		public function enqueue_scripts() {

			$path = plugins_url( '', __FILE__ ) . '/../js';

			wp_enqueue_script( 'jquery' );

			wp_enqueue_script(
				'bamboo-scrollTo',
				$path . '/jquery.scrollTo.min.js',
				'jquery',
				null,
				true
			);

			wp_enqueue_script(
				'bamboo-easing',
				$path . '/jquery.easing.min.js',
				'jquery',
				null,
				true
			);

	    	wp_enqueue_script(
				'bamboo-enquiries',
				$path . '/bamboo-enquiries.min.js',
				null,
				null,
				true
			);

		}

/*****************************************************************************/

	}

/*****************************************************************************/
?>