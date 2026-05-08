<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class MediaService
{
    /**
     * Configuration par type de média
     */
    private const ALLOWED_TYPES = [
        'image' => [
            'extensions' => ['png', 'jpeg', 'jpg', 'svg', 'webp'],
            'max_size'   => 5 * 1024,  // 5 Mo en Ko
            'disk'       => 'public',
        ],
        'video' => [
            'extensions' => ['mp4', 'avi'],
            'max_size'   => 50 * 1024, // 50 Mo en Ko
            'disk'       => 'public',
        ],
        'document' => [
            'extensions' => ['pdf', 'docx'],
            'max_size'   => 10 * 1024, // 10 Mo en Ko
            'disk'       => 'public',
        ],
    ];

    /**
     * Stocker un fichier avec validation et nommage formaté
     *
     * @param  UploadedFile $file Le fichier uploadé
     * @param  string $folder - Dossier de destination
     * @param  string $prefix - Préfixe du nom
     * @param  string $type - Type : "image" | "video" | "document"
     */
    public function store(
        UploadedFile $file,
        string $folder,
        string $prefix,
        string $type = 'image'
    ): string {
        $this->validate($file, $type);

        $filename = $this->buildFilename($file, $prefix);
        $path = $folder . '/' . $filename;

        $disk = self::ALLOWED_TYPES[$type]['disk'];

        Storage::disk($disk)->putFileAs($folder, $file, $filename);

        return $path;
    }

    /**
     * Remplacer un fichier existant
     */
    public function replace(
        UploadedFile $newFile,
        ?string $oldPath,
        string $folder,
        string $prefix,
        string $type = 'image'
    ): string {
        $this->delete($oldPath, $type);
        return $this->store($newFile, $folder, $prefix, $type);
    }

    /**
     * Supprimer un fichier
     */
    public function delete(?string $path, string $type = 'image'): void
    {
        if (!$path) {
            return;
        }

        $disk = self::ALLOWED_TYPES[$type]['disk'] ?? 'public';

        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }

    /**
     * Retourner l'URL publique d'un fichier
     */
    public function url(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return asset('storage/' . $path);
    }

    /// - Méthodes privées

    /**
     * Valider l'extension et la taille du fichier
     */
    private function validate(UploadedFile $file, string $type): void
    {
        if (!array_key_exists($type, self::ALLOWED_TYPES)) {
            throw new InvalidArgumentException(
                "Type de média non supporté : [{$type}]. Types autorisés : " .
                implode(', ', array_keys(self::ALLOWED_TYPES))
            );
        }

        $config = self::ALLOWED_TYPES[$type];
        $extension = strtolower($file->getClientOriginalExtension());
        $sizeInKo = $file->getSize() / 1024;
        $maxMo = $config['max_size'] / 1024;

        if (!in_array($extension, $config['extensions'])) {
            throw new InvalidArgumentException(
                "Extension [{$extension}] non autorisée pour le type [{$type}]. " .
                "Extensions acceptées : " . implode(', ', $config['extensions'])
            );
        }

        if ($sizeInKo > $config['max_size']) {
            throw new InvalidArgumentException(
                "Le fichier dépasse la taille maximale autorisée de {$maxMo} Mo " .
                "pour le type [{$type}]."
            );
        }
    }

    /**
     * Construire le nom du fichier formaté
     *
     * Format : {prefix}_{datetime}_{uuid_court}.{extension}
     */
    private function buildFilename(UploadedFile $file, string $prefix): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $slug = Str::slug($prefix);
        $datetime = now()->format('Y-m-d_H-i-s');
        $unique = Str::random(8);

        return "{$slug}_{$datetime}_{$unique}.{$extension}";
    }
}