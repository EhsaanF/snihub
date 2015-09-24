<?php global $current_user; $user = $current_user; ?>
<form method="POST" class="edit-profile-form">
	<?php wp_nonce_field( 'snihub_update_profile', 'snihub_nonce' ); ?>
	<div class="form-group">
		<label for="name">نام شما</label>
		<input type="text" name="snihub_profile[name]" id="name" class="form-control" value="<?php echo $user->first_name; ?>">
	</div>
	<div class="form-group">
		<label for="display_name">نام نمایشی</label>
		<input type="text" name="snihub_profile[display_name]" id="display_name" class="form-control" value="<?php echo $user->display_name; ?>">
	</div>

	<div class="form-group">
		<label for="email">ایمیل</label>
		<input type="email" name="snihub_profile[email]" id="email" class="form-control" value="<?php echo $user->user_email; ?>">
	</div>

	<div class="form-group">
		<label for="pass">رمزعبور</label>
		<input type="password" name="snihub_profile[pass]" id="pass" class="form-control" placeholder="در صورتی که قصد تغییر رمزعبور خود را دارید، این فیلد را پر کنید.">
	</div>

	<div class="form-group">
		<button type="submit" name="snihub_profile[done]" value="ok" class="btn btn-success"><span class="icon-ok"></span> ذخیره تغییرات</button>
	</div>

</form>