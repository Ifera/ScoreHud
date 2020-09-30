<?php
declare(strict_types = 1);

namespace Ifera\ScoreHud\event;

/**
 * Call this event when you need to change a tag
 * that is independent of the player. For example, for
 * displaying server TPS or Load or any other data.
 *
 * Use PlayerTagUpdateEvent in case the tag depends on
 * the player.
 *
 * Call this event, pass the tag that needs updating into the
 * constructor and let ScoreHud handle the rest.
 */
class ServerTagUpdateEvent extends ScoreHudEvent{
}