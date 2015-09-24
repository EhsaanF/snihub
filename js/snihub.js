jQuery( function( $ ) {
	$( '.like-code' ).click( function( e ) {
		e.preventDefault();

		if ( $( this ).hasClass( 'active' ) ) {
			$( this ).removeClass( 'active' );
		}

		var post_id = $( this ).data( 'id' );
		var el = $( this );

		$.ajax({
			method: 		'POST',
			data: 			{ 'snihub-action': 'like-code', 'post-id': post_id },
			success: 		function( data ) {
				data = JSON.parse( data );

				if ( data.success == true ) {
					if ( data.disliked == false ) {
						el.addClass( 'active' );
					} else {
						el.removeClass( 'active' );
					}
					el.html( '<span class="icon-trending"></span> پسندیدم: ' + data.new_count );
				}

				if ( data.yourlike == true ) {
					alert( 'شما نمی توانید کد خودتان را پسند کنید!' );
					return false;
				}

				if ( data.loggedin == false ) {
					alert( 'برای انجام این عمل باید وارد حساب کاربری خود شوید.' );
					return false;
				}

				if ( data.already_liked == true ) {
					alert( 'شما قبلاً این مورد را پسندیده اید' );
					return false;
				}
			}
		});
	} );

	$( 'input:radio[name=reason]' ).click( function() {
		var value = $( this ).val();

		if ( value == 'other' ) {
			$( '#report-detail' ).slideDown( 500 );
		} else {
			$( '#report-detail' ).slideUp( 500 );
		}
	} );

	$( '#send-report' ).click( function( e ) {
		e.preventDefault();

		var reason = $( 'input:radio[name=reason]:checked' ).val();
		var post_id = $( '#post_id' ).val();
		var other_desc = $( '#report-desc' ).val();
		$( this ).html( 'شکیبا باشید...' );

		if ( reason == 'other' && other_desc == '' ) {
			alert( 'لطفاً توضیحی درمورد گزارش خود بدهید!' );
			$( '#send-report' ).html( 'ارسال گزارش' );
			return;
		}

		$.ajax({
			'method': 			'POST',
			data: 				{ 'snihub-action': 'report', 'reason': reason, 'post_id': post_id, 'other_desc': other_desc },
			success: 			function( data ) {
				data = JSON.parse( data );

				if ( data.success == true ) {
					alert( 'با تشکر از ارسال گزارش شما! به زودی بررسی خواهد شد.' );
					$( '#reportDialog' ).modal( 'hide' );
				} else {
					alert( 'مشکلی در ارسال گزارش پیش آمد. مجدداً امتحان کنید' );
				}
			},
			complete: 			function() {
				$( '#send-report' ).html( 'ارسال گزارش' );
			}
		});
	} );
} );