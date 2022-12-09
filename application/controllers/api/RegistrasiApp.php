<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';


use Restserver\Libraries\REST_Controller;

class registrasiApp extends REST_Controller
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
    function index_post()
    {
        $data = [
            'email' => $this->post('email'),
            'password' => password_hash($this->post('password'), PASSWORD_DEFAULT),
            'nama' => $this->post('nama'),
            'jk' => $this->post('jk'),
            'alamat' => $this->post('alamat')
        ];
        $cekEmail = $this->db->get_where('user_app', ['email' => $data['email']])->result();
        if (count($cekEmail) >= 1) {
            $this->response(['message' => "email sudah terdaftar"], 400);
            return;
        }
        $insert = $this->db->insert('user_app', $data);
        if ($insert) {
            $this->response(['message' => "register berhasil", 'data' => $data], 200);
        } else {
            $this->response(['message' => "gagal register"], 502);
        }
    }
    function index_put()
    {
        $id = $this->uri->segment("3");
        $data = $this->put();
        $update = $this->db->update('user_app', $data, ['id' => $id]);
        if ($update) {
            $user = $this->db->get_where('user_app', ['id' => $id])->result();
            $this->response(['message' => 'success', 'data' => $user[0]], 200);
        } else {
            $this->response(['message' => 'update gagal'], 502);
        }
    }
}