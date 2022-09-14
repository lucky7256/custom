<?php
 
/**
 * @file
 * Contains \Drupal\migrate_custom\Plugin\migrate\source\Article.
 */
 
namespace Drupal\migrate_car\Plugin\migrate\source;
 
use Drupal\migrate\Row;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
 
/**
 * @MigrateSource(
 *   id = "migrate_car_image_media",
 *   source_module = "migrate_car"
 * )
 */
class MigrateCarMedia extends SqlBase {
 
  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('field_data_field_car_image', 'm')
      ->fields('m', array(
        'entity_id',
        'language',
        'field_car_image_fid'
      ));
    $query->orderBy('entity_id');
    return $query;
  }
  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = $this->baseFields();
    return $fields;
  }
 
 /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $nid = $row->getSourceProperty('field_car_image_fid');
 
    // getting alt text
    $result = $this->getDatabase()->query('
      SELECT
      field_file_image_alt_text_value
      FROM
      field_data_field_file_image_alt_text
      WHERE
      entity_id = :nid
    ', array(':nid' => $nid));
    foreach ($result as $record) {
      $row->setSourceProperty('field_file_image_alt_text_value', $record->field_file_image_alt_text_value );
    }
 
    // get the title of the image
    // (here we use MySQL's GROUP_CONCAT() function to merge all values into one row.)
    $result = $this->getDatabase()->query('
      SELECT
      field_file_image_title_text_value
      FROM
      field_data_field_file_image_title_text
      WHERE
      entity_id = :nid
    ', array(':nid' => $nid));
    foreach ($result as $record) {

        $row->setSourceProperty('field_file_image_title_text_value',$record->field_file_image_title_text_value );
      

    }
    return parent::prepareRow($row);
 
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {

    $ids['entity_id']['type'] = 'integer';
    $ids['entity_id']['alias'] = 'm';
    return $ids;
  }
 
  /**
   * {@inheritdoc}
   */
  public function bundleMigrationRequired() {
    return FALSE;
  }
 
  /**
   * {@inheritdoc}
   */
  public function entityTypeId() {
    return 'node';
  }
 
  /**
   * Returns the user base fields to be migrated.
   *
   * @return array
   *   Associative array having field name as key and description as value.
   */
  protected function baseFields() {
    $fields = array(
      'entity_id' => $this->t('mid'),
      'language'=>$this->t('language'),
      'field_car_image_fid'=>$this->t('field_car_image_fid'),
      'field_file_image_title_text_value'=>$this->t('field_file_image_title_text_value'),
      'field_file_image_alt_text_value'=>$this->t('field_file_image_alt_text_value')
    );
    return $fields;
  }
 
}