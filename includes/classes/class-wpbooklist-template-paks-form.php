<?php
/**
 * WPBookList_Template_Paks_Form Tab Class - class-wpbooklist-template-paks-form.php.
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  6.1.5.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Template_Paks_Form', false ) ) :

	/**
	 * WPBookList_Admin_Menu Class.
	 */
	class WPBookList_Template_Paks_Form {

		/**
		 * Class Constructor - Simply calls the Translations.
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
		public function output_template_paks_form() {

			// Perform check for previously-saved Amazon Authorization.
			global $wpdb;
			$table_name  = $wpdb->prefix . 'wpbooklist_jre_user_options';
			$opt_results = $wpdb->get_row( "SELECT * FROM $table_name" );

			$table_name = $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names';
			$db_row     = $wpdb->get_results( "SELECT * FROM $table_name" );

			// For grabbing an image from media library.
			wp_enqueue_media();
			$string1 = '	
				<div id="wpbooklist-addbook-container">
					<p class="wpbooklist-tab-intro-para">' . $this->trans->trans_290 . ' <span class="wpbooklist-color-orange-italic">' . $this->trans->trans_291 . '</span> ' . $this->trans->trans_292 . ' <span class="wpbooklist-color-orange-italic">' . $this->trans->trans_293 . '</span> ' . $this->trans->trans_294 . ' <span class="wpbooklist-color-orange-italic">' . $this->trans->trans_57 . '</span> ' . $this->trans->trans_295 . '
					</p>
					<br/>
					<br/>
				<div class="section group">
					<div class="col span_1_of_2">
			 			<p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/seo-template-white.svg" />' . $this->trans->trans_296 . '</p>
				   		<a id="wpbooklist-extensions-page-img-link" href="https://wpbooklist.com/index.php/downloads/template-pak-bundle/">
							<div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-6">
								<img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/seo-template-white.svg"  />
					   			<p class="wpbooklist-extension-p-stylepaks">' . $this->trans->trans_297 . '</p>
					   		</div>
				   		</a>
				   		<p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">' . $this->trans->trans_298 . '<span class="wpbooklist-color-orange-italic"> ' . $this->trans->trans_297 . '</span></span><span class="wpbooklist-top-line-span"></span>
				   		</p>
				   		<div class="wpbooklist-above-purchase-line"></div>
						<p class="wpbooklist-to-download-page"><a href="https://wpbooklist.com/index.php/downloads/template-pak-bundle/">' . $this->trans->trans_506 . '</a></p>
						<div class="wpbooklist-extensions-purchase-button-link">
							<a href="https://wpbooklist.com/index.php/downloads/template-pak-bundle/">' . $this->trans->trans_560 . '</a>
						</div>
					</div>
					<div class="col span_1_of_2">
						<p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/seo-template-white.svg" />' . $this->trans->trans_573 . '</p>
						<a id="wpbooklist-extensions-page-img-link" href="https://wpbooklist.com/index.php/downloads/template-pak-1/">
							<div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-1">
								<img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/seo-template-white.svg"  />
								<p class="wpbooklist-extension-p-stylepaks">' . $this->trans->trans_573 . '</p>
							</div>
						</a>
						<p class="wpbooklist-extension-excerpt">
							<span class="wpbooklist-excerpt-span"> ' . $this->trans->trans_578 . '</span>
							<span class="wpbooklist-top-line-span"></span>
						</p>
						<div class="wpbooklist-above-purchase-line"></div>
						<p class="wpbooklist-to-download-page">
							<a href="https://wpbooklist.com/index.php/downloads/template-pak-1/">' . $this->trans->trans_506 . '</a>
						</p>
						<div class="wpbooklist-extensions-purchase-button-link">
							<a href="https://wpbooklist.com/index.php/downloads/template-pak-1/">' . $this->trans->trans_559 . '</a>
						</div>
					</div>
				</div>
				<div class="section group">
					<div class="col span_1_of_2">
						<p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/seo-template-white.svg" />' . $this->trans->trans_574 . '</p>
						<a id="wpbooklist-extensions-page-img-link" href="https://wpbooklist.com/index.php/downloads/template-pak-2/">
							<div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-2">
								<img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/seo-template-white.svg"  />
								<p class="wpbooklist-extension-p-stylepaks">' . $this->trans->trans_574 . '</p>
							</div>
						</a>
						<p class="wpbooklist-extension-excerpt">
							<span class="wpbooklist-excerpt-span">' . $this->trans->trans_579 . '</span>
							<span class="wpbooklist-top-line-span"></span>
						</p>
						<div class="wpbooklist-above-purchase-line"></div>
						<p class="wpbooklist-to-download-page">
							<a href="https://wpbooklist.com/index.php/downloads/template-pak-2/">' . $this->trans->trans_506 . '</a>
						</p>
						<div class="wpbooklist-extensions-purchase-button-link">
							<a href="https://wpbooklist.com/index.php/downloads/template-pak-2/">' . $this->trans->trans_559 . '</a>
						</div>
					</div>
					<div class="col span_1_of_2">
						<p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/seo-template-white.svg" />' . $this->trans->trans_575 . '</p>
						<a id="wpbooklist-extensions-page-img-link" href="https://wpbooklist.com/index.php/downloads/template-pak-3/">
							<div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-3">
								<img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/seo-template-white.svg"  />
								<p class="wpbooklist-extension-p-stylepaks">' . $this->trans->trans_575 . '</p>
						 	</div>
						</a>
						<p class="wpbooklist-extension-excerpt">
							<span class="wpbooklist-excerpt-span"> ' . $this->trans->trans_580 . '</span>
							<span class="wpbooklist-top-line-span"></span>
						</p>
						<div class="wpbooklist-above-purchase-line"></div>
						<p class="wpbooklist-to-download-page">
							<a href="https://wpbooklist.com/index.php/downloads/template-pak-3/">' . $this->trans->trans_506 . '</a>
						</p>
						<div class="wpbooklist-extensions-purchase-button-link">
							<a href="https://wpbooklist.com/index.php/downloads/template-pak-3/">' . $this->trans->trans_559 . '</a>
						</div>
					</div>
				</div>
				<div class="section group">
					<div class="col span_1_of_2">
						<p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/seo-template-white.svg" />' . $this->trans->trans_576 . '</p>
						<a id="wpbooklist-extensions-page-img-link" href="https://wpbooklist.com/index.php/downloads/template-pak-4/">
							<div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-4">
								<img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/seo-template-white.svg"  />
								<p class="wpbooklist-extension-p-stylepaks">' . $this->trans->trans_576 . '</p>
							</div>
						</a>
						<p class="wpbooklist-extension-excerpt">
							<span class="wpbooklist-excerpt-span">' . $this->trans->trans_581 . '</span>
							<span class="wpbooklist-top-line-span"></span>
						</p>
						<div class="wpbooklist-above-purchase-line"></div>
						<p class="wpbooklist-to-download-page">
							<a href="https://wpbooklist.com/index.php/downloads/template-pak-4/">' . $this->trans->trans_506 . '</a>
						</p>
						<div class="wpbooklist-extensions-purchase-button-link">
							<a href="https://wpbooklist.com/index.php/downloads/template-pak-4/">' . $this->trans->trans_559 . '</a>
						</div>
					</div>
					<div class="col span_1_of_2">
						<p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/seo-template-white.svg" />' . $this->trans->trans_577 . '</p>
						<a id="wpbooklist-extensions-page-img-link" href="https://wpbooklist.com/index.php/downloads/template-pak-5/">
							<div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-5">
								<img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/seo-template-white.svg"  />
								<p class="wpbooklist-extension-p-stylepaks">' . $this->trans->trans_577 . '</p>
							</div>
						</a>
						<p class="wpbooklist-extension-excerpt">
							<span class="wpbooklist-excerpt-span">' . $this->trans->trans_582 . '  <a href="https://wpbooklist.com/index.php/downloads/library-stylepak-5/" class="targetpop-predictions-link-tracker-class">' . $this->trans->trans_565 . '</a>, ' . $this->trans->trans_583 . '</p></span>
							<span class="wpbooklist-top-line-span"></span>
						</p>
						<div class="wpbooklist-above-purchase-line"></div>
						<p class="wpbooklist-to-download-page">
							<a href="https://wpbooklist.com/index.php/downloads/template-pak-5/">' . $this->trans->trans_506 . '</a>
						</p>
						<div class="wpbooklist-extensions-purchase-button-link">
							<a href="https://wpbooklist.com/index.php/downloads/template-pak-5/">' . $this->trans->trans_559 . '</a>
						</div>
					</div>
				</div>
			</div>';

			return $string1;
		}
	}
endif;
