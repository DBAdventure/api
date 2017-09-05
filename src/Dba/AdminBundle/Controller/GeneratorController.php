<?php

namespace Dba\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Dba\AdminBundle\Controller\BaseController;
use Dba\AdminBundle\Form;
use Dba\GameBundle\Entity\Map;
use Dba\GameBundle\Entity\MapBox;
use Dba\GameBundle\Entity\MapImage;
use Dba\GameBundle\Entity\MapImageFile;

/**
 * @Route("/generator")
 */
class GeneratorController extends BaseController
{
    const MAP_DIVISOR = 30;
    /**
     * Little cache for image loaded with gd lib
     *
     * @var array
     */
    protected $images = [];

    /**
     * @Route("/minimap/{id}", name="admin.generator.minimap", methods={"GET", "POST"},
              defaults={"id": null}, requirements={"id": "\d+"})
     * @ParamConverter("map", class="Dba\GameBundle\Entity\Map", isOptional="true", options={"id" = "id"})
     */
    public function minimapAction(Map $map = null)
    {
        $mapRepo = $this->repos()->getMapRepository();
        $mapData = $mapRepo->generateGeneratorMap($map, null, null);
        $originalImage = imagecreatetruecolor(
            $map->getMaxX() * MapBox::MINIMAP_SIZE,
            $map->getMaxY() * MapBox::MINIMAP_SIZE
        );

        $webDirectory = $this->getParameter('kernel.root_dir') . '/../web/';
        $bundlePath = 'bundles/dbaadmin/images/';
        $imagePath = $webDirectory . $bundlePath;

        foreach ($mapData as $x => $images) {
            foreach ($images as $y => $image) {
                $imageCopy = $this->loadImage($imagePath . $image['image']);
                imagecopyresampled(
                    $originalImage,
                    $imageCopy,
                    ($x * MapBox::MINIMAP_SIZE) - MapBox::MINIMAP_SIZE,
                    ($y * MapBox::MINIMAP_SIZE) - MapBox::MINIMAP_SIZE,
                    0,
                    0,
                    MapBox::MINIMAP_SIZE,
                    MapBox::MINIMAP_SIZE,
                    imagesx($imageCopy),
                    imagesy($imageCopy)
                );
            }
        }

        $imagePath .= '/map/mini/' . $map->getId() . '.png';
        imagepng($originalImage, $imagePath);

        return $this->render(
            'DbaAdminBundle::generator/minimap.html.twig',
            [
                'map' => $map,
                'imagePath' => $bundlePath . '/map/mini/' . $map->getId() . '.png'
            ]
        );
    }

    /**
     * Load image with gd library
     *
     * @param string $imagePath Image path
     *
     * @return resource
     */
    protected function loadImage($imagePath)
    {
        if (empty($this->images[$imagePath])) {
            $this->images[$imagePath] = imagecreatefromPng($imagePath);
        }

        return $this->images[$imagePath];
    }

    /**
     * @Route("/map/{id}", name="admin.generator.map", methods={"GET", "POST"},
              defaults={"id": null}, requirements={"id": "\d+"})
     * @ParamConverter("map", class="Dba\GameBundle\Entity\Map", isOptional="true", options={"id" = "id"})
     */
    public function mapAction(Request $request, Map $map = null)
    {
        $query = $request->query;
        $partX = $query->get('partX', 0);
        $partY = $query->get('partY', 0);
        $minX = 0;
        $maxX = 0;
        $minY = 0;
        $maxY = 0;
        if (!empty($map)) {
            if ($request->isMethod('POST')) {
                $mapImageRepository = $this->repos()->getMapImageRepository();
                $mapBonusRepository = $this->repos()->getMapBonusRepository();
                $mapBoxRepository = $this->repos()->getMapBoxRepository();
                $serializedData = (array) json_decode($request->request->get('serialized-data'), true);
                foreach ($serializedData as $id => $data) {
                    try {
                        list($x, $y) = explode('-', $id);
                        $mapBox = $mapBoxRepository->findOneBy(
                            [
                                'x' => $x,
                                'y' => $y,
                                'map' => $map
                            ]
                        );

                        if (empty($mapBox)) {
                            $mapBox = new MapBox();
                            $mapBox->setMap($map);
                            $mapBox->setX($x);
                            $mapBox->setY($y);
                        }

                        $mapBox->setMapBonus($mapBonusRepository->findOneById($data['bonus_id']));
                        $mapBox->setMapImage($mapImageRepository->findOneById($data['image_id']));
                        $this->em()->persist($mapBox);
                    } catch (Exception $e) {
                        $this->getLogger()->error($e);
                    }
                }
                $this->em()->flush();
                return $this->redirect($this->generateUrl('admin.generator.map', ['id' => $map->getid()]));
            }

            $minX = (self::MAP_DIVISOR * $partX) + 1;
            $minX = $minX < 1 ? 1 : $minX;
            $maxX = ($partX + 1) * self::MAP_DIVISOR;
            $maxX = $maxX > $map->getMaxX() ? $map->getMaxX() : $maxX;
            $minY = (self::MAP_DIVISOR * $partY) + 1;
            $minY = $minY < 1 ?  : $minY;
            $maxY = ($partY + 1) * self::MAP_DIVISOR;
            $maxY = $maxY > $map->getMaxY() ? $map->getMaxY() : $maxY;
        }

        $mapRepo = $this->repos()->getMapRepository();

        return $this->render(
            'DbaAdminBundle::generator/index.html.twig',
            [
                'divisor' => self::MAP_DIVISOR,
                'maps' => $mapRepo->findAll(),
                'mapData' => $mapRepo->generateGeneratorMap($map, $partX, $partY),
                'partX' => $partX,
                'partY' => $partY,
                'availableImages' => $mapRepo->getAvailableImages(),
                'availableBonus' => $this->repos()->getMapBonusRepository()->findAll(),
                'map' => $map,
                'minX' => $minX,
                'maxX' => $maxX,
                'minY' => $minY,
                'maxY' => $maxY,
            ]
        );
    }

    /**
     * @Route("/map/create", name="admin.generator.map.create", methods={"GET", "POST"})
     */
    public function mapCreateAction(Request $request)
    {
        $map = new Map();
        $form = $this->createForm(
            Form\MapCreate::class,
            $map
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em()->persist($map);
            $this->em()->flush();
            $this->addFlash(
                'success',
                $this->trans('map.created')
            );

            return $this->redirect($this->generateUrl('admin.generator.map'));
        }

        return $this->render(
            'DbaAdminBundle::generator/create.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/image", name="admin.generator.image", methods={"GET", "POST"})
     */
    public function mapImageAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $webDirectory = $this->getParameter('kernel.root_dir') . '/../web/';
            $boxesPath = $webDirectory . 'bundles/dbaadmin/images/';
            $name = $request->request->get('name');
            $images = $request->request->get('images');
            $replace = $request->request->get('replace');
            $originalName = $request->request->get('original-name');

            $mapImage = $this->repos()->getMapImageRepository()->findOneByName($name);
            if (!empty($mapImage)) {
                if (empty($replace) || $originalName != $name) {
                    return $this->render(
                        'DbaAdminBundle::generator/image.html.twig',
                        [
                            'imagesData' => $images,
                            'name' => $name,
                        ]
                    );
                }

                $this->purgeImages($mapImage, $boxesPath);
                $this->em()->flush();
            } else {
                $mapImage = new MapImage();
                $mapImage->setName($name);
            }

            $arrayOfImages = [];
            foreach ($images as $key => $value) {
                if ($key == 'C') {
                    continue;
                }

                if (($arrayOfImages[$key] = imagecreatefrompng(realpath($webDirectory . $value))) == false) {
                    $error = true;
                    break;
                }
            }

            $imagesResult = [];
            $error = false;
            $imagesCenter = $this->findImages('center', basename(dirname($images['C'])));
            foreach ($imagesCenter as $key => $center) {
                $fileDay = 'map/day/' . $name . "_" . basename($center);
                $fileNight = 'map/night/' . $name . "_" . basename($center);

                $finalImage[$key] = imagecreatetruecolor(140, 140);
                $imageCenter = imagecreatefrompng(realpath($webDirectory . $center));
                if (!$imageCenter) {
                    imagedestroy($finalImage[$key]);
                    continue;
                }

                imagecopy($finalImage[$key], $arrayOfImages['ATR'], 120, 0, 0, 0, 20, 20);
                imagecopy($finalImage[$key], $arrayOfImages['ABR'], 120, 120, 0, 0, 20, 20);
                imagecopy($finalImage[$key], $arrayOfImages['ABL'], 0, 120, 0, 0, 20, 20);
                imagecopy($finalImage[$key], $arrayOfImages['ATL'], 0, 0, 0, 0, 20, 20);

                imagecopy($finalImage[$key], $arrayOfImages['BT'], 20, 0, 0, 0, 100, 20);
                imagecopy($finalImage[$key], $arrayOfImages['BB'], 20, 120, 0, 0, 100, 20);
                imagecopy($finalImage[$key], $arrayOfImages['BR'], 120, 20, 0, 0, 20, 100);
                imagecopy($finalImage[$key], $arrayOfImages['BL'], 0, 20, 0, 0, 20, 100);
                imagecopy($finalImage[$key], $imageCenter, 20, 20, 0, 0, 100, 100);

                $finalImageResized = imagecreatetruecolor(100, 100);
                imagecopyresampled($finalImageResized, $finalImage[$key], 0, 0, 0, 0, 100, 100, 140, 140);

                // create image for day
                if (!imagepng($finalImageResized, $boxesPath . $fileDay)) {
                    $error = true;
                    break;
                }

                // create night filters
                $filter = imagecreatetruecolor(100, 100);
                $c = imagecolorallocate($filter, 0, 0, 0);
                imagefilledrectangle($filter, 0, 0, 100, 100, $c);
                imagecopymerge($finalImageResized, $filter, 0, 0, 0, 0, 100, 100, 60);
                if (!imagepng($finalImageResized, $boxesPath . $fileNight)) {
                    $error = true;
                    break;
                }

                imagedestroy($filter);
                imagedestroy($finalImageResized);
                imagedestroy($finalImage[$key]);
                imagedestroy($imageCenter);

                $mapImageFile = new MapImageFile();
                $mapImageFile->setMapImage($mapImage);
                $mapImageFile->setDamage($key);
                $mapImageFile->setPeriod(0);
                $mapImageFile->setFile($fileDay);
                $this->em()->persist($mapImageFile);

                $mapImageFile = new MapImageFile();
                $mapImageFile->setMapImage($mapImage);
                $mapImageFile->setDamage($key);
                $mapImageFile->setPeriod(1);
                $mapImageFile->setFile($fileNight);
                $this->em()->persist($mapImageFile);

                $imagesResult[$key] = [
                    'day' => $fileDay,
                    'night' => $fileNight
                ];
            }

            $this->em()->persist($mapImage);
            $this->em()->flush();

            if (!empty($error)) {
                $this->purgeImages($mapImage, $boxesPath);
                $this->em()->remove($mapImage);
                $this->em()->flush();

                $this->addFlash(
                    'danger',
                    $this->trans('generator.image.failed')
                );
                return $this->redirect($this->generateUrl('admin.generator.image'));
            }

            // Destroy generated images
            if (!empty($arrayOfImages)) {
                foreach ($arrayOfImages as $key => $value) {
                    imagedestroy($arrayOfImages[$key]) ;
                }
            }

            return $this->render(
                'DbaAdminBundle::generator/image.html.twig',
                [
                    'result' => $imagesResult,
                ]
            );
        }

        return $this->render(
            'DbaAdminBundle::generator/image.html.twig',
            [
                'centers' => $this->findImages('center'),
                'angles' => $this->findImages('angle'),
                'verticalBorders' => $this->findImages('vertical-border'),
                'horizontalBorders' => $this->findImages('horizontal-border'),
            ]
        );
    }

    /**
     * Find all images from directory and type
     *
     * @param string $type Image type
     *
     * @return array
     */
    protected function findImages($type, $specificDirectory = '')
    {
        $finder = new Finder();
        $directory = 'bundles/dbaadmin/images/generator/';
        if ($type == 'center') {
            $files = $finder->name(
                empty($specificDirectory) ? '0.png' : '*.png'
            )->in($directory . 'center/' . ltrim($specificDirectory, '/'));
        } else {
            $files = $finder->name('*.png')->in($directory. 'border');
        }

        $return = [];
        foreach ($files as $file) {
            $filename = '/' . $file->getPathname();
            list ($height, $width) = getimagesize($file);
            if ($type == 'angle' && $height == 20 && $width == 20) {
                $return[$file->getBaseName('.png')] = $filename;
            } elseif ($type == 'vertical-border' && $height == 20 && $width == 100) {
                $return[$file->getBaseName('.png')] = $filename;
            } elseif ($type == 'horizontal-border' && $height == 100 && $width == 20) {
                $return[$file->getBaseName('.png')] = $filename;
            } elseif ($type == 'center' && $height == 100 && $width == 100) {
                if (empty($specificDirectory)) {
                    $return[$file->getRelativePath()] = $filename;
                } else {
                    $return[$file->getBaseName('.png')] = $filename;
                }
            }
        }

        ksort($return);
        return $return;
    }

    protected function purgeImages(MapImage $mapImage, $boxesPath)
    {
        $files = $this->repos()->getMapImageFileRepository()->findBy(
            [
                'mapImage' => $mapImage
            ]
        );

        foreach ($files as $file) {
            if (file_exists($boxesPath . $file->getFile())) {
                unlink($boxesPath . $file->getFile());
            }
            $this->em()->remove($file);
        }
    }
}
