<?php
/**
 * WPBookList Custom Libraries Form Tab Class
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_Custom_Libraries_Form', false ) ) :
/**
 * WPBookList_Admin_Menu Class.
 */
class WPBookList_Custom_Libraries_Form {

	/**
	 * Class Constructor - Simply calls the Translations
	 */
	public function __construct() {

		// Get Translations.
		require_once CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
		$this->trans = new WPBookList_Translations();
		$this->trans->trans_strings();
	}

	public function output_custom_libraries_form(){
		global $wpdb;

		// Set the current WordPress user.
		$currentwpuser = wp_get_current_user();

		// Now we'll determine access, and stop all execution if user isn't allowed in.
		require_once CLASS_UTILITIES_DIR . 'class-wpbooklist-utilities-accesscheck.php';
		$this->access          = new WPBookList_Utilities_Accesscheck();
		$this->currentwpbluser = $this->access->wpbooklist_accesscheck( $currentwpuser->ID, 'settings' );

		// If we received false from accesscheck class, display permissions message.
		if ( false === $this->currentwpbluser ) {

			// Outputs the 'No Permission!' message.
			$this->initial_output = $this->access->wpbooklist_accesscheck_no_permission_message();
			return $this->initial_output;
		}


		$table_name = $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names';
		$db_row = $wpdb->get_results("SELECT * FROM $table_name");

		$string1 = '<div id="wpbooklist-custom-libraries-container">
				<p class="wpbooklist-tab-intro-para">'.__('Create custom Libraries here, and read all about how the various shortcodes can be used and their arguments further down this page.', 'wpbooklist').'</p>';

		$string2 = '<table style="width: 100%; text-align: center; color: #555; border: 1px solid #ccc;">
						<tbody>
	          				<tr colspan="2">
	          					<td colspan="2">
	          						<p id="wpbooklist-use-shortcodes"></p>
	          					</td>
	  						</tr>';

  						$counter = 0;
          
          				$string3 = '';
						foreach($db_row as $db){
							$counter++;
							if(($db->user_table_name != "") || ($db->user_table_name != null)){
								$string3 = $string3.'<tr><td><p class="wpbooklist-bold-shortcode">[wpbooklist_shortcode table="'.$db->user_table_name.'"]</p></td><td><button id="'.$db->user_table_name.'_'.$counter.'" class="wpbooklist_delete_custom_lib" type="button" >'.__('Delete Library','wpbooklist').'</button></td></tr>'; 
							}
						}


		$string4 =
							'<tr>
								<td>
									<input type="text" value="'. $this->trans->trans_54 .'..." class= "wpbooklist-dynamic-input" id="wpbooklist-dynamic-input-library" name="wpbooklist-dynamic-input"></input>
								</td>
								<td>
									<button id="wpbooklist-dynamic-shortcode-button" type="button" disabled="true">'.__('Create New Library','wpbooklist').'</button>
								</td>
							</tr>
				        </tbody>
				    </table>
					<p style="margin-top:45px;">
						<span class="wpbooklist-bold-shortcode">[wpbooklist_shortcode]</span> - ('.__('default shortcode for default library','wpbooklist').')
					</p>
					<p class="wpbooklist-tab-intro-para-left">'.__('By specifying the \'Action\' argument in the default shortcode above, you can control what happens when a user clicks on a book cover or title. For example, if you\'d rather have the visitor directed to a book\'s Amazon page, simply add the \'Action\' argument like so:','wpbooklist').'</p>
					<p class="wpbooklist-tab-intro-para-left">
						<span class="wpbooklist-bold-shortcode-orange">[wpbooklist_shortcode action="amazon"]</span>
					</p>
					<ul style="list-style: disc; margin-left: 56px;">
						<li><span class="wpbooklist-bold-li-heading">'.__('All Available Action Values:','wpbooklist').'</span>
							<ul class="wpbooklist-bold-shortcode-sub-ul">
								<li>action="bookview"</li>
								<li>action="amazon"</li>
								<li>action="googlebooks"</li>
								<li>action="ibooks"</li>
								<li>action="booksamillion"</li>
								<li>action="kobo"</li>
							</ul>
						</li>
					</ul>
					<p style="margin-top:50px;">
						<span class="wpbooklist-bold-shortcode">[showbookcover]</span> - ('.__('shortcode for displaying Individual Books','wpbooklist').' - <a style="display: inline-block; width:initial;" target="_blank" href="https://wpbooklist.com/index.php/2017/09/22/wpbooklist-shortcode-guide/">Read the Guide</a>)
					</p>
					<ul style="list-style: disc; margin-left: 56px;">
						<li><span class="wpbooklist-bold-li-heading">'.__('Specify a book:', 'wpbooklist').'</span> isbn="xxxxxxxxxxxxx"</li>
						<li><span class="wpbooklist-bold-li-heading">'.__('Set Alignment:', 'wpbooklist').'</span> align="left"  <span style="font-style:italic;">or </span>align="right"</li>
						<li><span class="wpbooklist-bold-li-heading">'.__('Specify Library:', 'wpbooklist').'</span> table="nameoflibrary" ('.__('leave out to use default library', 'wpbooklist').')</li>
						<li><span class="wpbooklist-bold-li-heading">'.__('Set the Display:', 'wpbooklist').'</span> display="justimage"  <span style="font-style:italic;">or </span>display="excerpt"</li>
						<li><span class="wpbooklist-bold-li-heading">'.__('Set the Size:', 'wpbooklist').'</span> width="100"</li>
						<li><span class="wpbooklist-bold-li-heading">'.__('Specify the Action:', 'wpbooklist').'</span>
							<ul class="wpbooklist-bold-shortcode-sub-ul">
								<li>action="bookview"</li>
								<li>action="amazon"</li>
								<li>action="googlebooks"</li>
								<li>action="ibooks"</li>
								<li>action="booksamillion"</li>
								<li>action="kobo"</li>
							</ul>
						</li>
					</ul>
					<p class="wpbooklist-tab-intro-para-left">'.__('So, for example, to display just a book\'s cover from your default library on the left side of a page or post, with a size of 100, this shortcode would do the trick:','wpbooklist').'<br/><span class="wpbooklist-bold-shortcode-orange">[showbookcover display="justimage" isbn="123456789912" align="left" width="100"]</p>
					<p class="wpbooklist-tab-intro-para-left">'.__('To display a book and it\'s excerpt from a custom library, with a size of 200, this shortcode would do the trick:', 'wpbooklist').'</br><span class="wpbooklist-bold-shortcode-orange">[showbookcover display="excerpt" table="fiction" isbn="123456789912" align="left" width="200"]</p>
					<p class="wpbooklist-tab-intro-para-left">'.__('To display a book\'s cover, that links to it\'s Amazon page, from your default library, with a size of 150, this shortcode would do the trick:','wpbooklist').'</br><span class="wpbooklist-bold-shortcode-orange">[showbookcover action="amazon" isbn="123456789912" width="150"]</p>
					<p style="text-align:center; margin-bottom:50px; margin-top:50px;"><a href="https://wpbooklist.com/index.php/2017/09/22/wpbooklist-shortcode-guide/">'.__('Be sure to read this detailed post on all of the different ways that the WPBookList Shortcode can be used!','wpbooklist').'</a></p>
					</div>';




				



		

		echo $string1.$string2.$string3.$string4;

	}


}

endif;