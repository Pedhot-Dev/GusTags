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

use IvanCraft623\RankSystem\session\Session as RSSession;
use IvanCraft623\RankSystem\tag\Tag as RSTag;
use IvanCraft623\RankSystem\tag\TagManager as RSTagManager;
use PedhotDev\GusTags\Main;
use PedhotDev\GusTags\tags\Tag;
use pocketmine\player\Player;
use function array_keys;
use function array_map;
use function in_array;
use function is_string;

class Session
{

	public function __construct(private Player $player, private array $properties) {
		$purchasedTags = $this->properties["purchased_tags"];
		foreach ($purchasedTags as $tag) {
			if (!Main::getInstance()->getTagManager()->exists($tag)) return;
			$this->properties["purchased_tags"][$tag] = Main::getInstance()->getTagManager()->getTag($tag);
		}
		$this->setPurchasedTags(array_map(fn(string $tag) => Main::getInstance()->getTagManager()->getTag($tag), $purchasedTags));
		$this->setEquippedTag($this->properties["equipped_tag"] ?? $this->player->getXuid());
		$equippedTag = $this->getEquippedTag();
		RSTagManager::getInstance()->registerTag(new RSTag("gustags.tag", static function(RSSession $user) use ($equippedTag) : string {
			return $equippedTag == null ?: $equippedTag->getDisplayName();
		}));
	}

	public function getPlayer() : Player {
		return $this->player;
	}

	public function getProperties() : array {
		return $this->properties;
	}

	public function getEquippedTag() : ?Tag {
		return $this->properties["equipped_tag"];
	}

	public function setEquippedTag(Tag|string $tag) : bool {
		if (is_string($tag)) {
			$tag = Main::getInstance()->getTagManager()->getTag($tag);
		}
		$this->properties["equipped_tag"] = $tag;

		return true;
	}

	/**
	 * @return Tag[]
	 */
	public function getPurchasedTags() : array {
		$purchasedTags = $this->properties["purchased_tags"] ?? [];
		if (empty($purchasedTags)) return [];
		// $tags = [];
		// foreach ($purchasedTags as $tag) {
		// 	$tags[strtolower($tag)] = Main::getInstance()->getTagManager()->getTag($tag);
		// }
		// return $tags;
		// return array_map(fn(string $tag) => Main::getInstance()->getTagManager()->getTag($tag), $purchasedTags);
		return $purchasedTags;
	}

	public function setPurchasedTags(array $tags) : void {
		$this->properties["purchased_tags"] = $tags;
	}

	/**
	 * @return Tag[]
	 */
	public function getUnpurchasedTags() : array {
		$purchasedTags = $this->properties["purchased_tags"] ?? [];
		if (empty($purchasedTags)) {
			return Main::getInstance()->getTagManager()->getTags();
		}
		$tags = Main::getInstance()->getTagManager()->getTags();
		foreach ($purchasedTags as $purchasedTag) {
			if ($purchasedTag instanceof Tag) {
				unset($tags[$purchasedTag->getName()]);
			}
		}
		return $tags;
	}

	public function buyTag(Tag $tag) : bool {
		if (in_array($tag->getName(), array_keys($this->getPurchasedTags()), true)) {
			return false;
		}
		$tags = $this->getPurchasedTags();
		$tags[] = $tag;
		$this->setPurchasedTags($tags);
		return true;
	}

}