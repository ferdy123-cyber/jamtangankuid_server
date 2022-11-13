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
        $name = $this->get('name');
        $limit = $this->get('limit');
        $offset = $this->get('offset');
        if ($name) {
            $this->db->like('name', $name);
            $this->db->order_by('id', 'DESC');
            $api = $this->db->get('produk', $limit, $offset)->result();
            $this->db->like('name', $name);
            $total = $this->db->get('produk')->num_rows();
        } else {
            $total = $this->db->get('produk')->num_rows();
            $this->db->order_by('updatedAt', 'DESC');
            $api = $this->db->get('produk', $limit, $offset)->result();
        }
        $this->response(['message' => 'success', 'data' => $api, 'status' => 200, 'limit' => $limit, 'offset' => $offset, 'total' => $total], 200);
    }
    function detail_get()
    {
        $id = $this->uri->segment("4");
        $api = $this->db->get_where('produk', ['id' => $id])->result();
        if (count($api) != 0) {
            $this->response(['message' => 'success', 'data' => $api[0], 'status' => 200], 200);
        } else {
            $this->response(['message' => 'produk tidak ditemukan'], 200);
        }
    }
    function sort_terlaris_get()
    {
        $name = $this->get('name');
        $limit = $this->get('limit');
        $offset = $this->get('offset');
        if ($name) {
            $this->db->like('name', $name);
            $this->db->order_by('amount_of_selling', 'DESC');
            $api = $this->db->get('produk', $limit, $offset)->result();
            $this->db->like('name', $name);
            $total = $this->db->get('produk')->num_rows();
        } else {
            $total = $this->db->get('produk')->num_rows();
            $this->db->order_by('amount_of_selling', 'DESC');
            $api = $this->db->get('produk', $limit, $offset)->result();
        }
        $this->response(['message' => 'success', 'data' => $api, 'status' => 200, 'limit' => $limit, 'offset' => $offset, 'total' => $total], 200);
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
                'stok' => $this->post('stok'),
                'description' => $this->post('description'),
                'promo' => 'N',
                'rekomended' => 'N',
                'price_promo' => 0,
                'amount_of_selling' => 0,
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
    function rekomended_get()
    {
        $name = $this->get('name');
        $limit = $this->get('limit');
        $offset = $this->get('offset');
        if ($name) {
            $this->db->like('name', $name);
            $this->db->order_by('updatedAt', 'DESC');
            $this->db->where(['rekomended' => 'Y']);
            $api = $this->db->get('produk',  $limit, $offset)->result();
            $this->db->like('name', $name);
            $this->db->where(['rekomended' => 'Y']);
            $total = $this->db->get('produk')->num_rows();
        } else {
            $this->db->where(['rekomended' => 'Y']);
            $total = $this->db->get('produk')->num_rows();
            $this->db->order_by('updatedAt', 'DESC');
            $this->db->where(['rekomended' => 'Y']);
            $api = $this->db->get('produk', $limit, $offset)->result();
        }
        $this->response(['message' => 'success', 'data' => $api, 'status' => 200, 'limit' => $limit, 'offset' => $offset, 'total' => $total], 200);
    }
    function promo_get()
    {
        $name = $this->get('name');
        $limit = $this->get('limit');
        $offset = $this->get('offset');
        if ($name) {
            $this->db->like('name', $name);
            $this->db->order_by('updatedAt', 'DESC');
            $this->db->where(['promo' => 'Y']);
            $api = $this->db->get('produk',  $limit, $offset)->result();
            $this->db->like('name', $name);
            $this->db->where(['promo' => 'Y']);
            $total = $this->db->get('produk')->num_rows();
        } else {
            $this->db->where(['promo' => 'Y']);
            $total = $this->db->get('produk')->num_rows();
            $this->db->order_by('updatedAt', 'DESC');
            $this->db->where(['promo' => 'Y']);
            $api = $this->db->get('produk', $limit, $offset)->result();
        }
        $this->response(['message' => 'success', 'data' => $api, 'status' => 200, 'limit' => $limit, 'offset' => $offset, 'total' => $total], 200);
    }
}