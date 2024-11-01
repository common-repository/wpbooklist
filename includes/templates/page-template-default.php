<?php
/**
 * Default Page Template File - page-template-default.php.
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes/Templates
 * @version  6.1.5
 */

$string1  = '';
$string2  = '';
$string3  = '';
$string4  = '';
$string5  = '';
$string6  = '';
$string7  = '';
$string8  = '';
$string9  = '';
$string10 = '';
$string11 = '';
$string12 = '';
$string13 = '';
$string14 = '';
$string15 = '';
$string16 = '';
$string17 = '';
$string18 = '';
$string19 = '';
$string20 = '';
$string21 = '';
$string22 = '';
$string23 = '';
$string24 = '';
$string25 = '';
$string26 = '';
$string27 = '';
$string28 = '';
$string29 = '';
$string30 = '';
$string31 = '';
$string32 = '';
$string33 = '';
$string34 = '';
$string35 = '';
$string36 = '';
$string37 = '';
$string38 = '';
$string39 = '';
$string40 = '';
$string41 = '';
$string42 = '';
$string43 = '';
$string44 = '';
$string45 = '';
$string46 = '';
$string47 = '';
$string48 = '';
$string49 = '';
$string50 = '';
$string51 = '';


// Modify the Categories to include string from the 'Genres' as well.
if ( '' !== $book_row->genres && null !== $book_row->genres ) {

	if ( false !== stripos( $book_row->genres, '---' ) ) {
		$book_row->genres = explode( '---', $book_row->genres );

		foreach ( $book_row->genres as $key => $indivgenre ) {
			if ( false === stripos( $indivgenre, $book_row->category ) ) {
				if ( '' !== $indivgenre ) {
					$book_row->category = $book_row->category . ', ' . $indivgenre;
				}
			}
		}
	} else {
		if ( false === stripos( $book_row->genres, $book_row->category ) ) {
			if ( '' !== $book_row->genres ) {
				$book_row->category = $book_row->category . ', ' . $book_row->genres;
			}
		}
	}
}

$string1 = '<div id="wpbl-pagetd-top-container">
	<div id="wpbl-pagetd-left-row">
		<div id="wpbl-pagetd-image">';

if ( null === $options_page_row->hidebookimage || '0' === $options_page_row->hidebookimage ) {
	if ( null === $book_row->image ) {
		$string2 = '<img id="wpbl-pagetd-img" src="' . ROOT_IMG_URL . 'image_unavaliable.png"/>';
	} else {
		$string2 = '<img id="wpbl-pagetd-img" src="' . $book_row->image . '"/>';
	}
}


$string3 = '</div>
		<div id="wpbl-pagetd-details-div">';
if ( ( '1' !== $options_page_row->hiderating ) && ( '0' !== $book_row->rating ) ) {
	switch ( $book_row->rating ) {
		case '5':
			$string4 = '<img style="width: 80px;" src="' . ROOT_IMG_URL . '5star.jpg" />';
			break;
		case '4.5':
			$string4 = '<img style="width: 80px;" src="' . ROOT_IMG_URL . '4halfstar.jpg" />';
			break;
		case '4':
			$string4 = '<img style="width: 80px;" src="' . ROOT_IMG_URL . '4star.jpg" />';
			break;
		case '3.5':
			$string4 = '<img style="width: 80px;" src="' . ROOT_IMG_URL . '3halfstar.jpg" />';
			break;
		case '3':
			$string4 = '<img style="width: 80px;" src="' . ROOT_IMG_URL . '3star.jpg" />';
			break;
		case '2.5':
			$string4 = '<img style="width: 80px;" src="' . ROOT_IMG_URL . '2halfstar.jpg" />';
			break;
		case '2':
			$string4 = '<img style="width: 80px;" src="' . ROOT_IMG_URL . '2star.jpg" />';
			break;
		case '1.5':
			$string4 = '<img style="width: 80px;" src="' . ROOT_IMG_URL . '1halfstar.jpg" />';
			break;
		case '1':
			$string4 = '<img style="width: 80px;" src="' . ROOT_IMG_URL . '1star.jpg" />';
			break;
		default:
			$string4 = '<p style="margin:0px;font-size:10px; font-variant:all-small-caps; margin-left:5px;">' . $trans->trans_448 . '</p>';
			break;
	}
}

if ( '1' !== $options_page_row->hidefacebook || '1' !== $options_page_row->hidetwitter || '1' !== $options_page_row->hidemessenger || '1' !== $options_page_row->hidepinterest || '1' !== $options_page_row->hideemail ) {

	$string5 = '<div><p class="wpbl-pagetd-share-text">' . $trans->trans_447 . '</p>';

	if ( null === $options_page_row->hidefacebook || '0' === $options_page_row->hidefacebook ) {
		$string6 = '<div class="addthis_sharing_toolbox addthis_default_style" style="cursor:pointer"><a style="cursor:pointer;" href="" addthis:title="' . $book_row->title . '" addthis:description="' . htmlspecialchars( addslashes( $book_row->description ) ) . '"addthis:url="' . $book_row->amazon_detail_page . '" class="addthis_button_facebook"></a></div>';
	}

	if ( null === $options_page_row->hidetwitter || '0' === $options_page_row->hidetwitter ) {
		$string7 = '<div class="addthis_sharing_toolbox addthis_default_style" style="cursor:pointer"><a style="cursor:pointer;" href="" addthis:title="' . $book_row->title . '" addthis:description="' . htmlspecialchars( addslashes( $book_row->description ) ) . '"addthis:url="' . $book_row->amazon_detail_page . '" class="addthis_button_twitter"></a></div>';
	}

	if ( null === $options_page_row->hidegoogleplus || '0' === $options_page_row->hidegoogleplus ) {
		$string8 = '<div class="addthis_sharing_toolbox addthis_default_style" style="cursor:pointer"><a style="cursor:pointer;" href="" addthis:title="' . $book_row->title . '" addthis:description="' . htmlspecialchars( addslashes( $book_row->description ) ) . '"addthis:url="' . $book_row->amazon_detail_page . '" class="addthis_button_google_plusone_share"></a></div>';
	}

	if ( null === $options_page_row->hidemessenger || '0' === $options_page_row->hidemessenger ) {
		$string9 = '<div class="addthis_sharing_toolbox addthis_default_style" style="cursor:pointer"><a style="cursor:pointer;" href="" addthis:title="' . $book_row->title . '" addthis:description="' . htmlspecialchars( addslashes( $book_row->description ) ) . '"addthis:url="' . $book_row->amazon_detail_page . '" class="addthis_button_messenger"></a></div>';
	}

	if ( null === $options_page_row->hidepinterest || '0' === $options_page_row->hidepinterest ) {
		$string10 = '<div class="addthis_sharing_toolbox addthis_default_style" style="cursor:pointer"><a style="cursor:pointer;" href="" addthis:title="' . $book_row->title . '" addthis:description="' . htmlspecialchars( addslashes( $book_row->description ) ) . '"addthis:url="' . $book_row->amazon_detail_page . '" class="addthis_button_pinterest_share"></a></div>';
	}

	if ( null === $options_page_row->hideemail || '0' === $options_page_row->hideemail ) {
		$string11 = '<div class="addthis_sharing_toolbox addthis_default_style" style="cursor:pointer"><a style="cursor:pointer;" href="" addthis:title="' . $book_row->title . '" addthis:description="' . htmlspecialchars( addslashes( $book_row->description ) ) . '"addthis:url="' . $book_row->amazon_detail_page . '" class="addthis_button_gmail"></a></div>';
	}

	$string12 = '</div>';
}

if ( '1' !== $options_page_row->hidesimilar && '<span id="wpbooklist-page-span-hidden" style="display:none;"></span>' !== $similar_string ) {
	$string13 = '<div id="wpbl-similar-div"><p style="font-weight:bold; font-size:18px; margin-bottom:5px;" class="wpbl-pagetd-share-text">' . $trans->trans_455 . '</p>' . $similar_string;
}

$kindle_array = array( $book_row->isbn, $options_page_row->amazonaff );
$isbn_test    = preg_match( '/[a-z]/i', $book_row->isbn );
$string14     = '';
if ( ( null === $options_page_row->hidekindleprev || '0' === $options_page_row->hidekindleprev ) && $isbn_test ) {
	if ( has_filter( 'wpbooklist_add_to_page_kindle' ) ) {
		$string14 = apply_filters( 'wpbooklist_add_to_page_kindle', $kindle_array );
	}
}

if ( null === $options_page_row->hidegoogleprev || '0' === $options_page_row->hidegoogleprev ) {
	if ( has_filter( 'wpbooklist_add_to_page_google' ) ) {
		$string14 = $string14 . apply_filters( 'wpbooklist_add_to_page_google', $book_row->isbn );
	}
}

if ( null === $options_page_row->hidequote || '0' === $options_page_row->hidequote ) {
	$string14 = $string14 . '<div id="wpbl-pagetd-page-quote">' . stripslashes( $quote ) . '</div>';
}

$string15 = '</div>
	</div>
	<div id="wpbl-pagetd-right-row">';

if ( null === $options_page_row->hidetitle || '0' === $options_page_row->hidetitle ) {
	$string16 = '<h3 id="wpbl-pagetd-book-title">' . stripslashes( $book_row->title ) . '</h3>';
}

$string17 = '<div id="wpbl-pagetd-book-details-div">';

if ( null === $options_page_row->hideauthor || '0' === $options_page_row->hideauthor ) {
	$string18 = '<div id="wpbl-pagetd-book-details-1">
			<span>' . $trans->trans_14 . ': </span> ' . $book_row->author . '
		</div>';
}

if ( ( null !== $options_page_row->enablepurchase && '0' !== $options_page_row->enablepurchase ) && null !== $book_row->price && null !== $book_row->author_url ) {
	$string19 = '<div id="wpbl-pagetd-book-details-9">
		<span>' . $trans->trans_593 . ': </span>' . $book_row->price;
	$string20 = '</div>';
}

if ( null === $options_page_row->hidepages || '0' === $options_page_row->hidepages ) {
	$string21 = '<div id="wpbl-pagetd-book-details-2">
		<span>' . $trans->trans_142 . ': </span>' . $book_row->pages . '
	</div>';
}

if ( null === $options_page_row->hidecategory || '0' === $options_page_row->hidecategory ) {
	$string22 = '<div id="wpbl-pagetd-book-details-3">
		<span>' . $trans->trans_146 . ': </span>' . $book_row->category . '
	</div>';
}

if ( null === $options_page_row->hidepublisher || '0' === $options_page_row->hidepublisher ) {
	$string23 = '<div id="wpbl-pagetd-book-details-4">
		<span>' . $trans->trans_141 . ': </span>' . $book_row->publisher . '
	</div>';
}

if ( null === $options_page_row->hidesubject || '0' === $options_page_row->hidesubject ) {
	$string50 = '<div id="wpbl-pagetd-book-details-4">
		<span>' . $trans->trans_449 . ':  </span>' . $book_row->subject . '
	</div>';
}

if ( null === $options_page_row->hidecountry || '0' === $options_page_row->hidecountry ) {
	$string51 = '<div id="wpbl-pagetd-book-details-4">
		<span>' . $trans->trans_273 . ':  </span>' . $book_row->country . '
	</div>';
}

if ( null === $options_page_row->hidepubdate || '0' === $options_page_row->hidepubdate ) {
	$string24 = '<div id="wpbl-pagetd-book-details-5">
		<span>' . $trans->trans_143 . ': </span>' . $book_row->pub_year . '
	</div>';
}

if ( null === $options_page_row->hidefinished || '0' === $options_page_row->hidefinished ) {
	if ( false !== $book_row->finished && 'false' !== $book_row->finished && 'N/A' !== $book_row->finished ) {
		$string25 = '<div id="wpbl-pagetd-book-details-6">
			<span>' . $trans->trans_25 . '?</span> ' . $trans->trans_131;

		if ( 'undefined-undefined-' !== $book_row->date_finished ) {
			$string26 = ', ' . __( 'on', 'wpbooklist' ) . $book_row->date_finished;
		}

		$string27 = '</div>';
	} else {
		$string25 = '<div id="wpbl-pagetd-book-details-6">
			<span>' . $trans->trans_25 . '?</span> ' . __( 'No', 'wpbooklist' );
		$string27 = '</div>';
	}
}

if ( null === $options_page_row->hidesigned || '0' === $options_page_row->hidesigned ) {
	$string28 = '<div id="wpbl-pagetd-book-details-7">
		<span>' . $trans->trans_10 . '?</span> ';
	if ( 'false' === $book_row->signed || 'N/A' === $book_row->signed ) {
		$string29 = $trans->trans_132;
	} else {
		$string29 = $trans->trans_131;
	}

	$string30 = '</div>';
}

// If the Custom Fields Extension is active...
$customfields_basic_string = '';
if ( has_filter( 'wpbooklist_append_to_page_view_basic_fields' ) ) {
		$customfields_basic_string = apply_filters( 'wpbooklist_append_to_page_view_basic_fields', $book_row );
}

// If the Custom Fields Extension is active...
$customfields_text_link_string = '';
if ( has_filter( 'wpbooklist_append_to_page_view_text_link_fields' ) ) {
		$customfields_text_link_string = apply_filters( 'wpbooklist_append_to_page_view_text_link_fields', $book_row );
}

// If the Custom Fields Extension is active...
$customfields_dropdown_string = '';
if ( has_filter( 'wpbooklist_append_to_page_view_dropdown_fields' ) ) {
		$customfields_dropdown_string = apply_filters( 'wpbooklist_append_to_page_view_dropdown_fields', $book_row );
}

// If the Custom Fields Extension is active...
$customfields_image_link_string = '';
if ( has_filter( 'wpbooklist_append_to_page_view_image_link_fields' ) ) {
		$customfields_image_link_string = apply_filters( 'wpbooklist_append_to_page_view_image_link_fields', $book_row );
}

// If the Custom Fields Extension is active...
$customfields_paragraph_string = '';
if ( has_filter( 'wpbooklist_append_to_page_view_paragraph_fields' ) ) {
		$customfields_paragraph_string = apply_filters( 'wpbooklist_append_to_page_view_paragraph_fields', $book_row );
}




if ( null === $options_page_row->hidefirstedition || '0' === $options_page_row->hidefirstedition ) {

	$string31 = '<div id="wpbl-pagetd-book-details-8">
	<span>' . $trans->trans_155 . '</span>';
	if ( '' === $book_row->first_edition ) {
		$string32 = $trans->trans_221;
	} else {
		$string32 = $book_row->first_edition;
	}
	$string33 = '</div>';
}

$string34 = '</div>';

if ( ( null !== $options_page_row->enablepurchase && '0' !== $options_page_row->enablepurchase ) && null !== $book_row->price && null !== $book_row->author_url ) {

	if ( has_filter( 'wpbooklist_add_storefront_calltoaction_page' ) ) {
		$string35 = apply_filters( 'wpbooklist_add_storefront_calltoaction_page', $book_row->author_url );
	}
}

if ( ( null === $options_page_row->hidekobopurchase || '0' === $options_page_row->hidekobopurchase && ( null !== $book_row->kobo_link && 'http://store .kobobooks.com/en-ca/Search?Query=' !== $book_row->kobo_link ) ) || ( null === $options_page_row->hidebampurchase || '0' === $options_page_row->hidebampurchase && ( null !== $book_row->bam_link && 'http://www.booksamillion.com/p/' !== $book_row->bam_link ) ) || ( null === $options_page_row->hideamazonpurchase || '0' === $options_page_row->hideamazonpurchase && ( null !== $book_row->amazon_detail_page ) ) || ( null === $options_page_row->hidebnpurchase || '0' === $options_page_row->hidebnpurchase && ( null !== $book_row->isbn ) ) || ( null === $options_page_row->hidegooglepurchase || '0' === $options_page_row->hidegooglepurchase && ( null !== $book_row->google_preview ) ) || ( null === $options_page_row->hideitunespurchase || '0' === $options_page_row->hideitunespurchase && ( null !== $book_row->itunes_page ) ) || ( ( true === $book_row->storefront_active ) && ( null === $options_page_row->hidecolorboxbuyimg || '0' === $options_page_row->hidecolorboxbuyimg ) && ( null !== $book_row->author_url && '' !== $book_row->author_url ) ) ) {


	$string36 = '<div id="wpbl-pagetd-top-purchase-div">
				<h4 id="wpbl-pagetd-purchase-title">' . $trans->trans_454 . ': </h4>
				<div id="wpbl-pagetd-line-under-purchase"></div>';

	if ( ( null !== $book_row->amazon_detail_page ) && ( null === $options_page_row->hideamazonpurchase || '0' === $options_page_row->hideamazonpurchase ) ) {
		if ( preg_match( '/[a-z]/i', $book_row->isbn ) ) {
			$string37 = '<a class="wpbl-pagetd-wpbooklist-purchase-img" href="' . $book_row->amazon_detail_page . '" target="_blank"><img src="' . ROOT_IMG_URL . 'kindle.png" /></a>';
		} else {
			$string37 = '<a class="wpbl-pagetd-wpbooklist-purchase-img" href="' . $book_row->amazon_detail_page . '" target="_blank"><img src="' . ROOT_IMG_URL . 'amazon.png" /></a>';
		}
	}

	if ( null === $book_row->bn_link || '' === $book_row->bn_link ) {
		$book_row->bn_link = 'http://www.barnesandnoble.com/s/' . $book_row->isbn;
	}

	if ( ( null !== $book_row->isbn ) && ( null === $options_page_row->hidebnpurchase || '0' === $options_page_row->hidebnpurchase ) ) {
		$string38 = '<a class="wpbl-pagetd-wpbooklist-purchase-img" href="' . $book_row->bn_link . '" target="_blank"><img src="' . ROOT_IMG_URL . 'bn.png" /></a>';
	}

	if ( ( null !== $book_row->google_preview ) && ( null === $options_page_row->hidegooglepurchase || '0' === $options_page_row->hidegooglepurchase ) ) {
		$string39 = '<a class="wpbl-pagetd-wpbooklist-purchase-img" href="' . $book_row->google_preview . '" target="_blank"><img src="' . ROOT_IMG_URL . 'googlebooks.png" /></a>';
	}

	if ( ( null !== $book_row->itunes_page ) && ( null === $options_page_row->hideitunespurchase || '0' === $options_page_row->hideitunespurchase ) ) {
		$string40 = '<a class="wpbl-pagetd-wpbooklist-purchase-img" href="' . $book_row->itunes_page . '" target="_blank"><img id="wpbl-pagetd-itunes-img" src="' . ROOT_IMG_URL . 'ibooks.png" /></a>';
	}


	if ( ( null !== $book_row->bam_link ) && ( null === $options_page_row->hidebampurchase || '0' === $options_page_row->hidebampurchase ) ) {
		$string48 = '<a class="wpbl-pagetd-wpbooklist-purchase-img" href="' . $book_row->bam_link . '" target="_blank"><img src="' . ROOT_IMG_URL . 'bam-icon.jpg" /></a>';
	}

	if ( ( null !== $book_row->kobo_link ) && ( null === $options_page_row->hidekobopurchase || '0' === $options_page_row->hidekobopurchase ) ) {
		$string49 = '<a class="wpbl-pagetd-wpbooklist-purchase-img" href="' . $book_row->kobo_link . '" target="_blank"><img id="wpbl-pagetd-itunes-img" src="' . ROOT_IMG_URL . 'kobo-icon.png" /></a>';
	}

	if ( ( null !== $options_page_row->enablepurchase && 0 !== $options_page_row->enablepurchase ) && ( null !== $book_row->author_url ) ) {
		if ( has_filter( 'wpbooklist_add_storefront_bookimg_page' ) ) {
			$string41 = apply_filters( 'wpbooklist_add_storefront_bookimg_page', $book_row->author_url );
		}
	}

	$string42 = '</div>';

}

if ( ( null === $options_page_row->hidedescription || '0' === $options_page_row->hidedescription ) && null !== $book_row->description ) {
	$string43 = '<div id="wpbl-pagetd-book-description-div">
		<h5 id="wpbl-pagetd-book-description-h5">' . $trans->trans_457 . '</h5>
		<div id="wpbl-pagetd-book-description-contents">' . html_entity_decode( stripslashes( $book_row->description ) ) . '</div>
	</div>';
}


// Building out the Additional Images section.
$additional_images = '';

if ( '1' !== $options_page_row->hideadditionalimgs && ( ( null !== $book_row->backcover && '' !== $book_row->backcover ) || ( null !== $book_row->additionalimage1 && '' !== $book_row->additionalimage1 ) || ( null !== $book_row->additionalimage2 && '' !== $book_row->additionalimage2 ) ) ) {

	$additional_images = '<div id="wpbl-pagetd-book-description-div">
		<h5 id="wpbl-pagetd-book-description-h5">' . $trans->trans_584 . '</h5><div class="wpbooklist_desc_p_class"  id="wpbooklist-additional-images-id">';

	$img_array = array(
		$book_row->backcover,
		$book_row->additionalimage1,
		$book_row->additionalimage2,
	);

	foreach ( $img_array as $key => $img ) {
		if ( '' !== $img && null !== $img ) {
			$additional_images = $additional_images . '<img style="max-width:100px;" class="wpbooklist-additional-img-colorbox" src="' . $img . '"  />';
		}
	}

	$additional_images = $additional_images . '</div>';

}



if ( ( null === $options_page_row->hidenotes || '0' === $options_page_row->hidenotes ) && null !== $book_row->notes ) {

	$string44 = '
	<div id="wpbl-pagetd-book-notes-div">
		<h5 id="wpbl-pagetd-book-notes-h5">' . $trans->trans_153 . '</h5>
		<div id="wpbl-pagetd-book-notes-contents">' . html_entity_decode( stripslashes( $book_row->notes ) ) . '</div>
	</div>';
}

// If the Comments Extension is active...
$comments_array  = array( $book_row->ID, $table_name, $book_row->book_uid, $book_row->title );
$comments_string = '';
if ( has_filter( 'wpbooklist_append_to_colorbox_comments' ) ) {
		$comments_string = '<div id="wpbl-pagetd-book-comments-div">
		<h5 id="wpbl-pagetd-book-description-h5">' . $trans->trans_594 . '</h5>
		<div id="wpbl-pagetd-book-comments-contents">' . apply_filters( 'wpbooklist_append_to_colorbox_comments', $comments_array ) . '</div></div>';

	// Hide the inner title.
	if ( false !== stripos( $comments_string, 'id="wpbooklist_desc_id"' ) ) {
		$comments_string = str_replace( 'id="wpbooklist_desc_id"', 'id="wpbooklist_desc_id" style="display:none;"', $comments_string );
	}

	// Replace some elements with an H5.
	if ( false !== stripos( $comments_string, '<p class="wpbooklist-comments-add-comment-title"' ) ) {

		$comments_string = str_replace( '<p class="wpbooklist-comments-add-comment-title"', '<h5 class="wpbooklist-comments-add-comment-title"', $comments_string );

		$comments_string = str_replace( '</p><span class="wpbooklist-comments-for-php-string-mod" style="display:none"></span>', '</h5>', $comments_string );
	}

	// Replace some elements with an H5.
	if ( false !== stripos( $comments_string, '<p class="wpbooklist-comments-add-comment-rating-title"' ) ) {

		$comments_string = str_replace( '<p class="wpbooklist-comments-add-comment-rating-title"', '<h5 class="wpbooklist-comments-add-comment-rating-title"', $comments_string );

		$comments_string = str_replace( '</p><span class="wpbooklist-comments-for-php-string-mod" style="display:none"></span>', '</h5>', $comments_string );
	}
}

if ( ( null === $options_page_row->hideamazonreview || '0' === $options_page_row->hideamazonreview ) && null !== $book_row->review_iframe ) {
		$string45 = '<div id="wpbl-pagetd-book-amazon-review-div">
			<h5 id="wpbl-pagetd-book-amazon-review-h5">' . $trans->trans_266 . '</h5>
			<iframe id="wpbl-pagetd-book-amazon-review-contents" src="' . $book_row->review_iframe . '"></iframe>
		</div>';
}

$append_string = '';
if ( has_filter( 'wpbooklist_append_to_default_page_template_right_column' ) ) {
	$append_string = apply_filters( 'wpbooklist_append_to_default_page_template_right_column', $append_string );
}
$string46 = $append_string;
$string47 = '</div>
</div>';
