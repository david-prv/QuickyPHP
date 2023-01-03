# Establish SQL connection
```php
require __DIR__ . "/../../vendor/autoload.php";

$app = Quicky::create();

Quicky::sql()->prepareConnection("hostName", "userName", "userPass");
Quicky::sql()->useDB("databaseName");
// Optional: Quicky::sql()->useTable("tableName");

if (!Quicky::sql()->connect()) {
    die("SQL connection has failed!");
}

Quicky::sql()->disconnect();

$app->run();
```