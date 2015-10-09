<?php

/**
 * @file
 * Contains \Drupal\key\KeyRepository.
 */

namespace Drupal\key;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Responsible for the key management service.
 */
class KeyRepository {

  /**
   * Create the KeyRepository.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entityManager
   *   The entity manager.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \Drupal\Component\Plugin\PluginManagerInterface $pluginManager
   *   The plugin manager.
   */
  public function __construct(EntityManagerInterface $entityManager, ConfigFactoryInterface $configFactory, PluginManagerInterface $pluginManager) {
    $this->entityManager = $entityManager;
    $this->configFactory = $configFactory;
    $this->pluginManager = $pluginManager;
  }

  /*
   * Loading all keys.
   *
   * @param array $key_ids
   *   (optional) An array of entity IDs, or NULL to load all entities.
   * @return \Drupal\key\Entity\Key[]
   *   An array of keys indexed by their IDs. Returns an empty array if no
   *   matching entities are found.
   */
  public function getKeys(array $key_ids = NULL) {
    return $this->entityManager->getStorage('key')->loadMultiple($key_ids);
  }

  /*
   * Loading keys that are of the specified key provider.
   *
   * @param string $key_provider
   *   The key provider ID to use.
   */
  public function getKeysByProvider($key_provider_id) {
    return $this->entityManager->getStorage('key')->loadByProperties(array('key_provider' => $key_provider_id));
  }

  /*
   * Loading keys that are of the specified storage method.
   *
   * Storage method is an annotation of a key's key provider.
   *
   * @param string $storage_method
   *   The storage method of the key provider.
   */
  public function getKeysByStorageMethod($storage_method) {
    $key_providers = array_filter($this->pluginManager->getDefinitions(), function ($definition) use ($storage_method) {
      return $definition['storage_method'] == $storage_method;
    });

    $keys = [];
    foreach ($key_providers as $key_provider) {
      $keys = array_merge($keys, $this->getKeysByProvider($key_provider['id']));
    }
    return $keys;
  }

  /*
   * Loading a specific key.
   *
   * @param string $key_id
   *   The key ID to use.
   */
  public function getKey($key_id = NULL) {
    if ($key_id) {
      return $this->entityManager->getStorage('key')->load($key_id);
    } else {
      return $this->getDefaultKey();
    }
  }

  /**
   * Loading a default key.
   */
  public function getDefaultKey() {
    $keys = $this->entityManager->getStorage('key')->loadByProperties(['service_default'=>TRUE]);

    if (empty($keys)){
      throw new \Exception('There is no default key set for the key manager to process.');
    } else {
      return array_shift($keys);
    }
  }

  /**
   * Sets the key as service default.
   */
  public function setDefaultKey(KeyInterface $key) {
    $entities = \Drupal::entityManager()
      ->getStorage('key')
      ->loadByProperties(['service_default'=>TRUE]);
    foreach ($entities as $entity) {
      $entity->service_default = FALSE;
      $entity->save();
    }

    $key->setServiceDefault(TRUE);
  }

  /**
   * Removes the key as service default.
   */
  public function removeDefaultKey(KeyInterface $key) {
    $key->setServiceDefault(FALSE);
  }

}
