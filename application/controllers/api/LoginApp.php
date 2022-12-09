    <?php

    defined('BASEPATH') or exit('No direct script access allowed');

    require APPPATH . '/libraries/REST_Controller.php';


    use Restserver\Libraries\REST_Controller;

    class loginApp extends REST_Controller
    {


        public function __construct()
        {
            parent::__construct();
            header('Access-Control-Allow-Origin: *');
            header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
            header("Access-Control-Allow-Credentials: true");
            $method = $_SERVER['REQUEST_METHOD'];
            if ($method == "OPTIONS") {
                die();
            }
        }

        public function index_post()
        {
            // Get the post data
            $email = $this->post('email');
            $password = $this->post('password');
            $cekemail = $this->db->get_where('user_app', ['email' => $email])->result();
            if (count($cekemail) === 0) {
                $this->response(['status' => 400, 'message' => 'User tidak ditemukan'], 400);
                return;
            }
            $userpass = $this->db->get_where('user_app', ['email' => $email]);
            $cekpass = (password_verify($password, $userpass->row_array()['password']));
            if ($cekpass) {
                $this->response(['message' => "login berhasil", 'data' => $userpass->row_array()], 200);
            } else {
                $this->response(['message' => "password salah"], 400);
            }
        }
    }