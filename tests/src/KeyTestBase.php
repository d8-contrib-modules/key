<?php
/**
 * @file
 * Provides \Drupal\Tests\key\KeyTestBase.php.
 */

namespace Drupal\Tests\key;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Tests\UnitTestCase;

/**
 * @group key
 */
class KeyTestBase extends UnitTestCase {

  /**
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * @var \Drupal\Core\Config\Entity\ConfigEntityStorage
   */
  protected $configStorage;

  /**
   * @var \Drupal\Core\Entity\EntityManager
   */
  protected $entityManager;

  /**
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * @var \Drupal\Core\DependencyInjection\ContainerBuilder
   *
   * This should be used sparingly by test cases to add to the container as
   * necessary for tests.
   */
  protected $container;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Mock the Config object, but methods will be mocked in the test class.
    $this->config = $this->getMockBuilder('\Drupal\Core\Config\ImmutableConfig')
      ->disableOriginalConstructor()
      ->getMock();

    // Mock the ConfigFactory service.
    $this->configFactory = $this->getMockBuilder('\Drupal\Core\Config\ConfigFactory')
      ->disableOriginalConstructor()
      ->getMock();
    $this->configFactory->expects($this->any())
      ->method('get')
      ->with('key.default_config')
      ->willReturn($this->config);

    // Mock ConfigEntityStorage object, but methods will be mocked in the test
    // class.
    $this->configStorage = $this->getMockBuilder('\Drupal\Core\Config\Entity\ConfigEntityStorage')
      ->disableOriginalConstructor()
      ->getMock();

    // Mock EntityManager service.
    $this->entityManager = $this->getMockBuilder('\Drupal\Core\Entity\EntityManager')
      ->disableOriginalConstructor()
      ->getMock();
    $this->entityManager->expects($this->any())
      ->method('getStorage')
      ->with('key')
      ->willReturn($this->configStorage);

    // Create a dummy container.
    $this->container = new ContainerBuilder();
    $this->container->set('entity.manager', $this->entityManager);
    $this->container->set('config.factory', $this->configFactory);

    // Each test class should call \Drupal::setContainer() in its own setUp
    // method so that test classes can add mocked services to the container
    // without affecting other test classes.
  }

  /**
   * Return a token that could be a key.
   *
   * @return string
   *   A hashed string that could be confused as some secret token.
   */
  protected function createToken() {
    return strtoupper(hash('ripemd128', md5($this->getRandomGenerator()->string(30))));
  }

}
