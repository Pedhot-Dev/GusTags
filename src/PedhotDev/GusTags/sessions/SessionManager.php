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

namespace PedhotDev\GusTags\sessions;

use PedhotDev\GusTags\Main;
use pocketmine\player\Player;
use function strtolower;

class SessionManager {

	/** @var Session[] $sessions */
	private array $sessions;

	public function __construct(private Main $plugin) {}

	public function getSession(Player $player) : ?Session {
		$name = strtolower($player->getName());
		if (!$this->exists($player)) return null;
		return $this->sessions[$name];
	}

	public function register(Player $player, array $properties) : bool {
		$name = strtolower($player->getName());
		if($this->exists($player)) return false;
		$this->sessions[$name] = new Session($player, $properties);
		return true;
	}

	public function unregister(Player $player) : bool {
		$name = strtolower($player->getName());
		if(!$this->exists($player)) return false;
		unset($this->sessions[$name]);
		return true;
	}

	public function exists(Player $player) : bool {
		return isset($this->sessions[strtolower($player->getName())]);
	}

	public function getSessions() : array {
		return $this->sessions;
	}

}
