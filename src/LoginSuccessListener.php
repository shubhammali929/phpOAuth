<?php
namespace Minioarage2\Phpoauth;
interface LoginSuccessListener {
    public function onLoginSuccess($userInfo);
    public function onError($errorMessage);
}
