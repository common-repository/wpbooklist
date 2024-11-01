<?php
/**
 * WordPress Book List
 *
 * @package     WordPress Book List
 * @author      Jake Evans
 * @copyright   2018 Jake Evans
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: WordPress Book List
 * Plugin URI: https://www.jakerevans.com
 * Description: For authors, publishers, librarians, and book-lovers alike - use it to sell your books, record and catalog your library, and more!
 * Version: 6.2.1
 * Author: Jake Evans
 * Text Domain: wpbooklist
 * Author URI: https://www.jakerevans.com
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;

/* REQUIRE STATEMENTS */
	require_once 'includes/class-wpbooklist-general-functions.php';
	require_once 'includes/class-wpbooklist-ajax-functions.php';
	require_once 'includes/classes/rest/class-wpbooklist-rest-functions.php';
	require_once 'includes/classes/storytime/class-wpbooklist-storytime.php';
/* END REQUIRE STATEMENTS */

/* CONSTANT DEFINITIONS */

// Root plugin folder directory.
if ( ! defined( 'WPBOOKLIST_VERSION_NUM' ) ) {
	define( 'WPBOOKLIST_VERSION_NUM', '6.2.1' );
}

// Root plugin folder directory.
define( 'ROOT_DIR', plugin_dir_path( __FILE__ ) );

// Root WordPress Plugin Directory - this conditional is to accomodate the wpbooklist.com website.
if ( false !== stripos( plugin_dir_path( __FILE__ ), '/wpbooklist.com' ) ) {
	define( 'ROOT_WP_PLUGINS_DIR', str_replace( '/wpbooklist/', '', plugin_dir_path( __FILE__ ) . '/' ) );
} else {
	define( 'ROOT_WP_PLUGINS_DIR', str_replace( '/wpbooklist', '', plugin_dir_path( __FILE__ ) ) );
}

	// Root plugin folder URL .
	define( 'ROOT_URL', plugins_url() . '/wpbooklist/' );

	// Quotes Directory.
	define( 'QUOTES_DIR', ROOT_DIR . 'quotes/' );

	// Quotes URL.
	define( 'QUOTES_URL', ROOT_URL . 'quotes/' );

	// Root JavaScript Directory.
	define( 'JAVASCRIPT_URL', ROOT_URL . 'assets/js/' );

	// Root SOUNDS Directory.
	define( 'SOUNDS_URL', ROOT_URL . 'assets/sounds/' );

	// Root Classes Directory.
	define( 'CLASS_DIR', ROOT_DIR . 'includes/classes/' );

	// Root REST Classes Directory.
	define( 'CLASS_REST_DIR', ROOT_DIR . 'includes/classes/rest/' );

	// Root Users Classes Directory.
	define( 'CLASS_USERS_DIR', ROOT_DIR . 'includes/classes/users/' );

	// Root Storytime Classes Directory.
	define( 'CLASS_STORYTIME_DIR', ROOT_DIR . 'includes/classes/storytime/' );

	// Root Compatability Classes Directory.
	define( 'CLASS_COMPAT_DIR', ROOT_DIR . 'includes/classes/compat/' );

	// Root Book Classes Directory.
	define( 'CLASS_BOOK_DIR', ROOT_DIR . 'includes/classes/book/' );

	// Root Translations Directory.
	define( 'CLASS_TRANSLATIONS_DIR', ROOT_DIR . 'includes/classes/translations/' );

	// Root Transients Directory.
	define( 'CLASS_TRANSIENTS_DIR', ROOT_DIR . 'includes/classes/transients/' );

	// Root Page Directory.
	define( 'CLASS_PAGE_DIR', ROOT_DIR . 'includes/classes/page/' );

	// Root Post Directory.
	define( 'CLASS_POST_DIR', ROOT_DIR . 'includes/classes/post/' );

	// Root Backup Directory.
	define( 'CLASS_BACKUP_DIR', ROOT_DIR . 'includes/classes/backup/' );

	// Root Utilities directory.
	define( 'CLASS_UTILITIES_DIR', ROOT_DIR . 'includes/classes/utilities/' );

	// Root Image URL .
	define( 'ROOT_IMG_URL', ROOT_URL . 'assets/img/' );

	// Root Image Icons URL .
	define( 'ROOT_IMG_ICONS_URL', ROOT_URL . 'assets/img/icons/' );

	// Root CSS URL .
	define( 'ROOT_CSS_URL', ROOT_URL . 'assets/css/' );

	// Root JS URL .
	define( 'ROOT_JS_URL', ROOT_URL . 'assets/js/' );

	// Root UI directory.
	define( 'ROOT_INCLUDES_UI', ROOT_DIR . 'includes/ui/' );

	// Root UI Admin directory.
	define( 'ROOT_INCLUDES_UI_ADMIN_DIR', ROOT_DIR . 'includes/ui/admin/' );

	// Define the Uploads base directory.
	$uploads     = wp_upload_dir();
	$upload_path = $uploads['basedir'];
	define( 'UPLOADS_BASE_DIR', $upload_path . '/' );

	// Define the Uploads base URL.
	$upload_url = $uploads['baseurl'];
	define( 'UPLOADS_BASE_URL', $upload_url . '/' );

	// Define the Library Stylepaks base directory.
	define( 'LIBRARY_STYLEPAKS_UPLOAD_DIR', UPLOADS_BASE_DIR . 'wpbooklist/stylepaks/library/' );

	// Define the Library Stylepaks base url.
	define( 'LIBRARY_STYLEPAKS_UPLOAD_URL', UPLOADS_BASE_URL . 'wpbooklist/stylepaks/library/' );

	// Define the Posts Stylepaks base directory.
	define( 'POST_TEMPLATES_UPLOAD_DIR', UPLOADS_BASE_DIR . 'wpbooklist/templates/posts/' );

	// Define the Posts Stylepaks base url.
	define( 'POST_TEMPLATES_UPLOAD_URL', UPLOADS_BASE_URL . 'wpbooklist/templates/posts/' );

	// Define the Pages Stylepaks base directory.
	define( 'PAGE_TEMPLATES_UPLOAD_DIR', UPLOADS_BASE_DIR . 'wpbooklist/templates/pages/' );

	// Define the Pages Stylepaks base url.
	define( 'PAGE_TEMPLATES_UPLOAD_URL', UPLOADS_BASE_URL . 'wpbooklist/templates/pages/' );

	// Define the Library DB backups base directory.
	define( 'LIBRARY_DB_BACKUPS_UPLOAD_DIR', UPLOADS_BASE_DIR . 'wpbooklist/backups/library/db/' );

	// Define the Library DB backups base directory.
	define( 'LIBRARY_DB_BACKUPS_UPLOAD_URL', UPLOADS_BASE_URL . 'wpbooklist/backups/library/db/' );

	// Define the page templates base directory.
	define( 'PAGE_POST_TEMPLATES_DEFAULT_DIR', ROOT_DIR . 'includes/templates/' );

	// Define the edit page offset.
	define( 'EDIT_PAGE_OFFSET', 100 );

	// Nonces array.
	define( 'WPBOOKLIST_NONCES_ARRAY',
		wp_json_encode(array(
			'adminnonce2'  => 'wpbooklist_dashboard_add_book_action_callback',
			'adminnonce3'  => 'wpbooklist_show_book_in_colorbox_action_callback',
			'adminnonce4'  => 'wpbooklist_new_library_action_callback',
			'adminnonce5'  => 'wpbooklist_delete_library_action_callback',
			'adminnonce6'  => 'wpbooklist_dashboard_save_library_display_options_action_callback',
			'adminnonce7'  => 'wpbooklist_dashboard_save_page_display_options_action_callback',
			'adminnonce8'  => 'wpbooklist_dashboard_save_post_display_options_action_callback',
			'adminnonce9'  => 'wpbooklist_change_library_display_options_action_callback',
			'adminnonce10' => 'wpbooklist_edit_book_show_form_action_callback',
			'adminnonce11' => 'wpbooklist_edit_book_pagination_action_callback',
			'adminnonce12' => 'wpbooklist_edit_book_switch_lib_action_callback',
			'adminnonce13' => 'wpbooklist_edit_book_search_action_callback',
			'adminnonce14' => 'wpbooklist_edit_book_actual_action_callback',
			'adminnonce15' => 'wpbooklist_delete_book_action_callback',
			'adminnonce16' => 'wpbooklist_user_apis_action_callback',
			'adminnonce17' => 'wpbooklist_upload_new_stylepak_action_callback',
			'adminnonce18' => 'wpbooklist_assign_stylepak_action_callback',
			'adminnonce19' => 'wpbooklist_upload_new_post_template_action_callback',
			'adminnonce20' => 'wpbooklist_assign_post_template_action_callback',
			'adminnonce21' => 'wpbooklist_upload_new_page_template_action_callback',
			'adminnonce22' => 'wpbooklist_assign_page_template_action_callback',
			'adminnonce23' => 'wpbooklist_create_db_library_backup_action_callback',
			'adminnonce24' => 'wpbooklist_restore_db_library_backup_action_callback',
			'adminnonce25' => 'wpbooklist_create_csv_action_callback',
			'adminnonce26' => 'wpbooklist_amazon_localization_action_callback',
			'adminnonce27' => 'wpbooklist_delete_all_books_in_library_action_callback',
			'adminnonce28' => 'wpbooklist_delete_all_books_pages_and_posts_action_callback',
			'adminnonce29' => 'wpbooklist_delete_all_checked_books_action_callback',
			'adminnonce30' => 'wpbooklist_jre_dismiss_prem_notice_forever_action',
			'adminnonce31' => 'wpbooklist_reorder_action_callback',
			'adminnonce32' => 'wpbooklist_exit_results_action_callback',
			'adminnonce33' => 'wpbooklist_storytime_select_category_action_callback',
			'adminnonce34' => 'wpbooklist_storytime_get_story_action_callback',
			'adminnonce35' => 'wpbooklist_storytime_expand_browse_action_callback',
			'adminnonce36' => 'wpbooklist_storytime_save_settings_action_callback',
			'adminnonce37' => 'wpbooklist_delete_story_action_callback',
			'adminnonce38' => 'wpbooklist_storytime_select_category_action_callback',
			'adminnonce39' => 'wpbooklist_storytime_get_story_action_callback',
			'adminnonce40' => 'wpbooklist_storytime_expand_browse_action_callback',
			'adminnonce41' => 'wpbooklist_seed_book_form_autocomplete_action_callback',
			'adminnonce42' => 'wpbooklist_dashboard_edit_book_action_callback',
			'adminnonce43' => 'wpbooklist_get_library_view_display_options_action_callback',
			'adminnonce44' => 'wpbooklist_dashboard_save_book_display_options_action_callback',
			'adminnonce45' => 'wpbooklist_get_post_display_options_action_callback',
			'adminnonce46' => 'wpbooklist_get_page_display_options_action_callback',
			'adminnonce47' => 'wpbooklist_dashboard_create_wp_user_action_callback',
			'adminnonce48' => 'wpbooklist_save_user_data_action_callback',
			'adminnonce49' => 'wpbooklist_edit_user_form_action_callback',
			'adminnonce50' => 'wpbooklist_edit_user_data_action_callback',
			'adminnonce51' => 'wpbooklist_delete_user_data_action_callback',
			'adminnonce52' => 'wpbooklist_delete_all_transients_action_callback',
		))
	);

/* END OF CONSTANT DEFINITIONS */

/* MISC. INCLUSIONS & DEFINITIONS */

	// Parse the wpbooklistconfig file.
	$config_array = parse_ini_file( 'wpbooklistconfig.ini' );

	// Get the default admin message for inclusion into database.
	define( 'ADMIN_MESSAGE', $config_array['initial_admin_message'] );

	// Loading textdomain.
	load_plugin_textdomain( 'wpbooklist', false, ROOT_DIR . 'languages' );

/* END MISC. INCLUSIONS & DEFINITIONS */


/* CLASS INSTANTIATIONS */

	// Call the class found in wpbooklist-functions.php.
	$wp_book_list_general_functions = new WPBookList_General_Functions();

	// Call the class found in wpbooklist-functions.php.
	$wp_book_list_ajax_functions = new WPBookList_Ajax_Functions();

	// Call the class found in class-wpbooklist-rest-functions.php.
	$wp_book_list_rest_functions = new WPBookList_Rest_Functions();

	// Call the class found in class-wpbooklist-rest-functions.php.
	$wp_book_list_storytime = new WPBookList_StoryTime( null, null, null, null );


/* END CLASS INSTANTIATIONS */

/* FUNCTIONS FOUND IN CLASS-WPBOOKLIST-GENERAL-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */

	// For the admin pages.
	add_action( 'admin_menu', array( $wp_book_list_general_functions, 'wpbooklist_jre_my_admin_menu' ) );

	// Adding the function that will take our WPHEALTHTRACKER_NONCES_ARRAY Constant from below and create actual nonces to be passed to Javascript functions.
	add_action( 'init', array( $wp_book_list_general_functions, 'wpbooklist_jre_create_nonces' ) );

	// Adding Ajax library.
	add_action( 'wp_head', array( $wp_book_list_general_functions, 'wpbooklist_jre_prem_add_ajax_library' ) );

	// Registers table names.
	add_action( 'init', array( $wp_book_list_general_functions, 'wpbooklist_jre_register_table_name' ) );

	// Creates the WPBookList Users table and the default WPBookList User.
	register_activation_hook( __FILE__, array( $wp_book_list_general_functions, 'wpbooklist_add_user_table' ) );

	// Records the user's url upon activation.
	register_activation_hook( __FILE__, array( $wp_book_list_general_functions, 'wpbooklist_jre_record_user_url' ) );

	// Creates basic WPBookList User role on activation.
	register_activation_hook( __FILE__, array( $wp_book_list_general_functions, 'wpbooklist_add_wpbooklist_role_on_plugin_activation' ) );

	// Creates new WPBookList User on plugin activation, with info of currently logged-in user.
	register_activation_hook( __FILE__, array( $wp_book_list_general_functions, 'wpbooklist_create_wpbooklist_user_on_plugin_activation' ) );

	// Creates default table upon activation.
	register_activation_hook( __FILE__, array( $wp_book_list_general_functions, 'wpbooklist_jre_create_default_lib' ) );

	// Creates the one master display options table.
	register_activation_hook( __FILE__, array( $wp_book_list_general_functions, 'wpbooklist_jre_create_superadmin_display_options_table' ) );

	// Creates the one master display options table.
	register_activation_hook( __FILE__, array( $wp_book_list_general_functions, 'wpbooklist_jre_create_superadmin_post_options_table' ) );

	// Creates the one master display options table.
	register_activation_hook( __FILE__, array( $wp_book_list_general_functions, 'wpbooklist_jre_create_superadmin_page_options_table' ) );

	// Creates the one master display options table.
	register_activation_hook( __FILE__, array( $wp_book_list_general_functions, 'wpbooklist_jre_record_dynamic_tablenames_table' ) );

	// Creates the one master display options table.
	register_activation_hook( __FILE__, array( $wp_book_list_general_functions, 'wpbooklist_jre_create_book_quotes_table' ) );

	// Creates the one master display options table.
	register_activation_hook( __FILE__, array( $wp_book_list_general_functions, 'wpbooklist_jre_create_page_post_log_table' ) );

	// Creates the one master display options table.
	register_activation_hook( __FILE__, array( $wp_book_list_general_functions, 'wpbooklist_jre_create_featured_books_table' ) );

	// Creates the one master display options table.
	register_activation_hook( __FILE__, array( $wp_book_list_general_functions, 'wpbooklist_jre_create_storytime_stories_table' ) );

	// Creates the one master display options table.
	register_activation_hook( __FILE__, array( $wp_book_list_general_functions, 'wpbooklist_jre_create_storytime_stories_settings_table' ) );

	// 
	register_deactivation_hook( __FILE__, array( $wp_book_list_general_functions, 'wpbooklist_deactivate_all_extensions' ) );






	// Adding the front-end library ui css file.
	add_action( 'wp_enqueue_scripts', array( $wp_book_list_general_functions, 'wpbooklist_jre_frontend_library_ui_default_style' ) );

	// Adding the admin css file.
	add_action( 'admin_enqueue_scripts', array( $wp_book_list_general_functions, 'wpbooklist_jre_admin_style' ) );

	// Adding the jQuery Autocomplete that ships with WordPress.
	add_action( 'admin_enqueue_scripts', array( $wp_book_list_general_functions, 'wpbooklist_jre_add_core_jquery_ui' ) );

	// Adding the admin js file.
	add_action( 'admin_enqueue_scripts', array( $wp_book_list_general_functions, 'wpbooklist_jre_admin_js' ) );

	// Adding the frontend js file.
	add_action( 'wp_enqueue_scripts', array( $wp_book_list_general_functions, 'wpbooklist_jre_frontend_js' ) );

	// Adding the posts & pages css file.
	add_action( 'wp_enqueue_scripts', array( $wp_book_list_general_functions, 'wpbooklist_jre_posts_pages_default_style' ) );

	// For admin messages.
	add_action( 'admin_notices', array( $wp_book_list_general_functions, 'wpbooklist_jre_admin_notice' ) );

	// Adding the front-end library shortcode.
	add_shortcode( 'wpbooklist_shortcode', array( $wp_book_list_general_functions, 'wpbooklist_jre_plugin_dynamic_shortcode_function' ) );

	// Shortcode that allows a book image to be placed on a page.
	add_shortcode( 'showbookcover', array( $wp_book_list_general_functions, 'wpbooklist_book_cover_shortcode' ) );

	// The function that determines which template to load for WPBookList Pages.
	add_filter( 'the_content', array( $wp_book_list_general_functions, 'wpbooklist_set_page_post_template' ) );

	// Function to run any code that is needed to modify the plugin between different versions.
	add_action( 'plugins_loaded', array( $wp_book_list_general_functions, 'wpbooklist_update_upgrade_function' ) );

		// Function to run any code that is needed to modify the plugin between different versions.
	add_action( 'plugins_loaded', array( $wp_book_list_general_functions, 'wpbooklist_hide_admin_bar_if_basic_wpbooklist_user' ) );

	// Adding the function that will allow the displaying of the Adminpointers when question marks are hovered over.
	add_action( 'admin_footer', array( $wp_book_list_general_functions, 'wpbooklist_jre_admin_pointers_javascript' ) );

	/*
	// Adding the form check js file.
	add_action( 'admin_enqueue_scripts', array( $wp_book_list_general_functions, 'wpbooklist_form_checks_js' ) );

	// Adding the jquery masked js file.
	add_action( 'admin_enqueue_scripts', array( $wp_book_list_general_functions, 'wpbooklist_jquery_masked_input_js' ) );

	// Code for adding the jquery readmore file for text blocks like description and notes
	add_action( 'wp_enqueue_scripts', array( $wp_book_list_general_functions, 'wpbooklist_jquery_readmore_js' ) );

	// Adding colorbox JS file on both front-end and dashboard
	add_action( 'admin_enqueue_scripts', array( $wp_book_list_general_functions, 'wpbooklist_jre_plugin_colorbox_script' ) );
	add_action( 'wp_enqueue_scripts', array( $wp_book_list_general_functions, 'wpbooklist_jre_plugin_colorbox_script' ) );

	// Adding AddThis sharing JS file
	add_action( 'admin_enqueue_scripts', array( $wp_book_list_general_functions, 'wpbooklist_jre_plugin_addthis_script' ) );
	add_action( 'wp_enqueue_scripts', array( $wp_book_list_general_functions, 'wpbooklist_jre_plugin_addthis_script' ) );
	*/



/* END OF FUNCTIONS FOUND IN CLASS-WPBOOKLIST-GENERAL-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */

/* FUNCTIONS FOUND IN CLASS-WPBOOKLIST-AJAX-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */

	// For adding a book from the admin dashboard.
	add_action( 'wp_ajax_wpbooklist_dashboard_add_book_action', array( $wp_book_list_ajax_functions, 'wpbooklist_dashboard_add_book_action_callback' ) );

	// For adding a book from the frontend, or anywhere else a user could access the 'Add a Book' form while not being logged in.
	add_action( 'wp_ajax_nopriv_wpbooklist_dashboard_add_book_action', array( $wp_book_list_ajax_functions, 'wpbooklist_dashboard_add_book_action_callback' ) );

	// For creating a WordPress User.
	add_action( 'wp_ajax_wpbooklist_dashboard_create_wp_user_action', array( $wp_book_list_ajax_functions, 'wpbooklist_dashboard_create_wp_user_action_callback' ) );

	// For getting the 'Edit User' form
	add_action( 'wp_ajax_wpbooklist_edit_user_form_action', array( $wp_book_list_ajax_functions, 'wpbooklist_edit_user_form_action_callback' ) );

	// For saving user data.
	add_action( 'wp_ajax_wpbooklist_save_user_data_action', array( $wp_book_list_ajax_functions, 'wpbooklist_save_user_data_action_callback' ) );

	// For deleting user data.
	add_action( 'wp_ajax_wpbooklist_delete_user_data_action', array( $wp_book_list_ajax_functions, 'wpbooklist_delete_user_data_action_callback' ) );

	// For editing WPBookList Basic User data.
	add_action( 'wp_ajax_wpbooklist_edit_user_data_action', array( $wp_book_list_ajax_functions, 'wpbooklist_edit_user_data_action_callback' ) );

	// For editing a book from the admin dashboard.
	add_action( 'wp_ajax_wpbooklist_dashboard_edit_book_action', array( $wp_book_list_ajax_functions, 'wpbooklist_dashboard_edit_book_action_callback' ) );

	// For the saving of edits to existing books.
	//add_action( 'wp_ajax_wpbooklist_edit_book_actual_action', array( $wp_book_list_ajax_functions, 'wpbooklist_edit_book_actual_action_callback' ) );

	add_action( 'wp_ajax_wpbooklist_show_book_in_colorbox_action', array( $wp_book_list_ajax_functions, 'wpbooklist_show_book_in_colorbox_action_callback' ) );

	add_action( 'wp_ajax_nopriv_wpbooklist_show_book_in_colorbox_action', array( $wp_book_list_ajax_functions, 'wpbooklist_show_book_in_colorbox_action_callback' ) );

	// For creating custom libraries.
	add_action( 'wp_ajax_wpbooklist_new_library_action', array( $wp_book_list_ajax_functions, 'wpbooklist_new_library_action_callback' ) );

	// For deleting all WPBookList Transients.
	add_action( 'wp_ajax_wpbooklist_delete_all_transients_action', array( $wp_book_list_ajax_functions, 'wpbooklist_delete_all_transients_action_callback' ) );

	// For deleting custom libraries.
	add_action( 'wp_ajax_wpbooklist_delete_library_action', array( $wp_book_list_ajax_functions, 'wpbooklist_delete_library_action_callback' ) );

	// For saving library display options.
	add_action( 'wp_ajax_wpbooklist_dashboard_save_library_display_options_action', array( $wp_book_list_ajax_functions, 'wpbooklist_dashboard_save_library_display_options_action_callback' ) );

	// For saving book display options.
	add_action( 'wp_ajax_wpbooklist_dashboard_save_book_display_options_action', array( $wp_book_list_ajax_functions, 'wpbooklist_dashboard_save_book_display_options_action_callback' ) );

	// For saving post display options.
	add_action( 'wp_ajax_wpbooklist_dashboard_save_post_display_options_action', array( $wp_book_list_ajax_functions, 'wpbooklist_dashboard_save_post_display_options_action_callback' ) );


	// For saving page display options.
	add_action( 'wp_ajax_wpbooklist_dashboard_save_page_display_options_action', array( $wp_book_list_ajax_functions, 'wpbooklist_dashboard_save_page_display_options_action_callback' ) );

	// Function for changing the Library on the Library tab of the Display Options menu.
	add_action( 'wp_ajax_wpbooklist_change_library_display_options_action', array( $wp_book_list_ajax_functions, 'wpbooklist_change_library_display_options_action_callback' ) );

	// For editing a book from the admin dashboard.
	add_action( 'wp_ajax_wpbooklist_edit_book_show_form_action', array( $wp_book_list_ajax_functions, 'wpbooklist_edit_book_show_form_action_callback' ) );

	// For handling the pagination of the 'Edit & Delete Books' tab.
	add_action( 'wp_ajax_wpbooklist_edit_book_pagination_action', array( $wp_book_list_ajax_functions, 'wpbooklist_edit_book_pagination_action_callback' ) );

	// For switching libraries on the 'Edit & Delete Books' tab.
	add_action( 'wp_ajax_wpbooklist_edit_book_switch_lib_action', array( $wp_book_list_ajax_functions, 'wpbooklist_edit_book_switch_lib_action_callback' ) );

	// For searching for a title to edit.
	add_action( 'wp_ajax_wpbooklist_edit_book_search_action', array( $wp_book_list_ajax_functions, 'wpbooklist_edit_book_search_action_callback' ) );

	// For the saving of edits to existing books.
	add_action( 'wp_ajax_wpbooklist_edit_book_actual_action', array( $wp_book_list_ajax_functions, 'wpbooklist_edit_book_actual_action_callback' ) );

	// For deleting a book from the 'Edit & Delete Books' tab.
	add_action( 'wp_ajax_wpbooklist_delete_book_action', array( $wp_book_list_ajax_functions, 'wpbooklist_delete_book_action_callback' ) );

	// For saving a user's own API keys.
	add_action( 'wp_ajax_wpbooklist_user_apis_action', array( $wp_book_list_ajax_functions, 'wpbooklist_user_apis_action_callback' ) );

	// For uploading a new StylePak after purchase.
	add_action( 'wp_ajax_wpbooklist_upload_new_stylepak_action', array( $wp_book_list_ajax_functions, 'wpbooklist_upload_new_stylepak_action_callback' ) );

	// For assigning a StylePak to a library.
	add_action( 'wp_ajax_wpbooklist_assign_stylepak_action', array( $wp_book_list_ajax_functions, 'wpbooklist_assign_stylepak_action_callback' ) );

	// For uploading a new Post Template to a library.
	add_action( 'wp_ajax_wpbooklist_upload_new_post_template_action', array( $wp_book_list_ajax_functions, 'wpbooklist_upload_new_post_template_action_callback' ) );

	// For uploading a new Page Template to a library.
	add_action( 'wp_ajax_wpbooklist_upload_new_page_template_action', array( $wp_book_list_ajax_functions, 'wpbooklist_upload_new_page_template_action_callback' ) );

	// For assigning a Post Template to a Post.
	add_action( 'wp_ajax_wpbooklist_assign_post_template_action', array( $wp_book_list_ajax_functions, 'wpbooklist_assign_post_template_action_callback' ) );

	// For assigning a Page Template to a Page.
	add_action( 'wp_ajax_wpbooklist_assign_page_template_action', array( $wp_book_list_ajax_functions, 'wpbooklist_assign_page_template_action_callback' ) );

	// For creating a backup of a Library.
	add_action( 'wp_ajax_wpbooklist_create_db_library_backup_action', array( $wp_book_list_ajax_functions, 'wpbooklist_create_db_library_backup_action_callback' ) );

	// For restoring a backup of a Library.
	add_action( 'wp_ajax_wpbooklist_restore_db_library_backup_action', array( $wp_book_list_ajax_functions, 'wpbooklist_restore_db_library_backup_action_callback' ) );

	// For creating a .csv file of ISBN/ASIN numbers.
	add_action( 'wp_ajax_wpbooklist_create_csv_action', array( $wp_book_list_ajax_functions, 'wpbooklist_create_csv_action_callback' ) );

	// For setting the Amazon Localization.
	add_action( 'wp_ajax_wpbooklist_amazon_localization_action', array( $wp_book_list_ajax_functions, 'wpbooklist_amazon_localization_action_callback' ) );

	// For deleting all books in library.
	add_action( 'wp_ajax_wpbooklist_delete_all_books_in_library_action', array( $wp_book_list_ajax_functions, 'wpbooklist_delete_all_books_in_library_action_callback' ) );

	// For deleting all books, pages, and posts in library.
	add_action( 'wp_ajax_wpbooklist_delete_all_books_pages_and_posts_action', array( $wp_book_list_ajax_functions, 'wpbooklist_delete_all_books_pages_and_posts_action_callback' ) );

	// For deleting all checked books.
	add_action( 'wp_ajax_wpbooklist_delete_all_checked_books_action', array( $wp_book_list_ajax_functions, 'wpbooklist_delete_all_checked_books_action_callback' ) );

	// For dismissing notice.
	add_action( 'wp_ajax_wpbooklist_jre_dismiss_prem_notice_forever_action', array( $wp_book_list_ajax_functions, 'wpbooklist_jre_dismiss_prem_notice_forever_action_callback' ) );

	// For reordering books.
	add_action( 'wp_ajax_wpbooklist_reorder_action', array( $wp_book_list_ajax_functions, 'wpbooklist_reorder_action_callback' ) );

	// For receiving user feedback upon deactivation & deletion.
	add_action( 'wp_ajax_wpbooklist_exit_results_action', array( $wp_book_list_ajax_functions, 'wpbooklist_exit_results_action_callback' ) );

	// For retrieving the WPBookList StoryTime Stories from the server when the 'Select a Category' drop-down changes.
	add_action( 'wp_ajax_wpbooklist_storytime_select_category_action', array( $wp_book_list_ajax_functions, 'wpbooklist_storytime_select_category_action_callback' ) );

	// For retreiving a WPBookList StoryTime Story from the server, once the user has selected one in the reader.
	add_action( 'wp_ajax_wpbooklist_storytime_get_story_action', array( $wp_book_list_ajax_functions, 'wpbooklist_storytime_get_story_action_callback' ) );

	// For expanding the 'Browse Stories' section again once a Story has already been selected.
	add_action( 'wp_ajax_wpbooklist_storytime_expand_browse_action', array( $wp_book_list_ajax_functions, 'wpbooklist_storytime_expand_browse_action_callback' ) );

	// Makes a call to get every single book saved on website to seed the Book form for Autocomplete stuff.
	add_action( 'wp_ajax_wpbooklist_seed_book_form_autocomplete_action', array( $wp_book_list_ajax_functions, 'wpbooklist_seed_book_form_autocomplete_action_callback' ) );

	// For saving the StoryTime Settings.
	add_action( 'wp_ajax_wpbooklist_storytime_save_settings_action', array( $wp_book_list_ajax_functions, 'wpbooklist_storytime_save_settings_action_callback' ) );

	// For deleting a StoryTime Settings.
	add_action( 'wp_ajax_wpbooklist_delete_story_action', array( $wp_book_list_ajax_functions, 'wpbooklist_delete_story_action_callback' ) );

	// Function to populate the Library View Display Options checkboxes.
	add_action( 'wp_ajax_wpbooklist_get_library_view_display_options_action', array( $wp_book_list_ajax_functions, 'wpbooklist_get_library_view_display_options_action_callback' ) );

	// Function to populate the Library View Display Options checkboxes.
	add_action( 'wp_ajax_wpbooklist_get_post_display_options_action', array( $wp_book_list_ajax_functions, 'wpbooklist_get_post_display_options_action_callback' ) );

	// Function to populate the Library View Display Options checkboxes.
	add_action( 'wp_ajax_wpbooklist_get_page_display_options_action', array( $wp_book_list_ajax_functions, 'wpbooklist_get_page_display_options_action_callback' ) );

/* END OF FUNCTIONS FOUND IN CLASS-WPBOOKLIST-AJAX-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */

/* FUNCTIONS FOUND IN CLASS-WPBOOKLIST-STORYTIME.PHP THAT APPLY PLUGIN-WIDE */

	// For the admin pages.
	add_action( 'admin_menu', array( $wp_book_list_storytime, 'wpbooklist_jre_storytime_admin_notice' ) );
	// Function that displays StoryTime on the front end.
	add_shortcode( 'wpbooklist_storytime', array( $wp_book_list_storytime, 'wpbooklist_storytime_shortcode' ) );

/* END OF FUNCTIONS FOUND IN CLASS-WPBOOKLIST-STORYTIME.PHP THAT APPLY PLUGIN-WIDE */



/* REST FUNCTIONS FOUND IN CLASS-WPBOOKLIST-REST-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */

	// For the REST API update for validating patreon.
	add_action( 'rest_api_init', function () {
	  register_rest_route( 'wpbooklist/v1', '/firstkey/(?P<firstkey>[a-z0-9\-]+)/secondkey/(?P<secondkey>[a-z0-9\-]+)', array(
	  	'callback' => function() {
	  					// Call the class found in class-wpbooklist-rest-functions.php.
						$wp_book_list_rest_functions = new WPBookList_Rest_Functions();
						$wp_book_list_rest_functions->wpbooklist_jre_storytime_patreon_validate_rest_api_notice;
					  },
	    'methods' => 'GET',
	  ) );
	});

/* END OF REST FUNCTIONS FOUND IN CLASS-WPBOOKLIST-REST-FUNCTIONS.PHP THAT APPLY PLUGIN-WIDE */