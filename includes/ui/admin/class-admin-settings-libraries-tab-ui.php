<?php
/**
 * WPBookList_Settings_Libraries_Tab Tab - class-admin-settings-libraries-tab-ui.php.
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Settings_Libraries_Tab', false ) ) :

	/**
	 * WPBookList_Settings_Libraries_Tab Class.
	 */
	class WPBookList_Settings_Libraries_Tab {

		/**
		 * Class Constructor
		 */
		public function __construct() {
			require_once CLASS_DIR . 'class-admin-ui-template.php';
			require_once CLASS_DIR . 'class-custom-libraries-form.php';

			// Get Translations.
			require_once CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
			$this->trans = new WPBookList_Translations();
			$this->trans->trans_strings();

			// Instantiate the class.
			$this->template = new WPBookList_Admin_UI_Template();
			$this->form     = new WPBookList_Custom_Libraries_Form();
			$this->output_open_admin_container();
			$this->output_tab_content();
			$this->output_close_admin_container();
			$this->output_admin_template_advert();
		}

		/**
		 * Opens the admin container for the tab
		 */
		private function output_open_admin_container() {
			$title    = $this->trans->trans_302;
			$icon_url = ROOT_IMG_ICONS_URL . 'bookshelf.svg';

			// HTML to clear the WPBookList Transients.
			echo '<div id="wpbooklist-cache-clear-wrapper">
					<img class="wpbooklist-icon-image-question" data-label="book-settings-clearcache" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
					<button id="wpbooklist-cache-clear-button">' . $this->trans->trans_501 . '</button>
					<div class="wpbooklist-spinner" id="wpbooklist-spinner-cache"></div>
				</div>';

			echo $this->template->output_open_admin_container( $title, $icon_url );

		}

		/**
		 * Outputs actual tab contents
		 */
		private function output_tab_content() {
			echo $this->form->output_custom_libraries_form();
		}

		/**
		 * Closes admin container.
		 */
		private function output_close_admin_container() {
			echo $this->template->output_close_admin_container();
		}

		/**
		 * Outputs advertisment area.
		 */
		private function output_admin_template_advert() {
			echo $this->template->output_template_advert();
		}


	}
endif;

// Instantiate the class.
$cm = new WPBookList_Settings_Libraries_Tab();
