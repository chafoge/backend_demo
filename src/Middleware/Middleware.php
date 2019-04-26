<?php
namespace src\Middleware;

class Middleware extends Middleware_Controller
{
    public function __invoke ($request, $response, $next) {
        $auth_methods = new Authentication($this->container);
        $perm_methods = new Permission($this->container);

        $perm_data = null;
        if($this->is_perm){
            $perm_data = $perm_methods->permission($request, $response);
            if(isset($perm_data['perm']['errors'])){
                return $response->withJson( $this->render_data( $perm_data, $request ));
            }
        }

        $auth_data = null;
        if($this->is_auth){
            $auth_data = $auth_methods->authenticate($request, $response);
            if(isset($auth_data['errors'])){
                return $response->withJson( $this->render_data( $auth_data, $request ));
            }
        }

        //add request
        $request = $request->withAttribute('permission', $perm_data);

        //run response
        $response = $next($request, $response);

        $response_body = $this->rebuild_response_body($response);
        $response_body = $this->render_data($response_body, $request);
        $response_body = $this->render_auth($auth_data, $response_body);
        $response_body = $this->render_perm($perm_data, $response_body);

        return $response->withJson($response_body);
    }


    public function rebuild_response_body($response){
        $stream_body = $response->getBody();
        $stream_body->rewind();
        $content = $stream_body->getContents();

        return json_decode( $content, true );
    }



    public function render_data ($response_data, $request) {
        $params = $request->getParams();
        $response['response'] = $response_data;

        $return_token = isset($params['request_token']) ? $params['request_token'] : null;
        if ( $return_token != null) {
            $response['request_token'] = $return_token;
        }

        return $response;
    }


    public function render_auth ($auth_data, $response) {
        if ($auth_data !== null) {
            foreach ($auth_data as $key => $value) {
                $response[$key] = $value;
            }
        }

        return $response;
    }


    public function render_perm ($perm_data, $response) {
        $response['permission'] = $perm_data;
        return $response;
    }
}