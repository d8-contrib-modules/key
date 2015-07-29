<?php

/**
 * @file
 * Contains Drupal\key\KeyType\FileKey.
 */


namespace Drupal\key\Plugin\KeyType;

use Drupal\Core\Form\FormStateInterface;
use Drupal\key\KeyTypeBase;

/**
 * Enforces a number of a type of character in passwords.
 *
 * @KeyType(
 *   id = "key_type_file",
 *   title = @Translation("File Key"),
 *   description = @Translation("This key type is stored within a file in the filesystem."),
 * )
 */
class FileKey extends KeyTypeBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'file_key_location' => '',
      'file_key_method' => 'file_contents',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['file_key_location'] = array(
      '#type' => 'textfield',
      '#title' => t('Key Location'),
      '#description' => t('The location of the file in which the key will be stored. The path may be absolute (e.g., %abs), relative to the Drupal directory (e.g., %rel), or defined using a stream wrapper (e.g., %str).', array(
        '%abs' => '/etc/keys/foobar.key',
        '%rel' => '../keys/foobar.key',
        '%str' => 'private://keys/foobar.key',
      )),
      '#required' => TRUE,
      '#default_value' => $this->getConfiguration()['file_key_location'],
    );
    $form['file_key_method'] = array(
      '#type' => 'select',
      '#title' => t('Method'),
      '#description' => t('If the selected method is “File contents”, the contents of the file will be used as entered. If “MD5 hash” is selected, an MD5 hash of the file contents will be used as the key.'),
      '#options' => array(
        'file_contents' => t('File contents'),
        'md5' => t('MD5 hash'),
      ),
      '#default_value' => $this->getConfiguration()['file_key_method'],
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

    switch ($this->getConfiguration()['file_key_method']) {
      case 'file_contents':
        $key = file_get_contents($file);
        break;
      case 'md5':
        $key = md5_file($file);
        break;
      default:
        $key = NULL;
    }

    return $key;
  }
}
