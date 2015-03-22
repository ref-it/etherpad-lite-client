# PHP Etherpad Lite Client
This PHP Etherpad Lite class allows you to easily interact with Etherpad Lite API with PHP.  
Etherpad Lite is a collaborative editor provided by the Etherpad Foundation (http://etherpad.org)

## Basic usage

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

# License

Apache License

# Other stuff

The Etherpad Foundation also provides a jQuery plugin for Etherpad Lite.  
This can be found at http://etherpad.org/2011/08/14/etherpad-lite-jquery-plugin/
