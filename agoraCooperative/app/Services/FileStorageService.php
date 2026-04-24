<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileStorageService
{
    /**
     * Upload une photo de profil membre
     * 
     * @param UploadedFile $file
     * @param string $codeMembre
     * @return string Le chemin du fichier stocké
     */
    public function uploadPhoto(UploadedFile $file, string $codeMembre): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = $codeMembre . '_' . time() . '.' . $extension;
        
        $path = $file->storeAs(
            $codeMembre,
            $filename,
            'photos_membres'
        );
        
        return $path;
    }
    
    /**
     * Supprimer une photo de profil
     * 
     * @param string $path
     * @return bool
     */
    public function deletePhoto(string $path): bool
    {
        if (Storage::disk('photos_membres')->exists($path)) {
            return Storage::disk('photos_membres')->delete($path);
        }
        
        return false;
    }
    
    /**
     * Upload un document ressource
     * 
     * @param UploadedFile $file
     * @param string $categorie
     * @return array ['path' => string, 'nom_fichier' => string, 'extension' => string]
     */
    public function uploadRessource(UploadedFile $file, string $categorie): array
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) 
            . '_' . time() 
            . '.' . $extension;
        
        $path = $file->storeAs(
            $categorie,
            $filename,
            'ressources'
        );
        
        return [
            'path' => $path,
            'nom_fichier' => $originalName,
            'extension' => $extension,
        ];
    }
    
    /**
     * Supprimer un document ressource
     * 
     * @param string $path
     * @return bool
     */
    public function deleteRessource(string $path): bool
    {
        if (Storage::disk('ressources')->exists($path)) {
            return Storage::disk('ressources')->delete($path);
        }
        
        return false;
    }
    
    /**
     * Générer une URL signée temporaire pour téléchargement
     * 
     * @param string $path
     * @param string $disk
     * @param int $minutes
     * @return string
     */
    public function generateTemporaryUrl(string $path, string $disk = 'ressources', int $minutes = 5): string
    {
        return Storage::disk($disk)->temporaryUrl($path, now()->addMinutes($minutes));
    }
    
    /**
     * Obtenir le chemin complet d'un fichier
     * 
     * @param string $path
     * @param string $disk
     * @return string
     */
    public function getFullPath(string $path, string $disk = 'ressources'): string
    {
        return Storage::disk($disk)->path($path);
    }
}
