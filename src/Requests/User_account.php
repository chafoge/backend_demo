<?php
namespace src\Requests;
use src\Database\Tables\Accounts;
use src\Database\Tables\Table_Methods;
use src\Database\Taxonomies\Taxonomie_Methods;
use src\Library\Helper;
use src\Requests\Request_Controller;

class User_account extends Request_Controller
{

    /* returns a multi array of requested data
     */
    public function get_user_accounts($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);
        $accounts_class = new Accounts($this->container);

        $required_params = ['user_id'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $condtions = [
            'state' => 1,
            'relation_type' => $this->container->get('relations')['users'],
            'relation_id' => $params['user_id']
        ];

        $condtions = Helper::check_condition_method($params, $condtions);

        $accounts['accounts'] = $table_methods->get_all_accounts(
            $condtions,
            null,
            'ORDER BY date DESC' . Helper::check_limit_offset($params, 100)
        );

        foreach($accounts['accounts'] as $account_key => $account){
            $accounts['accounts'][$account_key]['value'] = Helper::dezimalFormat($account['value']);
        }

        $accounts['count'] = count($table_methods->get_all_accounts(
            [
                'relation_type' => $this->container->get('relations')['users'],
                'relation_id' => $params['user_id']
            ],
            null,
            ' '
        ));

        $accounts['balance'] = Helper::dezimalFormat($accounts_class->get_user_sum($params['user_id'])['value']);

        return $response->withJson( $accounts );
    }

    /* creates and returns true or false
        */
    public function create_user_account($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);

        $required_params = ['date', 'value','description', 'currency', 'user_id', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $params['relation_type'] = $this->container->get('relations')['users'];
        $params['relation_id'] = $params['user_id'];

        $created_data = Helper::is_created($table_methods->create_account($params));
        return $response->withJson( $created_data );
    }

    /* updates and returns true or false
    */
    public function update_user_account($request, $response){
        $params = $request->getParams();
        $table_methods = new Table_Methods($this->container);
        $time = new \DateTime();
        $current_date = $time->format('d/m/Y');

        $required_params = ['account_id', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $user = $table_methods->get_one_user(['id' => $params['uid']]);
        $params['description'] = isset($params['description'])
            ? " {$params['description']} *Editiert von {$user['firstname']} {$user['lastname']} am {$current_date}"
            : " *Editiert von {$user['firstname']} {$user['lastname']} am {$current_date}";

        $conditions['id'] = $params['account_id'];

        $updated_data = Helper::is_updated($table_methods->update_account($params, $conditions));
        return $response->withJson( $updated_data );
    }

    /* creates and returns true or false
       */
    public function delete_user_account($request, $response){
        $params = $request->getParams();
        $taxonomie_methods = new Taxonomie_Methods($this->container);

        $required_params = ['account_id', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $conditions['id'] = $params['account_id'];

        $updated_data = Helper::is_updated( $taxonomie_methods->update_state_vita(0, $params['uid'], $conditions) );
        return $response->withJson( $updated_data );
    }
}
