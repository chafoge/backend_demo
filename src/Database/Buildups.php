<?php
namespace src\Database;

class Buildups extends Database_Controller
{

    /* Basic fieldset for the database.
     */
    protected $fields = [


        //Users
        'users' => [
            [
                'field' => 'type',
                'type' => 'int(11) not null',
            ],
            [
                'field' => 'role',
                'type' => 'int(11) null',
            ],
            [
                'field' => 'name',
                'type' => 'varchar(50) null',
            ],
            [
                'field' => 'firstname',
                'type' => 'varchar(50) null',
            ],
            [
                'field' => 'lastname',
                'type' => 'varchar(50) null',
            ],
            [
                'field' => 'image',
                'type' => 'varchar(255) null',
            ],
            [
                'field' => 'gender',
                'type' => 'int(11) null',
            ],
            [
                'field' => 'description',
                'type' => 'longtext null',
            ],
            [
                'field' => 'relation_type',
                'type' => 'int(11) null',
            ],
            [
                'field' => 'relation_id',
                'type' => 'int(11) null',
            ]
        ],
        'ip' => [
            [
                'field' => 'ip',
                'type' => 'varchar(50) not null',
            ],
            [
                'field' => 'relation_type',
                'type' => 'int(11) null',
            ],
            [
                'field' => 'relation_id',
                'type' => 'int(11) null',
            ]
        ],
        'contact' => [
            [
                'field' => 'name',
                'type' => 'varchar(50) not null',
            ],
            [
                'field' => 'value',
                'type' => 'varchar(50) not null',
            ],
            [
                'field' => 'relation_type',
                'type' => 'int(11) null',
            ],
            [
                'field' => 'relation_id',
                'type' => 'int(11) null',
            ]
        ],
        'adress' => [
            [
                'field' => 'type',
                'type' => 'varchar(255) null',
            ],
            [
                'field' => 'street',
                'type' => 'varchar(50) not null',
            ],
            [
                'field' => 'zip',
                'type' => 'varchar(50) not null',
            ],
            [
                'field' => 'city',
                'type' => 'varchar(50) not null',
            ],
            [
                'field' => 'country',
                'type' => 'varchar(50) not null',
            ],
            [
                'field' => 'relation_type',
                'type' => 'int(11) null',
            ],
            [
                'field' => 'relation_id',
                'type' => 'int(11) null',
            ]
        ],
        'bank' => [
            [
                'field' => 'type',
                'type' => 'int(11) null',
            ],
            [
                'field' => 'name',
                'type' => 'varchar(255) not null',
            ],
            [
                'field' => 'iban',
                'type' => 'varchar(255) not null',
            ],
            [
                'field' => 'swift',
                'type' => 'varchar(255) not null',
            ],
            [
                'field' => 'relation_type',
                'type' => 'int(11) null',
            ],
            [
                'field' => 'relation_id',
                'type' => 'int(11) null',
            ]
        ],
        'secret' => [
            [
                'field' => 'type',
                'type' => 'int(11) not null',
            ],
            [
                'field' => 'hash',
                'type' => 'varchar(120) not null',
            ],
            [
                'field' => 'relation_type',
                'type' => 'int(11) null',
            ],
            [
                'field' => 'relation_id',
                'type' => 'int(11) null',
            ]
        ],
        'accounts' => [
            [
                'field' => 'date',
                'type' => 'bigint(20) not null',
            ],
            [
                'field' => 'value',
                'type' => 'float(11) not null',
            ],
            [
                'field' => 'currency',
                'type' => 'varchar(50) not null',
            ],
            [
                'field' => 'description',
                'type' => 'varchar(255) not null',
            ],
            [
                'field' => 'relation_type',
                'type' => 'int(11) not null',
            ],
            [
                'field' => 'relation_id',
                'type' => 'int(11) not null',
            ],
        ],
        'permission' => [
            [
                'field' => 'user_type',
                'type' => 'int(11) not null',
            ],
            [
                'field' => 'user_role',
                'type' => 'int(11) not null',
            ],
            [
                'field' => 'name',
                'type' => 'varchar(255) not null',
            ],
            [
                'field' => 'type',
                'type' => 'int(11) not null',
            ],
            [
                'field' => 'permission',
                'type' => 'int(11) not null',
            ],
        ],

        //Lists
        'lists' => [
            [
                'field' => 'list_group',
                'type' => 'varchar(50) not null',
            ],
            [
                'field' => 'name',
                'type' => 'varchar(50) not null',
            ],
            [
                'field' => 'value',
                'type' => 'varchar(50) null',
            ],
            [
                'field' => 'relation',
                'type' => 'varchar(50) null',
            ],
            [
                'field' => 'color',
                'type' => 'varchar(20) null',
            ]
        ],

        //Relations
        'relations' => [
            [
                'field' => 'object_type',
                'type' => 'varchar(255) not null',
            ],
            [
                'field' => 'object_id',
                'type' => 'int(11) not null',
            ],
            [
                'field' => 'relation_type',
                'type' => 'varchar(255) not null',
            ],
            [
                'field' => 'relation_id',
                'type' => 'int(11) not null',
            ]
        ],

        //Taxonomies
        'taxonomies' => [
            [
                'field' => 'type',
                'type' => 'int(11) not null',
            ],
            [
                'field' => 'field_type',
                'type' => 'varchar(255) null',
            ],
            [
                'field' => 'name',
                'type' => 'longtext null',
            ],
            [
                'field' => 'value',
                'type' => 'longtext null',
            ],
            [
                'field' => 'relation_type',
                'type' => 'int(11) null',
            ],
            [
                'field' => 'relation_id',
                'type' => 'int(11) null',
            ]
        ],

        //Taxonomies
        'metadata' => [
            [
                'field' => 'type',
                'type' => 'int(11) not null',
            ],
            [
                'field' => 'name',
                'type' => 'longtext null',
            ],
            [
                'field' => 'value',
                'type' => 'longtext null',
            ],
            [
                'field' => 'detail',
                'type' => 'varchar(255) null',
            ],
            [
                'field' => 'relation_type',
                'type' => 'int(11) null',
            ],
            [
                'field' => 'relation_id',
                'type' => 'int(11) null',
            ]
        ]
    ];



    /* Required appdata
     */
    protected $data = [
        'lists' => [
            //
            [
                'list_group' => 'admin_role',
                'name' => 'supervisor',
                'value' => 0
            ],
            [
                'list_group' => 'admin_role',
                'name' => 'manager',
                'value' => 1
            ],
            [
                'list_group' => 'admin_role',
                'name' => 'editor',
                'value' => 2
            ],

            //
            [
                'list_group' => 'user_role',
                'name' => 'data',
                'value' => 0
            ],
            [
                'list_group' => 'user_role',
                'name' => 'lead',
                'value' => 1
            ],
            [
                'list_group' => 'user_role',
                'name' => 'customer',
                'value' => 2
            ],
            [
                'list_group' => 'user_role',
                'name' => 'partner',
                'value' => 3
            ],
            [
                'list_group' => 'user_role',
                'name' => 'member',
                'value' => 4
            ],
            [
                'list_group' => 'user_role',
                'name' => 'leader',
                'value' => 5
            ],
            [
                'list_group' => 'user_role',
                'name' => 'stuff',
                'value' => 6
            ],

            //
            [
                'list_group' => 'company_role',
                'name' => 'data',
                'value' => 0
            ],
            [
                'list_group' => 'company_role',
                'name' => 'lead',
                'value' => 1
            ],
            [
                'list_group' => 'company_role',
                'name' => 'customer',
                'value' => 2
            ],
            [
                'list_group' => 'company_role',
                'name' => 'partner',
                'value' => 3
            ],

            //
            [
                'list_group' => 'state',
                'name' => 'inactive',
                'value' => 0
            ],
            [
                'list_group' => 'state',
                'name' => 'active',
                'value' => 1
            ],

            //
            [
                'list_group' => 'gender',
                'name' => 'female',
                'value' => 0
            ],
            [
                'list_group' => 'gender',
                'name' => 'male',
                'value' => 1
            ],
            [
                'list_group' => 'gender',
                'name' => 'unknown',
                'value' => 2
            ],

            //
            [
                'list_group' => 'contact_type',
                'name' => 'phone',
                'value' => 0
            ],
            [
                'list_group' => 'contact_type',
                'name' => 'email',
                'value' => 1
            ],
            [
                'list_group' => 'contact_type',
                'name' => 'skype',
                'value' => 2
            ],
            [
                'list_group' => 'contact_type',
                'name' => 'discord',
                'value' => 3
            ],
            [
                'list_group' => 'contact_type',
                'name' => 'facebook',
                'value' => 4
            ],
            [
                'list_group' => 'contact_type',
                'name' => 'github',
                'value' => 5
            ]
        ],
        'users' => [
            [
                'type' => 0,
                'name' => 'System',
                'firstname' => 'groe',
                'lastname' => 'app'
            ]
        ]
    ];



    public function get_fields_list_from($table){
        $fields = $this->fields[$table];
        return array_column($fields, 'field');
    }



    public function create(){
        $new['fields'] = $this->create_fields();
        $new['data'] = $this->create_data();
        return $new;
    }



    /* creates a table and there required columns
     */
    public function create_fields(){
        $db = new Database($this->container);
        $fields = $this->fields;
        $new = [];

        foreach($fields as $key => $field_list){
            $new[$key]['table'] = $db->create_table($key);
            $table_columns = $db->get_columns($key);

            if($table_columns !== false){
                foreach($field_list as $field_item){
                    if(array_search($field_item['field'], array_column($table_columns, 'Field')) === false){
                        $new[$key]['columns'][$field_item['field']] = $db->create_column($key, $field_item['field'], $field_item['type']);
                    }
                }
            }
        }

        return $new;
    }



    /* creates required data for the app
     */
    public function create_data(){
        $db = new Database($this->container);
        $datas = $this->data;
        $time = new \DateTime();
        $new = [];

        foreach($datas as $key => $data){
            $new[$key] = [];

            foreach ($data as $data_key => $data_values){
                $row = $db->get($key, 'multi', $data_values);
                if(count($row) === 0){
                    array_push($new[$key], $db->create($key, $data_values));
                }
            }
        }

        return $new;
    }
}