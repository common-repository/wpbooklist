<?php
/**
 * WPBookList Show Book In Colorbox Class - class-wpbooklist-show-book-in-colorbox.php.
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  6.1.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Show_Book_In_Colorbox', false ) ) :

	/**
	 * WPBookList_Admin_Menu Class.
	 */
	class WPBookList_Show_Book_In_Colorbox {

		// The final html output for the colorbox.
		public $output;

		// All saved book properties.
		public $amazon_auth_yes;
		public $library;
		public $settings_library;
		public $use_amazon_yes;
		public $isbn;
		public $title;
		public $author;
		public $author_url;
		public $category;
		public $price;
		public $pages;
		public $pub_year;
		public $publisher;
		public $description;
		public $subject;
		public $country;
		public $notes;
		public $rating;
		public $image;
		public $backcover;
		public $additionalimage1;
		public $additionalimage2;
		public $finished;
		public $date_finished;
		public $signed;
		public $first_edition;
		public $page_yes;
		public $post_yes;
		public $itunes_page;
		public $google_preview;
		public $amazon_detail_page;
		public $review_iframe;
		public $similar_products;
		public $kobo_link;
		public $bam_link;
		public $book_uid;

		# All settings properties
		public $enablepurchase;
		public $hidefacebook;
		public $hidetwitter;
		public $hidegoogleplus;
		public $hidemessenger;
		public $hidepinterest;
		public $hideemail;
		public $hidegoodreadswidget;
		public $hideamazonreview;
		public $hidedescription;
		public $hidesimilar;
		public $hidebookimage;
		public $hideadditionalimgs;
		public $hidefinished;
		public $hidebooktitle;
		public $hidelibrarytitle;
		public $hideauthor;
		public $hidegenres;
		public $hidepages;
		public $hidebookpage;
		public $hidebookpost;
		public $hidepublisher;
		public $hidesubgenre;
		public $hidecountry;
		public $hidepubdate;
		public $hidesigned;
		public $hidefirstedition;
		public $hidefeaturedtitles;
		public $hidenotes;
		public $hidebottompurchase;
		public $hidequotebook;
		public $hideratingbackend;
		public $amazoncountryinfo;
		public $amazonaff;
		public $itunesaff;
		public $hidegooglepurchase;
		public $hideamazonpurchase;
		public $hidebnpurchase;
		public $hidekobopurchase;
		public $hidebampurchase;
		public $hideitunespurchase;
		public $hidefrontendbuyimg;
		public $hidefrontendbuyprice;
		public $hidecolorboxbuyimg;
		public $hidecolorboxbuyprice;
		public $hidekindleprev;
		public $ebook;

		# All color data
		public $addbookcolor;
		public $backupcolor;
		public $searchcolor;
		public $statscolor;
		public $quotecolor;
		public $titlecolor;
		public $editcolor;
		public $deletecolor;
		public $pricecolor;
		public $purchasecolor;
		public $pagenumcolor;
		public $pagebackcolor;
		public $purchasebookcolor;
		public $titlebookcolor;
		public $quotebookcolor;
		public $storefront_active;
		public $sort_param;
		public $book_array = array();


		/** Class Constructor - Simply calls the Translations
		 *
		 *  @param string $book_id - The book's id.
		 *  @param string $book_table - The table to pull the book info from.
		 *  @param array  $book_array - The array that holds all the book info.
		 *  @param string $sort_param - The parameter to sort the books by .
		 */
		public function __construct( $book_id = null, $book_table = null, $book_array = array(), $sort_param ) {

			// Get Translations.
			require_once CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
			$this->trans = new WPBookList_Translations();
			$this->trans->trans_strings();

			// Require the Transients file.
			require_once CLASS_TRANSIENTS_DIR . 'class-wpbooklist-transients.php';
			$this->transients = new WPBookList_Transients();

			$this->sort_param = $sort_param;

			// Get active plugins to see if any extensions are in play .
			$this->active_plugins = (array) get_option( 'active_plugins', array() );

			if ( is_multisite() ) {
				// On the one multisite I have troubleshot, all plugins were merged into the $this->active_plugins variable, but the multisite plugins had an int value, not the actual name of the plugin, so, I had to build an array composed of the keys of the array that get_site_option( 'active_sitewide_plugins', array()) returned, and merge thatt .
				$multi_plugin_actual_name = array();
				$temp                     = get_site_option( 'active_sitewide_plugins', array() );
				foreach ( $temp as $key => $value ) {
					array_push( $multi_plugin_actual_name, $key );
				}

				$this->active_plugins = array_merge( $this->active_plugins, $multi_plugin_actual_name );
			}

			// Checking to see if the StoreFront extension is active .
			foreach ( $this->active_plugins as $key => $plugin ) {
				if ( false !== strpos( $plugin, 'wpbooklist-storefront.php' ) ) {
					$this->storefront_active = true;
				}
			}

			global $wpdb;
			// Construct the settings table name .
			if ( false !== strpos( $book_table, 'wpbooklist_jre_saved_book_log' ) || null === $book_table ) {
				$this->settings_library = $wpdb->prefix . 'wpbooklist_jre_user_options';
			} else {
				$temp_lib               = explode( '_', $book_table );
				$this->settings_library = $wpdb->prefix . 'wpbooklist_jre_settings_' . array_pop( $temp_lib );
			}

			// If class is being called from the BookFinder extension, otherwise ...
			if ( null === $book_id && null === $book_table ) {
				$this->book_array = $book_array;
				$this->gather_user_options();
				$this->modify_author_name();
				$this->gather_bookfinder_data();
				$this->set_amazon_localization();
				$this->modify_author_url();
				$this->create_similar_products();
				$this->dynamic_amazon_aff();
				$this->output_saved_book();
			} else {
				$this->library = $book_table;
				$this->book_id = $book_id;
				$this->gather_user_options();
				$this->gather_book_info();
				$this->modify_author_name();
				$this->set_amazon_localization();
				$this->modify_author_url();
				$this->create_similar_products();
				$this->dynamic_amazon_aff();
				$this->output_saved_book();
			}
		}

		/**
		 *  Function that gets book info from db.
		 */
		private function gather_book_info() {

			global $wpdb;

			$transient_name   = 'wpbl_' . md5( 'SELECT * FROM ' . $this->library . ' WHERE ID = ' . $this->book_id );
			$transient_exists = $this->transients->existing_transient_check( $transient_name );
			if ( $transient_exists ) {
				$saved_book = $transient_exists;
			} else {
				$query = $wpdb->prepare( "SELECT * FROM $this->library WHERE ID = %d", $this->book_id );
				$saved_book = $this->transients->create_transient( $transient_name, 'wpdb->get_row', $query, MONTH_IN_SECONDS );
			}

			$this->isbn                   = $saved_book->isbn;
			$this->id                     = $saved_book->ID;
			$this->title                  = $saved_book->title;
			$this->author                 = $saved_book->author;
			$this->authorfirst            = $saved_book->authorfirst;
			$this->authorlast             = $saved_book->authorlast;
			$this->author_url             = $saved_book->author_url;
			$this->genres                 = $saved_book->category;
			$this->price                  = $saved_book->price;
			$this->pages                  = $saved_book->pages;
			$this->pub_year               = $saved_book->pub_year;
			$this->publisher              = $saved_book->publisher;
			$this->description            = $saved_book->description;
			$this->subgenres              = $saved_book->subgenre;
			$this->subject                = $saved_book->subject;
			$this->country                = $saved_book->country;
			$this->notes                  = $saved_book->notes;
			$this->rating                 = $saved_book->rating;
			$this->image                  = $saved_book->image;
			$this->backcover              = $saved_book->backcover;
			$this->additionalimage1       = $saved_book->additionalimage1;
			$this->additionalimage2       = $saved_book->additionalimage2;
			$this->finished               = $saved_book->finished;
			$this->date_finished          = $saved_book->date_finished;
			$this->signed                 = $saved_book->signed;
			$this->edition                = $saved_book->edition;
			$this->first_edition          = $saved_book->first_edition;
			$this->page_yes               = $saved_book->page_yes;
			$this->post_yes               = $saved_book->post_yes;
			$this->appleibookslink        = $saved_book->appleibookslink;
			$this->google_preview         = $saved_book->google_preview;
			$this->bn_link                = $saved_book->bn_link;
			$this->amazon_detail_page     = $saved_book->amazon_detail_page;
			$this->review_iframe          = $saved_book->review_iframe;
			$this->similar_products       = $saved_book->similar_products;
			$this->page_id                = $saved_book->page_yes;
			$this->post_id                = $saved_book->post_yes;
			$this->similar_products_array = array();
			$this->featured_results       = array();
			$this->kobo_link              = $saved_book->kobo_link;
			$this->bam_link               = $saved_book->bam_link;
			$this->book_uid               = $saved_book->book_uid;
			$this->ebook                  = $saved_book->ebook;
			$this->saved_book             = $saved_book;

			// Let's see if isbn is empty, and if so, populate it with either isbn10 or asin.
			if ( '' === $this->isbn || null === $this->isbn ) {
				$this->isbn = $this->isbn13;
			}
			if ( '' === $this->isbn || null === $this->isbn ) {
				$this->isbn = $this->asin;
			}

			if ( 'https' === $this->review_iframe ) {
				$this->review_iframe = null;
			}

			// Make changes to the bn_link, if it's empty .
			if ( '' === $this->bn_link ) {
				$this->bn_link = 'http://www.barnesandnoble.com/s/' . $this->isbn;
			}

			// Modify the 'Genres' for display.
			if ( '' !== $saved_book->genres && null !== $saved_book->genres ) {

				if ( false !== stripos( $saved_book->genres, '---' ) ) {
					$saved_book->genres = explode( '---', $saved_book->genres );

					foreach ( $saved_book->genres as $key => $indivgenre ) {
						if ( false === stripos( $indivgenre, $this->genres ) ) {
							if ( '' !== $indivgenre ) {
								$this->genres = $this->genres . ', ' . $indivgenre;
							}
						}
					}
				} else {
					if ( false === stripos( $saved_book->genres, $this->genres ) ) {
						if ( '' !== $saved_book->genres ) {
							$this->genres = $this->genres . ', ' . $saved_book->genres;
						}
					}
				}
			}

			$this->genres = ltrim( $this->genres, ' ' );
			$this->genres = ltrim( $this->genres, ',' );
			$this->genres = ltrim( $this->genres, ' ' );

			$final_subgenres = '';
			// Modify the 'Genres' for display.
			if ( '' !== $this->subgenres && null !== $this->subgenres ) {
				if ( false !== stripos( $this->subgenres, '---' ) ) {

					$this->subgenres = explode( '---', $this->subgenres );
					foreach ( $this->subgenres as $key => $indivgenre ) {
						if ( '' !== $indivgenre ) {
							$final_subgenres = $final_subgenres . ', ' . $indivgenre;
						}
					}
				} else {
					$final_subgenres = $this->subgenres;
				}
			}

			$this->subgenres = $final_subgenres;
			$this->subgenres = ltrim( $this->subgenres, ' ' );
			$this->subgenres = ltrim( $this->subgenres, ',' );
			$this->subgenres = ltrim( $this->subgenres, ' ' );

		}

		/**
		 *  Function that gets user options from db.
		 */
		private function gather_user_options() {
			global $wpdb;

			// Get Options from the Options row specific to the library .
			$transient_name   = 'wpbl_' . md5( 'SELECT * FROM ' . $this->settings_library );
			$transient_exists = $this->transients->existing_transient_check( $transient_name );
			if ( $transient_exists ) {
				$options_results = $transient_exists;
			} else {
				$query           = 'SELECT * FROM ' . $this->settings_library;
				$options_results = $this->transients->create_transient( $transient_name, 'wpdb->get_row', $query, MONTH_IN_SECONDS );
			}

			// Get Options from the default Options row.
			$default_opt_table = $wpdb->prefix . 'wpbooklist_jre_user_options';
			$transient_name    = 'wpbl_' . md5( 'SELECT * FROM ' . $default_opt_table );
			$transient_exists  = $this->transients->existing_transient_check( $transient_name );
			if ( $transient_exists ) {
				$default_options_results = $transient_exists;
			} else {
				$query                   = "SELECT * FROM $default_opt_table";
				$default_options_results = $this->transients->create_transient( $transient_name, 'wpdb->get_row', $query, MONTH_IN_SECONDS );
			}

			$this->enablepurchase       = $options_results->enablepurchase;
			$this->hidefacebook         = $options_results->hidefacebook;
			$this->hidetwitter          = $options_results->hidetwitter;
			$this->hidegoogleplus       = $options_results->hidegoogleplus;
			$this->hidemessenger        = $options_results->hidemessenger;
			$this->hidepinterest        = $options_results->hidepinterest;
			$this->hideemail            = $options_results->hideemail;
			$this->hidegoodreadswidget  = $options_results->hidegoodreadswidget;
			$this->hideamazonreview     = $options_results->hideamazonreview;
			$this->hidedescription      = $options_results->hidedescription;
			$this->hidesimilar          = $options_results->hidesimilar;
			$this->hidebookimage        = $options_results->hidebookimage;
			$this->hideadditionalimgs   = $options_results->hideadditionalimgs;
			$this->hidefinished         = $options_results->hidefinished;
			$this->hidebooktitle        = $options_results->hidebooktitle;
			$this->hidelibrarytitle     = $options_results->hidelibrarytitle;
			$this->hideauthor           = $options_results->hideauthor;
			$this->hidegenres           = $options_results->hidegenres;
			$this->hidepages            = $options_results->hidepages;
			$this->hidebookpage         = $options_results->hidebookpage;
			$this->hidebookpost         = $options_results->hidebookpost;
			$this->hidepages            = $options_results->hidepages;
			$this->hidepublisher        = $options_results->hidepublisher;
			$this->hidesubgenre         = $options_results->hidesubgenre;
			$this->hidecountry          = $options_results->hidecountry;
			$this->hidepubdate          = $options_results->hidepubdate;
			$this->hidesigned           = $options_results->hidesigned;
			$this->hidefirstedition     = $options_results->hidefirstedition;
			$this->hidefeaturedtitles   = $options_results->hidefeaturedtitles;
			$this->hidenotes            = $options_results->hidenotes;
			$this->hidebottompurchase   = $options_results->hidebottompurchase;
			$this->hidequotebook        = $options_results->hidequotebook;
			$this->hideratingbook       = $options_results->hideratingbook;
			$this->amazoncountryinfo    = $default_options_results->amazoncountryinfo;
			$this->amazonaff            = $default_options_results->amazonaff;
			$this->itunesaff            = $options_results->itunesaff;
			$this->hidegooglepurchase   = $options_results->hidegooglepurchase;
			$this->hideamazonpurchase   = $options_results->hideamazonpurchase;
			$this->hidebnpurchase       = $options_results->hidebnpurchase;
			$this->hidekobopurchase     = $options_results->hidekobopurchase;
			$this->hidebampurchase      = $options_results->hidebampurchase;
			$this->hideitunespurchase   = $options_results->hideitunespurchase;
			$this->hidefrontendbuyimg   = $options_results->hidefrontendbuyimg;
			$this->hidefrontendbuyprice = $options_results->hidefrontendbuyprice;
			$this->hidecolorboxbuyimg   = $options_results->hidecolorboxbuyimg;
			$this->hidecolorboxbuyprice = $options_results->hidecolorboxbuyprice;
			$this->hidekindleprev       = $options_results->hidekindleprev;
			$this->hidegoogleprev       = $options_results->hidegoogleprev;
			$this->sortoption           = $options_results->sortoption;
		}

		/**
		 *  Function that handles the formatting of the Author's name.
		 */
		private function modify_author_name() {

			// The code to tell colorbox whether this string exists in the url: sortby=alphabeticallybyauthorlast, indicating that the Author names need to be swapped around.
			if ( $this->sort_param ) {
				$this->sort_param = 'alphabeticallybyauthorlast';
			} else {
				$this->sort_param = $this->sortoption;
			}

			// Swap around the Author names if sort option in the url is 'alphabeticallybyauthorlast'.
			if ( 'alphabeticallybyauthorlast' === $this->sort_param ) {
				$this->author = $this->authorlast . ', ' . $this->authorfirst;

				if ( false !== stripos( $this->author, ';' ) ) {

					$authlastarray   = explode( ';', $this->authorlast );
					$authfirstarray  = explode( ';', $this->authorfirst );
					$finalauthstring = '';

					foreach ( $authlastarray as $key => $value ) {
						$finalauthstring = $finalauthstring . $value . ', ' . $authfirstarray[ $key ] . ' & ';
					}

					$finalauthstring = rtrim( $finalauthstring, ' ' );
					$this->author    = rtrim( $finalauthstring, '&' );
				}
			}

			// If there is no author or it's blank or null, set to 'Not Available'.
			if ( '' === $this->author || null === $this->author ) {
				$this->author = $this->trans->trans_448;
			}
		}


		/**
		 *  Function that sets the Amazon Localization info.
		 */
		private function set_amazon_localization() {
			switch ( $this->amazoncountryinfo ) {
				case 'au':
					$this->amazon_detail_page = str_replace( '.com', '.com.au', $this->amazon_detail_page );
					$this->review_iframe      = str_replace( '.com', '.com.au', $this->review_iframe );
					break;
				case 'br':
					$this->amazon_detail_page = str_replace( '.com', '.com.br', $this->amazon_detail_page );
					$this->review_iframe      = str_replace( '.com', '.com.br', $this->review_iframe );
					break;
				case 'ca':
					$this->amazon_detail_page = str_replace( '.com', '.ca', $this->amazon_detail_page );
					$this->review_iframe      = str_replace( '.com', '.ca', $this->review_iframe );
					break;
				case 'cn':
					$this->amazon_detail_page = str_replace( '.com', '.cn', $this->amazon_detail_page );
					$this->review_iframe      = str_replace( '.com', '.cn', $this->review_iframe );
					break;
				case 'fr':
					$this->amazon_detail_page = str_replace( '.com', '.fr', $this->amazon_detail_page );
					$this->review_iframe      = str_replace( '.com', '.fr', $this->review_iframe );
					break;
				case 'de':
					$this->amazon_detail_page = str_replace( '.com', '.de', $this->amazon_detail_page );
					$this->review_iframe      = str_replace( '.com', '.de', $this->review_iframe );
					break;
				case 'in':
					$this->amazon_detail_page = str_replace( '.com', '.in', $this->amazon_detail_page );
					$this->review_iframe      = str_replace( '.com', '.in', $this->review_iframe );
					break;
				case 'it':
					$this->amazon_detail_page = str_replace( '.com', '.it', $this->amazon_detail_page );
					$this->review_iframe      = str_replace( '.com', '.it', $this->review_iframe );
					break;
				case 'jp':
					$this->amazon_detail_page = str_replace( '.com', '.co.jp', $this->amazon_detail_page );
					$this->review_iframe      = str_replace( '.com', '.co.jp', $this->review_iframe );
					break;
				case 'mx':
					$this->amazon_detail_page = str_replace( '.com', '.com.mx', $this->amazon_detail_page );
					$this->review_iframe      = str_replace( '.com', '.com.mx', $this->review_iframe );
					break;
				case 'nl':
					$this->amazon_detail_page = str_replace( '.com', '.nl', $this->amazon_detail_page );
					$this->review_iframe      = str_replace( '.com', '.nl', $this->review_iframe );
					break;
				case 'es':
					$this->amazon_detail_page = str_replace( '.com', '.es', $this->amazon_detail_page );
					$this->review_iframe      = str_replace( '.com', '.es', $this->review_iframe );
					break;
				case 'uk':
					$this->amazon_detail_page = str_replace( '.com', '.co.uk', $this->amazon_detail_page );
					$this->review_iframe      = str_replace( '.com', '.co.uk', $this->review_iframe );
					break;
				case 'sg':
					$this->amazon_detail_page = str_replace( '.com', '.com.sg', $this->amazon_detail_page );
					$this->review_iframe      = str_replace( '.com', '.com.sg', $this->review_iframe );
					break;
				default:

			}
		}

		/**
		 *  Function that modifies the Author URL.
		 */
		private function modify_author_url() {
			if ( null !== $this->author_url ) {
				if ( false === strpos( $this->author_url, 'http://' ) && false === strpos( $this->author_url, 'https://' ) ) {
					$this->author_url = 'http://' . $this->author_url;
				} else {
					$this->author_url = $this->author_url;
				}
			}
		}

		/**
		 *  Function that creates the Similar Products.
		 */
		private function create_similar_products() {

			// If no similar products were found, set array to null and return.
			if ( ';bsp;1---1;bsp;G---G' === $this->similar_products ) {
				$this->similar_products_array = null;
				return;
			}

			$similarproductsarray         = explode( ';bsp;', $this->similar_products );
			$similarproductsarray         = array_unique( $similarproductsarray );
			$this->similar_products_array = array_values( $similarproductsarray );
		}

		/**
		 *  Function that creates the Dynamic Amazon Affiliate ID, if user-provided.
		 */
		private function dynamic_amazon_aff() {

			// Removing my Affiliate ID with the user's, if set.
			if ( '' !== $this->amazonaff && null !== $this->amazonaff ) {
				$this->amazon_detail_page = str_replace( 'wpbooklisti0e-21', $this->amazonaff, $this->amazon_detail_page );
			}

			/*
			// Removing my Affiliate ID, as it's only needed for initial API calls when Adding/Editing/Searching for books.
			if ( 'wpbooklisti0e-21' === $this->amazonaff ) {
				$this->amazonaff = '';
			}

			// Removing my Affiliate ID, as it's only needed for initial API calls when Adding/Editing/Searching for books.
			if ( stripos( $this->amazon_detail_page, 'tag=wpbooklisti0e-21' ) !== false ) {
				$this->amazon_detail_page = str_replace( 'tag=wpbooklisti0e-21', '', $this->amazon_detail_page );
			}
			*/
		}

		/**
		 *  Function that creates the Featured Titles.
		 */
		private function gather_featured_titles() {
			global $wpdb;

			// Get Featured Titles.
			$table_name_featured = $wpdb->prefix . 'wpbooklist_jre_saved_books_for_featured';
			$transient_name      = 'wpbl_' . md5( 'SELECT * FROM ' . $table_name_featured );
			$transient_exists    = $this->transients->existing_transient_check( $transient_name );
			if ( $transient_exists ) {
				$this->featured_results = $transient_exists;
			} else {
				$query                  = 'SELECT * FROM ' . $table_name_featured;
				$this->featured_results = $this->transients->create_transient( $transient_name, 'wpdb->get_results', $query, MONTH_IN_SECONDS );
			}
		}

		/**
		 *  Function that actually outputs the HTML.
		 */
		private function output_saved_book() {
			$string1 = '<div id="wpbooklist_top_top_div">
					<div id="wpbooklist_top_display_container">
						<table>
							<tbody>
								<tr>
									<td id="wpbooklist_image_saved_border">
										<div id="wpbooklist_display_image_container">';

			// Determine which image to use for the title.
			$string2 = '';
			if ( null === $this->hidebookimage || '0' === $this->hidebookimage ) {
				if ( null === $this->image ) {
					$string2 = '<img id="wpbooklist_cover_image_popup" src="' . ROOT_IMG_URL . 'image_unavaliable.png"/>';
				} else {
					$string2 = '<img id="wpbooklist_cover_image_popup" src="' . $this->image . '"/>';
				}
			}

			$string3 = '<input type="submit" id="wpbooklist_desc_button" value="Description, Notes & Reviews"></input>';

			$string5 = '';
			$string4 = '';
			if ( ( null === $this->hideratingbook || '0' === $this->hideratingbook ) && ( 0 !== $this->rating ) ) {
				$string4 = '<p class="wpbooklist-share-text">' . $this->trans->trans_446 . '</p> 
				<div class="wpbooklist-line-7"></div>';

				switch ( $this->rating ) {
					case '5':
						$string5 = '<img style="width: 50px;" src="' . ROOT_IMG_URL . '5star.jpg" />';
						break;
					case '4.5':
						$string5 = '<img style="width: 50px;" src="' . ROOT_IMG_URL . '4halfstar.jpg" />';
						break;
					case '4':
						$string5 = '<img style="width: 50px;" src="' . ROOT_IMG_URL . '4star.jpg" />';
						break;
					case '3.5':
						$string5 = '<img style="width: 50px;" src="' . ROOT_IMG_URL . '3halfstar.jpg" />';
						break;
					case '3':
						$string5 = '<img style="width: 50px;" src="' . ROOT_IMG_URL . '3star.jpg" />';
						break;
					case '2.5':
						$string5 = '<img style="width: 50px;" src="' . ROOT_IMG_URL . '2halfstar.jpg" />';
						break;
					case '2':
						$string5 = '<img style="width: 50px;" src="' . ROOT_IMG_URL . '2star.jpg" />';
						break;
					case '1.5':
						$string5 = '<img style="width: 50px;" src="' . ROOT_IMG_URL . '1halfstar.jpg" />';
						break;
					case '1':
						$string5 = '<img style="width: 50px;" src="' . ROOT_IMG_URL . '1star.jpg" />';
						break;
					default:
						$string5 = '<p style="margin:0px;font-size:10px; font-variant:all-small-caps; margin-left:5px;">' . $this->trans->trans_448 . '</p>';
						break;
				}
			}

			if ( '1' !== $this->hidefacebook || '1' !== $this->hidetwitter || '1' !== $this->hidemessenger  || '1' !== $this->hidepinterest || '1' !== $this->hideemail ) {

				$string6 = '<p class="wpbooklist-share-text">' . $this->trans->trans_447 . '</p>
				<div class="wpbooklist-line-4"></div>';

				if ( null === $this->hidefacebook || '0' === $this->hidefacebook ) {
					$string7 = '<div class="addthis_sharing_toolbox addthis_default_style" style="cursor:pointer"><a style="cursor:pointer;" href="" addthis:title="' . $this->title . '" addthis:description="' . htmlspecialchars( addslashes( $this->description ) ) . '" addthis:url="' . $this->amazon_detail_page . '" class="addthis_button_facebook"></a></div>';
				} else {
					$string7 = '';
				}

				if ( null === $this->hidetwitter || '0' === $this->hidetwitter ) {
					$string8 = '<div class="addthis_sharing_toolbox addthis_default_style" style="cursor:pointer"><a style="cursor:pointer;" href="" addthis:title="' . $this->title . '" addthis:description="' . htmlspecialchars( addslashes( $this->description ) ) . '" addthis:url="' . $this->amazon_detail_page . '" class="addthis_button_twitter"></a></div>';
				} else {
					$string8 = '';
				}

				// Google Plus no longer exists! Long Live Google Plus!
				$string9 = '';

				if ( null === $this->hidepinterest || '0' === $this->hidepinterest ) {
					$string10 = '<div class="addthis_sharing_toolbox addthis_default_style" style="cursor:pointer"><a style="cursor:pointer;" href="" addthis:title="' . $this->title . '" addthis:description="' . htmlspecialchars( addslashes( $this->description ) ) . '" addthis:url="' . $this->amazon_detail_page . '" class="addthis_button_pinterest_share"></a></div>';
				} else {
					$string10 = '';
				}

				if ( null === $this->hidemessenger || '0' === $this->hidemessenger ) {
					$string11 = '<div class="addthis_sharing_toolbox addthis_default_style" style="cursor:pointer"><a style="cursor:pointer;" href="" addthis:title="' . $this->title . '" addthis:description="' . htmlspecialchars( addslashes( $this->description ) ) . '" addthis:url="' . $this->amazon_detail_page . '" class="addthis_button_messenger"></a></div>';
				} else {
					$string11 = '';
				}

				if ( null === $this->hideemail || '0' === $this->hideemail ) {
					$string12 = '<div class="addthis_sharing_toolbox addthis_default_style" style="cursor:pointer"><a style="cursor:pointer;" href="" addthis:title="' . $this->title . '" addthis:description="' . htmlspecialchars( addslashes( $this->description ) ) . '" addthis:url="' . $this->amazon_detail_page . '" class="addthis_button_gmail"></a></div>';
				} else {
					$string12 = '';
				}
			} else {
				$string6  = '';
				$string7  = '';
				$string8  = '';
				$string9  = '';
				$string10 = '';
				$string11 = '';
				$string12 = '';
			}

			$string13 = '</div></div></td></table></div></td></tr></tbody><a name="desc_scroll"></a></table>';

			$string14 = '<div id="wpbooklist_display_table">
				<table id="wpbooklist_display_table_2">';

			$string15 = '';
			$string16 = '';
			$string17 = '';
			if ( '1' !== $this->hidebooktitle ) {

				$string15 = '<tr>
					<td id="wpbooklist_title"><div';

				if ( null !== $this->titlebookcolor ) {
					$string16 = 'data-modifycolor=false style="color:#' . $this->titlebookcolor . '"';
				} else {
					$string16 = '';
				}

				$string17 = ' id="wpbooklist_title_div">' . htmlspecialchars_decode( stripslashes( $this->title ) ) . '</div>
					</td>
				</tr>';
			}

			$string18 = '';

			if ( '1' !== $this->hideauthor ) {
				$string18 = '<tr>
					<td>
						<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">' . $this->trans->trans_14 . ': </span><span class="wpbooklist-bold-stats-value">' . $this->author . '</span>
					</td>
				</tr>
				';
			}

			$string19 = '';
			if ( '1' === $this->enablepurchase && null !== $this->price && '' !== $this->price && '1' !== $this->hidecolorboxbuyprice ) {
				if ( has_filter( 'wpbooklist_append_to_colorbox_price' ) ) {
					$string19 = apply_filters( 'wpbooklist_append_to_colorbox_price', $this->price . '---' . $this->author_url );
				}
			}

			$ebook_upper_string = '';
			if ( has_filter( 'wpbooklist_append_to_colorbox_ebook_download' ) && ( '' !== $this->ebook && null !== $this->ebook ) ) {
					$ebook_upper_string = apply_filters( 'wpbooklist_append_to_colorbox_ebook_download', $this->ebook );
			}

			$string20 = '';
			$string21 = '';
			$string22 = '';
			if ( '1' !== $this->hidegenres ) {
				$string20 = '<tr>
						<td>';

				if ( null === $this->genres || '' === $this->genres ) {
					$string21 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">' . $this->trans->trans_146 . ': </span><span class="wpbooklist-bold-stats-value">' . $this->trans->trans_448 . '</span>';
				} else {
					$string21 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">' . $this->trans->trans_146 . ': </span><span class="wpbooklist-bold-stats-value">' . $this->genres . '</span>';
				}

				$string22 = '</td>
					</tr>';
			}

			$string23 = '';
			$string24 = '';
			$string25 = '';
			if ( '1' !== $this->hidepages ) {
				$string23 = '<tr>
						<td>';

				if ( null === $this->pages ) {
					$string24 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">' . $this->trans->trans_142 . ': </span><span class="wpbooklist-bold-stats-value">' . $this->trans->trans_448 . '</span>';
				} else {
					$string24 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">' . $this->trans->trans_142 . ': </span><span class="wpbooklist-bold-stats-value">' . $this->pages . '</span>';
				}

				$string25 = '</td>
					</tr>';
			}

			$string26 = '';
			$string27 = '';
			$string28 = '';
			if ( '1' !== $this->hidepublisher ) {
				$string26 = '<tr>
						<td>';

				if ( null === $this->publisher ) {
					$string27 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">' . $this->trans->trans_141 . ': </span><span class="wpbooklist-bold-stats-value">' . $this->trans->trans_448 . '</span>';
				} else {
					$string27 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">' . $this->trans->trans_141 . ': </span><span class="wpbooklist-bold-stats-value">' . stripslashes( stripslashes( $this->publisher ) ) . '</span>';
				}

				$string28 = '</td>
					</tr>';
			}

			$string92 = '';
			$string93 = '';
			$string94 = '';
			if ( '1' !== $this->hidesubgenre ) {
				$string92 = '<tr>
						<td>';
				if ( null === $this->subgenres || '' === $this->subgenres ) {
					$string93 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">' . $this->trans->trans_147 . ': </span><span class="wpbooklist-bold-stats-value">' . $this->trans->trans_448 . '</span>';
				} else {
					$string93 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">' . $this->trans->trans_147 . ': </span><span class="wpbooklist-bold-stats-value">' . stripslashes( stripslashes( $this->subgenres ) ) . '</span>';
				}

				$string94 = '</td>
					</tr>';
			}

			$string95 = '';
			$string96 = '';
			$string97 = '';
			if ( '1' !== $this->hidecountry ) {
				$string95 = '<tr>
						<td>';

				if ( null === $this->country || '' === $this->country ) {
					$string96 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">' . $this->trans->trans_273 . ': </span><span class="wpbooklist-bold-stats-value">' . $this->trans->trans_448 . '</span>';
				} else {
					$string96 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">' . $this->trans->trans_273 . ': </span><span class="wpbooklist-bold-stats-value">' . stripslashes( stripslashes( $this->country ) ) . '</span>';
				}

				$string97 = '</td>
					</tr>';
			}

			$string29 = '';
			$string30 = '';
			$string31 = '';
			if ( '1' !== $this->hidepubdate ) {
				$string29 = '<tr>
						<td>';
				if ( null === $this->pub_year ) {
					$string30 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">' . $this->trans->trans_143 . ': </span><span class="wpbooklist-bold-stats-value">' . $this->trans->trans_448 . '</span>';
				} else {
					$string30 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">' . $this->trans->trans_143 . ': </span><span class="wpbooklist-bold-stats-value">' . $this->pub_year . '</span>';
				}

				$string31 = '</td>
					</tr>';
			}

			$string32 = '';
			$string33 = '';
			$string34 = '';
			$string35 = '';
			if ( '1' !== $this->hidefinished ) {
				$string32 = '<tr>
						<td>';

				if ( 'Yes' === $this->finished ) {
					if ( '0' === $this->date_finished || null === $this->date_finished ) {
						$string33 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">' . $this->trans->trans_223 . ' </span><span class="wpbooklist-bold-stats-value">' . $this->trans->trans_131 . '</span>';
					} else {
						$string33 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">' . $this->trans->trans_223 . ' </span><span class="wpbooklist-bold-stats-value">' . $this->trans->trans_450 . ' ' . $this->date_finished . '</span>';
					}
				} else {
					$string34 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">' . $this->trans->trans_223 . ' </span><span class="wpbooklist-bold-stats-value">' . $this->trans->trans_451 . '</span>';
				}

				$string35 = '</td>
					</tr>';
			}

			$string36 = '';
			$string37 = '';
			$string38 = '';
			if ( '1' !== $this->hidesigned ) {

				$string36 = '<tr>
						<td>';

				if ( 'true' === $this->signed ) {
					$string37 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">' . $this->trans->trans_10 . '? </span><span class="wpbooklist-bold-stats-value">' . $this->trans->trans_131 . '</span>';
				} else {
					$string37 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">' . $this->trans->trans_10 . '? </span><span class="wpbooklist-bold-stats-value">' . $this->trans->trans_132 . '</span>';
				}
				$string38 = '</td>
					</tr>';
			}

			// If the Custom Fields Extension is active...
			$customfields_basic_string = '';
			if ( has_filter( 'wpbooklist_append_to_book_view_basic_fields' ) ) {
					$customfields_basic_string = apply_filters( 'wpbooklist_append_to_book_view_basic_fields', $this->saved_book );
			}

			// If the Custom Fields Extension is active...
			$customfields_text_link_string = '';
			if ( has_filter( 'wpbooklist_append_to_book_view_text_link_fields' ) ) {
					$customfields_text_link_string = apply_filters( 'wpbooklist_append_to_book_view_text_link_fields', $this->saved_book );
			}

			// If the Custom Fields Extension is active...
			$customfields_dropdown_string = '';
			if ( has_filter( 'wpbooklist_append_to_book_view_dropdown_fields' ) ) {
					$customfields_dropdown_string = apply_filters( 'wpbooklist_append_to_book_view_dropdown_fields', $this->saved_book );
			}

			// If the Custom Fields Extension is active...
			$customfields_image_link_string = '';
			if ( has_filter( 'wpbooklist_append_to_book_view_image_link_fields' ) ) {
					$customfields_image_link_string = apply_filters( 'wpbooklist_append_to_book_view_image_link_fields', $this->saved_book );
			}

			// If the Custom Fields Extension is active...
			$customfields_paragraph_string = '';
			if ( has_filter( 'wpbooklist_append_to_book_view_paragraph_fields' ) ) {
					$customfields_paragraph_string = apply_filters( 'wpbooklist_append_to_book_view_paragraph_fields', $this->saved_book );
			}

			$string39 = '';
			$string40 = '';
			$string41 = '';
			if ( '1' !== $this->hidefirstedition ) {

				$string39 = '<tr>
					<td>';

				if ( '' !== $this->edition ) {
					$string40 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">' . $this->trans->trans_155 . ': </span>' . $this->edition . '';
				} else {
					$string40 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">' . $this->trans->trans_155 . ': </span><span class="wpbooklist-bold-stats-value">' . $this->trans->trans_221 . '</span>';
				}

				$string41 = '</td>
					</tr>';
			}

			$string42 = '';
			if ( '1' !== $this->hidebookpage && null !== $this->page_id && 'false' !== $this->page_id && $this->page_id !== $this->trans->trans_221 ) {
				$string42 = '<tr>
					<td>
						<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold"><a id="wpbooklist-purchase-book-view" href="' . get_permalink( $this->page_id ) . '"><span class="wpbooklist-bold-stats-page">' . $this->trans->trans_452 . '</span></a></span>
						</td>
				</tr>';
			}

			$string43 = '';
			if ( '1' !== $this->hidebookpost && null !== $this->post_id && 'false' !== $this->post_id && $this->post_id !== $this->trans->trans_221 ) {
				$string43 = '<tr>
					<td>
						<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold"><a id="wpbooklist-purchase-book-view" href="' . get_permalink( $this->post_id ) . '"><span class="wpbooklist-bold-stats-page">' . $this->trans->trans_453 . '</span></a></span>
						</td>
				</tr>';

			}

			$string44 = '';
			$string45 = '';
			$string46 = '';
			if ( ( null !== $this->enablepurchase && 0 !== $this->enablepurchase ) && null !== $this->price && '' !== $this->author_url ) {
				$string44 = '
				<tr>
					<td>
						<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold"><a';

				if ( null !== $this->purchasebookcolor ) {
					$string45 = 'data-modifycolor=false style="color:#' . $this->purchasebookcolor . '"';

				}

				if ( ( null !== $this->enablepurchase && 0 !== $this->enablepurchase ) && null !== $this->author_url && '' !== $this->author_url && '1' !== $this->hidecolorboxbuyprice ) {
					$string46 = '';
					if ( has_filter( 'wpbooklist_append_to_colorbox_purchase_text_link' ) ) {
						$string46 = apply_filters( 'wpbooklist_append_to_colorbox_purchase_text_link', $this->author_url );
					}
				}
			}

			$string46 = $string46 . '</a>';

			// 1 for hidden, 0 for not hidden, null for not set.
			if ( ( null === $this->hidekobopurchase || '0' === $this->hidekobopurchase && ( null !== $this->kobo_link && 'http://store .kobobooks.com/en-ca/Search?Query=' !== $this->kobo_link ) ) || ( null === $this->hidebampurchase || '0' === $this->hidebampurchase && ( null !== $this->bam_link && 'http://www.booksamillion.com/p/' !== $this->bam_link ) ) || ( null === $this->hideamazonpurchase || '0' === $this->hideamazonpurchase && ( null !== $this->amazon_detail_page ) ) || ( null === $this->hidebnpurchase || '0' === $this->hidebnpurchase && ( null !== $this->isbn ) ) || ( null === $this->hidegooglepurchase || '0' === $this->hidegooglepurchase && ( null !== $this->google_preview ) ) || ( null === $this->hideitunespurchase || '0' === $this->hideitunespurchase && ( null !== $this->appleibookslink ) ) || ( ( true === $this->storefront_active ) && ( null === $this->hidecolorboxbuyimg || '0' === $this->hidecolorboxbuyimg ) && ( null !== $this->author_url && '' !== $this->author_url ) ) ) {

				$string47 = '</td></tr><tr>
					<td><div class="wpbooklist-line-2"></div></td>
				</tr>
				<tr>
					<td class="wpbooklist-purchase-title" colspan="2">' . $this->trans->trans_454 . ':</td>
				</tr>
				<tr>
					<td><div class="wpbooklist-line"></div></td>
				</tr>
				<tr>
					<td>
						<a';
			} else {
				$string47 = '<a';
			}

			$string48 = '';
			if ( null === $this->amazon_detail_page || '1' === $this->hideamazonpurchase ) {
				$string48 = ' style="display:none;"';
			}

			$string49 = ' class="wpbooklist-purchase-img" href="' . $this->amazon_detail_page . '" target="_blank">
			<img src="' . ROOT_IMG_URL . 'amazon.png" /></a>
			<a ';

			if ( preg_match( '/[a-z]/i', $this->isbn ) ) {
				$string49 = ' class="wpbooklist-purchase-img" href="' . $this->amazon_detail_page . '" target="_blank">
					<img src="' . ROOT_IMG_URL . 'kindle.png" /></a>
					<a ';
			} else {
				$string49 = ' class="wpbooklist-purchase-img" href="' . $this->amazon_detail_page . '" target="_blank">
					<img src="' . ROOT_IMG_URL . 'amazon.png" /></a>
					<a ';
			}

			$string50 = '';
			if ( null === $this->isbn || '1' === $this->hidebnpurchase ) {
				$string50 = ' style="display:none;"';
			}

			$string51 = ' class="wpbooklist-purchase-img" href="' . $this->bn_link . '" target="_blank">
			<img src="' . ROOT_IMG_URL . 'bn.png" /></a>
			<a ';

			$string52 = '';
			if ( null === $this->google_preview || '1' === $this->hidegooglepurchase ) {
				$string52 = ' style="display:none;"';
			}

			$string53 = ' class="wpbooklist-purchase-img" href="' . $this->google_preview . '" target="_blank">
			<img src="' . ROOT_IMG_URL . 'googlebooks.png" /></a><a ';

			$string54 = '';
			if ( null === $this->appleibookslink || '1' === $this->hideitunespurchase ) {
				$string54 = ' style="display:none;"';
			}

			$string55 = ' class="wpbooklist-purchase-img" href="' . $this->appleibookslink . '" target="_blank">
					<img src="' . ROOT_IMG_URL . 'ibooks.png" id="wpbooklist-itunes-img" /></a>';

			$string84 = '<a ';
			if ( '1' === $this->hidekobopurchase || null === $this->kobo_link || 'http://store.kobobooks.com/en-ca/Search?Query=' === $this->kobo_link ) {
				$string84 = $string84 . ' style="display:none;"';
			}
			$string85 = ' class="wpbooklist-purchase-img" href="' . $this->kobo_link . '" target="_blank">
					<img src="' . ROOT_IMG_URL . 'kobo-icon.png" /></a>';

			$string86 = '<a ';
			if ( '1' === $this->hidebampurchase || null === $this->bam_link || ( 'http://www.booksamillion.com/p/' === $this->bam_link ) ) {
				$string86 = $string86 . ' style="display:none;"';
			}
			$string87 = ' class="wpbooklist-purchase-img" href="' . $this->bam_link . '" target="_blank">
					<img src="' . ROOT_IMG_URL . 'bam-icon.jpg" /></a>';

			// If we've enabled the Purchase Links on the settings page, if the title has a specified Author URL to link to, and if we haven't choosen to hide the Colorbox Purchase link...
			$string57 = '';
			if ( '1' === $this->enablepurchase && null !== $this->author_url && 'https://' !== $this->author_url && 'http://' !== $this->author_url && '1' !== $this->hidecolorboxbuyimg ) {
				if ( has_filter( 'wpbooklist_append_to_colorbox_purchase_image_link' ) ) {
					$string57 = apply_filters( 'wpbooklist_append_to_colorbox_purchase_image_link', $this->author_url );
				}
			}

			$string58 = '</td>   
						</tr>
						<tr>';

			$string59 = '';
			// 1 for hidden, 0 for not hidden, null for not set.
			if ( ( null === $this->hidekobopurchase || '0' === $this->hidekobopurchase && ( null !== $this->kobo_link && 'http://store .kobobooks.com/en-ca/Search?Query=' !== $this->kobo_link ) ) || ( null === $this->hidebampurchase || '0' === $this->hidebampurchase && ( null !== $this->bam_link && 'http://www.booksamillion.com/p/' !== $this->bam_link ) ) || ( null === $this->hideamazonpurchase || '0' === $this->hideamazonpurchase && ( null !== $this->amazon_detail_page ) ) || ( null === $this->hidebnpurchase || '0' === $this->hidebnpurchase && ( null !== $this->isbn ) ) || ( null === $this->hidegooglepurchase || '0' === $this->hidegooglepurchase && ( null !== $this->google_preview ) ) || ( null === $this->hideitunespurchase || '0' === $this->hideitunespurchase && ( null !== $this->appleibookslink ) ) || ( ( true === $this->storefront_active ) && ( null === $this->hidecolorboxbuyimg || '0' === $this->hidecolorboxbuyimg ) && ( null !== $this->author_url && '' !== $this->author_url ) ) ) {

				$string59 = '</td>   
				</tr>
				<tr>
					<td><div class="wpbooklist-line-3"></div></td>
				</tr>
				<tr>';
			}

			$string60 = '';
			if ( null === $this->hidegoodreadswidget || '0' === $this->hidegoodreadswidget && ( '' !== $this->isbn || null !== $this->isbn ) ) {
				$string60 = '<td> 
					<div id="gr_add_to_books">
					<div class="gr_custom_each_container_">
					  <a target="_blank" style="border:none" href="https://www.goodreads.com/book/isbn/' . $this->isbn . '"><img alt="goodreads-image-of-book" src="https://www.goodreads.com/images/atmb_add_book-70x25.png" /></a>
					</div>
				  </div>
				  <script src="https://www.goodreads.com/book/add_to_books_widget_frame/' . $this->isbn . '?atmb_widget%5Bbutton%5D=atmb_widget_1.png"></script></td>';
			}

			$string61 = '</tr>
					</table>
					</div>
						 </div>		 
						<div id="wpbooklist_desc_id">';

			$string62 = '';
			$string63 = '';
			$string64 = '';
			if ( ( null === $this->hidesimilar || '0' === $this->hidesimilar ) && null !== $this->similar_products_array ) {
				if ( null !== $this->similar_products ) {
					$string62 = '<div class="wpbooklist-similar-featured-div">
							<p id="wpbooklist-similar-titles-id" class="wpbooklist_description_p">' . $this->trans->trans_455 . '</p> 
								<table class="wpbooklist-similar-titles-table"> <tr>';

					$string63 = '';
					foreach ( $this->similar_products_array as $key => $prod ) {

						$arr   = explode( '---', $prod, 2 );
						$asin  = $arr[0];
						$image = '';
						$url = '';

						if ( array_key_exists( 1, $arr ) && false !== stripos( $arr[1], '---' ) ) {
							$split = explode( '---', $arr[1] );
							$image = $split[1];
						} else {
							$image = 'http://images.amazon.com/images/P/' . $asin . '.01.LZZZZZZZ.jpg?rand=' . uniqid();
							$url   = 'https://www.amazon.com/dp/' . $asin . '?tag=' . $this->amazonaff;
						}

						// Now build the link - first try building a Post link, then a page link, then an amazon link if the asin was provided, then just no link at all.
						if ( '' === $url ) {
							if ( false !== stripos( $arr[1], '---' ) ) {
								$split = explode( '---', $arr[1] );
								if ( array_key_exists( 2, $split ) && '' !== $split[2] && 'No' !== $split[2] && null !== $split[2] ) {
									$url = get_permalink( $split[2] );
								}
							} elseif ( false !== stripos( $asin, 'asin' ) ) {

								$finalasin = explode( 'asin', $asin );
								$url       = 'https://www.amazon.com/dp/' . $finalasin[0] . '?tag=' . $this->amazonaff;
							} else {
								$url = 'nourl';
							}
						}

						if ( null !== $asin && '' !== $asin ) {
							if ( 6 === $key ) {
								if ( 'nourl' === $url || '' === $url ) {
									$string63 = $string63 . '</tr><tr><td><img class="wpbooklist-similar-image" src="' . $image . '" /></td>';
								} else {
									$string63 = $string63 . '</tr><tr><td><a class="wpbooklist-similar-link" target="_blank" href="' . $url . '"><img class="wpbooklist-similar-image" src="' . $image . '" /></a></td>';
								}
							} else {
								if ( 'nourl' === $url  || '' === $url ) {
									$string63 = $string63 . '<td><img class="wpbooklist-similar-image" src="' . $image . '" /></td>';
								} else {
									$string63 = $string63 . '<td><a class="wpbooklist-similar-link" target="_blank" href="' . $url . '"><img class="wpbooklist-similar-image" src="' . $image . '" /></a></td>';
								}
							}
						}
					}

					$string64 = '</tr>
								</table>
							</div>';
				}
			}

			// Building out the Additional Images section.
			$additional_images = '';
			if ( '1' !== $this->hideadditionalimgs && ( ( null !== $this->backcover && '' !== $this->backcover ) || ( null !== $this->additionalimage1 && '' !== $this->additionalimage1 ) || ( null !== $this->additionalimage2 && '' !== $this->additionalimage2 ) ) ) {

				$additional_images = '<p class="wpbooklist_description_p">' . $this->trans->trans_584 . '</p><div class="wpbooklist_desc_p_class"  id="wpbooklist-additional-images-id">';

				$img_array = array(
					$this->backcover,
					$this->additionalimage1,
					$this->additionalimage2,
				);

				foreach ( $img_array as $key => $img ) {
					if ( '' !== $img && null !== $img ) {
						$additional_images = $additional_images . '<img class="wpbooklist-additional-img-colorbox" src="' . $img . '"  />';
					}
				}

				$additional_images = $additional_images . '</div>';

			}

			// If the ebook Extension is active...
			$ebook_string = '';
			if ( has_filter( 'wpbooklist_append_to_colorbox_ebook' ) && ( '' !== $this->ebook && null !== $this->ebook ) ) {
					$ebook_string = apply_filters( 'wpbooklist_append_to_colorbox_ebook', $this->ebook );
			}

			// If the Comments Extension is active...
			$comments_array = array( $this->id, $this->library, $this->book_uid, $this->title );
			$comments_string = '';
			if ( has_filter( 'wpbooklist_append_to_colorbox_comments' ) ) {
					$comments_string = apply_filters( 'wpbooklist_append_to_colorbox_comments', $comments_array );
			}

			$string65 = '';
			$string66 = '';
			$string67 = '';
			if ( null === $this->hidefeaturedtitles || '0' === $this->hidefeaturedtitles ) {
				if ( 0 < count( $this->featured_results ) ) {
					$string65 = '<div class="wpbooklist-similar-featured-div" style="margin-left:5px">
						<p id="wpbooklist-similar-titles-id" class="wpbooklist_description_p">' . $this->trans->trans_456 . '</p> 
						<table class="wpbooklist-similar-titles-table"> <tr>';
					$string66 = '';
					foreach ( $this->featured_results as $key => $featured ) {

						$image = $featured->coverimage;
						$url   = $featured->amazondetailpage;

						if ( 51 < strlen( $image ) ) {
							if ( 5 === $key ) {
								$string66 = $string64 . '</tr><tr><td><a class="wpbooklist-similar-link" target="_blank" href="' . $url . '"><img class="wpbooklist-similar-image" src="' . $image . '" /></a></td>';
							} else {
								$string66 = $string64 . '<td><a class="wpbooklist-similar-link" target="_blank" href="' . $url . '"><img class="wpbooklist-similar-image" src="' . $image . '" /></a></td>';
							}
						}
					}

					$string67 = '</tr>
						</table>
					</div>';
				}
			}

			$string68   = '';
			$lend_array = array( $this->id, $this->library );
			if ( has_filter( 'wpbooklist_append_to_colorbox_lending_info' ) ) {
					$string68 = apply_filters( 'wpbooklist_append_to_colorbox_lending_info', $lend_array );
			}

			$kindle_array = array( $this->isbn, $this->amazonaff );
			$isbn_test    = preg_match( '/[a-z]/i', $this->isbn );
			if ( ( null === $this->hidekindleprev || '0' === $this->hidekindleprev ) && $isbn_test ) {
				if ( has_filter( 'wpbooklist_add_to_colorbox_kindle' ) ) {
					$string68 = $string68 . apply_filters( 'wpbooklist_add_to_colorbox_kindle', $kindle_array );
				}
			}

			if ( null === $this->hidegoogleprev || '0' === $this->hidegoogleprev ) {
				if ( has_filter( 'wpbooklist_add_to_colorbox_google' ) ) {
					$string68 = $string68 . apply_filters( 'wpbooklist_add_to_colorbox_google', $this->isbn );
				}
			}

			$string69 = '';
			if ( null === $this->hidedescription || '0' === $this->hidedescription ) {
				$string68 = $string68 . '<p class="wpbooklist_description_p" id="wpbooklist-desc-title-id">' . $this->trans->trans_457 . '</p>';

				if ( null === $this->description ) {
					$string69 = '<p class="wpbooklist_desc_p_class">' . $this->trans->trans_448 . '</p>';
				} else {
					$string69 = '<div class="wpbooklist_desc_p_class">' . stripslashes( html_entity_decode( $this->description  ) ) . '</div>';
				}
			}

			$string70 = '';
			if ( ( null === $this->hideamazonreview || '0' === $this->hideamazonreview ) && ( null !== $this->review_iframe ) ) {
				$string70 = '<p class="wpbooklist_description_p" id="wpbooklist-amazon-review-title-id">' . $this->trans->trans_266 . ':</p> 
					<p class="wpbooklist_desc_p_class"><iframe id="wpbooklist-review-iframe" src="' . $this->review_iframe . '"></iframe></p>';
			}

			$string71 = '';
			$string72 = '';
			if ( null === $this->hidenotes || '0' === $this->hidenotes ) {
				$string71 = '<p class="wpbooklist_description_p" id="wpbooklist-notes-title-id">' . $this->trans->trans_153 . '</p>';

				if ( null === $this->notes ) {
					$string72 = '<p class="wpbooklist_desc_p_class">' . $this->trans->trans_458 . '</p>';
				} else {
					$string72 = '<p class="wpbooklist_desc_p_class">' . stripslashes( html_entity_decode( $this->notes ) ) . '</p>';
				}
			}

			$string73 = '';
			// 1 for hidden, 0 for not hidden, null for not set.
			if ( ( null === $this->hidekobopurchase || '0' === $this->hidekobopurchase && ( null !== $this->kobo_link && 'http://store .kobobooks.com/en-ca/Search?Query=' !== $this->kobo_link ) ) || ( null === $this->hidebampurchase || '0' === $this->hidebampurchase && ( null !== $this->bam_link && 'http://www.booksamillion.com/p/' !== $this->bam_link ) ) || ( null === $this->hideamazonpurchase || '0' === $this->hideamazonpurchase && ( null !== $this->amazon_detail_page ) ) || ( null === $this->hidebnpurchase || '0' === $this->hidebnpurchase && ( null !== $this->isbn ) ) || ( null === $this->hidegooglepurchase || '0' === $this->hidegooglepurchase && ( null !== $this->google_preview ) ) || ( null === $this->hideitunespurchase || '0' === $this->hideitunespurchase && ( null !== $this->appleibookslink ) ) || ( ( true === $this->storefront_active ) && ( null === $this->hidecolorboxbuyimg || '0' === $this->hidecolorboxbuyimg ) && ( null !== $this->author_url && '' !== $this->author_url ) ) ) {

			} else {
				$string73 = '<div style="display:none;" >';
			}

			$string74 = '<div class="wpbooklist-line-5"></div>
			<p id="wpbooklist-purchase-title-id-bottom" class="wpbooklist-purchase-title">
				' . __( 'Purchase This Book At:', 'wpbooklist' ) . '
			</p>
			<div class="wpbooklist-line-6"></div>
			<a';

			$string75 = '';
			if ( null === $this->amazon_detail_page || '1' === $this->hideamazonpurchase ) {
				$string75 = ' style="display:none;"';
			}

			if ( preg_match( '/[a-z]/i', $this->isbn ) ) {
				$string76 = ' class="wpbooklist-purchase-img" href="' . $this->amazon_detail_page . '" target="_blank"><img src="' . ROOT_IMG_URL . 'kindle.png" /></a><a';
			} else {
				$string76 = ' class="wpbooklist-purchase-img" href="' . $this->amazon_detail_page . '" target="_blank"><img src="' . ROOT_IMG_URL . 'amazon.png" /></a><a';
			}

			$string77 = '';
			if ( null === $this->isbn || '1' === $this->hidebnpurchase ) {
				$string77 = ' style="display:none;"';
			}

			$string78 = ' class="wpbooklist-purchase-img" href="http://www.barnesandnoble.com/s/' . $this->isbn . '" target="_blank">
			<img src="' . ROOT_IMG_URL . 'bn.png" /></a><a ';

			$string79 = '';
			if ( null === $this->google_preview || '1' === $this->hidegooglepurchase ) {
				$string79 = ' style="display:none;"';
			}

			$string80 = ' class="wpbooklist-purchase-img" href="' . $this->google_preview . '" target="_blank">
					<img src="' . ROOT_IMG_URL . 'googlebooks.png" /></a><a ';

			$string81 = '';
			if ( null === $this->appleibookslink || '1' === $this->hideitunespurchase ) {
				$string81 = ' style="display:none;"';
			}

			$string82 = ' class="wpbooklist-purchase-img" href="' . $this->appleibookslink . '" target="_blank">
					<img id="wpbooklist-itunes-img" src="' . ROOT_IMG_URL . 'ibooks.png" /></a>';

			$string88 = '<a ';
			if ( '1' === $this->hidekobopurchase || null === $this->kobo_link || 'http://store.kobobooks.com/en-ca/Search?Query=' === $this->kobo_link ) {
				$string88 = $string88 . ' style="display:none;"';
			}

			$string89 = ' class="wpbooklist-purchase-img" href="' . $this->kobo_link . '" target="_blank">
					<img src="' . ROOT_IMG_URL . 'kobo-icon.png" /></a>';

			$string90 = '<a ';
			if ( '1' === $this->hidebampurchase || null === $this->bam_link || ( 'http://www.booksamillion.com/p/' === $this->bam_link ) ) {
				$string90 = $string90 . ' style="display:none;"';
			}
			$string91 = ' class="wpbooklist-purchase-img" href="' . $this->bam_link . '" target="_blank">
					<img src="' . ROOT_IMG_URL . 'bam-icon.jpg" /></a>';

			// If we've enabled the Purchase Links on the settings page, if the title has a specified Author URL to link to, and if we haven't choosen to hide the Colorbox Purchase link...
			if ( '1' === $this->enablepurchase && null !== $this->author_url && 'https://' !== $this->author_url && 'http://' !== $this->author_url && '1' !== $this->hidecolorboxbuyimg ) {
				if ( has_filter( 'wpbooklist_append_to_colorbox_purchase_image_link' ) ) {
					$string91 = $string91 . apply_filters( 'wpbooklist_append_to_colorbox_purchase_image_link', $this->author_url );
				}
			}

			$string83 = '';
			if ( ( null === $this->hidekobopurchase || '0' === $this->hidekobopurchase && ( null !== $this->kobo_link && 'http://store .kobobooks.com/en-ca/Search?Query=' !== $this->kobo_link ) ) || ( null === $this->hidebampurchase || '0' === $this->hidebampurchase && ( null !== $this->bam_link && 'http://www.booksamillion.com/p/' !== $this->bam_link ) ) || ( null === $this->hideamazonpurchase || '0' === $this->hideamazonpurchase && ( null !== $this->amazon_detail_page ) ) || ( null === $this->hidebnpurchase || '0' === $this->hidebnpurchase && ( null !== $this->isbn ) ) || ( null === $this->hidegooglepurchase || '0' === $this->hidegooglepurchase && ( null !== $this->google_preview ) ) || ( null === $this->hideitunespurchase || '0' === $this->hideitunespurchase && ( null !== $this->appleibookslink ) ) || ( ( true === $this->storefront_active ) && ( null === $this->hidecolorboxbuyimg || '0' === $this->hidecolorboxbuyimg ) && ( null !== $this->author_url && '' !== $this->author_url ) ) ) {

			} else {
				$string83 = '</div>';
			}

			$this->output = $string1 . $string2 . $string3 . $string4 . $string5 . $string6 . $string7 . $string8 . $string9 . $string10 . $string11 . $string12 . $string13 . $string14 . $string15 . $string16 . $string17 . $string18 . $string19 . $string20 . $string21 . $string22 . $string92 . $string93 . $string94 . $string23 . $string24 . $string25 . $string26 . $string27 . $string28 . $string95 . $string96 . $string97 . $string29 . $string30 . $string31 . $string39 . $string40 . $string41 . $string32 . $string33 . $string34 . $string35 . $string36 . $string37 . $string38 . $ebook_upper_string . $customfields_basic_string . $customfields_text_link_string . $customfields_dropdown_string . $string42 . $string43 . $customfields_image_link_string . $string44 . $string45 . $string46 . $string47 . $string48 . $string49 . $string50 . $string51 . $string52 . $string53 . $string54 . $string55 . $string84 . $string85 . $string86 . $string87 . $string57 . $string58 . $string59 . $string60 . $string61 . $string62 . $string63 . $string64 . $additional_images . $ebook_string . $comments_string . $string65 . $string66 . $string67 . $string68 . $string69 . $customfields_paragraph_string . $string70 . $string71 . $string72 . $string73 . $string74 . $string75 . $string76 . $string77 . $string78 . $string79 . $string80 . $string81 . $string82 . $string83 . $string88 . $string89 . $string90 . $string91;
		}

		/**
		 *  Function that gathers Bookfinder data.
		 */
		private function gather_bookfinder_data() {
			$this->title                  = $this->book_array['title'];
			$this->author                 = $this->book_array['author'];
			$this->genres                 = $this->book_array['category'];
			$this->appleibookslink            = $this->book_array['itunes_page'];
			$this->pages                  = $this->book_array['pages'];
			$this->pub_year               = $this->book_array['pub_year'];
			$this->publisher              = $this->book_array['publisher'];
			$this->description            = $this->book_array['description'];
			$this->image                  = $this->book_array['image'];
			$this->similar_products_array = array();
			$this->review_iframe          = $this->book_array['reviews'];
			$this->isbn                   = $this->book_array['isbn'];
			$this->amazon_detail_page     = $this->book_array['details'];
			$this->similar_products       = $this->book_array['similar_products'];
			$this->kobo_link              = $this->book_array['kobo_link'];
			$this->bam_link               = $this->book_array['bam_link'];
		}



	}


endif;
