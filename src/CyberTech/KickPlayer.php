<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace CyberTech;

use pocketmine\scheduler\PluginTask;
use pocketmine\Server;
use pocketmine\Player;


/**
 * Description of KickPlayer
 *
 * @author carlt_000
 */
class KickPlayer extends PluginTask {
    
    private $main;
    private $player;
    public function __construct(Main $main,Player $player ) {
        $this->main = $main;
        $this->player = $player;
        parent::__construct ( $main );
    }
    
    public function onRun($t) {
        $this->player->kick("Your Still Banned!");
    }
    //put your code here
}
