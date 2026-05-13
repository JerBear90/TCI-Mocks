<?php
class Forms_Custom_route extends WP_REST_Controller {
 
 /**
  * Register the routes for the objects of the controller.
  */
 public function register_routes() {
   $namespace = WAFNAMESPACE;
   register_rest_route( $namespace, '/config', array(
    array(
      'methods'             => WP_REST_Server::READABLE,
      'callback'            => array( $this, 'get_config' ),
      'permission_callback' => array( $this, 'get_items_permissions_check' ),
      'args'                => array(

      ),
    )
    ));
   register_rest_route( $namespace, '/', array(
     array(
       'methods'             => WP_REST_Server::READABLE,
       'callback'            => array( $this, 'get_items' ),
       'permission_callback' => array( $this, 'get_items_permissions_check' ),
       'args'                => array(

       ),
     ),
     array(
       'methods'             => WP_REST_Server::CREATABLE,
       'callback'            => array( $this, 'create_item' ),
       'permission_callback' => array( $this, 'create_item_permissions_check' ),
       'args'                => $this->get_endpoint_args_for_item_schema( true ),
     ),
   ) );
   register_rest_route( $namespace, '/' . '/(?P<id>[\d]+)', array(
     array(
       'methods'             => WP_REST_Server::READABLE,
       'callback'            => array( $this, 'get_item' ),
       'permission_callback' => array( $this, 'get_item_permissions_check' ),
       'args'                => array(
         'context' => array(
           'default' => 'view',
         ),
       ),
     ),
     array(
       'methods'             => WP_REST_Server::EDITABLE,
       'callback'            => array( $this, 'update_item' ),
       'permission_callback' => array( $this, 'update_item_permissions_check' ),
       'args'                => $this->get_endpoint_args_for_item_schema( false ),
     ),
     array(
       'methods'             => WP_REST_Server::DELETABLE,
       'callback'            => array( $this, 'delete_item' ),
       'permission_callback' => array( $this, 'delete_item_permissions_check' ),
       'args'                => array(
         'force' => array(
           'default' => false,
         ),
       ),
     ),
   ) );
   register_rest_route( $namespace, '/schema', array(
     'methods'  => WP_REST_Server::READABLE,
     'callback' => array( $this, 'get_public_item_schema' ),
   ) );
 }

 /**
  * Get a collection of items
  *
  * @param WP_REST_Request $request Full data about the request.
  * @return WP_Error|WP_REST_Response
  */
 public function get_items( $request ) {
    $items = getForms();

   $data = array();
   foreach( $items as $item ) {
     $itemdata = $this->prepare_item_for_response( $item, $request );
     $data[] = $this->prepare_response_for_collection( $itemdata );
   }

   return new WP_REST_Response( $data, 200 );
 }

 /**
  * Get form config data
  *
  * @param WP_REST_Request $request Full data about the request.
  * @return WP_Error|WP_REST_Response
  */
  public function get_config( $request ) {
    //get parameters from request
    $formConfig = getFormConfig();
    $data = $this->prepare_item_for_response( $formConfig, $request );
 
    //return a response or error based on some conditional
    if ( $data ) {
      return new WP_REST_Response( $data, 200 );
    } else {
      return new WP_Error( 'code', __( 'message', 'text-domain' ) );
    }
  }

 /**
  * Get one item from the collection
  *
  * @param WP_REST_Request $request Full data about the request.
  * @return WP_Error|WP_REST_Response
  */
 public function get_item( $request ) {
   //get parameters from request
   $params = $request->get_params();
   $form = getForm( $params['id'] );
   $data = $this->prepare_item_for_response( $form, $request );

   //return a response or error based on some conditional
   if ( $data ) {
     return new WP_REST_Response( $data, 200 );
   } else {
     return new WP_Error( 'code', __( 'message', 'text-domain' ) );
   }
 }

 /**
  * Create one item from the collection
  *
  * @param WP_REST_Request $request Full data about the request.
  * @return WP_Error|WP_REST_Request
  */
 public function create_item( $request ) {
   global $wpdb;
   $item = $this->prepare_item_for_database( $request );
   $data = createForm( $item );
   if ( is_array( $data ) ) return new WP_REST_Response( $data, 200 );
   
   return new WP_Error( 'cant-create', __( 'message', 'text-domain' ), array( 'status' => 500, 'message' => $wpdb->last_error ) );
 }

 /**
  * Update one item from the collection
  *
  * @param WP_REST_Request $request Full data about the request.
  * @return WP_Error|WP_REST_Request
  */
 public function update_item( $request ) {
   global $wpdb;
   $params = $request->get_params();
   $data = updateForm( $params );
  if ( is_array( $data ) ) return new WP_REST_Response( $data, 200 );

   return new WP_Error( 'cant-update', __( 'message', 'text-domain' ), array( 'status' => 500, 'message' => $wpdb->last_error ) );
 }

 /**
  * Delete one item from the collection
  *
  * @param WP_REST_Request $request Full data about the request.
  * @return WP_Error|WP_REST_Request
  */
 public function delete_item( $request ) {
   global $wpdb;
   $item = $this->prepare_item_for_database( $request );
   if ( function_exists( 'deleteSchedule' ) ) {
     $deleted = deleteSchedule( $item['id'] );
     if ( $deleted ) {
       return new WP_REST_Response( true, 200 );
     }
   }

   return new WP_Error( 'cant-delete', __( 'message', 'text-domain' ), array( 'status' => 500, 'message' => $wpdb->last_error ) );
 }

 /**
  * Check if a given request has access to get items
  *
  * @param WP_REST_Request $request Full data about the request.
  * @return WP_Error|bool
  */
 public function get_items_permissions_check( $request ) {
   return true;
   if( is_devel() ) return true;
   return current_user_can( 'edit_posts' );
 }

 /**
  * Check if a given request has access to get a specific item
  *
  * @param WP_REST_Request $request Full data about the request.
  * @return WP_Error|bool
  */
 public function get_item_permissions_check( $request ) {
    return true;
   return $this->get_items_permissions_check( $request );
 }

 /**
  * Check if a given request has access to create items
  *
  * @param WP_REST_Request $request Full data about the request.
  * @return WP_Error|bool
  */
 public function create_item_permissions_check( $request ) {
  return true;
   return current_user_can( 'edit_posts' );
 }

 /**
  * Check if a given request has access to update a specific item
  *
  * @param WP_REST_Request $request Full data about the request.
  * @return WP_Error|bool
  */
 public function update_item_permissions_check( $request ) {
   return $this->create_item_permissions_check( $request );
 }

 /**
  * Check if a given request has access to delete a specific item
  *
  * @param WP_REST_Request $request Full data about the request.
  * @return WP_Error|bool
  */
 public function delete_item_permissions_check( $request ) {
   return $this->create_item_permissions_check( $request );
 }

 /**
  * Prepare the item for create or update operation
  *
  * @param WP_REST_Request $request Request object
  * @return WP_Error|object $prepared_item
  */
 protected function prepare_item_for_database( $request ) {
    $params = $request->get_params();
    $params['time'] = strtotime( $params['date'] );
    return $params;
 }

 /**
  * Prepare the item for the REST response
  *
  * @param mixed $item WordPress representation of the item.
  * @param WP_REST_Request $request Request object.
  * @return mixed
  */
 public function prepare_item_for_response( $item, $request ) {
   return $item;
 }

 /**
  * Get the query params for collections
  *
  * @return array
  */
 public function get_collection_params() {
   return array(
     'page'     => array(
       'description'       => 'Current page of the collection.',
       'type'              => 'integer',
       'default'           => 1,
       'sanitize_callback' => 'absint',
     ),
     'per_page' => array(
       'description'       => 'Maximum number of items to be returned in result set.',
       'type'              => 'integer',
       'default'           => 10,
       'sanitize_callback' => 'absint',
     ),
     'search'   => array(
       'description'       => 'Limit results to those matching a string.',
       'type'              => 'string',
       'sanitize_callback' => 'sanitize_text_field',
     ),
   );
 }
}