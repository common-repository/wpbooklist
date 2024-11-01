<?php
/**
 * WPBookList_Template_Paks_Tab Tab Class - class-admin-template-templatepaks-tab-ui.php.
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/UI/Admin
 * @version  6.1.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Template_Paks_Tab', false ) ) :

	/**
	 * WPBookList_Admin_Menu Class.
	 */
	class WPBookList_Template_Paks_Tab {

		/**
		 * Class Constructor.
		 */
		public function __construct() {
			require_once CLASS_DIR . 'class-admin-ui-template.php';
			require_once CLASS_DIR . 'class-wpbooklist-template-paks-form.php';

			// Get Translations.
			require_once CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
			$this->trans = new WPBookList_Translations();
			$this->trans->trans_strings();

			// Instantiate the class.
			$this->template = new WPBookList_Admin_UI_Template();
			$this->form     = new WPBookList_Template_Paks_Form();
			$this->output_open_admin_container();
			$this->output_tab_content();
			$this->output_close_admin_container();
			$this->output_admin_template_advert();
		}

		/**
		 * Opens the admin container for the tab.
		 */
		private function output_open_admin_container() {
			$icon_url = ROOT_IMG_ICONS_URL . 'seo-template.svg';
			$title    = $this->trans->trans_293;
			echo $this->template->output_open_admin_container( $title, $icon_url );
		}

		/**
		 * Outputs actual tab contents
		 */
		private function output_tab_content() {
			echo $this->form->output_template_paks_form();
		}

		/**
		 * Closes admin container
		 */
		private function output_close_admin_container() {
			echo $this->template->output_close_admin_container();
		}

		/**
		 * Outputs advertisment area
		 */
		private function output_admin_template_advert() {
			echo $this->template->output_template_advert();
		}

	}

endif;


// Instantiate the class.
$am = new WPBookList_Template_Paks_Tab();
