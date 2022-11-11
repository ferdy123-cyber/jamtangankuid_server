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
            $this->db->order_by('updatedAt', 'DESC');
            $api = $this->db->get('produk', 0, 0)->result();
        } else {
            $this->db->where($param);
            $api = $this->db->get('produk')->result();
        }
        $this->response(['message' => 'success', 'data' => $api, 'status' => 200], 200);
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
        $this->db->where(['id' => $id]);
        $produk = $this->db->get('produk')->result()[0];
        unlink('.' . $produk->image);
        $delete = $this->db->delete("produk", ['id' => $id]);
        if ($delete) {
            $this->response(['message' => 'sucess delete produk'], 200);
        } else {
            $this->response(['message' => 'error saat delete produk'], 502);
        }
    }

    function index_post()
    {
        $config['upload_path'] = './images/';
        $config['allowed_types'] = 'gif|jpg|jpeg|png|jfif|webp';
        // $config['max_size']     = '100';
        // $config['max_width'] = '1024';
        // $config['max_height'] = '768';

        $this->load->library('upload', $config);
        $cek = $this->upload->do_upload('image');
        if ($cek) {
            $res = $this->upload->data();
            $data = [
                'name' => $this->post('name'),
                'price' => $this->post('price'),
                'description' => $this->post('description'),
                'image' => '/images/' . $res['file_name']
            ];
            $insert = $this->db->insert('produk', $data);
            if ($insert) {
                $this->response(['message' => "berhasil menambahkan produk", 'data' => $data], 200);
            } else {
                $this->response(['message' => "gagal menambahkan produk"], 502);
            }
        } else {
            $this->response(['message' => 'error upload'], 502);
        }
    }
}