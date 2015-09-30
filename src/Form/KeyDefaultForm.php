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
      $message = 'Are you sure you want to remove the default key from %name?';
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
      return $this->t('Remove Default');
    } else {
      return $this->t('Set Default');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if ($this->entity->getServiceDefault()) {
      $this->entity->removeServiceDefault();
    } else {
      $this->entity->setServiceDefault();
    }

    drupal_set_message(
      $this->t('content @type: @label is now default.',
        [
          '@type' => $this->entity->bundle(),
          '@label' => $this->entity->label()
        ]
        )
    );

    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
