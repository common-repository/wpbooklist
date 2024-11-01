<?php
/**
 * WPBookList Display Options Class
 * Handles functions for:
 * - Saving display options for Library
 * - Saving display options for Posts
 * - Saving display options for Pages
 * @author   Jake Evans
 * @category Root Product
 * @package  Includes/Classes
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Display_Options', false ) ) :

	/**
	 * WPBookList_Display_Options.
	 */
	class WPBookList_Display_Options {

		/**
		 * Class Constructor - Simply calls the Translations
		 */
		public function __construct() {

			// Require the Transients file.
			require_once CLASS_TRANSIENTS_DIR . 'class-wpbooklist-transients.php';
			$this->transients = new WPBookList_Transients();
		}

		/** Function to save Display Options.
		 *
		 * @param string $table - The Library these display options are being applied to.
		 * @param array  $settings_array - The actual settings to save.
		 */
		public function save_library_settings( $table, $settings_array = array() ) {

			global $wpdb;
			$final_table = '';
			if ( false !== strpos( $table, 'wpbooklist_jre_saved_book_log' ) ) {
				$final_table = $wpdb->prefix . 'wpbooklist_jre_user_options';
			} else {
				$temp        = explode( '_', $table );
				$size        = count( $temp );
				$temp        = $temp[ $size - 1 ];
				$final_table = $wpdb->prefix . 'wpbooklist_jre_settings_' . $temp;
			}

			foreach ( $settings_array as $key => $value ) {

				if ( 'customfieldsarray' === $key && is_array( $value ) ) {

					foreach ( $value as $key => $customfield ) {

						if ( false === stripos( $customfield, 'undefined' ) ) {
							$field = explode( ';', $customfield );

							if ( 'false' === $field[1] ) {
								$field[1] = 0;
							}

							if ( 'true' === $field[1] ) {
								$field[1] = 1;
							}

							$settings_array[ 'hide'.$field[0] ] = $field[1];

						}
					}
				}

				// Remove the 'customfieldsarray' entry now that we're done with it, as that column does not exist in any of the db tables - was just for passing data to this function.
				if ( 'customfieldsarray' === $key ) {
					unset( $settings_array['customfieldsarray'] );
				}

				if ( 'false' === $value ) {
					$settings_array[ $key ] = 0;
				}

				if ( 'true' === $value ) {
					$settings_array[ $key ] = 1;
				}
			}

			$where  = array( 'ID' => 1 );
			$result = $wpdb->update( $final_table, $settings_array, $where );

			// Delete all existing WPBookList Transients.
			$result = $this->transients->delete_all_wpbl_transients();

		}

		/** Function to save Post Display Options.
		 *
		 * @param array $settings_array - The actual settings to save.
		 */
		public function save_post_settings( $settings_array = array() ) {
			global $wpdb;
			$table = $wpdb->prefix . 'wpbooklist_jre_post_options';

			foreach ( $settings_array as $key => $value ) {

				if ( 'customfieldsarray' === $key && is_array( $value ) ) {

					foreach ( $value as $key => $customfield ) {

						if ( false === stripos( $customfield, 'undefined' ) ) {
							$field = explode( ';', $customfield );

							if ( 'false' === $field[1] ) {
								$field[1] = 0;
							}

							if ( 'true' === $field[1] ) {
								$field[1] = 1;
							}

							$settings_array[ 'hide'.$field[0] ] = $field[1];

						}
					}
				}

				// Remove the 'customfieldsarray' entry now that we're done with it, as that column does not exist in any of the db tables - was just for passing data to this function.
				if ( 'customfieldsarray' === $key ) {
					unset( $settings_array['customfieldsarray'] );
				}

				if ( 'false' === $value ) {
					$settings_array[ $key ] = 0;
				}

				if ( 'true' === $value ) {
					$settings_array[ $key ] = 1;
				}
			}

			$where  = array( 'ID' => 1 );
			$result = $wpdb->update( $table, $settings_array, $where );

			// Delete all existing WPBookList Transients.
			$result = $this->transients->delete_all_wpbl_transients();

		}

		/** Function to save Page Display Options.
		 *
		 * @param array $settings_array - The actual settings to save.
		 */
		public function save_page_settings( $settings_array = array() ) {

			global $wpdb;
			$table = $wpdb->prefix . 'wpbooklist_jre_page_options';

			foreach ( $settings_array as $key => $value ) {

				if ( 'customfieldsarray' === $key && is_array( $value ) ) {

					foreach ( $value as $key => $customfield ) {

						if ( false === stripos( $customfield, 'undefined' ) ) {
							$field = explode( ';', $customfield );

							if ( 'false' === $field[1] ) {
								$field[1] = 0;
							}

							if ( 'true' === $field[1] ) {
								$field[1] = 1;
							}

							$settings_array[ 'hide'.$field[0] ] = $field[1];

						}
					}
				}

				// Remove the 'customfieldsarray' entry now that we're done with it, as that column does not exist in any of the db tables - was just for passing data to this function.
				if ( 'customfieldsarray' === $key ) {
					unset( $settings_array['customfieldsarray'] );
				}

				if ( 'false' === $value ) {
					$settings_array[ $key ] = 0;
				}

				if ( 'true' === $value ) {
					$settings_array[ $key ] = 1;
				}
			}

			$where  = array( 'ID' => 1 );
			$result = $wpdb->update( $table, $settings_array, $where );

			// Delete all existing WPBookList Transients.
			$result = $this->transients->delete_all_wpbl_transients();
		}
	}

endif;