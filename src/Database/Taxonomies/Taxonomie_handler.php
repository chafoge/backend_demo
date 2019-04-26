<?php
namespace src\Database\Taxonomies;
use src\Database\Database_Controller;
use src\Database\Methods;

class Taxonomie_handler extends Database_Controller
{

    //Tables   ////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////

    public function get_one_post($conditions = null, $columns = null, $order = null){
        return $this->get_one('posts', $conditions, $columns, $order);
    }
    public function get_all_posts($conditions = null, $columns = null, $order = null){
        return $this->get_all('posts', $conditions, $columns, $order);
    }
    public function create_post($params){
        return $this->create('posts', $params);
    }
    public function update_post($params, $conditions = null){
        return $this->update('posts', $params, $conditions);
    }
    public function update_state_post($params, $user_id, $conditions = null){
        return $this->update_state('posts', $params, $user_id, $conditions);
    }

    

    public function get_one_vita($conditions = null, $columns = null, $order = null){
        return $this->get_one('vitas', $conditions, $columns, $order);
    }
    public function get_all_vitas($conditions = null, $columns = null, $order = null){
        return $this->get_all('vitas', $conditions, $columns, $order);
    }
    public function create_vita($params){
        return $this->create('vitas', $params);
    }
    public function update_vita($params, $conditions = null){
        return $this->update('vitas', $params, $conditions);
    }
    public function update_state_vita($params, $user_id, $conditions = null){
        return $this->update_state('vitas', $params, $user_id, $conditions);
    }




    //Columns    //////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////

    public function get_one_comment($conditions = null, $columns = null, $order = null){
        return $this->get_one('comment', $conditions, $columns, $order);
    }
    public function get_all_comments($conditions = null, $columns = null, $order = null){
        return $this->get_all('comment', $conditions, $columns, $order);
    }
    public function create_comment($params){
        return $this->create('comment', $params);
    }
    public function update_comment($params, $conditions = null){
        return $this->update('comment', $params, $conditions);
    }
    public function update_state_comment($params, $user_id, $conditions = null){
        return $this->update_state('comment', $params, $user_id, $conditions);
    }



    public function get_one_like($conditions = null, $columns = null, $order = null){
        return $this->get_one('like', $conditions, $columns, $order);
    }
    public function get_all_likes($conditions = null, $columns = null, $order = null){
        return $this->get_all('like', $conditions, $columns, $order);
    }
    public function create_like($params){
        return $this->create('like', $params);
    }
    public function update_like($params, $conditions = null){
        return $this->update('like', $params, $conditions);
    }
    public function update_state_like($params, $user_id, $conditions = null){
        return $this->update_state('like', $params, $user_id, $conditions);
    }



    public function get_one_media($conditions = null, $columns = null, $order = null){
        return $this->get_one('media', $conditions, $columns, $order);
    }
    public function get_all_medias($conditions = null, $columns = null, $order = null){
        return $this->get_all('media', $conditions, $columns, $order);
    }
    public function create_media($params){
        return $this->create('media', $params);
    }
    public function update_media($params, $conditions = null){
        return $this->update('media', $params, $conditions);
    }
    public function update_state_media($params, $user_id, $conditions = null){
        return $this->update_state('media', $params, $user_id, $conditions);
    }



    public function get_one_tag($conditions = null, $columns = null, $order = null){
        return $this->get_one('tag', $conditions, $columns, $order);
    }
    public function get_all_tags($conditions = null, $columns = null, $order = null){
        return $this->get_all('tag', $conditions, $columns, $order);
    }
    public function create_tag($params){
        return $this->create('tag', $params);
    }
    public function update_tag($params, $conditions = null){
        return $this->update('tag', $params, $conditions);
    }
    public function update_state_tag($params, $user_id, $conditions = null){
        return $this->update_state('tag', $params, $user_id, $conditions);
    }



    public function get_one_position($conditions = null, $columns = null, $order = null){
        return $this->get_one('position', $conditions, $columns, $order);
    }
    public function get_all_positions($conditions = null, $columns = null, $order = null){
        return $this->get_all('position', $conditions, $columns, $order);
    }
    public function create_position($params){
        return $this->create('position', $params);
    }
    public function update_position($params, $conditions = null){
        return $this->update('position', $params, $conditions);
    }
    public function update_state_position($params, $user_id, $conditions = null){
        return $this->update_state('position', $params, $user_id, $conditions);
    }




    //GETS   //////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////

    public function get_one($type, $conditions = null, $columns = null, $order = null){
        $methods = new Methods($this->container);

        $conditions['type'] = $this->container->get('relations')[$type];
        return $methods->get_one('taxonomies', $conditions, $columns, $order );
    }



    public function get_all($type, $conditions = null, $columns = null, $order = null){
        $methods = new Methods($this->container);

        $conditions['type'] = $this->container->get('relations')[$type];
        return $methods->get_all('taxonomies',$conditions, $columns, $order );
    }




    //INSERTS   ///////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////

    /* Creates data from given data
     * @params content, subject, type
     */
    public function create($type, $params){
        $methods = new Methods($this->container);

        $params['type'] = $this->container->get('relations')[$type];
        return $methods->create('taxonomies',$params);
    }



    /* Creates data from given data
     * @params content, subject, type
     */
    public function update($type, $params, $conditions = null){
        $methods = new Methods($this->container);

        $params['type'] = $this->container->get('relations')[$type];
        return $methods->update('taxonomies', $params, $conditions);
    }



    /* Updates table state with permission
     * @params params array(state = value)
     * @params conditions array(key value) where clause
     * @params user_id (string)
     */
    public function update_state($type, $state, $user_id, $conditions = null){
        $methods = new Methods($this->container);

        $conditions['type'] = $this->container->get('relations')[$type];
        return $methods->update_state('taxonomies',$state, $user_id, $conditions);

    }
}