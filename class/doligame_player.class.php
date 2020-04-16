<?php

if (!class_exists('SeedObject'))
{
    /**
     * Needed if $form->showLinkedObjectBlock() is call or for session timeout on our module page
     */
    define('INC_FROM_DOLIBARR', true);
    require_once dirname(__FILE__).'/../config.php';
}


class DoligamePlayer extends SeedObject
{


    public $table_element = 'doligame_player';

    public $element = 'doligameplayer';

    public $isextrafieldmanaged = 1;

    public $ismultientitymanaged = 0;

    public $fk_user;
    public $level;
    public $total_xp;
    public $levelup_xp;
    public $entity;

    public $fields = array(

        'fk_user' => array(
            'type' => 'integer',
            'label' => 'User',
            'enabled' => 1,
            'visible' => 1,
            'notnull' => 1,
            'position' => 10
        ),'level' => array(
            'type' => 'integer',
            'label' => 'Level',
            'enabled' => 1,
            'visible' => 1,
            'notnull' => 1,
            'position' => 20
        ),'total_xp' => array(
            'type' => 'integer',
            'label' => 'TotalXp',
            'enabled' => 1,
            'visible' => 1,
            'notnull' => 1,
            'position' => 30
        ),'levelup_xp' => array(
            'type' => 'integer',
            'label' => 'LevelUpXp',
            'enabled' => 1,
            'visible' => 1,
            'notnull' => 1,
            'position' => 40
        ),'entity' => array(
            'type' => 'integer',
            'label' => 'Entity',
            'enabled' => 1,
            'visible' => 0,
            'notnull' => 1,
            'position' => 50
        ),

    );

    public function __construct($db)
    {
        global $conf;

        parent::__construct($db);

        $this->init();

        $this->level = 1;
        $this->total_xp = 0;
        $this->levelup_xp = 50;
        $this->entity = $conf->entity;
    }

    /**
     * @param User $user User object
     * @return int
     */
    public function save($user)
    {
        return $this->create($user);
    }

    /**
     * Return login of user's player
     * @return string if OK, -1 if KO
     */
    public function getUserLogin(){

        $sql = "SELECT login FROM ".MAIN_DB_PREFIX."user u";
        $sql .= " JOIN ".MAIN_DB_PREFIX."doligame_player p ON p.fk_user = u.rowid";
        $sql .= " WHERE p.fk_user = '".$this->fk_user."'";

        $resql = $this->db->query($sql);

        if($resql){

            $obj = $this->db->fetch_object($resql);

            return $obj->login;
        } else {
            return -1;
        }

    }

    /**
     * Update Xp and level of the player
     * @return int >0 if OK, -1 if KO
     */
    public function updatePlayerXp(){

        global $user;

        //somme de tous les xps du joueur
        $sql = "SELECT SUM (xp) as total_xp FROM ".MAIN_DB_PREFIX."doligame_player_xp WHERE fk_player = '".$this->id."'";
        $resql = $this->db->query($sql);

        if($resql)
        {
            $obj = $this->db->fetch_object($resql);

            //mise à jour du total d'xp
            $this->total_xp = $obj->total_xp;

            //si le total d'xp ext supérieur ou égal au total d'xp nécessaire pour monter de niveau, alors on monte de niveau
            if($this->total_xp >= $this->levelup_xp){

                $xp_rest = $this->levelUp();

                if($xp_rest < 0) return -1;

                //tant que le reste d'xp est supérieur ou égal à l'xp nécessaire pour monter de niveau, alors on monte de niveau
                while($xp_rest >= $this->levelup_xp){

                    $xp_rest = $this->levelUp();

                    if($xp_rest < 0) return -1;
                }
            }
        }

        //mise à jour du joueur
        $res = $this->update($user, true);

        return $res;
    }


    /**
     * Level up the player
     * @return int >= if OK, -1 if KO
     */
    private function levelUp(){

        $xp_rest = $this->total_xp - $this->levelup_xp;

        if($xp_rest >= 0)
        {
            $this->level++;
            $this->levelup_xp += $this->levelup_xp + ($this->levelup_xp * 0.10);
            return $xp_rest;
        }
        else {
            return -1;
        }
    }

    /**
     * Fetch player by the user id
     * @param User $fk_user User id
     * @return >0 if OK, -1 if KO
     */
    public function fetchByUser($fk_user){

        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."doligame_player WHERE fk_user = '".$fk_user."'";
        $resql = $this->db->query($sql);

        if($resql){
            $obj = $this->db->fetch_object($resql);

            $res = $this->fetch($obj->rowid);

            return $res;
        } else {
            return -1;
        }

    }
}