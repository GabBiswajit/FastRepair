<?php
namespace mercurypl\Command;

use mercurypl\FastRepair;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Armor;
use pocketmine\item\Tool;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;


class FixCommand extends Command {
    public function __construct()
    {
        parent::__construct("fix","Fix your inventory or the item what you are holding.",null,["repair"]);
        $this->setPermission('FastRepair.fix-all.use');
    }
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player){
            $file = FastRepair::getConfiguration("messages.yml");
            if ($sender->hasPermission("FastRepair.fix-all.use") || $sender->hasPermission("FastRepair.fix-all-others.use") || $sender->hasPermission("FastRepair.fix-hand.use")) {
    
}           
                $hold = $sender->getInventory()->getItemInHand();
                if (empty($args)){
                    $msg = TextFormat::colorize($file->get("fix-usage"));
                    $sender->sendMessage($msg);
                    return FALSE;
                }
                switch ($args[0]){
                    case "hand":
                        if ($sender->hasPermission("fix-hand.use")){
                            if ($hold instanceof Armor or $hold instanceof Tool){
                                if($hold->getDamage() > 0){
                                    $sender->getInventory()->setItemInHand($hold->setDamage(0));
                                    $msg = TextFormat::colorize($file->get("fix-hand-success"));
                                    $sender->sendMessage($msg);
                                    return FALSE;
                                }
                                $msg = TextFormat::colorize($file->get("fix-max-error"));
                                $sender->sendMessage($msg);
                                return FALSE;
                            }
                            $msg = TextFormat::colorize($file->get("fix-error"));
                            $sender->sendMessage($msg);
                            return FALSE;
                        }
                        $msg = TextFormat::colorize($file->get("non-permission"));
                        break;
                    case "all":
                        if (empty($args[1])){
                            if ($sender->hasPermission("fix-all.use")) {
                                foreach ($sender->getInventory()->getContents() as $index => $item) {
                                    if ($item instanceof Armor or $item instanceof Tool) {
                                        if ($item->getDamage() > 0) {
                                            $sender->getInventory()->setItem($index, $item->setDamage(0));
                                        }
                                    }
                                }
                                foreach ($sender->getArmorInventory()->getContents() as $index => $item) {
                                    if ($item instanceof Armor) {
                                        if ($item->getDamage() > 0) {
                                            $sender->getArmorInventory()->setItem($index, $item->setDamage(0));
                                        }
                                    }
                                }
                                $msg = TextFormat::colorize($file->get("fix-all-success"));
                                $sender->sendMessage($msg);
                                return FALSE;
                            }
                            $msg = TextFormat::colorize($file->get("non-permission"));
                            $sender->sendMessage($msg);
                            return FALSE;
                        }
                        if ($sender->hasPermission("fix-all-others.use")){
                            $player = FastRepair::getInstance()->getServer()->getPlayerByPrefix($args[1]);
                            if (is_null($player)){

                                $msg = str_replace("{player}",$args[1],$file->get("no-player-found"));
                                $msg = TextFormat::colorize($msg);
                                $sender->sendMessage($msg);
                                return FALSE;
                            }
                            foreach ($player->getInventory()->getContents() as $index => $item){
                                if ($item instanceof Armor or $item instanceof Tool){
                                    if($item->getDamage() > 0){
                                        $player->getInventory()->setItem($index,$item->setDamage(0));
                                    }
                                }
                            }
                            foreach ($sender->getArmorInventory()->getContents() as $index => $item) {
                                if ($item instanceof Armor) {
                                    if ($item->getDamage() > 0) {
                                        $sender->getArmorInventory()->setItem($index, $item->setDamage(0));
                                    }
                                }
                            }
                            $msg = TextFormat::colorize($file->get("fix-all-others-success"));
                            $msg = str_replace("{player}",$player->getName(),$msg);
                            $sender->sendMessage($msg);
                            $msg2 = TextFormat::colorize($file->get("fix-all-others-receiver"));
                            $msg2 = str_replace("{sender}",$sender->getName(),$msg2);
                            $player->sendMessage($msg2);
                            return FALSE;
                        }
                        $msg = TextFormat::colorize($file->get("non-permission"));
                        break;
                    default:
                        $msg = TextFormat::colorize($file->get("fix-usage"));
                        break;
                }
                $sender->sendMessage($msg);
                return FALSE;
            }
            $msg = TextFormat::colorize($file->get("non-permission"));
            $sender->sendMessage($msg);
        }
}
