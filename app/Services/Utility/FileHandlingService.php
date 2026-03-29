<?php

namespace App\Services\Utility;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FileHandlingService
{
    public const PRODUCT_IMAGE_DIRECTORY = 'upload/products';
    public const DOCUMENT_DIRECTORY = 'upload/documents';
    public const CATEGORY_IMAGE_DIRECTORY = 'upload/categories';
    public const CAROUSEL_IMAGE_DIRECTORY = 'upload/corousel';
    public const ICON_DIRECTORY = 'upload/icons';

    // This stores one uploaded file under a chosen public folder and returns the saved relative path.
    public function storeUploadedFile(UploadedFile $uploadedFile, string $relativeDirectory, ?string $preferredFileName = null): string
    {
        // Step 1: normalize the target folder so every upload path stays web-accessible and predictable.
        $normalizedDirectory = trim(str_replace('\\', '/', $relativeDirectory), '/');
        $absoluteDirectory = public_path($normalizedDirectory);

        // Step 2: create the folder on demand so the upload flow does not depend on any manual directory setup.
        if (! is_dir($absoluteDirectory)) {
            mkdir($absoluteDirectory, 0777, true);
        }

        $extension = $uploadedFile->getClientOriginalExtension();
        $sanitizedFileName = $preferredFileName !== null
            ? $this->sanitizeFileName($preferredFileName).($extension !== '' ? '.'.$extension : '')
            : Str::uuid()->toString().($extension !== '' ? '.'.$extension : '');

        // Step 3: move the uploaded file into the final public folder so the browser can serve it directly.
        $uploadedFile->move($absoluteDirectory, $sanitizedFileName);

        return $normalizedDirectory.'/'.$sanitizedFileName;
    }

    // This writes a plain file into a public folder and returns the saved relative path.
    public function writePublicFile(string $relativePath, string $contents): string
    {
        $normalizedRelativePath = trim(str_replace('\\', '/', $relativePath), '/');
        $absoluteFilePath = public_path($normalizedRelativePath);
        $absoluteDirectory = dirname($absoluteFilePath);

        // Step 1: create the target folder before writing the generated file.
        if (! is_dir($absoluteDirectory)) {
            mkdir($absoluteDirectory, 0777, true);
        }

        // Step 2: write the file into the public upload area so demo downloads and generated assets stay reachable.
        file_put_contents($absoluteFilePath, $contents);

        return $normalizedRelativePath;
    }

    // This checks whether a saved public file is still available on disk.
    public function fileExists(string $relativePath): bool
    {
        return is_file($this->absolutePath($relativePath));
    }

    // This returns the public absolute path for one saved file.
    public function absolutePath(string $relativePath): string
    {
        return public_path(trim(str_replace('\\', '/', $relativePath), '/'));
    }

    // This returns the file size for one saved public file.
    public function fileSize(string $relativePath): int
    {
        $absoluteFilePath = $this->absolutePath($relativePath);

        return is_file($absoluteFilePath) ? (int) filesize($absoluteFilePath) : 0;
    }

    // This returns a browser download response for one saved public file.
    public function downloadPublicFile(string $relativePath, ?string $downloadName = null): BinaryFileResponse
    {
        $absoluteFilePath = $this->absolutePath($relativePath);

        // Step 1: stop the download when the saved file no longer exists in the configured public folder.
        if (! is_file($absoluteFilePath)) {
            throw new NotFoundHttpException('Requested file is not available.');
        }

        // Step 2: return the stored file using the business filename when one is provided.
        return response()->download(
            $absoluteFilePath,
            $downloadName ?: basename($absoluteFilePath),
        );
    }

    // This keeps uploaded filenames readable and safe for public URLs.
    protected function sanitizeFileName(string $fileName): string
    {
        $cleanName = Str::slug(pathinfo($fileName, PATHINFO_FILENAME));

        return $cleanName !== '' ? $cleanName : Str::uuid()->toString();
    }
}
