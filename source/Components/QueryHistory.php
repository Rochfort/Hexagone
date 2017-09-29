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
     * Log state (keep log or no)
     *
     * @var bool
     */
    private $stateLog = false;

    /**
     * Add data in log
     *
     * @param        $str
     * @param string $values
     * @return bool
     */
    protected function addQuery($str, $values = '')
    {
        if (!$this->stateLog) {
            return false;
        }

        $milliseconds = round(microtime(true) * 1000);
        $log = [
            'time' => $milliseconds,
            'query' => $str,
            'values'=>$values,
        ];

        $this->history[] = $log;

        return true;
    }

    /**
     * Clear array log
     */
    protected function clearLog() {
        $this->history = [];
    }

    /**
     * Изменить состояние лога
     *
     * @param $state
     * @return $this
     */
    public function setLogState($state)
    {
        $this->stateLog = (bool)$state;

        return $this;
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