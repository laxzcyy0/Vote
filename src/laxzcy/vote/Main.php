<?php

declare(strict_types=1;

namespace laxzcy\vote;

use pocketmine\plugin\PluginBase;
use laxzcy\vote\command\VoteCommand;
use laxzcy\vote\manager\VoteManager;

class Main extends PluginBase {

    private VoteManager $voteManager;

    protected function onEnable(): void {
        $this->saveDefaultConfig();
        $this->voteManager = new VoteManager($this);
        $this->getServer()->getCommandMap()->register("oy", new VoteCommand($this));
    }

    public function getVoteManager(): VoteManager {
        return $this->voteManager;
    }
}
