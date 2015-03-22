# PHP Etherpad Lite Client
This PHP Etherpad Lite class allows you to easily interact with Etherpad Lite API with PHP.  
Etherpad Lite is a collaborative editor provided by the Etherpad Foundation (http://etherpad.org)

## Basic Usage

You only need to include the 'etherpad-lite-client.php' file in your project. All other files
in the project are for supporting the generation of the client.

```php
<?php
include 'etherpad-lite-client.php';
$instance = new EtherpadLiteClient('EtherpadFTW', 'http://beta.etherpad.org/api');
$revisionCount = $instance->getRevisionsCount('testPad');
$revisionCount = $revisionCount->revisions;
echo "Pad has $revisionCount revisions";
```

# Running The Tests
The full-stack tests can be run by running `make test`.
 
The test suite makes the following assumptions:

* A copy of Etherpad is running at http://localhost:9001
* The data in the running instance of Etherpad can be destroyed
* The APIKey for the running instance is 'dcf118bfc58cc69cdf3ae870071f97149924f5f5a9a4a552fd2921b40830aaae'
* PHPUnit has been installed with [Composer](https://getcomposer.org/) (run `make dev-deps`)

# License

Apache License

# Other Stuff

The Etherpad Foundation also provides a jQuery plugin for Etherpad Lite.  
This can be found at http://etherpad.org/2011/08/14/etherpad-lite-jquery-plugin/
