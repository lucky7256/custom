<?php

namespace Drupal\migrate_article\Plugin\migrate\source;


use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\d7\FieldableEntity;

/**
 * Drupal 7 node source from database.
 *
 * Available configuration keys:
 * - node_type: The node_types to get from the source - can be a string or
 *   an array. If not declared then nodes of all types will be retrieved.
 *
 * Examples:
 *
 * @code
 * source:
 *   plugin: d7_node
 *   node_type: page
 * @endcode
 *
 * In this example nodes of type page are retrieved from the source database.
 *
 * @code
 * source:
 *   plugin: d7_node
 *   node_type: [page, test]
 * @endcode
 *
 * In this example nodes of type page and test are retrieved from the source
 * database.
 *
 * For additional configuration keys, refer to the parent classes.
 *
 * @see \Drupal\migrate\Plugin\migrate\source\SqlBase
 * @see \Drupal\migrate\Plugin\migrate\source\SourcePluginBase
 *
 * @MigrateSource(
 *   id = "custom_migrate_body",
 *   source_module = "migrate_article"
 * )
 */
class MigrateArticleDatacopy extends FieldableEntity {
 /**
   * {@inheritdoc}
   */
  public function query() {
    // // Source data is queried from 'curling_games' table.
    // $query = $this->select('node', 'n')
    //   ->fields('g', [
    //       'title',
    //       'type',
    //       'nid',
    //       'uid',
    //     ]);
    // return $query;

    $query = $this->select('node', 'n')
      ->condition('n.type', 'article')
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
  public function getIds() {

    $ids['nid']['type'] = 'integer';
    $ids['nid']['alias'] = 'n';
    return $ids;
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
        GROUP_CONCAT(fld.field_tags_tid) as tids
      FROM
        {field_data_field_tags} fld
      WHERE
        fld.entity_id = :nid
    ', array(':nid' => $nid));
    foreach ($result as $record) {
      if (!is_null($record->tids)) {
        $row->setSourceProperty('tags', explode(',', $record->tids) );
      
      }
    }
 
    
    return parent::prepareRow($row);
 
  }

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
    );
    return $fields;
}

}


