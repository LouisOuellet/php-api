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

Let’s try to understand the project structure.

* api.php: the entry-point of our application. It will act as a front-controller of our application.
* config/config.php: holds the configuration information of our API. Mainly, it will hold the database credentials. But you could use it to store other configurations.
* Controller/: This directory will contain all of your controllers.
* Controller/UserController.php: the User controller file which holds the necessary application code to entertain REST API calls. Mainly the methods that can be called.
* Model/: This directory will contain all of your models.
* Model/UserModel.php: the User model file which implements the necessary methods to interact with the users table in the MySQL database.

### Models
Model files implements the necessary methods to interact with a table in the MySQL database.

#### Example

```php

//Import Database class into the global namespace
use LaswitchTech\phpAPI\Database;

class UserModel extends Database {
  public function getUsers($limit) {
    return $this->select("SELECT * FROM users ORDER BY id ASC LIMIT ?", ["i", $limit]);
  }
}
```

### Controllers
Controller files holds the necessary application code to entertain REST API calls. Mainly the methods that can be called.

#### Example

```php

//Import BaseController class into the global namespace
use LaswitchTech\phpAPI\BaseController;

class UserController extends BaseController {

  public function listAction() {
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
