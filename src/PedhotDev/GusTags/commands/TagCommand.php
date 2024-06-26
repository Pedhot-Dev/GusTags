<?php

/*
 * _____         _ _           _   _____
 * |  __ \       | | |         | | |  __ \
 * | |__) |__  __| | |__   ___ | |_| |  | | _____   __
 * |  ___/ _ \/ _` | '_ \ / _ \| __| |  | |/ _ \ \ / /
 * | |  |  __/ (_| | | | | (_) | |_| |__| |  __/\ V /
 * |_|   \___|\__,_|_| |_|\___/ \__|_____/ \___| \_/
 *
 * Copyright 2024 PedhotDev
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * 	http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

declare(strict_types=1);

namespace PedhotDev\GusTags\commands;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use PedhotDev\GusTags\Main;
use pocketmine\command\CommandSender;

class TagCommand extends BaseCommand {

	protected function prepare() : void {
		$this->setPermission("gustags.tag.command");
		$this->addConstraint(new InGameRequiredConstraint($this));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
		Main::getInstance()->getFormManager()->sendFormEquipTag($sender);
	}

}
