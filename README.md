# Atlas Data Mapper

Atlas is an open source data mapper implementation for PHP. The primary focus is to create new models quickly and easily. 

The framework offers the following features:
- Minimal scaffolding required to create new models
- Easily expose business logic query layer
- Reduced application wide ripples from schema changes
- Automatic read/write routing
- Protection against SQL injection attacks
- RDBMS abstraction

## Use cases ##
Persisting a new user:
```
$user = new Model\User\Entity();
$user->set('_email', 'user@domain.com');
$user->set('_enabled', true);

/* Save to db */
$id = $this->model(Model\User::class)->save($user);
```

Fetching an instance of the user entity by primary key:
```
$user = $this->model(Model\User::class)->fetch($id);
```

Access properties using default getters:
```
$timestamp = $user->get('_lastLogin');
```

Access properties using custom getters:
```
$date = $user->getLastLogin('Y-m-d');
```

Persisting changes to the user model using default setters:
```
/* Fetch from db */
$user = $this->model(Model\User::class)->fetch($id);

/* Update entity */
$user->set('_lastLogin', time());

/* Save to db */
$this->model(Model\User::class)->save($user);
```

Persisting changes using custom setters:
```
/* Fetch from db */
$user = $this->model(Model\User::class)->fetch($id);

/* Update entity */
$user->setEmailAddress('user@domain.com');
$user->setEnabled(true);

/* Save to db */
$this->model(Model\User::class)->save($user);
```

Querying user model business layer:
```
$users = $this->model(Model\User::class)->query()
    ->isEnabled(true)
    ->hasLoggedSince(strtotime('5 days ago'))
    ->fetch()->all();
    
foreach ($users as $user) {
    echo $user->get('_email');
}
```

Using named queries for consistent results:
```
$users = $this->model(Model\User::class)->named()
    ->withRecentLogIn()
    -fetch()->all();
```

Optimised queries for simple operations like counts:
```
$count = $this->model(Model\User::class)->named()
    ->withRecentLogIn()
    -fetch()->count();
```

## Implementation ##
Each model consists of a set of classes. Each class extends a super class, to allow
new models to be created with minimal effort. 

### Using Canvas ###
The atlas repo ships with a script to quickly create the scaffolding required for new models.
```
php vendor/fivesqrd/atlas/scripts/Canvas.php User
php vendor/fivesqrd/atlas/scripts/Canvas.php Customer
php vendor/fivesqrd/atlas/scripts/Canvas.php Contact
```

Update the Mapper class with the table details 
```
<?php
namespace Application\Model\User;

class Mapper extends \Atlas\Model\Mapper
{
    protected $_alias = 'u';

    protected $_table = 'users';

    protected $_key = 'id';

    protected $_map = array(
        '_id'        => 'id',
        '_email'     => 'email',
        '_password   => 'password',
        '_lastLogin' => 'last_login'
    );

    protected $_readOnly = array('id');
}
```

Update the Entity class with the mapped properties 
```
<?php
namespace Application\Model\User;

class Entity extends \Atlas\Model\Entity
{
    protected $_id;
    
    protected $_email;
    
    protected $_password;
    
    protected $_lastLogin;
}
```

### File Structure ###
Below is an example what a project with 3 models might look like. For more details, have a look
at these [examples](https://github.com/christianjburger/Atlas/tree/master/examples/Application/)
```
|- Model
   |-- User.php
   |-- User
       |-- Entity.php
       |-- Mapper.php
       |-- Collection.php
       |-- Query.php
       |-- Named.php
       |-- Relation.php
   |-- Customer.php
   |-- Customer
       ...
   |-- Content.php
   |-- Contact
       ...
```

## Install and Setup ##

### Install ###
Via composer
``` 
cd /myproject
php composer.phar require fivesqrd/atlas:3.0 
```

### Canvas ###
Configure canvas to remember project specific settings, such as path to model classes and namespace:
```
cd /myproject
mkdir .atlas
cp vendor/fivesqrd/atlas/scripts/.atlas/config.php .atlas/config.php
vi .atlas/config.php
```

### Config ###
Add the following config to your project:
```
$config = array(
    'read' => array(
        'dsn'      => 'mysql:dbname=testdb;host=127.0.0.1',
        'username' => 'username',
        'password' => 'password',
    ),
    'write' => array(
        'dsn'      => 'mysql:dbname=testdb;host=127.0.0.1',
        'username' => 'username',
        'password' => 'password',
    ),
);
```

### Bootstrap from MVC ###
Atlas can be bootstrapped from within your MVC framework by passing the Proxy class to your controllers/views via a plugin or helper:
```
class MyControllerPlugin
{
    public function model($class) {
        return new Atlas\Proxy(
            new Atlas\Database\Factory($this->_config),
            new Atlas\Database\Resolver($class)
        );
    }
}
```
