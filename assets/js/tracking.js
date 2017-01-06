jQuery(document).ready( function($) {

	var ref_cookie = $.cookie( 'affwp_ref' );
	var visit_cookie = $.cookie( 'affwp_ref_visit_id' );
	var campaign_cookie = $.cookie( 'affwp_campaign' );

	var credit_last = AFFWP.referral_credit_last;

	if( '1' != credit_last && ref_cookie ) {
		return;
	}

	var ref = affwp_get_query_vars()[AFFWP.referral_var];
	var campaign = affwp_get_query_vars()['campaign'];

	if( typeof ref == 'undefined' || $.isFunction( ref ) ) {

		// See if we are using a pretty affiliate URL
		var path = window.location.pathname.split( '/' );
		$.each( path, function( key, value ) {
			if( AFFWP.referral_var == value ) {
				ref = path[ key + 1 ];
			}
		});

	}

	if( $.isFunction( ref ) ) {
		return;
	}

	if( typeof ref != 'undefined' && ! $.isNumeric( ref ) ) {

		// If a username was provided instead of an affiliate ID number, we need to retrieve the ID
		$.ajax({
			type: "POST",
			data: {
				action: 'affwp_get_affiliate_id',
				affiliate: ref
			},
			url: affwp_scripts.ajaxurl,
			success: function (response) {
				if( '1' == response.data.success ) {

					if( '1' == credit_last && ref_cookie && ref_cookie != response.data.affiliate_id ) {
						$.removeCookie( 'affwp_ref' );
					}

					if( ( '1' == credit_last && ref_cookie && ref_cookie != response.data.affiliate_id ) || ! ref_cookie ) {
						affwp_track_visit( response.data.affiliate_id, campaign );
					}
				}
			}

		}).fail(function (response) {
			if ( window.console && window.console.log ) {
				console.log( response );
			}
		});

	} else {

		// If a referral var is present and a referral cookie is not already set
		if( ref && ! ref_cookie ) {
			affwp_track_visit( ref, campaign );
		} else if( '1' == credit_last && ref && ref_cookie && ref !== ref_cookie ) {
			$.removeCookie( 'affwp_ref' );
			affwp_track_visit( ref, campaign );
		}

	}

	/**
	 * Tracks an affiliate visit.
	 *
	 * @since  1.0
	 *
	 * @param  {int}    affiliate_id The affiliate ID.
	 * @param  {string} url_campaign The campaign, if provided.
	 *
	 * @return void
	 */
	function affwp_track_visit( affiliate_id, url_campaign ) {

		// Set the cookie and expire it after 24 hours
		$.cookie( 'affwp_ref', affiliate_id, { expires: AFFWP.expiration, path: '/' } );

		// Fire an ajax request to log the hit
		$.ajax({
			type: "POST",
			data: {
				action: 'affwp_track_visit',
				affiliate: affiliate_id,
				campaign: url_campaign,
				url: document.URL,
				referrer: document.referrer
			},
			url: affwp_scripts.ajaxurl,
			success: function (response) {
				$.cookie( 'affwp_ref_visit_id', response, { expires: AFFWP.expiration, path: '/' } );
				$.cookie( 'affwp_campaign', url_campaign, { expires: AFFWP.expiration, path: '/' } );
			}

		}).fail(function (response) {
			if ( window.console && window.console.log ) {
				console.log( response );
			}
		});

	}

	/**
	 * Gets url query variables from the current URL.
	 *
	 * @since  1.0
	 *
	 * @return {array} vars The url query variables in the current site url, if present.
	 */
	function affwp_get_query_vars() {
		var vars = [], hash;
		var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
		for(var i = 0; i < hashes.length; i++) {
			hash = hashes[i].split('=');
			vars.push(hash[0]);

			var key = typeof hash[1] == 'undefined' ? 0 : 1;

			// Remove fragment identifiers
			var n = hash[key].indexOf('#');
			hash[key] = hash[key].substring(0, n != -1 ? n : hash[key].length);
			vars[hash[0]] = hash[key];
		}
		return vars;
	}

	if ( AFFWP.debug ) {
		/**
		 * Helpful utilities and data for debugging the JavaScript environment.
		 *
		 * @type {prototype Object} AFFWP.debug_utility  An AFFWP.debug_utility object.
		 *
		 * @since 2.0
		 * @var
		 */
		AFFWP.debug_utility = {
			/**
			 * Various data pertaining to AffiliateWP (if available).
			 *
			 * @since 2.0
			 * @return {array} An array of debug variables.
			 */
			vars : {
				ajax_url        : affwp_scripts.ajaxurl,
				ref_cookie      : ref_cookie,
				visit_cookie    : visit_cookie,
				credit_last     : AFFWP.referral_credit_last,
				campaign_cookie : campaign_cookie,
				ref             : AFFWP.referral_var,
				campaign        : affwp_get_query_vars()['campaign']
			},
			/**
			 * Returns the current time via the performance timing API.
			 *
			 * @since  2.0
			 * @return {int} The current browser client time.
			 */
			printTime: function() {
				return performance.now();
			},
			/**
			 * Halts all JavaScript at the given callable of AFFWP.debug_utility.halt().
			 *
			 * - Useful in implementing step-debuggers, and breakpoints.
			 *
			 * @since  2.0
			 *
			 * @param  {[type]} errorMessage Error message. Optional.
			 *
			 * @return void
			 */
			halt: function( errorMessage = '' ) {
				if ( errorMessage ) {
					console.affwp( errorMessage );
					console.log( '\n' );
				}

				console.affwp( 'Halting at ' + this.printTime() );
				debugger;
			},
			/**
			 * Outputs any available debug data
			 *
			 * @since  2.0
			 *
			 * @param  {string} heading   Optional title heading. Renders before the tabular data output.
			 * @param  {array} debugData  Available debug data.
			 *
			 * @return void
			 */
			output: function( heading = '', debugData ) {
				heading = 'Available debug data:'
				debugData = this.vars;
				console.affwp( heading );
				console.log( '\n' );
				console.table( debugData );
			}
		}

		/**
		 * Defines styles for AffiliateWP-related console output.
		 *
		 * @since 2.0
		 */
		var affwpConsoleStyles = [
		    'background: transparent'
		    , 'border: 6px solid #E34F43'
		    , 'color: black'
		    , 'display: block'
		    , 'line-height: 18px'
		    , 'text-align: left'
		    , 'margin: 4px'
		    , 'font-weight: bold'
		].join( ';' );

		/**
		 * An extension of the console.log prototype.
		 *
		 * Usage:
		 * - Callable with `console.affwp( "The error or message" )`
		 * - Disambiguates the source of the error or message.
		 *
		 * @since  2.0
		 *
		 * @return void
		 */
		console.affwp = function( message ) {
			console.log( '%c' + ' * AffiliateWP: ' + message, affwpConsoleStyles + ' *' );
		};

		AFFWP.debug_utility.output();
	}

});
