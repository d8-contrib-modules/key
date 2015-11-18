<?php

/**
 * @file
 * Contains Drupal\key\KeyInterface.
 */

namespace Drupal\key;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a Key entity.
 */
interface KeyInterface extends ConfigEntityInterface {

  /**
   * The description for the key.
   *
   * @return string
   */
  public function getDescription();

  /**
   * The plugin id of the selected key.
   *
   * @return string
   */
  public function getKeyProvider();

  /**
   * The plugin configuration for the selected key.
   *
   * @return array
   */
  public function getKeyProviderSettings();

  /**
   * If the key is the service default.
   *
   * @return boolean
   */
  public function getServiceDefault();

  /**
   * If the key is the service default.
   *
   * @param $is_default boolean
   */
  public function setServiceDefault($is_default);

  /**
   * Gets the value of the key.
   *
   * @return string
   */
  public function getKeyValue();

}
