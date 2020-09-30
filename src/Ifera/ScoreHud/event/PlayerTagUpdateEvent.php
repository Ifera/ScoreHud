<?php
declare(strict_types = 1);

namespace Ifera\ScoreHud\event;

/**
 * Call this event when you need to change a tag
 * that belongs to a specific player. For example this
 * may be used for displaying player name, player rank,
 * money or whatever.
 *
 * Use ServerTagUpdateEvent in case the tag is independent of
 * the player.
 *
 * Call this event, pass the tag that needs updating into the
 * constructor and let ScoreHud handle the rest.
 */
class PlayerTagUpdateEvent extends ScoreHudEvent{
}