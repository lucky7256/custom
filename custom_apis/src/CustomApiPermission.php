<?php
namespace Drupal\custom_apis;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides dynamic permissions of the filter module.
 */
class CustomApiPermission implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new FilterPermissions instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('entity_type.manager'));
  }

  /**
   * Returns an array of filter permissions.
   *
   * @return array
   */
  public function permissions() {
    $permissions = [];
    
    $permissions['create article using rest api']=[
        'title' => $this->t('create the article using rest api'),
        'description'=>['#prefix' => '<em>',
        '#markup' =>'permission to create the article using rest api',
        '#suffix' => '</em>']
    ];

    $permissions['update article using rest api']=[
        'title' => 'delete,update the article using rest api',
        'description'=>['#prefix' => '<em>',
        '#markup' =>'permission to delete,update the article using rest api',
        '#suffix' => '</em>']
    ];


    // foreach ($formats as $format) {
    //   if ($permission = $format->getPermissionName()) {
    //     $permissions[$permission] = [
    //       'title' => $this->t('Use the <a href=":url">@label</a> text format', [':url' => $format->toUrl()->toString(), '@label' => $format->label()]),
    //       'description' => [
    //         '#prefix' => '<em>',
    //         '#markup' => $this->t('Warning: This permission may have security implications depending on how the text format is configured.'),
    //         '#suffix' => '</em>',
    //       ],
    //       // This permission is generated on behalf of $format text format,
    //       // therefore add this text format as a config dependency.
    //       'dependencies' => [
    //         $format->getConfigDependencyKey() => [
    //           $format->getConfigDependencyName(),
    //         ],
    //       ],
    //     ];
    //   }
    // }
    return $permissions;
  }

}
