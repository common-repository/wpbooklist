<?php
/**
 * Class WPBookList_Book Class - class-wpbooklist-book.php
 *
 * @author   Jake Evans
 * @category Books
 * @package  Includes/Classes/Book
 * @version  6.1.5.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Book', false ) ) :

	/**
	 * WPBookList_Book Class.
	 */
	class WPBookList_Book {

		// Class varbiables that map to database fields in the 'saved_book_log' and Dynamic tables.
		public $additionalimage1;
		public $additionalimage2;
		public $amazon_detail_page;
		public $appleibookslink;
		public $asin;
		public $author2;
		public $author3;
		public $author;
		public $author_url;
		public $sale_url;
		public $backcover;
		public $bam_link;
		public $bn_link;
		public $book_uid;
		public $callnumber;
		public $category;
		public $copies;
		public $copieschecked;
		public $country;
		public $currentlendemail;
		public $currentlendname;
		public $date_finished;
		public $description;
		public $edition;
		public $finalauthorfirstnames2;
		public $finalauthorfirstnames3;
		public $finalauthorfirstnames;
		public $finalauthorlastnames2;
		public $finalauthorlastnames3;
		public $finalauthorlastnames;
		public $finished;
		public $first_edition;
		public $format;
		public $genres;
		public $goodreadslink;
		public $google_preview;
		public $illustrator;
		public $image;
		public $lowestusedprice;
		public $isbn13;
		public $isbn;
		public $itunes_page;
		public $keywords;
		public $kobo_link;
		public $language;
		public $lendable;
		public $lendedon;
		public $lendstatus;
		public $notes;
		public $numberinseries;
		public $originalpubyear;
		public $originaltitle;
		public $othereditions;
		public $outofprint;
		public $page_yes;
		public $pages;
		public $post_yes;
		public $price;
		public $pub_year;
		public $publisher;
		public $rating;
		public $review_iframe;
		public $series;
		public $shortdescription;
		public $signed;
		public $similar_books;
		public $similar_products;
		public $similarbooks;
		public $subgenre;
		public $subject;
		public $title;
		public $woocommerce;
		public $ebook;

		// Variables that pertain to the Storefront Extension / WooCommerce Specifics.
		public $saleprice;
		public $regularprice;
		public $stock;
		public $length;
		public $width;
		public $height;
		public $weight;
		public $sku;
		public $virtual;
		public $download;
		public $salebegin;
		public $saleend;
		public $purchasenote;
		public $productcategory;
		public $reviews;
		public $upsells;
		public $crosssells;
		public $defaultprice;

		// Class variables that pertain to ceratin actions taken in thsi class - results of DB actions, API call results, etc.
		public $action = '';
		public $add_result;
		public $edit_result = null;
		public $delete_result;
		public $retrieved_book;
		public $options_results;
		public $woofile;
		public $wooid;
		public $book_page;
		public $page_id;
		public $post_id;
		public $amazonbuylink;
		public $bnbuylink;
		public $googlebuylink;
		public $itunesbuylink;
		public $booksamillionbuylink;
		public $id;
		public $go_amazon;
		public $library;
		public $apireport          = '';
		public $amazonauth = null;
		public $amazonapiresult    = '';
		public $googleapiresult    = '';
		public $itunesapiresult    = '';
		public $openlibapiresult   = '';
		public $apiamazonfailcount = 0;
		public $apigoodreadsfailcount = 0;
		public $rerun_amazon_flag          = true;
		public $whichapifound              = array();
		public $amazon_array               = array();
		public $gather_amazon_attempt_with = 'isbn';
		public $gather_goodreads_attempt_with = 'isbn';
		public $db_insert_array            = array();

		/** Class Constructor - Simply calls the Translations
		 *
		 *  @param string $action - The string that will determine what functions run.
		 *  @param array  $book_array - The array that holds all the book info.
		 *  @param int    $id - The book's id.
		 */
		public function __construct( $action = null, $book_array = null, $id = null ) {

			global $wpdb;

			$this->book_array = $book_array;
			$this->action = $action;

			// Setting up default keys/values for the $whichapifound array.
			$this->whichapifound['title']            = '';
			$this->whichapifound['image']            = '';
			$this->whichapifound['author']           = '';
			$this->whichapifound['pages']            = '';
			$this->whichapifound['pub_year']         = '';
			$this->whichapifound['publisher']        = '';
			$this->whichapifound['description']      = '';
			$this->whichapifound['amazondetailpage'] = '';
			$this->whichapifound['review_iframe']    = '';
			$this->whichapifound['similar_products'] = '';
			$this->whichapifound['category']         = '';
			$this->whichapifound['google_preview']   = '';
			$this->whichapifound['itunes_page']      = '';

			// Require the Transients file.
			require_once CLASS_TRANSIENTS_DIR . 'class-wpbooklist-transients.php';
			$this->transients = new WPBookList_Transients();

			// Get the user options row for the class.
			$table_name_options = $wpdb->prefix . 'wpbooklist_jre_user_options';
			$transient_name     = 'wpbl_' . md5( 'SELECT * FROM ' . $table_name_options );
			$transient_exists   = $this->transients->existing_transient_check( $transient_name );
			if ( $transient_exists ) {
				$this->options_results = $transient_exists;
			} else {
				$query                 = 'SELECT * FROM ' . $table_name_options;
				$this->options_results = $this->transients->create_transient( $transient_name, 'wpdb->get_row', $query, MONTH_IN_SECONDS );
			}

			if ( null !== $book_array ) {

				// Setting up $book_array values, wrapped in isset() to prevent php error_log notices.
				if ( isset( $book_array['additionalimage1'] ) ) {
					$this->additionalimage1 = $book_array['additionalimage1'];
				}

				if ( isset( $book_array['additionalimage2'] ) ) {
					$this->additionalimage2 = $book_array['additionalimage2'];
				}

				if ( isset( $book_array['amazonauth'] ) ) {
					$this->amazonauth = $book_array['amazonauth'];
				}

				if ( isset( $book_array['amazon_detail_page'] ) ) {
					$this->amazon_detail_page = $book_array['amazon_detail_page'];
				}

				if ( isset( $book_array['appleibookslink'] ) ) {
					$this->appleibookslink = $book_array['appleibookslink'];
				}

				if ( isset( $book_array['asin'] ) ) {
					$this->asin = $book_array['asin'];
				}

				if ( isset( $book_array['author'] ) ) {
					$this->author = $book_array['author'];
				}

				if ( isset( $book_array['author2'] ) ) {
					$this->author2 = $book_array['author2'];
				}

				if ( isset( $book_array['author3'] ) ) {
					$this->author3 = $book_array['author3'];
				}

				if ( isset( $book_array['author_url'] ) ) {
					$this->author_url = $book_array['author_url'];
				}

				if ( isset( $book_array['sale_url'] ) ) {
					$this->sale_url = $book_array['sale_url'];
				}

				if ( isset( $book_array['backcover'] ) ) {
					$this->backcover = $book_array['backcover'];
				}

				if ( isset( $book_array['bn_link'] ) ) {
					$this->bn_link = $book_array['bn_link'];
				}

				if ( isset( $book_array['book_uid'] ) ) {
					$this->book_uid = $book_array['book_uid'];
				}

				if ( isset( $book_array['bam_link'] ) ) {
					$this->bam_link = $book_array['bam_link'];
				}

				if ( isset( $book_array['callnumber'] ) ) {
					$this->callnumber = $book_array['callnumber'];
				}

				if ( isset( $book_array['category'] ) ) {
					$this->category = $book_array['category'];
				}

				if ( isset( $book_array['copies'] ) ) {
					$this->copies = $book_array['copies'];
				}

				if ( isset( $book_array['country'] ) ) {
					$this->country = $book_array['country'];
				}

				if ( isset( $book_array['crosssells'] ) ) {
					$this->crosssells = $book_array['crosssells'];
				}

				if ( isset( $book_array['datefinished'] ) ) {
					$this->date_finished = $book_array['datefinished'];
				}

				if ( isset( $book_array['description'] ) ) {
					$this->description = $book_array['description'];
				}

				if ( isset( $book_array['download'] ) ) {
					$this->download = $book_array['download'];
				}

				if ( isset( $book_array['edition'] ) ) {
					$this->edition = $book_array['edition'];
				}

				if ( isset( $book_array['finished'] ) ) {
					$this->finished = $book_array['finished'];
				}

				if ( isset( $book_array['firstedition'] ) ) {
					$this->first_edition = $book_array['firstedition'];
				}

				if ( isset( $book_array['format'] ) ) {
					$this->format = $book_array['format'];
				}

				if ( isset( $book_array['genres'] ) ) {
					$this->genres = $book_array['genres'];
				}

				if ( isset( $book_array['goodreadslink'] ) ) {
					$this->goodreadslink = $book_array['goodreadslink'];
				}

				if ( isset( $book_array['google_preview'] ) ) {
					$this->google_preview = $book_array['google_preview'];
				}

				if ( isset( $book_array['height'] ) ) {
					$this->height = $book_array['height'];
				}

				if ( isset( $book_array['illustrator'] ) ) {
					$this->illustrator = $book_array['illustrator'];
				}

				if ( isset( $book_array['image'] ) ) {
					$this->image = $book_array['image'];
				}

				if ( isset( $book_array['isbn'] ) ) {
					$this->isbn = $book_array['isbn'];
				}

				if ( isset( $book_array['isbn13'] ) ) {
					$this->isbn13 = $book_array['isbn13'];
				}

				if ( isset( $book_array['keywords'] ) ) {
					$this->keywords = $book_array['keywords'];
				}

				if ( isset( $book_array['kobo_link'] ) ) {
					$this->kobo_link = $book_array['kobo_link'];
				}

				if ( isset( $book_array['language'] ) ) {
					$this->language = $book_array['language'];
				}

				if ( isset( $book_array['length'] ) ) {
					$this->length = $book_array['length'];
				}

				if ( isset( $book_array['library'] ) ) {
					$this->library = $book_array['library'];
				}

				if ( isset( $book_array['notes'] ) ) {
					$this->notes = $book_array['notes'];
				}

				if ( isset( $book_array['numberinseries'] ) ) {
					$this->numberinseries = $book_array['numberinseries'];
				}

				if ( isset( $book_array['originalpubyear'] ) ) {
					$this->originalpubyear = $book_array['originalpubyear'];
				}

				if ( isset( $book_array['originaltitle'] ) ) {
					$this->originaltitle = $book_array['originaltitle'];
				}

				if ( isset( $book_array['othereditions'] ) ) {
					$this->othereditions = $book_array['othereditions'];
				}

				if ( isset( $book_array['outofprint'] ) ) {
					$this->outofprint = $book_array['outofprint'];
				}

				if ( isset( $book_array['page_yes'] ) ) {
					$this->page_yes = $book_array['page_yes'];
				}

				if ( isset( $book_array['pages'] ) ) {
					$this->pages = $book_array['pages'];
				}

				if ( isset( $book_array['post_yes'] ) ) {
					$this->post_yes = $book_array['post_yes'];
				}

				if ( isset( $book_array['price'] ) ) {
					$this->price = $book_array['price'];
				}

				if ( isset( $book_array['productcategory'] ) ) {
					$this->productcategory = $book_array['productcategory'];
				}

				if ( isset( $book_array['pub_year'] ) ) {
					$this->pub_year = $book_array['pub_year'];
				}

				if ( isset( $book_array['publisher'] ) ) {
					$this->publisher = $book_array['publisher'];
				}

				if ( isset( $book_array['purchasenote'] ) ) {
					$this->purchasenote = $book_array['purchasenote'];
				}

				if ( isset( $book_array['rating'] ) ) {
					$this->rating = $book_array['rating'];
				}

				if ( isset( $book_array['regularprice'] ) ) {
					$this->regularprice = $book_array['regularprice'];
				}

				if ( isset( $book_array['reviews'] ) ) {
					$this->reviews = $book_array['reviews'];
				}

				if ( isset( $book_array['salebegin'] ) ) {
					$this->salebegin = $book_array['salebegin'];
				}

				if ( isset( $book_array['saleend'] ) ) {
					$this->saleend = $book_array['saleend'];
				}

				if ( isset( $book_array['saleprice'] ) ) {
					$this->saleprice = $book_array['saleprice'];
				}

				if ( isset( $book_array['series'] ) ) {
					$this->series = $book_array['series'];
				}

				if ( isset( $book_array['shortdescription'] ) ) {
					$this->shortdescription = $book_array['shortdescription'];
				}

				if ( isset( $book_array['signed'] ) ) {
					$this->signed = $book_array['signed'];
				}

				if ( isset( $book_array['similarbooks'] ) ) {
					$this->similarbooks = $book_array['similarbooks'];
				}

				if ( isset( $book_array['sku'] ) ) {
					$this->sku = $book_array['sku'];
				}

				if ( isset( $book_array['stock'] ) ) {
					$this->stock = $book_array['stock'];
				}

				if ( isset( $book_array['subgenre'] ) ) {
					$this->subgenre = $book_array['subgenre'];
				}

				if ( isset( $book_array['subject'] ) ) {
					$this->subject = $book_array['subject'];
				}

				if ( isset( $book_array['swapYes'] ) ) {
					$this->swap_yes = $book_array['swapYes'];
				}

				if ( isset( $book_array['title'] ) ) {
					$this->title = $book_array['title'];
				}

				if ( isset( $book_array['upsells'] ) ) {
					$this->upsells = $book_array['upsells'];
				}

				if ( isset( $book_array['use_amazon_yes'] ) ) {
					$this->use_amazon_yes = $book_array['use_amazon_yes'];
				}

				if ( isset( $book_array['virtual'] ) ) {
					$this->virtual = $book_array['virtual'];
				}

				if ( isset( $book_array['weight'] ) ) {
					$this->weight = $book_array['weight'];
				}

				if ( isset( $book_array['width'] ) ) {
					$this->width = $book_array['width'];
				}

				if ( isset( $book_array['woocommerce'] ) ) {
					$this->woocommerce = $book_array['woocommerce'];
				}

				if ( isset( $book_array['woofile'] ) ) {
					$this->woofile = $book_array['woofile'];
				}

				if ( isset( $book_array['ebook'] ) ) {
					$this->ebook = $book_array['ebook'];
				}

				$this->id = $id;
			}

			if ( 'add' === $this->action || 'addbulk' === $this->action ) {
				$this->add_book();
			}

			if ( 'edit' === $this->action ) {
				$this->id = $id;
				$this->edit_book();
			}

			if ( 'delete' === $this->action ) {
				$this->id = $id;
				$this->delete_book();
			}

			if ( 'search' === $this->action ) {
				$this->book_page  = $book_array['book_page'];
				$this->amazonauth = $book_array['amazonauth'];
				if ( 'true' === $this->amazonauth && 'true' === $this->use_amazon_yes ) {
					$this->go_amazon = true;
					$this->gather_amazon_data();
					//$this->gather_google_data();
					$this->gather_open_library_data();
					$this->gather_itunes_data();
					$this->gather_goodreads_data();
					$this->create_buy_links();
					$this->add_book();
					$this->add_result = true;
				} else {

					// If $this->go_amazon is false, query the other apis and add the provided data to database.
					$this->go_amazon = false;
					//$this->gather_google_data();
					$this->gather_open_library_data();
					$this->gather_itunes_data();
					$this->gather_goodreads_data();
					$this->create_buy_links();
					$this->add_book();
					$this->add_result = true;
				}
			}

			if ( 'bookfinder-colorbox' === $this->action ) {
				//$this->gather_google_data();
				$this->gather_open_library_data();
				$this->gather_itunes_data();
				$this->create_buy_links();
			}
		}

		/**
		 * Function to create the buy links.
		 */
		private function create_buy_links() {

			global $wp_filesystem;

			// Creating Kobo link, if one wasn't provided.
			if ( null === $this->bam_link || '' === $this->bam_link ) {
				$responsecode = '';
				$result       = wp_remote_get( 'http://store.kobobooks.com/en-ca/Search?Query=' . $this->isbn );

				// Check the response code.
				$response_code    = wp_remote_retrieve_response_code( $result );
				$response_message = wp_remote_retrieve_response_message( $result );

				if ( 200 !== $response_code && ! empty( $response_message ) ) {
					return new WP_Error( $response_code, $response_message );
				} elseif ( 200 !== $response_code ) {
					$this->apireport = $this->apireport . 'Unknown error occurred with wp_remote_get() trying to build Kobo link in the create_buy_links() function ';
					return new WP_Error( $response_code, 'Unknown error occurred with wp_remote_get() trying to build Kobo link in the create_buy_links() function' );
				} else {
					$result = wp_remote_retrieve_body( $result );
				}

				if ( false !== strpos( $result, 'did not return any results' ) ) {
					$this->kobo_link = null;
				} else {
					$this->kobo_link = 'http://store.kobobooks.com/en-ca/Search?Query=' . $this->isbn;
				}
			}	

			// Creating Books-a-Million link, if one wasn't provided.
			if ( null === $this->bam_link || '' === $this->bam_link ) {
				$result       = wp_remote_get( 'http://www.booksamillion.com/p/' . $this->isbn );
				$responsecode = '';

				// Check the response code.
				$response_code    = wp_remote_retrieve_response_code( $result );
				$response_message = wp_remote_retrieve_response_message( $result );

				if ( 200 !== $response_code && ! empty( $response_message ) ) {
					return new WP_Error( $response_code, $response_message );
				} elseif ( 200 !== $response_code ) {
					$this->apireport = $this->apireport . 'Unknown error occurred with wp_remote_get() trying to build Books-a-Million link in the create_buy_links() function ';
					return new WP_Error( $response_code, 'Unknown error occurred with wp_remote_get() trying to build Books-a-Million link in the create_buy_links() function' );
				} else {
					$result = wp_remote_retrieve_body( $result );
				}

				if ( false !== stripos( $result, 'Sorry, we could not find the requested product' ) ) {
					$this->bam_link = null;
				} else {
					$this->bam_link = 'http://www.booksamillion.com/p/' . $this->isbn;
				}
			}
		}

		/**
		 * Function to will handle the calling of functions to gather data and then actually add the book.
		 */
		private function add_book() {

			// First do Amazon Authorization check.
			if ( 'true' === $this->amazonauth && 'true' === $this->use_amazon_yes ) {
				$this->go_amazon = true;
				$this->gather_amazon_data();
				//$this->gather_google_data();
				$this->gather_open_library_data();
				$this->gather_itunes_data();
				$this->gather_goodreads_data();
				$this->create_buy_links();
				$this->set_default_woocommerce_data();
				$this->create_wpbooklist_woocommerce_product();
				$this->create_author_first_last();
				$this->create_similar_books();
				$this->add_to_db();
			} else {
				// If $this->go_amazon is false, query the other apis and add the provided data to database.
				$this->go_amazon = false;
				//$this->gather_google_data();
				$this->gather_open_library_data();
				$this->gather_itunes_data();
				$this->gather_goodreads_data();
				$this->create_buy_links();
				$this->set_default_woocommerce_data();
				$this->create_wpbooklist_woocommerce_product();
				$this->create_author_first_last();
				$this->create_similar_books();
				$this->add_to_db();
			}
		}

		/**
		 * Function to handle the gathering of Amazon data.
		 */
		private function gather_amazon_data() {

			global $wpdb;

			$params = array();

			// Begin Building Query and Determine Amazon region.
			$region = '';
			switch ( $this->options_results->amazoncountryinfo ) {
				case 'au':
					$region = 'com.au';
					break;
				case 'ca':
					$region = 'ca';
					break;
				case 'fr':
					$region = 'fr';
					break;
				case 'de':
					$region = 'de';
					break;
				case 'in':
					$region = 'in';
					break;
				case 'it':
					$region = 'it';
					break;
				case 'jp':
					$region = 'co.jp';
					break;
				case 'mx':
					$region = 'com.mx';
					break;
				case 'es':
					$region = 'es';
					break;
				case 'uk':
					$region = 'co.uk';
					break;
				case 'cn':
					$region = 'cn';
					break;
				case 'sg':
					$region = 'com.sg';
					break;
				case 'nl':
					$region = 'nl';
					break;
				case 'br':
					$region = 'com.br';
					break;
				default:
					$region = 'com';
			}

			// If user has saved their own Amazon API Keys.
			if ( null !== $this->options_results->amazonapisecret && '' !== $this->options_results->amazonapisecret && null !== $this->options_results->amazonapipublic && '' !== $this->options_results->amazonapipublic ) {
				$postdata = http_build_query(
					array(
						'isbn'          => $this->isbn,
						'associate_tag' => $this->options_results->amazonaff,
						'book_title'    => $this->title,
						'book_author'   => $this->author,
						'book_page'     => $this->book_page,
						'region'        => $region,
						'api_secret'    => $this->options_results->amazonapisecret,
						'api_public'    => $this->options_results->amazonapipublic,
					)
				);
			} else {
				$postdata = http_build_query(
					array(
						'isbn'          => $this->isbn,
						'associate_tag' => $this->options_results->amazonaff,
						'book_title'    => $this->title,
						'book_author'   => $this->author,
						'book_page'     => $this->book_page,
						'region'        => $region,
					)
				);
			}

			// Making a check for escaped ampersands.
			if ( false !== stripos( $postdata, '&amp;' ) ) {
				$postdata = str_replace( '&amp;', '&', $postdata );
			}

			if ( '' !== $this->isbn && null !== $this->isbn ) {
				$this->apireport = $this->apireport . 'Results for "' . $this->isbn . '": ';
			} elseif ( '' !== $this->title && null !== $this->title ) {
				$this->apireport = $this->apireport . 'Results for "' . $this->title . '": ';
			} else {
				$this->apireport = $this->apireport . 'Results for Unknown Book: ';
			}

			// Before we do anything else, let's make sure we don't have a saved transient for this book - if we do, no sense in making a new api call - will cut down on requests. Also, do not use a transient at all if we're editing a book, and try to delete an existing transient in the 'else' part before creating a new one.
			$transient_name   = 'wpbl_' . md5( $this->isbn . '_amazon' );
			$transient_exists = $this->transients->existing_transient_check( $transient_name );
			if ( $transient_exists && 'edit' !== $this->action ) {
				$this->amazonapiresult      = $transient_exists;
				$this->amazon_transient_use = 'Yes';
			} else {

				$status                     = '';
				$this->amazonapiresult      = '';
				$this->amazonapiresult      = wp_remote_get( 'https://sublime-vine-199216.appspot.com/?' . $postdata );
				$this->amazon_transient_use = 'No';

				// Check the response code.
				$response_code    = wp_remote_retrieve_response_code( $this->amazonapiresult );
				$response_message = wp_remote_retrieve_response_message( $this->amazonapiresult );

				if ( 200 !== $response_code && ! empty( $response_message ) ) {

					$this->apiamazonfailcount++;

					// Let's try this 2 more times, one for ISBN13, and one for ASIN, if they exist.
					if ( 'isbn-isbn13-asin' !== $this->gather_amazon_attempt_with ) {

						if ( 'isbn' === $this->gather_amazon_attempt_with ) {
							$this->gather_amazon_attempt_with = 'isbn-isbn13';
							$this->isbn                       = $this->isbn13;
							$this->gather_amazon_data();
						}

						if ( 'isbn-isbn13' === $this->gather_amazon_attempt_with ) {
							$this->gather_amazon_attempt_with = 'isbn-isbn13-asin';
							$this->isbn                       = $this->asin;
							$this->gather_amazon_data();
						}
					}

					$this->apireport = $this->apireport . 'Looks like we tried the Amazon wp_remote_get function, but something went wrong .  Status Code is: ' . $response_code . ' and Response Message is: ' . $response_message . ' .  URL Request was: https://sublime-vine-199216.appspot.com/?' . $postdata . ' ';
					return new WP_Error( $response_code, $response_message );
				} elseif ( 200 !== $response_code ) {
					$this->apireport = $this->apireport . 'Unknown error occurred with the Amazon wp_remote_get function';
					return new WP_Error( $response_code, 'Unknown error occurred with the Amazon wp_remote_get function' );
				} else {
					$this->apireport       = $this->apireport . 'Amazon API call via wp_remote_get looks to be successful.  URL Request was: https://sublime-vine-199216.appspot.com/?' . $postdata . ' ';
					$this->amazonapiresult = wp_remote_retrieve_body( $this->amazonapiresult );
				}

				// Actually attempting to delete existing transients before creation of new one.
				$transient_delete_api_data_result       = $this->transients->delete_transient( $transient_name );
				$this->transient_create_result = $this->transients->create_api_transient( $transient_name, $this->amazonapiresult, WEEK_IN_SECONDS );
			}

			// Convert result from API call to regular ol' array.
			if ( 3 > $this->apiamazonfailcount ) {

				// If we're dealing with the Bookfinder Extension, do not append '</ItemLookupResponse>', otherwise do so.
				if ( strpos( $this->amazonapiresult, '</ItemSearchResponse>' ) !== false ) {
					$this->amazonapiresult = explode( '</ItemSearchResponse>', $this->amazonapiresult );
					$this->amazonapiresult = $this->amazonapiresult[0] . '</ItemSearchResponse>';
				} else {
					$this->amazonapiresult = explode( '</ItemLookupResponse>', $this->amazonapiresult );
					$this->amazonapiresult = $this->amazonapiresult[0] . '</ItemLookupResponse>';
				}

				$xml = simplexml_load_string( $this->amazonapiresult, 'SimpleXMLElement', LIBXML_NOCDATA );

				// Checking to see if the XML conversion was successful.
				if ( false === $xml ) {
					$this->apireport = $this->apireport . 'Looks like something went wrong with converting the Amazon API result to XML. ';
				} else {
					$this->apireport = $this->apireport . 'Amazon XML conversion went well. ';

					// Convert XML to array.
					$json               = wp_json_encode( $xml );
					$this->amazon_array = json_decode( $json, true );

					// Now check and see if the converted XML contains any error report, and set the error flag if so.
					$error_flag = false;
					if ( array_key_exists( 'Items', $this->amazon_array )
						&& array_key_exists( 'Request', $this->amazon_array['Items'] )
						&& array_key_exists( 'Errors', $this->amazon_array['Items']['Request'] )
						&& array_key_exists( 'Error', $this->amazon_array['Items']['Request']['Errors'] )
						&& array_key_exists( 'Message', $this->amazon_array['Items']['Request']['Errors']['Error'] ) ) {

						$this->apireport = $this->apireport . "Amazon Error message is: '" . $this->amazon_array['Items']['Request']['Errors']['Error']['Message'] . "' ";
						$error_flag      = true;
					}

					// If $error_flag is false,  begin assigning values from $this->amazon_array to properties.
					if ( ! $error_flag ) {

						// Get values from the Amazon Array that has a '0' as a key.
						if ( array_key_exists( 'Items', $this->amazon_array ) && array_key_exists( 'Item', $this->amazon_array['Items'] ) && array_key_exists( 0, $this->amazon_array['Items']['Item'] ) ) {

							// Get ASIN number.
							if ( null === $this->asin || '' === $this->asin ) {
								if ( array_key_exists( 0, $this->amazon_array['Items']['Item'] ) ) {

									if ( array_key_exists( 'ASIN', $this->amazon_array['Items']['Item'][0] ) ) {
										$this->asin = $this->amazon_array['Items']['Item'][0]['ASIN'];
									}
								}
							}

							// Get title.
							if ( null === $this->title || '' === $this->title ) {
								$this->title = $this->amazon_array['Items']['Item'][0]['ItemAttributes']['Title'];
							}

							// Get lowest used price.
							if ( null === $this->lowestusedprice || '' === $this->lowestusedprice ) {
								if ( array_key_exists( 'OfferSummary', $this->amazon_array['Items']['Item'][0] ) && array_key_exists( 'LowestUsedPrice', $this->amazon_array['Items']['Item'][0]['OfferSummary'] ) && array_key_exists( 'FormattedPrice', $this->amazon_array['Items']['Item'][0]['OfferSummary']['LowestUsedPrice'] ) ) {

									$this->lowestusedprice = $this->amazon_array['Items']['Item'][0]['OfferSummary']['LowestUsedPrice']['FormattedPrice'];
								}
							}

							// Get cover image.
							if ( null === $this->image || '' === $this->image ) {
								if ( array_key_exists( 'LargeImage', $this->amazon_array['Items']['Item'][0] ) && array_key_exists( 'URL', $this->amazon_array['Items']['Item'][0]['LargeImage'] ) ) {
									$this->image = $this->amazon_array['Items']['Item'][0]['LargeImage']['URL'];
								}
							}

							// Get author.
							$author_string = '';
							if ( null === $this->author || '' === $this->author ) {

								if ( array_key_exists( 'Author', $this->amazon_array['Items']['Item'][0]['ItemAttributes'] ) ) {
									$this->author = $this->amazon_array['Items']['Item'][0]['ItemAttributes']['Author'];
								}

								if ( is_array( $this->author ) ) {
									foreach ( $this->author as $author ) {
										$author_string = $author_string . ', ' . $author;
									}
									$author_string = rtrim( $author_string, ', ' );
									$author_string = ltrim( $author_string, ', ' );
									$this->author  = $author_string;
								}
							}

							// Get format.
							$format_string = '';
							if ( null === $this->format || '' === $this->format ) {

								if ( array_key_exists( 'Binding', $this->amazon_array['Items']['Item'][0]['ItemAttributes'] ) ) {
									$this->format = $this->amazon_array['Items']['Item'][0]['ItemAttributes']['Binding'];
								}

								if ( is_array( $this->format ) ) {
									foreach ( $this->format as $format ) {
										$format_string = $format_string . ', ' . $format;
									}
									$format_string = rtrim( $format_string, ', ' );
									$format_string = ltrim( $format_string, ', ' );
									$this->format  = $format_string;
								}
							}

							// Get edition.
							$edition_string = '';
							if ( null === $this->edition || '' === $this->edition ) {

								if ( array_key_exists( 'Edition', $this->amazon_array['Items']['Item'][0]['ItemAttributes'] ) ) {
									$this->edition = $this->amazon_array['Items']['Item'][0]['ItemAttributes']['Edition'];
								}

								if ( is_array( $this->edition ) ) {
									foreach ( $this->edition as $edition ) {
										$edition_string = $edition_string . ', ' . $edition;
									}
									$edition_string = rtrim( $edition_string, ', ' );
									$edition_string = ltrim( $edition_string, ', ' );
									$this->edition  = $edition_string;
								}
							}

							// Get Language.
							$language_string = '';
							if ( null === $this->language || '' === $this->language ) {

								if ( array_key_exists( 'Languages', $this->amazon_array['Items']['Item'][0]['ItemAttributes'] ) ) {
									$this->language = $this->amazon_array['Items']['Item'][0]['ItemAttributes']['Languages'];
								}

								if ( is_array( $this->language ) ) {

									if ( array_key_exists( 'Language', $this->language ) ) {

										if ( array_key_exists( 0, $this->language['Language'] ) ) {
											$this->language = $this->language['Language'][0]['Name'];
										} else {
											$this->language = $this->language['Language']['Name'];
										}
									}
								}
							}

							// Getting pages.
							if ( null === $this->pages || '' === $this->pages ) {
								if ( array_key_exists( 'NumberOfPages', $this->amazon_array['Items']['Item'][0]['ItemAttributes'] ) ) {
									$this->pages = $this->amazon_array['Items']['Item'][0]['ItemAttributes']['NumberOfPages'];
								}
							}

							// Getting publication date.
							if ( null === $this->pub_year || '' === $this->pub_year ) {
								$this->pub_year = $this->amazon_array['Items']['Item'][0]['ItemAttributes']['PublicationDate'];
							}

							// Getting publisher.
							if ( null === $this->publisher || '' === $this->publisher ) {
								if ( array_key_exists( 'Publisher', $this->amazon_array['Items']['Item'][0]['ItemAttributes'] ) ) {
									$this->publisher = $this->amazon_array['Items']['Item'][0]['ItemAttributes']['Publisher'];
								}
							}

							// Getting description.
							if ( null === $this->description || '' === $this->description ) {

								if ( array_key_exists( 'EditorialReviews', $this->amazon_array['Items']['Item'][0] ) && array_key_exists( 'EditorialReview', $this->amazon_array['Items']['Item'][0]['EditorialReviews'] ) && array_key_exists( 'Content', $this->amazon_array['Items']['Item'][0]['EditorialReviews']['EditorialReview'] ) ) {
									$this->description = $this->amazon_array['Items']['Item'][0]['EditorialReviews']['EditorialReview']['Content'];
								}

								if ( null === $this->description || '' === $this->description ) {
									if ( array_key_exists( 'EditorialReviews', $this->amazon_array['Items']['Item'][0] ) && array_key_exists( 'EditorialReview', $this->amazon_array['Items']['Item'][0]['EditorialReviews'] ) && array_key_exists( 0, $this->amazon_array['Items']['Item'][0]['EditorialReviews']['EditorialReview'] ) && array_key_exists( 'Content', $this->amazon_array['Items']['Item'][0]['EditorialReviews']['EditorialReview'][0] ) ) {

										$this->description = $this->amazon_array['Items']['Item'][0]['EditorialReviews']['EditorialReview'][0]['Content'];
									}
								}
							}

							// Getting amazon link, if we don't already have one.
							if ( '' === $this->amazonbuylink || null === $this->amazonbuylink ) {
								if ( null === $this->amazon_detail_page || '' === $this->amazon_detail_page ) {
									$this->amazon_detail_page = $this->amazon_array['Items']['Item'][0]['DetailPageURL'];
								}
							} else {
								$this->amazon_detail_page = $this->amazonbuylink;
							}

							// Getting Amazon reviews iFrame.
							if ( null === $this->review_iframe || '' === $this->review_iframe ) {
								$this->review_iframe = $this->amazon_array['Items']['Item'][0]['CustomerReviews']['IFrameURL'];
							}

							// Getting similar books.
							$similarproductsstring = '';
							if ( null === $this->similar_products || '' === $this->similar_products ) {
								if ( array_key_exists( 'SimilarProducts', $this->amazon_array['Items']['Item'][0] ) ) {
									$this->similar_products = $this->amazon_array['Items']['Item'][0]['SimilarProducts']['SimilarProduct'];
								}
								if ( is_array( $this->similar_products ) && array_key_exists( 0, $this->similar_products ) ) {
									foreach ( $this->similar_products as $prod ) {
										$similarproductsstring = $similarproductsstring . ';bsp;' . $prod['ASIN'] . '---' . $prod['Title'];
									}
								} else {
									$similarproductsstring = $similarproductsstring . ';bsp;' . $this->similar_products['ASIN'] . '---' . $this->similar_products['Title'];
								}

								$this->similar_products = $similarproductsstring;
							}
						}

						// Get values from the Amazon Array that does not have a '0' as a key.
						if ( array_key_exists( 'Items', $this->amazon_array ) && array_key_exists( 'Item', $this->amazon_array['Items'] ) && ! array_key_exists( 0, $this->amazon_array['Items']['Item'] ) ) {

							// Get ASIN number.
							if ( null === $this->asin || '' === $this->asin ) {
								if ( array_key_exists( 'ASIN', $this->amazon_array['Items']['Item'] ) ) {
									$this->asin = $this->amazon_array['Items']['Item']['ASIN'];
								}
							}

							// Get title.
							if ( null === $this->title || '' === $this->title ) {
								$this->title = $this->amazon_array['Items']['Item']['ItemAttributes']['Title'];
							}

							// Get lowest used price.
							if ( null === $this->lowestusedprice || '' === $this->lowestusedprice ) {
								if ( array_key_exists( 'OfferSummary', $this->amazon_array['Items']['Item'] ) && array_key_exists( 'LowestUsedPrice', $this->amazon_array['Items']['Item']['OfferSummary'] ) && array_key_exists( 'FormattedPrice', $this->amazon_array['Items']['Item']['OfferSummary']['LowestUsedPrice'] ) ) {

									$this->lowestusedprice = $this->amazon_array['Items']['Item']['OfferSummary']['LowestUsedPrice']['FormattedPrice'];
								}
							}

							// Get cover image.
							if ( null === $this->image || '' === $this->image ) {
								$this->image = $this->amazon_array['Items']['Item']['LargeImage']['URL'];
							}

							// Get author.
							$author_string = '';
							if ( null === $this->author || '' === $this->author ) {
								if ( array_key_exists( 'Author', $this->amazon_array['Items']['Item']['ItemAttributes'] ) ) {
									$this->author = $this->amazon_array['Items']['Item']['ItemAttributes']['Author'];
								}
								if ( is_array( $this->author ) ) {
									foreach ( $this->author as $author ) {
										$author_string = $author_string . ', ' . $author;
									}
									$author_string = rtrim( $author_string, ', ' );
									$author_string = ltrim( $author_string, ', ' );
									$this->author  = $author_string;
								}
							}

							// Get format.
							$format_string = '';
							if ( null === $this->format || '' === $this->format ) {
								if ( array_key_exists( 'Binding', $this->amazon_array['Items']['Item']['ItemAttributes'] ) ) {
									$this->format = $this->amazon_array['Items']['Item']['ItemAttributes']['Binding'];
								}
								if ( is_array( $this->format ) ) {
									foreach ( $this->format as $format ) {
										$format_string = $format_string . ', ' . $format;
									}
									$format_string = rtrim( $format_string, ', ' );
									$format_string = ltrim( $format_string, ', ' );
									$this->format  = $format_string;
								}
							}

							// Get edition.
							$edition_string = '';
							if ( null === $this->edition || '' === $this->edition ) {
								if ( array_key_exists( 'Edition', $this->amazon_array['Items']['Item']['ItemAttributes'] ) ) {
									$this->edition = $this->amazon_array['Items']['Item']['ItemAttributes']['Edition'];
								}
								if ( is_array( $this->edition ) ) {
									foreach ( $this->edition as $edition ) {
										$edition_string = $edition_string . ', ' . $edition;
									}
									$edition_string = rtrim( $edition_string, ', ' );
									$edition_string = ltrim( $edition_string, ', ' );
									$this->edition  = $edition_string;
								}
							}

							// Get Language.
							$language_string = '';
							if ( null === $this->language || '' === $this->language ) {

								if ( array_key_exists( 'Languages', $this->amazon_array['Items']['Item']['ItemAttributes'] ) ) {
									$this->language = $this->amazon_array['Items']['Item']['ItemAttributes']['Languages'];
								}

								if ( is_array( $this->language ) ) {

									if ( array_key_exists( 'Language', $this->language ) ) {

										if ( array_key_exists( 0, $this->language['Language'] ) ) {
											$this->language = $this->language['Language'][0]['Name'];
										} else {
											$this->language = $this->language['Language']['Name'];
										}
									}
								}
							}

							// Getting pages.
							if ( null === $this->pages || '' === $this->pages ) {
								if ( array_key_exists( 'NumberOfPages', $this->amazon_array['Items']['Item']['ItemAttributes'] ) ) {
									$this->pages = $this->amazon_array['Items']['Item']['ItemAttributes']['NumberOfPages'];
								}
							}

							// Getting publication date.
							if ( null === $this->pub_year || '' === $this->pub_year ) {
								if ( array_key_exists( 'PublicationDate', $this->amazon_array['Items']['Item']['ItemAttributes'] ) ) {
									$this->pub_year = $this->amazon_array['Items']['Item']['ItemAttributes']['PublicationDate'];
								}
							}

							// Getting publisher.
							if ( null === $this->publisher || '' === $this->publisher ) {
								if ( array_key_exists( 'Publisher', $this->amazon_array['Items']['Item']['ItemAttributes'] ) ) {
									$this->publisher = $this->amazon_array['Items']['Item']['ItemAttributes']['Publisher'];
								}
							}

							// Getting description.
							if ( null === $this->description || '' === $this->description ) {

								if ( array_key_exists( 'EditorialReviews', $this->amazon_array['Items']['Item'] ) && array_key_exists( 'EditorialReview', $this->amazon_array['Items']['Item']['EditorialReviews'] ) && array_key_exists( 'Content', $this->amazon_array['Items']['Item']['EditorialReviews']['EditorialReview'] ) ) {
									$this->description = $this->amazon_array['Items']['Item']['EditorialReviews']['EditorialReview']['Content'];
								}

								if ( null === $this->description || '' === $this->description ) {

									if ( array_key_exists( 'EditorialReviews', $this->amazon_array['Items']['Item'] ) && array_key_exists( 'EditorialReview', $this->amazon_array['Items']['Item']['EditorialReviews'] ) && array_key_exists( 0, $this->amazon_array['Items']['Item']['EditorialReviews']['EditorialReview'] ) && array_key_exists( 'Content', $this->amazon_array['Items']['Item']['EditorialReviews']['EditorialReview'][0] ) ) {

										$this->description = $this->amazon_array['Items']['Item']['EditorialReviews']['EditorialReview'][0]['Content'];

									}
								}
							}

							// Getting amazon link, if we don't already have one.
							if ( '' === $this->amazonbuylink || null === $this->amazonbuylink ) {
								if ( null === $this->amazon_detail_page || '' === $this->amazon_detail_page ) {
									$this->amazon_detail_page = $this->amazon_array['Items']['Item']['DetailPageURL'];
								}
							} else {
								$this->amazon_detail_page = $this->amazonbuylink;
							}

							// Getting Amazon reviews iFrame.
							if ( null === $this->review_iframe || '' === $this->review_iframe ) {
								$this->review_iframe = $this->amazon_array['Items']['Item']['CustomerReviews']['IFrameURL'];
							}

							// Getting similar books.
							$similarproductsstring = '';
							if ( null === $this->similar_products || '' === $this->similar_products ) {

								if ( array_key_exists( 'SimilarProducts', $this->amazon_array['Items']['Item'] ) ) {
									$this->similar_products = $this->amazon_array['Items']['Item']['SimilarProducts']['SimilarProduct'];
								}

								if ( is_array( $this->similar_products ) && array_key_exists( 0, $this->similar_products ) ) {
									foreach ( $this->similar_products as $prod ) {
										$similarproductsstring = $similarproductsstring . ';bsp;' . $prod['ASIN'] . '---' . $prod['Title'];
									}
								} else {
									$similarproductsstring = $similarproductsstring . ';bsp;' . $this->similar_products['ASIN'] . '---' . $this->similar_products['Title'];
								}

								$this->similar_products = $similarproductsstring;
							}
						}

						// Setting up iFrame to play with https.
						if ( isset( $_SERVER['HTTPS'] ) ) {
							$pos                 = strpos( $this->review_iframe, ':' );
							$this->review_iframe = substr_replace( $this->review_iframe, 'https', 0, $pos );
						}
					}
				}
			} else {

				if ( $this->rerun_amazon_flag ) {
					sleep( 1 );
					$this->rerun_amazon_flag  = false;
					$this->apiamazonfailcount = 0;
					$this->gather_amazon_data();
				}
			}

			// Create report of what values were found and what weren't.
			if ( null !== $this->title && '' !== $this->title ) {
				$this->whichapifound['title'] = 'Amazon';
			}

			if ( null !== $this->image && '' !== $this->image ) {
				$this->whichapifound['image'] = 'Amazon';
			}

			if ( null !== $this->author && '' !== $this->author ) {
				$this->whichapifound['author'] = 'Amazon';
			}

			if ( null !== $this->pages && '' !== $this->pages ) {
				$this->whichapifound['pages'] = 'Amazon';
			}

			if ( null !== $this->pub_year && '' !== $this->pub_year ) {
				$this->whichapifound['pub_year'] = 'Amazon';
			}

			if ( null !== $this->publisher && '' !== $this->publisher ) {
				$this->whichapifound['publisher'] = 'Amazon';
			}

			if ( null !== $this->description && '' !== $this->description ) {
				$this->whichapifound['description'] = 'Amazon';
			}

			if ( null !== $this->amazon_detail_page && '' !== $this->amazon_detail_page ) {
				$this->whichapifound['amazondetailpage'] = 'Amazon';
			}

			if ( null !== $this->review_iframe && '' !== $this->review_iframe ) {
				$this->whichapifound['review_iframe'] = 'Amazon';
			}

			if ( null !== $this->similar_products && '' !== $this->similar_products ) {
				$this->whichapifound['similar_products'] = 'Amazon';
			}
		}

		/**
		 * Function to handle the gathering of Google Data.
		 */
		private function gather_google_data() {

			// If there's no ISBN # provided, there's no use in doing anything here.
			if ( null === $this->isbn || '' === $this->isbn ) {
				return;
			}

			if ( null !== $this->options_results->googleapi && '' !== $this->options_results->googleapi ) {
				$google_api = $this->options_results->googleapi;
			} else {
				$google_api = 'AIzaSyBl6KEeKRddmhnK-jX65pGkjBW1Y6Q5_rM';
			}

			// Before we do anything else, let's make sure we don't have a saved transient for this book - if we do, no sense in making a new api call - will cut down on requests. Also, do not use a transient at all if we're editing a book, and try to delete an existing transient in the 'else' part before creating a new one.
			$transient_name   = 'wpbl_' . md5( $this->isbn . '_google' );
			$transient_exists = $this->transients->existing_transient_check( $transient_name );
			if ( $transient_exists && 'edit' !== $this->action ) {
				$this->googleapiresult      = $transient_exists;
				$this->google_transient_use = 'Yes';
			} else {

				$status                     = '';
				$this->googleapiresult      = '';
				$this->googleapiresult      = wp_remote_get( 'https://www.googleapis.com/books/v1/volumes?q=isbn:' . $this->isbn . '&key=' . $google_api . '&country=US' );
				$this->google_transient_use = 'No';

				// Check the response code.
				$response_code    = wp_remote_retrieve_response_code( $this->googleapiresult );
				$response_message = wp_remote_retrieve_response_message( $this->googleapiresult );

				if ( 200 !== $response_code && ! empty( $response_message ) ) {
					$this->apireport = $this->apireport . 'Looks like we tried the google wp_remote_get function, but something went wrong .  Status Code is: ' . $response_code . ' and Response Message is: ' . $response_message . ' .  URL Request was: https://www.googleapis.com/books/v1/volumes?q=isbn:' . $this->isbn . '&key=' . $google_api . '&country=US';
					return new WP_Error( $response_code, $response_message );
				} elseif ( 200 !== $response_code ) {
					$this->apireport = $this->apireport . 'Unknown error occurred with the google wp_remote_get function';
					return new WP_Error( $response_code, 'Unknown error occurred with the google wp_remote_get function' );
				} else {
					$this->apireport       = $this->apireport . 'Google API call via wp_remote_get looks to be successful.  URL Request was: https://www.googleapis.com/books/v1/volumes?q=isbn:' . $this->isbn . '&key=' . $google_api . '&country=US';
					$this->googleapiresult = wp_remote_retrieve_body( $this->googleapiresult );
				}

				// Actually attempting to delete existing transients before creation of new one.
				$transient_delete_api_data_result       = $this->transients->delete_transient( $transient_name );
				$this->transient_create_result = $this->transients->create_api_transient( $transient_name, $this->googleapiresult, WEEK_IN_SECONDS );
			}

			if ( null !== $this->googleapiresult && '' !== $this->googleapiresult ) {

				// Convert result to array.
				$json_output_google = json_decode( $this->googleapiresult, true );

				if ( is_array( $json_output_google ) ) {
					$this->apireport = $this->apireport . ' Google Array conversion went well. ';
				} else {
					$this->apireport = $this->apireport . 'Looks like something went wrong with converting the Google API result to an array. ';
				}

				// Now check and see if the array contains any error report, and set the error flag if so.
				$error_flag = false;
				if ( array_key_exists( 'error', $json_output_google )
					&& array_key_exists( 'errors', $json_output_google['error'] )
					&& array_key_exists( 0, $json_output_google['error']['errors'] )
					&& array_key_exists( 'message', $json_output_google['error']['errors'][0] ) ) {

					$error_flag      = true;
					$this->apireport = $this->apireport . "Google Error message is: '" . $json_output_google['error']['errors'][0]['message'] . "' ";
				}

				if ( ! $error_flag ) {
					if ( is_array( $json_output_google ) && array_key_exists( 'items', $json_output_google ) && array_key_exists( 0, $json_output_google['items'] ) && array_key_exists( 'volumeInfo', $json_output_google['items'][0] ) ) {

						// Making sure we didn't miss any values from Amazon data grab.
						if ( null === $this->author || '' === $this->author ) {

							if ( array_key_exists( 'author', $json_output_google['items'][0]['volumeInfo'] ) ) {
								$this->author = $json_output_google['items'][0]['volumeInfo']['author'];
							}

							if ( array_key_exists( 'authors', $json_output_google['items'][0]['volumeInfo'] ) && array_key_exists( 0, $json_output_google['items'][0]['volumeInfo']['authors'] ) ) {
								$this->author = $json_output_google['items'][0]['volumeInfo']['authors'][0];
							}
						}

						if ( null === $this->image || '' === $this->image ) {
							$this->image = $json_output_google['items'][0]['volumeInfo']['imageLinks']['thumbnail'];
						}

						if ( null === $this->pages || '' === $this->pages ) {
							$this->pages = $json_output_google['items'][0]['volumeInfo']['pageCount'];
						}

						if ( null === $this->pub_year || '' === $this->pub_year ) {
							$this->pub_year = $json_output_google['items'][0]['volumeInfo']['publishedDate'];
						}

						if ( null === $this->publisher || '' === $this->publisher ) {
							$this->publisher = $json_output_google['items'][0]['volumeInfo']['publisher'];
						}

						if ( null === $this->description || '' === $this->description ) {
							if ( array_key_exists( 'description', $json_output_google['items'][0]['volumeInfo'] ) ) {
								$this->description = $json_output_google['items'][0]['volumeInfo']['description'];
							}
						}

						if ( null === $this->category || '' === $this->category ) {
							if ( array_key_exists( 'categories', $json_output_google['items'][0]['volumeInfo'] ) ) {
								$this->category = $json_output_google['items'][0]['volumeInfo']['categories'][0];
							}
						}
					}

					// Now getting new data.
					if ( '' === $this->googlebuylink || 'undefined' === $this->googlebuylink || null === $this->googlebuylink && ( '' === $this->google_preview || null === $this->google_preview || 'undefined' === $this->google_preview ) ) {
						if ( array_key_exists( 'items', $json_output_google ) ) {
							$this->google_preview = $json_output_google['items'][0]['accessInfo']['webReaderLink'];
						}
					}
				}
			}

			// Create report of what values were found and what weren't.
			if ( null !== $this->title && '' !== $this->title && '' === $this->whichapifound['title'] ) {
				$this->whichapifound['title'] = 'Google';
			}

			if ( null !== $this->image && '' !== $this->image && '' === $this->whichapifound['image'] ) {
				$this->whichapifound['image'] = 'Google';
			}

			if ( null !== $this->author && '' !== $this->author && '' === $this->whichapifound['author'] ) {
				$this->whichapifound['author'] = 'Google';
			}

			if ( null !== $this->pages && '' !== $this->pages && '' === $this->whichapifound['pages'] ) {
				$this->whichapifound['pages'] = 'Google';
			}

			if ( null !== $this->pub_year && '' !== $this->pub_year && '' === $this->whichapifound['pub_year'] ) {
				$this->whichapifound['pub_year'] = 'Google';
			}

			if ( null !== $this->publisher && '' !== $this->publisher && '' === $this->whichapifound['publisher'] ) {
				$this->whichapifound['publisher'] = 'Google';
			}

			if ( null !== $this->description && '' !== $this->description && '' === $this->whichapifound['description'] ) {
				$this->whichapifound['description'] = 'Google';
			}

			if ( null !== $this->category && '' !== $this->category && '' === $this->whichapifound['category'] ) {
				$this->whichapifound['category'] = 'Google';
			}

			if ( null !== $this->google_preview && '' !== $this->google_preview && '' === $this->whichapifound['google_preview'] ) {
				$this->whichapifound['google_preview'] = 'Google';
			}

		}

		/**
		 * Function to handle the gathering of OpenLibrary Data.
		 */
		private function gather_open_library_data() {

			// If there's no ISBN # provided, there's no use in doing anything here.
			if ( null === $this->isbn || '' === $this->isbn ) {
				return;
			}

			
			// Before we do anything else, let's make sure we don't have a saved transient for this book - if we do, no sense in making a new api call - will cut down on requests. Also, do not use a transient at all if we're editing a book, and try to delete an existing transient in the 'else' part before creating a new one.
			$transient_name   = 'wpbl_' . md5( $this->isbn . '_openlib' );
			$transient_exists = $this->transients->existing_transient_check( $transient_name );
			if ( $transient_exists && 'edit' !== $this->action ) {
				$this->openlibapiresult = $transient_exists;
				$this->openlib_transient_use = 'Yes';
			} else {

				$status                      = '';
				$this->openlibapiresult      = '';
				$this->openlibapiresult      = wp_remote_get( 'https://openlibrary.org/api/books?bibkeys=ISBN:' . $this->isbn . '&jscmd=data&format=json' );
				$this->openlib_transient_use = 'No';

				// Check the response code.
				$response_code    = wp_remote_retrieve_response_code( $this->openlibapiresult );
				$response_message = wp_remote_retrieve_response_message( $this->openlibapiresult );

				if ( 200 !== $response_code && ! empty( $response_message ) ) {
					$this->apireport = $this->apireport . 'Looks like we tried the openlib wp_remote_get function, but something went wrong .  Status Code is: ' . $response_code . ' and Response Message is: ' . $response_message . ' .  URL Request was: https://openlibrary.org/api/books?bibkeys=ISBN:' . $this->isbn . '&jscmd=data&format=json';
					return new WP_Error( $response_code, $response_message );
				} elseif ( 200 !== $response_code ) {
					$this->apireport = $this->apireport . 'Unknown error occurred with the openlib wp_remote_get function';
					return new WP_Error( $response_code, 'Unknown error occurred with the openlib wp_remote_get function' );
				} else {
					$this->apireport        = $this->apireport . 'Openlib API call via wp_remote_get looks to be successful.  URL Request was: https://openlibrary.org/api/books?bibkeys=ISBN:' . $this->isbn . '&jscmd=data&format=json';
					$this->openlibapiresult = wp_remote_retrieve_body( $this->openlibapiresult );
				}

				// Actually attempting to delete existing transients before creation of new one.
				$transient_delete_api_data_result       = $this->transients->delete_transient( $transient_name );
				$this->transient_create_result = $this->transients->create_api_transient( $transient_name, $this->openlibapiresult, WEEK_IN_SECONDS );
			}

			if ( '' !== $this->openlibapiresult ) {

				// Convert result to array.
				$json_output_ol = json_decode( $this->openlibapiresult, true );
				$isbn_var       = 'ISBN:' . $this->isbn;

				if ( is_array( $json_output_ol ) && 0 < count( $json_output_ol ) ) {
					$this->apireport = $this->apireport . ' OpenLibrary Array conversion went well. ';
				} else {

					if ( ! is_array( $json_output_ol ) ) {
						$this->apireport = $this->apireport . 'Looks like results may or may not have been returned from OpenLibrary, but either way, something went wrong with converting the result to an array. ';
					} else {
						$this->apireport = $this->apireport . 'Looks like the conversion to an array from OpenLibrary was successful, but it doesn\'t contain any data - book can\'t be found via OpenLibrary API. ';
					}
				}

				if ( array_key_exists( $isbn_var, $json_output_ol ) ) {

					if ( null === $this->author || '' === $this->author ) {
						if ( array_key_exists( 'authors', $json_output_ol[ $isbn_var ] ) && array_key_exists( 0, $json_output_ol[ $isbn_var ]['authors'] ) && array_key_exists( 'name', $json_output_ol[ $isbn_var ]['authors'][0] ) ) {
							$this->author = $json_output_ol[ $isbn_var ]['authors'][0]['name'];
						}
					}

					if ( null === $this->image || '' === $this->image ) {
						if ( array_key_exists( 'cover', $json_output_ol[ $isbn_var ] ) ) {
							$this->image = $json_output_ol[ $isbn_var ]['cover']['large'];
						}
					}

					if ( null === $this->pages || '' === $this->pages ) {
						if ( array_key_exists( 'number_of_pages', $json_output_ol[ $isbn_var ] ) ) {
							$this->pages = $json_output_ol[ $isbn_var ]['number_of_pages'];
						}
					}

					if ( null === $this->pub_year || '' === $this->pub_year ) {
						if ( array_key_exists( 'publish_date', $json_output_ol[ $isbn_var ] ) ) {
							$this->pub_year = $json_output_ol[ $isbn_var ]['publish_date'];
						}
					}

					if ( null === $this->publisher || '' === $this->publisher ) {
						if ( array_key_exists( 'publishers', $json_output_ol[ $isbn_var ] ) ) {
							$this->publisher = $json_output_ol[ $isbn_var ]['publishers'][0]['name'];
						}
					}

					if ( null === $this->category || '' === $this->category ) {
						if ( array_key_exists( 'subjects', $json_output_ol[ $isbn_var ] ) ) {
							$this->category = $json_output_ol[ $isbn_var ]['subjects'][0]['name'];
						}
					}
				}
			}

			// Create report of what values were found and what weren't.
			if ( null !== $this->title && '' !== $this->title && '' === $this->whichapifound['title'] ) {
				$this->whichapifound['title'] = 'OpenLibrary';
			}

			if ( null !== $this->image && '' !== $this->image && '' === $this->whichapifound['image'] ) {
				$this->whichapifound['image'] = 'OpenLibrary';
			}

			if ( null !== $this->author && '' !== $this->author && '' === $this->whichapifound['author'] ) {
				$this->whichapifound['author'] = 'OpenLibrary';
			}

			if ( null !== $this->pages && '' !== $this->pages && '' === $this->whichapifound['pages'] ) {
				$this->whichapifound['pages'] = 'OpenLibrary';
			}

			if ( null !== $this->pub_year && '' !== $this->pub_year && '' === $this->whichapifound['pub_year'] ) {
				$this->whichapifound['pub_year'] = 'OpenLibrary';
			}

			if ( null !== $this->publisher && '' !== $this->publisher && '' === $this->whichapifound['publisher'] ) {
				$this->whichapifound['publisher'] = 'OpenLibrary';
			}

			if ( null !== $this->description && '' !== $this->description && '' === $this->whichapifound['description'] ) {
				$this->whichapifound['description'] = 'OpenLibrary';
			}

			if ( null !== $this->category && '' !== $this->category && '' === $this->whichapifound['category'] ) {
				$this->whichapifound['category'] = 'OpenLibrary';
			}
		}

		/**
		 * Function to handle the gathering of iTunes Data.
		 */
		private function gather_itunes_data() {

			// If there's no ISBN # provided, there's no use in doing anything here.
			if ( null === $this->isbn || '' === $this->isbn ) {
				return;
			}

			global $wpdb;

			
			// Before we do anything else, let's make sure we don't have a saved transient for this book - if we do, no sense in making a new api call - will cut down on requests. Also, do not use a transient at all if we're editing a book, and try to delete an existing transient in the 'else' part before creating a new one.
			$transient_name   = 'wpbl_' . md5( $this->isbn . '_itunes' );
			$transient_exists = $this->transients->existing_transient_check( $transient_name );
			if ( $transient_exists && 'edit' !== $this->action ) {
				$this->itunesapiresult = $transient_exists;
				$this->itunes_transient_use = 'Yes';
			} else {

				$status                     = '';
				$this->itunesapiresult      = '';
				$this->itunesapiresult      = wp_remote_get( 'https://itunes.apple.com/lookup?isbn=' . $this->isbn . '&at=' . $this->options_results->itunesaff );
				$this->itunes_transient_use = 'No';

				// Check the response code.
				$response_code    = wp_remote_retrieve_response_code( $this->itunesapiresult );
				$response_message = wp_remote_retrieve_response_message( $this->itunesapiresult );

				if ( 200 !== $response_code && ! empty( $response_message ) ) {
					$this->apireport = $this->apireport . 'Looks like we tried the itunes wp_remote_get function, but something went wrong .  Status Code is: ' . $response_code . ' and Response Message is: ' . $response_message . ' .  URL Request was: https://itunes.apple.com/lookup?isbn=' . $this->isbn . '&at=' . $this->options_results->itunesaff;
					return new WP_Error( $response_code, $response_message );
				} elseif ( 200 !== $response_code ) {
					$this->apireport = $this->apireport . 'Unknown error occurred with the itunes wp_remote_get function';
					return new WP_Error( $response_code, 'Unknown error occurred with the itunes wp_remote_get function' );
				} else {
					$this->apireport       = $this->apireport . 'iTunes API call via wp_remote_get looks to be successful.  URL Request was: https://itunes.apple.com/lookup?isbn=' . $this->isbn . '&at=' . $this->options_results->itunesaff;
					$this->itunesapiresult = wp_remote_retrieve_body( $this->itunesapiresult );
				}

				// Actually attempting to delete existing transients before creation of new one.
				$transient_delete_api_data_result       = $this->transients->delete_transient( $transient_name );
				$this->transient_create_result = $this->transients->create_api_transient( $transient_name, $this->itunesapiresult, WEEK_IN_SECONDS );
			}

			if ( '' !== $this->itunesapiresult ) {
				$json_output_itunes = json_decode( $this->itunesapiresult, true );

				if ( is_array( $json_output_itunes ) && array_key_exists( 'resultCount', $json_output_itunes ) && 0 !== $json_output_itunes['resultCount'] ) {
					$this->apireport = $this->apireport . ' iTunes iBooks Array conversion went well. ';
				} else {

					if ( ! is_array( $json_output_itunes ) ) {
						$this->apireport = $this->apireport . 'Looks like results may or may not have been returned from iTunes iBooks, but either way, something went wrong with converting the result to an array. ';
					} else {
						$this->apireport = $this->apireport . 'Looks like the conversion to an array from iTunes iBooks was successful, but it doesn\'t contain any data - book can\'t be found via iTunes iBooks API. ';
					}
				}

				if ( '' === $this->itunesbuylink || null === $this->itunesbuylink ) {
					if ( null !== $json_output_itunes && is_array( $json_output_itunes ) && array_key_exists( 'results', $json_output_itunes ) && array_key_exists( 0, $json_output_itunes ) && array_key_exists( 'trackViewUrl', $json_output_itunes ) ) {
						$this->itunes_page = $json_output_itunes['results'][0]['trackViewUrl'];
					}
				} else {
					$this->itunes_page = $this->itunesbuylink;
				}
			}

			// If we didn't find the book via iBooks, let's search for the Audiobook via itunes.
			if ( null === $this->itunes_page || '' === $this->itunes_page ) {

				// Before we do anything else, let's make sure we don't have a saved transient for this book - if we do, no sense in making a new api call - will cut down on requests. Also, do not use a transient at all if we're editing a book, and try to delete an existing transient in the 'else' part before creating a new one.
				$transient_name   = 'wpbl_' . md5( $this->isbn . '_itunesaudio' );
				$transient_exists = $this->transients->existing_transient_check( $transient_name );
				if ( $transient_exists && 'edit' !== $this->action ) {
					$this->itunes_audio_transient_use = $transient_exists;
					$this->itunes_audio_transient_use = 'Yes';
				} else {

					$status                           = '';
					$this->itunesapiresult            = '';
					$this->itunesapiresult            = wp_remote_get( 'https://itunes.apple.com/search?term=' . $this->title . '&at=' . $this->options_results->itunesaff );
					$this->itunes_audio_transient_use = 'No';

					// Check the response code.
					$response_code    = wp_remote_retrieve_response_code( $this->itunesapiresult );
					$response_message = wp_remote_retrieve_response_message( $this->itunesapiresult );

					if ( 200 !== $response_code && ! empty( $response_message ) ) {
						$this->apireport = $this->apireport . 'Looks like we tried the itunes audiobook wp_remote_get function, but something went wrong .  Status Code is: ' . $response_code . ' and Response Message is: ' . $response_message . ' .  URL Request was: https://itunes.apple.com/search?term=' . $this->title . '&at=' . $this->options_results->itunesaff;
						return new WP_Error( $response_code, $response_message );
					} elseif ( 200 !== $response_code ) {
						$this->apireport = $this->apireport . 'Unknown error occurred with the itunes audiobook wp_remote_get function';
						return new WP_Error( $response_code, 'Unknown error occurred with the itunes audiobook wp_remote_get function' );
					} else {
						$this->apireport       = $this->apireport . 'iTunes audiobook API call via wp_remote_get looks to be successful.  URL Request was: https://itunes.apple.com/search?term=' . $this->title . '&at=' . $this->options_results->itunesaff;
						$this->itunesapiresult = wp_remote_retrieve_body( $this->itunesapiresult );
					}

					// Actually attempting to delete existing transients before creation of new one.
					$transient_delete_api_data_result       = $this->transients->delete_transient( $transient_name );
					$this->transient_create_result = $this->transients->create_api_transient( $transient_name, $this->itunesapiresult, WEEK_IN_SECONDS );
				}

				$json_output_itunes = json_decode( $this->itunesapiresult, true );

				if ( is_array( $json_output_itunes ) && array_key_exists( 'resultCount', $json_output_itunes ) && 0 !== $json_output_itunes['resultCount'] ) {
					$this->apireport = $this->apireport . ' iTunes Audiobooks Array conversion went well. ';
				} else {

					if ( ! is_array( $json_output_itunes ) ) {
						$this->apireport = $this->apireport . 'Looks like results may or may not have been returned from iTunes Audiobooks, but either way, something went wrong with converting the result to an array. ';
					} else {
						$this->apireport = $this->apireport . 'Looks like the conversion to an array from iTunes Audiobooks was successful, but it doesn\'t contain any data - book can\'t be found via iTunes Audiobooks API. ';
					}
				}

				if ( null !== $json_output_itunes && is_array( $json_output_itunes ) && array_key_exists( 'results', $json_output_itunes ) && array_key_exists( 0, $json_output_itunes ) && array_key_exists( 'trackViewUrl', $json_output_itunes ) ) {
					$this->itunes_page = $json_output_itunes['results'][0]['trackViewUrl'];
				}
			}

			// Create report of what values were found and what weren't.
			if ( null !== $this->itunes_page && '' !== $this->itunes_page && '' === $this->whichapifound['itunes_page'] ) {
				$this->whichapifound['itunes_page'] = 'iTunes iBooks';
			}
		}



		/**
		 * Function to handle the gathering of Goodreads data.
		 */
		private function gather_goodreads_data() {

			global $wpdb;

			if ( '' !== $this->isbn && null !== $this->isbn ) {
				$this->apireport = $this->apireport . 'Results for "' . $this->isbn . '": ';
			} elseif ( '' !== $this->title && null !== $this->title ) {
				$this->apireport = $this->apireport . 'Results for "' . $this->title . '": ';
			} else {
				$this->apireport = $this->apireport . 'Results for Unknown Book: ';
			}

			// Before we do anything else, let's make sure we don't have a saved transient for this book - if we do, no sense in making a new api call - will cut down on requests. Also, do not use a transient at all if we're editing a book, and try to delete an existing transient in the 'else' part before creating a new one.
			$transient_name   = 'wpbl_' . md5( $this->isbn . '_goodreads' );
			$transient_exists = $this->transients->existing_transient_check( $transient_name );
			if ( $transient_exists && 'edit' !== $this->action ) {
				$this->goodreadsapiresult      = $transient_exists;
				$this->goodreads_transient_use = 'Yes';

			} else {

				$status                        = '';
				$this->goodreadsapiresult      = '';
				$this->goodreadsapiresult      = wp_remote_get( 'https://sublime-vine-199216.appspot.com/?whichapi=goodreads&key=LgBmcNLBTzCrOxIF4O7R6g&q=' . $this->isbn );
				$this->goodreads_transient_use = 'No';

				//error_log( print_r($this->goodreadsapiresult,true) );

				// Check the response code.
				$response_code    = wp_remote_retrieve_response_code( $this->goodreadsapiresult );
				$response_message = wp_remote_retrieve_response_message( $this->goodreadsapiresult );

				if ( 200 !== $response_code && ! empty( $response_message ) ) {

					$this->apigoodreadsfailcount++;

					// Let's try this 2 more times, one for ISBN13, and one for ASIN, if they exist.
					if ( 'isbn-isbn13-asin' !== $this->gather_goodreads_attempt_with ) {

						if ( 'isbn' === $this->gather_goodreads_attempt_with ) {
							$this->gather_goodreads_attempt_with = 'isbn-isbn13';
							$this->isbn                          = $this->isbn13;
							$this->gather_goodreads_data();
						}

						if ( 'isbn-isbn13' === $this->gather_goodreads_attempt_with ) {
							$this->gather_goodreads_attempt_with = 'isbn-isbn13-asin';
							$this->isbn                          = $this->asin;
							$this->gather_goodreads_data();
						}
					}

					$this->apireport = $this->apireport . 'Looks like we tried the Goodreads wp_remote_get function, but something went wrong .  Status Code is: ' . $response_code . ' and Response Message is: ' . $response_message . ' .  URL Request was: https://wpbooklist.com/awsapiconfig.php?key=LgBmcNLBTzCrOxIF4O7R6g&q=' . $this->isbn . ' ';
					return new WP_Error( $response_code, $response_message );
				} elseif ( 200 !== $response_code ) {
					$this->apireport = $this->apireport . 'Unknown error occurred with the Goodreads wp_remote_get function';
					return new WP_Error( $response_code, 'Unknown error occurred with the Goodreads wp_remote_get function' );
				} else {
					$this->apireport          = $this->apireport . 'Goodreads API call via wp_remote_get looks to be successful.  URL Request was: https://wpbooklist.com/awsapiconfig.php?key=LgBmcNLBTzCrOxIF4O7R6g&q=' . $this->isbn . ' ';
					$this->goodreadsapiresult = wp_remote_retrieve_body( $this->goodreadsapiresult );
				}

				// Actually attempting to delete existing transients before creation of new one.
				$transient_delete_api_data_result = $this->transients->delete_transient( $transient_name );
				$this->transient_create_result    = $this->transients->create_api_transient( $transient_name, $this->goodreadsapiresult, WEEK_IN_SECONDS );
			}

			// Convert result from API call to regular ol' array.
			if ( 3 > $this->apigoodreadsfailcount ) {

				$xml = simplexml_load_string( $this->goodreadsapiresult, 'SimpleXMLElement', LIBXML_NOCDATA );

				// Checking to see if the XML conversion was successful.
				if ( false === $xml ) {
					$this->apireport = $this->apireport . 'Looks like something went wrong with converting the Goodreads API result to XML. ';
				} else {
					$this->apireport = $this->apireport . 'Goodreads XML conversion went well. ';

					// Convert XML to array.
					$json                  = wp_json_encode( $xml );
					$this->goodreads_array = json_decode( $json, true );

					//error_log( print_r($this->goodreads_array , true ) );

					// Now check and see if the converted XML contains any error report, and set the error flag if so.
					$error_flag = false;

					// If $error_flag is false,  begin assigning values from $this->goodreads_array to properties.
					if ( ! $error_flag ) {

						if ( null === $this->author || '' === $this->author ) {
							if ( array_key_exists( 'search', $this->goodreads_array )
								&& array_key_exists( 'results', $this->goodreads_array['search'] )
								&& array_key_exists( 'work', $this->goodreads_array['search']['results'] )
								&& array_key_exists( 'best_book', $this->goodreads_array['search']['results']['work'] )
								&& array_key_exists( 'author', $this->goodreads_array['search']['results']['work']['best_book'] )
								&& array_key_exists( 'name', $this->goodreads_array['search']['results']['work']['best_book']['author'] ) ) {
									$this->author = $this->goodreads_array['search']['results']['work']['best_book']['author']['name'];
							}
						}

						//if ( null === $this->image || '' === $this->image ) {
							if ( array_key_exists( 'search', $this->goodreads_array )
								&& array_key_exists( 'results', $this->goodreads_array['search'] )
								&& array_key_exists( 'work', $this->goodreads_array['search']['results'] )
								&& array_key_exists( 'best_book', $this->goodreads_array['search']['results']['work'] )
								&& array_key_exists( 'image_url', $this->goodreads_array['search']['results']['work']['best_book'] ) ) {
									$this->image = $this->goodreads_array['search']['results']['work']['best_book']['image_url'];
									$this->image = explode( '/books', $this->image );
									$this->image = str_replace( 'm/', 'l/', $this->image[1] );
									$this->image = 'https://images.gr-assets.com/books' . $this->image;
							}
						//}

						if ( null !== $this->title || '' !== $this->title ) {
							if ( array_key_exists( 'search', $this->goodreads_array )
								&& array_key_exists( 'results', $this->goodreads_array['search'] )
								&& array_key_exists( 'work', $this->goodreads_array['search']['results'] )
								&& array_key_exists( 'best_book', $this->goodreads_array['search']['results']['work'] )
								&& array_key_exists( 'title', $this->goodreads_array['search']['results']['work']['best_book'] ) ) {
									$this->title = $this->goodreads_array['search']['results']['work']['best_book']['title'];
							}
						}
					}
				}
			} else {

				if ( $this->rerun_goodreads_flag ) {
					sleep( 1 );
					$this->rerun_goodreads_flag  = false;
					$this->apigoodreadsfailcount = 0;
					$this->gather_goodreads_data();
				}
			}

			// Create report of what values were found and what weren't.
			if ( null !== $this->title && '' !== $this->title ) {
				$this->whichapifound['title'] = 'Goodreads';
			}

			if ( null !== $this->image && '' !== $this->image ) {
				$this->whichapifound['image'] = 'Goodreads';
			}

			if ( null !== $this->author && '' !== $this->author ) {
				$this->whichapifound['author'] = 'Goodreads';
			}

		}

















		/**
		 * Function to handle the setting of default WooCommerce data for creation of WooCommerce products.
		 */
		private function set_default_woocommerce_data() {
			global $wpdb;

			// Check to see if Storefront extension is active.
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
			if ( is_plugin_active( 'wpbooklist-storefront/wpbooklist-storefront.php' ) ) {

				// Get saved settings.
				$settings_table = $wpdb->prefix . 'wpbooklist_storefront_settings';
				$settings       = $wpdb->get_row( "SELECT * FROM $settings_table" );

				if ( '' === $this->saleprice || null === $this->saleprice ) {
					$this->saleprice = $settings->defaultsaleprice;
				}

				if ( '' === $this->regularprice || null === $this->regularprice ) {
					$this->regularprice = $settings->defaultregularprice;
				}

				if ( '' === $this->stock || null === $this->stock ) {
					$this->stock = $settings->defaultstock;
				}

				if ( '' === $this->length || null === $this->length ) {
					$this->length = $settings->defaultlength;
				}

				if ( '' === $this->width || null === $this->width ) {
					$this->width = $settings->defaultwidth;
				}

				if ( '' === $this->height || null === $this->height ) {
					$this->height = $settings->defaultheight;
				}

				if ( '' === $this->weight || null === $this->weight ) {
					$this->weight = $settings->defaultweight;
				}

				if ( '' === $this->sku || null === $this->sku ) {
					$this->sku = $settings->defaultsku;
				}

				if ( '' === $this->virtual || null === $this->virtual ) {
					$this->virtual = $settings->defaultvirtual;
				}

				if ( '' === $this->download || null === $this->download ) {
					$this->download = $settings->defaultdownload;
				}

				if ( '-undefined-undefined' === $this->salebegin || null === $this->salebegin ) {
					$this->salebegin = $settings->defaultsalebegin;
				}

				if ( '-undefined-undefined' === $this->saleend || null === $this->saleend ) {
					$this->saleend = $settings->defaultsaleend;
				}

				if ( '' === $this->purchasenote || null === $this->purchasenote ) {
					$this->purchasenote = $settings->defaultnote;
				}

				if ( '' === $this->productcategory || null === $this->productcategory ) {
					$this->productcategory = $settings->defaultcategory;
				}

				if ( '' === $this->upsells || null === $this->upsells ) {
					$this->upsells = $settings->defaultupsell;
				}

				if ( '' === $this->crosssells || null === $this->crosssells ) {
					$this->crosssells = $settings->defaultcrosssell;
				}
			}
		}

		/**
		 * Function to handle the actual creation of WooCommerce products.
		 */
		private function create_wpbooklist_woocommerce_product() {

			global $wpdb;

			if ( 'Yes' === $this->woocommerce ) {
				$price = '';
				if ( null !== $this->price && '' !== $this->price ) {
					if ( ! is_numeric( $this->price[0] ) ) {
						$price = substr( $this->price, 1 );
					} else {
						$price = $this->price;
					}
				} else {
					if ( null !== $this->regularprice && '' !== $this->regularprice ) {
						if ( ! is_numeric( $this->regularprice[0] ) ) {
							$price = substr( $this->regularprice, 1 );
						} else {
							$price = $this->regularprice;
						}
					} else {
						$price = '0.00';
					}
				}

				$this->book_array['price'] = $price;

				$woocommerce_existing_id = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->library WHERE ID = %d", $this->id ) );

				$existingid = null;
				if ( null !== $woocommerce_existing_id ) {
					$existingid = $woocommerce_existing_id->woocommerce;
				}

				include_once STOREFRONT_CLASS_DIR . 'class-wpbooklist-storefront-woocommerce.php';
				$this->woocommerce = new WPBookList_StoreFront_WooCommerce( $this->book_array, $existingid, $this->title, $this->description, $this->image, $this->upsells, $this->crosssells );

				$this->wooid = $this->woocommerce->post_id;

			}
		}

		/**
		 * Function to handle the formatting of an Author's First and Last names.
		 */
		private function create_author_first_last() {

			$title_array = array(
				'Jr. ',
				'Ph.D. ',
				'Mr. ',
				'Mrs. ',
			);

			$title                       = '';
			$origauthorname              = $this->author;
			$this->finalauthorlastnames  = '';
			$this->finalauthorfirstnames = '';

			// First let's handle names with commas, which we'll assume indicates multiple authors.
			if ( false !== strpos( $origauthorname, ',' ) && '' === $this->finalauthorlastnames && '' === $this->finalauthorfirstnames ) {
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

					if ( '' !== $title ) {
						$firstnamecolonstring = $firstnamecolonstring . ';' . $firstname . ' ' . $title;
					} else {
						$firstnamecolonstring = $firstnamecolonstring . ';' . $firstname;
					}
				}

				// trim left spaces and ;.
				$lastnamecolonstring = ltrim( $lastnamecolonstring, ' ' );
				$lastnamecolonstring = ltrim( $lastnamecolonstring, ';' );

				// trim left spaces and ;.
				$firstnamecolonstring = ltrim( $firstnamecolonstring, ' ' );
				$firstnamecolonstring = ltrim( $firstnamecolonstring, ';' );

				// Now build finalfirstname and finallastname string for the two new db columns.
				$this->finalauthorlastnames  = $lastnamecolonstring;
				$this->finalauthorfirstnames = $firstnamecolonstring;
			}

			// Next we'll handle the names of single authors who may have a title in their name.
			foreach ( $title_array as $titlekey => $titlevalue ) {

				// If author name has a title in it, and does not have a comma (indicating multiple authors ), then proceed.
				if ( '' === $this->finalauthorlastnames && '' === $this->finalauthorfirstnames && false !== stripos( $origauthorname, $titlevalue ) && false === stripos( $origauthorname, ',' ) ) {
					$tempname = str_replace( $titlevalue, '', $origauthorname );
					$tempname = rtrim( $tempname, ' ' );
					$title    = $titlevalue;

					// Now split up first/last names.
					$this->finalauthorfirstnames = implode( ' ', explode( ' ', $tempname, -1 ) ) . ' ' . $titlevalue;
					$temp                        = explode( ' ', strrev( $tempname ), 2 );
					$this->finalauthorlastnames  = strrev( $temp[0] );

				}
			}

			// Now if the Author's name does not contain a comma or a title...
			foreach ( $title_array as $titlekey => $titlevalue ) {

				// If author name does not have a title in it, and does not have a comma (indicating multiple authors ), then proceed.
				if ( '' === $this->finalauthorlastnames && '' === $this->finalauthorfirstnames && false === stripos( $origauthorname, $titlevalue ) && false === stripos( $origauthorname, ',' ) ) {

					// Now split up first/last names.
					$this->finalauthorfirstnames = implode( ' ', explode( ' ', $origauthorname, -1 ) );
					$temp                        = explode( ' ', strrev( $origauthorname ), 2 );
					$this->finalauthorlastnames  = strrev( $temp[0] );
				}
			}
		}

		/**
		 * Function to handle the Similar books stuff - account for if the user has specificed similar books, and if not, replace with what might have been found by Amazon.
		 */
		private function create_similar_books() {

			global $wpdb;

			$similar_string = ';bsp;';
			if ( false !== stripos( $this->similarbooks, '---' ) ) {
				$similarbooks   = explode( '---', $this->similarbooks );
				foreach ( $similarbooks as $key => $book ) {

					if ( '' !== $book && false !== stripos( $book, ';' ) ) {
						$split_book = explode( ';', $book );

						// Get book from DB.
						$bookinfo = $wpdb->get_row( 'SELECT * FROM ' . $split_book[1] . " WHERE book_uid = '" . $split_book[0] . "'" );

						$final_isbn = $bookinfo->asin . 'asin';
						if ( 'asin' === $final_isbn ) {
							$final_isbn = $bookinfo->isbn;
						}
						if ( null === $final_isbn || '' === $final_isbn ) {
							$final_isbn = $bookinfo->isbn13;
						}

						// Append the Post or Page ID, if one exists.
						if ( null !== $bookinfo->post_yes && '' !== $bookinfo->post_yes ) {
							$similar_string = $similar_string . $final_isbn . '---' . $bookinfo->title . '---' . $bookinfo->image . '---' . $bookinfo->post_yes . ';bsp;';
						} elseif ( null !== $bookinfo->page_yes && '' !== $bookinfo->page_yes ) {
							$similar_string = $similar_string . $final_isbn . '---' . $bookinfo->title . '---' . $bookinfo->image . '---' . $bookinfo->page_yes . ';bsp;';
						} else {
							$similar_string = $similar_string . $final_isbn . '---' . $bookinfo->title . '---' . $bookinfo->image . '---;bsp;';
						}
					}
				}
			}

			$similar_string = rtrim( $similar_string, ';bsp;' );

			// Now replace the Amazon-found Similar Products with this string, if it doesn't equal '' or ;bsp;...
			if ( '' !== $similar_string && ';bsp;' !== $similar_string ) {
				$this->similar_products = $similar_string;
			}
		}

		/**
		 * Function to handle actually adding the book to the Databas.
		 */
		private function add_to_db() {

			$post = null;
			$page = null;

			// Create a unique identifier for this book.
			$this->book_uid = uniqid();

			if ( 'Yes' === $this->page_yes || 'Yes' === $this->post_yes ) {
				$page_post_array = array(
					'library'            => $this->library,
					'amazonauth'         => $this->amazonauth,
					'use_amazon_yes'     => $this->use_amazon_yes,
					'title'              => $this->title,
					'isbn'               => $this->isbn,
					'author'             => $this->author,
					'author_url'         => $this->author_url,
					'sale_url'           => $this->sale_url,
					'price'              => $this->price,
					'finished'           => $this->finished,
					'date_finished'      => $this->date_finished,
					'signed'             => $this->signed,
					'first_edition'      => $this->first_edition,
					'image'              => $this->image,
					'pages'              => $this->pages,
					'pub_year'           => $this->pub_year,
					'publisher'          => $this->publisher,
					'category'           => $this->category,
					'subject'            => $this->subject,
					'country'            => $this->country,
					'description'        => $this->description,
					'notes'              => $this->notes,
					'rating'             => $this->rating,
					'page_yes'           => $this->page_yes,
					'post_yes'           => $this->post_yes,
					'itunes_page'        => $this->itunes_page,
					'google_preview'     => $this->google_preview,
					'amazon_detail_page' => $this->amazon_detail_page,
					'review_iframe'      => $this->review_iframe,
					'similar_products'   => $this->similar_products,
					'book_uid'           => $this->book_uid,
					'lendable'           => $this->lendable,
					'copies'             => $this->copies,
					'kobo_link'          => $this->kobo_link,
					'bam_link'           => $this->bam_link,
					'woocommerce'        => $this->wooid,
					'authorfirst'        => $this->finalauthorfirstnames,
					'authorlast'         => $this->finalauthorlastnames,
				);

				// Each of these class instantiations will return the ID of the page/post created for storage in DB.
				$page = $this->page_yes;
				$post = $this->post_yes;

				if ( 'Yes' === $this->post_yes ) {
					require_once CLASS_POST_DIR . 'class-wpbooklist-post.php';
					$post = new WPBookList_Post( $page_post_array );
					$post = $post->post_id;
				}

				if ( 'Yes' === $this->page_yes ) {
					require_once CLASS_PAGE_DIR . 'class-wpbooklist-page.php';
					$page = new WPBookList_Page( $page_post_array );
					$page = $page->create_result;
				}
			}

			// Check to see if Storefront extension is active.
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
			if ( is_plugin_active( 'wpbooklist-storefront/wpbooklist-storefront.php' ) ) {
				if ( '' === $this->author_url || null === $this->author_url ) {
					if ( '' !== $this->wooid || null !== $this->wooid ) {
						$this->author_url = get_permalink( $this->wooid );

						if ( null === $this->price || '' === $this->price ) {
							$this->price = $this->regularprice;
						}
					}
				}
			}

			// Add the Categories into the Genres...
			if ( '' !== $this->category && null !== $this->category ) {

				// If it's not already in the genres string...
				if ( false === stripos( $this->genres, $this->category ) ) {
					$this->genres = rtrim( $this->genres, '---' );
					$this->genres = $this->genres  . '---' . $this->category;
				}
			}

			// Building array to add to DB.
			$this->db_insert_array = array(
				'additionalimage1'   => $this->additionalimage1,
				'additionalimage2'   => $this->additionalimage2,
				'amazon_detail_page' => $this->amazon_detail_page,
				'appleibookslink'    => $this->appleibookslink,
				'asin'               => $this->asin,
				'author'             => $this->author,
				'author2'            => $this->author2,
				'author3'            => $this->author3,
				'author_url'         => $this->author_url,
				'sale_url'           => $this->sale_url,
				'price'              => $this->price,
				'backcover'          => $this->backcover,
				'bam_link'           => $this->bam_link,
				'bn_link'            => $this->bn_link,
				'book_uid'           => $this->book_uid,
				'callnumber'         => $this->callnumber,
				'category'           => $this->category,
				'copies'             => $this->copies,
				'country'            => $this->country,
				'date_finished'      => $this->date_finished,
				'description'        => $this->description,
				'edition'            => $this->edition,
				'finished'           => $this->finished,
				'first_edition'      => $this->first_edition,
				'format'             => $this->format,
				'genres'             => $this->genres,
				'goodreadslink'      => $this->goodreadslink,
				'google_preview'     => $this->google_preview,
				'illustrator'        => $this->illustrator,
				'image'              => $this->image,
				'isbn'               => $this->isbn,
				'isbn13'             => $this->isbn13,
				'keywords'           => $this->keywords,
				'kobo_link'          => $this->kobo_link,
				'language'           => $this->language,
				'notes'              => $this->notes,
				'numberinseries'     => $this->numberinseries,
				'othereditions'      => $this->othereditions,
				'originalpubyear'    => $this->originalpubyear,
				'originaltitle'      => $this->originaltitle,
				'outofprint'         => $this->outofprint,
				'page_yes'           => $page,
				'pages'              => $this->pages,
				'post_yes'           => $post,
				'pub_year'           => $this->pub_year,
				'publisher'          => $this->publisher,
				'rating'             => $this->rating,
				'review_iframe'      => $this->review_iframe,
				'series'             => $this->series,
				'shortdescription'   => $this->shortdescription,
				'signed'             => $this->signed,
				'similarbooks'       => $this->similarbooks,
				'similar_products'   => $this->similar_products,
				'subgenre'           => $this->subgenre,
				'subject'            => $this->subject,
				'title'              => $this->title,
				'woocommerce'        => $this->wooid,
				'ebook'              => $this->ebook,
			);

			// Building mask array to add to DB.
			$db_mask_insert_array = array(
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%d',
				'%s',
				'%f',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
			);

			// Now adding in any custom fields to above arrays for insertion into DB.
			global $wpdb;
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
							$this->db_insert_array[ $entry_details[0] ] = $this->book_array[ $entry_details[0] ];

							// Adding a mask for new value.
							array_push( $db_mask_insert_array, '%s' );
						}
					}
				}
			}

			// Actually Adding submitted values to the DB.
			global $wpdb;

			if ( 'search' !== $this->action ) {
				$result = $wpdb->insert( $this->library, $this->db_insert_array, $db_mask_insert_array );
			}

			$this->add_result = $result;
			if ( 1 === $result ) {
				$row              = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->library WHERE book_uid = %s", $this->book_uid ) );
				$this->add_result = $this->add_result . ',' . $row->ID;
			} else {
				$this->add_result = $this->add_result . ',' . $wpdb->last_error;

			}

			// Insert the Amazon Authorization into the DB if it's not already set to 'Yes' .
			if ( 'true' !== $this->options_results->amazonauth ) {
				$data         = array(
					'amazonauth' => $this->amazonauth,
				);
				$format       = array( '%s' );
				$where        = array( 'ID' => 1 );
				$where_format = array( '%d' );
				$wpdb->update( $wpdb->prefix . 'wpbooklist_jre_user_options', $data, $where, $format, $where_format );
			}

			// Now delete the user options transient.
			$transient_user_options_name = 'wpbl_' . md5( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_user_options' );
			// Actually attempting to delete existing transients before creation of new one.
			$transient_delete_colorbox_result = $this->transients->delete_transient( $transient_user_options_name );

		}

		/**
		 * Function to handle displaying the 'Edit Book' form.
		 */
		public static function display_edit_book_form() {

			// Perform check for previously-saved Amazon Authorization.
			global $wpdb;

			$table_name = $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names';
			$db_row = $wpdb->get_results( "SELECT * FROM $table_name" );

			// For grabbing an image from media library.
			wp_enqueue_media();
			$string1 = '<div id="wpbooklist-editbook-container">
					<p><span ';

			if ( 'true' === $opt_results->amazonauth ) {
				$string2 = 'style="display:none;"';
			} else {
				$string2 = '';
			}

			$string3 = ' >You must check the box below to authorize <span class="wpbooklist-color-orange-italic">WPBookList</span> to gather data from Amazon, otherwise, the only data that will be added for your book is what you fill out on the form below. WPBookList uses it\'s own Amazon Product Advertising API keys to gather book data, but if you happen to have your own API keys, you can use those instead by adding them on the <a href="' . menu_page_url( 'WPBookList-Options-settings', false ) . '&tab=amazon">Amazon Settings</a> page.</span></p>
					<form id="wpbooklist-editbook-form" method="post" action="">
						<div id="wpbooklist-authorize-amazon-container">
							<table>';

			if ( 'true' === $opt_results->amazonauth ) {
				$string4 = '<tr style="display:none;"">
					<td><p id="auth-amazon-question-label">Authorize Amazon Usage?</p></td>
				</tr>
				<tr style="display:none;"">
					<td>
						<input checked type="checkbox" name="authorize-amazon-yes" />
						<label for="authorize-amazon-yes">Yes</label>
						<input type="checkbox" name="authorize-amazon-no" />
						<label for="authorize-amazon-no">No</label>
					</td>
				</tr>';
			} else {
				$string4 = '<tr>
					<td><p id="auth-amazon-question-label">Authorize Amazon Usage?</p></td>
				</tr>
				<tr>
					<td>
						<input type="checkbox" name="authorize-amazon-yes" />
						<label for="authorize-amazon-yes">Yes</label>
						<input type="checkbox" name="authorize-amazon-no" />
						<label for="authorize-amazon-no">No</label>
					</td>
				</tr>';
			}

			$string5 = '</table>
						</div>
						<div id="wpbooklist-use-amazon-container">
							<table>
								<tr>
									<td><p id="use-amazon-question-label">Automatically Gather Book Info From Amazon ( ISBN/ASIN number required )?</p></td>
								</tr>
								<tr>
									<td style="text-align:center;">
										<input checked type="checkbox" name="use-amazon-yes" />
										<label for="use-amazon-yes">Yes</label>
										<input type="checkbox" name="use-amazon-no" />
										<label for="use-amazon-no">No</label>
									</td>
								</tr>
							</table>
						</div>
						<table>
							<tbody>
								<tr>
								<td>
									<label for="isbn">ISBN/ASIN: </label>
								</td>
								<td>
									<label id="wpbooklist-editbook-label-booktitle" for="book-title">Book Title:</label>
								</td>
								<td>
									<label for="book-author">Author: </label>
								</td>
								<td>
									<label for="book-category">Category: </label><br>
								</td>
								</tr>
								<tr>
									<td>
										<input type="text" id="wpbooklist-editbook-isbn" name="book-isbn">
									</td>
									<td>
										<input type="text" id="wpbooklist-editbook-title" name="book-title" size="30">
									</td>
									<td>
										<input type="text" id="wpbooklist-editbook-author" name="book-author" size="30">
									</td>
									<td>
										<input type="text" id="wpbooklist-editbook-category" name="book-category" size="30">
									</td>
								</tr>
								<tr>
									<td>
										<label for="book-pages">' . __( 'Pages:', 'wpbooklist' ) . ' </label><br>
									</td>
									<td>
										<label for="book-pubdate">' . __( 'Publication Year:', 'wpbooklist' ) . ' </label><br>
									</td>
									<td>
										<label for="book-publisher">' . __( 'Publisher:', 'wpbooklist' ) . ' </label><br>
									</td>
									<td>
										<label for="book-subject">' . __( 'Subject:', 'wpbooklist' ) . ' </label><br>
									</td>
								</tr>
								<tr>
									<td>
										<input type="number" id="wpbooklist-editbook-pages" name="book-pages" size="30">
									</td>
									<td>
										<input type="text" id="wpbooklist-editbook-pubdate" name="book-pubdate" size="30">
									</td>
									<td>
										<input type="text" id="wpbooklist-editbook-publisher" name="book-publisher" size="30">
									</td>
									<td>
										<input type="text" id="wpbooklist-editbook-subject" name="book-subject" size="30">
									</td>
								</tr>
								<tr>
									<td>
										<label for="book-country">' . __( 'Country:', 'wpbooklist' ) . ' </label><br>
									</td>
								</tr>
								<tr>
									<td>
										<input type="text" id="wpbooklist-editbook-country" name="book-country" size="30">
									</td>
								</tr>
								<tr id="wpbooklist-addbook-page-post-create-label-row">
									<td colspan="2">
										<label class="wpbooklist-editbook-page-post-label" for="book-indiv-page">Create Individual Page?</label><br>
									</td>
									<td colspan="2">
										<label class="wpbooklist-editbook-page-post-label" for="book-indiv-post">Create Individual Post? </label><br>
									</td>
								</tr>
								<tr id="wpbooklist-editbook-page-post-row">
								<td colspan="2" class="wpbooklist-editbook-post-page-checkboxes">
									<input type="checkbox" id="wpbooklist-editbook-page-yes" name="book-indiv-page-yes" value="yes"/><label>Yes</label>
									<input type="checkbox" id="wpbooklist-editbook-page-no" name="book-indiv-page-no" value="no"/><label>No</label>
								</td>
								<td colspan="2" class="wpbooklist-editbook-post-page-checkboxes">
									<input type="checkbox" id="wpbooklist-editbook-post-yes" name="book-indiv-post-yes" value="yes"/><label>Yes</label>
									<input type="checkbox" id="wpbooklist-editbook-post-no" name="book-indiv-post-no" value="no"/><label>No</label>
								</td>
								</tr>
								<tr>
									<td colspan="2">
										<label for="book-description">Description (accepts html ): </label><br>
									</td>
									<td colspan="2">
										<label for="book-notes">Notes (accepts html ):</label><br>
									</td>
								</tr>
								<tr>
								<td colspan="2">
									<textarea id="wpbooklist-editbook-description" name="book-description" rows="3" size="30"></textarea>
								</td>
								<td colspan="2">
									<textarea id="wpbooklist-editbook-notes" name="book-notes" rows="3" size="30"></textarea>
								</td>
								</tr>
								<tr>
									<td colspan="2">
										<label for="book-rating">Rate This Title: </label><img id="wpbooklist-editbook-rating-img" src="' . ROOT_IMG_URL . '5star.png" /><br>
									</td>
									<td colspan="2">
										<label id="wpbooklist-editbook-image-label" for="book-image">Cover Image:</label><input id="wpbooklist-editbook-upload_image_button" type="button" value="Choose Image"/><br>
									</td>
								</tr>
								<tr>
									<td colspan="2" style="vertical-align:top">
										<select id="wpbooklist-editbook-rating">
											<option selected>
												Select a Rating ...
											</option>
											<option value="5">
												5 Stars
											</option>
											<option value="4">
												4 Stars
											</option>
											<option value="3">
												3 Stars
											</option>
											<option value="2">
												2 Stars
											</option>
											<option value="1">
												1 Star
											</option>
										</select>
									</td>
									<td colspan="2" style="position:relative">
										<input type="text" id="wpbooklist-editbook-image" name="book-image">
										<img id="wpbooklist-editbook-preview-img" src="' . ROOT_IMG_ICONS_URL . 'book-placeholder.svg" />
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<label for="amazon-purchase-link">Amazon Link: </label><br>
									</td>
									<td colspan="2">
										<label for="bn-link">Barnes & Noble Link:</label><br>
									</td>
								</tr>
								<tr>
								<td colspan="2">
									<input type="text" id="wpbooklist-editbook-amazon-buy-link" name="amazon-purchase-link">
								</td>
								<td colspan="1">
									<input type="text" id="wpbooklist-editbook-bn-link" name="bn-link">
								</td>
								</tr>
								<tr>
									<td colspan="2">
										<label for="google-purchase-link">Google Play Link: </label><br>
									</td>
									<td colspan="2">
										<label for="itunes-link">iTunes Link:</label><br>
									</td>
								</tr>
								<tr>
								<td colspan="2">
									<input type="text" id="wpbooklist-editbook-google-play-buy-link" name="google-purchase-link">
								</td>
								<td colspan="1">
									<input type="text" id="wpbooklist-editbook-itunes-link" name="itunes-link">
								</td>
								</tr>
								<tr>
									<td colspan="2">
										<label for="booksamillion-purchase-link">Books-A-Million Link: </label><br>
									</td>
									<td colspan="2">
										<label for="kobo-link">Kobo Link:</label><br>
									</td>
								</tr>
								<tr>
								<td colspan="2">
									<input type="text" id="wpbooklist-editbook-books-a-million-buy-link" name="booksamillion-purchase-link">
								</td>
								<td colspan="1">
									<input type="text" id="wpbooklist-editbook-kobo-link" name="kobo-link">
								</td>
								</tr>';

			// This filter allows the addition of one or more rows of items into the 'Add A Book' form.
			$string6 = '';
			if ( has_filter( 'wpbooklist_append_to_editbook_form' ) ) {
				$string6 = apply_filters( 'wpbooklist_append_to_editbook_form', $string6 );
			}

			// This filter allows the addition of one or more rows of items into the 'Add A Book' form.
			if ( has_filter( 'wpbooklist_append_to_addbook_form_bookswapper' ) ) {
				$string6 = apply_filters( 'wpbooklist_append_to_addbook_form_bookswapper', $string6 );
			}

			$string7 = '
							</tbody>
						</table>
						<div id="wpbooklist-editbook-signed-first-container">
							<table id="wpbooklist-editbook-signed-first-table">
								<tbody>
									<tr>
										<td><label for="book-date-finished">Have You Finished This Book?</label></td>
										<td><label id="wpbooklist-editbook-signed-question" for="book-signed">Is This Book Signed?</label></td>
										<td><label id="wpbooklist-editbook-first-edition-question" for="book-first-edition">Is it a First Edition?</label></td>
									</tr>
									<tr>
										<td>
											<input type="checkbox" id="wpbooklist-editbook-finished-yes" name="book-finished-yes" value="yes"/><label>Yes</label>
											<input type="checkbox" id="wpbooklist-editbook-finished-no" name="book-finished-no" value="no"/><label>No</label>
										</td>
										<td id="wpbooklist-editbook-signed-td">
											<input type="checkbox" id="wpbooklist-editbook-signed-yes" name="book-signed-yes" value="yes"/><label>Yes</label>
											<input type="checkbox" id="wpbooklist-editbook-signed-no" name="book-signed-no" value="no"/><label>No</label>
										</td>
										<td id="wpbooklist-editbook-firstedition-td">
											<input type="checkbox" id="wpbooklist-editbook-firstedition-yes" name="book-firstedition-yes" value="yes"/><label>Yes</label>
											<input type="checkbox" id="wpbooklist-editbook-firstedition-no" name="book-firstedition-no" value="no"/><label>No</label>
										</td>
										<tr>
											<td id="wpbooklist-editbook-date-finished-td" colspan="3">
												<label for="book-date-finished-text"  id="book-date-finished-label">Date Finished: </label>
												<input name="book-date-finished-text" type="date" id="wpbooklist-editbook-date-finished" />
												<div id="wpbooklist-editbook-add-cancel-div">
													<button type="button" id="wpbooklist-admin-editbook-button">Edit Book</button>
													<button type="button" id="wpbooklist-admin-cancel-button">Cancel</button>
												</div>
												<div class="wpbooklist-spinner" id="wpbooklist-spinner-edit-indiv"></div>
												<div id="wpbooklist-editbook-success-div" data-bookid="" data-booktable="">

												</div>
											</td>
										</tr>
									</tr>
								</tbody>
							</table>
						</div>
					</form>
					<div id="wpbooklist-add-book-error-check" data-add-book-form-error="true" style="display:none" data-></div>
				</div>';

				return $string1 . $string2 . $string3 . $string4 . $string5 . $string6 . $string7;
		}

		/**
		 * Function to handle actually editing a book.
		 */
		private function edit_book() {
			global $wpdb;

			// First do Amazon Authorization check.
			if ( 'true' === $this->amazonauth && 'true' === $this->use_amazon_yes ) {
				$this->go_amazon = true;
				$this->gather_amazon_data();
				$this->gather_google_data();
				$this->gather_open_library_data();
				$this->gather_itunes_data();
				$this->create_wpbooklist_woocommerce_product();
				$this->create_author_first_last();
			} else {

				// If $this->go_amazon is false, query the other apis and add the provided data to database.
				$this->go_amazon = false;
				$this->gather_google_data();
				$this->gather_open_library_data();
				$this->gather_itunes_data();
				$this->create_wpbooklist_woocommerce_product();
				$this->create_author_first_last();
			}

			$page = null;
			$post = null;
			if ( 'Yes' === $this->page_yes || 'Yes' === $this->post_yes ) {

				$page_post_array = array(
					'title'              => $this->title,
					'isbn'               => $this->isbn,
					'author'             => $this->author,
					'author_url'         => $this->author_url,
					'sale_url'           => $this->sale_url,
					'price'              => $this->price,
					'finished'           => $this->finished,
					'date_finished'      => $this->date_finished,
					'signed'             => $this->signed,
					'first_edition'      => $this->first_edition,
					'image'              => $this->image,
					'pages'              => $this->pages,
					'pub_year'           => $this->pub_year,
					'publisher'          => $this->publisher,
					'category'           => $this->category,
					'description'        => $this->description,
					'notes'              => $this->notes,
					'rating'             => $this->rating,
					'page_yes'           => $this->page_yes,
					'post_yes'           => $this->post_yes,
					'itunes_page'        => $this->itunes_page,
					'google_preview'     => $this->google_preview,
					'amazon_detail_page' => $this->amazon_detail_page,
					'review_iframe'      => $this->review_iframe,
					'similar_products'   => $this->similar_products,
					'book_uid'           => $this->book_uid,
					'lendable'           => $this->lendable,
					'copies'             => $this->copies,
					'bn_link'            => $this->bnbuylink,
					'bam_link'           => $this->booksamillionbuylink,
					'kobo_link'          => $this->kobo_link,
				);

				// Each of these class instantiations will return the ID of the page/post created for storage in DB.
				$page = $this->page_id;
				$post = $this->post_id;
				if ( 'Yes' === $this->page_yes ) {
					require_once CLASS_PAGE_DIR . 'class-wpbooklist-page.php';
					$page = new WPBookList_Page( $page_post_array );
					$page = $page->create_result;
				}

				if ( 'Yes' === $this->post_yes ) {
					require_once CLASS_POST_DIR . 'class-wpbooklist-post.php';
					$post = new WPBookList_Post( $page_post_array );
					$post = $post->post_id;
				}
			} else {
				$page = $this->page_yes;
				$post = $this->post_yes;
			}

			// Add the Categories into the Genres...
			if ( '' !== $this->category && null !== $this->category ) {

				// If it's not already in the genres string...
				if ( false === stripos( $this->genres, $this->category ) ) {
					$this->genres = rtrim( $this->genres, '---' );
					$this->genres = $this->genres  . '---' . $this->category;
				}
			}

			// Building array to edit existing book.
			$data_for_edits = array(
				'additionalimage1'   => $this->additionalimage1,
				'additionalimage2'   => $this->additionalimage2,
				'amazon_detail_page' => $this->amazon_detail_page,
				'appleibookslink'    => $this->appleibookslink,
				'asin'               => $this->asin,
				'author'             => $this->author,
				'author2'            => $this->author2,
				'author3'            => $this->author3,
				'author_url'         => $this->author_url,
				'sale_url'           => $this->sale_url,
				'price'              => $this->price,
				'backcover'          => $this->backcover,
				'bam_link'           => $this->bam_link,
				'bn_link'            => $this->bn_link,
				'book_uid'           => $this->book_uid,
				'callnumber'         => $this->callnumber,
				'category'           => $this->category,
				'copies'             => $this->copies,
				'country'            => $this->country,
				'date_finished'      => $this->date_finished,
				'description'        => $this->description,
				'edition'            => $this->edition,
				'finished'           => $this->finished,
				'first_edition'      => $this->first_edition,
				'format'             => $this->format,
				'genres'             => $this->genres,
				'goodreadslink'      => $this->goodreadslink,
				'google_preview'     => $this->google_preview,
				'illustrator'        => $this->illustrator,
				'image'              => $this->image,
				'isbn'               => $this->isbn,
				'isbn13'             => $this->isbn13,
				'keywords'           => $this->keywords,
				'kobo_link'          => $this->kobo_link,
				'language'           => $this->language,
				'notes'              => $this->notes,
				'numberinseries'     => $this->numberinseries,
				'othereditions'      => $this->othereditions,
				'originalpubyear'    => $this->originalpubyear,
				'originaltitle'      => $this->originaltitle,
				'outofprint'         => $this->outofprint,
				'page_yes'           => $page,
				'pages'              => $this->pages,
				'post_yes'           => $post,
				'pub_year'           => $this->pub_year,
				'publisher'          => $this->publisher,
				'rating'             => $this->rating,
				'review_iframe'      => $this->review_iframe,
				'series'             => $this->series,
				'shortdescription'   => $this->shortdescription,
				'signed'             => $this->signed,
				'similarbooks'       => $this->similarbooks,
				'similar_products'   => $this->similar_products,
				'subgenre'           => $this->subgenre,
				'subject'            => $this->subject,
				'title'              => $this->title,
				'woocommerce'        => $this->wooid,
				'ebook'              => $this->ebook,
			);

			// Building mask array to edit existing book.
			$data_for_edits_format = array(
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%d',
				'%s',
				'%f',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
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
							$data_for_edits[ $entry_details[0] ] = $this->book_array[ $entry_details[0] ];

							// Adding a mask for new value.
							array_push( $data_for_edits_format, '%s' );
						}
					}
				}
			}



			// Now actually updating the book in the db.
			$where        = array( 'ID' => $this->id );
			$where_format = array( '%d' );
			$result       = $wpdb->update( $this->library, $data_for_edits, $where, $data_for_edits_format, $where_format );

			// Now delete the colorbox transient.
			$transient_colorbox_name = 'wpbl_' . md5( 'SELECT * FROM ' . $this->library . ' WHERE ID = ' . $this->id );
			// Actually attempting to delete existing transients before creation of new one.
			$transient_delete_colorbox_result = $this->transients->delete_transient( $transient_colorbox_name );

			// Insert the Amazon Authorization into the DB if it's not already set to 'Yes'.
			if ( 'true' !== $this->options_results->amazonauth ) {
				$data         = array(
					'amazonauth' => $this->amazonauth,
				);
				$format       = array( '%s' );
				$where        = array( 'ID' => 1 );
				$where_format = array( '%d' );
				$wpdb->update( $wpdb->prefix . 'wpbooklist_jre_user_options', $data, $where, $format, $where_format );
			}

			// Now delete the user options transient.
			$transient_user_options_name = 'wpbl_' . md5( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_user_options' );
			// Actually attempting to delete existing transients before creation of new one.
			$transient_delete_colorbox_result = $this->transients->delete_transient( $transient_user_options_name );

			$this->edit_result = $result;

			if ( false === $this->edit_result ) {
				$this->edit_result = $this->edit_result . '--sep--WPBOOKLISTEDITERROR' . $wpdb->last_error;
			}
		}


		/** Function to empty a given table.
		 *
		 *  @param string $library - The library to empty.
		 */
		public function empty_table( $library ) {
			global $wpdb;
			$wpdb->query( "TRUNCATE TABLE $library" );

			// Drop table and re-create.
			$row2 = $wpdb->get_results( 'SHOW CREATE TABLE ' . $library );
			$wpdb->query( "DROP TABLE $library" );
			$wpdb->query( $row2[0]->{'Create Table'} );

			// Make sure auto_increment is set to 1.
			$wpdb->query( "ALTER TABLE $library AUTO_INCREMENT = 1" );

		}

		/** Function to empty a given table and delete the associated Posts and Pages.
		 *
		 *  @param string $library - The library to empty.
		 */
		public function empty_everything( $library ) {
			global $wpdb;
			$results = $wpdb->get_results( "SELECT * FROM $library" );

			foreach ( $results as $result ) {
				wp_delete_post( $result->page_yes, true );
				wp_delete_post( $result->post_yes, true );
			}

			$wpdb->query( "TRUNCATE TABLE $library" );
		}

		/** Function to delete a book
		 *
		 *  @param string $library - The library to empty.
		 *  @param int $book_id - The ID of the book to delete.
		 *  @param string $delete_string - A string of books to delete.
		 */
		public function delete_book( $library, $book_id, $delete_string = null ) {
			global $wpdb;

			// Delete the associated post and page.
			$post_delete = '';
			if ( null !== $delete_string ) {
				$delete_array = explode( '-', $delete_string );
				foreach ( $delete_array as $delete ) {
					$delete_result = wp_delete_post( $delete, true );

					$d_result = '';
					if ( $delete_result ) {
						$d_result = 1;
					}

					$post_delete = $post_delete . '-' . $d_result;
				}
			}

			// Deleting book from saved_page_post_log.
			$book_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $library WHERE ID = %d", $book_id ) );
			if ( is_object( $book_row ) ) {
				$uid      = $book_row->book_uid;
				$pp_table = $wpdb->prefix . 'wpbooklist_jre_saved_page_post_log';
				$wpdb->delete( $pp_table, array( 'book_uid' => $uid ) );
			}

			// Deleting the actual book row.
			$book_delete = $wpdb->delete( $library, array( 'ID' => $book_id ) );

			// Dropping primary key in database to alter the IDs and the AUTO_INCREMENT value.
			$wpdb->query( "ALTER TABLE $library MODIFY ID BIGINT(190) NOT NULL" );
			$wpdb->query( "ALTER TABLE $library DROP PRIMARY KEY" );

			// Adjusting ID values of remaining entries in database.
			$title_count = $wpdb->get_var( "SELECT COUNT(*) FROM $library" );
			for ( $x = $book_id; $x <= $title_count; $x++) {
				$data = array(
					'ID' => $book_id,
				);
				$book_id++;
				$format       = array( '%d' );
				$where        = array( 'ID' => $book_id );
				$where_format = array( '%d' );
				$wpdb->update( $library, $data, $where, $format, $where_format );
			}

			// Now delete the colorbox transient.
			$transient_colorbox_name = 'wpbl_' . md5( 'SELECT * FROM ' . $library . ' WHERE ID = ' . $book_id );
			// Actually attempting to delete existing transients before creation of new one.
			$transient_delete_colorbox_result = $this->transients->delete_transient( $transient_colorbox_name );

			// Adding primary key back to database.
			$wpdb->query( "ALTER TABLE $library ADD PRIMARY KEY (`ID`)" );
			$wpdb->query( "ALTER TABLE $library MODIFY ID BIGINT(190) AUTO_INCREMENT" );

			// Setting the AUTO_INCREMENT value based on number of remaining entries.
			$title_count++;
			$wpdb->query( $wpdb->prepare( "ALTER TABLE $library AUTO_INCREMENT = %d", $title_count ) );

			return $book_delete . '-' . $post_delete;
		}

		/** Function to handle refreshing of Amazon review.
		 *
		 *  @param int    $id - The ID of the book to refresh.
		 *  @param string $library - The library.
		 */
		public function refresh_amazon_review( $id, $library ) {
			global $wpdb;

			// Build options table.
			if ( strpos( $library, 'wpbooklist_jre_saved_book_log' ) !== false ) {
				$table_name_options = $wpdb->prefix . 'wpbooklist_jre_user_options';
			} else {
				$table              = explode( 'wpbooklist_jre_', $library );
				$table_name_options = $wpdb->prefix . 'wpbooklist_jre_settings_' . $table[1];
			}

			// Get options for amazon affiliate id and hideamazonreview.
			$this->options_results = $wpdb->get_row( "SELECT * FROM $table_name_options" );

			// Get book by id.
			$this->get_book_by_id( $id, $library );

			// Set isbn for gather Amazon data function.
			$this->isbn = $this->retrieved_book->isbn;

			// Check and see if Amazon review URL is expired. If so, make a new api call, get URL, saved in DB.
			if ( null === $this->options_results->hideamazonreview || 0 === $this->options_results->hideamazonreview ) {
				parse_str( $this->retrieved_book->review_iframe, $output );
				if ( null !== $output && '' !== $output && isset( $output['exp'] ) ) {
					$expire_date = substr( $output['exp'], 0, 10 );
					$today_date  = date( 'Y-m-d' );

					if ( $today_date === $expire_date || $today_date > $expire_date ) {

						$date                = new DateTime( 'tomorrow' );
						$newdate             = $date->format( 'Y-m-d' );
						$this->review_iframe = str_replace( $expire_date, $newdate, $this->retrieved_book->review_iframe );

						/*
						// Used to make API call every time colorbox was opened, then discovered I can simply change the expiration date in the url above.
						$this->isbn = $this->retrieved_book->isbn;
						$this->title = $this->retrieved_book->title;

						// Gather Amazon data
						$this->gather_amazon_data();
						*/

						// Save new iframe url.
						$data         = array(
							'review_iframe' => $this->review_iframe,
						);
						$format       = array( '%s' );
						$where        = array( 'ID' => $this->retrieved_book->ID );
						$where_format = array( '%d' );
						$wpdb->update( $library, $data, $where, $format, $where_format );
					}

					// Now delete the colorbox transient.
					$transient_colorbox_name = 'wpbl_' . md5( 'SELECT * FROM ' . $library . ' WHERE ID = ' . $this->retrieved_book->ID );
					// Actually attempting to delete existing transients before creation of new one.
					$transient_delete_colorbox_result = $this->transients->delete_transient( $transient_colorbox_name );

				}
			}
		}

		/** Function to handle retreiving a book by it's ID.
		 *
		 *  @param int    $id - The ID of the book to refresh.
		 *  @param string $library - The library.
		 */
		private function get_book_by_id( $id, $library ) {
			global $wpdb;
			$this->retrieved_book = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $library WHERE ID = %d", $id ) );
		}
	}

endif;
