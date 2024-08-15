<?php
namespace App\Controllers;

use App\Models\MLogin;
use App\Models\MMember;
use CodeIgniter\RESTful\ResourceController;

class LoginController extends ResourceController {

    public function login() {
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        // Debugging
        log_message('debug', 'Email: ' . $email);
        log_message('debug', 'Password: ' . $password);

        $memberModel = new MMember();
        $member = $memberModel->where(['email' => $email])->first();
        if (!$member) {
            return $this->responseHasil(400, false, 'Email tidak ditemukan');
        }

        if (!password_verify($password, $member['password'])) {
            return $this->responseHasil(400, false, 'Password tidak valid');
        }

        $loginModel = new MLogin();
        $auth_key = $this->RandomString();
        $loginModel->save([
            'member_id' => $member['id'],
            'auth_key' => $auth_key
        ]);

        $data = [
            'token' => $auth_key,
            'user' => [
                'id' => $member['id'],
                'email' => $member['email']
            ]
        ];

        return $this->responseHasil(200, true, $data);
    }

    private function RandomString($length = 100) {
        $karakter = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $panjang_karakter = strlen($karakter);
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $karakter[rand(0, $panjang_karakter - 1)];
        }
        return $str;
    }

    protected function responseHasil($code, $status, $data) {
        return $this->respond([
            'code' => $code,
            'status' => $status,
            'data' => $data
        ]);
    }
}