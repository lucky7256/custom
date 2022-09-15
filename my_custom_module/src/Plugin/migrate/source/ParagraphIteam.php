<?php
namespace Drupal\my_custom_module\Plugin\migrate\source;
use Drupal\migrate\Row;  
use Drupal\migrate_drupal\Plugin\migrate\source\d7\FieldableEntity; 
 /**  
* D7_paragraph_item source.  
*  
* @MigrateSource(  
*   id = "paragraph_iteam"  
* )  
*/  
class ParagraphIteam extends FieldableEntity { 

  /**  
  * {@inheritdoc}  
  */  
  public function query() {  
   // Select node in its last revision.  
   $query = $this->select('paragraphs_item', 'fci')  
   ->fields('fci', [  
     'item_id',  
     'field_name',  
     'revision_id',  
   ]);  
   if (isset($this->configuration['field_name'])) {  
     $query->innerJoin('field_data_' . $this->configuration['field_name'], 'fd', 'fd.' . $this->configuration['field_name'] . '_value = fci.item_id');  
     $query->fields('fd',  
       ['entity_type',  
       'bundle',  
       'entity_id',  
       $this->configuration['field_name'] . '_revision_id',  
     ]);  
     $query->condition('fci.field_name', $this->configuration['field_name']); 

   } 

   return $query;  
 } 

  /**  
  * {@inheritdoc}  
  */  
  public function prepareRow(Row $row) { 
    // If field specified, get field revision ID so there aren't issues mapping.  
   if (isset($this->configuration['field_name'])) {  
     $row->setSourceProperty('revision_id', $row->getSourceProperty($this->configuration['field_name'] . '_revision_id'));  
   } 

     // Get field API field values.  
    foreach (array_keys($this->getFields('paragraphs_item', 'contact')) as $field) { 
    
        // get the necessary argumentss
        $item_id = $row->getSourceProperty('item_id');  
        $revision_id = $row->getSourceProperty('revision_id');  

        // set this field in tehe row...
        $row->setSourceProperty($field, $this->getFieldValues('paragraphs_item', $field, $item_id, $revision_id)); 
    }
  
  return parent::prepareRow($row);  
} 

  /**  
  * {@inheritdoc}  
  */  
  public function fields() {  
   $fields = [  
     'item_id' => $this->t('Item ID'),  
     'revision_id' => $this->t('Revision ID'),  
     'field_name' => $this->t('Name of field'),  
   ];  
   return $fields;  
 }  

    /**  
    * {@inheritdoc}  
    */  
    public function getIds() {  
     $ids['item_id']['type'] = 'integer';  
     $ids['item_id']['alias'] = 'fci';  
     return $ids;  
   } 
 } 