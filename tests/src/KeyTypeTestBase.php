<?php
/**
 * @file
 * Provides \Drupal\Tests\key\KeyTypeTestBase
 */

namespace Drupal\Tests\key;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Form\FormState;
use Drupal\Tests\Core\Form\FormTestBase;

/**
 * Provides a base form to test plugin form methods.
 */
abstract class KeyTypeTestBase extends FormTestBase {

  /**
   * @var \Drupal\Core\Form\FormState
   */
  protected $form_state;

  /**
   * @var \Drupal\key\KeyTypeInterface
   */
  protected $plugin;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Despite the protected variable being defined in FormTestBase it is not
    // defined.
    $this->translationManager = $this->getMockBuilder('Drupal\Core\StringTranslation\TranslationManager')
      ->disableOriginalConstructor()
      ->getMock();

    $this->container = new ContainerBuilder();
    $this->container->set('string_translation', $this->translationManager);
    \Drupal::setContainer($this->container);

    $this->form_state = new FormState();
    $definition = [
      'id' => static::PLUGIN_ID,
      'title' => static::PLUGIN_TITLE,
      'storage_method' => static::PLUGIN_STORAGE
    ];

    $plugin_class = static::PLUGIN_CLASS;
    $this->plugin = new $plugin_class([], static::PLUGIN_ID, $definition);
  }
}
