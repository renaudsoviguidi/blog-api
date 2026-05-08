<?php

namespace Database\Seeders;

use App\Models\Habilitation;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HabilitationsRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $admin = Role::where("libelle", "ADMIN")->first();
        $editor = Role::where("libelle", "EDITOR")->first();
        $user = Role::where("libelle", "USER")->first();

        // ADMIN → toutes les permissions
        $admin->habilitations()->sync(
            Habilitation::pluck('id')->toArray()
        );

        // EDITOR
        $editor->habilitations()->sync(
            Habilitation::whereIn('slug', [
                'post.read',
                'post.update',
                'post.publish',
                'post.reject',
            ])->pluck('id')->toArray()
        );

        // USER
        $user->habilitations()->sync(
            Habilitation::whereIn('slug', [
                'post.read',
                'post.create',
                'post.update',
            ])->pluck('id')->toArray()
        );
    }
}
