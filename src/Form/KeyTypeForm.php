<?php
/**
 * Created by PhpStorm.
 * User: kris
 * Date: 4/24/15
 * Time: 4:14 PM
 */

namespace Drupal\key\Form;


use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class KeyTypeForm extends FormBase {

  /**
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $manager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('plugin.manager.key.key_type'));
  }

  function __construct(PluginManagerInterface $manager) {
    $this->manager = $manager;
  }

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'key_type_form';
  }

  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $key_type_id = NULL) {
    if (is_numeric($key_type_id)) {
      $instance = $this->manager->createInstance($key_type_id['id'], $key_type_id);
    }
    else {
      $instance = $this->manager->createInstance($key_type_id, []);
    }
    /** @var $instance \Drupal\key\KeyInterface */
    return $instance->buildConfigurationForm($form, $form_state);

    if (isset($id)) {
      // Conditionally set this form element so that we can update or add.
      $form['id'] = [
        '#type' => 'value',
        '#value' => $id
      ];
    }
    $form['instance'] = [
      '#type' => 'value',
      '#value' => $instance
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#ajax' => [
        'callback' => [$this, 'ajaxSave'],
      ]
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /*$cached_values = $this->tempstore->get($this->tempstore_id)->get($this->machine_name);
    /** @var $instance \Drupal\password_policy\PasswordConstraintInterface */
    $instance = $form_state->getValue('instance');
    $instance->submitConfigurationForm($form, $form_state);
    if ($form_state->hasValue('id')) {
      $cached_values['policy_constraints'][$form_state->getValue('id')] = $instance->getConfiguration();
    }
    else {
      $cached_values['policy_constraints'][] = $instance->getConfiguration();
    }
    $this->tempstore->get($this->tempstore_id)->set($this->machine_name, $cached_values);
    $form_state->setRedirect('entity.password_policy.wizard.edit', ['machine_name' => $this->machine_name, 'step' => 'constraint']);
  }

}
