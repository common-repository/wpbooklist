<?php
/**
 * Class WPBookList_General_Functions - class-wpbooklist-general-functions.php
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes
 * @version  6.1.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_General_Functions', false ) ) :
	/**
	 * WPBookList_General_Functions class. Here we'll do things like enqueue scripts/css, set up menus, etc.
	 */
	class WPBookList_General_Functions {

		/**
		 * This function registers the 'wpbooklist_deactivate_all_extensions_later_hook' function with the "update_option_active_plugins" hook upon deactivaion of this core WPBookList plugin.
		 */
		public function wpbooklist_deactivate_all_extensions() {
			add_action( 'update_option_active_plugins', array( $this, 'wpbooklist_deactivate_all_extensions_later_hook' ) );
		}

		/**
		 * This function does the actual deactivation of the WPBookList Extensions, when the user deactivates the core WPBookList plugin.
		 */
		public function wpbooklist_deactivate_all_extensions_later_hook() {

			$dependent_array = array(
				'wpbooklist-affiliates/wpbooklist-affiliates.php',
				'wpbooklist-bookfinder/wpbooklist-bookfinder.php',
				'wpbooklist-branding/wpbooklist-branding.php',
				'wpbooklist-bulkbookupload/wpbooklist-bulkbookupload.php',
				'wpbooklist-carousel/wpbooklist-carousel.php',
				'wpbooklist-categories/wpbooklist-categories.php',
				'wpbooklist-comments/wpbooklist-comments.php',
				'wpbooklist-customfields/wpbooklist-customfields.php',
				'wpbooklist-goodreads/wpbooklist-goodreads.php',
				'wpbooklist-kindlepreview/wpbooklist-kindlepreview.php',
				'wpbooklist-googlepreview/wpbooklist-googlepreview.php',
				'wpbooklist-mobile/wpbooklist-mobile.php',
				'wpbooklist-search/wpbooklist-search.php',
				'wpbooklist-storefront/wpbooklist-storefront.php',
				'wpbooklist-stylizer/wpbooklist-stylizer.php',
				'wpbooklist-ebook/wpbooklist-ebook.php',
			);

			foreach ( $dependent_array as $key => $extension ) {
				deactivate_plugins( $extension );
			}
		}

		/**
		 * This function hides the admin bar if the logged-in user is a basic WPBookList User.
		 */
		public function wpbooklist_hide_admin_bar_if_basic_wpbooklist_user() {

			if ( is_user_logged_in() ) {
				$user = wp_get_current_user();
				$role = (array) $user->roles;

				// If the array isn't empty...
				if ( 0 < count( $role ) ) {
					if ( 'wpbooklist_basic_user' === $role[0] ) {
						show_admin_bar( false );
					}
				}
			}
		}

		/**
		 * Create new WPBookList User Role on plugin activation.
		 */
		public function wpbooklist_add_wpbooklist_role_on_plugin_activation() {

			require_once CLASS_UTILITIES_DIR . 'class-wpbooklist-utilities-accesscheck.php';
			$this->access          = new WPBookList_Utilities_Accesscheck();
			$this->currentwpbluser = $this->access->wpbooklist_accesscheck_create_role( 'WPBookList Basic User' );

			// Set the date.
			require_once CLASS_UTILITIES_DIR . 'class-wpbooklist-utilities-date.php';
			$utilities_date = new WPBookList_Utilities_Date();
			$this->date     = $utilities_date->wpbooklist_get_date_via_current_time( 'mysql' );
		}

		/**
		 * Create new WPBookList User on plugin activation based on logged-in user.
		 */
		public function wpbooklist_create_wpbooklist_user_on_plugin_activation() {

			global $wpdb;

			// Set the date.
			require_once CLASS_UTILITIES_DIR . 'class-wpbooklist-utilities-date.php';
			$utilities_date = new WPBookList_Utilities_Date();
			$this->date     = $utilities_date->wpbooklist_get_date_via_current_time( 'mysql' );

			// Checking if table exists.
			$test_name = $wpdb->prefix . 'wpbooklist_jre_users_table';
			if ( $test_name === $wpdb->get_var( "SHOW TABLES LIKE '$test_name'" ) ) {

				// First let's check and see that we don't already have a user with the SuperAdmin role.
				$superadmin = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . "wpbooklist_jre_users_table WHERE role = 'SuperAdmin'" );

				// If we don't have a user with the 'SuperAdmin' role, create a new user as a SuperAdmin with the logged-in user's info.
				if ( null === $superadmin ) {
					if ( is_user_logged_in() ) {

						$current_user = wp_get_current_user();
						if ( ! $current_user->exists() ) {
							return;
						}

						// Create the permissions string.
						$permissions = 'Yes-Yes-Yes-Yes-Yes';

						$users_save_array = array(
							'firstname'    => $current_user->user_firstname,
							'lastname'     => $current_user->user_lastname,
							'datecreated'  => $this->date,
							'wpuserid'     => $current_user->ID,
							'email'        => $current_user->user_email,
							'username'     => $current_user->user_email,
							'role'         => 'SuperAdmin',
							'permissions'  => $permissions,
							'libraries'    => 'alllibraries',
							'profileimage' => get_avatar_url( $current_user->ID ),
						);

						// Requiring & Calling the file/class that will insert or update our data.
						require_once CLASS_USERS_DIR . 'class-wpbooklist-save-users-data.php';
						$save_class      = new WPBOOKLIST_Save_Users_Data( $users_save_array );
						$db_write_result = $save_class->wpbooklist_jre_save_users_actual();
					}
				}

				// Now add all WordPress users as basic wpbooklist users.
				$all_users = get_users();
				foreach ( $all_users as $key => $value ) {

					$regularadmin = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_users_table WHERE wpuserid = ' . $value->ID );

					// Add this user if they don't already exist. Limit them to the Default Library, and prevent them from making any Display or Setting changes.
					if ( null === $regularadmin ) {

						// Create the permissions string.
						$permissions = 'Yes-Yes-Yes-No-No';

						$users_save_array = array(
							'firstname'    => $value->user_firstname,
							'lastname'     => $value->user_lastname,
							'datecreated'  => $this->date,
							'wpuserid'     => $value->ID,
							'email'        => $value->user_email,
							'username'     => $value->user_email,
							'role'         => null,
							'permissions'  => $permissions,
							'libraries'    => '-wp_wpbooklist_jre_saved_book_log',
							'profileimage' => get_avatar_url( $value->ID ),
						);

						// Requiring & Calling the file/class that will insert or update our data.
						require_once CLASS_USERS_DIR . 'class-wpbooklist-save-users-data.php';
						$save_class      = new WPBOOKLIST_Save_Users_Data( $users_save_array );
						$db_write_result = $save_class->wpbooklist_jre_save_users_actual();

					}
				}
			}

		}

		/**
		 *  Functions that loads up all menu pages/contents, etc.
		 */
		public function wpbooklist_jre_admin_page_function() {
			global $wpdb;
			require_once ROOT_INCLUDES_UI_ADMIN_DIR . 'class-admin-master-ui.php';
		}

		/**
		 *  Function to add the admin menu
		 */
		public function wpbooklist_jre_my_admin_menu() {

			add_menu_page( 'WPBookList Options', 'WPBookList', 'wpbooklist_dashboard_access', 'WPBookList-Options', array( $this, 'wpbooklist_jre_admin_page_function' ), ROOT_IMG_URL . 'icon-256x256.png', 6 );

			$submenu_array = array(
				'Books',
				'Users',
				'Display Options',
				'Settings',
				'StoryTime',
				'Extensions',
				'StylePaks',
				'Template Paks',
			);

			// Filter to allow the addition of a new subpage.
			if ( has_filter( 'wpbooklist_add_sub_menu' ) ) {
				$submenu_array = apply_filters( 'wpbooklist_add_sub_menu', $submenu_array );
			}

			foreach ( $submenu_array as $key => $submenu ) {
				$menu_slug = strtolower( str_replace( ' ', '-', $submenu ) );
				add_submenu_page( 'WPBookList-Options', 'WPBookList', $submenu, 'wpbooklist_dashboard_access', 'WPBookList-Options-' . $menu_slug, array( $this, 'wpbooklist_jre_admin_page_function' ) );
			}

			remove_submenu_page( 'WPBookList-Options', 'WPBookList-Options' );
		}
		
		/**
		 *  Code for adding ajax
		 */
		public function wpbooklist_jre_prem_add_ajax_library() {

			$html = '<script type="text/javascript">';

			// Checking $protocol in HTTP or HTTPS.
			if ( isset( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] ) {
				// This is HTTPS.
				$protocol = 'https';
			} else {
				// This is HTTP.
				$protocol = 'http';
			}
			$temp_ajax_path = admin_url( 'admin-ajax.php' );
			$good_ajax_url  = $protocol . strchr( $temp_ajax_path, ':' );

			$html .= 'var ajaxurl = "' . $good_ajax_url . '"';
			$html .= '</script>';
			echo $html;
		}

		/**
		 *  Here we take the Constant defined in wpbooklist.php that holds the values that all our nonces will be created from, we create the actual nonces using wp_create_nonce, and the we define our new, final nonces Constant, called WPBOOKLIST_FINAL_NONCES_ARRAY.
		 */
		public function wpbooklist_jre_create_nonces() {

			$temp_array = array();
			foreach ( json_decode( WPBOOKLIST_NONCES_ARRAY ) as $key => $noncetext ) {
				$nonce              = wp_create_nonce( $noncetext );
				$temp_array[ $key ] = $nonce;
			}

			// Defining our final nonce array.
			define( 'WPBOOKLIST_FINAL_NONCES_ARRAY', wp_json_encode( $temp_array ) );

		}

		/**
		 * Adding the admin js file
		 */
		public function wpbooklist_jre_admin_js() {

			global $wpdb;

			wp_register_script( 'adminjs', ROOT_JS_URL . 'wpbooklist_admin.min.js', array( 'jquery' ), WPBOOKLIST_VERSION_NUM, true );

			// Next 4-5 lines are required to allow translations of strings that would otherwise live in the wpbooklist-admin-js.js JavaScript File.
			require_once CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
			$trans = new WPBookList_Translations();

			// Localize the script with the appropriate translation array from the Translations class.
			$translation_array1 = $trans->trans_strings();

			// Now grab all of our Nonces to pass to the JavaScript for the Ajax functions and merge with the Translations array.
			$final_array_of_php_values = array_merge( $translation_array1, json_decode( WPBOOKLIST_FINAL_NONCES_ARRAY, true ) );

			// Adding some other individual values we may need.
			$final_array_of_php_values['ROOT_IMG_ICONS_URL']   = ROOT_IMG_ICONS_URL;
			$final_array_of_php_values['ROOT_IMG_URL']   = ROOT_IMG_URL;
			$final_array_of_php_values['EDIT_PAGE_OFFSET']   = EDIT_PAGE_OFFSET;
			$final_array_of_php_values['FOR_TAB_HIGHLIGHT']    = admin_url() . 'admin.php';
			$final_array_of_php_values['SAVED_ATTACHEMENT_ID'] = get_option( 'media_selector_attachment_id', 0 );
			$final_array_of_php_values['LIBRARY_DB_BACKUPS_UPLOAD_URL'] = LIBRARY_DB_BACKUPS_UPLOAD_URL;
			$final_array_of_php_values['SOUNDS_URL'] = SOUNDS_URL;
			$final_array_of_php_values['SETTINGS_PAGE_URL'] = menu_page_url( 'WPBookList-Options-settings', false );
			$final_array_of_php_values['DB_PREFIX'] = $wpdb->prefix;

			// Adding the Custom Fields String.
			$this->user_options = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_user_options' );
			$final_array_of_php_values['CUSTOM_FIELDS_STRING'] = $this->user_options->customfields;

			// Now registering/localizing our JavaScript file, passing all the PHP variables we'll need in our $final_array_of_php_values array, to be accessed from 'wpbooklist_php_variables' object (like wpbooklist_php_variables.nameofkey, like any other JavaScript object).
			wp_localize_script( 'adminjs', 'wpbooklistPhpVariables', $final_array_of_php_values );

			wp_enqueue_script( 'adminjs' );

			return $final_array_of_php_values;

		}

		/**
		 * Adding the jQuery Autocomplete that ships with WordPress
		 */
		public function wpbooklist_jre_add_core_jquery_ui() {
			wp_enqueue_script( 'jquery-ui-autocomplete' );
		}

		/**
		 * Adding the frontend js file
		 */
		public function wpbooklist_jre_frontend_js() {

			wp_register_script( 'frontendjs', ROOT_JS_URL . 'wpbooklist_frontend.min.js', array( 'jquery' ), WPBOOKLIST_VERSION_NUM, true );

			// Next 4-5 lines are required to allow translations of strings that would otherwise live in the wpbooklist-admin-js.js JavaScript File.
			require_once CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
			$trans = new WPBookList_Translations();

			// Localize the script with the appropriate translation array from the Translations class.
			$translation_array1 = $trans->trans_strings();

			// Now grab all of our Nonces to pass to the JavaScript for the Ajax functions and merge with the Translations array.
			$final_array_of_php_values = array_merge( $translation_array1, json_decode( WPBOOKLIST_FINAL_NONCES_ARRAY, true ) );

			// Adding some other individual values we may need.
			$final_array_of_php_values['ROOT_IMG_ICONS_URL'] = ROOT_IMG_ICONS_URL;
			$final_array_of_php_values['ROOT_IMG_URL']       = ROOT_IMG_URL;
			$final_array_of_php_values['SOUNDS_URL']         = SOUNDS_URL;

			// Now registering/localizing our JavaScript file, passing all the PHP variables we'll need in our $final_array_of_php_values array, to be accessed from 'wpbooklist_php_variables' object (like wpbooklist_php_variables.nameofkey, like any other JavaScript object).
			wp_localize_script( 'frontendjs', 'wpbooklistPhpVariables', $final_array_of_php_values );

			wp_enqueue_script( 'frontendjs' );

			return $final_array_of_php_values;

		}

		/**
		 *  Function to add table names to the global $wpdb
		 */
		public function wpbooklist_jre_register_table_name() {
			global $wpdb;
			$wpdb->wpbooklist_jre_saved_book_log             = "{$wpdb->prefix}wpbooklist_jre_saved_book_log";
			$wpdb->wpbooklist_jre_saved_page_post_log        = "{$wpdb->prefix}wpbooklist_jre_saved_page_post_log";
			$wpdb->wpbooklist_jre_saved_books_for_featured   = "{$wpdb->prefix}wpbooklist_jre_saved_books_for_featured";
			$wpdb->wpbooklist_jre_user_options               = "{$wpdb->prefix}wpbooklist_jre_user_options";
			$wpdb->wpbooklist_jre_page_options               = "{$wpdb->prefix}wpbooklist_jre_page_options";
			$wpdb->wpbooklist_jre_post_options               = "{$wpdb->prefix}wpbooklist_jre_post_options";
			$wpdb->wpbooklist_jre_list_dynamic_db_names      = "{$wpdb->prefix}wpbooklist_jre_list_dynamic_db_names";
			$wpdb->wpbooklist_jre_book_quotes                = "{$wpdb->prefix}wpbooklist_jre_book_quotes";
			$wpdb->wpbooklist_jre_purchase_stylepaks         = "{$wpdb->prefix}wpbooklist_jre_purchase_stylepaks";
			$wpdb->wpbooklist_jre_color_options              = "{$wpdb->prefix}wpbooklist_jre_color_options";
			$wpdb->wpbooklist_jre_active_extensions          = "{$wpdb->prefix}wpbooklist_jre_active_extensions";
			$wpdb->wpbooklist_jre_storytime_stories          = "{$wpdb->prefix}wpbooklist_jre_storytime_stories";
			$wpdb->wpbooklist_jre_storytime_stories_settings = "{$wpdb->prefix}wpbooklist_jre_storytime_stories_settings";
			$wpdb->wpbooklist_jre_users_table                = "{$wpdb->prefix}wpbooklist_jre_users_table";
		}

		/**
		 *  Runs once upon plugin activation and records the user's url.
		 */
		public function wpbooklist_jre_record_user_url() {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			global $wpdb;
			global $charset_collate;

			$url      = home_url();
			$plugin   = 'WPBookList';
			$date     = time();
			$postdata = http_build_query(
				array(
					'url'    => $url,
					'plugin' => $plugin,
					'date'   => $date,
				)
			);

			$opts = array(
				'http' =>
					array(
						'method'  => 'POST',
						'header'  => 'Content-type: application/x-www-form-urlencoded',
						'content' => $postdata,
					),
			);

			$context      = stream_context_create( $opts );
			$result       = '';
			$responsecode = '';
			if ( function_exists( 'file_get_contents' ) ) {
				wp_remote_get( 'https://jakerevans.com/pmfileforrecord.php', false, $context );
			} else {
				if ( function_exists( 'curl_init' ) ) {
					$ch = curl_init();
					curl_setopt( $ch, CURLOPT_HEADER, 0 );
					curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
					curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
					curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
					curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
					$url = 'https://jakerevans.com/pmfileforrecord.php';
					curl_setopt( $ch, CURLOPT_URL, $url );

					$data = array(
						'url'    => $url,
						'plugin' => $plugin,
						'date'   => $date,
					);

					curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $data ) );

					$result = curl_exec( $ch );
					$responsecode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
					curl_close( $ch );
				}
			}
		}

		/**
		 *  Runs once upon plugin activation and creates default table
		 */
		public function wpbooklist_jre_create_default_lib() {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			global $wpdb;
			global $charset_collate;

			// Call this manually as we may have missed the init hook.
			$this->wpbooklist_jre_register_table_name();

			$default_table     = $wpdb->prefix . 'wpbooklist_jre_saved_book_log';
			$sql_create_table1 = "CREATE TABLE {$wpdb->wpbooklist_jre_saved_book_log} 
			(
				ID bigint(190) auto_increment,
				additionalimage1 TEXT,
				additionalimage2 TEXT,
				amazon_detail_page TEXT,
				appleibookslink TEXT,
				asin TEXT,
				author TEXT,
				author2 TEXT,
				author3 TEXT,
				author_url TEXT,
				sale_url TEXT,
				authorfirst TEXT,
				authorfirst2 TEXT,
				authorfirst3 TEXT,
				authorlast TEXT,
				authorlast2 TEXT,
				authorlast3 TEXT,
				backcover TEXT,
				bam_link TEXT,
				bn_link TEXT,
				book_uid TEXT,
				callnumber TEXT,
				category TEXT,
				copies bigint(255),
				copieschecked bigint(255),
				country TEXT,
				currentlendemail TEXT,
				currentlendname TEXT,
				date_finished TEXT,
				description MEDIUMTEXT,
				ebook TEXT,
				edition TEXT,
				finished TEXT,
				first_edition TEXT,
				format TEXT,
				genres TEXT,
				goodreadslink TEXT,
				google_preview TEXT,
				illustrator TEXT,
				image TEXT,
				isbn varchar(190),
				isbn13 TEXT,
				itunes_page TEXT,
				keywords MEDIUMTEXT,
				kobo_link TEXT,
				language TEXT,
				lendable TEXT,
				lendedon bigint(255),
				lendstatus TEXT,
				notes MEDIUMTEXT,
				numberinseries TEXT,
				originalpubyear bigint(255),
				originaltitle TEXT,
				othereditions MEDIUMTEXT,
				outofprint TEXT,
				page_yes TEXT,
				pages bigint(255),
				post_yes TEXT,
				price TEXT,
				pub_year bigint(255),
				publisher TEXT,
				rating float,
				review_iframe TEXT,
				series TEXT,
				shortdescription MEDIUMTEXT, 
				signed TEXT,
				similar_books MEDIUMTEXT,
				similar_products MEDIUMTEXT,
				similarbooks MEDIUMTEXT,
				subgenre TEXT,
				subject TEXT,
				title TEXT,
				woocommerce TEXT,
				PRIMARY KEY  (ID),
				KEY isbn (isbn)
			) $charset_collate; ";
			dbDelta( $sql_create_table1 );
		}

		/**
		 *  Runs once upon plugin activation and creates the master display options table.
		 */
		public function wpbooklist_jre_create_superadmin_display_options_table() {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			global $wpdb;
			global $charset_collate;

			// Call this manually as we may have missed the init hook.
			$this->wpbooklist_jre_register_table_name();

			$sql_create_table2 = "CREATE TABLE {$wpdb->wpbooklist_jre_user_options} 
			(
				ID bigint(190) auto_increment,
				username varchar(190),
				version varchar(255) NOT NULL DEFAULT '3.3',
				amazonaff varchar(255) NOT NULL DEFAULT 'wpbooklisti0e-21',
				amazonauth varchar(255),
				itunesaff varchar(255) NOT NULL DEFAULT '1010lnPx',
				enablepurchase bigint(255),
				amazonapipublic varchar(255),
				amazonapisecret varchar(255),
				googleapi varchar(255),
				appleapi varchar(255),
				openlibraryapi varchar(255),
				hidestats bigint(255),
				hidesortby bigint(255),
				hidesearch bigint(255),
				hidefilter bigint(255),
				hidebooktitle bigint(255),
				hidebookimage bigint(255),
				hidefinished bigint(255),
				hidelibrarytitle bigint(255),
				hideauthor bigint(255),
				hidecategory bigint(255),
				hidepages bigint(255),
				hidebookpage bigint(255),
				hidebookpost bigint(255),
				hidepublisher bigint(255),
				hidepubdate bigint(255),
				hidesigned bigint(255),
				hidesubject bigint(255),
				hidecountry bigint(255),
				hidefirstedition bigint(255),
				hidefinishedsort bigint(255),
				hidesignedsort bigint(255),
				hidefirstsort bigint(255),
				hidesubjectsort bigint(255),
				hidefacebook bigint(255),
				hidemessenger bigint(255),
				hidetwitter bigint(255),
				hidegoogleplus bigint(255),
				hidepinterest bigint(255),
				hideemail bigint(255),
				hidefrontendbuyimg bigint(255),
				hidefrontendbuyprice bigint(255),
				hidecolorboxbuyimg bigint(255),
				hidecolorboxbuyprice bigint(255),
				hidegoodreadswidget bigint(255),
				hidedescription bigint(255),
				hidesimilar bigint(255),
				hideamazonreview bigint(255),
				hidenotes bigint(255),
				hidebottompurchase bigint(255),
				hidegooglepurchase bigint(255),
				hidefeaturedtitles bigint(255),
				hidebnpurchase bigint(255),
				hideitunespurchase bigint(255),
				hideamazonpurchase bigint(255),
				hiderating bigint(255),
				hideratingbook bigint(255),
				hidequote bigint(255),
				hidequotebook bigint(255),
				sortoption varchar(255),
				booksonpage bigint(255) NOT NULL DEFAULT 12,
				amazoncountryinfo varchar(255) NOT NULL DEFAULT 'US',
				stylepak varchar(255) NOT NULL DEFAULT 'Default',
				admindismiss bigint(255) NOT NULL DEFAULT 1,
				activeposttemplate varchar(255),
				activepagetemplate varchar(255),
				hidekindleprev bigint(255),
				hidegoogleprev bigint(255),
				hidebampurchase bigint(255),
				hidekobopurchase bigint(255),
				hideasin bigint(255),
				hidegenres bigint(255),
				hideisbn10 bigint(255),
				hideisbn13 bigint(255),
				hidekeywords bigint(255),
				hideothereditions bigint(255),
				hideoutofprint bigint(255),
		        hidecallnumber bigint(255),
		        hideformat bigint(255),
		        hideillustrator bigint(255),
		        hidelanguage bigint(255),
		        hidenumberinseries bigint(255),
		        hideorigpubyear bigint(255),
		        hideorigtitle bigint(255),
		        hideseries bigint(255),
		        hideshortdesc bigint(255),
		        hidesubgenre bigint(255),
				patreonaccess varchar(255),
				patreonrefresh varchar(255),
				patreonack varchar(255),
				extensionversions MEDIUMTEXT,
				adminmessage varchar(10000) NOT NULL DEFAULT '" . ADMIN_MESSAGE . "',
				customfields MEDIUMTEXT,
				hideadditionalimgs bigint(255),
				PRIMARY KEY  (ID),
				KEY username (username)
			) $charset_collate; ";

			// If table doesn't exist, create table and add initial data to it.
			$test_name = $wpdb->prefix . 'wpbooklist_jre_user_options';
			if ( $test_name !== $wpdb->get_var( "SHOW TABLES LIKE '$test_name'" ) ) {
				dbDelta( $sql_create_table2 );
				$table_name = $wpdb->prefix . 'wpbooklist_jre_user_options';
				$wpdb->insert( $table_name, array( 'ID' => 1 ) );
			}
		}


		/**
		 *  Runs once upon plugin activation and creates the master page display options table.
		 */
		public function wpbooklist_jre_create_superadmin_page_options_table() {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			global $wpdb;
			global $charset_collate;

			// Call this manually as we may have missed the init hook.
			$this->wpbooklist_jre_register_table_name();

			$sql_create_table3 = "CREATE TABLE {$wpdb->wpbooklist_jre_page_options} 
			(
				ID bigint(190) auto_increment,
				username varchar(190),
				amazonaff varchar(255) NOT NULL DEFAULT 'wpbooklisti0e-21',
				amazonauth varchar(255),
				barnesaff varchar(255),
				itunesaff varchar(255) NOT NULL DEFAULT '1010lnPx',
				enablepurchase bigint(255),
				hidetitle bigint(255),
				hidebooktitle bigint(255),
				hidebookimage bigint(255),
				hidefinished bigint(255),
				hideauthor bigint(255),
				hidefrontendbuyimg bigint(255),
				hidefrontendbuyprice bigint(255),
				hidecolorboxbuyimg bigint(255),
				hidecolorboxbuyprice bigint(255),
				hidecategory bigint(255),
				hidepages bigint(255),
				hidepublisher bigint(255),
				hidepubdate bigint(255),
				hidesigned bigint(255),
				hidesubject bigint(255),
				hidecountry bigint(255),
				hidefirstedition bigint(255),
				hidefinishedsort bigint(255),
				hidesignedsort bigint(255),
				hidefirstsort bigint(255),
				hidesubjectsort bigint(255),
				hidefacebook bigint(255),
				hidemessenger bigint(255),
				hidetwitter bigint(255),
				hidegoogleplus bigint(255),
				hidepinterest bigint(255),
				hideemail bigint(255),
				hidedescription bigint(255),
				hidesimilar bigint(255),
				hideamazonreview bigint(255),
				hidenotes bigint(255),
				hidegooglepurchase bigint(255),
				hidefeaturedtitles bigint(255),
				hidebnpurchase bigint(255),
				hideitunespurchase bigint(255),
				hideamazonpurchase bigint(255),
				hiderating bigint(255),
				hidequote bigint(255),
				hidekindleprev bigint(255),
				hidegoogleprev bigint(255),
				hidebampurchase bigint(255),
				hidekobopurchase bigint(255),
				amazoncountryinfo varchar(255) NOT NULL DEFAULT 'US',
				stylepak varchar(255) NOT NULL DEFAULT 'Default',
				PRIMARY KEY  (ID),
				KEY username (username)
			) $charset_collate; ";

			// If table doesn't exist, create table and add initial data to it.
			$test_name = $wpdb->prefix . 'wpbooklist_jre_page_options';
			if ( $test_name !== $wpdb->get_var( "SHOW TABLES LIKE '$test_name'" ) ) {
				dbDelta( $sql_create_table3 );
				$table_name = $wpdb->prefix . 'wpbooklist_jre_page_options';
				$wpdb->insert( $table_name, array( 'ID' => 1 ) );
			}
		}

		/**
		 *  Runs once upon plugin activation and creates the master post display options table.
		 */
		public function wpbooklist_jre_create_superadmin_post_options_table() {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			global $wpdb;
			global $charset_collate;

			// Call this manually as we may have missed the init hook.
			$this->wpbooklist_jre_register_table_name();

			$sql_create_table4 = "CREATE TABLE {$wpdb->wpbooklist_jre_post_options} 
			(
				ID bigint(190) auto_increment,
				username varchar(190),
				amazonaff varchar(255) NOT NULL DEFAULT 'wpbooklisti0e-21',
				amazonauth varchar(255),
				barnesaff varchar(255),
				itunesaff varchar(255) NOT NULL DEFAULT '1010lnPx',
				enablepurchase bigint(255),
				hidetitle bigint(255),
				hidebooktitle bigint(255),
				hidebookimage bigint(255),
				hidefinished bigint(255),
				hideauthor bigint(255),
				hidefrontendbuyimg bigint(255),
				hidefrontendbuyprice bigint(255),
				hidecolorboxbuyimg bigint(255),
				hidecolorboxbuyprice bigint(255),
				hidecategory bigint(255),
				hidepages bigint(255),
				hidepublisher bigint(255),
				hidepubdate bigint(255),
				hidesigned bigint(255),
				hidesubject bigint(255),
				hidecountry bigint(255),
				hidefirstedition bigint(255),
				hidefacebook bigint(255),
				hidemessenger bigint(255),
				hidetwitter bigint(255),
				hidegoogleplus bigint(255),
				hidepinterest bigint(255),
				hideemail bigint(255),
				hidedescription bigint(255),
				hidesimilar bigint(255),
				hideamazonreview bigint(255),
				hidenotes bigint(255),
				hidegooglepurchase bigint(255),
				hidefeaturedtitles bigint(255),
				hidebnpurchase bigint(255),
				hideitunespurchase bigint(255),
				hideamazonpurchase bigint(255),
				hiderating bigint(255),
				hidequote bigint(255),
				hidekindleprev bigint(255),
				hidegoogleprev bigint(255),
				hidebampurchase bigint(255),
				hidekobopurchase bigint(255),
				amazoncountryinfo varchar(255) NOT NULL DEFAULT 'US',
				stylepak varchar(255) NOT NULL DEFAULT 'Default',
				PRIMARY KEY  (ID),
				KEY username (username)
			) $charset_collate; ";

			// If table doesn't exist, create table and add initial data to it.
			$test_name = $wpdb->prefix . 'wpbooklist_jre_post_options';
			if ( $test_name !== $wpdb->get_var( "SHOW TABLES LIKE '$test_name'" ) ) {
				dbDelta( $sql_create_table4 );
				$table_name = $wpdb->prefix . 'wpbooklist_jre_post_options';
				$wpdb->insert( $table_name, array( 'ID' => 1 ) );
			}
		}

		/**
		 *  Runs once upon plugin activation and creates the table the records info about the user-created libraries.
		 */
		public function wpbooklist_jre_record_dynamic_tablenames_table() {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			global $wpdb;
			global $charset_collate;

			// Call this manually as we may have missed the init hook.
			$this->wpbooklist_jre_register_table_name();

			$sql_create_table5 = "CREATE TABLE {$wpdb->wpbooklist_jre_list_dynamic_db_names} 
			(
				ID bigint(190) auto_increment,
				stylepak varchar(190),
				user_table_name varchar(255) NOT NULL,
				PRIMARY KEY  (ID),
				KEY stylepak (stylepak)
			) $charset_collate; ";
			dbDelta( $sql_create_table5 );
		}

		/**
		 *  Runs once upon plugin activation and creates the table that holds the book quotes.
		 */
		public function wpbooklist_jre_create_book_quotes_table() {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			global $wpdb;
			global $charset_collate;

			// Call this manually as we may have missed the init hook.
			$this->wpbooklist_jre_register_table_name();

			$sql_create_table6 = "CREATE TABLE {$wpdb->wpbooklist_jre_book_quotes} 
			(
				ID bigint(190) auto_increment,
				placement varchar(190),
				quote varchar(255),
				PRIMARY KEY  (ID),
				KEY placement (placement)
			) $charset_collate; ";
			dbDelta( $sql_create_table6 );

			// Get the default quotes for adding to database.
			$response = wp_remote_get( esc_url_raw( QUOTES_URL . 'defaultquotes.txt' ) );

			// Check the response code.
			$response_code    = wp_remote_retrieve_response_code( $response );
			$response_message = wp_remote_retrieve_response_message( $response );

			if ( 200 !== $response_code && ! empty( $response_message ) ) {
				return new WP_Error( $response_code, $response_message );
			} elseif ( 200 !== $response_code ) {
				return new WP_Error( $response_code, 'Unknown error occurred' );
			} else {
				$response = wp_remote_retrieve_body( $response );
			}

			$quote_array = explode( ';', $response );
			$table_name  = $wpdb->prefix . 'wpbooklist_jre_book_quotes';
			foreach ( $quote_array as $quote ) {

				if ( strlen( $quote ) > 100 ) {
					$placement = 'ui';
				} else {
					$placement = 'book';
				}

				if ( strlen( $quote ) > 1 ) {
					$wpdb->insert( $table_name,
						array(
							'quote'     => $quote,
							'placement' => $placement,
						)
					);
				}
			}
		}

		/**
		 *  Runs once upon plugin activation and creates the table that holds info on WPBookList Pages & Posts.
		 */
		public function wpbooklist_jre_create_page_post_log_table() {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			global $wpdb;
			global $charset_collate;

			// Call this manually as we may have missed the init hook.
			$this->wpbooklist_jre_register_table_name();

			$sql_create_table7 = "CREATE TABLE {$wpdb->wpbooklist_jre_saved_page_post_log} 
			(
				ID bigint(190) auto_increment,
				book_uid varchar(190),
				book_title varchar(255),
				post_id bigint(255),
				type varchar(255),
				post_url varchar(255),
				author bigint(255),
				active_template varchar(255),
				PRIMARY KEY  (ID),
				KEY book_uid (book_uid)
			) $charset_collate; ";
			dbDelta( $sql_create_table7 );

		}

		/**
		 *  Runs once upon plugin activation and creates the table that holds info on WPBookList Pages & Posts.
		 */
		public function wpbooklist_jre_create_featured_books_table() {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			global $wpdb;
			global $charset_collate;

			// Call this manually as we may have missed the init hook.
			$this->wpbooklist_jre_register_table_name();

			// Creating the table.
			$sql_create_table8 = "CREATE TABLE {$wpdb->wpbooklist_jre_saved_books_for_featured} 
			(
				ID bigint(190) auto_increment,
				book_title varchar(190),
				isbn varchar(255),
				subject varchar(255),
				country varchar(255),
				author varchar(255),
				authorurl varchar(255),
				purchaseprice varchar(255),
				currentdate varchar(255),
				finishedyes varchar(255),
				finishedno varchar(255),
				booksignedyes varchar(255),
				booksignedno varchar(255),
				firsteditionyes varchar(255),
				firsteditionno varchar(255),
				yearfinished bigint(255),
				coverimage varchar(255),
				pagenum bigint(255),
				pubdate bigint(255),
				publisher varchar(255),
				weight bigint(255),
				category varchar(255),
				description MEDIUMTEXT, 
				notes MEDIUMTEXT,
				itunespage varchar(255),
				googlepreview varchar(255),
				amazondetailpage varchar(255),
				bookrating bigint(255),
				reviewiframe varchar(255),
				similarproducts MEDIUMTEXT,
				PRIMARY KEY  (ID),
				KEY book_title (book_title)
			) $charset_collate; ";
			dbDelta( $sql_create_table8 );
		}


		/**
		 *  Runs once upon plugin activation and creates the table that holds the actual Storytime Stories.
		 */
		public function wpbooklist_jre_create_storytime_stories_table() {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			global $wpdb;
			global $charset_collate;

			// Call this manually as we may have missed the init hook.
			$this->wpbooklist_jre_register_table_name();

			$sql_create_table10 = "CREATE TABLE {$wpdb->wpbooklist_jre_storytime_stories} 
			(
				ID bigint(190) auto_increment,
				providername varchar(190),
				providerimg varchar(255),
				providerbio MEDIUMTEXT,
				content LONGTEXT,
				title varchar(255),
				category varchar(255),
				pageid bigint(255),
				postid bigint(255),
				storydate bigint(255),
				PRIMARY KEY  (ID),
				KEY providername (providername)
			) $charset_collate; ";
			dbDelta( $sql_create_table10 );

			// Call the class that will insert default Storytime data into the table we just created. Seperate file simply because of length of content.
			require_once CLASS_STORYTIME_DIR . 'class-wpbooklist-storytime.php';
			$storytime_class = new WPBookList_Storytime( 'install' );

		}

		/**
		 *  Runs once upon plugin activation and creates the table that holds info on WPBookList Pages & Posts.
		 */
		public function wpbooklist_jre_create_storytime_stories_settings_table() {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			global $wpdb;
			global $charset_collate;

			// Call this manually as we may have missed the init hook.
			$this->wpbooklist_jre_register_table_name();

			$sql_create_table11 = "CREATE TABLE {$wpdb->wpbooklist_jre_storytime_stories_settings} 
			(
				ID bigint(190) auto_increment,
				getstories bigint(255),
				createpost bigint(255),
				createpage bigint(255),
				storypersist bigint(255),
				deletedefault bigint(255),
				notifydismiss bigint(255) NOT NULL DEFAULT 1,
				newnotify bigint(255) NOT NULL DEFAULT 1,
				notifymessage MEDIUMTEXT,
				storytimestylepak varchar(255) NOT NULL DEFAULT 'default',
				PRIMARY KEY  (ID),
				KEY getstories (getstories)
			) $charset_collate; ";
			dbDelta( $sql_create_table11 );

			$table_name = $wpdb->prefix . 'wpbooklist_jre_storytime_stories_settings';

			$wpdb->insert( $table_name,
				array(
					'notifydismiss'     => 1,
					'newnotify'         => 1,
					'storytimestylepak' => 'default',
				)
			);

		}

		/**
		 *  Runs once upon plugin activation and creates the Users tables
		 */
		public function wpbooklist_add_user_table() {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			global $wpdb;
			global $charset_collate;

			// Call this manually as we may have missed the init hook.
			$this->wpbooklist_jre_register_table_name();

			// If table doesn't exist, create table.
			$test_name = $wpdb->prefix . 'wpbooklist_jre_users_table';
			if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $test_name ) ) !== $test_name ) {

				// This is the table that holds static data about users - things like username, password, height, gender...
				$sql_create_table = "CREATE TABLE {$wpdb->wpbooklist_jre_users_table} 
					(
			            ID smallint(190) auto_increment,
			            firstname varchar(190),
			            lastname varchar(255),
			            datecreated varchar(255),
			            wpuserid bigint(255),
			            email varchar(255),
			            username varchar(255),
			            role varchar(255),
			            permissions varchar(255),
			            country varchar(255),
			            streetaddress1 varchar(255),
						streetaddress2 varchar(255),
						city varchar(255),
						state varchar(255),
						zip varchar(255),
						phone varchar(255),
			            profileimage varchar(255),
			            height varchar(255),
			            age varchar(255),
			            birthday varchar(255),
			            gender varchar(255),
			            bio MEDIUMTEXT,
						website varchar(255),
						facebook varchar(255),
						twitter varchar(255),
						instagram varchar(255),
						snapchat varchar(255),
						libraries varchar(255),
			            PRIMARY KEY  (ID),
			              KEY firstname (firstname)
     				) $charset_collate; ";

				// If table doesn't exist, create table and add initial data to it.
				$test_name = $wpdb->prefix . 'wpbooklist_jre_users_table';
				if ( $test_name !== $wpdb->get_var( "SHOW TABLES LIKE '$test_name'" ) ) {
					$db_delta_result = dbDelta( $sql_create_table );
					$table_name      = $wpdb->prefix . 'wpbooklist_jre_users_table';
					$current_user    = wp_get_current_user();
					if ( ! ( $current_user instanceof WP_User ) ) {
						return;
					}

					// Create the permissions string.
					$permissions = 'Yes-Yes-Yes-Yes-Yes';

					$users_save_array = array(
						'firstname'   => $current_user->user_firstname,
						'lastname'    => $current_user->user_lastname,
						'email'       => $current_user->user_email,
						'username'    => $current_user->user_login,
						'permissions' => $permissions,
						'wpuserid'    => $current_user->ID,
						'datecreated' => $this->date,
						'libraries'   => 'alllibraries',
						'role'        => 'SuperAdmin',
					);

					// Requiring & Calling the file/class that will insert or update our data.
					require_once CLASS_USERS_DIR . 'class-wpbooklist-save-users-data.php';
					$save_class      = new WPBOOKLIST_Save_Users_Data( $users_save_array );
					$db_write_result = $save_class->wpbooklist_jre_save_users_actual();

				}
				$key = $wpdb->prefix . 'wpbooklist_jre_users_table';
				return $db_delta_result[ $key ];
			} else {
				return 'Table already exists';
			}

		}

		/**
		 * Adding the admin css file
		 */
		public function wpbooklist_jre_admin_style() {

			wp_register_style( 'adminui', ROOT_CSS_URL . 'wpbooklist-main-admin.css', null, WPBOOKLIST_VERSION_NUM );
			wp_enqueue_style( 'adminui' );

		}



		/**
		 * Adding the front-end library ui css file or StylePak
		 */
		public function wpbooklist_jre_frontend_library_ui_default_style() {
			global $wpdb;
			$id      = get_the_ID();
			$post    = get_post( $id );
			$content = '';
			if ( $post ) {
				$content = $post->post_content;
			}
			$stylepak = '';

			$table_name2 = $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names';
			$db_row = $wpdb->get_results( "SELECT * FROM $table_name2" );
			foreach ( $db_row as $table ) {
				$shortcode = 'wpbooklist_shortcode table="' . $table->user_table_name . '"';

				if ( stripos( $content, $shortcode ) !== false ) {
					$stylepak = $table->stylepak;
				}
			}

			if ( '' === $stylepak || null === $stylepak ) {
				if ( stripos( $content, '[wpbooklist_shortcode' ) !== false ) {
					$table_name2 = $wpdb->prefix . 'wpbooklist_jre_user_options';
					$row         = $wpdb->get_results( "SELECT * FROM $table_name2" );
					$stylepak    = $row[0]->stylepak;
				}
			}

			if ( '' === $stylepak || null === $stylepak || 'Default' === $stylepak ) {
				$stylepak = 'default';
			}

			if ( 'default' === $stylepak || 'Default StylePak' === $stylepak ) {

				$id      = get_the_ID();
				$post    = get_post( $id );
				$content = '';
				if ( $post ) {
					$content = $post->post_content;
				}

				// If we find any of these in $content, load the frontend-library-ui.css.
				$shortcode_array = array(
					'showbookcover',
					'wpbooklist_shortcode',
					'wpbooklist_bookfinder',
					'wpbooklist_carousel',
					'wpbooklist_categories',
					'wpbooklist',
				);

				// Checking for WPBookList content on page.
				foreach ( $shortcode_array as $key => $value ) {
					if ( stripos( $content, $value ) !== false ) {
						wp_register_style( 'frontendlibraryui', ROOT_CSS_URL . 'wpbooklist-main-frontend.css', null, WPBOOKLIST_VERSION_NUM );
						wp_enqueue_style( 'frontendlibraryui' );
					}
				}

				// If we're on the homepage or the blog page, just go ahead and load.
				if ( ! wp_script_is( 'frontendlibraryui' ) ) {
					if ( is_front_page() || is_home() ) {
						wp_register_style( 'frontendlibraryui', ROOT_CSS_URL . 'wpbooklist-main-frontend.css', null, WPBOOKLIST_VERSION_NUM );
						wp_enqueue_style( 'frontendlibraryui' );
					}
				}
			}

			$library_stylepaks_upload_dir = LIBRARY_STYLEPAKS_UPLOAD_URL;

			// Modify the 'LIBRARY_STYLEPAKS_UPLOAD_URL' to make sure we're using the right protocol, as it seems that wp_upload_dir() doesn't return https - introduced in 5.5.2.
			$protocol = ( array_key_exists( 'HTTPS', $_SERVER ) && ! empty( $_SERVER['HTTPS'] ) && 'off' !== filter_var( wp_unslash( $_SERVER['HTTPS'] ), FILTER_SANITIZE_STRING ) ) || ( isset( $_SERVER['SERVER_PORT'] ) && 443 === filter_var( wp_unslash( $_SERVER['SERVER_PORT'] ), FILTER_SANITIZE_NUMBER_INT ) ) ? 'https://' : 'http://';

			if ( 'https://' === $protocol || 'https' === $protocol ) {
				if ( strpos( LIBRARY_STYLEPAKS_UPLOAD_URL, 'http://' ) !== false ) {
					$library_stylepaks_upload_dir = str_replace( 'http://', 'https://', LIBRARY_STYLEPAKS_UPLOAD_URL );
				}
			}

			if ( 'StylePak1' === $stylepak ) {
				wp_register_style( 'StylePak1', $library_stylepaks_upload_dir . 'StylePak1.css', null, WPBOOKLIST_VERSION_NUM );
				wp_enqueue_style( 'StylePak1' );
			}

			if ( 'StylePak2' === $stylepak ) {
				wp_register_style( 'StylePak2', $library_stylepaks_upload_dir . 'StylePak2.css', null, WPBOOKLIST_VERSION_NUM );
				wp_enqueue_style( 'StylePak2' );
			}

			if ( 'StylePak3' === $stylepak ) {
				wp_register_style( 'StylePak3', $library_stylepaks_upload_dir . 'StylePak3.css', null, WPBOOKLIST_VERSION_NUM );
				wp_enqueue_style( 'StylePak3' );
			}

			if ( 'StylePak4' === $stylepak ) {
				wp_register_style( 'StylePak4', $library_stylepaks_upload_dir . 'StylePak4.css', null, WPBOOKLIST_VERSION_NUM );
				wp_enqueue_style( 'StylePak4' );
			}

			if ( 'StylePak5' === $stylepak ) {
				wp_register_style( 'StylePak5', $library_stylepaks_upload_dir . 'StylePak5.css', null, WPBOOKLIST_VERSION_NUM );
				wp_enqueue_style( 'StylePak5' );
			}

			if ( 'StylePak6' === $stylepak ) {
				wp_register_style( 'StylePak6', $library_stylepaks_upload_dir . 'StylePak6.css', null, WPBOOKLIST_VERSION_NUM );
				wp_enqueue_style( 'StylePak6' );
			}

			if ( 'StylePak7' === $stylepak ) {
				wp_register_style( 'StylePak7', $library_stylepaks_upload_dir . 'StylePak7.css', null, WPBOOKLIST_VERSION_NUM );
				wp_enqueue_style( 'StylePak7' );
			}

			if ( 'StylePak8' === $stylepak ) {
				wp_register_style( 'StylePak8', $library_stylepaks_upload_dir . 'StylePak8.css', null, WPBOOKLIST_VERSION_NUM );
				wp_enqueue_style( 'StylePak8' );
			}
		}

		/**
		 * Code for adding the default posts & pages CSS file
		 */
		public function wpbooklist_jre_posts_pages_default_style() {

			global $wpdb;
			$id       = get_the_ID();
			$stylepak = '';

			$table_name = $wpdb->prefix . 'wpbooklist_jre_saved_page_post_log';

			$row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE post_id = %d", $id ) );

			if ( null !== $row ) {
				if ( 'post' === $row->type ) {
					$table_name_post = $wpdb->prefix . 'wpbooklist_jre_post_options';
				} else {
					$table_name_post = $wpdb->prefix . 'wpbooklist_jre_page_options';
				}

				$row = $wpdb->get_row( "SELECT * FROM $table_name_post" );
				$stylepak = $row->stylepak;
			}

			if ( '' === $stylepak || null === $stylepak || 'Default StylePak' === $stylepak ) {
				$stylepak = 'default';
			}

			if ( 'Default' === $stylepak || 'default' === $stylepak || 'Default StylePak' === $stylepak ) {
				wp_register_style( 'postspagesdefaultcssforwpbooklist', ROOT_CSS_URL . 'wpbooklist-posts-pages-default.css', null, WPBOOKLIST_VERSION_NUM );
				wp_enqueue_style( 'postspagesdefaultcssforwpbooklist' );
			}

			if ( 'Post-StylePak1' === $stylepak ) {
				wp_register_style( 'Post-StylePak1', POST_STYLEPAKS_UPLOAD_URL . 'Post-StylePak1.css', null, WPBOOKLIST_VERSION_NUM );
				wp_enqueue_style( 'Post-StylePak1' );
			}

			if ( 'Post-StylePak1' === $stylepak ) {
				wp_register_style( 'Post-StylePak2', POST_STYLEPAKS_UPLOAD_URL . 'Post-StylePak2.css', null, WPBOOKLIST_VERSION_NUM );
				wp_enqueue_style( 'Post-StylePak2' );
			}

			if ( 'Post-StylePak1' === $stylepak ) {
				wp_register_style( 'Post-StylePak3', POST_STYLEPAKS_UPLOAD_URL . 'Post-StylePak3.css', null, WPBOOKLIST_VERSION_NUM );
				wp_enqueue_style( 'Post-StylePak3' );
			}

			if ( 'Post-StylePak1' === $stylepak ) {
				wp_register_style( 'Post-StylePak4', POST_STYLEPAKS_UPLOAD_URL . 'Post-StylePak4.css', null, WPBOOKLIST_VERSION_NUM );
				wp_enqueue_style( 'Post-StylePak4' );
			}

			if ( 'Post-StylePak1' === $stylepak ) {
				wp_register_style( 'Post-StylePak5', POST_STYLEPAKS_UPLOAD_URL . 'Post-StylePak5.css', null, WPBOOKLIST_VERSION_NUM );
				wp_enqueue_style( 'Post-StylePak5' );
			}
		}

		/**
		 * Code for adding the top admin notice/advert
		 */
		public function wpbooklist_jre_admin_notice() {
			global $wpdb;
			$table_name  = $wpdb->prefix . 'wpbooklist_jre_user_options';
			$options_row = $wpdb->get_results( "SELECT * FROM $table_name" );
			$dismiss     = $options_row[0]->admindismiss;

			if ( '1' === $dismiss ) {
				$message    = $options_row[0]->adminmessage;
				$url        = home_url();
				$newmessage = str_replace( 'alaainqphpaholeechoaholehomeanusurlalparpascaholeainqara', $url, $message );
				$newmessage = str_replace( 'asq', "'", $newmessage );
				$newmessage = str_replace( 'hshmrk', '#', $newmessage );
				$newmessage = str_replace( 'ampersand', '&', $newmessage );
				$newmessage = str_replace( 'adq', '"', $newmessage );
				$newmessage = str_replace( 'aco', ':', $newmessage );
				$newmessage = str_replace( 'asc', ';', $newmessage );
				$newmessage = str_replace( 'aslash', '/', $newmessage );
				$newmessage = str_replace( 'ahole', ' ', $newmessage );
				$newmessage = str_replace( 'ara', '>', $newmessage );
				$newmessage = str_replace( 'ala', '<', $newmessage );
				$newmessage = str_replace( 'anem', '!', $newmessage );
				$newmessage = str_replace( 'dash', '-', $newmessage );
				$newmessage = str_replace( 'akomma', ', ', $newmessage );
				$newmessage = str_replace( 'anequal', '=', $newmessage );
				$newmessage = str_replace( 'dot', '.', $newmessage );
				$newmessage = str_replace( 'anus', '_', $newmessage );
				$newmessage = str_replace( 'adollar', '$', $newmessage );
				$newmessage = str_replace( 'ainq', '?', $newmessage );
				$newmessage = str_replace( 'alp', '( ', $newmessage );
				$newmessage = str_replace( 'arp', ')', $newmessage );
				?>
				<div class="notice notice-success is-dismissible">
					<p><?php echo $newmessage; ?></p>
				</div>
				<?php
			}
		}

		/** Function to allow users to specify which table they want displayed by passing as an argument in the shortcode
		 *
		 *  @param array $atts - The array that contains the shortcode attributes/arguments.
		 */
		public function wpbooklist_jre_plugin_dynamic_shortcode_function( $atts ) {
			global $wpdb;

			extract(
				shortcode_atts(
					array(
						'table'   => $wpdb->prefix . "wpbooklist_jre_saved_book_log",
						'action'  => 'colorbox',
						'display' => 'default',
						'fields'  => 'default',

					),
				$atts )
			);

			// Set up the table.
			if ( isset( $atts['table'] ) ) {
				$which_table = $wpdb->prefix . 'wpbooklist_jre_' . $table;
			} else {
				$which_table = $wpdb->prefix . 'wpbooklist_jre_saved_book_log';
			}

			// Set up the action taken when cover image is clicked on.
			if ( isset( $atts['action'] ) ) {
				$action = $atts['action'];
			} else {
				$action = 'colorbox';
			}

			// Set up the display variable that will determine which layout to use.
			if ( isset( $atts['display'] ) ) {
				$display = $atts['display'];
			} else {
				$display = 'default';
			}

			// Set up the fields variable that will determine which book details to display.
			if ( isset( $atts['fields'] ) ) {
				$fields = $atts['fields'];
			} else {
				$fields = 'default';
			}

			// Set up the action taken when cover image is clicked on.
			if ( isset( $atts['action'] ) ) {
				$action = $atts['action'];
			} else {
				$action = 'colorbox';
			}

			if ( null === $atts ) {
				$which_table = $wpdb->prefix . 'wpbooklist_jre_saved_book_log';
				$action      = 'colorbox';
			}

			$offset = 0;

			// If the 'Display' shortcode argument isn't set, display the deafult Library layout.
			if ( 'default' === $display ) {

				ob_start();
				include_once ROOT_INCLUDES_UI . 'class-wpbooklist-frontend-library-ui.php';
				$front_end_library_ui = new WPBookList_Frontend_Library_UI( $which_table, $action );
				return ob_get_clean();

			} else {

				if ( 'list' === $display ) {

					ob_start();
					include_once ROOT_INCLUDES_UI . 'class-wpbooklist-frontend-library-list-ui.php';
					$front_end_library_list_ui = new WPBookList_Frontend_Library_List_UI( $which_table, $action, $display, $fields );
					return ob_get_clean();

				}

				

			}

			
		}


		/** The function that determines which template to load for WPBookList Pages/Posts
		 *
		 *  @param string $content - Post/Page content.
		 */
		public function wpbooklist_set_page_post_template( $content ) {

			global $wpdb;

			require_once CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
			$trans = new WPBookList_Translations();

			$id            = get_the_id();
			$blog_url      = get_permalink( get_option( 'page_for_posts' ) );
			$actual_link   = ( isset( $_SERVER['HTTPS'] ) ? 'https' : 'http' ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			$table_name    = $wpdb->prefix . 'wpbooklist_jre_saved_page_post_log';
			$page_post_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE post_id = %d", $id ) );

			// If current page/post is a WPBookList Page or Post...
			if ( null !== $page_post_row ) {

				if ( 'page' === $page_post_row->type ) {
					$table_name       = $wpdb->prefix . 'wpbooklist_jre_page_options';
					$options_page_row = $wpdb->get_row( "SELECT * FROM $table_name" );
				}

				if ( 'post' === $page_post_row->type ) {
					$table_name       = $wpdb->prefix . 'wpbooklist_jre_post_options';
					$options_post_row = $wpdb->get_row( "SELECT * FROM $table_name" );
				}

				$options_table_name  = $wpdb->prefix . 'wpbooklist_jre_user_options';
				$options_row         = $wpdb->get_row( "SELECT * FROM $options_table_name" );
				$amazon_country_info = $options_row->amazoncountryinfo;
				$table_name          = $wpdb->prefix . 'wpbooklist_jre_saved_book_log';
				$book_row            = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE book_uid = %s", $page_post_row->book_uid ) );

				// If book wasn't found in default library, loop through and search custom libraries.
				if ( null === $book_row ) {
					$table_name = $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names';
					$db_row     = $wpdb->get_results( "SELECT * FROM $table_name" );

					foreach ( $db_row as $row ) {
						$table_name = $wpdb->prefix . 'wpbooklist_jre_' . $row->user_table_name;
						$book_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE book_uid = %s", $page_post_row->book_uid ) );
						if ( null === $book_row ) {
							continue;
						} else {
							break;
						}
					}
				}

				switch ( $amazon_country_info ) {
					case 'au':
						$book_row->amazon_detail_page = str_replace( '.com', '.com.au', $book_row->amazon_detail_page );
						$book_row->$review_iframe     = str_replace( '.com', '.com.au', $this->$review_iframe );
						break;
					case 'br':
						$book_row->amazon_detail_page = str_replace( '.com', '.com.br', $book_row->amazon_detail_page );
						$book_row->review_iframe      = str_replace( '.com', '.com.br', $book_row->review_iframe );
						break;
					case 'ca':
						$book_row->amazon_detail_page = str_replace( '.com', '.ca', $book_row->amazon_detail_page );
						$book_row->review_iframe      = str_replace( '.com', '.ca', $book_row->review_iframe );
						break;
					case 'cn':
						$book_row->amazon_detail_page = str_replace( '.com', '.cn', $book_row->amazon_detail_page );
						$book_row->review_iframe      = str_replace( '.com', '.cn', $book_row->review_iframe );
						break;
					case 'fr':
						$book_row->amazon_detail_page = str_replace( '.com', '.fr', $book_row->amazon_detail_page );
						$book_row->review_iframe      = str_replace( '.com', '.fr', $book_row->review_iframe );
						break;
					case 'de':
						$book_row->amazon_detail_page = str_replace( '.com', '.de', $book_row->amazon_detail_page );
						$book_row->review_iframe      = str_replace( '.com', '.de', $book_row->review_iframe );
						break;
					case 'in':
						$book_row->amazon_detail_page = str_replace( '.com', '.in', $book_row->amazon_detail_page );
						$book_row->review_iframe      = str_replace( '.com', '.in', $book_row->review_iframe );
						break;
					case 'it':
						$book_row->amazon_detail_page = str_replace( '.com', '.it', $book_row->amazon_detail_page );
						$book_row->review_iframe      = str_replace( '.com', '.it', $book_row->review_iframe );
						break;
					case 'jp':
						$book_row->amazon_detail_page = str_replace( '.com', '.co.jp', $book_row->amazon_detail_page );
						$book_row->review_iframe      = str_replace( '.com', '.co.jp', $book_row->review_iframe );
						break;
					case 'mx':
						$book_row->amazon_detail_page = str_replace( '.com', '.com.mx', $book_row->amazon_detail_page );
						$book_row->review_iframe      = str_replace( '.com', '.com.mx', $book_row->review_iframe );
						break;
					case 'nl':
						$book_row->amazon_detail_page = str_replace( '.com', '.nl', $book_row->amazon_detail_page );
						$book_row->review_iframe      = str_replace( '.com', '.nl', $book_row->review_iframe );
						break;
					case 'es':
						$book_row->amazon_detail_page = str_replace( '.com', '.es', $book_row->amazon_detail_page );
						$book_row->review_iframe      = str_replace( '.com', '.es', $book_row->review_iframe );
						break;
					case 'uk':
						$book_row->amazon_detail_page = str_replace( '.com', '.co.uk', $book_row->amazon_detail_page );
						$book_row->review_iframe      = str_replace( '.com', '.co.uk', $book_row->review_iframe );
						break;
					case 'sg':
						$this->amazon_detail_page = str_replace( '.com', '.com.sg', $this->amazon_detail_page );
						$this->review_iframe      = str_replace( '.com', '.com.sg', $this->review_iframe );
						break;
					default:

				}

				// Getting/creating quotes.
				$response = wp_remote_get( QUOTES_URL . 'defaultquotes.txt' );

				// Check the response code.
				$response_code    = wp_remote_retrieve_response_code( $response );
				$response_message = wp_remote_retrieve_response_message( $response );

				if ( 200 !== $response_code && ! empty( $response_message ) ) {
					return new WP_Error( $response_code, $response_message );
				} elseif ( 200 !== $response_code ) {
					return new WP_Error( $response_code, 'Unknown error occurred' );
				} else {
					$response = wp_remote_retrieve_body( $response );
				}

				$quotes_array  = explode( ';', $response );
				$quote         = $quotes_array[ array_rand( $quotes_array ) ];
				$quote_array_2 = explode( '-', $quote );

				if ( 2 === count( $quote_array_2 ) ) {
					$quote = '<span id="wpbooklist-quote-italic">' . $quote_array_2[0] . '</span> - <span id="wpbooklist-quote-bold">' . $quote_array_2[1] . '</span>';
				}

				// Getting Similar titles.
				if ( 'post' === $page_post_row->type ) {
					$similar_string = '<span id="wpbooklist-post-span-hidden" style="display:none;"></span>';
				}

				if ( 'page' === $page_post_row->type ) {
					$similar_string = '<span id="wpbooklist-page-span-hidden" style="display:none;"></span>';
				}

				$similarproductsarray   = explode( ';bsp;', $book_row->similar_products );
				$similarproductsarray   = array_unique( $similarproductsarray );
				$similar_products_array = array_values( $similarproductsarray );

				foreach ( $similar_products_array as $key => $prod ) {
					$arr  = explode( '---', $prod, 2 );
					$asin = $arr[0];

					// Removing my Affiliate ID, as it's only needed for initial API calls when Adding/Editing/Searching for books.
					if ( '' === $options_row->amazonaff || null === $options_row->amazonaff ) {
						$options_row->amazonaff = 'wpbooklisti0e-21';
					}

					$image = 'http://images.amazon.com/images/P/' . $asin . '.01.LZZZZZZZ.jpg';
					$url   = 'https://www.amazon.com/dp/' . $asin . '?tag=' . $options_row->amazonaff;
					if ( strlen( $image ) > 51 ) {
						if ( 'page' === $page_post_row->type ) {
							$similar_string = $similar_string . '<a class="wpbooklist-similar-link-post" target="_blank" href="' . $url . '"><img class="wpbooklist-similar-image-page" src="' . $image . '" /></a>';
						}

						if ( 'post' === $page_post_row->type ) {
							$similar_string = $similar_string . '<a class="wpbooklist-similar-link-post" target="_blank" href="' . $url . '"><img class="wpbooklist-similar-image-post" src="' . $image . '" /></a>';
						}
					}
				}

				$similar_string       = $similar_string . '</div>';
				$table_name_options   = $wpdb->prefix . 'wpbooklist_jre_user_options';
				$row                  = $wpdb->get_row( "SELECT * FROM $table_name_options" );
				$active_post_template = $row->activeposttemplate;
				$active_page_template = $row->activepagetemplate;

				// Double-check that Amazon review isn't expired.
				require_once CLASS_BOOK_DIR . 'class-wpbooklist-book.php';
				$book = new WPBookList_Book( $book_row->ID, $table_name );
				$book->refresh_amazon_review( $book_row->ID, $table_name );

				// Removing my Affiliate ID, as it's only needed for initial API calls when Adding/Editing/Searching for books.
				if ( false !== stripos( $book_row->amazon_detail_page, 'tag=wpbooklisti0e-21' ) ) {
					//$book_row->amazon_detail_page = str_replace( 'tag=wpbooklisti0e-21', '', $book_row->amazon_detail_page );
				}

				if ( 'page' === $page_post_row->type ) {

					switch ( $active_page_template ) {
						case 'Page-Template-1':
							include PAGE_TEMPLATES_UPLOAD_DIR . 'Page-Template-1.php';
							return $content . $string1 . $string2 . $string3 . $string4 . $string5 . $string6 . $string7 . $string8 . $string9 . $string10 . $string11 . $string12 . $string13 . $string14 . $string15 . $string16 . $string17 . $string18 . $string19 . $string20 . $string21 . $string22 . $string23 . $string24 . $string25 . $string26 . $string27 . $string28 . $string29 . $string30 . $string31 . $string32 . $string33 . $string34 . $string35 . $string36 . $string37 . $string38 . $string39 . $string40 . $string41 . $string42 . $string43 . $additional_images . $string44 . $string45 . $string46 . $string47;

						case 'Page-Template-2':
							include PAGE_TEMPLATES_UPLOAD_DIR . 'Page-Template-2.php';
							return $content . $string1 . $string2 . $string3 . $string4 . $string5 . $string6 . $string7 . $string8 . $string9 . $string10 . $string11 . $string12 . $string13 . $string14 . $string15 . $string16 . $string17 . $string18 . $string19 . $string20 . $string21 . $string22 . $string23 . $string24 . $string25 . $string26 . $string27 . $string28 . $string29 . $string30 . $string31 . $string32 . $string33 . $string34 . $string35 . $string36 . $string37 . $string38 . $string39 . $string40 . $string41 . $string42 . $string43 . $additional_images . $string44 . $string45 . $string46 . $string47;

						case 'Page-Template-3':
							include PAGE_TEMPLATES_UPLOAD_DIR . 'Page-Template-3.php';
							return $content . $string1 . $string2 . $string3 . $string4 . $string5 . $string6 . $string7 . $string8 . $string9 . $string10 . $string11 . $string12 . $string13 . $string14 . $string15 . $string16 . $string17 . $string18 . $string19 . $string20 . $string21 . $string22 . $string23 . $string24 . $string25 . $string26 . $string27 . $string28 . $string29 . $string30 . $string31 . $string32 . $string33 . $string34 . $string35 . $string36 . $string37 . $string38 . $string39 . $string40 . $string41 . $string42 . $string43 . $additional_images . $string44 . $string45 . $string46 . $string47;

						case 'Page-Template-4':
							include PAGE_TEMPLATES_UPLOAD_DIR . 'Page-Template-4.php';
							return $content . $string1 . $string2 . $string3 . $string4 . $string5 . $string6 . $string7 . $string8 . $string9 . $string10 . $string11 . $string12 . $string13 . $string14 . $string15 . $string16 . $string17 . $string18 . $string19 . $string20 . $string21 . $string22 . $string23 . $string24 . $string25 . $string26 . $string27 . $string28 . $string29 . $string30 . $string31 . $string32 . $string33 . $string34 . $string35 . $string36 . $string37 . $string38 . $string39 . $string40 . $string41 . $string42 . $string43 . $additional_images . $string44 . $string45 . $string46 . $string47;

						case 'Page-Template-5':
							include PAGE_TEMPLATES_UPLOAD_DIR . 'Page-Template-5.php';
							return $content . $string1 . $string2 . $string3 . $string4 . $string5 . $string6 . $string7 . $string8 . $string9 . $string10 . $string11 . $string12 . $string13 . $string14 . $string15 . $string16 . $string17 . $string18 . $string19 . $string20 . $string21 . $string22 . $string23 . $string24 . $string25 . $string26 . $string27 . $string28 . $string29 . $string30 . $string31 . $string32 . $string33 . $string34 . $string35 . $string36 . $string37 . $string38 . $string39 . $string40 . $string41 . $string42 . $string43 . $additional_images . $string44 . $string45 . $string46 . $string47;

						default:
							include PAGE_POST_TEMPLATES_DEFAULT_DIR . 'page-template-default.php';
							return $content . $string1 . $string2 . $string3 . $string4 . $string5 . $string6 . $string7 . $string8 . $string9 . $string10 . $string11 . $string12 . $string13 . $string14 . $string15 . $string16 . $string17 . $string18 . $string19 . $string20 . $string21 . $string22 . $string23 . $string50 . $string51 . $string24 . $string31 . $string32 . $string33 . $string25 . $string26 . $string27 . $string28 . $string29 . $string30 . $customfields_basic_string . $customfields_text_link_string . $customfields_dropdown_string . $customfields_image_link_string . $string34 . $string35 . $string36 . $string37 . $string38 . $string39 . $string40 . $string48 . $string49 . $string41 . $string42 . $string43 . $customfields_paragraph_string . $additional_images . $string44 . $comments_string . $string45 . $string46 . $string47;

					}
				}

				if ( 'post' === $page_post_row->type ) {

					switch ( $active_post_template ) {
						case 'Post-Template-1':
							include POST_TEMPLATES_UPLOAD_DIR . 'Post-Template-1.php';
							return $content . $string1 . $string2 . $string3 . $string4 . $string5 . $string6 . $string7 . $string8 . $string9 . $string10 . $string11 . $string12 . $string13 . $string14 . $string15 . $string16 . $string17 . $string18 . $string19 . $string20 . $string21 . $string22 . $string23 . $string24 . $string25 . $string26 . $string27 . $string28 . $string29 . $string30 . $string31 . $string32 . $string33 . $string34 . $string35 . $string36 . $string37 . $string38 . $string39 . $string40 . $string41 . $string42 . $string43 . $additional_images . $string44 . $string45 . $string46 . $string47;

						case 'Post-Template-2':
							include POST_TEMPLATES_UPLOAD_DIR . 'Post-Template-2.php';
							return $content . $string1 . $string2 . $string3 . $string4 . $string5 . $string6 . $string7 . $string8 . $string9 . $string10 . $string11 . $string12 . $string13 . $string14 . $string15 . $string16 . $string17 . $string18 . $string19 . $string20 . $string21 . $string22 . $string23 . $string24 . $string25 . $string26 . $string27 . $string28 . $string29 . $string30 . $string31 . $string32 . $string33 . $string34 . $string35 . $string36 . $string37 . $string38 . $string39 . $string40 . $string41 . $string42 . $string43 . $additional_images . $string44 . $string45 . $string46 . $string47;

						case 'Post-Template-3':
							include POST_TEMPLATES_UPLOAD_DIR . 'Post-Template-3.php';
							return $content . $string1 . $string2 . $string3 . $string4 . $string5 . $string6 . $string7 . $string8 . $string9 . $string10 . $string11 . $string12 . $string13 . $string14 . $string15 . $string16 . $string17 . $string18 . $string19 . $string20 . $string21 . $string22 . $string23 . $string24 . $string25 . $string26 . $string27 . $string28 . $string29 . $string30 . $string31 . $string32 . $string33 . $string34 . $string35 . $string36 . $string37 . $string38 . $string39 . $string40 . $string41 . $string42 . $string43 . $additional_images . $string44 . $string45 . $string46 . $string47;

						case 'Post-Template-4':
							include POST_TEMPLATES_UPLOAD_DIR . 'Post-Template-4.php';
							return $content . $string1 . $string2 . $string3 . $string4 . $string5 . $string6 . $string7 . $string8 . $string9 . $string10 . $string11 . $string12 . $string13 . $string14 . $string15 . $string16 . $string17 . $string18 . $string19 . $string20 . $string21 . $string22 . $string23 . $string24 . $string25 . $string26 . $string27 . $string28 . $string29 . $string30 . $string31 . $string32 . $string33 . $string34 . $string35 . $string36 . $string37 . $string38 . $string39 . $string40 . $string41 . $string42 . $string43 . $additional_images . $string44 . $string45 . $string46 . $string47;

						case 'Post-Template-5':
							include POST_TEMPLATES_UPLOAD_DIR . 'Post-Template-5.php';
							return $content . $string1 . $string2 . $string3 . $string4 . $string5 . $string6 . $string7 . $string8 . $string9 . $string10 . $string11 . $string12 . $string13 . $string14 . $string15 . $string16 . $string17 . $string18 . $string19 . $string20 . $string21 . $string22 . $string23 . $string24 . $string25 . $string26 . $string27 . $string28 . $string29 . $string30 . $string31 . $string32 . $string33 . $string34 . $string35 . $string36 . $string37 . $string38 . $string39 . $string40 . $string41 . $string42 . $string43 . $additional_images . $string44 . $string45 . $string46 . $string47;

						default:
							include PAGE_POST_TEMPLATES_DEFAULT_DIR . 'post-template-default.php';
							return $content . $string1 . $string2 . $string3 . $string4 . $string5 . $string6 . $string7 . $string8 . $string9 . $string10 . $string11 . $string12 . $string13 . $string14 . $string15 . $string16 . $string17 . $string18 . $string19 . $string20 . $string21 . $string22 . $string23 . $string50 . $string51 . $string24 . $string31 . $string32 . $string33 . $string25 . $string26 . $string27  . $string28 . $string29 . $string30 . $customfields_basic_string . $customfields_text_link_string . $customfields_dropdown_string . $customfields_image_link_string . $string34 . $string35 . $string36 . $string37 . $string38 . $string39 . $string40 . $string48 . $string49 . $string41 . $string42 . $string43 . $customfields_paragraph_string . $additional_images . $string44 . $comments_string . $string45 . $string46 . $string47;

					}
				}

				switch ( $page_post_row->active_template ) {
					case 'template1':
						if ( 'page' === $page_post_row->type ) {
							include PAGE_TEMPLATES_UPLOAD_DIR . 'page-template-1.php';
						} else {
							include POST_TEMPLATES_UPLOAD_DIR . 'post-template-1.php';
						}
						break;
					case 'template2':
						if ( 'page' === $page_post_row->type ) {
							include PAGE_TEMPLATES_UPLOAD_DIR . 'page-template-2.php';
						} else {
							include POST_TEMPLATES_UPLOAD_DIR . 'post-template-2.php';
						}
						break;
					case 'default':
						if ( 'page' === $page_post_row->type ) {
							include PAGE_TEMPLATES_DEFAULT_DIR . 'page-template-default.php';

							// Double-check that Amazon review isn't expired.
							require_once CLASS_BOOK_DIR . 'class-wpbooklist-book.php';
							$book = new WPBookList_Book( $book_row->ID, $table_name );
							$book->refresh_amazon_review( $book_row->ID, $table_name );

							return $content . $string1 . $string2 . $string3 . $string4 . $string5 . $string6 . $string7 . $string8 . $string9 . $string10 . $string11 . $string12 . $string13 . $string14 . $string15 . $string16 . $string17 . $string18 . $string19 . $string20 . $string21 . $string22 . $string23 . $string24 . $string25 . $string26 . $string27 . $string28 . $string29 . $string30 . $string31 . $string32 . $string33 . $string34 . $string35 . $string36 . $string37 . $string38 . $string39 . $string40 . $string41 . $string42 . $string43 . $additional_images . $string44 . $string45 . $string46 . $string47;

						} else {

							include POST_TEMPLATES_DEFAULT_DIR . 'post-template-default.php';

							// Double-check that Amazon review isn't expired.
							require_once CLASS_BOOK_DIR . 'class-wpbooklist-book.php';
							$book = new WPBookList_Book( $book_row->ID, $table_name );
							$book->refresh_amazon_review( $book_row->ID, $table_name );

							return $content . $string1 . $string2 . $string3 . $string4 . $string5 . $string6 . $string7 . $string8 . $string9 . $string10 . $string11 . $string12 . $string13 . $string14 . $string15 . $string16 . $string17 . $string18 . $string19 . $string20 . $string21 . $string22 . $string23 . $string24 . $string25 . $string26 . $string27 . $string28 . $string29 . $string30 . $string31 . $string32 . $string33 . $string34 . $string35 . $string36 . $string37 . $string38 . $string39 . $string40 . $string41 . $string42 . $string43 . $additional_images . $string44 . $string45 . $string46 . $string47;
						}
						break;
					default:
						break;
				}
			}

			// Making double-sure content gets returned.
			return $content;
		}

		/** Shortcode function for displaying book cover image/link
		 *
		 *  @param array $atts - The array that contains the shortcode attributes/arguments.
		 */
		public function wpbooklist_book_cover_shortcode( $atts ) {

			global $wpdb;

			extract(
				shortcode_atts(
					array(
						'table'   => $wpdb->prefix . 'saved_book_log',
						'isbn'    => '',
						'width'   => '100',
						'align'   => 'left',
						'margin'  => '5px',
						'action'  => 'bookview',
						'display' => 'justimage',
					), $atts
				)
			);

			if ( null === $atts ) {
				$table       = $wpdb->prefix . 'wpbooklist_jre_saved_book_log';
				$options_row = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table  LIMIT %d",1 ) );
				$isbn        = $options_row[0]->isbn;
				$width       = '100';

			}

			if ( ! isset( $atts['isbn'] ) && ! isset( $atts['table'] ) ) {
				$table       = $wpdb->prefix . 'wpbooklist_jre_saved_book_log';
				$options_row = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table LIMIT %d", 1 ) );
				$isbn        = $options_row[0]->isbn;
			}

			if ( ! isset( $atts['isbn'] ) && isset( $atts['table'] ) ) {
				$table       = $wpdb->prefix . 'wpbooklist_jre_' . strtolower( $table );
				$options_row = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table  LIMIT %d", 1 ) );
				$isbn        = $options_row[0]->isbn;

			}

			if ( isset( $atts['isbn'] ) && ! isset( $atts['table'] ) ) {
				$table = $wpdb->prefix . 'wpbooklist_jre_saved_book_log';
			}

			if ( isset( $atts['isbn'] ) && isset( $atts['table'] ) ) {
				$table = $wpdb->prefix . 'wpbooklist_jre_' . strtolower( $table );
			}

			$isbn        = str_replace( '-', '', $isbn );
			$options_row = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table WHERE isbn = %s", $isbn ) );
			if ( 0 === count( $options_row ) ) {
				echo __( "This book isn't in your Library! Please check the ISBN/ASIN number you provided.", 'wpbooklist' );
			} else {

				$image              = $options_row[0]->image;
				$link               = $options_row[0]->amazon_detail_page;
				$table_name_options = $wpdb->prefix . 'wpbooklist_jre_user_options';
				$options_results    = $wpdb->get_row( "SELECT * FROM $table_name_options" );

				// Replace with user's affiliate id, if available.
				$amazonaff = $options_results->amazonaff;
				if ( '' !== $amazonaff && null !== $amazonaff ) {
					$link = str_replace( 'wpbooklisti0e-21', $amazonaff, $link );
				}

				$amazoncountryinfo = $options_results->amazoncountryinfo;
				switch ( $amazoncountryinfo ) {
					case 'au':
						$link = str_replace( '.com', '.com.au', $link );
						break;
					case 'br':
						$link = str_replace( '.com', '.com.br', $link );
						break;
					case 'ca':
						$link = str_replace( '.com', '.ca', $link );
						break;
					case 'cn':
						$link = str_replace( '.com', '.cn', $link );
						break;
					case 'fr':
						$link = str_replace( '.com', '.fr', $link );
						break;
					case 'de':
						$link = str_replace( '.com', '.de', $link );
						break;
					case 'in':
						$link = str_replace( '.com', '.in', $link );
						break;
					case 'it':
						$link = str_replace( '.com', '.it', $link );
						break;
					case 'jp':
						$link = str_replace( '.com', '.co.jp', $link );
						break;
					case 'mx':
						$link = str_replace( '.com', '.com.mx', $link );
						break;
					case 'nl':
						$link = str_replace( '.com', '.nl', $link );
						break;
					case 'es':
						$link = str_replace( '.com', '.es', $link );
						break;
					case 'uk':
						$link = str_replace( '.com', '.co.uk', $link );
						break;
					default:
						$link;
				}
			}

			$class = 'class="wpbooklist_jre_book_cover_shortcode_link wpbooklist-show-book-colorbox"';
			if ( isset( $atts['action'] ) ) {
				switch ( $atts['action'] ) {
					case 'amazon':
						$class = 'class="wpbooklist_jre_book_cover_shortcode_link"';
						$link  = $link;
						break;
					case 'googlebooks':
						$class = 'class="wpbooklist_jre_book_cover_shortcode_link"';
						$link  = $options_row[0]->google_preview;
						if ( null === $link ) {
							$link = $options_row[0]->amazon_detail_page;
						}
						break;
					case 'ibooks':
						$class = 'class="wpbooklist_jre_book_cover_shortcode_link"';
						$link  = $options_row[0]->itunes_page;
						if ( null === $link ) {
							$link = $options_row[0]->amazon_detail_page;
						}
						break;
					case 'booksamillion':
						$class = 'class="wpbooklist_jre_book_cover_shortcode_link"';
						$link  = $options_row[0]->bam_link;
						if ( null === $link ) {
							$link = $options_row[0]->amazon_detail_page;
						}
						break;
					case 'barnesandnoble':
						$class = 'class="wpbooklist_jre_book_cover_shortcode_link"';
						$link  = $options_row[0]->bn_link;
						if ( null === $link ) {
							$link = $options_row[0]->amazon_detail_page;
						}
						break;
					case 'kobo':
						$class = 'class="wpbooklist_jre_book_cover_shortcode_link"';
						$link  = $options_row[0]->kobo_link;
						if ( null === $link ) {
							$link = $options_row[0]->amazon_detail_page;
						}
						break;
					case 'page':
						$class = 'class="wpbooklist_jre_book_cover_shortcode_link"';
						$link  = get_permalink( $options_row[0]->page_yes );
						if (  false === $link ) {
							$link = $options_row[0]->amazon_detail_page;
						}
						break;
					case 'post':
						$class = 'class="wpbooklist_jre_book_cover_shortcode_link"';
						$link  = get_permalink( $options_row[0]->post_yes );
						if ( false === $link ) {
							$link = $options_row[0]->amazon_detail_post;
						}
						break;
					case 'bookview':
						$class = 'class="wpbooklist_jre_book_cover_shortcode_link wpbooklist-show-book-colorbox"';
						break;
					default:
						$class = 'class="wpbooklist_jre_book_cover_shortcode_link wpbooklist-show-book-colorbox"';
						$link  = $link;
						break;
				}
			} else {
				$link  = $link;
				$class = 'class="wpbooklist_jre_book_cover_shortcode_link wpbooklist-show-book-colorbox"';
			}



			$final_link = '<div style="float:' . $align . '; margin:' . $margin . '; margin-bottom:50px;" class="wpbooklist-shortcode-entire-container"><a  style="z-index:9; float:' . $align . '; margin:' . $margin . ';" ' . $class . ' data-booktable="' . $table . '" data-bookid="' . $options_row[0]->ID . '" ' . $class . ' target="_blank" href="' . $link . '"><img style="min-width:150px; margin-right: 5px; width:' . $width . 'px!important" src="' . $image . '"/></a>';

			$display = '';
			if ( isset( $atts['display'] ) ) {
				switch ( $atts['display'] ) {
					case 'justimage':
						$display = '';
						break;
					case 'excerpt':
						$final_link = str_replace( 'float:right', 'float:left', $final_link );
						$final_link = str_replace( 'float:right', 'float:left', $final_link );

						$text  = $options_row[0]->description;
						$text  = str_replace( '<br />', ' ', html_entity_decode( $text ) );
						$text  = str_replace( '<br/>', ' ', html_entity_decode( $text ) );
						$text  = str_replace( '<div>', '', html_entity_decode( $text ) );
						$text  = str_replace( '</div>', '', html_entity_decode( $text ) );
						$limit = 40;

						if ( str_word_count( $text, 0 ) > $limit ) {
							$words = str_word_count( $text, 2 );
							$pos   = array_keys( $words );
							$text  = substr( $text, 0, $pos[ $limit ] ) . '...';
						}

						$title = $options_row[0]->title;
						$limit = 10;

						if ( str_word_count( $title, 0 ) > $limit ) {
							$words = str_word_count( $title, 2 );
							$pos   = array_keys( $words );
							$title = substr( $title, 0, $pos[ $limit ] ) . '...';
						}

						// If the 'allow_url_fopen' directive is allowed, use getimagesize(), otherwise do the roundabout cUrl way to retrieve the remote image and determine the size.
						if ( ini_get( 'allow_url_fopen' ) ) {
							$size = getimagesize( $image );
						} else {
							$ch      = curl_init();
							$timeout = 5;
							curl_setopt ( $ch, CURLOPT_URL, $image );
							curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
							curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
							$file_contents = curl_exec( $ch );
							curl_close( $ch );

							$new_image = ImageCreateFromString( $file_contents );
							imagejpeg( $new_image, 'temp.png', 100 );

							// Get new dimensions.
							$size = getimagesize( 'temp.png' );

						}

						$origwidth    = $size[0];
						$origheight   = $size[1];
						$final_height = ( $origheight * $width ) / $origwidth;
						$descheight   = $final_height - 90;
						$string1      = '';
						$string2      = '';
						$string3      = '';
						$string4      = '';
						$string5      = '';
						$string6      = '';

						$display = '
						<div style="display:grid; height:' . $final_height . 'px" class="wpbooklist-shortcode-below-link-div">
							<h3 class="wpbooklist-shortcode-h3" style="text-align:' . $align . ';">' . $title . '</h3>
							<div style="text-align:' . $align . '; position:relative; bottom:5px; class="wpbooklist-shortcode-below-link-excerpt">' . $text . '</div>
							<div class="wpbooklist-shortcode-link-holder-media" style="text-align:' . $align . '; bottom:-10px; class="wpbooklist-shortcode-purchase-links">';

						if ( null !== $options_row[0]->amazon_detail_page ) {
							$string1 = '
							<a class="wpbooklist-purchase-img" href="' . $options_row[0]->amazon_detail_page . '" target="_blank">
								<img src="' . ROOT_IMG_URL . 'amazon.png">
							</a>';
						}

						$string2 = '
						<a class="wpbooklist-purchase-img" href="http://www . barnesandnoble . com/s/' . $options_row[0]->isbn . '" target="_blank">
							<img src="' . ROOT_IMG_URL . 'bn.png">
						</a>';

						if ( null !== $options_row[0]->google_preview ) {
							$string3 = '
							<a class="wpbooklist-purchase-img" href="' . $options_row[0]->google_preview . '" target="_blank">
								<img src="' . ROOT_IMG_URL . 'googlebooks.png">
							</a>';
						}

						if ( null !== $options_row[0]->itunes_page ) {
							$string4 =
							'<a class="wpbooklist-purchase-img" href="' . $options_row[0]->itunes_page . '" target="_blank">
								<img src="' . ROOT_IMG_URL . 'ibooks.png" id="wpbooklist-itunes-img">
							</a>';
						}

						if ( null !== $options_row[0]->bam_link ) {
							$string5 =
							'<a class="wpbooklist-purchase-img" href="' . $options_row[0]->bam_link . '" target="_blank">
								<img src="' . ROOT_IMG_URL . 'bam-icon.jpg">
							</a>';
						}

						if ( null !== $options_row[0]->kobo_link ) {
							$string6 = '<a class="wpbooklist-purchase-img" href="' . $options_row[0]->kobo_link . '" target="_blank">
								<img src="' . ROOT_IMG_URL . 'kobo-icon.png">
							</a>';
						}

						$string7 = '</div></div></div>';
						$display = $display . $string1 . $string2 . $string3 . $string4 . $string5 . $string6 . $string7;

						break;
					default:
						$display = '';
						break;
				}
			}
			return $final_link . $display;
		}

		/**
		 *  Function to run the compatability code in the Compat class for upgrades/updates, if stored version number doesn't match the defined global in wpbooklist.php
		 */
		public function wpbooklist_update_upgrade_function() {

			// Get current version #.
			global $wpdb;
			$table_name = $wpdb->prefix . 'wpbooklist_jre_user_options';
			$row        = $wpdb->get_row( "SELECT * FROM $table_name" );
			$version    = $row->version;

			// If version number does not match the current version number found in wpbooklist.php, call the Compat class and run upgrade functions.
			if ( WPBOOKLIST_VERSION_NUM !== $version ) {
				require_once CLASS_COMPAT_DIR . 'class-wpbooklist-compat-functions.php';
				$compat_class = new WPBookList_Compat_Functions();
			}
		}

		/**
		 *  Function that calls the Style and Scripts needed for displaying of admin pointer messages.
		 */
		public function wpbooklist_jre_admin_pointers_javascript() {
			wp_enqueue_style( 'wp-pointer' );
			wp_enqueue_script( 'wp-pointer' );
			wp_enqueue_script( 'utils' );
		}

	}
endif;
