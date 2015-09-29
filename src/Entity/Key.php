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
 *       "delete" = "Drupal\key\Form\KeyDeleteForm",
 *       "default" = "Drupal\key\Form\KeyDefaultForm"
 *     }
 *   },
 *   config_prefix = "key",
 *   admin_permission = "administer keys",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "add-form" = "/admin/config/security/key/add",
 *     "edit-form" = "/admin/config/security/key/manage/{key}",
 *     "delete-form" = "/admin/config/security/key/manage/{key}/delete",
 *     "collection" = "/admin/config/security/key",
 *     "set-default" = "/admin/config/security/key/manage/{key}/default",
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

  protected $description;

  protected $key_provider;

  protected $key_settings = [];

  protected $service_default;

  public function getDescription() {
    return $this->description;
  }

  public function getKeyProvider() {
    return $this->key_provider;
  }

  public function getKeySettings() {
    return $this->key_settings;
  }

  public function getServiceDefault() {
    return $this->service_default;
  }

  /**
   * {@inheritdoc}
   */
  public function setServiceDefault() {
    $entities = \Drupal::entityManager()
      ->getStorage('key')
      ->loadByProperties(['service_default'=>TRUE]);
    foreach ($entities as $entity) {
      $entity->service_default = FALSE;
      $entity->save();
    }

    $this->service_default = TRUE;
    $this->save();
  }

  /*
   * Returns key contents.
   */
  public function getKeyValue() {
    // Create instance of the plugin.
    $plugin = \Drupal::service('plugin.manager.key.key_provider');
    $key_provider = $plugin->createInstance($this->key_provider, $this->key_settings);

    // Return its key contents.
    return $key_provider->getKeyValue();
  }

}
