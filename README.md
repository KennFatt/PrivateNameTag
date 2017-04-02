# PrivateNameTag

Hide your players name tag into a private name tag.
If you enable XBox authentication for your server and you won't others player to add them, you can use this plugin. Keep your server feel premium!


Example Usage:
set a character you want to replace player's nametag on setting:
```
replace.with: "x"
```
and then, player's nametag will changed to "xxxx", if their original name tag is "Steve"(with length: 5), it will be change to "xxxxx" (also with same length as original)


Features: 
- Replace players name tag and hide it. (configurable)
- Switch on / off by typing command "/pnt".
- Enable on login. (configurable)


Note: Not tested with PureChat or others plugin. (this plugin is saved original name tag and set it to private, if you had a issue, try to set false on `enable.onlogin` setting)


## Command and Permission
```
Command: /pnt
Permission: kennan.pnt.cmd (default op)
```
