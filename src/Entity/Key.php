<?php

/**
 * @file
 * Contains Drupal\key\Entity\Key.
 */

namespace Drupal\key\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\key\KeyInterface;

/**
 * Defines the Key entity.
 *
 * @ConfigEntityType(
 *   id = "key",
 *   label = @Translation("Key"),
 *   handlers = {
 *     "list_builder" = "Drupal\key\Controller\KeyListBuilder",
 *     "form" = {
 *       "add" = "Drupal\key\Form\KeyForm",
 *       "edit" = "Drupal\key\Form\KeyForm",
 *       "delete" = "Drupal\key\Form\KeyDeleteForm"
 *     }
 *   },
 *   config_prefix = "key",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "edit-form" = "entity.key.edit_form",
 *     "delete-form" = "entity.key.delete_form",
 *     "collection" = "entity.key.collection"
 *   }
 * )
 */
class Key extends ConfigEntityBase implements KeyInterface {
  /**
   * The Key ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Key label.
   *
   * @var string
   */
  protected $label;

  protected $key_type;

  protected $key_settings = [];

  public function getKeyType() {
    return $this->key_type;
  }

  public function getKeySettings() {
    return $this->key_settings;
  }

  /*
   * Returns key contents.
   */
  public function getKeyValue(){
    // Create instance of the plugin.
    $plugin = \Drupal::service('plugin.manager.key.key_type');
    $key_type = $plugin->createInstance($this->key_settings['id'], $this->key_type);

    // Return it's key contents.
    return $key_type->getKeyValue();
  }

}
