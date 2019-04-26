<?php
namespace src\Uploads;
use src\Database\Tables\Table_Methods;
use src\Library\Helper;

class Uploads
{
    protected $container;



    public function __construct ($container) {
        $this->container = $container;
    }



    public function upload_user_image($request){
        $table_methods = new Table_Methods($this->container);
        $params = $request->getParams();
        $files = $request->getUploadedFiles();

        //check file exists
        if (empty($files['files'])) {
            throw new \Exception('Expected a file');
        }
        $file = $files['files'];

        //check user exists
        $user = $table_methods->get_one_user(
            ['id' => $params['user_id']]
        );


        $file_name = null;
        if(isset($user['id'])){
            $file_name = 'user' . $user['id'] . '.'. pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        }
        else {
            return array('errors' => $this->container->get('state')['UNKNOWN_USER']);
        }

        //move file
        $moved_file = $this->move_uploaded_file($file, $params['file_type'], $file_name);
        if(isset($moved_file['errors'])){
            return $moved_file;
        }
        else {
            //update user
            $update_params['image'] = $moved_file['directory'];
            $update_params['uid'] = $params['uid'];
            $conditions['id'] = $params['user_id'];

            //delete old file

            return $table_methods->update_user($update_params, $conditions);
        }
    }



    public function delete_user_image($user_id){
        $query = "
            UPDATE users 
            SET image = null, edit_user_id = :edit_user_id, edit_date = :edit_date
            WHERE id = :user_id
        ";

        $data_head = Helper::data_head($user_id);

        $statement = $this->container['db']->prepare($query);
        return $statement->execute([':user_id' => $user_id, ':edit_user_id' => $data_head['edit_user_id'], ':edit_date' => $data_head['edit_date']]);
    }



    public function name_versions($path, $file_name) {
        $full_path = "$path/$file_name";
        if (!file_exists($full_path)) return $file_name;
        $file_name_only = pathinfo($file_name,PATHINFO_FILENAME);
        $extension_only = pathinfo($file_name, PATHINFO_EXTENSION);

        $i = 1;
        while(file_exists("$path/$file_name_only ($i).$extension_only")) $i++;
        return "$file_name_only ($i).$extension_only";
    }



    function move_uploaded_file($file, $filetype, $file_name = null)
    {
        $directorys = $this->container->get('directorys');

        if(empty($directorys)){
            throw new \Exception('File directory missing');
        }

        if ($file->getError() === UPLOAD_ERR_OK) {
            $uploadFileName = $file->getClientFilename();

            $file_name = $file_name === null
                ? $uploadFileName
                : $file_name;

            $file_name = $this->name_versions($directorys[$filetype]['server'], $file_name);

            $file->moveTo($directorys[$filetype]['server'] . '/' . $file_name);
            return array('directory' => $directorys[$filetype]['request'] . '/' . $file_name);
        }
        else {
            return array('errors' => $file->getError());
        }
    }
}