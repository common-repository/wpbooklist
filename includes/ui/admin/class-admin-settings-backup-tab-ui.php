<?php
/**
 * WPBookList_Backup_Settings Tab - class-admin-settings-backup-tab-ui.php.
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/UI/Admin
 * @version  6.1.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Backup_Settings', false ) ) :

	/**
	 * WPBookList_Backup_Settings Class.
	 */
	class WPBookList_Backup_Settings {

		/**
		 * Constructor that calls all of the Classes functions upon instantiation.
		 */
		public function __construct() {

			require_once CLASS_DIR . 'class-admin-ui-template.php';
			require_once CLASS_BACKUP_DIR . 'class-wpbooklist-backup-settings-form.php';

			// Get Translations.
			require_once CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
			$this->trans = new WPBookList_Translations();
			$this->trans->trans_strings();

			// Instantiate the class.
			$this->template = new WPBookList_Admin_UI_Template();
			$this->form     = new WPBookList_Backup_Settings_Form();
			$this->output_open_admin_container();
			$this->output_tab_content();
			$this->output_close_admin_container();
			$this->output_admin_template_advert();
		}


		/**
		 * Outputs the Title and image for this tab.
		 */
		private function output_open_admin_container() {
			$title    = $this->trans->trans_73;
			$icon_url = ROOT_IMG_ICONS_URL . 'backup.svg';

			// HTML to clear the WPBookList Transients.
			echo '<div id="wpbooklist-cache-clear-wrapper">
					<img class="wpbooklist-icon-image-question" data-label="book-settings-clearcache" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
					<button id="wpbooklist-cache-clear-button">' . $this->trans->trans_501 . '</button>
					<div class="wpbooklist-spinner" id="wpbooklist-spinner-cache"></div>
				</div>';

			echo $this->template->output_open_admin_container( $title, $icon_url );
		}

		/**
		 * Outputs the actual HTML content for this tab.
		 */
		private function output_tab_content() {
			echo $this->form->output_backup_settings_form();
		}

		/**
		 * Simply closes open HTML elements.
		 */
		private function output_close_admin_container() {
			echo $this->template->output_close_admin_container();
		}

		/**
		 * Adds in the default advertisment at the bottom of each tab
		 */
		private function output_admin_template_advert() {
			echo $this->template->output_template_advert();
		}


	}
endif;

// Instantiate the class.
$cm = new WPBookList_Backup_Settings();
