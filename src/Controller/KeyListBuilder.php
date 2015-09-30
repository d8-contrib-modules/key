<?php

/**
 * @file
 * Contains Drupal\key\Controller\KeyListBuilder.
 */

namespace Drupal\key\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Key.
 */
class KeyListBuilder extends ConfigEntityListBuilder {
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Key');
    $header['id'] = $this->t('Machine name');
    $header['service_default'] = $this->t('Default');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $this->getLabel($entity);
    $row['id'] = $entity->id();
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
