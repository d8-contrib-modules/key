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

  public static $modules = array('key');

  /**
   * Test password length behaviors.
   */
  function testPasswordLengthBehaviors() {
    global $base_url;

    // Create user with permission to create policy.
    $user1 = $this->drupalCreateUser(array('administer site configuration'));
    $this->drupalLogin($user1);

    // Create new password length policy.
    $edit = array();
    $edit['character_length'] = '5';
    $this->drupalPostForm('admin/config/security/password-policy/password-length', $edit, t('Add constraint'));

    // Get latest ID to get policy.
    $id = db_select("password_policy_length_constraints", 'p')
      ->fields('p', array('cid'))
      ->orderBy('p.cid', 'DESC')
      ->execute()
      ->fetchObject();

    // Create user with policy applied.
    $user2 = $this->drupalCreateUser(array('enforce password_policy_length_constraint.' . $id->cid . ' constraint'));
    $uid = $user2->id();

    // Login.
    $this->drupalLogin($user2);

    // Change own password with one too short.
    $edit = array();
    $edit['pass'] = '1';
    $edit['current_pass'] = $user2->pass_raw;
    $this->drupalPostAjaxForm("user/" . $uid . "/edit", $edit, 'pass');

    // Verify we see an error.
    $this->assertText('Fail - The length of the password is 1 characters, which is less than the 5 characters of the policy');

    // Change own password with one long enough.
    $edit = array();
    $edit['pass'] = '111111111111';
    $edit['current_pass'] = $user2->pass_raw;
    $this->drupalPostAjaxForm("user/" . $uid . "/edit", $edit, 'pass');

    // Verify we see do not error.
    $this->assertNoText('Fail - The length of the password is 12 characters, which is less than the 5 characters of the policy');


    $this->drupalLogout();
  }
}
