<?php
/**
 * Class WPBookList_Save_Users_Data - class-wpbooklist--save-users-data.php
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Users
 * @version  0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBOOKLIST_Save_Users_Data', false ) ) :

	/**
	 * WPBOOKLIST_Save_Users_Data class. This class will hold all of the logic needed to save the user's Users data.
	 */
	class WPBOOKLIST_Save_Users_Data {

		/** Common member variable
		 *
		 *  @var string $human_date
		 */
		public $email = '';

		/** Common member variable
		 *
		 *  @var string $first_name
		 */
		public $first_name = '';

		/** Common member variable
		 *
		 *  @var string $last_name
		 */
		public $last_name = '';

		/** Common member variable
		 *
		 *  @var string $wpuserid
		 */
		public $wpuserid = '';

		/** Common member variable
		 *
		 *  @var object $transients
		 */
		public $transients = '';

		/** Common member variable
		 *
		 *  @var string $dbmode
		 */
		public $dbmode = '';

		/** Common member variable
		 *
		 *  @var string $users_table
		 */
		public $users_table = '';

		/** Common member variable
		 *
		 *  @var array $users_save_array
		 */
		public $users_save_array = array();

		/** Common member variable
		 *
		 *  @var string $db_result
		 */
		public $db_result = '';

		/** Common member variable
		 *
		 *  @var string $last_query
		 */
		public $last_query = '';

		/** Common member variable
		 *
		 *  @var string $transients_deleted
		 */
		public $transients_deleted = 'No Transients Deleted';

		/** Class Constructor
		 *
		 *  @param array $users_save_array - The user's array of data to save - all users items.
		 */
		public function __construct( $users_save_array = array() ) {

			global $wpdb;
			$this->users_save_array = $users_save_array;
			$this->email            = $users_save_array['email'];
			$this->wpuserid         = $users_save_array['wpuserid'];
			$this->first_name       = $users_save_array['firstname'];
			$this->last_name        = $users_save_array['lastname'];
			$this->users_table      = $wpdb->prefix . 'wpbooklist_jre_users_table';

			// Require the Transients file.
			require_once CLASS_TRANSIENTS_DIR . 'class-wpbooklist-transients.php';
			$this->transients = new WPBookList_Transients();

			// Determine if we're updating a row or inserting a new row.
			$this->wpbooklist_jre_determine_insert_or_update();

		}

		/**
		 *  Determine if we're updating a row or inserting a new row.
		 */
		public function wpbooklist_jre_determine_insert_or_update() {

			global $wpdb;
			$query = $wpdb->prepare( "SELECT * FROM $this->users_table WHERE (wpuserid = %d AND email = %s)", $this->wpuserid, $this->email );
			$wpdb->get_row( $query );

			if ( $wpdb->num_rows > 0 ) {
				$this->dbmode = 'update';
			} else {
				$this->dbmode = 'insert';
			}

			return $this->dbmode;

		}

		/**
		 *  Actually save the user's Users data.
		 */
		public function wpbooklist_jre_save_users_actual() {

			global $wpdb;

			// If we already have a row of saved data for this user on humandate, just update.
			if ( 'update' === $this->dbmode ) {

				// Update our custom table.
				$format = array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%d', '%s', '%s' );
				$where  = array( 'wpuserid' => $this->users_save_array['wpuserid'] );
				$where_format = array( '%d' );
				$this->db_result = $wpdb->update( $wpdb->prefix . 'wpbooklist_jre_users_table', $this->users_save_array, $where, $format, $where_format );

				// Now we'll update the WordPress user.
				$wp_user_array = array(
					'ID' => $this->users_save_array['wpuserid'],
					'user_email' => $this->users_save_array['email'],
					'first_name' => $this->users_save_array['firstname'],
					'last_name' => $this->users_save_array['lastname'],
				);

				$this->wp_db_result = wp_update_user( $wp_user_array );

			}

			// If we don't have data saved for this user.
			if ( 'insert' === $this->dbmode ) {

				// Try getting a profile image.
				if ( ! array_key_exists( 'profileimage', $this->users_save_array ) ) {
					$this->users_save_array['profileimage'] = null;
				}
				if ( '' === $this->users_save_array['profileimage'] || null === $this->users_save_array['profileimage'] ) {
					$this->users_save_array['profileimage'] = get_avatar_url( $this->users_save_array['wpuserid'] );
				}

				$this->db_result = $wpdb->insert( $this->users_table, $this->users_save_array, array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%d', '%s', '%s' ) );

			}

			$this->last_query = $wpdb->last_query;
			if ( false === $this->db_result ) {
				$this->db_result = $wpdb->last_error;
			}

			// If we modified the DB in any way (if there were no errors and if more than 0 rows were affected), then check for an existing applicable Transient and delete it.
			if ( $this->db_result > 0 ) {
				require_once CLASS_TRANSIENTS_DIR . 'class-wpbooklist-transients.php';
				$transients = new WPBookList_Transients();

				// Transients to check for and delete if they exist.
				$transient_name1 = 'wpbl_' . md5( 'SELECT * FROM ' . $this->users_table . ' ORDER BY firstname' );

				// Actually attempting to delete transients.
				$result1 = $transients->delete_transient( $transient_name1 );

				// Recording results of transient deletion (which were actually deleted, if any).
				if ( $result1 ) {
					$this->transients_deleted = '';
				}
				if ( $result1 ) {
					$this->transients_deleted = $this->transients_deleted . 'SELECT * FROM ' . $this->users_table . ' ORDER BY firstname';
				}

			}

			return $this->db_result;
		}
	}
endif;

