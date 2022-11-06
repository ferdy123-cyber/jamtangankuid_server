<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';


use Restserver\Libraries\REST_Controller;

class Upload extends REST_Controller
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
    function index_post()
    {

        $data = $this->post();
        var_dump($data);
        $this->response(['message' => "berhasil menambahkan produk", 'data' => $data], 200);
        // $insert = $this->db->insert('produk', $data['produk']);
        // $insert_id = $this->db->insert_id();
        // $arr = array();
        // foreach ($data['ukuran'] as  $value) {
        //     array_push($arr, ['ukuran_id' => $value['ukuran_id'], 'produk_id' => $insert_id]);
        // };
        // $insert_ukuran = $this->db->insert_batch('ukuran_produk', $arr);
        // if ($insert & $insert_ukuran) {
        //     $this->response(['message' => "berhasil menambahkan produk", 'data' => $data['produk']], 200);
        // } else {
        //     $this->response(['message' => "gagal menambahkan produk"], 502);
        // }
    }
}