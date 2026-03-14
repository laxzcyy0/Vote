<?php

declare(strict_types=1;

namespace laxzcy\vote\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use laxzcy\vote\Main;

class VoteCommand extends Command {

    public function __construct(private Main $plugin) {
        parent::__construct("oy", "Oy vererek ödül almanı sağlar.", "/oy");
        $this->setPermission("vote.use");
        $this->setAliases(["vote"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage("§cBu komut sadece oyun içinde kullanılabilir.");
            return;
        }

        $manager = $this->plugin->getVoteManager();
        $name = $sender->getName();

        if ($manager->hasVotedToday($name)) {
            $sender->sendMessage("§8» §aBugün zaten oy verdin ve ödülünü aldın.");
            return;
        }

        if ($manager->checkAndClaim($sender)) {
            $sender->sendMessage("§aOy ödülü başarıyla alındı.");
            Server::getInstance()->broadcastMessage("§7»\n » §b{$name} §aoy verdi ve ödüllerini aldı! Hemen sende oy vererek ödüller kazanabilirsin.\n§7»");
        } else {
            $sender->sendMessage("§8» §cBugün oy vermemişsin!");
        }
    }
}
