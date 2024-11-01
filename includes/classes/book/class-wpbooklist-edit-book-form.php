<?php
/**
 * WPBookList Edit-Book-Form Tab Class - class-wpbooklist-edit-book-form.php
 *
 * @author   Jake Evans
 * @category Book
 * @package  Includes/Classes/Book
 * @version  6.1.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Edit_Book_Form', false ) ) :
	/**
	 * WPBookList_Edit_Book Class.
	 */
	class WPBookList_Edit_Book_Form {

		public $table;
		public $limit;

		/** Outputs the book editing form
		 *
		 *  @param string $table - The table to pull book info from.
		 *  @param int    $offset - The offset to begin pulling from db on.
		 *  @param string $search_mode - The way in which to perform a search.
		 *  @param string $search_term - The actual search term.
		 */
		public function output_edit_book_form( $table, $offset, $search_mode = null, $search_term = null ) {
			global $wpdb;

			// Get Translations.
			require_once CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
			$this->trans = new WPBookList_Translations();
			$this->trans->trans_strings();

			// Set the current WordPress user.
			$currentwpuser = wp_get_current_user();

			// Now we'll determine access, and stop all execution if user isn't allowed in.
			require_once CLASS_UTILITIES_DIR . 'class-wpbooklist-utilities-accesscheck.php';
			$this->access          = new WPBookList_Utilities_Accesscheck();
			$this->currentwpbluser = $this->access->wpbooklist_accesscheck( $currentwpuser->ID, 'editdelete' );

			// If we received false from accesscheck class, display permissions message and stop all further execution in this class.
			if ( false === $this->currentwpbluser ) {

				// Outputs the 'No Permission!' message.
				$this->initial_output = $this->access->wpbooklist_accesscheck_no_permission_message();
				return $this->initial_output;
			}

			// Now we'll determine if the user is allowed to only delete titles.
			require_once CLASS_UTILITIES_DIR . 'class-wpbooklist-utilities-accesscheck.php';
			$this->access                    = new WPBookList_Utilities_Accesscheck();
			$this->currentwpbluserdeleteonly = $this->access->wpbooklist_accesscheck( $currentwpuser->ID, 'deleteonly' );

			// Now we'll determine if the user is allowed to only edit titles.
			require_once CLASS_UTILITIES_DIR . 'class-wpbooklist-utilities-accesscheck.php';
			$this->access                  = new WPBookList_Utilities_Accesscheck();
			$this->currentwpblusereditonly = $this->access->wpbooklist_accesscheck( $currentwpuser->ID, 'editonly' );

			// Now we'll get what libraries the user is allowed to access.
			require_once CLASS_TRANSIENTS_DIR . 'class-wpbooklist-transients.php';
			$transients          = new WPBookList_Transients();
			$settings_table_name = $wpdb->prefix . 'wpbooklist_jre_users_table';
			$transient_name      = 'wpbl_' . md5( 'SELECT * FROM ' . $settings_table_name . ' WHERE wpuserid = ' . $currentwpuser->ID );
			$transient_exists    = $transients->existing_transient_check( $transient_name );
			if ( $transient_exists ) {
				$this->wpbl_user = $transient_exists;
			} else {
				$query           = 'SELECT * FROM ' . $settings_table_name . ' WHERE wpuserid = ' . $currentwpuser->ID;
				$this->wpbl_user = $transients->create_transient( $transient_name, 'wpdb->get_row', $query, MONTH_IN_SECONDS );
			}

			$wpuser = $this->wpbl_user;

			wp_enqueue_media();

			// Determine what table to display upon page load.
			$this->table = $table;
			if ( 'default' === $this->table ) {

				// If the user is allowed access to the default table.
				if ( false !== stripos( $wpuser->libraries, 'alllibraries' ) || false !== stripos( $wpuser->libraries, 'wpbooklist_jre_saved_book_log' ) ) {
					$this->table = $wpdb->prefix . 'wpbooklist_jre_saved_book_log';
				} else {

					// If user is not allowed access to default table, grab first tabe user is allowed access to.
					if ( false !== stripos( $wpuser->libraries, '-' ) ) {

						$temp = explode( '-', $wpuser->libraries );

						if ( '' !== $temp[0] && null !== $temp[0] ) {
							$this->table = $temp[0];
						} else {
							$this->table = $temp[1];
						}
					} else {
						if ( '' !== $wpuser->libraries && null !== $wpuser->libraries ) {
							$this->table = $wpuser->libraries;
						}
					}
				}
			}

			global $wpdb;
			if ( null !== $search_mode && null !== $search_term ) {
				if ( 'author' === $search_mode ) {
					$this->books_actual = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $this->table WHERE author LIKE '%s'", '%' . $search_term . '%' ) );
				}

				if ( 'title' === $search_mode ) {
					$this->books_actual = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $this->table WHERE title LIKE '%s'", '%' . $search_term.'%' ) );
				}

				if ( 'both' === $search_mode ) {
					$this->books_actual = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $this->table WHERE title LIKE '%s' OR author LIKE '%s'", '%' . $search_term . '%', '%' . $search_term . '%' ) );
				}
			} else {
				$this->books_actual = $wpdb->get_results( "SELECT * FROM $this->table" );
			}

			// Getting number of results.
			$this->limit = $wpdb->num_rows;

			/** Default sorting - sorts by IDs from low to high.
			 *
			 *  @param string $a - variable to sort.
			 *  @param string $b - variable to sort.
			 */
			function compare_ids( $a, $b ) {
				return $a->ID - $b->ID;
			}

			usort( $this->books_actual, 'compare_ids' );

			// Set up library drop-down.
			$table_name = $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names';
			$db_row     = $wpdb->get_results( "SELECT * FROM $table_name" );

			$string1 = '<div id="wpbooklist-edit-books-lib-search-div">
				<div id="wpbooklist-edit-books-lib-div">
					<p class="wpbooklist-tab-intro-para">Select a Library to Edit Books From</p>
					<select class="wpbooklist-editbook-select-default" id="wpbooklist-editbook-select-library">';
			// If user has 'alllibraries' in the 'Libraries' DB Column, add in the default Library.
			$string2     = '';
			$defaultflag = true;
			if ( false !== stripos( $wpuser->libraries, 'alllibraries' ) || false !== stripos( $wpuser->libraries, 'wpbooklist_jre_saved_book_log' ) ) {
				$string2     = $string2 . '<option selected default value="' . $wpdb->prefix . 'wpbooklist_jre_saved_book_log">' . $this->trans->trans_61 . '</option> ';
				$defaultflag = false;
			}

			// Building drop-down of all libraries.
			foreach ( $db_row as $key => $db ) {
				if ( ( '' !== $db->user_table_name ) || ( null !== $db->user_table_name ) ) {

					// Making sure the user is allowed to access this particular library - first check for 'alllibraries' access.
					if ( false !== stripos( $wpuser->libraries, 'alllibraries' ) || 'SuperAdmin' === $wpuser->role ) {

						// If we're on the first iteration of the foreach, make this the selected default value.
						if ( 0 === $key ) {

							// If we haven't already set a default...
							if ( $defaultflag ) {
								$string2 = $string2 . '<option selected default value="' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . '">' . ucfirst( $db->user_table_name ) . '</option>';
							} else {
								$string2 = $string2 . '<option value="' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . '">' . ucfirst( $db->user_table_name ) . '</option>';
							}
						} else {
							$string2 = $string2 . '<option value="' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . '">' . ucfirst( $db->user_table_name ) . '</option>';
						}
					} else {

						if ( false !== stripos( $wpuser->libraries, $db->user_table_name ) ) {

							// If we're on the first iteration of the foreach, make this the selected default value.
							if ( 0 === $key ) {
								// If we haven't already set a default...
								if ( $defaultflag ) {
									$string2 = $string2 . '<option selected default value="' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . '">' . ucfirst( $db->user_table_name ) . '</option>';
								} else {
									$string2 = $string2 . '<option value="' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . '">' . ucfirst( $db->user_table_name ) . '</option>';
								}
							} else {
								$string2 = $string2 . '<option value="' . $wpdb->prefix . 'wpbooklist_jre_' . $db->user_table_name . '">' . ucfirst( $db->user_table_name ) . '</option>';
							}
						}
					}
				}
			}

			$string3 = '</select>
					</div>
					<div class="wpbooklist-spinner" id="wpbooklist-spinner-edit-change-lib"></div>
					<div id="wpbooklist-edit-books-search-div">
						<p id="wpbooklist-edit-books-lib-p">Search for a Book to Edit</p>
						<div class="wpbooklist-edit-book-search-by-div">
							<label>Search by Title</label>
							<input id="wpbooklist-search-title-checkbox" type="checkbox"/>
						</div>
						<div class="wpbooklist-edit-book-search-by-div">
							<label>Search by Author</label>
							<input id="wpbooklist-search-author-checkbox" type="checkbox"/>
						</div>
						<input id="wpbooklist-edit-book-search-input" type="text" placeholder="Enter a Search Term..." />
						<button id="wpbooklist-edit-book-search-button" type="button">Search</button>
					</div>
				</div>
				<div id="wpbooklist-bulk-edit-div">';

			// If we received true from accesscheck class, do not display the bulk delete button.
			if ( $this->currentwpbluserdeleteonly ) {
				$string3 = $string3 . '<button id="wpbooklist-bulk-edit-mode-on-button"';

				$string4 = '';
				if ( 0 === count( $this->books_actual ) ) {
					$string3 = $string3 . 'disabled';
				}

					$string3 = $string3 . ' type="button">Bulk Delete Mode</button>
					<div id="wpbooklist-bulk-edit-mode-on-div">
						<button disabled id="wpbooklist-bulk-edit-mode-delete-checked" type="button">Delete Checked Books</button>
						<button id="wpbooklist-bulk-edit-mode-delete-all-in-lib" type="button">Delete All Books in This Library</button>
						<button id="wpbooklist-bulk-edit-mode-delete-all-plus-pp-in-lib" type="button">Delete All Books & Pages & Posts in This Library</button>
						<button id="wpbooklist-bulk-edit-mode-delete-all-in-lib-cancel" type="button">Cancel</button>
					</div>
					<button id="wpbooklist-reorder-button" type="button">Reorder Books</button>
					<button id="wpbooklist-cancel-reorder-button" type="button">Cancel</button>';

			}

			$string3 = $string3 . '</div>';

			// Now build the total number of books in this Library.
			$total_books_html = '
			<div id="wpbooklist-edit-books-total-count-wrapper">
				<p><span id="wpbooklist-edit-books-total-count-text">' . $this->trans->trans_657 . ': </span><span id="wpbooklist-edit-books-total-count-number">' . number_format( count( $this->books_actual ) ) . '</span></p>
			</div>';
			$string3 = $string3 . $total_books_html;

			$string6 = '';

			// If there are no results from the query...
			if ( null === $this->books_actual ) {
				$string6 = '<div class="wpbooklist-search-indiv-container"><div id="wpbooklist-search-results-info"></div>';
			}

			$divclose = '';
			if ( 1 > $this->books_actual || null === $this->books_actual ) {
				$divclose = '</div>';
			} else {

				// The loop that will construct each line.
				foreach ( $this->books_actual as $key => $book ) {

					if ( ( $key >= $offset ) && ( $key < ( $offset + EDIT_PAGE_OFFSET ) ) ) {

						if ( '' === $book->title || null === $book->title ) {
							$book->title = $this->trans->trans_595 . '!';
						}

						if ( '' === $book->author || null === $book->author ) {
							$book->author = $this->trans->trans_596 . '!';
						}

						if ( '' === $book->image || null === $book->image ) {
							$book->image = ROOT_IMG_URL . 'image_unavaliable.png';
						}

						$string6 = $string6 . '<div class="wpbooklist-search-indiv-container"><div id="wpbooklist-search-results-info">

						</div>
						<div class="wpbooklist-edit-book-indiv-div-class" id="wpbooklist-edit-book-indiv-div-id-' . $key . '"">
							<div class="wpbooklist-edit-title-div">
								<div class="wpbooklist-bulk-delete-checkbox-div">
									<input data-key="' . $key . '" data-table="' . $this->table . '" data-book-id="' . $book->ID . '" class="wpbooklist-bulk-delete-checkbox" type="checkbox" /><label>Delete Title</label>
								</div>
								<div class="wpbooklist-edit-img-author-div">
									<img data-bookid="' . $book->ID . '" data-bookuid="' . $book->book_uid . '" data-booktable="' . $this->table . '" class="wpbooklist-edit-book-cover-img wpbooklist-show-book-colorbox" src="' . $book->image . '"/>
									<p class="wpbooklist-edit-book-title wpbooklist-show-book-colorbox" data-booktable="' . $this->table . '" data-bookid="' . $book->ID . '">' . stripslashes( $book->title ) . '</p><br/>
									<img class="wpbooklist-edit-book-icon wpbooklist-book-icon-author " src="' . ROOT_IMG_ICONS_URL . 'author.svg"/><p class="wpbooklist-edit-book-author">' . $book->author . '</p>
								</div>
							</div>
							<div class="wpbooklist-edit-actions-div">';

						// If we received true from accesscheck class, do not display the edit button.
						if ( $this->currentwpblusereditonly ) {

									$string6 = $string6 . '
									<div class="wpbooklist-edit-actions-edit-button" data-key="' . $key . '" data-table="' . $this->table . '" data-book-id="' . $book->ID . '">
										<p>Edit
											<img class="wpbooklist-edit-book-icon wpbooklist-edit-book-icon-button" src="' . ROOT_IMG_ICONS_URL . 'pencil.svg"/> 
										</p>
									</div>';
						}

						// If we received true from accesscheck class, display the individual delete button.
						if ( $this->currentwpbluserdeleteonly ) {
									$string6 = $string6 . '
									<div class="wpbooklist-edit-actions-delete-button" data-key="' . $key . '" data-table="' . $this->table . '" data-book-id="' . $book->ID . '"> 
										<p>Delete
											<img class="wpbooklist-edit-book-icon wpbooklist-edit-book-icon-button" src="' . ROOT_IMG_ICONS_URL . 'garbage-bin.svg"/>
										</p>
									</div>
									<div class="wpbooklist-edit-book-delete-page-post-div">';

							if ( null !== $book->page_yes && 'false' !== $book->page_yes ) {
								$string6 = $string6 . '<input data-id="' . $book->page_yes . '" id="wpbooklist-delete-page-input" type="checkbox"/><label for="wpbooklist-edit-delete-page">' . $this->trans->trans_601 . '</label><br/>';
							}

							if ( null !== $book->post_yes && 'false' !== $book->post_yes ) {
								$string6 = $string6 . '<input data-id="' . $book->post_yes . '" id="wpbooklist-delete-post-input" type="checkbox"/><label for="wpbooklist-edit-delete-post">' . $this->trans->trans_602 . '</label>';
							}

							$string6 = $string6 . '</div>';
						}

						$string6 = $string6 . '
							</div>
							<div class="wpbooklist-spinner" id="wpbooklist-spinner-' . $key . '"></div>
							<div class="wpbooklist-delete-result" id="wpbooklist-delete-result-' . $key . '"></div>
							<div class="wpbooklist-edit-form-div" id="wpbooklist-edit-form-div-' . $key . '">
								
							</div>
						</div></div>';
					}
				}
			}

			// Begin building the Pagination Stuff.
			$string7 = '<div id="wpbooklist-edit_books-pagination-div">
							<div ';

			$string8 = '';
			if ( 0 === count( $this->books_actual ) ) {
				$string8 = 'style="opacity:0.3; pointer-events:none;"';
			}

			// If we have more books in the Library than the EDIT_PAGE_OFFSET constant, display pagination.
			if ( count( $this->books_actual ) > EDIT_PAGE_OFFSET ) {

				$pagination_options_string = '';

				// Setting up variables to determine the previous offset to go back to, or to disable that ability if on Page 1.
				$prevnum          = EDIT_PAGE_OFFSET;
				$styledisableleft = '';

				// Setting up variables to determine the next offset to go to, or to disable that ability if on last Page.
				if ( 0 < ( count( $this->books_actual ) - EDIT_PAGE_OFFSET ) ) {
					$nextnum           = EDIT_PAGE_OFFSET;
					$styledisableright = '';
				} else {
					$nextnum           = 0;
					$styledisableright = 'style="pointer-events:none;opacity:0.5;"';
				}

				// Getting total number of full pages and/or if there's only a partial/remainder page.
				if ( count( $this->books_actual ) > 0 && EDIT_PAGE_OFFSET > 0 ) {

					// Getting whole pages. Can be zero if total number of books is less that amount set to be displayed per page in the backend settings.
					$whole_pages = floor( count( $this->books_actual ) / EDIT_PAGE_OFFSET );

					// Determing whether there is a partial page, whose contents contains less books than amount set to be displayed per page in the backend settings. Will only be 0 if total number of books is evenly divisible by EDIT_PAGE_OFFSET.
					$remainder_pages = count( $this->books_actual ) % EDIT_PAGE_OFFSET;
					if ( 0 !== $remainder_pages ) {
						$remainder_pages = 1;
					}

					// If there's only one page, don't show pagination.
					if ( ( 1 === $whole_pages && 0 === $remainder_pages ) || ( 0 === $whole_pages && 1 === $remainder_pages ) ) {
						return;
					}

					// The loop that will create the <option> html for the <select>.
					for ( $i = 1; $i <= ( $whole_pages + $remainder_pages ); $i++ ) {

						if ( ( 1 + ( 0 / EDIT_PAGE_OFFSET ) ) === $i ) {
							$pagination_options_string = $pagination_options_string . '<option value=' . ( ( $i - 1 ) * EDIT_PAGE_OFFSET ) . ' selected>' . $this->trans->trans_600 . ' ' . $i . '</option>';
						} else {
							$pagination_options_string = $pagination_options_string . '<option value=' . ( ( $i - 1 ) * EDIT_PAGE_OFFSET ) . '>' . $this->trans->trans_600 . ' ' . $i . '</option>';
						}
					}
				}

				// Actual Pagination HTML.
				if ( '' !== $pagination_options_string ) {

					$string_pagination = '
					<div class="wpbooklist-pagination-div">
						<div class="wpbooklist-pagination-div-inner">
							<div style="opacity:0.3; pointer-events:none;" data-limit="' . $this->limit . '" id="wpbooklist-edit-previous-100" class="wpbooklist-pagination-left-div" ' . $styledisableleft . ' data-offset="' . $prevnum . '" data-table="' . $this->table . '">
								<p><img class="wpbooklist-pagination-prev-img" src="' . ROOT_IMG_URL . 'next-left.png" />' . $this->trans->trans_36 . '</p>
							</div>
							<div class="wpbooklist-pagination-middle-div">
								<select data-limit="' . $this->limit . '" class="wpbooklist-pagination-middle-div-select" data-table="' . $this->table . '">
									' . $pagination_options_string . '
								</select>
							</div>
							<div data-limit="' . $this->limit . '" id="wpbooklist-edit-next-100" class="wpbooklist-pagination-right-div" ' . $styledisableright . ' data-offset="' . $nextnum . '" data-table="' . $this->table . '">
								<p>' . $this->trans->trans_37 . '<img class="wpbooklist-pagination-prev-img" src="' . ROOT_IMG_URL . 'next-right.png" /></p>
							</div>
						</div>
					</div>';
				} else {
					$string_pagination = '';
				}

				$this->pagination_string = $string_pagination . '</div>
										<div class="wpbooklist-spinner" id="wpbooklist-spinner-pagination"></div>' . $divclose;
			} else {
				$this->pagination_string = '</div></div></div>';
			}

			return $string1 . $string2 . $string3 . $string6 . $string7 . $string8 . $this->pagination_string;
		}


	}

endif;
