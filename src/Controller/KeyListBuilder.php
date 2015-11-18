<?php

/**
 * @file
 * Contains Drupal\key\Controller\KeyListBuilder.
 */

namespace Drupal\key\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\key\KeyProviderManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a listing of Key.
 */
class KeyListBuilder extends ConfigEntityListBuilder {

  private $KeyProviderManager;

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.manager')->getStorage($entity_type->id()),
      $container->get('plugin.manager.key.key_provider')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, KeyProviderManager $key_provider_manager) {
    parent::__construct($entity_type, $storage);

    $this->KeyProviderManager = $key_provider_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Key');
    $header['provider'] = array(
      'data' => t('Provider'),
      'class' => array(RESPONSIVE_PRIORITY_MEDIUM),
    );
    $header['service_default'] = $this->t('Default');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    $row['provider'] = $this->KeyProviderManager->getDefinition($entity->getKeyProvider())['title'];
    $row['service_default'] = ($entity->getServiceDefault())?"Yes":"No";
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $entities = $this->load();
    $build = parent::render();
    $build['table']['#empty'] = t('No keys are available. <a href="@link">Add a key</a>.', array('@link' => \Drupal::url('entity.key.add_form')));
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    /** @var \Drupal\Core\Config\Entity\ConfigEntityInterface $entity */
    $operations = parent::getDefaultOperations($entity);

    $message = 'Set Default';
    if ($entity->getServiceDefault()) {
      $message = 'Unset Default';
    }

    $operations['set_default'] = array(
      'title' => t($message),
      'weight' => 10,
      'url' => $entity->urlInfo('set-default'),
    );

    return $operations;
  }

}
