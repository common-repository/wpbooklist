<?php
/**
 * WPBookList_Storytime_Settings_Form Class - class-wpbooklist-storytime-settings-form.php
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes/Storytime
 * @version  6.1.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Storytime_Settings_Form', false ) ) :

	/**
	 * WPBookList_Storytime_Settings_Form Class.
	 */
	class WPBookList_Storytime_Settings_Form {

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
		 * Function to output the actual HTML.
		 */
		public function output_add_book_form() {

			// Perform check for previously-saved Amazon Authorization.
			global $wpdb;
			$settings_results = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_storytime_stories_settings' );

			if ( '1' === $settings_results->createpost ) {
				$input1 = '<input checked id="wpbooklist-storytime-settings-input-1" type="checkbox" />';
			} else {
				$input1 = '<input id="wpbooklist-storytime-settings-input-1" type="checkbox" />';
			}

			if ( '1' === $settings_results->createpage ) {
				$input2 = '<input checked id="wpbooklist-storytime-settings-input-2" type="checkbox" />';
			} else {
				$input2 = '<input id="wpbooklist-storytime-settings-input-2" type="checkbox" />';
			}

			if ( '1' === $settings_results->deletedefault ) {
				$input3 = '<input checked id="wpbooklist-storytime-settings-input-3" type="checkbox" />';
			} else {
				$input3 = '<input id="wpbooklist-storytime-settings-input-3" type="checkbox" />';
			}

			if ( '1' === $settings_results->newnotify ) {
				$input4 = '<input checked id="wpbooklist-storytime-settings-input-4" type="checkbox" />';
			} else {
				$input4 = '<input id="wpbooklist-storytime-settings-input-4" type="checkbox" />';
			}

			if ( '1' === $settings_results->getstories ) {
				$input5 = '<input checked id="wpbooklist-storytime-settings-input-5" type="checkbox" />';
			} else {
				$input5 = '<input id="wpbooklist-storytime-settings-input-5" type="checkbox" />';
			}

			if ( null !== $settings_results->storypersist ) {
				$input6 = '<input value="' . $settings_results->storypersist . '" id="wpbooklist-storytime-settings-input-6" type="number" />';
			} else {
				$input6 = '<input id="wpbooklist-storytime-settings-input-6" type="number" />';
			}

			$string1 = '
			<div id="wpbooklist-addbook-container">
				<p class="wpbooklist-tab-intro-para">' . $this->trans->trans_230 . ' <span class="wpbooklist-color-orange-italic">' . $this->trans->trans_231 . '</span> ' . $this->trans->trans_232 . '</p>
				<p class="wpbooklist-tab-intro-para">' . $this->trans->trans_233 . '&nbsp;<strong>[wpbooklist_storytime]</strong></p>
				<div id="wpbooklist-storytime-settings-cont">
					<p class="wpbooklist-tab-intro-para">Settings</p>
					<div id="wpbooklist-storytime-settings-inner">
					<div class="wpbooklist-storytime-row-div">
						<label>' . $this->trans->trans_234 . '</label>
						' . $input1 . '
					</div>
					<div class="wpbooklist-storytime-row-div">
						<label>' . $this->trans->trans_235 . '</label>
						' . $input2 . '
					</div>
					<div class="wpbooklist-storytime-row-div">
						<label>' . $this->trans->trans_236 . '</label>
						' . $input3 . '
					</div>
					<div class="wpbooklist-storytime-row-div">
						<label>' . $this->trans->trans_237 . '</label>
						' . $input4 . '
					</div>
					<div class="wpbooklist-storytime-row-div">
						<label>' . $this->trans->trans_238 . ' <em>' . $this->trans->trans_239 . '</em> ' . $this->trans->trans_240 . ')?</label>
						' . $input5 . '
					</div>
					<div style="display:none;" class="wpbooklist-storytime-row-div" id="wpbooklist-storytime-row-div-6">
						<label>' . $this->trans->trans_241 . '</label>
						' . $input6 . '
						<label>' . $this->trans->trans_242 . '</label>
					</div>
					</div>
					<div class="wpbooklist-storytime-settings-save-div">
					<button class="wpbooklist-response-success-fail-button" id="wpbooklist-storytime-settings-save">' . $this->trans->trans_243 . '</button>
					<div class="wpbooklist-spinner" id="wpbooklist-spinner-storytime-settings"></div>
					</div>
				</div>
			</div>';

			return $string1;
		}


	}

endif;
