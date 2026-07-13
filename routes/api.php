<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\NewsletterSubscriberController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get("/user", function (Request $request) {
    return $request->user();
})->middleware("auth:sanctum");

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/verify-email', [VerificationController::class, 'verify']);
    Route::post('/resend-activation', [VerificationController::class, 'resend']);

    /// - Routes pour l'authentification par google
    Route::post('/google', [AuthController::class, 'googleLogin']);

    /// - Routes pour la réinitialisation de mot de passe
    Route::post('/forgot-password', [PasswordResetController::class, 'sendOtp']);
    Route::post('/verify-otp', [PasswordResetController::class, 'verifyOtp']);
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);
});

Route::middleware('auth:api')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
});

Route::prefix('v1')->group(function () {

    /// - Non authentifié

    Route::get('posts/trending', [PostController::class, 'trending'])->name('posts.trending');
    Route::get('posts/recommended', [PostController::class, 'recommended'])->name('posts.recommended');
    Route::get('tags/popular', [TagController::class, 'popular'])->name('tags.popular');

    /// - Categories
    Route::apiResource('categories', CategoryController::class)
        ->only(['index', 'show'])
        ->parameters(['categories' => 'category:ref']);
    
    /// - Tag
    Route::apiResource('tags', TagController::class)
        ->only(['index', 'show'])
        ->parameters(['tags' => 'tag:ref']);
    
    /// ─ Post 
    Route::apiResource('posts', PostController::class)
        ->only(['index', 'show'])
        ->parameters(['posts' => 'post:ref']);

    Route::middleware(['auth:api', 'permission:post.update'])
    ->get('posts/{post:ref}/edit', [PostController::class, 'edit'])
    ->name('posts.edit');

    /// ─ Commentaires
    Route::get('posts/{post:ref}/comments', [CommentController::class, 'byPost'])
        ->name('comments.byPost');

    // Poster un commentaire
    Route::post('posts/{post:ref}/comments', [CommentController::class, 'store'])
        ->name('comments.store');
    
    /// - Newsletters
    Route::post('newsletter/subscribe', [NewsletterSubscriberController::class, 'subscribe'])
        ->name('newsletter.subscribe');

    Route::get(
        'newsletter/{token}/unsubscribe',
        [NewsletterSubscriberController::class, 'unsubscribe']
    )->name('newsletter.unsubscribe');

    /* Route::get('newsletter/{token}/confirm', [NewsletterSubscriberController::class, 'confirm'])
        ->name('newsletter.subscribe'); */


    /// - Authentifié
    Route::middleware(['auth:api'])->group(function () {   
        
        /// -  Categories
        Route::middleware('permission:category.create')
            ->post('categories', [CategoryController::class, 'store'])
            ->name('categories.store');

        Route::middleware('permission:category.update')
            ->put('categories/{category:ref}', [CategoryController::class, 'update'])
            ->name('categories.update');

        Route::middleware('permission:category.update')
            ->patch('categories/{category:ref}', [CategoryController::class, 'update'])
            ->name('categories.update');

        Route::middleware('permission:category.delete')
            ->delete('categories/{category:ref}', [CategoryController::class, 'destroy'])
            ->name('categories.destroy');

        /// -  Tag
        Route::middleware('permission:tag.create')
            ->post('tags', [TagController::class, 'store'])
            ->name('tags.store');

        Route::middleware('permission:tag.update')
            ->put('tags/{tag:ref}', [TagController::class, 'update'])
            ->name('tags.update');

        Route::middleware('permission:tag.update')
            ->patch('tags/{tag:ref}', [TagController::class, 'update'])
            ->name('tags.update');

        Route::middleware('permission:tag.delete')
            ->delete('tags/{tag:ref}', [TagController::class, 'destroy'])
            ->name('tags.destroy');

        /// - Post
        Route::middleware(['auth:api', 'permission:post.create'])
            ->post('posts', [PostController::class, 'store'])
            ->name('posts.store');

        Route::middleware(['auth:api', 'permission:post.update'])
            ->put('posts/{post:ref}', [PostController::class, 'update'])
            ->name('posts.update');

        Route::middleware(['auth:api', 'permission:post.update'])
            ->patch('posts/{post:ref}', [PostController::class, 'update'])
            ->name('posts.update');

        Route::middleware(['auth:api', 'permission:post.publish'])
            ->patch('posts/{post:ref}/publish', [PostController::class, 'publish'])
            ->name('posts.publish');

        Route::middleware(['auth:api', 'permission:post.reject'])
            ->patch('posts/{post:ref}/reject', [PostController::class, 'reject'])
            ->name('posts.reject');

        Route::middleware(['auth:api', 'permission:post.delete'])
            ->delete('posts/{post:ref}', [PostController::class, 'destroy'])
            ->name('posts.destroy');

        /// - Commentaires
        Route::get('comments', [CommentController::class, 'index'])->name('comments.index');
        Route::get('comments/stats', [CommentController::class, 'stats'])->name('comments.stats');

        Route::middleware(['auth:api', 'permission:comment.moderate'])
            ->patch('comments/{comment:ref}/moderate', [CommentController::class, 'moderate'])
            ->name('comments.moderate');
            
        Route::middleware(['auth:api', 'permission:comment.delete'])
            ->delete('comments/{comment:ref}', [CommentController::class, 'destroy'])
            ->name('comments.destroy');

        /// - Newsletter Subsrciber
        Route::middleware(['auth:api', 'permission:newsletter.read'])
        ->get('newsletter', [NewsletterSubscriberController::class, 'index'])
        ->name('newsletter.index');

        Route::middleware(['auth:api', 'permission:newsletter.delete'])
            ->delete('newsletter/{subscriber:ref}', [NewsletterSubscriberController::class, 'destroy'])
            ->name('newsletter.destroy');


        /// - Utilisateurs
        Route::get('users/stats', [UserController::class, 'stats'])
            ->name('users.stats');

        Route::middleware('permission:user.read')
            ->get('users', [UserController::class, 'index'])
            ->name('users.index');

        Route::middleware('permission:user.read')
            ->get('users/{user:ref}', [UserController::class, 'show'])
            ->name('users.show');

        Route::middleware('permission:user.create')
            ->post('users', [UserController::class, 'store'])
            ->name('users.store');

        Route::middleware('permission:user.update')
            ->put('users/{user:ref}', [UserController::class, 'update'])
            ->name('users.update');

        // Toggle actif/inactif
        Route::middleware('permission:user.update')
            ->patch('users/{user:ref}/toggle-active', [UserController::class, 'toggleActive'])
            ->name('users.toggle-active');

        Route::middleware('permission:user.delete')
            ->delete('users/{user:ref}', [UserController::class, 'destroy'])
            ->name('users.destroy');


        /// - Tous les rôles sans pagination (pour les selects)
        Route::get('roles/all', [RoleController::class, 'all'])->name('roles.all');

        /// - Toutes les habilitations disponibles
        Route::get('roles/habilitations', [RoleController::class, 'habilitations'])->name('roles.habilitations');

        Route::middleware('permission:role.read')
            ->get('roles', [RoleController::class, 'index'])->name('roles.index');

        Route::middleware('permission:role.create')
            ->post('roles', [RoleController::class, 'store'])->name('roles.store');

        Route::middleware('permission:role.update')
            ->put('roles/{role:ref}', [RoleController::class, 'update'])->name('roles.update');

        Route::middleware('permission:role.delete')
            ->delete('roles/{role:ref}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });
});



Route::prefix('v1')->group(function () {

    

    // ── Admin ──────────────────────────────────────────────
    Route::middleware('auth:api')->group(function () {

        // Liste + stats
        

        // Modération
        
    });
});