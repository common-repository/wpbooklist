<?php
/**
 * Class WPBookList_Ajax_Functions - class-wpbooklist-ajax-functions.php
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes
 * @version  6.1.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Ajax_Functions', false ) ) :
	/**
	 * WPBookList_Ajax_Functions class. Here we'll do things like enqueue scripts/css, set up menus, etc.
	 */
	class WPBookList_Ajax_Functions {

		/**
		 * Class Constructor - Simply calls the Translations
		 */
		public function __construct() {

			// Get Translations.
			require_once CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
			$this->trans = new WPBookList_Translations();
			$this->trans->trans_strings();

			// Set the date.
			require_once CLASS_UTILITIES_DIR . 'class-wpbooklist-utilities-date.php';
			$utilities_date = new WPBookList_Utilities_Date();
			$this->date     = $utilities_date->wpbooklist_get_date_via_current_time( 'mysql' );

			// Require the Transients file.
			require_once CLASS_TRANSIENTS_DIR . 'class-wpbooklist-transients.php';
			$this->transients = new WPBookList_Transients();
		}

		/**
		 * Callback function for adding a book.
		 */
		public function wpbooklist_dashboard_add_book_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_dashboard_add_book_action_callback', 'security' );

			// First set the variables we'll be passing to class-wpbooklist-book.php to ''.
			$additionalimage1   = '';
			$additionalimage2   = '';
			$amazon_detail_page = '';
			$amazonauth         = '';
			$appleibookslink    = '';
			$asin               = '';
			$author             = '';
			$author2            = '';
			$author3            = '';
			$author_url         = '';
			$sale_url           = '';
			$backcover          = '';
			$bam_link           = '';
			$bn_link            = '';
			$callnumber         = '';
			$category           = '';
			$copies             = '';
			$country            = '';
			$crosssells         = '';
			$date_finished      = '';
			$description        = '';
			$download           = '';
			$edition            = '';
			$finished           = '';
			$first_edition      = '';
			$format             = '';
			$genres             = '';
			$goodreadslink      = '';
			$google_preview     = '';
			$height             = '';
			$illustrator        = '';
			$image              = '';
			$isbn               = '';
			$isbn13             = '';
			$kobo_link          = '';
			$keywords           = '';
			$language           = '';
			$length             = '';
			$library            = '';
			$notes              = '';
			$numberinseries     = '';
			$originalpubyear    = '';
			$originaltitle      = '';
			$othereditions      = '';
			$outofprint         = '';
			$page_yes           = '';
			$pages              = '';
			$post_yes           = '';
			$price              = '';
			$productcategory    = '';
			$pub_year           = '';
			$publisher          = '';
			$purchasenote       = '';
			$rating             = '';
			$regularprice       = '';
			$reviews            = '';
			$salebegin          = '';
			$saleend            = '';
			$saleprice          = '';
			$series             = '';
			$shortdescription   = '';
			$signed             = '';
			$similarbooks       = '';
			$sku                = '';
			$stock              = '';
			$subgenre           = '';
			$subject            = '';
			$swap_yes           = '';
			$title              = '';
			$upsells            = '';
			$use_amazon_yes     = '';
			$virtual            = '';
			$weight             = '';
			$width              = '';
			$woocommerce        = '';
			$woofile            = '';
			$bookaction         = '';
			$ebookurl           = '';

			// First set the variables we'll be passing to class-wpbooklist-book.php to ''.
			if ( isset( $_POST['additionalimage1'] ) ) {
				$additionalimage1 = filter_var( wp_unslash( $_POST['additionalimage1'] ), FILTER_SANITIZE_STRING );
			}

			// First set the variables we'll be passing to class-wpbooklist-book.php to ''.
			if ( isset( $_POST['additionalimage2'] ) ) {
				$additionalimage2 = filter_var( wp_unslash( $_POST['additionalimage2'] ), FILTER_SANITIZE_STRING );
			}

			// First set the variables we'll be passing to class-wpbooklist-book.php to ''.
			if ( isset( $_POST['amazonauth'] ) ) {
				$amazonauth = filter_var( wp_unslash( $_POST['amazonauth'] ), FILTER_SANITIZE_STRING );
			}

			// First set the variables we'll be passing to class-wpbooklist-book.php to ''.
			if ( isset( $_POST['amazondetailpage'] ) ) {
				$amazon_detail_page = filter_var( wp_unslash( $_POST['amazondetailpage'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['appleibookslink'] ) ) {
				$appleibookslink = filter_var( wp_unslash( $_POST['appleibookslink'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['asin'] ) ) {
				$asin = filter_var( wp_unslash( $_POST['asin'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['author'] ) ) {
				$author = filter_var( wp_unslash( $_POST['author'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['author2'] ) ) {
				$author2 = filter_var( wp_unslash( $_POST['author2'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['author3'] ) ) {
				$author3 = filter_var( wp_unslash( $_POST['author3'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['authorurl'] ) ) {
				$author_url = filter_var( wp_unslash( $_POST['authorurl'] ), FILTER_SANITIZE_URL );
			}

			if ( isset( $_POST['saleurl'] ) ) {
				$sale_url = filter_var( wp_unslash( $_POST['saleurl'] ), FILTER_SANITIZE_URL );
			}

			if ( isset( $_POST['backcover'] ) ) {
				$backcover = filter_var( wp_unslash( $_POST['backcover'] ), FILTER_SANITIZE_URL );
			}

			if ( isset( $_POST['bnlink'] ) ) {
				$bn_link = filter_var( wp_unslash( $_POST['bnlink'] ), FILTER_SANITIZE_URL );
			}

			if ( isset( $_POST['bamlink'] ) ) {
				$bam_link = filter_var( wp_unslash( $_POST['bamlink'] ), FILTER_SANITIZE_URL );
			}

			if ( isset( $_POST['callnumber'] ) ) {
				$callnumber = filter_var( wp_unslash( $_POST['callnumber'] ), FILTER_SANITIZE_URL );
			}

			if ( isset( $_POST['category'] ) ) {
				$category = filter_var( wp_unslash( $_POST['category'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['copies'] ) ) {
				$copies = filter_var( wp_unslash( $_POST['copies'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['country'] ) ) {
				$country = filter_var( wp_unslash( $_POST['country'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['crosssells'] ) ) {
				$crosssells = filter_var( wp_unslash( $_POST['crosssells'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['datefinished'] ) ) {
				$date_finished = filter_var( wp_unslash( $_POST['datefinished'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['description'] ) ) {
				$description = htmlentities( filter_var( wp_unslash( $_POST['description'] ), FILTER_SANITIZE_STRING ) );
			}

			if ( isset( $_POST['download'] ) ) {
				$download = filter_var( wp_unslash( $_POST['download'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['edition'] ) ) {
				$edition = filter_var( wp_unslash( $_POST['edition'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['finished'] ) ) {
				$finished = filter_var( wp_unslash( $_POST['finished'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['firstedition'] ) ) {
				$first_edition = filter_var( wp_unslash( $_POST['firstedition'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['format'] ) ) {
				$format = filter_var( wp_unslash( $_POST['format'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['genres'] ) ) {
				$genres = filter_var( wp_unslash( $_POST['genres'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['goodreadslink'] ) ) {
				$goodreadslink = filter_var( wp_unslash( $_POST['goodreadslink'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['googlepreview'] ) ) {
				$google_preview = filter_var( wp_unslash( $_POST['googlepreview'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['height'] ) ) {
				$height = filter_var( wp_unslash( $_POST['height'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['illustrator'] ) ) {
				$illustrator = filter_var( wp_unslash( $_POST['illustrator'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['image'] ) ) {
				$image = filter_var( wp_unslash( $_POST['image'] ), FILTER_SANITIZE_URL );
			}

			if ( isset( $_POST['isbn'] ) ) {
				$isbn = filter_var( wp_unslash( $_POST['isbn'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['isbn13'] ) ) {
				$isbn13 = filter_var( wp_unslash( $_POST['isbn13'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['keywords'] ) ) {
				$keywords = filter_var( wp_unslash( $_POST['keywords'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['kobolink'] ) ) {
				$kobo_link = filter_var( wp_unslash( $_POST['kobolink'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['language'] ) ) {
				$language = filter_var( wp_unslash( $_POST['language'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['length'] ) ) {
				$length = filter_var( wp_unslash( $_POST['length'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['library'] ) ) {
				$library = filter_var( wp_unslash( $_POST['library'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['notes'] ) ) {
				$notes = htmlentities( filter_var( wp_unslash( $_POST['notes'] ), FILTER_SANITIZE_STRING ) );
			}

			if ( isset( $_POST['numberinseries'] ) ) {
				$numberinseries = filter_var( wp_unslash( $_POST['numberinseries'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['originalpubyear'] ) ) {
				$originalpubyear = filter_var( wp_unslash( $_POST['originalpubyear'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['originaltitle'] ) ) {
				$originaltitle = filter_var( wp_unslash( $_POST['originaltitle'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['othereditions'] ) ) {
				$othereditions = filter_var( wp_unslash( $_POST['othereditions'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['outofprint'] ) ) {
				$outofprint = filter_var( wp_unslash( $_POST['outofprint'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['pageyes'] ) ) {
				$page_yes = filter_var( wp_unslash( $_POST['pageyes'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['pages'] ) ) {
				$pages = filter_var( wp_unslash( $_POST['pages'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['postyes'] ) ) {
				$post_yes = filter_var( wp_unslash( $_POST['postyes'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['price'] ) ) {
				$price = filter_var( wp_unslash( $_POST['price'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['productcategory'] ) ) {
				$productcategory = filter_var( wp_unslash( $_POST['productcategory'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['pubyear'] ) ) {
				$pub_year = filter_var( wp_unslash( $_POST['pubyear'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['publisher'] ) ) {
				$publisher = filter_var( wp_unslash( $_POST['publisher'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['purchasenote'] ) ) {
				$purchasenote = filter_var( wp_unslash( $_POST['purchasenote'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['rating'] ) ) {
				$rating = filter_var( wp_unslash( $_POST['rating'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['regularprice'] ) ) {
				$regularprice = filter_var( wp_unslash( $_POST['regularprice'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['reviews'] ) ) {
				$reviews = filter_var( wp_unslash( $_POST['reviews'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['salebegin'] ) ) {
				$salebegin = filter_var( wp_unslash( $_POST['salebegin'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['saleend'] ) ) {
				$saleend = filter_var( wp_unslash( $_POST['saleend'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['saleprice'] ) ) {
				$saleprice = filter_var( wp_unslash( $_POST['saleprice'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['series'] ) ) {
				$series = filter_var( wp_unslash( $_POST['series'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['shortdescription'] ) ) {
				$shortdescription = filter_var( wp_unslash( $_POST['shortdescription'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['signed'] ) ) {
				$signed = filter_var( wp_unslash( $_POST['signed'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['similarbooks'] ) ) {
				$similarbooks = filter_var( wp_unslash( $_POST['similarbooks'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['sku'] ) ) {
				$sku = filter_var( wp_unslash( $_POST['sku'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['stock'] ) ) {
				$stock = filter_var( wp_unslash( $_POST['stock'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['subgenre'] ) ) {
				$subgenre = filter_var( wp_unslash( $_POST['subgenre'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['subject'] ) ) {
				$subject = filter_var( wp_unslash( $_POST['subject'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['swapYes'] ) ) {
				$swap_yes = filter_var( wp_unslash( $_POST['swapYes'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['title'] ) ) {
				$title = filter_var( wp_unslash( $_POST['title'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['upsells'] ) ) {
				$upsells = filter_var( wp_unslash( $_POST['upsells'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['useAmazonYes'] ) ) {
				$use_amazon_yes = filter_var( wp_unslash( $_POST['useAmazonYes'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['virtual'] ) ) {
				$virtual = filter_var( wp_unslash( $_POST['virtual'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['weight'] ) ) {
				$weight = filter_var( wp_unslash( $_POST['weight'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['width'] ) ) {
				$width = filter_var( wp_unslash( $_POST['width'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['woocommerce'] ) ) {
				$woocommerce = filter_var( wp_unslash( $_POST['woocommerce'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['woofile'] ) ) {
				$woofile = filter_var( wp_unslash( $_POST['woofile'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['bookaction'] ) ) {
				$bookaction = filter_var( wp_unslash( $_POST['bookaction'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['bookid'] ) ) {
				$bookid = filter_var( wp_unslash( $_POST['bookid'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['ebookurl'] ) ) {
				$ebookurl = filter_var( wp_unslash( $_POST['ebookurl'] ), FILTER_SANITIZE_URL );
			}

			// Removing any dashes from the ISBN Field.
			if ( '' !== $isbn && null !== $isbn && false !== stripos( $isbn, '-' ) ) {
				$isbn = str_replace( '-', '', $isbn );
			}

			// Removing any dashes from the ISBN13 Field.
			if ( '' !== $isbn13 && null !== $isbn13 && false !== stripos( $isbn13, '-' ) ) {
				$isbn13 = str_replace( '-', '', $isbn13 );
			}

			// Make check for what kind of isbn/asin we have.
			if ( '' === $isbn || null === $isbn ) {
				if ( '' !== $isbn13 && null !== $isbn13 ) {
					$isbn = $isbn13;
				} else {
					$isbn = $asin;
				}
			}

			$book_array = array(
				'additionalimage1'   => $additionalimage1,
				'additionalimage2'   => $additionalimage2,
				'amazon_detail_page' => $amazon_detail_page,
				'amazonauth'         => $amazonauth,
				'appleibookslink'    => $appleibookslink,
				'asin'               => $asin,
				'author'             => $author,
				'author2'            => $author2,
				'author3'            => $author3,
				'author_url'         => $author_url,
				'sale_url'           => $sale_url,
				'backcover'          => $backcover,
				'bam_link'           => $bam_link,
				'bn_link'            => $bn_link,
				'callnumber'         => $callnumber,
				'category'           => $category,
				'copies'             => $copies,
				'country'            => $country,
				'crosssells'         => $crosssells,
				'date_finished'      => $date_finished,
				'description'        => $description,
				'download'           => $download,
				'edition'            => $edition,
				'finished'           => $finished,
				'first_edition'      => $first_edition,
				'format'             => $format,
				'genres'             => $genres,
				'goodreadslink'      => $goodreadslink,
				'google_preview'     => $google_preview,
				'height'             => $height,
				'illustrator'        => $illustrator,
				'image'              => $image,
				'isbn'               => $isbn,
				'isbn13'             => $isbn13,
				'keywords'           => $keywords,
				'kobo_link'          => $kobo_link,
				'language'           => $language,
				'length'             => $length,
				'library'            => $library,
				'notes'              => $notes,
				'numberinseries'     => $numberinseries,
				'othereditions'      => $othereditions,
				'originalpubyear'    => $originalpubyear,
				'originaltitle'      => $originaltitle,
				'outofprint'         => $outofprint,
				'page_yes'           => $page_yes,
				'pages'              => $pages,
				'post_yes'           => $post_yes,
				'price'              => $price,
				'productcategory'    => $productcategory,
				'pub_year'           => $pub_year,
				'publisher'          => $publisher,
				'purchasenote'       => $purchasenote,
				'rating'             => $rating,
				'regularprice'       => $regularprice,
				'reviews'            => $reviews,
				'salebegin'          => $salebegin,
				'saleend'            => $saleend,
				'saleprice'          => $saleprice,
				'series'             => $series,
				'shortdescription'   => $shortdescription,
				'signed'             => $signed,
				'similarbooks'       => $similarbooks,
				'sku'                => $sku,
				'stock'              => $stock,
				'subgenre'           => $subgenre,
				'subject'            => $subject,
				'swap_yes'           => $swap_yes,
				'title'              => $title,
				'upsells'            => $upsells,
				'use_amazon_yes'     => $use_amazon_yes,
				'virtual'            => $virtual,
				'weight'             => $weight,
				'width'              => $width,
				'woocommerce'        => $woocommerce,
				'woofile'            => $woofile,
				'ebook'              => $ebookurl,
			);

			// Now adding in any custom fields to above arrays for insertion into DB.
			$this->user_options = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_user_options' );
			$this->user_options->customfields;

			// Loop through the Custom Fields.
			if ( false !== stripos( $this->user_options->customfields, '--' ) ) {
				$fields = explode( '--', $this->user_options->customfields );

				// Loop through each custom field entry.
				foreach ( $fields as $key => $entry ) {

					if ( false !== stripos( $entry, ';' ) ) {
						$entry_details = explode( ';', $entry );

						// All kinds of checks to make sure good value exists.
						if ( array_key_exists( 0, $entry_details ) && isset( $entry_details[0] ) && '' !== $entry_details[0] && null !== $entry_details[0] ) {

							// Add new value with key into DB array.
							if ( isset( $_POST[ $entry_details[0] ] ) ) {
								$book_array[ $entry_details[0] ] = filter_var( wp_unslash( $_POST[ $entry_details[0] ] ), FILTER_SANITIZE_STRING );
							}
						}
					}
				}
			}

			require_once CLASS_BOOK_DIR . 'class-wpbooklist-book.php';
			$book_class    = new WPBookList_Book( $bookaction, $book_array, $bookid );
			$insert_result = explode( ',', $book_class->add_result );

			// If book added succesfully, get the ID of the book we just inserted, and return the result and that ID.
			if ( $insert_result[0] ) {
				$book_table_name = $wpdb->prefix . 'wpbooklist_jre_user_options';
				$id_result       = $insert_result[1];
				$row             = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $library WHERE ID = %d", $id_result ) );

				// Get saved page URL.
				$pageurl      = '';
				$table_name   = $wpdb->prefix . 'wpbooklist_jre_saved_page_post_log';
				$page_results = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE book_uid = %s AND type = 'page'", $row->book_uid ) );
				if ( is_object( $page_results ) ) {
					$pageurl = $page_results->post_url;
				}

				// Get saved post URL.
				$posturl      = '';
				$table_name   = $wpdb->prefix . 'wpbooklist_jre_saved_page_post_log';
				$post_results = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE book_uid = %s AND type = 'post'", $row->book_uid ) );
				if ( is_object( $post_results ) ) {
					$posturl = $post_results->post_url;
				}

				wp_die( $insert_result[0] . '--sep--' . $id_result . '--sep--' . $library . '--sep--' . $page_yes . '--sep--' . $post_yes . '--sep--' . $pageurl . '--sep--' . $posturl . '--sep--' . $book_class->apireport . '--sep--' . wp_json_encode( $book_class->whichapifound ) . '--sep--' . $book_class->apiamazonfailcount . '--sep--' . $book_class->amazon_transient_use . '--sep--' . $book_class->google_transient_use . '--sep--' . $book_class->openlib_transient_use . '--sep--' . $book_class->itunes_transient_use . '--sep--' . $book_class->itunes_audio_transient_use . '--sep--' . wp_json_encode( $book_class->db_insert_array ) );
			} else {
				wp_die( $insert_result[0] . '--sep--' . $insert_result[1] );
			}
		}

		/**
		 * Callback function for creating a WordPress user.
		 */
		public function wpbooklist_dashboard_create_wp_user_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_dashboard_create_wp_user_action_callback', 'security' );

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
					wp_die( '$user_id--' . $user_id );
				}
			}


		}

		/**
		 * Callback function for adding a user.
		 */
		public function wpbooklist_save_user_data_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_save_user_data_action_callback', 'security' );

			$firstname       = '';
			$lastname        = '';
			$email           = '';
			$emailconfirm    = '';
			$password        = '';
			$passwordconfirm = '';
			$username        = '';
			$addbooks        = '';
			$editbooks       = '';
			$deletebooks     = '';
			$displayoptions  = '';
			$settings        = '';
			$wpuserid        = '';
			$librarystring   = '';

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

			if ( isset( $_POST['addbooks'] ) ) {
				$addbooks = filter_var( $_POST['addbooks'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['editbooks'] ) ) {
				$editbooks = filter_var( $_POST['editbooks'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['deletebooks'] ) ) {
				$deletebooks = filter_var( $_POST['deletebooks'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['displayoptions'] ) ) {
				$displayoptions = filter_var( $_POST['displayoptions'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['settings'] ) ) {
				$settings = filter_var( $_POST['settings'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['wpuserid'] ) ) {
				$wpuserid = filter_var( $_POST['wpuserid'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['librarystring'] ) ) {
				$librarystring = filter_var( $_POST['librarystring'], FILTER_SANITIZE_STRING );
			}

			// Create the permissions string.
			$permissions = $addbooks . '-' . $editbooks . '-' . $deletebooks . '-' . $displayoptions . '-' . $settings;

			$users_save_array = array(
				'firstname'   => $firstname,
				'lastname'    => $lastname,
				'email'       => $email,
				'username'    => $username,
				'permissions' => $permissions,
				'wpuserid'    => $wpuserid,
				'datecreated' => $this->date,
				'libraries'   => $librarystring,
			);

			// Requiring & Calling the file/class that will insert or update our data.
			require_once CLASS_USERS_DIR . 'class-wpbooklist-save-users-data.php';
			$save_class      = new WPBOOKLIST_Save_Users_Data( $users_save_array );
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

		/**
		 * Callback function for deleting a user.
		 */
		public function wpbooklist_delete_user_data_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_delete_user_data_action_callback', 'security' );

			if ( isset( $_POST['wpuserid'] ) ) {
				$wpuserid = filter_var( $_POST['wpuserid'], FILTER_SANITIZE_STRING );
			}

			// Let's make sure we're not deleting the SuperAdmin...
			$custom_user_info = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_users_table WHERE wpuserid = ' . $wpuserid );
			if ( 'SuperAdmin' !== $custom_user_info->role  ) {

				// Let's make sure we're not deleting the logged-in user...
				$user = wp_get_current_user();
				if ( $wpuserid !== $user->ID ) {

					// First delete from our custom table.
					$custom_delete_result = $wpdb->delete( $wpdb->prefix . 'wpbooklist_jre_users_table', array( 'wpuserid' => $wpuserid ), array( '%d' ) );

					// Now delete the associated WordPress user.
					$wp_delete_result = 1;
					// $wp_delete_result = wp_delete_user( $wpuserid );

					wp_die( $custom_delete_result . ' ' . $wp_delete_result );

				}

			}

		}

		/**
		 * Callback function for editing a saved WPBookList Basic User.
		 */
		public function wpbooklist_edit_user_data_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_edit_user_data_action_callback', 'security' );

			$firstname       = '';
			$lastname        = '';
			$email           = '';
			$emailconfirm    = '';
			$password        = '';
			$passwordconfirm = '';
			$username        = '';
			$addbooks        = '';
			$editbooks       = '';
			$deletebooks     = '';
			$displayoptions  = '';
			$settings        = '';
			$wpuserid        = '';
			$librarystring   = '';

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

			if ( isset( $_POST['addbooks'] ) ) {
				$addbooks = filter_var( $_POST['addbooks'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['editbooks'] ) ) {
				$editbooks = filter_var( $_POST['editbooks'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['deletebooks'] ) ) {
				$deletebooks = filter_var( $_POST['deletebooks'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['displayoptions'] ) ) {
				$displayoptions = filter_var( $_POST['displayoptions'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['settings'] ) ) {
				$settings = filter_var( $_POST['settings'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['wpuserid'] ) ) {
				$wpuserid = filter_var( $_POST['wpuserid'], FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['librarystring'] ) ) {
				$librarystring = filter_var( $_POST['librarystring'], FILTER_SANITIZE_STRING );
			}

			// Create the permissions string.
			$permissions = $addbooks . '-' . $editbooks . '-' . $deletebooks . '-' . $displayoptions . '-' . $settings;

			$users_save_array = array(
				'firstname'   => $firstname,
				'lastname'    => $lastname,
				'email'       => $email,
				'username'    => $username,
				'permissions' => $permissions,
				'wpuserid'    => $wpuserid,
				'libraries'   => $librarystring,
			);

			// Requiring & Calling the file/class that will insert or update our data.
			require_once CLASS_USERS_DIR . 'class-wpbooklist-save-users-data.php';
			$save_class      = new WPBOOKLIST_Save_Users_Data( $users_save_array );
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

		/**
		 * Callback function for getting form for editing user.
		 */
		public function wpbooklist_edit_user_form_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_edit_user_form_action_callback', 'security' );

			if ( isset( $_POST['wpuserid'] ) ) {
				$wpuserid = filter_var( wp_unslash( $_POST['wpuserid'], FILTER_SANITIZE_STRING ) );
			}

			// Now get this user's info.
			$custom_user_info = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_users_table WHERE wpuserid = ' . $wpuserid );

			require_once CLASS_USERS_DIR . 'class-wpbooklist-users-form.php';
			$this->form = new WPBookList_User_Form();
			$form       = $this->form->output_users_edit_form( $custom_user_info );

			wp_die( $form );

		}


		/**
		 * Callback function for editing a book. Should be almost identical to wpbooklist_dashboard_add_book_action_callback() except for handling the response from class-wpbooklist-book.php.
		 */
		public function wpbooklist_dashboard_edit_book_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_dashboard_edit_book_action_callback', 'security' );

			// First set the variables we'll be passing to class-wpbooklist-book.php to ''.
			$additionalimage1   = '';
			$additionalimage2   = '';
			$amazon_detail_page = '';
			$amazonauth         = '';
			$appleibookslink    = '';
			$asin               = '';
			$author             = '';
			$author2            = '';
			$author3            = '';
			$author_url         = '';
			$sale_url           = '';
			$backcover          = '';
			$bam_link           = '';
			$bn_link            = '';
			$callnumber         = '';
			$category           = '';
			$copies             = '';
			$country            = '';
			$crosssells         = '';
			$date_finished      = '';
			$description        = '';
			$download           = '';
			$edition            = '';
			$finished           = '';
			$first_edition      = '';
			$format             = '';
			$genres             = '';
			$goodreadslink      = '';
			$google_preview     = '';
			$height             = '';
			$illustrator        = '';
			$image              = '';
			$isbn               = '';
			$isbn13             = '';
			$kobo_link          = '';
			$keywords           = '';
			$language           = '';
			$length             = '';
			$library            = '';
			$notes              = '';
			$numberinseries     = '';
			$originalpubyear    = '';
			$originaltitle      = '';
			$othereditions      = '';
			$outofprint         = '';
			$page_yes           = '';
			$pages              = '';
			$post_yes           = '';
			$price              = '';
			$productcategory    = '';
			$pub_year           = '';
			$publisher          = '';
			$purchasenote       = '';
			$rating             = '';
			$regularprice       = '';
			$reviews            = '';
			$salebegin          = '';
			$saleend            = '';
			$saleprice          = '';
			$series             = '';
			$shortdescription   = '';
			$signed             = '';
			$similarbooks       = '';
			$sku                = '';
			$stock              = '';
			$subgenre           = '';
			$subject            = '';
			$swap_yes           = '';
			$title              = '';
			$upsells            = '';
			$use_amazon_yes     = '';
			$virtual            = '';
			$weight             = '';
			$width              = '';
			$woocommerce        = '';
			$woofile            = '';
			$bookaction         = '';
			$ebookurl           = '';

			// First set the variables we'll be passing to class-wpbooklist-book.php to ''.
			if ( isset( $_POST['additionalimage1'] ) ) {
				$additionalimage1 = filter_var( wp_unslash( $_POST['additionalimage1'] ), FILTER_SANITIZE_STRING );
			}

			// First set the variables we'll be passing to class-wpbooklist-book.php to ''.
			if ( isset( $_POST['additionalimage2'] ) ) {
				$additionalimage2 = filter_var( wp_unslash( $_POST['additionalimage2'] ), FILTER_SANITIZE_STRING );
			}

			// First set the variables we'll be passing to class-wpbooklist-book.php to ''.
			if ( isset( $_POST['amazonauth'] ) ) {
				$amazonauth = filter_var( wp_unslash( $_POST['amazonauth'] ), FILTER_SANITIZE_STRING );
			}

			// First set the variables we'll be passing to class-wpbooklist-book.php to ''.
			if ( isset( $_POST['amazondetailpage'] ) ) {
				$amazon_detail_page = filter_var( wp_unslash( $_POST['amazondetailpage'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['appleibookslink'] ) ) {
				$appleibookslink = filter_var( wp_unslash( $_POST['appleibookslink'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['asin'] ) ) {
				$asin = filter_var( wp_unslash( $_POST['asin'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['author'] ) ) {
				$author = filter_var( wp_unslash( $_POST['author'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['author2'] ) ) {
				$author2 = filter_var( wp_unslash( $_POST['author2'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['author3'] ) ) {
				$author3 = filter_var( wp_unslash( $_POST['author3'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['authorurl'] ) ) {
				$author_url = filter_var( wp_unslash( $_POST['authorurl'] ), FILTER_SANITIZE_URL );
			}

			if ( isset( $_POST['saleurl'] ) ) {
				$sale_url = filter_var( wp_unslash( $_POST['saleurl'] ), FILTER_SANITIZE_URL );
			}

			if ( isset( $_POST['backcover'] ) ) {
				$backcover = filter_var( wp_unslash( $_POST['backcover'] ), FILTER_SANITIZE_URL );
			}

			if ( isset( $_POST['bnlink'] ) ) {
				$bn_link = filter_var( wp_unslash( $_POST['bnlink'] ), FILTER_SANITIZE_URL );
			}

			if ( isset( $_POST['bamlink'] ) ) {
				$bam_link = filter_var( wp_unslash( $_POST['bamlink'] ), FILTER_SANITIZE_URL );
			}

			if ( isset( $_POST['bookuid'] ) ) {
				$book_uid = filter_var( wp_unslash( $_POST['bookuid'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['callnumber'] ) ) {
				$callnumber = filter_var( wp_unslash( $_POST['callnumber'] ), FILTER_SANITIZE_URL );
			}

			if ( isset( $_POST['category'] ) ) {
				$category = filter_var( wp_unslash( $_POST['category'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['copies'] ) ) {
				$copies = filter_var( wp_unslash( $_POST['copies'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['country'] ) ) {
				$country = filter_var( wp_unslash( $_POST['country'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['crosssells'] ) ) {
				$crosssells = filter_var( wp_unslash( $_POST['crosssells'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['datefinished'] ) ) {
				$date_finished = filter_var( wp_unslash( $_POST['datefinished'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['description'] ) ) {
				$description = htmlentities( filter_var( wp_unslash( $_POST['description'] ) ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['download'] ) ) {
				$download = filter_var( wp_unslash( $_POST['download'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['edition'] ) ) {
				$edition = filter_var( wp_unslash( $_POST['edition'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['finished'] ) ) {
				$finished = filter_var( wp_unslash( $_POST['finished'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['firstedition'] ) ) {
				$first_edition = filter_var( wp_unslash( $_POST['firstedition'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['format'] ) ) {
				$format = filter_var( wp_unslash( $_POST['format'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['genres'] ) ) {
				$genres = filter_var( wp_unslash( $_POST['genres'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['goodreadslink'] ) ) {
				$goodreadslink = filter_var( wp_unslash( $_POST['goodreadslink'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['googlepreview'] ) ) {
				$google_preview = filter_var( wp_unslash( $_POST['googlepreview'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['height'] ) ) {
				$height = filter_var( wp_unslash( $_POST['height'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['illustrator'] ) ) {
				$illustrator = filter_var( wp_unslash( $_POST['illustrator'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['image'] ) ) {
				$image = filter_var( wp_unslash( $_POST['image'] ), FILTER_SANITIZE_URL );
			}

			if ( isset( $_POST['isbn'] ) ) {
				$isbn = filter_var( wp_unslash( $_POST['isbn'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['isbn13'] ) ) {
				$isbn13 = filter_var( wp_unslash( $_POST['isbn13'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['keywords'] ) ) {
				$keywords = filter_var( wp_unslash( $_POST['keywords'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['kobolink'] ) ) {
				$kobo_link = filter_var( wp_unslash( $_POST['kobolink'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['language'] ) ) {
				$language = filter_var( wp_unslash( $_POST['language'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['length'] ) ) {
				$length = filter_var( wp_unslash( $_POST['length'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['library'] ) ) {
				$library = filter_var( wp_unslash( $_POST['library'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['notes'] ) ) {
				$notes = htmlentities( filter_var( wp_unslash( $_POST['notes'] ) ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['numberinseries'] ) ) {
				$numberinseries = filter_var( wp_unslash( $_POST['numberinseries'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['originalpubyear'] ) ) {
				$originalpubyear = filter_var( wp_unslash( $_POST['originalpubyear'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['originaltitle'] ) ) {
				$originaltitle = filter_var( wp_unslash( $_POST['originaltitle'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['othereditions'] ) ) {
				$othereditions = filter_var( wp_unslash( $_POST['othereditions'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['outofprint'] ) ) {
				$outofprint = filter_var( wp_unslash( $_POST['outofprint'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['pageyes'] ) ) {
				$page_yes = filter_var( wp_unslash( $_POST['pageyes'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['pages'] ) ) {
				$pages = filter_var( wp_unslash( $_POST['pages'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['postyes'] ) ) {
				$post_yes = filter_var( wp_unslash( $_POST['postyes'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['price'] ) ) {
				$price = filter_var( wp_unslash( $_POST['price'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['productcategory'] ) ) {
				$productcategory = filter_var( wp_unslash( $_POST['productcategory'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['pubyear'] ) ) {
				$pub_year = filter_var( wp_unslash( $_POST['pubyear'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['publisher'] ) ) {
				$publisher = filter_var( wp_unslash( $_POST['publisher'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['purchasenote'] ) ) {
				$purchasenote = filter_var( wp_unslash( $_POST['purchasenote'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['rating'] ) ) {
				$rating = filter_var( wp_unslash( $_POST['rating'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['regularprice'] ) ) {
				$regularprice = filter_var( wp_unslash( $_POST['regularprice'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['reviews'] ) ) {
				$reviews = filter_var( wp_unslash( $_POST['reviews'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['salebegin'] ) ) {
				$salebegin = filter_var( wp_unslash( $_POST['salebegin'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['saleend'] ) ) {
				$saleend = filter_var( wp_unslash( $_POST['saleend'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['saleprice'] ) ) {
				$saleprice = filter_var( wp_unslash( $_POST['saleprice'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['series'] ) ) {
				$series = filter_var( wp_unslash( $_POST['series'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['shortdescription'] ) ) {
				$shortdescription = filter_var( wp_unslash( $_POST['shortdescription'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['signed'] ) ) {
				$signed = filter_var( wp_unslash( $_POST['signed'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['similarbooks'] ) ) {
				$similarbooks = filter_var( wp_unslash( $_POST['similarbooks'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['sku'] ) ) {
				$sku = filter_var( wp_unslash( $_POST['sku'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['stock'] ) ) {
				$stock = filter_var( wp_unslash( $_POST['stock'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['subgenre'] ) ) {
				$subgenre = filter_var( wp_unslash( $_POST['subgenre'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['subject'] ) ) {
				$subject = filter_var( wp_unslash( $_POST['subject'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['swapYes'] ) ) {
				$swap_yes = filter_var( wp_unslash( $_POST['swapYes'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['title'] ) ) {
				$title = filter_var( wp_unslash( $_POST['title'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['upsells'] ) ) {
				$upsells = filter_var( wp_unslash( $_POST['upsells'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['useAmazonYes'] ) ) {
				$use_amazon_yes = filter_var( wp_unslash( $_POST['useAmazonYes'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['virtual'] ) ) {
				$virtual = filter_var( wp_unslash( $_POST['virtual'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['weight'] ) ) {
				$weight = filter_var( wp_unslash( $_POST['weight'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['width'] ) ) {
				$width = filter_var( wp_unslash( $_POST['width'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['woocommerce'] ) ) {
				$woocommerce = filter_var( wp_unslash( $_POST['woocommerce'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['woofile'] ) ) {
				$woofile = filter_var( wp_unslash( $_POST['woofile'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['bookaction'] ) ) {
				$bookaction = filter_var( wp_unslash( $_POST['bookaction'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['bookid'] ) ) {
				$bookid = filter_var( wp_unslash( $_POST['bookid'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['ebookurl'] ) ) {
				$ebookurl = filter_var( wp_unslash( $_POST['ebookurl'] ), FILTER_SANITIZE_URL );
			}

			// Removing any dashes from the ISBN Field.
			if ( '' !== $isbn && null !== $isbn && false !== stripos( $isbn, '-' ) ) {
				$isbn = str_replace( '-', '', $isbn );
			}



			// Removing any dashes from the ISBN13 Field.
			if ( '' !== $isbn13 && null !== $isbn13 && false !== stripos( $isbn13, '-' ) ) {
				$isbn13 = str_replace( '-', '', $isbn13 );
			}

			// Make check for what kind of isbn/asin we have.
			if ( '' === $isbn || null === $isbn ) {
				if ( '' !== $isbn13 && null !== $isbn13 ) {
					$isbn = $isbn13;
				} else {
					$isbn = $asin;
				}
			}

			$book_array = array(
				'additionalimage1'   => $additionalimage1,
				'additionalimage2'   => $additionalimage2,
				'amazon_detail_page' => $amazon_detail_page,
				'amazonauth'         => $amazonauth,
				'appleibookslink'    => $appleibookslink,
				'asin'               => $asin,
				'author'             => $author,
				'author2'            => $author2,
				'author3'            => $author3,
				'author_url'         => $author_url,
				'sale_url'           => $sale_url,
				'backcover'          => $backcover,
				'bam_link'           => $bam_link,
				'book_uid'			 => $book_uid,
				'bn_link'            => $bn_link,
				'callnumber'         => $callnumber,
				'category'           => $category,
				'copies'             => $copies,
				'country'            => $country,
				'crosssells'         => $crosssells,
				'date_finished'      => $date_finished,
				'description'        => $description,
				'download'           => $download,
				'edition'            => $edition,
				'finished'           => $finished,
				'first_edition'      => $first_edition,
				'format'             => $format,
				'genres'             => $genres,
				'goodreadslink'      => $goodreadslink,
				'google_preview'     => $google_preview,
				'height'             => $height,
				'illustrator'        => $illustrator,
				'image'              => $image,
				'isbn'               => $isbn,
				'isbn13'             => $isbn13,
				'keywords'           => $keywords,
				'kobo_link'          => $kobo_link,
				'language'           => $language,
				'length'             => $length,
				'library'            => $library,
				'notes'              => $notes,
				'numberinseries'     => $numberinseries,
				'othereditions'      => $othereditions,
				'originalpubyear'    => $originalpubyear,
				'originaltitle'      => $originaltitle,
				'outofprint'         => $outofprint,
				'page_yes'           => $page_yes,
				'pages'              => $pages,
				'post_yes'           => $post_yes,
				'price'              => $price,
				'productcategory'    => $productcategory,
				'pub_year'           => $pub_year,
				'publisher'          => $publisher,
				'purchasenote'       => $purchasenote,
				'rating'             => $rating,
				'regularprice'       => $regularprice,
				'reviews'            => $reviews,
				'salebegin'          => $salebegin,
				'saleend'            => $saleend,
				'saleprice'          => $saleprice,
				'series'             => $series,
				'shortdescription'   => $shortdescription,
				'signed'             => $signed,
				'similarbooks'       => $similarbooks,
				'sku'                => $sku,
				'stock'              => $stock,
				'subgenre'           => $subgenre,
				'subject'            => $subject,
				'swap_yes'           => $swap_yes,
				'title'              => $title,
				'upsells'            => $upsells,
				'use_amazon_yes'     => $use_amazon_yes,
				'virtual'            => $virtual,
				'weight'             => $weight,
				'width'              => $width,
				'woocommerce'        => $woocommerce,
				'woofile'            => $woofile,
				'ebook'              => $ebookurl,
			);

			// Now adding in any custom fields to above arrays for insertion into DB.
			$this->user_options = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_user_options' );
			$this->user_options->customfields;

			// Loop through the Custom Fields.
			if ( false !== stripos( $this->user_options->customfields, '--' ) ) {
				$fields = explode( '--', $this->user_options->customfields );

				// Loop through each custom field entry.
				foreach ( $fields as $key => $entry ) {

					if ( false !== stripos( $entry, ';' ) ) {
						$entry_details = explode( ';', $entry );

						// All kinds of checks to make sure good value exists.
						if ( array_key_exists( 0, $entry_details ) && isset( $entry_details[0] ) && '' !== $entry_details[0] && null !== $entry_details[0] ) {

							// Add new value with key into DB array.
							if ( isset( $_POST[ $entry_details[0] ] ) ) {
								$book_array[ $entry_details[0] ] = filter_var( wp_unslash( $_POST[ $entry_details[0] ] ), FILTER_SANITIZE_STRING );
							}
						}
					}
				}
			}

			//error_log( 'Here is the Array being sent to Class-wpbooklist-book.php to Edit a book:' );
			//error_log( print_r( $book_array, true ) );

			require_once CLASS_BOOK_DIR . 'class-wpbooklist-book.php';
			$book_class  = new WPBookList_Book( 'edit', $book_array, $bookid );
			$edit_result = $book_class->edit_result;

			// If book was edited succesfully, get the ID of the book we just inserted, and return the result and that ID.
			if ( 1 === $edit_result || 0 === $edit_result ) {

				$book_table_name = $wpdb->prefix . 'wpbooklist_jre_user_options';
				$id_result       = $book_class->id;
				$row             = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $library WHERE ID = %d", $id_result ) );

				// Get saved page URL.
				$pageurl      = '';
				$table_name   = $wpdb->prefix . 'wpbooklist_jre_saved_page_post_log';
				$page_results = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE book_uid = %s AND type = 'page'", $row->book_uid ) );
				if ( is_object( $page_results ) ) {
					$pageurl = $page_results->post_url;
				}

				// Get saved post URL.
				$posturl      = '';
				$table_name   = $wpdb->prefix . 'wpbooklist_jre_saved_page_post_log';
				$post_results = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE book_uid = %s AND type = 'post'", $row->book_uid ) );
				if ( is_object( $post_results ) ) {
					$posturl = $post_results->post_url;
				}

				wp_die( $edit_result . '--sep--' . $id_result . '--sep--' . $library . '--sep--' . $page_yes . '--sep--' . $post_yes . '--sep--' . $pageurl . '--sep--' . $posturl . '--sep--' . $book_class->apireport . '--sep--' . wp_json_encode( $book_class->whichapifound) . '--sep--' . $book_class->apiamazonfailcount . '--sep--' . $book_class->amazon_transient_use . '--sep--' . $book_class->google_transient_use . '--sep--' . $book_class->openlib_transient_use . '--sep--' . $book_class->itunes_transient_use . '--sep--' . $book_class->itunes_audio_transient_use );
			} else {

				// Handling an actual Database error and displaying error code for troubleshooting.
				wp_die( $edit_result );
			}

			wp_die();
		}

		/**
		 * Callback function for showing books in the colorbox window
		 */
		public function wpbooklist_show_book_in_colorbox_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_show_book_in_colorbox_action_callback', 'security' );

			if ( isset( $_POST['bookId'] ) ) {
				$book_id = filter_var( wp_unslash( $_POST['bookId'] ), FILTER_SANITIZE_NUMBER_INT );
			}

			$book_table = filter_var( wp_unslash( $_POST['bookTable'] ), FILTER_SANITIZE_STRING );
			$sortParam  = filter_var( wp_unslash( $_POST['sortParam'] ), FILTER_SANITIZE_STRING );

			// Double-check that Amazon review isn't expired.
			require_once CLASS_BOOK_DIR . 'class-wpbooklist-book.php';
			$book = new WPBookList_Book( $book_id, $book_table );
			$book->refresh_amazon_review( $book_id, $book_table );

			// Instantiate the class that shows the book in colorbox.
			require_once CLASS_DIR . 'class-wpbooklist-show-book-in-colorbox.php';
			$colorbox = new WPBookList_Show_Book_In_Colorbox( $book_id, $book_table, null, $sortParam );

			echo $colorbox->output . '---sep---' . $colorbox->isbn;
			wp_die();
		}

		/** Helper function for cleaning up user-provided string.
		 *
		 *  @param string $string - The string that contains the table name.
		 */
		public function wpbooklist_clean( $string ) {
			$string = str_replace( ' ', '_', $string );
			$string = str_replace( '-', '_', $string );
			return preg_replace( '/[^A-Za-z0-9\-]/', '', $string );
		}

		/**
		 * Callback function for creating a new Custom Library.
		 */
		public function wpbooklist_delete_all_transients_action_callback() {

			global $wpdb;
			check_ajax_referer( 'wpbooklist_delete_all_transients_action_callback', 'security' );

			$this->transients->delete_all_wpbl_transients();

			wp_die( $result );

		}

		/**
		 * Callback function for creating a new Custom Library.
		 */
		public function wpbooklist_new_library_action_callback() {

			global $wpdb;
			global $charset_collate;
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			check_ajax_referer( 'wpbooklist_new_library_action_callback', 'security' );
			$table_name_dynamic = $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names';

			// Set up table namee.
			$db_name = '';
			if ( isset( $_POST['currentval'] ) ) {
				$db_name = sanitize_text_field( wp_unslash( $_POST['currentval'] ) );
				$db_name = $this->wpbooklist_clean( $db_name );
			}

			// Create a new custom table - both the table for books and the Settings table.
			if ( ( '' !== $db_name ) && ( null !== $db_name ) ) {
				$wpdb->wpbooklist_jre_dynamic_db_name          = "{$wpdb->prefix}wpbooklist_jre_{$db_name}";
				$wpdb->wpbooklist_jre_dynamic_db_name_settings = "{$wpdb->prefix}wpbooklist_jre_settings_{$db_name}";
				$wpdb->wpbooklist_jre_list_dynamic_db_names    = "{$wpdb->prefix}wpbooklist_jre_list_dynamic_db_names";
				$sql_create_table                              = "CREATE TABLE {$wpdb->wpbooklist_jre_dynamic_db_name} 
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
				dbDelta( $sql_create_table );

				// Now we need to add any existing Custom Fields into this newly-created Library and it's settings table.
				$default_settings = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_user_options' );

				if ( false !== stripos( $default_settings->customfields, '--' ) ) {

					$fields = explode( '--', $default_settings->customfields );

					foreach ( $fields as $key => $indivfield ) {

						if ( false !== stripos( $indivfield, ';' ) ) {

							$temp = explode( ';', $indivfield );
							if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$wpdb->wpbooklist_jre_dynamic_db_name` LIKE '" . $temp[0] . "'" ) ) {
								$wpdb->query( "ALTER TABLE $wpdb->wpbooklist_jre_dynamic_db_name ADD " . $temp[0] . " TEXT" );
							}
						}
					}
				}

				$sql_create_table2 = "CREATE TABLE {$wpdb->wpbooklist_jre_dynamic_db_name_settings} 
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
					hideadditionalimgs bigint(255),
					adminmessage varchar(10000) NOT NULL DEFAULT '" . ADMIN_MESSAGE . "',
					PRIMARY KEY  (ID),
					KEY username (username )
				) $charset_collate; ";
				dbDelta( $sql_create_table2 );

				$table_name = $wpdb->wpbooklist_jre_dynamic_db_name_settings;
				$wpdb->insert( $table_name, array( 'ID' => 1 ) );

				$wpdb->insert( $table_name_dynamic, array( 'user_table_name' => $db_name ) );

				if ( false !== stripos( $default_settings->customfields, '--' ) ) {

					$fields = explode( '--', $default_settings->customfields );

					foreach ( $fields as $key => $indivfield ) {

						if ( false !== stripos( $indivfield, ';' ) ) {

							$temp = explode( ';', $indivfield );
							if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'hide" . $temp[0] . "'" ) ) {
								$wpdb->query( "ALTER TABLE $table_name ADD hide" . $temp[0] . " bigint(255)" );
							}
						}
					}
				}

				// ADD COLUMNS TO THE 'wpbooklist_jre_user_options' TABLE.
				if ( 0 === $wpdb->query( "SHOW COLUMNS FROM `$table_name` LIKE 'activeposttemplate'" ) ) {
					$wpdb->query( "ALTER TABLE $table_name ADD activeposttemplate varchar( 255 ) NOT NULL DEFAULT 'default'" );
				}
			}

			$this->transients->delete_all_wpbl_transients();
			
			wp_die();
		}

		/**
		 * Callback function for deleting a Custom Library.
		 */
		public function wpbooklist_delete_library_action_callback() {

			// Grabbing the existing options from DB.
			global $wpdb;
			global $charset_collate;
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			check_ajax_referer( 'wpbooklist_delete_library_action_callback', 'security' );
			$table_name_dynamic = $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names';

			// Delete the table.
			if ( isset( $_POST['table'] ) ) {
				$table = $wpdb->prefix . 'wpbooklist_jre_' . sanitize_text_field( wp_unslash( $_POST['table'] ) );
				$pos   = strripos( $table, '_' );
				$table = substr( $table, 0, $pos );
				$wpdb->query( "DROP TABLE IF EXISTS $table" );

				$delete_from_list = sanitize_text_field( wp_unslash( $_POST['table'] ) );
				$pos2             = strripos( $delete_from_list, '_' );
				$delete_id        = substr( $delete_from_list, ( $pos2 + 1 ) );
				$wpdb->delete( $table_name_dynamic, array( 'ID' => $delete_id ), array( '%d' ) );

				// Dropping primary key in database to alter the IDs and the AUTO_INCREMENT value.
				$table_name_dynamic = str_replace( '\'', '`', $table_name_dynamic );
				$wpdb->query( "ALTER TABLE $table_name_dynamic MODIFY ID bigint(190) NOT NULL" );
				$wpdb->query( "ALTER TABLE $table_name_dynamic DROP PRIMARY KEY" );

				// Adjusting ID values of remaining entries in database.
				$my_query    = $wpdb->get_results( "SELECT * FROM $table_name_dynamic" );
				$title_count = $wpdb->num_rows;

				for ( $x = $delete_id; $x <= $title_count; $x++ ) {
					$data   = array(
						'ID' => $delete_id,
					);
					$format = array( '%s' );
					$delete_id++;
					$where        = array( 'ID' => ( $delete_id ) );
					$where_format = array( '%d' );
					$wpdb->update( $table_name_dynamic, $data, $where, $format, $where_format );
				}

				// Adding primary key back to database.
				$wpdb->query( "ALTER TABLE $table_name_dynamic ADD PRIMARY KEY (`ID`)" );
				$wpdb->query( "ALTER TABLE $table_name_dynamic MODIFY ID bigint(190) AUTO_INCREMENT" );

				// Setting the AUTO_INCREMENT value based on number of remaining entries.
				$title_count++;
				$query = $wpdb->prepare( "ALTER TABLE $table_name_dynamic AUTO_INCREMENT = %d", $title_count );
				$query = str_replace( '\'', '`', $query );
				$wpdb->query( $query );

				$this->transients->delete_all_wpbl_transients();
			}

			wp_die();
		}

		/**
		 * Callback function for saving Library display options.
		 */
		public function wpbooklist_dashboard_save_library_display_options_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_dashboard_save_library_display_options_action_callback', 'security' );

			if ( isset( $_POST['booksonpage'] ) ) {
				$booksonpage = filter_var( wp_unslash( $_POST['booksonpage'] ), FILTER_SANITIZE_NUMBER_INT );
			}

			if ( isset( $_POST['hidefilter'] ) ) {
				$hidefilter = filter_var( wp_unslash( $_POST['hidefilter'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidefinishedsort'] ) ) {
				$hidefinishedsort = filter_var( wp_unslash( $_POST['hidefinishedsort'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidefirstsort'] ) ) {
				$hidefirstsort = filter_var( wp_unslash( $_POST['hidefirstsort'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidelibrarytitle'] ) ) {
				$hidelibrarytitle = filter_var( wp_unslash( $_POST['hidelibrarytitle'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidequote'] ) ) {
				$hidequote = filter_var( wp_unslash( $_POST['hidequote'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hiderating'] ) ) {
				$hiderating = filter_var( wp_unslash( $_POST['hiderating'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidesearch'] ) ) {
				$hidesearch = filter_var( wp_unslash( $_POST['hidesearch'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidesignedsort'] ) ) {
				$hidesignedsort = filter_var( wp_unslash( $_POST['hidesignedsort'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidestats'] ) ) {
				$hidestats = filter_var( wp_unslash( $_POST['hidestats'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidesubjectsort'] ) ) {
				$hidesubjectsort = filter_var( wp_unslash( $_POST['hidesubjectsort'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['library'] ) ) {
				$library = filter_var( wp_unslash( $_POST['library'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['sortoption'] ) ) {
				$sortoption = filter_var( wp_unslash( $_POST['sortoption'] ), FILTER_SANITIZE_STRING );
			}

			$hidefrontendbuyimg = null;
			if ( isset( $_POST['hidefrontendbuyimg'] ) ) {
				$hidefrontendbuyimg = filter_var( wp_unslash( $_POST['hidefrontendbuyimg'] ), FILTER_SANITIZE_STRING );
			}

			$hidefrontendbuyprice = null;
			if ( isset( $_POST['hidefrontendbuyprice'] ) ) {
				$hidefrontendbuyprice = filter_var( wp_unslash( $_POST['hidefrontendbuyprice'] ), FILTER_SANITIZE_STRING );
			}

			$enablepurchase = null;
			if ( isset( $_POST['enablepurchase'] ) ) {
				$enablepurchase = filter_var( wp_unslash( $_POST['enablepurchase'] ), FILTER_SANITIZE_STRING );
			}

			$settings_array = array(
				'booksonpage'          => $booksonpage,
				'hidefilter'           => $hidefilter,
				'hidefinishedsort'     => $hidefinishedsort,
				'hidefirstsort'        => $hidefirstsort,
				'hidelibrarytitle'     => $hidelibrarytitle,
				'hidequote'            => $hidequote,
				'hiderating'           => $hiderating,
				'hidesearch'           => $hidesearch,
				'hidesignedsort'       => $hidesignedsort,
				'hidestats'            => $hidestats,
				'hidesubjectsort'      => $hidesubjectsort,
				'sortoption'           => $sortoption,
				'hidefrontendbuyimg'   => $hidefrontendbuyimg,
				'hidefrontendbuyprice' => $hidefrontendbuyprice,
				'enablepurchase'       => $enablepurchase,
			);

			require_once CLASS_DIR . 'class-display-options.php';
			$settings_class = new WPBookList_Display_Options();
			$settings_class->save_library_settings( $library, $settings_array );
			wp_die();
		}

		/**
		 * Callback function for saving Book display options.
		 */
		public function wpbooklist_dashboard_save_book_display_options_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_dashboard_save_book_display_options_action_callback', 'security' );

			if ( isset( $_POST['library'] ) ) {
				$library = filter_var( wp_unslash( $_POST['library'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['booksonpage'] ) ) {
				$booksonpage = filter_var( wp_unslash( $_POST['booksonpage'] ), FILTER_SANITIZE_NUMBER_INT );
			}

			if ( isset( $_POST['hidefacebook'] ) ) {
				$hidefacebook = filter_var( wp_unslash( $_POST['hidefacebook'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideasin'] ) ) {
				$hideasin = filter_var( wp_unslash( $_POST['hideasin'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideformat'] ) ) {
				$hideformat = filter_var( wp_unslash( $_POST['hideformat'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidegenres'] ) ) {
				$hidegenres = filter_var( wp_unslash( $_POST['hidegenres'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideillustrator'] ) ) {
				$hideillustrator = filter_var( wp_unslash( $_POST['hideillustrator'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideisbn10'] ) ) {
				$hideisbn10 = filter_var( wp_unslash( $_POST['hideisbn10'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideisbn13'] ) ) {
				$hideisbn13 = filter_var( wp_unslash( $_POST['hideisbn13'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidekeywords'] ) ) {
				$hidekeywords = filter_var( wp_unslash( $_POST['hidekeywords'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidelanguage'] ) ) {
				$hidelanguage = filter_var( wp_unslash( $_POST['hidelanguage'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidenumberinseries'] ) ) {
				$hidenumberinseries = filter_var( wp_unslash( $_POST['hidenumberinseries'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideorigpubyear'] ) ) {
				$hideorigpubyear = filter_var( wp_unslash( $_POST['hideorigpubyear'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideorigtitle'] ) ) {
				$hideorigtitle = filter_var( wp_unslash( $_POST['hideorigtitle'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideothereditions'] ) ) {
				$hideothereditions = filter_var( wp_unslash( $_POST['hideothereditions'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideoutofprint'] ) ) {
				$hideoutofprint = filter_var( wp_unslash( $_POST['hideoutofprint'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideseries'] ) ) {
				$hideseries = filter_var( wp_unslash( $_POST['hideseries'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideshortdesc'] ) ) {
				$hideshortdesc = filter_var( wp_unslash( $_POST['hideshortdesc'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidesubgenre'] ) ) {
				$hidesubgenre = filter_var( wp_unslash( $_POST['hidesubgenre'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidecallnumber'] ) ) {
				$hidecallnumber = filter_var( wp_unslash( $_POST['hidecallnumber'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidetwitter'] ) ) {
				$hidetwitter = filter_var( wp_unslash( $_POST['hidetwitter'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidegoogleplus'] ) ) {
				$hidegoogleplus = filter_var( wp_unslash( $_POST['hidegoogleplus'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidemessenger'] ) ) {
				$hidemessenger = filter_var( wp_unslash( $_POST['hidemessenger'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidepinterest'] ) ) {
				$hidepinterest = filter_var( wp_unslash( $_POST['hidepinterest'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideemail'] ) ) {
				$hideemail = filter_var( wp_unslash( $_POST['hideemail'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidegoodreadswidget'] ) ) {
				$hidegoodreadswidget = filter_var( wp_unslash( $_POST['hidegoodreadswidget'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideadditionalimgs'] ) ) {
				$hideadditionalimgs = filter_var( wp_unslash( $_POST['hideadditionalimgs'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideamazonreview'] ) ) {
				$hideamazonreview = filter_var( wp_unslash( $_POST['hideamazonreview'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidedescription'] ) ) {
				$hidedescription = filter_var( wp_unslash( $_POST['hidedescription'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidesimilar'] ) ) {
				$hidesimilar = filter_var( wp_unslash( $_POST['hidesimilar'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidebooktitle'] ) ) {
				$hidebooktitle = filter_var( wp_unslash( $_POST['hidebooktitle'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidebookimage'] ) ) {
				$hidebookimage = filter_var( wp_unslash( $_POST['hidebookimage'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidefinished'] ) ) {
				$hidefinished = filter_var( wp_unslash( $_POST['hidefinished'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideauthor'] ) ) {
				$hideauthor = filter_var( wp_unslash( $_POST['hideauthor'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidecategory'] ) ) {
				$hidecategory = filter_var( wp_unslash( $_POST['hidecategory'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidebookpage'] ) ) {
				$hidebookpage = filter_var( wp_unslash( $_POST['hidebookpage'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidepages'] ) ) {
				$hidepages = filter_var( wp_unslash( $_POST['hidepages'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidebookpost'] ) ) {
				$hidebookpost = filter_var( wp_unslash( $_POST['hidebookpost'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidepublisher'] ) ) {
				$hidepublisher = filter_var( wp_unslash( $_POST['hidepublisher'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidepubdate'] ) ) {
				$hidepubdate = filter_var( wp_unslash( $_POST['hidepubdate'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidesigned'] ) ) {
				$hidesigned = filter_var( wp_unslash( $_POST['hidesigned'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidesubject'] ) ) {
				$hidesubject = filter_var( wp_unslash( $_POST['hidesubject'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidecountry'] ) ) {
				$hidecountry = filter_var( wp_unslash( $_POST['hidecountry'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidefirstedition'] ) ) {
				$hidefirstedition = filter_var( wp_unslash( $_POST['hidefirstedition'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidefeaturedtitles'] ) ) {
				$hidefeaturedtitles = filter_var( wp_unslash( $_POST['hidefeaturedtitles'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidenotes'] ) ) {
				$hidenotes = filter_var( wp_unslash( $_POST['hidenotes'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidequotebook'] ) ) {
				$hidequotebook = filter_var( wp_unslash( $_POST['hidequotebook'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideratingbook'] ) ) {
				$hideratingbook = filter_var( wp_unslash( $_POST['hideratingbook'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidegooglepurchase'] ) ) {
				$hidegooglepurchase = filter_var( wp_unslash( $_POST['hidegooglepurchase'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideamazonpurchase'] ) ) {
				$hideamazonpurchase = filter_var( wp_unslash( $_POST['hideamazonpurchase'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidebnpurchase'] ) ) {
				$hidebnpurchase = filter_var( wp_unslash( $_POST['hidebnpurchase'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideitunespurchase'] ) ) {
				$hideitunespurchase = filter_var( wp_unslash( $_POST['hideitunespurchase'] ), FILTER_SANITIZE_STRING );
			}

			$hidecolorboxbuyimg = null;
			if ( isset( $_POST['hidecolorboxbuyimg'] ) ) {
				$hidecolorboxbuyimg = filter_var( wp_unslash( $_POST['hidecolorboxbuyimg'] ), FILTER_SANITIZE_STRING );
			}

			$hidecolorboxbuyprice = null;
			if ( isset( $_POST['hidecolorboxbuyprice'] ) ) {
				$hidecolorboxbuyprice = filter_var( wp_unslash( $_POST['hidecolorboxbuyprice'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidekindleprev'] ) ) {
				$hidekindleprev = filter_var( wp_unslash( $_POST['hidekindleprev'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidegoogleprev'] ) ) {
				$hidegoogleprev = filter_var( wp_unslash( $_POST['hidegoogleprev'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidekobopurchase'] ) ) {
				$hidekobopurchase = filter_var( wp_unslash( $_POST['hidekobopurchase'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidebampurchase'] ) ) {
				$hidebampurchase = filter_var( wp_unslash( $_POST['hidebampurchase'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['sortoption'] ) ) {
				$sortoption = filter_var( wp_unslash( $_POST['sortoption'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['booksonpage'] ) ) {
				$booksonpage = filter_var( wp_unslash( $_POST['booksonpage'] ), FILTER_SANITIZE_NUMBER_INT );
			}

			$enablepurchase = null;
			if ( isset( $_POST['enablepurchase'] ) ) {
				$enablepurchase = filter_var( wp_unslash( $_POST['enablepurchase'] ), FILTER_SANITIZE_STRING );
			}

			$settings_array = array(
				'hidecolorboxbuyimg'   => $hidecolorboxbuyimg,
				'hidecolorboxbuyprice' => $hidecolorboxbuyprice,
				'enablepurchase'       => $enablepurchase,
				'booksonpage'          => $booksonpage,
				'hideadditionalimgs'   => $hideadditionalimgs,
				'hideamazonpurchase'   => $hideamazonpurchase,
				'hideamazonreview'     => $hideamazonreview,
				'hideasin'             => $hideasin,
				'hideauthor'           => $hideauthor,
				'hidebnpurchase'       => $hidebnpurchase,
				'hidefinished'         => $hidefinished,
				'hidebookpage'         => $hidebookpage,
				'hidepages'            => $hidepages,
				'hidebookpost'         => $hidebookpost,
				'hidebooktitle'        => $hidebooktitle,
				'hidebampurchase'      => $hidebampurchase,
				'hidecallnumber'       => $hidecallnumber,
				'hidecountry'          => $hidecountry,
				'hidefirstedition'     => $hidefirstedition,
				'hideemail'            => $hideemail,
				'hidemessenger'        => $hidemessenger,
				'hidefacebook'         => $hidefacebook,
				'hidefeaturedtitles'   => $hidefeaturedtitles,
				'hideformat'           => $hideformat,
				'hidebookimage'        => $hidebookimage,
				'hidedescription'      => $hidedescription,
				'hidegenres'           => $hidegenres,
				'hidegoodreadswidget'  => $hidegoodreadswidget,
				'hidegooglepurchase'   => $hidegooglepurchase,
				'hideillustrator'      => $hideillustrator,
				'hideisbn10'           => $hideisbn10,
				'hideisbn13'           => $hideisbn13,
				'hideitunespurchase'   => $hideitunespurchase,
				'hidekeywords'         => $hidekeywords,
				'hidekobopurchase'     => $hidekobopurchase,
				'hidelanguage'         => $hidelanguage,
				'hidenotes'            => $hidenotes,
				'hidenumberinseries'   => $hidenumberinseries,
				'hideorigpubyear'      => $hideorigpubyear,
				'hideorigtitle'        => $hideorigtitle,
				'hideothereditions'    => $hideothereditions,
				'hideoutofprint'       => $hideoutofprint,
				'hidepinterest'        => $hidepinterest,
				'hidepubdate'          => $hidepubdate,
				'hidepublisher'        => $hidepublisher,
				'hideratingbook'       => $hideratingbook,
				'hideseries'           => $hideseries,
				'hideshortdesc'        => $hideshortdesc,
				'hidesigned'           => $hidesigned,
				'hidesimilar'          => $hidesimilar,
				'hidesubgenre'         => $hidesubgenre,
				'hidetwitter'          => $hidetwitter,
				'sortoption'           => $sortoption,
			);

			require_once CLASS_DIR . 'class-display-options.php';
			$settings_class = new WPBookList_Display_Options();
			$settings_class->save_library_settings( $library, $settings_array );
			wp_die();
		}

		/**
		 * Callback function for saving post display options.
		 */
		public function wpbooklist_dashboard_save_post_display_options_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_dashboard_save_post_display_options_action_callback', 'security' );

			if ( isset( $_POST['hiderating'] ) ) {
				$hiderating = filter_var( wp_unslash( $_POST['hiderating'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidequote'] ) ) {
				$hidequote = filter_var( wp_unslash( $_POST['hidequote'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidefacebook'] ) ) {
				$hidefacebook = filter_var( wp_unslash( $_POST['hidefacebook'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideasin'] ) ) {
				$hideasin = filter_var( wp_unslash( $_POST['hideasin'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideformat'] ) ) {
				$hideformat = filter_var( wp_unslash( $_POST['hideformat'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidegenres'] ) ) {
				$hidegenres = filter_var( wp_unslash( $_POST['hidegenres'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideillustrator'] ) ) {
				$hideillustrator = filter_var( wp_unslash( $_POST['hideillustrator'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideisbn10'] ) ) {
				$hideisbn10 = filter_var( wp_unslash( $_POST['hideisbn10'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideisbn13'] ) ) {
				$hideisbn13 = filter_var( wp_unslash( $_POST['hideisbn13'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidekeywords'] ) ) {
				$hidekeywords = filter_var( wp_unslash( $_POST['hidekeywords'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidelanguage'] ) ) {
				$hidelanguage = filter_var( wp_unslash( $_POST['hidelanguage'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidenumberinseries'] ) ) {
				$hidenumberinseries = filter_var( wp_unslash( $_POST['hidenumberinseries'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideorigpubyear'] ) ) {
				$hideorigpubyear = filter_var( wp_unslash( $_POST['hideorigpubyear'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideorigtitle'] ) ) {
				$hideorigtitle = filter_var( wp_unslash( $_POST['hideorigtitle'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideothereditions'] ) ) {
				$hideothereditions = filter_var( wp_unslash( $_POST['hideothereditions'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideoutofprint'] ) ) {
				$hideoutofprint = filter_var( wp_unslash( $_POST['hideoutofprint'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideseries'] ) ) {
				$hideseries = filter_var( wp_unslash( $_POST['hideseries'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideshortdesc'] ) ) {
				$hideshortdesc = filter_var( wp_unslash( $_POST['hideshortdesc'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidesubgenre'] ) ) {
				$hidesubgenre = filter_var( wp_unslash( $_POST['hidesubgenre'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidecallnumber'] ) ) {
				$hidecallnumber = filter_var( wp_unslash( $_POST['hidecallnumber'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidetwitter'] ) ) {
				$hidetwitter = filter_var( wp_unslash( $_POST['hidetwitter'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidegoogleplus'] ) ) {
				$hidegoogleplus = filter_var( wp_unslash( $_POST['hidegoogleplus'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidemessenger'] ) ) {
				$hidemessenger = filter_var( wp_unslash( $_POST['hidemessenger'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidepinterest'] ) ) {
				$hidepinterest = filter_var( wp_unslash( $_POST['hidepinterest'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideemail'] ) ) {
				$hideemail = filter_var( wp_unslash( $_POST['hideemail'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideamazonreview'] ) ) {
				$hideamazonreview = filter_var( wp_unslash( $_POST['hideamazonreview'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidedescription'] ) ) {
				$hidedescription = filter_var( wp_unslash( $_POST['hidedescription'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidesimilar'] ) ) {
				$hidesimilar = filter_var( wp_unslash( $_POST['hidesimilar'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidebooktitle'] ) ) {
				$hidebooktitle = filter_var( wp_unslash( $_POST['hidebooktitle'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidebookimage'] ) ) {
				$hidebookimage = filter_var( wp_unslash( $_POST['hidebookimage'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidefinished'] ) ) {
				$hidefinished = filter_var( wp_unslash( $_POST['hidefinished'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideauthor'] ) ) {
				$hideauthor = filter_var( wp_unslash( $_POST['hideauthor'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidecategory'] ) ) {
				$hidecategory = filter_var( wp_unslash( $_POST['hidecategory'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidepages'] ) ) {
				$hidepages = filter_var( wp_unslash( $_POST['hidepages'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidepublisher'] ) ) {
				$hidepublisher = filter_var( wp_unslash( $_POST['hidepublisher'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidepubdate'] ) ) {
				$hidepubdate = filter_var( wp_unslash( $_POST['hidepubdate'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidesigned'] ) ) {
				$hidesigned = filter_var( wp_unslash( $_POST['hidesigned'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidesubject'] ) ) {
				$hidesubject = filter_var( wp_unslash( $_POST['hidesubject'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidecountry'] ) ) {
				$hidecountry = filter_var( wp_unslash( $_POST['hidecountry'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidefirstedition'] ) ) {
				$hidefirstedition = filter_var( wp_unslash( $_POST['hidefirstedition'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidefeaturedtitles'] ) ) {
				$hidefeaturedtitles = filter_var( wp_unslash( $_POST['hidefeaturedtitles'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidenotes'] ) ) {
				$hidenotes = filter_var( wp_unslash( $_POST['hidenotes'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidequotebook'] ) ) {
				$hidequotebook = filter_var( wp_unslash( $_POST['hidequotebook'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidegooglepurchase'] ) ) {
				$hidegooglepurchase = filter_var( wp_unslash( $_POST['hidegooglepurchase'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideamazonpurchase'] ) ) {
				$hideamazonpurchase = filter_var( wp_unslash( $_POST['hideamazonpurchase'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidebnpurchase'] ) ) {
				$hidebnpurchase = filter_var( wp_unslash( $_POST['hidebnpurchase'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideitunespurchase'] ) ) {
				$hideitunespurchase = filter_var( wp_unslash( $_POST['hideitunespurchase'] ), FILTER_SANITIZE_STRING );
			}

			$hidefrontendbuyimg = null;
			if ( isset( $_POST['hidefrontendbuyimg'] ) ) {
				$hidefrontendbuyimg = filter_var( wp_unslash( $_POST['hidefrontendbuyimg'] ), FILTER_SANITIZE_STRING );
			}

			$hidecolorboxbuyimg = null;
			if ( isset( $_POST['hidecolorboxbuyimg'] ) ) {
				$hidecolorboxbuyimg = filter_var( wp_unslash( $_POST['hidecolorboxbuyimg'] ), FILTER_SANITIZE_STRING );
			}

			$hidecolorboxbuyprice = null;
			if ( isset( $_POST['hidecolorboxbuyprice'] ) ) {
				$hidecolorboxbuyprice = filter_var( wp_unslash( $_POST['hidecolorboxbuyprice'] ), FILTER_SANITIZE_STRING );
			}

			$hidefrontendbuyprice = null;
			if ( isset( $_POST['hidefrontendbuyprice'] ) ) {
				$hidefrontendbuyprice = filter_var( wp_unslash( $_POST['hidefrontendbuyprice'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidekindleprev'] ) ) {
				$hidekindleprev = filter_var( wp_unslash( $_POST['hidekindleprev'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidegoogleprev'] ) ) {
				$hidegoogleprev = filter_var( wp_unslash( $_POST['hidegoogleprev'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidekobopurchase'] ) ) {
				$hidekobopurchase = filter_var( wp_unslash( $_POST['hidekobopurchase'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidebampurchase'] ) ) {
				$hidebampurchase = filter_var( wp_unslash( $_POST['hidebampurchase'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['booksonpage'] ) ) {
				$booksonpage = filter_var( wp_unslash( $_POST['booksonpage'] ), FILTER_SANITIZE_NUMBER_INT );
			}

			if ( isset( $_POST['booksonpage'] ) ) {
				$booksonpage = filter_var( wp_unslash( $_POST['booksonpage'] ), FILTER_SANITIZE_NUMBER_INT );
			}

			if ( isset( $_POST['hidefilter'] ) ) {
				$hidefilter = filter_var( wp_unslash( $_POST['hidefilter'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidefinishedsort'] ) ) {
				$hidefinishedsort = filter_var( wp_unslash( $_POST['hidefinishedsort'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidefirstsort'] ) ) {
				$hidefirstsort = filter_var( wp_unslash( $_POST['hidefirstsort'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidelibrarytitle'] ) ) {
				$hidelibrarytitle = filter_var( wp_unslash( $_POST['hidelibrarytitle'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidequote'] ) ) {
				$hidequote = filter_var( wp_unslash( $_POST['hidequote'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hiderating'] ) ) {
				$hiderating = filter_var( wp_unslash( $_POST['hiderating'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidesearch'] ) ) {
				$hidesearch = filter_var( wp_unslash( $_POST['hidesearch'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidesignedsort'] ) ) {
				$hidesignedsort = filter_var( wp_unslash( $_POST['hidesignedsort'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidestats'] ) ) {
				$hidestats = filter_var( wp_unslash( $_POST['hidestats'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidesubjectsort'] ) ) {
				$hidesubjectsort = filter_var( wp_unslash( $_POST['hidesubjectsort'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['library'] ) ) {
				$library = filter_var( wp_unslash( $_POST['library'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['sortoption'] ) ) {
				$sortoption = filter_var( wp_unslash( $_POST['sortoption'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideadditionalimgs'] ) ) {
				$hideadditionalimgs = filter_var( wp_unslash( $_POST['hideadditionalimgs'] ), FILTER_SANITIZE_STRING );
			}

			$enablepurchase = null;
			if ( isset( $_POST['enablepurchase'] ) ) {
				$enablepurchase = filter_var( wp_unslash( $_POST['enablepurchase'] ), FILTER_SANITIZE_STRING );
			}

			$settings_array = array(
				'hideadditionalimgs'   => $hideadditionalimgs,
				'hideamazonpurchase'   => $hideamazonpurchase,
				'hideamazonreview'     => $hideamazonreview,
				'hideasin'             => $hideasin,
				'hideauthor'           => $hideauthor,
				'hidebnpurchase'       => $hidebnpurchase,
				'hidefinished'         => $hidefinished,
				'hidepages'            => $hidepages,
				'hidebooktitle'        => $hidebooktitle,
				'hidebampurchase'      => $hidebampurchase,
				'hidecallnumber'       => $hidecallnumber,
				'hidecountry'          => $hidecountry,
				'hidefirstedition'     => $hidefirstedition,
				'hideemail'            => $hideemail,
				'hidemessenger'        => $hidemessenger,
				'hidefacebook'         => $hidefacebook,
				'hidefeaturedtitles'   => $hidefeaturedtitles,
				'hideformat'           => $hideformat,
				'hidebookimage'        => $hidebookimage,
				'hidedescription'      => $hidedescription,
				'hidegenres'           => $hidegenres,
				'hidegooglepurchase'   => $hidegooglepurchase,
				'hideillustrator'      => $hideillustrator,
				'hideisbn10'           => $hideisbn10,
				'hideisbn13'           => $hideisbn13,
				'hideitunespurchase'   => $hideitunespurchase,
				'hidekeywords'         => $hidekeywords,
				'hidekobopurchase'     => $hidekobopurchase,
				'hidelanguage'         => $hidelanguage,
				'hidenotes'            => $hidenotes,
				'hidenumberinseries'   => $hidenumberinseries,
				'hideorigpubyear'      => $hideorigpubyear,
				'hideorigtitle'        => $hideorigtitle,
				'hideothereditions'    => $hideothereditions,
				'hideoutofprint'       => $hideoutofprint,
				'hidepinterest'        => $hidepinterest,
				'hidepubdate'          => $hidepubdate,
				'hidepublisher'        => $hidepublisher,
				'hideseries'           => $hideseries,
				'hideshortdesc'        => $hideshortdesc,
				'hidesigned'           => $hidesigned,
				'hidesimilar'          => $hidesimilar,
				'hidesubgenre'         => $hidesubgenre,
				'hidetwitter'          => $hidetwitter,
				'hiderating'           => $hiderating,
				'hidequote'            => $hidequote,
				'hidefrontendbuyimg'   => $hidefrontendbuyimg,
				'hidefrontendbuyprice' => $hidefrontendbuyprice,
				'enablepurchase'       => $enablepurchase,
			);

			require_once CLASS_DIR . 'class-display-options.php';
			$settings_class = new WPBookList_Display_Options();
			$settings_class->save_post_settings( $settings_array );
			wp_die();
		}

		/**
		 * Callback function for saving page display options.
		 */
		public function wpbooklist_dashboard_save_page_display_options_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_dashboard_save_page_display_options_action_callback', 'security' );

			if ( isset( $_POST['hiderating'] ) ) {
				$hiderating = filter_var( wp_unslash( $_POST['hiderating'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidequote'] ) ) {
				$hidequote = filter_var( wp_unslash( $_POST['hidequote'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidefacebook'] ) ) {
				$hidefacebook = filter_var( wp_unslash( $_POST['hidefacebook'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideasin'] ) ) {
				$hideasin = filter_var( wp_unslash( $_POST['hideasin'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideformat'] ) ) {
				$hideformat = filter_var( wp_unslash( $_POST['hideformat'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidegenres'] ) ) {
				$hidegenres = filter_var( wp_unslash( $_POST['hidegenres'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideillustrator'] ) ) {
				$hideillustrator = filter_var( wp_unslash( $_POST['hideillustrator'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideisbn10'] ) ) {
				$hideisbn10 = filter_var( wp_unslash( $_POST['hideisbn10'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideisbn13'] ) ) {
				$hideisbn13 = filter_var( wp_unslash( $_POST['hideisbn13'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidekeywords'] ) ) {
				$hidekeywords = filter_var( wp_unslash( $_POST['hidekeywords'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidelanguage'] ) ) {
				$hidelanguage = filter_var( wp_unslash( $_POST['hidelanguage'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidenumberinseries'] ) ) {
				$hidenumberinseries = filter_var( wp_unslash( $_POST['hidenumberinseries'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideorigpubyear'] ) ) {
				$hideorigpubyear = filter_var( wp_unslash( $_POST['hideorigpubyear'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideorigtitle'] ) ) {
				$hideorigtitle = filter_var( wp_unslash( $_POST['hideorigtitle'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideothereditions'] ) ) {
				$hideothereditions = filter_var( wp_unslash( $_POST['hideothereditions'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideoutofprint'] ) ) {
				$hideoutofprint = filter_var( wp_unslash( $_POST['hideoutofprint'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideseries'] ) ) {
				$hideseries = filter_var( wp_unslash( $_POST['hideseries'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideshortdesc'] ) ) {
				$hideshortdesc = filter_var( wp_unslash( $_POST['hideshortdesc'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidesubgenre'] ) ) {
				$hidesubgenre = filter_var( wp_unslash( $_POST['hidesubgenre'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidecallnumber'] ) ) {
				$hidecallnumber = filter_var( wp_unslash( $_POST['hidecallnumber'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidetwitter'] ) ) {
				$hidetwitter = filter_var( wp_unslash( $_POST['hidetwitter'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidegoogleplus'] ) ) {
				$hidegoogleplus = filter_var( wp_unslash( $_POST['hidegoogleplus'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidemessenger'] ) ) {
				$hidemessenger = filter_var( wp_unslash( $_POST['hidemessenger'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidepinterest'] ) ) {
				$hidepinterest = filter_var( wp_unslash( $_POST['hidepinterest'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideemail'] ) ) {
				$hideemail = filter_var( wp_unslash( $_POST['hideemail'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideamazonreview'] ) ) {
				$hideamazonreview = filter_var( wp_unslash( $_POST['hideamazonreview'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidedescription'] ) ) {
				$hidedescription = filter_var( wp_unslash( $_POST['hidedescription'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidesimilar'] ) ) {
				$hidesimilar = filter_var( wp_unslash( $_POST['hidesimilar'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidebooktitle'] ) ) {
				$hidebooktitle = filter_var( wp_unslash( $_POST['hidebooktitle'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidebookimage'] ) ) {
				$hidebookimage = filter_var( wp_unslash( $_POST['hidebookimage'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidefinished'] ) ) {
				$hidefinished = filter_var( wp_unslash( $_POST['hidefinished'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideauthor'] ) ) {
				$hideauthor = filter_var( wp_unslash( $_POST['hideauthor'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidecategory'] ) ) {
				$hidecategory = filter_var( wp_unslash( $_POST['hidecategory'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidepages'] ) ) {
				$hidepages = filter_var( wp_unslash( $_POST['hidepages'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidepublisher'] ) ) {
				$hidepublisher = filter_var( wp_unslash( $_POST['hidepublisher'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidepubdate'] ) ) {
				$hidepubdate = filter_var( wp_unslash( $_POST['hidepubdate'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidesigned'] ) ) {
				$hidesigned = filter_var( wp_unslash( $_POST['hidesigned'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidesubject'] ) ) {
				$hidesubject = filter_var( wp_unslash( $_POST['hidesubject'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidecountry'] ) ) {
				$hidecountry = filter_var( wp_unslash( $_POST['hidecountry'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidefirstedition'] ) ) {
				$hidefirstedition = filter_var( wp_unslash( $_POST['hidefirstedition'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidefeaturedtitles'] ) ) {
				$hidefeaturedtitles = filter_var( wp_unslash( $_POST['hidefeaturedtitles'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidenotes'] ) ) {
				$hidenotes = filter_var( wp_unslash( $_POST['hidenotes'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidequotebook'] ) ) {
				$hidequotebook = filter_var( wp_unslash( $_POST['hidequotebook'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidegooglepurchase'] ) ) {
				$hidegooglepurchase = filter_var( wp_unslash( $_POST['hidegooglepurchase'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideamazonpurchase'] ) ) {
				$hideamazonpurchase = filter_var( wp_unslash( $_POST['hideamazonpurchase'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidebnpurchase'] ) ) {
				$hidebnpurchase = filter_var( wp_unslash( $_POST['hidebnpurchase'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideitunespurchase'] ) ) {
				$hideitunespurchase = filter_var( wp_unslash( $_POST['hideitunespurchase'] ), FILTER_SANITIZE_STRING );
			}

			$hidefrontendbuyimg = null;
			if ( isset( $_POST['hidefrontendbuyimg'] ) ) {
				$hidefrontendbuyimg = filter_var( wp_unslash( $_POST['hidefrontendbuyimg'] ), FILTER_SANITIZE_STRING );
			}

			$hidecolorboxbuyimg = null;
			if ( isset( $_POST['hidecolorboxbuyimg'] ) ) {
				$hidecolorboxbuyimg = filter_var( wp_unslash( $_POST['hidecolorboxbuyimg'] ), FILTER_SANITIZE_STRING );
			}

			$hidecolorboxbuyprice = null;
			if ( isset( $_POST['hidecolorboxbuyprice'] ) ) {
				$hidecolorboxbuyprice = filter_var( wp_unslash( $_POST['hidecolorboxbuyprice'] ), FILTER_SANITIZE_STRING );
			}

			$hidefrontendbuyprice = null;
			if ( isset( $_POST['hidefrontendbuyprice'] ) ) {
				$hidefrontendbuyprice = filter_var( wp_unslash( $_POST['hidefrontendbuyprice'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidekindleprev'] ) ) {
				$hidekindleprev = filter_var( wp_unslash( $_POST['hidekindleprev'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidegoogleprev'] ) ) {
				$hidegoogleprev = filter_var( wp_unslash( $_POST['hidegoogleprev'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidekobopurchase'] ) ) {
				$hidekobopurchase = filter_var( wp_unslash( $_POST['hidekobopurchase'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidebampurchase'] ) ) {
				$hidebampurchase = filter_var( wp_unslash( $_POST['hidebampurchase'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['booksonpage'] ) ) {
				$booksonpage = filter_var( wp_unslash( $_POST['booksonpage'] ), FILTER_SANITIZE_NUMBER_INT );
			}

			if ( isset( $_POST['booksonpage'] ) ) {
				$booksonpage = filter_var( wp_unslash( $_POST['booksonpage'] ), FILTER_SANITIZE_NUMBER_INT );
			}

			if ( isset( $_POST['hidefilter'] ) ) {
				$hidefilter = filter_var( wp_unslash( $_POST['hidefilter'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidefinishedsort'] ) ) {
				$hidefinishedsort = filter_var( wp_unslash( $_POST['hidefinishedsort'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidefirstsort'] ) ) {
				$hidefirstsort = filter_var( wp_unslash( $_POST['hidefirstsort'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidelibrarytitle'] ) ) {
				$hidelibrarytitle = filter_var( wp_unslash( $_POST['hidelibrarytitle'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidequote'] ) ) {
				$hidequote = filter_var( wp_unslash( $_POST['hidequote'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hiderating'] ) ) {
				$hiderating = filter_var( wp_unslash( $_POST['hiderating'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidesearch'] ) ) {
				$hidesearch = filter_var( wp_unslash( $_POST['hidesearch'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidesignedsort'] ) ) {
				$hidesignedsort = filter_var( wp_unslash( $_POST['hidesignedsort'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidestats'] ) ) {
				$hidestats = filter_var( wp_unslash( $_POST['hidestats'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hidesubjectsort'] ) ) {
				$hidesubjectsort = filter_var( wp_unslash( $_POST['hidesubjectsort'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['library'] ) ) {
				$library = filter_var( wp_unslash( $_POST['library'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['sortoption'] ) ) {
				$sortoption = filter_var( wp_unslash( $_POST['sortoption'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['hideadditionalimgs'] ) ) {
				$hideadditionalimgs = filter_var( wp_unslash( $_POST['hideadditionalimgs'] ), FILTER_SANITIZE_STRING );
			}

			$enablepurchase = null;
			if ( isset( $_POST['enablepurchase'] ) ) {
				$enablepurchase = filter_var( wp_unslash( $_POST['enablepurchase'] ), FILTER_SANITIZE_STRING );
			}

			$settings_array = array(
				'hideadditionalimgs'   => $hideadditionalimgs,
				'hideamazonpurchase'   => $hideamazonpurchase,
				'hideamazonreview'     => $hideamazonreview,
				'hideasin'             => $hideasin,
				'hideauthor'           => $hideauthor,
				'hidebnpurchase'       => $hidebnpurchase,
				'hidefinished'         => $hidefinished,
				'hidepages'            => $hidepages,
				'hidebooktitle'        => $hidebooktitle,
				'hidebampurchase'      => $hidebampurchase,
				'hidecallnumber'       => $hidecallnumber,
				'hidecountry'          => $hidecountry,
				'hidefirstedition'     => $hidefirstedition,
				'hideemail'            => $hideemail,
				'hidemessenger'        => $hidemessenger,
				'hidefacebook'         => $hidefacebook,
				'hidefeaturedtitles'   => $hidefeaturedtitles,
				'hideformat'           => $hideformat,
				'hidebookimage'        => $hidebookimage,
				'hidedescription'      => $hidedescription,
				'hidegenres'           => $hidegenres,
				'hidegooglepurchase'   => $hidegooglepurchase,
				'hideillustrator'      => $hideillustrator,
				'hideisbn10'           => $hideisbn10,
				'hideisbn13'           => $hideisbn13,
				'hideitunespurchase'   => $hideitunespurchase,
				'hidekeywords'         => $hidekeywords,
				'hidekobopurchase'     => $hidekobopurchase,
				'hidelanguage'         => $hidelanguage,
				'hidenotes'            => $hidenotes,
				'hidenumberinseries'   => $hidenumberinseries,
				'hideorigpubyear'      => $hideorigpubyear,
				'hideorigtitle'        => $hideorigtitle,
				'hideothereditions'    => $hideothereditions,
				'hideoutofprint'       => $hideoutofprint,
				'hidepinterest'        => $hidepinterest,
				'hidepubdate'          => $hidepubdate,
				'hidepublisher'        => $hidepublisher,
				'hideseries'           => $hideseries,
				'hideshortdesc'        => $hideshortdesc,
				'hidesigned'           => $hidesigned,
				'hidesimilar'          => $hidesimilar,
				'hidesubgenre'         => $hidesubgenre,
				'hidetwitter'          => $hidetwitter,
				'hiderating'           => $hiderating,
				'hidequote'            => $hidequote,
				'hidefrontendbuyimg'   => $hidefrontendbuyimg,
				'hidefrontendbuyprice' => $hidefrontendbuyprice,
				'enablepurchase'       => $enablepurchase,
			);

			require_once CLASS_DIR . 'class-display-options.php';
			$settings_class = new WPBookList_Display_Options();
			$settings_class->save_page_settings( $settings_array );
			wp_die();
		}

		/**
		 * Callback function for getting Library display options for when the Library drop-down changes.
		 */
		public function wpbooklist_change_library_display_options_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_change_library_display_options_action_callback', 'security' );

			$row = '';
			if ( isset( $_POST['library'] ) ) {
				$library    = filter_var( wp_unslash( $_POST['library'] ), FILTER_SANITIZE_STRING );
				$table_name = '';
				if ( $library === $wpdb->prefix . 'wpbooklist_jre_saved_book_log' ) {
					$table_name = $wpdb->prefix . 'wpbooklist_jre_user_options';
				} else {
					$library    = explode( '_', $library );
					$library    = array_pop( $library );
					$table_name = $wpdb->prefix . 'wpbooklist_jre_settings_' . $library;
				}

				$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE ID = %d", 1 ) );

				// Now get the Custom Fields string from the default settings table, and add that into the return results.
				$default_settings = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_user_options' );
				$row->customfields = $default_settings->customfields;

			}
			wp_die( wp_json_encode( $row ) );
		}

		/**
		 * Callback Function for showing the Edit Book form.
		 */
		public function wpbooklist_edit_book_show_form_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_edit_book_show_form_action_callback', 'security' );

			if ( isset( $_POST['bookId'] ) ) {
				$book_id = filter_var( wp_unslash( $_POST['bookId'] ), FILTER_SANITIZE_NUMBER_INT );
			}

			if ( isset( $_POST['table'] ) ) {
				$table = filter_var( wp_unslash( $_POST['table'] ), FILTER_SANITIZE_STRING );
			}

			$book_data         = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE ID = %d", $book_id ) );
			$crosssell_ids     = '';
			$crosssell_titles  = '';
			$upsell_ids        = '';
			$upsell_titles     = '';
			$product           = 'null';
			$image_thumb       = array();
			$id                = null;
			$image_url['file'] = '';
			$image_url['name'] = '';
			$attachment        = array();

			// Get Woocommerce product, if one exists.
			$cat = '';
			if ( null !== $book_data->woocommerce ) {
				$product = get_post_meta( $book_data->woocommerce );

				// Get all downloadable files associated with product.
				if ( array_key_exists( '_downloadable_files', $product ) && array_key_exists( 0, $product['_downloadable_files'] ) ) {
					$df         = wp_json_encode( current( unserialize( $product['_downloadable_files'][0] ) ) );
					$image_url  = current( unserialize( $product['_downloadable_files'][0] ) );
					$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid = %s", $image_url['file'] ) );

					if ( is_array( $attachment ) && array_key_exists( 0, $attachment ) ) {
						$image_thumb = wp_get_attachment_image_src( $attachment[0], 'thumbnail' );
					}
				}

				// Get crosssell IDs and titles.
				$cs = unserialize( $product['_crosssell_ids'][0] );
				foreach ( $cs as $key => $value ) {
						$crosssell_ids = $crosssell_ids . ',' . $value;
				}

				// Get upsell IDs and titles.
				$us = unserialize( $product['_upsell_ids'][0] );
				foreach ( $us as $key => $value ) {
						$upsell_ids = $upsell_ids . ',' . $value;
				}

				// Get product category.
				$cat = get_the_terms ( $book_data->woocommerce, 'product_cat' );
				if ( is_array( $cat ) && array_key_exists( 0, $cat ) ) {
					$cat = $cat[0]->name;
				} else {
					$cat = '';
				}

				$product = wp_json_encode( $product );
			}

			require_once CLASS_BOOK_DIR . 'class-wpbooklist-book-form.php';
			$form      = new WPBookList_Book_Form();
			$edit_form = $form->output_book_form();

			// Convert html entites back to normal as needed.
			$book_data->title = stripslashes( html_entity_decode( $book_data->title, ENT_QUOTES | ENT_XML1, 'UTF-8' ) );

			// Encode all book data for return trip.
			$book_data = wp_json_encode( $book_data );

			// Check to see if Storefront extension is active.
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
			if ( is_plugin_active( 'wpbooklist-storefront/wpbooklist-storefront.php' ) ) {
				$storefront = 'true';
			} else {
				$storefront = 'false';
			}

			if ( is_array( $attachment ) && array_key_exists( 0, $attachment ) ) {
				$attachment = $attachment[0];
			} else {
				$attachment = '';
			}

			echo $book_data . '–sep-seperator-sep–' . $edit_form . '–sep-seperator-sep–' . $product . '–sep-seperator-sep–' . $storefront . '–sep-seperator-sep–' . $crosssell_ids . '–sep-seperator-sep–' . $crosssell_ids . '–sep-seperator-sep–' . $upsell_ids . '–sep-seperator-sep–' . $upsell_ids . '–sep-seperator-sep–' . $cat . '–sep-seperator-sep–' . basename( $image_url['file'] ) . '–sep-seperator-sep–' . $attachment;

			wp_die();
		}

		/**
		 * Callback function for the Edit Book pagination.
		 */
		public function wpbooklist_edit_book_pagination_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_edit_book_pagination_action_callback', 'security' );

			if ( isset( $_POST['currentOffset'] ) ) {
				$current_offset = filter_var( wp_unslash( $_POST['currentOffset'] ), FILTER_SANITIZE_NUMBER_INT );
			}

			if ( isset( $_POST['library'] ) ) {
				$library = filter_var( wp_unslash( $_POST['library'] ), FILTER_SANITIZE_STRING );
			}

			require_once CLASS_BOOK_DIR . 'class-wpbooklist-edit-book-form.php';
			$form = new WPBookList_Edit_Book_Form();
			echo $form->output_edit_book_form( $library, $current_offset ) . '_Separator_' . $library;
			wp_die();
		}

		/**
		 * Callback Function for switching libraries on the Edit Book tab.
		 */
		public function wpbooklist_edit_book_switch_lib_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_edit_book_switch_lib_action_callback', 'security' );

			if ( isset( $_POST['library'] ) ) {
				$library = filter_var( wp_unslash( $_POST['library'] ), FILTER_SANITIZE_STRING );
			}

			require_once CLASS_BOOK_DIR . 'class-wpbooklist-edit-book-form.php';
			$form = new WPBookList_Edit_Book_Form();
			echo $form->output_edit_book_form( $library, 0 ) . '_Separator_' . $library;

			wp_die();
		}

		/**
		 * Callback Function for searching for a title to edit.
		 */
		public function wpbooklist_edit_book_search_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_edit_book_search_action_callback', 'security' );

			if ( isset( $_POST['searchTerm'] ) ) {
				$search_term = filter_var( wp_unslash( $_POST['searchTerm'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['authorCheck'] ) ) {
				$author_check = filter_var( wp_unslash( $_POST['authorCheck'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['titleCheck'] ) ) {
				$title_check = filter_var( wp_unslash( $_POST['titleCheck'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['library'] ) ) {
				$library = filter_var( wp_unslash( $_POST['library'] ), FILTER_SANITIZE_STRING );
			}

			if ( 'true' === $title_check ) {
				$search_mode = 'title';
			}

			if ( 'true' === $author_check ) {
				$search_mode = 'author';
			}

			if ( 'true' === $author_check && 'true' === $title_check ) {
				$search_mode = 'both';
			}

			if ( 'true' !== $author_check && 'true' !== $title_check ) {
				$search_mode = 'both';
			}

			require_once CLASS_BOOK_DIR . 'class-wpbooklist-edit-book-form.php';
			$form = new WPBookList_Edit_Book_Form();
			echo $form->output_edit_book_form( $library, 0, $search_mode, $search_term ) . '_Separator_' . $library . '_Separator_' . $form->limit;
			wp_die();
		}


		/**
		 * Callback function editing a book.
		 
		public function wpbooklist_edit_book_actual_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_edit_book_actual_action_callback', 'security' );

			// First set the variables we'll be passing to class-wpbooklist-book.php to ''.
			$amazonauth           = '';
			$library              = '';
			$use_amazon_yes       = '';
			$isbn                 = '';
			$title                = '';
			$author               = '';
			$author_url           = '';
			$sale_url             = '';
			$category             = '';
			$price                = '';
			$pages                = '';
			$pub_year             = '';
			$publisher            = '';
			$description          = '';
			$subject              = '';
			$country              = '';
			$notes                = '';
			$rating               = '';
			$image                = '';
			$finished             = '';
			$date_finished        = '';
			$signed               = '';
			$first_edition        = '';
			$page_yes             = '';
			$post_yes             = '';
			$lendable             = '';
			$copies               = '';
			$page_id              = '';
			$post_id              = '';
			$book_uid             = '';
			$book_id              = '';
			$woocommerce          = '';
			$saleprice            = '';
			$regularprice         = '';
			$stock                = '';
			$length               = '';
			$width                = '';
			$height               = '';
			$weight               = '';
			$sku                  = '';
			$virtual              = '';
			$download             = '';
			$woofile              = '';
			$salebegin            = '';
			$saleend              = '';
			$purchasenote         = '';
			$productcategory      = '';
			$reviews              = '';
			$crosssells           = '';
			$upsells              = '';
			$amazonbuylink        = '';
			$bnbuylink            = '';
			$googlebuylink        = '';
			$itunesbuylink        = '';
			$booksamillionbuylink = '';
			$kobobuylink          = '';

			if ( isset( $_POST['amazonauth'] ) ) {
				$amazonauth = filter_var( wp_unslash( $_POST['amazonauth'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['library'] ) ) {
				$library = filter_var( wp_unslash( $_POST['library'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['useAmazonYes'] ) ) {
				$use_amazon_yes = filter_var( wp_unslash( $_POST['useAmazonYes'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['isbn'] ) ) {
				$isbn = filter_var( wp_unslash( $_POST['isbn'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['title'] ) ) {
				$title = filter_var( wp_unslash( $_POST['title'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['author'] ) ) {
				$author = filter_var( wp_unslash( $_POST['author'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['authorurl'] ) ) {
				$author_url = filter_var( wp_unslash( $_POST['authorurl'] ), FILTER_SANITIZE_URL );
			}

			if ( isset( $_POST['saleurl'] ) ) {
				$sale_url = filter_var( wp_unslash( $_POST['saleurl'] ), FILTER_SANITIZE_URL );
			}

			if ( isset( $_POST['category'] ) ) {
				$category = filter_var( wp_unslash( $_POST['category'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['price'] ) ) {
				$price = filter_var( wp_unslash( $_POST['price'] ), FILTER_SANITIZE_STRING );
			}	

			if ( isset( $_POST['pages'] ) ) {
				$pages = filter_var( wp_unslash( $_POST['pages'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['pubYear'] ) ) {
				$pub_year = filter_var( wp_unslash( $_POST['pubYear'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['publisher'] ) ) {
				$publisher = filter_var( wp_unslash( $_POST['publisher'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['description'] ) ) {
				$description = filter_var( htmlentities( wp_unslash( $_POST['description'] ) ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['subject'] ) ) {
				$subject = filter_var( wp_unslash( $_POST['subject'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['country'] ) ) {
				$country = filter_var( wp_unslash( $_POST['country'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['notes'] ) ) {
				$notes = filter_var( htmlentities( wp_unslash( $_POST['notes'] ) ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['rating'] ) ) {
				$rating = filter_var( wp_unslash( $_POST['rating'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['image'] ) ) {
				$image = filter_var( wp_unslash( $_POST['image'] ), FILTER_SANITIZE_URL );
			}

			if ( isset( $_POST['finished'] ) ) {
				$finished = filter_var( wp_unslash( $_POST['finished'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['datefinished'] ) ) {
				$date_finished = filter_var( wp_unslash( $_POST['datefinished'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['signed'] ) ) {
				$signed = filter_var( wp_unslash( $_POST['signed'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['firstedition'] ) ) {
				$first_edition = filter_var( wp_unslash( $_POST['firstedition'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['pageYes'] ) ) {
				$page_yes = filter_var( wp_unslash( $_POST['pageYes'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['postYes'] ) ) {
				$post_yes = filter_var( wp_unslash( $_POST['postYes'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['lendable'] ) ) {
				$signed = filter_var( wp_unslash( $_POST['lendable'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['copies'] ) ) {
				$copies = filter_var( wp_unslash( $_POST['copies'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['pageId'] ) ) {
				$page_yes = filter_var( wp_unslash( $_POST['pageId'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['postId'] ) ) {
				$post_yes = filter_var( wp_unslash( $_POST['postId'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['bookUid'] ) ) {
				$book_uid = filter_var( wp_unslash( $_POST['bookUid'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['woocommerce'] ) ) {
				$woocommerce = filter_var( wp_unslash( $_POST['woocommerce'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['saleprice'] ) ) {
				$saleprice = filter_var( wp_unslash( $_POST['saleprice'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['regularprice'] ) ) {
				$regularprice = filter_var( wp_unslash( $_POST['regularprice'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['stock'] ) ) {
				$stock = filter_var( wp_unslash( $_POST['stock'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['length'] ) ) {
				$length = filter_var( wp_unslash( $_POST['length'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['width'] ) ) {
				$width = filter_var( wp_unslash( $_POST['width'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['height'] ) ) {
				$height = filter_var( wp_unslash( $_POST['height'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['weight'] ) ) {
				$weight = filter_var( wp_unslash( $_POST['weight'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['sku'] ) ) {
				$sku = filter_var( wp_unslash( $_POST['sku'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['virtual'] ) ) {
				$virtual = filter_var( wp_unslash( $_POST['virtual'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['download'] ) ) {
				$download = filter_var( wp_unslash( $_POST['download'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['woofile'] ) ) {
				$woofile = filter_var( wp_unslash( $_POST['woofile'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['salebegin'] ) ) {
				$salebegin = filter_var( wp_unslash( $_POST['salebegin'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['saleend'] ) ) {
				$saleend = filter_var( wp_unslash( $_POST['saleend'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['purchasenote'] ) ) {
				$purchasenote = filter_var( wp_unslash( $_POST['purchasenote'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['productcategory'] ) ) {
				$productcategory = filter_var( wp_unslash( $_POST['productcategory'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['reviews'] ) ) {
				$reviews = filter_var( wp_unslash( $_POST['reviews'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['crosssells'] ) ) {
				$crosssells = filter_var( wp_unslash( $_POST['crosssells'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['upsells'] ) ) {
				$upsells = filter_var( wp_unslash( $_POST['upsells'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['amazonbuylink'] ) ) {
				$amazonbuylink = filter_var( wp_unslash( $_POST['amazonbuylink'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['bnbuylink'] ) ) {
				$bnbuylink = filter_var( wp_unslash( $_POST['bnbuylink'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['googlebuylink'] ) ) {
				$googlebuylink = filter_var( wp_unslash( $_POST['googlebuylink'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['itunesbuylink'] ) ) {
				$itunesbuylink = filter_var( wp_unslash( $_POST['itunesbuylink'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['booksamillionbuylink'] ) ) {
				$booksamillionbuylink = filter_var( wp_unslash( $_POST['booksamillionbuylink'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['kobobuylink'] ) ) {
				$kobobuylink = filter_var( wp_unslash( $_POST['kobobuylink'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['bookId'] ) ) {
				$book_id = filter_var( wp_unslash( $_POST['bookId'] ), FILTER_SANITIZE_STRING );
			}

			$book_array = array(
				'amazon_auth_yes'      => $amazonauth,
				'library'              => $library,
				'use_amazon_yes'       => $use_amazon_yes,
				'isbn'                 => $isbn,
				'title'                => $title,
				'author'               => $author,
				'author_url'           => $author_url,
				'sale_url'             => $sale_url,
				'category'             => $category,
				'price'                => $price,
				'pages'                => $pages,
				'pub_year'             => $pub_year,
				'publisher'            => $publisher,
				'description'          => $description,
				'subject'              => $subject,
				'country'              => $country,
				'notes'                => $notes,
				'rating'               => $rating,
				'image'                => $image,
				'finished'             => $finished,
				'date_finished'        => $date_finished,
				'signed'               => $signed,
				'first_edition'        => $first_edition,
				'page_yes'             => $page_yes,
				'post_yes'             => $post_yes,
				'lendable'             => $lendable,
				'copies'               => $copies,
				'page_id'              => $page_id,
				'post_id'              => $post_id,
				'book_uid'             => $book_uid,
				'woocommerce'          => $woocommerce,
				'saleprice'            => $saleprice,
				'regularprice'         => $regularprice,
				'stock'                => $stock,
				'length'               => $length,
				'width'                => $width,
				'height'               => $height,
				'weight'               => $weight,
				'sku'                  => $sku,
				'virtual'              => $virtual,
				'download'             => $download,
				'woofile'              => $woofile,
				'salebegin'            => $salebegin,
				'saleend'              => $saleend,
				'purchasenote'         => $purchasenote,
				'productcategory'      => $productcategory,
				'reviews'              => $reviews,
				'crosssells'           => $crosssells,
				'upsells'              => $upsells,
				'amazonbuylink'        => $amazonbuylink,
				'bnbuylink'            => $bnbuylink,
				'googlebuylink'        => $googlebuylink,
				'itunesbuylink'        => $itunesbuylink,
				'booksamillionbuylink' => $booksamillionbuylink,
				'kobobuylink'          => $kobobuylink,
			);

			error_log( 'Here is the Array being sent to Class-wpbooklist-book.php -actual' );
			error_log( print_r( $book_array, true ) );

			require_once CLASS_BOOK_DIR . 'class-wpbooklist-book.php';
			$book_class = new WPBookList_Book( 'edit', $book_array, $book_id );

			$edit_result = $book_class->edit_result;

			// If book was succesfully edited, and return the page/post results.
			if ( 1 === $edit_result ) {
				$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $library WHERE ID = %d", $book_id ) );

				// Get saved page URL.
				$table_name   = $wpdb->prefix . 'wpbooklist_jre_saved_page_post_log';
				$page_results = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE book_uid = %s AND type = 'page'", $row->book_uid ) );
				if ( is_object( $page_results ) ) {
					$page_url = $page_results->post_url;
				} else {
					$page_url = '';
				}

				// Get saved post URL.
				$table_name   = $wpdb->prefix . 'wpbooklist_jre_saved_page_post_log';
				$post_results = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE book_uid = %s AND type = 'post'", $row->book_uid ) );
				if ( is_object( $page_results ) ) {
					$post_url = $post_results->post_url;
				} else {
					$post_url = '';
				}

				echo $edit_result . '--sep--' . $book_id . '--sep--' . $library . '--sep--' . $page_yes . '--sep--' . $post_yes . '--sep--' . $page_url . '--sep--' . $post_url . '--sep--' . $wpdb->prefix . '--sep--' . $book_class->apireport . '--sep--' . wp_json_encode( $book_class->whichapifound ) . '--sep--' . $book_class->apiamazonfailcount . '---sep--' . $book_class->amazon_transient_use;

			} else {
				echo $edit_result;
			}

			wp_die();
		}
		*/

		/**
		 * Callback function for deleting books.
		 */
		public function wpbooklist_delete_book_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_delete_book_action_callback', 'security' );

			if ( isset( $_POST['deleteString'] ) ) {
				$delete_string = filter_var( wp_unslash( $_POST['deleteString'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['bookId'] ) ) {
				$book_id = filter_var( wp_unslash( $_POST['bookId'] ), FILTER_SANITIZE_NUMBER_INT );
			}

			if ( isset( $_POST['library'] ) ) {
				$library = filter_var( wp_unslash( $_POST['library'] ), FILTER_SANITIZE_STRING );
			}

			require_once CLASS_BOOK_DIR . 'class-wpbooklist-book.php';
			$book_class = new WPBookList_Book();
			$delete_result = $book_class->delete_book( $library, $book_id, $delete_string );
			echo $delete_result;
			wp_die();
		}

		/**
		 * Callback function for saving user's API info.
		 */
		public function wpbooklist_user_apis_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_user_apis_action_callback', 'security' );

			if ( isset( $_POST['amazonapipublic'] ) ) {
				$amazonapipublic = filter_var( wp_unslash( $_POST['amazonapipublic'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['amazonapisecret'] ) ) {
				$amazonapisecret = filter_var( wp_unslash( $_POST['amazonapisecret'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['googleapi'] ) ) {
				$googleapi = filter_var( wp_unslash( $_POST['googleapi'] ), FILTER_SANITIZE_STRING );
			}

			$table_name   = $wpdb->prefix . 'wpbooklist_jre_user_options';
			$data         = array(
				'amazonapipublic' => $amazonapipublic,
				'amazonapisecret' => $amazonapisecret,
				'googleapi'       => $googleapi,
			);
			$format       = array( '%s' );
			$where        = array( 'ID' => ( 1 ) );
			$where_format = array( '%d' );
			$result       = $wpdb->update( $table_name, $data, $where, $format, $where_format );

			$this->transients->delete_all_wpbl_transients();

			echo $result;
			wp_die();
		}

		/**
		 * Callback function for uploading a new StylePak after purchase.
		 */
		public function wpbooklist_upload_new_stylepak_action_callback() {

			global $wpdb;
			check_ajax_referer( 'wpbooklist_upload_new_stylepak_action_callback', 'security' );

			// Create file structure in the uploads dir.
			$mkdir1 = null;
			if ( ! file_exists( UPLOADS_BASE_DIR . 'wpbooklist' ) ) {
				$mkdir1 = mkdir( UPLOADS_BASE_DIR . 'wpbooklist', 0777, true );
			}

			// Create file structure in the uploads dir.
			$mkdir2 = null;
			if ( ! file_exists( LIBRARY_STYLEPAKS_UPLOAD_DIR ) ) {
				$mkdir2 = mkdir( LIBRARY_STYLEPAKS_UPLOAD_DIR, 0777, true );
			}

			$move_result = move_uploaded_file( $_FILES['my_uploaded_file']['tmp_name'], LIBRARY_STYLEPAKS_UPLOAD_DIR . "{$_FILES['my_uploaded_file']['name']}" );

			// Unzip the file if it's zipped.
			if (strpos( $_FILES['my_uploaded_file']['name'], '.zip') !== false ){
				$zip = new ZipArchive;
				$res = $zip->open(LIBRARY_STYLEPAKS_UPLOAD_DIR.$_FILES['my_uploaded_file']['name'] );
				if ( $res === TRUE) {
					$zip->extractTo(LIBRARY_STYLEPAKS_UPLOAD_DIR);
					$zip->close();
					unlink(LIBRARY_STYLEPAKS_UPLOAD_DIR.$_FILES['my_uploaded_file']['name'] );
				}
			}

			echo $mkdir1 . 'sep' . $mkdir2 .'sep' . $move_result;
			wp_die();
		}


		/**
		 * Callback function for assigning a StylePak to a library.
		 */
		public function wpbooklist_assign_stylepak_action_callback() {

			global $wpdb;
			check_ajax_referer( 'wpbooklist_assign_stylepak_action_callback', 'security' );

			if ( isset( $_POST['stylepak'] ) ) {
				$stylepak = filter_var( wp_unslash( $_POST['stylepak'] ), FILTER_SANITIZE_STRING );
			}

			if ( isset( $_POST['library'] ) ) {
				$library = filter_var( wp_unslash( $_POST['library'] ), FILTER_SANITIZE_STRING );
			}

			$stylepak = str_replace( '.css', '', $stylepak );
			$stylepak = str_replace( '.zip', '', $stylepak );

			// Build table name to store StylePak in.
			if ( false !== strpos( $library, 'wpbooklist_jre_saved_book_log' ) ) {
				$table_name   = $wpdb->prefix . 'wpbooklist_jre_user_options';
				$data         = array(
					'stylepak' => $stylepak,
				);
				$format       = array( '%s' );
				$where        = array( 'ID' => 1 );
				$where_format = array( '%d' );
				echo $wpdb->update( $table_name, $data, $where, $format, $where_format );

				$this->transients->delete_all_wpbl_transients();

			} else {
				$table_name   = $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names';
				$library      = substr( $library, strrpos( $library, '_' ) + 1 );
				$data         = array(
					'stylepak' => $stylepak,
				);
				$format       = array( '%s' );
				$where        = array( 'user_table_name' => $library );
				$where_format = array( '%s' );
				echo $stylepak . ' ' . $library;
				echo $wpdb->update( $table_name, $data, $where, $format, $where_format );

				$this->transients->delete_all_wpbl_transients();
			}

			wp_die();
		}

		/**
		 *  Callback function for uploading a new Post Template after purchase.
		 */
		public function wpbooklist_upload_new_post_template_action_callback() {

			global $wpdb;
			check_ajax_referer( 'wpbooklist_upload_new_post_template_action_callback', 'security' );

			// Create file structure in the uploads dir.
			$mkdir1 = null;
			if ( ! file_exists( UPLOADS_BASE_DIR . 'wpbooklist' ) ) {
				$mkdir1 = mkdir( UPLOADS_BASE_DIR . 'wpbooklist', 0777, true );
			}

			// Create file structure in the uploads dir.
			$mkdir2 = null;
			if ( ! file_exists( POST_TEMPLATES_UPLOAD_DIR ) ) {
				$mkdir2 = mkdir( POST_TEMPLATES_UPLOAD_DIR, 0777, true );
			}

			$move_result = move_uploaded_file( $_FILES['my_uploaded_file']['tmp_name'], POST_TEMPLATES_UPLOAD_DIR . "{$_FILES['my_uploaded_file']['name']}" );

			// Unzip the file if it's zipped.
			if (strpos( $_FILES['my_uploaded_file']['name'], '.zip') !== false ){
				$zip = new ZipArchive;
				$res = $zip->open(POST_TEMPLATES_UPLOAD_DIR.$_FILES['my_uploaded_file']['name'] );
				if ( $res === TRUE) {
					$zip->extractTo(POST_TEMPLATES_UPLOAD_DIR);
					$zip->close();
					unlink(POST_TEMPLATES_UPLOAD_DIR.$_FILES['my_uploaded_file']['name'] );
				}
			}

				echo $mkdir1.'sep' . $mkdir2.'sep' . $move_result;
			wp_die();
		}

		// Callback function for uploading a new Post Template after purchase
		public function wpbooklist_assign_post_template_action_callback() {

			global $wpdb;
			check_ajax_referer( 'wpbooklist_assign_post_template_action_callback', 'security' );

			// For assigning a Template to a Library
				$template = filter_var( wp_unslash( $_POST["template"] ), FILTER_SANITIZE_STRING );

				$template = str_replace( '.php', '', $template );
				$template = str_replace( '.zip', '', $template );

				$table_name = $wpdb->prefix . 'wpbooklist_jre_user_options';

				$data = array(
					'activeposttemplate' => $template,
					);
					$format = array( '%s' );   
					$where = array( 'ID' => 1 );
					$where_format = array( '%d' );
					echo $wpdb->update( $table_name, $data, $where, $format, $where_format );

					$this->transients->delete_all_wpbl_transients();

			wp_die();
		}

		

		// Callback function for uploading a new page Template after purchase
		public function wpbooklist_upload_new_page_template_action_callback() {

			global $wpdb;
			check_ajax_referer( 'wpbooklist_upload_new_page_template_action_callback', 'security' );


				// Create file structure in the uploads dir 
				$mkdir1 = null;
				if (!file_exists(UPLOADS_BASE_DIR."wpbooklist")) {
					// TODO: create log file entry 
					$mkdir1 = mkdir(UPLOADS_BASE_DIR."wpbooklist", 0777, true );
				}

				// Create file structure in the uploads dir 
				$mkdir2 = null;
				if (!file_exists(PAGE_TEMPLATES_UPLOAD_DIR)) {
					// TODO: create log file entry 
					$mkdir2 = mkdir(PAGE_TEMPLATES_UPLOAD_DIR, 0777, true );
				}

				// TODO: create log file entry 
				$move_result = move_uploaded_file( $_FILES['my_uploaded_file']['tmp_name'], PAGE_TEMPLATES_UPLOAD_DIR."{$_FILES['my_uploaded_file'] ['name']}" );

				// Unzip the file if it's zipped
				if (strpos( $_FILES['my_uploaded_file']['name'], '.zip') !== false ){
					$zip = new ZipArchive;
					$res = $zip->open(PAGE_TEMPLATES_UPLOAD_DIR.$_FILES['my_uploaded_file']['name'] );
					if ( $res === TRUE) {
						$zip->extractTo(PAGE_TEMPLATES_UPLOAD_DIR);
						$zip->close();
						unlink(PAGE_TEMPLATES_UPLOAD_DIR.$_FILES['my_uploaded_file']['name'] );
					}
				}

				echo $mkdir1.'sep' . $mkdir2.'sep' . $move_result;
			wp_die();
		}

		// Callback function for uploading a new page Template after purchase
		public function wpbooklist_assign_page_template_action_callback() {

			global $wpdb;
			check_ajax_referer( 'wpbooklist_assign_page_template_action_callback', 'security' );

			// For assigning a page_template.
			$template = filter_var( wp_unslash( $_POST["template"] ), FILTER_SANITIZE_STRING );

			$template = str_replace( '.php', '', $template );
			$template = str_replace( '.zip', '', $template );

			$table_name = $wpdb->prefix . 'wpbooklist_jre_user_options';

			$data = array(
				'activepagetemplate' => $template,
			);
			$format = array( '%s' );
			$where = array( 'ID' => 1 );
			$where_format = array( '%d' );
			$wpdb->update( $table_name, $data, $where, $format, $where_format );

			$this->transients->delete_all_wpbl_transients();

			wp_die();
		}

		// Callback function for creating a DB backup of a Library
		public function wpbooklist_create_db_library_backup_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_create_db_library_backup_action_callback', 'security' );
			$library = filter_var( wp_unslash( $_POST['library'] ), FILTER_SANITIZE_STRING );

			require_once CLASS_BACKUP_DIR . 'class-wpbooklist-backup.php';
			$backup_class = new WPBookList_Backup( 'library_database_backup', $library );
			echo $backup_class->create_backup_result; 
			wp_die();
		}

		// Callback function for restoring a backup of a Library.
		public function wpbooklist_restore_db_library_backup_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_restore_db_library_backup_action_callback', 'security' );
			$table = filter_var( wp_unslash( $_POST['table'] ), FILTER_SANITIZE_STRING );
			$backup = filter_var( wp_unslash( $_POST['backup'] ), FILTER_SANITIZE_STRING );

			require_once CLASS_BACKUP_DIR . 'class-wpbooklist-backup.php';
			$backup_class = new WPBookList_Backup( 'library_database_restore', $table, $backup);

			wp_die();
		}

		// Callback function for creating a .csv file of ISBN/ASIN numbers
		public function wpbooklist_create_csv_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_create_csv_action_callback', 'security' );
			$table = filter_var( wp_unslash( $_POST['table'] ), FILTER_SANITIZE_STRING );
			
			require_once CLASS_BACKUP_DIR . 'class-wpbooklist-backup.php';
			$backup_class = new WPBookList_Backup( 'create_csv_file', $table );

			echo $backup_class->create_csv_result;
			wp_die();
		}

		// Callback function for setting the Amazon Localization.
		public function wpbooklist_amazon_localization_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_amazon_localization_action_callback', 'security' );
			$country = filter_var( wp_unslash( $_POST['country'] ), FILTER_SANITIZE_STRING );
			$table_name = $wpdb->prefix . 'wpbooklist_jre_user_options';

			$data = array(
					'amazoncountryinfo' => $country
			);
			$format = array( '%s' );  
			$where = array( 'ID' => 1 );
			$where_format = array( '%d' );
			$wpdb->update( $table_name, $data, $where, $format, $where_format );

			$this->transients->delete_all_wpbl_transients();

			wp_die();
		}

	
		// Callback function for deleting all books in library.
		public function wpbooklist_delete_all_books_in_library_action_callback() {

			check_ajax_referer( 'wpbooklist_delete_all_books_in_library_action_callback', 'security' );
			require_once CLASS_BOOK_DIR . 'class-wpbooklist-book.php';
			$book_class = new WPBookList_Book;

			$library = filter_var( wp_unslash( $_POST['library'] ), FILTER_SANITIZE_STRING );
			$delete_result = $book_class->empty_table( $library );

			wp_die();
		}

		// Callback function for deleting all books, pages, and posts in library.
		public function wpbooklist_delete_all_books_pages_and_posts_action_callback() {

			check_ajax_referer( 'wpbooklist_delete_all_books_pages_and_posts_action_callback', 'security' );
			require_once CLASS_BOOK_DIR . 'class-wpbooklist-book.php';
			$book_class = new WPBookList_Book;

			$library = filter_var( wp_unslash( $_POST['library'] ), FILTER_SANITIZE_STRING );
			$delete_result = $book_class->empty_everything( $library );

			wp_die();
		}

		// Callback function for deleting all checked books.
		public function wpbooklist_delete_all_checked_books_action_callback() {
			require_once CLASS_BOOK_DIR . 'class-wpbooklist-book.php';
			$book_class = new WPBookList_Book;
			check_ajax_referer( 'wpbooklist_delete_all_checked_books_action_callback', 'security' );

			$library = filter_var( wp_unslash( $_POST['library'] ), FILTER_SANITIZE_STRING );
			$delete_string = filter_var( wp_unslash( $_POST['deleteString'] ), FILTER_SANITIZE_STRING );
			$book_id = filter_var( wp_unslash( $_POST['bookId'] ), FILTER_SANITIZE_STRING );
			$book_id = ltrim( $book_id, 'sep' );

			// Creating array of IDs to delete.
			$delete_array = explode( 'sep', $book_id);

			// Creating array of Page/Post IDs to delete
			if ( $delete_string != null && $delete_string != ''){
				$delete_string = ltrim( $delete_string, 'sep' );
				$delete_page_post_array = explode( 'sep', $delete_string );
			}	


			// Required to delete the correct book, update the IDs, then delete the next correct book
			$delete_array = array_reverse( $delete_array );

			// The loop that will send each book ID and Page/Post ID to class-wpbooklist-book.php to be deleted.
			foreach( $delete_array as $key=>$delete ){

				// Send page/post IDs to delete to class-wpbooklist-book.php if they exist, otherwise don't send
				if ( $delete_string != null && $delete_string != ''){
					$delete_result = $book_class->delete_book( $library, $delete, $delete_page_post_array[$key]);
				} else {
					$delete_result = $book_class->delete_book( $library, $delete, null);
				}	
			}

			

			wp_die();
		}

		// Callback function for dismissing the admin notice forever.
		public function wpbooklist_jre_dismiss_prem_notice_forever_action_callback() {
			
			global $wpdb; // this is how you get access to the database
			check_ajax_referer( 'wpbooklist_jre_dismiss_prem_notice_forever_action', 'security' );

			$id = filter_var( wp_unslash( $_POST['id'] ), FILTER_SANITIZE_STRING );
		 
		 	// Handling the dismiss of the general admin message
			if ( $id == 'wpbooklist-my-notice-dismiss-forever-general'){
				$table_name = $wpdb->prefix . 'wpbooklist_jre_user_options';

				$data = array(
					'admindismiss' => 0
				);
				$where = array( 'ID' => 1 );
				$format = array( '%d' );  
				$where_format = array( '%d' );
				echo $wpdb->update( $table_name, $data, $where, $format, $where_format );

				$this->transients->delete_all_wpbl_transients();

				wp_die();
			}

			// Handling the dismiss of the StoryTime admin message
			if ( $id == 'wpbooklist-my-notice-dismiss-forever-storytime'){
				$table_name = $wpdb->prefix . 'wpbooklist_jre_storytime_stories_settings';

				$data = array(
					'notifydismiss' => 0
				);
				$where = array( 'ID' => 1 );
				$format = array( '%d' );  
				$where_format = array( '%d' );
				echo $wpdb->update( $table_name, $data, $where, $format, $where_format );

				$this->transients->delete_all_wpbl_transients();

				wp_die();
			}
		}

		// Callback function for re-ordering books on the 'Edit & Delete Books' tab.
		public function wpbooklist_reorder_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_reorder_action_callback', 'security' );
			$table = filter_var( wp_unslash( $_POST['table'] ), FILTER_SANITIZE_STRING );
			$idarray = stripslashes( $_POST['idarray'] );
			$idarray = json_decode( $idarray );

			// Dropping primary key in database to alter the IDs and the AUTO_INCREMENT value
			$wpdb->query( "ALTER TABLE $table MODIFY ID BIGINT(190) NOT NULL" );

			$wpdb->query( "ALTER TABLE $table DROP PRIMARY KEY" );

			foreach ( $idarray as $key => $value ) {
				$data = array(
						'ID' => $key+1
				);

				$format = array( '%d' );  
				$where = array( 'book_uid' => $value );
				$where_format = array( '%s' );
				$wpdb->update( $table, $data, $where, $format, $where_format );

			}

			// Adding primary key back to database 
			echo $wpdb->query( "ALTER TABLE $table ADD PRIMARY KEY (`ID`)" ); 

			// Adjusting ID values of remaining entries in database
			$my_query = $wpdb->get_results( "SELECT * FROM $table" );
			$title_count = $wpdb->num_rows;   

			$wpdb->query( "ALTER TABLE $table MODIFY ID BIGINT(190) AUTO_INCREMENT" );

			// Setting the AUTO_INCREMENT value based on number of remaining entries
			$title_count++;
			$wpdb->query( $wpdb->prepare( "ALTER TABLE $table AUTO_INCREMENT = %d", $title_count ) );

			$this->transients->delete_all_wpbl_transients();

			wp_die();
		}

		// Callback function for the exit survey triggered when user deactivates WPBookList.
		public function wpbooklist_exit_results_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_exit_results_action_callback', 'security' );
			$reason1 = filter_var( wp_unslash( $_POST['reason1'] ), FILTER_SANITIZE_STRING );
			$reason2 = filter_var( wp_unslash( $_POST['reason2'] ), FILTER_SANITIZE_STRING );
			$reason3 = filter_var( wp_unslash( $_POST['reason3'] ), FILTER_SANITIZE_STRING );
			$reason4 = filter_var( wp_unslash( $_POST['reason4'] ), FILTER_SANITIZE_STRING );
			$reason5 = filter_var( wp_unslash( $_POST['reason5'] ), FILTER_SANITIZE_STRING );
			$reason6 = filter_var( wp_unslash( $_POST['reason6'] ), FILTER_SANITIZE_STRING );
			$reason7 = filter_var( wp_unslash( $_POST['reason7'] ), FILTER_SANITIZE_STRING );
			$reason8 = filter_var( wp_unslash( $_POST['reason8'] ), FILTER_SANITIZE_STRING );
			$reason9 = filter_var( wp_unslash( $_POST['reason9'] ), FILTER_SANITIZE_STRING );
			$id = filter_var( wp_unslash( $_POST['id'] ), FILTER_SANITIZE_STRING );
			$reasonOther = filter_var( wp_unslash( $_POST['reasonOther'] ), FILTER_SANITIZE_STRING );
			$featureSuggestion = filter_var( wp_unslash( $_POST['featureSuggestion'] ), FILTER_SANITIZE_STRING );
			$reasonEmail = filter_var( wp_unslash( $_POST['reasonEmail'] ), FILTER_SANITIZE_EMAIL);

			$message = $reason1.' ' . $reason2.' ' . $reason3.' ' . $reason4.' ' . $reason5.' ' . $reason6.' ' . $reason7.' ' . $reason8.' ' . $reason9.' ' . $featureSuggestion.' ' . $reasonOther.' ' . $reasonEmail;

			if ( $id == 'wpbooklist-modal-submit'){
				wp_mail( 'jake@jakerevans.com', 'WPBookList Exit Survey', $message );

				if ( $reasonEmail != ''){
					$autoresponseMessage = 'Thanks for trying out WPBookList and providing valuable feedback that will help make WPBookList even better! I\'ll review your feedback and get back with you ASAP.  -Jake' ;
					wp_mail( $reasonEmail, 'WPBookList Deactivation Survey', $autoresponseMessage );
				}
			}

			deactivate_plugins( 'wpbooklist/wpbooklist.php' );
			wp_die();
		}

		// Callback function for retrieving the WPBookList StoryTime Stories from the server when the 'Select a Category' drop-down changes.
		public function wpbooklist_storytime_select_category_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_storytime_select_category_action_callback', 'security' );
			$category = filter_var( wp_unslash( $_POST['category'] ), FILTER_SANITIZE_STRING );

			require_once CLASS_STORYTIME_DIR . 'class-wpbooklist-storytime.php';
				$storytime_class = new WPBookList_Storytime( 'categorychange', $category );


			echo $storytime_class->category_change_output;
			wp_die();
		}

		// Callback function for retreiving a WPBookList StoryTime Story from the server, once the user has selected one in the reader
		public function wpbooklist_storytime_get_story_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_storytime_get_story_action_callback', 'security' );
			$dataId = filter_var( wp_unslash( $_POST['dataId'] ), FILTER_SANITIZE_NUMBER_INT);
			
			require_once CLASS_STORYTIME_DIR . 'class-wpbooklist-storytime.php';
				$storytime_class = new WPBookList_Storytime( 'getcontent', null, $dataId);

				echo json_encode( $storytime_class->stories_db_data);

			wp_die();
		}

		// Callback function for expanding the 'Browse Stories' section again once a Story has already been selected
		public function wpbooklist_storytime_expand_browse_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_storytime_expand_browse_action_callback', 'security' );

			require_once CLASS_STORYTIME_DIR . 'class-wpbooklist-storytime.php';
				$storytime_class = new WPBookList_Storytime( 'categorychange', 'Recent Additions' );


			echo $storytime_class->category_change_output;
			wp_die();
		}

		

		// Callback function for saving the StoryTime Settings
		public function wpbooklist_storytime_save_settings_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_storytime_save_settings_action_callback', 'security' );
			$createpost = filter_var( wp_unslash( $_POST['input1'] ), FILTER_SANITIZE_STRING );
			$createpage = filter_var( wp_unslash( $_POST['input2'] ), FILTER_SANITIZE_STRING );
			$deletedefault = filter_var( wp_unslash( $_POST['input3'] ), FILTER_SANITIZE_STRING );
			$newnotify = filter_var( wp_unslash( $_POST['input4'] ), FILTER_SANITIZE_STRING );
			$getstories = filter_var( wp_unslash( $_POST['input5'] ), FILTER_SANITIZE_STRING );
			$storypersist = filter_var( wp_unslash( $_POST['input6'] ), FILTER_SANITIZE_NUMBER_INT);

			if ( $createpost == 'true'){
				$createpost = 1;
			} else {
				$createpost = 0;
			}

			if ( $createpage == 'true'){
				$createpage = 1;
			} else {
				$createpage = 0;
			}

			if ( $deletedefault == 'true'){
				$deletedefault = 1;

				// Delete default data
				$stories_table = $wpdb->prefix . 'wpbooklist_jre_storytime_stories';
				$query_for_default_data = $wpdb->get_results( "SELECT * FROM $stories_table" );

				// If the default data still exists (based on the fact that war of the worlds should be first in db), proceed, otherwise do nothing.
				if ( $query_for_default_data[0]->title == 'Sample Chapter - The War of the Worlds'){

					$wpdb->query( "DELETE FROM $stories_table WHERE providername = 'H. G. Wells' AND title = 'Sample Chapter - The War of the Worlds'" );

					$wpdb->query( "DELETE FROM $stories_table WHERE providername = 'Jane Austen' AND title = 'Sample Chapter - Pride and Predjudice'" );

					$wpdb->query( "DELETE FROM $stories_table WHERE providername = 'Matthew Dawes' AND title = 'Sample Chapter - Nightfall'" );

					$wpdb->query( "DELETE FROM $stories_table WHERE providername = 'Maine Authors Publishing' AND title = 'Interview - Maine Authors Publishing'" );

					$wpdb->query( "DELETE FROM $stories_table WHERE providername = 'Missouri Writers’ Guild' AND title = 'Article - Missouri Writers’ Guild'" );

					$wpdb->query( "DELETE FROM $stories_table WHERE providername = 'Benjamin Franklin' AND title = 'Autobiography of Benjamin Franklin'" );

					$wpdb->query( "DELETE FROM $stories_table WHERE providername = 'Zac Wilson' AND title = 'Sample Chapter - Morningland'" );

					$wpdb->query( "DELETE FROM $stories_table WHERE providername = 'David Luddington' AND title = 'Author Showcase - David Luddington'" );

					$wpdb->query( "DELETE FROM $stories_table WHERE providername = 'Bram Stoker' AND title = 'Sample Chapter - Dracula'" );

					$wpdb->query( "DELETE FROM $stories_table WHERE providername = 'Brendan T. Beery' AND title = 'Author Showcase - Brendan T. Beery'" );

					// Dropping primary key in database to alter the IDs and the AUTO_INCREMENT value
					$wpdb->query( "ALTER TABLE $stories_table MODIFY ID bigint(190)" );
					$wpdb->query( "ALTER TABLE $stories_table DROP PRIMARY KEY" );

					// Adjusting ID values of remaining entries in database
					$my_query = $wpdb->get_results( "SELECT * FROM $stories_table" );
					$title_count = $wpdb->num_rows;
					$book_id = 10; // Hard-coded based on number of default rows included with WPBookList
					for ( $x = 1; $x <= $title_count; $x++) {
						$data = array(
								'ID' => $x
						);
						$format = array( '%d' );  
						$where = array( 'ID' => $book_id);
						$where_format = array( '%d' );
						$wpdb->update( $stories_table, $data, $where, $format, $where_format );
						$book_id++; 
					}  

					// Adding primary key back to database 
					$wpdb->query( "ALTER TABLE $stories_table ADD PRIMARY KEY (`ID`)" );    
					$wpdb->query( "ALTER TABLE $stories_table MODIFY ID bigint(190) AUTO_INCREMENT" );

					// Setting the AUTO_INCREMENT value based on number of remaining entries
					$title_count++;
					$wpdb->query( $wpdb->prepare( "ALTER TABLE $stories_table AUTO_INCREMENT = %d", $title_count ) );
				}

			} else {
				$deletedefault = 0;
			}

			if ( $newnotify == 'true'){
				$newnotify = 1;
			} else {
				$newnotify = 0;
			}

			if ( $getstories == 'true'){
				$getstories = 1;
			} else {
				$getstories = 0;
			}

			if ( $storypersist == '' || $storypersist == null || $storypersist == 0){
				$storypersist = null;
			}

			// Update StoryTime settings table
			$table_name = $wpdb->prefix . 'wpbooklist_jre_storytime_stories_settings';
			$data = array(
					'createpost' => $createpost,
				'createpage' => $createpage,
				'deletedefault' => $deletedefault,
				'newnotify' => $newnotify,
				'getstories' => $getstories,
				'storypersist' => $storypersist,
			  );
			  $format = array( '%d','%d','%d','%d','%d','%d' ); 
			  $where = array( 'ID' => 1 );
			  $where_format = array( '%d' );
			  $wpdb->update( $table_name, $data, $where, $format, $where_format );

			  $this->transients->delete_all_wpbl_transients();

			wp_die();
		}

		// Callback function for deleting a Story.
		public function wpbooklist_delete_story_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_delete_story_action_callback', 'security' );
			$id = filter_var( wp_unslash( $_POST['dataId'] ), FILTER_SANITIZE_NUMBER_INT);

			$stories_table = $wpdb->prefix . 'wpbooklist_jre_storytime_stories';
			$query_for_default_data = $wpdb->get_results( "SELECT * FROM $stories_table" );

			$wpdb->query( "DELETE FROM $stories_table WHERE ID = $id" );

			// Dropping primary key in database to alter the IDs and the AUTO_INCREMENT value
			$wpdb->query( "ALTER TABLE $stories_table MODIFY ID bigint(190)" );
			$wpdb->query( "ALTER TABLE $stories_table DROP PRIMARY KEY" );

			// Adjusting ID values of remaining entries in database
			$my_query = $wpdb->get_results( "SELECT * FROM $stories_table" );
			$title_count = $wpdb->num_rows;
			for ( $x = $id; $x <= $title_count; $x++) {
				$data = array(
						'ID' => $id
				);
				$format = array( '%d' ); 
				$id++;  
				$where = array( 'ID' => ( $id) );
				$where_format = array( '%d' );
				$wpdb->update( $stories_table, $data, $where, $format, $where_format );
			} 

			// Adding primary key back to database.
			$wpdb->query( "ALTER TABLE $stories_table ADD PRIMARY KEY (`ID`)" );    
			$wpdb->query( "ALTER TABLE $stories_table MODIFY ID bigint(190) AUTO_INCREMENT" );

			// Setting the AUTO_INCREMENT value based on number of remaining entries
			$title_count++;
			echo $wpdb->query( $wpdb->prepare( "ALTER TABLE $stories_table AUTO_INCREMENT = %d", $title_count ) );

			$this->transients->delete_all_wpbl_transients();

			wp_die();
		}


		// Makes a call to get every single book saved on website to seed the Book form for Autocomplete stuff.
		public function wpbooklist_seed_book_form_autocomplete_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_seed_book_form_autocomplete_action_callback', 'security' );

			// Get all books from default Library, and push into our final array to return to the javascript.
			$final_total_array  = array();
			$default_table_name = $wpdb->prefix . 'wpbooklist_jre_saved_book_log';
			$default_results = $wpdb->get_results( "SELECT * FROM $default_table_name" );
			array_push( $final_total_array, $default_results );

			// Get all books from all user-created libraries.
			$dynamic_table_name  = $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names';
			$dynamic_name_db_row = $wpdb->get_results( "SELECT * FROM $dynamic_table_name" );

			foreach ( $dynamic_name_db_row as $db ) {
				if ( ( '' !== $db->user_table_name ) || ( null !== $db->user_table_name ) ) {

					$user_created_lib_name = $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name;
					$dynamic_results = $wpdb->get_results( "SELECT * FROM $user_created_lib_name" );
					array_push( $final_total_array, $dynamic_results );
				}
			}

			echo json_encode( $final_total_array );

			wp_die();
		}

		// Function to populate the Library and Book View Display Options checkboxes.
		public function wpbooklist_get_library_view_display_options_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_get_library_view_display_options_action_callback', 'security' );

			if ( isset( $_POST['library'] ) ) {
				$library = filter_var( wp_unslash( $_POST['library'] ), FILTER_SANITIZE_STRING );

			}

			if ( false === stripos( $library, 'wpbooklist_jre_saved_book_log' ) ) {
				$library = filter_var( wp_unslash( $_POST['library'] ), FILTER_SANITIZE_STRING );
				$library = explode( '_wpbooklist_jre_', $library );
				$library = $wpdb->prefix . 'wpbooklist_jre_settings_' . $library[1];
			} else {
				$library = $wpdb->prefix . 'wpbooklist_jre_user_options';
			}

			// Now get the Display Options.
			global $wpdb;
			$this->user_options = $wpdb->get_row( 'SELECT * FROM ' . $library );
			wp_die( wp_json_encode( $this->user_options ) );
		}

		// Function to populate the Post View Display Options checkboxes.
		public function wpbooklist_get_post_display_options_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_get_post_display_options_action_callback', 'security' );

			// Now get the Display Options.
			global $wpdb;
			$this->post_options = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_post_options' );
			wp_die( wp_json_encode( $this->post_options ) );
		}

		// Function to populate the page View Display Options checkboxes.
		public function wpbooklist_get_page_display_options_action_callback() {
			global $wpdb;
			check_ajax_referer( 'wpbooklist_get_page_display_options_action_callback', 'security' );

			// Now get the Display Options.
			global $wpdb;
			$this->page_options = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_page_options' );
			wp_die( wp_json_encode( $this->page_options ) );
		}
	}
endif;
