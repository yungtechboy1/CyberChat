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
use CyberTech\Purge\PurgeOn;
use CyberTech\Main;


/**
 * Description of KickPlayer
 *
 * @author carlt_000
 */
class StartPurge extends PluginTask {
    
    public $main;
    private $time;
    
    public function __construct(Main $main, $time = null) {
        $this->main = $main;
        $this->time = $time;
        parent::__construct ( $main );
    }
    
    public function onRun($t) {
        if ($this->time !== null && $this->time > 0){
            $this->WarningSecs($this->time);
        }
        if ($this->time == -1){
             $this->main->getServer()->getScheduler()->scheduleTask(new PurgeOn($this->main));
        }
        if ($this->time == null){
            $this->main->getServer()->broadcastMessage("[PURGE] WARNING!!!!\n[PURGE] The Purge Will Commence in 1 Min!\n[PURGE] ALL PVP WILL BE ENABLED!");
            $this->main->getServer()->getScheduler()->scheduleDelayedTask(new StartPurge($this->main, 30) , 20*30);
            $this->main->getServer()->getScheduler()->scheduleDelayedTask(new StartPurge($this->main, 15) , 20*45);
            $this->main->getServer()->getScheduler()->scheduleDelayedTask(new StartPurge($this->main, 10) , 20*50);
            $this->main->getServer()->getScheduler()->scheduleDelayedTask(new StartPurge($this->main, 5) , 20*55);
            $this->main->getServer()->getScheduler()->scheduleDelayedTask(new StartPurge($this->main, 3) , 20*57);
            $this->main->getServer()->getScheduler()->scheduleDelayedTask(new StartPurge($this->main, 2) , 20*58);
            $this->main->getServer()->getScheduler()->scheduleDelayedTask(new StartPurge($this->main, 1) , 20*59);
            $this->main->getServer()->getScheduler()->scheduleDelayedTask(new StartPurge($this->main, -1) , 20*60);
        }
       
      
    }
    
    public function WarningSecs($time) {
        $this->main->getServer()->broadcastMessage("[PURGE] ".$time." Secs");
    }
    
    public function StartPurge() {
        
    }
    
    public function StopPurge() {
        
    }
    //put your code here
}
