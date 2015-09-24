<?php get_header(); ?>
		<div class="container" id="main-container" style="margin-top: 64px;">
			<div class="row">
				<div class="col-md-9">
					<?php
						if ( is_search() ) {
							echo '<h2>نتایج جستجو برای ' . get_search_query() . '</h2>';
						}
						if ( is_tax( 'language' ) ) {
							echo '<h2>تکه‌کدهای زبان ' . get_queried_object()->name . '</h2>';
						}
						if ( is_tax( 'snippet_tags' ) ) {
							echo '<h2>تکه‌کدهایی با برچسب ' . get_queried_object()->name . '</h2>';
						}

					?>
					<?php if ( have_posts() ) {
						if ( get_query_var( 'snippets_by', false ) ) {
							$user = new WP_User( get_query_var( 'snippets_by' ) );
							echo '<h2>' . get_avatar( $user->user_email, 96, '', false, array( 'class' => 'img-circle' ) ) . ' تکه‌کدهای ' . $user->display_name . '</h2>';
						}
						while( have_posts() ) : the_post();
							global $post;
							if ( $post->post_type != 'snippet' )
								continue;
							
							?>
							<article class="post" name="snippet-<?php the_ID(); ?>" id="snippet-<?php the_ID(); ?>">
								<h3><a href="<?php the_permalink(); ?>" title="پیوند یکتا به <?php the_title(); ?>"><?php the_title(); ?></a></h3>
								<p><?php the_content(); ?></p>
								<div class="row">
									<div class="pull-left">
										<?php $cat = snihub_cat_data( get_the_ID() ); $class = 'btn-warning'; if ( is_tax( 'language', $cat['title'] ) ) $class = 'btn-warning active'; ?>
										<a href="<?php echo $cat['link']; ?>" class="btn <?php echo $class; ?>"><span class="icon-bug"></span> زبان: <?php echo $cat['title']; ?></a>
										<?php snippet_like_btn( get_the_ID() ); ?>
										<a href="<?php the_permalink(); ?>" class="btn btn-success"><span class="icon-code"></span> مشاهده کد</a>
									</div>
									<div class="pull-right">
										<?php $tags = snihub_tags( get_the_ID() ); if ( $tags ) { ?>
										<div class="btn-group">
											<?php foreach( $tags as $tag ) { ?>
											<?php $class = 'btn-default'; if ( is_tax( 'snippet_tags', $tag['name'] ) ) $class = 'btn-info'; ?>
											<a href="<?php echo $tag['link']; ?>" class="btn <?php echo $class; ?>"><?php echo $tag['name']; ?></a>
											<?php } ?>
											<a class="btn btn-success"><span class="icon-tag"></span> برچسب ها</a>
										</div>
										<?php } ?>
									</div>
								</div>
							</article>
							<?php
						endwhile;
						snihub_pagination();
					} else {
						?>
						<h3>چیزی اینجا نیست!</h3>
						<p>مطمئنی اشتباه نیومدی؟</p>
						<?php
					} ?>
				</div>
				<?php get_sidebar(); ?>
			</div>
		</div>
<?php get_footer(); ?>