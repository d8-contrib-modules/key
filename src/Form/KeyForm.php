<?php

/**
 * @file
 * Contains Drupal\key\Form\KeyForm.
 */

namespace Drupal\key\Form;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class KeyForm.
 *
 * @package Drupal\key\Form
 */
class KeyForm extends EntityForm {
  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $key = $this->entity;
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $key->label(),
      '#description' => $this->t("Label for the Key."),
      '#required' => TRUE,
    );

    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $key->id(),
      '#machine_name' => array(
        'exists' => '\Drupal\key\Entity\Key::load',
      ),
      '#disabled' => !$key->isNew(),
    );

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $key = $this->entity;
    $status = $key->save();

    if ($status) {
      drupal_set_message($this->t('Saved the %label Key.', array(
        '%label' => $key->label(),
      )));
    }
    else {
      drupal_set_message($this->t('The %label Key was not saved.', array(
        '%label' => $key->label(),
      )));
    }
    $form_state->setRedirectUrl($key->urlInfo('collection'));
  }

}
