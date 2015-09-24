				<div class="col-md-3">
					<ul class="nav nav-pills nav-stacked browse-snippets">
						<li<?php echo ( is_home() && ! get_query_var( 'hot_snippets', false ) ? ' class="active"' : '' ); ?>><a href="<?php bloginfo( 'url' ); ?>"><span class="icon-enter"></span> تکه&zwnj;کدهای اخیر</a></li>
						<li><a href="<?php bloginfo( 'url' ); ?>/random/"><span class="icon-snippets"></span> تکه&zwnj;کد تصادفی</a></li>
						<li<?php echo ( get_query_var( 'hot_snippets', false ) ? ' class="active"' : '' ); ?>><a href="<?php bloginfo( 'url' ); ?>/hot/"><span class="icon-hot"></span> پرطرفدارترین ها</a></li>
						<li><a href="<?php global $snihub_options;echo get_permalink( $snihub_options['new_snippet_page'] ); ?>"><span class="icon-rocket"></span> ارسال تکه&zwnj;کد</a></li>
					</ul>
					<hr>
					<div class="list-group languages">
						<h4 style="direction: rtl;">زبان‌های برنامه‌نویسی</h4>
						<?php
							$categories = get_terms( 'language', array(
								'type' 			=>	'snippet',
								'taxonomy'		=>	'language',
								'hide_empty' 	=>	false,
								'orderby' 		=>	'count',
								'order' 		=>	'desc'
							) );

							if ( $categories ) {
								foreach( $categories as $cat ) {
									$active = ( is_tax( 'language', $cat->term_id ) ? ' active' : '' );
									echo '<a href="' . get_term_link( intval( $cat->term_id ), 'language' ) . '" class="list-group-item' . $active . '">' . $cat->name . '</a>';
								}
							}
						?>
					</div>
				</div>