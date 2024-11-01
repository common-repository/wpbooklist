<?php
/**
 * WPBookList API Settings Tab
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_Amazon_Localization_Settings', false ) ) :
/**
 * WPBookList_Amazon_Localization_Settings Class.
 */
class WPBookList_Amazon_Localization_Settings {

    public function __construct() {
        require_once(CLASS_DIR.'class-admin-ui-template.php');
        require_once(CLASS_DIR.'class-amazonlocalization-settings-form.php');
        // Instantiate the class
        $this->template = new WPBookList_Admin_UI_Template;
        $this->form = new WPBookList_Amazon_Localization_Settings_Form;
        $this->output_open_admin_container();
        $this->output_tab_content();
        $this->output_close_admin_container();
        $this->output_admin_template_advert();

        // Get Translations.
        require_once CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
        $this->trans = new WPBookList_Translations();
        $this->trans->trans_strings();

    }

    private function output_open_admin_container(){
        $title = __('Amazon Localization','wpbooklist');
        $icon_url = ROOT_IMG_ICONS_URL.'localization.svg';

        // Get Translations.
        require_once CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
        $this->trans = new WPBookList_Translations();
        $this->trans->trans_strings();

        // HTML to clear the WPBookList Transients.
        echo '<div id="wpbooklist-cache-clear-wrapper">
                <img class="wpbooklist-icon-image-question" data-label="book-settings-clearcache" src="' . ROOT_IMG_ICONS_URL . 'question-black.svg">
                <button id="wpbooklist-cache-clear-button">' . $this->trans->trans_501 . '</button>
                <div class="wpbooklist-spinner" id="wpbooklist-spinner-cache"></div>
            </div>';

        echo $this->template->output_open_admin_container($title, $icon_url);
    }

    private function output_tab_content(){
        echo $this->form->output_amazon_localization_settings_form();
    }

    private function output_close_admin_container(){
        echo $this->template->output_close_admin_container();
    }

    private function output_admin_template_advert(){
        echo $this->template->output_template_advert();
    }


}
endif;

// Instantiate the class
$cm = new WPBookList_Amazon_Localization_Settings;