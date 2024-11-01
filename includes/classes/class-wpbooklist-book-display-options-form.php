<?php
/**
 * WPBookList Book Display Options Form Tab Class - class-wpbooklist-book-display-options-form.php
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  6.1.5.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Book_Display_Options_Form', false ) ) :

	/**
	 * WPBookList_Admin_Menu Class.
	 */
	class WPBookList_Book_Display_Options_Form {


		/**
		 * Class Constructor - Simply calls the Translations
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
		public function output_book_display_options_form() {
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

			// Set the current WordPress user.
			$currentwpuser = wp_get_current_user();

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
							<p class="wpbooklist-tab-intro-para">' . $this->trans->trans_264 . '</p>
							<select class="wpbooklist-select-centered" id="wpbooklist-book-display-settings-select">';

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
				'<div id="wpbooklist-display-options-indiv-entry-wrapper">
					<div class="wpbooklist-spinner" id="wpbooklist-spinner-2"></div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-additionalimgs" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_584 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-additionalimgs"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-amazonreviews" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_266 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-amazonreviews"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-amazonpurchaselink" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_265 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-amazonpurchaselink"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-asin" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_137 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-asin"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-author" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_14 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-author"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-bnpurchaselink" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_267 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-bnpurchaselink"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-bookfinished" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_268 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-bookfinished"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-booktitle" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_271 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-booktitle"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-bampurchaselink" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_272 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-bampurchaselink"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-callnumber" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_144 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-callnumber"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-country" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_273 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-country"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-edition" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_155 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-edition"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-emailsharebutton" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_274 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-emailsharebutton"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-facebookmessengerbutton" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_275 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-facebookmessengerbutton"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-facebooksharebutton" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_276 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-facebooksharebutton"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-featuredtitlessection" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_277 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-featuredtitlessection"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-format" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_158 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-format"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-frontcoverimage" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_278 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-frontcoverimage"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-fulldescription" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_152 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-fulldescription"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-genres" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_146 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-genres"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-goodreadswidget" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_279 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-goodreadswidget"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-googlepurchaselink" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_280 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-googlepurchaselink"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-illustrator" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_281 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-illustrator"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-isbn10" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_135 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-isbn10"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-isbn13" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_136 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-isbn13"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-ibookspurchaselink" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_282 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-ibookspurchaselink"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-keywords" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_149 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-keywords"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-kobopurchaselink" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_283 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-kobopurchaselink"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-language" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_154 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-language"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-notes" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_153 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-notes"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-numberinseries" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_157 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-numberinseries"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-originalpublicationyear" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_145 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-originalpublicationyear"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-originaltitle" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_139 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-originaltitle"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-othereditions" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_150 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-othereditions"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-outofprint" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_284 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-outofprint"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-pages" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_142 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-pages"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-pinterestsharebutton" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_285 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-pinterestsharebutton"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-publicationdate" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_143 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-publicationdate"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-publisher" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_141 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-publisher"></input>
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
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-series" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_156 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-series"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-shortdescription" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_151 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-shortdescription"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-signed" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_10 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-signed"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-similarbooks" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_148 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-similarbooks"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-subgenre" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_147 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-subgenre"></input>
						</div>
					</div>
					<div class="wpbooklist-display-options-indiv-entry">
						<div class="wpbooklist-display-options-label-div">
							<img class="wpbooklist-icon-image-question-display-options wpbooklist-icon-image-question" data-label="library-display-form-twittersharebutton" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
							<label>' . $this->trans->trans_286 . '</label>
						</div>
						<div class="wpbooklist-margin-right-td">
							<input type="checkbox" name="hide-library-display-form-twittersharebutton"></input>
						</div>
					</div>';

			// This filter allows the addition of one or more rows of items into the 'Book View Display Options' form.
			if ( has_filter( 'wpbooklist_add_to_book_display_options' ) ) {
				$string4 = $string4 . apply_filters( 'wpbooklist_add_to_book_display_options', null );
			}

			$string5 = '</div>
				<div id="wpbooklist-display-opt-check-div">
					<label>' . $this->trans->trans_257 . '</label>
					<input id="wpbooklist-check-all" type="checkbox" name="check-all">
					<label>' . $this->trans->trans_258 . '</label>
					<input id="wpbooklist-uncheck-all" type="checkbox" name="uncheck-all">
				</div>
				<button class="wpbooklist-response-success-fail-button wpbooklist-admin-save-book-display-button" type="button">' . $this->trans->trans_245 . '</button>
				<div class="wpbooklist-spinner" id="wpbooklist-spinner-1"></div>
			</div>';

			echo $string1 . $string2 . $string3 . $string4 . $string5;
		}
	}
endif;
