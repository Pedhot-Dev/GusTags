<?php

namespace PedhotDev\GusTags\sessions;

use PedhotDev\GusTags\Main;
use PedhotDev\GusTags\tags\Tag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class Session
{

    public function __construct(private Player $player, private array $properties) {}

    public function getPlayer(): Player {
        return $this->player;
    }

    public function getProperties(): array {
        return $this->properties;
    }

    public function getEquippedTag(): ?Tag {
        return Main::getInstance()->getTagManager()->getTag(($this->properties["equipped-tag"] ?? $this->player->getXuid()));
    }

    public function setEquippedTag(Tag|string $tag): bool {
        if ($tag instanceof Tag) {
            $tag = $tag->getName();
        }
        $tag = strtolower($tag);
        $this->properties["equipped-tag"] = Main::getInstance()->getTagManager()->exists($tag) ? $tag : null;
        return true;
    }

    /**
     * @return ?Tag[]
     */
    public function getPurchasedTags(): ?array {
        $purchasedTags = $this->properties["purchased-tags"] ?? [];
        if (empty($purchasedTags)) return null;
        return array_map(fn(string $tag) => Main::getInstance()->getTagManager()->getTag($tag), $purchasedTags);
    }

    public function setPurchasedTags(array $tags): void {
        $this->properties["purchased-tags"] = $tags;
    }
    
    /**
     * @return Tag[]
     */
    public function getUnpurchasedTags(): array {
        $purchasedTags = $this->properties["purchased-tags"] ?? [];
        if (empty($purchasedTags)) {
            return Main::getInstance()->getTagManager()->getTags();
        }
        $tags = Main::getInstance()->getTagManager()->getTags();
        foreach ($purchasedTags as $purchasedTag) {
            if (Main::getInstance()->getTagManager()->exists($purchasedTag)) {
                unset($tags[$purchasedTag]);
            }
        }
        return $tags;
    }

    public function buyTag(Tag $tag): bool {
        if (in_array($tag->getName(), array_keys($this->getPurchasedTags()))) {
            return false;
        }
        $tags = $this->getPurchasedTags();
        $tags[] = $tag;
        $this->setPurchasedTags($tags);
        return true;
    }

}