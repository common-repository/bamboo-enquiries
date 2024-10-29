<?php
/*****************************************************************************/

	// Exit if called directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

/*****************************************************************************/

	class BambooEnquiriesShortcodes {

/*****************************************************************************/

		public function __construct() {

			add_shortcode(
				'bamboo-enquiry',
				array( $this, 'shortcode_bamboo_enquiry' )
			);

		}

/*****************************************************************************/

		public function shortcode_bamboo_enquiry( $atts, $content=null ) {

			if( isset( $_POST['bamboo_enquiry_form_to_address'] ) ) {

				$this->send_enquiry();

				$message = ( isset( $atts['message'] ) ) ? $atts['message']   : 'Thank you<br/>We will be in touch shortly';

				$html = '<div class="bamboo_enquiry_message">';
				$html.= $message;
				$html.= '</div>';

				return $html;

			} else {

				if( isset( $atts['from'] ) ) {
					$from = $atts['from'];
				} else {
					$from = '';
				}

				if( isset( $atts['to'] ) ) {
					$to = $atts['to'];
				} else {
					$to = '';
				}

				if( isset( $atts['auto_labels'] ) ) {
					$auto_labels = $atts['auto_labels'];
				} else {
					$auto_labels = 'off';
				}

				if( isset( $atts['honeypot'] ) ) {
					$honeypot = $atts['honeypot'];
				} else {
					$honeypot = 'off';
				}

				do_action( 'before_bamboo_enquiry' );

				$html = "<form enctype=\"multipart/form-data\" class=\"bamboo_enquiry";
				if ( "on" == $auto_labels ) $html.= " auto_labels ";
				$html.= "\" method=\"post\" action=\"\">";

				$html.= "<input type=\"hidden\" name=\"bamboo_enquiry_form_to_address\" value=\"$to\"/>";
				$html.= "<input type=\"hidden\" name=\"bamboo_enquiry_form_from_address\" value=\"$from\"/>";
				$html.= "<input type=\"hidden\" name=\"bamboo_enquiry_form_honeypot\" value=\"$honeypot\"/>";

				if ( "on" == $honeypot ) {
					$html.= "<input type=\"hidden\" name=\"email\"/>";
				}

				$html.= do_shortcode($content);

				$html.= "</form>";

				do_action( 'after_bamboo_enquiry' );

				return $html;

			}

		}

/*****************************************************************************/

		private function send_enquiry() {

			global $post;

			// Address to send enquiries to.
			$to_address = $_POST["bamboo_enquiry_form_to_address"];

			// Address to send enquiries from.
			$from_address = $_POST["bamboo_enquiry_form_from_address"];

			// Honeypot indicator ("on" or "off").
			$honeypot = $_POST["bamboo_enquiry_form_honeypot"];

			// Default reply address if one is not supplied.
			$reply_address	= $from_address;

			// Start of the email subject.
			$subject = 'Website Enquiry';

			// Email introduction.
			$intro = '<p>There has been an enquiry sent from your website, the details are below:</p>';

			// Establish if the form is blank.
			$all_blank = true;
			foreach ( $_POST as $key => $value ) {
				if ( ( substr( $key, 0, 20) != "bamboo_enquiry_form_" && $key != "undefined" ) && ( $value != '' ) ) {
					$all_blank = false;
				}
			}

			// Establish if the honeypot has been triggered.
			$honeypot_triggered = false;
			if( "on"==$honeypot ) {
				$honeypot_value = '';
				if( isset( $_POST["email"] ) ) {
					$honeypot_value = $_POST["email"];
				}
				if( ''!= $honeypot_value ) {
					$honeypot_triggered = true;
				}
			}
			// If the form isn't blank and the honeypot hasn't been triggered we can send the enquiry.
			if( ! $all_blank && ! $honeypot_triggered) {


				// If an email address was supplied use it for the reply address.
				if( isset( $_POST["email"] ) ) {
					$reply_address = $_POST["email"];
				}
				if( isset( $_POST["Email"] ) ) {
					$reply_address = $_POST["Email"];
				}
				if( isset( $_POST["email_address"] ) ) {
					$reply_address = $_POST["email_address"];
				}
				if( isset( $_POST["Email_Address"] ) ) {
					$reply_address = $_POST["Email_Address"];
				}

				// Generate a random MIME content boundary.
				$mime_boundary = uniqid('noodle-enquiries');

				// Construct the headers.
				$headers = "MIME-Version: 1.0" . "\r\n";
				$headers.= "Content-Type: multipart/mixed;boundary=\"$mime_boundary\"\r\n";
				$headers.= "From: $from_address" . "\r\n";
				$headers.= "Reply-To: $reply_address" . "\r\n";

				// Contruct the form content.
				$content = '<p><strong>Page:</strong>&nbsp;';
				$content.= $post->post_title;
				$content.= '</p>';

				foreach ( $_POST as $key => $value ) {
					if( substr( $key, 0, 20 ) != "bamboo_enquiry_form_" && $key != "undefined" && $value != '' ) {
						if( is_array( $value ) ) {
							$text = '';
							foreach( $value as $val ) {
								if( ''!=$text ) {
									$text.=', ';
								}
								$text.= $val;
							}
						} else {
							$text = $value;
						}
						$content .= "<p><strong>" . str_replace( "_", " ", $key ) . ":</strong>&nbsp;" . $text . "</p>";
					}
				}

				// Construct the log entry.
				$log_entry = $post->post_title;
				$log_entry.= ',';

				foreach ( $_POST as $key => $value ) {
					if( substr( $key, 0, 20 ) != "bamboo_enquiry_form_" && $key != "undefined" ) {
						if( is_array( $value ) ) {
							$text = '';
							foreach( $value as $val ) {
								if( ''!=$text ) {
									$text.='; ';
								}
								$text.= $val;
							}
						} else {
							$text = $value;
						}
						$log_entry .= ',' . str_replace( ',', ';', $text );
					}
				}

				// Log the enquiry.
				$this->log_enquiry( $log_entry );

				// Inidicate in the email if there are files attached.
				$file_attached = false;
				foreach ( $_FILES as $key => $value ) {
					if( $_FILES[$key]["size"] > 0 ) {
						$file_attached = true;
					}
				}
				if( true==$file_attached ) {
					$content .= "<p><strong>File Attached</strong></p>";
				}

				// Wrap content in container tags.
				$content = "<html><head><title>$subject</title></head><body>$intro" . $content;
				$content .= "</body></html>";

				// Construct the message.
				$message  = "This is a MIME encoded message.";
				$message .= "\r\n\r\n--" . $mime_boundary . "\r\n";
				$message .= "Content-Type: text/html;charset=utf-8\r\n\r\n";
				$message .= $content;
				$message .= "\r\n\r\n--" . $mime_boundary . "\r\n";

				// Add any submitted files.
				foreach ( $_FILES as $key => $value ) {
					if( $_FILES[$key]["size"] > 0 ) {
						$message .= "Content-Type: {" . $_FILES[$key]["type"] . "}; name=\"" . $_FILES[$key]["name"] . "\"\r\n";
						$message .= "Content-Transfer-Encoding: base64\r\n";
						$message .= "Content-Disposition: attachment;\r\n; filename=\"" . $_FILES[$key]["name"] . "\"\r\n\r\n";
						$message .= chunk_split(base64_encode(file_get_contents($_FILES[$key]["tmp_name"])));
						$message .= "\r\n\r\n--" . $mime_boundary . "\r\n";
					}
				}

				// Send the message.
				mail( $to_address, $subject, $message, $headers );

			}

		}

/*****************************************************************************/

		private function log_enquiry( $entry ) {

			// Establish the path for the log file.
			$filepath = WP_CONTENT_DIR . '/enquiry_log/';
			if( !file_exists( $filepath ) ) {
				mkdir( $filepath );
			}

			// Ensure there is a .htaccess file preventing access to the directory.
			$access_file = $filepath . ".htaccess";
			if( !file_exists( $access_file ) ) {
				file_put_contents( $access_file, "deny from all\n" );
			}

			// Establish the filename for the log file.
			$filename = get_option( 'bamboo_enquiries_filename' );
			if( !$filename ) {
				$filename = $this->generate_filename();
				update_option( 'bamboo_enquiries_filename', $filename );
			}

			// Prepend a timestamp to the entry.
			$timestamp = date( 'j/n/Y H:i:s' );
			$entry = $timestamp . "," . $entry;

			// Add the entry to the log file.
	 		$file = fopen( $filepath . $filename, "a");
	 		fwrite( $file, $entry . "\r\n" );
	 		fclose( $file );

			return true;

		}

/*****************************************************************************/

		private function generate_filename() {

			$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
			$length = 20;

			$filename = '';
			for( $i=1; $i<=$length; $i++ ) {

				$rand = rand( 0, 61 );
				$char = substr( $characters, $rand, 1 );
				$filename .= $char;
			}

			return $filename . '.csv';

		}

/*****************************************************************************/

	}

/*****************************************************************************/
?>