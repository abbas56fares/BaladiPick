<?php

namespace App\Helpers;

class DocumentHelper
{
    /**
     * Get the URL to view a document
     * 
     * @param string|null $documentPath
     * @return string|null
     */
    public static function getDocumentUrl(?string $documentPath): ?string
    {
        if (!$documentPath) {
            return null;
        }

        return route('documents.show', ['path' => $documentPath]);
    }
}
