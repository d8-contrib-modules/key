<?php

/**
 * @file
 * Contains Drupal\key\KeyType\SimpleKey.
 */


namespace Drupal\key\Plugin\KeyType;

use Drupal\Core\Form\FormStateInterface;
use Drupal\key\KeyTypeBase;

/**
 * Enforces a number of a type of character in passwords.
 *
 * @KeyType(
 *   id = "key_type_simple",
 *   title = @Translation("Simple Key"),
 *   description = @Translation("This key type is stored within the Drupal database."),
 *   storage_method = "config",
 * )
 */
class SimpleKey extends KeyTypeBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'simple_key_value' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['simple_key_value'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Key Value'),
      '#description' => $this->t('Enter the value of the key'),
      '#required' => TRUE,
      '#default_value' => $this->getConfiguration()['simple_key_value'],
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['simple_key_value'] = $form_state->getValue('simple_key_value');
  }

  /**
   * {@inheritdoc}
   */
  public function getKeyValue() {
    return $this->configuration['simple_key_value'];
  }

}
