# Queue Module

## Overview

Simple multi driver queue system. This is essentially a port of the [Laravel](http://laravel.com/) queue system.

Still very much a WIP not for use in production!

## Requirements

SilverStripe 3.1 or newer.

## Installation Instructions

### Composer

Run the following to add this module as a requirement and install it via composer.

	$ composer require studiobonito/silverstripe-queue

### Manual

Copy the 'queue' folder to your the root of your SilverStripe installation.

## Usage Overview

Configure the queue drivers with the following YAML.

    StudioBonito\SilverStripe\Queue\QueueManager:
      default: 'db'
      db:
        driver: 'db'
      beanstalkd:
        driver: 'beanstalkd'
        host: 'localhost'
        ttr: 60