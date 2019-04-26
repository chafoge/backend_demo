<?php
namespace src\Database;

class Methods extends Database_Controller
{

    //GETS   //////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////


    public function get_one($table, $conditions = null, $columns = null, $order = null){
        $db = new Database($this->container);
        return $db->get($table, 'single', $conditions, $columns, $order );
    }



    public function get_all($table, $conditions = null, $columns = null, $order = null){
        $db = new Database($this->container);
        return $db->get($table, 'multi', $conditions, $columns, $order );
    }



    public function get_all_types($table, $relation_id, $relation_type, $types, $order = null){
        $types =  implode(',', $types);

        $query = "
           SELECT * 
           FROM " . $table . " 
           WHERE relation_type = :relation_type AND relation_id = :relation_id AND type in ({$types})
        ";

        if($order !== null){
            $query .= ' ' . $order;
        }

        $statement = $this->container['db']->prepare($query);
        $statement->execute([':relation_type' => $relation_type, ':relation_id' => $relation_id]);
        $result = $statement->fetchAll();

        return $result;
    }



    //INSERTS   ///////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////

    /* Creates from given data
     * Available columns shown in buildups
     */
    public function create($table, $columns){
        $db = new Database($this->container);

        $valid_columns  = $db->fill_valid_fields($table, $columns);
        $created_data = $db->create($table, $valid_columns, $columns['uid']);

        return $created_data;
    }



    /* Updates from given data
     * Available columns shown in buildups
     */
    public function update($table, $columns, $conditions = null){
        $db = new Database($this->container);

        $valid_columns  = $db->fill_valid_fields($table, $columns);
        $created_data = $db->update($table, $valid_columns, $conditions, $columns['uid']);

        return $created_data;
    }



    /* Updates state
     */
    public function update_state($table,$state, $user_id, $conditions = null){
        $db = new Database($this->container);

        $created_data =  $db->update($table, ['state' => $state], $conditions, $user_id );
        return  $created_data;
    }
}