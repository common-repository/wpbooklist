<?php
/**
 * Class WPBookList_Compat_Functions Class - class-wpbooklist-compat-functions.php
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes/Compat
 * @version  6.1.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Compat_Functions', false ) ) :
	/**
	 * WPBookList_Compat_Functions class. Here we'll run functions that make older versions of WPBookList compatible with newest version
	 */
	class WPBookList_Compat_Functions {


		/** Common member variable
		 *
		 *  @var string $this->version
		 */
		public $version = '';

		/**
		 *  Simply sets the version number for the class
		 */
		public function __construct() {

			// Get current version #.
			global $wpdb;
			$table_name    = $wpdb->prefix . 'wpbooklist_jre_user_options';
			$row           = $wpdb->get_row( "SELECT * FROM $table_name" );
			$this->version = $row->version;

			// Now call all the functions to make updates.
			$this->wpbooklist_upgrade_modify_user_options_table();
			$this->wpbooklist_upgrade_modify_page_options_table();
			$this->wpbooklist_upgrade_modify_post_options_table();
			$this->wpbooklist_upgrade_modify_default_table();
			$this->wpbooklist_upgrade_modify_custom_libs_books_and_settings_tables();
			$this->wpbooklist_upgrade_modify_add_storytime_table();
			$this->wpbooklist_upgrade_modify_add_storytime_settings_table();
			$this->wpbooklist_upgrade_change_admin_message();
			$this->wpbooklist_add_author_first_last_default_table();
			$this->wpbooklist_add_author_first_last_dynamic_table();
			$this->wpbooklist_upgrade_modify_add_users_table();
			$this->wpbooklist_add_wpbooklist_basic_user_role();
			$this->wpbooklist_create_wpbooklist_user_on_plugin_update();

			// Now call the function that will update the version number, which will ensure none of these function ever run again until the next update/upgrade.
			$this->wpbooklist_update_version_number_function();

		}

		/**
		 *  Function that modifies the wpbooklist_jre_user_options table as needed.
		 */
		public function wpbooklist_upgrade_modify_user_options_table() {

			global $wpdb;

			// If version number does not match the current version number found in wpbooklist.php.
			if ( WPBOOKLIST_VERSION_NUM !== $this->version ) {

				$table_name = $wpdb->prefix . 'wpbooklist_jre_user_options';

				// ADD COLUMNS TO THE 'wpbooklist_jre_user_options' TABLE.
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'activeposttemplate'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD activeposttemplate varchar( 255 ) NOT NULL DEFAULT 'default'" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'activepagetemplate'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD activepagetemplate varchar( 255 ) NOT NULL DEFAULT 'default'" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hidekindleprev'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hidekindleprev bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hidegoogleprev'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hidegoogleprev bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hidekobopurchase'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hidekobopurchase bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hidebampurchase'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hidebampurchase bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hidesubject'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hidesubject bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hidecountry'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hidecountry bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hidefilter'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hidefilter bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hidefinishedsort'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hidefinishedsort bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hidesignedsort'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hidesignedsort bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hidefirstsort'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hidefirstsort bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hidesubjectsort'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hidesubjectsort bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hideadditionalimgs'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hideadditionalimgs bigint(255)" );
				}



				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'patreonaccess'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD patreonaccess varchar(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'patreonrefresh'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD patreonrefresh varchar(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'patreonack'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD patreonack bigint(255)" );
				}

				// Begin addition of version 6.0.0 columns.
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'customfields'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD customfields TEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'extensionversions'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD extensionversions TEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hideasin'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hideasin bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hideoutofprint'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hideoutofprint bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hideothereditions'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hideothereditions bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hidekeywords'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hidekeywords bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hideisbn10'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hideisbn10 bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hideisbn13'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hideisbn13 bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hidegenres'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hidegenres bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hidecallnumber'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hidecallnumber bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hideformat'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hideformat bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hideillustrator'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hideillustrator bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hidelanguage'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hidelanguage bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hidenumberinseries'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hidenumberinseries bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hideorigpubyear'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hideorigpubyear bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hideorigtitle'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hideorigtitle bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hideseries'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hideseries bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hideshortdesc'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hideshortdesc bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hidesubgenre'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD hidesubgenre bigint(255)" );
				}
			}
		}

		/**
		 *  Function that modifies the wpbooklist_jre_page_options table as needed.
		 */
		public function wpbooklist_upgrade_modify_page_options_table() {

			global $wpdb;

			// If version number does not match the current version number found in wpbooklist.php.
			if ( WPBOOKLIST_VERSION_NUM !== $this->version ) {

				// ADD COLUMNS TO THE 'wpbooklist_jre_page_options' TABLE.
				$table_name_page_options = $wpdb->prefix . 'wpbooklist_jre_page_options';
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_page_options` LIKE 'hidekindleprev'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_page_options ADD hidekindleprev bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_page_options` LIKE 'hidegoogleprev'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_page_options ADD hidegoogleprev bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_page_options` LIKE 'hidekobopurchase'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_page_options ADD hidekobopurchase bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_page_options` LIKE 'hidebampurchase'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_page_options ADD hidebampurchase bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_page_options` LIKE 'hidesubject'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_page_options ADD hidesubject bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_page_options` LIKE 'hidecountry'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_page_options ADD hidecountry bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_page_options` LIKE 'hidefilter'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_page_options ADD hidefilter bigint(255)" );
				}

				// Begin addition of version 6.0.0 columns.
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_page_options` LIKE 'hideasin'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_page_options ADD hideasin bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_page_options` LIKE 'hideoutofprint'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_page_options ADD hideoutofprint bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_page_options` LIKE 'hideothereditions'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_page_options ADD hideothereditions bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_page_options` LIKE 'hidekeywords'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_page_options ADD hidekeywords bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_page_options` LIKE 'hideisbn10'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_page_options ADD hideisbn10 bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_page_options` LIKE 'hideisbn13'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_page_options ADD hideisbn13 bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_page_options` LIKE 'hidegenres'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_page_options ADD hidegenres bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_page_options` LIKE 'hidecallnumber'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_page_options ADD hidecallnumber bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_page_options` LIKE 'hideformat'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_page_options ADD hideformat bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_page_options` LIKE 'hideillustrator'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_page_options ADD hideillustrator bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_page_options` LIKE 'hidelanguage'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_page_options ADD hidelanguage bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_page_options` LIKE 'hidenumberinseries'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_page_options ADD hidenumberinseries bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_page_options` LIKE 'hideorigpubyear'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_page_options ADD hideorigpubyear bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_page_options` LIKE 'hideorigtitle'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_page_options ADD hideorigtitle bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_page_options` LIKE 'hideseries'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_page_options ADD hideseries bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_page_options` LIKE 'hideshortdesc'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_page_options ADD hideshortdesc bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_page_options` LIKE 'hidesubgenre'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_page_options ADD hidesubgenre bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_page_options` LIKE 'hideadditionalimgs'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_page_options ADD hideadditionalimgs bigint(255)" );
				}

				

			}
		}

		/**
		 *  Function that modifies the wpbooklist_jre_post_options table as needed.
		 */
		public function wpbooklist_upgrade_modify_post_options_table() {

			global $wpdb;

			// If version number does not match the current version number found in wpbooklist.php.
			if ( WPBOOKLIST_VERSION_NUM !== $this->version ) {

				// ADD COLUMNS TO THE 'wpbooklist_jre_post_options' TABLE.
				$table = $wpdb->prefix . 'wpbooklist_jre_post_options';
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidekindleprev'" ) ) {
					$wpdb->query( "ALTER TABLE $table ADD hidekindleprev bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidegoogleprev'" ) ) {
					$wpdb->query( "ALTER TABLE $table ADD hidegoogleprev bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidekobopurchase'" ) ) {
					$wpdb->query( "ALTER TABLE $table ADD hidekobopurchase bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidebampurchase'" ) ) {
					$wpdb->query( "ALTER TABLE $table ADD hidebampurchase bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidesubject'" ) ) {
					$wpdb->query( "ALTER TABLE $table ADD hidesubject bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidecountry'" ) ) {
					$wpdb->query( "ALTER TABLE $table ADD hidecountry bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidefilter'" ) ) {
					$wpdb->query( "ALTER TABLE $table ADD hidefilter bigint(255)" );
				}

				// Begin addition of version 6.0.0 columns.
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hideasin'" ) ) {
					$wpdb->query( "ALTER TABLE $table ADD hideasin bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hideoutofprint'" ) ) {
					$wpdb->query( "ALTER TABLE $table ADD hideoutofprint bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hideothereditions'" ) ) {
					$wpdb->query( "ALTER TABLE $table ADD hideothereditions bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidekeywords'" ) ) {
					$wpdb->query( "ALTER TABLE $table ADD hidekeywords bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hideisbn10'" ) ) {
					$wpdb->query( "ALTER TABLE $table ADD hideisbn10 bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hideisbn13'" ) ) {
					$wpdb->query( "ALTER TABLE $table ADD hideisbn13 bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidegenres'" ) ) {
					$wpdb->query( "ALTER TABLE $table ADD hidegenres bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidecallnumber'" ) ) {
					$wpdb->query( "ALTER TABLE $table ADD hidecallnumber bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hideformat'" ) ) {
					$wpdb->query( "ALTER TABLE $table ADD hideformat bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hideillustrator'" ) ) {
					$wpdb->query( "ALTER TABLE $table ADD hideillustrator bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidelanguage'" ) ) {
					$wpdb->query( "ALTER TABLE $table ADD hidelanguage bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidenumberinseries'" ) ) {
					$wpdb->query( "ALTER TABLE $table ADD hidenumberinseries bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hideorigpubyear'" ) ) {
					$wpdb->query( "ALTER TABLE $table ADD hideorigpubyear bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hideorigtitle'" ) ) {
					$wpdb->query( "ALTER TABLE $table ADD hideorigtitle bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hideseries'" ) ) {
					$wpdb->query( "ALTER TABLE $table ADD hideseries bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hideshortdesc'" ) ) {
					$wpdb->query( "ALTER TABLE $table ADD hideshortdesc bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidesubgenre'" ) ) {
					$wpdb->query( "ALTER TABLE $table ADD hidesubgenre bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hideadditionalimgs'" ) ) {
					$wpdb->query( "ALTER TABLE $table ADD hideadditionalimgs bigint(255)" );
				}

			}
		}

		/**
		 *  Function that modifies the wpbooklist_jre_saved_book_log table as needed.
		 */
		public function wpbooklist_upgrade_modify_default_table() {

			global $wpdb;

			// If version number does not match the current version number found in wpbooklist.php.
			if ( WPBOOKLIST_VERSION_NUM !== $this->version ) {

				// Add columns to the default WPBookList table, if they don't exist.
				$table_name_default = $wpdb->prefix . 'wpbooklist_jre_saved_book_log';
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'subject'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD subject varchar(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'country'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD country varchar(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'woocommerce'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD woocommerce varchar(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'kobo_link'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD kobo_link varchar(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'bam_link'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD bam_link varchar(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'bn_link'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD bn_link varchar(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'lendstatus'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD lendstatus varchar(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'currentlendemail'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD currentlendemail varchar(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'currentlendname'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD currentlendname varchar(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'lendable'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD lendable varchar(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'copies'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD copies bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'copieschecked'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD copieschecked bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'lendedon'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD lendedon bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'authorfirst'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD authorfirst varchar(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'authorlast'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD authorlast varchar(255)" );
				}

				// Begin addition of version 6.0.0 columns..
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'additionalimage1'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD additionalimage1 TEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'additionalimage2'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD additionalimage2 TEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'appleibookslink'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD appleibookslink TEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'asin'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD asin varchar(190)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'author2'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD author2 TEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'author3'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD author3 TEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'authorfirst2'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD authorfirst2 TEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'authorfirst3'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD authorfirst3 TEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'authorlast2'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD authorlast2 TEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'authorlast3'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD authorlast3 TEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'backcover'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD backcover TEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'callnumber'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD callnumber TEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'edition'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD edition TEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'format'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD format TEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'genres'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD genres TEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'goodreadslink'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD goodreadslink TEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'illustrator'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD illustrator TEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'isbn13'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD isbn13 TEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'keywords'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD keywords MEDIUMTEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'language'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD language TEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'numberinseries'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD numberinseries TEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'originalpubyear'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD originalpubyear bigint(255)" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'originaltitle'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD originaltitle TEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'othereditions'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD othereditions MEDIUMTEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'outofprint'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD outofprint TEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'series'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD series TEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'shortdescription'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD shortdescription MEDIUMTEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'similarbooks'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD similarbooks MEDIUMTEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'subgenre'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD subgenre TEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'sale_url'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD sale_url TEXT" );
				}
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'ebook'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name_default ADD ebook TEXT" );
				}
				

				// Modify the ISBN column in the default library to be varchar, which will allow the storage of ASIN numbers.
				$wpdb->query( "ALTER TABLE $table_name_default MODIFY isbn varchar( 190 )" );


				// Modify the Rating column in the default library to be float, which will allow the storage of half-star numbers.
				$wpdb->query( "ALTER TABLE $table_name_default MODIFY rating float" );



			}
		}

		/**
		 *  Function that modifies existing custom libraries - both the book data and the settings data.
		 */
		public function wpbooklist_upgrade_modify_custom_libs_books_and_settings_tables() {

			global $wpdb;

			// If version number does not match the current version number found in wpbooklist.php.
			if ( WPBOOKLIST_VERSION_NUM !== $this->version ) {

				// Modify any existing custom libraries - both the book data and the settings data.
				$table_dyna = $wpdb->prefix . "wpbooklist_jre_list_dynamic_db_names";
				$user_created_tables = $wpdb->get_results( "SELECT * FROM $table_dyna" );
				foreach ( $user_created_tables as $utable ) {

					$table = $wpdb->prefix . "wpbooklist_jre_" . $utable->user_table_name;

					// This is how we get the column type.
					$result = $wpdb->get_row( "SHOW COLUMNS FROM `$table` LIKE 'isbn'" );
					if ( 'varchar(190)' !== $result->Type ) {
						$wpdb->query( "ALTER TABLE $table MODIFY isbn varchar( 190 )" );
					}

					// Modify the Rating column in the default library to be float, which will allow the storage of half-star numbers.
					$result = $wpdb->get_row( "SHOW COLUMNS FROM `$table` LIKE 'rating'" );
					if ( 'float' !== $result->Type ) {
						$wpdb->query( "ALTER TABLE $table MODIFY rating float" );
					}

					// Add WooCommerce column.
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'woocommerce'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD woocommerce varchar(255)" );
					}

					// Add additional columns that may not be there already.
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'kobo_link'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD kobo_link varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'bam_link'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD bam_link varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'bn_link'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD bn_link varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'lendstatus'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD lendstatus varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'currentlendemail'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD currentlendemail varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'currentlendname'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD currentlendname varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'lendable'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD lendable varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'copies'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD copies bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'copieschecked'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD copieschecked bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'lendedon'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD lendedon bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'subject'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD subject varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'country'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD country varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'authorfirst'" ) ) {
					$wpdb->query( "ALTER TABLE $table ADD authorfirst varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'authorlast'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD authorlast varchar(255)" );
					}

					// Begin addition of version 6.0.0 columns..
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'additionalimage1'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD additionalimage1 varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'additionalimage2'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD additionalimage2 varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'appleibookslink'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD appleibookslink varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'asin'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD asin varchar(190)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'author2'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD author2 varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'author3'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD author3 varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'authorfirst2'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD authorfirst2 varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'authorfirst3'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD authorfirst3 varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'authorlast2'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD authorlast2 varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'authorlast3'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD authorlast3 varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'backcover'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD backcover varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'callnumber'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD callnumber varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'edition'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD edition varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'format'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD format varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'genres'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD genres varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'goodreadslink'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD goodreadslink TEXT" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'illustrator'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD illustrator varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'isbn13'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD isbn13 varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'keywords'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD keywords MEDIUMTEXT" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'language'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD language varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'numberinseries'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD numberinseries varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'originalpubyear'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD originalpubyear bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'originaltitle'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD originaltitle varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'othereditions'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD othereditions MEDIUMTEXT" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'outofprint'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD outofprint varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'series'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD series varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'shortdescription'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD shortdescription MEDIUMTEXT" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'similarbooks'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD similarbooks MEDIUMTEXT" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'subgenre'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD subgenre varchar(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'sale_url'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD sale_url TEXT" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'ebook'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD ebook TEXT" );
					}

					// Now begin modifying the custom library's settings tables.
					$table = $wpdb->prefix . "wpbooklist_jre_settings_" . $utable->user_table_name;
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'activeposttemplate'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD activeposttemplate varchar( 255 ) NOT NULL DEFAULT 'default'" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'activepagetemplate'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD activepagetemplate varchar( 255 ) NOT NULL DEFAULT 'default'" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidekindleprev'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hidekindleprev bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidegoogleprev'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hidegoogleprev bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidekobopurchase'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hidekobopurchase bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidebampurchase'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hidebampurchase bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidesubject'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hidesubject bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidecountry'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hidecountry bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidefilter'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hidefilter bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidefinishedsort'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hidefinishedsort bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidesignedsort'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hidesignedsort bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidefirstsort'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hidefirstsort bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidesubjectsort'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hidesubjectsort bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hideadditionalimgs'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hideadditionalimgs bigint(255)" );
					}

					// Begin addition of version 6.0.0 columns for the Custom Library's Settings table.
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hideasin'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hideasin bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hideoutofprint'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hideoutofprint bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hideothereditions'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hideothereditions bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidekeywords'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hidekeywords bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hideisbn10'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hideisbn10 bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hideisbn13'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hideisbn13 bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidegenres'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hidegenres bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidecallnumber'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hidecallnumber bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hideformat'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hideformat bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hideillustrator'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hideillustrator bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidelanguage'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hidelanguage bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidenumberinseries'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hidenumberinseries bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hideorigpubyear'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hideorigpubyear bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hideorigtitle'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hideorigtitle bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hideseries'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hideseries bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hideshortdesc'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hideshortdesc bigint(255)" );
					}
					if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table` LIKE 'hidesubgenre'" ) ) {
						$wpdb->query( "ALTER TABLE $table ADD hidesubgenre bigint(255)" );
					}
				}
			}
		}


		/**
		 *  Function that adds the StoryTime table, introduced in 5.7.0
		 */
		public function wpbooklist_upgrade_modify_add_storytime_table() {

			global $wpdb;

			// If version number does not match the current version number found in wpbooklist.php.
			if ( WPBOOKLIST_VERSION_NUM !== $this->version ) {

				// Add the StoryTime table, introduced in 5.7.0.
				$storytime_table_name = $wpdb->prefix . 'wpbooklist_jre_storytime_stories';
				if ( $storytime_table_name !== $wpdb->get_var("SHOW TABLES LIKE '$storytime_table_name'") ) {

					// Include everything needed to add a table, and register the table name.
					global $charset_collate;
					require_once ABSPATH . 'wp-admin/includes/upgrade.php';
					$wpdb->wpbooklist_jre_storytime_stories = "{$wpdb->prefix}wpbooklist_jre_storytime_stories";

					// Add the StoryTime table.
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

					require_once CLASS_STORYTIME_DIR . 'class-storytime.php';
					$storytime_class = new WPBookList_Storytime( 'install' );
				}
			}
		}

		/**
		 *  Function that adds the StoryTime Settings table, introduced in 5.7.0
		 */
		public function wpbooklist_upgrade_modify_add_storytime_settings_table() {

			global $wpdb;

			// If version number does not match the current version number found in wpbooklist.php.
			if ( WPBOOKLIST_VERSION_NUM !== $this->version ) {

				// Add the StoryTime Settings table, introduced in 5.7.0.
				$storytime_settings_table_name = $wpdb->prefix . 'wpbooklist_jre_storytime_stories_settings';
				if ( $storytime_settings_table_name !== $wpdb->get_var("SHOW TABLES LIKE '$storytime_settings_table_name'") ) {

					// Include everything needed to add a table, and register the table name.
					global $charset_collate;
					require_once ABSPATH . 'wp-admin/includes/upgrade.php';
					$wpdb->wpbooklist_jre_storytime_stories_settings = "{$wpdb->prefix}wpbooklist_jre_storytime_stories_settings";

					// Add the StoryTime settings table.
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

					// Insert the row.
					$table_name = $wpdb->prefix . 'wpbooklist_jre_storytime_stories_settings';
					$wpdb->insert( $table_name, array( 'ID' => 1 ) );
				}
			}
		}

		/**
		 *  Function that adds the WPBookList Users table, introduced in 6.0.0.
		 */
		public function wpbooklist_upgrade_modify_add_users_table() {

			global $wpdb;

			// If version number does not match the current version number found in wpbooklist.php.
			if ( WPBOOKLIST_VERSION_NUM !== $this->version ) {

				// If table doesn't exist, create table.
				$test_name = $wpdb->prefix . 'wpbooklist_jre_users_table';
				if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $test_name ) ) !== $test_name ) {

					// Include everything needed to add a table, and register the table name.
					global $charset_collate;
					require_once ABSPATH . 'wp-admin/includes/upgrade.php';
					$wpdb->wpbooklist_jre_users_table = "{$wpdb->prefix}wpbooklist_jre_users_table";

					// This is the table that holds static data about users - things like username, password, height, gender...
					$sql_create_table = "CREATE TABLE {$wpdb->wpbooklist_jre_users_table} 
						(
				            ID smallint(190) auto_increment,
				            firstname varchar(190),
				            lastname varchar(255),
				            datecreated varchar(255),
				            wpuserid smallint(6),
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
							googleplus varchar(255),
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

						$firstname = '';
						$lastname  = '';
						if ( '' === $current_user->user_firstname || null === $current_user->user_firstname ) {

							if ( '' === $current_user->display_name || null === $current_user->display_name ) {
								$firstname = 'Admin';
								$lastname  = '';
							} else {
								$firstname = $current_user->display_name;
								$lastname  = '';
							}
						} else {
							$firstname = $current_user->user_firstname;
							$lastname  = $current_user->user_lastname;
						}

						$wpdb->insert( $table_name,
							array(
								'firstname'    => $firstname,
								'lastname'     => $lastname,
								'datecreated'  => $this->date,
								'wpuserid'     => $current_user->ID,
								'email'        => $current_user->user_email,
								'username'     => $current_user->user_email,
								'role'         => 'SuperAdmin',
								'permissions'  => 'Yes-Yes-Yes-Yes-Yes',
								'libraries'    => 'alllibraries',
								'profileimage' => get_avatar_url( $current_user->ID ),
							)
						);
					}
					$key = $wpdb->prefix . 'wpbooklist_jre_users_table';
					return $db_delta_result[ $key ];
				} else {
					return 'Table already exists';
				}
			}
		}

		/**
		 *  Function that updates the Admin message if needed
		 */
		public function wpbooklist_upgrade_change_admin_message() {

			global $wpdb;

			// If version number does not match the current version number found in wpbooklist.php.
			if ( WPBOOKLIST_VERSION_NUM !== $this->version ) {

				$table_name = $wpdb->prefix . 'wpbooklist_jre_user_options';

				// Update admin message.
				$data = array(
					'adminmessage' => 'aladivaholeidanequaladqwpbooklistdashnoticedashholderadqaraaholeaholealaaaholehrefanequaladqhttpsacoaslashaslashwpbooklistdotcomaslashadqaraaholeaholeaholeaholealaimgaholewidthanequaladq100ampersandpercntascadqaholesrcanequaladqhttpsacoaslashaslashwpbooklistdotcomaslashwpdashcontentaslashuploadsaslash2018aslash01aslashScreenshotdash2018dash01dash25dash18dot35dot59dash5dotpngadqaslasharaaholeaholealaaslashaaraaholeaholealadivaholeclassanequaladqwpbooklistdashmydashnoticedashdismissdashforeveradqaholeidanequaladqwpbooklistdashmydashnoticedashdismissdashforeverdashgeneraladqaraDismissaholeForeveralaaslashdivaraaholeaholealabuttonaholetypeanequaladqbuttonadqaholeclassanequaladqnoticedashdismissadqaraaholeaholeaholeaholealaspanaholeclassanequaladqscreendashreaderdashtextadqaraDismissaholethisaholenoticealaaslashspanaraaholeaholealaaslashbuttonaraalaaslashdivaraalabuttonaholetypeanequaladqbuttonadqaholeclassanequaladqnoticedashdismissadqaraalaspanaholeclassanequaladqscreendashreaderdashtextadqaraDismissaholethisaholenoticedotalaaslashspanaraalaaslashbuttonara',
				);
				$format       = array( '%s' );
				$where        = array( 'ID' => 1 );
				$where_format = array( '%d' );
				$wpdb->update( $table_name, $data, $where, $format, $where_format );

			}
		}


		/**
		 *  Function for taking Authors from existing Author column, splitting the name up, and adding to the new authorfirst and authorlast columns in the default table.
		 */
		public function wpbooklist_add_author_first_last_default_table() {

			// If version number does not match the current version number found in wpbooklist.php.
			if ( WPBOOKLIST_VERSION_NUM !== $this->version ) {
				global $wpdb;

				// Modifying the default WPBookList table First, then any possible user-created dynamic tables.
				$table_name_default = $wpdb->prefix . 'wpbooklist_jre_saved_book_log';

				// If the two new Author columns do exist...
				if ( 0 !== $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'authorfirst'" ) && 0 !== $wpdb->query("SHOW COLUMNS FROM `$table_name_default` LIKE 'authorlast'") ) {

					$book_array = $wpdb->get_results( "SELECT * FROM $table_name_default" );
					$nonamearray = array();
					foreach ( $book_array as $key => $value ) {

						// Building array of titles to look for in author's names.
						$title_array = array(
							'Jr.',
							'Ph.D.',
							'Mr.',
							'Mrs.',
						);

						$origauthorname  = $value->author;
						$title           = '';
						$finallastnames  = '';
						$finalfirstnames = '';

						// First let's handle names with commas, which we'll assume indicates multiple authors.
						if ( false !== strpos( $origauthorname, ',' ) && '' === $finallastnames && '' === $finalfirstnames ) {

							$origauthorcommaarray = explode( ',', $origauthorname );
							$lastnamecolonstring  = '';
							$firstnamecolonstring = '';

							foreach ( $origauthorcommaarray as $key2 => $individual ) {

								// First let's remove troublesome things like Ph.D., Jr., etc, and save them to be added back to end of the name.
								foreach ( $title_array as $titlekey => $titlevalue ) {
									if( false !== stripos( $individual, $titlevalue ) ) {
										$individual = str_replace( $titlevalue, '', $individual );
										$individual = rtrim( $individual, ' ' );
										$title = $titlevalue;
									}
								}

								// Explode by last space in name.
								$firstname = implode(' ', explode( ' ', $individual, -1 ) );
								$temp = explode( ' ', strrev( $individual ), 2 );
								$lastname = strrev( $temp[0] );

								$lastnamecolonstring = $lastnamecolonstring.';'.$lastname;

								if( '' !== $title && null !== $title ) {
									$firstnamecolonstring = $firstnamecolonstring . ';' . $firstname . ' ' . $title;
								} else {
									$firstnamecolonstring = $firstnamecolonstring . ';' . $firstname;
								}
							}

							// Trim left spaces and ;.
							$lastnamecolonstring = ltrim( $lastnamecolonstring, ' ' );
							$lastnamecolonstring = ltrim( $lastnamecolonstring, ';' );

							// Trim left spaces and ;.
							$firstnamecolonstring = ltrim( $firstnamecolonstring, ' ' );
							$firstnamecolonstring = ltrim( $firstnamecolonstring, ';' );

							// Now build finalfirstname and finallastname string for the two new db columns.
							$finallastnames  = $lastnamecolonstring;
							$finalfirstnames = $firstnamecolonstring;
						}

						// Next we'll handle the names of single authors who may have a title in their name.
						foreach ( $title_array as $titlekey => $titlevalue ) {

							// If author name has a title in it, and does not have a comma (indicating multiple authors), then proceed.
							if ( ( '' === $finallastnames || null === $finallastnames ) && ( '' === $finalfirstnames || null === $finalfirstnames ) && false !== stripos( $origauthorname, $titlevalue ) && false === stripos( $origauthorname, ',' ) ) {
								$tempname = str_replace( $titlevalue, '', $origauthorname );
								$tempname = rtrim( $tempname, ' ' );
								$title    = $titlevalue;

								// Now split up first/last names.
								$finalfirstnames = implode( ' ', explode( ' ', $tempname, -1 ) ) . ' ' . $titlevalue;
								$temp            = explode( ' ', strrev( $tempname ), 2 );
								$finallastnames  = strrev( $temp[0] );
							}
						}

						// Now if the Author's name does not contain a comma or a title...
						foreach ( $title_array as $titlekey => $titlevalue ) {
							// If author name does not have a title in it, and does not have a comma (indicating multiple authors), then proceed.
							if ( ( '' === $finallastnames || null === $finallastnames ) && ( '' === $finalfirstnames || null === $finalfirstnames ) && false === stripos( $origauthorname, $titlevalue ) && false === stripos( $origauthorname, ',' ) ) {

								// Now split up first/last names.
								$finalfirstnames = implode( ' ', explode( ' ', $origauthorname, -1 ) );
								$temp            = explode( ' ', strrev( $origauthorname ), 2 );
								$finallastnames  = strrev( $temp[0] );
							}
						}

						// Now update every row in the default table with our new author first name and author last name values.
						$data = array(
							'authorfirst' => $finalfirstnames,
							'authorlast'  => $finallastnames,
						);

						$format              = array( '%s', '%s' );
						$where               = array( 'ID' => $value->ID );
						$where_format        = array( '%d' );
						$admin_notice_result = $wpdb->update( $table_name_default, $data, $where, $format, $where_format );

					}
				}
			}
		}

		/**
		 *  Function for taking Authors from existing Author column, splitting the name up, and adding to the new authorfirst and authorlast columns in all dynamic, user-created tables.
		 */
		public function wpbooklist_add_author_first_last_dynamic_table() {

			global $wpdb;

			// Modify any existing custom libraries - both the book data and the settings data.
			$table_dyna          = $wpdb->prefix . "wpbooklist_jre_list_dynamic_db_names";
			$user_created_tables = $wpdb->get_results( "SELECT * FROM $table_dyna" );
			foreach ( $user_created_tables as $utable ) {

				// If version number does not match the current version number found in wpbooklist.php.
				if ( WPBOOKLIST_VERSION_NUM !== $this->version ) {

					// Modifying the default WPBookList table First, then any possible user-created dynamic tables.
					$table_name_default = $wpdb->prefix . "wpbooklist_jre_" . $utable->user_table_name;

					// If the two new Author columns do exist...
					if ( 0 !== $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'authorfirst'" ) && 0 !== $wpdb->query( "SHOW COLUMNS FROM `$table_name_default` LIKE 'authorlast'" ) ) {

						$book_array = $wpdb->get_results( "SELECT * FROM $table_name_default" );
						$nonamearray = array();
						foreach ( $book_array as $key => $value ) {
							// Building array of titles to look for in author's names.
							$title_array = array(
								'Jr.',
								'Ph.D.',
								'Mr.',
								'Mrs.',
							);

							$origauthorname  = $value->author;
							$title           = '';
							$finallastnames  = '';
							$finalfirstnames = '';

							// First let's handle names with commas, which we'll assume indicates multiple authors.
							if ( false !== strpos( $origauthorname, ',' ) && ( '' === $finallastnames || null === $finallastnames ) && ( '' === $finalfirstnames || null === $finalfirstnames ) ) {
								$origauthorcommaarray = explode( ',', $origauthorname );

								$lastnamecolonstring  = '';
								$firstnamecolonstring = '';

								foreach ( $origauthorcommaarray as $key2 => $individual ) {

									// First let's remove troublesome things like Ph.D., Jr., etc, and save them to be added back to end of the name.
									foreach ( $title_array as $titlekey => $titlevalue ) {
										if ( false !== stripos( $individual, $titlevalue ) ) {
											$individual = str_replace( $titlevalue, '', $individual );
											$individual = rtrim( $individual, ' ' );
											$title      = $titlevalue;
										}
									}

									// Explode by last space in name.
									$firstname = implode( ' ', explode( ' ', $individual, -1 ) );
									$temp      = explode( ' ', strrev( $individual ), 2 );
									$lastname  = strrev( $temp[0] );

									$lastnamecolonstring = $lastnamecolonstring . ';' . $lastname;

									if ( '' !== $title && null !== $title ) {
										$firstnamecolonstring = $firstnamecolonstring . ';' . $firstname . ' ' . $title;
									} else {
										$firstnamecolonstring = $firstnamecolonstring . ';' . $firstname;
									}
								}

								// Trim left spaces and ;.
								$lastnamecolonstring = ltrim( $lastnamecolonstring, ' ' );
								$lastnamecolonstring = ltrim( $lastnamecolonstring, ';' );

								// Trim left spaces and ;.
								$firstnamecolonstring = ltrim( $firstnamecolonstring, ' ' );
								$firstnamecolonstring = ltrim( $firstnamecolonstring, ';' );

								// Now build finalfirstname and finallastname string for the two new db columns.
								$finallastnames  = $lastnamecolonstring;
								$finalfirstnames = $firstnamecolonstring;
							}

							// Next we'll handle the names of single authors who may have a title in their name.
							foreach ( $title_array as $titlekey => $titlevalue ) {

								// If author name has a title in it, and does not have a comma (indicating multiple authors), then proceed.
								if ( ( '' === $finallastnames || null === $finallastnames ) && ( '' === $finalfirstnames || null === $finalfirstnames ) && false !== stripos( $origauthorname, $titlevalue ) && false === stripos( $origauthorname, ',' ) ) {
									$tempname = str_replace( $titlevalue, '', $origauthorname );
									$tempname = rtrim( $tempname, ' ' );
									$title    = $titlevalue;

									// Now split up first/last names.
									$finalfirstnames = implode( ' ', explode( ' ', $tempname, -1 ) ) . ' ' . $titlevalue;
									$temp            = explode( ' ', strrev( $tempname ), 2 );
									$finallastnames  = strrev( $temp[0] );
								}
							}

							// Now if the Author's name does not contain a comma or a title...
							foreach ( $title_array as $titlekey => $titlevalue ) {
								// If author name does not have a title in it, and does not have a comma (indicating multiple authors), then proceed.
								if ( ( '' === $finallastnames || null === $finallastnames ) && ( '' === $finalfirstnames || null === $finalfirstnames ) && false === stripos( $origauthorname, $titlevalue ) && false === stripos( $origauthorname, ',' ) ) {

									// Now split up first/last names.
									$finalfirstnames = implode( ' ', explode( ' ', $origauthorname, -1 ) );
									$temp            = explode( ' ', strrev( $origauthorname ), 2 );
									$finallastnames  = strrev( $temp[0] );
								}
							}

							// Now update every row in the default table with our new author first name and author last name values.
							$data = array(
								'authorfirst' => $finalfirstnames,
								'authorlast'  => $finallastnames,
							);

							$format              = array( '%s', '%s' );
							$where               = array( 'ID' => $value->ID );
							$where_format        = array( '%d' );
							$admin_notice_result = $wpdb->update( $table_name_default, $data, $where, $format, $where_format );

						}
					}
				}
			}
		}

		/**
		 *  Function to create the WPBookList Basic User Role.
		 */
		public function wpbooklist_add_wpbooklist_basic_user_role() {

			// If version number does not match the current version number found in wpbooklist.php.
			if ( WPBOOKLIST_VERSION_NUM !== $this->version ) {
				require_once CLASS_UTILITIES_DIR . 'class-wpbooklist-utilities-accesscheck.php';
				$this->access          = new WPBookList_Utilities_Accesscheck();
				$this->currentwpbluser = $this->access->wpbooklist_accesscheck_create_role( 'WPBookList Basic User' );

			}
		}

		/**
		 * Create new WPBookList User on update based on logged-in user.
		 */
		public function wpbooklist_create_wpbooklist_user_on_plugin_update() {

			// If version number does not match the current version number found in wpbooklist.php.
			if ( WPBOOKLIST_VERSION_NUM !== $this->version ) {

				global $wpdb;

				// Set the date.
				require_once CLASS_UTILITIES_DIR . 'class-wpbooklist-utilities-date.php';
				$utilities_date = new WPBookList_Utilities_Date();
				$this->date     = $utilities_date->wpbooklist_get_date_via_current_time( 'mysql' );

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
				} else {

					// If we already have a SuperAdmin, then add this user with a role of null, if they don't already exist.
					$current_user = wp_get_current_user();
					if ( ! $current_user->exists() ) {
						return;
					}

					$regularadmin = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_users_table WHERE wpuserid = ' . $current_user->ID );

					// add this user if they don't already exist. Limit them to the Default Library, and prevent them from making any Display or Setting changes.
					if ( null === $regularadmin ) {

						// Create the permissions string.
						$permissions = 'Yes-Yes-Yes-No-No';

						$users_save_array = array(
							'firstname'    => $current_user->user_firstname,
							'lastname'     => $current_user->user_lastname,
							'datecreated'  => $this->date,
							'wpuserid'     => $current_user->ID,
							'email'        => $current_user->user_email,
							'username'     => $current_user->user_email,
							'role'         => null,
							'permissions'  => $permissions,
							'libraries'    => '-wp_wpbooklist_jre_saved_book_log',
							'profileimage' => get_avatar_url( $current_user->ID ),
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
		 *  Function to update the version number.
		 */
		public function wpbooklist_update_version_number_function() {

			// If version number does not match the current version number found in wpbooklist.php.
			if ( WPBOOKLIST_VERSION_NUM !== $this->version ) {

				global $wpdb;
				$table_name = $wpdb->prefix . 'wpbooklist_jre_user_options';

				// Update verison number.
				$data = array(
					'version' => WPBOOKLIST_VERSION_NUM,
				);

				$format       = array( '%s' );
				$where        = array( 'ID' => 1 );
				$where_format = array( '%d' );
				$wpdb->update( $table_name, $data, $where, $format, $where_format );
			}
		}
	}
endif;
