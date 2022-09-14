<?php
# modules/custom/my_custom_module/src/Plugin/migrate/process/SkipYoutubeVideos.php
namespace Drupal\my_custom_module\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Skip youtube videos.
 *
 * @MigrateProcessPlugin(
 *   id = "youtube_files"
 * )
 */
class SkipYoutubeVideos2 extends ProcessPluginBase {

  const SCHEME = 'youtube://';
  const BASE_URL = 'http://youtube.com/watch?';
  /**
   * {@inheritdoc}
   */
  
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // if (parse_url(end($value), PHP_URL_SCHEME) == 'youtube') {
    //   throw new MigrateSkipRowException();
    // }

    // return $value;



    if (strpos($value, static::SCHEME) !== FALSE) {
      $value = static::BASE_URL . implode('=', explode('/', str_replace(static::SCHEME, '', $value), 2));
    }
    else {
      $value = NULL;
    }
    return $value;
    
  }

}