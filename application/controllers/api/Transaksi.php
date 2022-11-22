<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';


use Restserver\Libraries\REST_Controller;

class Transaksi extends REST_Controller
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
        $this->db->order_by('id', 'DESC');
        if (count($param) == 0) {
            $api = $this->db->get('transaksi')->result();
        } else {
            $this->db->where($param);
            $api = $this->db->get('transaksi')->result();
        }
        $this->response(['message' => 'success', 'data' => $api, 'status' => 200], 200);
    }
    function index_delete()
    {
        $id = $this->uri->segment("3");
        $this->db->where(['cart_id' => $id]);
        $cart = $this->db->get('cart')->result()[0];
        $cart_data = (json_decode(json_encode($cart), true));
        // var_dump($cart_data);
        $produk = $this->db->get_where('produk', ['id' => $cart_data['produk_id']])->result()[0];
        $produk_data = (json_decode(json_encode($produk), true));
        $delete = $this->db->delete("cart", ['cart_id' => $id]);
        if ($delete) {
            $this->db->update(
                'produk',
                ['stok' => $cart_data['quantity'] + $produk_data['stok']],
                ['id' => $cart_data['produk_id']]
            );
            $this->response(['message' => 'sucess delete keranjang'], 200);
        } else {
            $this->response(['message' => 'error saat delete keranjang'], 502);
        }
    }

    function index_post()
    {
        $data = $this->post();
        $insert = $this->db->insert('transaksi', $data);
        if ($insert) {
            $insert_id = $this->db->insert_id();
            $this->db->update('cart', ['transaction_id' => $insert_id], ['user_id' => $data['user_id'], 'transaction_id' => 0]);
            $this->response(['message' => "berhasil menambah transaksi", 'data' => $data, 'id' => $insert_id], 200);
        } else {
            $this->response(['message' => "gagal menambah transaksi"], 502);
        }
    }
}