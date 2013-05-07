# Kayako Staff REST API Wrapper Class

by Matt Goodwin - www.rdi.co.uk

License: MIT

## About
-------

This is a PHP wrapper class for interacting with Kayakos' Staff API

Requirements
- PHP 5.3
- Testing with Kayako 4 (unsure if it works on versions before 4?!)

Documentation can be found for the Kayako Staff API at http://wiki.kayako.com/display/DEV/Kayako+Staff+API

## Installation

### If you are using Composer

In your composer file, define a repositories block containing the following:

```json
"repositories": [
    {
        "type": "package",
        "package": {
            "name": "rdimatt/kayako-staff-rest",
            "version": "dev-master",
            "autoload": {
                "psr-0": { 
                    "Kayako": ""
                 }
            },
            "source": {
                "url": "git@github.com:rdimatt/Kayako-STAFF-REST-API-Wrapper-Class.git",
                "type": "git",
                "reference": "master"
            }
        }
    }
],
```

**To Do: add to packagist**

## Usage Example

```php

<?php

$kayakoAPI = new Kayako\Staff_API('myKayakoURL', 'myUsername', 'myPassword');

$ticket = $kayakoAPI->find(/* Ticket ID*/);

if ($ticket != null) {
	print $ticket->subject;
}
else {
	echo 'Ticket not found';
}

```

### Get the top 10 tickets by a specific email address 

```php

$tickets = $kayakoAPI->search('email@mycompany.com', array('email' => 1), array('start' => 0, 'limit' => 10));

if (isset($tickets->ticket)) {
	foreach ($tickets->ticket as $ticket) {
	
		print $ticket->attributes()->id . ' - ';
		print $ticket->subject . ' - ';
		print $ticket->email . ' - ';
		print date('d/m/Y', (string)$ticket->lastactivity) . ' - ';
		print $ticket->statustitle . ' - ';
		print '<br />';
		
	}
}
else {
	var_dump('No tickets');
}

```
