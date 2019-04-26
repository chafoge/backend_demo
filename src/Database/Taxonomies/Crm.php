<?php
namespace src\Database\Taxonomies;
use src\Database\Database;
use src\Database\Database_Controller;

class Crm extends Database_Controller
{
    /* returns a multi array of requested data
     */
    public function get_crm(array $tables, $params, $required_columns = null){
        $database = new Database($this->container);

        $indicator = null;
        $joins = [];
        foreach($tables as $table_key => $table){
            if(isset($table['indicator'])){
                $indicator = $table;
            }
            else {
                $joins[$table_key] = $table;
            }
        }

        $whers = [];
        $orders = [
            'desc' => [],
            'asc' => []
        ];

        foreach($tables as $table_key => $table){

            if(isset($params[$table_key]['conditions'])){
                $whers[$table_key] = $params[$table_key]['conditions'];
            }

            if(isset($params[$table_key]['columns'])){
                if(count($params[$table_key]['columns']) === 0){
                    if(isset($joins[$table_key])){
                        unset($joins[$table_key]);
                    }
                    else {
                        unset($tables[$table_key]['columns']);
                    }
                }
                else{
                    $are_valid_columns = true;
                    foreach ($params[$table_key]['columns'] as $column){
                        if (array_search($column, $table['columns']) === false) {
                            $are_valid_columns = false;
                        }
                    }

                    if(!$are_valid_columns){
                        return array('errors' => $this->container->get('state')['INVALID_COLUMNS_REQUESTED'], 'details' => $column);
                    }

                    if(isset($joins[$table_key])){
                        $joins[$table_key]['columns'] = $params[$table_key]['columns'];
                    }
                    else {
                        $table['columns'] = $params[$table_key]['columns'];
                    }
                }
            }

            if(isset($params[$table_key]['desc'])){
                foreach($params[$table_key]['desc'] as $order){
                    array_push($orders['desc'], "{$table_key}.{$order}");
                }
            }

            if(isset($params[$table_key]['asc'])){
                foreach($params[$table_key]['asc'] as $order){
                    array_push($orders['asc'], "{$table_key}.{$order}");
                }
            }
        }

        if(!isset($indicator['columns'])){
            $indicator['columns'] = [];
        }

        if($required_columns !== null){
            foreach($required_columns as $column){
                array_unshift($indicator['columns'], $column);
            }
        }

        $order = '';
        $order .= count($orders['asc']) > 0
            ?  count($orders['desc']) > 0
                ? " ORDER BY " . implode(' , ',$orders['asc']) .', '
                : " ORDER BY " . implode(' , ',$orders['asc']) . " ASC"
            : '';
        $order .= count($orders['desc']) > 0
            ? count($orders['asc']) > 0
                ? implode(' , ', $orders['desc']) . " DESC"
                : " ORDER BY " . implode(' , ',$orders['desc']) . " DESC"
            : '';
        $order .= isset($params['limit'])
            ? isset($params['offset'])
                ? " LIMIT {$params['offset']}, {$params['limit']}"
                : " LIMIT " . $params['limit']
            : '';
        $order .= isset($params['offset'])
            ? isset($params['limit'])
                ? ''
                : " LIMIT {$params['offset']}, 100"
            : '';

        $get_crm = $database->get_join( $indicator, $joins, $whers, $order);

        return  $get_crm ;
    }

    /* builds up
     */
    public function build_custom_field($table, $columns = null, $crm_type = null){
        $taxonomie_methods = new Taxonomie_Methods($this->container);
        $relations = $this->container->get('relations');

        $table_field_conditions = [
            'type' => $relations['table_field'],
            'relation_type' => $relations[$table],
        ];

        if($crm_type !== null){
            $table_field_conditions['search->value'] = 'c_' . $crm_type;
        }

        $get_table_fields = $taxonomie_methods->get_all_table_fields($table_field_conditions);

        $tables = [];
        foreach ($get_table_fields as $table_field){
            $tables[$table_field['value']] = [
                'table' => 'taxonomies',
                'alias' => $table_field['value'],
                'type' =>'LEFT JOIN',
                'conditions' => [
                    'state' => 1,
                    'name' => $table_field['value'],
                    'type' => $this->container->get('relations')['field'],
                    'relation_type' => $this->container->get('relations')[$table],
                    'at->relation_id' => $table.'.id'
                ],
                'columns' => ['value']
            ];

            if($columns !== null){
                $tables[$table_field['value']]['columns'] = $columns;
            }
        }

        return $tables;
    }
}