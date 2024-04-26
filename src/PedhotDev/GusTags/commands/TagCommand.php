<?php

namespace PedhotDev\GusTags\commands;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use PedhotDev\GusTags\Main;
use pocketmine\command\CommandSender;

class TagCommand extends BaseCommand {

    protected function prepare(): void {
        $this->setPermission("gustags.tag.command");
        $this->addConstraint(new InGameRequiredConstraint($this));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        Main::getInstance()->getFormManager()->sendFormEquipTag($sender);
    }

}