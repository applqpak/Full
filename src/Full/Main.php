<?php

  namespace Full;
  
  use pocketmine\plugin\PluginBase;
  use pocketmine\event\Listener;
  use pocketmine\utils\TextFormat as TF;
  use pocketmine\utils\Config;
  use pocketmine\event\player\PlayerPreLoginEvent;
  use pocketmine\command\Command;
  use pocketmine\command\CommandSender;
  
  class Main extends PluginBase implements Listener
  {
  
    const PREFIX = TF::RED . "[" . TF::AQUA . "Full" . TF::RED . "] " . TF::RESET;
  
    public function dataPath()
    {
    
      return $this->getServer()->getDataFolder();
    
    }
    
    public function server()
    {
    
      return $this->getServer();
    
    }
    
    public function onlinePlayers()
    {
    
      return $this->server()->getOnlinePlayers();
    
    }
    
    public function countOnlinePlayers()
    {
    
      return count($this->server()->onlinePlayers());
    
    }
    
    public function logger()
    {
    
      return $this->getLogger();
    
    }
    
    public function pluginManager()
    {
    
      return $this->server()->getPluginManager();
    
    }
    
    public function onEnable()
    {
    
      $this->server()->pluginManager()->registerEvents($this, $this);
      
      $this->logger()->info("Enabled.");
      
      $this->cfg = new Config($this->dataPath() . "config.yml", Config::YAML, array("join_permission" => "full.join", "no_permission" => "You don't have permission to join!"));
    
    }
    
    public function onDisable()
    {
    
      $this->logger->info("Disabled.");
    
    }
    
    public function onPreLogin(PlayerPreLoginEvent $event)
    {
    
      $join_permission = $this->cfg->get("join_permission");
      
      $no_permission = $this->cfg->get("no_permission");
    
      $player = $event->getPlayer();
      
      if($this->countOnlinePlayers() === 75)
      {
      
        if(!($player->hasPermission($join_permission)))
        {
        
          $player->kick($no_permission, false);
          
          $event->setCancelled();
        
        }
      
      }
    
    }
    
    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args)
    {
    
      switch(strtolower($cmd->getName()))
      {
      
        case "full":
        
          if(!(isset($args[0]) and isset($args[1])))
          {
          
            $sender->sendMessage(PREFIX . "You must enter the sub-commands.");
            
            $sender->sendMessage(PREFIX . "For help use /full help");
            
            return true;
          
          }
          else
          {
          
            switch(strtolower($args[0]))
            {
            
              case "permission":
              
                $newPermission = $args[1];
                
                $this->cfg->set("join_permission", $newPermission);
                
                $this->cfg->save();
                
                $sender->sendMessage(PREFIX . "Successfully set the new permission to " . $newPermission . ".");
                
                return true;
              
              break;
              
              case "nopermission":
              
                if(isset($args[2]))
                {
                
                  unset($args[0]);
                  
                  unset($args[1]);
                  
                  $no_permission_message = implode(" ", $args);
                  
                  $this->cfg->set("no_permission", $no_permission_message);
                  
                  $this->cfg->save();
                  
                  $sender->sendMessage(PREFIX . "Successfully set the no permission message to " . $no_permission_message . ".");
                  
                  return true;
                
                }
                else
                {
                
                  $sender->sendMessage(PREFIX . "You must enter the no permission message.");
                  
                  return true;
                
                }
              
              break;
              
              case "help":
              
                $sender->sendMessage(PREFIX . "Help");
                
                $sender->sendMessage("/full permission <permission> - Sets the permission.");
                
                $sender->sendMessage("/full nopermission < message > - Sets the no permission message.");
                
                return true;
              
              break;
            
            }
          
          }
        
        break;
      
      }
    
    }
  
  }
  
  
?>