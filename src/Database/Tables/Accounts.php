<?php
namespace src\Database\Tables;
use src\Database\Database_Controller;

class Accounts extends Database_Controller
{
    public function get_user_sum($user_id){
        $relation_type = $this->container->get('relations')['users'];

        $query = "
          SELECT sum(value) as value
          FROM accounts 
          WHERE relation_type = :relation_type AND relation_id = :relation_id";
        $statement = $this->container['db']->prepare($query);
        $statement->execute([':relation_type' => $relation_type, ':relation_id' => $user_id]);
        return $statement->fetch();
    }
}