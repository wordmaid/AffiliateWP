### Payout

Namespace: AffWP\Affiliate

Extends: `\AffWP\Base_Object`

----


$payout_id
`public` `Payout ID.` 


access
`public` 
since
`1.9` 
var

int


$affiliate_id
`public` `Affiliate ID.` 


access
`public` 
since
`1.9` 
var

int


$referrals
`array()` `public` `IDs for referrals associated with the payout.` 


access
`public` 
since
`1.9` 
var

array


$amount
`public` `Payout amount.` 


access
`public` 
since
`1.9` 
var

float


$payout_method
`public` `Payout method.` 


access
`public` 
since
`1.9` 
var

string


$status
`public` `Payout status.` 


access
`public` 
since
`1.9` 
var

string


$date
`public` `Payout date.` 


access
`public` 
since
`1.9` 
var

string


$cache_token
`'affwp_payouts'` `1` `public` `Token to use for generating cache keys.` 


access
`public` 
since
`1.9` 
var

string


static

see
`AffWP\Base_Object::get_cache_key()` 
$db_group
`'affiliates:payouts'` `1` `public` `Database group.` 
`<p>Used in \AffWP\Base_Object for accessing the affiliates DB class methods.</p>` 

since
`1.9` 
access
`public` 
var

string


$object_type
`'payouts'` `1` `public` `Object type.` 
`<p>Used as the cache group and for accessing object DB classes in the parent.</p>` 

access
`public` 
since
`1.9` 
var

string


static
