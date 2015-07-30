# Key Module for Drupal 8

[![Build Status](https://travis-ci.org/d8-contrib-modules/key.svg?branch=master)](https://travis-ci.org/d8-contrib-modules/key)

This module provides a global key management service that can be invoked via the services interface.

## Architecture

Key leverages the Drupal 8 Plugin API for Key Types. Key Types define an interface to get key contents. Key Types have
their own configuration forms that store key-type specific settings when creating a Key entity.

Plugins allow for extensibility for customized needs. This allows other modules to create their own types of keys, the
key type settings, and the logic for retrieving the key value.

## Settings

To manage keys, visit `admin/config/system/key`.

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


