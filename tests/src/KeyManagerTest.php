<?php
/**
 * Provides \Drupal\Tests\key\KeyManagerTest.php
 */

namespace Drupal\Tests\key;

use Drupal\key\Entity\Key;
use Drupal\key\KeyManager;

class KeyManagerTest extends KeyTestBase {


  /**
   * Test loading of default key entity.
   */
  public function testGetDefaultKey() {

    $key_id = $this->getRandomGenerator()->word(15);
    $key = new Key(['key_id' => $key_id], 'key');

    // Mock load method to return when key is not defined and when it is
    // defined.
    $this->configStorage->expects($this->any())
      ->method('load')
      ->with($key_id)
      ->will($this->onConsecutiveCalls(NULL, $key));

    // Create a new key manager object.
    $keyManager = new KeyManager($this->entityManager, $this->configFactory);

    // On the first run, config storage will return NULL.
    $default_key = $keyManager->getDefaultKey();
    $this->assertEquals(NULL, $default_key);

    // On the second run, config storage will return the key entity.
    $default_key = $keyManager->getDefaultKey();
    $this->assertEquals($key_id, $default_key->get('key_id'));
  }

}
