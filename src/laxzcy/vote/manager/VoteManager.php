<?php

declare(strict_types=1;

namespace laxzcy\vote\manager;

use onebone\economyapi\EconomyAPI;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\Internet;
use laxzcy\vote\Main;

class VoteManager {

    private Config $voteData;
    private string $apiKey;
    private string $webhookUrl;

    public function __construct(private Main $plugin) {
        $this->voteData = new Config($plugin->getDataFolder() . "votes.yml", Config::YAML);
        $this->apiKey = $plugin->getConfig()->get("api-key", "");
        $this->webhookUrl = $plugin->getConfig()->get("webhook-url", "");
    }

    public function hasVotedToday(string $name): bool {
        return $this->voteData->get($name) === date("Y-m-d");
    }

    public function checkAndClaim(Player $player): bool {
        $name = $player->getName();
        $url = "https://minecraftpocket-servers.com/api/?object=votes&element=claim&key={$this->apiKey}&username=" . urlencode($name);
        
        $response = Internet::getURL($url);
        if ($response === null || trim($response->getBody()) !== "1") {
            return false;
        }

        EconomyAPI::getInstance()->addMoney($player, 7500);
        $this->voteData->set($name, date("Y-m-d"));
        $this->voteData->save();
        
        $this->sendDiscordNotification($name);
        return true;
    }

    private function sendDiscordNotification(string $playerName): void {
        if ($this->webhookUrl === "") return;

        $payload = [
            "username" => "{$playerName}",
            "embeds" => [[
                "title" => "🗳️ Oy Bildirimi",
                "description" => "**{$playerName}** bugün oy verdi ve ödülünü aldı!",
                "color" => hexdec("00FF00"),
                "timestamp" => date("c")
            ]]
        ];

        Internet::postURL($this->webhookUrl, json_encode($payload), 10, ["Content-Type: application/json"]);
    }
}
