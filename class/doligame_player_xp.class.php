<?php

if (!class_exists('SeedObject'))
{
    /**
     * Needed if $form->showLinkedObjectBlock() is call or for session timeout on our module page
     */
    define('INC_FROM_DOLIBARR', true);
    require_once dirname(__FILE__).'/../config.php';
}


class DoligamePlayerXp extends SeedObject
{
    public $table_element = 'doligame_player_xp';

    public $element = 'doligameplayerxp';

    public $isextrafieldmanaged = 1;

    public $ismultientitymanaged = 0;

    public $fk_player;
    public $code_action;
    public $xp;
    public $entity;

    public $fields = array(

        'fk_player' => array(
            'type' => 'integer',
            'label' => 'Player',
            'enabled' => 1,
            'visible' => 1,
            'notnull' => 1,
            'position' => 10
        ),'code_action' => array(
            'type' => 'string',
            'label' => 'Code Action',
            'enabled' => 1,
            'visible' => 1,
            'notnull' => 1,
            'position' => 20
        ),'xp' => array(
            'type' => 'integer',
            'label' => 'Xp',
            'enabled' => 1,
            'visible' => 1,
            'notnull' => 1,
            'position' => 30
        ),'entity' => array(
            'type' => 'integer',
            'label' => 'Entity',
            'enabled' => 1,
            'visible' => 0,
            'notnull' => 1,
            'position' => 40
        ),

    );

    public function __construct($db)
    {
        global $conf;

        parent::__construct($db);

        $this->init();

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
     * Fetch player xp of a player by code_action
     * @param int $player_id Player Id
     * @param string $code_action Action code
     * @return int >0 if OK, -1 if KO
     */
    public function fetchByAction($player_id, $code_action){

        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."doligame_player_xp WHERE code_action='".$code_action."' AND fk_player = '".$player_id."'";
        $resql = $this->db->query($sql);

        if($resql){

            $obj = $this->db->fetch_object($resql);
            return $this->fetch($obj->rowid);
        }
    }

    /**
     * Fetch all player xp of a player
     * @param int $player_id Player Id
     * @return int >0 if OK, -1 if KO
     */
    public function fetchAllByPlayer($player_id){

        $sql = "SELECT rowid as id, xp FROM ".MAIN_DB_PREFIX."doligame_player_xp WHERE fk_player ='". $player_id ."'";
        $resql = $this->db->query($sql);

        if($resql){

            $TPlayerXps = array();

            while($obj = $this->db->fetch_object($resql)){
                $TPlayerXps[] = $obj;
            }

            return $TPlayerXps;
        } else {
            return -1;
        }

    }

}