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


/**
 * Description of KickPlayer
 *
 * @author carlt_000
 */
class Unmute extends PluginTask {
    
    private $main;
    private $player;
    public function __construct(Main $main,Player $player) {
        $this->main = $main;
        $this->player = $player;
        parent::__construct ( $main );
    }
    
    public function onRun($t) {
        $this->main->muted[$this->player->getName()] = FALSE;
        //$this->main->yml["spammer"][$this->player->getName()] = 1;
        $this->player->sendMessage("Make Sure Not To Spam!");
    }
    //put your code here
}
