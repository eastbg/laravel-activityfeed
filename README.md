ln -s /Users/trailo/dev/vendors.east.fi/example-app/vendor/east/laravel-activityfeed/src/Resources/views/backpack/fields/af_select_from_array.blade.php /Users/trailo/dev/vendors.east.fi/example-app/resources/views/vendor/backpack/crud/fields/af_select_from_array.blade.php
ln -s /Users/trailo/dev/vendors.east.fi/example-app/vendor/east/laravel-activityfeed/src/Resources/views/backpack/fields/af_select_multiple_json.blade.php /Users/trailo/dev/vendors.east.fi/example-app/resources/views/vendor/backpack/crud/fields/af_select_multiple_json.blade.php
ln -s /Users/trailo/dev/vendors.east.fi/example-app/vendor/east/laravel-activityfeed/src/Resources/js/af.js /Users/trailo/dev/vendors.east.fi/example-app/public/js/af.js

# laravel-activityfeed

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Travis](https://img.shields.io/travis/east/laravel-activityfeed.svg?style=flat-square)]()
[![Total Downloads](https://img.shields.io/packagist/dt/east/laravel-activityfeed.svg?style=flat-square)](https://packagist.org/packages/east/laravel-activityfeed)

## Install
```bash
composer require east/laravel-activityfeed
```
It is strongly recommended to have Laravel Backpack Pro version installed (https://backpackforlaravel.com/). The web interface for defining ruling and templates relies on Backpack and it will be hard to manage without it.

### Important note about database
This extension relies heavily on users table and requires you to have a column called "admin". The users model is published to make it easier to edit columns and relationships if you have customised the database. Note though that 
```bash
php artisan vendor:publish --force
```
will overwrite your changes to this model. So you can change this model to extend your existing user class.


#### Run installer
```bash
php artisan af:install
```

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

### Individual notifications

Best way to use ActivityFeed is to extend your base model with ActivityFeedBaseModel. This allows the rules to hook into database events directly based on the rules that are tied to database tables. You can also create notification directly. You still need to have a rule for this purpose, as rule defines the template. 


```php
AfNotify::setTemplate('template-slug')   // mandatory
  ->addChannel(['email'])          // additional channels
  ->setSubject('New message')      // optional, template defines this already
  ->setTarget('user')              // default: user
  ->setUser(auth()->user()->id)    // default: current user
  ->setDigest()                    // set to digest instead of notifying immediately
  ->setVars($array)                // key-value replacement
  ->setObjects(['user' => $obj1, 'company' => $obj2])      // database objects
  ->add();
```

### Model based triggering - rules

Rules are tied to events in particular database tables and include the following options:
 * Table name
 * Event (create, update, delete)
 * If it's update, which column will trigger the update
   * Change (any, based on rules)
   * Column name
   * Operator
   * Value

In addition, you can create custom rules with PHP code. These go under app/ActivityFeed/Rules/. When you do the install, an example rule will be put in place. 


### Creators

In addition to manually defined rules that take care of the saving, you can also use a custom save class - these are called creators. The creators go under app/ActivityFeed/Creators/. When you do the install, an example creator will be put in place.


### Targeting

Target should always be an individual user, regardless of whether it's shown on a feed or sent via some other channel(s). In addition target can be admin users. Whether a particular notification

This is where it gets slightly complicated. Let's say a database record modification/creation in *Posts* launches a notification event that should be targeted at certain group of users. Example database structure:

```php
      Posts
        ↓
Recipients (pivot)
        ↓
    Recipient
        ↓
      Users
```
As pivot records don't exist when creating the Posts, we will save this event to be created as a notification by the cron job. The difficult part is on mapping the relationship chain.



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

Rules define targeting and channels.

### Forking / messing with the models

As I'm lazy, I've used the excellent model generator from krlove. You can use it like this (adjust paths obviously):

```bash
php artisan krlove:generate:model --base-class-name='East\LaravelActivityfeed\Models\ActivityFeedBaseModel' --namespace='East\LaravelActivityfeed\Models\ActiveModels' --output-path=../vendor/east/laravel-activityfeed/src/Models/ActiveModels/ --table-name=af_events AfEventsModel

php artisan krlove:generate:model --base-class-name='East\LaravelActivityfeed\Models\ActivityFeedBaseModel' --namespace='East\LaravelActivityfeed\Models\ActiveModels' --output-path=../vendor/east/laravel-activityfeed/src/Models/ActiveModels/ --table-name=af_rules AfRules

php artisan krlove:generate:model --base-class-name='East\LaravelActivityfeed\Models\ActivityFeedBaseModel' --namespace='East\LaravelActivityfeed\Models\ActiveModels' --output-path=../vendor/east/laravel-activityfeed/src/Models/ActiveModels/ --table-name=af_categories AfCategories

php artisan krlove:generate:model --base-class-name='East\LaravelActivityfeed\Models\ActivityFeedBaseModel' --namespace='East\LaravelActivityfeed\Models\ActiveModels' --output-path=../vendor/east/laravel-activityfeed/src/Models/ActiveModels/ --table-name=af_templates AfTemplatesModel

php artisan krlove:generate:model --base-class-name='East\LaravelActivityfeed\Models\ActivityFeedBaseModel' --namespace='East\LaravelActivityfeed\Models\ActiveModels' --output-path=../vendor/east/laravel-activityfeed/src/Models/ActiveModels/ --table-name=af_notifications AfNotificationsModel
```

### Rendering

Upon install we publish af.css to public css directory and it's included with the widgets. Alternatively you can copy it to resources/css/ and import it to your app.css. In this case make sure to set configuration option for not including the css with the widgets. 

Include widgets 

Available widgets:
 * FeedWidget
 * MenuWidget
 * NotificationWidget


### Understanding the flow

```php
    Model event
        ↓
    Check rules
        ↓
    Create event
        ↓
 Cronjob (afpoll:run)
        ↓
 Create notifications (uses mapping)
        ↓
Render and/or send notification
```
As pivot records don't exist when creating the Posts, we will save this event to be created as a notification by the cron job. The difficult part is on mapping the relationship chain.



## Testing

Run the tests with:

```bash
vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## Security

If you discover any security-related issues, please email timo@east.fi instead of using the issue tracker.


## License

The MIT License (MIT). Please see [License File](/LICENSE.md) for more information.