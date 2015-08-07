<?php
/**
 * @file
 * Provides \Drupal\Tests\key\Plugin\KeyType\FileKeyTest
 */

namespace Drupal\Tests\key\Plugin\KeyType;

use Drupal\Tests\key\KeyTypeTestBase;

/**
 * Test the FileKey plugin.
 */
class FileKeyTest extends KeyTypeTestBase {

  const PLUGIN_CLASS = '\Drupal\key\Plugin\KeyType\FileKey';
  const PLUGIN_ID = 'key_type_file';
  const PLUGIN_TITLE = 'File Key';
  const PLUGIN_STORAGE = 'file';

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Create a private key.
    $output = '';
    $this->keyFile = sys_get_temp_dir() . '/' . $this->getRandomGenerator()->word(10) . '.key';
    $resource = openssl_pkey_new(['digest_alg' => 'sha1', 'private_key_bits' => 1024, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
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
   * Test the plugin configuration form when using file contents method.
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
        ['Method'],
        ['If the selected method is “File contents”, the contents of the file will be used as entered. If “MD5 hash” is selected, an MD5 hash of the file contents will be used as the key.'],
        ['File contents'],
        ['MD5 hash'],
        ['File does not exist or is not readable.']
      )
      ->willReturn('File does not exist or is not readable.');

    $form['key_settings'] = $this->plugin->buildConfigurationForm($form, $this->form_state);
    $this->assertNotNull($form['key_settings']['file_key_location']);
    $this->assertNotNull($form['key_settings']['file_key_method']);

    // Test that the file is validated.
    $this->form_state->setValues(['file_key_location' => 'bogus', 'file_key_method' => 'file_contents']);
    $this->plugin->validateConfigurationForm($form, $this->form_state);
    $this->assertEquals('File does not exist or is not readable.', $this->form_state->getErrors()['file_key_location']);

    // Set the form state value, and simulate a form submission.
    $this->form_state->clearErrors();
    $this->form_state->setValues(['file_key_location' => $this->keyFile, 'file_key_method' => 'file_contents']);
    $this->plugin->validateConfigurationForm($form, $this->form_state);
    $this->assertEmpty($this->form_state->getErrors());

    // Submission.
    $this->plugin->submitConfigurationForm($form, $this->form_state);
    $this->assertEquals($this->keyFile, $this->plugin->getConfiguration()['file_key_location']);
    $this->assertEquals('file_contents', $this->plugin->getConfiguration()['file_key_method']);

    // Make sure that the file contents are valid.
    $resource = openssl_pkey_get_private($this->plugin->getKeyValue());
    $this->assertNotFalse($resource);
  }

  /**
   * Test the plugin for MD5 hash storage.
   *
   * @group key
   */
  public function testMD5Key() {
    $form = [];

    $form['key_settings'] = $this->plugin->buildConfigurationForm($form, $this->form_state);

    // Set the form state value, and simulate a form submission.
    $this->form_state->setValues(['file_key_location' => $this->keyFile, 'file_key_method' => 'md5']);
    $this->plugin->validateConfigurationForm($form, $this->form_state);
    $this->assertEmpty($this->form_state->getErrors());

    // Submission.
    $this->plugin->submitConfigurationForm($form, $this->form_state);
    $this->assertEquals($this->keyFile, $this->plugin->getConfiguration()['file_key_location']);
    $this->assertEquals('md5', $this->plugin->getConfiguration()['file_key_method']);

    // Make sure that the md5 hash is valid.
    $this->assertEquals(md5_file($this->keyFile), $this->plugin->getKeyValue());
  }
}
