<?php
/**
 * Class WPBookList_Utilities_Date - class-wpbooklist-utilities-date.php
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Exercise
 * @version  0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPBookList_Utilities_Date', false ) ) :
	/**
	 * WPBookList_Utilities_Date class. Here we'll house everything to do with getting the current date.
	 */
	class WPBookList_Utilities_Date {

		/** Common member variable
		 *
		 *  @var string $human_date
		 */
		public $returndate = '';

		/**
		 * Returns the current date using the WordPress 'current_time()' function and accepts a time format
		 *
		 * @param string $format - The format for the date.
		 */
		public function wpbooklist_get_date_via_current_time( $format ) {

			if ( 'mysql' === $format ) {
				$blogtime = current_time( $format );
				list( $today_year, $today_month, $today_day, $hour, $minute, $second ) = preg_split( '([^0-9])', $blogtime );
				$this->return_date = $today_month . '-' . $today_day . '-' . $today_year;
				return $this->return_date;
			}

			if ( 'timestamp' === $format ) {
				$this->return_timestamp = current_time( $format );
				return $this->return_timestamp;
			}

		}

	}

endif;
