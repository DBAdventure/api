<?php

namespace Dba\GameBundle\Controller;

use Dba\GameBundle\Entity\MapBox;
use Dba\GameBundle\Repository\MapRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/map")
 */
class MapController extends BaseController
{
    /**
     * @Route("/refresh/menu", name="map.refresh.menu", methods="GET")
     * @Template()
     */
    public function refreshMenuAction()
    {
        return $this->jsonContent(
            [
                'player' => 'DbaGameBundle::menu/player.html.twig',
                'movement' => 'DbaGameBundle::menu/movement.html.twig'
            ]
        );
    }

    /**
     * @Route("/mini.png", name="map.mini.png", methods="GET")
     * @Template()
     */
    public function renderMinimapAction()
    {
        // @TODO Minimap: Check for map in inventory
        $player = $this->getUser();
        $map = $player->getMap();

        $webDirectory = $this->getParameter('kernel.root_dir') . '/../web/';
        $imagePath = $webDirectory . 'bundles/dbagame/images/map/mini/' . $map->getId() . '.png';
        $image = imagecreatefrompng($imagePath);

        $buildings = $this->repos()->getBuildingRepository()->findByMap($map);
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
     * @param Entity $entity Entity to display
     * @param resource $color Color of the ellipse
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
     * @Route("/mini", name="map.mini", methods="GET")
     * @Template()
     */
    public function miniAction()
    {
        // @TODO Minimap: Check for map in inventory
        $player = $this->getUser();
        $map = $player->getMap();
        $buildings = $this->repos()->getBuildingRepository()->findBy(
            [ 'map' => $map ]
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

        return $this->render(
            'DbaGameBundle::map/mini.html.twig',
            [
                'map' => $map,
                'dot' => MapBox::MINIMAP_SIZE,
                'items' => [
                    'buildings' => $buildings,
                    'players' => $guildPlayers,
                ]
            ]
        );
    }

    /**
     * @Route("/{what}", name="map", methods="GET", requirements={"what": "(partial|elements)"},
              defaults={"what": null})
     * @Template()
     */
    public function mapAction($what)
    {
        $mapRepo = $this->repos()->getMapRepository();
        $borders = $mapRepo->findPlayerBorders($this->getUser());

        switch ($what) {
            case 'partial':
                return new JsonResponse(
                    [
                        'content' => $this->render(
                            'DbaGameBundle::map/map.html.twig',
                            [
                                'map' => $this->getMap($mapRepo),
                                'borders' => $borders,
                                'items' => $this->getItems($mapRepo, $borders)
                            ]
                        )->getContent()
                    ]
                );
                break;
            case 'elements':
                return new JsonResponse(
                    [
                        'content' => $this->render(
                            'DbaGameBundle::map/elements.html.twig',
                            [
                                'map' => $this->getMap($mapRepo),
                                'items' => $this->getItems($mapRepo, $borders)
                            ]
                        )->getContent()
                    ]
                );
                break;
        }

        return $this->render(
            'DbaGameBundle::map/index.html.twig',
            [
                'map' => $this->getMap($mapRepo),
                'borders' => $borders,
                'items' => $this->getItems($mapRepo, $borders)
            ]
        );
    }


    /**
     * Get map data
     *
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
     * @param array $borders Limits
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
            $borders,
            $this->getParameter('kernel.root_dir') . '/../web/'
        );
    }
}