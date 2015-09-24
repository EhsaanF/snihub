<?php

/**
 * Theme options
 */
global $snihub_options;
$snihub_options = array(
	'new_snippet_page' 			=>	4, // [submit-code]
	'my_snippets_page' 			=>	6, // [my-codes]
	'profile_page' 				=>	8 // [user-profile]
);

/**
 * After setup function
 *
 * @return 				void
 */
function snihub_theme_setup() {
	// WordPress 4.3 title tag
	add_theme_support( 'title-tag' );
}
add_action( 'after_setup_theme', 'snihub_theme_setup' );

/**
 * Theme switching for registering post types and taxonomies
 * then flush the rewrites
 *
 * @return 				void
 */
function snihub_theme_switch() {
	snippet_post_type();
	snippet_taxonomies();

	flush_rewrite_rules();
}

/**
 * Snippets post type
 *
 * @return 				void
 */
function snippet_post_type() {
	$labels = array(
		'name' 					=>	'کدها',
		'singular_name'			=>	'کد',
		'menu_name' 			=>	'تکه‌کدها',
		'name_admin_bar'		=>	'کدها',
		'all_items' 			=>	'همه‌ی کدها',
		'add_new' 				=>	'کد جدید',
		'add_new_item' 			=>	'کد جدید',
		'edit_item' 			=>	'ویرایش کد',
		'new_item' 				=>	'کد جدید',
		'view_item' 			=>	'دیدن کد',
		'search_items' 			=>	'جستجوی کدها',
		'not_found' 			=>	'کدی یافت نشد!',
		'not_found_in_trash' 	=>	'کدی در بازیافت پیدا نشد!',
		'parent_item_colon' 	=>	''
	);

	$args = array(
		'labels' 				=>	apply_filters( 'snippet_labels', $labels ),
		'public' 				=>	true,
		'menu_position' 		=>	5.19991217,
		'menu_icon' 			=>	'dashicons-editor-code',
		'supports' 				=>	array( 'title', 'editor' )
	);

	register_post_type( 'snippet', $args );
}
add_action( 'init', 'snippet_post_type' );

/**
 * Snippet taxonomies
 *
 * @return 					void
 */
function snippet_taxonomies() {
	$labels = array(
		'name' 					=>	'زبان‌',
		'singular_name' 		=>	'زبان',
		'menu_name' 			=>	'زبان‌ تکه‌ها',
		'all_items' 			=>	'همه‌ی زبان‌ها',
		'edit_item' 			=>	'ویرایش زبان',
		'view_item' 			=>	'دیدن زبان',
		'update_item' 			=>	'به‌روزرسانی زبان',
		'add_new_item' 			=>	'افزودن زبان جدید',
		'new_item_name' 		=>	'افزودن زبان',
		'parent_item' 			=>	'زبان مادر',
		'parent_item_colon' 	=>	'زبان مادر:',
		'search_items' 			=>	'جستجوی زبان‌ها',
		'popular_items' 		=>	'زبان‌های پرطرفدار',
		'not_found' 			=>	'چیزی یافت نشد!'
	);

	$args = array(
		'labels' 				=>	apply_filters( 'language_labels', $labels ),
		'public' 				=>	true,
		'show_in_menu'			=>	'edit.php?post_type=snippet',
		'show_admin_column'		=> 	true,
		'hierarchical'			=>	true
	);
	register_taxonomy( 'language', 'snippet', $args );

	$args = array(
		'label'					=>	'برچسب‌ها',
		'public' 				=>	true,
		'show_in_menu'			=>	'edit.php?post_type=snippet',
		'show_admin_column'		=>	true,
		'hierarchical'			=>	false
	);
	register_taxonomy( 'snippet_tags', 'snippet', $args );
}
add_action( 'init', 'snippet_taxonomies' );

/**
 * Add rewrite tag for hot snippets
 *
 * @return 				void
 */
function snihub_rewrite_tags() {
	global $wp;
	$wp->add_query_var( 'hot_snippets' );
	$wp->add_query_var( 'random' );
	$wp->add_query_var( 'snippets_by' );

	add_rewrite_rule( 'hot/?$', 'index.php?hot_snippets=true', 'top' );
	add_rewrite_rule( 'random/?$', 'index.php?random=true', 'top' );
	add_rewrite_rule( 'snippets_by/([^/]*)/?$', 'index.php?snippets_by=$matches[1]', 'top' );

	if ( ! is_admin() )
		show_admin_bar( false );
}
add_action( 'init', 'snihub_rewrite_tags' );

/**
 * Pre get posts for customizing the post type
 *
 * @param 					WP_Query $query
 * @return 					void
 */
function snihub_pre_get_posts( $query ) {
	if ( $query->is_home() && $query->is_main_query() ) {
		$query->set( 'post_type', 'snippet' );
		$query->set( 'meta_query', array( array( 'key' => 'no_index', 'compare' => 'NOT EXISTS' ) ) );
		if ( get_query_var( 'hot_snippets', false ) ) {
			$query->set( 'order', 'DESC' );
			$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'meta_key', 'likes' );
		}

		if ( get_query_var( 'snippets_by', false ) ) {
			$query->set( 'author', get_query_var( 'snippets_by', '0' ) );
		}
	}
}
add_action( 'pre_get_posts', 'snihub_pre_get_posts' );

/**
 * Handler for random posts
 *
 * @return 						void
 */
function snihub_random_post() {
	if ( get_query_var( 'random' ) == 'true' ) {
		$posts = get_posts( 'post_type=snippet&orderby=rand&numberposts=1' );
		foreach( $posts as $post ) {
			$link = get_permalink( $post );
		}
		wp_redirect( $link, 307 );
		exit;
	}
}
add_action( 'template_redirect', 'snihub_random_post', 999 );

/**
 * Snihub category data for post
 *
 * @param 				int $post_id
 * @return 				array
 */
function snihub_cat_data( $post_id ) {
	$category = wp_get_object_terms( $post_id, 'language' );
	$category = $category[0];

	$output = array();
	$output['link'] = get_term_link( $category->term_id, 'language' );
	$output['title'] = $category->name;

	return $output;
}

/**
 * Get snippet language for pretty printer
 *
 * @param 				int $post_id
 * @return 				string
 */
function snihub_snippet_language( $post_id ) {
	$category = wp_get_object_terms( $post_id, 'language' );
	$category = $category[0];

	return $category->slug;
}

/**
 * Snihub tags
 *
 * @param 				int $post_id
 * @return 				array
 */
function snihub_tags( $post_id ) {
	$tags = wp_get_object_terms( $post_id, 'snippet_tags' );
	$output = array();

	if ( ! $tags )
		return array();

	foreach( $tags as $tag ) {
		$output[] = array( 'name' => $tag->name, 'link' => get_term_link( $tag->term_id, 'snippet_tags' ) );
	}

	return $output;
}

/**
 * Snihub tags comma
 *
 * @param 					int $post_id
 * @return 					string
 */
function snihub_tags_comma( $post_id ) {
	$tags = snihub_tags( $post_id );
	$tags_name = array();
	foreach( $tags as $tag )
		$tags_name[] = $tag['name'];

	return implode( ',' , $tags_name );
}

/**
 * Snihub checks an snippet is private or not.
 *
 * @param 				int $post_id
 * @return 				bool
 */
function snihub_is_private( $post_id ) {
	$no_index = get_post_meta( $post_id, 'no_index', true );

	return $no_index != null;
}

/**
 * WP Parsi login logo
 *
 * @return 				void
 */
function wp_parsi_login_logo() { ?>
    <style type="text/css">

@font-face {
	font-family: "Yekan Web";
	src: url( '<?php echo get_stylesheet_directory_uri(); ?>/fonts/yekan-regular.woff' ) format( 'woff' );
	font-weight: normal;
}
@font-face {
	font-family: 'Roboto';
	font-style: normal;
	font-weight: 300;
	src: local('Roboto Light'), local('Roboto-Light'), url('<?php echo get_stylesheet_directory_uri(); ?>/fonts/Roboto-Light.woff') format('woff');
}
@font-face {
	font-family: 'Roboto';
	font-style: normal;
	font-weight: normal;
	src: local('Roboto Regular'), local('Roboto-Regular'), url('<?php echo get_stylesheet_directory_uri(); ?>/fonts/Roboto-Regular.woff') format('woff');
}
@font-face {
	font-family: 'Roboto';
	font-style: normal;
	font-weight: bold;
		src: local('Roboto Bold'), local('Roboto-Bold'), url('<?php echo get_stylesheet_directory_uri(); ?>/fonts/Roboto-Bold.woff') format('woff');
}
body {
	font-family: 'Yekan Web', tahoma, sans-serif !important;
}
        .login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/icon.svg);
        }
        #login { padding-top: 5% !important; }
        input[type=text], input[type=password], input[type=email] { direction: ltr !important; text-align: left !important; font-family: 'Roboto', arial,tahoma,sans-serif !important; }
        input[type=checkbox]:checked:before {

}
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'wp_parsi_login_logo' );

/**
 * Like action
 *
 * @return 					void
 */
function snihub_like_snippet() {
	if ( isset( $_POST['snihub-action'] ) && $_POST['snihub-action'] == 'like-code' ) {
		$id = (int) $_POST['post-id'];

		if ( ! is_user_logged_in() ) {
			echo json_encode( array( 'success' => false, 'loggedin' => false ) );
			exit;
		}

		$details = get_post( $id );
		if ( $details->post_author == get_current_user_id() ) {
			echo json_encode( array( 'success' => false, 'yourlike' => true ) );
			exit;
		}

		$liked_posts = get_user_meta( get_current_user_id(), 'liked', true );
		if ( in_array( $id, $liked_posts ) ) {
			$new_count = get_post_meta( $id, 'likes', true );
			$new_count--;
			update_post_meta( $id, 'likes', $new_count );

			$liked_index = array_search( $id, $liked_posts );
			unset( $liked_posts[$liked_index] );
			update_user_meta( get_current_user_id(), 'liked', $liked_posts );

			echo json_encode( array( 'success' => true, 'disliked' => true, 'new_count' => $new_count ) );
			exit;
		}

		$new_count = get_post_meta( $id, 'likes', true );
		$new_count++;
		update_post_meta( $id, 'likes', $new_count );

		$liked_posts[] = $id;
		update_user_meta( get_current_user_id(), 'liked', $liked_posts );

		echo json_encode( array( 'success' => true, 'new_count' => $new_count, 'disliked' => false ) );
		exit;
	}
}
add_action( 'init', 'snihub_like_snippet' );

/**
 * Redirect normal users to home
 *
 * @return 				string
 */ 
function snihub_login_redirect( $redirect_to, $request, $user ) {
	global $user;

	if ( isset( $user->roles ) && is_array( $user->roles ) ) {
		if ( in_array( 'administrator', $user->roles ) )
			return $redirect_to;
		else
			return home_url();
	}

	return $redirect_to;
}

/**
 * Block dashboard for those can't manage options
 *
 * @return 				void
 */
function snihub_block_dashboard() {
	if ( ! current_user_can( 'manage_options' ) && $_SERVER['DOING_AJAX'] != '/wp-admin/admin-ajax.php' ) {
		wp_redirect( home_url() );
		exit;
	}
}
add_action( 'admin_init', 'snihub_block_dashboard' );

/**
 * Bootstrap pagination support
 *
 * @return 				void
 */
function snihub_pagination() {
	global $wp_query;
	$total_pages = $wp_query->max_num_pages;

	if ( $total_pages <= 1 )
		return; // No need to pagination

	$first_page = 1;
	$last_page = $total_pages;

	echo '<nav style="text-align: center;"><ul class="pagination">';
	$current_page = max( 1, get_query_var( 'paged' ) );

	$class = ( $current_page == $first_page ) ? ' class="disabled"' : '';
	$previous_page = ( $current_page == $first_page ) ? 1 : $current_page - 1;
	echo '<li' . $class . '><a href="' . home_url() . '/page/' . $previous_page . '/">&larr;</a></li>';

	while( $count < $total_pages ) {
		$count++;

		$class = ( $count == $current_page ) ? ' class="active"' : '';
		echo '<li' . $class . '><a href="' . home_url() . '/page/' . $count . '/">' . $count . '</a></li>';
	}

	$class = ( $current_page == $last_page ) ? ' class="disabled"' : '';
	$next_page = ( $current_page == $last_page ) ? $last_page : $current_page + 1;
	echo '<li' . $class . '><a href="' . home_url() . '/page/' . $next_page . '/">&rarr;</a></li>';
}

/**
 * Report action
 *
 * @return 				void
 */
function snihub_report_action() {
	if ( isset( $_POST['snihub-action'] ) && $_POST['snihub-action'] == 'report' ) {
		$post_id = (int) $_POST['post_id'];
		$reason = $_POST['reason'];
		$other = $_POST['other_desc'];

		wp_mail(
			get_bloginfo( 'admin_email' ),
			'گزارش تکه #' . $post_id,
			"سلام، کاربر " . $_SERVER['REMOTE_ADDR'] . " با ایجنت " . $_SERVER['HTTP_USER_AGENT'] . " اسنیپت شماره $post_id را گزارش داده است.\r\nدلیل: $reason\r\nتوضیحات اضافه: $other"
		);

		echo json_encode( array( 'success' => true ) );
		exit;
	}
}
add_action( 'init', 'snihub_report_action' );

/**
 * Add meta boxes for snippet section.
 *
 * @return 				void
 */
function snihub_meta_boxes() {
	add_meta_box(
		'snippet_details',
		'جزییات ‌تکه‌کد',
		'snihub_details_meta_box',
		'snippet',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'snihub_meta_boxes' );

/**
 * Details meta box content
 *
 * @return 				void
 */
function snihub_details_meta_box( $post ) {
	wp_nonce_field( 'snihub_details_data', 'snihub_nonce' );

	$likes = get_post_meta( $post->ID, 'likes', true );
	$code = get_post_meta( $post->ID, 'code', true );
	$no_index = get_post_meta( $post->ID, 'no_index', true );

	$likes = ( $likes ? $likes : 0 );
	$code = ( $code ? $code : '' );
	$no_index = ( $no_index ? ' checked' : '' );

	?>
	<p><label for="snihub-likes">پسندها:</label> <input type="number" name="snihub[likes]" id="snihub-likes" value="<?php echo $likes; ?>"></p>
	<p><label for="snihub-no-index"><input type="checkbox" name="snihub[noindex]" id="snihub-no-index"<?php echo $no_index; ?>> عدم نمایش در ایندکس</label></p>
	<p><label for="snihub-code">کد:</label><br>
	<textarea name="snihub[code]" id="snihub-code" style="width: 100%;direction: ltr;text-align: left;font-family: monospace" rows="10"><?php echo $code; ?></textarea></p>
	<script>
	var myInput = document.getElementById("snihub-code");
    if(myInput.addEventListener ) {
        myInput.addEventListener('keydown',keyHandler,false);
    } else if(myInput.attachEvent ) {
        myInput.attachEvent('onkeydown',keyHandler); /* damn IE hack */
    }

    function keyHandler(e) {
        var TABKEY = 9;
        if(e.keyCode == TABKEY) {
            this.value += "\t";
            if(e.preventDefault) {
                e.preventDefault();
            }
            return false;
        }
    }
	</script>
	<?php
}

/**
 * Save meta box data
 *
 * @return 				void
 */
function snihub_save_meta_box( $post_id ) {
	if ( ! isset( $_POST['snihub_nonce'] ) )
		return;

	if ( ! wp_verify_nonce( $_POST['snihub_nonce'], 'snihub_details_data' ) )
		return;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;

	if ( ! current_user_can( 'edit_post', $post_id ) )
		return;

	$likes = (int) $_POST['snihub']['likes'];
	$code = htmlspecialchars( $_POST['snihub']['code'] );

	update_post_meta( $post_id, 'likes', $likes );
	update_post_meta( $post_id, 'code', $code );
	if ( isset( $_POST['snihub']['noindex'] ) )
		update_post_meta( $post_id, 'no_index', 'yes' );
	else
		delete_post_meta( $post_id, 'no_index' ); 
}
add_action( 'save_post', 'snihub_save_meta_box' );

/**
 * Shows Like button for an snippet
 *
 * @param 				int $post_id
 * @return 				void
 */
function snippet_like_btn( $post_id ) {
	$btn_class = ''; 
	if ( is_user_logged_in() ) {
		$likes = ( get_user_meta( get_current_user_id(), 'liked', true ) ? get_user_meta( get_current_user_id(), 'liked', true ) : array() );
		if ( in_array( $post_id , $likes ) )
			$btn_class .= ' active';
	} ?>
	<a href="#like" data-id="<?php echo $post_id; ?>" class="like-code btn btn-info<?php echo $btn_class; ?>"><span class="icon-trending"></span> پسندیدم: <?php echo ( get_post_meta( $post_id, 'likes', true ) ? get_post_meta( $post_id, 'likes', true ) : '0' ); ?></a>
	<?php
}


/************ SHORT CODES ************/
/**
 * Short code for editing profile
 *
 * @return 				string
 */
function snihub_edit_profile_shortcode() {
	if ( ! is_user_logged_in() ) {
		return '<span class="text-danger">برای ویرایش شناسنامه خود، ابتدا وارد حساب کاربری شوید.</span>';
	}

	global $current_user;

	ob_start();
	$content = snihub_update_profile();
	$current_user = wp_get_current_user();
	include 'templates/edit-profile.php';
	$content .= ob_get_clean();

	return $content;
}
add_shortcode( 'user-profile', 'snihub_edit_profile_shortcode' );

/**
 * Update user profile by given parameters
 *
 * @return 				mixed
 */
function snihub_update_profile() {
	if ( isset( $_POST['snihub_profile'] ) && isset( $_POST['snihub_profile']['done'] ) && $_POST['snihub_profile']['done'] == 'ok' ) {
		$errors = array();

		if ( ! isset( $_POST['snihub_nonce'] ) )
			$errors[] = 'خطای امنیتی: فیلد Nonce یافت نشد';

		if ( ! wp_verify_nonce( $_POST['snihub_nonce'], 'snihub_update_profile' ) )
			$errors[] = 'خطای امنیتی: فیلد Nonce اعتبار ندارد';

		$info = $_POST['snihub_profile'];
		if ( ! isset( $info['display_name'] ) || $info['display_name'] == '' )
			$errors[] = 'لطفاً یک نام عمومی برای خودتان انتخاب کنید';

		if ( ! isset( $info['email'] ) || $info['email'] == '' )
			$errors[] = 'ایمیل نمی تواند خالی باشد.';

		if ( isset( $info['pass'] ) && $info['pass'] != '' && strlen( $info['pass'] ) < 6 )
			$errors[] = 'رمزعبور نمی تواند کمتر از 6 کاراکتر باشد';

		if ( ! empty( $errors ) ) {
			$output = '<div class="alert alert-danger"><p>لطفاً خطاهای زیر را برطرف کنید:</p><ul>';
			foreach( $errors as $error )
				$output .= '<li>' . $error . '</li>';
			$output .= '</ul></div>';

			return $output;
		}

		$user_args = array(
			'ID' 			=>	get_current_user_id(),
			'first_name'	=>	sanitize_text_field( $_POST['snihub_profile']['name'] ),
			'display_name'	=>	sanitize_text_field( $_POST['snihub_profile']['display_name'] ),
			'email'			=>	sanitize_text_field( $_POST['snihub_profile']['email'] )
		);

		wp_update_user( $user_args );

		if ( isset( $info['pass'] ) && $info['pass'] != '' ) {
			wp_set_password( $info['pass'], $user_args['ID'] );
		}

		return '<div class="alert alert-success"><p>پروفایل شما با موفقیت ویرایش شد!</p></div>';
	}

	return '';
}

/**
 * Short code for viewing snippets and starred snippets
 *
 * @return 				void
 */
function snihub_my_snippets_short_code() {
	if ( ! is_user_logged_in() ) {
		return '<span class="text-danger">برای ویرایش شناسنامه خود، ابتدا وارد حساب کاربری شوید.</span>';
	}
	
	ob_start();
	include 'templates/my-snippets.php';
	$content = ob_get_clean();

	return $content;
}
add_shortcode( 'my-codes', 'snihub_my_snippets_short_code' );

/**
 * Short code for submiting/editing a snippet
 *
 * @return 					void
 */
function snihub_submit_snippet_short_code() {
	if ( ! is_user_logged_in() ) {
		return '<span class="text-danger">برای ارسال تکه‌کد باید وارد حساب کاربری خود شوید.</span>';
	}

	global $edit;
	$edit = false;
	if ( isset( $_GET['edit_snippet'] ) )
		$edit = (int) $_GET['edit_snippet'];

	global $snippet;
	$snippet = false;

	if ( $edit ) {
		$snippet = get_post( $edit );

		if ( ! $snippet || is_a( $snippet, 'WP_Error' ) )
			return '<span class="text-danger">تکه‌کد موردنظر وجود ندارد.</span>';

		if ( $snippet->post_type != 'snippet' )
			return '<span class="text-danger">شما اجازه ویرایش این رکورد را ندارید</span>';

		if ( ( $snippet->post_author != get_current_user_id() ) && ( ! current_user_can( 'edit_post', $edit ) ) )
			return '<span class="text-danger">تکه‌کد موردنظر متعلق به شما نیست!</span>';
	}

	ob_start();
	$content = snihub_do_submit();
	if ( $edit ) {
		$snippet = get_post( $edit );	
	}
	include 'templates/submit-code.php';
	$content .= ob_get_clean();

	return $content;
}
add_shortcode( 'submit-code', 'snihub_submit_snippet_short_code' );

/**
 * Do submit/edit a snippet
 *
 * @return 					string
 */
function snihub_do_submit() {
	if ( isset( $_POST['submit_snippet'] ) && isset( $_POST['submit_snippet']['ok'] ) && $_POST['submit_snippet']['ok'] == 'yes' ) {
		$errors = array();

		if ( ! isset( $_POST['snihub_nonce'] ) )
			$errors[] = 'خطای امنیتی: فیلد Nonce یافت نشد';

		if ( ! wp_verify_nonce( $_POST['snihub_nonce'], 'snihub_submit_code' ) )
			$errors[] = 'خطای امنیتی: فیلد Nonce اعتبار ندارد';

		$meta = $_POST['submit_snippet'];
		if ( ! isset( $meta['title'] ) || $meta['title'] == '' )
			$errors[] = 'یک عنوان برای تکه‌کد انتخاب کنید';

		if ( ! isset( $meta['code'] ) || $meta['code'] == '' )
			$errors[] = 'تکه‌کد بدون کد؟ مگه داریم؟ مگه میشه؟';

		if ( ! isset( $meta['tags'] ) || $meta['tags'] == '' )
			$errors[] = 'لطفاً حداقل یک برچسب وارد کنید';

		$meta['tags'] = explode( ',', $meta['tags'] );
		if ( count( $meta['tags'] ) > 3 )
			$errors[] = 'شما بیش از 3 برچسب نمی توانید ایجاد کنید.';

		if ( ! empty( $errors ) ) {
			$output = '<div class="alert alert-danger"><p>لطفاً خطاهای زیر را برطرف کنید:</p><ul>';
			foreach( $errors as $error )
				$output .= '<li>' . $error . '</li>';
			$output .= '</ul></div>';

			return $output;
		}

		global $edit, $snippet;
		if ( ! $edit ) {
			$snippet_id = snihub_create_snippet( $meta );
			if ( $snippet_id == false )
				return '<div class="alert alert-danger"><p>در هنگام ایجاد تکه‌کد، مشکلی فنی پیش آمد. مجدداً امتحان کنید.</p></div>';

			$permalink = get_permalink( $snippet_id );
			return '<div class="alert alert-success"><p>تکه‌کد با موفقیت منتشر شد! <a href="' . $permalink . '">آن را ببینید!</a></p></div>';
		} else {
			$ok = snihub_update_snippet( $meta, $edit );
			if ( $ok == false )
				return '<div class="alert alert-danger"><p>در بروزرسانی تکه‌کد مشکلی فنی پیش آمد. لطفاً مجدداً تلاش کنید.</p></div>';

			$permalink = get_permalink( $edit );
			return '<div class="alert alert-success"><p>تکه‌کد با موفقیت ویرایش شد! <a href="' . $permalink . '">آن را ببینید!</a></p></div>';
		}
	}
}

/**
 * Creates a snippet based on given data.
 *
 * @param 			array $meta
 * @return 			int New ID
 */
function snihub_create_snippet( $meta ) {
	$name = snihub_random_string();
	$args = array(
		'post_title' 		=>	sanitize_text_field( $meta['title'] ),
		'post_type' 		=>	'snippet',
		'post_author' 		=>	get_current_user_id(),
		'post_content' 		=>	$meta['content'],
		'post_name' 		=>	$name,
		'post_status' 		=>	'publish'
	);

	$snippet_id = wp_insert_post( $args );
	if ( is_a( $snippet_id, 'WP_Error' ) )
		return false;

	// Set taxonomies
	wp_set_object_terms( $snippet_id, $meta['lang'], 'language' );
	wp_set_object_terms( $snippet_id, $meta['tags'], 'snippet_tags' );

	// Set metas
	update_post_meta( $snippet_id, 'code', htmlspecialchars( $meta['code'] ) );

	if ( isset( $meta['no_index'] ) )
		update_post_meta( $snippet_id, 'no_index', true );

	update_post_meta( $snippet_id, 'likes', 0 );

	return $snippet_id;
}

/**
 * Updates a snippet based on given data.
 *
 * @param 			array $meta
 * @return 			int New ID
 */
function snihub_update_snippet( $meta, $last_id ) {
	$args = array(
		'ID' 				=>	$last_id,
		'post_title' 		=>	sanitize_text_field( $meta['title'] ),
		'post_content' 		=>	$meta['content'],
		'post_type' 		=>	'snippet',
		'post_status' 		=>	'publish'
	);

	$snippet_id = wp_update_post( $args );
	if ( is_a( $snippet_id, 'WP_Error' ) )
		return false;

	// Set taxonomies
	wp_set_object_terms( $snippet_id, $meta['lang'], 'language' );
	wp_set_object_terms( $snippet_id, $meta['tags'], 'snippet_tags' );

	// Set metas
	update_post_meta( $snippet_id, 'code', htmlspecialchars( $meta['code'] ) );
	
	if ( isset( $meta['no_index'] ) )
		update_post_meta( $snippet_id, 'no_index', true );
	else
		delete_post_meta( $snippet_id, 'no_index' );

	return $snippet_id;
}

/**
 * Generate a word for post names.
 *
 * @param 				int $length
 * @return 				string
 */
function snihub_random_string( $length = 10 ) {
	$a_words = array( 'a', 'e', 'i', 'o', 'u' );
	$b_words = array( 'b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'q', 'r', 's', 't', 'v', 'w', 'x', 'y', 'z' );

	$output = '';
	while( $length > 0 ) {
		shuffle( $a_words );
		shuffle( $b_words );

		$output .= $a_words[1] . $b_words[0];
		$length = $length - 2;
	}

	return $output;
}	