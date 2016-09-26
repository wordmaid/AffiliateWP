### Meta_Box

Namespace: AffWP\Admin


$meta_box_id
`public` `The ID of the meta box. Must be unique.` 


abstract

access
`public` 
var
`The ID of the meta box` `$meta_box_id` 
since
`1.9` 
$meta_box_name
`public` `The name of the meta box.` 
`<p>This should very briefly describe the contents of the meta box.</p>` 

abstract

access
`public` 
var
`The name of the meta box` `$meta_box_name` 
since
`1.9` 
$affwp_screen
`array('toplevel_page_affiliate-wp', 'affiliates_page_affiliate-wp-affiliates', 'affiliates_page_affiliate-wp-referrals', 'affiliates_page_affiliate-wp-visits', 'affiliates_page_affiliate-wp-creatives', 'affiliates_page_affiliate-wp-reports', 'affiliates_page_affiliate-wp-tools', 'affiliates_page_affiliate-wp-settings', 'affiliates_page_affiliate-wp-add-ons')` `private` `The AffiliateWP screen on which to show the meta box.` 
`<p>Defaults to affiliates_page_affiliate-wp-reports, the AffiliateWP Reports Overview tab page.</p> <p>The uri of this page is: admin.php?page=affiliate-wp-reports.</p>` 

access
`private` 
var
`The screen ID of the page on which to display this meta box.` `$affwp_screen` 
since
`1.9` 
$context
`'primary'` `public` `The position in which the meta box will be loaded.` 
`<p>AffiliateWP uses custom meta box contexts. These contexts are listed below.</p> <p>'primary':   Loads in the left column. 'secondary': Loads in the center column. 'tertiary':  Loads in the right column.</p> <p>All columns will collapse as needed on smaller screens, as WordPress core meta boxes are in use.</p>` 

access
`public` 
var
`$context` 
since
`1.9` 
$action
`'affwp_overview_meta_boxes'` `public` `The action on which the meta box will be loaded.` 
`<p>AffiliateWP uses custom meta box actions. These contexts are listed below:</p> <p>'affwp_overview_meta_boxes': Loads on the Overview page.</p>` 

access
`public` 
var
`$action` 
since
`1.9` 
$display_callback
`public` `Display callback for the meta box.` 
`<p>Normal instantiation uses the content() method for display.</p>` 

access
`public` 
since
`1.9` 
var

string


$extra_args
`array()` `public` `Additional arguments to pass to the meta box display callback.` 


access
`public` 
since
`1.9` 
var

array

