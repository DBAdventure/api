<?php

namespace Dba\GameBundle\Services;

use Dba\GameBundle\Repository;

class RepositoryService extends BaseService
{
    /**
     * @return Repository\PlayerRepository
     */
    public function getPlayerRepository()
    {
        return $this->em()->getRepository('DbaGameBundle:Player');
    }

    /**
     * @return Repository\SideRepository
     */
    public function getSideRepository()
    {
        return $this->em()->getRepository('DbaGameBundle:Side');
    }

    /**
     * @return Repository\RankRepository
     */
    public function getRankRepository()
    {
        return $this->em()->getRepository('DbaGameBundle:Rank');
    }

    /**
     * @return Repository\RaceRepository
     */
    public function getRaceRepository()
    {
        return $this->em()->getRepository('DbaGameBundle:Race');
    }

    /**
     * @return Repository\BankRepository
     */
    public function getBankRepository()
    {
        return $this->em()->getRepository('DbaGameBundle:Bank');
    }

    /**
     * @return Repository\MapRepository
     */
    public function getMapRepository()
    {
        return $this->em()->getRepository('DbaGameBundle:Map');
    }

    /**
     * @return Repository\MapBonusRepository
     */
    public function getMapBonusRepository()
    {
        return $this->em()->getRepository('DbaGameBundle:MapBonus');
    }

    /**
     * @return Repository\MapImageRepository
     */
    public function getMapImageRepository()
    {
        return $this->em()->getRepository('DbaGameBundle:MapImage');
    }

    /**
     * @return Repository\MapImageFileRepository
     */
    public function getMapImageFileRepository()
    {
        return $this->em()->getRepository('DbaGameBundle:MapImageFile');
    }

    /**
     * @return Repository\MapBoxRepository
     */
    public function getMapBoxRepository()
    {
        return $this->em()->getRepository('DbaGameBundle:MapBox');
    }

    /**
     * @return Repository\MapObjectRepository
     */
    public function getMapObjectRepository()
    {
        return $this->em()->getRepository('DbaGameBundle:MapObject');
    }

    /**
     * @return Repository\MapObjectTypeRepository
     */
    public function getMapObjectTypeRepository()
    {
        return $this->em()->getRepository('DbaGameBundle:MapObjectType');
    }

    /**
     * @return Repository\BuildingRepository
     */
    public function getBuildingRepository()
    {
        return $this->em()->getRepository('DbaGameBundle:Building');
    }

    /**
     * @return Repository\ObjectRepository
     */
    public function getObjectRepository()
    {
        return $this->em()->getRepository('DbaGameBundle:Object');
    }

    /**
     * @return Repository\PlayerObjectRepository
     */
    public function getPlayerObjectRepository()
    {
        return $this->em()->getRepository('DbaGameBundle:PlayerObject');
    }

    /**
     * @return Repository\PlayerEventRepository
     */
    public function getPlayerEventRepository()
    {
        return $this->em()->getRepository('DbaGameBundle:PlayerEvent');
    }

    /**
     * @return Repository\GuildRepository
     */
    public function getGuildRepository()
    {
        return $this->em()->getRepository('DbaGameBundle:Guild');
    }

    /**
     * @return Repository\GuildRankRepository
     */
    public function getGuildRankRepository()
    {
        return $this->em()->getRepository('DbaGameBundle:GuildRank');
    }

    /**
     * @return Repository\EventTypeRepository
     */
    public function getEventTypeRepository()
    {
        return $this->em()->getRepository('DbaGameBundle:EventType');
    }

    /**
     * @return Repository\InboxRepository
     */
    public function getInboxRepository()
    {
        return $this->em()->getRepository('DbaGameBundle:Inbox');
    }

    /**
     * @return Repository\NewsRepository
     */
    public function getNewsRepository()
    {
        return $this->em()->getRepository('DbaGameBundle:News');
    }

    /**
     * @return Repository\SpellRepository
     */
    public function getSpellRepository()
    {
        return $this->em()->getRepository('DbaGameBundle:Spell');
    }

    /**
     * @return Repository\PlayerSpellRepository
     */
    public function getPlayerSpellRepository()
    {
        return $this->em()->getRepository('DbaGameBundle:PlayerSpell');
    }
}
