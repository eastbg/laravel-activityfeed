# laravel-activityfeed

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Travis](https://img.shields.io/travis/east/laravel-activityfeed.svg?style=flat-square)]()
[![Total Downloads](https://img.shields.io/packagist/dt/east/laravel-activityfeed.svg?style=flat-square)](https://packagist.org/packages/east/laravel-activityfeed)




## Install

```bash
composer require east/laravel-activityfeed
```

#### Run installer
php artisan af:install

## Usage

### Available command line commands

This will create a rule templates and corresponding template entries based on your entire database structure. Running it again will not overwrite anything, but will add any tables that were not there before. 
```bash
php artisan af:discover_rules
```
Template, rule etc. handling interfaces is based on Laravel Backpack. It's not included with the ActivityFeed package. 
```bash
php artisan af:install_backpack
```

### Recording events

Best way to use ActivityFeed is to extend your base model with ActivityFeedBaseModel. This allows the rules to hook into database events directly based on the rules that are tied to database tables. You can also use the facades in the following way:

```php
AfCreate::setTemplate('template-slug')   // mandatory
  ->addChannel(['email'])          // additional channels
  ->setSubject('New message')      // optional, template defines this already
  ->setTarget('user')              // default: user
  ->setUser(auth()->user()->id)    // default: current user
  ->setDigest()                    // set to digest instead of notifying immediately
  ->setVars($array)                // key-value replacement
  ->setObjects(['user' => $obj1, 'company' => $obj2])      // database objects
  ->add();
```

### Templating

Templates are in Laravel Blade format and saved to database. Templates are fed with var replacement and data replacement. Idea is that you can dump data from your database record and it's relations directly to the template. So you would define it like this:
```php
You have a new notification, click <a href="{{$url ?? ''}}">here</a> to read it.
```
So also this would work:
```php
@if(isset($username) AND $username)) Hello {{$username}}! @endif
You have a new notification, click <a href="{{$url ?? ''}}">here</a> to read it.
```
And this (provided you are sending the correct objects):
```php
@if(isset($user->profile) AND $user->profile)) Hello {{$user->profile->name}}! @endif
You have a new notification, click <a href="{{$url ?? ''}}">here</a> to read it.
```
The variable replacement happens at save time and is "blind" so you should adjust your templates accordingly. 

Template itself usually defines:
 * channel
 * subject
 * digest vs. individual notification
 * target (admins vs user)

## Testing

Run the tests with:

```bash
vendor/bin/phpunit
```


### Forking / messing with the models

As I'm lazy, I've used the excellent model generator from krlove. You can use it like this:

```bash
php artisan krlove:generate:model --base-class-name='East\LaravelActivityfeed\Models\ActiveModels\ActivityFeedBaseModel' --namespace='East\LaravelActivityfeed\Models\ActiveModels' --output-path=../vendor/east/laravel-activityfeed/src/Models/ActiveModels/ --table-name=af_events AfEventsModel
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## Security

If you discover any security-related issues, please email timo@east.fi instead of using the issue tracker.


## License

The MIT License (MIT). Please see [License File](/LICENSE.md) for more information.