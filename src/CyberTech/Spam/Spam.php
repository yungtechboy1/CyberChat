<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace CyberTech\Spam;

use pocketmine\scheduler\PluginTask;
use pocketmine\Server;
use pocketmine\Player;
use CyberTech\Main;
use CyberTech\Spam\Unmute;


/**
 * Description of KickPlayer
 *
 * @author carlt_000
 */
class Spam extends PluginTask {
    
    private $main;
    private $player;
    public function __construct(Main $main,Player $player) {
        $this->main = $main;
        $this->player = $player;
        parent::__construct ( $main );
    }
    
    public function onRun($t) {
        if (!isset( $this->main->muted[$this->player->getName()]) || $this->main->muted[$this->player->getName()] != true){
        $this->main->muted[$this->player->getName()] = true;
        if (isset($this->main->yml["spammer"][$this->player->getName()])){
            if ($this->main->yml["spammer"][$this->player->getName()] > 1){
                $this->player->sendMessage("Last Warning! Make Sure Not To Spam!");
                $this->player->sendMessage("You Will Be Unmuted In 60 Secs!");
                $this->main->getServer()->getScheduler()->scheduleDelayedTask(new Unmute($this->main, $this->player), 20 * 60);
                return true;
            }

            if ($this->main->yml["spammer"][$this->player->getName()] >= 4){
                $this->player->sendMessage("Fuckin Spammer!");
                $this->player->sendMessage("You Will Be Unmuted In 1 Hour!");
                $this->main->getServer()->getScheduler()->scheduleDelayedTask(new Unmute($this->main, $this->player), 20 * 60 * 60);
                return true;
            } 
        }else{
            $this->main->yml["spammer"][$this->player->getName()] = 1;
            $this->player->sendMessage("Careful Warning! Make Sure Not To Spam!");
            $this->player->sendMessage("You Will Be Unmuted In 30 Secs!");
            $this->main->getServer()->getScheduler()->scheduleDelayedTask(new Unmute($this->main, $this->player), 20 * 30);
            return true;
        }
    }
    
    return true;
    }
    //put your code here
}
