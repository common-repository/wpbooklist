<?php
/**
 * WPBookList Front-End Library UI Class - class-wpbooklist-frontend-library-ui.php
 *
 * @author   Jake Evans
 * @category Front-End UI
 * @package  Includes/UI
 * @version  6.1.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Front_End_Library_UI', false ) ) :

	/**
	 * WPBookList_Frontend_Library_UI Class.
	 */
	class WPBookList_Frontend_Library_UI {

		// One-off Class string variables.
		public $table                 = '';
		public $display_options_table = '';
		public $query_table           = '';
		public $quotes_table          = '';
		public $branding_table        = '';
		public $url_param_string      = '';
		public $total_book_count      = 0;
		public $total_books_filter    = 1;
		public $total_quotes_count    = 0;
		public $total_author_count    = 0;
		public $total_subject_count   = 0;
		public $total_country_count   = 0;
		public $library_actual_string = '';
		public $params_true           = false;
		public $action                = '';
		public $trans                 = '';


		// Dealing with Sort/Search/Filter/Pagination.
		public $sortby            = '';
		public $offset            = 0;
		public $booksonpage       = 12;
		public $searchbytitle     = '';
		public $searchbyauthor    = '';
		public $searchbycategory  = '';
		public $searchterm        = '';
		public $sortbyselected1   = '';
		public $sortbyselected2   = '';
		public $sortbyselected3   = '';
		public $sortbyselected4   = '';
		public $sortbyselected5   = '';
		public $sortbyselected6   = '';
		public $sortbyselected7   = '';
		public $sortbyselected8   = '';
		public $sortbyselected9   = '';
		public $pagination_string = '';
		public $filterauthor      = '';
		public $filtercategory    = '';
		public $filtersubject     = '';
		public $filtercountry     = '';
		public $filterflag        = false;
		public $filterpubyears    = '';


		// Query strings.
		public $final_query                 = '';
		public $search_query                = '';
		public $query_before_limit_offset   = '';
		public $prepare_before_limit_offset = '';

		// Stats variables.
		public $total_book_read_count          = 0;
		public $total_book_signed_count        = 0;
		public $total_book_first_edition_count = 0;
		public $total_pages_read_count         = 0;
		public $total_category_count           = 0;

		// Dealing with the Branding Extension.
		public $brandingtext1 = '';
		public $brandingtext2 = '';
		public $brandinglogo1 = '';
		public $brandinglogo2 = '';

		// All arrays.
		public $active_plugins                   = array();
		public $display_options_actual           = array();
		public $books_actual                     = array();
		public $library_prepare_array            = array();
		public $quotes_actual                    = array();
		public $final_author_array               = array();
		public $final_subject_array              = array();
		public $final_country_array              = array();
		public $final_category_array             = array();
		public $books_before_limit_offset_actual = array();

		/** Class Constructor.
		 *
		 *  @param string $which_table - The WPBookList table in question.
		 *  @param string $action - The action to take.
		 */
		public function __construct( $which_table, $action ) {

			// First set up some class-wide stuff.
			global $wpdb;
			$this->table  = $which_table;
			$this->action = $action;

			// Get Translations.
			require_once CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
			$this->trans = new WPBookList_Translations();
			$this->trans->trans_strings();

			// Require the Transients file.
			require_once CLASS_TRANSIENTS_DIR . 'class-wpbooklist-transients.php';
			$this->transients = new WPBookList_Transients();

			// Get active plugins to see if any extensions are in play.
			$this->active_plugins = (array) get_option( 'active_plugins', array() );
			if ( is_multisite() ) {
				// On the one multisite I have troubleshot, all plugins were merged into the $this->active_plugins variable, but the multisite plugins had an int value, not the actual name of the plugin, so, I had to build an array composed of the keys of the array that get_site_option('active_sitewide_plugins', array()) returned, and merge that.
				$multi_plugin_actual_name = array();
				$temp                     = get_site_option( 'active_sitewide_plugins', array() );
				foreach ( $temp as $key => $value ) {
					array_push( $multi_plugin_actual_name, $key );
				}

				$this->active_plugins = array_merge( $this->active_plugins, $multi_plugin_actual_name );
			}

			// Setting up some data for the Branding Extension.
			foreach ( $this->active_plugins as $key => $value ) {
				if ( 'wpbooklist-branding/wpbooklist-branding.php' === $value ) {
					$this->branding_table = $wpdb->prefix . 'wpbooklist_branding_table';
					$row                  = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->branding_table WHERE ID = %d", 1 ) );

					$this->brandingtext1 = $row->brandingtext1;
					$this->brandingtext2 = $row->brandingtext2;
					$this->brandinglogo1 = $row->brandinglogo1;
					$this->brandinglogo2 = $row->brandinglogo2;

				}
			}

			// Building the Display Options table name.
			if ( $wpdb->prefix . 'wpbooklist_jre_saved_book_log' === $this->table ) {
				$this->display_options_table = $wpdb->prefix . 'wpbooklist_jre_user_options';
			} else {

				$temp = explode( '_', $this->table );
				$temp = array_pop( $temp );

				$this->display_options_table = $wpdb->prefix . 'wpbooklist_jre_settings_' . strtolower( $temp );
			}

			// Getting all the Display Options from the table name we set just above.
			$this->display_options_actual = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->display_options_table WHERE ID = %d", 1 ) );

			// Setting how many books will be displayed per page, based on backend setting.
			if ( null !== $this->display_options_actual->booksonpage && 0 !== $this->display_options_actual->booksonpage && '0' !== $this->display_options_actual->booksonpage) {
				$this->booksonpage = $this->display_options_actual->booksonpage;
			}

			// Getting and Setting any and all Search/Sort/Filter stuff from URL.
			$this->set_url_param_variables();

			// Setting the default sort option, based on backend settings if set, which are over-ruled if there's a 'sortby' parameter in the URL.
			$this->set_sort_by_options();

			// Build the final Database query, run it, and get our array of books ( $this->books_actual).
			$this->build_db_query();

			// If a filter is in play in the URL, modify the $books_actual array we just created in $this->build_db_query();.
			if ( $this->filterflag ) {
				$this->filter_book_actual();
			}

			// Output default non-variable first bit of html.
			$this->output_beginning_html();

			// Output search if not hidden.
			if ( '1' !== $this->display_options_actual->hidesearch ) {
				$this->output_search();
			}
			// Output filter if not hidden.
			if ( '1' !== $this->display_options_actual->hidefilter ) {
				$this->build_filter_values();
				$this->output_filter_html();
			}

			// Output the closure of the sort and search elements.
			$this->output_close_sort_search();

			// Output Stats if not hidden.
			if ( '1' !== $this->display_options_actual->hidestats ) {
				$this->output_stats();
			}
			// Output Quote if not hidden.
			if ( '1' !== $this->display_options_actual->hidequote ) {
				$this->output_quote();
			}

			// Builds the actual Library the user ends up seeing.
			$this->build_library_actual();

			// Build the pagination HTML below each Library.
			$this->build_library_pagination();

			// Now we finally output the full UI.
			$this->output_library_actual();
		}

		/**
		 *  Handles the display of the Sort By options drop-down
		 */
		private function set_sort_by_options() {

			// If $this->sortby wasn't set to a value from the URL in the set_url_param_variables() function, then set it to the value selected in the backend, if one exists.
			if ( '' === $this->sortby ) {
				if ( null !== $this->display_options_actual->sortoption && '' !== $this->display_options_actual->sortoption ) {
					$this->sortby = $this->display_options_actual->sortoption;
				}
			}

			// These $this->sortbyselected variables are used to set the 'Sort By..' select input to the proper selected value.
			switch ( $this->sortby ) {
				case '':
					$this->sortbyselected1 = 'selected';
					break;
				case 'alphabeticallybytitle':
					$this->sortbyselected2 = 'selected';
					break;
				case 'alphabeticallybyauthorfirst':
					$this->sortbyselected3 = 'selected';
					break;
				case 'alphabeticallybyauthorlast':
					$this->sortbyselected4 = 'selected';
					break;
				case 'year_read':
					$this->sortbyselected5 = 'selected';
					break;
				case 'pages_desc':
					$this->sortbyselected6 = 'selected';
					break;
				case 'pages_asc':
					$this->sortbyselected7 = 'selected';
					break;
				case 'signed':
					$this->sortbyselected8 = 'selected';
					break;
				case 'first_edition':
					$this->sortbyselected9 = 'selected';
					break;
				default:
					$this->sortbyselected1 = 'selected';
					break;
			}
		}

		/**
		 *  Handles url parameters
		 */
		private function set_url_param_variables() {

			// Getting all URL parameters.
			$this->url_param_string = urldecode( http_build_query( array_merge( $_GET ) ) );

			$this->params_true_flag = false;
			$this->filterflag       = false;
			$this->searchflag       = false;
			$this->sortflag         = false;

			if ( '' !== $this->url_param_string ) {

				// Set a flag indicating there is some sort of URL parameter in existence.
				$this->params_true_flag = true;

				// Seeing if the querytable parameter exists.
				if ( false !== strpos( $this->url_param_string, 'querytable' ) ) {
					if ( isset( $_GET['querytable'] ) ) {
						$this->query_table = filter_var( wp_unslash( $_GET['querytable'] ), FILTER_SANITIZE_STRING );
					}
				}

				// This check is to ensure that the URL Search parameters are getting applied to the correct Library on the page - this allows for the search functionality to exist for each library on the same page, but not at the same time - when the user performs a search, all other libraries on the same page will revert back to their default results, i.e., the basic "SELECT * FROM $this->table".
				if ( $this->query_table === $this->table ) {

					// Seeing if the sortby parameter exists in $this->url_param_string.
					if ( false !== strpos( $this->url_param_string, 'filterauthor' ) ) {
						if ( isset( $_GET['filterauthor'] ) ) {
							$this->filterauthor = filter_var( wp_unslash( $_GET['filterauthor'] ), FILTER_SANITIZE_STRING );
						}

						// Setting the filter flag, indicating one of the filters is active.
						$this->filterflag = true;
					}

					if ( false !== strpos( $this->url_param_string, 'filtercategory' ) ) {
						if ( isset( $_GET['filtercategory'] ) ) {
							$this->filtercategory = filter_var( wp_unslash( $_GET['filtercategory'] ), FILTER_SANITIZE_STRING );
						}

						// Setting the filter flag, indicating one of the filters is active.
						$this->filterflag = true;
					}

					if ( false !== strpos( $this->url_param_string, 'filtersubject' ) ) {
						if ( isset( $_GET['filtersubject'] ) ) {
							$this->filtersubject = filter_var( wp_unslash( $_GET['filtersubject'] ), FILTER_SANITIZE_STRING );
						}

						// Setting the filter flag, indicating one of the filters is active.
						$this->filterflag = true;
					}

					if ( false !== strpos( $this->url_param_string, 'filtercountry' ) ) {
						if ( isset( $_GET['filtercountry'] ) ) {
							$this->filtercountry = filter_var( wp_unslash( $_GET['filtercountry'] ), FILTER_SANITIZE_STRING );
						}

						// Setting the filter flag, indicating one of the filters is active.
						$this->filterflag = true;
					}

					if ( false !== strpos( $this->url_param_string, 'filterpubyears' ) ) {
						if ( isset( $_GET['filterpubyears'] ) ) {
							$this->filterpubyears = filter_var( wp_unslash( $_GET['filterpubyears'] ), FILTER_SANITIZE_STRING );
						}

						// Setting the filter flag, indicating one of the filters is active.
						$this->filterflag = true;
					}

					// Seeing if the sortby parameter exists in $this->url_param_string.
					if ( false !== strpos( $this->url_param_string, 'sortby' ) ) {
						if ( isset( $_GET['sortby'] ) ) {
							$this->sortby = filter_var( wp_unslash( $_GET['sortby'] ), FILTER_SANITIZE_STRING );
						}

						// Setting the sort flag, indicating some kind of sorting is active.
						$this->sortflag = true;
					}

					// Seeing if the searchbytitle parameter exists.
					if ( false !== strpos( $this->url_param_string, 'searchbytitle' ) ) {
						if ( isset( $_GET['searchbytitle'] ) ) {
							$this->searchbytitle = filter_var( wp_unslash( $_GET['searchbytitle'] ), FILTER_SANITIZE_STRING );
						}
					}

					// Seeing if the searchbyauthor parameter exists.
					if ( false !== strpos( $this->url_param_string, 'searchbyauthor' ) ) {
						if ( isset( $_GET['searchbyauthor'] ) ) {
							$this->searchbyauthor = filter_var( wp_unslash( $_GET['searchbyauthor'] ), FILTER_SANITIZE_STRING );
						}
					}

					// Seeing if the searchbycategory parameter exists.
					if ( false !== strpos( $this->url_param_string, 'searchbycategory' ) ) {
						if ( isset( $_GET['searchbycategory'] ) ) {
							$this->searchbycategory = filter_var( wp_unslash( $_GET['searchbycategory'] ), FILTER_SANITIZE_STRING );
						}
					}

					// Seeing if the searchterm parameter exists.
					if ( false !== strpos( $this->url_param_string, 'searchterm' ) ) {
						if ( isset( $_GET['searchterm'] ) ) {
							$this->searchterm = filter_var( wp_unslash( $_GET['searchterm'] ), FILTER_SANITIZE_STRING );
						}

						// Setting the search flag, indicating a search is active.
						$this->searchflag = true;
					}

					// Seeing if the Offset parameter exists.
					if ( false !== strpos( $this->url_param_string, 'offset' ) ) {
						if ( isset( $_GET['offset'] ) ) {
							$this->offset = filter_var( wp_unslash( $_GET['offset'] ), FILTER_SANITIZE_NUMBER_INT );
						}
					}
				}
			}
		}

		/**
		 *  Builds the actual database query
		 */
		private function build_db_query() {

			global $wpdb;
			// Build basic query, if no search/filter/sort params are set.
			if ( '' === $this->url_param_string || null === $this->url_param_string ) {
				$this->final_query = "SELECT * FROM $this->table";
			} else {

				$this->final_query = "SELECT * FROM $this->table ";

				// First, let's build the query if a search term is set.
				if ( '' !== $this->searchterm ) {

					// Build a query if the Search section has all three values checked, and if nothing else is set.
					if ( ( ( '' !== $this->searchbyauthor && '' !== $this->searchbycategory && '' !== $this->searchbytitle ) && '' !== $this->searchterm ) && '' === $this->filterauthor && '' === $this->filtercategory && '' === $this->filtersubject && '' === $this->filtercountry && '' === $this->filterpubyears ) {

						$this->search_query = 'WHERE (title LIKE %s OR author LIKE %s OR category LIKE %s)';
						$this->final_query  = $this->final_query . $this->search_query;

						array_push( $this->library_prepare_array, '%' . $this->searchterm . '%', '%' . $this->searchterm . '%', '%' . $this->searchterm . '%' );

					}

					// Build a query if the Search section has just the title checked, and if nothing else is set.
					if ( ( ( '' !== $this->searchbytitle && '' === $this->searchbyauthor && '' === $this->searchbycategory ) && '' !== $this->searchterm ) && '' === $this->filterauthor && '' === $this->filtercategory && '' === $this->filtersubject && '' === $this->filtercountry && '' === $this->filterpubyears ) {

						$this->search_query = 'WHERE title LIKE %s';
						$this->final_query  = $this->final_query . $this->search_query;
						array_push( $this->library_prepare_array, '%' . $this->searchterm . '%' );

					}

					// Build a query if the Search section has just the author checked, and if nothing else is set.
					if ( ( ( '' === $this->searchbytitle && '' !== $this->searchbyauthor && '' === $this->searchbycategory ) && '' !== $this->searchterm ) && '' === $this->filterauthor && '' === $this->filtercategory && '' === $this->filtersubject && '' === $this->filtercountry && '' === $this->filterpubyears ) {

						$this->search_query = 'WHERE author LIKE %s';
						$this->final_query  = $this->final_query . $this->search_query;
						array_push( $this->library_prepare_array, '%' . $this->searchterm . '%' );

					}

					// Build a query if the Search section has just the category checked, and if nothing else is set.
					if ( ( ( '' === $this->searchbytitle && '' === $this->searchbyauthor && '' !== $this->searchbycategory ) && '' !== $this->searchterm ) && '' === $this->filterauthor && '' === $this->filtercategory && '' === $this->filtersubject && '' === $this->filtercountry && '' === $this->filterpubyears ) {

						$this->search_query = 'WHERE category LIKE %s';
						$this->final_query  = $this->final_query . $this->search_query;
						array_push( $this->library_prepare_array, '%' . $this->searchterm . '%' );

					}

					// Build a query if the Search section has the title and author checked, and if nothing else is set.
					if ( ( ( '' !== $this->searchbytitle && '' !== $this->searchbyauthor && '' === $this->searchbycategory ) && '' !== $this->searchterm ) && '' === $this->filterauthor && '' === $this->filtercategory && '' === $this->filtersubject && '' === $this->filtercountry && '' === $this->filterpubyears ) {

						$this->search_query = 'WHERE (title LIKE %s OR author LIKE %s)';
						$this->final_query  = $this->final_query . $this->search_query;
						array_push( $this->library_prepare_array, '%' . $this->searchterm . '%', '%' . $this->searchterm . '%' );

					}

					// Build a query if the Search section has the title and category checked, and if nothing else is set.
					if ( ( ( '' !== $this->searchbytitle && '' === $this->searchbyauthor && '' !== $this->searchbycategory ) && '' !== $this->searchterm ) && '' === $this->filterauthor && '' === $this->filtercategory && '' === $this->filtersubject && '' === $this->filtercountry && '' === $this->filterpubyears ) {

						$this->search_query = 'WHERE (title LIKE %s OR category LIKE %s)';
						$this->final_query  = $this->final_query . $this->search_query;
						array_push( $this->library_prepare_array, '%' . $this->searchterm . '%', '%' . $this->searchterm . '%' );

					}

					// Build a query if the Search section has the author and category checked, and if nothing else is set.
					if ( ( ( '' === $this->searchbytitle && '' !== $this->searchbyauthor && '' !== $this->searchbycategory ) && '' !== $this->searchterm ) && '' === $this->filterauthor && '' === $this->filtercategory && '' === $this->filtersubject && '' === $this->filtercountry && '' === $this->filterpubyears ) {

						$this->search_query = 'WHERE (author LIKE %s OR category LIKE %s)';
						$this->final_query  = $this->final_query . $this->search_query;
						array_push( $this->library_prepare_array, '%' . $this->searchterm . '%', '%' . $this->searchterm . '%' );

					}
				}
			}

			// Getting total search/filter book count returned from query, BEFORE appending the LIMIT set on the 'Display Options' page.
			if ( '' !== $this->search_query ) {
				$this->total_books_filter = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $this->table " . $this->search_query, $this->library_prepare_array ) );
			}

			// A switch for the 'Sort By' param. Modify the query if a search temr is in play, by changing the WHERE clause to an AND.
			if ( false !== stripos( $this->final_query, 'WHERE' ) ) {
				$clause = 'AND';
			} else {
				$clause = 'WHERE';
			}

			switch ( $this->sortby ) {
				case 'alphabeticallybytitle':
					$this->final_query = $this->final_query . ' ' . $clause . " title != 'NULL' AND title != '' ORDER by title ";
					break;
				case 'alphabeticallybyauthorfirst':
					$this->final_query = $this->final_query . ' ' . $clause . " author != 'NULL' AND author != '' ORDER by author ";
					break;
				case 'alphabeticallybyauthorlast':
					$this->final_query = $this->final_query . ' ' . $clause . " authorlast != 'NULL' AND authorlast != '' ORDER by authorlast ";
					break;
				case 'year_read':
					$this->final_query = $this->final_query . ' ' . $clause . " date_finished != 'undefined-undefined-' AND date_finished != 'NULL' AND finished != 'false' AND date_finished != '' ORDER by date_finished ";
					break;
				case 'pages_asc':
					$this->final_query = $this->final_query . ' ORDER by pages ';
					break;
				case 'pages_desc':
					$this->final_query = $this->final_query . ' ORDER by pages DESC';
					break;
				case 'signed':
					$this->final_query = $this->final_query . ' ' . $clause . " signed='true'";
					break;
				case 'first_edition':
					$this->final_query = $this->final_query . ' ' . $clause . " edition != 'NULL' ORDER by edition";
					break;
				default:
					break;
			}

			// Getting total number of books in this entire library, after the sort filter.
			$tempquery = str_replace( 'SELECT * FROM', 'SELECT COUNT(*) FROM', $this->final_query );
			if ( '' !== $tempquery ) {

				// Trim any whitespace.
				$tempquery = rtrim( $tempquery );
				$tempquery = ltrim( $tempquery );

				// Introduce this check to make sure wpdb->prepare has correct number of placeholders.
				if ( 'SELECT COUNT(*) FROM ' . $this->table === $tempquery ) {

					// We don't even need prepare if this is the case.
					$this->total_book_count = $wpdb->get_var( $tempquery );
				} else {

					if ( count( $this->library_prepare_array ) > 0 ) {
						$this->total_book_count = $wpdb->get_var( $wpdb->prepare( $tempquery, $this->library_prepare_array ) );
					} else {
						$this->total_book_count = $wpdb->get_var( $tempquery );
					}
				}
			}

			// Setting the query before offset/pagination, specifically for populating the Filter drop-downs correctly.
			$this->query_before_limit_offset   = $this->final_query;
			$this->prepare_before_limit_offset = $this->library_prepare_array;

			// Now taking offset/pagination into account.
			$this->final_query = $this->final_query . ' LIMIT %d OFFSET %d';
			array_push( $this->library_prepare_array, $this->booksonpage, $this->offset );

			// Finally running our completed DB query and getting all books.
			$this->books_actual = $wpdb->get_results( $wpdb->prepare( $this->final_query, $this->library_prepare_array ) );

		}

		/**
		 *  Actually results in getting the requested books
		 */
		private function filter_book_actual() {

			global $wpdb;
			if ( 0 === count( $this->books_before_limit_offset_actual ) ) {

				if ( count( $this->prepare_before_limit_offset ) > 0 ) {
					$this->books_before_limit_offset_actual = $wpdb->get_results( $wpdb->prepare( $this->query_before_limit_offset, $this->prepare_before_limit_offset ) );
				} else {
					$this->books_before_limit_offset_actual = $wpdb->get_results( $this->query_before_limit_offset );
				}
			}

			// If the Filter By Authors... dropdown is set.
			if ( '' !== $this->filterauthor ) {

				// If the sort option is set to author's last names.
				if ( 'alphabeticallybyauthorlast' === $this->sortby ) {

					$mult_auth_flag  = false;
					$lastnamestring  = '';
					$firstnamestring = '';

					// Handling multiple authors...
					if ( false !== strpos( $this->filterauthor, '-' ) ) {

						// Set a flag indicating multiple authors.
						$mult_auth_flag = true;
						$autharray      = explode( '-', $this->filterauthor );
						foreach ( $autharray as $key => $value ) {

							// Split up by first/last.
							if ( false !== strpos( $value, ',' ) ) {
								$firstlast       = explode( ',', $value );
								$lastnamestring  = $lastnamestring . rtrim( $firstlast[0] ) . ';';
								$firstnamestring = $firstnamestring . rtrim( $firstlast[1] ) . ';';
							}
						}
					} else {
						// Split up by first/last.
						if ( false !== strpos( $this->filterauthor, ',' ) ) {
							$firstlast       = explode( ',', $this->filterauthor );
							$lastnamestring  = $firstlast[0];
							$firstnamestring = $firstlast[1];
						}
					}

					$lastnamestring  = rtrim( $lastnamestring, ';' );
					$firstnamestring = rtrim( $firstnamestring, ';' );

					// Building array of titles to look for in author's names.
					$title_array = array(
						'Jr.',
						'Ph.D.',
						'Mr.',
						'Mrs.',
					);

					// Removing all possible spaces from ends of strings.
					$lastnamestring  = ltrim( $lastnamestring, ' ' );
					$lastnamestring  = rtrim( $lastnamestring, ' ' );
					$firstnamestring = ltrim( $firstnamestring, ' ' );
					$firstnamestring = rtrim( $firstnamestring, ' ' );

					$title = '';
					foreach ( $title_array as $key => $value ) {
						if ( false !== strpos( $firstnamestring, $value ) ) {
							$temp            = explode( $value, $firstnamestring );
							$firstnamestring = $temp[0];

							if ( 'Mrs.' === $value || 'Mr.' === $value ) {
								$firstnamestring = $value . ' ' . $firstnamestring;
							} else {
								if ( ! $mult_auth_flag ) {
									$lastnamestring = $lastnamestring . ' ' . $value;
								}
							}
						}
					}

					// Removing all possible spaces from ends of lastnamestring, remove/convert characters/entites, etc.
					$lastnamestring = ltrim( $lastnamestring, ' ' );
					$lastnamestring = rtrim( $lastnamestring, ' ' );
					$lastnamestring = str_replace( ' ', '', $lastnamestring );
					$lastnamestring = stripslashes( $lastnamestring );
					$lastnamestring = htmlspecialchars_decode( $lastnamestring, ENT_QUOTES );

					// Removing spaces from firstnamestring.
					$firstnamestring = ltrim( $firstnamestring, ' ' );
					$firstnamestring = rtrim( $firstnamestring, ' ' );

					foreach ( $this->books_before_limit_offset_actual as $key => $value ) {
						if ( $mult_auth_flag ) {
							if ( $value->authorlast !== $lastnamestring ) {
								unset( $this->books_before_limit_offset_actual[ $key ] );
							}
						} else {
							if ( $value->author !== $firstnamestring . ' ' . $lastnamestring ) {
								unset( $this->books_before_limit_offset_actual[ $key ] );
							}
						}
					}
				} else {
					foreach ( $this->books_before_limit_offset_actual as $key => $value ) {

						// Strip slashes and convert characters if needed.
						$this->filterauthor = stripslashes( $this->filterauthor );
						$this->filterauthor = htmlspecialchars_decode( $this->filterauthor, ENT_QUOTES );

						if ( $value->author !== $this->filterauthor ) {
							unset( $this->books_before_limit_offset_actual[ $key ] );
						}
					}
				}
			}

			// If the Filter By Category... dropdown is set.
			if ( '' !== $this->filtercategory ) {

				// Replace any possible '-' characters in string with '&'.
				$this->filtercategory = str_replace( '-', '&', $this->filtercategory );

				foreach ( $this->books_before_limit_offset_actual as $key => $value ) {
					if ( $value->category !== $this->filtercategory ) {
						unset( $this->books_before_limit_offset_actual[ $key ] );
					}
				}
			}

			// If the Filter By subject... dropdown is set.
			if ( '' !== $this->filtersubject ) {
				foreach ( $this->books_before_limit_offset_actual as $key => $value ) {
					if ( $value->subject !== $this->filtersubject ) {
						unset( $this->books_before_limit_offset_actual[ $key ] );
					}
				}
			}

			// If the Filter By country... dropdown is set.
			if ( '' !== $this->filtercountry ) {
				foreach ( $this->books_before_limit_offset_actual as $key => $value ) {
					if ( $value->country !== $this->filtercountry ) {
						unset( $this->books_before_limit_offset_actual[ $key ] );
					}
				}
			}

			// If the Filter By country... dropdown is set.
			if ( '' !== $this->filterpubyears ) {

				// Splitting up $this->filterpubyears.
				$this->filterpubyears = explode( '-', $this->filterpubyears );
				foreach ( $this->books_before_limit_offset_actual as $key => $value ) {
					if ( ( $value->pub_year < $this->filterpubyears[0] || $value->pub_year > $this->filterpubyears[1] ) ) {
						unset( $this->books_before_limit_offset_actual[ $key ] );
					}
				}
			}

			// Now modify the filtered array to include the offset and page number, etc...
			$this->books_before_limit_offset_actual = array_values( $this->books_before_limit_offset_actual );

			$temparray = array();
			foreach ( $this->books_before_limit_offset_actual as $key => $value ) {
				if ( $this->offset > 0 ) {
					if ( $key >= $this->offset && $key < ( $this->offset + $this->booksonpage ) ) {
						array_push( $temparray, $value );
					}
				} else {
					if ( $key < $this->booksonpage ) {
						array_push( $temparray, $value );
					}
				}
			}

			// Update the official $this->books_actual with the array we've just created.
			$this->books_actual = $temparray;

			// Update the book count for page count, stats area, etc.
			$this->total_book_count   = count( $this->books_before_limit_offset_actual );
			$this->total_books_filter = count( $this->books_actual );
		}

		/**
		 *  Output the first HTML part
		 */
		private function output_beginning_html() {
			echo '<div class="wpbooklist-top-container" data-table="' . $this->table . '">
	    			<div class="wpbooklist-table-for-app">' . $this->table . '</div>
	    			<div class="wpbooklist-for-branding" id="wpbooklist-branding-text-1" data-value="' . $this->brandingtext1 . '"></div>
	    			<div class="wpbooklist-for-branding" id="wpbooklist-branding-text-2" data-value="' . $this->brandingtext2 . '"></div>
	    			<div class="wpbooklist-for-branding" id="wpbooklist-branding-logo-1" data-value="' . $this->brandinglogo1 . '"></div>
	    			<div class="wpbooklist-for-branding" id="wpbooklist-branding-logo-2" data-value="' . $this->brandinglogo2 . '"></div>
	    			<p id="specialcaseforappid"></p>
					<a id="hidden-link-for-styling" style="display:none"></a>
					<div id="wpbooklist-filter-search-container">';
		}

		/**
		 *  Outputs the search HTML
		 */
		private function output_search() {

			if ( '' === $this->searchterm ) {
				$searchval = $this->trans->trans_1 . '...';
			} else {
				$searchval = $this->searchterm;
			}

			$string1 = '<div id="wpbooklist-search-div">
							<div id="wpbooklist-search-sort-inner-cont">
							<div id="wpbooklist-sort-search-div">
								<div id="wpbooklist-select-sort-div">
							        <select class="wpbooklist-sort-select-box" id="wpbooklist-sort-select-box-' . uniqid() . '" data-table=' . $this->table . '>    
										<option ' . $this->sortbyselected1 . ' disabled>' . $this->trans->trans_2 . '...</option>
										<option value="default">' . $this->trans->trans_3 . '</option>
										 <option ' . $this->sortbyselected2 . ' value="alphabeticallybytitle">' . $this->trans->trans_4 . '</option>
										<option ' . $this->sortbyselected3 . ' value="alphabeticallybyauthorfirst">' . $this->trans->trans_5 . '</option><option ' . $this->sortbyselected4 . ' value="alphabeticallybyauthorlast">' . $this->trans->trans_6 . '</option>';

			if ( '1' !== $this->display_options_actual->hidefinishedsort ) {
				$string1 = $string1 . '<option ' . $this->sortbyselected5 . ' value="year_read">' . $this->trans->trans_7 . '</option>';
			}

			$string1 = $string1 . '<option ' . $this->sortbyselected6 . ' value="pages_desc">' . $this->trans->trans_8 . '</option> <option ' . $this->sortbyselected7 . ' value="pages_asc">' . $this->trans->trans_9 . '</option>';

			if ( '1' !== $this->display_options_actual->hidesignedsort ) {
				$string1 = $string1 . '<option ' . $this->sortbyselected8 . ' value="signed">' . $this->trans->trans_10 . '</option>';
			}

			if ( '1' !== $this->display_options_actual->hidefirstsort ) {
				$string1 = $string1 . '<option ' . $this->sortbyselected9 . ' value="first_edition">' . $this->trans->trans_11 . '</option>';
			}

			$string1 = $string1 . '</select>
					    		</div>
					    	</div>
					   	</div>
					   	<form class="wpbooklist-search-form" id="wpbooklist-search-form-' . uniqid() . '" data-table=' . $this->table . '>
					    	<div id="wpbooklist-search-checkboxes">
						        <p>' . $this->trans->trans_12 . ':
						            <input id="wpbooklist-book-title-search" type="checkbox" name="book-title-search" value="book-title-search">' . $this->trans->trans_13 . '</input>
						            <input id="wpbooklist-author-search" type="checkbox" name="author-search" value="author-search">' . $this->trans->trans_14 . '</input>
						            <input id="wpbooklist-cat-search" type="checkbox" name="cat-search" value="author-search">' . $this->trans->trans_15 . '</input>
						        </p>
					    	</div>
					    	<div>
					    		<input id="wpbooklist-search-text" class="wpbooklist-search-text-class" type="text" name="search-query" value="' . $searchval . '">
					    	</div>
						    <div id="wpbooklist-search-submit">
						        <input data-table="' . $this->table . '" id="wpbooklist-search-sub-button" type="submit" name="search-button" value="' . $this->trans->trans_1 . '"></input>
						    </div>
						    </form>
						</div>';

			echo $string1;
		}

		/**
		 *  Builds the values found in the filter drop-downs
		 */
		private function build_filter_values() {

			global $wpdb;

			// Getting all the books that exist for query currently in play, before the limit and offset were applied.
			if ( count( $this->books_before_limit_offset_actual ) === 0 ) {

				// Trim any whitespace.
				$this->query_before_limit_offset = rtrim( $this->query_before_limit_offset );
				$this->query_before_limit_offset = ltrim( $this->query_before_limit_offset );

				// Determining if we need $wpdb->prepare.
				if ( 'SELECT * FROM ' . $this->table === $this->query_before_limit_offset ) {
					$this->books_before_limit_offset_actual = $wpdb->get_results( $this->query_before_limit_offset );
				} else {

					if ( count( $this->prepare_before_limit_offset ) > 0 ) {
						$this->books_before_limit_offset_actual = $wpdb->get_results( $wpdb->prepare( $this->query_before_limit_offset, $this->prepare_before_limit_offset ) );
					} else {
						$this->books_before_limit_offset_actual = $wpdb->get_results( $this->query_before_limit_offset );
					}
				}
			}

			// If user or visitor has set the Sort option to 'Alphabetically by Author's Last Name', then modify the drop-down to reflect that as well.
			if ( 'alphabeticallybyauthorlast' === $this->sortby ) {

				// Creating a unique list of authors.
				$temp_author_array = array();
				foreach ( $this->books_before_limit_offset_actual as $key => $book ) {

					// If there are multiple authors...
					if ( false !== strpos( $book->authorlast, ';' ) ) {

						$authfirst = explode( ';', $book->authorfirst );
						$authlast  = explode( ';', $book->authorlast );
						$authname  = '';

						foreach ( $authfirst as $key => $value ) {
							if ( array_key_exists( $key, $authlast ) ) {
								$authname = $authname . $authlast[ $key ] . ', ' . $value . ' & ';
							}
						}
						$authname = rtrim( $authname, '& ' );
						array_push( $temp_author_array, $authname );
					} else {
						array_push( $temp_author_array, $book->authorlast . ', ' . $book->authorfirst );
					}
				}
				$temp_author_array = array_unique( $temp_author_array );
				foreach ( $temp_author_array as $cat ) {
					if ( '' !== $cat ) {
						array_push( $this->final_author_array, $cat );
					}
				}
				sort( $this->final_author_array );
				$this->total_author_count = count( $this->final_author_array );
			} else {
				// Creating a unique list of authors.
				$temp_author_array = array();
				foreach ( $this->books_before_limit_offset_actual as $key => $book ) {
					array_push( $temp_author_array, $book->author );
				}

				$temp_author_array = array_unique( $temp_author_array );
				foreach ( $temp_author_array as $cat ) {
					if ( '' !== $cat ) {
						array_push( $this->final_author_array, $cat );
					}
				}
				sort( $this->final_author_array );
				$this->total_author_count = count( $this->final_author_array );
			}

			// Creating a unique list of subjects.
			$temp_subject_array = array();
			foreach ( $this->books_before_limit_offset_actual as $key => $book ) {
				array_push( $temp_subject_array, $book->subject );
			}
			$temp_subject_array = array_unique( $temp_subject_array );
			foreach ( $temp_subject_array as $cat ) {
				if ( '' !== $cat ) {
					array_push( $this->final_subject_array, $cat );
				}
			}
			sort( $this->final_subject_array );
			$this->total_subject_count = count( $this->final_subject_array );

			// Creating a unique list of countries.
			$temp_country_array = array();
			foreach ( $this->books_before_limit_offset_actual as $key => $book ) {
				array_push( $temp_country_array, $book->country );
			}
			$temp_country_array = array_unique( $temp_country_array );
			foreach ( $temp_country_array as $cat ) {
				if ( '' !== $cat ) {
					array_push( $this->final_country_array, $cat );
				}
			}
			sort( $this->final_country_array );
			$this->total_country_count = count( $this->final_country_array );

			// Creating a unique list of categories.
			$temp_category_array = array();
			foreach ( $this->books_before_limit_offset_actual as $key => $book ) {
				array_push( $temp_category_array, $book->category );
			}
			$temp_category_array = array_unique( $temp_category_array );
			foreach ( $temp_category_array as $cat ) {
				if ( '' !== $cat ) {
					array_push( $this->final_category_array, $cat );
				}
			}
			sort( $this->final_category_array );
			$this->total_category_count = count( $this->final_category_array );

		}

		/**
		 *  Outputs the filter HTML
		 */
		private function output_filter_html() {

			// Splitting up the year filters.
			$pub1 = 'XX';
			$pub2 = 'XX';
			$pub3 = 'XX';
			$pub4 = 'XX';

			if ( '' !== $this->filterpubyears ) {
				$pub1 = (int) substr( $this->filterpubyears[0], 0, 2 );
				$pub2 = (int) substr( $this->filterpubyears[0], 2, 2 );
				$pub3 = (int) substr( $this->filterpubyears[1], 0, 2 );
				$pub4 = (int) substr( $this->filterpubyears[1], 2, 2 );
			}

			// Building the options for the publication year drop-downs.
			$pubyearopt1 = '';
			$selectflag  = false;
			for ( $i = 20; $i > 14; $i-- ) {
				if ( $i === $pub1 ) {
					$pubyearopt1 = $pubyearopt1 . '<option selected>' . $i . '</option>';
					$selectflag  = true;
				} else {
					$pubyearopt1 = $pubyearopt1 . '<option>' . $i . '</option>';
				}
			}

			if ( ! $selectflag ) {
				$pubyearopt1 = '<option selected disabled>XX</option>' . $pubyearopt1;
			} else {
				$pubyearopt1 = '<option disabled>XX</option>' . $pubyearopt1;
			}

			// Building the options for the publication year drop-downs.
			$pubyearopt2 = '';
			$selectflag  = false;
			for ( $i = 0; $i <= 99; $i++ ) {
				if ( $i < 10 ) {
					$displaynum = '0' . $i;
				} else {
					$displaynum = $i;
				}

				if ( $i === $pub2 ) {
					$pubyearopt2 = $pubyearopt2 . '<option selected>' . $displaynum . '</option>';
					$selectflag  = true;
				} else {
					$pubyearopt2 = $pubyearopt2 . '<option>' . $displaynum . '</option>';
				}
			}

			if ( ! $selectflag ) {
				$pubyearopt2 = '<option selected disabled>XX</option>' . $pubyearopt2;
			} else {
				$pubyearopt2 = '<option disabled>XX</option>' . $pubyearopt2;
			}

			// Building the options for the publication year drop-downs.
			$pubyearopt3 = '';
			$selectflag  = false;
			for ( $i = 20; $i > 14; $i-- ) {
				if ( $i === $pub3 ) {
					$pubyearopt3 = $pubyearopt3 . '<option selected>' . $i . '</option>';
					$selectflag  = true;
				} else {
					$pubyearopt3 = $pubyearopt3 . '<option>' . $i . '</option>';
				}
			}

			if ( ! $selectflag ) {
				$pubyearopt3 = '<option selected disabled>XX</option>' . $pubyearopt3;
			} else {
				$pubyearopt3 = '<option disabled>XX</option>' . $pubyearopt3;
			}

			// Building the options for the publication year drop-downs.
			$pubyearopt4 = '';
			$selectflag  = false;
			for ( $i = 0; $i <= 99; $i++ ) {
				if ( $i < 10 ) {
					$displaynum = '0' . $i;
				} else {
					$displaynum = $i;
				}

				if ( $i === $pub4 ) {
					$pubyearopt4 = $pubyearopt4 . '<option selected>' . $displaynum . '</option>';
					$selectflag  = true;
				} else {
					$pubyearopt4 = $pubyearopt4 . '<option>' . $displaynum . '</option>';
				}
			}

			if ( ! $selectflag ) {
				$pubyearopt4 = '<option selected disabled>XX</option>' . $pubyearopt4;
			} else {
				$pubyearopt4 = '<option disabled>XX</option>' . $pubyearopt4;
			}

			$string1 = '<div id="wpbooklist-filter-div">
							<div id="wpbooklist-filter-author-box">
								<div id="wpbooklist-select-sort-div">
							        <select id="wpbooklist-filter-author-box-div" class="wpbooklist-frontend-filter-select" data-table="' . $this->table . '" data-which="author">    
										<option selected disabled>' . $this->trans->trans_16 . '...</option>';

			$string2 = '';
			foreach ( $this->final_author_array as $auth ) {

				// Set the selected option if the array is only size of 1.
				if ( 1 === count( $this->final_author_array ) ) {
					$string2 = $string2 . '<option selected value="' . $auth . '">' . $auth . '</option>';
				} else {
					$string2 = $string2 . '<option value="' . $auth . '">' . $auth . '</option>';
				}
			}

			$string3 = '</select></div></div>';
			$string4 = '<div id="wpbooklist-filter-category-box-div">
							<div id="wpbooklist-select-sort-div">
						        <select id="wpbooklist-filter-category-box-div" class="wpbooklist-frontend-filter-select" data-table="' . $this->table . '" data-which="category">    
						            <option selected disabled>' . $this->trans->trans_17 . '</option>';

			$string5 = '';
			foreach ( $this->final_category_array as $cat ) {
				// Set the selected Option value, if there is one.
				if ( $this->filtercategory === $cat ) {
					$string5 = $string5 . '<option selected value="' . $cat . '">' . $cat . '</option>';
				} else {
					$string5 = $string5 . '<option value="' . $cat . '">' . $cat . '</option>';
				}
			}

			$string6 = '
						        </select>
					    	</div>
					    </div>';

			$string7 = '<div id="wpbooklist-filter-subject-box-div">
							<div id="wpbooklist-select-sort-div">
						        <select id="wpbooklist-filter-subject-box-div" class="wpbooklist-frontend-filter-select" data-table="' . $this->table . '" data-which="subject">    
						            <option selected disabled>' . $this->trans->trans_18 . '</option>';

			$string8 = '';
			foreach ( $this->final_subject_array as $subject ) {
				// Set the selected Option value, if there is one.
				if ( $this->filtersubject === $subject ) {
					$string8 = $string8 . '<option selected value="' . $subject . '">' . $subject . '</option>';
				} else {
					$string8 = $string8 . '<option value="' . $subject . '">' . $subject . '</option>';
				}
			}

			$string9  = '</select></div></div>';
			$string10 = '<div id="wpbooklist-filter-country-box-div">
							<div id="wpbooklist-select-sort-div">
						        <select id="wpbooklist-filter-country-box-div" class="wpbooklist-frontend-filter-select" data-table="' . $this->table . '" data-which="country">    
						            <option selected disabled>' . $this->trans->trans_19 . '</option>';

			$string11 = '';
			foreach ( $this->final_country_array as $country ) {
				// Set the selected Option value, if there is one.
				if ( $this->filtercountry === $country ) {
					$string11 = $string11 . '<option selected value="' . $country . '">' . $country . '</option>';
				} else {
					$string11 = $string11 . '<option value="' . $country . '">' . $country . '</option>';
				}
			}

			$string12 = '</select>
					    	</div>
					    	<div id="wpbooklist-filter-between-year-div">
					    		<p>' . $this->trans->trans_20 . '</p>
					    		<select id="wpbooklist-year-range-1-' . $this->table . '">
					    			' . $pubyearopt1 . '
					    		</select>
					    		<select id="wpbooklist-year-range-2-' . $this->table . '">
					    			' . $pubyearopt2 . '
					    		</select>
					    		<span id="wpbooklist-to-span"> ' . $this->trans->trans_21 . ' </span>
					    		<select id="wpbooklist-year-range-3-' . $this->table . '">
					    			' . $pubyearopt3 . '
					    		</select>
					    		<select id="wpbooklist-year-range-4-' . $this->table . '" class="wpbooklist-frontend-filter-select" data-table="' . $this->table . '" data-which="pubyears">
					    			' . $pubyearopt4 . '
					    		</select>
					    	</div>
					    	<button class="wpbooklist-reset-search-and-filters">' . $this->trans->trans_22 . '</button>
					    </div>
					</div>';

			echo $string1 . $string2 . $string3 . $string4 . $string5 . $string6 . $string7 . $string8 . $string9 . $string10 . $string11 . $string12;
		}

		/**
		 *  Closes the sort and search area
		 */
		private function output_close_sort_search() {
			echo '</div>';
		}

		/**
		 *  Outputs the stats area
		 */
		private function output_stats() {

			global $wpdb;



			// This is here to accommodate multiple libraries being displayed on the same page.
			if ( $this->query_table === $this->table ) {
				if ( $this->params_true_flag ) {
					//$stats_books_string = $this->trans->trans_24 . ':';
					$book_count         = $this->total_book_count;
				}
			}

			if ( ! $this->filterflag && ! $this->searchflag && ! $this->sortflag ) {

				// Getting the number of books finished - checking for Transients first!
				$transient_name   = 'wpbl_' . md5( 'SELECT COUNT(*) FROM ' . $this->table . ' WHERE finished = Yes' );
				$transient_exists = $this->transients->existing_transient_check( $transient_name );
				if ( $transient_exists ) {
					$all_results_for_stats_finished = $transient_exists;
					$this->total_book_read_count    = $all_results_for_stats_finished;
				} else {
					$query = 'SELECT COUNT(*) FROM ' . $this->table . " WHERE finished = 'Yes'";
					$all_results_for_stats_finished = $wpdb->get_var( $query );
					$all_results_for_stats_finished = $this->transients->create_transient( $transient_name, 'wpdb->get_var', $query, MONTH_IN_SECONDS );
					$this->total_book_read_count = $all_results_for_stats_finished;
				}

				// Getting total book count from entire library - checking for Transients first!
				$transient_name   = 'wpbl_' . md5( 'SELECT COUNT(*) FROM ' . $this->table );
				$transient_exists = $this->transients->existing_transient_check( $transient_name );
				if ( $transient_exists ) {
					$all_results_for_stats_total = $transient_exists;
					$book_count                  = $all_results_for_stats_total;
				} else {
					$query = 'SELECT COUNT(*) FROM ' . $this->table;
					$all_results_for_stats_total = $wpdb->get_var( $query );
					$all_results_for_stats_total = $this->transients->create_transient( $transient_name, 'wpdb->get_var', $query, MONTH_IN_SECONDS );
					$book_count                  = $all_results_for_stats_total;
				}

				// Setting some variables and strings for the Stats section below.
				$stats_books_string       = $this->trans->trans_23 . ':';
				$stats_completion_percent = $this->trans->trans_30 . ':';

				// Getting total # of pages read/finished - checking for Transients first!
				$transient_name   = 'wpbl_' . md5( 'SELECT * FROM ' . $this->table . ' WHERE finished = Yes' );
				$transient_exists = $this->transients->existing_transient_check( $transient_name );
				if ( $transient_exists ) {
					$all_results_for_stats_finished_bookpages = $transient_exists;
				} else {
					$query = 'SELECT * FROM ' . $this->table . " WHERE finished = 'Yes'";
					$all_results_for_stats_finished_bookpages = $wpdb->get_results( $query );
					$all_results_for_stats_finished_bookpages = $this->transients->create_transient( $transient_name, 'wpdb->get_results', $query, MONTH_IN_SECONDS );
				}

				foreach ( $all_results_for_stats_finished_bookpages as $book ) {
					if ( 'Yes' === $book->finished ) {
						$this->total_pages_read_count = $this->total_pages_read_count + $book->pages;
					}
				}

				// Getting total # of books signed - checking for Transients first!
				$transient_name   = 'wpbl_' . md5( 'SELECT COUNT(*) FROM ' . $this->table . ' WHERE finished = Yes' );
				$transient_exists = $this->transients->existing_transient_check( $transient_name );
				if ( $transient_exists ) {
					$all_results_for_stats_signed  = $transient_exists;
					$this->total_book_signed_count = $all_results_for_stats_finished;
				} else {
					$query = 'SELECT COUNT(*) FROM ' . $this->table . " WHERE finished = 'Yes'";
					$all_results_for_stats_signed  = $wpdb->get_var( $query );
					$all_results_for_stats_signed  = $this->transients->create_transient( $transient_name, 'wpdb->get_var', $query, MONTH_IN_SECONDS );
					$this->total_book_signed_count = $all_results_for_stats_signed;
				}

				// Getting total # of books first edition. - checking for Transients first!
				$transient_name   = 'wpbl_' . md5( 'SELECT COUNT(*) FROM ' . $this->table . ' WHERE first_edition = Yes OR first_edition = true OR edition = 1 OR edition = 1st OR edition = first or edition = First' );
				$transient_exists = $this->transients->existing_transient_check( $transient_name );
				if ( $transient_exists ) {
					$all_results_for_stats_firstedition  = $transient_exists;
					$this->total_book_first_edition_count = $all_results_for_stats_finished;
				} else {
					$query = 'SELECT COUNT(*) FROM ' . $this->table . "  WHERE first_edition = 'Yes' OR first_edition = 'true' OR edition = '1' OR edition = '1st' OR edition = 'first' or edition = 'First'";
					$all_results_for_stats_firstedition  = $wpdb->get_var( $query );
					$all_results_for_stats_firstedition  = $this->transients->create_transient( $transient_name, 'wpdb->get_var', $query, MONTH_IN_SECONDS );
					$this->total_book_first_edition_count = $all_results_for_stats_signed;
				}
				
			} else {

				// Setting some variables and strings for the Stats section below.
				$book_count               = $this->total_book_count;
				$stats_books_string       = $this->trans->trans_24 . ':';
				$stats_completion_percent = $this->trans->trans_548 . ':';

				// If the Sort flag is true, and if the 'sortby' value equals specific terms, get all books for Stats calculations, otherwise stick with what we have.
				if ( $this->sortflag && 'year_read' !== $this->sortby && 'signed' !== $this->sortby && 'first_edition' !== $this->sortby ) {

					// Getting the number of books finished - checking for Transients first!
					$transient_name   = 'wpbl_' . md5( 'SELECT COUNT(*) FROM ' . $this->table . ' WHERE finished = Yes' );
					$transient_exists = $this->transients->existing_transient_check( $transient_name );
					if ( $transient_exists ) {
						$all_results_for_stats_finished = $transient_exists;
						$this->total_book_read_count    = $all_results_for_stats_finished;
					} else {
						$query = 'SELECT COUNT(*) FROM ' . $this->table . " WHERE finished = 'Yes'";
						$all_results_for_stats_finished = $wpdb->get_var( $query );
						$all_results_for_stats_finished = $this->transients->create_transient( $transient_name, 'wpdb->get_var', $query, MONTH_IN_SECONDS );
						$this->total_book_read_count = $all_results_for_stats_finished;
					}

					// Getting total book count from entire library - checking for Transients first!
					$transient_name   = 'wpbl_' . md5( 'SELECT COUNT(*) FROM ' . $this->table );
					$transient_exists = $this->transients->existing_transient_check( $transient_name );
					if ( $transient_exists ) {
						$all_results_for_stats_total = $transient_exists;
						$book_count                  = $all_results_for_stats_total;
					} else {
						$query = 'SELECT COUNT(*) FROM ' . $this->table;
						$all_results_for_stats_total = $wpdb->get_var( $query );
						$all_results_for_stats_total = $this->transients->create_transient( $transient_name, 'wpdb->get_var', $query, MONTH_IN_SECONDS );
						$book_count                  = $all_results_for_stats_total;
					}

					// Setting some variables and strings for the Stats section below.
					$stats_books_string       = $this->trans->trans_23 . ':';
					$stats_completion_percent = $this->trans->trans_30 . ':';

					// Getting total # of pages read/finished - checking for Transients first!
					$transient_name   = 'wpbl_' . md5( 'SELECT * FROM ' . $this->table . ' WHERE finished = Yes' );
					$transient_exists = $this->transients->existing_transient_check( $transient_name );
					if ( $transient_exists ) {
						$all_results_for_stats_finished_bookpages = $transient_exists;
					} else {
						$query = 'SELECT * FROM ' . $this->table . " WHERE finished = 'Yes'";
						$all_results_for_stats_finished_bookpages = $wpdb->get_results( $query );
						$all_results_for_stats_finished_bookpages = $this->transients->create_transient( $transient_name, 'wpdb->get_results', $query, MONTH_IN_SECONDS );
					}

					foreach ( $all_results_for_stats_finished_bookpages as $book ) {
						if ( 'Yes' === $book->finished ) {
							$this->total_pages_read_count = $this->total_pages_read_count + $book->pages;
						}
					}

					// Getting total # of books signed - checking for Transients first!
					$transient_name   = 'wpbl_' . md5( 'SELECT COUNT(*) FROM ' . $this->table . ' WHERE finished = Yes' );
					$transient_exists = $this->transients->existing_transient_check( $transient_name );
					if ( $transient_exists ) {
						$all_results_for_stats_signed  = $transient_exists;
						$this->total_book_signed_count = $all_results_for_stats_finished;
					} else {
						$query = 'SELECT COUNT(*) FROM ' . $this->table . " WHERE finished = 'Yes'";
						$all_results_for_stats_signed  = $wpdb->get_var( $query );
						$all_results_for_stats_signed  = $this->transients->create_transient( $transient_name, 'wpdb->get_var', $query, MONTH_IN_SECONDS );
						$this->total_book_signed_count = $all_results_for_stats_signed;
					}

					// Getting total # of books first edition. - checking for Transients first!
					$transient_name   = 'wpbl_' . md5( 'SELECT COUNT(*) FROM ' . $this->table . ' WHERE first_edition = Yes OR first_edition = true OR edition = 1 OR edition = 1st OR edition = first or edition = First' );
					$transient_exists = $this->transients->existing_transient_check( $transient_name );
					if ( $transient_exists ) {
						$all_results_for_stats_firstedition  = $transient_exists;
						$this->total_book_first_edition_count = $all_results_for_stats_finished;
					} else {
						$query = 'SELECT COUNT(*) FROM ' . $this->table . "  WHERE first_edition = 'Yes' OR first_edition = 'true' OR edition = '1' OR edition = '1st' OR edition = 'first' or edition = 'First'";
						$all_results_for_stats_firstedition  = $wpdb->get_var( $query );
						$all_results_for_stats_firstedition  = $this->transients->create_transient( $transient_name, 'wpdb->get_var', $query, MONTH_IN_SECONDS );
						$this->total_book_first_edition_count = $all_results_for_stats_signed;
					}
				} else {

					// Getting total # of books read/finished.
					foreach ( $this->books_actual as $book ) {
						if ( 'Yes' === $book->finished ) {
							$this->total_book_read_count++;
						}
					}

					// Getting total # of pages read/finished.
					foreach ( $this->books_actual as $book ) {
						if ( 'Yes' === $book->finished ) {
							$this->total_pages_read_count = $this->total_pages_read_count + $book->pages;
						}
					}

					// Getting total # of books signed.
					foreach ( $this->books_actual as $book ) {
						if ( 'true' === $book->signed ) {
							$this->total_book_signed_count++;
						}
					}

					// Getting total # of books first edition.
					foreach ( $this->books_actual as $book ) {
						if ( 'true' === $book->first_edition ) {
							$this->total_book_first_edition_count++;
						}
					}
				}
			}

			$string1 = '
			<div class="wpbooklist_stats_tdiv">
				<p class="wpbooklist_control_panel_stat">' . $stats_books_string . ' ' . number_format( $book_count ) . '</p>
				<p class="wpbooklist_control_panel_stat">' . $this->trans->trans_25 . ': ' . number_format( $this->total_book_read_count ) . '</p>
				<p class="wpbooklist_control_panel_stat">' . $this->trans->trans_26 . ': ' . number_format( $this->total_book_signed_count ) . '</p>
				<p class="wpbooklist_control_panel_stat">' . $this->trans->trans_27 . ': ' . number_format( $this->total_book_first_edition_count ) . '</p>
				<p class="wpbooklist_control_panel_stat">' . $this->trans->trans_28 . ': ' . number_format( $this->total_pages_read_count ) . '</p>
				<p class="wpbooklist_control_panel_stat">' . $this->trans->trans_29 . ': ' . number_format( $this->total_category_count ) . '</p>
				<p class="wpbooklist_control_panel_stat">' . $stats_completion_percent . ' ';

			if ( ( 0 === $this->total_book_read_count ) || ( 0 === $this->total_book_count ) ) {
				$string2 = '0%';
			} else {

				// Division by zero check.
				if ( 0 !== $this->total_book_read_count && 0 !== $book_count && null !== $book_count && '0' !== $this->total_book_read_count && '0' !== $book_count ) {

					$string2 = number_format( ( ( $this->total_book_read_count / $book_count ) * 100 ), 2 ) . '%';
				} else {
					$string2 = '0%';
				}
			}

			$string3 = '</p></div>';

			echo $string1 . $string2 . $string3;
		}

		/**
		 *  Outputs the quote
		 */
		private function output_quote() {

			global $wpdb;

			// Getting quotes - checking for Transients first!
			$this->quotes_table = $wpdb->prefix . 'wpbooklist_jre_book_quotes';
			$transient_name     = 'wpbl_' . md5( 'SELECT * FROM ' . $this->quotes_table );
			$transient_exists   = $this->transients->existing_transient_check( $transient_name );
			if ( $transient_exists ) {
				$this->quotes_actual = $transient_exists;
			} else {
				$query               = 'SELECT * FROM ' . $this->quotes_table;
				$this->quotes_actual = $wpdb->get_results( $query );
				//$this->quotes_actual = $this->transients->create_transient( $transient_name, 'wpdb->get_results', $query, MONTH_IN_SECONDS );
			}

			// Getting number of quotes.
			$this->total_quotes_count = count( $this->quotes_actual );

			$quote_num = wp_rand( 0, $this->total_quotes_count );
			if ( null !== $quote_num ) {

				// Making sure the array key exists.
				if ( array_key_exists( $quote_num, $this->quotes_actual ) ) {
					$quote_actual = $this->quotes_actual[ $quote_num ]->quote;
				} else {
					$quote_actual = $this->quotes_actual[0]->quote;
				}

				$pos          = strpos( $quote_actual, '" - ' );
				$attribution  = substr( $quote_actual, $pos +1 );
				$quote        = substr( $quote_actual, 0, $pos + 1 );
				echo '<div class="wpbooklist-ui-quote-area-div">
		    		<p class="wpbooklist-ui-quote-area-p">
		    			<span id="wpbooklist-quote-actual">' . stripslashes( $quote ) . '</span>
		    			<span id="wpbooklist-attribution-actual">' . stripslashes( $attribution ) . '</span>
		    		</p>
		    	  </div>';
			}
		}

		/**
		 *  Output the actual Library HTML
		 */
		private function build_library_actual() {

			$string1 = '<div id="wpbooklist_main_display_div">';

			// Create some messages if there aren't any books in a library, or if no search results were found.
			$nobooksmessage = '';

			// Check for zero results found - if so, display a error message.
			if ( '0' === $this->total_books_filter || '0' === $this->total_book_count ) {
				$nobooksmessage = '<p><img class="wpbooklist-storytime-shocked-img-front" src="' . ROOT_IMG_ICONS_URL . 'shocked.svg"/>' . $this->trans->trans_31 . '<br/>' . $this->trans->trans_32 . ' <a href="https://wordpress.org/plugins/wpbooklist/">' . $this->trans->trans_33 . '</a><br/><br/>' . $this->trans->trans_34 . ' <a href="https://wpbooklist.com/">' . $this->trans->trans_35 . '</a></p>';
			}

			// Add the no books message to the HTML string.
			$string1    = $string1 . $nobooksmessage;
			$onpage_key = 1;
			$string2    = '';

			foreach ( $this->books_actual as $key => $book ) {

				// Determine if we're hiding the book title.
				$hidelibrarytitle = '';
				if ( '1' === $this->display_options_actual->hidelibrarytitle ) {
					$hidelibrarytitle = 'style="display:none;"';
				}

				// Replace default tag if the user has provided their own - 5.5.3.
				if ( strpos( $book->amazon_detail_page, 'wpbooklisti0e-21' ) ) {
					if ( '' !== $this->display_options_actual->amazonaff && null !== $this->display_options_actual->amazonaff && 'wpbooklisti0e-21' !== $this->display_options_actual->amazonaff ) {
						$book->amazon_detail_page = str_replace( 'wpbooklisti0e-21', $this->display_options_actual->amazonaff, $book->amazon_detail_page );
					}
				}

				// Replace my API key/Subscription ID if the user has provided their own - 5.5.3.
				if ( strpos( $book->amazon_detail_page, 'AKIAJCI3DJTKR6N4LR2A' ) ) {
					if ( '' !== $this->display_options_actual->amazonapipublic && null !== $this->display_options_actual->amazonapipublic ) {
						$book->amazon_detail_page = str_replace( 'AKIAJCI3DJTKR6N4LR2A', $this->display_options_actual->amazonapipublic, $book->amazon_detail_page );
					}
				}

				// Displaying books based on provided action.
				if ( 'post' === $this->action ) {
					if ( null !== $book->post_yes && 'false' !== $book->post_yes && 'N/A' !== $book->post_yes && 'no' !== $book->post_yes) {
						$string2 = $string2 . '<div class="wpbooklist_entry_div">
						<p style="display:none;" id="wpbooklist-hidden-isbn1">' . $book->isbn . '</p>
						<div class="wpbooklist_inner_main_display_div">
						    <a href="' . get_permalink( $book->post_yes ) . '"><img class="wpbooklist_cover_image_class" data-bookid="' . $book->ID . '" data-booktable="' . $this->table . '" id="wpbooklist_cover_image" src="' . $book->image . '" style="opacity: 1;"></a>
						    <span class="hidden_id_title">' . $book->ID . '</span>
						    <a href="' . $book->amazon_detail_page . '"><p ' . $hidelibrarytitle . ' class="wpbooklist_saved_title_link" data-bookid="' . $book->ID . '" data-booktable="' . $this->table . '" id="wpbooklist_saved_title_link">' . stripslashes( $book->title ) . '<span class="hidden_id_title">1</span>
						    </p></a>';
					} else {
						$string2 = $string2 . '<div class="wpbooklist_entry_div">
						<p style="display:none;" id="wpbooklist-hidden-isbn1">' . $book->isbn . '</p>
						<div class="wpbooklist_inner_main_display_div">
						    <img class="wpbooklist_cover_image_class wpbooklist-show-book-colorbox" data-bookid="' . $book->ID . '" data-booktable="' . $this->table . '" id="wpbooklist_cover_image" src="' . $book->image . '" style="opacity: 1;">
						    <span class="hidden_id_title">' . $book->ID . '</span>
						    <p ' . $hidelibrarytitle . ' class="wpbooklist_saved_title_link wpbooklist-show-book-colorbox" data-bookid="' . $book->ID . '" data-booktable="' . $this->table . '" id="wpbooklist_saved_title_link">' . stripslashes( $book->title ) . '<span class="hidden_id_title">1</span>
						    </p>';
					}
				} elseif ( 'page' === $this->action ) {
					if ( null !== $book->page_yes && 'false' !== $book->page_yes && 'N/A' !== $book->page_yes && 'no' !== $book->page_yes ) {
						$string2 = $string2 . '<div class="wpbooklist_entry_div">
						<p style="display:none;" id="wpbooklist-hidden-isbn1">' . $book->isbn . '</p>
						<div class="wpbooklist_inner_main_display_div">
						    <a href="' . get_permalink( $book->page_yes ) . '"><img class="wpbooklist_cover_image_class" data-bookid="' . $book->ID . '" data-booktable="' . $this->table . '" id="wpbooklist_cover_image" src="' . $book->image . '" style="opacity: 1;"></a>
						    <span class="hidden_id_title">' . $book->ID . '</span>
						    <a href="' . $book->amazon_detail_page . '"><p ' . $hidelibrarytitle . ' class="wpbooklist_saved_title_link" data-bookid="' . $book->ID . '" data-booktable="' . $this->table . '" id="wpbooklist_saved_title_link">' . stripslashes( $book->title ) . '<span class="hidden_id_title">1</span>
						    </p></a>';
					} else {
						$string2 = $string2 . '<div class="wpbooklist_entry_div">
						<p style="display:none;" id="wpbooklist-hidden-isbn1">' . $book->isbn . '</p>
						<div class="wpbooklist_inner_main_display_div">
						    <img class="wpbooklist_cover_image_class wpbooklist-show-book-colorbox" data-bookid="' . $book->ID . '" data-booktable="' . $this->table . '" id="wpbooklist_cover_image" src="' . $book->image . '" style="opacity: 1;">
						    <span class="hidden_id_title">' . $book->ID . '</span>
						    <p ' . $hidelibrarytitle . ' class="wpbooklist_saved_title_link wpbooklist-show-book-colorbox" data-bookid="' . $book->ID . '" data-booktable="' . $this->table . '" id="wpbooklist_saved_title_link">' . stripslashes( $book->title ) . '<span class="hidden_id_title">1</span>
						    </p>';
					}
				} elseif ( 'amazon' === $this->action ) {
					$string2 = $string2 . '<div class="wpbooklist_entry_div">
					<p style="display:none;" id="wpbooklist-hidden-isbn1">' . $book->isbn . '</p>
					<div class="wpbooklist_inner_main_display_div">
					    <a href="' . $book->amazon_detail_page . '"><img class="wpbooklist_cover_image_class" data-bookid="' . $book->ID . '" data-booktable="' . $this->table . '" id="wpbooklist_cover_image" src="' . $book->image . '" style="opacity: 1;"></a>
					    <span class="hidden_id_title">' . $book->ID . '</span>
					    <a href="' . $book->amazon_detail_page . '"><p ' . $hidelibrarytitle . ' class="wpbooklist_saved_title_link" data-bookid="' . $book->ID . '" data-booktable="' . $this->table . '" id="wpbooklist_saved_title_link">' . stripslashes( $book->title ) . '<span class="hidden_id_title">1</span>
					    </p></a>';
				} elseif ( 'googlebooks' === $this->action ) {
					$string2 = $string2 . '<div class="wpbooklist_entry_div">
					<p style="display:none;" id="wpbooklist-hidden-isbn1">' . $book->isbn . '</p>
					<div class="wpbooklist_inner_main_display_div">
					    <a href="' . $book->google_preview . '"><img class="wpbooklist_cover_image_class" data-bookid="' . $book->ID . '" data-booktable="' . $this->table . '" id="wpbooklist_cover_image" src="' . $book->image . '" style="opacity: 1;"></a>
					    <span class="hidden_id_title">' . $book->ID . '</span>
					    <a href="' . $book->google_preview . '"><p ' . $hidelibrarytitle . ' class="wpbooklist_saved_title_link" data-bookid="' . $book->ID . '" data-booktable="' . $this->table . '" id="wpbooklist_saved_title_link">' . stripslashes( $book->title ) . '<span class="hidden_id_title">1</span>
					    </p></a>';

				} elseif ( 'ibooks' === $this->action ) {
					$string2 = $string2 . '<div class="wpbooklist_entry_div">
					<p style="display:none;" id="wpbooklist-hidden-isbn1">' . $book->isbn . '</p>
					<div class="wpbooklist_inner_main_display_div">
					    <a href="' . $book->itunes_page . '"><img class="wpbooklist_cover_image_class" data-bookid="' . $book->ID . '" data-booktable="' . $this->table . '" id="wpbooklist_cover_image" src="' . $book->image . '" style="opacity: 1;"></a>
					    <span class="hidden_id_title">' . $book->ID . '</span>
					    <a href="' . $book->itunes_page . '"><p ' . $hidelibrarytitle . ' class="wpbooklist_saved_title_link" data-bookid="' . $book->ID . '" data-booktable="' . $this->table . '" id="wpbooklist_saved_title_link">' . stripslashes( $book->title ) . '<span class="hidden_id_title">1</span>
					    </p></a>';

				} elseif ( 'booksamillion' === $this->action ) {
					$string2 = $string2 . '<div class="wpbooklist_entry_div">
					<p style="display:none;" id="wpbooklist-hidden-isbn1">' . $book->isbn . '</p>
					<div class="wpbooklist_inner_main_display_div">
					    <a href="http://www.anrdoezrs.net/links/8090484/type/dlg/' . $book->bam_link . '?id=7059442747215"><img class="wpbooklist_cover_image_class" data-bookid="' . $book->ID . '" data-booktable="' . $this->table . '" id="wpbooklist_cover_image" src="' . $book->image . '" style="opacity: 1;"></a>
					    <span class="hidden_id_title">' . $book->ID . '</span>
					    <a href="http://www.anrdoezrs.net/links/8090484/type/dlg/' . $book->bam_link . '?id=7059442747215"><p ' . $hidelibrarytitle . ' class="wpbooklist_saved_title_link" data-bookid="' . $book->ID . '" data-booktable="' . $this->table . '" id="wpbooklist_saved_title_link">' . stripslashes( $book->title ) . '<span class="hidden_id_title">1</span>
					    </p></a>';

				} elseif ( 'kobo' === $this->action ) {
					$string2 = $string2 . '<div class="wpbooklist_entry_div">
					<p style="display:none;" id="wpbooklist-hidden-isbn1">' . $book->isbn . '</p>
					<div class="wpbooklist_inner_main_display_div">
					    <a href="' . $book->kobo_link . '"><img class="wpbooklist_cover_image_class" data-bookid="' . $book->ID . '" data-booktable="' . $this->table . '" id="wpbooklist_cover_image" src="' . $book->image . '" style="opacity: 1;"></a>
					    <span class="hidden_id_title">' . $book->ID . '</span>
					    <a href="' . $book->kobo_link . '"><p ' . $hidelibrarytitle . ' class="wpbooklist_saved_title_link" data-bookid="' . $book->ID . '" data-booktable="' . $this->table . '" id="wpbooklist_saved_title_link">' . stripslashes( $book->title ) . '<span class="hidden_id_title">1</span>
					    </p></a>';
				} else {
					$string2 = $string2 . '<div class="wpbooklist_entry_div">
					<p style="display:none;" id="wpbooklist-hidden-isbn1">' . $book->isbn . '</p>
					<div class="wpbooklist_inner_main_display_div">
					    <img class="wpbooklist_cover_image_class wpbooklist-show-book-colorbox" data-bookid="' . $book->ID . '" data-booktable="' . $this->table . '" id="wpbooklist_cover_image" src="' . $book->image . '" style="opacity: 1;">
					    <span class="hidden_id_title">' . $book->ID . '</span>
					    <p ' . $hidelibrarytitle . ' class="wpbooklist_saved_title_link wpbooklist-show-book-colorbox" data-bookid="' . $book->ID . '" data-booktable="' . $this->table . '" id="wpbooklist_saved_title_link">' . stripslashes( $book->title ) . '<span class="hidden_id_title">1</span>
					    </p>';
				}

				if ( '1' !== $this->display_options_actual->hiderating && 0 !== $book->rating && null !== $book->rating ) {

					if ( '1' === $book->rating ) {
						$string2 = $string2 . '<img style="opacity: 1;" class="wpbooklist-rating-image" src="' . ROOT_IMG_URL . '1star.jpg">';
					}

					if ( '1.5' === $book->rating ) {
						$string2 = $string2 . '<img style="opacity: 1;" class="wpbooklist-rating-image" src="' . ROOT_IMG_URL . '1halfstar.jpg">';
					}

					if ( '2' === $book->rating ) {
						$string2 = $string2 . '<img style="opacity: 1;" class="wpbooklist-rating-image" src="' . ROOT_IMG_URL . '2star.jpg">';
					}

					if ( '2.5' === $book->rating ) {
						$string2 = $string2 . '<img style="opacity: 1;" class="wpbooklist-rating-image" src="' . ROOT_IMG_URL . '2halfstar.jpg">';
					}

					if ( '3' === $book->rating ) {
						$string2 = $string2 . '<img style="opacity: 1;" class="wpbooklist-rating-image" src="' . ROOT_IMG_URL . '3star.jpg">';
					}

					if ( '3.5' === $book->rating ) {
						$string2 = $string2 . '<img style="opacity: 1;" class="wpbooklist-rating-image" src="' . ROOT_IMG_URL . '3halfstar.jpg">';
					}

					if ( '4' === $book->rating ) {
						$string2 = $string2 . '<img style="opacity: 1;" class="wpbooklist-rating-image" src="' . ROOT_IMG_URL . '4star.jpg">';
					}

					if ( '4.5' === $book->rating ) {
						$string2 = $string2 . '<img style="opacity: 1;" class="wpbooklist-rating-image" src="' . ROOT_IMG_URL . '4halfstar.jpg">';
					}

					if ( '5' === $book->rating ) {
						$string2 = $string2 . '<img style="opacity: 1;" class="wpbooklist-rating-image" src="' . ROOT_IMG_URL . '5star.jpg">';
					}
				}

				$string2 = $string2 . '<div class="wpbooklist-library-frontend-purchase-div">';

				$sales_array = array( $book->author_url, $book->price );

				if ( '1' === $this->display_options_actual->enablepurchase && ( null !== $book->price && '' !== $book->price ) && '1' !== $this->display_options_actual->hidefrontendbuyprice ) {
					if ( has_filter( 'wpbooklist_append_to_frontend_library_price_purchase' ) ) {
						$string2 = $string2 . apply_filters( 'wpbooklist_append_to_frontend_library_price_purchase', $sales_array );
					}
				}

				if ( '1' === $this->display_options_actual->enablepurchase && '' !== $book->author_url && '1' !== $this->display_options_actual->hidefrontendbuyimg ) {
					if ( has_filter( 'wpbooklist_append_to_frontend_library_image_purchase' ) ) {
						$string2 = $string2 . apply_filters( 'wpbooklist_append_to_frontend_library_image_purchase', $sales_array );
					}
				}

				$string2 = $string2 . '</div></div></div>';
				$onpage_key++;
			}

			$string3                     = '</div>';
			$this->library_actual_string = $string1 . $string2;
		}

		/**
		 *  Build the Library Pagination
		 */
		private function build_library_pagination() {

			if ( $this->total_book_count > $this->booksonpage ) {

				$pagination_options_string = '';

				// Setting up variables to determine the previous offset to go back to, or to disable that ability if on Page 1.
				if ( '0' !== $this->offset ) {
					$prevnum          = $this->offset - $this->booksonpage;
					$styledisableleft = '';
				} else {
					$prevnum          = 0;
					$styledisableleft = 'style="pointer-events:none;opacity:0.5;"';
				}

				// Setting up variables to determine the next offset to go to, or to disable that ability if on last Page.
				if ( $this->offset < ( $this->total_book_count - $this->booksonpage ) ) {
					$nextnum           = $this->offset + $this->booksonpage;
					$styledisableright = '';
				} else {
					$nextnum           = $this->offset;
					$styledisableright = 'style="pointer-events:none;opacity:0.5;"';
				}

				// Getting total number of full pages and/or if there's only a partial/remainder page.
				if ( $this->total_book_count > 0 && $this->booksonpage > 0 ) {

					// Getting whole pages. Can be zero if total number of books is less that amount set to be displayed per page in the backend settings.
					$whole_pages = floor( $this->total_book_count / $this->booksonpage );

					// Determing whether there is a partial page, whose contents contains less books than amount set to be displayed per page in the backend settings. Will only be 0 if total number of books is evenly divisible by $this->booksonpage.
					$remainder_pages = $this->total_book_count % $this->booksonpage;
					if ( 0 !== $remainder_pages ) {
						$remainder_pages = 1;
					}

					// If there's only one page, don't show pagination.
					if ( ( 1 === $whole_pages && 0 === $remainder_pages ) || ( 0 === $whole_pages && 1 === $remainder_pages ) ) {
						return;
					}

					// The loop that will create the <option> html for the <select>.
					for ( $i = 1; $i <= ( $whole_pages + $remainder_pages ); $i++ ) {

						if ( ( 1 + ( $this->offset / $this->booksonpage ) ) === $i ) {
							$pagination_options_string = $pagination_options_string . '<option value=' . ( ( $i - 1 ) * $this->booksonpage ) . ' selected>'. $this->trans->trans_600 .' ' . $i . '</option>';
						} else {
							$pagination_options_string = $pagination_options_string . '<option value=' . ( ( $i - 1 ) * $this->booksonpage ) . '>'. $this->trans->trans_600 .' ' . $i . '</option>';
						}
					}
				}

				// Actual Pagination HTML.
				if ( '' !== $pagination_options_string ) {
					$string1 = '
					<div class="wpbooklist-pagination-div">
						<div class="wpbooklist-pagination-div-inner">
							<div class="wpbooklist-pagination-left-div" ' . $styledisableleft . ' data-offset="' . $prevnum . '" data-table="' . $this->table . '">
								<p><img class="wpbooklist-pagination-prev-img" src="' . ROOT_IMG_URL . 'next-left.png" />' . $this->trans->trans_36 . '</p>
							</div>
							<div class="wpbooklist-pagination-middle-div">
								<select class="wpbooklist-pagination-middle-div-select" data-table="' . $this->table . '">
									' . $pagination_options_string . '
								</select>
							</div>
							<div class="wpbooklist-pagination-right-div" ' . $styledisableright . ' data-offset="' . $nextnum . '" data-table="' . $this->table . '">
								<p>' . $this->trans->trans_37 . '<img class="wpbooklist-pagination-prev-img" src="' . ROOT_IMG_URL . 'next-right.png" /></p>
							</div>
						</div>
					</div>';
				} else {
					$string1 = '';
				}

				$this->pagination_string = $string1;
			} else {
				$this->pagination_string = '';
			}
		}

		/**
		 *  Output the completed Library
		 */
		private function output_library_actual() {
			echo $this->library_actual_string . $this->pagination_string . '</div></div>';
		}


	}


endif;
