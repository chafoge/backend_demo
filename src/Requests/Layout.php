<?php
namespace src\Requests;
use src\Database\Tables\Accounts;
use src\Database\Tables\Table_Methods;
use src\Database\Taxonomies\Taxonomie_Methods;
use src\Library\Helper;

class Layout extends Request_Controller
{
    /* returns a multi array of requested data
     */
    public function get_header($request, $response){
        $params = $request->getParams();
        $table_methods =  new Table_Methods($this->container);
        $accounts_class = new Accounts($this->container);

        $required_params = ['uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

       $user =  $table_methods->get_one_user(
            ['id' => $params['uid']],
            ['id', 'lastname', 'firstname', 'name', 'image', 'type']
        );

       if(!isset($user['type'])){
           return null;
       }

       $user['account'] = Helper::dezimalFormat($accounts_class->get_user_sum($user['id'])['value']);

        return $response->withJson( $user );
    }

    /*
     */
    public function get_uhead($request, $response)
    {
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);

        $required_params = ['user_id'];
        if (($errors = Helper::check_required_params($required_params, $params)) !== false) {
            return $response->withJson($errors);
        }

        $user = $table_methods->get_one_user(
            ['id' => $params['user_id']],
            ['id', 'lastname', 'firstname', 'name', 'image', 'description']
        );
        if (isset($user['id']) === false) {
            return $response->withJson(array('errors' => $this->container->get('state')['UNKNOWN_USER']));
        }

        return $response->withJson($user);
    }
}
