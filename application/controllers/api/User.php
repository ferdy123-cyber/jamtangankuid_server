<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';


use Restserver\Libraries\REST_Controller;

class User extends REST_Controller
{

    function __construct($config = 'rest')
    {
        parent::__construct($config);
        $this->load->database();
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }
    }

    //Menampilkan hasil loping login akun user
    function index_get()
    {
        $param = $this->get();
        if (count($param) == 0) {
            $api = $this->db->get('user')->result();
        } else {
            $this->db->where($param);
            $api = $this->db->get('user')->result();
        }
        $this->response(['message' => 'success', 'data' => $api, 'status' => 200], 200);
    }
    function index_put()
    {
        $id = $this->uri->segment("3");
        $data = $this->put();
        $update = $this->db->update('user', $data, ['id' => $id]);
        if ($update) {
            $this->response(['message' => 'update user berhasil', 'data' => $data, 'status' => 200], 200);
        } else {
            $this->response(['message' => 'update gagal', 'status' => 502], 502);
        }
    }
    function index_delete()
    {
        $id = $this->uri->segment("3");
        $delete = $this->db->delete("user", ['id' => $id]);
        if ($delete) {
            $this->response(['message' => 'sucess delete user'], 200);
        } else {
            $this->response(['message' => 'error saat delete user'], 502);
        }
    }
}