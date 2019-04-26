<?php
namespace src\Library;

class Helper
{
    public static function time_ago($time_ago){
        $time = new \DateTime();
        return $time->getTimestamp() - intval($time_ago);
    }



    public static function human_time($seconds){
        $seconds = ($seconds <1 )
            ? 1
            : $seconds;

        $time_names = array (
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );

        foreach ($time_names as $time => $name) {
            if ($seconds < $time) continue;

            $units = floor($seconds / $time);
            return array('units' => $units, 'name' => $name);
        }
    }



    public static function time_unit_amount($seconds, $unit){
        $seconds = ($seconds <1 )
            ? 1
            : $seconds;

        $units = array (
            'year' => 31536000,
            'month' => 2592000,
            'week' => 604800,
            'day' => 86400,
            'hour' => 3600,
            'minute' => 60,
            'second' => 1
        );

        return floor($seconds / $units[$unit]);
    }



    /* creates a array for mysql row addings
     * @params $user_id
     */
    public static function data_head( $user_id, $method = null){
        $time = new \DateTime();

        $head = [
            'edit_user_id' => $user_id,
            'edit_date' => $time->getTimestamp(),
        ];

        if($method === 'create'){
            $head['state'] = 1;
            $head['create_user_id'] = $user_id;
            $head['create_date'] = $time->getTimestamp();
        }

        return $head;
    }



    public static function generateRandomString ($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }



    /**
     * Checks if the given data is valid
     * @param $data - the data to check
     * @param string $type - the type that the data is supposed to be
     *        possible values: text, number, integer, date
     * @param $options
     *          for text type:
     *              maxlength - integer value, maximum length that the text should have
     *              minlength - integer value, minimum length that the text should have
     *          for number type:
     *              minvalue - minimum value that the data should have (inclusive)
     *              maxvalue - maximum value that the data should have (inclusive)
     *          for integer type:
     *              same options as number type
     *          for date type:
     *              no options available
     * @return boolean - true data is valid, false otherwise
     */
    public static function validate($data,$type = 'text', $options = null){
        switch ($type){
            default:
            case 'text':
                if (is_string($data) || is_numeric($data)){
                    if (isset($options['minlength']) && strlen($data) < $options['minlength'])
                        return 3;
                    if (isset($options['maxlength']) && strlen($data) > $options['maxlength'])
                        return 4;
                    return 2;
                }else{
                    return 5;
                }
                break;
            case 'number':
                if (is_numeric($data)){
                    if (isset($options['minvalue']) && $data < $options['minvalue'])
                        return 3;
                    if (isset($options['maxvalue']) && $data > $options['maxvalue'])
                        return 4;
                    return 2;
                }else{
                    return 6;
                }
                break;
            case 'integer':
                if (is_numeric($data) && strpos($data, '.') === false ){
                    if (isset($options['minvalue']) && $data < $options['minvalue'])
                        return 3;
                    if (isset($options['maxvalue']) && $data > $options['maxvalue'])
                        return 4;
                    return 2;
                }else{
                    return 7;
                }
                break;
            case 'email':
                if (filter_var($data, FILTER_VALIDATE_EMAIL) ){
                    if (isset($options['minvalue']) && $data < $options['minvalue'])
                        return 3;
                    if (isset($options['maxvalue']) && $data > $options['maxvalue'])
                        return 4;
                    return 2;
                }else{
                    return 13;
                }
                break;
            case 'date':
                $date = explode(".",$data);
                if (count($date) != 3)
                    return 2;
                if (checkdate($date[1], $date[0], $date[2])) {
                    return true;
                } else {
                    return 8;
                }
                break;
        }
    }


    /**
     * Formats the given date string into a german date string of the format dd.mm.yyyy
     * @param $date
     * @return string
     */
    public static function formatDateToGerman($timestamp){
        setlocale(LC_TIME, "de_DE");
        $date = date('d.m.Y',$timestamp);
        return $date;
    }



    /**
     * Formats the given german date string into an international date string of the format yyyy-mm-dd
     * @param $date
     * @return string the formatted date string or false if the given date is not a valid german date string
     */
    public static function formatDateFromGerman($date){
        $date = explode(".",$date);
        if (count($date) != 3 || !checkdate($date[1], $date[0], $date[2]))
            return false;
        $date = new \DateTime("$date[2]-$date[1]-$date[0]");
        return $date->format("Y-m-d");
    }



    public static function decimalNumber($number){
        if( strpos($number, ',') !== false ) {
            $number = str_replace(',', '.', $number);
        }

        return round($number, 2);
    }



    public static function buildFullName($elements){
        foreach($elements as $elemKey => $element){
            $elements[$elemKey]['user_name'] = $element['firstname'] . ' ' . $element['lastname'];
        }

        return $elements;
    }



    public static function dezimalFormat($number){
        return number_format(
            $number,
            2,
            ".",
            ""
        );
    }



    public static function check_required_params($required_params, $given_params){
        $errors = [];

        foreach($required_params as $required_param){
            if(!isset($given_params[$required_param])){
                array_push($errors, $required_param);
            }
        }

        if(count($errors) > 0){
            return array(
                'errors' => 22,
                'details' => $errors
            );
        }
        else {
            return false;
        }
    }


    public static function unset_in_array( array $datas, array $unsets){
        foreach ($datas as $d_key => $data){
            foreach($unsets as $unset){
                if(isset($datas[$d_key][$unset])){
                    unset( $datas[$d_key][$unset] );
                }
            }
        }
        return $datas;
    }


    public static function is_created($data){
        return isset($data['errors'])
            ? $data
            : array('create' => true);
    }



    public static function is_updated($data){
        return isset($data['errors'])
            ? $data
            : array('update' => true);
    }



    public static function array_key_first( $array ) {
        $key = null;

        if ( is_array( $array ) ) {

            foreach ( $array as $key => $value ) {
                break;
                    }
        }

    return $key;
    }



    public static function check_limit_offset($params, $max = null){
        $max = $max === null
            ? '10'
            : $max;


        $limit = isset($params['limit'])
            ? $params['limit'] > $max
                ? $max
                : $params['limit']
            : '';



        $order = isset($params['limit'])
            ? isset($params['offset'])
                ? " LIMIT {$params['offset']}, {$limit}"
                : " LIMIT " . $limit
            : '';
        $order .= isset($params['offset'])
            ? isset($params['limit'])
                ? ''
                : " LIMIT {$params['offset']}, {$max}"
            : '';

        return $order;
    }



    public static function check_condition_method($params, $condtions){
        foreach ($params as $param_key => $param){
            if(strpos($param_key, '->') > 0){
                $condtions[$param_key] = $param;
            }
        }
        return $condtions;
    }
}