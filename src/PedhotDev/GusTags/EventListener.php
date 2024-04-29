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

namespace PedhotDev\GusTags;

use PedhotDev\GusTags\tags\Tag;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use function array_map;
use function is_string;
use function str_replace;
use function strtolower;

class EventListener implements Listener {

	public function __construct(private Main $plugin) {}

	public function onJoin(PlayerJoinEvent $event) {
		$player = $event->getPlayer();
		$nameTag = $player->getNameTag();
		$displayName = $player->getDisplayName();
		$sessionManager = $this->plugin->getSessionManager();
		$sessionManager->register($player, $this->plugin->getDatabase()->get(strtolower($player->getName()), ["purchased_tags" => []]));
		$equippedTag = $sessionManager->getSession($player)->getEquippedTag();
		$player->setNameTag(str_replace("{gustags.tag}", $equippedTag == null ? "" : $equippedTag->getDisplayName(), $nameTag));
		$player->setDisplayName(str_replace("{gustags.tag}", $equippedTag == null ? "" : $equippedTag->getDisplayName(), $displayName));
	}

	public function onQuit(PlayerQuitEvent $event) {
		$player = $event->getPlayer();
		$sessionManager = $this->plugin->getSessionManager();
		$session = $sessionManager->getSession($player);
		$equippedTag = $session->getProperties()["equipped_tag"];
		switch (true) {
			case $equippedTag instanceof Tag:
				$tag = strtolower($equippedTag->getName());
				break;
			case is_string($equippedTag):
				$tag = strtolower($equippedTag);
				break;
		}
		$properties["equipped_tag"] = $tag;
		$properties["purchased_tags"] = array_map(fn (Tag $tag) => $tag->getName(), $session->getProperties()["purchased_tags"]);
		$this->plugin->getDatabase()->set(strtolower($player->getName()), $properties);
	}

}
