<?php

/**
 * @file
 * Contains Drupal\key\KeyProviderInterface.
 */

namespace Drupal\key;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Plugin\PluginFormInterface;

/**
 * Provides an interface defining a Key Provider plugin.
 */
interface KeyProviderInterface extends PluginInspectionInterface, ConfigurablePluginInterface, PluginFormInterface {

  /**
   * Returns the value of a key from the key provider.
   * @return string
   */
  public function getKeyValue(KeyInterface $key);

}
