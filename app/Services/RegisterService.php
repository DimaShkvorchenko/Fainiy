<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class RegisterService
{
    /**
     * @param array $fields
     * @return Model|User
     */
    public function createAdmin(array $fields): Model|User
    {
        return $this->createUser(array_merge($fields, ['account_type' => User::ADMIN_TYPE]));
    }

    /**
     * @param array $fields
     * @return Model|User
     */
    public function createClient(array $fields): Model|User
    {
        if (empty($fields['modules'])) {
            $fields['modules'] = json_encode(["transfer", "exchange"]);
        }
        return $this->createUser(array_merge($fields, ['account_type' => User::CLIENT_TYPE]));
    }

    /**
     * @param array $fields
     * @return Model|User
     */
    public function createStaff(array $fields): Model|User
    {
        return $this->createUser(array_merge($fields, ['account_type' => User::STAFF_TYPE]));
    }

    /**
     * @param array $fields
     * @return Model|User
     */
    public function createUser(array $fields): Model|User
    {
        return User::create(
            Arr::except($fields, ['password', 'password_confirmation']) +
            ['password' => Hash::make($fields['password'])]
        );
    }
}
