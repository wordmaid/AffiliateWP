### Consumer

Namespace: AffWP\REST

Extends: `\AffWP\Base_Object`

----


$consumer_id
`public` `API consumer ID.` 


access
`public` 
since
`1.9` 
var

int


$user_id
`public` `API consumer user ID.` 


access
`public` 
since
`1.9` 
var

int


$token
`''` `public` `API consumer token.` 


access
`public` 
since
`1.9` 
var

string


$public_key
`''` `public` `API consumer public key.` 


access
`public` 
since
`1.9` 
var

string


$secret_key
`''` `public` `API consumer secret key.` 


access
`public` 
since
`1.9` 
var

string


$cache_token
`'affwp_consumers'` `1` `public` `Token to use for generating cache keys.` 


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
`'REST:consumers'` `1` `public` `Database group.` 
`<p>Used in \AffWP\Base_Object for accessing the consumers DB class methods.</p> <p>Note the use of primary and secondary db groups separated with a colon.</p>` 

access
`public` 
since
`1.9` 
var

string


static

$object_type
`'consumer'` `1` `public` `Object type.` 
`<p>Used as the cache group and for accessing object DB classes in the parent.</p>` 

access
`public` 
since
`1.9` 
var

string


static
