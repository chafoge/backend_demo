<?php
namespace src\Requests;
use src\Database\Tables\Table_Methods;
use src\Database\Taxonomies\Taxonomie_Methods;
use src\Library\Helper;
use src\Requests\Request_Controller;

class Global_post extends Request_Controller
{
    /*
     */
    public function get_posts($request, $response){
        $params = $request->getParams();
        $taxonomie_methods = new Taxonomie_Methods($this->container);

        $params['type'] = $this->container->get('relations')['articles'];
        $params['relation_type'] = $this->container->get('relations')[$params['relation_type']];
        return $response->withJson( $taxonomie_methods->get_all_posts($params) );
    }

    /*
     */
    public function get_post($request, $response){
        $params = $request->getParams();
        $taxonomie_methods = new Taxonomie_Methods($this->container);

        $params['type'] = $this->container->get('relations')['articles'];
        $params['relation_type'] = $this->container->get('relations')[$params['relation_type']];
        return $response->withJson( $taxonomie_methods->get_one_post($params) );
    }

    /*
     */
    public function create_post($request, $response){
        $params = $request->getParams();
        $taxonomie_methods = new Taxonomie_Methods($this->container);

        $created_data = Helper::is_created( $taxonomie_methods->create_post($params) );
        return $response->withJson( $created_data );
    }

    /*
     */
    public function update_post($request, $response){
        $params = $request->getParams();
        $taxonomie_methods = new Taxonomie_Methods($this->container);

        if(isset($params['id'])){
            $conditions['id'] = $params['id'];
        }

        $updated_data = Helper::is_updated( $taxonomie_methods->update_post($params,$conditions) );
        return $response->withJson( $updated_data );
    }



    /*
     */
    public function get_post_comments($request, $response){
        $params = $request->getParams();
        $taxonomie_methods = new Taxonomie_Methods($this->container);
        $table_methods = new Table_Methods($this->container);

        $required_params = ['relation_type', 'relation_id'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $conditions['relation_type'] = $params['relation_type'];
        $conditions['relation_id'] = $params['relation_id'];
        $comments['comments'] = $taxonomie_methods->get_all_comments($conditions);

        foreach ($comments['comments'] as $key => $comment){
            $user = $table_methods->get_one_user(['id' => $comment['create_user_id']]);
            $comments['comments'][$key]['firstname'] = $user['firstname'];
            $comments['comments'][$key]['lastname'] = $user['lastname'];
            $comments['comments'][$key]['image'] = $user['image'];
        }
        
        $comments['count'] = count($taxonomie_methods->get_all_comments(['relation_id' => $params['relation_id'], 'relation_type' => $params['relation_type'], 'state' => 1], ['id']));

        $comments['comments'] =  Helper::unset_in_array($comments['comments'], array('relation_type', 'relation_id', 'edit_date', 'edit_user_id'));
        return $response->withJson( $comments );
    }

    /*
     */
    public function create_post_comment($request, $response){
        $params = $request->getParams();
        $taxonomie_methods = new Taxonomie_Methods($this->container);

        $required_params = ['comment', 'relation_type', 'relation_id', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $params['value'] = $params['comment'];
        $created_data = Helper::is_created( $taxonomie_methods->create_comment($params) );
        return $response->withJson( $created_data );
    }

    /*
     */
    public function update_post_comment($request, $response){
        $params = $request->getParams();
        $taxonomie_methods = new Taxonomie_Methods($this->container);

        $required_params = ['id', 'comment', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $params['value'] = $params['comment'];
        $conditions['id'] = $params['id'];

        $updated_data = Helper::is_updated( $taxonomie_methods->update_comment($params,$conditions) );
        return $response->withJson( $updated_data );
    }



    /*
     */
    public function get_post_likes($request, $response){
        $params = $request->getParams();
        $taxonomie_methods = new Taxonomie_Methods($this->container);
        $table_methods = new Table_Methods($this->container);

        $required_params = ['relation_type', 'relation_id', 'user_id'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $conditions['relation_type'] = $params['relation_type'];
        $conditions['relation_id'] = $params['relation_id'];
        $likes['likes'] = $taxonomie_methods->get_all_likes($conditions);

        foreach ($likes['likes'] as $key => $like){
            $user = $table_methods->get_one_user(['id' => $like['create_user_id']]);
            $likes['likes'][$key]['firstname'] = $user['firstname'];
            $likes['likes'][$key]['lastname'] = $user['lastname'];
            $likes['likes'][$key]['image'] = $user['image'];
        }

        $active = $taxonomie_methods->get_one_like(['create_user_id' => $params['user_id'], 'relation_id' => $params['relation_id'], 'relation_type' => $params['relation_type'], 'state' => 1]);
        $likes['active'] = isset($active['errors']) ? $active : $active !== false;
        $likes['count'] = count($taxonomie_methods->get_all_likes(['relation_id' => $params['relation_id'], 'relation_type' => $params['relation_type'], 'state' => 1], ['id']));

        $likes['likes'] = Helper::unset_in_array($likes['likes'], array('relation_type', 'relation_id', 'edit_date', 'edit_user_id'));
        return $response->withJson( $likes );
    }

    /*
     */
    public function set_post_like($request, $response){
        $params = $request->getParams();
        $taxonomie_methods = new Taxonomie_Methods($this->container);

        $required_params = ['state', 'relation_id', 'relation_type', 'uid'];
        if(($errors = Helper::check_required_params($required_params, $params)) !== false){
            return $response->withJson( $errors );
        }

        $active_like = $taxonomie_methods->get_one_like(['relation_id' => $params['relation_id'], 'relation_type' => $params['relation_type']]);
        if($active_like !== false){
            $conditions = [
                'relation_id' => $params['relation_id'],
                'relation_type' => $params['relation_type']
            ];
            $like_action = Helper::is_created($taxonomie_methods->update_state_like($params['state'], $params['uid'], $conditions));
        }
        else{
            $like_action = Helper::is_created($taxonomie_methods->create_like($params));
        }

        return $response->withJson( $like_action );
    }



    /*
     */
    public function create_post_tag($request, $response){
        $params = $request->getParams();
        $taxonomie_methods = new Taxonomie_Methods($this->container);

        $created_data = Helper::is_created( $taxonomie_methods->create_tag($params) );
        return $response->withJson( $created_data );
    }

    /*
     */
    public function update_post_tag($request, $response){
        $params = $request->getParams();
        $taxonomie_methods = new Taxonomie_Methods($this->container);

        if(isset($params['id'])){
            $conditions['id'] = $params['id'];
        }

        $updated_data = Helper::is_updated( $taxonomie_methods->update_tag($params,$conditions) );
        return $response->withJson( $updated_data );
    }



    /*
     */
    public function create_article_media($request, $response){
        $params = $request->getParams();
        $taxonomie_methods = new Taxonomie_Methods($this->container);

        $created_data = Helper::is_created( $taxonomie_methods->create_media($params) );
        return $response->withJson( $created_data );
    }

    /*
     */
    public function update_article_media($request, $response){
        $params = $request->getParams();
        $taxonomie_methods = new Taxonomie_Methods($this->container);

        if(isset($params['id'])){
            $conditions['id'] = $params['id'];
        }

        $updated_data = Helper::is_updated( $taxonomie_methods->update_media($params,$conditions) );
        return $response->withJson( $updated_data );
    }
}