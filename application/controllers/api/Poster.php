<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';


use Restserver\Libraries\REST_Controller;

class Poster extends REST_Controller
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
        $total = $this->db->get('poster')->num_rows();
        $this->db->order_by('updatedAt', 'DESC');
        $api = $this->db->get('poster', $limit, $offset)->result();

        $this->response(['message' => 'success', 'data' => $api, 'status' => 200, 'limit' => $limit, 'offset' => $offset, 'total' => $total], 200);
    }
    function index_delete()
    {
        $id = $this->uri->segment("3");
        $this->db->where(['id' => $id]);
        $poster = $this->db->get('poster')->result()[0];
        unlink('.' . $poster->image);
        $delete = $this->db->delete("poster", ['id' => $id]);
        if ($delete) {
            $this->response(['message' => 'sucess delete poster'], 200);
        } else {
            $this->response(['message' => 'error saat delete poster'], 502);
        }
    }

    function index_post()
    {
        $config['upload_path'] = './images/poster/';
        $config['allowed_types'] = 'gif|jpg|jpeg|png|jfif|webp';
        $this->load->library('upload', $config);
        $cek = $this->upload->do_upload('image');
        if ($cek) {
            $res = $this->upload->data();
            $data = [
                'image' => '/images/poster/' . $res['file_name']
            ];
            $insert = $this->db->insert('poster', $data);
            if ($insert) {
                $this->response(['message' => "berhasil menambahkan poster", 'data' => $data], 200);
            } else {
                $this->response(['message' => "gagal menambahkan poster"], 502);
            }
        } else {
            $this->response(['message' => 'error upload'], 502);
        }
    }
}