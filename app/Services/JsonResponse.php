<?php

namespace App\Services;

/**
 * JsonResponse class for special case
 */
class JsonResponse
{
    /**
     * status
     *
     * @var int
     */
    private $status;

    /**
     * data
     *
     * @var array
     */
    private $data;

    /**
     * __construct
     *
     * @param  array $data
     * @param  int $status
     */
    public function __construct(array $data = array(), int $status = 200)
    {
        $this->data = $data;
        $this->status = $status;
        $this->response();
    }

    /**
     * response
     *
     * @return void
     */
    public function response(): void
    {
        //set the response header
        http_response_code($this->status);
        header('Content-Type: application/json');

        echo json_encode(['data' => $this->data]);
        die();
    }
}
