<?php
/**
 * WPBookList Add-Edit-Book-Form Tab Class
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  6.1.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Add_Book_Form', false ) ) :

	/**
	 * WPBookList_Admin_Menu Class.
	 */
	class WPBookList_Add_Book_Form {

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
		 * Outputs all HTML elements on the page .
		 */
		public function output_add_book_form() {

			// Perform check for previously-saved Amazon Authorization.
			global $wpdb;
			$table_name  = $wpdb->prefix . 'wpbooklist_jre_user_options';
			$opt_results = $wpdb->get_row("SELECT * FROM $table_name");

			$table_name = $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names';
			$db_row     = $wpdb->get_results( "SELECT * FROM $table_name" );

			$string1 = '<div style="text-align:center;" id="wpbooklist-addbook-container">
				<p style="max-width:600px; margin-left:auto; margin-right:auto;">' . $this->trans->trans_388 . '.</p>
				<p style="text-align:center; max-width:600px; font-weight:bold; font-size:15px; margin-left:auto; margin-right:auto;">' . $this->trans->trans_389 . '</p>
				<p style="max-width:600px; margin-left:auto; margin-right:auto;"><span class="wpbooklist-color-orange-italic">' . $this->trans->trans_57 . '</span>&nbsp;' . $this->trans->trans_390 . '&nbsp;<a href="https://wpbooklist.com/index.php/downloads/mobile-app-extension/">' . $this->trans->trans_392 . '</a>, ' . $this->trans->trans_393 . '</p>
				<p style="text-align:center; max-width:600px; font-weight:bold; font-size:15px; margin-left:auto; margin-right:auto;">' . $this->trans->trans_394 . '</p>
				<p style="max-width:600px; margin-left:auto; margin-right:auto;">' . $this->trans->trans_395 . ' <a href="mailto:General@WPBookList.com">General@WPBookList.com</a>&nbsp;' . $this->trans->trans_396 . '</p>
				<p style="text-align:center; max-width:600px; font-weight:bold; font-size:15px; margin-left:auto; margin-right:auto;">' . $this->trans->trans_397 . '</p>
				<p style="max-width:600px; margin-left:auto; margin-right:auto;">' . $this->trans->trans_398 . ' <a href="https://wpbooklist.com/index.php/extensions/">' . $this->trans->trans_116 . '</a>,&nbsp;<a href="https://wpbooklist.com/index.php/stylepaks-2/">' . $this->trans->trans_299 . '</a>,&nbsp;' . $this->trans->trans_399 . '&nbsp;<a href="https://wpbooklist.com/index.php/templates-2/">' . $this->trans->trans_293 . '</a>,&nbsp;' . $this->trans->trans_400 . '&nbsp;<span style="font-weight:bold; font-style:italic;">' . $this->trans->trans_401 . '</span>&nbsp;' . $this->trans->trans_402 . '&nbsp;<a href="mailto:General@WPBookList.com">General@WPBookList.com</a>&nbsp;</p><br/><br/>
				<p style="text-align:center; max-width:600px; font-weight:bold; font-size:17px; margin-left:auto; margin-right:auto;">' . $this->trans->trans_403 . '&nbsp;<span class="wpbooklist-color-orange-italic">' . $this->trans->trans_57 . '</span></p>
				<img style="margin-left:auto; margin-right:auto; width:75px; margin-bottom:50px;" src="' . ROOT_IMG_ICONS_URL . 'happy.svg" />
				</div>';

			return $string1;
		}
	}

endif;
