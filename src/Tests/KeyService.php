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
  function testKeyValue() {

    // Create user with permission to create policy.
    $user1 = $this->drupalCreateUser(array('administer site configuration'));
    $this->drupalLogin($user1);

    // Create new simple key.
    $test_string = 'testing 123 &*#';
    $this->drupalGet('admin/config/system/key/add');
    $edit = [
      'key_type' => 'key_type_simple',
    ];
    $this->drupalPostAjaxForm(NULL, $edit, 'key_type');

    $edit = [
      'id' => 'testing_key',
      'label' => 'Testing Key',
      'key_type' => 'key_type_simple',
      'key_settings[simple_key_value]' => $test_string,
    ];
    $this->drupalPostForm(NULL, $edit, t('Save'));


    // Test getKeyValue service.
    $key_value_string = \Drupal::service('key_manager')->getKeyValue('testing_key');

    $this->verbose('Key Value: ' . $key_value_string);

    $this->assertEqual($key_value_string, $test_string, 'The getKeyValue function is not properly processing');
  }
}
