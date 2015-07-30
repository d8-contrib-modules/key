<?php

/**
 * @file
 * Contains \Drupal\key\KeyManager.
 */

namespace Drupal\key;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Responsible for the key service.
 */
class KeyManager {

  /**
   * Create the KeyManager.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entityManager
   *   The entity manager.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   */
  public function __construct(EntityManagerInterface $entityManager, ConfigFactoryInterface $configFactory) {
    $this->entityManager = $entityManager;
    $this->configFactory = $configFactory;
  }

  /*
   * Loading a specific key.
   *
   * @param string $key_id
   *   The key ID to use.
   */
  public function getKeys() {
    return $this->entityManager->getStorage('key')->loadMultiple();
  }

  /*
   * Loading a specific key.
   *
   * @param string $key_id
   *   The key ID to use.
   */
  public function getKey($key_id) {
    return $this->entityManager->getStorage('key')->load($key_id);
  }

  /*
   * Loading key contents for a specific key.
   *
   * @param string $key_id
   *   The key ID to use.
   */
  public function getKeyValue($key_id) {
    return $this->entityManager->getStorage('key')->load($key_id)->getKeyValue();
  }

  /*
   * Loading the configured default key.
   */
  public function getDefaultKey() {
    $key_id = $this->configFactory->get('key.default_config')->get('default_key');
    if ($key_id) {
      return $this->entityManager->getStorage('key')->load($key_id);
    }
    return NULL;
  }

  /*
   * Loading the key contents for the configured default key.
   */
  public function getDefaultKeyContents() {
    $key_id = \Drupal::config('key.default_config')->get('default_key');
    if ($key_id) {
      $key = \Drupal::entityManager()->getStorage('key')->load($key_id);
      return $key->getContents();
    }
    return NULL;
  }

}
