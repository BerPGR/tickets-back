<?php

namespace app\models;
use app\models\Model;

class Users extends Model {
    protected static string $table = "users";
    protected static array $hidden = ['password_hash'];
}