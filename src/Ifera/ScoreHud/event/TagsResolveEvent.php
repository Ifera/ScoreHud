<?php
declare(strict_types = 1);

namespace Ifera\ScoreHud\event;

/**
 * This event is called when player joins the server or
 * when there is an update to send to players for which all tags
 * need to be known, for e.g. upon changing world the scoreboard
 * needs to be reconstructed.
 *
 * This event is not meant for sending timely updates to ScoreHud.
 * For handling timely updates to score lines separate events are
 * there for use.
 *
 * Note:
 *
 * All plugins using ScoreHud must implement this event and send it
 * the update for the tags being called. This works very much the same
 * way as HRKChat.
 */
class TagsResolveEvent extends ScoreHudEvent{
}