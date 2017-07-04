<?php

namespace Dba\GameBundle\Controller;

use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Dba\GameBundle\Form\PlayerRegistration;
use Dba\GameBundle\Entity\Player;
use Dba\GameBundle\Entity\Object;
use Dba\GameBundle\Entity\Side;
use Dba\GameBundle\Entity\Race;

class DefaultController extends BaseController
{
    public function postRegisterAction(Request $request)
    {
        if (!empty($this->getUser())) {
            return $this->forbidden();
        }

        $player = new Player();
        $form = $this->createForm(PlayerRegistration::class, $player);
        $form->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->badRequest($this->getErrorMessages($form));
        }
        $player->setPassword(
            $this->container->get('security.password_encoder')->encodePassword(
                $player,
                $player->getPassword()
            )
        );
        $player->setConfirmationToken(rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='));
        $player->setRoles([Player::ROLE_PLAYER]);
        $player->setZeni(50);
        $player->setRank(
            $this->repos()->getRankRepository()->findOneBy(
                [
                    'race' => $player->getRace(),
                    'level' => 1,
                ]
            )
        );
        $player->setActionPoints(30);
        $player->setMovementPoints(100);
        $player->setFatiguePoints(30);
        $player->setBattlePoints(0);

        $player->setSidePoints(
            $player->getSide()->getId() == Side::GOOD ?
            1 :
            -1
        );

        $player->setIp($request->getClientIp());
        $dateTime = new DateTime();
        $player->setActionUpdatedAt($dateTime);
        $player->setKiUpdatedAt($dateTime);
        $player->setMovementUpdatedAt($dateTime);
        $player->setFatigueUpdatedAt($dateTime);

        $this->services()->getPlayerService()->respawn($player);

        $this->setRegisterStats($request, $player);
        $this->addPlayerObjects($player);

        $this->em()->persist($player);
        $this->em()->flush();
        $this->services()->getMailService()->send(
            $player,
            $this->trans('account.registered'),
            'account-confirm'
        );

        return [
            'messages' => 'account.created'
        ];
    }

    protected function setRegisterStats($request, Player $player)
    {
        $playerRegistration = $request->request->get('player_registration');
        switch ($playerRegistration['class']) {
            case 1:
                $player->setHealth(770);
                $player->setMaxHealth(770);
                $player->setKi(1);
                $player->setMaxKi(1);
                $player->setStrength(10);
                $player->setResistance(5);
                $player->setAccuracy(9);
                $player->setAgility(3);
                $player->setVision(1);
                $player->setAnalysis(1);
                $player->setSkill(1);
                $player->setIntellect(1);
                break;
            case 2:
                $player->setHealth(365);
                $player->setMaxHealth(365);
                $player->setKi(15);
                $player->setMaxKi(15);
                $player->setStrength(1);
                $player->setResistance(3);
                $player->setAccuracy(1);
                $player->setAgility(1);
                $player->setVision(5);
                $player->setAnalysis(3);
                $player->setSkill(4);
                $player->setIntellect(15);
                break;
            case 3:
                $player->setHealth(500);
                $player->setMaxHealth(500);
                $player->setKi(1);
                $player->setMaxKi(1);
                $player->setStrength(5);
                $player->setResistance(1);
                $player->setAccuracy(11);
                $player->setAgility(15);
                $player->setVision(1);
                $player->setAnalysis(2);
                $player->setSkill(1);
                $player->setIntellect(1);
                break;
            case 4:
                $player->setHealth(500);
                $player->setMaxHealth(500);
                $player->setKi(9);
                $player->setMaxKi(9);
                $player->setStrength(4);
                $player->setResistance(1);
                $player->setAccuracy(1);
                $player->setAgility(1);
                $player->setVision(1);
                $player->setAnalysis(1);
                $player->setSkill(17);
                $player->setIntellect(7);
                break;
            case 5:
                $player->setHealth(500);
                $player->setMaxHealth(500);
                $player->setKi(1);
                $player->setMaxKi(1);
                $player->setStrength(6);
                $player->setResistance(2);
                $player->setAccuracy(4);
                $player->setAgility(4);
                $player->setVision(15);
                $player->setAnalysis(1);
                $player->setSkill(1);
                $player->setIntellect(4);
                break;
            case 6:
                $player->setHealth(500);
                $player->setMaxHealth(500);
                $player->setKi(1);
                $player->setMaxKi(1);
                $player->setStrength(5);
                $player->setResistance(3);
                $player->setAccuracy(4);
                $player->setAgility(3);
                $player->setVision(5);
                $player->setAnalysis(15);
                $player->setSkill(1);
                $player->setIntellect(1);
                break;
        }
    }

    /**
     * Add player objects
     *
     * @param Player $player Player
     */
    protected function addPlayerObjects(Player $player)
    {
        $objects = [
            Object::DEFAULT_SENZU => 1,
            Object::DEFAULT_POTION_OF_FATIGUE => 2,
            Object::DEFAULT_BERRIES => 5
        ];
        $objectService = $this->services()->getObjectService();
        $objectRepo = $this->repos()->getObjectRepository();
        foreach ($objects as $objectId => $quantity) {
            $objectService->addToInventory(
                $player,
                $objectRepo->findOneById($objectId),
                false,
                $quantity
            );
        }
    }
}
