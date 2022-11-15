<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';


use Restserver\Libraries\REST_Controller;

class Cart extends REST_Controller
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
        $limit = $this->get('limit');
        $offset = $this->get('offset');
        $user_id = $this->uri->segment("3");;
        $this->db->where(['user_id' => $user_id]);
        $total = $this->db->get('cart')->num_rows();
        $this->db->select('*');
        $this->db->from('cart');
        $this->db->join('produk', 'produk.id = cart.produk_id');
        $this->db->where(['user_id' => $user_id, 'transaction_id' => 0]);
        $this->db->order_by('cart_id', 'DESC');
        $this->db->limit($limit, $offset);
        $api = $this->db->get()->result();

        $this->response(['message' => 'success', 'data' => $api, 'status' => 200, 'limit' => $limit, 'offset' => $offset, 'total' => $total], 200);
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
        $cekcart = $this->db->get_where('cart', [
            'produk_id' => $data['produk_id'],
            'user_id' => $data['user_id']
        ])->result();
        if (count($cekcart) >= 1) {
            $produk = $this->db->get_where('produk', ['id' => $data['produk_id']])->result()[0];
            $produk_stok = (json_decode(json_encode($produk), true)['stok']);
            if ($produk_stok - $data['quantity'] < 0) {
                $this->response(['message' => "pemesanan melebihi stok"], 400);
                return;
            }
            $this->db->update('produk', ['stok' => $produk_stok - $data['quantity']], ['id' => $data['produk_id']]);
            $cart_qty = (json_decode(json_encode($cekcart), true)[0]);
            // var_dump($cart_qty['quantity'] + $data['quantity']);
            $this->db->update('cart', ['quantity' => $cart_qty['quantity'] + $data['quantity']], [
                'produk_id' => $data['produk_id'],
                'user_id' => $data['user_id']
            ]);
            $this->response(['message' => "berhasil menambah keranjang"], 200);
            return;
        }
        $insert = $this->db->insert('cart', $data);
        if ($insert) {
            $produk = $this->db->get_where('produk', ['id' => $data['produk_id']])->result()[0];
            $produk_stok = (json_decode(json_encode($produk), true)['stok']);
            if ($produk_stok - $data['quantity'] < 0) {
                $this->response(['message' => "pemesanan melebihi stok"], 400);
                return;
            }
            $this->db->update('produk', ['stok' => $produk_stok - $data['quantity']], ['id' => $data['produk_id']]);
            $this->response(['message' => "berhasil menambah keranjang", 'data' => $data], 200);
        } else {
            $this->response(['message' => "gagal menambah keranjang"], 502);
        }
    }
}