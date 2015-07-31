<?php
/**
 * Provides \Drupal\Tests\key\KeyManagerTest.php
 */

namespace Drupal\Tests\key;

use Drupal\key\Plugin\KeyType\SimpleKey;
use Drupal\key\Entity\Key;
use Drupal\key\KeyManager;

class KeyManagerTest extends KeyTestBase {

  /**
   * @var \Drupal\key\KeyManager
   */
  protected $keyManager;

  /**
   * @var string
   *   Random string to use as key id.
   */
  protected $key_id;

  /**
   * @var \Drupal\key\Entity\Key
   */
  protected $key;

  /**
   * Provide test values for default key content.
   */
  public function defaultKeyContentProvider() {
    $defaults = ['simple_key_value' => $this->createToken()];
    $definition = [
      'id' => 'key_type_Simple',
      'class' => 'Drupal\key\Plugin\KeyType\SimpleKey',
      'title' => 'Simple Key',
    ];
    $keyType = new SimpleKey($defaults, 'key_type_simple', $definition);

    return [
      [$defaults, $keyType]
    ];
  }

  /**
   * Test load by multiple key ids.
   *
   * @group key
   */
  public function testGetKeys() {
    $key_id2 = $this->getRandomGenerator()->word(15);
    $key2 = new Key(['key_id' => $key_id2], 'key');

    $this->entityManager->expects($this->any())
      ->method('loadMultiple')
      ->with([$this->key_id, $key_id2])
      ->willReturn([$this->key_id => $this->key, $key_id2 => $key2]);

    $keys = $this->keyManager->getKeys([$this->key_id, $key_id2]);
    $this->assertEquals(2, count($keys));
  }

  /**
   * Test load by key id.
   *
   * @group key
   */
  public function testGetKey() {
    $key = $this->keyManager->getKey($this->key_id);
    $this->assertInstanceOf('\Drupal\key\Entity\Key', $key);
    $this->assertEquals($this->key->get('key_id'), $key->get('key_id'));
  }

  /**
   * Test get key value.
   *
   * @group key
   * @dataProvider defaultKeyContentProvider
   */
  public function testGetKeyValue($defaults, $keyType) {
    // Make the key type plugin manager return a plugin instance.
    $this->keyTypeManager->expects($this->any())
      ->method('createInstance')
      ->with('key_type_simple', $defaults)
      ->willReturn($keyType);

    $this->key->set('key_settings', $defaults);

    $settings = $this->keyManager->getKeyValue($this->key_id);
    $this->assertEquals($defaults['simple_key_value'], $settings);
  }

  /**
   * Test loading of default key entity.
   *
   * @group key
   */
  public function testGetDefaultKey() {
    // On the first run, config storage will return NULL.
    $default_key = $this->keyManager->getDefaultKey();
    $this->assertEquals(NULL, $default_key);

    // On the second run, config storage will return the key entity.
    $default_key = $this->keyManager->getDefaultKey();
    $this->assertInstanceOf('\Drupal\key\Entity\Key', $default_key);
    $this->assertEquals($this->key_id, $default_key->get('key_id'));
  }

  /**
   * Test load of defaul key content.
   *
   * @group key
   * @dataProvider defaultKeyContentProvider
   */
  public function testGetDefaultKeyContent($defaults, $keyType) {
    // On the first run, config storage will return NULL.
    $settings = $this->keyManager->getDefaultKeyContents();
    $this->assertEquals(NULL, $settings);

    // Make the key type plugin manager return a plugin instance.
    $this->keyTypeManager->expects($this->any())
      ->method('createInstance')
      ->with('key_type_simple', $defaults)
      ->willReturn($keyType);

    $this->key->set('key_settings', $defaults);

    $settings = $this->keyManager->getDefaultKeyContents();
    $this->assertEquals($defaults['simple_key_value'], $settings);
  }

  /**
   * Test get keys by type.
   *
   * @group key
   */
  public function testGetKeysByType() {
    // Create a key type plugin to play with.
    $defaults = ['simple_key_value' => $this->createToken()];
    $definition = [
      'id' => 'key_type_Simple',
      'class' => 'Drupal\key\Plugin\KeyType\SimpleKey',
      'title' => 'Simple Key',
    ];
    $keyType = new SimpleKey($defaults, 'key_type_simple', $definition);

    // Make the key type plugin manager return a plugin instance.
    $this->keyTypeManager->expects($this->any())
      ->method('createInstance')
      ->with('key_type_simple', $defaults)
      ->willReturn($keyType);

    $this->key->set('key_settings', $defaults);

    $settings = $this->keyManager->getKeysByType('key_type_simple');
    $this->assertEquals([$keyType], $settings);
  }

  /**
   * Test get keys by storage method.
   *
   * @group key
   */
  public function testGetKeysByStorageMethod() {
    // Create a key type plugin to play with.
    $defaults = ['simple_key_value' => $this->createToken()];
    $definition = [
      'id' => 'key_type_Simple',
      'class' => 'Drupal\key\Plugin\KeyType\SimpleKey',
      'title' => 'Simple Key',
    ];
    $keyType = new SimpleKey($defaults, 'key_type_simple', $definition);

    // Make the key type plugin manager return a plugin instance.
    $this->keyTypeManager->expects($this->any())
      ->method('createInstance')
      ->with('key_type_simple', $defaults)
      ->willReturn($keyType);

    $this->key->set('key_settings', $defaults);

    $settings = $this->keyManager->getKeysByStorageMethod('config');
    $this->assertEquals([$keyType], $settings);
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->key_id = $this->getRandomGenerator()->word(15);
    $defaults = [
      'key_id' => $this->key_id,
      'key_type' => 'key_type_simple'
    ];
    $this->key = new Key($defaults, 'key');

    // Mock the get method on the Config object.
    $this->config->expects($this->any())
      ->method('get')
      ->with('default_key')
      ->will($this->onConsecutiveCalls(FALSE, $this->key_id));

    // Mock load method to return when key is not defined and when it is
    // defined.
    $this->configStorage->expects($this->any())
      ->method('load')
      ->with($this->key_id)
      ->willReturn($this->key);

    // Create a new key manager object.
    $this->keyManager = new KeyManager($this->entityManager, $this->configFactory, $this->keyTypeManager);
  }

}
