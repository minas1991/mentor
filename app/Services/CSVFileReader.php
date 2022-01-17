<?php

namespace App\Services;

/**
 * CSVFileReader class for special case
 */
class CSVFileReader
{
    /**
     * handle
     *
     * @var resource
     */
    private $handle;

    /**
     * content
     *
     * @var array
     */
    private $content = array();

    /**
     * header
     *
     * @var array
     */
    private $header = array(
        'name', 'email', 'division', 'age', 'timezone'
    );

    /**
     * __construct
     *
     * @param  string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
        $this->handle = fopen($this->path, 'r');
    }

    /**
     * getContent
     *
     * @return array
     */
    public function getContent(): array
    {
        // Collect CSV data and fill content array
        while (($data = fgetcsv($this->handle)) !== FALSE) {
            // Check count of columns
            $length = count($data);
            if ($length != count($this->header)) {
                throw new \Exception('Incorrect CSV fields');
            }

            $tmp = array();
            // Fill assoc array by header key and column values
            for ($i = 0; $i < $length; $i++) {
                $key = $this->header[$i];
                $value = trim($data[$i]);

                // Check CSV has header
                if ($key === strtolower($value)) {
                    continue 2;
                }

                $tmp[$key] = $value;
            }

            if ($tmp) $this->content[] = $tmp;
        }

        return $this->content;
    }

    /**
     * __destruct
     */
    public function __destruct()
    {
        // Close file
        if ($this->handle) {
            fclose($this->handle);
        }
    }
}
