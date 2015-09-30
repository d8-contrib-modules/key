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
  public function getKeySettings();

  /**
   * If the key is the service default.
   *
   * @return boolean
   */
  public function getServiceDefault();

  /**
   * Sets the key as service default.
   */
  public function setServiceDefault();

  /**
   * Removes the key as service default.
   */
  public function removeServiceDefault();

}
