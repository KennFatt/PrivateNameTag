<?php

namespace kennan;

/**
 * Created by @KennFatt
 * Released on: 02 April 2017
 * Description: A Plugin to hide your real Nametag (usually for xbox authenticate server)
 */
class Main extends \pocketmine\plugin\PluginBase implements \pocketmine\event\Listener
{

  /** @var array */
  private $cache = [];

  /** @var Config */
  public $config;

  const REAL_NAMETAG = 0;
  const PRIVATE_NAMETAG = 1;

  public function onEnable()
  {
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    $this->saveResource("config.yml");
    $this->loadSetting();
    $this->getLogger()->info("§l§o§bPrivateNametag by @KennFatt has been enabled!§r");
  }

  public function onCommand(\pocketmine\command\CommandSender $sender, \pocketmine\command\Command $command, $l, array $args)
  {
    if ($command->getName() == 'pnt' or $command->getName() == 'privatename') {
      if (!$sender instanceof \pocketmine\Player) {
        $sender->sendMessage("§o§ePlease run this command inside a game!§r");
        return true;
      }
      if (!$sender->hasPermission('kennan.pnt.cmd')) {
        $sender->sendMessage("§o§cYou dont have permission to do that!§r");
        return true;
      }

      $this->setPrivateNametag($sender, $this->config->get('show.current'));
      return true;
    }
  }

  public function onLogin(\pocketmine\event\player\PlayerLoginEvent $e)
  {
    if ($this->config->get('enable.onlogin')) {
      $this->setPrivateNametag($e->getPlayer());
    }
  }

  /**
   * Load all plugin's files
   */
  public function loadSetting()
  {
    if (!is_dir($this->getDataFolder())) {
      mkdir($this->getDataFolder());
    }

    $this->config = new \pocketmine\utils\Config($this->getDataFolder()."config.yml", \pocketmine\utils\Config::YAML, [
      'replace.with' => "§k0",
      'show.current' => false,
      'enable.onlogin' => true,
    ]);
  }

  /**
   * Get a cache
   * @return array
   */
  public function getCache() : array
  {
    return $this->cache;
  }

  /**
   * Check if target is already used private nametag
   * @param Player
   * @return boolean
   */
  public function isInPrivate(\pocketmine\Player $target) : bool
  {
    return array_key_exists($target->getName(), $this->getCache());
  }

  /**
   * Get player nametag
   * @param Player
   * @param int
   * @return string
   */
  public function getNameTag(\pocketmine\Player $target, int $value) : string
  {
    switch ($value) {
      case Main::REAL_NAMETAG:
      if (!$this->isInPrivate($target)) {
        return $target->getNameTag();
      }
      return $this->cache[$target->getName()][0];
      break;

      case Main::PRIVATE_NAMETAG:
      if (!$this->isInPrivate($target)) {
        return $target->getNameTag();
      }
      return $this->cache[$target->getName()][1];
      break;

      default:
      $this->getLogger()->notice("An error detected! @param ".$target->getName()." @param ".$value);
      return "";
      break;
    }
  }

  /**
   * Processing system
   * @param Player
   * @param boolean
   *
   * Boolean will check if this function working properly or not
   * @return boolean
   */
  public function setPrivateNametag(\pocketmine\Player $target, $showMsg = false) : bool
  {
    if ($this->isInPrivate($target)) {
      $this->revertNameTag($target);
      if ($showMsg) {
        $target->sendMessage("§o§6Current NameTag:§r ".$this->getNameTag($target, Main::PRIVATE_NAMETAG)."§r");
      }
      return false;
    }

    $pnt = "";

    $this->cache[$target->getName()] = [$target->getNameTag()]; //TODO try hacky way ($this->getNameTag($target))
    for ($i=1; $i <= strlen($target->getNameTag()); $i++) {
      $pnt .= $this->config->get('replace.with');
    }
    array_push($this->cache[$target->getName()], $pnt);
    $target->setNameTag($pnt);
    $target->sendMessage("§o§6Your NameTag has been updated to private!");
    $pnt = "";
    return true;
  }

  /**
   * Reset player's name tag
   * @param Player
   *
   * Will return to boolean and check if this function are working properly or not
   * @return boolean
   */
  public function revertNameTag(\pocketmine\Player $target) : boolean
  {
    if (!$this->isInPrivate($target)) {
      $target->sendMessage("§o§6You are using default NameTag!");
      return false;
    }

    $target->setNameTag($this->getNameTag($target, Main::REAL_NAMETAG));
    unset($this->cache[$target->getName()]);
    $target->sendMessage("§o§6NameTag has been reverted!");
    return true;
  }
}
