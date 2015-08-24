<?php

/**
 * @file
 * Contains Drupal\key\KeyProvider\FileKey.
 */


namespace Drupal\key\Plugin\KeyProvider;

use Drupal\Core\Form\FormStateInterface;
use Drupal\key\KeyProviderBase;

/**
 * Enforces a number of a type of character in passwords.
 *
 * @KeyProvider(
 *   id = "key_provider_file",
 *   title = @Translation("File Key"),
 *   description = @Translation("This key provider is stored within a file in the filesystem."),
 *   storage_method = "file",
 * )
 */
class FileKey extends KeyProviderBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'file_key_location' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['file_key_location'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Key Location'),
      '#description' => $this->t('The location of the file in which the key will be stored. The path may be absolute (e.g., %abs), relative to the Drupal directory (e.g., %rel), or defined using a stream wrapper (e.g., %str).', array(
        '%abs' => '/etc/keys/foobar.key',
        '%rel' => '../keys/foobar.key',
        '%str' => 'private://keys/foobar.key',
      )),
      '#required' => TRUE,
      '#default_value' => $this->getConfiguration()['file_key_location'],
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    $file = $form_state->getValue('file_key_location');

    // Does the file exist and is it readable?
    if (!is_file($file) || !is_readable($file)) {
      $form_state->setErrorByName('file_key_location', $this->t('File does not exist or is not readable.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['file_key_location'] = $form_state->getValue('file_key_location');
  }

  /**
   * {@inheritdoc}
   */
  public function getKeyValue() {
    $file = $this->configuration['file_key_location'];

    // Make sure the file exists and is readable.
    if (!is_file($file) || !is_readable($file)) {
      return NULL;
    }

    $key = file_get_contents($file);

    return $key;
  }
}
