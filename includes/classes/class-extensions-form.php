<?php
/**
 * WPBookList Add-Edit-Book-Form Tab Class
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  6.1.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Add_Book_Form', false ) ) :

	/**
	 * WPBookList_Admin_Menu Class.
	 **/
	class WPBookList_Add_Book_Form {

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
		public function output_add_book_form() {

			// Perform check for previously-saved Amazon Authorization.
			global $wpdb;
			$table_name  = $wpdb->prefix . 'wpbooklist_jre_user_options';
			$opt_results = $wpdb->get_row( "SELECT * FROM $table_name" );

			$table_name = $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names';
			$db_row     = $wpdb->get_results( "SELECT * FROM $table_name" );

			$string1 = '<div id="wpbooklist-addbook-container">
			<p class="wpbooklist-tab-intro-para">' . $this->trans->trans_502 . ' <span class="wpbooklist-color-orange-italic">WPBookList</span> ' . $this->trans->trans_503 . '<br/><br/>
				<div class="wpbooklist-mini-ultimate-advert">
						<div id="span_1_of_1_featured">
							<div id="wpbooklist-featured-bundle-img-text-div">
							<div id="wpbooklist-featured-bundle-text">
								<p style="display:none;">Get every <span class="wpbooklist-color-orange-italic">WPBookList</span> product in <strong><em>one single purchase! </em></strong> Choose an <a href="https://wpbooklist.com/index.php/ultimate-bundles/">Ultimate WPBookList Bundle</a> and receive:</p>
								<p>
									<img class="wpbooklist-featured-icon-img" src="https://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepaksblack.svg" scale="0"><a class="wpbooklist-icon-link" href="https://wpbooklist.com/index.php/ultimate-bundles/">Every StylePak</a><img class="wpbooklist-featured-icon-img" src="https://wpbooklist.com/wp-content/plugins/wpbooklist/assets/img/icons/web-site.svg" scale="0"><a class="wpbooklist-icon-link" href="https://wpbooklist.com/index.php/ultimate-bundles/">Every Extension</a><img class="wpbooklist-featured-icon-img" src="https://wpbooklist.com/wp-content/uploads/2017/08/svgs/seo-template.svg" scale="0"><a class="wpbooklist-icon-link" href="https://wpbooklist.com/index.php/ultimate-bundles/">Every Template Pak</a><br><img class="wpbooklist-featured-icon-img" src="https://wpbooklist.com/wp-content/uploads/2017/08/svgs/new.svg" scale="0"><a class="wpbooklist-icon-link" href="https://wpbooklist.com/index.php/ultimate-bundles/">All Future Extensions</a><img class="wpbooklist-featured-icon-img" src="https://wpbooklist.com/wp-content/uploads/2017/08/svgs/support-woman.svg" scale="0"><a class="wpbooklist-icon-link" href="https://wpbooklist.com/index.php/ultimate-bundles/">Priority Tech Support</a>
								</p>
							</div>
							<div id="wpbooklist-featured-bundle-purchase-div">
							<div class="wpbooklist-extensions-purchase-button-link"><a href="https://wpbooklist.com/index.php/ultimate-bundles/">Bundles Start at $30
							Purchase Now!</a></div>
							</div>
						</div>
					</div>
				</div>
				<div class="section group">
					<div class="col span_1_of_2">
						<a id="wpbooklist-extensions-page-img-link" href="http://wpbooklist.com/index.php/downloads/extensions-bundle/">
							<div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-6">
								<img class="wpbooklist-extension-img-bundle-mult" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/book.svg"/>
								<img class="wpbooklist-extension-img-bundle-mult" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/ereader-with-bookmark.svg"/>
								<img class="wpbooklist-extension-img-bundle-mult" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/profits.svg">
								<p class="wpbooklist-extension-p-bundle-ext">Extensions Bundle</p>
								<p class="wpbooklist-tab-intro-para"><img class="wpbooklist-extension-img-bundle-mult" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/server.svg">
									<img class="wpbooklist-extension-img-bundle-mult" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/ereader.svg"/>
									<img class="wpbooklist-extension-img-bundle-mult" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/goodreads-letter-logo.svg"/>
								</p>
							</div>
						</a>
						<p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">' . $this->trans->trans_505 . ' <span class="wpbooklist-color-orange-italic">' . $this->trans->trans_504 . '</span></span><span class="wpbooklist-top-line-span"></span></p>
						<div class="wpbooklist-above-purchase-line"></div>
						<p class="wpbooklist-to-download-page">
							<a href="http://wpbooklist.com/index.php/downloads/extensions-bundle/">' . $this->trans->trans_506 . '</a>
						</p>
						<div class="wpbooklist-extensions-purchase-button-link">
							<a href="http://wpbooklist.com/index.php/downloads/extensions-bundle/">' . $this->trans->trans_507 . '</a>
						</div>
					</div>
					<div class="col span_1_of_2">
						<p class="wpbooklist-extension-title">
							<img class="wpbooklist-extension-icon-img" src="http://www.wpbooklist.com/wp-content/uploads/wpbooklist/icons/affiliate.svg" />' . $this->trans->trans_508 . '</p>
						<a id="wpbooklist-extensions-page-img-link" href="http://wpbooklist.com/index.php/downloads/affiliate-extension/">
							<div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-2">
								<img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/profits.svg"  />
								<p class="wpbooklist-extension-p">' . $this->trans->trans_509 . '</p>
							</div>
						</a>
						<p class="wpbooklist-extension-excerpt">
							<span class="wpbooklist-excerpt-span">' . $this->trans->trans_510 . ' <span class="wpbooklist-color-orange-italic">' . $this->trans->trans_511 . '</span>
							</span>
							<span class="wpbooklist-top-line-span"></span>
						</p>
						<div class="wpbooklist-above-purchase-line"></div>
						<p class="wpbooklist-to-download-page">
							<a href="http://wpbooklist.com/index.php/downloads/affiliate-extension/">' . $this->trans->trans_506 . '</a>
						</p>
						<div class="wpbooklist-extensions-purchase-button-link">
							<a href="http://wpbooklist.com/index.php/downloads/affiliate-extension/">' . $this->trans->trans_512 . '</a>
						</div>
					</div>
				</div>
				<div class="section group">
					<div class="col span_1_of_2">
						<p class="wpbooklist-extension-title">
							<img class="wpbooklist-extension-icon-img" src="http://www.wpbooklist.com/wp-content/uploads/wpbooklist/icons/book.svg" />' . $this->trans->trans_296 . '
						</p>
						<a id="wpbooklist-extensions-page-img-link" href="http://wpbooklist.com/index.php/downloads/storefront-extension/">
							<div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-1">
								<img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/book.svg"  />
								<p class="wpbooklist-extension-p">' . $this->trans->trans_513 . '</p>
							</div>
						</a>
						<p class="wpbooklist-extension-excerpt">
							<span class="wpbooklist-excerpt-span">' . $this->trans->trans_514 . ' <span class="wpbooklist-color-orange-italic">' . $this->trans->trans_515 . '</span></span>
							<span class="wpbooklist-top-line-span"></span>
						</p>
						<div class="wpbooklist-above-purchase-line"></div>
						<p class="wpbooklist-to-download-page">
							<a href="http://wpbooklist.com/index.php/downloads/storefront-extension/">' . $this->trans->trans_506 . '</a>
						</p>
						<div class="wpbooklist-extensions-purchase-button-link">
							<a href="http://wpbooklist.com/index.php/downloads/storefront-extension/">' . $this->trans->trans_516 . '</a>
						</div>
					</div>
					<div class="col span_1_of_2">
						<p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/chat.svg" />' . $this->trans->trans_517 . '
						</p>
						<a id="wpbooklist-extensions-page-img-link" href="https://wpbooklist.com/index.php/downloads/comments-extension/">
							<div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-13">
								<img class="wpbooklist-extension-img" src="' . ROOT_IMG_ICONS_URL . 'chat.svg"  />
								<p class="wpbooklist-extension-p" style="margin-top:33px;">' . $this->trans->trans_517 . '</p>
							</div>
						</a>
						<p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">' . $this->trans->trans_518 . ' </span>
							<span class="wpbooklist-top-line-span"></span>
						</p>
						<div class="wpbooklist-above-purchase-line"></div>
						<p class="wpbooklist-to-download-page"><a href="https://wpbooklist.com/index.php/downloads/comments-extension/">' . $this->trans->trans_506 . '</a></p>
						<div class="wpbooklist-extensions-purchase-button-link">
							<a href="https://wpbooklist.com/index.php/downloads/comments-extension/">' . $this->trans->trans_512 . '</a>
						</div>
					</div>
				</div>
				<div class="section group">
					<div class="col span_1_of_2">
						<p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://www.wpbooklist.com/wp-content/uploads/wpbooklist/icons/server.svg" />' . $this->trans->trans_520 . '
						</p>
						<a id="wpbooklist-extensions-page-img-link" href="http://wpbooklist.com/index.php/downloads/bulk-upload-extension/">
							<div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-3">
								<img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/server.svg"  />
								<p class="wpbooklist-extension-p">' . $this->trans->trans_521 . '</p>
							</div>
						</a>
						<p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">' . $this->trans->trans_522 . ' <span class="wpbooklist-color-orange-italic">' . $this->trans->trans_69 . '!</span></span>
							<span class="wpbooklist-top-line-span"></span>
						</p>
						<div class="wpbooklist-above-purchase-line"></div>
						<p class="wpbooklist-to-download-page">
							<a href="http://wpbooklist.com/index.php/downloads/bulk-upload-extension/">' . $this->trans->trans_506 . '</a>
						</p>
						<div class="wpbooklist-extensions-purchase-button-link">
							<a href="http://wpbooklist.com/index.php/downloads/bulk-upload-extension/">' . $this->trans->trans_512 . '</a>
						</div>
					</div>
					<div class="col span_1_of_2">
						<p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://www.wpbooklist.com/wp-content/uploads/wpbooklist/icons/ereader.svg" />' . $this->trans->trans_524 . '</p>
						<a id="wpbooklist-extensions-page-img-link" href="http://wpbooklist.com/index.php/downloads/mobile-app-extension/">
							<div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-4">
								<img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/ereader.svg"  />
								<p class="wpbooklist-extension-p">' . $this->trans->trans_525 . '</p>
							</div>
						</a>
						<p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">' . $this->trans->trans_526 . ' <span class="wpbooklist-color-orange-italic">' . $this->trans->trans_527 . '!</span></span><span class="wpbooklist-top-line-span"></span>
						</p>
						<div class="wpbooklist-above-purchase-line"></div>
						<p class="wpbooklist-to-download-page">
							<a href="http://wpbooklist.com/index.php/downloads/mobile-app-extension/">' . $this->trans->trans_506 . '</a>
						</p>
						<div class="wpbooklist-extensions-purchase-button-link">
							<a href="http://wpbooklist.com/index.php/downloads/mobile-app-extension/">' . $this->trans->trans_512 . '</a>
						</div>
					</div>
				</div>
				<div class="section group">
					<div class="col span_1_of_2">
						<p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/list-white.svg" />' . $this->trans->trans_586 . '</p>
						<a id="wpbooklist-extensions-page-img-link" href="https://wpbooklist.com/index.php/downloads/custom-fields-extension/">
							<div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-15">
								<img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/list-white.svg"  />
								<p class="wpbooklist-extension-p" style="margin-top:33px;">' . $this->trans->trans_586 . '</p>
							</div>
						</a>
						<p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">' . $this->trans->trans_585 . ' </span>
							<span class="wpbooklist-top-line-span"></span>
						</p>
						<div class="wpbooklist-above-purchase-line"></div>
						<p class="wpbooklist-to-download-page">
							<a href="https://wpbooklist.com/index.php/downloads/custom-fields-extension/">' . $this->trans->trans_506 . '</a>
						</p>
						<div class="wpbooklist-extensions-purchase-button-link">
							<a href="https://wpbooklist.com/index.php/downloads/custom-fields-extension/">' . $this->trans->trans_512 . '</a>
						</div>
					</div>
					<div class="col span_1_of_2">
						<p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://www.wpbooklist.com/wp-content/uploads/wpbooklist/icons/goodreads.svg" />' . $this->trans->trans_528 . '</p>
						<a id="wpbooklist-extensions-page-img-link" href="http://wpbooklist.com/index.php/downloads/goodreads-extension/">
							<div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-5">
								<img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/goodreads-letter-logo.svg"  />
								<p class="wpbooklist-extension-p">' . $this->trans->trans_529 . '</p>
							</div>
						</a>
						<p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">' . $this->trans->trans_530 . ' <span class="wpbooklist-color-orange-italic">' . $this->trans->trans_531 . '!</span></span>
							<span class="wpbooklist-top-line-span"></span>
						</p>
						<div class="wpbooklist-above-purchase-line"></div>
							<p class="wpbooklist-to-download-page">
								<a href="http://wpbooklist.com/index.php/downloads/goodreads-extension/">' . $this->trans->trans_506 . '</a>
							</p>
						<div class="wpbooklist-extensions-purchase-button-link"><a href="http://wpbooklist.com/index.php/downloads/goodreads-extension/">' . $this->trans->trans_512 . '</a></div>
					</div>
				</div>
				<div class="section group">
					<div class="col span_1_of_2">
						<p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/search-white.svg" />' . $this->trans->trans_536 . '</p>
						<a id="wpbooklist-extensions-page-img-link" href="https://wpbooklist.com/index.php/downloads/bookfinder-extension/">
							<div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-9">
								<img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/search-white.svg"  />
								<p class="wpbooklist-extension-p">' . $this->trans->trans_537 . '</p>
							</div>
						</a>
						<p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">' . $this->trans->trans_538 . ' <span class="wpbooklist-color-orange-italic">' . $this->trans->trans_539 . '!</span></span>
							<span class="wpbooklist-top-line-span"></span>
						</p>
						<div class="wpbooklist-above-purchase-line"></div>
						<p class="wpbooklist-to-download-page">
							<a href="https://wpbooklist.com/index.php/bookfinder-demo/">' . $this->trans->trans_540 . '!</a>
						</p>
						<div class="wpbooklist-extensions-purchase-button-link">
							<a href="https://wpbooklist.com/index.php/downloads/bookfinder-extension/">' . $this->trans->trans_512 . '</a>
						</div>
					</div>
					<div class="col span_1_of_2">
						<p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="https://wpbooklist.com/wp-content/uploads/edd/2017/09/Screenshot-2017-09-17-14.12.39.png" />' . $this->trans->trans_513 . '</p>
						<a id="wpbooklist-extensions-page-img-link" href="https://wpbooklist.com/index.php/downloads/e-books-extension/">
							<div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-8">
								<img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/ebook.svg"  />
								<p class="wpbooklist-extension-p">' . $this->trans->trans_698 . '</p>
							</div>
						</a>
						<p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">' . $this->trans->trans_699 . '</span>
							<span class="wpbooklist-top-line-span"></span>
						</p>
						<div class="wpbooklist-above-purchase-line"></div>
						<p class="wpbooklist-to-download-page">
							<a href="https://wpbooklist.com/index.php/downloads/e-books-extension/">' . $this->trans->trans_506 . '</a>
						</p>
						<div class="wpbooklist-extensions-purchase-button-link">
							<a href="https://wpbooklist.com/index.php/downloads/e-books-extension/">' . $this->trans->trans_512 . '</a>
						</div>
					</div>
				</div>
				<div class="section group">
					<div class="col span_1_of_2">
						<p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/carousel-white.svg" />' . $this->trans->trans_536 . '</p>
						<a id="wpbooklist-extensions-page-img-link" href="https://wpbooklist.com/index.php/downloads/carousel-extension/">
							<div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-11">
								<img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/carousel-white.svg"  />
								<p class="wpbooklist-extension-p">' . $this->trans->trans_545 . '</p>
							</div>
						</a>
						<p class="wpbooklist-extension-excerpt">
							<span class="wpbooklist-excerpt-span">' . $this->trans->trans_546 . ' </span>
							<span class="wpbooklist-top-line-span"></span>
						</p>
						<div class="wpbooklist-above-purchase-line"></div>
							<p class="wpbooklist-to-download-page">
								<a href="https://wpbooklist.com/index.php/2017/12/08/wpbooklist-carousel-extension-guide/">' . $this->trans->trans_540 . '!</a>
							</p>
						<div class="wpbooklist-extensions-purchase-button-link">
							<a href="https://wpbooklist.com/index.php/downloads/carousel-extension/">' . $this->trans->trans_512 . '</a>
						</div>
					</div>
					<div class="col span_1_of_2">
						<p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/carousel-white.svg" />' . $this->trans->trans_536 . '</p>
						<a id="wpbooklist-extensions-page-img-link" href="https://wpbooklist.com/index.php/downloads/categories-extension/">
							<div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-12">
								<img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/dropdown-white.svg"  />
								<p class="wpbooklist-extension-p">' . $this->trans->trans_29 . '</p>
							</div>
						</a>
						<p class="wpbooklist-extension-excerpt">
							<span class="wpbooklist-excerpt-span">' . $this->trans->trans_547 . '! </span>
							<span class="wpbooklist-top-line-span"></span>
						</p>
						<div class="wpbooklist-above-purchase-line"></div>
						<p class="wpbooklist-to-download-page">
							<a href="https://wpbooklist.com/index.php/categories-extension-demo/">' . $this->trans->trans_540 . '!</a>
						</p>
						<div class="wpbooklist-extensions-purchase-button-link">
							<a href="https://wpbooklist.com/index.php/downloads/categories-extension/">' . $this->trans->trans_512 . '</a>
						</div>
					</div>
				</div>
				<div class="section group">
					<div class="col span_1_of_2">
						<p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/computer-white.svg" />' . $this->trans->trans_552 . '</p>
						<a id="wpbooklist-extensions-page-img-link" href="https://wpbooklist.com/index.php/downloads/branding-extension/">
							<div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-14">
								<img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/computer-white.svg"  />
								<p class="wpbooklist-extension-p" style="margin-top:33px;">' . $this->trans->trans_552 . '</p>
							</div>
						</a>
						<p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">' . $this->trans->trans_553 . ' </span>
							<span class="wpbooklist-top-line-span"></span>
						</p>
						<div class="wpbooklist-above-purchase-line"></div>
						<p class="wpbooklist-to-download-page">
							<a href="https://wpbooklist.com/index.php/downloads/branding-extension/">' . $this->trans->trans_506 . '</a>
						</p>
						<div class="wpbooklist-extensions-purchase-button-link">
							<a href="https://wpbooklist.com/index.php/downloads/branding-extension/">' . $this->trans->trans_512 . '</a>
						</div>
					</div>
					<div class="col span_1_of_2">
						<p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="https://wpbooklist.com/wp-content/uploads/edd/2017/09/Screenshot-2017-09-17-14.12.39.png" />' . $this->trans->trans_513 . '</p>
						<a id="wpbooklist-extensions-page-img-link" href="https://wpbooklist.com/index.php/downloads/kindle-preview-extension/">
							<div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-7">
								<img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/ereader-with-bookmark.svg"  />
								<p class="wpbooklist-extension-p">' . $this->trans->trans_533 . '</p>
							</div>
						</a>
						<p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">' . $this->trans->trans_534 . ' <a href="http://wpbooklist.com/index.php/downloads/affiliate-extension/"><span class="wpbooklist-color-orange-italic">' . $this->trans->trans_511 . '</span></a></span>
							<span class="wpbooklist-top-line-span"></span>
						</p>
						<div class="wpbooklist-above-purchase-line"></div>
						<p class="wpbooklist-to-download-page">
							<a href="https://wpbooklist.com/index.php/downloads/kindle-preview-extension/">' . $this->trans->trans_506 . '</a>
						</p>
						<div class="wpbooklist-extensions-purchase-button-link">
							<a href="https://wpbooklist.com/index.php/downloads/kindle-preview-extension/">' . $this->trans->trans_535 . '</a>
						</div>
					</div>
				</div>
				<div class="section group">
					<div class="col span_1_of_2">
						<p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/content-white.svg" />' . $this->trans->trans_541 . '</p>
						<a id="wpbooklist-extensions-page-img-link" href="https://wpbooklist.com/index.php/downloads/stylizer-extension/">
							<div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-10">
								<img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/content-white.svg"  />
								<p class="wpbooklist-extension-p">' . $this->trans->trans_542 . '</p>
							</div>
						</a>
						<p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">' . $this->trans->trans_543 . ' <span class="wpbooklist-color-orange-italic">' . $this->trans->trans_57 . '</span> ' . $this->trans->trans_544 . '!</span>
							<span class="wpbooklist-top-line-span"></span>
						</p>
						<div class="wpbooklist-above-purchase-line"></div>
						<p class="wpbooklist-to-download-page">
							<a href="https://wpbooklist.com/index.php/downloads/stylizer-extension/">' . $this->trans->trans_506 . '</a>
						</p>
						<div class="wpbooklist-extensions-purchase-button-link">
							<a href="https://wpbooklist.com/index.php/downloads/stylizer-extension/">' . $this->trans->trans_512 . '</a>
						</div>
					</div>
				</div>


			</div>';

			return $string1;
		}


	}

endif;
