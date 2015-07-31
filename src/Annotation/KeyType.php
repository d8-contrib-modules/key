<?php

/**
 * @file
 * Contains Drupal\key\Annotation\KeyType.
 */

namespace Drupal\key\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a key type annotation object.
 *
 * @Annotation
 */
class KeyType extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the constraint type.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $title;

  /**
   * The description shown to users.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $description;

  /**
   * The storage method of the key type.
   *
   * This is an enumeration of {file, config, database, remote}
   *
   * @var string
   */
  public $storage_method;

}
