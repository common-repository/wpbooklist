<?php
/**
 * Class WPBookList_Rest_Functions - class-wpbooklist-rest-functions.php
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes/Rest
 * @version  6.1.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Rest_Functions', false ) ) :
	/**
	 * WPBookList_Rest_Functions class. Here are all the functions that manipulate date received from REST Endpoints created by WPBookList
	 */
	class WPBookList_Rest_Functions {

		/**
		 * Class Constructor - Simply calls the Translations
		 */
		public function __construct() {

				// For the REST API update for validating patreon.
				add_action( 'rest_api_init', function () {
					register_rest_route( 'wpbooklist/v1', '/firstkey/(?P<firstkey>[a-z0-9\-]+)/secondkey/(?P<secondkey>[a-z0-9\-]+)', array(
						'methods'  => 'GET',
						'callback' => array( $this, 'wpbooklist_jre_storytime_patreon_validate_rest_api_notice' ),
					) );
				});

				// For the REST API update for dashboard messages.
				add_action( 'rest_api_init', function () {
					register_rest_route( 'wpbooklist/v1', '/notice/(?P<notice>[a-z0-9\-]+)', array(
						'methods'  => 'GET',
						'callback' => array( $this, 'wpbooklist_jre_rest_api_notice' ),
					) );
				});

				// For the REST API update for adding new StoryTime Stories.
				add_action( 'rest_api_init', function () {
					register_rest_route( 'wpbooklist/v1', '/storytime', array(
						'methods'  => 'POST',
						'callback' => array( $this, 'wpbooklist_jre_storytime_rest_api_notice' ),
					) );
				});

				// For the REST API for deleting StoryTime Stories.
				add_action( 'rest_api_init', function () {
					register_rest_route( 'wpbooklist/v1', '/storytimedelete', array(
						'methods'  => 'POST',
						'callback' => array( $this, 'wpbooklist_jre_storytime_delete_rest_api_notice' ),
					) );
				});
		}


		/** For pushing a new message to the admin notice area
		 *
		 * @param array $data - The array that contains the info passed to the custom REST endpoint.
		 */
		public function wpbooklist_jre_rest_api_notice( $data ) {
			global $wpdb;
			$table_name  = $wpdb->prefix . 'wpbooklist_jre_user_options';
			$options_row = $wpdb->get_results( "SELECT * FROM $table_name" );
			$newmessage  = $data['notice'];
			$dismiss     = $options_row[0]->admindismiss;

			if ( '0' === $dismiss ) {
				$data         = array(
					'admindismiss' => 1,
					'adminmessage' => $newmessage,
				);
				$format       = array( '%d', '%s' );
				$where        = array( 'ID' => 1 );
				$where_format = array( '%d' );
				$result       = $wpdb->update( $table_name, $data, $where, $format, $where_format );
				$result       = $result . ' - Also Changed admindismiss';
			} else {
				$data         = array(
					'adminmessage' => $newmessage,
				);
				$format       = array( '%s' );
				$where        = array( 'ID' => 1 );
				$where_format = array( '%d' );
				$result       = $wpdb->update( $table_name, $data, $where, $format, $where_format );
				$result       = $result . ' - only updated adminmessage';
			}

			return ( $result );
		}


		/** For adding new StoryTime Stories
		 *
		 * @param array $data - The array that contains the info passed to the custom REST endpoint.
		 */
		public function wpbooklist_jre_storytime_rest_api_notice( $data ) {
			global $wpdb;
			$table_name   = $wpdb->prefix . 'wpbooklist_jre_storytime_stories';
			$responsedata = array();
			$result       = '';

			// Get parameters from POST call coming from PluginManage.
			$data = $data->get_params();

			// Check and see if this content has already been added to this website.
			$duplicate       = false;
			$stories_db_data = $wpdb->get_results( "SELECT * FROM $table_name" );
			foreach ( $stories_db_data as $key => $value ) {
				if ( $data['providername'] === $value->providername && $data['title'] === $value->title ) {
					$duplicate = true;
				}
			}

			if ( false === $duplicate ) {

				// Get the StoryTime settings table and perform actions accordingly.
				$table_name_settings = $wpdb->prefix . 'wpbooklist_jre_storytime_stories_settings';
				$settings_results = $wpdb->get_row( "SELECT * FROM $table_name_settings" );

				// If duplicate data wasn't detected, the user hasn't opted out of receiving Stories.
				if ( null === $settings_results->getstories || 0 === $settings_results->getstories ) {

					// Check and see if I choose to send Stories to ALL users, or just those that are validated Patreon Patrons.
					if ( 'true' === $data['sendtopatreononly'] ) {

						// Get the wpbooklist user options to verify that user has validated their Patreon Status. If validated, finally add to DB.
						$table_name_patreon_settings = $wpdb->prefix . 'wpbooklist_jre_user_options';
						$patreon_settings_results = $wpdb->get_row( "SELECT * FROM $table_name_patreon_settings" );

						if ( null !== $patreon_settings_results->patreonaccess && null !== $patreon_settings_results->patreonrefresh && null !== $patreon_settings_results->patreonack ) {

							// Insert the Story.
							$insert_data = array(
								'content'      => $data['content'],
								'providername' => $data['providername'],
								'providerimg'  => $data['providerimg'],
								'providerbio'  => $data['providerbio'],
								'title'        => $data['title'],
								'category'     => $data['category'],
							);
							$mask_array  = array( '%s', '%s', '%s', '%s', '%s', '%s' );
							$result      = $wpdb->insert( $table_name, $insert_data, $mask_array );

							// Create a page, if user has opted for that.
							if ( 1 === $settings_results->createpage ) {
								require_once CLASS_STORYTIME_DIR . 'class-storytime.php';
								$storytime_class = new WPBookList_Storytime( 'createpage', null, null, $insert_data );
								$page_result     = $storytime_class->create_page_result;

								if ( 0 !== $page_result ) {
									$result = $result . ' - Page Succesfully Created -';
								} else {
									$result = $result . ' - Page Creation Failed -';
								}
							}

							// Create a post, if user has opted for that.
							if ( 1 === $settings_results->createpost ) {
								require_once CLASS_STORYTIME_DIR . 'class-storytime.php';
								$storytime_class = new WPBookList_Storytime( 'createpost', null, null, $insert_data );
								$post_result     = $storytime_class->create_post_result;

								if ( 1 !== $post_result ) {
									$result = $result . ' - Post Succesfully Created -';
								} else {
									$result = $result . ' - Post Creation Failed -';
								}
							}

							// Add the new admin message for this latest Story, if user hasn't disabled it.
							if ( 1 === $settings_results->newnotify ) {

								// Create the new message.
								$new_story_message = $storytime_class->create_admin_message_html( $data );

								// Reset the dismiss flag for this new message.
								$data                = array(
									'notifymessage' => $new_story_message,
									'notifydismiss' => 1,
								);
								$format              = array( '%s', '%d' );
								$where               = array( 'ID' => 1 );
								$where_format        = array( '%d' );
								$admin_notice_result = $wpdb->update( $table_name_settings, $data, $where, $format, $where_format );

								if ( 1 === $admin_notice_result ) {
									$result = $result . ' - Also added new Dashboard Notification Message - ';
								} else {
									$result = $result . ' - Possible problem adding a Dashboard Notification Message - ';
								}
							}
						} else {
							$result = $result . " - User isn't a validated Patreon Patron - ";
						}
					} else {
						// Insert the Story.
						$insert_data = array(
							'content'      => $data['content'],
							'providername' => $data['providername'],
							'providerimg'  => $data['providerimg'],
							'providerbio'  => $data['providerbio'],
							'title'        => $data['title'],
							'category'     => $data['category'],
						);
						$mask_array  = array( '%s', '%s', '%s', '%s', '%s', '%s' );
						$result      = $wpdb->insert( $table_name, $insert_data, $mask_array);

						// Create a page, if user has opted for that.
						if ( 1 === $settings_results->createpage ) {
							require_once CLASS_STORYTIME_DIR . 'class-storytime.php';
							$storytime_class = new WPBookList_Storytime( 'createpage', null, null, $insert_data );
							$page_result     = $storytime_class->create_page_result;

							if ( 0 !== $page_result ) {
								$result = $result . ' - Page Succesfully Created -';
							} else {
								$result = $result . ' - Page Creation Failed -';
							}
						}

						// Create a post, if user has opted for that.
						if ( 1 === $settings_results->createpost ) {
							require_once CLASS_STORYTIME_DIR . 'class-storytime.php';
							$storytime_class = new WPBookList_Storytime( 'createpost', null, null, $insert_data );
							$post_result     = $storytime_class->create_post_result;

							if ( 0 !== $post_result ) {
								$result = $result . ' - Post Succesfully Created -';
							} else {
								$result = $result . ' - Post Creation Failed -';
							}
						}

						// Add the new admin message for this latest Story, if user hasn't disabled it.
						if ( 1 === $settings_results->newnotify ) {

							// Create the new message.
							$new_story_message = $storytime_class->create_admin_message_html( $data );

							// Reset the dismiss flag for this new message.
							$data                = array(
								'notifymessage' => $new_story_message,
								'notifydismiss' => 1,
							);
							$format              = array( '%s', '%d' );
							$where               = array( 'ID' => 1 );
							$where_format        = array( '%d' );
							$admin_notice_result = $wpdb->update( $table_name_settings, $data, $where, $format, $where_format );

							if ( 1 === $admin_notice_result ) {
								$result = $result . ' - Also added new Dashboard Notification Message - ';
							} else {
								$result = $result . ' - Possible problem adding a Dashboard Notification Message - ';
							}
						}
					}
				} else {
					$result = 'Looks Like the user disabled StoryTime!';
				}
			} else {
				$result = 'No Dice - Duplicate content detected!';
			}

			return ( $result );
		}


		/** For deleting StoryTime Stories
		 *
		 * @param array $data - The array that contains the info passed to the custom REST endpoint.
		 */
		public function wpbooklist_jre_storytime_delete_rest_api_notice( $data ) {
			global $wpdb;
			$stories_table    = $wpdb->prefix . 'wpbooklist_jre_storytime_stories';
			$responsedata     = array();
			$providername     = $data['providername'];
			$providertitle    = $data['title'];
			$providercategory = $data['category'];

			// Get the ID of the Story we're deleting.
			$query_for_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $stories_table WHERE providername = %s AND title = %s AND category = %s", $providername, $providertitle, $providercategory ) );

			$result = '';
			if ( is_object( $query_for_data ) ) {
				$id     = $query_for_data->ID;
				$result = 'Found The title in the user\'s database. It\'s ID is ' . $id . '. ';
			} else {
				$result = 'Couldn\'t find the Title in User\'s Database! Check your entries in the TextAreas above. ';
				return $result;
			}

			$result1 = $wpdb->query( $wpdb->prepare( "DELETE FROM $stories_table WHERE providername = %s AND title = %s AND category = %s", $providername, $providertitle, $providercategory ) );

			// Dropping primary key in database to alter the IDs and the AUTO_INCREMENT value.
			$result2 = $wpdb->query( "ALTER TABLE $stories_table MODIFY ID bigint(190)" );
			$result3 = $wpdb->query( "ALTER TABLE $stories_table DROP PRIMARY KEY" );

			// Adjusting ID values of remaining entries in database.
			$my_query    = $wpdb->get_results( "SELECT * FROM $stories_table" );
			$title_count = $wpdb->num_rows;
			$result4     = '';
			for ( $x = $id; $x <= $title_count; $x++ ) {
				$data   = array(
					'ID' => $id,
				);
				$format = array( '%d' );
				$id++;
				$where        = array( 'ID' => $id );
				$where_format = array( '%d' );
				$result4      = $result4 . $wpdb->update( $stories_table, $data, $where, $format, $where_format );
			}

			// Adding primary key back to database.
			$result5 = $wpdb->query( "ALTER TABLE $stories_table ADD PRIMARY KEY (`ID`)" );
			$result6 = $wpdb->query( "ALTER TABLE $stories_table MODIFY ID bigint(190) AUTO_INCREMENT" );

			// Setting the AUTO_INCREMENT value based on number of remaining entries.
			$title_count++;
			$result7 = $wpdb->query( $wpdb->prepare( "ALTER TABLE $stories_table AUTO_INCREMENT = %d", $title_count ) );

			return $result . ' Actual Deletion: ' . $result1 . ' ALTER TABLE: ' . $result2 . ' Drop Primary Key: ' . $result3 . ' ID Adjustments: ' . $result4 . ' Add Primary Key:  ' . $result5 . ' ALTER TABLE: ' . $result6 . ' Adjust Auto_Increment Value: ' . $result7;
		}


		/** For validating patreon status
		 *
		 * @param array $data - The array that contains the info passed to the custom REST endpoint.
		 */
		public function wpbooklist_jre_storytime_patreon_validate_rest_api_notice( $data ) {
			global $wpdb;

			$table_name   = $wpdb->prefix . 'wpbooklist_jre_user_options';
			$data         = array(
				'patreonaccess'  => urldecode( $data['firstkey'] ),
				'patreonrefresh' => urldecode( $data['secondkey'] ),
				'patreonack'     => time(),
			);
			$format       = array( '%s', '%s', '%s' );
			$where        = array( 'ID' => 1 );
			$where_format = array( '%d' );
			$result       = $wpdb->update( $table_name, $data, $where, $format, $where_format );
			$result       = $result . ' - Saved Patreon Access Credentials';

			return ( $result );
		}
	}
endif;
