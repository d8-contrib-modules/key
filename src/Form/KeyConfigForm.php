<?php

/**
 * @file
 * Contains Drupal\key\Form\KeyConfigForm.
 */

namespace Drupal\key\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class KeyConfigForm.
 *
 * @package Drupal\key\Form
 */
class KeyConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'key.default_config'
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'key_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('key.default_config');

    // TODO - Load keys in here and set options.
    $form['default_key'] = array(
      '#type' => 'select',
      '#title' => $this->t('Default Key'),
      '#description' => $this->t('Select which key to load by default'),
      '#options' => array($this->t('test') => $this->t('test'), $this->t('test1') => $this->t('test1'), $this->t('test2') => $this->t('test2')),
      '#default_value' => $config->get('default_key'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('key.default_config')
      ->set('default_key', $form_state->getValue('default_key'))
      ->save();
  }

}
