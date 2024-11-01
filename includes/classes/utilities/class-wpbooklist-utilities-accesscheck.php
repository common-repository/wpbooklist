<?php
/**
 * Class WPBookList_Utilities_Date - class-wpbooklist-utilities-accesscheck.php
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes/Utilities
 * @version  6.1.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Utilities_Accesscheck', false ) ) :
	/**
	 * WPBookList_Utilities_Date class. Here we'll house everything to do with getting the current accesscheck.
	 */
	class WPBookList_Utilities_Accesscheck {

		/** Common member variable
		 *
		 *  @var array $user
		 */
		public $user = array();

		/**
		 * The users ID we're checking access on.
		 *
		 * @param int $wpuserid - The users ID we're checking access on.
		 */
		public function wpbooklist_accesscheck( $wpuserid, $request ) {

			global $wpdb;

			// Get all saved Users from the WPBookList Users table.
			$users_table_name = $wpdb->prefix . 'wpbooklist_jre_users_table';

			// Make call to Transients class to see if Transient currently exists. If so, retrieve it, if not, make call to create_transient() with all required Parameters.
			require_once CLASS_TRANSIENTS_DIR . 'class-wpbooklist-transients.php';
			$transients       = new WPBookList_Transients();
			$transient_name   = 'wpbl_' . md5( 'SELECT * FROM ' . $users_table_name . ' WHERE wpuserid == ' . $wpuserid );
			$transient_exists = $transients->existing_transient_check( $transient_name );
			if ( $transient_exists ) {
				$this->user = $transient_exists;
			} else {
				$query      = 'SELECT * FROM ' . $users_table_name . '  WHERE wpuserid = ' . $wpuserid;
				$this->user = $transients->create_transient( $transient_name, 'wpdb->get_row', $query, MONTH_IN_SECONDS );
			}

			// If we've retreived a user, continue on to permission check, otherwise return false.
			if ( null !== $this->user ) {

				// Get user's specific permissions.
				$perms = $this->user->permissions;
				$perms = explode( '-', $perms );

				$return_val = false;

				// Now check permissions.
				switch ( $request ) {
					case 'addbook':
						if ( 'Yes' === $perms[0] ) {
							$return_val = true;
						}
						break;
					case 'displayoptions':
						if ( 'Yes' === $perms[3] ) {
							$return_val = true;
						}
						break;
					case 'settings':
						if ( 'Yes' === $perms[4] ) {
							$return_val = true;
						}
						break;
					case 'editdelete':
						if ( 'Yes' === $perms[1] && 'Yes' === $perms[2] || ( 'No' === $perms[1] && 'Yes' === $perms[2] ) || ( 'Yes' === $perms[1] && 'No' === $perms[2] ) ) {
							$return_val = true;
						}
						break;
					case 'deleteonly':
						if ( 'Yes' === $perms[2] ) {
							$return_val = true;
						}
						break;
					case 'editonly':
						if ( 'Yes' === $perms[1] ) {
							$return_val = true;
						}
						break;
					case 'createuser':
						if ( 'SuperAdmin' === $this->user->role ) {
							$return_val = true;
						}
						break;
					default:
						# code...
						break;
				}

				return $return_val;

			} else {

				// No registered WPBookList user was found - return false.
				return false;
			}
		}

		/**
		 * Create the 'No Access' message.
		 */
		public function wpbooklist_accesscheck_no_permission_message() {

			// Grab Superadmin from the settings table to the user knows who to contact.
			global $wpdb;

			// First we'll get all the translations for this tab.
			require_once CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
			$this->trans = new WPBookList_Translations();
			$this->trans->trans_strings();

			// Make call to Transients class to see if Transient currently exists. If so, retrieve it, if not, make call to create_transient() with all required Parameters.
			require_once CLASS_TRANSIENTS_DIR . 'class-wpbooklist-transients.php';
			$transients          = new WPBookList_Transients();
			$settings_table_name = $wpdb->prefix . 'wpbooklist_jre_users_table';
			$transient_name      = 'wpbl_' . md5( 'SELECT * FROM ' . $settings_table_name . " WHERE role = 'SuperAdmin'" );
			$transient_exists    = $transients->existing_transient_check( $transient_name );
			if ( $transient_exists ) {
				$this->wpbl_super_admin = $transient_exists;
			} else {
				$query                  = 'SELECT * FROM ' . $settings_table_name . " WHERE role = 'SuperAdmin'";
				$this->wpbl_super_admin = $transients->create_transient( $transient_name, 'wpdb->get_row', $query, MONTH_IN_SECONDS );
			}

			if ( null !== $this->wpbl_super_admin && 'undefined' !== $this->wpbl_super_admin ) {
				$sauser = $this->wpbl_super_admin;

				// If SuperAdmin's First and last name have been set.
				if ( '' !== $sauser->firstname && null !== $sauser->firstname && '' !== $sauser->lastname && null !== $sauser->lastname ) {

					return '<div class="wpbooklist-no-saved-data-stats-div">
						<p class="wpbooklist-tab-intro-para">
							<img id="wpbooklist-smile-icon-3" src="' . ROOT_IMG_ICONS_URL . 'shocked.svg">
							<span class="wpbooklist-no-saved-span-stats-1">' . $this->trans->trans_90 . '</span>
							<br>
							' . $this->trans->trans_490 . '
							<br>
							' . $this->trans->trans_491 . ' ' . $sauser->firstname . ' ' . $sauser->lastname . ' ' . $this->trans->trans_492 . ' ' . $sauser->email . ' ' . $this->trans->trans_493 . '
							<br><br>
						</p>
					</div>';
				}

				// If SuperAdmin's First name has been set.
				if ( '' !== $sauser->firstname && null !== $sauser->firstname && ( '' === $sauser->lastname || null === $sauser->lastname ) ) {

					return '<div class="wpbooklist-no-saved-data-stats-div">
						<p class="wpbooklist-tab-intro-para">
							<img id="wpbooklist-smile-icon-3" src="' . ROOT_IMG_ICONS_URL . 'shocked.svg">
							<span class="wpbooklist-no-saved-span-stats-1">' . $this->trans->trans_90 . '</span>
							<br>
							' . $this->trans->trans_490 . '
							<br>
							' . $this->trans->trans_491 . ' ' . $sauser->firstname . ' ' . $this->trans->trans_492 . ' <a href="mailto:' . $sauser->email . '"> ' .  $sauser->email . '</a> ' . $this->trans->trans_493 . '
							<br><br>
						</p>
					</div>';
				}

				// If SuperAdmin's Last name has been set.
					if ( ( '' === $sauser->firstname || null === $sauser->firstname ) && ( '' !== $sauser->lastname && null !== $sauser->lastname ) ) {

					return '<div class="wpbooklist-no-saved-data-stats-div">
						<p class="wpbooklist-tab-intro-para">
							<img id="wpbooklist-smile-icon-3" src="' . ROOT_IMG_ICONS_URL . 'shocked.svg">
							<span class="wpbooklist-no-saved-span-stats-1">' . $this->trans->trans_90 . '</span>
							<br>
							' . $this->trans->trans_490 . '
							<br>
							' . $this->trans->trans_491 . ' ' . $sauser->lastname . ' ' . $this->trans->trans_492 . ' <a href="mailto:' . $sauser->email . '"> ' .  $sauser->email . '</a> ' . $this->trans->trans_493 . '
							<br><br>
						</p>
					</div>';
				}

				// If neither of SuperAdmin's names have been set.
				if ( ( '' === $sauser->firstname || null === $sauser->firstname ) && ( '' === $sauser->lastname || null === $sauser->lastname ) ) {

					return '<div class="wpbooklist-no-saved-data-stats-div">
						<p class="wpbooklist-tab-intro-para">
							<img id="wpbooklist-smile-icon-3" src="' . ROOT_IMG_ICONS_URL . 'shocked.svg">
							<span class="wpbooklist-no-saved-span-stats-1">' . $this->trans->trans_90 . '</span>
							<br>
							' . $this->trans->trans_490 . '
							<br>
							' . $this->trans->trans_491 . ' <a href="mailto:' . $sauser->email . '"> ' .  $sauser->email . '</a> ' . $this->trans->trans_493 . '
							<br><br>
						</p>
					</div>';
				}
			} else {
				return '<div class="wpbooklist-no-saved-data-stats-div">
						<p class="wpbooklist-tab-intro-para">
							<img id="wpbooklist-smile-icon-3" src="' . ROOT_IMG_ICONS_URL . 'shocked.svg">
							<span class="wpbooklist-no-saved-span-stats-1">' . $this->trans->trans_90 . '</span>
							<br>
							' . $this->trans->trans_490 . '
						</p>
					</div>';
			}
		}

		/**
		 * Creates custom WPBookList WordPress roles
		 *
		 * @param string $role_name - The name of the role we're wanting to create.
		 */
		public function wpbooklist_accesscheck_create_role( $role_name ) {

			// Require the translations file.
			require_once CLASS_TRANSLATIONS_DIR . 'class-wpbooklist-translations.php';
			$this->translations = new WPBookList_Translations();
			$this->translations->trans_strings();

			$role_caps    = array();
			$display_name = '';

			switch ( $role_name ) {
				case $this->translations->trans_489:
					// Basic WPBookList User.
					$role_caps = array(
						'read'                   => true,
						'edit_posts'             => true, // Required for dashboard access - can't really modify anything still though.
						'delete_posts'           => false,
						'edit_others_posts'      => false,
						'edit_published_posts'   => false,
						'publish_posts'          => false,
						'delete_others_posts'    => false,
						'delete_published_posts' => false,
						'delete_private_posts'   => false,
						'edit_private_posts'     => false,
						'read_private_posts'     => false,
						'edit_pages'             => false,
						'delete_pages'           => false,
						'edit_others_pages'      => false,
						'edit_published_pages'   => false,
						'publish_pages'          => false,
						'delete_others_pages'    => false,
						'delete_published_pages' => false,
						'delete_private_pages'   => false,
						'edit_private_pages'     => false,
						'read_private_pages'     => false,
						'moderate_comments'      => false,

					);

					$role_name    = 'wpbooklist_basic_user';
					$display_name = $this->translations->trans_489;

					break;
				default:
					break;
			}

			// Create the wpbooklist_basic_user role.
			$result = add_role( $role_name, $display_name, $role_caps );

			// Now get each role we have in WordPress and add our custom 'wpbooklist_dashboard_access' capability to ensure that each user has access to the WPBookList menu pages.
			global $wp_roles;
			$roles = $wp_roles->get_names();
			foreach ( $roles as $key => $role ) {
				$role       = strtolower( $role );
				$role       = str_replace( ' ', '_', $role );
				$indiv_role = get_role( $role );
				if ( null !== $indiv_role ) {
					$indiv_role->add_cap( 'wpbooklist_dashboard_access' );
				}
			}
		}
	}

endif;
