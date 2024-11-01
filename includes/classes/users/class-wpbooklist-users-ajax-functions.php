<?php
/**
 * Class WPHealthTracker_Users_Ajax_Functions - wpbooklist-users-ajax.php
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Users
 * @version  0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPHealthTracker_Users_Ajax_Functions', false ) ) :
	/**
	 * WPHealthTracker_Users_Ajax_Functions class. Here we'll house all of the Ajax Callback functions we'll need to
	 * make the Users tab run as expected. The initial functions that send calls to the functions in this file are
	 * located in the wpbooklist-users-js.js JavaScript file.
	 */
	class WPHealthTracker_Users_Ajax_Functions {

		/**
		 * Class Constructor
		 */
		public function __construct() {

			// Set the date.
			require_once CLASS_UTILITIES_DIR . 'class-wpbooklist-utilities-date.php';
			$utilities_date = new WPBookList_Utilities_Date();
			$this->date     = $utilities_date->wpbooklist_get_date_via_current_time( 'mysql' );

		}

		/**
		 * Callback function for the wpbooklist_jre_save_users_data() function found in wpbooklist-users-js.js - for saving user's data.
		 */
		public function wpbooklist_jre_create_wp_users_data_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_jre_create_wp_users_data_action', 'security' );

			$username = '';
			$email    = '';
			$password = '';

			if ( isset( $_POST['email'] ) ) {
				$email = filter_var( $_POST['email'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['password'] ) ) {
				$password = filter_var( $_POST['password'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['username'] ) ) {
				$username = filter_var( $_POST['username'], FILTER_SANITIZE_STRING );
			}

			$error   = '';
			$user_id = username_exists( $username );

			if ( $user_id ) {
				$error = 'Username Exists';
				wp_die( $error );
			}

			if ( email_exists( $email ) ) {
				$error = 'E-Mail Exists';
				wp_die( $error );
			}

			if ( ! $user_id && false === email_exists( $email ) ) {
				$user_id = wp_create_user( $username, $password, $email );
				if ( ! is_wp_error( $user_id ) ) {
					$user = get_user_by( 'id', $user_id );
					$user->set_role( 'wpbooklist_basic_user' );
					wp_die( '$user_id' );
				}
			}
		}

		/**
		 * Callback function for the wpbooklist_jre_save_users_data() function found in wpbooklist-users-js.js - for saving user's data.
		 */
		public function wpbooklist_jre_save_users_data_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_jre_save_users_data_action_callback', 'security' );

			$firstname         = '';
			$lastname          = '';
			$email             = '';
			$emailconfirm      = '';
			$password          = '';
			$passwordconfirm   = '';
			$username          = '';
			$role              = '';
			$country           = '';
			$street1           = '';
			$street2           = '';
			$city              = '';
			$state             = '';
			$zip               = '';
			$phone             = '';
			$profileimage      = '';
			$birthday          = '';
			$gender            = '';
			$heightfeet        = '';
			$heightinches      = '';
			$mainfocus         = '';
			$motivationalquote = '';
			$bio               = '';
			$wpuserid          = '';

			if ( isset( $_POST['firstname'] ) ) {
				$firstname = filter_var( $_POST['firstname'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['lastname'] ) ) {
				$lastname = filter_var( $_POST['lastname'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['email'] ) ) {
				$email = filter_var( $_POST['email'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['emailconfirm'] ) ) {
				$emailconfirm = filter_var( $_POST['emailconfirm'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['password'] ) ) {
				$password = filter_var( $_POST['password'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['passwordconfirm'] ) ) {
				$passwordconfirm = filter_var( $_POST['passwordconfirm'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['username'] ) ) {
				$username = filter_var( $_POST['username'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['role'] ) ) {
				$role = filter_var( $_POST['role'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['country'] ) ) {
				$country = filter_var( $_POST['country'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['street1'] ) ) {
				$street1 = filter_var( $_POST['street1'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['street2'] ) ) {
				$street2 = filter_var( $_POST['street2'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['city'] ) ) {
				$city = filter_var( $_POST['city'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['state'] ) ) {
				$state = filter_var( $_POST['state'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['zip'] ) ) {
				$zip = filter_var( $_POST['zip'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['phone'] ) ) {
				$phone = filter_var( $_POST['phone'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['profileimage'] ) ) {
				$profileimage = filter_var( $_POST['profileimage'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['birthday'] ) ) {
				$birthday = filter_var( $_POST['birthday'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['gender'] ) ) {
				$gender = filter_var( $_POST['gender'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['heightfeet'] ) ) {
				$heightfeet = filter_var( $_POST['heightfeet'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['heightinches'] ) ) {
				$heightinches = filter_var( $_POST['heightinches'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['mainfocus'] ) ) {
				$mainfocus = filter_var( $_POST['mainfocus'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['motivationalquote'] ) ) {
				$motivationalquote = filter_var( $_POST['motivationalquote'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['bio'] ) ) {
				$bio = filter_var( $_POST['bio'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['wpuserid'] ) ) {
				$wpuserid = filter_var( $_POST['wpuserid'], FILTER_SANITIZE_STRING );
			}

			$users_save_array = array(
				'firstname'                 => $firstname,
				'lastname'                  => $lastname,
				'email'                     => $email,
				'username'                  => $username,
				'role'                      => $role,
				'country'                   => $country,
				'streetaddress1'            => $street1,
				'streetaddress2'            => $street2,
				'city'                      => $city,
				'state'                     => $state,
				'zip'                       => $zip,
				'phone'                     => $phone,
				'profileimage'              => $profileimage,
				'birthday'                  => $birthday,
				'gender'                    => $gender,
				'height'                    => $heightfeet . ' ' . $heightinches,
				'mainexercisecategory'      => $mainfocus,
				'favoritemotivationalquote' => $motivationalquote,
				'bio'                       => $bio,
				'wpuserid'                  => $wpuserid,
				'datecreated'               => $this->date,
			);

			// Requiring & Calling the file/class that will insert or update our data.
			require_once WPHEALTHTRACKER_USERS_DIR . 'class-wpbooklist-save-users-data.php';
			$save_class      = new WPHEALTHTRACKER_Save_Users_Data( $users_save_array );
			$db_write_result = $save_class->wpbooklist_jre_save_users_actual();

			// Build array of values to return to browser.
			$return_array = array(
				$db_write_result,
				$save_class->dbmode,
				$save_class->email,
				$save_class->wpuserid,
				$save_class->last_query,
				$save_class->transients_deleted,
				wp_json_encode( $save_class->users_save_array ),
			);

			// Serialize array.
			$return_array = wp_json_encode( $return_array );
			wp_die( $return_array );

		}
	}

endif;


