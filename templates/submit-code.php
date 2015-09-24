<?php
	global $edit, $snippet;
?>
<form method="POST" class="submit-code-form">
	<?php wp_nonce_field( 'snihub_submit_code', 'snihub_nonce' ); ?>

	<div class="form-group">
		<label for="snippet_title">عنوان تکه‌کد</label>
		<input type="text" class="form-control" required name="submit_snippet[title]" maxlength="256" placeholder="توضیح تکه‌کد در چند کلمه، مثال: تکه‌کد حذف نسخه وردپرس" value="<?php if ( $snippet ) echo $snippet->post_title; ?>">
	</div>

	<div class="form-group">
		<label for="snippet_desc">توضیحات تکه‌کد <small>مختصر و مفید</small></label>
		<?php
			wp_editor( ( $snippet ) ? $snippet->post_content : '', 'snippet_desc', array(
				'textarea_name' 			=>	'submit_snippet[content]',
				'editor_height' 			=>	'150px',
				'media_buttons' 			=>	false,
				'tinymce'					=>	true,
				'editor_class' 				=>	'form-control',
			) );
		?>
	</div>

	<div class="form-group">
		<label for="snippet_lang">زبان تکه‌کد</label>
		<select class="form-control" name="submit_snippet[lang]" id="snippet_lang">
			<?php
				$current_lang = ( $edit ) ? snihub_snippet_language( $edit ) : false;
				$terms = get_terms( 'language', array( 'hide_empty' => false ) );

				foreach( $terms as $term ) {
					$selected = ( $term->slug == $current_lang ) ? ' selected' : '';
					echo '<option value="' . $term->slug . '"' . $selected . '>' . $term->name . '</option>';
				}
			?>
		</select>
	</div>

	<div class="form-group">
		<label for="snippet_tags">برچسب&zwnj;ها (حداکثر 3تا. با کامای لاتین جدا کنید)</label>
		<input type="text" name="submit_snippet[tags]" class="form-control" value="<?php if ( $edit != false ) echo snihub_tags_comma( $edit ); ?>" placeholder="مثال: وردپرس,کاربردی,افزونه">
	</div>

	<div class="form-group">
		<label for="snippet_code">کد مربوطه <small>آن را Paste کنید</small></label>
		<textarea name="submit_snippet[code]" style="width: 100%;direction: ltr;text-align: left;font-family: monospace" rows="10" class="form-control" id="snippet_code"><?php if ( $edit ) echo get_post_meta( $edit, 'code', true ); ?></textarea>
	</div>

	<div class="form-group">
		<label for="snippet_priv"><input type="checkbox" name="submit_snippet[no_index]" id="snippet_priv"<?php if ( $edit != false && snihub_is_private( $edit ) ) echo ' checked'; ?>> عدم نمایش در لیست تکه&zwnj;کدها</label>
	</div>

	<div class="form-group">
		<?php if ( $edit ) { ?>
			<button type="submit" name="submit_snippet[ok]" value="yes" class="btn btn-success"><span class="icon-edit"></span> ویرایش</button>
		<?php } else { ?>
			<button type="submit" name="submit_snippet[ok]" value="yes" class="btn btn-primary"><span class="icon-ok"></span> انتشار</button>
		<?php } ?>
	</div>
</form>