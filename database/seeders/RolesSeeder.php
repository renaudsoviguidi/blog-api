<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $adminUser = User::where("email", "devblogapi@yopmail.com")->first();

        $data_roles = [
            ["libelle" => "ADMIN", "description" => "Administrateur du système"],
            ["libelle" => "GUEST", "description" => "Peut lire les articles"],
            ["libelle" => "EDITOR", "description" => "Peut valider les articles"],
            ["libelle" => "USER", "description" => "Peut écrire des articles"],
        ];

        foreach ($data_roles as $item) {

            Role::updateOrCreate(
                ["libelle" => $item["libelle"]],
                [
                    "ref" => Str::uuid(),
                    "description" => $item["description"],
                    "created_by" => $adminUser->id,
                    "updated_by" => $adminUser->id
                ]
            );

        }
    }
}
