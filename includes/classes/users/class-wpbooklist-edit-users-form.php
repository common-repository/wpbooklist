<?php
/**
 * WPBookList WPBookList_User_Form Class - class-wpbooklist-book-form.php
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes/Book
 * @version  6.1.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_User_Form', false ) ) :
	/**
	 * WPBookList_User_Form Class.
	 */
	class WPBookList_User_Form {

		/** Common member variable
		 *
		 *  @var object $trans
		 */
		public $trans = null;

		/** Common member variable
		 *
		 *  @var object $opt_results
		 */
		public $opt_results = null;

		/** Common member variable
		 *
		 *  @var array() $dynamic_libs
		 */
		public $dynamic_libs = array();

		/**
		 * Class constructor.
		 */
		public function __construct() {

			global $wpdb;

			// Get Translations.
			require_once CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
			$this->trans = new WPBookList_Translations();
			$this->trans->trans_strings();

			// Get all of the possible User-created Libraries.
			$table_name         = $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names';
			$this->dynamic_libs = $wpdb->get_results( 'SELECT * FROM ' . $table_name );

			// Get every single book, period.
			$this->all_books_array = array();
			$table_name            = $wpdb->prefix . 'wpbooklist_jre_saved_book_log';
			$default_array         = $wpdb->get_results( 'SELECT * FROM ' . $table_name );
			$this->all_books_array = array_merge( $this->all_books_array, $default_array );
			foreach ( $this->dynamic_libs as $db ) {
				if ( ( '' !== $db->user_table_name ) || ( null !== $db->user_table_name ) ) {
					$table_name            = $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name;
					$dyn_array             = $wpdb->get_results( 'SELECT * FROM ' . $table_name );
					$this->all_books_array = array_merge( $this->all_books_array, $dyn_array );
				}
			}

			// Building drop-down of all libraries.
			$this->libstring = '';
			foreach ( $this->dynamic_libs as $db ) {
				if ( ( '' !== $db->user_table_name ) || ( null !== $db->user_table_name ) ) {
					$this->libstring = $this->libstring . '<option value="' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . '">' . ucfirst( $db->user_table_name ) . '</option>';
				}
			}

			// For grabbing an image from media library.
			wp_enqueue_media();

		}

		/**
		 * Outputs the form for adding or editing a book.
		 */
		public function output_book_form() {

			global $wpdb;

			// Set the current WordPress user.
			$currentwpuser = wp_get_current_user();

			// Now we'll determine access, and stop all execution if user isn't allowed in.
			require_once CLASS_UTILITIES_DIR . 'class-wpbooklist-utilities-accesscheck.php';
			$this->access          = new WPBookList_Utilities_Accesscheck();
			$this->currentwpbluser = $this->access->wpbooklist_accesscheck( $currentwpuser->ID, 'createuser' );

			// If we received false from accesscheck class, display permissions message and stop all further execution.
			if ( false === $this->currentwpbluser ) {

				// Outputs the 'No Permission!' message.
				$this->initial_output = $this->access->wpbooklist_accesscheck_no_permission_message();
				return $this->initial_output;
			}

			// Get all current WPBookList Basic Users.
			$users = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_users_table' );

			$string1 = '';

			$user_string = '';
			foreach ( $users as $key => $user ) {

				// Build the User's Name
				$user_display_name = '';
				$user_display_name = $user->firstname . ' ' . $user->lastname;
				if ( ' ' === $user_display_name ) {
					$user_display_name = $user->username;
				}

				if ( ' ' === $user_display_name || '' === $user_display_name ) {
					$user_display_name = $user->email;
				}

				// Try getting a profile image.
				if ( '' === $user->profileimage || null === $user->profileimage ) {
					$user->profileimage = get_avatar_url( $user->wpuserid );
				}
				
				$user_string = $user_string . '<div class="wpbooklist-edit-book-indiv-div-class" id="wpbooklist-edit-book-indiv-div-id-' . $key . '" "="">
						<div class="wpbooklist-edit-title-div">
							<div class="wpbooklist-bulk-delete-checkbox-div">
								<input data-key=' . $key . ' data-table="wp_wpbooklist_jre_saved_book_log" data-book-id="1" class="wpbooklist-bulk-delete-checkbox" type="checkbox"><label>Delete Title</label>
							</div>
							<div class="wpbooklist-edit-img-author-div">
								<img data-bookid="1" data-bookuid="5bf2d1167febc" data-booktable="wp_wpbooklist_jre_saved_book_log" class="wpbooklist-edit-book-cover-img wpbooklist-show-book-colorbox" src="' . $user->profileimage . '">
								<p class="wpbooklist-edit-book-title">' . $user_display_name . '</p><br>
								<p class="wpbooklist-edit-book-author">' . $this->trans->trans_496 . ' ' . $user->datecreated . '</p>
							</div>
						</div>
						<div class="wpbooklist-edit-actions-div">
							<div class="wpbooklist-edituser-actions-edit-button" data-key=' . $key . ' data-table="wp_wpbooklist_jre_saved_book_log" data-wpuserid="' . $user->wpuserid . '">
								<p>Edit
									<img class="wpbooklist-edit-book-icon wpbooklist-edit-book-icon-button" src="http://localhost/local/wp-content/plugins/wpbooklist/assets/img/icons/pencil.svg"> 
								</p>
							</div>
							<div class="wpbooklist-edituser-actions-delete-button" data-key=' . $key . ' data-table="wp_wpbooklist_jre_saved_book_log" data-wpuserid="' . $user->wpuserid . '"> 
								<p>Delete
									<img class="wpbooklist-edit-book-icon wpbooklist-edit-book-icon-button" src="http://localhost/local/wp-content/plugins/wpbooklist/assets/img/icons/garbage-bin.svg">
								</p>
							</div>
						</div>
						<div class="wpbooklist-spinner" id="wpbooklist-spinner-' . $key . '"></div>
						<div class="wpbooklist-delete-result" id="wpbooklist-delete-result-' . $key . '"></div>
						<div class="wpbooklist-edit-form-div" id="wpbooklist-edit-form-div-' . $key . '">
							
						</div>
					</div>';
			}

			return $string1 . $user_string;
		}



	}

endif;
