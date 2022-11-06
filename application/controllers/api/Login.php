    <?php

    defined('BASEPATH') or exit('No direct script access allowed');

    require APPPATH . '/libraries/REST_Controller.php';


    use Restserver\Libraries\REST_Controller;

    class login extends REST_Controller
    {


        public function __construct()
        {
            parent::__construct();
            $this->load->database();
            header('Access-Control-Allow-Origin: *');
            header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
            header("Access-Control-Allow-Credentials: true");
            $method = $_SERVER['REQUEST_METHOD'];
            if ($method == "OPTIONS") {
                die();
            }
            // Load the user model
            $this->load->model('user');
        }

        public function index_post()
        {
            // Get the post data
            $email = $this->post('email');
            $password = $this->post('password');

            $cekuser = $this->db->get_where('user', ['email' => $email])->result();

            if (count($cekuser) === 0) {
                $this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'message' => 'User tidak ditemukan'], REST_Controller::HTTP_BAD_REQUEST);
                return;
            }

            // Validate the post data
            if (!empty($email) && !empty($password)) {

                $userpass = $this->db->get_where('user', ['email' => $email]);
                $cekpass = (password_verify($password, $userpass->row_array()['password']));

                // Check if any user exists with the given credentials
                $con['returnType'] = 'single';
                $con['conditions'] = array(
                    'email' => $email
                );
                $user = $this->user->getRows($con);

                if ($user && $cekpass) {
                    // Set the response and exit
                    $this->response([
                        'status' => REST_Controller::HTTP_OK,
                        'is_active' => TRUE,
                        'message' => 'User login berhasil bro.',
                        'data' => $user
                    ], REST_Controller::HTTP_OK);
                } else {
                    // Set the response and exit
                    //BAD_REQUEST (400) being the HTTP response code
                    $this->response(['message' => "Ada kesalahan di email atau password.", 'status' => REST_Controller::HTTP_BAD_REQUEST], REST_Controller::HTTP_BAD_REQUEST);
                }
            } else {
                // Set the response and exit
                $this->response(['message' => "Belum mengisi email dan password.", 'status' => REST_Controller::HTTP_BAD_REQUEST], REST_Controller::HTTP_BAD_REQUEST);
            }
        }
    }