<?php
/**
 * WPBookList_Storytime Class - class-wpbooklist-storytime.php
 *
 * @author   Jake Evans
 * @category Root Product
 * @package  Includes/Classes/Storytime
 * @version  6.1.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( ' WPBookList_Storytime', false ) ) :

	/**
	 *  WPBookList_Storytime Class.
	 */
	class  WPBookList_Storytime {

		public $table;
		public $default_array1;
		public $default_array2;
		public $default_array3;
		public $default_array4;
		public $default_array5;
		public $default_array6;
		public $category;
		public $category_change_output;
		public $id;
		public $post_type;
		public $reader_shortcode_output;
		public $default_mask_array  = array();
		public $stories_db_data     = array();
		public $incoming_story_data = array();
		public $create_page_result  = '';
		public $create_post_result  = '';

		/** Class Constructor.
		 *
		 *  @param string $action - The string that will determine what functions run.
		 *  @param string $category - The category of a particular story.
		 *  @param int    $id - The id that determines a particular story.
		 *  @param array  $incoming_story_data - The array that holds all the particulars for an individual Story.
		 */
		public function __construct( $action, $category = null, $id = null, $incoming_story_data = null ) {

			global $wpdb;

			// Checking if table exists.
			$test_name = $wpdb->prefix . 'wpbooklist_jre_storytime_stories';
			if ( $test_name === $wpdb->get_var( "SHOW TABLES LIKE '$test_name'" ) ) {

				// Default class-wide variables.
				$this->table               = $wpdb->prefix . 'wpbooklist_jre_storytime_stories';
				$this->stories_db_data     = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'wpbooklist_jre_storytime_stories' );
				$this->default_mask_array  = array( '%s', '%s', '%s', '%s', '%s', '%s' );
				$this->category            = $category;
				$this->id                  = $id;
				$this->incoming_story_data = $incoming_story_data;

				if ( 'install' === $action ) {
					$this->create_default_data();
					$this->insert_default_data();
				}

				if ( 'categorychange' === $action ) {
					$this->category_change();
				}

				if ( 'getcontent' === $action ) {
					$this->get_content();
				}

				if ( 'createpage' === $action ) {
					$this->post_type = 'page';
					$this->create_page_category();
					$this->create_page();
				}

				if ( 'createpost' === $action ) {
					$this->post_type = 'post';
					$this->create_post_category();
					$this->create_post();
				}

				if ( 'frontend_shortcode_output' === $action ) {
					$this->output_storytime_reader();
				}

			}
		}


		/** Function to create a new Storytime admin message.
		 *
		 *  @param array $data - The array that holds all the particulars for creating this admin notice.
		 */
		public function create_admin_message_html( $data = array() ) {

			return '
			<div id="wpbooklist-notice-holder">
				<div id="wpbooklist-storytime-notify-div">
					<div id="wpbooklist-storytime-notify-div-inner-1">
						<img src="' . $data['providerimg'] . '" />
					</div>
					<div id="wpbooklist-storytime-notify-div-inner-2">
						<h2>A New</h2>
						<div id="wpbooklist-storytime-notify-span-div">
							<span id="wpbooklist-storytime-notify-span-div1" class="wpbooklist-color-orange-italic">WPBookList</span> 
							<span id="wpbooklist-storytime-notify-span-div2">StoryTime</span> 
							<img id="wpbooklist-storytime-notify-span-div-img-1" src="' . ROOT_IMG_ICONS_URL . 'storytime.svg" />
						</div>
						<h2>Story Has Arrived!</h2>
						<p>Be sure to check out <span id="wpbooklist-storytime-notify-span1">"' . $data['title'] . '"</span>, from <span id="wpbooklist-storytime-notify-span2">' . $data['providername'] . '</span></p>
						<a href="' . admin_url() . 'admin.php?page=WPBookList-Options-storytime">Check it out now</a>
					</div>
					<div class="wpbooklist-my-notice-dismiss-forever" id="wpbooklist-my-notice-dismiss-forever-storytime">Dismiss</div>
					<button type="button" class="notice-dismiss">
						<span class="screen-reader-text">Dismiss</span>
					</button>
				</div>
			</div>
			<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss</span></button>';
		}

		/**
		 *  Function that creates a post for a new story.
		 */
		private function create_post() {

			// Initialize the page ID to -1. This indicates no action has been taken.
			$this->create_post_result = -1;

			$excerpt = wp_trim_words( $this->incoming_story_data['content'], 40, ' ... ' );

			if ( '' === $excerpt || null === $excerpt ) {
				$excerpt = $this->title;
			}

			if ( '' === $excerpt || null === $excerpt ) {
				$excerpt = 'No excerpt available';
			}

			// Set the post ID so that we know the post was created successfully.
			$this->create_post_result = wp_insert_post(
				array(
					'comment_status' => 'open',
					'ping_status'    => 'closed',
					'post_author'    => get_current_user_id(),
					'post_name'      => $this->incoming_story_data['title'],
					'post_title'     => wp_strip_all_tags( $this->incoming_story_data['title'] ),
					'post_status'    => 'publish',
					'post_type'      => $this->post_type,
					'post_content'   => '<div class="wpbooklist-page-content">DO NOT DELETE</div>',
					'post_excerpt'   => $excerpt,
				)
			);

			// Assign the category to our new post.
			$get_cat_id = get_cat_ID( 'WPBookList StoryTime Post' );
			$cat_slug   = 'wpbooklist-storytime-post-cat';
			if ( 0 < $this->create_post_result ) {

				$db_result = $this->add_to_db_post();
				$this->create_post_image( $this->incoming_story_data['providerimg'], $this->create_post_result );

				wp_set_post_terms( $this->create_post_result, array( $get_cat_id ), 'category' );
			}
		}

		/**
		 *  Function that creates a page for a new story.
		 */
		private function create_page() {
			global $wpdb;
			$post = get_page_by_title( $this->incoming_story_data['title'], 'OBJECT', 'page' );

			$post_data = array(
				'post_title'    => wp_strip_all_tags( $this->incoming_story_data['title'] ),
				'post_name'     => $this->incoming_story_data['title'] . ' (page)',
				'post_status'   => 'publish',
				'post_type'     => $this->post_type,
				'post_author'   => get_current_user_id(),
				'post_content'  => $this->incoming_story_data['content'],
				'post_category' => array( 'wpbooklist-storytime-page-cat' ),
			);

			$error_obj                = false;
			$this->create_page_result = wp_insert_post( $post_data, $error_obj );

			if ( ! isset( $post ) ) {
				add_action( 'admin_init', 'hbt_create_post' );
				if ( ! $error_obj ) {
					$db_result = $this->add_to_db_page();
					$this->create_page_image( $this->incoming_story_data['providerimg'], $this->create_page_result );
					if ( 1 === $db_result ) {
						return $this->create_page_result;
					}
				}
			}
		}

		private function create_post_category(){
			// Create default WPBookList Book Post Category if it doesn't already exist
			$create_cat = true;
			$cat_id = 0;
			foreach((get_categories()) as $category) {
				if ( $category->cat_name == 'WPBookList StoryTime Post' ) {
					$cat_id = get_cat_ID('WPBookList StoryTime Post');
					$create_cat = false;
				}
			}

			if ( $create_cat == false){
				return $cat_id;
				
			} else {
				$result = wp_insert_term(
					'WPBookList StoryTime Post',
					'category',
					array(
					  'description'	=> 'This is a category created by WPBookList StoryTime to display individual Stories on thier very own individual posts. ',
					  'slug' 		=> 'wpbooklist-storytime-post-cat'
					)
				);

				if (is_object( $result)){
					//TODO: Log messages here. This part here will fire if the category already exists, and apparently $result will be a wp error object
					$this->cat_create_result = $result;
				} else {
					$this->cat_create_result = $result['term_id'];
				}
			}
		}

		private function create_page_category(){
			// Create default WPBookList StoryTime Category if it doesn't already exist
			$create_cat = true;
			$cat_id = 0;
			foreach((get_categories()) as $category) {
				if ( $category->cat_name == 'WPBookList StoryTime Page' ) {
					$cat_id = get_cat_ID('WPBookList StoryTime Page');
					$create_cat = false;
				}
			}

			if ( $create_cat == false){
				return $cat_id;
				
			} else {
				$result = wp_insert_term(
					'WPBookList StoryTime Page',
					'category',
					array(
					  'description'	=> 'This is a category created by WPBookList StoryTime to display individual Stories on thier very own individual pages',
					  'slug' 		=> 'wpbooklist-storytime-page-cat'
					)
				);

				if (is_object( $result)){
					//TODO: Log messages here. This part here will fire if the category already exists, and apparently $result will be a wp error object
					$this->cat_create_result = $result;
				} else {
					$this->cat_create_result = $result['term_id'];
				}
			}
		}

		private function create_page_image( $image_url, $post_id  ){
			global $wpdb;
			$upload_dir = wp_upload_dir();
			$image_data = file_get_contents( $image_url);

			$image_url = str_replace('%', '', $image_url);

			$filename = basename( $image_url);
			if (wp_mkdir_p( $upload_dir['path']))	 $file = $upload_dir['path'] . '/' . $filename;
			else									$file = $upload_dir['basedir'] . '/' . $filename;
			file_put_contents( $file, $image_data);

			$wp_filetype = wp_check_filetype( $filename, null );
			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => sanitize_file_name( $filename),
				'post_status' => 'inherit'
			);
			$attach_id = wp_insert_attachment( $attachment, $file, $post_id );
			require_once(ABSPATH . 'wp-admin/includes/image.php');
			$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
			$res1= wp_update_attachment_metadata( $attach_id, $attach_data );
			$res2= set_post_thumbnail( $post_id, $attach_id );
		}

		private function create_post_image( $image_url, $post_id  ){
			$upload_dir = wp_upload_dir();
			$image_data = file_get_contents( $image_url);

			$image_url = str_replace('%', '', $image_url);

			$filename = basename( $image_url);
			if (wp_mkdir_p( $upload_dir['path']))	 $file = $upload_dir['path'] . '/' . $filename;
			else									$file = $upload_dir['basedir'] . '/' . $filename;
			file_put_contents( $file, $image_data);

			$wp_filetype = wp_check_filetype( $filename, null );
			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => sanitize_file_name( $filename),
				'post_content' => '',
				'post_status' => 'inherit'
			);
			$attach_id = wp_insert_attachment( $attachment, $file, $post_id );
			require_once(ABSPATH . 'wp-admin/includes/image.php');
			$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
			$res1= wp_update_attachment_metadata( $attach_id, $attach_data );
			$res2= set_post_thumbnail( $post_id, $attach_id );
		}

		private function add_to_db_page(){
			global $wpdb;
			$insert_array = array(
				'pageid' => $this->create_page_result, 
			);

			$format = array( '%d'); 
			$where = array( 'title' => $this->incoming_story_data['title'], 'providername' =>  $this->incoming_story_data['providername']);
			$where_format = array( '%s', '%s' );
			$wpdb->update( $this->table, $insert_array, $where, $format, $where_format );

		}

		private function add_to_db_post(){
			global $wpdb;
			$insert_array = array(
				'postid' => $this->create_post_result, 
			);

			$format = array( '%d'); 
			$where = array( 'title' => $this->incoming_story_data['title'], 'providername' =>  $this->incoming_story_data['providername']);
			$where_format = array( '%s', '%s' );
			$wpdb->update( $this->table, $insert_array, $where, $format, $where_format );

		}

		private function get_content(){


			global $wpdb;
			$this->stories_db_data = $wpdb->get_row("SELECT * FROM $this->table WHERE ID = $this->id");

			$this->stories_db_data->providerbio = stripslashes( $this->stories_db_data->providerbio);
			

		}

		private function category_change(){

			global $wpdb;

			// Build the Categories string
			$categories_string = '<select id="wpbooklist-storytime-category-select"><option disabled>Select a Category...</option><option>Recent Additions</option>';
			$cat_array = array();
			foreach ( $this->stories_db_data as $key => $value) {
				array_push( $cat_array, $value->category);
			}
			$cat_array = array_unique( $cat_array);
			foreach ( $cat_array as $key => $value) {
			  if ( $value == $this->category){
					$categories_string = $categories_string. '<option selected value="' . $value. '">' . $value. '</option>';
				} else {
					$categories_string = $categories_string. '<option value="' . $value. '">' . $value. '</option>';
				}
			}

			if ( $this->category == 'Recent Additions' ) {

				// Build the Entries string
				$entries_string = '';
				$this->stories_db_data = array_reverse( $this->stories_db_data);
				foreach ( $this->stories_db_data as $key => $value) {
					if ( $key <= 9){
				  		$entries_string = $entries_string. '<p class="wpbooklist-storytime-listed-story" data-id="' . $value->ID. '">' . $value->title. '</p>';
			  		}
				}

			} else {
				// Build the Entries string
				$entries_string = '';
				foreach ( $this->stories_db_data as $key => $value) {
					if ( $value->category == $this->category){
				  		$entries_string = $entries_string. '<p class="wpbooklist-storytime-listed-story" data-id="' . $value->ID. '">' . $value->title. '</p>';
			  		}
				}
			}

			

			$this->category_change_output = $categories_string. '</select>' . $entries_string;

		}

		private function insert_default_data(){

			global $wpdb;

			// Check and see if this content has already been added to this website
			$stories_db_data = $wpdb->get_results("SELECT * FROM $this->table");

			$duplicate1 = false;
			foreach ( $stories_db_data as $key => $value) {
			  if ( $this->default_array1['providername'] == $value->providername && $this->default_array1['title'] == $value->title){
				$duplicate1 = true;
			  }
			}
			if ( $duplicate1 == false){
				$result1 = $wpdb->insert( $this->table, $this->default_array1, $this->default_mask_array);
			}

			$duplicate2 = false;
			foreach ( $stories_db_data as $key => $value) {
			  if ( $this->default_array2['providername'] == $value->providername && $this->default_array2['title'] == $value->title){
				$duplicate2 = true;
			  }
			}
			if ( $duplicate2 == false){
				$result2 = $wpdb->insert( $this->table, $this->default_array2, $this->default_mask_array);
			}

			$duplicate3 = false;
			foreach ( $stories_db_data as $key => $value) {
			  if ( $this->default_array3['providername'] == $value->providername && $this->default_array3['title'] == $value->title){
				$duplicate3 = true;
			  }
			}
			if ( $duplicate3 == false){
				$result3 = $wpdb->insert( $this->table, $this->default_array3, $this->default_mask_array);
			}

			$duplicate4 = false;
			foreach ( $stories_db_data as $key => $value) {
			  if ( $this->default_array4['providername'] == $value->providername && $this->default_array4['title'] == $value->title){
				$duplicate4 = true;
			  }
			}
			if ( $duplicate4 == false){
				$result4 = $wpdb->insert( $this->table, $this->default_array4, $this->default_mask_array);
			}

			$duplicate5 = false;
			foreach ( $stories_db_data as $key => $value) {
			  if ( $this->default_array5['providername'] == $value->providername && $this->default_array5['title'] == $value->title){
				$duplicate5 = true;
			  }
			}
			if ( $duplicate5 == false){
				$result5 = $wpdb->insert( $this->table, $this->default_array5, $this->default_mask_array);
			}

			$duplicate6 = false;
			foreach ( $stories_db_data as $key => $value) {
			  if ( $this->default_array6['providername'] == $value->providername && $this->default_array6['title'] == $value->title){
				$duplicate6 = true;
			  }
			}
			if ( $duplicate6 == false){
				$result6 = $wpdb->insert( $this->table, $this->default_array6, $this->default_mask_array);
			}

			$duplicate7 = false;
			foreach ( $stories_db_data as $key => $value) {
			  if ( $this->default_array7['providername'] == $value->providername && $this->default_array7['title'] == $value->title){
				$duplicate7 = true;
			  }
			}
			if ( $duplicate7 == false){
				$result7 = $wpdb->insert( $this->table, $this->default_array7, $this->default_mask_array);
			}

			$duplicate8 = false;
			foreach ( $stories_db_data as $key => $value) {
			  if ( $this->default_array8['providername'] == $value->providername && $this->default_array8['title'] == $value->title){
				$duplicate8 = true;
			  }
			}
			if ( $duplicate8 == false){
				$result8 = $wpdb->insert( $this->table, $this->default_array8, $this->default_mask_array);
			}

			$duplicate9 = false;
			foreach ( $stories_db_data as $key => $value) {
			  if ( $this->default_array9['providername'] == $value->providername && $this->default_array9['title'] == $value->title){
				$duplicate9 = true;
			  }
			}
			if ( $duplicate9 == false){
				$result9 = $wpdb->insert( $this->table, $this->default_array9, $this->default_mask_array);
			}

			$duplicate10 = false;
			foreach ( $stories_db_data as $key => $value) {
			  if ( $this->default_array10['providername'] == $value->providername && $this->default_array10['title'] == $value->title){
				$duplicate10 = true;
			  }
			}
			if ( $duplicate10 == false){
				$result10 = $wpdb->insert( $this->table, $this->default_array10, $this->default_mask_array);
			}
			
		

		}

		private function create_default_data(){
			
			$this->default_array1 = array(

				'title' => 'Sample Chapter - The War of the Worlds',
				'category' => 'Sci-Fi',
				'providername' => 'H. G. Wells',
				'providerimg' => ROOT_IMG_URL. 'waroftheworlds.jpg',
				'providerbio' => 'H. G. Wells was an English writer prolific in many genres, including novels, short stories, social commentaries, satire, biographies, autobiographies, and two books on war games. ',
				'content' => "<p class='wpbooklist-content-actual-title'>Chapter 1</p>No one would have believed in the last years of the nineteenth century that this world was being watched keenly and closely by intelligences greater than man's and yet as mortal as his own; that as men busied themselves about their various concerns they were scrutinised and studied, perhaps almost as narrowly as a man with a microscope might scrutinise the transient creatures that swarm and multiply in a drop of water.  With infinite complacency men went to and fro over this globe about their little affairs, serene in their assurance of their empire over matter.  It is possible that the infusoria under the microscope do the same.  No one gave a thought to the older worlds of space as sources of human danger, or thought of them only to dismiss the idea of life upon them as impossible or improbable.  It is curious to recall some of the mental habits of those departed days.  At most terrestrial men fancied there might be other men upon Mars, perhaps inferior to themselves and ready to welcome a missionary enterprise.  Yet across the gulf of space, minds that are to our minds as ours are to those of the beasts that perish, intellects vast and cool and unsympathetic, regarded this earth with envious eyes, and slowly and surely drew their plans against us.  And early in the twentieth century came the great disillusionment.<br/><br/>The planet Mars, I scarcely need remind the reader, revolves about the sun at a mean distance of 140,000,000 miles, and the light and heat it receives from the sun is barely half of that received by this world. It must be, if the nebular hypothesis has any truth, older than our world; and long before this earth ceased to be molten, life upon its surface must have begun its course.  The fact that it is scarcely one seventh of the volume of the earth must have accelerated its cooling to the temperature at which life could begin.  It has air and water and all that is necessary for the support of animated existence.<br/><br/>Yet so vain is man, and so blinded by his vanity, that no writer, up to the very end of the nineteenth century, expressed any idea that intelligent life might have developed there far, or indeed at all, beyond its earthly level.  Nor was it generally understood that since Mars is older than our earth, with scarcely a quarter of the superficial area and remoter from the sun, it necessarily follows that it is not only more distant from time's beginning but nearer its end.<br/><br/>The secular cooling that must someday overtake our planet has already gone far indeed with our neighbour.  Its physical condition is still largely a mystery, but we know now that even in its equatorial region the midday temperature barely approaches that of our coldest winter.  Its air is much more attenuated than ours, its oceans have shrunk until they cover but a third of its surface, and as its slow seasons change huge snowcaps gather and melt about either pole and periodically inundate its temperate zones.  That last stage of exhaustion, which to us is still incredibly remote, has become a present-day problem for the inhabitants of Mars.  The immediate pressure of necessity has brightened their intellects, enlarged their powers, and hardened their hearts.  And looking across space with instruments, and intelligences such as we have scarcely dreamed of, they see, at its nearest distance only 35,000,000 of miles sunward of them, a morning star of hope, our own warmer planet, green with vegetation and grey with water, with a cloudy atmosphere eloquent of fertility, with glimpses through its drifting cloud wisps of broad stretches of populous country and narrow, navy-crowded seas.<br/><br/>And we men, the creatures who inhabit this earth, must be to them at least as alien and lowly as are the monkeys and lemurs to us.  The intellectual side of man already admits that life is an incessant struggle for existence, and it would seem that this too is the belief of the minds upon Mars.  Their world is far gone in its cooling and this world is still crowded with life, but crowded only with what they regard as inferior animals.  To carry warfare sunward is, indeed, their only escape from the destruction that, generation after generation, creeps upon them.<br/><br/>And before we judge of them too harshly we must remember what ruthless and utter destruction our own species has wrought, not only upon animals, such as the vanished bison and the dodo, but upon its inferior races.  The Tasmanians, in spite of their human likeness, were entirely swept out of existence in a war of extermination waged by European immigrants, in the space of fifty years.  Are we such apostles of mercy as to complain if the Martians warred in the same spirit?<br/><br/>The Martians seem to have calculated their descent with amazing subtlety--their mathematical learning is evidently far in excess of ours--and to have carried out their preparations with a well-nigh perfect unanimity.  Had our instruments permitted it, we might have seen the gathering trouble far back in the nineteenth century.  Men like Schiaparelli watched the red planet--it is odd, by-the-bye, that for countless centuries Mars has been the star of war--but failed to interpret the fluctuating appearances of the markings they mapped so well.  All that time the Martians must have been getting ready.<br/><br/>During the opposition of 1894 a great light was seen on the illuminated part of the disk, first at the Lick Observatory, then by Perrotin of Nice, and then by other observers.  English readers heard of it first in the issue of _Nature_ dated August 2.  I am inclined to think that this blaze may have been the casting of the huge gun, in the vast pit sunk into their planet, from which their shots were fired at us.  Peculiar markings, as yet unexplained, were seen near the site of that outbreak during the next two oppositions.<br/><br/>The storm burst upon us six years ago now.  As Mars approached opposition, Lavelle of Java set the wires of the astronomical exchange palpitating with the amazing intelligence of a huge outbreak of incandescent gas upon the planet.  It had occurred towards midnight of the twelfth; and the spectroscope, to which he had at once resorted, indicated a mass of flaming gas, chiefly hydrogen, moving with an enormous velocity towards this earth.  This jet of fire had become invisible about a quarter past twelve.  He compared it to a colossal puff of flame suddenly and violently squirted out of the planet, 'as flaming gases rushed out of a gun. '<br/><br/>A singularly appropriate phrase it proved.  Yet the next day there was nothing of this in the papers except a little note in the _Daily Telegraph_, and the world went in ignorance of one of the gravest dangers that ever threatened the human race. I might not have heard of the eruption at all had I not met Ogilvy, the well-known astronomer, at Ottershaw.  He was immensely excited at the news, and in the excess of his feelings invited me up to take a turn with him that night in a scrutiny of the red planet.<br/><br/>In spite of all that has happened since, I still remember that vigil very distinctly: the black and silent observatory, the shadowed lantern throwing a feeble glow upon the floor in the corner, the steady ticking of the clockwork of the telescope, the little slit in the roof--an oblong profundity with the stardust streaked across it. Ogilvy moved about, invisible but audible.  Looking through the telescope, one saw a circle of deep blue and the little round planet swimming in the field.  It seemed such a little thing, so bright and small and still, faintly marked with transverse stripes, and slightly flattened from the perfect round.  But so little it was, so silvery warm--a pin's-head of light! It was as if it quivered, but really this was the telescope vibrating with the activity of the clockwork that kept the planet in view.<br/><br/>As I watched, the planet seemed to grow larger and smaller and to advance and recede, but that was simply that my eye was tired.  Forty millions of miles it was from us--more than forty millions of miles of void.  Few people realise the immensity of vacancy in which the dust of the material universe swims.<br/><br/>Near it in the field, I remember, were three faint points of light, three telescopic stars infinitely remote, and all around it was the unfathomable darkness of empty space.  You know how that blackness looks on a frosty starlight night.  In a telescope it seems far profounder.  And invisible to me because it was so remote and small, flying swiftly and steadily towards me across that incredible distance, drawing nearer every minute by so many thousands of miles, came the Thing they were sending us, the Thing that was to bring so much struggle and calamity and death to the earth.  I never dreamed of it then as I watched; no one on earth dreamed of that unerring missile.<br/><br/>That night, too, there was another jetting out of gas from the distant planet.  I saw it.  A reddish flash at the edge, the slightest projection of the outline just as the chronometer struck midnight; and at that I told Ogilvy and he took my place.  The night was warm and I was thirsty, and I went stretching my legs clumsily and feeling my way in the darkness, to the little table where the siphon stood, while Ogilvy exclaimed at the streamer of gas that came out towards us.<br/><br/>That night another invisible missile started on its way to the earth from Mars, just a second or so under twenty-four hours after the first one.  I remember how I sat on the table there in the blackness, with patches of green and crimson swimming before my eyes.  I wished I had a light to smoke by, little suspecting the meaning of the minute gleam I had seen and all that it would presently bring me.  Ogilvy watched till one, and then gave it up; and we lit the lantern and walked over to his house.  Down below in the darkness were Ottershaw and Chertsey and all their hundreds of people, sleeping in peace.<br/><br/>He was full of speculation that night about the condition of Mars, and scoffed at the vulgar idea of its having inhabitants who were signalling us.  His idea was that meteorites might be falling in a heavy shower upon the planet, or that a huge volcanic explosion was in progress.  He pointed out to me how unlikely it was that organic evolution had taken the same direction in the two adjacent planets.<br/><br/>'The chances against anything manlike on Mars are a million to one, ' he said.<br/><br/>Hundreds of observers saw the flame that night and the night after about midnight, and again the night after; and so for ten nights, a flame each night.  Why the shots ceased after the tenth no one on earth has attempted to explain.  It may be the gases of the firing caused the Martians inconvenience.  Dense clouds of smoke or dust, visible through a powerful telescope on earth as little grey, fluctuating patches, spread through the clearness of the planet's atmosphere and obscured its more familiar features.<br/><br/>Even the daily papers woke up to the disturbances at last, and popular notes appeared here, there, and everywhere concerning the volcanoes upon Mars.  The seriocomic periodical _Punch_, I remember, made a happy use of it in the political cartoon.  And, all unsuspected, those missiles the Martians had fired at us drew earthward, rushing now at a pace of many miles a second through the empty gulf of space, hour by hour and day by day, nearer and nearer. It seems to me now almost incredibly wonderful that, with that swift fate hanging over us, men could go about their petty concerns as they did.  I remember how jubilant Markham was at securing a new photograph of the planet for the illustrated paper he edited in those days. People in these latter times scarcely realise the abundance and enterprise of our nineteenth-century papers.  For my own part, I was much occupied in learning to ride the bicycle, and busy upon a series of papers discussing the probable developments of moral ideas as civilisation progressed.<br/><br/>One night (the first missile then could scarcely have been 10,000,000 miles away) I went for a walk with my wife.  It was starlight and I explained the Signs of the Zodiac to her, and pointed out Mars, a bright dot of light creeping zenithward, towards which so many telescopes were pointed.  It was a warm night.  Coming home, a party of excursionists from Chertsey or Isleworth passed us singing and playing music.  There were lights in the upper windows of the houses as the people went to bed.  From the railway station in the distance came the sound of shunting trains, ringing and rumbling, softened almost into melody by the distance.  My wife pointed out to me the brightness of the red, green, and yellow signal lights hanging in a framework against the sky.  It seemed so safe and tranquil."
			);

			$this->default_array2 = array(

				'title' => 'Sample Chapter - Pride and Predjudice',
				'category' => 'Classics',
				'providername' => 'Jane Austen',
				'providerimg' => ROOT_IMG_URL. 'prideandpredjudice.jpg',
				'providerbio' => "Jane Austen was a Georgian era author, best known for her social commentary in novels including 'Sense and Sensibility, ' 'Pride and Prejudice, ' and 'Emma. '",
				'content' => '<p class="wpbooklist-content-actual-title">Chapter 1</p>It is a truth universally acknowledged, that a single man in possession of a good fortune, must be in want of a wife.<br/><br/>However little known the feelings or views of such a man may be on his first entering a neighbourhood, this truth is so well fixed in the minds of the surrounding families, that he is considered the rightful property of some one or other of their daughters.<br/><br/>“My dear Mr. Bennet,” said his lady to him one day, “have you heard that Netherfield Park is let at last?”<br/><br/>Mr. Bennet replied that he had not.<br/><br/>“But it is,” returned she; “for Mrs. Long has just been here, and she told me all about it.”<br/><br/>Mr. Bennet made no answer.<br/><br/>“Do you not want to know who has taken it?” cried his wife impatiently.<br/><br/>“_You_ want to tell me, and I have no objection to hearing it.”<br/><br/>This was invitation enough.<br/><br/>“Why, my dear, you must know, Mrs. Long says that Netherfield is taken by a young man of large fortune from the north of England; that he came down on Monday in a chaise and four to see the place, and was so much delighted with it, that he agreed with Mr. Morris immediately; that he is to take possession before Michaelmas, and some of his servants are to be in the house by the end of next week.”<br/><br/>“What is his name?”<br/><br/>“Bingley.”<br/><br/>“Is he married or single?”<br/><br/>“Oh! Single, my dear, to be sure! A single man of large fortune; four or five thousand a year. What a fine thing for our girls!”<br/><br/>“How so? How can it affect them?”<br/><br/>“My dear Mr. Bennet,” replied his wife, “how can you be so tiresome! You must know that I am thinking of his marrying one of them.”<br/><br/>“Is that his design in settling here?”<br/><br/>“Design! Nonsense, how can you talk so! But it is very likely that he _may_ fall in love with one of them, and therefore you must visit him as soon as he comes.”<br/><br/>“I see no occasion for that. You and the girls may go, or you may send them by themselves, which perhaps will be still better, for as you are as handsome as any of them, Mr. Bingley may like you the best of the party.”<br/><br/>“My dear, you flatter me. I certainly _have_ had my share of beauty, but I do not pretend to be anything extraordinary now. When a woman has five grown-up daughters, she ought to give over thinking of her own beauty.”<br/><br/>“In such cases, a woman has not often much beauty to think of.”<br/><br/>“But, my dear, you must indeed go and see Mr. Bingley when he comes into the neighbourhood.”<br/><br/>“It is more than I engage for, I assure you.”<br/><br/>“But consider your daughters. Only think what an establishment it would be for one of them. Sir William and Lady Lucas are determined to go, merely on that account, for in general, you know, they visit no newcomers. Indeed you must go, for it will be impossible for _us_ to visit him if you do not.”<br/><br/>“You are over-scrupulous, surely. I dare say Mr. Bingley will be very glad to see you; and I will send a few lines by you to assure him of my hearty consent to his marrying whichever he chooses of the girls; though I must throw in a good word for my little Lizzy.”<br/><br/>“I desire you will do no such thing. Lizzy is not a bit better than the others; and I am sure she is not half so handsome as Jane, nor half so good-humoured as Lydia. But you are always giving _her_ the preference.”<br/><br/>“They have none of them much to recommend them,” replied he; “they are all silly and ignorant like other girls; but Lizzy has something more of quickness than her sisters.”<br/><br/>“Mr. Bennet, how _can_ you abuse your own children in such a way? You take delight in vexing me. You have no compassion for my poor nerves.”<br/><br/>“You mistake me, my dear. I have a high respect for your nerves. They are my old friends. I have heard you mention them with consideration these last twenty years at least.”<br/><br/>“Ah, you do not know what I suffer.”<br/><br/>“But I hope you will get over it, and live to see many young men of four thousand a year come into the neighbourhood.”<br/><br/>“It will be no use to us, if twenty such should come, since you will not visit them.”<br/><br/>“Depend upon it, my dear, that when there are twenty, I will visit them all.”<br/><br/>Mr. Bennet was so odd a mixture of quick parts, sarcastic humour, reserve, and caprice, that the experience of three-and-twenty years had been insufficient to make his wife understand his character. _Her_ mind was less difficult to develop. She was a woman of mean understanding, little information, and uncertain temper. When she was discontented, she fancied herself nervous. The business of her life was to get her daughters married; its solace was visiting and news. '
			);

			$this->default_array3 = array(

				'title' => 'Sample Chapter - Nightfall',
				'category' => 'Horror',
				'providername' => 'Matthew Dawes',
				'providerimg' => ROOT_IMG_URL. 'nightfall.jpeg',
				'providerbio' => 'Matthew Dawes is the author of "Nightfall", which can be read in full at <a href="http://www.ficfun.com/novel/1961581-Nightfall.html">FicFun.com</a>',
				'content' => '<p class="wpbooklist-content-actual-title">Chapter 1</p>Rain fell from the ever-gray clouds over the towering cathedral of Prosser, Washington. It ran down the spires and over the blackened steel arches before spilling over the edge of the steep metal roof and into the accumulating rivers below.<br><br/><br/>The cleansing downpour rinsed the dirt and blood from my battered steel armor as I approached the monolithic structure before me. Water pooled on the brim of my helmet before dripping off, running down my mask and into the collar of my angular breastplate. My rifle hung on my back with oily rags wrapped around the muzzle and receiver: my feeble attempt to prevent water from saturating the inner workings of the tarnished old weapon. The equally worn sword that hung from my left side sharply contrasted with the much newer, semi-automatic handgun that was holstered on the right.<br/><br/>The sword and rifle, along with my armor, were a final gift from my father. An inheritance and a tribute to the proud legacy of a Republic soldier. My father was always a hero in my eyes. A soldier to the core and loyal to the Republic, he was rarely home, but when he was, he showed me wonderful things and taught me even more. Every campaign he went on, he always found a trinket to bring back for me, souvenirs if you will, trophies from the battlefield and the myriad of cities he visited. I still have some of them, some of my favorites, packed away safely in the locker next to my bunk in the leviathan waiting just outside the city walls. Among the items is a string of three-inch claws from a Scourge he killed while defending the city of Richland, a crude metal spearhead from a cannibalistic tribe near the Madras fortress, and a cracked seashell from a trader on Tillamook Island; the mother of pearl still glistened brightly just as it had the day he gave it to me. But my favorite gift is something that I still carry on me during every deployment and use almost every day: an old three-inch brass artillery shell that he made a handle for and turned into a mug. It isn\'t quite as convenient or versatile as the flasks or canteens that most soldiers prefer, but it\'s a hell of a lot more stylish.<br/><br/>It hung on my belt even then. Its patched and dented surface lightly clinked against my armor as I walked through the downpour to the cathedral. I stopped at the outer gate and grasped my helmet on each side, unclasping it from my mask and lifting it from my head, I set it on the pommel of my sword, letting it hang at my side. Then came the mask that the helmet was locked to. The buckles at the back of my head were loosed and the mask was hung from its straps next to the helmet.<br/><br/>You never realize how entombed you are in cold steel plates and foam padding until you shed them and feel what the world has to offer without a stifling barrier numbing you to all that you touch. The cold wind and pounding rain hit me in a exhilarating blast that would stirr vitality in even the most unfeeling of warriors. I closed my eyes for a moment, relishing the feeling of rain on my face. When I opened them again, I gazed at the cathedral for the first time since my father\'s death without an orange glass visor tinting my vision. I had barely had the opportunity to visit the city of Prosser since, and every time I did, it was on duty.<br/><br/>Five years ago, when the cathedral was only 2 years old, I visited it for the first time under much graver circumstances. I had just learned that my father had been killed in combat. The Crusades had just begun, and troops were being sent out all over Washington, intent on the destruction of the heretics who dared oppose us. My father\'s was among the first platoons deployed to the wasteland that Central Washington had become. Most of the Western cities were overrun with Scourge that had never been cleared after Impact, and nests stretched for miles under the ruins of the Old World. Human settlements were scattered all over Central and Eastern Washington; some were friendly and even traded at times, but most were openly hostile and opposed our every move. The Republic had never indicated the intention of expanding to Washington, but terrorists can\'t be reasoned with. Seven years ago, a platoon of soldiers on a diplomatic mission to the Centralia coal plant were attacked unprovoked, imprisoned, and left to die.<br/><br/>Immediately afterwards, the Baker City Fortress was bombed at night in a suicide run using the stolen trucks from the Centralia platoon. Over 100 soldiers were killed in the subsequent Scourge invasion. Fortunately, the same lockdown protocols that condemned the soldiers to death saved thousands of civilians from the bloodthirsty horde that ravaged the city until daybreak.<br/><br/>The Crusades began the following month as hundreds of soldiers were deployed to Washington, then thousands. My father was killed by the same terrorists who initiated the war to begin with. I blame his death on them and rightly so. Now I follow in his footsteps, bringing death to all enemies of the Republic, the Church, and our Lord God Almighty.<br/><br/>My father told me when I was very young that the Republic was chosen long ago to lead the world out of the darkness. In serving the Republic, my father told me he was doing the will of God and that\'s the best thing you can hope to accomplish in this life. He told me that if anything ever happened to him, I shouldn’t be sad, because he faithfully served God all his life and he would be granted eternal life in heaven because of it. If I did the same, I would too and we would be together again. I grew up hearing this; I went to church every week, even when my father was gone, and I read the Bible as much as I could. When I received the news that he had been killed in combat, I felt a strange mix of emotions: sadness intertwined with happiness as I knew that he was in paradise and would never have to enter the bloody field of battle again. I also knew that it was my time to pick up his armor, rifle, and sword and take his place defending the light in a dark world.<br/><br/>I stepped through the gateway and followed the same path that I had five years earlier. I nodded to the monolithic concrete statues of war heroes as I climbed the front steps to face the high-arched double doors. Walking closer, I examined the beautiful carvings that covered the thick, banded slabs of wood. The intricate carvings were weather-worn, but I could still easily recognize the scenes depicted in the splintering etchings.<br/><br/>It was a recount of our history: the God-ordained events that brought the world to its knees and lifted the Republic to power above all others. The left door was dedicated to pre-Impact events that profoundly shaped our faith to what it is today: namely, the creation of the universe and the birth, death, and resurrection of Jesus Christ, the Son of God. The Harrowing of Hell was shown in particularly gruesome detail. The final panel at the bottom of the left door was a large piece portraying Impact and the destruction that it wrought upon the world.<br/><br/>One side of the carving showed a fiery meteorite streaking towards a sprawling city and the oblivious world that could never comprehend what monstrosities it might contain. Towards the edge of the city rose flames and the decaying ruins of a crumbling civilization. Piles of bodies were burned in a desperate attempt to contain the virus that was ravaging the world but that did nothing to slow it\'s all-consuming path of destruction. The other side showed war and destruction, the death that humanity brought upon itself in a last final struggle for superiority in a faltering world. The Scourge were shown fighting among soldiers, ripping men apart and sinking long ragged fangs into their struggling victims. Some scenes portrayed soldiers killing the Scourge, blowing them apart and staking their heads outside imposing fortresses: small victories in a long line of defeats.<br/><br/>The right door was reserved for more recent history, and I spotted several well known events that almost every child raised in a Republic city learns from a young age. The founding of the Republic from only a handful of farmers and bedraggled militia 130 years after Impact. The appointment of the Church and its subsequent rise to power after the divine ordainment of High Patriarch Kaden Wright.<br/><br/>The construction of the cathedral was shown as well as the beginning of the crusades only six years ago; the doors must be new. I peered closely at the panels of the crusades, carefully noting each armored figure shown: the detail of their weapons; their tattoos and armor; and, most importantly, the platoon code painted on their chest in stark white lettering. It was tiny and hard to make out, but it looked like most bore the "B" of an infantry platoon. The background showed several distant gunships releasing bombs on the condemned city of Yakima. Those bombs were the same ones that ended the battle that killed my father and most of his platoon. After death, it would seem that they had been immortalized for all to see, carved in the face of a door in one of the most important buildings in the Republic\'s sprawling territory.<br/><br/>Smiling at the thought of what my father would think if he ever found out about it, I grasped the blackened steel handle and shoved the door open with an echoing creak. I stepped inside and closed it again, cringing slightly as the rasping sound echoed through the sprawling interior and gained me several irritated glances from nearby proselytes. I nodded pleasantly to the custodians as I passed them, walking down the center of the main hall and turning right towards the west wing.<br/><br/>My boots thudded softly on the polished concrete floor and echoed through the wide open space. The wing was really just a long, wide hallway with small rooms on either side. The peak of the high arched ceiling stood at least 80 feet above me, and ornate chandeliers hung from the heavy steel beams that spanned the 30 foot hallway and turned sharply into vertical pillars that ran down the walls and into the floor. Statues like the ones I passed outside stood at regular intervals along the walls of the hallway. They were smaller than those outside, 20 feet tall rather than 50, but they were equally striking. Though all bore the same distinctive armor worn by Republic soldiers for the last 45 years, they were not all identical. Some stood at attention, arms at their sides and rifle slung on their back. Featureless, battleworn masks shielded their faces from the world, giving them the cold, inhuman facade of unfeeling warriors. Others appeared to be in the midst of combat, with a sword or mace held high in anticipation of delivering the killing blow to an enemy.<br/><br/>Each statue represented hundreds, if not thousands, of soldiers lost in battle. The side rooms that bordered the hallway led to stairs to the catacombs below the cathedral, catacombs that housed the remains of those lost soldiers. I would not go inside. It was an endless maze of tombs that had quickly been filled and turned into mass graves with no sense of reverence or honor for the dead. My father was in there somewhere, but I didn\'t know where. The cathedral itself was an immense grave to all those lost in the recent wars, and visiting it instead of a formal grave and tombstone was the best I could do.<br/><br/>I reached the end of the hallway and knelt before the heartbreaking monument that covered the entire back wall. It was a monument of death, reaped from the fields of battle and paid for with the lives of thousands of men and women of the Republic. The wall was covered, from top to bottom with hooks, nails, and any other hardware you can imagine. The assorted bits of metal protruded crookedly from the wooden planks of the wall and served as supports for the sheet of glistening metal that covered it. Thousands of dog tags covered the wall, many hanging from the same chains they had been issued with. Some were old and rusting while others were bright and new. They hung like a coat of armor over the wall, barely allowing an inch of wood to show through the curtain of gleaming steel.<br/><br/>Each tag represented a dead soldier, and not only those in the catacombs. Over the years since the cathedral\'s construction, people have been bringing tags to hang on the wall. It started with one or two at first, then dozens at a time, usually from Prosser locals. Then the word spread, and hundreds of tags began to appear each month.<br/><br/>I made the decision to hang my father\'s tags here five years ago. They have since been buried under dozens more, but I still know exactly where to find them. In the lower right corner, about six feet off the ground and two feet from the side wall, is a rusty, square-head nail that I scavenged out of a pile of scrap wood outside for the occasion. I hammered it in with the pommel of the sword I had just received from the proselytes who had prepared my father\'s body for burial. I spent hours convincing myself it was the right thing to do before I hung the tags up, collected my inherited gear, and left, hoping never to return.<br/><br/>Yet here I am once again.<br/><br/>Standing, I walked slowly to the right corner of the wall, brushing my hand across the tags as I went. They lightly clanked and rustled as they swung against each other and the wall that supported them. I stopped and eyed the spot for a moment, not quite sure if I was ready to reopen the old wound. Several minutes passed, and I heard footsteps approaching at a distance. I ignored them as they drew closer, thinking it was a grieving spouse or friend come to lay yet another soldier to rest. The footsteps eventually stopped, and I soon forgot that they had even been there as my internal conflict raged on. Finally, I forced myself to the wall, stiffly pulled off my right glove and reached a trembling hand to brush away the chains and tags that obscured my view.<br/><br/>There was the nail just as I had left it, but with several more chains than I had left it with. I traced the chains down the wall to the clump of tangled tags at the bottom. Minorly irritated at the mess that had accumulated in my absence, I began to shuffle through tags in search of my father\'s. My temperament softened as I read the names and codes on the tags. Hunter Scott, B-27. Abigail Scott, B-28. Hunter and Abigail Scott; a married couple, I wondered, or perhaps siblings. Saddened at the thought I moved on. Joshua Evans, E-49: Suppression platoon, affectionately known as the Purge Squad. I automatically respected Joshua for that. Anyone brave enough to sign up for the Purge Squad has to be simultaneously tough as nails and stupid enough to run into a Scourge nest with nothing but a flamethrower and balls of steel. Their distinctive emblem of a charred cross and wings commanded recognition from whoever they encountered, Republic or otherwise. When you put your life on the line to clear entire cities of Scourge considering the logical course of action would be to burn it down and start over, you deserve all the respect you can get.<br/><br/>Gently pushing the tags aside, I reached for the last pair hanging from the nail and slowly turned them over to reveal the name and code. Daniel Anderson, B-19, right where I left him.<br/><br/>I stroked the tarnished metal surface lightly, feeling the indentions of the letters and the slight nicks around the edges. Suddenly, it felt like it had only been a day since I left it, and unwelcome emotions came rushing back all at once. I fought them back, pushing away the regret and pain, loneliness and feelings of abandonment. I hated the power that a small piece of metal held over me. Feeling my eyes well up, I dropped the tag abruptly and stepped back as it fell back into the mass of others and was swallowed in the curtain of metal. I stared absently at the place it had been for several minutes, collecting my dignity and pushing my emotions away.<br/><br/>I started as a sudden voice broke the silence behind me, "Laying someone to rest?”<br/><br/>Remembering the approaching footsteps from earlier, I turned around to find an old, wrinkled man in the typical attire of a proselyte standing in the hallway leaning on his long mace. Though he wore the ornate robes and armor of a proselyte ready for battle, he appeared far too frail to put the ceremonial mace to use any longer outside the capacity of a walking stick. His thin face held the stern countenance of a religious leader, but his eyes were kind and almost seemed to smile on me.<br/><br/>He seemed genuinely concerned about my well-being and patiently waited for me to answer his question. Though he seemed like the kind of person I could easily converse with, I had no desire to talk or even tolerate the presence of another human being in my current state. I knew I would regret it later, but I brushed him off quickly as I started down the long hallway, "Just making sure he\'s still here.”<br/><br/>I heard a quiet "I\'m sorry\' as I walked away and immediately felt bad for my shortness, but I couldn\'t go back then. I silently swore to return to the cathedral the next time through Prosser to find the old man and apologize.<br/><br/>Stepping up to the doors again I pulled them open and stepped back out into the rain. As I walked down the steps, I buckled my mask back on and locked my helmet on over it. Time to take the mantle again, strap on the armor, and pick up the sword; time to be a man, and leave the little boy in the cathedral with the wall of dead memories.\''
			);

			$this->default_array4 = array(

				'title' => 'Interview - Maine Authors Publishing',
				'category' => 'Interview',
				'providername' => 'Maine Authors Publishing',
				'providerimg' => ROOT_IMG_URL. 'map.jpg',
				'providerbio' => 'Maine Authors Publishing helps self-published and independent authors get their books published and in the hands of their readers. Visit them at <a href="http://maineauthorspublishing.com/">Maine Authors Publishing.</a>',
				'content' => '<p class="wpbooklist-content-actual-title">Why do you feel it’s important that writers have options to publish outside of the traditional publishing world?</p>Traditional publishing is bound to the financial success of a book, therefore a lot of new works that deserve to be published are overlooked. Independent publishing gives these books a chance to reach the hands of readers. Strictly “self-publishing” on one’s own is an option for some, but it doesn’t solve the marketing issues a lone author can face. Maine Authors Publishing presents a third option, a “cooperative-style” independent publishing model that offers more support and helps solve some of the marketing and distribution challenges of publishing outside of the traditional channels.<br/><br/>Another reason it is important to have options outside of traditional publishing is if an author wants to retain more control of their project. Many of our authors have traditionally published before and have chosen to publish independently so that they can receive a better royalty percentage, retain creative control, and choose their own publication date. Maine Authors Publishing takes 0% of the book royalties when we sell our authors’ books to bookstores. Publication can also happen faster than traditional publishing. The books chosen for our catalog require professional editing and design, so we cannot produce an instant book as you can online, but our six- to twelve -week turn time can be a better option for some compared to the eight to eighteen months that some large publishing companies may take.<br/></br><p class="wpbooklist-content-actual-title">You offer a co-op membership to authors who publish through you. Has this helped to build a writing community?</p>For writing support we encourage writers to join existing communities such as local writing groups and the MWPA. Our cooperative membership has helped to build more of a “marketing community” where our authors train and support each other. The community focuses on learning how to get their books into the hands of the reading public. Authors also share the marketing costs in many ways. They participate in dozens of book signings and trade shows per year and a booth can cost up to $500. Authors can donate $10 per title to cover the cost. Many of these events are organized by our sales representative, Kelly, as part of the cooperative membership. The membership offers marketing support by providing distribution, a listing in our trade catalog, and sales training.<br/><br/><p class="wpbooklist-content-actual-title">How does Maine Authors Publishing help authors face the challenges of publishing independently?</p>Some of the biggest challenges self-published authors face is that their books are not vetted and do not have a presence in bookstores or brand recognition. Bookstore buyers have no way of knowing if self-published books have been edited professionally.<br/><br/>Only a percentage of our books are allowed to publish under the imprint Maine Authors Publishing. We vet all of the books that carry our name to ensure that the books have been professionally edited and designed. Our authors’ books are not seen as “self-published” by most entities—we instead are seen as a small press. Publishers Weekly and the Kirkus Review have chosen to place us in the category of a small press, as have many other organizations. We carry a separate Indie Author imprint for books that either do not make the cut or simply don’t need our catalog and services. The imprint has a separate logo and website, but we still offer many of the same services to the nearly 100 books we carry in this category.<br/><br/>Our goal is to support local independent authors throughout the publishing process with affordable services. Authors who may not have the chance to publish their work traditionally are given access to local editing, design, and distribution. '
			);

			$this->default_array5 = array(

				'title' => "Article - Missouri Writers’ Guild",
				'category' => 'Article',
				'providername' => "Missouri Writers’ Guild",
				'providerimg' => ROOT_IMG_URL. 'mwg.jpg',
				'providerbio' => '<a href="http://missouriwritersguild.org/">The Missouri Writers’ Guild</a> is a statewide organization for professional writers that provides workshops, networking opportunities, and other activities to assist writers in developing their careers. ',
				'content' => '<p class="wpbooklist-content-actual-title">Writing Across Genres - written by Guy Anthony De Marco</p>In the olden days, about four or five years ago, there was an unofficial “rule” that dictated what you were allowed to write. If you wrote western stories and you had a decent following (which means you had several traditional book deals and an agent), you were supposed to stick with that genre in order to not dilute the value of your name by having it associated with another area, such as fantasy. If you happened to be a prolific writer, you might end up writing under several pseudonyms. Some authors were able to successfully branch out under their own names, but those were rarities.</br></br>An older example of this type of reasoning could be found even at the top tier of authors. The name Stephen King was synonymous for a particular type of scary story. When he wanted to try something different, he wrote under the name Richard Bachman. JK Rowling, famous for her YA novels concerning a particular wizard with a peculiar scar, wrote under Robert Galbraith when she delved into crime novels. Once the word leaked that Galbraith was actually Rowling, sales of the book shot up over 4,000 percent.</br></br>What about authors just starting out? Should an author stick to one narrow genre and adopt enough pseudonyms to cover the rest? The answer is a resounding…maybe.</br></br>If you have a decent following and have only written in one genre, you may want to keep that name “clean”. Otherwise, since you’re still in the honeymoon phase of becoming a world-famous author, you can feel comfortable writing in several genres. If you begin your career as someone who can genre-hop, producing quality stories for different tastes, your potential audience will be expanded and your name won’t be pegged as “that person who writes Lovecraftian Romance novels”.</br></br>If you’re comfortable writing different genres, understanding that there are different tropes and requirements for each one, you should consider producing work in whatever genre you enjoy. If you write science fiction but you enjoy Gothic romance, feel free to expand your writing skills by producing work in a new field. In fact, different genres will be classified using BISAC codes, which will allow your books to show up in a catalog search at the library or over at your favorite bookstore. There are many science fiction readers who wouldn’t touch a romance novel if each copy included a golden ticket to tour Willie Wonka’s chocolate factory. The inverse is also true.</br></br>If you write novels in the science fiction, western, and romance genres, you have a much wider pool of potential readers. That is three shots at becoming a runaway bestseller, compared to writing three novels in one field. The audience field is still limited by focusing on only one group. If your goal is to become a recognized and respected science fiction author and nothing else, then stick with the one genre. If you’re open to having more kernels of popcorn in the pan (to paraphrase Kevin J. Anderson’s Popcorn Theory), consider writing under one name across several genres.</br></br>There is also a middle ground you can consider … genre-blending. Enjoy writing westerns and science fiction? Come up with the next Firefly. Enjoy writing paranormal urban fantasy and romance? Come up with a better blend than the Twilight series. '
			);

			$this->default_array6 = array(

				'title' => "Autobiography of Benjamin Franklin",
				'category' => 'Excerpt',
				'providername' => "Benjamin Franklin",
				'providerimg' => ROOT_IMG_URL. 'bf.jpg',
				'providerbio' => 'Benjamin Franklin was an author, printer, political theorist, freemason, postmaster, scientist, inventor, statesman, and diplomat, but he is known above all as a Founding Father of the United States. ',
				'content' => "<p class='wpbooklist-content-actual-title'>The Autobiography of Benjamin Franklin</p>...Peace being concluded, and the association business therefore at an end, I turn'd my thoughts again to the affair of establishing an academy. The first step I took was to associate in the design a number of active friends, of whom the Junto furnished a good part; the next was to write and publish a pamphlet, entitled Proposals Relating to the Education of Youth in Pennsylvania. This I distributed among the principal inhabitants gratis; and as soon as I could suppose their minds a little prepared by the perusal of it, I set on foot a subscription for opening and supporting an academy; it was to be paid in quotas yearly for five years; by so dividing it, I judg'd the subscription might be larger, and I believe it was so, amounting to no less, if I remember right, than five thousand pounds.<br/><br/>In the introduction to these proposals, I stated their publication, not as an act of mine, but of some publick-spirited gentlemen, avoiding as much as I could, according to my usual rule, the presenting myself to the publick as the author of any scheme for their benefit.<br/><br/>The subscribers, to carry the project into immediate execution, chose out of their number twenty-four trustees, and appointed Mr. Francis, then attorney-general, and myself to draw up constitutions for the government of the academy; which being done and signed, a house was hired, masters engag'd, and the schools opened, I think, in the same year, 1749.<br/><br/>The scholars increasing fast, the house was soon found too small, and we were looking out for a piece of ground, properly situated, with intention to build, when Providence threw into our way a large house ready built, which, with a few alterations, might well serve our purpose. This was the building before mentioned, erected by the hearers of Mr. Whitefield, and was obtained for us in the following manner.<br/><br/>It is to be noted that the contributions to this building being made by people of different sects, care was taken in the nomination of trustees, in whom the building and ground was to be vested, that a predominancy should not be given to any sect, lest in time that predominancy might be a means of appropriating the whole to the use of such sect, contrary to the original intention. It was therefore that one of each sect was appointed, viz., one Church-of-England man, one Presbyterian, one Baptist, one Moravian, etc., those, in case of vacancy by death, were to fill it by election from among the contributors. The Moravian happen'd not to please his colleagues, and on his death they resolved to have no other of that sect. The difficulty then was, how to avoid having two of some other sect, by means of the new choice.<br/><br/>Several persons were named, and for that reason not agreed to. At length one mention'd me, with the observation that I was merely an honest man, and of no sect at all, which prevail'd with them to chuse me. The enthusiasm which existed when the house was built had long since abated, and its trustees had not been able to procure fresh contributions for paying the ground-rent, and discharging some other debts the building had occasion'd, which embarrass'd them greatly. Being now a member of both setts of trustees, that for the building and that for the Academy, I had a good opportunity of negotiating with both, and brought them finally to an agreement, by which the trustees for the building were to cede it to those of the academy, the latter undertaking to discharge the debt, to keep for ever open in the building a large hall for occasional preachers, according to the original intention, and maintain a free-school for the instruction of poor children. Writings were accordingly drawn, and on paying the debts the trustees of the academy were put in possession of the premises; and by dividing the great and lofty hall into stories, and different rooms above and below for the several schools, and purchasing some additional ground, the whole was soon made fit for our purpose, and the scholars remov'd into the building. The care and trouble of agreeing with the workmen, purchasing materials, and superintending the work, fell upon me; and I went thro' it the more cheerfully, as it did not then interfere with my private business, having the year before taken a very able, industrious, and honest partner, Mr. David Hall, with whose character I was well acquainted, as he had work'd for me four years. He took off my hands all care of the printing-office, paying me punctually my share of the profits. This partnership continued eighteen years, successfully for us both.<br/><br/>The trustees of the academy, after a while, were incorporated by a charter from the governor; their funds were increas'd by contributions in Britain and grants of land from the proprietaries, to which the Assembly has since made considerable addition; and thus was established the present University of Philadelphia. I have been continued one of its trustees from the beginning, now near forty years, and have had the very great pleasure of seeing a number of the youth who have receiv'd their education in it, distinguish'd by their improv'd abilities, serviceable in public stations, and ornaments to their country.<br/><br/>When I disengaged myself, as above mentioned, from private business, I flatter'd myself that, by the sufficient tho' moderate fortune I had acquir'd, I had secured leisure during the rest of my life for philosophical studies and amusements. I purchased all Dr. Spence's apparatus, who had come from England to lecture here, and I proceeded in my electrical experiments with great alacrity; but the publick, now considering me as a man of leisure, laid hold of me for their purposes, every part of our civil government, and almost at the same time, imposing some duty upon me. The governor put me into the commission of the peace; the corporation of the city chose me of the common council, and soon after an alderman; and the citizens at large chose me a burgess to represent them in Assembly. This latter station was the more agreeable to me, as I was at length tired with sitting there to hear debates, in which, as clerk, I could take no part, and which were often so unentertaining that I was induc'd to amuse myself with making magic squares or circles, or any thing to avoid weariness; and I conceiv'd my becoming a member would enlarge my power of doing good. I would not, however, insinuate that my ambition was not flatter'd by all these promotions; it certainly was; for, considering my low beginning, they were great things to me; and they were still more pleasing, as being so many spontaneous testimonies of the public good opinion, and by me entirely unsolicited."
			);

			$this->default_array7 = array(

				'title' => "Sample Chapter - Morningland",
				'category' => 'Sci-Fi',
				'providername' => "Zac Wilson",
				'providerimg' => ROOT_IMG_URL. 'morningland.jpg',
				'providerbio' => 'Zac Wilson is the author of "Morningland", which can be read in full at <a href="http://www.ficfun.com/novel/1961675-Morningland.html">FicFun.com</a>',
				'content' => "<p class='wpbooklist-content-actual-title'>Chapter 1</p>I always thought my first beach experience would be on Earth.<br/><br/>And I didn’t think it would involve a 700-million ton spacecraft falling through the sky.<br/><br/>But everybody’s wrong once in a while.<br/><br/>I’d never seen the entire ship all at once, until now, with my soaking shoes pressed into the blinding white sand.<br/><br/>Sure, we went over diagrams in class, saw pictures, watched instructional and safety videos, and I’d been around at least a quarter of the interior by then. But this was the first time I’d gotten a glimpse of the massive spaceship, top to bottom.<br/><br/>Up close, anyway.<br/><br/>The last time I got a good look at it was 4 weeks ago, on my last day on Earth.<br/><br/>Got a brief glance at it before boarding a smaller vessel with no windows, just a black box with seatbelts, which shuttled a thousand of us up to the ship that dwarfed our tiny vehicle. The ship that was now plummeting helplessly from space: Shamus Five.<br/><br/>Too heavy to be efficiently built on Earth, Shamus Five was constructed in space over a yearlong period, just outside the planet’s atmosphere. 11 other Shamus ships just like it were constructed simultaneously. Each one was to house and transport 8 million passengers apiece.<br/><br/>The Shamus ships were, for the entire planet, priority number one.<br/><br/>During my final few moments on Earth, I saw the massive spacecrafts as miniscule dots in the sky when I glanced up. They looked like a mix between satellites and old-fashioned blimps. Then I was hastily ushered into the smaller vessel, the little black box, and the wide metal door shut behind me. That shuttle, and hundreds of others like it, was kept hidden from all but those of us meant to leave Earth.<br/><br/>The chosen survivors.<br/><br/>Why I was selected to live, to have the chance to seek out new inhabitable planets while the rest of mankind was left to die, I’d never know.<br/><br/>I’d seen footage of the Shamus ships being built, and the riots that took place worldwide concerning who got to board and who didn’t. Part of me wondered why it wasn’t one of those protesters in my place.<br/><br/>Two months prior to boarding, I was brought to and kept in a secret location with a large group of others. Somewhere underground, I assume. DNA identification was rigorous and regular, and security was tight, ensuring no imposters were smuggling their way off planet.<br/><br/>Over the next couple months, passengers were gradually shuttled up to their respective Shamus ship via the little black boxes. When the time came for me to board, I sat shoulder-to-shoulder with a thousand strangers, and ascended up into one of the giant arcs. Shamus Five. My new home for 80+ weeks of intergalactic travel.<br/><br/>Less than a day later, all 12 Shamus ships left our doomed solar system, never to return.<br/><br/>But now, with my feet planted on this mysterious beach, I saw the entirety of Shamus Five up close and in vivid detail. One twelfth of humanity’s last chance. Our vessel of salvation, our Noah’s Arc, our beacon of hope…<br/><br/>I watched in awe and fear as all 700 million tons of it fell out of the sky.<br/><br/>It’s hard to describe exactly what I was looking at, but it wasn’t good. The gigantic rear thrusters that once propelled the ship were now dead, and broken off from the rest of Shamus Five. The enormous bow had also detached from the lengthy fuselage, creating a storm of shattered debris in between.<br/><br/>Three colossal chunks of what housed 8 million souls just minutes before… Were now hurling through the bright blue sky towards the ocean surface. Well, not the ocean surface. <i>An</i> ocean surface, I guess. I didn’t know where we were, hadn’t really had time to think about it.<br/><br/>It wasn’t Earth though, I knew that. But it certainly felt like Earth.<br/><br/>My muscles, despite being shaken up by the escape pod’s rough landing, told me that the gravity here was nearly identical to Earth’s. Maybe even less powerful. The trees that overlooked the beach were tall. Huge, in fact. Thick and sturdy at the bottom, like miniature mountains. The trunks essentially narrowed into palm trees after a few meters. The sand beneath my soaked shoes was a fine, white powder. It reminded me of Earth’s tropical destinations. The ones I’d seen on television, but never been to. The water I had just swam out of, along with the rest of Class 331B, was cool but not too cold. Possibly salty, but my senses were far too overwhelmed to know for sure. It felt like I was drying quickly though. I felt the astounding heat wrap around me like a suffocating blanket. The sun here was incredibly powerful. The sun…<br/><br/>It hovered above the horizon, which was just endless ocean as far as I could see. But it was currently eclipsed by the falling pieces of what used to be our ship.<br/><br/>Shamus Five, like all of the Shamus ships, was named after its famed designer, Dr. Philemon Shamus. He was hailed as a genius and a savior after he emerged with his cutting edge designs. His face was plastered across the globe for Earth’s final year of existence, apparently the only face left worth advertising. He reached true immortal status when he suddenly died, just after his blueprints had started undergoing production.<br/><br/>Of the 12 ships that left Earth, two of them were designed specially as college ships. Every other spacecraft ran an elementary and high school system, but these two ships were made specifically to be post-secondary educational systems.<br/><br/>Shamus Five was one of those two college ships.<br/><br/>The idea was to educate the next generation on the essentials of rebuilding life and community on our new planets, how to deal with the potential encounter of extraterrestrial life, survival in new hostile environments, etc. So the population onboard Shamus Five was mostly students.<br/><br/>Water droplets fell from my eyebrows as I tried to unglue my sight from the shattered, freefalling ship. I managed to look away and assess the situation here on the beach. Based off of my initial scan of the panic-heavy shore, I spotted no one over the age of 20 as survivors scrambled onto the beach. A few dozen students from as young as 18 years old were running, tripping, crawling their way out of the water.<br/><br/>Our escape pod must have sunken to the sea floor by now…<br/><br/>The tall, lanky kid by my feet, Benjamin, suddenly coughed up a mouthful of water. I looked down to see him on his side, eyes closed behind his crooked glasses, breathing slowly. <i>Good, he’s alive</i>. He was a good kid from what I could tell. Smart.<br/><br/>I had just dragged his unconscious body from the crash-landing spot of the escape pod, which was about 30 meters out and at least as far beneath the waves. I could still see the turbulence in the water from where we hit. Still mostly out of it, Benjamin stayed relatively motionless by my shoes.<br/><br/>Our teacher was in the escape pod when we ejected, but I wasn’t sure where he was now. I forgot his name, but he could still be underwater. Suddenly, a disturbing thought struck me: I didn’t see Jess come back up.<br/><br/>Jess sat two rows behind me in class. She was lovely, quiet, unfailingly rebellious, and that’s basically all I knew about her. We’d never spoken before. I prepared to run towards the water, to dive back down to where our escape pod had sunk. Then her voice stopped me as I heard her yell my name.<br/><br/>“Rick!”<br/><br/>My heels swiveled in the sand and I saw her, worse-for-wear but alive. Her curly blonde hair was drenched and stuck to  the sides of her face. Her wet clothes were spattered with grains of glimmering sand. Panting heavily, she pointed behind me to the horizon. I felt relieved; she was safe. But the look on her face quickly snuffed out that relief. Her expression was not a comforting one, as she continued to stare and point out to the ocean. I turned to look, and to my horror, I saw what she was pointing at. I was wrong. She wasn’t safe.<br/><br/>None of us were.<br/><br/>Two miles out from shore, the colossal bow of Shamus Five had just hit the water. The gravity of the impact was impossible to wrap my head around.<br/><br/>The enormous shaft and rear thrusters were following separately, just a second behind it.<br/><br/>I suppose the ejection angle of our escape pod resulted in us landing a few minutes before the mothership. <i>I can’t believe we survived the landing…</i><br/><br/>The gigantic bow of Shamus Five split through the surface of the sea. A deafening crash made itself heard as the jagged edge of the bow plunged into the ocean. The equally massive shaft and thrusters collided with the water just moments after.<br/><br/>I just stood there and watched the unthinkable happen. It wasn’t unthinkable because it was illogical. It made perfect sense. It was just the last thing I wanted to see.<br/><br/>As the scattered pieces of Shamus Five submerged violently in the distance, a 60-foot wave emerged from the impact. The ocean rose up angrily and instantly.<br/><br/>It was like watching the world end all over again.<br/><br/>The immense wave began crashing towards us, somehow both slowly and quickly. Like a thick, haunting fog consisting of a million gallons of water. The charging wall grew to dizzying heights and moved like an unstoppable force. It rapidly closed the distance between us, and the demolished remains of Shamus Five. Twisted metal slapped and sank into the vast body of water accompanied by a sickening screech. The ocean curled itself into a devastating weapon, and we were its target.<br/><br/>I turned towards Jess, who stood as helplessly as me just several feet away. I shook my head in disbelief as I looked into her frightened blue eyes.<br/><br/>“I don’t know, I don’t know what to…” I muttered.<br/><br/>The tsunami-like wave rushed towards us without remorse, as 30 odd students, just kids really, stood there staring at it. Waiting for it. Waiting for the end.<br/><br/>30 odd students standing helplessly on the beautiful beach of an unknown planet, with no more ship, no more Earth, no more hope.<br/><br/>Everything else we knew had come to a swift end, and now it was our turn.<br/><br/>We waited quietly, scattered along the shore. None of us dumb enough or smart enough to try and run. Together but alone, we waited as our last minute ticked down.<br/><br/>Then Benjamin grabbed my leg."
			);

			$this->default_array8 = array(

				'title' => "Author Showcase - David Luddington",
				'category' => 'Author Showcase',
				'providername' => "David Luddington",
				'providerimg' => ROOT_IMG_URL. 'davludd.jpg',
				'providerbio' => 'David Luddington is an author of comedic works. Gentle, British comedic works. Be sure to check out his website at <a href="http://luddington.com/">Luddington.com</a>',
				'content' => "<p class='wpbooklist-content-actual-title'>About David Luddington</p>In case you don’t know, I write comedy. Gentle British comedy. Having grown up with P.G. Wodehouse, the Ealing Comedies and the Carry On movies I like to think I’ve captured the tone of traditional British Humour but brought it firmly into the… where are we now? No… I’m not going to think about that.<br/><br/>I also write to a theme. I believe many of us have lost sight of who we are in our rush to ride the next big wave. But when that big wave dumps us on the shores of ‘Couldn’t Care Less’ and carries on without us, who are we then? Can I stand up on the beach in nothing but my… well… nothing and tell the world who I am, or do I need to preface it with “Well, I used to be…”<br/><br/>I used to be something big in the place where they needed people who were something big… for a while. Then they decided they didn’t need people who were something big anymore and I become a nobody in a place where nobody cared anyway.<br/><br/>My stories deal with identity and the bewilderment we face when that identity is taken from us by a world that has suddenly decided it can cope perfectly well without bubble lamp repairmen or human telephone receptionists.<br/><br/>My stories concern real people who feel the world has become a slightly difficult place. A place where one used to know how to programme the video recorder or remove a roll of film from a camera without it exploding like a Rasta’s head in a hairdryer but now have to deal with isometric bandwidth widgity watsits on a daily basis before we can even put our MP3 in the toaster.<br/><br/>I am a believer in hope and second chances. I believe we all have a soulmate and that some lucky few are destined to find them.  I believe that the truth is out there, in the number 42 and that HAL was just having a bad day.  And I believe in butterscotch Angel Delight. Most of all I believe in butterscotch Angel Delight.<br/><br/>So, if you want a slice of old-fashioned humour (Note to the colonists, humour has a letter U so please stop criticising my spelling) … erm … (that’s criticising with an S, not a Z by the way) oh (and it’s a zed, not a zee!) Anyway, good old-fashioned humour with a heart and an understanding that our time here is precious. <br/><br/>However, if you’re looking for cruel, biting satire then please buy my books anyway. You’ll be terribly disappointed but I need the money."
			);

			$this->default_array9 = array(

				'title' => "Sample Chapter - Dracula",
				'category' => 'Horror',
				'providername' => "Bram Stoker",
				'providerimg' => ROOT_IMG_URL. 'dracula.jpg',
				'providerbio' => "Born in Ireland in 1847, Bram Stoker was an Irish writer best known for authoring the classic 19th century horror novel 'Dracula. '",
				'content' => "<p class='wpbooklist-content-actual-title'>Chapter 1 - Jonathan Harker’s Journal. (Kept in shorthand.)</p>3 May. Bistritz.—Left Munich at 8.35 p.m. on 1st May, arriving at Vienna early next morning; should have arrived at 6.46, but train was an hour late. Buda-Pesth seems a wonderful place, from the glimpse which I got of it from the train and the little I could walk through the streets. I feared to go very far from the station, as we had arrived late and would start as near the correct time as possible. The impression I had was that we were leaving the West and entering the East; the most Western of splendid bridges over the Danube, which is here of noble width and depth, took us among the traditions of Turkish rule.<br/><br/>We left in pretty good time, and came after nightfall to Klausenburgh. Here I stopped for the night at the Hotel Royale. I had for dinner, or rather supper, a chicken done up some way with red pepper, which was very good but thirsty. (Mem., get recipe for Mina.) I asked the waiter, and he said it was called “paprika hendl,” and that, as it was a national dish, I should be able to get it anywhere along the Carpathians. I found my smattering of German very useful here; indeed, I don’t know how I should be able to get on without it.<br/><br/>Having some time at my disposal when in London, I had visited the British Museum, and made search among the books and maps of the library regarding Transylvania; it had struck me that some foreknowledge of the country could hardly fail to have some importance in dealing with a noble[Pg 2] of that country. I find that the district he named is in the extreme east of the country, just on the borders of three states, Transylvania, Moldavia, and Bukovina, in the midst of the Carpathian mountains; one of the wildest and least known portions of Europe. I was not able to light on any map or work giving the exact locality of the Castle Dracula, as there are no maps of this country as yet to compare with our own Ordnance Survey maps; but I found that Bistritz, the post town named by Count Dracula, is a fairly well-known place. I shall enter here some of my notes, as they may refresh my memory when I talk over my travels with Mina.<br/><br/>In the population of Transylvania there are four distinct nationalities: Saxons in the south, and mixed with them the Wallachs, who are the descendants of the Dacians; Magyars in the west; and Szekelys in the east and north. I am going among the latter, who claim to be descended from Attila and the Huns. This may be so, for when the Magyars conquered the country in the eleventh century they found the Huns settled in it. I read that every known superstition in the world is gathered into the horseshoe of the Carpathians, as if it were the centre of some sort of imaginative whirlpool; if so my stay may be very interesting. (Mem., I must ask the Count all about them.)<br/><br/>I did not sleep well, though my bed was comfortable enough, for I had all sorts of queer dreams. There was a dog howling all night under my window, which may have had something to do with it; or it may have been the paprika, for I had to drink up all the water in my carafe, and was still thirsty. Towards morning I slept and was wakened by the continuous knocking at my door, so I guess I must have been sleeping soundly then. I had for breakfast more paprika, and a sort of porridge of maize flour which they said was “mamaliga,” and egg-plant stuffed with forcemeat, a very excellent dish, which they call “impletata.” (Mem., get recipe for this also.) I had to hurry breakfast, for the train started a little before eight, or rather it ought to have done so, for after rushing to the station at 7.30 I had to sit in the carriage for more than an hour before we began to move. It seems to me that the further East you go the more unpunctual are the trains. What ought they to be in China?<br/><br/>All day long we seemed to dawdle through a country which was full of beauty of every kind. Sometimes we saw little towns or castles on the top of steep hills such as we see in old missals; sometimes we ran by rivers and streams which seemed from the wide stony margin on each side of them to be subject to great floods. It takes a lot of water, and running strong, to sweep the outside edge of a river clear. At every station there were groups of people, sometimes crowds, and in all sorts of attire. Some of them were just like the peasants at home or those I saw coming through France and Germany, with short jackets and round hats and home-made trousers; but others were very picturesque. The women looked pretty, except when you got near them, but they were very clumsy about the waist. They had all full white sleeves of some kind or other, and most of them had big belts with a lot of strips of something fluttering from them like the dresses in a ballet, but of course petticoats under them. The strangest figures we saw were the Slovaks, who are more barbarian than the rest, with their big cowboy hats, great baggy dirty-white trousers, white linen shirts, and enormous heavy leather belts, nearly a foot wide, all studded over with brass nails. They wore high boots, with their trousers tucked into them, and had long black hair and heavy black moustaches. They are very picturesque, but do not look prepossessing. On the stage they would be set down at once as some old Oriental band of brigands. They are, however, I am told, very harmless and rather wanting in natural self-assertion.<br/><br/>It was on the dark side of twilight when we got to Bistritz, which is a very interesting old place. Being practically on the frontier—for the Borgo Pass leads from it into Bukovina—it has had a very stormy existence, and it certainly shows marks of it. Fifty years ago a series of great fires took place, which made terrible havoc on five separate occasions. At the very beginning of the seventeenth century it underwent a siege of three weeks and lost 13,000 people, the casualties of war proper being assisted by famine and disease.<br/><br/>Count Dracula had directed me to go to the Golden Krone Hotel, which I found, to my delight, to be thoroughly old-fashioned, for of course I wanted to see all I could of the ways of the country. I was evidently expected, for when I got near the door I faced a [Pg 4]cheery-looking elderly woman in the usual peasant dress—white undergarment with long double apron, front and back, of coloured stuff fitting almost too tight for modesty. When I came close she bowed, and said: “The Herr Englishman?” “Yes,” I said, “Jonathan Harker.” She smiled, and gave some message to an elderly man in white shirt-sleeves, who had followed her to the door. He went, but immediately returned with a letter:—<br/><br/>“MY FRIEND,—Welcome to the Carpathians. I am anxiously expecting you. Sleep well to-night. At three to-morrow the diligence will start for Bukovina; a place on it is kept for you. At the Borgo Pass my carriage will await you and will bring you to me. I trust that your journey from London has been a happy one, and that you will enjoy your stay in my beautiful land.<br/><br/>“Your friend,<br/><br/>“DRACULA.”<br/><br/>4 May.—I found that my landlord had got a letter from the Count, directing him to secure the best place on the coach for me; but on making inquiries as to details he seemed somewhat reticent, and pretended that he could not understand my German. This could not be true, because up to then he had understood it perfectly; at least, he answered my questions exactly as if he did. He and his wife, the old lady who had received me, looked at each other in a frightened sort of way. He mumbled out that the money had been sent in a letter, and that was all he knew. When I asked him if he knew Count Dracula, and could tell me anything of his castle, both he and his wife crossed themselves, and, saying that they knew nothing at all, simply refused to speak further. It was so near the time of starting that I had no time to ask any one else, for it was all very mysterious and not by any means comforting.<br/><br/>Just before I was leaving, the old lady came up to my room and said in a very hysterical way:<br/><br/>“Must you go? Oh! young Herr, must you go?” She was in such an excited state that she seemed to have lost her grip of what German she knew, and mixed it all up with some other language which I did not know at all. I was just able to follow her by asking many questions. When I told her[Pg 5] that I must go at once, and that I was engaged on important business, she asked again:<br/><br/>“Do you know what day it is?” I answered that it was the fourth of May. She shook her head as she said again:<br/><br/>“Oh, yes! I know that, I know that! but do you know what day it is?” On my saying that I did not understand, she went on:<br/><br/>“It is the eve of St. George’s Day. Do you not know that to-night, when the clock strikes midnight, all the evil things in the world will have full sway? Do you know where you are going, and what you are going to?” She was in such evident distress that I tried to comfort her, but without effect. Finally she went down on her knees and implored me not to go; at least to wait a day or two before starting. It was all very ridiculous, but I did not feel comfortable. However, there was business to be done, and I could allow nothing to interfere with it. I therefore tried to raise her up, and said, as gravely as I could, that I thanked her, but my duty was imperative, and that I must go. She then rose and dried her eyes, and taking a crucifix from her neck offered it to me. I did not know what to do, for, as an English Churchman, I have been taught to regard such things as in some measure idolatrous, and yet it seemed so ungracious to refuse an old lady meaning so well and in such a state of mind. She saw, I suppose, the doubt in my face, for she put the rosary round my neck, and said, “For your mother’s sake,” and went out of the room. I am writing up this part of the diary whilst I am waiting for the coach, which is, of course, late; and the crucifix is still round my neck. Whether it is the old lady’s fear, or the many ghostly traditions of this place, or the crucifix itself, I do not know, but I am not feeling nearly as easy in my mind as usual. If this book should ever reach Mina before I do, let it bring my good-bye. Here comes the coach!<br/><br/>5 May. The Castle.—The grey of the morning has passed, and the sun is high over the distant horizon, which seems jagged, whether with trees or hills I know not, for it is so far off that big things and little are mixed. I am not sleepy, and, as I am not to be called till I awake, naturally I write till sleep comes. There are many odd things to put down, and, lest who reads them may fancy that I dined too well before I[Pg 6] left Bistritz, let me put down my dinner exactly. I dined on what they call “robber steak”—bits of bacon, onion, and beef, seasoned with red pepper, and strung on sticks and roasted over the fire, in the simple style of the London cat’s-meat! The wine was Golden Mediasch, which produces a queer sting on the tongue, which is, however, not disagreeable. I had only a couple of glasses of this, and nothing else.<br/><br/>When I got on the coach the driver had not taken his seat, and I saw him talking with the landlady. They were evidently talking of me, for every now and then they looked at me, and some of the people who were sitting on the bench outside the door—which they call by a name meaning “word-bearer”—came and listened, and then they looked at me, most of them pityingly. I could hear a lot of words often repeated, queer words, for there were many nationalities in the crowd; so I quietly got my polyglot dictionary from my bag and looked them out. I must say they were not cheering to me, for amongst them were “Ordog”—Satan, “pokol”—hell, “stregoica”—witch, “vrolok” and “vlkoslak”—both of which mean the same thing, one being Slovak and the other Servian for something that is either were-wolf or vampire. (Mem., I must ask the Count about these superstitions.)<br/><br/>When we started, the crowd round the inn door, which had by this time swelled to a considerable size, all made the sign of the cross and pointed two fingers towards me. With some difficulty I got a fellow-passenger to tell me what they meant; he would not answer at first, but on learning that I was English, he explained that it was a charm or guard against the evil eye. This was not very pleasant for me, just starting for an unknown place to meet an unknown man; but every one seemed so kind-hearted, and so sorrowful, and so sympathetic that I could not but be touched. I shall never forget the last glimpse which I had of the inn-yard and its crowd of picturesque figures, all crossing themselves, as they stood round the wide archway, with its background of rich foliage of oleander and orange trees in green tubs clustered in the centre of the yard. Then our driver, whose wide linen drawers covered the whole front of the box-seat—“gotza” they call them—cracked his big whip over his four small horses, which ran abreast, and we set off on our journey.<br/><br/>I soon lost sight and recollection of ghostly fears in the beauty of the scene as we drove along, although had I known the language, or rather languages, which my fellow-passengers were speaking, I might not have been able to throw them off so easily. Before us lay a green sloping land full of forests and woods, with here and there steep hills, crowned with clumps of trees or with farmhouses, the blank gable end to the road. There was everywhere a bewildering mass of fruit blossom—apple, plum, pear, cherry; and as we drove by I could see the green grass under the trees spangled with the fallen petals. In and out amongst these green hills of what they call here the “Mittel Land” ran the road, losing itself as it swept round the grassy curve, or was shut out by the straggling ends of pine woods, which here and there ran down the hillsides like tongues of flame. The road was rugged, but still we seemed to fly over it with a feverish haste. I could not understand then what the haste meant, but the driver was evidently bent on losing no time in reaching Borgo Prund. I was told that this road is in summer-time excellent, but that it had not yet been put in order after the winter snows. In this respect it is different from the general run of roads in the Carpathians, for it is an old tradition that they are not to be kept in too good order. Of old the Hospadars would not repair them, lest the Turk should think that they were preparing to bring in foreign troops, and so hasten the war which was always really at loading point.<br/><br/>Beyond the green swelling hills of the Mittel Land rose mighty slopes of forest up to the lofty steeps of the Carpathians themselves. Right and left of us they towered, with the afternoon sun falling upon them and bringing out all the glorious colours of this beautiful range, deep blue and purple in the shadows of the peaks, green and brown where grass and rock mingled, and an endless perspective of jagged rock and pointed crags, till these were themselves lost in the distance, where the snowy peaks rose grandly. Here and there seemed mighty rifts in the mountains, through which, as the sun began to sink, we saw now and again the white gleam of falling water. One of my companions touched my arm as we swept round the base of a hill and opened up the lofty, snow-covered peak of a mountain, which seemed, as we wound on our serpentine way, to be right before us:—<br/><br/>“Look! Isten szek!”—“God’s seat!”—and he crossed himself reverently. As we wound on our endless way, and the sun sank lower and lower behind us, the shadows of the evening began to creep round us. This was emphasised by the fact that the snowy mountain-top still held the sunset, and seemed to glow out with a delicate cool pink. Here and there we passed Cszeks and Slovaks, all in picturesque attire, but I noticed that goitre was painfully prevalent. By the roadside were many crosses, and as we swept by, my companions all crossed themselves. Here and there was a peasant man or woman kneeling before a shrine, who did not even turn round as we approached, but seemed in the self-surrender of devotion to have neither eyes nor ears for the outer world. There were many things new to me: for instance, hay-ricks in the trees and here and there very beautiful masses of weeping birch, their white stems shining like silver through the delicate green of the leaves. Now and again we passed a leiterwagon—the ordinary peasant’s cart, with its long, snake-like vertebra, calculated to suit the inequalities of the road. On this were sure to be seated quite a group of home-coming peasants, the Cszeks with their white, and the Slovaks with their coloured, sheepskins, the latter carrying lance-fashion their long staves, with axe at end. As the evening fell it began to get very cold, and the growing twilight seemed to merge into one dark mistiness the gloom of the trees, oak, beech, and pine, though in the valleys which ran deep between the spurs of the hills, as we ascended through the Pass, the dark firs stood out here and there against the background of late-lying snow. Sometimes, as the road was cut through the pine woods that seemed in the darkness to be closing down upon us, great masses of greyness, which here and there bestrewed the trees, produced a peculiarly weird and solemn effect, which carried on the thoughts and grim fancies engendered earlier in the evening, when the falling sunset threw into strange relief the ghost-like clouds which amongst the Carpathians seem to wind ceaselessly through the valleys. Sometimes the hills were so steep that, despite our driver’s haste, the horses could only go slowly. I wished to get down and walk up them, as we do at home, but the driver would not hear of it. “No, no,” he said; “you must not walk here; the dogs are too fierce!” and then he added, with what he evidently meant for[Pg 9]  grim pleasantry—for he looked round to catch the approving smile of the rest—“and you may have enough of such matters before you go to sleep.” The only stop he would make was a moment’s pause to light his lamps.<br/><br/>When it grew dark there seemed to be some excitement amongst the passengers, and they kept speaking to him, one after the other, as though urging him to further speed. He lashed the horses unmercifully with his long whip, and with wild cries of encouragement urged them on to further exertions. Then through the darkness I could see a sort of patch of grey light ahead of us, as though there were a cleft in the hills. The excitement of the passengers grew greater; the crazy coach rocked on its great leather springs, and swayed like a boat tossed on a stormy sea. I had to hold on. The road grew more level, and we appeared to fly along. Then the mountains seemed to come nearer to us on each side and to frown down upon us; we were entering the Borgo Pass. One by one several of the passengers offered me gifts, which they pressed upon me with earnestness which would take no denial; these were certainly of an odd and varied kind, but each was given in simple good faith, with a kindly word, and a blessing, and that strange mixture of fear-meaning movements which I had seen outside the hotel at Bistritz—the sign of the cross and the guard against the evil eye. Then, as we flew along, the driver leaned forward, and on each side the passengers, craning over the edge of the coach, peered eagerly into the darkness. It was evident that something very exciting was either happening or expected, but though I asked each passenger, no one would give me the slightest explanation. This state of excitement kept on for some little time; and at last we saw before us the Pass opening out on the eastern side. There were dark, rolling clouds overhead, and in the air the heavy, oppressive sense of thunder. It seemed as though the mountain range had separated two atmospheres, and that now we had got into the thunderous one. I was now myself looking out for the conveyance which was to take me to the Count. Each moment I expected to see the glare of lamps through the blackness; but all was dark. The only light was the flickering rays of our own lamps, in which steam from our hard-driven horses rose in a white cloud. We could now see the sandy road lying white before us, but there was on it no sign of[Pg 10] a vehicle. The passengers drew back with a sigh of gladness, which seemed to mock my own disappointment. I was already thinking what I had best do, when the driver, looking at his watch, said to the others something which I could hardly hear, it was spoken so quietly and in so low a tone; I thought it was, “An hour less than the time.” Then, turning to me, he said in German worse than my own:—<br/><br/>“There is no carriage here. The Herr is not expected, after all. He will now come on to Bukovina, and return to-morrow or the next day; better the next day.” Whilst he was speaking the horses began to neigh and snort and plunge wildly, so that the driver had to hold them up. Then, amongst a chorus of screams from the peasants and a universal crossing of themselves, a calèche, with four horses, drove up behind us, overtook us, and drew up beside the coach. I could see from the flash of our lamps, as the rays fell on them, that the horses were coal-black and splendid animals. They were driven by a tall man, with a long brown beard and a great black hat, which seemed to hide his face from us. I could only see the gleam of a pair of very bright eyes, which seemed red in the lamplight, as he turned to us. He said to the driver:—<br/><br/>“You are early to-night, my friend.” The man stammered in reply:—<br/><br/>“The English Herr was in a hurry,” to which the stranger replied:—<br/><br/>“That is why, I suppose, you wished him to go on to Bukovina. You cannot deceive me, my friend; I know too much, and my horses are swift.” As he spoke he smiled, and the lamplight fell on a hard-looking mouth, with very red lips and sharp-looking teeth, as white as ivory. One of my companions whispered to another the line from Burger’s “Lenore:”—<br/><br/>“Denn die Todten reiten schnell.”—(“For the dead travel fast.”)<br/><br/>The strange driver evidently heard the words, for he looked up with a gleaming smile. The passenger turned his face away, at the same time putting out his two fingers and crossing himself. “Give me the Herr’s luggage,” said the driver; and with exceeding alacrity my bags were handed out and put in the calèche. Then I descended from the side of the coach, as the calèche was close alongside, the driver helping me with[Pg 11] a hand which caught my arm in a grip of steel; his strength must have been prodigious. Without a word he shook his reins, the horses turned, and we swept into the darkness of the Pass. As I looked back I saw the steam from the horses of the coach by the light of the lamps, and projected against it the figures of my late companions crossing themselves. Then the driver cracked his whip and called to his horses, and off they swept on their way to Bukovina.<br/><br/>As they sank into the darkness I felt a strange chill, and a lonely feeling came over me; but a cloak was thrown over my shoulders, and a rug across my knees, and the driver said in excellent German:—<br/><br/>“The night is chill, mein Herr, and my master the Count bade me take all care of you. There is a flask of slivovitz [the plum brandy of the country] underneath the seat, if you should require it.” I did not take any, but it was a comfort to know it was there, all the same. I felt a little strange, and not a little frightened. I think had there been any alternative I should have taken it, instead of prosecuting that unknown night journey. The carriage went at a hard pace straight along, then we made a complete turn and went along another straight road. It seemed to me that we were simply going over and over the same ground again; and so I took note of some salient point, and found that this was so. I would have liked to have asked the driver what this all meant, but I really feared to do so, for I thought that, placed as I was, any protest would have had no effect in case there had been an intention to delay. By and by, however, as I was curious to know how time was passing, I struck a match, and by its flame looked at my watch; it was within a few minutes of midnight. This gave me a sort of shock, for I suppose the general superstition about midnight was increased by my recent experiences. I waited with a sick feeling of suspense.<br/><br/>Then a dog began to howl somewhere in a farmhouse far down the road—a long, agonised wailing, as if from fear. The sound was taken up by another dog, and then another and another, till, borne on the wind which now sighed softly through the Pass, a wild howling began, which seemed to come from all over the country, as far as the imagination could grasp it through the gloom of the night. At the first howl the horses began to strain and rear, but the driver spoke[Pg 12] to them soothingly, and they quieted down, but shivered and sweated as though after a runaway from sudden fright. Then, far off in the distance, from the mountains on each side of us began a louder and sharper howling—that of wolves—which affected both the horses and myself in the same way—for I was minded to jump from the calèche and run, whilst they reared again and plunged madly, so that the driver had to use all his great strength to keep them from bolting. In a few minutes, however, my own ears got accustomed to the sound, and the horses so far became quiet that the driver was able to descend and to stand before them. He petted and soothed them, and whispered something in their ears, as I have heard of horse-tamers doing, and with extraordinary effect, for under his caresses they became quite manageable again, though they still trembled. The driver again took his seat, and shaking his reins, started off at a great pace. This time, after going to the far side of the Pass, he suddenly turned down a narrow roadway which ran sharply to the right.<br/><br/>Soon we were hemmed in with trees, which in places arched right over the roadway till we passed as through a tunnel; and again great frowning rocks guarded us boldly on either side. Though we were in shelter, we could hear the rising wind, for it moaned and whistled through the rocks, and the branches of the trees crashed together as we swept along. It grew colder and colder still, and fine powdery snow began to fall, so that soon we and all around us were covered with a white blanket. The keen wind still carried the howling of the dogs, though this grew fainter as we went on our way. The baying of the wolves sounded nearer and nearer, as though they were closing round on us from every side. I grew dreadfully afraid, and the horses shared my fear; but the driver was not in the least disturbed. He kept turning his head to left and right, but I could not see anything through the darkness.<br/><br/>Suddenly, away on our left, I saw a faint flickering blue flame. The driver saw it at the same moment; he at once checked the horses and, jumping to the ground, disappeared into the darkness. I did not know what to do, the less as the howling of the wolves grew closer; but while I wondered the driver suddenly appeared again, and without a word took his seat, and we resumed our journey. I think I must have[Pg 13] fallen asleep and kept dreaming of the incident, for it seemed to be repeated endlessly, and now, looking back, it is like a sort of awful nightmare. Once the flame appeared so near the road that even in the darkness around us I could watch the driver’s motions. He went rapidly to where the blue flame rose—it must have been very faint, for it did not seem to illumine the place around it at all—and gathering a few stones, formed them into some device. Once there appeared a strange optical effect: when he stood between me and the flame he did not obstruct it, for I could see its ghostly flicker all the same. This startled me, but as the effect was only momentary, I took it that my eyes deceived me straining through the darkness. Then for a time there were no blue flames, and we sped onwards through the gloom, with the howling of the wolves around us, as though they were following in a moving circle.<br/><br/>At last there came a time when the driver went further afield than he had yet done, and during his absence the horses began to tremble worse than ever and to snort and scream with fright. I could not see any cause for it, for the howling of the wolves had ceased altogether; but just then the moon, sailing through the black clouds, appeared behind the jagged crest of a beetling, pine-clad rock, and by its light I saw around us a ring of wolves, with white teeth and lolling red tongues, with long, sinewy limbs and shaggy hair. They were a hundred times more terrible in the grim silence which held them than even when they howled. For myself, I felt a sort of paralysis of fear. It is only when a man feels himself face to face with such horrors that he can understand their true import.<br/><br/>All at once the wolves began to howl as though the moonlight had had some peculiar effect on them. The horses jumped about and reared, and looked helplessly round with eyes that rolled in a way painful to see; but the living ring of terror encompassed them on every side, and they had perforce to remain within it. I called to the coachman to come, for it seemed to me that our only chance was to try to break out through the ring and to aid his approach. I shouted and beat the side of the calèche, hoping by the noise to scare the wolves from that side, so as to give him a chance of reaching the trap. How he came there, I know not, but I heard his[Pg 14] voice raised in a tone of imperious command, and looking towards the sound, saw him stand in the roadway. As he swept his long arms, as though brushing aside some impalpable obstacle, the wolves fell back and back further still. Just then a heavy cloud passed across the face of the moon, so that we were again in darkness.<br/><br/>When I could see again the driver was climbing into the calèche, and the wolves had disappeared. This was all so strange and uncanny that a dreadful fear came upon me, and I was afraid to speak or move. The time seemed interminable as we swept on our way, now in almost complete darkness, for the rolling clouds obscured the moon. We kept on ascending, with occasional periods of quick descent, but in the main always ascending. Suddenly I became conscious of the fact that the driver was in the act of pulling up the horses in the courtyard of a vast ruined castle, from whose tall black windows came no ray of light, and whose broken battlements showed a jagged line against the moonlit sky."
			);

			$this->default_array10 = array(

				'title' => "Author Showcase - Brendan T. Beery",
				'category' => 'Author Showcase',
				'providername' => "Brendan T. Beery",
				'providerimg' => ROOT_IMG_URL. 'brenberry.jpg',
				'providerbio' => 'Brendan T. Beery is the author of "The Sylvian Fissure", as well as a Law professor and frequent media commentator. He can be found online at <a href="https://beeryblog.com/">Beeryblog.com</a>',
				'content' => "<p class='wpbooklist-content-actual-title'>Brendan T. Beery</p>Law professor and frequent media commentator Brendan Beery hails from Tampa, Florida, where he lives happily with his two German shepherds. To write The Sylvian Fissure, he partnered with Michigan native Dan Ray, an attorney as well as a former law professor. The two have crafted a unique, visionary tale that provides insight into the tribulations and redemption of the human spirit.<br/><br/><p class='wpbooklist-content-actual-title'>About The Sylvian Fissure</p>Father Jim Downs has many secrets, among them that he no longer believes in God. His only remaining wish is to be let alone—to forget, to brood, and to die.<br/><br/>His world turns upside down when he learns that a parishioner, a woman named Bernice who is dying of cancer, has been recruited into the biggest experiment of all time: government scientists will use her death to find out what exists in the hereafter—or what some might call Heaven.<br/><br/>Downs is shocked to learn that Bernice is not the only one from his parish who has been conscripted into the government’s project; he has too, and when he discovers the jarring reason why, he is forced to confront his own demons, and a dead loved one, head-on. Bernice guides Downs to a place he thought did not exist: a place inside his own self, where he must find redemption."
			);
		}

		
		public function output_storytime_reader(){

			// Build the Categories string
			$categories_string = '';
			$cat_array = array();
			foreach ( $this->stories_db_data as $key => $value) {
				array_push( $cat_array, $value->category);
			}
			$cat_array = array_unique( $cat_array);
			foreach ( $cat_array as $key => $value) {
			  $categories_string = $categories_string. '<option value="' . $value. '">' . $value. '</option>';
			}

			// Build the Most Recent string
			$recent_string = '';
			$this->stories_db_data = array_reverse( $this->stories_db_data);
			foreach ( $this->stories_db_data as $key => $value) {
			  $recent_string = $recent_string. '<p class="wpbooklist-storytime-listed-story" data-id="' . $value->ID. '">' . $value->title. '</p>';
			}


		  	$this->reader_shortcode_output = '
			<div class="wpbooklist-storytime-reader-top-cont">
			  <div id="wpbooklist-storytime-reader-inner-cont">

				<div id="wpbooklist-storytime-reader-titlebar-div">
				  <div class="wpbooklist-storytime-reader-titlebar-div-1">
					<img src="' .ROOT_IMG_ICONS_URL. 'storytime.svg" />
					<p><a href="https://www.wpbooklist.com">' .__('WPBookList StoryTime Reader', 'wpbooklist'). '</a></p>
				  </div>
				  <div id="wpbooklist-storytime-reader-titlebar-div-2">
					<h2>' .__('Select a Story', 'wpbooklist'). ' ...</h2>
				  </div>
				</div>

				<div class="wpbooklist-storytime-reader-selection-div">
				  <div id="wpbooklist-storytime-reader-selection-div-1-inner-1">
					<select id="wpbooklist-storytime-category-select">
					  <option selected default disabled>Select a Category...</option>
					  <option>' .__('Recent Additions', 'wpbooklist'). '</option>
					  ' . $categories_string. '
					</select>
					' . $recent_string. '
				  </div>
				  <div id="wpbooklist-storytime-reader-selection-div-1-inner-2" data-location="frontend">
					<p>' .__('Browse Stories', 'wpbooklist'). ' ...</p>
					<img src="' .ROOT_IMG_URL. 'next-down.png" />
				  </div>
				</div>
				<div id="wpbooklist-storytime-reader-content-div" data-location="frontend">

				</div>
				<div id="wpbooklist-storytime-reader-pagination-div">
				  <div id="wpbooklist-storytime-reader-pagination-div-1">
					<img src="' .ROOT_IMG_URL. 'next-left.png" />
				  </div>
				  <div class="wpbooklist-storytime-reader-pagination-div-2">
					<div class="wpbooklist-storytime-reader-pagination-div-2-inner">
					  <p>
						<span id="wpbooklist-storytime-reader-pagination-div-2-span-1">1</span><span id="wpbooklist-storytime-reader-pagination-div-2-span-2">/</span><span id="wpbooklist-storytime-reader-pagination-div-2-span-3">10</span>
					  </p>
					</div>
				  </div>
				  <div id="wpbooklist-storytime-reader-pagination-div-3">
					<img src="' .ROOT_IMG_URL. 'next-right.png" />
				  </div>
				</div>
				<div id="wpbooklist-storytime-reader-provider-div">
				  <div id="wpbooklist-storytime-reader-provider-div-1">
					<img src="' .ROOT_IMG_URL. 'icon-256x256.png" />
				  </div>
				  <div id="wpbooklist-storytime-reader-provider-div-2">
					<p id="wpbooklist-storytime-reader-provider-p-1"> Discover new Authors and Publishers!</p>
					<p id="wpbooklist-storytime-reader-provider-p-2">' .__("WPBookList StoryTime is WPBooklist's Content-Delivery System, providing you and your website visitors with Sample Chapters, Short Stories, News, Interviews and more!", 'wpbooklist'). '</p>
				  </div>
				  <div id="wpbooklist-storytime-reader-provider-div-delete">
				  	<p id="wpbooklist-storytime-reader-provider-div-delete-p"><a href="https://wordpress.org/plugins/wpbooklist/">' .__("Powered by WPBookList", 'wpbooklist'). '</a></p>
				  </div>
				</div>
			  </div>
			</div>';


		}

			/**
			 * Code for adding the top admin stroytime notice/advert
			 */
			public function wpbooklist_jre_storytime_admin_notice() {
				global $wpdb;
				$table_name	   = $wpdb->prefix . 'wpbooklist_jre_storytime_stories_settings';
				$settings_results = $wpdb->get_row( "SELECT * FROM $table_name" );
				$dismiss		  = $settings_results->newnotify;
				$dismissmessage   = $settings_results->notifydismiss;

				if ( 1 === $dismiss && '' !== $settings_results->notifymessage && 1 === $dismissmessage ) {
					$message = $settings_results->notifymessage;
					?>
					<div class="notice notice-success is-dismissible">
						<p><?php echo $message; ?></p>
					</div>
					<?php
				}
			}

			/** Function that displays StoryTime on the front end
			 *
			 *  @param array $atts - The array that contains the shortcode attributes/arguments.
			 */
			public function wpbooklist_storytime_shortcode( $atts ) {
				global $wpdb;

				ob_start();
				require_once CLASS_STORYTIME_DIR . 'class-storytime.php';
				$storytime_class = new WPBookList_Storytime( 'frontend_shortcode_output' );
				echo $storytime_class->reader_shortcode_output;
				return ob_get_clean();
			}

	}

endif;
