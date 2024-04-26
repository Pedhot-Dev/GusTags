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

use DaPigGuy\libPiggyEconomy\libPiggyEconomy;
use DaPigGuy\libPiggyEconomy\providers\EconomyProvider;
use PedhotDev\GusTags\forms\FormManager;
use PedhotDev\GusTags\sessions\SessionManager;
use PedhotDev\GusTags\tags\TagManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase {
	use SingletonTrait {
		setInstance as private;
		reset as private;
	}

	private Config $tagConfig;

	private TagManager $tagManager;

	private SessionManager $sessionManager;

	private FormManager $formManager;

	private EconomyProvider $economyProvider;

	protected function onLoad() : void {
		self::setInstance($this);
	}

	protected function onEnable() : void {
		$this->saveDefaultConfig();
		$this->saveResource("tags.yml");
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->tagConfig = new Config($this->getDataFolder() . "tags.yml");
		$this->tagManager = new TagManager($this);
		$this->sessionManager = new SessionManager($this);
		$this->formManager = new FormManager($this);
		libPiggyEconomy::init();
		$this->economyProvider = libPiggyEconomy::getProvider($this->getConfig()->get("economy"));
	}

	public function getTagConfig() : Config {
		return $this->tagConfig;
	}

	public function getTagManager() : TagManager {
		return $this->tagManager;
	}

	public function getSessionManager() : SessionManager {
		return $this->sessionManager;
	}

	public function getEconomyProvider() : EconomyProvider {
		return $this->economyProvider;
	}

}
