<?php
namespace src\Requests;
use src\Database\Buildups;
use src\Database\Crm\Crm_Methods;
use src\Database\Database;
use src\Database\Tables\Lists;
use src\Database\Tables\Secret;
use src\Database\Tables\Table_Methods;
use src\Database\Taxonomies\Taxonomie_Methods;
use src\Library\Helper;
use src\Mail\Mail;
use src\Mail\Templates\Templates;

class Buildup extends Request_Controller
{

    /* Builds up the basic database dependencies
     */
    public function test($request, $response){
        $params = $request->getParams();
        $crm_methods = new Crm_Methods($this->container);

        $created_fields = $crm_methods->create_field($params);

        return $response->withJson( $created_fields );
    }



    /* Builds up the basic database dependencies
     */
    public function test_mail($request, $response){
        $params = $request->getParams();
        $class = new Mail($this->container);
        $mail = [];

        $mail['head']['from']['adress'] = $params['from'];
        $mail['head']['from']['name'] = $params['from'];
        $mail['head']['to']['adress'] = $params['to'];
        $mail['head']['to']['name'] = $params['to'];
        $mail['head']['subject'] = $params['subject'];
        $mail['head']['message'] = $params['message'];

        return $response->withJson( $class->send($mail));
    }


    /* Builds up the basic database dependencies
     */
    public function buildup($request, $response){
        $buildup = new Buildups($this->container);
        return $response->withJson($buildup->create());
    }



    /*
     * @request_params $table
     */
    public function create_table($request, $response){
        $params = $request->getParams();
        $db = new Database($this->container);

        return $response->withJson($db->create_table($params['table']));
    }



    /*
     * @request_params $table, $field, $type
     */
    public function create_column($request, $response){
        $params = $request->getParams();
        $db = new Database($this->container);
        $tables = $db->get_columns($params['table']);

        if(array_search($params['field'], array_column($tables, 'Field')) === false){
            $db->create_column($params['table'], $params['field'], $params['type']);
        }

        $tables = $db->get_columns($params['table']);
        return $response->withJson($tables);
    }



    /*
     * @request_params $table
     */
    public function get_columns($request, $response){
        $params = $request->getParams();
        $db = new Database($this->container);

        return $response->withJson($db->get_columns($params['table']));
    }
}
