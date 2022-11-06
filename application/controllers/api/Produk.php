<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';


use Restserver\Libraries\REST_Controller;

class Produk extends REST_Controller
{

    function __construct($config = 'rest')
    {
        parent::__construct($config);
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }
        $this->load->database();
    }

    function index_get()
    {
        $param = $this->get();
        if (count($param) == 0) {
            $this->db->order_by('tanggal_input', 'DESC');
            $api = $this->db->get('produk')->result();
        } else {
            $this->db->where($param);
            $api = $this->db->get('produk')->result();
        }
        $this->response(['message' => 'success', 'data' => $api, 'status' => 200], 200);
    }
    function index_post()
    {

        $data = $this->post();
        $insert = $this->db->insert('produk', $data['produk']);
        $insert_id = $this->db->insert_id();
        $arr = array();
        foreach ($data['ukuran'] as  $value) {
            array_push($arr, ['ukuran_id' => $value['ukuran_id'], 'produk_id' => $insert_id]);
        };
        $insert_ukuran = $this->db->insert_batch('ukuran_produk', $arr);
        if ($insert & $insert_ukuran) {
            $this->response(['message' => "berhasil menambahkan produk", 'data' => $data['produk']], 200);
        } else {
            $this->response(['message' => "gagal menambahkan produk"], 502);
        }
    }
    function index_put()
    {
        $id = $this->uri->segment("3");
        $data = $this->put();
        $update = $this->db->update('produk', $data, ['id' => $id]);
        if ($update) {
            $this->response(['message' => 'update produk berhasil', 'data' => $data], 200);
        } else {
            $this->response(['message' => 'update gagal'], 502);
        }
    }
    function index_delete()
    {
        $id = $this->uri->segment("3");
        $delete = $this->db->delete("motivasi", ['id' => $id]);
        if ($delete) {
            $this->response(['message' => 'sucess delete motivasi'], 200);
        } else {
            $this->response(['message' => 'error saat delete motivasi'], 502);
        }
    }
}