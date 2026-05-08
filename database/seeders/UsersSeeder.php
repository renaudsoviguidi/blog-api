<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $data_users = [
            [
                "name" => "Admin Blog API",
                "email" => "devblogapi@yopmail.com",
                "password" => Hash::make("Blog@PI2025"),
            ]
        ];

        foreach ($data_users as $item) {

            User::updateOrCreate(
                ["email"=>$item["email"]],
                [
                    "name"=>$item["name"],
                    "password"=>$item["password"],
                    "is_active" => 1,
                    "ref" => Str::uuid()
                ]
            );

        }
    }
}
