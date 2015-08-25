<?php
/**
 * @file
 * Provides \Drupal\Tests\key\Plugin\KeyProvider\ConfigKeyProviderTest
 */

namespace Drupal\Tests\key\Plugin\KeyProvider;

use Drupal\Tests\key\KeyProviderTestBase;

/**
 * Test the ConfigKeyProvider plugin.
 */
class ConfigKeyProviderTest extends KeyProviderTestBase {

  const PLUGIN_CLASS = '\Drupal\key\Plugin\KeyProvider\ConfigKeyProvider';
  const PLUGIN_ID = 'config';
  const PLUGIN_TITLE = 'Configuration';
  const PLUGIN_STORAGE = 'config';

  /**
   * Test the plugin configuration form.
   *
   * @group key
   */
  public function testPluginForm() {
    $value = $this->getRandomGenerator()->word(10);
    $form = [];

    $form['key_settings'] = $this->plugin->buildConfigurationForm($form, $this->form_state);
    $this->assertNotNull($form['key_settings']['key_value']);
    $this->assertEmpty($form['key_settings']['key_value']['#default_value']);

    // Set the form state value, and simulate a form submission.
    $this->form_state->setValues(['key_value' => $value]);
    $this->plugin->validateConfigurationForm($form, $this->form_state);
    $this->assertEmpty($this->form_state->getErrors());

    // Submission.
    $this->plugin->submitConfigurationForm($form, $this->form_state);
    $this->assertEquals($value, $this->plugin->getConfiguration()['key_value']);
  }
}
