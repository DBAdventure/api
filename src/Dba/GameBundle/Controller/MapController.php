<?php

namespace Dba\GameBundle\Controller;

use FOS\RestBundle\Controller\Annotations;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Dba\GameBundle\Entity\MapBox;
use Dba\GameBundle\Entity\Object;
use Dba\GameBundle\Repository\MapRepository;

class MapController extends BaseController
{
    protected function checkForMiniMap()
    {
        $playerObject = $this->repos()->getPlayerObjectRepository()->findOneBy(
            [
                'player' => $this->getUser(),
                'object' => $this->repos()->getObjectRepository()->findOneBy(
                    [
                        'id' => Object::DEFAULT_MAP,
                        'enabled' => true,
                    ]
                ),
            ]
        );
        return !empty($playerObject) && $playerObject->getNumber() == 1;
    }

    /**
     * @Annotations\Get("/mini.png")
     */
    public function renderMinimapAction()
    {
        if (!$this->checkForMinimap()) {
            return $this->forbidden();
        }

        $player = $this->getUser();
        $map = $player->getMap();
        $webDirectory = $this->getParameter('kernel.root_dir') . '/../web/';
        $imagePath = $webDirectory . 'media/map/mini/' . $map->getId() . '.png';
        $image = imagecreatefrompng($imagePath);

        $buildings = $this->repos()->getBuildingRepository()->findBy(
            ['map' => $map, 'enabled' => true]
        );
        foreach ($buildings as $building) {
            $this->drawEllipse($image, $building, imagecolorallocate($image, 0, 0, 255));
        }

        if (!empty($player->getGuildPlayer())) {
            $guildPlayers = $this->repos()->getGuildRepository()->findOneById(
                $player->getGuildPlayer()->getGuild()->getId()
            );
            foreach ($guildPlayers->getPlayers() as $guildPlayer) {
                $guildPlayer = $guildPlayer->getPlayer();
                if ($guildPlayer->getMap()->getId() != $map->getId()) {
                    continue;
                }

                $this->drawEllipse($image, $guildPlayer, imagecolorallocate($image, 255, 0, 255));
            }
        }

        $this->drawEllipse($image, $player, imagecolorallocate($image, 255, 0, 0));

        ob_start();
        imagegif($image);
        imagedestroy($image);
        $content = ob_get_clean();

        return new Response(
            $content,
            200,
            [
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'inline; filename="image-' . $player->getId() . '.png"'
            ]
        );
    }

    /**
     * Draw Ellipse for mini map
     *
     * @param resource $originalImage Original image
     * @param Entity   $entity        Entity to display
     * @param resource $color         Color of the ellipse
     *
     * @return void
     */
    protected function drawEllipse($originalImage, $entity, $color)
    {
        imagefilledellipse(
            $originalImage,
            ($entity->getX() * MapBox::MINIMAP_SIZE) - (MapBox::MINIMAP_SIZE / 2),
            ($entity->getY() * MapBox::MINIMAP_SIZE) - (MapBox::MINIMAP_SIZE / 2),
            MapBox::MINIMAP_SIZE,
            MapBox::MINIMAP_SIZE,
            $color
        );
    }

    /**
     * Mini map
     *
     * @Annotations\Get("/mini")
     * @return array
     */
    public function miniAction()
    {
        if (!$this->checkForMinimap()) {
            return $this->forbidden();
        }

        $player = $this->getUser();
        $map = $player->getMap();
        $buildings = $this->repos()->getBuildingRepository()->findBy(
            ['map' => $map, 'enabled' => true]
        );

        $guildPlayers = [];
        if (!empty($player->getGuildPlayer())) {
            $guildPlayers = $this->repos()->getGuildRepository()->findOneById(
                $player->getGuildPlayer()->getGuild()->getId()
            );
            foreach ($guildPlayers->getPlayers() as $guildPlayer) {
                $guildPlayer = $guildPlayer->getPlayer();
                if ($guildPlayer->getMap()->getId() != $map->getId()) {
                    continue;
                }
            }
        }

        return [
            'map' => $map,
            'dot' => MapBox::MINIMAP_SIZE,
            'items' => [
                'buildings' => $buildings,
                'players' => $guildPlayers,
            ]
        ];
    }

    /**
     * Get map
     *
     * @Annotations\Get("")
     * @return array
     */
    public function getAction()
    {
        $mapRepo = $this->repos()->getMapRepository();
        $borders = $mapRepo->findPlayerBorders($this->getUser());

        return [
            'map' => $this->getMap($mapRepo),
            'borders' => $borders,
            'items' => $this->getItems($mapRepo, $borders)
        ];
    }

    /**
     * Get map data
     *
     * @Annotations\Get("/player")
     * @param MapRepository $mapRepo Map repository
     *
     * @return array
     */
    protected function getMap(MapRepository $mapRepo)
    {
        return $mapRepo->generate(
            $this->getUser(),
            $this->services()->getTemplateService()->getPeriod()
        );
    }

    /**
     * Get items on the map
     *
     * @param MapRepository $mapRepo Map repository
     * @param array         $borders Limits
     *
     * @return array
     */
    protected function getItems(MapRepository $mapRepo, array $borders)
    {
        return $mapRepo->findItems(
            [
                'buildings' => $this->repos()->getBuildingRepository(),
                'players' => $this->repos()->getPlayerRepository(),
                'objects' => $this->repos()->getMapObjectRepository(),
            ],
            $this->getUser(),
            $borders
        );
    }
}
