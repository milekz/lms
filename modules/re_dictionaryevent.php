<?php
$layout['pagetitle'] = 'Słownik - Rodzaje zdarzeń';

if (isset($_GET['delete']) && isset($_GET['is_sure']) && isset($_GET['id']) && $_GET['is_sure'] == '1' && !empty($_GET['id'])) {
    $RE->deletedDictionaryEvent($_GET['id']);
}

$cartype = NULL;

$akcja = 'lista';

function add_event_type() {
    global $SMARTY,$akcja;
    $obj = new xajaxResponse();
    
    $SMARTY->assign('akcja','add');
    $SMARTY->assign('eventtype',NULL);
    $obj->assign('id_edit_event_type','innerHTML',$SMARTY->fetch('re_dictionaryevent.html'));
    $obj->script("document.getElementById('id_name').focus();");
    return $obj;
}

function edit_event_type($id) {
    global $SMARTY,$akcja,$RE;
    $obj = new xajaxResponse();
    
    $SMARTY->assign('akcja','edit');
    $SMARTY->assign('eventtype',$RE->getDictionaryEvent($id));
    $obj->assign('id_edit_event_type','innerHTML',$SMARTY->fetch('re_dictionaryevent.html'));
    $obj->script("document.getElementById('id_name').focus();");
    
    return $obj;
}

function save_event_type($forms) {
    global $DB,$SMARTY,$action,$RE;
    $obj = new xajaxResponse();
    $blad = false;
    
    $form = $forms['eventtype'];
    
    $obj->script("removeClassid('id_name','alerts');");
    $obj->assign("id_name_alerts","innerHTML","");
    
    if (empty($form['name'])) {
	$blad = true;
	$obj->script("addClassId('id_name','alerts');");
	$obj->assign("id_name_alerts","innerHTML","nazwa jest wymagana");
	$obj->script("document.getElementById('id_name').focus();");
    } elseif ($RE->CheckIssetDictionaryEvent($form['name'],($form['id'] ? $form['id'] : NULL))) {
	$blad = true;
	$obj->script("addClassId('id_name','alerts');");
	$obj->assign("id_name_alerts","innerHTML","podane zadrzenie już istnieje");
	$obj->script("document.getElementById('id_name').focus();");
    }
    
    if (!$blad) {
	if (isset($form['id']) && !empty($form['id'])) {
	    $RE->updateDictionaryEvent($form);
	} else {
	    $RE->addDictionaryEvent($form);
	}
	
	$obj->script("self.location.href='?m=re_dictionaryevent';");
    }
    return $obj;
}

function set_active_event_type($id) {
    global $DB;
    $obj = new xajaxResponse();
	$active = $DB->GetOne('SELECT active FROM re_dictionary_event WHERE id = ? '.$DB->limit(1).' ;',array($id));
	
	if($active) {
	    $obj->script("addClassId('id_eventlist_tr_".$id."','blend');");
	    $obj->script("document.getElementById('id_img_active_".$id."').src='img/noaccess.gif';");
	    $DB->Execute('UPDATE re_dictionary_event SET active = 0 WHERE id = ?;',array($id));
	} else {
	    $obj->script("removeClassId('id_eventlist_tr_".$id."','blend');");
	    $obj->script("document.getElementById('id_img_active_".$id."').src='img/access.gif';");
	    $DB->Execute('UPDATE re_dictionary_event SET active = 1 WHERE id = ?;',array($id));
	}
    
    return $obj;
}

$eventlist = $RE->GetDictionaryEventList();

$LMS->InitXajax();
$LMS->RegisterXajaxFunction(array('add_event_type','edit_event_type','save_event_type','set_active_event_type'));
$SMARTY->assign('xajax',$LMS->RunXajax());

$SMARTY->assign('akcja',$akcja);
$SMARTY->assign('eventlist',$eventlist);
$SMARTY->display('re_dictionaryevent.html');
?>