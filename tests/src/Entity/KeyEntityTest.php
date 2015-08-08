<?php
/**
 *  @file
 *  Provides \Drupal\Tests\key\Entity\KeyEntityTest
 */

namespace Drupal\Tests\key\Entity;

use Drupal\key\Entity\Key;
use Drupal\Tests\key\KeyTestBase;

/**
 * Test the key entity methods.
 *
 * @coversDefaultClass \Drupal\key\Entity\Key
 */
class KeyEntityTest extends KeyTestBase {

  /**
   * Assert that key entity getters work.
   *
   * @group key
   */
  public function testGetters() {
    // Create a key entity using simple provider.
    $values = [
      'key_id' => $this->getRandomGenerator()->word(15),
      'key_provider' => 'key_provider_simple',
      'key_settings' => ['simple_key_value' => $this->createToken()]
    ];
    $key = new Key($values, 'key');

    $this->assertEquals($values['key_provider'], $key->getKeyProvider());
    $this->assertEquals($values['key_settings'], $key->getKeySettings());
  }
}
