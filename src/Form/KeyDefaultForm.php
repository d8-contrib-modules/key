<?php

/**
 * @file
 * Contains Drupal\key\Form\KeyDefaultForm.
 */

namespace Drupal\key\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Builds the form to make key entities default.
 */
class KeyDefaultForm extends EntityConfirmFormBase {
  /**
   * {@inheritdoc}
   */
  public function getQuestion() {

    $message = 'Are you sure you want to make %name the default key?';

    if ($this->entity->getServiceDefault()) {
      $message = 'Are you sure you want to unset %name as the default key?';
    }

    return $this->t($message, array('%name' => $this->entity->label()));
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->t('');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.key.collection');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    if ($this->entity->getServiceDefault()) {
      return $this->t('Unset Default');
    } else {
      return $this->t('Set Default');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if ($this->entity->getServiceDefault()) {
      \Drupal::service('key_repository')->removeDefaultKey($this->entity);
      drupal_set_message($this->t('%label is no longer the default key.', ['%label' => $this->entity->label()]));
    } else {
      \Drupal::service('key_repository')->setDefaultKey($this->entity);
      drupal_set_message($this->t('%label is now the default key.', ['%label' => $this->entity->label()]));
    }

    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
