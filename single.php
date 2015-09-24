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
						while( have_posts() ) : the_post();

							?>
							<article class="post" name="snippet-<?php the_ID(); ?>" id="snippet-<?php the_ID(); ?>">
								<h3><?php if ( snihub_is_private( get_the_ID() ) ) echo '<span class="icon-private snippet-title-icon"></span> '; ?><a href="<?php the_permalink(); ?>" title="پیوند یکتا به <?php the_title(); ?>"><?php the_title(); ?></a></h3>
								<p><?php the_content(); ?></p>
								<?php $code = get_post_meta( get_the_ID(), 'code', true ); if ( $code ) { ?>
								<pre class="line-numbers">
<code class="language-<?php echo snihub_snippet_language( get_the_ID() ); ?>"><?php echo $code; ?></code>
								</pre>
								<?php } ?>
								<div class="row">
									<div class="pull-left">
										<?php $cat = snihub_cat_data( get_the_ID() ); $class = 'btn-warning'; if ( is_tax( 'language', $cat['title'] ) ) $class = 'btn-warning active'; ?>
										<a href="<?php echo $cat['link']; ?>" class="btn <?php echo $class; ?>"><span class="icon-bug"></span> زبان: <?php echo $cat['title']; ?></a>
										<?php $btn = ''; if ( is_user_logged_in() ) {
													$likes = ( get_user_meta( get_current_user_id(), 'liked', true ) ? get_user_meta( get_current_user_id(), 'liked', true ) : array() );
													if ( in_array( get_the_ID() , $likes ) )
														$btn .= ' active';
												} ?>
										<a href="#like" data-id="<?php the_ID(); ?>" class="like-code btn btn-info<?php echo $btn; ?>"><span class="icon-trending"></span> پسندیدم: <?php echo ( get_post_meta( get_the_ID(), 'likes', true ) ? get_post_meta( get_the_ID(), 'likes', true ) : '0' ); ?></a>
										<a href="#report" data-target="#reportDialog" data-toggle="modal" class="btn btn-danger"><span class="icon-alert"></span> گزارش</a>
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
					} else {
						?>
						<h3>چیزی اینجا نیست!</h3>
						<p>مطمئنی اشتباه نیومدی؟</p>
						<?php
					} ?>
				</div>
				<div class="col-md-3">
					<h4 style="text-align: center;">نویسنده</h4>
					<p style="text-align: center;"><?php echo get_avatar( get_the_author_meta( 'ID' ), 96, '', false, array( 'class' => 'img-circle' ) ); ?></p>
					<h3><a href="<?php bloginfo( 'wpurl' ); ?>/snippets_by/<?php the_author_meta( 'ID' ); ?>"><?php the_author_meta( 'display_name' ); ?></a> <a href="<?php bloginfo( 'wpurl' ); ?>/snippets_by/<?php the_author_meta( 'ID' ); ?>" class="btn btn-default">بقیه تکه&zwnj;کدها</a></h3>
					<hr>
					<p>کد استفاده در انجمن:</p>
					<input type="text" readonly style="direction: ltr;" value="[snippet id='<?php global $post; echo $post->post_name; ?>']" class="form-control">
				</div>
			</div>
		</div>
		<div class="modal fade" id="reportDialog" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">گزارش</h4>
					</div>
					<div class="modal-body">
						<p>از همکاری شما برای پیشبرد این شبکه متشکریم! لطفاً یکی از گزینه&zwnj;های زیر را که فکر می‌کنید درباره تکه‌کد موردنظر صدق می‌کند را انتخاب کنید.</p>
						<p><label><input type="radio" name="reason" class="report-reason" value="words" checked> استفاده از کلمات توهین‌آمیز در توضیحات یا کد</label></p>
						<p><label><input type="radio" name="reason" class="report-reason" value="crypted"> کد Cryptشده و غیرمتن باز</label></p>
						<p><label><input type="radio" name="reason" class="report-reason" value="dangerous"> کد مخرب</label></p>
						<p><label><input type="radio" name="reason" class="report-reason" value="irrelevant"> متن نامربوط یا قراردادن هرچیزی غیر از کد</label></p>
						<p><label><input type="radio" name="reason" class="report-reason" value="untagged"> عدم تگ گذاری صحیح</label></p>
						<p><label><input type="radio" name="reason" class="report-reason" value="other" id="other_option"> دلیل دیگر</label></p>
						<div id="report-detail" style="display: none;">
							<p>لطفاً درمورد علت گزارش توضیحی دهید:</p>
							<p><textarea id="report-desc" class="form-control"></textarea></p>
						</div>
					</div>
					<div class="modal-footer">
						<input type="hidden" id="post_id" value="<?php the_ID(); ?>">
						<button type="button" class="btn btn-default" data-dismiss="modal">بی&zwnj;خیال</button>
						<button type="button" class="btn btn-primary" id="send-report">ارسال گزارش</button>
					</div>
				</div>
			</div>
		</div>
<?php get_footer(); ?>