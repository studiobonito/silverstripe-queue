# Queue Module

## Overview

Simple multi driver queue system. This is essentially a port of the [Laravel](http://laravel.com/) queue system.

**Still very much a WIP not for use in production!**

Although it would be better to use `illuminate/queue` directly it has too many dependancies that would replicate `silverstripe/framework` functionality at this time. By porting the code we can take advantage of SilverStripes command line framework and dependancy injection whilst still having a feature rich and multi backend queue system.

So our thanks go to [Taylor Otwell](https://twitter.com/taylorotwell) et al for their excellent work on [Laravel](http://laravel.com/).

## Requirements

- SilverStripe 3.1 or newer.
- `pda/pheanstalk` 2.1 or newer for Beanstalkd support

## Supported Backends

- [x] Synchronous
- [x] SilverStripe Database
- [x] Beanstalkd
- [ ] IronMQ
- [ ] Amazon SQS
- [ ] Redis

## Installation Instructions

### Composer

Run the following to add this module as a requirement and install it via composer.

```bash
$ composer require studiobonito/silverstripe-queue
```

### Manual

Copy the 'queue' folder to your the root of your SilverStripe installation.

## Configuration Overview

Configure the queue drivers with the following YAML.

```yaml
StudioBonito\SilverStripe\Queue\QueueManager:
    default: 'db'
    db:
        driver: 'db'
        queue: 'default'
    beanstalkd:
        driver: 'beanstalkd'
        host: 'localhost'
        queue: 'default'
        ttr: 60
```

## Usage Overview

### Import QueueManager

Import the `QueueManager` class for ease of use.

```php
use StudioBonito\SilverStripe\Queue\QueueManager;

```

### Push A Job Onto The Queue

Use `QueueManager::inst()` to get an instance of the `QueueManger` class and then call the `push()` method passing in the name of a job handler and an array of data.

```php
QueueManager::inst()->push('SendEmail', array('message' => $message));

```

### Creating A Job Handler

Job handlers are simple classes that contain a `run` method with `$job` and `$data` parameters.

```php
class SendEmail {

    public function run($job, $data)
    {
        // Code for processing job goes here.
    }
}
```

## Contributing

### Unit Testing

```bash
$ composer install --prefer-dist --dev
$ phpunit
```

## License

All original work copyright of Taylor Otwell under [MIT license](http://opensource.org/licenses/MIT).
All subsequent work copyright [Studio Bonito Ltd.](http://www.studiobonito.co.uk/) under BSD-2-Clause license.