<?php
/**
 * WPBookList Backup Settings Form Tab Class - class-wpbooklist-backup-settings-form.php
 *
 * @author   Jake Evans
 * @category Backup
 * @package  Includes/Classes/Backup
 * @version  6.1.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Backup_Settings_Form', false ) ) :
	/**
	 * WPBookList_Admin_Menu Class.
	 */
	class WPBookList_Backup_Settings_Form {

		/**
		 * Class Constructor - Simply calls the Translations
		 */
		public function __construct() {

			// Get Translations.
			require_once CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
			$this->trans = new WPBookList_Translations();
			$this->trans->trans_strings();

			// Require the Transients file.
			require_once CLASS_TRANSIENTS_DIR . 'class-wpbooklist-transients.php';
			$this->transients = new WPBookList_Transients();
		}

		/**
		 * Outputs the Backup Form.
		 */
		public function output_backup_settings_form() {
			global $wpdb;

			// Set the current WordPress user.
			$currentwpuser = wp_get_current_user();

			// Now we'll determine access, and stop all execution if user isn't allowed in.
			require_once CLASS_UTILITIES_DIR . 'class-wpbooklist-utilities-accesscheck.php';
			$this->access          = new WPBookList_Utilities_Accesscheck();
			$this->currentwpbluser = $this->access->wpbooklist_accesscheck( $currentwpuser->ID, 'settings' );

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

			$table_name       = $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names';
			$transient_name   = 'wpbl_' . md5( 'SELECT * FROM ' . $table_name );
			$transient_exists = $this->transients->existing_transient_check( $transient_name );
			if ( $transient_exists ) {
				$db_row = $transient_exists;
			} else {
				$query  = 'SELECT * FROM ' . $table_name;
				$db_row = $this->transients->create_transient( $transient_name, 'wpdb->get_results', $query, MONTH_IN_SECONDS );
			}

			$string1 = '<div id="wpbooklist-backup-settings-container">
							<p class="wpbooklist-tab-intro-para">' . $this->trans->trans_56 . ' <span class="wpbooklist-color-orange-italic">' . $this->trans->trans_57 . '</span> ' . $this->trans->trans_58 . '</p>';

			$string2 = '<div id="wpbooklist-apply-stylepak-wrapper">
							<div id="wpbooklist-backup-select-library-label" for="wpbooklist-backup-select-library">' . $this->trans->trans_59 . '</div>
								<select class="wpbooklist-stylepak-select-default" id="wpbooklist-backup-select-library">
									<option selected disabled value="' . $this->trans->trans_60 . '...">' . $this->trans->trans_60 . '...</option>';

			// If user has 'alllibraries' in the 'Libraries' DB Column, add in the default Library.
			$string3     = '';
			$defaultflag = true;
			if ( false !== stripos( $wpuser->libraries, 'alllibraries' ) || false !== stripos( $wpuser->libraries, 'wpbooklist_jre_saved_book_log' ) ) {
				$string3     = $string3 . '<option selected default value="' . $wpdb->prefix . 'wpbooklist_jre_saved_book_log">' . $this->trans->trans_61 . '</option> ';
				$defaultflag = false;
			}

			// Building drop-down of all libraries.
			foreach ( $db_row as $key => $db ) {
				if ( ( '' !== $db->user_table_name ) || ( null !== $db->user_table_name ) ) {

					// Making sure the user is allowed to access this particular library - first check for 'alllibraries' access.
					if ( false !== stripos( $wpuser->libraries, 'alllibraries' ) || 'SuperAdmin' === $wpuser->role ) {

						$string3 = $string3 . '<option value="' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . '">' . ucfirst( $db->user_table_name ) . '</option>';

					} else {

						if ( false !== stripos( $wpuser->libraries, $db->user_table_name ) ) {

							$string3 = $string3 . '<option value="' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . '">' . ucfirst( $db->user_table_name ) . '</option>';
						}
					}
				}
			}

			$string4 = '</select>
							<button class="wpbooklist-response-success-fail-button" id="wpbooklist-apply-library-backup">' . $this->trans->trans_62 . '</button>
							<div class="wpbooklist-spinner" id="wpbooklist-spinner-backup"></div>
						</div>';

			$string5 = '<p class="wpbooklist-tab-intro-para">' . $this->trans->trans_63 . ' <span class="wpbooklist-color-orange-italic">' . $this->trans->trans_57 . '</span> ' . $this->trans->trans_64 . '</p>
			<div id="wpbooklist-apply-stylepak-wrapper">
				<div id="wpbooklist-backup-select-library-label">' . $this->trans->trans_65 . ':</div>
					<select class="wpbooklist-stylepak-select-default" id="wpbooklist-select-library-backup">	
						<option selected disabled>' . $this->trans->trans_65 . '...</option>';

			// If user has 'alllibraries' in the 'Libraries' DB Column, add in the default Library.
			$string6     = '';
			$defaultflag = true;
			if ( false !== stripos( $wpuser->libraries, 'alllibraries' ) || false !== stripos( $wpuser->libraries, 'wpbooklist_jre_saved_book_log' ) ) {
				$string6     = $string6 . '<option selected default value="' . $wpdb->prefix . 'wpbooklist_jre_saved_book_log">' . $this->trans->trans_61 . '</option> ';
				$defaultflag = false;
			}

			// Building drop-down of all libraries.
			foreach ( $db_row as $key => $db ) {
				if ( ( '' !== $db->user_table_name ) || ( null !== $db->user_table_name ) ) {

					// Making sure the user is allowed to access this particular library - first check for 'alllibraries' access.
					if ( false !== stripos( $wpuser->libraries, 'alllibraries' ) || 'SuperAdmin' === $wpuser->role ) {

						$string6 = $string6 . '<option value="' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . '">' . ucfirst( $db->user_table_name ) . '</option>';

					} else {

						if ( false !== stripos( $wpuser->libraries, $db->user_table_name ) ) {

							$string6 = $string6 . '<option value="' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . '">' . ucfirst( $db->user_table_name ) . '</option>';
						}
					}
				}
			}

			$string7 = '</select>';

			$string8 = '<div id="wpbooklist-backup-select-library-label">Select a Backup:</div>
							<select disabled class="wpbooklist-stylepak-select-default" id="wpbooklist-select-actual-backup">	
								<option selected disabled>' . $this->trans->trans_66 . '...</option>';

			$string9 = '';
			foreach ( glob( LIBRARY_DB_BACKUPS_UPLOAD_DIR . '*.sql' ) as $filename ) {

				// Exclude the csv/txt files.
				if ( false === strpos( $filename, 'isbn_asin' ) ) {
					$filename     = basename( $filename );
					$display_name = explode( '_-_', $filename );
					$string9      = $string9 . '<option class="wpbooklist-backup-actual-option" data-table="' . $display_name[0] . '" id="' . $filename . '" value="' . $filename . '">' . $display_name[1] . ' - ' . date( 'h:i a', intval( $display_name[2] ) ) . '</option>';
				}
			}

			$string10 = '</select>
						 <button class="wpbooklist-response-success-fail-button" id="wpbooklist-apply-library-restore">' . $this->trans->trans_67 . '</button>
						 <div class="wpbooklist-spinner" id="wpbooklist-spinner-restore-backup"></div>
						 </div>';

			$string11 = '<div id="wpbooklist-backup-create-csv-div">
							<p class="wpbooklist-tab-intro-para">' . $this->trans->trans_68 . ' <span class="wpbooklist-color-orange-italic"><a href="https://wpbooklist.com/index.php/downloads/bulk-upload-extension/">' . $this->trans->trans_69 . '!</a></span> ' . $this->trans->trans_70 . ' <span class="wpbooklist-color-orange-italic">' . $this->trans->trans_57 . '</span> ' . $this->trans->trans_71 . '.</p>
							<div id="wpbooklist-apply-stylepak-wrapper">
								<div id="wpbooklist-backup-select-library-label" for="wpbooklist-backup-select-library">' . $this->trans->trans_370 . '</div>
								<select class="wpbooklist-stylepak-select-default" id="wpbooklist-backup-csv-select-library">
									<option selected disabled value="' . $this->trans->trans_60 . '...">' . $this->trans->trans_60 . '...</option>';

			// If user has 'alllibraries' in the 'Libraries' DB Column, add in the default Library.
			$defaultflag = true;
			if ( false !== stripos( $wpuser->libraries, 'alllibraries' ) || false !== stripos( $wpuser->libraries, 'wpbooklist_jre_saved_book_log' ) ) {
				$string11    = $string11 . '<option selected default value="' . $wpdb->prefix . 'wpbooklist_jre_saved_book_log">' . $this->trans->trans_61 . '</option> ';
				$defaultflag = false;
			}

			// Building drop-down of all libraries.
			foreach ( $db_row as $key => $db ) {
				if ( ( '' !== $db->user_table_name ) || ( null !== $db->user_table_name ) ) {

					// Making sure the user is allowed to access this particular library - first check for 'alllibraries' access.
					if ( false !== stripos( $wpuser->libraries, 'alllibraries' ) || 'SuperAdmin' === $wpuser->role ) {

						$string11 = $string11 . '<option value="' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . '">' . ucfirst( $db->user_table_name ) . '</option>';

					} else {

						if ( false !== stripos( $wpuser->libraries, $db->user_table_name ) ) {

							$string11 = $string11 . '<option value="' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . '">' . ucfirst( $db->user_table_name ) . '</option>';
						}
					}
				}
			}

			$string11 = $string11 . '</select>
								<button class="wpbooklist-response-success-fail-button" id="wpbooklist-apply-library-backup-csv">' . $this->trans->trans_72 . '</button>
								<div class="wpbooklist-spinner" id="wpbooklist-spinner-backup-csv"></div>
							</div>
						</div>';

			$string12 = '<div id="wpbooklist-addbackup-success-div"></div></div>';

			echo $string1 . $string2 . $string3 . $string4 . $string5 . $string6 . $string7 . $string8 . $string9 . $string10 . $string11 . $string12;

		}


	}

endif;
