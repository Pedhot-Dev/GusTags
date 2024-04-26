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

namespace PedhotDev\GusTags\forms;

use PedhotDev\GusTags\Main;
use PedhotDev\GusTags\tags\Tag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use Vecnavium\FormsUI\SimpleForm;

class FormManager {

	private $buyTagCallable, $equipTagCallable, $confirmBuyTagCallable, $confirmEquipTagCallable;

	public function __construct(private Main $plugin) {
		$this->buyTagCallable = function (Player $player, $data) : void {
			if ($data == null) return;
			if ($data === 0) return;
			$tag = $this->plugin->getTagManager()->getTag($data);
			if (!$tag instanceof Tag) return;
			$this->sendFormConfirmation($player, $tag, true);
		};
		$this->equipTagCallable = function (Player $player, $data) : void {
			if ($data == null) return;
			if ($data === 0) return;
			$tag = $this->plugin->getTagManager()->getTag($data);
			$this->sendFormConfirmation($player, $tag, false);
		};
		$this->confirmBuyTagCallable = function (Player $player, $data) : void {
			if ($data == null) return;
			if ($data === 1) return;
			$tag = $this->plugin->getTagManager()->getTag($data);
			$this->plugin->getEconomyProvider()->takeMoney($player, $tag->getPrice(), function (bool $success) use ($player, $tag) {
				if ($success) {
					if ($this->plugin->getSessionManager()->getSession($player)->buyTag($tag)) {
						$player->sendMessage("Tag " . $tag->getDisplayName() . TextFormat::RESET . " telah terbeli, anda sekarang bisa menggunakannya dengan menggunakan command /tag use");
					}
				}else {
					$player->sendMessage("Gagal membeli tag dengan alasan yang tidak diketahui");
				}
			});
		};
		$this->confirmEquipTagCallable = function (Player $player, $data) : void {
			if ($data == null) return;
			if ($data === 1) return;
			$tag = $this->plugin->getTagManager()->getTag($data);
			$this->plugin->getSessionManager()->getSession($player)->setEquippedTag($tag);
			$player->sendMessage("Berhasil menggunakan tag " . $tag->getDisplayName());
		};
	}

	public function sendFormBuyTag(Player $player) : void {
		$form = new SimpleForm($this->buyTagCallable);
		$form->setTitle("Beli tag");
		$form->addButton("Keluar");
		foreach ($this->plugin->getSessionManager()->getSession($player)->getUnpurchasedTags() as $tag) {
			$form->addButton($tag->getDisplayName() . TextFormat::RESET . "\n" . $this->plugin->getEconomyProvider()->getMonetaryUnit() . $tag->getPrice(), -1, "", $tag->getName());
		}
		$form->sendToPlayer($player);
	}

	public function sendFormEquipTag(Player $player) : void {
		$form = new SimpleForm($this->equipTagCallable);
		$form->setTitle("Gunakan tag");
		$form->setContent($this->plugin->getSessionManager()->getSession($player)->getEquippedTag() == null ? "" : "Sekarang anda menggunakan tag " . $this->plugin->getSessionManager()->getSession($player)->getEquippedTag());
		$form->addButton("Keluar");
		foreach ($this->plugin->getSessionManager()->getSession($player)->getPurchasedTags() as $tag) {
			$form->addButton($tag->getDisplayName() . TextFormat::RESET . "\n" . $this->plugin->getEconomyProvider()->getMonetaryUnit() . $tag->getPrice(), -1, "", $tag->getName());
		}
		$form->sendToPlayer($player);
	}

	public function sendFormConfirmation(Player $player, Tag $tag, bool $isBuy) : void {
		$form = new SimpleForm($isBuy ? $this->confirmBuyTagCallable : $this->confirmEquipTagCallable);
		$form->setTitle("Konfirmasi");
		$form->setContent($isBuy ? "Apakah anda yakin ingin membeli tag " . $tag->getDisplayName() . TextFormat::RESET . " seharga " . TextFormat::GREEN . $this->plugin->getEconomyProvider()->getMonetaryUnit() . $tag->getPrice() . TextFormat::RESET . "?\n\n\n\n\n\n" : "Apakah anda yakin ingin menggunakan tag " . $tag->getDisplayName() . TextFormat::RESET . "?\n\n\n\n\n\n");
		$form->addButton("Ya", -1, "", $tag->getName());
		$form->addButton("Tidak");
		$form->sendToPlayer($player);
	}

}
