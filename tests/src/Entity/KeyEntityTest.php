<?php
/**
 *  @file
 *  Provides \Drupal\Tests\key\Entity\KeyEntityTest
 */

namespace Drupal\Tests\key\Entity;

use Drupal\key\Entity\Key;
use Drupal\key\Plugin\KeyProvider\ConfigKeyProvider;
use Drupal\Tests\key\KeyTestBase;

/**
 * Test the key entity methods.
 *
 * @coversDefaultClass \Drupal\key\Entity\Key
 */
class KeyEntityTest extends KeyTestBase {

  /**
   * @var []
   *   Key provider settings to use for Configuration key provider.
   */
  protected $key_provider_settings;

  /**
   * Assert that key entity getters work.
   *
   * @group key
   */
  public function testGetters() {
    // Create a key entity using Configuration key provider.
    $values = [
      'key_id' => $this->getRandomGenerator()->word(15),
      'key_provider' => 'config',
      'key_provider_settings' => $this->key_provider_settings,
    ];
    $key = new Key($values, 'key');

    $this->assertEquals($values['key_provider'], $key->getKeyProvider());
    $this->assertEquals($values['key_provider_settings'], $key->getKeyProviderSettings());
    $this->assertEquals($values['key_provider_settings']['key_value'], $key->getKeyValue());
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $definition = [
      'id' => 'config',
      'title' => 'Configuration',
      'storage_method' => 'config'
    ];
    $this->key_provider_settings = ['key_value' => $this->createToken()];
    $plugin = new ConfigKeyProvider($this->key_provider_settings, 'config', $definition);

    // Mock the KeyProviderManager service.
    $this->KeyProviderManager = $this->getMockBuilder('\Drupal\key\KeyProviderManager')
      ->disableOriginalConstructor()
      ->getMock();

    $this->KeyProviderManager->expects($this->any())
      ->method('getDefinitions')
      ->willReturn([
        ['id' => 'file', 'title' => 'File', 'storage_method' => 'file'],
        ['id' => 'config', 'title' => 'Configuration', 'storage_method' => 'config']
      ]);
    $this->KeyProviderManager->expects($this->any())
      ->method('createInstance')
      ->with('config', $this->key_provider_settings)
      ->willReturn($plugin);
    $this->container->set('plugin.manager.key.key_provider', $this->KeyProviderManager);

    \Drupal::setContainer($this->container);
  }
}
