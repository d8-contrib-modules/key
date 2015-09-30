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
    return $this->t('Are you sure you want to make %name the default key?', array('%name' => $this->entity->label()));
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
    return $this->t('Set Default');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->entity->setServiceDefault();

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
