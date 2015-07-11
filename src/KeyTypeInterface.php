<?php

/**
 * @file
 * Contains Drupal\key\KeyTypeInterface.
 */

namespace Drupal\key;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Plugin\PluginFormInterface;

/**
 * Provides an interface defining a Key Type plugin.
 */
interface KeyTypeInterface extends PluginInspectionInterface, ConfigurablePluginInterface, PluginFormInterface {
  /**
   * Returns a translated string for the constraint title.
   * @return string
   */
  public function getTitle();

  /**
   * Returns a translated description for the constraint description.
   * @return string
   */
  public function getDescription();

  /**
   * Returns the value of a key from the key type.
   * @return string
   */
  public function getKeyValue();

}
