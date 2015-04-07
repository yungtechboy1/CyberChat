<?php
/*
 * CC - Custom Chanels (v1.0.0) by CyberTech++
 * Developer: CyberTech++ (Yungtechboy1)
 * Website: http://www.cybertechpp.com
 * Date: 3/7/15 5:45 PM(CST)
 * Copyright & License: (C) 2015 Cybertech++
 * All Rights Reserved
 */

namespace CyberTech;

use onebone\economyapi\EconomyAPI;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerChatEvent;
use FactionsPro;
use FactionsPro\FactionMain;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerBucketEmptyEvent;
use pocketmine\event\player\PlayerBucketEvent;
use pocketmine\event\player\PlayerBucketFillEvent;
use pocketmine\math\Vector3;
use pocketmine\level\Position;
use CyberTech\Purge\StartPurge;
use pocketmine\event\player\PlayerDeathEvent;
use CyberTech\Spam\Spam;
use CyberTech\Spam\Clear;
use pocketmine\event\player\PlayerMoveEvent;

class Main extends PluginBase implements Listener{

    private $playerschan = array();
    //Player Muted Chat
    private $pmc = array();
    public $yml;
    private $channels = array();
    public $faction;
    private $p1;
    private $p2;
    private $bans;
    private $pips;
    private $homes;
    private $chatoff = false;
    public $purge;
    private $tpl;
    private $api;
    public $spam;
    
    
    
    public function onEnable() {
        @mkdir($this->getDataFolder());
        $this->loadYml();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        //$this->factionspro = FactionMain::getInstance();
        $this->faction = $this->getServer()->getPluginManager()->getPlugin("CyberFactions");
        $this->CreateChannelInitals();
        $this->api = EconomyAPI::getInstance();
        $this->getLogger()->info("LOADED");
        $this->getServer()->getScheduler()->scheduleDelayedRepeatingTask(new StartPurge($this), 20 * 60 * 2, 20 * 60 * 20);
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new Clear($this), 20*5);
        //30 Mins
    }
    
    public function onDisable() {
        $this->SaveYML();
        $this->getLogger()->warning("DISABLED");
    }
    
    public function onCommand(CommandSender $sender, Command $command, $label, array $args){
    $player = $this->getServer()->getPlayerExact($sender->getName());
    switch($command->getName()){
                        case "purge";
                            if ($this->purge == false){
                            $this->getServer()->getScheduler()->scheduleTask(new StartPurge($this));   
                            }
                            break;
                        case "chon":
                            $this->chatoff = false;
                            $this->getServer()->broadcastMessage("All Chat Is UnMuted!");
                            return true;
                            break;
                        case "choff":
                            $this->chatoff = true;
                            $this->getServer()->broadcastMessage("All Chat Is Muted!");
                            return true;
                            break;
                        case "ch":
                            if (isset($args[0]) && $args[0] !== "" && $args[0] == "help"){
                                $sender->sendMessage("--------ChatChannel Commands------");
                                $sender->sendMessage("Use [ /ch list ] To List all chat Channels");
                                $sender->sendMessage("Use [ /ch join ] To Join a Channel");
                                $sender->sendMessage("Use [ /ch mute ] To Mute Chat");
                                $sender->sendMessage("Use [ /ch unmute ] To UnMute Chat");
                                return true;
                            }elseif(isset($args[0]) && $args[0] !== "" && $args[0] == "mute"){
                                $player = $this->getServer()->getPlayerExact($sender->getName());
                                if ($player instanceof Player){
                                    $playern = $player->getName();
                                    $this->pmc[$playern] = true;
                                    $player->sendMessage("Chat is now Muted!");
                                    return true;
                                }else{
                                    $this->getLogger()->info("Silly Server! Players Only!!");
                                    return true;
                                } 
                            }elseif(isset($args[0]) && $args[0] !== "" && $args[0] == "unmute"){
                                $player = $this->getServer()->getPlayerExact($sender->getName());
                                if ($player instanceof Player){
                                    $playern = $player->getName();
                                    $this->pmc[$playern] = false;
                                    $player->sendMessage("Chat is now UnMuted!");
                                    return true;
                                }else{
                                    $this->getLogger()->info("Silly Server! Players Only!!");
                                    return true;
                                } 
                            }elseif(isset($args[0]) && $args[0] !== "" && $args[0] == "join" ){
                                if (isset($args[1]) && $args[1] !== ""){
                                    if ($player instanceof Player){
                                        $name = strtoupper($args[1]);
                                        if (isset($this->yml["channels"][$name]["active"]) && $this->yml["channels"][$name]["active"] == true){
                                            $this->playerschan[$player->getName()] = $args[1];
                                            $player->sendMessage("You Have Just Joined ".$args[1]." Chat!");
                                        }else{
                                            if (isset($this->yml["channels-initals"][$args[1]]) && $this->yml["channels-initals"][$args[1]] !== ""){
                                                $this->playerschan[$player->getName()] = $this->yml["channels-initals"][$args[1]];
                                                $player->sendMessage("You Have Just Joined ".$this->yml["channels-initals"][$args[1]]." Chat!");
                                            }
                                        }
                                    }
                                }else{
                                    $player->sendMessage("Please Enter a Valid Channel ID");
                                }
                            }elseif(isset($args[0]) && $args[0] !== "" && $args[0] == "list"){
                                if (isset($args[2])){
                                    $aa = ($args[2]*5) - 5;
                                    $ab = ($args[2]*5);
                                }else{
                                    $aa = 0;
                                    $ab = 5;
                                }
                                $player->sendMessage("--------ChatChannels--------");
                                $x = 0;
                                foreach($this->yml["channels"] as $a => $b){
                                    //    5     5     10
                                    if (($aa <= $x) <= $ab){
                                        if ($b['active'] == TRUE){
                                            $c = str_replace("_", " ", $a);
                                            $d = $b['description'];
                                            $i = $b["initals"];
                                            $e = "0";
                                            $player->sendMessage("[$c] - [$e] -  $d - /ch join $i");
                                            //$player->sendMessage("[$c] - /ch join $i");
                                        }
                                    }
                                    $x++;
                                }
                            }else{
                                if ($sender instanceof Player){$player->sendMessage("Unknown Command!");}
                                return false;
                            }
                            break;
                        case "setprefix":
                            if ($player instanceof Player){
                                if ($player->hasPermission("cc.op.setprefix")){
                                    if (count($args) == 2 ){
										if (isset($args[0]) && $args[0] !== "" && isset($args[1]) && $args[1] !== ""){
										$setplayer = $this->getServer()->getPlayer($args[0]);
											if ($setplayer instanceof Player){
												$this->yml["prefixs"][$setplayer->getName()] = $args[1];
												$this->SaveYML();
												return true;
											}
										}else{
											$player->sendMessage("Player Does Not Exist!");
										}
									}elseif(isset($args[0])){
										$this->yml["prefixs"][$sender->getName()] = $args[0];
										$this->SaveYML();
										return true;
									}
                                }
                            }else{
                                if (isset($args[0]) && $args[0] !== "" && isset($args[1]) && $args[1] !== ""){
                                    $setplayer = $this->getServer()->getPlayer($args[0]);
                                    if ($setplayer instanceof Player){
                                        $this->yml["prefixs"][$setplayer->getName()] = $args[1];
                                        $this->SaveYML();
                                        return true;
                                    }
                                }
                            }
                            break;
                        case "cmute":
                            if (isset($args[0]) && $args[0] !== ""){
                                if ($sender->hasPermission("cc.op.mute")){
                                    $sp = $this->getServer()->getPlayer($args[0]);
                                    if ($sp instanceof Player){
                                        $this->muted[$sp->getName()] = TRUE;
                                        $sp->sendMessage("You Are Now Muted!");
                                        return true;
                                    }else{
                                        $sender->sendMessage("Player Does Not Exist");
                                    }
                                }
                            }
                            break;
                        case "cunmute":
                            if (isset($args[0]) && $args[0] !== ""){
                                if ($sender->hasPermission("cc.op.unmute")){
                                    $sp = $this->getServer()->getPlayer($args[0]);
                                    if ($sp instanceof Player){
                                        $this->muted[$sp->getName()] = FALSE;
                                        $sp->sendMessage("You Are Now UnMuted!");
                                        return true;
                                    }else{
                                        $sender->sendMessage("Player Does Not Exist");
                                    }
                                }
                            }
                            break;
                        case "p1":
                            if ($player->hasPermission("cc.op")){
                                $this->p1 = $player->getPosition();
                                $player->sendMessage("Ready For /p2");
                            }
                            break;
                        case "p2":
                            if ($player->hasPermission("cc.op")){
                                $this->p2 = $player->getPosition();
                            }
                            break;
                        case "area":
                            if (isset($args[0]) && $args[0] !== "" && $args[0] == "set"){
                                if (isset($args[1]) && $args[1] !== ""){
                                    //if (!isset($this->yml['Areas'][$args[1]]["active"]) && $this->yml['Areas'][$args[1]]["active"] !== "true"){
                                        $this->yml['Areas'][$args[1]]["active"] = "true";
                                        $this->yml['Areas'][$args[1]]["pvp"] = "false";
                                        $this->yml['Areas'][$args[1]]["edit"] = "false";
                                        $this->yml['Areas'][$args[1]]["x1"] = $this->p1->getX(); 
                                        $this->yml['Areas'][$args[1]]["z1"] = $this->p1->getZ();  
                                        $this->yml['Areas'][$args[1]]["x2"] = $this->p2->getX(); 
                                        $this->yml['Areas'][$args[1]]["z2"] = $this->p2->getZ(); 
                                        $this->SaveYML();
                                    //}
                                }
                            }
                            break;
                        case "tb":
                            if (isset($args[0]) && $args[0] !== ""){
                                if (isset($args[1]) && $args[1] !== ""){
                                    if (!isset($args[2])){
                                        $a = 0;
                                    }else{
                                        $a = $args[2];
                                    }
                                        if (! $this->AddTempBan($args[0], $a, $args[1]) && $player instanceof Player){
                                            $player->sendMessage("Error! Please Try again!");
                                        }
                                }
                            }else{
                                if ($player->getName() !== "CONSOLE"){
                                    $player->sendMessage("/tb <player> <hours> <mins>");
                                }
                            }
                            break;
                        case "tbp":
                            if (isset($args[0]) && $args[0] !== ""){
                        $ssender = $this->getServer()->getOfflinePlayer($args[0]);
                        $this->bans[$ssender->getName()] = 0;
                            }
                            break;
                        case "treload":
                                $this->loadYml();
                            break;
                        case "tsave":
                                $this->SaveYML();
                            break;
                        case "sethome":
                            if ($player instanceof Player){
                                if (isset($args[0]) && $args[0] != ""){
                                    $q = $args[0];
                                    if (($q == "1" || $q == "2" || $q == "3" ) && count($q) == 1){
                                        if (isset($this->homes[$player->getName()]) && isset($this->homes[$player->getName()][$q])){
                                            if (isset($args[1]) && $args[1] != "" && $args[1] == "f"){
                                                $this->homes[$player->getName()][$q] = $player->getX()."|".$player->getY()."|".$player->getZ()."|".$player->getLevel()->getName();
                                                $player->sendMessage("Home Set!");
                                                return true;
                                            }else{    
                                                $player->sendMessage("Are You Sure You Want To OverRide Home $q?");
                                                $player->sendMessage("If So Add 'f' to the End Of the Command");
                                                return true;
                                            }
                                        }else{
                                            $this->homes[$player->getName()][$q] = $player->getX()."|".$player->getY()."|".$player->getZ()."|".$player->getLevel()->getName();
                                            $player->sendMessage("Home Set!");
                                            return true;
                                        }
                                    }else{
                                        $player->sendMessage("Useage : /sethome < 1 | 2 | 3 > (f)");
                                        return true;
                                    }
                                }else{
                                    $player->sendMessage("Useage : /sethome < 1 | 2 | 3 > (f)");
                                    return true;
                                }
                            }
                            return false;
                            break;
                        case "home":
                            if ($player instanceof Player){
                                if (isset($args[0]) && $args[0] != ""){
                                    $q = $args[0];
                                    if ($q == 1 || $q == 2 || $q == 3){
                                        if (isset($this->homes[$player->getName()]) && isset($this->homes[$player->getName()][$q])){
                                            $cord = array();
                                            $string = explode("|" , $this->homes[$player->getName()][$q]);
                                            $cord['x'] = $string[0];
                                            $cord['y'] = $string[1];
                                            $cord['z'] = $string[2];
                                            $cord['level'] = $string[3];
                                            $pos = new Vector3($cord['x'], $cord['y'], $cord['z']);
                                            $player->sendMessage("Teleporting In 3 Secs!");
                                            $level = $this->getServer()->getLevelByName($cord['level']);
                                            $task = new TPHome($this, $player, $pos, $level);
                                            $this->getServer()->getScheduler()->scheduleDelayedTask($task, 60);
                                            return true;
                                        }else{
                                            $player->sendMessage("Please Set Home a First!");
                                            return true;
                                        }
                                    }else{
                                        $player->sendMessage("Useage : /home < 1 | 2 | 3 >");
                                        return true;
                                    }
                                }else{
                                   $player->sendMessage("Useage : /home < 1 | 2 | 3 >"); 
                                   return true;
                                }
                            }
                            return false;
                            break ;
			default:
				return false;
		}

    return false;
    }
    
    public function onMove(PlayerMoveEvent $event){
        if($event->getPlayer() instanceof Player and !$event->getPlayer()->isOp()){
            $player = $event->getPlayer();                    
            $block = $event->getPlayer()->getLevel()->getBlock(new Vector3($player->getX(),$player->getY()-1,$player->getZ()));
            if($block->getID() == 0){
                if(!isset($this->flyers[$player->getName()])) $this->flyers[$player->getName()] = 0;
                $this->flyers[$player->getName()]++;
                if($this->flyers[$player->getName()] >= 180){ $this->AddTempBan($player->getName(), 15 , 0);$this->flyers[$player->getName()] = 0;}
            }else{
                $this->flyers[$player->getName()] = 0;
            }
            }
            }
    
    public function AddTempBan($name, $mins , $hours) {
        $player = $this->getServer()->getPlayer($name);
        if($player instanceof Player){
            $playern = $player->getName();
            $a = strtotime("+ $hours hours $mins mins");
            $this->bans[$playern] = $a;
            if (isset($this->bans["IPS"][$player->getAddress()]) && !in_array($playern, $this->bans["IPS"][$player->getAddress()])){
                $this->bans["IPS"][$player->getAddress()][] = $playern;
            }
            $this->SaveYML();
            $this->getServer()->broadcastMessage($playern ." Is Now Banned For $hours Hours and $mins Mins!");
            $player->kick("Temp Ban!");
            return true;
        }
        echo "NOT PLAYER!";
        return false;
    }
    
    public function OnPlayerDeath(PlayerDeathEvent $event) {
        $this->api->addMoney($event->getEntity()->getName(), "500");
    }
    
    public function OnBucketUse(PlayerBucketEmptyEvent $param) {
        if(!$param->getPlayer()->isOp()){
            $param->setCancelled();
            $player = $param->getPlayer();
            $player->kick("Buckets Arnt Allowed!");
        }    
    }
    
    public function OnBucketFill(PlayerBucketFillEvent $param) {
        if(!$param->getPlayer()->isOp()){
            $param->setCancelled();
            $player = $param->getPlayer();
            $player->kick("Buckets Arnt Allowed!");
        }    
    }
    
    public function OnBucketPlace(PlayerBucketEmptyEvent $param) {
         if(!$param->getPlayer()->isOp()){
            $param->setCancelled();
            $player = $param->get;
            $player->kick("Buckets Arnt Allowed!");
        }
    }
    
    public function OnPlayerJoin(PlayerJoinEvent $event) {
        $a = false;
        $z = 0;
        $c = false;
        if ($this->GetPlayerPrefix($event->getPlayer()) !== "Steve"){
            $event->getPlayer()->setNameTag("[".$this->GetPlayerPrefix($event->getPlayer())."] ".$event->getPlayer()->getName());
        }
        if (isset($this->pips[$event->getPlayer()->getAddress()])){
            if (! in_array($event->getPlayer()->getName() , $this->pips[$event->getPlayer()->getAddress()], true)){
                $this->pips[$event->getPlayer()->getAddress()][] = $event->getPlayer()->getName();
            }
        }
            
        if (isset($this->bans[$event->getPlayer()->getName()])){
            if ($this->bans[$event->getPlayer()->getName()] > strtotime("now")){
                //$event->getPlayer()->kick("Your Still Banned!");
                $timeleft = $this->bans[$event->getPlayer()->getName()] - strtotime("now");
                $a = gmdate("H:i:s", $timeleft);
                $event->setJoinMessage($event->getPlayer()->getName() . " Is Banned For $a !");
                $task = New KickPlayer($this, $event->getPlayer());
                $this->getServer()->getScheduler()->scheduleDelayedTask($task, (20*3));
                $event->getPlayer()->sendMessage("Your Still Banned for $a");
                $event->getPlayer()->sendMessage("Your Still Banned for $a");
                $event->getPlayer()->sendMessage("Your Still Banned for $a");
                $event->getPlayer()->sendMessage("Your Still Banned for $a");
                $event->getPlayer()->sendMessage("Your Still Banned for $a");
                $event->getPlayer()->sendMessage("Your Still Banned for $a");
                $event->getPlayer()->sendMessage("Your Still Banned for $a");
                $event->getPlayer()->sendMessage("Your Still Banned for $a");
                $event->getPlayer()->sendMessage("Your Still Banned for $a");
                $event->getPlayer()->sendMessage("Your Still Banned for $a");
                $event->getPlayer()->sendMessage("Your Still Banned for $a");
                $event->getPlayer()->sendMessage("Your Still Banned for $a");
                return true;
            }
        }else{
            if (isset($this->bans["IPS"][$event->getPlayer()->getAddress()])){
            foreach($this->bans["IPS"][$event->getPlayer()->getAddress()] as $f => $g){
                if ($g == $event->getPlayer()->getName()){
                    unset($this->bans['IPS'][$event->getPlayer()->getAddress()][$f]);
                }
            }
            }
        }
        //$this->bans["IPS"][$player->getAddress()][] = $playern;
        
        if (isset($this->bans["IPS"][$event->getPlayer()->getAddress()]) && count($this->bans["IPS"][$event->getPlayer()->getAddress()]) >= 3){
             //$event->getPlayer()->kick("Your Still Banned!");
                $event->setJoinMessage(null);
                $task = New KickPlayer($this, $event->getPlayer());
                $this->getServer()->getScheduler()->scheduleDelayedTask($task, (20*3));
                $event->getPlayer()->sendMessage("This IP Has Too Many Temp Bans! Try Again Later");
                $event->getPlayer()->sendMessage("This IP Has Too Many Temp Bans! Try Again Later");
                $event->getPlayer()->sendMessage("This IP Has Too Many Temp Bans! Try Again Later");
                $event->getPlayer()->sendMessage("This IP Has Too Many Temp Bans! Try Again Later");
                $event->getPlayer()->sendMessage("This IP Has Too Many Temp Bans! Try Again Later");
                $event->getPlayer()->sendMessage("This IP Has Too Many Temp Bans! Try Again Later");
                $event->getPlayer()->sendMessage("This IP Has Too Many Temp Bans! Try Again Later");
                $event->getPlayer()->sendMessage("This IP Has Too Many Temp Bans! Try Again Later");
                $event->getPlayer()->sendMessage("This IP Has Too Many Temp Bans! Try Again Later");
                $event->getPlayer()->sendMessage("This IP Has Too Many Temp Bans! Try Again Later");
                $event->getPlayer()->sendMessage("This IP Has Too Many Temp Bans! Try Again Later");
                $event->getPlayer()->sendMessage("This IP Has Too Many Temp Bans! Try Again Later");
                $event->getPlayer()->sendMessage("This IP Has Too Many Temp Bans! Try Again Later");
                return true;
        }
        
        if (!isset( $this->playerschan[$event->getPlayer()->getName()])){
            $this->playerschan[$event->getPlayer()->getName()] = "HOME";
        }
        $event->setJoinMessage(null);
        //$event->setJoinMessage("[+] ".$event->getPlayer()->getName());
        $event->getPlayer()->sendMessage("Use [ /ch help ] to View All Channel Commands!");
        return true;
    }
    
    public function OnPlayerLeave(PlayerQuitEvent $event) {
        $event->setQuitMessage(null);
        return true;
    }
    
    public function GetAmountOfPlayersInChannel($channel) {
        $x = 0;
        foreach($this->playerschan as $a){
            if ($a == $channel){
                $x++;
            }
        }
        return $x;
    }

    public function MuteChat(Player $p , $sec = null) {
        
    }
    
    public function OnBlockBreak(BlockBreakEvent $block) {
        $player = $block->getPlayer();
        if ($player->isOp()){
            return true;
        }else{
            if ($this->CheckIfProtechted($player->getX(), $player->getZ(), "edit")){
                $block->setCancelled();
                return true;
            }else{
                return true;
            }
        }
    }
    
    public function OnBlockPlace(BlockPlaceEvent $block) {
           $player = $block->getPlayer();
        if ($player->isOp()){
            return true;
        }else{
            if ($this->CheckIfProtechted($player->getX(), $player->getZ(), "edit")){
                $block->setCancelled();
            }else{
                return true;
            }
        }
    }
    
    public function PlayerDamage(EntityDamageEvent $event) {
        if($event->getEntity() instanceof Player){
            $player = $event->getEntity();
            if ($this->CheckIfProtechted($player->getX(), $player->getZ(), "pvp") && $this->purge == false){
                $event->setCancelled();
            }
        }
    }
    
    public function CheckIfProtechted($x,$z,$damage) {
        if (!isset($this->yml['Areas'])){
            return false;
        }
        foreach($this->yml['Areas'] as $a){
            if ((min($a['x1'],$a['x2']) <= $x) && (max($a['x1'],$a['x2']) >= $x)){
                if ((min($a['z1'],$a['z2']) <= $z) && (max($a['z1'],$a['z2']) >= $z)){
                    if ($a[$damage] == "false"){
                        return TRUE;
                    }else{
                        return false;
                    }
                }
            }
        }
    }
    
    /**
     * Did Player Mute Chat For them Self?
     * 
     * @param Player $player
     * @return Boolean If Player Has Muted them self then it returns True
     */
    public function PlayerMutedChat(Player $player) {
        if(isset($this->pmc[$player->getName()]) && $this->pmc[$player->getName()] == TRUE){
            return true;
        }else{
            return false;
        }
    }
    
    public $muted;
    /**
     * Has an admin Muted You???
     * 
     * @param Player $player Player
     * @return boolean Returns True if Is Muted
     */
    public function IsPlayerMuted(Player $player) {
        if ( isset($this->muted[$player->getName()]) && $this->muted[$player->getName()] == true){
            return true;
        }else{
            return false;
        }
    }
    /**
     *  @param PlayerRespawnEvent $event
     *  @priority HIGHEST
     *  @final TRUE
     */
    public function OnPlayerChat(PlayerChatEvent $event) {
        $channel = $this->GetPlayersChannel($event->getPlayer());
        $message = $this->SetFormat($event->getMessage(), $event->getPlayer());
        $sender = $event->getPlayer();
        $event->setCancelled(TRUE);
        if (!isset($this->spam[$sender->getName()])){
            $this->spam[$sender->getName()] = 0;
        }else{
            $this->spam[$sender->getName()]++;
        }
        $this->isPlayerSpamming($sender);
        if ($this->IsPlayerMuted($event->getPlayer()) == FALSE ){
            if ($this->chatoff){
                if (!$sender->isOp()){
                $sender->sendMessage("Chat Is Disabled! Please Wait...");
                return true;
                }
            }
            $loggedps = $this->getServer()->getOnlinePlayers();
            foreach ($loggedps as $p){
                $pchannel = $this->GetPlayersChannel($p);
                if ($channel == $pchannel){
                    if ($this->PlayerMutedChat($p) != TRUE){
                        //$message = $this->SetFormat($message, $event->getPlayer());
                        $p->sendMessage($message);
                        //$this->getLogger()->info($message);
                        //$this->getLogger()->log($level, $message);
                    }else{
                        
                    }
                }
            }
            Server::getInstance()->getLogger()->info("{ $channel } ". $message);
            return true;
        }else{
            $event->getPlayer()->sendMessage("Um... Your Muted. Try again Later!");
            return true;
        }
    }
    
    public function isPlayerSpamming(Player $player){
        if (isset($this->spam[$player->getName()]) && $this->spam[$player->getName()] >= 3){
        $this->getServer()->getScheduler()->scheduleTask(new Spam($this, $player));
        return true;
        }
    }
    
    
   //$this->playerschan["yungtech"] = "HOME"
    public function GetPlayersChannel(Player $p){
        $playern = $p->getName();
        if (isset($this->playerschan[$playern]) && !empty($this->playerschan[$playern]) && $this->playerschan[$playern] !== ""){
            return $this->playerschan[$playern];
        }else{
            $this->playerschan[$playern] = "HOME";
            return "HOME";
        }
    }

    public function SetFormat($message, Player $player){
        
        $format = $this->yml["format"];
        $a = str_replace("%prefix", $this->GetPlayerPrefix($player), $format);
        $b = str_replace("%player", $player->getName(), $a);
        $c = str_replace("%message", $message, $b);
        if (isset($this->faction) && $this->faction->isInFaction($player->getName())){
            $ff = $this->faction->getPlayerFaction($player->getName());
        }else{
            $ff = "NF";
        }
        $d = str_replace("%faction", $ff, $c);
        return $d;
        
    }
    
    public function GetPlayerPrefix(Player $player) {
        if(isset($this->yml["prefixs"][$player->getName()])){
            return $this->yml["prefixs"][$player->getName()];
        }else{
            return "Steve";
        }
    }
    
    public function CreateChannelInitals() {
        foreach($this->yml['channels'] as $k => $c){
            if ($c['active'] == TRUE){
                $in = strtoupper($c['initals']);
                $this->yml['channels-initals'][$in] = strtoupper($k);
            }
        }
        $this->SaveYML();
        return true;
    }


    public function loadYml(){
        @mkdir($this->getServer()->getDataPath() . "/plugins/CC/");
        $this->yml = (new Config($this->getServer()->getDataPath() . "/plugins/CC/" . "Data.yml", Config::YAML ,array(
            "channels"=>array(
                'HOME' => array(
                    'active'=>true,
                    'limit'=>null,
                    'initals'=>"HM",
                    'description'=>"Main Chat Lobby Where you spawn",
                    'welcom_message'=>"Welcome to %c! Please use /ch list to view all channels"
                ),
                'LOBBY1' => array(
                    'active'=>true,
                    'limit'=>null,
                    'initals'=>"L1",
                    'description'=>"Da' 1st Lobby!",
                    'welcom_message'=>"Welcome to %c! Please use /ch list to view all channels"
                ),
                'LOOKING_FOR_FACTIONS' => array(
                    'active'=>true,
                    'limit'=>null,
                    'initals'=>"LFF",
                    'description'=>"Chat for all lookng for a faction!",
                    'welcom_message'=>"Welcome to %c! Please use /ch list to view all channels"
                )
            ),
            "channels-initals"=>array(
            ),
            "format" => "[%prefix][%faction]<%player>: %message",
            "default-prfix" => "STEVE",
            "spammer"=>array()
        )))->getAll();
        $this->bans = (new Config($this->getServer()->getDataPath() . "/plugins/CC/" . "Bans.yml", Config::YAML ,array()))->getAll();
        $this->pips = (new Config($this->getServer()->getDataPath() . "/plugins/CC/" . "Player-IP.yml", Config::YAML ,array()))->getAll();
        $this->homes = (new Config($this->getServer()->getDataPath() . "/plugins/CC/" . "homes.yml", Config::YAML ,array()))->getAll();
        //$this->SaveYML();
       // $this->data = (new Config($this->getServer()->getDataPath() . "/plugins/PK/" . "Data.yml", Config::YAML ,array()))->getAll();
       return true;
    }
    
    public function SaveYML() {
        $a = new Config($this->getServer()->getDataPath() . "/plugins/CC/" . "Data.yml", Config::YAML);
        $a->setAll($this->yml);
        $a->save();
        
        $b = new Config($this->getServer()->getDataPath() . "/plugins/CC/" . "Bans.yml", Config::YAML);
        $b->setAll($this->bans);
        $b->save();
        
        $c = new Config($this->getServer()->getDataPath() . "/plugins/CC/" . "Player-IP.yml", Config::YAML);
        $c->setAll($this->pips);
        $c->save();
        
        $d = new Config($this->getServer()->getDataPath() . "/plugins/CC/" . "homes.yml", Config::YAML);
        $d->setAll($this->homes);
        $d->save();
        return true;
    }
   }
