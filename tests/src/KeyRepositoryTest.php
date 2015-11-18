<?php
/**
 * Provides \Drupal\Tests\key\KeyManagerTest.php
 */

namespace Drupal\Tests\key;

use Drupal\key\KeyRepository;
use Drupal\key\Plugin\KeyProvider\ConfigKeyProvider;
use Drupal\key\Entity\Key;

/**
 * @group key
 */
class KeyRepositoryTest extends KeyTestBase {

  /**
   * @var \Drupal\key\KeyRepository
   */
  protected $keyRepository;

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
    $defaults = ['key_value' => $this->createToken()];
    $definition = [
      'id' => 'config',
      'class' => 'Drupal\key\Plugin\KeyProvider\ConfigKeyProvider',
      'title' => 'Configuration',
    ];
    $KeyProvider = new ConfigKeyProvider($defaults, 'config', $definition);

    return [
      [$defaults, $KeyProvider]
    ];
  }

  /**
   * Provide data values for testGetKeys().
   */
  public function getKeysProvider() {
    $key_id1 = $this->getRandomGenerator()->word(15);
    $key1 = new Key(['key_id' => $key_id1], 'key');
    $key_id2 = $this->getRandomGenerator()->word(15);
    $key2 = new Key(['key_id' => $key_id2], 'key');

    // This mocks the return value for loadMultiple in scenarios of an array of
    // 1 key, 2 keys, and no keys.
    return [
      [[$key_id1], [$key_id1 => $key1]],
      [[$key_id1, $key_id2], [$key_id1 => $key1, $key_id2 => $key2]],
      [[], [$key_id1 => $key1, $key_id2 => $key2]]
    ];
  }

  /**
   * Test load by multiple key ids.
   *
   * @group key
   * @dataProvider getKeysProvider
   */
  public function testGetKeys(array $key_ids, array $keys) {
    // Mock the loadMultiple to return results per the behavior documented in
    // \Drupal\Core\Entity\EntityStorageBase::loadMultiple().
    $this->configStorage->expects($this->any())
      ->method('loadMultiple')
      ->with($key_ids)
      ->willReturn($keys);

    // Assert that the array count is the same for the scenario provided by the
    // data provider above.
    $entities = $this->keyRepository->getKeys($key_ids);
    $this->assertEquals(count($keys), count($entities));
  }

  /**
   * Test load by key id.
   *
   * @group key
   */
  public function testGetKey() {
    $key = $this->keyRepository->getKey($this->key_id);
    $this->assertInstanceOf('\Drupal\key\Entity\Key', $key);
    $this->assertEquals($this->key->get('key_id'), $key->get('key_id'));
  }

  /**
   * Test get key value.
   *
   * @group key
   * @dataProvider defaultKeyContentProvider
   */
  public function testGetKeyValue($defaults, $KeyProvider) {
    // Make the key provider plugin manager return a plugin instance.
    $this->KeyProviderManager->expects($this->any())
      ->method('createInstance')
      ->with('config', $defaults)
      ->willReturn($KeyProvider);

    $this->key->set('key_provider_settings', $defaults);

    $settings = $this->keyRepository->getKey($this->key_id)->getKeyValue();
    $this->assertEquals($defaults['key_value'], $settings);
  }

  /**
   * Test get keys by provider.
   *
   * @group key
   */
  public function testGetKeysByProvider() {
    // Create a key provider plugin to play with.
    $defaults = ['key_value' => $this->createToken()];
    $definition = [
      'id' => 'config',
      'class' => 'Drupal\key\Plugin\KeyProvider\ConfigKeyProvider',
      'title' => 'Configuration',
    ];
    $KeyProvider = new ConfigKeyProvider($defaults, 'config', $definition);

    // Make the key provider plugin manager return a plugin instance.
    $this->KeyProviderManager->expects($this->any())
      ->method('createInstance')
      ->with('config', $defaults)
      ->willReturn($KeyProvider);

    // Mock the loadByProperties method in entity manager.
    $this->configStorage->expects($this->any())
      ->method('loadByProperties')
      ->with(['key_provider' => 'config'])
      ->willReturn([$this->key_id => $this->key]);

    $this->key->set('key_provider_settings', $defaults);

    $keys = $this->keyRepository->getKeysByProvider('config');
    $this->assertEquals($this->key, $keys[$this->key_id]);
  }

  /**
   * Test get keys by storage method.
   *
   * @group key
   */
  public function testGetKeysByStorageMethod() {
    // Create a key provider plugin to play with.
    $defaults = ['key_value' => $this->createToken()];
    $definition = [
      'id' => 'config',
      'class' => 'Drupal\key\Plugin\KeyProvider\ConfigKeyProvider',
      'title' => 'Configuration',
    ];
    $KeyProvider = new ConfigKeyProvider($defaults, 'config', $definition);

    // Mock the loadByProperties method in entity manager.
    $this->configStorage->expects($this->any())
      ->method('loadByProperties')
      ->with(['key_provider' => 'config'])
      ->willReturn([$this->key_id => $this->key]);

    // Make the key provider plugin manager return a plugin instance.
    $this->KeyProviderManager->expects($this->any())
      ->method('createInstance')
      ->with('config', $defaults)
      ->willReturn($KeyProvider);

    $this->key->set('key_provider_settings', $defaults);

    $keys = $this->keyRepository->getKeysByStorageMethod('config');
    $this->assertEquals([$this->key_id => $this->key], $keys);
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->key_id = $this->getRandomGenerator()->word(15);
    $defaults = [
      'key_id' => $this->key_id,
      'key_provider' => 'config'
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

    $this->container->set('plugin.manager.key.key_provider', $this->KeyProviderManager);
    \Drupal::setContainer($this->container);

    // Create a new key manager object.
    $this->keyRepository = new KeyRepository($this->entityManager, $this->configFactory, $this->KeyProviderManager);
  }

}
