<?php get_header(); ?>
		<div class="container" id="main-container" style="margin-top: 64px;">
			<div class="row">
				<div class="col-md-12">
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
						while( have_posts() ) : the_post();

							?>
							<article class="post" name="snippet-<?php the_ID(); ?>" id="snippet-<?php the_ID(); ?>">
								<h3><a href="<?php the_permalink(); ?>" title="پیوند یکتا به <?php the_title(); ?>"><?php the_title(); ?></a></h3>
								<p><?php the_content(); ?></p>
							</article>
							<?php

						endwhile;
					} else {
						?>
						<h3>چیزی اینجا نیست!</h3>
						<p>مطمئنی اشتباه نیومدی؟</p>
						<?php
					} ?>
				</div>
			</div>
		</div>
<?php get_footer(); ?>