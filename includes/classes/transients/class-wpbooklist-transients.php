<?php
/**
 * Class WPBookList_Transients - class-wpbooklist-transients.php
 *
 * @author   Jake Evans
 * @category Transients
 * @package  Includes/Classes/Transients
 * @version  6.1.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Transients', false ) ) :
	/**
	 * WPBookList_Transients class. This class will house all the Transients stuff
	 */
	class WPBookList_Transients {

		/**
		 *  Function that checks for existing transient.
		 *
		 *  @param string $transient_name - The transient name to check for.
		 */
		public function existing_transient_check( $transient_name ) {

			$transient_actual = false;
			if ( false === get_transient( $transient_name ) ) {
				return false;
			} else {
				$transient_actual = get_transient( $transient_name );
				return $transient_actual;
			}
		}

		/**
		 *  Function that checks for existing transient.
		 *
		 *  @param string $transient_name - The transient name to check for.
		 *  @param string $wpdb_type - The type of db call to be used.
		 *  @param string $query - The query to be used in the db call.
		 *  @param string $ttl - How long the transient will exist.
		 */
		public function create_transient( $transient_name, $wpdb_type, $query, $ttl ) {

			global $wpdb;

			switch ( $wpdb_type ) {
				case 'wpdb->get_results':
					$query_result = $wpdb->get_results( $query );
					break;
				case 'wpdb->get_row':
					$query_result = $wpdb->get_row( $query );
					break;
				case 'wpdb->get_var':
					$query_result = $wpdb->get_var( $query );
					break;

				default:
					// code...
					break;
			}

			$set_result = set_transient( $transient_name, $query_result, $ttl );
			return $query_result;
		}

		/**
		 *  Function that checks for existing transient.
		 *
		 *  @param string $transient_name - The transient name to check for.
		 *  @param string $data - The data to save in the transient - in this case, the result of the Amazon Product API call.
		 *  @param string $ttl - How long the transient will exist.
		 */
		public function create_api_transient( $transient_name, $data, $ttl ) {
			$set_result = set_transient( $transient_name, $data, $ttl );
			return $set_result;
		}

		/**
		 *  Function that deletes transient.
		 *
		 *  @param string $transient_name - The transient name to check for.
		 */
		public function delete_transient( $transient_name ) {
			return delete_transient( $transient_name );
		}

		/**
		 *  Function that deletes all wpbl transients.
		 */
		public function delete_all_wpbl_transients() {

			global $wpdb;
			$result = $wpdb->query( 'DELETE FROM ' . $wpdb->prefix . "options WHERE option_name LIKE ('%wpbl\_%')" );

			return $result;
		}
	}
endif;
