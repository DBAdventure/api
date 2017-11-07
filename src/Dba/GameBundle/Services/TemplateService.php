<?php
namespace Dba\GameBundle\Services;

use DateTime;
use Symfony\Component\HttpFoundation\Request;

class TemplateService extends BaseService
{
    protected $period;
    protected $authenticationError;

    const PERIOD_DAY = 'day';
    const PERIOD_NIGHT = 'night';

    /**
     * Get period
     *
     * @return string
     */
    public function getPeriod()
    {
        if (empty($this->period)) {
            $date = new DateTime();
            $morning = new DateTime('07:30');
            $evening = new DateTime('19:30');
            $this->period = ($date >= $morning and $date <= $evening) ? self::PERIOD_DAY : self::PERIOD_NIGHT;
        }

        return $this->period;
    }

    /**
     * Get last authentication errors
     *
     * @return integer
     */
    public function getLastAuthenticationError()
    {
        if (empty($this->authenticationError)) {
            $authenticationUtils = $this->container->get('security.authentication_utils');
            // get the login error if there is one
            $this->authenticationError = $authenticationUtils->getLastAuthenticationError();
        }

        return $this->authenticationError;
    }
}
