# Key Module for Drupal 8

[![Build Status](https://travis-ci.org/d8-contrib-modules/key.svg?branch=master)](https://travis-ci.org/d8-contrib-modules/key)

This module provides a global key management service that can be invoked via the services interface.

## Architecture

Key leverages the Drupal 8 Plugin API for Key Providers. Key Providers define an interface to get key contents. Key Providers have
their own configuration forms that store settings specific to that Key Provider when creating a Key entity.

Plugins allow for extensibility for customized needs. This allows other modules to create their own key providers, the
key provider settings, and the logic for retrieving the key value.

## Settings

To manage keys, visit `admin/config/system/key`.

Plugins register themselves to the Key Plugin Manager. One important feature, built as an annotation, is the Storage
Method for the plugin. Each plugin needs to register the type of storage used within the plugin. 

For convention, we recommend using the following storage method values within your annotation.

1. File - Stored in files, private filesystem or path-based files
1. Configuration - Stored with Drupal's built in configuration management (settings, entities)
1. Database - Stored as content within the database
1. Remote - Stored offsite and integrated through a web service API

## Use of Services

After configuring the service, the service provides the ability to encrypt and decrypt.

The Key Manager allows you to retrieve keys that your module uses. It is best practice to
[inject the service](https://www.drupal.org/node/2133171) into your own service, [form](https://www.drupal.org/node/2203931),
 or [controller](https://api.drupal.org/api/drupal/core!lib!Drupal!Core!DependencyInjection!ContainerInjectionInterface.php/interface/ContainerInjectionInterface/8). The following examples assume the use of the `\Drupal` object for brevity, but the examples can be extrapolated to fit
 the use case of your module.

### Get All Keys

`Drupal::service('key_manager')->getKeys();`

### Get Single Key

`Drupal::service('key_manager')->getKey($key_id);`

### Get Key Value

`Drupal::service('key_manager')->getKeyValue($key_id);`


