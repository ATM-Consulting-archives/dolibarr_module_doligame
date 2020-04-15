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

}