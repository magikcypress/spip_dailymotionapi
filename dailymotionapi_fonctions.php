<?php
/**
 * Utilisations de fonctions par dailymotionapi
 *
 * @plugin     dailymotionapi
 * @copyright  2014
 * @author     vincent
 * @licence    GNU/GPL
 * @package    SPIP\dailymotionapi\Pipelines
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function dailymotionapi_config() {

    include_spip('inc/config');
    $username = lire_config('dailymotionapi/username_dailymotionapi');
    $password = lire_config('dailymotionapi/password_dailymotionapi');
    $api_key = lire_config('dailymotionapi/api_key_dailymotionapi');
    $api_secret_key = lire_config('dailymotionapi/api_secret_key_dailymotionapi');
    include_once(_DIR_PLUGIN_DAILYMOTIONAPI."lib/dailymotion-sdk-php/Dailymotion.php");

    $api = new Dailymotion();
    $api->setGrantType(Dailymotion::GRANT_TYPE_PASSWORD, $api_key, $api_secret_key, $scope = array('manage_videos'), array('username' => $username, 'password' => $password));

    return $api;
}

function dailymotionapi_recup_public_video() {

    $api = dailymotionapi_config();
    $result = $api->get('/videos', array('fields' => 'id,title,description'));
    
    $i=0;
    foreach($result as $media) {
        $list_dailymotionapi[] = array(
                'id' => $result['list'][$i]['id'], 
                'titre' => $result['list'][$i]['title'],
                'description' => $result['list'][$i]['description']);
        $i++;
    }

    return $list_dailymotionapi;

}

function dailymotionapi_recup_video() {

    $api = dailymotionapi_config();
    $result = $api->get('/me/videos', array('fields' => 'id,title,url,owner.screenname,owner.url,created_time,channel'));

    $i=0;
    while ($i <= count($result)) {
        $list_dailymotionapi[] = array(
                'id' => $result['list'][$i]['id'], 
                'titre' => $result['list'][$i]['title'],
                'creer' => strftime("%c", $result['list'][$i]['created_time']),
                'url' => $result['list'][$i]['url'],
                'auteur' => $result['list'][$i]['owner.screenname'],
                'url_auteur' => $result['list'][$i]['owner.url'],
                'categorie' => $result['list'][$i]['channel']);
        $i++;
    }

    return $list_dailymotionapi;

}

function dailymotionapi_playlist_video() {

    $api = dailymotionapi_config();
    $result = $api->get('/playlists', array('fields' => 'id,name,description,owner.username'));

    $i=0;
    while ($i <= count($result)) {
        $playlist_dailymotionapi[] = array(
                'id' => $result['list'][$i]['id'], 
                'nom' => $result['list'][$i]['name'],
                'description' => $result['list'][$i]['description'],
                'auteur' => $result['list'][$i]['owner.username']);
        $i++;
    }

    return $playlist_dailymotionapi;

}