<?php

namespace Dba\GameBundle\Services;

class ServicesService extends BaseService
{
    /**
     *
     * @return PlayerService
     */
    public function getPlayerService()
    {
        return $this->container->get('dba.game.player');
    }

    /**
     *
     * @return MailService
     */
    public function getMailService()
    {
        return $this->container->get('dba.game.mail');
    }

    /**
     *
     * @return ObjectService
     */
    public function getObjectService()
    {
        return $this->container->get('dba.game.object');
    }

    /**
     *
     * @return SpellService
     */
    public function getSpellService()
    {
        return $this->container->get('dba.game.spell');
    }

    /**
     *
     * @return TemplateService
     */
    public function getTemplateService()
    {
        return $this->container->get('dba.game.template');
    }
}
