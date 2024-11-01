<?php
/**
 * WPBookList Library Display Options Form Tab Class - class-wpbooklist-library-display-options-form.php
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  6.1.5.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Library_Display_Options_Form', false ) ) :

	/**
	 * WPBookList_Admin_Menu Class.
	 */
	class WPBookList_Library_Display_Options_Form {


		/**
		 * Class Constructor - Simply calls the Translations.
		 */
		public function __construct() {

			// Get Translations.
			require_once CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
			$this->trans = new WPBookList_Translations();
			$this->trans->trans_strings();

		}

		/**
		 * Outputs all HTML elements on the page.
		 */
		public function output_library_display_options_form() {

			global $wpdb;

			// Set the current WordPress user.
			$currentwpuser = wp_get_current_user();

			// Now we'll determine access, and stop all execution if user isn't allowed in.
			require_once CLASS_UTILITIES_DIR . 'class-wpbooklist-utilities-accesscheck.php';
			$this->access          = new WPBookList_Utilities_Accesscheck();
			$this->currentwpbluser = $this->access->wpbooklist_accesscheck( $currentwpuser->ID, 'displayoptions' );

			// If we received false from accesscheck class, display permissions message.
			if ( false === $this->currentwpbluser ) {

				// Outputs the 'No Permission!' message.
				$this->initial_output = $this->access->wpbooklist_accesscheck_no_permission_message();
				return $this->initial_output;
			}

			// Now we'll get what libraries the user is allowed to access.
			require_once CLASS_TRANSIENTS_DIR . 'class-wpbooklist-transients.php';
			$transients          = new WPBookList_Transients();
			$settings_table_name = $wpdb->prefix . 'wpbooklist_jre_users_table';
			$transient_name      = 'wpbl_' . md5( 'SELECT * FROM ' . $settings_table_name . " WHERE wpuserid = " . $currentwpuser->ID );
			$transient_exists    = $transients->existing_transient_check( $transient_name );
			if ( $transient_exists ) {
				$this->wpbl_user = $transient_exists;
			} else {
				$query                  = 'SELECT * FROM ' . $settings_table_name . " WHERE wpuserid = " . $currentwpuser->ID;
				$this->wpbl_user = $transients->create_transient( $transient_name, 'wpdb->get_row', $query, MONTH_IN_SECONDS );
			}

			$wpuser = $this->wpbl_user;

			// Getting all user-created libraries.
			$db_row = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names' );

			// Getting settings for Default library.
			$options_row = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_user_options' );

			$string1 = '<div id="wpbooklist-display-options-container">
							<p class="wpbooklist-tab-intro-para">' . $this->trans->trans_262 . '</p>
							<select class="wpbooklist-select-centered" id="wpbooklist-library-display-settings-select">';

			// If user has 'alllibraries' in the 'Libraries' DB Column, add in the default Library.
			$string2     = '';
			$defaultflag = true;
			if ( false !== stripos( $wpuser->libraries, 'alllibraries' ) || false !== stripos( $wpuser->libraries, 'wpbooklist_jre_saved_book_log' ) ) {
				$string2     = $string2 . '<option selected default value="' . $wpdb->prefix . 'wpbooklist_jre_saved_book_log">' . $this->trans->trans_61 . '</option> ';
				$defaultflag = false;
			}

			// Building drop-down of all libraries.
			foreach ( $db_row as $key => $db ) {
				if ( ( '' !== $db->user_table_name ) || ( null !== $db->user_table_name ) ) {

					// Making sure the user is allowed to access this particular library - first check for 'alllibraries' access.
					if ( false !== stripos( $wpuser->libraries, 'alllibraries' ) || 'SuperAdmin' === $wpuser->role ) {

						// If we're on the first iteration of the foreach, make this the selected default value.
						if ( 0 === $key ) {

							// If we haven't already set a default...
							if ( $defaultflag ) {
								$string2 = $string2 . '<option selected default value="' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . '">' . ucfirst( $db->user_table_name ) . '</option>';
							} else {
								$string2 = $string2 . '<option value="' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . '">' . ucfirst( $db->user_table_name ) . '</option>';
							}
						} else {
							$string2 = $string2 . '<option value="' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . '">' . ucfirst( $db->user_table_name ) . '</option>';
						}
					} else {

						if ( false !== stripos( $wpuser->libraries, $db->user_table_name ) ) {

							// If we're on the first iteration of the foreach, make this the selected default value.
							if ( 0 === $key ) {
								// If we haven't already set a default...
								if ( $defaultflag ) {
									$string2 = $string2 . '<option selected default value="' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . '">' . ucfirst( $db->user_table_name ) . '</option>';
								} else {
									$string2 = $string2 . '<option value="' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . '">' . ucfirst( $db->user_table_name ) . '</option>';
								}
							} else {
								$string2 = $string2 . '<option value="' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . '">' . ucfirst( $db->user_table_name ) . '</option>';
							}
						}
					}
				}
			}

			$string3 = '</select>';

			$string4 =
				'<div class="wpbooklist-spinner" id="wpbooklist-spinner"></div>
				<div id="wpbooklist-display-options-indiv-entry-wrapper">
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-booktitle" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_138 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-booktitle"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-filter" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_248 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-filter"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-editionsort" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_249 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-editionsort"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-quote" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_250 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-quote"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-reviewstars" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_251 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-reviewstars"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-searchsort" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_252 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-searchsort"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-signedsort" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_253 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-signedsort"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-statistics" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_254 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-statistics"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-subjectsort" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_255 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-subjectsort"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-finished" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_256 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-finishedsort"></input>
						</div>
					</div>';

			if ( has_filter( 'wpbooklist_add_to_library_display_options' ) ) {

				$string4 = $string4 . apply_filters( 'wpbooklist_add_to_library_display_options', null );
			}

			$string4 = $string4 . '</div>';

			$string5 =
				'<div id="wpbooklist-display-opt-check-div">
                    <label>' . $this->trans->trans_257 . '</label>
                    <input id="wpbooklist-check-all" type="checkbox" name="check-all">
                    <label>' . $this->trans->trans_258 . '</label>
                    <input id="wpbooklist-uncheck-all" type="checkbox" name="uncheck-all">
                </div>
                <table id="wpbooklist-library-options-lower-table">
                	<tbody>
                		<tr></tr>
	                	<tr>
			              <td class="wpbooklist-display-bottom-4"><label>' . $this->trans->trans_259 . '</label></td>
			              <td class="wpbooklist-display-bottom-4">
			                <select name="sort-value" id="wpbooklist-jre-sorting-select"><option selected="selected" value="' . $this->trans->trans_3 . '">' . $this->trans->trans_3 . '</option>
			                  <option value="alphabeticallybytitle">' . $this->trans->trans_4 . '</option>
			                  <option value="alphabeticallybyauthorfirst">' . $this->trans->trans_5 . '</option>
			                  <option value="alphabeticallybyauthorlast">' . $this->trans->trans_6 . '</option>
			                  <option value="year_read">' . $this->trans->trans_7 . '</option>
			                  <option value="pages_desc">' . $this->trans->trans_8 . '</option>
			                  <option value="pages_asc">' . $this->trans->trans_9 . '</option>
			                  <option value="signed">' . $this->trans->trans_10 . '</option>
			                  <option value="first_edition">' . $this->trans->trans_11 . '</option>
			                </select><br>
			              </td>
		            	</tr>
		            	<tr>
			                <td class="wpbooklist-display-bottom-4"><label>' . $this->trans->trans_260 . '</label></td>
			                <td class="wpbooklist-display-bottom-4"><input class="wpbooklist-dynamic-input" id="wpbooklist-book-control" type="text" name="books-per-page"></td>
	            		</tr>
	            	</tbody>
	            </table>
	            <button class="wpbooklist-response-success-fail-button wpbooklist-admin-save-library-display-button" type="button">' . $this->trans->trans_245 . '</button>
	           	<div class="wpbooklist-spinner" id="wpbooklist-spinner-1"></div>
			</div>';

			echo $string1 . $string2 . $string3 . $string4 . $string5;
		}
	}
endif;
