<?php

/**
 * @file
 * Definition of Drupal\key\Tests\KeyService.
 */

namespace Drupal\key\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the key service.
 *
 * @group key
 */
class KeyService extends WebTestBase {

  public static $modules = array('key', 'dblog');

  /**
   * Test getKeyValue functions.
   */
  function testSimpleKeyService() {

    // Create user with permission to create policy.
    $user1 = $this->drupalCreateUser(array('administer site configuration'));
    $this->drupalLogin($user1);

    // Create new simple key.
    $test_string = 'testing 123 &*#';
    $this->drupalGet('admin/config/system/key/add');
    $edit = [
      'key_provider' => 'key_provider_simple',
    ];
    $this->drupalPostAjaxForm(NULL, $edit, 'key_provider');

    $edit = [
      'id' => 'testing_key',
      'label' => 'Testing Key',
      'key_provider' => 'key_provider_simple',
      'key_settings[simple_key_value]' => $test_string,
    ];
    $this->drupalPostForm(NULL, $edit, t('Save'));


    // Test getKeyValue service.
    $key_value_string = \Drupal::service('key_manager')->getKeyValue('testing_key');

    $this->verbose('Key Value: ' . $key_value_string);

    $this->assertEqual($key_value_string, $test_string, 'The getKeyValue function is not properly processing');

    // Test getKeysByProvider service.
    $keys = \Drupal::service('key_manager')->getKeysByProvider('key_provider_simple');
    $this->assertEqual(count($keys), '1', 'The getKeysByProvider function is not returning 1 simple key');

    // Create another simple key.
    $test_string = 'testing 12345678 (837#';
    $this->drupalGet('admin/config/system/key/add');
    $edit = [
      'key_provider' => 'key_provider_simple',
    ];
    $this->drupalPostAjaxForm(NULL, $edit, 'key_provider');

    $edit = [
      'id' => 'testing_key2',
      'label' => 'Testing Key 2',
      'key_provider' => 'key_provider_simple',
      'key_settings[simple_key_value]' => $test_string,
    ];
    $this->drupalPostForm(NULL, $edit, t('Save'));

    // Test getKeysByProvider service.
    $keys = \Drupal::service('key_manager')->getKeysByProvider('key_provider_simple');
    $this->assertEqual(count($keys), '2', 'The getKeysByProvider function is not returning 2 simple keys');
  }

  /**
   * Test getKeyValue functions.
   */
  function testFileKeyService() {
    $rpath = realpath(drupal_get_path('module','key').'/tests/assets/testkey.txt');
    $contents = file_get_contents($rpath);

    // Create user with permission to create policy.
    $user1 = $this->drupalCreateUser(array('administer site configuration'));
    $this->drupalLogin($user1);

    // Create a new file key.
    $this->drupalGet('admin/config/system/key/add');
    $edit = [
      'key_provider' => 'key_provider_file',
    ];
    $this->drupalPostAjaxForm(NULL, $edit, 'key_provider');

    $edit = [
      'id' => 'testing_key_file',
      'label' => 'Testing Key File',
      'key_provider' => 'key_provider_file',
      'key_settings[file_key_location]' => $rpath,
      'key_settings[file_key_method]' => 'file_contents',
    ];
    $this->drupalPostForm(NULL, $edit, t('Save'));

    // Test getKeyValue service.
    $key_value_string = \Drupal::service('key_manager')->getKeyValue('testing_key_file');

    $this->verbose('Key Value: ' . $key_value_string);

    $this->assertEqual($key_value_string, $contents, 'The getKeyValue function is not properly processing');

    // Test getKeysByStorageMethod service.
    $keys = \Drupal::service('key_manager')->getKeysByStorageMethod('file');
    $this->assertEqual(count($keys), '1', 'The getKeysByStorageMethod function is not returning 1 file key');

    // Create a second new file key.
    $this->drupalGet('admin/config/system/key/add');
    $edit = [
      'key_provider' => 'key_provider_file',
    ];
    $this->drupalPostAjaxForm(NULL, $edit, 'key_provider');

    $edit = [
      'id' => 'testing_key_file2',
      'label' => 'Testing Key File2',
      'key_provider' => 'key_provider_file',
      'key_settings[file_key_location]' => $rpath,
      'key_settings[file_key_method]' => 'file_contents',
    ];
    $this->drupalPostForm(NULL, $edit, t('Save'));

    // Test getKeysByStorageMethod service.
    $keys = \Drupal::service('key_manager')->getKeysByStorageMethod('file');
    $this->assertEqual(count($keys), '2', 'The getKeysByStorageMethod function is not returning 2 file keys');

  }

}
