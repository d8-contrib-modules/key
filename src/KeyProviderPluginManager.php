<?php
/**
 * @file
 * Contains Drupal\key\KeyProviderPluginManager.
 */

namespace Drupal\key;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;


class KeyProviderPluginManager extends \Drupal\Core\Plugin\DefaultPluginManager {
  /**
   * Constructs a new KeyProviderPluginManager.
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
    parent::__construct('Plugin/KeyProvider', $namespaces, $module_handler, 'Drupal\key\KeyProviderInterface', 'Drupal\key\Annotation\KeyProvider');
    $this->alterInfo('key_constraint_info');
    $this->setCacheBackend($cache_backend, 'key_provider');
  }

}