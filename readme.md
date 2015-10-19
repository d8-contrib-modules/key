# Key Module for Drupal 8

[![Build Status](https://travis-ci.org/d8-contrib-modules/key.svg?branch=master)](https://travis-ci.org/d8-contrib-modules/key)

This module provides the ability for site administrators to manage sitewide keys, which can be used, for example, to integrate with external services. Keys are available for use by other modules using a Drupal service provided by Key.

## Managing Keys

To manage keys, visit `admin/config/system/key`. When creating a file, enter a name for the key and select the key provider to be used. Additional settings for the selected key provider may be required. For instance, if the File key provider is selected, enter a path to the file that contains the key.

## Key Providers

Key leverages the Drupal 8 Plugin API for key providers, so that other modules can define additional key providers through the Key Plugin Manager. A key provider defines a method for retrieving a key, along with settings specific to that key provider, which are saved when creating a Key entity. Each plugin needs to register, as an annotation, the storage method used within the plugin.

For convention, it is recommended to use one the following storage method values:

1. File - Stored in a file and retrieved via the local filesystem or by using a stream wrapper
1. Configuration - Stored with Drupal’s built-in configuration management (settings, entities)
1. Database - Stored as a field in a database record
1. Remote - Retrieved via a remote service call

## Using a Key

Modules can retrieve information about keys or a specific key value by making a call to the Key Manager service. It is best practice to
[inject the service](https://www.drupal.org/node/2133171) into your own service, [form](https://www.drupal.org/node/2203931),
 or [controller](https://api.drupal.org/api/drupal/core!lib!Drupal!Core!DependencyInjection!ContainerInjectionInterface.php/interface/ContainerInjectionInterface/8). The following examples assume the use of the `\Drupal` object for brevity, but the examples can be extrapolated to fit
 the use case of your module.

### Get All Keys

`Drupal::service('key_repository')->getKeys();`

### Get Single Key

`Drupal::service('key_repository')->getKey($key_id);`

### Get Default Key

`Drupal::service('key_repository')->getKey();`

### Get Key Value

`Drupal::service('key_repository')->getKey($key_id)->getKeyValue();`
