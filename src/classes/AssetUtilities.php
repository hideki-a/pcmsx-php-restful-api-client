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
        $base64Data = base64_encode(file_get_contents($filePath));

        if ($imageInfo) {
            $imageType = $imageInfo[2];
            $mimeType = image_type_to_mime_type($imageType);
            return "data:{$mimeType};base64,{$base64Data}";
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $pcmsxMimeTypes = PTUtil::mime_types();
        $mimeType = $pcmsxMimeTypes[$extension] ?? 'text/plain';

        return "data:{$mimeType};base64,{$base64Data}";
    }
}
