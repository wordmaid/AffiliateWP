jQuery(document).ready(function($) {
    // Settings uploader
	var file_frame;
	window.formfield = '';

	$('body').on('click', '.affwp_settings_upload_button', function(e) {

		e.preventDefault();

		var button = $(this);

		window.formfield = $(this).parent().prev();

		// If the media frame already exists, reopen it.
		if( file_frame ) {
			file_frame.open();
			return;
		}

		// Create the media frame
		file_frame = wp.media.frames.file_frame = wp.media({
			frame: 'post',
			state: 'insert',
			title: button.data( 'uploader_title' ),
			button: {
				text: button.data( 'uploader_button_text' )
			},
			multiple: false
		});

		file_frame.on( 'menu:render:default', function( view ) {
			// Store our views in an object,
			var views = {};

			// Unset default menu items
			view.unset( 'library-separator' );
			view.unset( 'gallery' );
			view.unset( 'featured-image' );
			view.unset( 'embed' );

			// Initialize the views in our view object
			view.set( views );
		});

		// When an image is selected, run a callback
		file_frame.on( 'insert', function() {
			var selection = file_frame.state().get( 'selection' );

			selection.each( function( attachment, index ) {
				attachment = attachment.toJSON();
				window.formfield.val(attachment.url);
			});
		});

		// Open the modal
		file_frame.open();
	});

	var file_frame;
	window.formfield = '';

	// Show referral export form
	$('.affwp-referrals-export-toggle').click(function() {
		$('.affwp-referrals-export-toggle').toggle();
		$('#affwp-referrals-export-form').slideToggle();
	});

	$('#affwp-referrals-export-form').submit(function() {
		if( ! confirm( affwp_vars.confirm ) ) {
			return false;
		}
	});

	// datepicker
	if( $('.affwp-datepicker').length ) {
		$('.affwp-datepicker').datepicker({dateFormat: 'mm/dd/yy'});
	}

	// Ajax user search.
	$( '.affwp-user-search' ).each( function() {
		var	$this    = $( this ),
			$action  = 'affwp_search_users',
			$search  = $this.val(),
			$status  = $this.data( 'affwp-status'),
			$user_id = $( '#user_id' );

		$this.autocomplete( {
			source: ajaxurl + '?action=' + $action + '&term=' + $search + '&status=' + $status,
			delay: 500,
			minLength: 2,
			position: { offset: '0, -1' },
			select: function( event, data ) {
				$user_id.val( data.item.user_id );
			},
			open: function() {
				$this.addClass( 'open' );
			},
			close: function() {
				$this.removeClass( 'open' );
			}
		} );

		// Unset the user_id input if the input is cleared.
		$this.on( 'keyup', function() {
			if ( ! this.value ) {
				$user_id.val( '' );
			}
		} );
	} );

	// select image for creative
	var file_frame;
	$('body').on('click', '.upload_image_button', function(e) {

		e.preventDefault();

		var formfield = $(this).prev();

		// If the media frame already exists, reopen it.
		if ( file_frame ) {
			//file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
			file_frame.open();
			return;
		}

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			frame: 'select',
			title: 'Choose Image',
			multiple: false,
			library: {
				type: 'image'
			},
			button: {
				text: 'Use Image'
			}
		});

		file_frame.on( 'menu:render:default', function(view) {
	        // Store our views in an object.
	        var views = {};

	        // Unset default menu items
	        view.unset('library-separator');
	        view.unset('gallery');
	        view.unset('featured-image');
	        view.unset('embed');

	        // Initialize the views in our view object.
	        view.set(views);
	    });

		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			var attachment = file_frame.state().get('selection').first().toJSON();
			formfield.val(attachment.url);

			var img = $('<img />');
			img.attr('src', attachment.url);
			// replace previous image with new one if selected
			$('#preview_image').empty().append( img );

			// show preview div when image exists
			if ( $('#preview_image img') ) {
				$('#preview_image').show();
			}
		});

		// Finally, open the modal
		file_frame.open();
	});

	// Confirm referral deletion
	$('body').on('click', '.affiliates_page_affiliate-wp-referrals .delete', function(e) {

		if( confirm( affwp_vars.confirm_delete_referral) ) {
			return true;
		}

		return false;

	});

	function maybe_activate_migrate_users_button() {
		var checked = $('#affiliate-wp-migrate-user-accounts input:checkbox:checked' ).length,
		    $button = $('#affiliate-wp-migrate-user-accounts input[type=submit]');

		if ( checked > 0 ) {
			$button.prop( 'disabled', false );
		} else {
			$button.prop( 'disabled', true );
		}
	}

	maybe_activate_migrate_users_button();

	$('body').on('change', '#affiliate-wp-migrate-user-accounts input:checkbox', function() {
		maybe_activate_migrate_users_button();
	});

	$('#affwp_add_affiliate #status').change(function() {

		var status = $(this).val();
		if( 'active' == status ) {
			$('#affwp-welcome-email-row').show();
		} else {
			$('#affwp-welcome-email-row').hide();
			$('#affwp-welcome-email-row #welcome_email').prop( 'checked', false );
		}

	});

	/**
	 * Enable meta box toggle states
	 *
	 * @since  1.9
	 *
	 * @param  typeof postboxes postboxes object
	 *
	 * @return {void}
	 */
	if ( typeof postboxes !== 'undefined' ) {
		postboxes.add_postbox_toggles( pagenow );
	}

	/**
	 * Batch Processor.
	 *
	 * @since 2.0
	 */
	var AffWP_Batch = {

		init : function() {
			this.submit();
			this.dismiss_message();
		},

		/**
		 * Handles form submission preceding batch processing.
		 *
		 * @since 2.0
		 */
		submit : function() {

			var	self = this,
				form = $( '.affwp-batch-form' );

			form.on( 'submit', function( event ) {
				event.preventDefault();

				var submitButton = $(this).find( 'input[type="submit"]' );

				if ( ! submitButton.hasClass( 'button-disabled' ) ) {

					var data = {
						batch_id: $( this ).data( 'batch_id' ),
						nonce: $( this ).data( 'nonce' ),
						form: form.serializeAssoc(),
					};

					// Disable the button.
					submitButton.addClass( 'button-disabled' );

					$( this ).find('.notice-wrap').remove();

					// Add the progress bar.
					$( this ).append( '<div class="notice-wrap"><span class="spinner is-active"></span><div class="affwp-batch-progress"><div></div></div></div>' );

					// Start the process.
					self.process_step( 1, data, self );

				}

			} );
		},

		/**
		 * Processes a single batch of data.
		 *
		 * @since 2.0
		 *
		 * @param {integer}  step Step in the process.
		 * @param {string[]} data Form data.
		 * @param {object}   self Instance.
		 */
		process_step : function( step, data, self ) {

			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					batch_id: data.batch_id,
					action: 'process_batch_request',
					nonce: data.nonce,
					form: data.form,
					step: step
				},
				dataType: "json",
				success: function( response ) {

					if( response.data.done || response.data.error ) {

						// We need to get the actual in progress form, not all forms on the page
						var batch_form  = $('.affwp-batch-form').find('.affwp-batch-progress').parent().parent();
						var notice_wrap = batch_form.find('.notice-wrap');

						batch_form.find('.button-disabled').removeClass('button-disabled');

						if ( response.data.error ) {

							notice_wrap.html('<div class="updated error"><p>' + response.data.error + '</p></div>');

						} else if ( response.data.done ) {

							if ( response.data.url ) {
								notice_wrap.hide();
								window.location = response.data.url;
							} else {
								notice_wrap.html('<div id="affwp-batch-success" class="updated notice is-dismissible"><p>' + response.data.message + '<span class="notice-dismiss"></span></p></div>');
							}

						} else {

							notice_wrap.remove();

						}
					} else {
						$('.affwp-batch-progress div').animate({
							width: response.data.percentage + '%',
						}, 50, function() {
							// Animation complete.
						});

						self.process_step( parseInt( response.data.step ), data, self );
					}

				}
			}).fail(function (response) {
				if ( window.console && window.console.log ) {
					console.log( response );
				}
			});

		},

		dismiss_message : function() {
			$('body').on( 'click', '#affwp-batch-success .notice-dismiss', function() {
				$('#affwp-batch-success').parent().slideUp('fast');
			});
		}

	};
	AffWP_Batch.init();

	$.extend({
		isArray: function (arr){
			if (typeof arr == 'object'){
				if (arr.constructor == Array){
					return true;
				}
			}
			return false;
		},
		arrayMerge: function (){
			var a = {};
			var n = 0;
			var argv = $.arrayMerge.arguments;
			for (var i = 0; i < argv.length; i++){
				if ($.isArray(argv[i])){
					for (var j = 0; j < argv[i].length; j++){
						a[n++] = argv[i][j];
					}
					a = $.makeArray(a);
				} else {
					for (var k in argv[i]){
						if (isNaN(k)){
							var v = argv[i][k];
							if (typeof v == 'object' && a[k]){
								v = $.arrayMerge(a[k], v);
							}
							a[k] = v;
						} else {
							a[n++] = argv[i][k];
						}
					}
				}
			}
			return a;
		},
		count: function (arr){
			if ($.isArray(arr)){
				return arr.length;
			} else {
				var n = 0;
				for (var k in arr){
					if (!isNaN(k)){
						n++;
					}
				}
				return n;
			}
		},
	});

	$.fn.extend({
		serializeAssoc: function (){
			var o = {
				aa: {},
				add: function (name, value){
					var tmp = name.match(/^(.*)\[([^\]]*)\]$/);
					if (tmp){
						var v = {};
						if (tmp[2])
							v[tmp[2]] = value;
						else
							v[$.count(v)] = value;
						this.add(tmp[1], v);
					}
					else if (typeof value == 'object'){
						if (typeof this.aa[name] != 'object'){
							this.aa[name] = {};
						}
						this.aa[name] = $.arrayMerge(this.aa[name], value);
					}
					else {
						this.aa[name] = value;
					}
				}
			};
			var a = $(this).serializeArray();
			for (var i = 0; i < a.length; i++){
				o.add(a[i].name, a[i].value);
			}
			return o.aa;
		}
	});
} );
