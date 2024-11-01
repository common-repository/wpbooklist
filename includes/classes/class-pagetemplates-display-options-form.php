<?php
/**
 * WPBookList PageTemplates Display Options Form Tab Class - class-librarystylepaks-display-options-form.php.
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  6.1.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_PageTemplates_Display_Options_Form', false ) ) :
	/**
	 * WPBookList_Admin_Menu Class.
	 **/
	class WPBookList_PageTemplates_Display_Options_Form {

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
		public function output_add_edit_form() {
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

			$table_name = $wpdb->prefix . 'wpbooklist_jre_user_options';
			$default    = $wpdb->get_row( "SELECT * FROM $table_name" );

			if ( null === $default->activepagetemplate || 'Default' === $default->activepagetemplate ) {
				$default->activepagetemplate = 'Default Page Template';
			}

			$default->activepagetemplate = str_replace( 'Page-', 'Page ', $default->activepagetemplate );
			$default->activepagetemplate = str_replace( 'Template-', 'Template ', $default->activepagetemplate );

			$string_table = '<div id="wpbooklist-stylepak-table-container">
								<table>
									<tr id="wpbooklist-stylepak-heading-row">
										<th>
											<img class="wpbooklist-stylepak-heading-img" src="' . ROOT_IMG_ICONS_URL . 'librarystylepak.svg"><div class="wpbooklist-stylepak-table-heading">' . $this->trans->trans_327 . '</div>
										</th>
									</tr>
									<tr>
										<td>
											<div class="wpbooklist-stylepak-table-stylepak">' . ucfirst( $default->activepagetemplate ) . '</div>
										</td>
									</tr>';

			$string_table = $string_table .
								'</table>
							</div>
							<div id="wpbooklist-upload-stylepaks-div">
								<input id="wpbooklist-add-new-page-template" style="display:none;" type="file" name="files[]" multiple="">
								<button onclick="document.getElementById(\'wpbooklist-add-new-page-template\') .click();" name="add-library-stylepak" type="button">' . $this->trans->trans_328 . '</button>
									<div class="wpbooklist-spinner" id="wpbooklist-spinner-1"></div>
							</div>';

			$string1 = '<p class="wpbooklist-tab-intro-para">' . $this->trans->trans_290 . ' <span class="wpbooklist-color-orange-italic">' . $this->trans->trans_329 . '</span> ' . $this->trans->trans_292 . ' <span class="wpbooklist-color-orange-italic">' . $this->trans->trans_330 . '</span> ' . $this->trans->trans_294 . ' <span class="wpbooklist-color-orange-italic">' . $this->trans->trans_57 . '</span> ' . $this->trans->trans_331 . '</p><p class="wpbooklist-tab-intro-para">' . $this->trans->trans_312 . ' <a href="https://wpbooklist.com/index.php/templates-2/">' . $this->trans->trans_332 . '</a>, ' . $this->trans->trans_314 . ' <span class="wpbooklist-color-orange-italic">\'' . $this->trans->trans_333 . '\'</span>&nbsp;' . $this->trans->trans_334 . '</p>

				<div id="wpbooklist-stylepak-demo-links">
					<a href="https://wpbooklist.com/index.php/downloads/template-pak-1/">' . $this->trans->trans_335 . '</a>
					<a href="https://wpbooklist.com/index.php/downloads/template-pak-2/">' . $this->trans->trans_336 . '</a>
					<a href="https://wpbooklist.com/index.php/downloads/template-pak-3/">' . $this->trans->trans_337 . '</a>
					<a href="https://wpbooklist.com/index.php/downloads/template-pak-4/">' . $this->trans->trans_338 . '</a>
					<a href="https://wpbooklist.com/index.php/downloads/template-pak-5/">' . $this->trans->trans_339 . '</a>
				</div>

				<div id="wpbooklist-buy-library-stylepaks-div">
					<a id="wpbooklist-stylepak-buy-link" href="https://wpbooklist.com/index.php/templates-2/"><img src="' . ROOT_IMG_URL . 'getpagetemplates.png" /></a>
				</div>';

			$string2 = '<div id="wpbooklist-apply-stylepak-wrapper"><div id="wpbooklist-stylepak-select-stylepak-label">' . $this->trans->trans_340 . ':</div>
							<select class="wpbooklist-stylepak-select-default" id="wpbooklist-select-page-template">	
								<option selected disabled>' . $this->trans->trans_341 . '</option>
								<option value="Default Template">' . $this->trans->trans_342 . '</option>';

			foreach ( glob( PAGE_TEMPLATES_UPLOAD_DIR . '*.*' ) as $filename ) {
				$filename = basename( $filename );
				if ( ( strpos( $filename, '.php' ) || strpos( $filename, '.zip' ) ) && false !== strpos( $filename, 'Page' ) && false !== strpos( $filename, 'Template' ) ) {
					$display_name = str_replace( ' .php', '', $filename );
					$display_name = str_replace( '.php', '', $display_name );
					$display_name = str_replace( 'Template-', 'Template ', $display_name );
					$display_name = str_replace( 'Page-', 'Page ', $display_name );
					$string2      = $string2 . '<option id="' . $filename . '" value="' . $filename . '">' . $display_name . '</option>';
				}
			}

			$string3 = '</select></div><button disabled id="wpbooklist-apply-page-template">' . $this->trans->trans_343 . '</button>
							<div id="wpbooklist-addtemplate-success-div"></div>';

			echo $string1 . $string_table . $string2 . $string3;
		}


	}

endif;
