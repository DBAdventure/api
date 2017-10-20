<?php

namespace Dba\GameBundle\Tests\Controller;

class AccountControllerTest extends BaseTestCase
{
    public function testProfile()
    {
        $this->login();
        $this->client->request('GET', '/api/account/player');
        $this->assertJsonResponse($this->client->getResponse());
        $json = json_decode($this->client->getResponse()->getContent(), true);
        $keys = [
            'id',
            'username',
            'email',
            'name',
            'history',
            'image',
            'x',
            'y',
            'side_points',
            'action_points',
            'max_action_points',
            'fatigue_points',
            'max_fatigue_points',
            'movement_points',
            'max_movement_points',
            'battle_points',
            'battle_points_remaining_start',
            'battle_points_remaining_end',
            'skill_points',
            'created_at',
            'updated_at',
            'time_remainning' => [
                'action_points',
                'fatigue_points',
                'movement_points',
                'ki_points',
            ],
            'zeni',
            'level',
            'total_strength',
            'total_accuracy',
            'total_agility',
            'total_analysis',
            'total_skill',
            'total_intellect',
            'total_resistance',
            'total_vision',
            'total_max_ki',
            'total_max_health',
            'strength',
            'accuracy',
            'agility',
            'analysis',
            'skill',
            'intellect',
            'resistance',
            'vision',
            'max_ki',
            'max_health',
            'health',
            'ki',
            'objects' => [
                'strength',
                'accuracy',
                'agility',
                'analysis',
                'skill',
                'intellect',
                'resistance',
                'vision',
                'max_health',
                'max_ki',
            ],
            'last_login',
            'roles',
            'class',
            'guild_player',
            'map',
            'rank',
            'side',
            'race',
            'target',
            'stats' => [
                'death_count',
                'nb_kill_good',
                'nb_hit_good',
                'nb_damage_good',
                'nb_kill_bad',
                'nb_hit_bad',
                'nb_damage_bad',
                'nb_kill_npc',
                'nb_hit_npc',
                'nb_damage_npc',
                'nb_kill_hq',
                'nb_hit_hq',
                'nb_damage_hq',
                'nb_stolen_zeni',
                'nb_action_stolen_zeni',
                'nb_dodge',
                'nb_wanted',
                'nb_analysis',
                'nb_spell',
                'nb_health_given',
                'nb_total_health_given',
                'nb_slap_taken',
                'nb_slap_given'
            ],
            'betrayals',
            'head_price',
            'inventory_max_weight',
            'inventory_weight',
            'has_mini_map',
            'is_admin',
        ];

        foreach ($keys as $key => $value) {
            if (!is_array($value)) {
                $this->assertArrayHasKey($value, $json);
            } else {
                foreach ($value as $childKey) {
                    $this->assertArrayHasKey($childKey, $json[$key]);
                }
            }
        }
    }

    public function testAppearanceChange()
    {
        $player = $this->login();
        $data = [
            'player_registration_race' =>  2,
            'player_appearance' => [
                'type' => 'HS1',
                'image' => 'HS10.png',
            ]
        ];
        $response = $this->client->request(
            'POST',
            '/api/account/appearance',
            $data
        );

        $this->assertJsonResponse($this->client->getResponse());
        $player = $this->repos()->getPlayerRepository()->findOneById($player->getId());
        $this->assertEquals('HS10.png', $player->getImage());
    }

    public function testAppearanceNotChange()
    {
        $player = $this->login();
        $data = [
            'player_registration_race' =>  2,
            'player_appearance' => [
                'type' => 'H1',
            ]
        ];
        $response = $this->client->request(
            'POST',
            '/api/account/appearance',
            $data
        );

        $this->assertJsonResponse($this->client->getResponse(), 400);
        $player = $this->repos()->getPlayerRepository()->findOneById($player->getId());
        $this->assertEquals('HS15.png', $player->getImage());
    }

    public function testTrainingRoomWithWrongParameter()
    {
        $this->login();
        $this->client->request('POST', '/api/account/training/Nothing');
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testTrainingRoomWithoutActionPoints()
    {
        $player = $this->login();
        $player->setActionPoints(0);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/account/training/health');
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testTrainingRoomWithoutSkillPoints()
    {
        $player = $this->login();
        $player->setSkillPoints(0);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/account/training/health');
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testTrainingRoomHealth()
    {
        $player = $this->login();
        $player->setActionPoints(5);
        $player->setSkillPoints(1);
        $oldHealth = $player->getMaxHealth();
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/account/training/health');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals($oldHealth + 45, $player->getMaxHealth());
        $this->assertEquals($oldHealth + 45, $player->getHealth());
        $this->assertEquals(0, $player->getSkillPoints());
        $this->assertEquals(0, $player->getActionPoints());
    }

    public function testTrainingRoomKi()
    {
        $player = $this->login();
        $player->setActionPoints(5);
        $player->setSkillPoints(1);
        $oldKi = $player->getTotalMaxKi();
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/account/training/ki');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertEquals($oldKi + 2, $player->getMaxKi());
        $this->assertEquals($oldKi + 2, $player->getTotalMaxKi());
        $this->assertEquals($oldKi + 2, $player->getKi());
        $this->assertEquals(0, $player->getSkillPoints());
        $this->assertEquals(0, $player->getActionPoints());
    }

    public function testTrainingRoomSkill()
    {
        $player = $this->login();
        $player->setActionPoints(5);
        $player->setSkillPoints(1);
        $oldResistance = $player->getResistance();
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/account/training/resistance');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertEquals($oldResistance + 1, $player->getResistance());
        $this->assertEquals(0, $player->getSkillPoints());
        $this->assertEquals(0, $player->getActionPoints());
    }
}
