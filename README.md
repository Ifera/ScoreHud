# ScoreHud

>## Notice: <br />
> **Backwards Incompatible Update**
> Starting from **ScoreHud version 6.0**, addons are no longer supported. <br />
> All old addons will stop working on versions 6.0 and above. <br />
> All old tags will stop functioning as well. <br />
> More details can be found below <br />
> **Please read FAQs below.**

| HitCount | License | Poggit | Release |
|:--:|:--:|:--:|:--:|
|[![HitCount](http://hits.dwyl.io/Ifera/ScoreHud.svg)](http://hits.dwyl.io/Ifera/ScoreHud)|[![GitHub license](https://img.shields.io/github/license/Ifera/ScoreHud.svg)](https://github.com/Ifera/ScoreHud/blob/master/LICENSE)|[![Poggit-CI](https://poggit.pmmp.io/ci.shield/JackMD/ScoreHud/ScoreHud)](https://poggit.pmmp.io/ci/JackMD/ScoreHud/ScoreHud)|[![](https://poggit.pmmp.io/shield.state/ScoreHud)](https://poggit.pmmp.io/p/ScoreHud)|

### A highly customizable plugin to add Scoreboards on your Minecraft Bedrock Server.

### Features:

 - This plugin adds **scoreboard** to your server.
 - Completely **Event Driven**!! Good bye tasks!
 - Everything is customizable.
 - Easy and simple API for developers to use and integrate ScoreHud.
 - You can find some basic tags via [ScoreHudX](https://github.com/Ifera/ScoreHudX) plugin.
 
### How to setup?

 - Put the plugin in your plugins folder.
 - Start and then stop the server.
 - Edit the `config.yml` and `scorehud.yml` to suit your needs.
 - Restart and enjoy.
 
### Version 6.0 Update:

The main goal of version 6.0 update was to remove ScoreHud dependency pocketmine tasks. So that was achieved by making
ScoreHud **event driven**! <br /><br />

**But what exactly does event driven mean? And what are its benefits?** <br />
Well, its simple, instead of using tasks to update values on scoreboard every 20 or so ticks even when there was no 
update, ScoreHud now listens for events that are fired by the plugin which implements ScoreHud. In doing so, the scoreboard 
is only updated when there is an actual update and not the other way round. Although not tested but this should improve 
ScoreHud's performance.<br /><br />

Addon support was removed with version 6.0 update. Reason being that it was no longer feasible and users had reported addons 
not working on different systems either due to hosting problem or some other reasons. Removing addons and listening to events 
instead should now work on almost all systems.<br /><br />

ScoreHud using events is in the benefit of plugin developers and the end-user as well. Users no longer will need to download 
and place addons in separate folder. And plugin developers will no longer need to make a separate addon for ScoreHud. They 
can just integrate ScoreHud directly into their plugin and fire events to update their tags on ScoreHud. 

### FAQs:

**Q: How many lines can I set in `default-board`?**<br />
A: You can set 1 to 15 lines in `default-board`. <br /><br />
**Q: How many `server-names` can I set?**<br />
A: You can set infinite many number of `server-names`. <br /><br />
**Q: I am having problems and cannot set the plugin correctly. What do I do?**<br />
A: Well you can always open a new issue on this repository or contact me via Discord: `Ifera#3717` or Twitter: `@ifera_tr`. <br /><br />
**Q: I like the plugin. What do I do?**<br />
A: Well, that is extremely fortunate. Why not to star this repository and might as well a good review on [poggit](https://poggit.pmmp.io/p/ScoreHud).<br /><br />
**Q: What happened to addons?**<br />
A: Addons system being simple and similar to pocketmine had some flaws especially when combined with tasks caused a great deal of lag. ScoreHud is now event driven and no longer depends on tasks, so it was only fitting to remove addon support as a whole. <br /><br />

### For Developers:

 - Refer to [wiki](https://github.com/Ifera/ScoreHud/wiki).
