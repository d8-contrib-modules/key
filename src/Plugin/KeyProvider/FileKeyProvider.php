<?php

/**
 * @file
 * Contains Drupal\key\KeyProvider\FileKeyProvider.
 */


namespace Drupal\key\Plugin\KeyProvider;

use Drupal\Core\Form\FormStateInterface;
use Drupal\key\KeyProviderBase;

use Drupal\key\KeyInterface;

/**
 * Adds a key provider that allows a key to be stored in a file.
 *
 * @KeyProvider(
 *   id = "file",
 *   title = @Translation("File"),
 *   description = @Translation("Allows a key to be stored in a file within the filesystem."),
 *   storage_method = "file",
 * )
 */
class FileKeyProvider extends KeyProviderBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'file_location' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['file_location'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('File location'),
      '#description' => $this->t('The location of the file in which the key will be stored. The path may be absolute (e.g., %abs), relative to the Drupal directory (e.g., %rel), or defined using a stream wrapper (e.g., %str).', array(
        '%abs' => '/etc/keys/foobar.key',
        '%rel' => '../keys/foobar.key',
        '%str' => 'private://keys/foobar.key',
      )),
      '#required' => TRUE,
      '#default_value' => $this->getConfiguration()['file_location'],
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    $key_provider_settings = $form_state->getValue('key_provider_settings');
    $file = $key_provider_settings['file_location'];

    // Does the file exist and is it readable?
    if (!is_file($file) || !is_readable($file)) {
      $form_state->setErrorByName('file_location', $this->t('File does not exist or is not readable.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $key_provider_settings = $form_state->getValue('key_provider_settings');
    $this->configuration['file_location'] = $key_provider_settings['file_location'];
  }

  /**
   * {@inheritdoc}
   */
  public function getKeyValue(KeyInterface $key) {
    $file = $this->configuration['file_location'];

    // Make sure the file exists and is readable.
    if (!is_file($file) || !is_readable($file)) {
      return NULL;
    }

    $key = file_get_contents($file);

    return $key;
  }
}
