<?php
/**
 * @file
 * Contains \Drupal\custom_form\Controller\Display.
 */

namespace Drupal\implement_twig\Controller;
use Drupal\Core\Controller\ControllerBase;

/**
 * Class Display.
 *
 * @package Drupal\custom_form\Controller
 */

    
class Display extends ControllerBase {

  /**
   * showdata.
   *
   * @return string
   *   Return Table format data.
   */

 
  public function showdata() {
  


    return [
      '#theme' => 'MyTemplate',
      '#test_var' => $this->t('All is well'),
    ];
  }
}