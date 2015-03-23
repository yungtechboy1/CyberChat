<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace CyberTech\Purge;

use pocketmine\scheduler\PluginTask;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\level\Position;
use pocketmine\level\Level;
use CyberTech\Main;
use CyberTech\Purge\StartPurge;

/**
 * Description of KickPlayer
 *
 * @author carlt_000
 */
class PurgeOff extends PluginTask {
    
    private $main;
    
    public function __construct(Main $main) {
        $this->main = $main;
        parent::__construct ( $main );
    }
    
    public function onRun($t) {
        $this->main->purge = false;
        $this->main->getServer()->broadcastMessage("[PURGE] The PURGE Is Now Over! \n[PURGE] We Hope That Cleared your Souls!");
    }
    //put your code here
}
