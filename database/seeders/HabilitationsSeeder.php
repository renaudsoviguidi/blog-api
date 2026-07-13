<?php

namespace Database\Seeders;

use App\Models\Habilitation;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HabilitationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $adminUser = User::where("email", "devblogapi@yopmail.com")->first();

        $data_habilitations = [

            // POSTS
            ["libelle" => "Read Post", "slug" => "post.read", "description" => "Voir la liste des posts"],
            ["libelle" => "Create Post", "slug" => "post.create", "description" => "Créer un post"],
            ["libelle" => "Update Post", "slug" => "post.update", "description" => "Modifier un post"],
            ["libelle" => "Delete Post", "slug" => "post.delete", "description" => "Supprimer un post"],
            ["libelle" => "Publish Post", "slug" => "post.publish", "description" => "Publier un post"],
            ["libelle" => "Reject Post", "slug" => "post.reject", "description" => "Refuser un post"],
            ["libelle" => "Restore Post", "slug" => "post.restore", "description" => "Restaurer un post supprimé"],
            ["libelle" => "Force Delete Post", "slug" => "post.force_delete", "description" => "Supprimer définitivement un post"],

            // CATEGORIES
            ["libelle" => "Read Categories", "slug" => "category.read", "description" => "Voir les catégories"],
            ["libelle" => "Create Category", "slug" => "category.create", "description" => "Créer une catégorie"],
            ["libelle" => "Update Category", "slug" => "category.update", "description" => "Modifier une catégorie"],
            ["libelle" => "Delete Category", "slug" => "category.delete", "description" => "Supprimer une catégorie"],

            // TAGS
            ["libelle" => "Read Tag", "slug" => "tag.read", "description" => "Voir les tags"],
            ["libelle" => "Create Tag", "slug" => "tag.create", "description" => "Créer un tag"],
            ["libelle" => "Update Tag", "slug" => "tag.update", "description" => "Modifier un tag"],
            ["libelle" => "Delete Tag", "slug" => "tag.delete", "description" => "Supprimer un tag"],

            // COMMENTS
            ["libelle" => "Read Comment", "slug" => "comment.read", "description" => "Lire les commentaires"],
            ["libelle" => "Create Comment", "slug" => "comment.create", "description" => "Créer un commentaire"],
            ["libelle" => "Update Comment", "slug" => "comment.update", "description" => "Modifier un commentaire"],
            ["libelle" => "Delete Comment", "slug" => "comment.delete", "description" => "Supprimer un commentaire"],
            ["libelle" => "Moderate Comment", "slug" => "comment.moderate", "description" => "Modérer les commentaires"],

            // USERS
            ["libelle" => "Read Users", "slug" => "user.read", "description" => "Voir les utilisateurs"],
            ["libelle" => "Create User", "slug" => "user.create", "description" => "Créer un utilisateur"],
            ["libelle" => "Update User", "slug" => "user.update", "description" => "Modifier un utilisateur"],
            ["libelle" => "Delete User", "slug" => "user.delete", "description" => "Supprimer un utilisateur"],

            // ROLES
            ["libelle" => "Read Role", "slug" => "role.read", "description" => "Voir les rôles"],
            ["libelle" => "Create Role", "slug" => "role.create", "description" => "Créer un rôle"],
            ["libelle" => "Update Role", "slug" => "role.update", "description" => "Modifier un rôle"],
            ["libelle" => "Delete Role", "slug" => "role.delete", "description" => "Supprimer un rôle"],
            ["libelle" => "Assign Role", "slug" => "role.assign", "description" => "Attribuer un rôle à un utilisateur"],

            // DASHBOARD
            ["libelle" => "View Dashboard", "slug" => "dashboard.view", "description" => "Accéder au dashboard"],
            ["libelle" => "Update Settings", "slug" => "settings.update", "description" => "Modifier les paramètres du blog"],

            // NEWSLETTERS
            ["libelle" => "Lire Newsletter", "slug" => "newsletter.read", "description" => "Voir les abonnés"],
            ["libelle" => "Delete Newsletter", "slug" => "newsletter.delete", "description" => "Supprimer un abonné"],
        ];

        foreach ($data_habilitations as $item) {

            Habilitation::updateOrCreate(
                ["slug" => $item["slug"]],
                [
                    "ref" => Str::uuid(),
                    "libelle" => $item["libelle"],
                    "description" => $item["description"],
                    "created_by" => $adminUser->id,
                    "updated_by" => $adminUser->id
                ]
            );

        }

    }
}
