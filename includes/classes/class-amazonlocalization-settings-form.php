<?php
/**
 * WPBookList Custom Libraries Form Tab Class
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Amazon_Localization_Settings_Form', false ) ) :
	/**
	 * WPBookList_Admin_Menu Class.
	 */
	class WPBookList_Amazon_Localization_Settings_Form {

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
		public function output_amazon_localization_settings_form() {
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

			$table_name = $wpdb->prefix . 'wpbooklist_jre_user_options';
				$options_row = $wpdb->get_row( "SELECT * FROM $table_name" );

			$string1 = '<div id="wpbooklist-amazon_localization-settings-container">
					<p class="wpbooklist-tab-intro-para">' . $this->trans->trans_371  . ' <span class="wpbooklist-color-orange-italic">' . $this->trans->trans_57 . '</span> ' . $this->trans->trans_372 . '.</p>';

			$string2 = '<table class="wpbooklist-jre-backend-localization-table">
						<tbody>
						<tr>
							<td><label>' . $this->trans->trans_373 . '</label></td>
							<td><input ';

			$string3 = '';
			if ( 'us' === $options_row->amazoncountryinfo ) {
					$string3 = 'checked ';
			}

			$string4 = 'class="wpbooklist-localization-checkbox" value="us" type="checkbox" name="us-based-book-info"></td>
				<td><label>' . $this->trans->trans_374 . '</label></td>
				<td><input ';

			$string5 = '';
			if ( 'uk' === $options_row->amazoncountryinfo ) {
				$string5 = 'checked ';
			}

			$string6 = 'class="wpbooklist-localization-checkbox" value="uk" type="checkbox" name="uk-based-book-info"></td>
			</tr>
			<tr>
				<td><label>' . $this->trans->trans_375 . '</label></td>
				<td><input ';

			$string7 = '';
			if ( 'au' === $options_row->amazoncountryinfo ) {
				$string7 = 'checked ';
			}

			$string8 = 'class="wpbooklist-localization-checkbox" value="au" type="checkbox" name="au-based-book-info"></td>
				<td><label>' . $this->trans->trans_376 . '</label></td>
				<td><input ';

			$string9 = '';
			if ( 'br' === $options_row->amazoncountryinfo ) {
				$string9 = 'checked ';
			}

			$string10 = 'class="wpbooklist-localization-checkbox" value="br" type="checkbox" name="br-based-book-info"></td>
			</tr>
			<tr>
				<td><label>' . $this->trans->trans_377 . '</label></td>
				<td><input ';

			$string11 = '';
			if ( 'ca' === $options_row->amazoncountryinfo ) {
				$string11 = 'checked ';
			}

			$string12 = 'class="wpbooklist-localization-checkbox" value="ca" type="checkbox" name="ca-based-book-info"></td>
				<td><label>' . $this->trans->trans_378 . '</label></td>
				<td><input ';

			$string13 = '';
			if ( 'cn' === $options_row->amazoncountryinfo ) {
				$string13 = 'checked ';
			}

			$string14 = 'class="wpbooklist-localization-checkbox" value="cn" type="checkbox" name="cn-based-book-info"></td>
			</tr>
			<tr>
				<td><label>' . $this->trans->trans_379 . '</label></td>
				<td><input ';

			$string15 = '';
			if ( 'fr' === $options_row->amazoncountryinfo ) {
				$string15 = 'checked ';
			}

			$string16 = 'class="wpbooklist-localization-checkbox" value="fr" type="checkbox" name="fr-based-book-info"></td>
				<td><label>' . $this->trans->trans_380 . '</label></td>
				<td><input ';

			$string17 = '';
			if ( 'de' === $options_row->amazoncountryinfo ) {
				$string17 = 'checked ';
			}

			$string18 = 'class="wpbooklist-localization-checkbox" value="de" type="checkbox" name="de-based-book-info"></td>
			</tr>
			<tr>
				<td><label>' . $this->trans->trans_381 . '</label></td>
				<td><input ';

			$string19 = '';
			if ( 'in' === $options_row->amazoncountryinfo ) {
				$string19 = 'checked ';
			}

			$string20 = 'class="wpbooklist-localization-checkbox" value="in" type="checkbox" name="in-based-book-info"></td>
				<td><label>' . $this->trans->trans_382 . '</label></td>
				<td><input ';

			$string21 = '';
			if ( 'it' === $options_row->amazoncountryinfo ) {
				$string21 = 'checked ';
			}

			$string22 = 'class="wpbooklist-localization-checkbox" value="it" type="checkbox" name="it-based-book-info"></td>
			</tr>
			<tr>
				<td><label>' . $this->trans->trans_383 . '</label></td>
				<td><input ';

			$string23 = '';
			if ( 'jp' === $options_row->amazoncountryinfo ) {
				$string23 = 'checked ';
			}

			$string24 = 'class="wpbooklist-localization-checkbox" value="jp" type="checkbox" name="jp-based-book-info"></td>
			<td><label>' . $this->trans->trans_384 . '</label></td>
			<td><input ';

			$string25 = '';
			if ( 'mx' === $options_row->amazoncountryinfo ) {
				$string25 = 'checked ';
			}

			$string26 = 'class="wpbooklist-localization-checkbox" value="mx" type="checkbox" name="mx-based-book-info"></td>
			</tr>
			<tr>
			<td><label>' . $this->trans->trans_385 . '</label></td>
			<td><input ';

			$string27 = '';
			if ( 'nl' === $options_row->amazoncountryinfo ) {
				$string27 = 'checked ';
			}

			$string28 = 'class="wpbooklist-localization-checkbox" value="nl" type="checkbox" name="nl-based-book-info"></td>
			<td><label>' . $this->trans->trans_386 . '</label></td>
			<td><input ';

			$string29 = '';
			if ( 'es' === $options_row->amazoncountryinfo ) {
				$string29 = 'checked ';
			}

			$string30 = 'class="wpbooklist-localization-checkbox" value="es" type="checkbox" name="es-based-book-info"></td>
			</tr>
			<tr>
				<td><label>' . $this->trans->trans_387 . '</label></td>
				<td><input ';

			$string31 = '';
			if ( 'sg' === $options_row->amazoncountryinfo ) {
				$string31 = 'checked ';
			}

			$string32 = 'class="wpbooklist-localization-checkbox" value="sg" type="checkbox" name="sg-based-book-info"></td>
			</tr>
			</tbody>
		</table>';

			$string33 = '<button class="wpbooklist-response-success-fail-button" id="wpbooklist-save-localization" name="save-backend-localization" type="button">' . $this->trans->trans_245 . '</button><div id="wpbooklist-addamazon_localization-success-div"></div></div>';

		echo $string1 . $string2 . $string3 . $string4 . $string5 . $string6 . $string7 . $string8 . $string9 . $string10 . $string11 . $string12 . $string13 . $string14 . $string15 . $string16 . $string17 . $string18 . $string19 . $string20 . $string21 . $string22 . $string23 . $string24 . $string25 . $string26 . $string27 . $string28 . $string29 . $string30 . $string31 . $string32 . $string33;

		}


	}

endif;
