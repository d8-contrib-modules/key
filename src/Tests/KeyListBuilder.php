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
    $this->drupalGet('admin/config/system/key');

    // Verify that the "no keys" message displays.
    $this->assertText(t('No keys are available. Add a key.'));

    // Add a key.
    $this->drupalGet('admin/config/system/key/add');
    $edit = [
      'key_provider' => 'config',
    ];
    $this->drupalPostAjaxForm(NULL, $edit, 'key_provider');

    $edit = [
      'id' => 'testing_key',
      'label' => 'Testing Key',
      'key_provider' => 'config',
      'key_provider_settings[key_value]' => 'mustbesixteenbit',
    ];
    $this->drupalPostForm(NULL, $edit, t('Save'));

    // Go to the Key list page.
    $this->drupalGet('admin/config/system/key');

    // Verify that the "no keys" message does not display.
    $this->assertNoText(t('No keys are available. Add a key.'));
  }

}
