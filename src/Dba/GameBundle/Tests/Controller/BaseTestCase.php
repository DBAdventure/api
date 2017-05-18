<?php

namespace Dba\GameBundle\Tests\Controller;

use DateTime;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Dba\GameBundle\Entity\Player;
use Dba\GameBundle\Entity\Guild;
use Dba\GameBundle\Entity\GuildPlayer;
use Dba\GameBundle\Entity\GuildRank;
use Dba\GameBundle\Entity\Side;
use Dba\GameBundle\Entity\Type;

class BaseTestCase extends WebTestCase
{
    protected $player;
    protected $client;
    protected $container;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->clearPlayerTable();
        $this->clearNewsTable();
        $this->clearGuildTable();
    }

    public function clearPlayerTable()
    {
        $this->em()->createQuery('DELETE FROM DbaGameBundle:Player')->execute();
        $this->em()->flush();
    }

    public function clearGuildTable()
    {
        $this->em()->createQuery('DELETE FROM DbaGameBundle:Guild')->execute();
        $this->em()->flush();
    }

    public function clearNewsTable()
    {
        $this->em()->createQuery('DELETE FROM DbaGameBundle:News')->execute();
        $this->em()->flush();
    }

    protected function assertJsonResponse($response, $responseCode = 200)
    {
        $this->assertEquals($responseCode, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        return json_decode($response->getContent());
    }

    protected function em()
    {
        return $this->container->get('doctrine')->getManager();
    }

    public function services()
    {
        return $this->container->get('dba.game.services');
    }

    protected function repos()
    {
        return $this->container->get('dba.game.repos');
    }

    protected function login()
    {
        if (!empty($this->player)) {
            return $this->player;
        }

        $firewallName = 'main';
        $this->createPlayer();
        $player = $this->repos()->getPlayerRepository()->findOneByUsername('admin');

        // save the login token into the session and put it in a cookie
        $session = $this->container->get('session');
        $token = new UsernamePasswordToken($player, null, $firewallName, array('ROLE_ADMIN'));
        $session->set('_security_'. $firewallName, serialize($token));
        $session->save();
        $this->client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        $this->player = $player;
        return $this->player;
    }

    protected function createPlayer($name = 'admin')
    {
        $player = new Player();
        $player->setImage('HS15.png');
        $player->setPassword(
            $this->container->get('security.password_encoder')->encodePassword(
                $player,
                'test'
            )
        );
        $player->setConfirmationToken(rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='));
        $player->setRoles([Player::ROLE_ADMIN]);
        $player->setZeni(50);
        $player->setEnabled(true);
        $player->setName($name);
        $player->setUsername($name);
        $player->setEmail($name . '@dba.com');
        $player->setRace($this->repos()->getRaceRepository()->findOneById(2));
        $player->setRank(
            $this->repos()->getRankRepository()->findOneBy([
                'race' => $player->getRace(),
                'level' => 1,
            ])
        );
        $player->setActionPoints(30);
        $player->setMovementPoints(100);
        $player->setFatiguePoints(30);
        $player->setBattlePoints(0);

        $side = $this->repos()->getSideRepository()->findOneById(Side::GOOD);
        $player->setSide($side);
        $player->setSidePoints(1);

        $player->setIp('127.0.0.1');
        $dateTime = new DateTime();
        $player->setCreatedAt($dateTime);
        $player->setUpdatedAt($dateTime);
        $player->setActionUpdatedAt($dateTime);
        $player->setKiUpdatedAt($dateTime);
        $player->setMovementUpdatedAt($dateTime);
        $player->setFatigueUpdatedAt($dateTime);

        $this->services()->getPlayerService()->respawn($player);

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
        $this->em()->persist($player);
        $this->em()->flush();
        $this->em()->refresh($player);

        return $player;
    }

    protected function createGuild(Player $player, $name)
    {
        $guild = new Guild();
        $guild->setShortName($name);
        $guild->setName($name);
        $guild->setHistory('');
        $guild->setCreatedBy($player);
        $guild->setEnabled(true);

        $guildRank = new GuildRank();
        $guildRank->setName('member');
        $guildRank->setGuild($guild);
        $guildRank->setRole(GuildRank::ROLE_PLAYER);
        $this->em()->persist($guildRank);

        $guildRank = new GuildRank();
        $guildRank->setName('moderator');
        $guildRank->setGuild($guild);
        $guildRank->setRole(GuildRank::ROLE_MODO);
        $this->em()->persist($guildRank);

        $guildRank = new GuildRank();
        $guildRank->setName('administrator');
        $guildRank->setGuild($guild);
        $guildRank->setRole(GuildRank::ROLE_ADMIN);
        $this->em()->persist($guildRank);

        $guildPlayer = new GuildPlayer();
        $guildPlayer->setPlayer($player);
        $guildPlayer->setGuild($guild);
        $guildPlayer->setEnabled(true);
        $guildPlayer->setZeni(0);
        $guildPlayer->setRank($guildRank);

        $this->em()->persist($player);
        $this->em()->persist($guildPlayer);
        $this->em()->persist($guild);
        $this->em()->flush();
        $this->em()->refresh($player);

        return $guild;
    }

    protected function joinGuild(Guild $guild, Player $player)
    {
        $guildRank = $this->repos()->getGuildRankRepository()
                   ->findOneBy(['role' => GuildRank::ROLE_PLAYER]);

        $guildPlayer = new GuildPlayer();
        $guildPlayer->setPlayer($player);
        $guildPlayer->setGuild($guild);
        $guildPlayer->setEnabled(true);
        $guildPlayer->setZeni(0);
        $guildPlayer->setRank($guildRank);

        $this->em()->persist($guildPlayer);
        $this->em()->flush();
    }
}
