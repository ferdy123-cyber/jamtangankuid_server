<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';


use Restserver\Libraries\REST_Controller;

class registrasi extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
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
        $nama = strip_tags($this->post('nama'));
        $email = strip_tags($this->post('email'));
        $password = $this->post('password');
        $role_id = $this->post('role_id');
        $alamat = $this->post('alamat');
        $tanggal_input = $this->post('tanggal_input');


        // Validate the post data
        if (!empty($nama) && !empty($email) && !empty($password)) {

            // Check if the given email already exists
            $con['returnType'] = 'count';
            $con['conditions'] = array(
                'email' => $email,
            );
            $userCount = $this->user->getRows($con);

            if ($userCount > 0) {
                // Set the response and exit
                $this->response(['message' => "The given email already exists.", 'status' => REST_Controller::HTTP_BAD_REQUEST], REST_Controller::HTTP_BAD_REQUEST);
            } else {
                // Insert user data
                $userData = array(
                    'nama' => $nama,
                    'email' => $email,
                    'role_id' => $role_id,
                    'alamat' => $alamat,
                    'tanggal_input' => $tanggal_input,
                    'password' => password_hash($password, PASSWORD_DEFAULT),

                );
                $insert = $this->user->insert($userData);

                // Check if the user data is inserted
                if ($insert) {
                    // Set the response and exit
                    $this->response([
                        'is_active' => TRUE,
                        'message' => 'The user has been added successfully.',
                        'data' => $insert
                    ], REST_Controller::HTTP_OK);
                } else {
                    // Set the response and exit
                    $this->response(['message' => "Some problems occurred, please try again.", 'status' => REST_Controller::HTTP_BAD_REQUEST], REST_Controller::HTTP_BAD_REQUEST);
                }
            }
        } else {
            // Set the response and exit
            $this->response(['message' => "Provide complete user info to add.", 'status' => REST_Controller::HTTP_BAD_REQUEST], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}