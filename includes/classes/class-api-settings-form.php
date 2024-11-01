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

if ( ! class_exists( 'WPBookList_Api_Settings_Form', false ) ) :
	/**
	 * WPBookList_Admin_Menu Class.
	 */
	class WPBookList_Api_Settings_Form {


		/**
		 * Class Constructor - Simply calls the Translations
		 */
		public function __construct() {

			// Get Translations.
			require_once CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
			$this->trans = new WPBookList_Translations();
			$this->trans->trans_strings();

		}

		public function output_api_settings_form() {
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
			
			$table_name  = $wpdb->prefix . 'wpbooklist_jre_user_options';
			$options_row = $wpdb->get_row( "SELECT * FROM $table_name" );

			$string1 = '<div id="wpbooklist-api-settings-container">
							<p class="wpbooklist-tab-intro-para"><span class="wpbooklist-color-orange-italic">' . $this->trans->trans_57 . '</span> ' . $this->trans->trans_361 . ' <span class="wpbooklist-color-orange-italic">\'' . $this->trans->trans_362 . '\'</span>.</p>';

			$string2 = '<table>
							<tbody>';

								$string3 = '<tr>
									<td>
										<label for="amazon-api-public">' . $this->trans->trans_363 . '</label>
										<input placeholder="' . $this->trans->trans_364 . '"';

										$string4 = '';
										if ( null !== $options_row->amazonapipublic ) {
											$string4 = 'value="' . $options_row->amazonapipublic . '"';
										}

										$string5 = ' id="wpbooklist-amazon-api-public" name="amazon-api-public" type="text" />
										<a href="https://affiliate-program.amazon.com/assoc_credentials/home?openid.assoc_handle=amzn_associates_us&aToken=Atza%7CIwEBIFY4PNTc_kAMxJJnxo_Ek0gpK6WDvxFLfiHsHYtrwerqfugUKQNvzE_nxCgl9n_x1yRELr8_lQxHcmOjJkUG2zex61d-XYCuP559ilYGVeg47nLxtWLZzso8h9lC_6Ws1SWdYPYr8pJN2iybknX0hBk_XSgshu4MRLf5zYpWjrH2RtbgRs43eISurd7VMy0UGQkYxn1AHEqwEMLEieWQx6PJbr7fFYrItqTDzijc5dsZaPZYWJ4FeX4EzS7hpt_SWs0pcEyzbvbqSa-hUAUoCoTZoG2j3T7uEBeZ7-5UNfsIeqPFfzZtjtRTFbLkaHRUXadfxO3RMmSRdVoC1frvcOIfyWAwxXx1phVssvH3fzhmi7TxT_asfLiJeaEDepf4UcdSu03PBQAQ6HMbb4PFhchX&openid.claimed_id=https%3A%2F%2Fwww.amazon.com%2Fap%2Fid%2Famzn1.account.AH72MNZJ2ZDMZPDOZWUK4KTQBOJA&openid.identity=https%3A%2F%2Fwww.amazon.com%2Fap%2Fid%2Famzn1.account.AH72MNZJ2ZDMZPDOZWUK4KTQBOJA&openid.mode=id_res&openid.ns=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0&openid.op_endpoint=https%3A%2F%2Fwww.amazon.com%2Fap%2Fsignin&openid.response_nonce=2017-08-29T21%3A04%3A06Z8240409384152911475&openid.return_to=https%3A%2F%2Faffiliate-program.amazon.com%2Fassoc_credentials%2Fhome&openid.signed=assoc_handle%2CaToken%2Cclaimed_id%2Cidentity%2Cmode%2Cns%2Cop_endpoint%2Cresponse_nonce%2Creturn_to%2Cns.pape%2Cpape.auth_policies%2Cpape.auth_time%2Csigned&openid.ns.pape=http%3A%2F%2Fspecs.openid.net%2Fextensions%2Fpape%2F1.0&openid.pape.auth_policies=http%3A%2F%2Fschemas.openid.net%2Fpape%2Fpolicies%2F2007%2F06%2Fnone&openid.pape.auth_time=2017-08-29T21%3A04%3A06Z&openid.sig=A%2FXU49pRFfJPVL6ToHSqMMqlwfV%2Fr%2BZsLgHVQeNBnE4%3D&serial=&" class="wpbooklist-api-get-link">' . $this->trans->trans_365 . '</a>
									</td>
								</tr>';

								$string6 = '<tr>
									<td>
										<label for="amazon-api-secret">' . $this->trans->trans_366 . '</label>
										<input placeholder="' . $this->trans->trans_364 . '"';

										$string7 = '';
										if ( null !== $options_row->amazonapisecret ) {
											$string7 = 'value="' . $options_row->amazonapisecret . '"';
										}

										$string8 = 'id="wpbooklist-amazon-api-secret" name="amazon-api-secret" type="text" />
										<a href="https://affiliate-program.amazon.com/assoc_credentials/home?openid.assoc_handle=amzn_associates_us&aToken=Atza%7CIwEBIFY4PNTc_kAMxJJnxo_Ek0gpK6WDvxFLfiHsHYtrwerqfugUKQNvzE_nxCgl9n_x1yRELr8_lQxHcmOjJkUG2zex61d-XYCuP559ilYGVeg47nLxtWLZzso8h9lC_6Ws1SWdYPYr8pJN2iybknX0hBk_XSgshu4MRLf5zYpWjrH2RtbgRs43eISurd7VMy0UGQkYxn1AHEqwEMLEieWQx6PJbr7fFYrItqTDzijc5dsZaPZYWJ4FeX4EzS7hpt_SWs0pcEyzbvbqSa-hUAUoCoTZoG2j3T7uEBeZ7-5UNfsIeqPFfzZtjtRTFbLkaHRUXadfxO3RMmSRdVoC1frvcOIfyWAwxXx1phVssvH3fzhmi7TxT_asfLiJeaEDepf4UcdSu03PBQAQ6HMbb4PFhchX&openid.claimed_id=https%3A%2F%2Fwww.amazon.com%2Fap%2Fid%2Famzn1.account.AH72MNZJ2ZDMZPDOZWUK4KTQBOJA&openid.identity=https%3A%2F%2Fwww.amazon.com%2Fap%2Fid%2Famzn1.account.AH72MNZJ2ZDMZPDOZWUK4KTQBOJA&openid.mode=id_res&openid.ns=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0&openid.op_endpoint=https%3A%2F%2Fwww.amazon.com%2Fap%2Fsignin&openid.response_nonce=2017-08-29T21%3A04%3A06Z8240409384152911475&openid.return_to=https%3A%2F%2Faffiliate-program.amazon.com%2Fassoc_credentials%2Fhome&openid.signed=assoc_handle%2CaToken%2Cclaimed_id%2Cidentity%2Cmode%2Cns%2Cop_endpoint%2Cresponse_nonce%2Creturn_to%2Cns.pape%2Cpape.auth_policies%2Cpape.auth_time%2Csigned&openid.ns.pape=http%3A%2F%2Fspecs.openid.net%2Fextensions%2Fpape%2F1.0&openid.pape.auth_policies=http%3A%2F%2Fschemas.openid.net%2Fpape%2Fpolicies%2F2007%2F06%2Fnone&openid.pape.auth_time=2017-08-29T21%3A04%3A06Z&openid.sig=A%2FXU49pRFfJPVL6ToHSqMMqlwfV%2Fr%2BZsLgHVQeNBnE4%3D&serial=&" class="wpbooklist-api-get-link">' . $this->trans->trans_365 . '</a>
									</td>
								</tr>';

								$string9 = '<tr>
									<td>
										<label for="google-api">' . $this->trans->trans_367 . '</label>
										<input placeholder="' . $this->trans->trans_364 . '"';

										$string10 = '';
										if($options_row->googleapi != null){
											$string10 = 'value="'.$options_row->googleapi.'"';
										}

										$string11 = ' id="wpbooklist-google-api" name="google-api" type="text" />
										<a href="https://developers.google.com/books/docs/v1/using#APIKey" class="wpbooklist-api-get-link">' . $this->trans->trans_368 . '</a>
									</td>
								</tr>';

							$string18 = '</tbody>
						</table>
						<div class="wpbooklist-spinner" id="wpbooklist-spinner-api"></div>
						<div id="wpbooklist-api-results"></div>
						<button class="wpbooklist-response-success-fail-button" id="wpbooklist-save-api-settings" type="button">' . $this->trans->trans_362 . '</button>
					</div>';

			echo $string1.$string2.$string3.$string4.$string5.$string6.$string7.$string8.$string9.$string10.$string11.$string18;

		}


	}

endif;
