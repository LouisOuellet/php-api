![GitHub repo logo](/dist/img/logo.png)

# phpAPI
![License](https://img.shields.io/github/license/LouisOuellet/php-api?style=for-the-badge)
![GitHub repo size](https://img.shields.io/github/repo-size/LouisOuellet/php-api?style=for-the-badge&logo=github)
![GitHub top language](https://img.shields.io/github/languages/top/LouisOuellet/php-api?style=for-the-badge)
![Version](https://img.shields.io/github/v/release/LouisOuellet/php-api?label=Version&style=for-the-badge)

phpAPI is an easy to use REST API for php applications

## Features
 - REST API

## Why you might need it
If you are looking for an easy start for your PHP REST API. Then this PHP Class is for you.

## Can I use this?
Sure!

## License
This software is distributed under the [GNU General Public License v3.0](https://www.gnu.org/licenses/gpl-3.0.en.html) license. Please read [LICENSE](LICENSE) for information on the software availability and distribution.

## Requirements
PHP >= 8.0
* MySQL or MariaDB

### SQL Requirements
To support authentication in your application, you will need at least one table called users. Since phpAUTH is packed with phpDB, you can create the table like this:
```php

//Import Database class into the global namespace
//These must be at the top of your script, not inside a function
use LaswitchTech\phpDB\Database;

//Load Composer's autoloader
require 'vendor/autoload.php';

//Initiate Database
$phpDB = new Database("localhost","demo","demo","demo");

//Create the users table
$phpDB->create('users',[
  'id' => [
    'type' => 'BIGINT(10)',
    'extra' => ['UNSIGNED','AUTO_INCREMENT','PRIMARY KEY']
  ],
  'username' => [
    'type' => 'VARCHAR(60)',
    'extra' => ['NOT NULL','UNIQUE']
  ],
  'password' => [
    'type' => 'VARCHAR(100)',
    'extra' => ['NOT NULL']
  ],
  'token' => [
    'type' => 'VARCHAR(100)',
    'extra' => ['NOT NULL','UNIQUE']
  ]
]);

//Optionally you may want to add a type column if you want to support multiple Authentication Back-Ends like LDAP, SMTP, IMAP, etc.
$phpDB->alter('users',[
  'type' => [
    'action' => 'ADD',
    'type' => 'VARCHAR(10)',
    'extra' => ['NOT NULL','DEFAULT "SQL"']
  ]
]);

//Other Suggestions
$phpDB->alter('users',[
  'created' => [
    'action' => 'ADD',
    'type' => 'DATETIME',
    'extra' => ['DEFAULT CURRENT_TIMESTAMP']
  ],
  'modified' => [
    'action' => 'ADD',
    'type' => 'DATETIME',
    'extra' => ['DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP']
  ]
]);

//If you enable Roles, you will need a roles table and a roles column to your users table.
$phpDB->create('roles',[
  'id' => [
    'type' => 'BIGINT(10)',
    'extra' => ['UNSIGNED','AUTO_INCREMENT','PRIMARY KEY']
  ],
  'name' => [
    'type' => 'VARCHAR(60)',
    'extra' => ['NOT NULL','UNIQUE']
  ],
  'permissions' => [
    'type' => 'LONGTEXT',
    'extra' => ['NULL']
  ],
  'members' => [
    'type' => 'LONGTEXT',
    'extra' => ['NULL']
  ]
]);

//Optionally you may want to add a roles column if you want to quickly list roles memberships.

$phpDB->alter('users',[
  'roles' => [
    'action' => 'ADD',
    'type' => 'LONGTEXT',
    'extra' => ['NULL']
  ]
]);

//Other Suggestions
$phpDB->alter('roles',[
  'created' => [
    'action' => 'ADD',
    'type' => 'DATETIME',
    'extra' => ['DEFAULT CURRENT_TIMESTAMP']
  ],
  'modified' => [
    'action' => 'ADD',
    'type' => 'DATETIME',
    'extra' => ['DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP']
  ]
]);

//Create user
$UserID = $phpDB->insert("INSERT INTO users (username, password, token) VALUES (?,?,?)", ["user1",password_hash("pass1", PASSWORD_DEFAULT),hash("sha256", "pass1", false)]);

//Create role
$RoleID = $phpDB->insert("INSERT INTO roles (name, permissions, members) VALUES (?,?,?)", ["users",json_encode(["users/list" => 1],JSON_UNESCAPED_SLASHES),json_encode([["users" => $UserID]],JSON_UNESCAPED_SLASHES)]);

//Update user
$phpDB->update("UPDATE users SET roles = ? WHERE id = ?", [json_encode([["roles" => $RoleID]],JSON_UNESCAPED_SLASHES),$UserID]);
```

## Security
Please disclose any vulnerabilities found responsibly – report security issues to the maintainers privately.

## Installation
Using Composer:
```sh
composer require laswitchtech/php-api
```

## How do I use it?
In this documentations, we will use a table called users for our examples.

### Skeleton
Let's start with the skeleton of your API project directory.

```sh
├── api.php
├── config
│   └── config.php
├── Controller
│   └── UserController.php
└── Model
    └── UserModel.php
```

* api.php: The api fileis the entry-point of our application. It will initiate the controller being called in our application.
* config/config.php: The config file holds the configuration information of our API. Mainly, it will hold the database credentials. But you could use it to store other configurations.
* Controller/: This directory will contain all of your controllers.
* Controller/UserController.php: the User controller file which holds the necessary application code to entertain REST API calls. Mainly the methods that can be called.
* Model/: This directory will contain all of your models.
* Model/UserModel.php: the User model file which implements the necessary methods to interact with the users table in the MySQL database.

### Models
Model files implements the necessary methods to interact with a table in the MySQL database. These model files needs to extend the Database class in order to access the database.

#### Naming convention
The name of your model file should start with a capital character and be followed by ```Model.php```.  If not, the bootstrap will not load it.
The class name in your Model files should match the name of the model file.

#### Example
```php

//Import BaseModel class into the global namespace
use LaswitchTech\phpAPI\BaseModel;

class UserModel extends BaseModel {
  public function getUsers($limit) {
    return $this->select("SELECT * FROM users ORDER BY id ASC LIMIT ?", ["i", $limit]);
  }
}
```

### Controllers
Controller files holds the necessary application code to entertain REST API calls. Mainly the methods that can be called. These controller files needs to extend the BaseController class in order to access the basic methods.

#### Naming convention
The name of your controller file should start with a capital character and be followed by ```Controller.php```.  If not, the bootstrap will not load it. The class name in your Model files should match the name of the model file.

Finally, callable methods need to end with ```Action```.

#### Example
```php

//Import BaseController class into the global namespace
use LaswitchTech\phpAPI\BaseController;

//Import Auth class into the global namespace
use LaswitchTech\phpAUTH\Auth;

class UserController extends BaseController {

  public function listAction() {
    $Auth = new Auth();
    $Auth->isAuthorized("users/list");
    $strErrorDesc = '';
    $requestMethod = $_SERVER["REQUEST_METHOD"];
    $arrQueryStringParams = $this->getQueryStringParams();
    if (strtoupper($requestMethod) == 'GET') {
      try {
        $userModel = new UserModel();
        $intLimit = 10;
        if (isset($arrQueryStringParams['limit']) && $arrQueryStringParams['limit']) {
          $intLimit = $arrQueryStringParams['limit'];
        }
        $arrUsers = $userModel->getUsers($intLimit);
        $responseData = json_encode($arrUsers);
      } catch (Error $e) {
        $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
        $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
      }
    } else {
      $strErrorDesc = 'Method not supported';
      $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
    }
    if (!$strErrorDesc) {
      $this->sendOutput(
        $responseData,
        array('Content-Type: application/json', 'HTTP/1.1 200 OK')
      );
    } else {
      $this->sendOutput(json_encode(array('error' => $strErrorDesc)),
        array('Content-Type: application/json', $strErrorHeader)
      );
    }
  }
}
```

### Configurations
The config file holds the configuration information of our API. Mainly, it will hold the database credentials. But you could use it to store other configurations. The configuration file must be stored in config/config.php. As this file is already being loaded in the bootstrap.

#### Example

```php
// Auth Configuration Information
define("AUTH_F_TYPE", "BEARER");
define("AUTH_B_TYPE", "SQL");
define("AUTH_ROLES", true);
define("AUTH_GROUPS", false);
define("AUTH_RETURN", "HEADER");

//MySQL Configuration Information
define("DB_HOST", "localhost");
define("DB_USERNAME", "demo");
define("DB_PASSWORD", "demo");
define("DB_DATABASE_NAME", "demo");
```

### API
The api file is the entry-point of our application. It will initiate the controller being called in our application. The file itself can be named any way you want. As long as you point your API calls to it. In our example we use api.php. This name is useful because it allows you to build a front-end using the index.php file and it also makes it obvious as the URL of your API.

#### Example

```php

//Import API class into the global namespace
//These must be at the top of your script, not inside a function
use LaswitchTech\phpAPI\phpAPI;

//Load Composer's autoloader
require 'vendor/autoload.php';

new phpAPI();
```

### Calling the API
Once you have setup your first controller and model, you can start calling your api.

#### Example

##### GET
```sh
GET /api.php/user/list?limit=2 HTTP/1.1
Authorization: Bearer cGFzczE=
Host: phpapi.local
User-Agent: HTTPie
```

##### Output
```json
[
  {
    "id": 1,
    "username": "user1",
    "email": "user1@domain.com",
    "status": 0
  },
  {
    "id": 2,
    "username": "user2",
    "email": "user2@domain.com",
    "status": 1
  },
  {
    "id": 3,
    "username": "user3",
    "email": "user3@domain.com",
    "status": 1
  },
  {
    "id": 4,
    "username": "user4",
    "email": "user4@domain.com",
    "status": 0
  }
]
```
