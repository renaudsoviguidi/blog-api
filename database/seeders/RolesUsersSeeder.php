<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RolesUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $adminUser = User::where("email", "devblogapi@yopmail.com")->first();

        $adminUser->roles()->sync(
            Role::whereIn('libelle', ['ADMIN', 'EDITOR'])->pluck('id')->toArray()
        );
    }
}
