<?php

/**
 * @file
 * Contains Drupal\key\KeyProvider\ConfigKeyProvider.
 */


namespace Drupal\key\Plugin\KeyProvider;

use Drupal\Core\Form\FormStateInterface;
use Drupal\key\KeyProviderBase;

/**
 * Adds a key provider that allows a key to be stored in configuration.
 *
 * @KeyProvider(
 *   id = "config",
 *   title = @Translation("Configuration"),
 *   description = @Translation("Allows a key to be stored in Drupal's configuration system."),
 *   storage_method = "config",
 * )
 */
class ConfigKeyProvider extends KeyProviderBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'key_value' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['key_value'] = array(
      '#type' => 'textarea',
      '#title' => t('Key value'),
      '#description' => t("Enter the key to save in Drupal's configuration system."),
      '#required' => TRUE,
      '#default_value' => $this->getConfiguration()['key_value'],
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
    $this->configuration['key_value'] = $form_state->getValue('key_value');
  }

  /**
   * {@inheritdoc}
   */
  public function getKeyValue() {
    return $this->configuration['key_value'];
  }

}
