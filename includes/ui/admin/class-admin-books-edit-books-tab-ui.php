<?php
/**
 * WPBookList Edit Book Tab Class
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/UI/Admin
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_EditBook_Tab', false ) ) :
	/**
	 * WPBookList_Edit_Book Class.
	 */
	class WPBookList_EditBook_Tab {

		/**
		 * Class Constructor
		 */
		public function __construct() {
			require_once CLASS_DIR . 'class-admin-ui-template.php';
			require_once CLASS_BOOK_DIR . 'class-wpbooklist-edit-book-form.php';

			// Instantiate the class.
			$this->template = new WPBookList_Admin_UI_Template();
			$this->form     = new WPBookList_Edit_Book_Form();
			$this->output_open_admin_container();
			$this->output_tab_content();
			$this->output_close_admin_container();
			$this->output_admin_template_advert();
		}

		/**
		 * Opens the admin container for the tab
		 */
		private function output_open_admin_container() {
			$title    = __( 'Edit & Delete Books', 'wpbooklist' );
			$icon_url = ROOT_IMG_ICONS_URL . 'edit.svg';
			echo $this->template->output_open_admin_container( $title, $icon_url );
		}

		/**
		 * Outputs actual tab contents
		 */
		private function output_tab_content() {
			echo $this->form->output_edit_book_form( 'default', 0 );
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


// Instantiate the class
$am = new WPBookList_EditBook_Tab;