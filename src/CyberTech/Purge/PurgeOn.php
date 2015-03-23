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
use CyberTech\Purge\PurgeOff;

/**
 * Description of KickPlayer
 *
 * @author carlt_000
 */
class PurgeOn extends PluginTask {
    
    public $main;
    
    public function __construct(Main $main) {
        $this->main = $main;
        parent::__construct ( $main );
    }
    
    public function onRun($t) {
       $this->main->getServer()->broadcastMessage("[PURGE] PUREGE!!!!!");
       $this->main->purge = true;
       foreach ($this->main->getServer()->getOnlinePlayers() as $p){
           $p->setHealth("50");
       }
       $this->main->getServer()->getScheduler()->scheduleDelayedTask(new PurgeOff($this->main) , 20*60);
       //Scheduel Purge Off
       
    }
}
