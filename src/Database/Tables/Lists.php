<?php
namespace src\Database\Tables;
use src\Database\Database_Controller;
use src\Database\Methods;

class Lists extends Database_Controller
{

//    public function get_all_user_types(){
//        $dependency_list = $this->container->get('users_type');
//        return $this->prepare_dependency_list($dependency_list, 'user_type');
//    }

//    public function get_all_permission_types(){
//        $dependency_list = $this->container->get('permission')['types'];
//        return $this->prepare_dependency_list($dependency_list, 'permission_type');
//    }

//    public function get_all_permissions(){
//        $dependency_list = $this->container->get('permission')['permission_types'];
//        $lists = $this->prepare_dependency_list($dependency_list, 'permissions');
//        $return_list = [];
//        foreach ($lists as $list_key => $list)
//            if($list_key > 1){
//                array_push($return_list, $list);
//            }
//        return $return_list;
//    }

//    public function get_all_0_roles(){
//        $dependency_list = $this->container->get('roles')['0_role'];
//        return $this->prepare_dependency_list($dependency_list, '0_role');
//    }
//
//    public function get_all_1_roles(){
//        $dependency_list = $this->container->get('roles')['1_role'];
//        return $this->prepare_dependency_list($dependency_list, '1_role');
//    }

//    public function get_all_2_roles(){
//        $dependency_list = $this->container->get('roles')['2_role'];
//        return $this->prepare_dependency_list($dependency_list, '2_role');
//    }

//    public function get_all_3_roles(){
//        $dependency_list = $this->container->get('roles')['3_role'];
//        return $this->prepare_dependency_list($dependency_list, '3_role');
//    }


    public function prepare_dependency_list($dependency_list, $list_group){
        $list = [];

        foreach ($dependency_list as $name => $value){
            $item = [
                'list_group' => $list_group,
                'name' => $name,
                'value' => strval($value),
            ];
            array_push($list, $item);
        }

        return $list;
    }
}