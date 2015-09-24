<?php
	global $snihub_options;
	$query = new WP_Query( array(
		'post_type' 			=>	'snippet',
		'posts_per_page' 		=>	-1,
		'author' 				=>	get_current_user_id(),
		'post_status' 			=>	'publish'
	) );

	if ( ! $query->have_posts() ) {
		echo '<span style="text-align: center;">شما هنوز تکه&zwnj;کدی ایجاد نکرده&zwnj;اید! <a href="' . get_permalink( $snihub_options['new_snippet_page'] ) . '">یکی بسازید!</a>';
	} else {
		?><table class="table table-hover">
			<thead>
				<tr>
					<th>عنوان</th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			<?php while( $query->have_posts() ) : $query->the_post(); ?>
				<tr class="post-<?php the_ID(); ?>">
					<td><a href="<?php the_permalink(); ?>" title="پیوند یکتا به <?php the_title(); ?>"><?php if ( snihub_is_private( get_the_ID() ) ) echo '<span class="icon-private snippet-title-icon"></span> '; ?> <?php the_title(); ?></a></td>
					<td><a href="<?php the_permalink(); ?>" class="btn btn-primary"><span class="icon-snippets"></span> مشاهده</a></td>
					<td><?php snippet_like_btn( get_the_ID() ); ?></td>
					<td><a href="<?php echo add_query_arg( 'edit_snippet', get_the_ID(), get_permalink( $snihub_options['new_snippet_page'] ) ); ?>" class="btn btn-success"><span class="icon-edit"></span> ویرایش</a></td>
				</tr>
			<?php endwhile; ?>
			</tbody>
		</table><?php
		wp_reset_postdata();
	}

	?>
	<h4 style="margin-top: 10px;">تکه&zwnj;کدهای پسندیده&zwnj;شده</h4>
	<?php
	$liked = get_user_meta( get_current_user_id(), 'liked', true );

	if ( ! $liked || empty( $liked ) )
		echo '<span style="text-align: center;">شما هنوز هیچ کدی را نپسندیده اید.</span>';
	else {
		?><table class="table table-hover">
			<thead>
				<tr>
					<th>عنوان</th>
					<th></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach( $liked as $snippet_id ) {
				if ( ! is_int( $snippet_id ) )
					continue;

				$snippet = get_post( $snippet_id );
				if ( ! $snippet )
					continue;

				if ( $snippet->post_type != 'snippet' )
					continue;

				?>
				<tr class="post-<?php echo $snippet->ID; ?>">
					<td><a href="<?php echo get_permalink( $snippet_id ); ?>" title="پیوند یکتا به <?php echo $snippet->post_title; ?>"><?php if ( snihub_is_private( $snippet->ID ) ) echo '<span class="icon-private snippet-title-icon"></span> '; ?> <?php echo $snippet->post_title; ?></a></td>
					<td><a href="<?php echo get_permalink( $snippet_id ); ?>" class="btn btn-primary"><span class="icon-snippets"></span> مشاهده</a></td>
					<td><?php snippet_like_btn( $snippet_id ); ?></td>
				</tr>
				<?php
			}
			?></tbody>
		</table><?php
		wp_reset_postdata();
	}