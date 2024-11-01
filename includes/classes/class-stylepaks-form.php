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
	 */
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
		 * Outputs all HTML elements on the page.
		 */
		public function output_add_book_form() {

			// Perform check for previously-saved Amazon Authorization.
			global $wpdb;
			$table_name  = $wpdb->prefix . 'wpbooklist_jre_user_options';
			$opt_results = $wpdb->get_row( "SELECT * FROM $table_name" );
			$table_name  = $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names';
			$db_row      = $wpdb->get_results( "SELECT * FROM $table_name" );

			// For grabbing an image from media library.
			wp_enqueue_media();
			$string1 = '
			<div id="wpbooklist-addbook-container">
				<p class="wpbooklist-tab-intro-para"><p class="wpbooklist-tab-intro-para">' . $this->trans->trans_290 . ' <span class="wpbooklist-color-orange-italic">StylePak</span> ' . $this->trans->trans_292 . ' <span class="wpbooklist-color-orange-italic">StylePaks</span> ' . $this->trans->trans_294 . ' <span class="wpbooklist-color-orange-italic">' . $this->trans->trans_57 . '</span> ' . $this->trans->trans_555 . '!</p>
				<br/>
				<br/>
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
						<p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepak.svg" />' . $this->trans->trans_296 . '</p>
						<a id="wpbooklist-extensions-page-img-link" href="http://wpbooklist.com/index.php/downloads/library-stylepak-bundle/">
							<div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-6">
								<img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepak.svg"  />
								<p class="wpbooklist-extension-p-stylepaks">' . $this->trans->trans_556 . '!</p>
							</div>
						</a>
						<p class="wpbooklist-extension-excerpt">
							<span class="wpbooklist-excerpt-span">' . $this->trans->trans_557 . ' <span class="wpbooklist-color-orange-italic">' . $this->trans->trans_556 . '!</span></span>
							<span class="wpbooklist-top-line-span"></span>
						</p>
						<div class="wpbooklist-above-purchase-line"></div>
						<p class="wpbooklist-to-download-page">
							<a href="http://wpbooklist.com/index.php/downloads/library-stylepak-bundle/">' . $this->trans->trans_506 . '</a>
						</p>
						<div class="wpbooklist-extensions-purchase-button-link">
							<a href="http://wpbooklist.com/index.php/downloads/library-stylepak-bundle/">' . $this->trans->trans_560 . '</a>
						</div>
					</div>
					<div class="col span_1_of_2">
						<p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepak.svg" />' . $this->trans->trans_561 . '</p>
						<a id="wpbooklist-extensions-page-img-link" href="http://wpbooklist.com/index.php/downloads/library-stylepak-1/">
							<div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-1">
								<img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepak.svg"  />
								<p class="wpbooklist-extension-p-stylepaks">' . $this->trans->trans_561 . '</p>
							</div>
						</a>
						<p class="wpbooklist-extension-excerpt">
							<span class="wpbooklist-excerpt-span"> ' . $this->trans->trans_567 . '</span>
							<span class="wpbooklist-top-line-span"></span>
						</p>
						<div class="wpbooklist-above-purchase-line"></div>
						<p class="wpbooklist-to-download-page">
							<a href="http://wpbooklist.com/index.php/library-stylepak-1/">' . $this->trans->trans_558 . '</a>
						</p>
						<div class="wpbooklist-extensions-purchase-button-link">
							<a href="http://wpbooklist.com/index.php/downloads/library-stylepak-1/">' . $this->trans->trans_559 . '</a>
						</div>
					</div>
				</div>
				<div class="section group">
					<div class="col span_1_of_2">
						<p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepak.svg" />' . $this->trans->trans_562 . '</p>
						<a id="wpbooklist-extensions-page-img-link" href="http://wpbooklist.com/index.php/downloads/library-stylepak-2/">
							<div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-2">
								<img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepak.svg"  />
								<p class="wpbooklist-extension-p-stylepaks">' . $this->trans->trans_562 . '</p>
							</div>
						</a>
						<p class="wpbooklist-extension-excerpt">
							<span class="wpbooklist-excerpt-span">' . $this->trans->trans_568 . '</span>
							<span class="wpbooklist-top-line-span"></span>
						</p>
						<div class="wpbooklist-above-purchase-line"></div>
						<p class="wpbooklist-to-download-page">
							<a href="http://wpbooklist.com/index.php/library-stylepak-2/">' . $this->trans->trans_558 . '</a>
						</p>
						<div class="wpbooklist-extensions-purchase-button-link">
							<a href="http://wpbooklist.com/index.php/downloads/library-stylepak-2/">' . $this->trans->trans_559 . '</a>
						</div>
					</div>
					<div class="col span_1_of_2">
						<p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepak.svg" />' . $this->trans->trans_563 . '</p>
						<a id="wpbooklist-extensions-page-img-link" href="http://wpbooklist.com/index.php/downloads/library-stylepak-3/">
							<div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-3">
								<img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepak.svg"  />
								<p class="wpbooklist-extension-p-stylepaks">' . $this->trans->trans_563 . '</p>
							</div>
						</a>
						<p class="wpbooklist-extension-excerpt">
							<span class="wpbooklist-excerpt-span"> ' . $this->trans->trans_569 . '</span>
							<span class="wpbooklist-top-line-span"></span>
						</p>
						<div class="wpbooklist-above-purchase-line"></div>
						<p class="wpbooklist-to-download-page">
							<a href="http://wpbooklist.com/index.php/library-stylepak-3/">' . $this->trans->trans_558 . '</a>
						</p>
						<div class="wpbooklist-extensions-purchase-button-link">
							<a href="http://wpbooklist.com/index.php/downloads/library-stylepak-3/">' . $this->trans->trans_559 . '</a>
						</div>
					</div>
				</div>
				<div class="section group">
					<div class="col span_1_of_2">
						<p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepak.svg" />' . $this->trans->trans_564 . '</p>
						<a id="wpbooklist-extensions-page-img-link" href="http://wpbooklist.com/index.php/downloads/library-stylepak-4/">
							<div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-4">
								<img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepak.svg"  />
								<p class="wpbooklist-extension-p-stylepaks">' . $this->trans->trans_564 . '</p>
							</div>
						</a>
						<p class="wpbooklist-extension-excerpt">
							<span class="wpbooklist-excerpt-span">' . $this->trans->trans_570 . '</span>
							<span class="wpbooklist-top-line-span"></span>
						</p>
						<div class="wpbooklist-above-purchase-line"></div>
						<p class="wpbooklist-to-download-page">
							<a href="http://wpbooklist.com/index.php/library-stylepak-4/">' . $this->trans->trans_558 . '</a>
						</p>
						<div class="wpbooklist-extensions-purchase-button-link">
							<a href="http://wpbooklist.com/index.php/downloads/library-stylepak-4/">' . $this->trans->trans_559 . '</a>
						</div>
					</div>
					<div class="col span_1_of_2">
						<p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepak.svg" />' . $this->trans->trans_565 . '</p>
						<a id="wpbooklist-extensions-page-img-link" href="http://wpbooklist.com/index.php/downloads/library-stylepak-5/">
							<div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-5">
								<img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepak.svg"  />
								<p class="wpbooklist-extension-p-stylepaks">' . $this->trans->trans_565 . '</p>
							</div>
						</a>
						<p class="wpbooklist-extension-excerpt">
							<span class="wpbooklist-excerpt-span">' . $this->trans->trans_571 . '</span>
							<span class="wpbooklist-top-line-span"></span>
						</p>
						<div class="wpbooklist-above-purchase-line"></div>
						<p class="wpbooklist-to-download-page">
							<a href="http://wpbooklist.com/index.php/library-stylepak-5/">' . $this->trans->trans_558 . '</a>
						</p>
						<div class="wpbooklist-extensions-purchase-button-link">
							<a href="http://wpbooklist.com/index.php/downloads/library-stylepak-5/">' . $this->trans->trans_559 . '</a>
						</div>
					</div>
				</div>
				<div class="section group">
					<div class="col span_1_of_2">
						<p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepak.svg" />' . $this->trans->trans_566 . '</p>
						<a id="wpbooklist-extensions-page-img-link" href="http://wpbooklist.com/index.php/downloads/library-stylepak-6/">
							<div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-8">
								<img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepak.svg"  />
								<p class="wpbooklist-extension-p-stylepaks">' . $this->trans->trans_566 . '</p>
							</div>
						</a>
						<p class="wpbooklist-extension-excerpt">
							<span class="wpbooklist-excerpt-span">' . $this->trans->trans_572 . '</span>
							<span class="wpbooklist-top-line-span"></span>
						</p>
						<div class="wpbooklist-above-purchase-line"></div>
						<p class="wpbooklist-to-download-page">
							<a href="http://wpbooklist.com/index.php/library-stylepak-6/">' . $this->trans->trans_558 . '</a>
						</p>
						<div class="wpbooklist-extensions-purchase-button-link">
							<a href="http://wpbooklist.com/index.php/downloads/library-stylepak-6/">' . $this->trans->trans_559 . '</a>
						</div>
					</div>
				</div>
			</div>';
			return $string1;
		}
	}
endif;
