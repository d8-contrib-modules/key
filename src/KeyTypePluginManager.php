<?php
/**
 * @file
 * Contains Drupal\key\KeyTypePluginManager.
 */

namespace Drupal\key;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;


class KeyTypePluginManager extends \Drupal\Core\Plugin\DefaultPluginManager {
  /**
   * Constructs a new KeyTypePluginManager.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/KeyType', $namespaces, $module_handler, 'Drupal\key\KeyTypeInterface', 'Drupal\key\Annotation\KeyType');
    $this->alterInfo('key_constraint_info');
    $this->setCacheBackend($cache_backend, 'key_type');
  }

}