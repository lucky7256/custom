<?php
 
/**
 * @file
 * Contains \Drupal\migrate_custom\Plugin\migrate\source\Article.
 */
 
namespace Drupal\migrate_car\Plugin\migrate\source;
 
use Drupal\migrate\Row;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
 
/**
 * Drupal 7 Blog node source plugin
 *
 * @MigrateSource(
 *   id = "migrate_car"
 * )
 */
class MigrateCar extends SqlBase {
 
  /**
   * {@inheritdoc}
   */
  public function query() {
    // this queries the built-in metadata, but not the body, tags, or images.
    $query = $this->select('node', 'n')
      ->condition('n.type', 'car')
      ->fields('n', array(
        'nid',
        'vid',
        'type',
        'language',
        'title',
        'uid',
        'status',
        'created',
        'changed',
        'promote',
        'sticky',
      ));
    $query->orderBy('nid');
    return $query;
  }
 
  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = $this->baseFields();
    $fields['body/format'] = $this->t('Format of body');
    $fields['body/value'] = $this->t('Full text of body');
    $fields['body/summary'] = $this->t('Summary of body');
    return $fields;
  }
 
  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $nid = $row->getSourceProperty('nid');
 
    // body (compound field with value, summary, and format)
    $result = $this->getDatabase()->query('
      SELECT
        fld.body_value,
        fld.body_summary,
        fld.body_format
      FROM
        {field_data_body} fld
      WHERE
        fld.entity_id = :nid
    ', array(':nid' => $nid));
    foreach ($result as $record) {
      $row->setSourceProperty('body_value', $record->body_value );
      $row->setSourceProperty('body_summary', $record->body_summary );
      $row->setSourceProperty('body_format', $record->body_format );
    }
 
    // taxonomy term IDs
    // (here we use MySQL's GROUP_CONCAT() function to merge all values into one row.)
    $result = $this->getDatabase()->query('
      SELECT
        GROUP_CONCAT(fld.field_car_color_tid) as tids
      FROM
        {field_data_field_car_color} fld
      WHERE
        fld.entity_id = :nid
    ', array(':nid' => $nid));
    foreach ($result as $record) {
      if (!is_null($record->tids)) {
        $row->setSourceProperty('car_color', explode(',', $record->tids) );
      }
    }
 
    // images
    $result = $this->getDatabase()->query('
      SELECT
      field_car_image_fid
      FROM
        field_data_field_car_image
      WHERE
        entity_id = :nid
    ', array(':nid' => $nid));
    // Create an associative array for each row in the result. The keys
    // here match the last part of the column name in the field table. 
    
    foreach ($result as $record) {

        $row->setSourceProperty('images', $record->field_car_image_fid);
   
    }
  
    return parent::prepareRow($row);
  }
 
  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['nid']['type'] = 'integer';
    $ids['nid']['alias'] = 'n';
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
      'nid' => $this->t('Node ID'),
      'vid' => $this->t('Version ID'),
      'type' => $this->t('Type'),
      'title' => $this->t('Title'),
      'format' => $this->t('Format'),
      'teaser' => $this->t('Teaser'),
      'uid' => $this->t('Authored by (uid)'),
      'created' => $this->t('Created timestamp'),
      'changed' => $this->t('Modified timestamp'),
      'status' => $this->t('Published'),
      'promote' => $this->t('Promoted to front page'),
      'sticky' => $this->t('Sticky at top of lists'),
      'language' => $this->t('Language (fr, en, ...)'),
      ''
    );
    return $fields;
  }
 
}