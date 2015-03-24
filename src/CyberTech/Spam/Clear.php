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
class Clear extends PluginTask {
    
    private $main;
    public function __construct(Main $main) {
        $this->main = $main;
        parent::__construct ( $main );
    }
    
    public function onRun($t) {
        unset($this->main->muted);
        $this->main->muted = array();
    }
    //put your code here
}
