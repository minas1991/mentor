<?php

namespace App\Logic;

/**
 * Matching
 */
class Matching
{
    /**
     * @property static array $list
     */
    private static $list;

    /**
     * @property static array $conditions
     */
    private static $conditions;

    /**
     * @property static int $count
     */
    private static $count;

    /**
     * @property static array $combinations
     */
    private static $combinations;

    /**
     * @property static array $maxValue
     */
    private static $maxValue;

    /**
     * @property static array $skips
     */
    private static $skips;

    /**
     * get
     *
     * @param  array $data
     * @param  array $conditions
     * @return array
     */
    public static function get(array $data, array $conditions = array()): array
    {
        // Define array length
        self::$count = count($data);

        // Check data and conditions are not empty and possible to get pairs
        if (empty($conditions) || self::$count < 2) {
            return [];
        }

        self::$conditions = $conditions;

        // Fill pairs matrix
        for ($i = 1; $i < self::$count; $i++) {
            self::seperatePairing($i - 1, $i, $data);
        }

        // List the unique pair combinations by each row max value
        foreach (self::$list as $key => $each) {
            self::$skips = array();
            $max = self::getRowMaxScore($each['pairs']);
            self::$skips = [$key, $max['id']];

            self::$combinations[$key]['pairs'][] = $max;
            self::$combinations[$key]['total_score'] = $max['score'];

            // Get unique pair combinations for each case
            self::getScores(self::$list, self::$skips, $key);

            // Keep highest score
            self::keepHighestScore($key);
        }

        return self::$combinations[self::$maxValue['key']]['pairs'] ?? [];
    }

    /**
     * keepHighestScore
     *
     * @param  string $key
     * @return void
     */
    private static function keepHighestScore(string $key): void
    {
        if (
            !isset(self::$maxValue) ||
            self::$maxValue['score'] < self::$combinations[$key]['total_score']
        ) {
            self::$maxValue = array(
                'key' => $key,
                'score' => self::$combinations[$key]['total_score']
            );
        }
    }


    /**
     * seperatePairing
     *
     * @param  int $index
     * @param  int $offset
     * @param  array $data
     * @return void
     */
    private static function seperatePairing(int $index, int $offset, array $data): void
    {
        // Use email field as a unique key
        $key = $data[$index]['email'];

        for ($i = $offset; $i < self::$count; $i++) {
            // Get pair score
            $score = self::getMatchScore($data[$index], $data[$i]);

            $pair = array(
                'id' => $data[$i]['email'],
                'name1' => $data[$index]['name'],
                'name2' => $data[$i]['name'],
                'score' => $score
            );

            // Fill pair in the list
            self::$list[$key]['pairs'][$data[$i]['email']] = $pair;
        }
    }

    /**
     * getMatchScore
     *
     * @param  array $employee_one
     * @param  array $employee_two
     * @return int
     */
    private static function getMatchScore(array $employee_one, array $employee_two): int
    {
        $score = 0;

        foreach (self::$conditions as $key => $condition) {
            if (!isset($employee_one[$key]) || !isset($employee_two[$key])) {
                continue;
            }

            // Scip 0 score value
            if (!(int) $condition['score']) {
                continue;
            }

            // If range condition equal
            if (empty($condition['range']) || $condition['range'] === '=') {
                if ($employee_one[$key] === $employee_two[$key]) {
                    $score += (int) $condition['score'];
                }
            } else {
                // Check if range condition true for pairs
                if (abs($employee_one[$key] - $employee_two[$key]) <= (int)$condition['range']) {
                    $score += (int) $condition['score'];
                }
            }
        }

        return $score;
    }

    /**
     * getRowMaxScore
     *
     * @param  array $each
     * @return array
     */
    private static function getRowMaxScore(array $each): array
    {
        $pair = [];
        foreach ($each as $key => $row) {
            // Skip already filled pairs
            if (in_array($key, self::$skips)) {
                continue;
            }

            if (empty($pair) || $row['score'] >= $pair['score']) {
                $pair = $row;
            }
        }

        return $pair;
    }


    /**
     * getScores
     *
     * @param  array $rest
     * @param  array $rowKeys
     * @param  string $combinationsKey
     * @return void
     */
    private static function getScores(array $rest, array $rowKeys, string $combinationsKey): void
    {
        // Remove rows already filles
        unset($rest[$rowKeys[0]]);
        unset($rest[$rowKeys[1]]);

        foreach ($rest as $key => $each) {
            // Get Row max score
            if ($max = self::getRowMaxScore($each['pairs'])) {
                self::$skips[] = $key;
                self::$skips[] = $max['id'];

                if ($max['score']) {
                    self::$combinations[$combinationsKey]['pairs'][] = $max;
                    self::$combinations[$combinationsKey]['total_score'] += $max['score'];
                }

                // Repeat from currennt row
                self::getScores($rest, [$key, $max['id']], $combinationsKey);
            }
        }
    }
}
