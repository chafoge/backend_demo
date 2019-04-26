<?php

namespace src\Database\Taxonomies;
use src\Database\Database_Controller;
use src\Database\Methods;

class Taxonomie_Methods extends Database_Controller
{

    /* Calls get or insert method with related table from function name:
    * example get_one_post calls get_one with table posts
    * valid table names can be find in buildups
    *
    * types must be specifyed in relations list in dependencies.php
    */
    public function __call ($method, $arguments) {

        //modifying dependencies from called method name string
        $class_methods = get_class_methods($this);

        $method_prefix = null;
        $method_suffix = null;
        $method_type = null;

        foreach ($class_methods as $class_method){
            if( strpos($method, $class_method) !== false ){
                $method_prefix = $class_method;
                $method_suffix = str_replace($method_prefix . '_', '', $method);
            }
        }

        $is_relation = false;
        foreach ($this->container->get('relations') as $relation => $value){
            if($method_suffix === $relation){ $is_relation = true; $method_type = $relation; }
            if($method_suffix . 's' === $relation){ $is_relation = true; $method_type = $relation; }
            if($method_suffix . 'es' === $relation){ $is_relation = true; $method_type = $relation; }
            if(substr($method_suffix, 0, -1) === $relation){ $is_relation = true; $method_type = $relation; }
        }

        if($is_relation === false){
            return array('errors' => 'INVALID_METHOD_SUFFIX', 'method' => $method, 'method_suffix' => $method_suffix);
        }


        //check if exeption class exists
        $exeption_class = ucfirst($method_type);
        $expetion_object = null;
        if(class_exists('src\Database\Taxonomies\\' . $exeption_class)){
            $expetion_object = 'src\Database\Taxonomies\\' . $exeption_class;
            $exeption_class = new $expetion_object($this->container);
        }

        //check if exeption method exists
        if (method_exists($exeption_class, $method)) {
            return call_user_func_array(array($exeption_class, $method), $arguments);
        }
        //use default method
        else {
            array_unshift($arguments, $method_type);

            foreach ($class_methods as $class_method){
                if($class_method === $method_prefix){
                    return call_user_func_array(array($this, $method_prefix), $arguments);
                }
            }

            return array('errors' => 'INVALID_METHOD_PREFIX', 'method' => $method, 'details' => $method_prefix );
        }
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