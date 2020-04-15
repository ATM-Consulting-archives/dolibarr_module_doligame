<?php
/* Copyright (C) 2020 ATM Consulting <support@atm-consulting.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 	\file		admin/players.php
 * 	\ingroup	doligame
 * 	\brief		This file is an example about page
 * 				Put some comments here
 */
// Dolibarr environment
$res = @include '../../main.inc.php'; // From htdocs directory
if (! $res) {
    $res = @include '../../../main.inc.php'; // From "custom" directory
}

// Libraries
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once '../lib/doligame.lib.php';
require_once '../class/doligame_player.class.php';
dol_include_once('abricot/includes/lib/admin.lib.php');


// Translations
$langs->load('doligame@doligame');

// Access control
if (! $user->admin) {
    accessforbidden();
}

// Parameters
$action = GETPOST('action', 'alpha');
$user_id = GETPOST('user', 'alpha');
$id_player = GETPOST('id_player', 'alpha');

$player = new DoligamePlayer($db);

/*
 * Actions
 */

if($action == 'add_player'){

    if(!empty($user_id)){

        $player->fk_user = $user_id;
        $res = $player->save($user);

        if($res > 0){
            setEventMessage('UserAdded');
        } else {
            setEventMessage('Error', 'error');
        }
    } else {
        setEventMessage('NoUserSelected', 'error');
    }

} elseif($action == 'remove_player'){

    if(!empty($id_player)){

        $res = $player->fetch($id_player);

        if($res){

            $res = $player->delete($user);

            if($res > 0){
                setEventMessage('UserDeleted');
            }

        } else{
            setEventMessage('Error', 'error');
        }
    }
}

if (preg_match('/set_(.*)/', $action, $reg))
{
    $code=$reg[1];
    if (dolibarr_set_const($db, $code, GETPOST($code), 'chaine', 0, '', $conf->entity) > 0)
    {
        header("Location: ".$_SERVER["PHP_SELF"]);
        exit;
    }
    else
    {
        dol_print_error($db);
    }
}

if (preg_match('/del_(.*)/', $action, $reg))
{
    $code=$reg[1];
    if (dolibarr_del_const($db, $code, 0) > 0)
    {
        Header("Location: ".$_SERVER["PHP_SELF"]);
        exit;
    }
    else
    {
        dol_print_error($db);
    }
}

/*
* View
*/
$page_name = 'playersSetup';
llxHeader('', $langs->trans($page_name));

// Subheader
$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">'
    . $langs->trans('BackToModuleList') . '</a>';
print load_fiche_titre($langs->trans($page_name), $linkback);

// Configuration header
$head = doligameAdminPrepareHead();
dol_fiche_head(
    $head,
    'players_setup',
    $langs->trans("Module104662Name"),
    -1,
    "doligame@doligame"
);


// Setup page goes here
$form=new Form($db);
$var=false;
print '<table class="noborder" width="100%">';


if(!function_exists('setup_print_title')){
    print '<div class="error" >'.$langs->trans('AbricotNeedUpdate').' : <a href="http://wiki.atm-consulting.fr/index.php/Accueil#Abricot" target="_blank"><i class="fa fa-info"></i> Wiki</a></div>';
    exit;
}


//Ajout de joueurs

setup_print_title("AddPlayers");

//Utilisateurs qui sont déjà des joueurs
$TUsersExclude = array();
$TPlayers = $player->fetchAll();

if($res > 0){
    foreach($TPlayers as $player){
        $TUsersExclude[] = $player->fk_user;
    }
}

print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="action" value="add_player">';

print '<table class="noborder centpercent">';

print '<tr class="oddeven">';
print '<td>';
print '<label>'.$langs->trans("Users").'</label>';
print $form->select_users('', 'user', '', $TUsersExclude);
print '</td>';
print '</tr>';

print '</table>';

print '<div class="center">';
print '<input type="submit" class="button" value="' . $langs->trans("AddPlayer") . '" name="button">';
print '</div>';

print '</form>';


print '<br>';


//Liste des joueurs

print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="action" value="edit_player">';

print '<table class="noborder centpercent">';

print '<tr class="oddeven">';
print '<th>'.$langs->trans('Player').'</th>';
print '<th>'.$langs->trans('Level').'</th>';
print '<th>'.$langs->trans('Xp').'</th>';
print '<th>&nbsp;</th>';
print '</tr>';

foreach($TPlayers as $player){

    print '<tr >';
    print '<td class="center">';
    print $player->getUserLogin();
    print '</td>';

    print '<td class="center">';
    print $player->level;
    print '</td>';

    print '<td class="center">';
    print $player->total_xp;
    print '</td>';

    print '<td class = "linecoldelete center"><a href='.$_SERVER['PHP_SELF'].'?action=remove_player&id_player='.$player->id.'>'. img_picto($langs->trans("Delete"), 'delete') . '</a></td>';

    print '</tr>';

}

print '</table>';

print '</form>';


print '</table>';

dol_fiche_end(-1);

llxFooter();

$db->close();
