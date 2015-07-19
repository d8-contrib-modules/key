<?php

/**
 * @file
 * Contains \Drupal\key\KeyManager.
 */

namespace Drupal\key;

/**
 * Responsible for the key service.
 */
class KeyManager {
  /*
   * Loading a specific key.
   *
   * @param string $key_id
   *   The key ID to use.
   */
  public function getKeys() {
    return \Drupal::entityManager()->getStorage('key')->loadMultiple();
  }

  /*
   * Loading a specific key.
   *
   * @param string $key_id
   *   The key ID to use.
   */
  public function getKey($key_id) {
    return \Drupal::entityManager()->getStorage('key')->load($key_id);
  }

  /*
   * Loading key contents for a specific key.
   *
   * @param string $key_id
   *   The key ID to use.
   */
  public function getKeyValue($key_id) {
    return \Drupal::entityManager()->getStorage('key')->load($key_id)->getKeyValue();
  }

  /*
   * Loading the configured default key.
   */
  public function getDefaultKey() {
    $key_id = \Drupal::config('key.default_config')->get('default_key');
    if ($key_id) {
      return \Drupal::entityManager()->getStorage('key')->load($key_id);
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
