# UUID Class
Koseven comes shipped with a UUID class for creating and validating RFC 4122 compliant Universally Unique Identifiers (UUID)
 version 3, 4 and 5.
 
## Validate UUID
To validate a new UUID, you can do the following:

	$uuid = '00000000-0000-0000-0000-000000000000';
	if (UUID::valid($uuid)) 
	{
	    echo "Valid UUID";
	}
	
## Convert UUID
To convert a UUID from `string` to `bin` simply call:

    $uuid = '00000000-0000-0000-0000-000000000000';
    $binary = UUID::bin($uuid); // Done
    
to convert it back to `string` simply do:

    $string = UUID::str($binary);
    
    
## Generating UUID
Koseven can generate three different versions of UUIDs (v3, v4, v5).    
v3 and v5 require namespaces (DNS, URL, OID, X500, NIL) and a Name. All namespaces are
already implemented in Koseven and can be accessed via `UUID` class constants (e.g. `UUID::DNS`).

    // Generating a v3 UUID
    $v3 = UUID::v3(UUID::DNS, 'example');
    
    // Generating a v4 UUID
    $v4 = UUID::v4();
    
    // Generating a v5 UUID
    $v5 = UUID::v5(UUID::URL, 'example');
