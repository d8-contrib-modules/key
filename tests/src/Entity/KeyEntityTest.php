<?php
/**
 *  @file
 *  Provides \Drupal\Tests\key\Entity\KeyEntityTest
 */

namespace Drupal\Tests\key\Entity;

use Drupal\key\Entity\Key;
use Drupal\key\Plugin\KeyProvider\SimpleKey;
use Drupal\Tests\key\KeyTestBase;

/**
 * Test the key entity methods.
 *
 * @coversDefaultClass \Drupal\key\Entity\Key
 */
class KeyEntityTest extends KeyTestBase {

  /**
   * @var []
   *   Key settings to use for SimpleKey provider.
   */
  protected $key_settings;

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
      'key_settings' => $this->key_settings,
    ];
    $key = new Key($values, 'key');

    $this->assertEquals($values['key_provider'], $key->getKeyProvider());
    $this->assertEquals($values['key_settings'], $key->getKeySettings());
    $this->assertEquals($values['key_settings']['simple_key_value'], $key->getKeyValue());
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $definition = [
      'id' => 'key_provider_simple',
      'title' => 'Simple Key',
      'storage_method' => 'config'
    ];
    $this->key_settings = ['simple_key_value' => $this->createToken()];
    $plugin = new SimpleKey($this->key_settings, 'key_provider_simple', $definition);

    // Mock the KeyProviderPluginManager service.
    $this->KeyProviderManager = $this->getMockBuilder('\Drupal\key\KeyProviderPluginManager')
      ->disableOriginalConstructor()
      ->getMock();

    $this->KeyProviderManager->expects($this->any())
      ->method('getDefinitions')
      ->willReturn([
        ['id' => 'key_provider_file', 'title' => 'File Key', 'storage_method' => 'file'],
        ['id' => 'key_provider_simple', 'title' => 'Simple Key', 'storage_method' => 'config']
      ]);
    $this->KeyProviderManager->expects($this->any())
      ->method('createInstance')
      ->with('key_provider_simple', $this->key_settings)
      ->willReturn($plugin);
    $this->container->set('plugin.manager.key.key_provider', $this->KeyProviderManager);

    \Drupal::setContainer($this->container);
  }
}
