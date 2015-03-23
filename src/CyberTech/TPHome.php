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
use pocketmine\math\Vector3;
use pocketmine\level\Position;
use pocketmine\level\Level;

/**
 * Description of KickPlayer
 *
 * @author carlt_000
 */
class TPHome extends PluginTask {
    
    private $main;
    private $player;
    private $level;
    private $vector3;
    
    public function __construct(Main $main, Player $player, Vector3 $pos, Level $level) {
        $this->main = $main;
        $this->player = $player;
        $this->vector3 = $pos;
        $this->level = $level;
        parent::__construct ( $main );
    }
    
    public function onRun($t) {
        $this->player->sendMessage("Teleporting You Home!");
        $this->player->teleport($this->vector3);
        $this->player->setLevel($this->level);
    }
    //put your code here
}
