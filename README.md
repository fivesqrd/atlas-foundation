# Atlas Data Mapper

Atlas is a simple data mapper implementation for PHP. The primary focus is to create new models quickly and easily. 

The framework offers the following features:
- Minimal construction required (new models are quickly ready to be used)
- Easily create business logic query layer
- Reduced schema chang ripples
- Automatic read/write routing
- Protection against SQL injection attacks
- RDBMS abstraction

## Use cases ##
Persisting a new user:
```
$user = Model\User();
$user->set('_email', 'user@domain.com');
$user->set('_enabled', true);
$id = $this->model(Model\User::class)->save($user);
```

Fetching an instance of the user model by primary key:
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
$user = $this->model(Model\User::class)->fetch($id);
$user->set('_lastLogin', time());
$this->model(Model\User::class)->save($user);
```

Persisting changes using custom setters:
```
$user = $this->model(Model\User::class)->fetch($id);
$user->setEmailAddress('user@domain.com');
$user->setEnabled(true);
$this->model(Model\User::class)->save($user);
```

Querying user model business layer:
```
$users = $this->model(Model\User::class)->query()
    ->isEnabled(true)
    ->hasLoggedSince(strtotime('5 days ago'))
    ->fetch()->all();
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

### Install ###
Via composer
``` 
php composer.phar require fivesqrd/atlas:3.0 
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

### Config ###
Atlas can automatically split read and write statements. Simply provide the database connection configs as below.
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
Atlas can be bootstrapped in your MVC framework by passing the Proxy class to your controllers/views via a plugin or helper:
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
