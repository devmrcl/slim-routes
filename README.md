# SlimRoutes

PHP8 attributes for easier and cleaner routing in [Slim](https://www.slimframework.com/).

# Table of contents

* [Installation](#installation)
* [Usage](#usage)
    * [Attributes](#attributes)
    * [Configuration](#configuration)
* [Further Information](#further-information)
    * [Route patterns](#route-patterns)
    * [Middleware](#middleware)
    * [HTTP methods](#http-methods)
    * [Grouping routes](#grouping-routes)
    * [API versioning](#api-versioning)

# Installation

SlimRoutes requires >= PHP 8.1

```
composer require mrcl/slim-routes
```

# Usage

## Attributes

### Controller

[`#[Controller]`](src/Attribute/Controller.php) marks a controller class as routable.

```php
use Mrcl\SlimRoutes\Attribute\Controller;

#[Controller]
class UserController
{...}
```

#### Controller parameters

| Parameter  | Description                                                                     |
|------------|---------------------------------------------------------------------------------|
| pattern    | Prefixes all routes' pattern                                                    |
| middleware | Adds middleware to all routes                                                   |
| version    | Specify the basic API version(s) for all routes<br/>Can get overriden by route  |
| groupId    | Group all routes by specific group configuration<br/>Can get overriden by route |                                                                                 |           |

### Route

[`#[Route]`](src/Attribute/Route.php) maps a route to an action.  
By default, it uses the `GET` method.

```php
use Mrcl\SlimRoutes\Attribute\Route;

#[Route('users/{id}')]
class ViewUserAction
{
    public function __invoke
}

Routes:
-> GET /users/{id}
```

```php
use Mrcl\SlimRoutes\Attribute\Controller;
use Mrcl\SlimRoutes\Attribute\Route;
use Mrcl\SlimRoutes\Routing\HttpMethod;

#[Controller('users')]
class UserController
{
    #[Route('{id}')]
    public function getUser

    #[Route(method: HttpMethod::POST)]
    public function addUser
}

Routes:
-> GET  /users/{id}
-> POST /users
```

#### Route parameters

| Parameter  | Description                        |
|------------|------------------------------------|
| pattern    | Route pattern                      |
| method     | HTTP method(s)                     |
| middleware | Route middleware                   |
| version    | Specify the route's API version(s) |
| groupId    | Use specific group configuration   | 
| priority   | Prioritize route                   |
| name       | Unique route name                  |

## Configuration

### Minimal configuration

For a minimal configuration you only need to pass an instance of the `Slim/App` (or any other class which
implements `Slim\Interfaces\RouteCollectorInterface` or `Slim\Interfaces\RouteCollectorProxyInterface`) and directories
where your action/controller classes are located.

```php
use Mrcl\SlimRoutes\SlimRoutes;
use Slim\Factory\AppFactory;

$app = AppFactory::create();
$sr = new SlimRoutes(
    $app,
    __DIR__ . '/../src'
);
$sr->registerRoutes();
```

> Note: `#[Controller(version, groupId)]` and `#[Route(version. groupId, priority)]` do not work without configuration.

### Configuration options

#### Caching

Recommended for production usage.

```php
$sr->enableCache($cacheFile)
```

#### Additional directory

Add another directory to search for routable classes.

```php
$sr->addDirectory($directory)
```

#### File name pattern

You can minimize the amount of scanned classes by setting a file name/extension pattern (regex).  
Recommended if you have a lot of other classes in your folders and/or you do not use caching (e.g. in development).

```php
$sr->setFileNamePattern($fileNamePattern, $fileExtensionPattern = 'php')
```

Example:   
All wanted file names end with 'Controller' and file extensions are 'php' or 'PHP'

```php 
Regex: /^(.+Controller)\.(php|PHP)$/
setFileNamePattern('.+Controller', 'php|PHP')
```

Options for `*Action.php` and `*Controller.php` files are ready to use.

```php
$sr->useActionFilePattern($fileExtensionPattern = 'php')
// OR
$sr->useControllerFilePattern($fileExtensionPattern = 'php')
```

#### Groups

```php
$sr->addGroup($groupConfiguration)
```

For more details, see [Advanced grouping with groups](#advanced-grouping-with-groups).

#### API version

```php
$sr->addApiVersion($versionConfiguration)
```

For more details, see [API versioning](#api-versioning).

#### Route priority

If you want to prioritize your routes, you need to enable it first. Predefined constants are available
in [`RoutePriority`](src/Routing/RoutePriority.php).

```php
$sr->enableRoutePrioritization($defaultPriority = RoutePriority::NORMAL)
```

If you plan to use your own range of priorities, you can pass a `defaultPriority`. Priorities are simple integers, the
lower the number the higher the priority (better position in the route stack).

#### Change mapping of _ANY_

The special `HttpMethod::ANY` maps "any" HTTP method to your route.

By default, it maps to `GET`, `POST`, `PUT`, `PATCH`, `DELETE` and `OPTIONS` (like in Slim).

You can configure it to your own needs:

```php
$sr->setAnyHttpMethods($methods, $override = true)
```

# Further information

## Route patterns

### Leading slash

You may have noticed that all route patterns in this documentation do not use a leading `/`. They are automatically
added on route generation.  
If you prefer to use leading slashes, just use them.

### Pattern order

Depending on Slim configuration a base path or group can be the first pattern element.

`[/Slim][/ApiVersion][[/Parent...Group]/Group][/Controller]/Route`

## Middleware

Unlike in Slim added middleware runs in the order you set it.

```php
#[Controller('users', [FirstMiddleware::class, SecondMiddleware::class])]
class UserController {
    #[Route(middleware: [ThirdMiddleware::class, FourthMiddleware::class])] 
    public function getAllUsers
}
```

> Request > FirstMiddleware > SecondMiddleware > ThirdMiddleware > FourthMiddleware  
`UserController:getAllUsers`  
> FourthMiddleware > ThirdMiddleware > SecondMiddleware > FirstMiddleware > Response`

### Middleware order

Added middleware on Slim level is always the first to be run.

`[SlimMiddleware][ApiVersionMiddleware][[ParentGroup...Middleware]GroupMiddleware][ControllerMiddleware][RouteMiddleware]`

## HTTP methods

SlimRoutes comes with [predefined constants](src/Routing/HttpMethod.php) of the most used HTTP methods.   
Also see, [Change mapping of ANY](#change-mapping-of-any).

## Grouping routes

### Controller pattern

By using the `#[Controller]` attribute you have the option to pass a `pattern` which prefixes all routes within the
class.

```php
#[Controller('users')]
class UserController {
    #[Route] 
    public function getAllUsers
    
    #[Route('{id}')]
    public function getUser
}

Routes:
-> GET /users
-> GET /users/{id}
```

### Advanced grouping with groups

If you want to simply group action classes or have a more complex route setup and do not want to reassign patterns and
middleware all the time, you can configure groups to use in your routes.

You can use an extra class for defining [`GroupConfiguration`](src/Routing/GroupConfiguration.php)s.

```php
use Mrcl\SlimRoutes\Routing\GroupConfiguration;

class Group
{
    final public const ANIMALS   = 'animals';
    final public const CATS      = 'cats';
    final public const ELEPHANTS = 'elephants';

    private array $groups;

    public function __construct()
    {
        $this->groups = [
            self::ANIMALS => ($animals = new GroupConfiguration(id: self::ANIMALS, pattern: 'animals', middleware: AnimalsMiddleware::class)),
            self::ELEPHANTS => new GroupConfiguration(id: self::ELEPHANTS, pattern: 'elephants', middleware: ElephantsMiddleware::class, parent: $animals),
            self::CATS => new GroupConfiguration(id: self::CATS, pattern: 'cats', middleware: CatsMiddleware::class, parent: $animals)
        ];
    }

    public function get(string $id): GroupConfiguration
    {
        return $this->groups[$id];
    }
}
```

Add groups to SlimRoutes

```php 
$sr
  //->addGroup(Group->get(Group::ANIMALS)) you only need to add a group if you use it directly 
  ->addGroup(Group->get(Group::CATS)) 
  ->addGroup(Group->get(Group::ELEPHANTS))
```

Pass the group's ID

```php 
#[Controller(groupId: Group::CATS)]
class CatsController {
    #[Route] 
    public function getAllCats
    
    #[Route('{id}')]
    public function getCat
}

Routes:
-> GET /animals/cats      [AnimalsMiddleware, CatsMiddleware]
-> GET /animals/cats/{id} [AnimalsMiddleware, CatsMiddleware]
```

```php 
#[Route(method: HttpMethod::POST, groupId: Group::ELEPHANTS)]
class AddElephantAction
{
    public function __invoke
}

Routes:
-> POST /animals/elephants [AnimalsMiddleware, ElephantsMiddleware]
```

## API versioning

For enabling API versioning to all of your routes, you have to configure
an [`VersionConfiguration`](src/Routing/VersionConfiguration.php).

```php
$sr->addApiVersion(new VersionConfiguration(version: 'v1', middleware: MyMiddleware::class))
```

### Multiple API versions

Let's assume you have three API versions `v1`, `v2`, `v3`.

- v1 is only for some legacy routes, e.g. `/v1/updates`
- v3 is the latest and just went live, users are in the process of updating
- v2 is still used by the majority of users

A possible configuration could look like the following:

```php
use Mrcl\SlimRoutes\Routing\VersionConfiguration;

$sr
  ->addApiVersion(new VersionConfiguration(version: 'v1', middleware: ApiV1Middleware::class, priority: RoutePriority::LOW, default: false))
  ->addApiVersion(new VersionConfiguration(version: 'v2', middleware: ApiV2Middleware::class))
  ->addApiVersion(new VersionConfiguration(version: 'v3', middleware: [ApiMiddleware::class, OtherApiMiddleware::class]))
  ->enableApiVersionPrioritization()
  ...
```

#### Route order

The route stack would contain routes in the following order

```
/v3/route1
/v2/route1
/v3/route2
/v2/route2
... 
all v3 and v2 routes
...
/v1/...
```

You can still use the `priority` argument on routes to lower or heighten their position.

#### Versioning for specific routes

We have the following additional config

```php
  ...
  ->addRouteGroup(new GroupConfiguration(id: 'cats-v1', pattern: 'cats'))
  ->addRouteGroup(new GroupConfiguration(id: 'cats', pattern: 'cats', middleware: CatsMiddleware::class))
  ->enableRoutePrioritization()
```

For a controller setup a possible config could be:

```php 
#[Controller(groupId: 'cats')]
class CatController {
    #[Route] 
    public function getAllCats
    
    #[Route(
      pattern: 'all', 
      groupId: 'cats-v1', 
      version: 'v1'
    )] 
    public function getAllCatsV1
}

Routes:
-> GET /v3/cats     [ApiMiddleware, OtherApiMiddleware, CatsMiddleware]
-> GET /v2/cats     [ApiV2Middleware, CatsMiddleware]
-> GET /v1/cats/all [ApiV1Middleware]
```

For action classes:

```php 
#[Route(groupId: 'cats')] 
class ViewCatsAction {
    public function __invoke
}
```

```php 
#[Route(
  pattern: 'all', 
  groupId: 'cats-v1', 
  version: 'v1'
)] 
class ViewCatsActionV1 {
    public function __invoke
}
```

### Unversioned routes

#### Exclude only some routes

If you want to exclude some routes from versioning you can do so by pass `VersionConfiguration::NONE` to the `#[Route]`
or `#[Controller]`
attribute.

```php
#[Route(
  pattern: 'my-action', 
  version: VersionConfiguration::NONE
)]
class MyAction
{
    public function __invoke
}
```

Additionally, if **all of your unversioned** routes need some specific middleware you can add a `VersionConfiguration`.

```php
$sr->addApiVersion(new VersionConfiguration(VersionConfiguration::NONE, SomeMiddleware::class, default: false))
```

#### Make all routes available without version

If you have the special case that **all routes** (without specific version assignment) need also to be accessed without
versioning you can do so

```php
$sr->addApiVersion(new VersionConfiguration(VersionConfiguration::NONE))
```

### Versioned route names

When you add an API version and use route names, route names will be prefixed with the added version(s).

```php
$sr
  ->addApiVersion(new VersionConfiguration('v1'))
  ->addApiVersion(new VersionConfiguration('v2'))
```

```php
#[Route(name: 'my-action')]
class MyAction
{
    public function __invoke
}
```

Route names are `v1-my-action` and `v2-my-action`
