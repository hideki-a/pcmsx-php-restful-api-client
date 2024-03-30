<?php

declare(strict_types=1);

namespace PowerCMSX\RESTfulAPI;

use PTUtil;

class AssetUtilities
{
    /**
     * ファイルのBase64エンコード
     *
     * @param string $filePath ファイルのパス
     * @return string Base64エンコードしたデータ
     */
    public static function encodeBase64(string $filePath): string
    {
        $imageInfo = getimagesize($filePath);
        $data = file_get_contents($filePath);

        if (!$data) {
            exit('Could not open file at specified path.');
        }

        $base64Data = base64_encode($data);

        if ($imageInfo) {
            $imageType = $imageInfo[2];
            $mimeType = image_type_to_mime_type($imageType);
            return "data:{$mimeType};base64,{$base64Data}";
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $pcmsxMimeTypes = PTUtil::mime_types(); /** @phpstan-ignore-line */
        $mimeType = $pcmsxMimeTypes[$extension] ?? 'text/plain';

        return "data:{$mimeType};base64,{$base64Data}";
    }
}
