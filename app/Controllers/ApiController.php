<?php

namespace App\Controllers;

use App\Base\BaseController;
use App\Services\FileUpload;
use App\Services\CSVFileReader;
use App\Services\Validator;
use App\Services\JsonResponse;
use App\Logic\Matching;

/**
 * ApiController
 */
class ApiController extends BaseController
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * upload
     *
     * @return \App\Services\JsonResponse
     */
    public function upload(): JsonResponse
    {
        // Validate Request
        $validate = new Validator();

        if (!$validate->isSuccess()) {
            return new JsonResponse($validate->errors, 400);
        }

        // Upload File
        if ($file = new FileUpload('file')) {
            try {
                // Set File upload Directory
                $file->setUploadDir(ROOT_DIR . 'public/uploads');

                // Process upload
                $filePath = $file->upload();
            } catch (\Throwable $e) {
                return new JsonResponse([$e->getMessage()], 400);
            }
        } else {
            return new JsonResponse(['Fail Upload Failed'], 400);
        }

        $csv = new CSVFileReader($filePath);

        try {
            $data = Matching::get($csv->getContent(), $_POST);
        } catch (\Throwable $e) {
            return new JsonResponse([$e->getMessage()], 400);
        }

        return  new JsonResponse($data);
    }
}
