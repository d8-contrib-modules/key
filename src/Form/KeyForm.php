<?php

/**
 * @file
 * Contains Drupal\key\Form\KeyForm.
 */

namespace Drupal\key\Form;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\ReplaceCommand;

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

    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $key_types = [];
    foreach ($this->manager->getDefinitions() as $plugin_id => $definition) {
      $key_types[$plugin_id] = (string) $definition['title'];
    }

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

    $form['key_type'] = array(
      '#type' => 'select',
      '#options' => $key_types,
      '#ajax' => [
        'callback' => [$this, 'getKeyTypeForm'],
        'event' => 'select',
      ],
    );

    $form['key_type_form'] = array(
      '#type' => 'container',
      '#attributes' => array(
        'id' => array('key-type-form')
      ),
    );


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

  /**
   * AJAX action to retrieve the appropriate key type into the form.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @return \Drupal\key\Form\AjaxResponse
   */
  public function getKeyTypeForm(array &$form, FormStateInterface $form_state) {
    $key_type = $form_state->getValue('key_type');
    $content = \Drupal::formBuilder()->getForm('\Drupal\key\Form\KeyTypeForm', $key_type, $this->machine_name);
    $content['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $response = new AjaxResponse();
    $response->addCommand(new ReplaceCommand('#key-type-form', $content));
    return $response;
  }

}
