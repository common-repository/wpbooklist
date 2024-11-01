<?php
/**
 * Class WPBookList_Post - class-wpbooklist-post.php
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes/Post
 * @version  6.1.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Post', false ) ) :

	/**
	 * WPBookList_Post Class.
	 */
	class WPBookList_Post {


		/** Class Constructor
		 *
		 *  @param array $book_array - The array holding all the book info.
		 */
		public function __construct( $book_array ) {

			$this->isbn               = $book_array['isbn'];
			$this->title              = $book_array['title'];
			$this->author             = $book_array['author'];
			$this->author_url         = $book_array['author_url'];
			$this->category           = $book_array['category'];
			$this->price              = $book_array['price'];
			$this->pages              = $book_array['pages'];
			$this->pub_year           = $book_array['pub_year'];
			$this->publisher          = $book_array['publisher'];
			$this->description        = $book_array['description'];
			$this->notes              = $book_array['notes'];
			$this->rating             = $book_array['rating'];
			$this->image              = $book_array['image'];
			$this->finished           = $book_array['finished'];
			$this->date_finished      = $book_array['date_finished'];
			$this->signed             = $book_array['signed'];
			$this->first_edition      = $book_array['first_edition'];
			$this->page_yes           = $book_array['page_yes'];
			$this->post_yes           = $book_array['post_yes'];
			$this->itunes_page        = $book_array['itunes_page'];
			$this->google_preview     = $book_array['google_preview'];
			$this->amazon_detail_page = $book_array['amazon_detail_page'];
			$this->review_iframe      = $book_array['review_iframe'];
			$this->similar_products   = $book_array['similar_products'];
			$this->book_uid           = $book_array['book_uid'];

			// Get author id.
			$this->page_author_id = get_current_user_id();

			// Create the WPBookList Post Category.
			$cat_id = $this->create_post_category();
			$this->create_book_post();
			$this->add_to_db();
		}

		/**
		 *  Function to create the WPBookList page category.
		 */
		private function create_post_category() {

			// Create default WPBookList Book Post Category if it doesn't already exist.
			$create_cat = true;
			$cat_id     = 0;
			foreach ( ( get_categories() ) as $category ) {
				if ( 'WPBookList Book Post' === $category->cat_name ) {
					$cat_id     = get_cat_ID( 'WPBookList Book Post' );
					$create_cat = false;
				}
			}

			if ( false === $create_cat ) {
				return $cat_id;
			} else {
				$result = wp_insert_term(
					'WPBookList Book Post',
					'category',
					array(
						'description' => 'This is a category created by WPBookList to display a book in it\'s very own individual post',
						'slug'        => 'wpbooklist-book-post-cat',
					)
				);

				if ( is_object( $result ) ) {
					$this->cat_create_result = $result;
				} else {
					$this->cat_create_result = $result['term_id'];
				}
			}
		}

		/**
		 *  Function to actually create the post.
		 */
		private function create_book_post() {

			// Initialize the page ID to -1. This indicates no action has been taken.
			$this->post_id = -1;

			$excerpt = $this->description;

			if ( '' === $excerpt || null === $excerpt ) {
				$excerpt = $this->title;
			}

			if ( '' === $excerpt || null === $excerpt ) {
				$excerpt = 'No excerpt available';
			}

			// Set the post ID so that we know the post was created successfully.
			$this->post_id = wp_insert_post(
				array(
					'comment_status' => 'open',
					'ping_status'    => 'closed',
					'post_author'    => get_current_user_id(),
					'post_name'      => $this->title . ' (post)',
					'post_title'     => wp_strip_all_tags( $this->title ),
					'post_status'    => 'publish',
					'post_type'      => 'post',
					'post_content'   => '<div class="wpbooklist-page-content">DO NOT DELETE</div>',
					'post_excerpt'   => $excerpt,
				)
			);

			// Assign the category to our new post.
			$get_cat_id = get_cat_ID( 'WPBookList Book Post' );
			$cat_slug   = 'wpbooklist-book-post-cat';
			if ( $this->post_id > 0 ) {

				$this->create_post_image( $this->image, $this->post_id );
				wp_set_post_terms( $this->post_id, array( $get_cat_id ), 'category' );
			}
		}

		/** Function to create the image for the post.
		 *
		 *  @param string $image_url - The url of the image.
		 *  @param int    $post_id - The post ID.
		 */
		private function create_post_image( $image_url, $post_id ) {
			$upload_dir = wp_upload_dir();

			$image_data = wp_remote_get( $image_url );

			// Check the response code.
			$response_code    = wp_remote_retrieve_response_code( $image_data );
			$response_message = wp_remote_retrieve_response_message( $image_data );

			if ( 200 !== $response_code && ! empty( $response_message ) ) {
				return new WP_Error( $response_code, $response_message );
			} elseif ( 200 !== $response_code ) {
				return new WP_Error( $response_code, 'Unknown error occurred with wp_remote_get() trying to get an image url in the create_post_image() function' );
			} else {
				$image_data = wp_remote_retrieve_body( $image_data );
			}

			$image_url = str_replace( '%', '', $image_url );
			$filename  = basename( $image_url );
			if ( wp_mkdir_p( $upload_dir['path'] ) ) {
				$file = $upload_dir['path'] . '/' . $filename;
			} else {
				$file = $upload_dir['basedir'] . '/' . $filename;
			}

			// Initialize the WP filesystem.
			global $wp_filesystem;
			if ( empty( $wp_filesystem ) ) {
				require_once ABSPATH . '/wp-admin/includes/file.php';
				WP_Filesystem();
			}

			$result      = $wp_filesystem->put_contents( $file, $image_data );
			$wp_filetype = wp_check_filetype( $filename, null );
			$attachment  = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title'     => sanitize_file_name( $filename ),
				'post_content'   => '',
				'post_status'    => 'inherit',
			);

			require_once ABSPATH . 'wp-admin/includes/image.php';
			$attach_id   = wp_insert_attachment( $attachment, $file, $post_id );
			$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
			$res1        = wp_update_attachment_metadata( $attach_id, $attach_data );
			$res2        = set_post_thumbnail( $post_id, $attach_id );
		}

		/**
		 *  Function to record the post's creation and details..
		 */
		private function add_to_db() {
			global $wpdb;

			$table_name = $wpdb->prefix . 'wpbooklist_jre_saved_page_post_log';

			$insert_array = array(
				'book_uid'        => $this->book_uid,
				'book_title'      => $this->title,
				'post_id'         => $this->post_id,
				'type'            => 'post',
				'post_url'        => get_permalink( $this->post_id ),
				'author'          => $this->page_author_id,
				'active_template' => 'default',
			);

			return $wpdb->insert( $table_name, $insert_array);
		}
	}

endif;
