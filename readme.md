PHP Salesforce client
=====================

Supported technologies:

    - rest
        - oauth2 grant type: password.

Please, contribute to support other one.

Usage
-----

```php
use WakeOnWeb\SalesforceClient\REST;
use WakeOnWeb\SalesforceClient\ClientInterface;

$client = new REST\Client(
    new REST\Gateway('https://cs81.salesforce.com', '41.0'),
    new REST\GrantType\PasswordStrategy(
        'consumer_key',
        'consumer_secret',
        'login',
        'password',
        'security_token'
    )
);
```
Available exception -------------------

- DuplicatesDetectedException
- EntityIsDeletedException (when try to delete an entity already deleted)
- NotFoundException (when an object cannot be found)
- ...

Get object
-----------

```php
try {
    $salesforceObject = $client->getObject( 'Account', '1337ID')); // all fields
} catch (\WakeOnWeb\SalesforceClient\Exception\NotFoundException) {
    // this object does not exist, do a specifig thing.
}

//$salesforceObject->getAttributes();
//$salesforceObject->getFields();

//$client->getObject( 'Account', '1337ID', ['Name', 'OwnerId', 'CreatedAt'] )); // specific fields
```

Create object 
-----------

```php
// creation will be a SalesforceObjectCreationObject
$creation = $client->createObject( 'Account', ['name' => 'Chuck Norrs'] );
// $creation->getId();
// $creation->isSuccess();
// $creation->getErrors();
// $creation->getWarnings();
```

Edit object 
-----------

```php
$client->patchObject( 'Account', '1337ID', ['name' => 'Chuck Norris'] ));
```

Delete object 
-----------

```
$client->deleteObject( 'Account', '1337ID'));
```

SOQL
----

```php
// creation will be a SalesforceObjectCreationObjectResults
$client->searchSOQL('SELECT name from Account'); // NOT_ALL by default.
$client->searchSOQL('SELECT name from Account', ClientInterface::ALL);
// $creation->getTotalSize();
// $creation->isDone();
// $creation->getRecords();
```

Other
-----

```php
$client->getAvailableResources();
$client->getAllObjects();
$client->describeObjectMetadata('Account');
```
