<?php

namespace Drupal\custom_apis\Plugin\rest\resource;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Config\Config;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\node\Entity\Node;
use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use GuzzleHttp\Psr7\Response;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * An example REST resource to expose data from Drupal.
 *
 * @RestResource(
 *   id = "articles",
 *   label = @Translation("Example Resource"),
 *   uri_paths = {
 *     "canonical" = "/article/{nodeid}",
 *     "create" = "/article"
 * 
 *   }
 * )
 */
class GetArticles extends ResourceBase {
  
  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;
  /**
   * Psr\Log\LoggerInterface;
   *
   * @var Psr\Log\LoggerInterface;
   */
  protected $logger;

  protected $current_user;
  
  /**
   * Constructs a ExampleResource instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(
    array $config,
    $module_id,
    $module_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    EntityTypeManagerInterface $entityTypeManager,
    AccountProxyInterface $current_user) {
    parent::__construct($config, $module_id, $module_definition, $serializer_formats, $logger);
    $this->entityTypeManager=$entityTypeManager;
    $this->current_user=$current_user;
    $this->logger=$logger;

  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
   return new static($configuration,$plugin_id,$plugin_definition,$container->getParameter('serializer.formats'),$container->get('logger.factory')->get('rest'),
    $container->get('entity_type.manager'), $container->get('current_user')
   );
  }

  /**
   * Responds to GET requests.
   *
   *
   * @return \Drupal\rest\ResourceResponse
   *   The HTTP response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function get($nodeid)
    {
      try {
        
      
       $node=$this->entityTypeManager->getStorage('node')->load($nodeid);
       if(!$node)
       {
        $response = new ResourceResponse(" No article found with node id ".$nodeid,400);
        return $response;
       }
       foreach ($node->getFields() as $name => $field) {
        $myFields[$name] = $field->getString();
      }
  
      $response = new ResourceResponse($myFields,200);
      $response->addCacheableDependency($myFields);
      return $response;
  
      } catch (EntityStorageException $e) {
        $this->logger->error($e->getMessage());
        
      }
    }

    public function post($data) {

      // Use current user after pass authentication to validate access.
      if (!$this->current_user->hasPermission('create article using rest api')) {
  
        // Display the default access denied page.
        throw new AccessDeniedHttpException('Access Denied.');
      }
    
      if(!$data["title"])
      {
        $response = new ResourceResponse("provide the required detials",400);
        return $response;
      }
       
     
      $node = Node::create(
        array(
          'title' => $data["title"],
          'body' => [
            'summary' => '',
            'value' => $data["body"],
            'format' => 'full_html',
          ],
          'field_tags' =>$data["field_tags"],
          'field_image' =>$data["field_image"],
          'type'=>'article',
          'status' => 0,
        )
      );
      $node->save();

      $response = new ResourceResponse(" Sucessfully added the node",200);
     
      return $response;
    }



    
    public function patch($nodeid,$data)
    {
       
     
           // Use current user after pass authentication to validate access.
      if (!$this->current_user->hasPermission('update article using rest api')) {
  
        // Display the default access denied page.
        throw new AccessDeniedHttpException('Access Denied.');
      }
       
      $node=$this->entityTypeManager->getStorage('node')->load($nodeid);
      
      if(!$node)
      {
       $response = new ResourceResponse(" No article found with node id ".$nodeid,400);
       return $response;
      }
      $node->title=$data['title'];
      $node->body=$data['body'];
      $node->field_tags=$data['field_tags'];
      $node->status=$data['status'];
      $node->save();

      $response = new ResourceResponse(" Sucessfully update the node the node",200);
      return $response;
    
    }
   
    public function delete($nodeid)
    {
     
          // Use current user after pass authentication to validate access.
          if (!$this->current_user->hasPermission('update article using rest api')) {
  
            // Display the default access denied page.
            throw new AccessDeniedHttpException('Access Denied.');
          }
         
          try {
          $getstorage=$this->entityTypeManager->getStorage('node');
          $node=$getstorage->load($nodeid);
          if(!$node)
          {
            $this->logger->notice("node not found");
            return new ModifiedResourceResponse(NULL, 400);
          }
          else{
          $node->delete();
          return new ModifiedResourceResponse(NULL, 200);
          }
        } catch (EntityStorageException $e) {
          $this->logger->error($e->getMessage());
        }
      
    }

}
  
  