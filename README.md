![GitHub repo logo](/dist/img/logo.png)

# phpAPI
![License](https://img.shields.io/github/license/LouisOuellet/php-api?style=for-the-badge)
![GitHub repo size](https://img.shields.io/github/repo-size/LouisOuellet/php-api?style=for-the-badge&logo=github)
![GitHub top language](https://img.shields.io/github/languages/top/LouisOuellet/php-api?style=for-the-badge)
![Version](https://img.shields.io/github/v/release/LouisOuellet/php-api?label=Version&style=for-the-badge)

## Features
 - REST API

## Why you might need it
If you are looking for an easy start for your PHP REST API. Then this PHP Class is for you.

## Can I use this?
Sure!

## License
This software is distributed under the [GNU General Public License v3.0](https://www.gnu.org/licenses/gpl-3.0.en.html) license. Please read [LICENSE](LICENSE) for information on the software availability and distribution.

## Requirements
* PHP >= 8.0
* MySQL or MariaDB

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
│   └── api.cfg
├── Controller
│   └── UserController.php
└── Model
    └── UserModel.php
```

* api.php: The api file is the entry-point of our application. It will initiate the controller being called in our application.
* config/api.cfg: The config file holds the configuration information of our API. Mainly, it will hold the database credentials. But you could use it to store other configurations.
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
The name of your controller file should start with a capital character and be followed by ```Controller.php```.  If not, the bootstrap will not load it. The class name in your Controller files should match the name of the controller file.

Finally, callable methods need to end with ```Action```.

#### Example
```php

//Import BaseController class into the global namespace
use LaswitchTech\phpAPI\BaseController;

class UserController extends BaseController {

  public function __construct($Auth){

    // Set the controller Authentication Policy
    $this->Public = true; // Set to false to require authentication

    // Set the controller Authorization Policy
    $this->Permission = false; // Set to true to require a permission for the namespace used. Ex: namespace>/user/list
    $this->Level = 1; // Set the permission level required

    // Call the parent constructor
    parent::__construct($Auth);
  }

  public function listAction() {
    try {

      // Namespace: /user/list

      // Check the request method
      if($this->Method !== 'GET'){
        throw new Error('Invalid request method.');
      }

      // Initialize the user model
      $UserModel = new UserModel();

      // Configure default limit
      $Limit = 25;

      // Check if the limit is set
      if($this->getQueryStringParams('limit')){
        $Limit = intval($this->getQueryStringParams('limit'));
      }

      // Get the users
      $Users = $UserModel->getUsers($Limit);

      // Check if the users were found
      if(count($Users) <= 0){
        throw new Error('Users not found.');
      }
      
      // Send the output
      $this->output(
        $Users,
        array('Content-Type: application/json', 'HTTP/1.1 200 OK')
      );
    } catch (Error $e) {

      // Set the error
      $this->Error = $e->getMessage();

      // Log the error
      $this->Logger->error($e->getMessage());

      // Send the output
      $this->output(
        array('error' => $this->Error . ' - Something went wrong! Please contact support.'),
        array('Content-Type: application/json', 'HTTP/1.1 500 Internal Server Error'),
      );
    }
  }
}
```

### Configurations
The config file holds the configuration information of our API. Mainly, it will hold the database credentials. But you could use it to store other configurations. The configuration file must be stored in config/config.php. As this file is already being loaded in the bootstrap.

#### Example

```json
{
    "sql": {
        "host": "localhost",
        "database": "demo3",
        "username": "demo",
        "password": "demo"
    }
}
```

### API
The api file is the entry-point of our application. It will initiate the controller being called in our application. The file itself can be named any way you want. As long as you point your API calls to it. In our example we use api.php. This name is useful because it allows you to build a front-end using the index.php file and it also makes it obvious as the URL of your API.

#### Example

```php
// Initiate Session
session_start();

// These must be at the top of your script, not inside a function
use LaswitchTech\phpAPI\phpAPI;

// Load Composer's autoloader
require 'vendor/autoload.php';

// Initiate phpAPI
new phpAPI();
```

### Calling the API
Once you have setup your first controller and model, you can start calling your api.

### JavaScript Implementation
phpAPI comes packed with a JavaScript implementation. The class is available in /vendor/laswitchtech/php-api/dist/js/phpAPI.js.

### Examples
For more example, look into the [example](example) folder.

### Installer Example
```php
// Initiate Session
session_start();

// These must be at the top of your script, not inside a function
use LaswitchTech\phpLogger\phpLogger;
use LaswitchTech\phpSMS\phpSMS;
use LaswitchTech\SMTP\phpSMTP;
use LaswitchTech\phpDB\Database;
use LaswitchTech\phpAUTH\phpAUTH;

// Load Composer's autoloader
require 'vendor/autoload.php';

// Initiate phpLogger
$phpLogger = new phpLogger();

// Configure phpLogger
$phpLogger->config("level",0); // Set Logging Level

// Initiate phpSMS
$phpSMS = new phpSMS();

// Configure phpSMS
$phpSMS->config('provider','twilio')
       ->config('sid', 'your_account_sid')
       ->config('token', 'your_auth_token')
       ->config('phone', 'your_twilio_phone_number');

// Initiate phpDB
$phpDB = new Database();

// Configure phpDB
$phpDB->config("host","localhost")
      ->config("username","demo")
      ->config("password","demo")
      ->config("database","demo3");

// Initiate phpSMTP
$phpSMTP = new phpSMTP();

// Configure phpSMTP
$phpSMTP->config("username","username@domain.com")
        ->config("password","*******************")
        ->config("host","smtp.domain.com")
        ->config("port",465)
        ->config("encryption","ssl");

// Construct Hostnames
$Hostnames = ["localhost","::1","127.0.0.1"];
if(isset($_SERVER['SERVER_NAME']) && !in_array($_SERVER['SERVER_NAME'],$Hostnames)){
  $Hostnames[] = $_SERVER['SERVER_NAME'];
}
if(isset($_SERVER['HTTP_HOST']) && !in_array($_SERVER['HTTP_HOST'],$Hostnames)){
  $Hostnames[] = $_SERVER['HTTP_HOST'];
}

// Initiate phpAUTH
$phpAUTH = new phpAUTH();

// Configure phpAUTH
$phpAUTH->config("hostnames",$Hostnames)
        ->config("basic",false) // Enable/Disable Basic Authentication
        ->config("bearer",true) // Enable/Disable Bearer Token Authentication
        ->config("request",false) // Enable/Disable Request Authentication
        ->config("cookie",false) // Enable/Disable Cookie Authentication
        ->config("session",false) // Enable/Disable Session Authentication
        ->config("2fa",false) // Enable/Disable 2-Factor Authentication
        ->config("maxAttempts",5) // Max amount of authentication attempts per windowAttempts
        ->config("maxRequests",1000) // Max amount of API request per windowRequests
        ->config("lockoutDuration",1800) // 30 mins
        ->config("windowAttempts",100) // 100 seconds
        ->config("windowRequests",60) // 60 seconds
        ->config("window2FA",60) // 60 seconds
        ->config("windowVerification",2592000) // 30 Days
        ->init();

// Install phpAUTH
$Installer = $phpAUTH->install();

// Create a User
$User = $Installer->create("api",["username" => "username@domain.com"]);

// Activate User
$User->activate();

// Verify User
$User->verify();

// Initiate phpConfigurator
$Configurator = new phpConfigurator('account');

// Save Account for Testing
$Configurator->set('account','url',"https://{$Hostname}/api.php")
             ->set('account','token',$User->get('username').":".$User->getToken());
```