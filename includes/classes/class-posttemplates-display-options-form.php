<?php
/**
 * WPBookList PostTemplates Display Options Form Tab Class
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_PostTemplates_Display_Options_Form', false ) ) :

	/**
	 * WPBookList_Admin_Menu Class.
	 **/
	class WPBookList_PostTemplates_Display_Options_Form {

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

			if ( null === $default->activeposttemplate || 'Default' === $default->activeposttemplate ) {
				$default->activeposttemplate = 'Default Post Template';
			}

			$default->activeposttemplate = str_replace( 'Post-', 'Post ', $default->activeposttemplate );
			$default->activeposttemplate = str_replace( 'Template-', 'Template ', $default->activeposttemplate );

			$string_table = '<div id="wpbooklist-stylepak-table-container">
								<table>
									<tr id="wpbooklist-stylepak-heading-row">
										<th>
											<img class="wpbooklist-stylepak-heading-img" src="' . ROOT_IMG_ICONS_URL . 'librarystylepak.svg"><div class="wpbooklist-stylepak-table-heading">' . $this->trans->trans_344 . '</div>
										</th>
									</tr>
									<tr>
										<td>
											<div class="wpbooklist-stylepak-table-stylepak">' . ucfirst( $default->activeposttemplate ) . '</div>
										</td>
									</tr>';

			$string_table = $string_table . '</table><div id="wpbooklist-upload-stylepaks-div">
					<input id="wpbooklist-add-new-post-template" style="display:none;" type="file" name="files[]" multiple="">
					<button onclick="document.getElementById(\'wpbooklist-add-new-post-template\').click();" name="add-library-stylepak" type="button">' . $this->trans->trans_345 . '</button>
						<div class="wpbooklist-spinner" id="wpbooklist-spinner-1"></div>
				</div></div>';

			$string1 = '<p class="wpbooklist-tab-intro-para" >' . $this->trans->trans_290 . ' <span class="wpbooklist-color-orange-italic">' . $this->trans->trans_346 . '</span> ' . $this->trans->trans_292 . ' <span class="wpbooklist-color-orange-italic">' . $this->trans->trans_347 . '</span> ' . $this->trans->trans_294. ' <span class="wpbooklist-color-orange-italic">' . $this->trans->trans_57 . '</span> ' . $this->trans->trans_348 . '</p><p class="wpbooklist-tab-intro-para">' . $this->trans->trans_312 . ' <a href="https://wpbooklist.com/index.php/templates-2/">' . $this->trans->trans_349 . '</a>, ' . $this->trans->trans_314 . ' <span class="wpbooklist-color-orange-italic">\'' . $this->trans->trans_350 . '\'</span>&nbsp;' . $this->trans->trans_351 . '</p>

				<div id="wpbooklist-stylepak-demo-links">
					<a href="https://wpbooklist.com/index.php/downloads/template-pak-1/">' . $this->trans->trans_352 . '</a>
					<a href="https://wpbooklist.com/index.php/downloads/template-pak-2/">' . $this->trans->trans_353 . '</a>
					<a href="https://wpbooklist.com/index.php/downloads/template-pak-3/">' . $this->trans->trans_354 . '</a>
					<a href="https://wpbooklist.com/index.php/downloads/template-pak-4/">' . $this->trans->trans_355 . '</a>
					<a href="https://wpbooklist.com/index.php/downloads/template-pak-5/">' . $this->trans->trans_356 . '</a>
				</div>

				<div id="wpbooklist-buy-library-stylepaks-div">
					<a id="wpbooklist-stylepak-buy-link" href="https://wpbooklist.com/index.php/templates-2/"><img src="' . ROOT_IMG_URL . 'getposttemplates.png" /></a>
				</div>';

			$string2 = '<div id="wpbooklist-apply-stylepak-wrapper"><div id="wpbooklist-stylepak-select-stylepak-label">' . $this->trans->trans_357 . ':</div>
							<select class="wpbooklist-stylepak-select-default" id="wpbooklist-select-post-template">	
								<option selected disabled>' . $this->trans->trans_358 . '</option>
								<option value="Default Template">' . $this->trans->trans_359 . '</option>';

			foreach ( glob( POST_TEMPLATES_UPLOAD_DIR . '*.*' ) as $filename ) {
				$filename = basename( $filename );
				if ( ( strpos( $filename, '.php') || strpos( $filename, '.zip' ) ) && false !== strpos( $filename, 'Post' ) && false !== strpos( $filename, 'Template' ) ) {
					$display_name = str_replace( '.php', '', $filename );
					$display_name = str_replace( '-', ' ', $display_name );
					$display_name = str_replace( 'Post-', 'Post ', $display_name );
					$string2 = $string2 . '<option id="' . $filename . '" value="' . $filename . '">' . $display_name . '</option>';
				}
			}

			$string3 = '</select></div><button disabled id="wpbooklist-apply-post-template">' . $this->trans->trans_360 . '</button>
							<div id="wpbooklist-addtemplate-success-div"></div>';


			echo $string1.$string_table.$string2.$string3;
		}


	}

endif;
