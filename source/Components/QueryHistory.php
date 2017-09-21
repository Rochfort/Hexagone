<?php
/**
 * Created by PhpStorm.
 * User: rochfort
 * Date: 24.05.17
 * Time: 17:38
 */

namespace Hexagone\Components;

/**
 * Trait QueryHistory
 *
 * Logging queries and velues for prepare statements. For debug.
 *
 * @package Components
 */
trait QueryHistory
{
    /**
     * Time line
     *
     * @var array
     */
    private $history = [];

    /**
     * Add data in log
     *
     * @param        $str
     * @param string $values
     */
    protected function addQuery($str, $values = '')
    {
        $milliseconds = round(microtime(true) * 1000);
        $log = [
            'time' => $milliseconds,
            'query' => $str,
            'values'=>$values,
        ];

         $this->history[] = $log;
    }

    /**
     * Clear array log
     */
    protected function clearLog() {
        $this->history = [];
    }

    /**
     * return log
     *
     * @param bool $limit
     * @return array
     */
    public function getLog($limit = false) {
        $result = $this->history;
        if (false !== $limit) {
            if ($limit < 1) {
                $limit = $limit * -1;
            }
            $result = array_slice($result, $limit);
        }

        return $result;
    }
}