<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2014                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

/**
 * Gestion du formulaire pour dailymotionapi
 *
 * @package SPIP\Formulaires
**/
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Chargement du formulaire pour dailymotionapi
 *
 * @return array
 *     Environnement du formulaire
**/
function formulaires_upload_dailymotionapi_charger_dist(){

    $valeurs = array();
    include_spip('dailymotionapi_fonctions');
    $api = dailymotionapi_config();

    $result = $api->get('/channels', array('fields' => 'id,name'));
    
    $i=0;
    while ($i <= count($result)) {
        $valeurs['categorie'][] = array(
                'id' => $result['list'][$i]['id'],
                'name' => $result['list'][$i]['name']);
        $i++;
    }

    return $valeurs;
    
}

/**
 * Vérifications du formulaire pour dailymotionapi
 *
 * @return array
 *     Tableau des erreurs
**/
function formulaires_upload_dailymotionapi_verifier_dist(){
    $erreurs = array();
            
    return $erreurs;
}

/**
 * Traitement du formulaire pour dailymotionapi
 *
 * @return array
 *     Retours du traitement
**/
function formulaires_upload_dailymotionapi_traiter_dist(){

    include_spip('dailymotionapi_fonctions');
    $api = dailymotionapi_config();

    if (isset($_FILES['fichier']) AND $_FILES['fichier']['tmp_name']) {
        include_spip('action/ajouter_documents');
        include_spip('inc/joindre_document');

        $doc = &$_FILES['fichier'];

        if (!deplacer_fichier_upload($doc['tmp_name'], _DIR_TMP . $doc['name']))
            $erreurs['message_erreur'] = _T('copie_document_impossible');

        if (isset($erreurs['message_erreur'])){
            spip_unlink(_DIR_TMP . $doc['name']);
            unset($_FILES['fichier']);
        }


        $url = $api->uploadFile(_DIR_TMP . $doc['name']);  
        $result = $api->call('video.create', array('url' => $url, 'title' => _request('titre'), 'channel' => _request('categorie'), 'tags' => _request('tags'), 'description' => _request('description'), 'published' => true));  
        $videourl = 'http://www.dailymotion.com/video/'.$result['id'];
        spip_log($videourl, 'test.' . _LOG_ERREUR);
    }
        
    return array('editable' => true, 'message_ok'=>_T('dailymotionapi:upload_info_enregistree'));
}

?>
