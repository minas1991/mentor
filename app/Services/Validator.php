<?php

namespace App\Services;

/**
 * Validator class for special case
 */
class Validator
{
    /**
     * @var int $maxScore
     */
    private $maxScore;

    /**
     * @var array $errors
     */
    public $errors = array();

    /**
     * @var array $rules
     */
    private $rules = array(
        'total_max_score' => 'Total Max score should not be greater than 100%',
        'score' => 'Score value should not be less than 0',
        'file_type' => 'Accepted file format is CSV',
        'file_required' => 'CSV File upload is required'
    );

    /**
     * __construct
     */
    public function __construct()
    {
        // Validate File
        $this->validateFile($_FILES);

        // Validate Total Score
        $this->validateTotalScore($this->getMaxScore($_POST));
    }

    /**
     * getMaxScore
     *
     * @param  mixed $data
     * @return int
     */
    public function getMaxScore(array $data): int
    {
        $this->maxScore = 0;
        foreach ($data as $each) {
            if (isset($each['score'])) {
                // Validate field Score
                $this->validateScore((int) $each['score']);

                // Fill Max Score
                $this->maxScore += (int) $each['score'];
            }
        }

        return $this->maxScore;
    }

    /**
     * validateFile
     *
     * @param  mixed $files
     * @return void
     */
    public function validateFile(array $files = array()): void
    {
        $file = $files['file'] ?? null;

        // Check if file uploaded
        if (!$file) {
            $this->errors[] = $this->rules['file_required'];
        } else {
            // Get the file extension
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);

            // File Type Validation
            if (empty($extension) || $extension !== 'csv') {
                $this->errors[] = $this->rules['file_type'];
            }
        }
    }

    /**
     * validateScore
     *
     * @param  int $score
     * @return void
     */
    public function validateScore(int $score): void
    {
        if ($score < 0) {
            $this->errors[] = $this->rules['score'];
        }
    }

    /**
     * validateTotalScore
     *
     * @param  int $totalScore
     * @return void
     */
    public function validateTotalScore(int $totalScore): void
    {
        if ($totalScore > 100) {
            $this->errors[] = $this->rules['total_max_score'];
        }
    }

    /**
     * isSuccess
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return empty($this->errors);
    }
}
