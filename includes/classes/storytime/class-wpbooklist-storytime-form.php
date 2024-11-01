<?php
/**
 * WPBookList WPBookList_Storytime_Form Class - class-wpbooklist-storytime-form.php
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes/Storytime
 * @version  6.1.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Storytime_Form', false ) ) :
	/**
	 * WPBookList_Storytime_Form Class.
	 */
	class WPBookList_Storytime_Form {

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
		 * Function to output the actual HTML.
		 */
		public function output_storytime_form() {

			global $wpdb;
			$opt_results = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_user_options' );
			$db_row      = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names' );

			$stories_table_name = $wpdb->prefix . 'wpbooklist_jre_storytime_stories';
			$stories_db_data    = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_storytime_stories' );

			// Build the Categories string.
			$categories_string = '';
			$cat_array         = array();
			foreach ( $stories_db_data as $key => $value ) {
				array_push( $cat_array, $value->category );
			}

			$cat_array = array_unique( $cat_array );
			foreach ( $cat_array as $key => $value ) {
				$categories_string = $categories_string . '<option value="' . $value . '">' . $value . '</option>';
			}

			// Build the Most Recent string.
			$recent_string   = '';
			$stories_db_data = array_reverse( $stories_db_data );
			foreach ( $stories_db_data as $key => $value ) {
				$recent_string = $recent_string . '<p class="wpbooklist-storytime-listed-story" data-id="' . $value->ID . '">' . $value->title . '</p>';
			}

			// For displaying html informing the user they need to become a Patreon patron.
			if ( ( null === $opt_results->patreonack || 0 === $opt_results->patreonack ) && ( null === $opt_results->patreonaccess || 0 === $opt_results->patreonaccess ) && ( null === $opt_results->patreonrefresh || 0 === $opt_results->patreonrefresh ) ) {

				$patreon = '
				<div class="wpbooklist-storytime-patreon-div">
				<div>
					<p class="wpbooklist-storytime-p-1">' . $this->trans->trans_90 . '<img class="wpbooklist-storytime-shocked-img" src="' . ROOT_IMG_ICONS_URL . 'shocked.svg"/></p>
					<p class="wpbooklist-storytime-p-2">' . $this->trans->trans_404 . '<a href="https://www.patreon.com/wpbooklist"><img class="wpbooklist-storytime-patreon-img" src="' . ROOT_IMG_URL . 'patreon-cropped.png" /></a>&nbsp;' . $this->trans->trans_405 . '</p><p class="wpbooklist-storytime-patreon-line"></p>
					<p class="wpbooklist-tab-intro-para"><a class="wpbooklist-storytime-for-just-link" href="https://www.patreon.com/wpbooklist">' . $this->trans->trans_406 . '<span>&nbsp;' . $this->trans->trans_407 . '</span></a>&nbsp;' . $this->trans->trans_408 . '</p>
					<ul>
					<li><span class="wpbooklist-storytime-tilde">~</span>' . $this->trans->trans_409 . '<span class="wpbooklist-storytime-tilde">~</span></li>
					<li><span class="wpbooklist-storytime-tilde">~</span>' . $this->trans->trans_410 . '<span class="wpbooklist-storytime-tilde">~</span></li>
					<li><span class="wpbooklist-storytime-tilde">~</span>' . $this->trans->trans_411 . '<span class="wpbooklist-storytime-tilde">~</span></li>
					<li><span class="wpbooklist-storytime-tilde">~</span>' . $this->trans->trans_412 . '<span class="wpbooklist-storytime-tilde">~</span></li>
					<li><span class="wpbooklist-storytime-tilde">~</span>' . $this->trans->trans_413 . '<span class="wpbooklist-storytime-tilde">~</span></li>
					</ul>
					<p class="wpbooklist-tab-intro-para">' . $this->trans->trans_414 . ' <a id="wpbooklist-storytime-for-demo-link" href="#wpbooklist-storytime-demo-top-cont">(' . $this->trans->trans_415 . '&nbsp;' . $this->trans->trans_416 . '&nbsp' . $this->trans->trans_417 . ')</a> - ' . $this->trans->trans_418 . '<br/><br/></p>
					<p class="wpbooklist-tab-intro-para">' . $this->trans->trans_419 . '<br/>' . $this->trans->trans_420 . '</p><p class="wpbooklist-storytime-patreon-line"></p><br/><br/>
					<div class="wpbooklist-storytime-signup-div">
					<div class="wpbooklist-storytime-signup-div-left">
						<p class="wpbooklist-storytime-signup-button-p">' . $this->trans->trans_421 . ':</p>
						<p class="wpbooklist-storytime-signup-button-div">' . $this->trans->trans_422 . '</p>
						<img src="' . ROOT_IMG_URL . 'patreonsquare.jpg" />
					</div>
					<div class="wpbooklist-storytime-signup-div-middle">
						<img src="' . ROOT_IMG_URL . 'redo.svg" />
					</div>
					<div class="wpbooklist-storytime-signup-div-right">
						<p class="wpbooklist-storytime-signup-button-p">' . $this->trans->trans_423 . ':</p>
						<p class="wpbooklist-storytime-signup-button-div">' . $this->trans->trans_424 . '</p>
						<img src="' . ROOT_IMG_URL . 'accept.svg" />
					</div>
					</div>
				</div>
				</div>';

				$demo_header = '
				<div class="wpbooklist-storytime-demo-top-cont" id="wpbooklist-storytime-demo-top-cont">
				<p>' . $this->trans->trans_425 . '<span>' . $this->trans->trans_57 . '<br/>' . $this->trans->trans_244 . '<img src="' . ROOT_IMG_ICONS_URL . 'storytime.svg" /></span>&nbsp;' . $this->trans->trans_417 . '</p>
				</div>';

				$storytime_reader = '
				<div class="wpbooklist-storytime-reader-top-cont">
					<div id="wpbooklist-storytime-reader-inner-cont">

					<div id="wpbooklist-storytime-reader-titlebar-div">
						<div class="wpbooklist-storytime-reader-titlebar-div-1">
						<img src="' . ROOT_IMG_ICONS_URL . 'storytime.svg" />
						<p class="wpbooklist-tab-intro-para">' . $this->trans->trans_426 . '</p>
						</div>
						<div id="wpbooklist-storytime-reader-titlebar-div-2">
						<h2>' . $this->trans->trans_427 . '...</h2>
						</div>
					</div>

					<div class="wpbooklist-storytime-reader-selection-div">
						<div id="wpbooklist-storytime-reader-selection-div-1-inner-1">
						<select id="wpbooklist-storytime-category-select">
							<option selected default disabled>' . $this->trans->trans_428 . '...</option>
							<option>' . $this->trans->trans_429 . '</option>
							' . $categories_string . '
						</select>
						' . $recent_string . '
						</div>
						<div id="wpbooklist-storytime-reader-selection-div-1-inner-2" data-location="backend">
						<p class="wpbooklist-tab-intro-para">' . $this->trans->trans_430 . '...</p>
						<img src="' . ROOT_IMG_URL . 'next-down.png" />
						</div>
					</div>
					<div id="wpbooklist-storytime-reader-content-div" data-location="backend">
					</div>
					<div id="wpbooklist-storytime-reader-pagination-div">
						<div id="wpbooklist-storytime-reader-pagination-div-1">
						<img src="' . ROOT_IMG_URL . 'next-left.png" />
						</div>
						<div class="wpbooklist-storytime-reader-pagination-div-2">
						<div class="wpbooklist-storytime-reader-pagination-div-2-inner">
							<p class="wpbooklist-tab-intro-para">
							<span id="wpbooklist-storytime-reader-pagination-div-2-span-1">' . $this->trans->trans_431 . '</span><span id="wpbooklist-storytime-reader-pagination-div-2-span-2">/</span><span id="wpbooklist-storytime-reader-pagination-div-2-span-3">' . $this->trans->trans_432 . '</span>
							</p>
						</div>
						</div>
						<div id="wpbooklist-storytime-reader-pagination-div-3">
						<img src="' . ROOT_IMG_URL . 'next-right.png" />
						</div>
					</div>
					<div id="wpbooklist-storytime-reader-provider-div">
						<div id="wpbooklist-storytime-reader-provider-div-1">
						<img src="' . ROOT_IMG_URL . 'icon-256x256.png" />
						</div>
						<div id="wpbooklist-storytime-reader-provider-div-2">
						<p id="wpbooklist-storytime-reader-provider-p-1"> ' . $this->trans->trans_433 . '</p>
						<p id="wpbooklist-storytime-reader-provider-p-2">' . $this->trans->trans_434 . '</p>
						</div>
						<div id="wpbooklist-storytime-reader-provider-div-delete">
						</div>
					</div>
					</div>
				</div>';

				$advertise = '<div class="wpbooklist-storytime-provider-advert-div">
					<p class="wpbooklist-tab-intro-para">' . $this->trans->trans_435 . ' <span class="wpbooklist-color-orange-italic">' . $this->trans->trans_231 . '?		 </span><br/><br/>' . $this->trans->trans_436 . ' <span class="wpbooklist-color-orange-italic">' . $this->trans->trans_231 . '</span> ' . $this->trans->trans_437 . '</p>
					<p class="wpbooklist-tab-intro-para">' . $this->trans->trans_438 . ' <a href="mailto:advertising@wpbooklist.com">Advertising@WPBookList.com</a> ' . $this->trans->trans_439 . '!</p>
				</div>';
			} else {
				$patreon          = '';
				$demo_header      = '';
				$storytime_reader = '
				<div class="wpbooklist-storytime-reader-top-cont">
					<div id="wpbooklist-storytime-reader-inner-cont">

					<div id="wpbooklist-storytime-reader-titlebar-div">
						<div class="wpbooklist-storytime-reader-titlebar-div-1">
						<img src="' . ROOT_IMG_ICONS_URL . 'storytime.svg" />
						<p class="wpbooklist-tab-intro-para">' . $this->trans->trans_426 . '</p>
						</div>
						<div id="wpbooklist-storytime-reader-titlebar-div-2">
						<h2>' . $this->trans->trans_427 . '...</h2>
						</div>
					</div>

					<div class="wpbooklist-storytime-reader-selection-div">
						<div id="wpbooklist-storytime-reader-selection-div-1-inner-1">
						<select id="wpbooklist-storytime-category-select">
							<option selected default disabled>' . $this->trans->trans_428 . '...</option>
							<option>' . $this->trans->trans_429 . '</option>
							' . $categories_string . '
						</select>
						' . $recent_string . '
						</div>
						<div id="wpbooklist-storytime-reader-selection-div-1-inner-2" data-location="backend">
						<p class="wpbooklist-tab-intro-para">' . $this->trans->trans_430 . '...</p>
						<img src="' . ROOT_IMG_URL . 'next-down.png" />
						</div>
					</div>
					<div id="wpbooklist-storytime-reader-content-div" data-location="backend">

					</div>
					<div id="wpbooklist-storytime-reader-pagination-div">
						<div id="wpbooklist-storytime-reader-pagination-div-1">
						<img src="' . ROOT_IMG_URL . 'next-left.png" />
						</div>
						<div class="wpbooklist-storytime-reader-pagination-div-2">
						<div class="wpbooklist-storytime-reader-pagination-div-2-inner">
							<p class="wpbooklist-tab-intro-para">
							<span id="wpbooklist-storytime-reader-pagination-div-2-span-1">' . $this->trans->trans_431 . '</span><span id="wpbooklist-storytime-reader-pagination-div-2-span-2">/</span><span id="wpbooklist-storytime-reader-pagination-div-2-span-3">' . $this->trans->trans_432 . '</span>
							</p>
						</div>
						</div>
						<div id="wpbooklist-storytime-reader-pagination-div-3">
						<img src="' . ROOT_IMG_URL . 'next-right.png" />
						</div>
					</div>
					<div id="wpbooklist-storytime-reader-provider-div">
						<div id="wpbooklist-storytime-reader-provider-div-1">
						<img src="' . ROOT_IMG_URL . 'icon-256x256.png" />
						</div>
						<div id="wpbooklist-storytime-reader-provider-div-2">
						<p id="wpbooklist-storytime-reader-provider-p-1"> ' . $this->trans->trans_433 . '</p>
						<p id="wpbooklist-storytime-reader-provider-p-2">' . $this->trans->trans_434 . '</p>
						</div>
						<div id="wpbooklist-storytime-reader-provider-div-delete">
						</div>
					</div>
					</div>
				</div>';

				$advertise = '<div style="position:absolute; bottom:-145px;" class="wpbooklist-storytime-provider-advert-div">
					<p class="wpbooklist-tab-intro-para">' . $this->trans->trans_435 . ' <span class="wpbooklist-color-orange-italic">' . $this->trans->trans_231 . '?		 </span><br/><br/>' . $this->trans->trans_436 . ' <span class="wpbooklist-color-orange-italic">' . $this->trans->trans_231 . '</span> ' . $this->trans->trans_437 . '</p>
					<p class="wpbooklist-tab-intro-para">' . $this->trans->trans_438 . ' <a href="mailto:advertising@wpbooklist.com">Advertising@WPBookList.com</a> ' . $this->trans->trans_439 . '!</p>
				</div>';
			}

			$string1 = '
			<div id="wpbooklist-addbook-container">
				<p class="wpbooklist-tab-intro-para">' . $this->trans->trans_443 . ' <span class="wpbooklist-storytime-word-actual">' . $this->trans->trans_244 . '</span>&nbsp;' . $this->trans->trans_292 . '</br></br><span class="wpbooklist-storytime-word-actual">' . $this->trans->trans_244 . '</span>&nbsp;' . $this->trans->trans_444 . '&nbsp;<span class="wpbooklist-color-orange-italic">' . $this->trans->trans_445 . '</span>&nbsp;' . $this->trans->trans_442 . '<br/><br/>' . $this->trans->trans_441 . '.
				</p>
				<p class="wpbooklist-tab-intro-para">' . $this->trans->trans_440 . ':&nbsp;<strong>[wpbooklist_storytime]</strong></p>
				<br/>
				<br/>
					' . $patreon . '
					' . $advertise . '
				' . $demo_header . $storytime_reader . '
			</div>
			';

			return $string1;
		}


	}

endif;
