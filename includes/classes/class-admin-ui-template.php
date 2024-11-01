<?php
/**
 * WPBookList Admin UI Template Class
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  6.1.5.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Admin_UI_Template', false ) ) :
	/**
	 * WPBookList_Admin_Menu Class.
	 */
	class WPBookList_Admin_UI_Template {


		/** Function that outputs the beginning of the admin container.
		 *
		 *  @param string $title - The string that contains the title for that tab.
		 *  @param string $iconurl - The string that contains the icon's URL for that tab.
		 */
		public static function output_open_admin_container( $title, $iconurl ) {
			return '<div class="wpbooklist-admin-top-container">
				<p class="wpbooklist-admin-top-title"><img class="wpbooklist-admin-top-title-icon" src="' . $iconurl . '" />' . $title . '</p>
				<div class="wpbooklist-admin-top-inner-container">';
		}

		/**
		 *  Closes the Admin Container.
		 */
		public static function output_close_admin_container() {
			return '</div></div>';
		}

		/**
		 *  Outputs the Bottom advertisment area that appears on every page.
		 */
		public static function output_template_advert() {
			return '<div class="wpbooklist-admin-advert-container">
					<div id="wpbooklist-admin-advert-flex-container">
					<div class="wpbooklist-admin-advert-site-div">
						<div class="wpbooklist-admin-advert-visit-me-title">For Everything WPBookList</div>
						<a target="_blank" class="wpbooklist-admin-advert-visit-me-link" href="https://wpbooklist.com/">
							<img src="' . ROOT_IMG_URL . 'wpblsiteimage.png">
							WPBookList.com
						</a>
					</div>
					<div class="wpbooklist-admin-advert-site-div">
						<div class="wpbooklist-admin-advert-visit-me-title">For Everything WPGameList</div>
						<a target="_blank" class="wpbooklist-admin-advert-visit-me-link" href="https://wordpress.org/plugins/wpgamelist/">
							<img src="' . ROOT_IMG_URL . 'wpglsiteimage.png">
							WPGameList.com
						</a>
					</div>
					</div>
					<p id="wpbooklist-admin-advert-email-me">E-mail with questions, issues, concerns, suggestions, or anything else at <a href="mailto:general@wpbooklist.com">General@wpbooklist.com</a></p>
					<div id="wpbooklist-facebook-link-div">
					<a href="https://www.facebook.com/WPGameList-490463747966630/" target="_blank"><img height="34" style="border:0px;height:34px;" src="' . ROOT_IMG_URL . 'fbadvert.png" border="0" alt="Visit WPGameList of facebook!"></a>
					</div>
					<div id="wpbooklist-admin-advert-money-container">
						<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
						<input type="hidden" name="cmd" value="_s-xclick">
						<input type="hidden" name="hosted_button_id" value="VUVFXRUQ462UU">
						<input type="image" src="' . ROOT_IMG_URL . 'paypaldonate.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
						<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
						</form>

						<a target="_blank" id="wpbooklist-patreon-link" href="https://www.patreon.com/wpbooklist"><img id="wpbooklist-patreon-img" src="' . ROOT_IMG_URL . 'becomemypatreon.png"></a>
						<a href="https://ko-fi.com/A8385C9" target="_blank"><img height="34" style="border:0px;height:34px;" src="' . ROOT_IMG_URL . 'kofi1.png" border="0" alt="Buy Me a Coffee at ko-fi.com"></a>
						<p>And be sure to <a target="_blank" href="https://wordpress.org/support/plugin/wpbooklist/reviews/">leave a 5-star review of WPBookList!</a></p>
					</div>
				</div>';
		}

	}

endif;


