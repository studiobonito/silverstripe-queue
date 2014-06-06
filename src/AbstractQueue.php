<?php namespace StudioBonito\SilverStripe\Queue;

use Convert;
use DateTime;
use Injector;

abstract class AbstractQueue extends \Object
{
    /**
     * The injector instance.
     *
     * @var \Injector
     */
    protected $injector;

    /**
     * Create a payload string from the given job and data.
     *
     * @param  string $job
     * @param  mixed  $data
     *
     * @return string
     */
    protected function createPayload($job, $data = null)
    {
        return Convert::raw2json(array('job' => $job, 'data' => $data));
    }

    /**
     * Set additional meta on a payload string.
     *
     * @param  string $payload
     * @param  string $key
     * @param  string $value
     *
     * @return string
     */
    protected function setMeta($payload, $key, $value)
    {
        $payload = Convert::json2array($payload);

        $payload[$key] = $value;

        return Convert::array2json($payload);
    }

    /**
     * Calculate the number of seconds with the given delay.
     *
     * @param  \DateTime|int $delay
     *
     * @return int
     */
    protected function getSeconds($delay)
    {
        if ($delay instanceof DateTime) {
            return max(0, $delay->getTimestamp() - $this->getTime());
        } else {
            return intval($delay);
        }
    }

    /**
     * Get the current UNIX timestamp.
     *
     * @codeCoverageIgnore
     *
     * @return int
     */
    public function getTime()
    {
        return time();
    }

    /**
     * Set the injector instance.
     *
     * @param  \Injector $injector
     *
     * @return void
     */
    public function setInjector(Injector $injector)
    {
        $this->injector = $injector;
    }
}
