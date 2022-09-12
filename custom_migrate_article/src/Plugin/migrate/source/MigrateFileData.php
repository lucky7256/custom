<?php

namespace Drupal\custom_migrate_article\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Minimalistic example for a SqlBase source plugin.
 *
 * @MigrateSource(
 *   id = "custom_migrate_files",
 *   source_module = "custom_migrate_files",
 * )
 */
class MigrateFileData extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {


    $query = $this->select('file_managed', 'fm')
      ->fields('fm', array(
        'fid',
        'uid',
    'filename',	
    'uri',	
    'filemime',	
    'filesize',	
    'status',	
    'timestamp',
      ));
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
   $fields="";
  return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {

    $ids['fid']['type'] = 'integer';
    $ids['fid']['alias'] = 'fm';
    return $ids;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    
    return parent::prepareRow($row);
 
  }

  
}