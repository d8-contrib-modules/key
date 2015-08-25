<?php
/**
 * @file
 * Provides \Drupal\Tests\key\Plugin\KeyProvider\FileKeyProviderTest
 */

namespace Drupal\Tests\key\Plugin\KeyProvider;

use Drupal\Tests\key\KeyProviderTestBase;

/**
 * Test the FileKeyProvider plugin.
 */
class FileKeyProviderTest extends KeyProviderTestBase {

  const PLUGIN_CLASS = '\Drupal\key\Plugin\KeyProvider\FileKeyProvider';
  const PLUGIN_ID = 'file';
  const PLUGIN_TITLE = 'File';
  const PLUGIN_STORAGE = 'file';

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Create a private key.
    $output = '';
    $this->keyFile = sys_get_temp_dir() . '/' . $this->getRandomGenerator()->word(10) . '.key';
    $resource = openssl_pkey_new(['digest_alg' => 'sha1', 'private_key_bits' => 1024, 'private_key_provider' => OPENSSL_KEYTYPE_RSA]);
    openssl_pkey_export($resource, $output);

    file_put_contents($this->keyFile, $output);
  }

  /**
   * {@inheritdoc}
   */
  protected function tearDown() {
    if (file_exists($this->keyFile)) {
      unlink($this->keyFile);
    }

    parent::tearDown();
  }

  /**
   * Test the plugin configuration form.
   *
   * @group key
   */
  public function testFileContentsKey() {
    $form = [];

    // Mock the translation manager translate method. This test does not assert
    // any other translation messages so the return value will always be the
    // same message on each consecutive call to t().
    $this->translationManager->expects($this->any())
      ->method('translate')
      ->withConsecutive(
        ['Key Location'],
        ['The location of the file in which the key will be stored. The path may be absolute (e.g., %abs), relative to the Drupal directory (e.g., %rel), or defined using a stream wrapper (e.g., %str).'],
        ['File does not exist or is not readable.']
      )
      ->willReturn('File does not exist or is not readable.');

    $form['key_settings'] = $this->plugin->buildConfigurationForm($form, $this->form_state);
    $this->assertNotNull($form['key_settings']['file_location']);

    // Test that the file is validated.
    $this->form_state->setValues(['file_location' => 'bogus']);
    $this->plugin->validateConfigurationForm($form, $this->form_state);
    $this->assertEquals('File does not exist or is not readable.', $this->form_state->getErrors()['file_location']);

    // Set the form state value, and simulate a form submission.
    $this->form_state->clearErrors();
    $this->form_state->setValues(['file_location' => $this->keyFile]);
    $this->plugin->validateConfigurationForm($form, $this->form_state);
    $this->assertEmpty($this->form_state->getErrors());

    // Submission.
    $this->plugin->submitConfigurationForm($form, $this->form_state);
    $this->assertEquals($this->keyFile, $this->plugin->getConfiguration()['file_location']);

    // Make sure that the file contents are valid.
    $resource = openssl_pkey_get_private($this->plugin->getKeyValue());
    $this->assertNotFalse($resource);
  }

}
