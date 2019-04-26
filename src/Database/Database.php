<?php
namespace src\Database;
use src\Library\Helper;

class Database extends Database_Controller
{


  /* Validates and creates given date in database
   * @params $table (table name),
   * @params $fields_body (data $key = column-name, $value = column-value)
   * @params $user_id (id of creator)
   */
  public function create(string $table, array $fields_body, $user_id = 0){
      $fields_head = Helper::data_head($user_id, 'create');
      $conditions = array_merge($fields_head, $fields_body);
      $validation = $this->validation($table, $conditions);

      if(empty($validation)){
          $columns = implode(',', array_keys($conditions));
          $values = implode(',', array_fill(0, count($conditions), '?'));

          $statement = $this->container['db']->prepare("INSERT INTO ". $table ." ({$columns}) VALUES ({$values})");
          $statement->execute(array_values($conditions));

          $id = $this->container['db']->lastInsertId();
          if ($id){
              $result = $this->get($table, 'single', ['id' => $id]);
              return ['create' => $result];

          } else {
              return false;
          }
      }
      else{
          return ['errors' => $validation ];
      }
  }



    /* Validates and updates given date in database
     * @params $table (table name),
     * @params $fields_body (data-column $key = column-name, $value = column-value)
     * @params $conditions (where $key = column-name, $value = column-value )
     * @params $user_id (id of creator)
     */
    public function update(string $table,  array $fields_body, $conditions = null, $user_id = 0){
        $fields_head = Helper::data_head($user_id);
        $columns = array_merge($fields_head, $fields_body);
        $validation = $this->validation($table, $columns);

        if(empty($validation)){
            $query = "UPDATE " . $table . " SET ";

            $setColumns = [];
            foreach ($columns as $key => $value){
                $setColumns[] = "{$key} = '{$value}'";

            }
            $query .= implode(', ', $setColumns);

            if ($conditions) {
                $where = [];
                foreach ($conditions as $key => $value){
                    $where[] = $this->build_where_clause_item($key, $value);
                }
                $query .= " WHERE " . print_r(implode(' AND ', $where), true);
            }

            $statement = $this->container['db']->prepare($query);
            $statement->execute();

            $result = $this->get($table, 'single',null, null, 'ORDER BY edit_date DESC');
            return ['update' => $result];

        }
        else{
            return ['errors' => $validation ];
        }
    }



    /* Returns database data from given params
     * @params $table (table name)
     * @params $method (listed in execute_get method)
     * @params $conditions (where $key = column-name, $value = column-value )
     * @params $columns (fetched columns as array for expample. ['id', 'create_date',...])
     * @params $columns (string 'order by ... DESC')
     */
    public function get(string $table, string $method, $conditions = null, $columns = null, $order = null){

        if($columns){
            $query = "SELECT "  . implode(' , ', $columns) . " FROM " . $table;
        } else {
            $query = "SELECT * FROM " . $table;
        }

        if ($conditions) {
            $where = [];
            foreach ($conditions as $key => $value){
                $where[] = $this->build_where_clause_item($key, $value);
            }
            $query .= " WHERE " . implode(' AND ', $where);
        }

        $result = $this->execute_get($method, $query, $order);
        return $result;
    }



    /* Returns database data from given params
    * @params $table (table name)
    * @params $method (listed in execute_get method)
    * @params $conditions (where $key = column-name, $value = column-value )
    * @params $columns (fetched columns as array for expample. ['id', 'create_date',...])
    * @params $columns (string 'order by ... DESC')
    */
    public function get_or(string $table, string $method, $conditions = null, $columns = null, $order = null){

        if($columns){
            $query = "SELECT "  . implode(' , ', $columns) . " FROM " . $table;
        } else {
            $query = "SELECT * FROM " . $table;
        }

        if ($conditions) {
            $where = [];
            foreach ($conditions as $key => $value){
                $where[] = "{$key} = '{$value}'";
            }
            $query .= " WHERE " . implode(' OR ', $where);
        }

        $result = $this->execute_get($method, $query, $order);
        return $result;
    }


    /* returns database values from given params
       @param
        $indicator = [
            ''
        }
     *
     */
    public function get_join(array $indicator, array $joins, $where = null, $order = null){
        $join_types = [
            'JOIN',
            'INNER JOIN',
            'LEFT JOIN',
            'RIGHT JOIN',
            'FULL OUTER JOIN'
        ];

        function check_params($state, $table, $join = null){
            $alias = isset($table['alias']) && gettype($table['alias']) === 'string'? $table['alias'] : '';

            if(!isset($table['table']) || empty($table['table']) && gettype($table['table']) !== 'string'){
                return array('errors' => $state['INVALID_JOIN_PARAM'], 'details' =>$table['table'] . '' . $alias . ' table');
            }
            if($join){
                if(!isset($table['type']) || empty($table['type']) && gettype($table['type']) !== 'string'){
                    return array('errors' => $state['INVALID_JOIN_PARAM'], 'details' => $table['table'] . '' . $alias . ' type');
                }
            }
            if(isset($table['alias']) && gettype($table['alias']) !== 'string'){
                return array('errors' => $state['INVALID_JOIN_PARAM'], 'details' => $table['table'] . '' . $alias . ' alias');
            }
            if(isset($table['conditions']) && gettype($table['conditions']) !== 'array' ){
                return array('errors' => $state['INVALID_JOIN_PARAM'], 'details' => $table['table'] . '' . $alias . ' conditions');
            }
            if(isset($table['columns']) && gettype($table['columns']) !== 'array' ){
                return array('errors' => $state['INVALID_JOIN_PARAM'], 'details' => $table['table'] . '' . $alias . ' columns');
            }
        }

        $type_check_indicator = check_params($this->container->get('state'), $indicator);
        if(isset($type_check_indicator['errors'])){
            return $type_check_indicator;
        }

        $columns_query = [];
        $join_query = [];
        foreach($joins as $join_key => $join){
            $type_check_join = check_params($this->container->get('state'), $join, true);
            if(isset($type_check_join['errors'])){
                return $type_check_join;
            }

            $validate_join_type = isset($join_types[$join['type']]);
            if($validate_join_type){
                return array('errors' => $this->container->get('state')['INVALID_JOIN_PARAM'], 'details' => 'join type ' . $join['type']);
            }

            $join_alias =  isset($join['alias'])  && $join['alias'] !== null
                ? $join['alias']
                : $join['table'];

            
            if(isset($join['columns']) && $join['columns'] !== null){
                foreach ($join['columns'] as $column){
                    array_push($columns_query, "{$join_alias}.{$column} AS '{$join_alias}.{$column}'");
                }
            }

            if(isset($join['conditions']) && $join['conditions'] !== null){
                $join_on_items = [];
                foreach($join['conditions'] as $conditions_key => $condition){
                    array_push($join_on_items, $this->build_where_clause_item($conditions_key, $condition, $join_alias));
                }

                $join_where = count($join_on_items) > 0
                    ? ' ON ' . implode( ' AND ', $join_on_items)
                    : '';

                array_push($join_query, $join['type'] . ' ' . $join['table'] . ' AS ' . $join_alias . $join_where);
            }
        }

        $where_query = [];
        if($where){
            foreach ($where as $table_alias => $table_conditions){
                foreach($table_conditions as $conditions_key => $condition){
                    array_push($where_query, $this->build_where_clause_item($conditions_key, $condition, $table_alias));
                }
            }
        }
        
        $indicator_alias =  isset($indicator['alias'])  && $indicator['alias'] !== null
            ? $indicator['alias']
            : $indicator['table'];

        if(isset($indicator['columns']) && $indicator['columns'] !== null){
            foreach ($indicator['columns'] as $column){
                array_push($columns_query, "{$indicator_alias}.{$column} AS '{$indicator_alias}.{$column}'" );
            }
        }

        if(isset($indicator['conditions']) && $indicator['conditions'] !== null){
            foreach($indicator['conditions'] as $conditions_key => $condition){
                array_push($where_query, $this->build_where_clause_item($conditions_key, $condition, $indicator_alias));
            }
        }

        $columns_query = count($columns_query)
            ? implode(' , ', $columns_query)
            : '*';

        $join_query = count($join_query)
            ? implode(' ', $join_query)
            : '';

        $where_query = count($where_query)
            ? 'WHERE ' . implode(' AND ',$where_query)
            : '';

        $query = "SELECT {$columns_query} FROM {$indicator['table']} as {$indicator_alias} {$join_query} {$where_query}";

        //return array($query, $order);
        //return array('query' =>$query, 'order' => $order, 'data' => $this->execute_get('multi',$query, $order));
        return $this->execute_get('multi',$query, $order);
    }



    /* builds up where clause
     * @params $key, $value, $alias
     * options on $key:
         * search-> equ. %searchvalue%
         * at-> for mysql variables examp. users.id
         * in-> like or
     */
    public function build_where_clause_item(string $key, $value, $alias = null){

        if(substr( $key, 0, 8 ) === "search->"){
            $key = substr( $key, 8 );
            $key = $alias
                ? "{$alias}.{$key}"
                : $key;
            $item = "{$key} LIKE '%{$value}%'";
        }
        else if(substr( $key, 0, 4 ) === "at->"){
            $key = substr( $key, 4 );
            $key = $alias
                ? "{$alias}.{$key}"
                : $key;
            $item = "{$key} = {$value}";
        }
        else if(substr( $key, 0, 8 ) === "bigger->"){
            $key = substr( $key, 8 );
            $key = $alias
                ? "{$alias}.{$key}"
                : $key;
            $item = "{$key} > '{$value}'";
        }
        else if(substr( $key, 0, 9 ) === "smaller->"){
            $key = substr( $key, 9 );
            $key = $alias
                ? "{$alias}.{$key}"
                : $key;
            $item = "{$key} < '{$value}'";
        }
        else if(substr( $key, 0, 4 ) === "in->"){
            $key = substr( $key, 4 );
            $key = $alias
                ? "{$alias}.{$key}"
                : $key;
            $value = implode(' , ', $value);
            $item = "{$key} IN ({$value})";
        }
        else if(substr( $key, 0, 8 ) === "starts->"){
            $key = substr( $key, 8 );
            $key = $alias
                ? "{$alias}.{$key}"
                : $key;
            $value = implode(" OR {$key} LIKE " , $value);
            $item = "{$key} LIKE '{$value}%'";
        }
        else{
            $key = $alias
                ? "{$alias}.{$key}"
                : $key;
            $item = "{$key} = '{$value}'";
        }

        return $item;
    }



    /*
     * @params $method, $query
     */
    public function execute_get( $method, $query, $order = null){

        if($order !== null){
            $query .= ' '. $order;
        }

        else {
            $query .= ' Limit 10';
        }

        switch ($method){
            case 'multi':
                $statement = $this->container['db']->prepare($query);
                $statement->execute();
                $result = $statement->fetchAll();
                break;

            case 'single':
                $statement = $this->container['db']->prepare($query);
                $statement->execute();
                $result = $statement->fetch();
                break;
            default:
                return array('errors' => $this->container->get('state')['EXECUTE_ERROR'], 'details' => 'execute method: ' . $method);
        }

        return $result;
    }



    /* Checks database field conditions with given fieldset
     * @params $table (table name), $fields
     */
    public function validation(string $table, array $fields){
        $table_fields = $this->get_columns($table);
        $validations = [];
        $state = $this->container->get('state');

        foreach($fields as $key => $field){
            $table_field_position = array_search($key, array_column($table_fields, 'Field'));

            if( $table_field_position <= 0){
                $validations[$key] = $state['UNAVAILABLE'];
            }
            else {
                $type = $table_fields[$table_field_position]['Type'];
                $sub_type = substr($type, 0, strpos($type, '('));
                $sub_length = (int) filter_var($type, FILTER_SANITIZE_NUMBER_INT);

                $options['minlength'] = $table_fields[$table_field_position]['Null'] === 'NO' ? 1 : 0;
                $options['maxlength'] = $sub_length;

                switch ($sub_type){
                    case 'int':
                        $check = Helper::validate($field, 'integer', $options);
                        break;
                    case 'tinyint':
                        $check = Helper::validate($field, 'integer', $options);
                        break;
                    case 'bigint':
                        $check = Helper::validate($field, 'integer', $options);
                        break;
                    case 'float':
                        $check = Helper::validate($field, 'number', $options);
                        break;
                    case 'date':
                        $check = Helper::validate($field, 'date', $options);
                        break;
                    case 'varchar':
                        $check = Helper::validate($field, 'text', $options);
                        break;
                    case 'mediumtext':
                        $check = Helper::validate($field, 'text');
                        break;
                    case 'longtext':
                        $check = Helper::validate($field, 'text');
                        break;
                    default:
                        $check = Helper::validate($field, 'text');
                        break;
                }

                if($check !== $state['VALID']){
                    if($check === $state['TO_LONG']){
                        $validations[$key]['error'] = $state['TO_LONG'];
                        $validations[$key]['detail'] = $options['maxlength'];
                    }

                    else  if($check === $state['TO_SHORT']){
                        $validations[$key]['error'] = $state['TO_SHORT'];
                        $validations[$key]['detail'] = $options['minlength'];
                    }

                    else {
                        $validations[$key]['error'] = $check;
                    }
                }
            }
        }

        return $validations;
    }



    /* Fetched possible fields form buildup
     * and fills up values
     *
     * @params table
     * @params params array(key = value) as in database
     */
    public function fill_valid_fields($table, $params){
        $buildups = new Buildups($this->container);
        $fields = $buildups->get_fields_list_from($table);
        $valid_fields = [];

        foreach($fields as $field){
            if(isset($params[$field])){
                $valid_fields[$field] = $params[$field];
            }
        }

        return $valid_fields;
    }



    /*
     * @params $table,
     */
    public function create_table(string $table){
        $query = "
            CREATE TABLE IF NOT EXISTS `".$table."` (
              id INT(11) AUTO_INCREMENT PRIMARY KEY,
              state INT(11) not null,
              create_user_id INT(11),
              create_date BIGINT(20) not null,
              edit_user_id INT(11),
              edit_date BIGINT(20) not null
            );
        ";

        $statement = $this->container['db']->prepare($query);
        return $statement->execute();
    }



    /*
     * @params $table, $column, $type, $null
     */
    public function create_column(string $table, string $column, string $type){
        $query = "ALTER TABLE $table ADD $column $type AFTER edit_date";
        $statement = $this->container['db']->prepare($query);
        return $statement->execute();
    }



    /*
     * @params $table
     */
    public function get_columns(string $table){
        $query = "SHOW COLUMNS FROM $table";
        $statement = $this->container['db']->prepare($query);
        $statement->execute();
        return $statement->fetchAll();
    }

}
