<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Upload extends BaseConfig
{
    /**
     * Default upload directory
     */
    public $uploadPath = WRITEPATH . 'uploads/';

    /**
     * Allowed file types
     */
    public $allowedTypes = 'pdf|doc|docx|txt|jpg|jpeg|png|mp4|avi';

    /**
     * Maximum file size in kilobytes
     */
    public $maxSize = 10240; // 10MB

    /**
     * Maximum filename length
     */
    public $maxFilename = 255;

    /**
     * Whether to overwrite existing files
     */
    public $overwrite = false;

    /**
     * Whether to encrypt filenames
     */
    public $encryptName = true;

    /**
     * Whether to remove spaces from filenames
     */
    public $removeSpaces = true;
}
