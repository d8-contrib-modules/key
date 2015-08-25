<?php

/**
 * @file
 * Definition of Drupal\key\Tests\KeyListBuilder.
 */

namespace Drupal\key\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the key list builder.
 *
 * @group key
 */
class KeyListBuilder extends WebTestBase {

  public static $modules = array('key', 'dblog');

  /**
   * Test KeyListBuilder functions.
   */
  function testListBuilder() {
    // Create user with permission to administer keys.
    $user1 = $this->drupalCreateUser(array('administer keys'));
    $this->drupalLogin($user1);

    // Go to the Key list page.
    $this->drupalGet('admin/config/security/key');

    // Verify that the "no keys" message displays.
    $this->assertText(t('No keys are available. Add a key.'));

    // Add a key.
    $test_string = 'testing 123 &*#';
    $this->drupalGet('admin/config/security/key/add');
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

    // Go to the Key list page.
    $this->drupalGet('admin/config/security/key');

    // Verify that the "no keys" message does not display.
    $this->assertNoText(t('No keys are available. Add a key.'));
  }

}
