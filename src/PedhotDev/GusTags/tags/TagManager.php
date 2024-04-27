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

namespace PedhotDev\GusTags\tags;

use PedhotDev\GusTags\Main;
use function strtolower;

class TagManager {

	/** @var Tag[] $array */
	private array $tags;

	public function __construct(private Main $plugin) {
		$this->registerAll();
	}

	public function getTag(string $name) : ?Tag {
		$name = strtolower($name);
		if (!$this->exists($name)) return null;
		return $this->tags[$name];
	}

	public function register(string $name, array $properties) : bool {
		$name = strtolower($name);
		if($this->exists($name)) return false;
		$this->tags[$name] = new Tag($name, $properties);
		return true;
	}

	public function unregister(string $name) : bool {
		$name = strtolower($name);
		if(!$this->exists($name)) return false;
		unset($this->tags[$name]);
		return true;
	}

	public function exists(?string $name) : bool {
		return $name === null ? false : isset($this->tags[strtolower($name)]);
	}

	public function registerAll() : void {
		$config = $this->plugin->getTagConfig();
		foreach ($config->getAll() as $name => $properties) {
			if (!$this->register(strtolower($name), $properties)) {
				$this->plugin->getLogger()->debug("Failed to register tag");
			}
		}
	}

	public function getTags() : array {
		return $this->tags;
	}

}