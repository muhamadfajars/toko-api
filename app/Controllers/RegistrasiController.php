<?php

namespace App\Controllers;

use App\Models\MRegistrasi;
use CodeIgniter\RESTful\ResourceController;

class RegistrasiController extends ResourceController
{
    protected $format = 'json';

    public function registrasi()
    {
        $validation =  \Config\Services::validation();
        $validation->setRules([
            'nama' => 'required',
            'email' => 'required|valid_email',
            'password' => 'required|min_length[5]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->responseHasil(400, false, $validation->getErrors());
        }

        $data = [
            'nama' => $this->request->getVar('nama'),
            'email' => $this->request->getVar('email'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT)
        ];

        $model = new MRegistrasi();
        try {
            $model->save($data);
            return $this->responseHasil(200, true, "Registrasi Berhasil");
        } catch (\Exception $e) {
            return $this->responseHasil(500, false, "Registrasi Gagal: " . $e->getMessage());
        }
    }

    protected function responseHasil($code, $status, $data)
    {
        return $this->respond([
            'code' => $code,
            'status' => $status,
            'data' => $data
        ]);
    }
}