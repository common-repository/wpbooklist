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
		public function output_users_form() {

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

			$string1 = '<p class="wpbooklist-tab-intro-para">' . $this->trans->trans_460 . ':<br/><br/><span class="wpbooklist-color-orange-italic"> ' . $this->trans->trans_461 . ' </span><br/><span class="wpbooklist-color-orange-italic"> ' . $this->trans->trans_462 . ' </span><br/><span class="wpbooklist-color-orange-italic"> ' . $this->trans->trans_463 . ' </span><br/><span class="wpbooklist-color-orange-italic"> ' . $this->trans->trans_464 . ' </span><br/><br/>' . $this->trans->trans_465 . '<img id="wpbooklist-smile-icon-2" src="' . ROOT_IMG_ICONS_URL . 'happy.svg">';

			$string2 = '<div class="wpbooklist-book-form-container" id="wpbooklist-user-form-container">
							<div class="wpbooklist-book-form-inner-container">
								<div class="wpbooklist-book-form-inner-container-basic-fields">
									<div id="wpbooklist-addbook-select-library-label">
										<p>
											<img class="wpbooklist-icon-image-question-with-link" data-label="user-permissions-heading" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
											' . $this->trans->trans_477 . '
										</p>
									</div>
									<br/>
									<div class="wpbooklist-book-form-indiv-attribute-container">
										<img class="wpbooklist-icon-image-question" data-label="user-form-firstname" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
										<label class="wpbooklist-question-icon-label" for="user-firstname">' . $this->trans->trans_461 . '</label>
										<input type="text" id="wpbooklist-adduser-firstname" name="user-firstname">
									</div>
									<div class="wpbooklist-book-form-indiv-attribute-container">
										<img class="wpbooklist-icon-image-question" data-label="user-form-lastname" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
										<label class="wpbooklist-question-icon-label" for="user-lastname">' . $this->trans->trans_466 . '</label>
										<input type="text" id="wpbooklist-adduser-lastname" name="user-lastname">
									</div>
									<div class="wpbooklist-book-form-indiv-attribute-container">
										<img class="wpbooklist-icon-image-question" data-label="user-form-emailname" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
										<label class="wpbooklist-question-icon-label" for="user-emailname">' . $this->trans->trans_462 . '</label>
										<input type="text" id="wpbooklist-adduser-emailname" name="user-emailname">
									</div>
									<div class="wpbooklist-book-form-indiv-attribute-container">
										<img class="wpbooklist-icon-image-question" data-label="user-form-confirmemailname" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
										<label class="wpbooklist-question-icon-label" for="user-confirmemailname">' . $this->trans->trans_467 . '</label>
										<input type="text" id="wpbooklist-adduser-confirmemailname" name="user-confirmemailname">
									</div>
									<div class="wpbooklist-book-form-indiv-attribute-container">
										<img class="wpbooklist-icon-image-question" data-label="user-form-passwordname" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
										<label class="wpbooklist-question-icon-label" for="user-passwordname">' . $this->trans->trans_463 . '</label>
										<input type="password" id="wpbooklist-adduser-passwordname" name="user-passwordname">
									</div>
									<div class="wpbooklist-book-form-indiv-attribute-container">
										<img class="wpbooklist-icon-image-question" data-label="user-form-confirmpasswordname" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
										<label class="wpbooklist-question-icon-label" for="user-confirmpasswordname">' . $this->trans->trans_468 . '</label>
										<input type="password" id="wpbooklist-adduser-confirmpasswordname" name="user-confirmpasswordname">
									</div>
									<div class="wpbooklist-book-form-indiv-attribute-container">
										<img class="wpbooklist-icon-image-question" data-label="user-form-usernamename" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
										<label class="wpbooklist-question-icon-label" for="user-usernamename">' . $this->trans->trans_464 . '</label>
										<input type="text" id="wpbooklist-adduser-usernamename" name="user-usernamename">
									</div>';

			// This filter allows the addition of one or more rows of items into the Basic Fields section of the 'Add a User' form.
			if ( has_filter( 'wpbooklist_append_to_user_form_basic_fields' ) ) {
				$string2 = apply_filters( 'wpbooklist_append_to_user_form_basic_fields', $string2 );
			}

			$string2 = $string2 . '<div id="wpbooklist-user-form-show-passwords">' . $this->trans->trans_481 . '</div><br/><div class="wpbooklist-adduser-field-checks-div" id="wpbooklist-adduser-field-checks-email-div"></div>
									<div class="wpbooklist-adduser-field-checks-div" id="wpbooklist-adduser-field-checks-password-div"></div>
								</div>
								<div class="wpbooklist-user-form-inner-container-auth-fields">
									<div id="wpbooklist-addbook-select-library-label">
										<p>
											<img class="wpbooklist-icon-image-question-with-link" data-label="user-permissions-heading" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
											' . $this->trans->trans_469 . '
										</p>
										<br/>
										<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-exception">
											<img class="wpbooklist-icon-image-question" data-label="user-form-usernamename" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
											<label class="wpbooklist-question-icon-label" for="user-usernamename">' . $this->trans->trans_471 . '</label><br/>
											<select class="wpbooklist-addbook-select-default select2-input-libraries" id="wpbooklist-addbook-select-library" name="libraries[]" multiple="multiple">
												<option selected default value="' . $wpdb->prefix . 'wpbooklist_jre_saved_book_log">' . $this->trans->trans_61 . '</option> 
												<option value="alllibraries">' . $this->trans->trans_470 . '</option> 
												' . $this->libstring . '
											</select>
										</div>
										<div class="wpbooklist-book-form-indiv-attribute-container">
											<img class="wpbooklist-icon-image-question" data-label="user-form-usernamename" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
											<label class="wpbooklist-question-icon-label" for="user-usernamename">' . $this->trans->trans_472 . '</label>
											<select class="wpbooklist-addbook-select-default" id="wpbooklist-adduser-permissions-add-book">
												<option selected="" default="">' . $this->trans->trans_131 . '</option>
												<option>' . $this->trans->trans_132 . '</option>
											</select>
										</div>
										<div class="wpbooklist-book-form-indiv-attribute-container">
											<img class="wpbooklist-icon-image-question" data-label="user-form-usernamename" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
											<label class="wpbooklist-question-icon-label" for="user-usernamename">' . $this->trans->trans_473 . '</label>
											<select class="wpbooklist-addbook-select-default" id="wpbooklist-adduser-permissions-edit-book">
												<option selected="" default="">' . $this->trans->trans_131 . '</option>
												<option>' . $this->trans->trans_132 . '</option>
											</select>
										</div>
										<div class="wpbooklist-book-form-indiv-attribute-container">
											<img class="wpbooklist-icon-image-question" data-label="user-form-usernamename" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
											<label class="wpbooklist-question-icon-label" for="user-usernamename">' . $this->trans->trans_474 . '</label>
											<select class="wpbooklist-addbook-select-default" id="wpbooklist-adduser-permissions-delete-book">
												<option selected="" default="">' . $this->trans->trans_131 . '</option>
												<option>' . $this->trans->trans_132 . '</option>
											</select>
										</div>
										<div class="wpbooklist-book-form-indiv-attribute-container">
											<img class="wpbooklist-icon-image-question" data-label="user-form-usernamename" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
											<label class="wpbooklist-question-icon-label" for="user-usernamename">' . $this->trans->trans_475 . '</label>
											<select class="wpbooklist-addbook-select-default" id="wpbooklist-adduser-permissions-change-display-options">
												<option selected="" default="">' . $this->trans->trans_131 . '</option>
												<option>' . $this->trans->trans_132 . '</option>
											</select>
										</div>
										<div class="wpbooklist-book-form-indiv-attribute-container">
											<img class="wpbooklist-icon-image-question" data-label="user-form-usernamename" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
											<label class="wpbooklist-question-icon-label" for="user-usernamename">' . $this->trans->trans_476 . '</label>
											<select class="wpbooklist-addbook-select-default" id="wpbooklist-adduser-permissions-change-settings">
												<option selected="" default="">' . $this->trans->trans_131 . '</option>
												<option>' . $this->trans->trans_132 . '</option>
											</select>
										</div>';

			// This filter allows the addition of one or more rows of items into the Permissions Fields section of the 'Add a User' form.
			if ( has_filter( 'wpbooklist_append_to_user_form_auth_fields' ) ) {
				$string2 = apply_filters( 'wpbooklist_append_to_user_form_auth_fields', $string2 );
			}

			$string2 = $string2 . '</div>
								</div>
							</div>
							<div class="wpbooklist-response-success-fail-container">
					    		<button class="wpbooklist-response-success-fail-button wpbooklist-admin-adduser-add-button" type="button" id="wpbooklist-admin-adduser-create-button">' . $this->trans->trans_478 . '</button>
					    		<div class="wpbooklist-spinner" id="wpbooklist-spinner-1"></div>
					    		<div class="wpbooklist-response-success-fail-response-actual-container" id="wpbooklist-admin-adduser-response-actual-container"></div>
				    		</div>
						</div>
			';

			return $string1 . $string2;
		}


		/** Outputs the form for editing a user.
		 *
		 *  @param array $user_info - The array that contains the user's DB info.
		 */
		public function output_users_edit_form( $user_info ) {

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

			$string2 = '<div class="wpbooklist-book-form-container">
							<div class="wpbooklist-book-form-inner-container">
								<div class="wpbooklist-book-form-inner-container-basic-fields">
									<div id="wpbooklist-addbook-select-library-label">
										<p>
											<img class="wpbooklist-icon-image-question-with-link" data-label="user-permissions-heading" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
											' . $this->trans->trans_477 . '
										</p>
									</div>
									<br/>
									<div class="wpbooklist-book-form-indiv-attribute-container">
										<img class="wpbooklist-icon-image-question" data-label="user-form-firstname" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
										<label class="wpbooklist-question-icon-label" for="user-firstname">' . $this->trans->trans_461 . '</label>
										<input type="text" id="wpbooklist-adduser-firstname" name="user-firstname" value="' . $user_info->firstname . '">
									</div>
									<div class="wpbooklist-book-form-indiv-attribute-container">
										<img class="wpbooklist-icon-image-question" data-label="user-form-lastname" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
										<label class="wpbooklist-question-icon-label" for="user-lastname">' . $this->trans->trans_466 . '</label>
										<input type="text" id="wpbooklist-adduser-lastname" name="user-lastname" value="' . $user_info->lastname . '">
									</div>
									<div class="wpbooklist-book-form-indiv-attribute-container">
										<img class="wpbooklist-icon-image-question" data-label="user-form-emailname" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
										<label class="wpbooklist-question-icon-label" for="user-emailname">' . $this->trans->trans_462 . '</label>
										<input type="text" id="wpbooklist-adduser-emailname" name="user-emailname" value="' . $user_info->email . '">
									</div>
									<div class="wpbooklist-book-form-indiv-attribute-container">
										<img class="wpbooklist-icon-image-question" data-label="user-form-confirmemailname" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
										<label class="wpbooklist-question-icon-label" for="user-confirmemailname">' . $this->trans->trans_467 . '</label>
										<input type="text" id="wpbooklist-adduser-confirmemailname" name="user-confirmemailname" value="' . $user_info->email . '">
									</div>
									<div class="wpbooklist-book-form-indiv-attribute-container" style="display:none;">
										<img class="wpbooklist-icon-image-question" data-label="user-form-passwordname" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
										<label class="wpbooklist-question-icon-label" for="user-passwordname">' . $this->trans->trans_463 . '</label>
										<input type="hidden" id="wpbooklist-adduser-passwordname" name="user-passwordname" value="hacky">
									</div>
									<div class="wpbooklist-book-form-indiv-attribute-container"  style="display:none;">
										<img class="wpbooklist-icon-image-question" data-label="user-form-confirmpasswordname" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
										<label class="wpbooklist-question-icon-label" for="user-confirmpasswordname">' . $this->trans->trans_468 . '</label>
										<input type="hidden" id="wpbooklist-adduser-confirmpasswordname" name="user-confirmpasswordname" value="hacky">
									</div>
									<div class="wpbooklist-book-form-indiv-attribute-container">
										<img class="wpbooklist-icon-image-question" data-label="user-form-usernamename" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
										<label class="wpbooklist-question-icon-label" for="user-usernamename">' . $this->trans->trans_464 . '</label>
										<input type="text" id="wpbooklist-adduser-usernamename" name="user-usernamename" value="' . $user_info->username . '">
									</div>';

			// This filter allows the addition of one or more rows of items into the Basic Fields section of the 'Add a User' form.
			if ( has_filter( 'wpbooklist_append_to_user_form_basic_fields' ) ) {
				$string2 = apply_filters( 'wpbooklist_append_to_user_form_basic_fields', $string2 );
			}

			// Building drop-down of all selected libraries.
			$this->libstring = '';
			$match_flag      = false;

			// If the user's library string contains the default librry, add it as being selected - if not, just add it in as not selected.
			if ( false !== stripos( $user_info->libraries, 'wpbooklist_jre_saved_book_log' ) ) {
				$this->libstring = $this->libstring . '<option selected default value="' . $wpdb->prefix . 'wpbooklist_jre_saved_book_log">' . $this->trans->trans_61 . '</option>';
			} else {
				$this->libstring = $this->libstring . '<option default value="' . $wpdb->prefix . 'wpbooklist_jre_saved_book_log">' . $this->trans->trans_61 . '</option>';
			}

			// Now add in all the custom Libraries.
			foreach ( $this->dynamic_libs as $db ) {
				if ( ( '' !== $db->user_table_name ) || ( null !== $db->user_table_name ) ) {

					// If user's 'libraries' value isn't simply 'alllibraries'...
					if ( 'alllibraries' !== $user_info->libraries ) {

						if ( false !== stripos( $user_info->libraries, '-' ) ) {
							$libs = explode( '-', $user_info->libraries );
							foreach ( $libs as $key => $value ) {
								if ( $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name === $value ) {
									$this->libstring = $this->libstring . '<option selected value="' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . '">' . ucfirst( $db->user_table_name ) . '</option>';

									$match_flag = true;
								}
							}

							if ( ! $match_flag ) {
								$this->libstring = $this->libstring . '<option value="' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . '">' . ucfirst( $db->user_table_name ) . '</option>';
							}
						} else {

							if ( $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name === $user_info->libraries ) {
								$this->libstring = $this->libstring . '<option selected value="' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . '">' . ucfirst( $db->user_table_name ) . '</option>';
							} else {
								$this->libstring = $this->libstring . '<option value="' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . '">' . ucfirst( $db->user_table_name ) . '</option>';
							}
						}
					} else {
						$this->libstring = $this->libstring . '<option value="' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . '">' . ucfirst( $db->user_table_name ) . '</option>';
					}
				}
			}

			// If user has Libraries set to 'alllibraries', adjust the libstring as being selected, else, just add it as an option.
			if ( 'alllibraries' === $user_info->libraries ) {
				$this->libstring = $this->libstring . '<option selected value="alllibraries">' . $this->trans->trans_470 . '</option>';
			} else {
				$this->libstring = $this->libstring . '<option value="alllibraries">' . $this->trans->trans_470 . '</option>';
			}

			$permissions = explode( '-', $user_info->permissions );

			// Now build the Option strings.
			switch ( $permissions[0] ) {
				case 'Yes':
					$addbooks_option = '<option selected>' . $this->trans->trans_131 . '</option>
										<option>' . $this->trans->trans_132 . '</option>';
					break;
				case 'No':
					$addbooks_option = '<option>' . $this->trans->trans_131 . '</option>
										<option selected>' . $this->trans->trans_132 . '</option>';
					break;
				default:
					$addbooks_option = '<option>' . $this->trans->trans_131 . '</option>
										<option>' . $this->trans->trans_132 . '</option>';
					break;
			}

			switch ( $permissions[1] ) {
				case 'Yes':
					$editbooks_option = '<option selected>' . $this->trans->trans_131 . '</option>
										<option>' . $this->trans->trans_132 . '</option>';
					break;
				case 'No':
					$editbooks_option = '<option>' . $this->trans->trans_131 . '</option>
										<option selected>' . $this->trans->trans_132 . '</option>';
					break;

				default:
					$editbooks_option = '<option>' . $this->trans->trans_131 . '</option>
										<option>' . $this->trans->trans_132 . '</option>';
					break;
			}

			switch ( $permissions[2] ) {
				case 'Yes':
					$deletebooks_option = '<option selected>' . $this->trans->trans_131 . '</option>
										<option>' . $this->trans->trans_132 . '</option>';
					break;
				case 'No':
					$deletebooks_option = '<option>' . $this->trans->trans_131 . '</option>
										<option selected>' . $this->trans->trans_132 . '</option>';
					break;

				default:
					$deletebooks_option = '<option>' . $this->trans->trans_131 . '</option>
										<option>' . $this->trans->trans_132 . '</option>';
					break;
			}

			switch ( $permissions[3] ) {
				case 'Yes':
					$changedisplay_option = '<option selected>' . $this->trans->trans_131 . '</option>
										<option>' . $this->trans->trans_132 . '</option>';
					break;
				case 'No':
					$changedisplay_option = '<option>' . $this->trans->trans_131 . '</option>
										<option selected>' . $this->trans->trans_132 . '</option>';
					break;

				default:
					$changedisplay_option = '<option>' . $this->trans->trans_131 . '</option>
										<option>' . $this->trans->trans_132 . '</option>';
					break;
			}

			switch ( $permissions[4] ) {
				case 'Yes':
					$changesettings_option = '<option selected>' . $this->trans->trans_131 . '</option>
										<option>' . $this->trans->trans_132 . '</option>';
					break;
				case 'No':
					$changesettings_option = '<option>' . $this->trans->trans_131 . '</option>
										<option selected>' . $this->trans->trans_132 . '</option>';
					break;

				default:
					$changesettings_option = '<option>' . $this->trans->trans_131 . '</option>
										<option>' . $this->trans->trans_132 . '</option>';
					break;
			}

			$string2 = $string2 . '<div style="display:none;" id="wpbooklist-user-form-show-passwords">' . $this->trans->trans_481 . '</div><br/><div class="wpbooklist-adduser-field-checks-div" id="wpbooklist-adduser-field-checks-email-div"></div>
									<div class="wpbooklist-adduser-field-checks-div" id="wpbooklist-adduser-field-checks-password-div"></div>
								</div>
								<div class="wpbooklist-user-form-inner-container-auth-fields" style="margin-top:50px;">
									<div id="wpbooklist-addbook-select-library-label">
										<p>
											<img class="wpbooklist-icon-image-question-with-link" data-label="user-permissions-heading" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
											' . $this->trans->trans_469 . '
										</p>
										<br/>
										<div class="wpbooklist-book-form-indiv-attribute-container wpbooklist-book-form-indiv-attribute-container-exception">
											<img class="wpbooklist-icon-image-question" data-label="user-form-usernamename" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
											<label class="wpbooklist-question-icon-label" for="user-usernamename">' . $this->trans->trans_471 . '</label><br/>
											<select class="wpbooklist-addbook-select-default select2-input-libraries" id="wpbooklist-addbook-select-library" name="libraries[]" multiple="multiple">  
												' . $this->libstring . '
											</select>
										</div>
										<div class="wpbooklist-book-form-indiv-attribute-container">
											<img class="wpbooklist-icon-image-question" data-label="user-form-usernamename" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
											<label class="wpbooklist-question-icon-label" for="user-usernamename">' . $this->trans->trans_472 . '</label>
											<select class="wpbooklist-addbook-select-default" id="wpbooklist-adduser-permissions-add-book">
												' . $addbooks_option . '
											</select>
										</div>
										<div class="wpbooklist-book-form-indiv-attribute-container">
											<img class="wpbooklist-icon-image-question" data-label="user-form-usernamename" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
											<label class="wpbooklist-question-icon-label" for="user-usernamename">' . $this->trans->trans_473 . '</label>
											<select class="wpbooklist-addbook-select-default" id="wpbooklist-adduser-permissions-edit-book">
												' . $editbooks_option . '
											</select>
										</div>
										<div class="wpbooklist-book-form-indiv-attribute-container">
											<img class="wpbooklist-icon-image-question" data-label="user-form-usernamename" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
											<label class="wpbooklist-question-icon-label" for="user-usernamename">' . $this->trans->trans_474 . '</label>
											<select class="wpbooklist-addbook-select-default" id="wpbooklist-adduser-permissions-delete-book">
												' . $deletebooks_option . '
											</select>
										</div>
										<div class="wpbooklist-book-form-indiv-attribute-container">
											<img class="wpbooklist-icon-image-question" data-label="user-form-usernamename" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
											<label class="wpbooklist-question-icon-label" for="user-usernamename">' . $this->trans->trans_475 . '</label>
											<select class="wpbooklist-addbook-select-default" id="wpbooklist-adduser-permissions-change-display-options">
												' . $changedisplay_option . '
											</select>
										</div>
										<div class="wpbooklist-book-form-indiv-attribute-container">
											<img class="wpbooklist-icon-image-question" data-label="user-form-usernamename" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
											<label class="wpbooklist-question-icon-label" for="user-usernamename">' . $this->trans->trans_476 . '</label>
											<select class="wpbooklist-addbook-select-default" id="wpbooklist-adduser-permissions-change-settings">
												' . $changesettings_option . '
											</select>
										</div>';

			// This filter allows the addition of one or more rows of items into the Permissions Fields section of the 'Add a User' form.
			if ( has_filter( 'wpbooklist_append_to_user_form_auth_fields' ) ) {
				$string2 = apply_filters( 'wpbooklist_append_to_user_form_auth_fields', $string2 );
			}

			$string2 = $string2 . '</div>
								</div>
							</div>
							<div class="wpbooklist-response-success-fail-container">
					    		<button data-wpuserid="' . $user_info->wpuserid . '" class="wpbooklist-response-success-fail-button wpbooklist-admin-adduser-add-button" type="button" id="wpbooklist-admin-edituser-edit-button">' . $this->trans->trans_497 . '</button>
					    		<div class="wpbooklist-spinner" id="wpbooklist-spinner-edit"></div>
					    		<div class="wpbooklist-response-success-fail-response-actual-container" id="wpbooklist-admin-adduser-response-actual-container"></div>
				    		</div>
						</div>
			';

			return $string2;
		}



	}

endif;
