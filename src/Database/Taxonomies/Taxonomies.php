<?php
namespace src\Database\Taxonomies;
use src\Database\Database;
use src\Database\Database_Controller;


class Taxonomies extends Database_Controller
{
    //GETS   //////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////


    public function get_one($conditions = null, $columns = null, $order = null){
        $db = new Database($this->container);
        return $db->get('taxonomies', 'single', $conditions, $columns, $order );
    }



    public function get_all($conditions = null, $columns = null, $order = null){
        $db = new Database($this->container);
        return $db->get('taxonomies', 'multi', $conditions, $columns, $order );
    }



    public function get_all_types($relation_id, $relation_type, $types, $order = null){
        $types =  implode(',', $types);

        $query = "
           SELECT * 
           FROM taxonomies
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
    public function create($columns){
        $db = new Database($this->container);

        $valid_columns  = $db->fill_valid_fields('taxonomies', $columns);
        $created_data = $db->create('taxonomies', $valid_columns, $columns['uid']);

        return   isset($created_data['errors'])
            ? $created_data
            : array('create' => true);
    }



    /* Updates from given data
     * Available columns shown in buildups
     */
    public function update($columns, $conditions = null){
        $db = new Database($this->container);

        $valid_columns  = $db->fill_valid_fields('taxonomies', $columns);
        $created_data = $db->update('taxonomies', $valid_columns, $conditions, $columns['uid']);

        return   isset($created_data['errors'])
            ? $created_data
            : array('update' => true);
    }



    /* Updates state
     */
    public function update_state($state, $user_id, $conditions = null){
        $db = new Database($this->container);

        $created_data =  $db->update('taxonomies', ['state' => $state], $conditions, $user_id );
        return  isset($created_data['errors'])
            ? $created_data
            : array('update_state' => true);
    }
}