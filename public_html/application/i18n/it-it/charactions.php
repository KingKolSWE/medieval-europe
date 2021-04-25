<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = Array
(
'pending_action_exists' => 'Non puoi procedere con questa azione, sei occupato a fare altro.', 
'donate_ok' => 'Hai donato gli oggetti specificati', 
'take_ok' => 'Hai prelevato degli oggetti.', 
'donate_executeerror' => 'Si &egrave;verificato un errore durante la donazione. Contatta un&#8217;amministratore.', 
'take_executeerror' => 'Si &egrave;verificato un errore durante l&#8217; operazione. Contatta un&#8217;amministratore.', 
'take_maxweightreached' => 'Stai portando troppe cose, liberati di qualche oggetto.', 
'global_notenoughmoney' => 'Non hai abbastanza soldi.', 
'house_buyok' => 'Hai acquistato una %s per %s denari.', 
'sellhouse_ok' => 'Hai venduto la tua %s per %s denari. Hai pagato tasse per %s denari.', 
'sellhouse_housenotfound' => 'Questa casa non esiste o non &egrave;tua.', 
'sellhouse_itemsinhouse' => 'Prima di vendere la casa devi svuotarla.', 
'drop_storablecapacityfinished' => 'Hai superato la capacit&agrave; di immagazzinamento della struttura, non puoi depositare cos&igrave tanti oggetti.', 
'drop_ok' => 'Hai depositato i tuoi oggetti.', 
'market_itemsnotowned' => 'Non ci sono cos&igrave tanti oggetti di questo tipo.', 
'item_notexist' => 'L&#8217;oggetto non esiste', 
'item_notininventory' => 'Non possiedi cosi tanti oggetti di questo tipo.', 
'item_soldsubject' => 'Un tuo oggetto &egrave;stato acquistato.', 
'item_soldbody' => 'Hai venduto %s %s per %s denari.', 
'itemsquantitynotowned' => 'Non hai cosi tanti oggetti di questo tipo.', 
'marketsellitem_pricelessthanzero' => 'Il prezzo di vendita deve essere positivo.', 
'marketbuyitem_cannotbuyownitems' => 'Non puoi comprare gli oggetti che tu stesso hai messo in vendita.', 
'marketcancellsell_ok' => 'Hai recuperato i tuoi oggetti dal mercato.', 
'item_notwearable' => 'L&#8217;oggetto non puo&#8217; essere indossato', 
'item_notundressable' => 'L&#8217;oggetto non puo&#8217; essere rimosso', 
'getwood_no_energy' => 'Non hai abbastanza energia per raccogliere della legna!', 
'getwood_no_handaxe' => 'Devi impugnare un&#8217;accetta per abbattere gli alberi!', 
'knifeneeded' => 'Devi impugnare un coltello per poter eseguire questa azione.', 
'pickaxeneeded' => 'Per fare questa azione devi impugnare un piccone.', 
'breedingbuy_ok' => 'Hai comprato un allevamento', 
'terrainbuy_ok' => 'Hai comprato un terreno per %s denari.', 
'terrainsell_ok' => 'Hai venduto un terreno per %s denari. Hai versato tasse per %s denari.', 
'royalp_appointvassal_ok' => 'Hai appena dato la carica di Vassallo di %s a %s.', 
'royalp_candidateisincharge' => 'Il candidato ricopre gi&agrave; un incarico.', 
'royalp_candidateisfromdifferentkingdom' => 'Il candidato appartiene ad un regno diverso.', 
'resign_from_role_messagesubject' => '%s ha rinunciato all&#8217; incarico.', 
'resign_from_role_messagebody' => '%s ha rinunciato all&#8217;incarico di %s con la seguente motivazione: %s', 
'resign_from_role_ok' => 'Hai rinunciato al tuo incarico.', 
'revoke_role' => 'Revoca ruolo', 
'revoke_role_ok' => ' Hai revocato il ruolo %s a %s.', 
'char_hasntrole' => 'Il giocatore scelto non ricopre nessun ruolo.', 
'castle_deletelaw_ok' => 'Hai abrogato la legge.', 
'castle_editlaw_ok' => 'Hai modificato la legge.', 
'askaudience_messagesubject' => '%s ha chiesto Udienza', 
'royalp_askaudience_ok' => 'Hai richiesto audienza al Reggente.', 
'appoint_priest' => 'Nomina', 
'appoint_bishop' => 'Nomina', 
'appoint_cardinal' => 'Nomina un Cardinale', 
'askaudience_ok' => 'Hai inviato la tua richiesta per un udienza.', 
'marketbuy_maxweightreached' => 'Stai portando troppe cose, non sei riuscito a comprare l&#8217; oggetto. ', 
'wear_ok' => 'Hai indossato: %s.', 
'undress_ok' => 'Hai rimosso: %s.', 
'shop_craftok' => 'Hai iniziato la costruzione dell&#8217;oggetto.', 
'craft_cantcreateobject' => 'Non puoi creare questo oggetto con questa tipologia di bottega.', 
'craft_chardoesnthaveneededitems' => 'Non hai tutti gli oggetti necessari per la creazione.', 
'negative_quantity' => 'Inserisci un numero positivo.', 
'free_motivationempty' => 'Devi fornire una motivazione per la scarcerazione.', 
'freeprisoner_ok' => 'Hai scarcerato %s.', 
'publishsentence_parametersempty' => 'Il ricevente ed il testo della sentenza sono obbligatori.', 
'publishsentence_ok' => 'Hai ufficializzato la sentenza.', 
'publishsentence_characterhasarole' => 'Non puoi emettere una sentenza verso %s, la diplomazia deve gestire questi casi.', 
'publishsentence_messagesubject' => 'Il magistrato ha emesso una sentenza', 
'publishsentence_sentencetoolong' => 'La sentenza pu&ograve; essere lunga al massimo 250 caratteri.', 
'deletesentence_notnew' => 'Non &egrave;possibile cancellare la sentenza.', 
'deletesentence_messagesubject_target' => 'Il magistrato ha cancellato una sentenza emessa verso di te.', 
'deletesentence_ok' => 'Hai cancellato la sentenza.', 
'castle_candidateisfromdifferentnode' => 'Puoi nominare solo personaggi residenti nella tua regione.', 
'castle_appointsheriff_ok' => 'Hai appena nominato %s Capitano delle Guardie di %s.', 
'castle_appointmagistrate_ok' => 'Hai appena nominato %s Magistrato di %s.', 
'charhasnomorerole' => 'Non puoi revocare il ruolo a %s.', 
'change_city_samecity' => 'Non puoi trasferirti a %s perch&egrave; sei gi&agrave; un suo cittadino.', 
'change_city_ok' => 'Ti sei trasferito ed ora sei un cittadino di %s.', 
'change_city_timenotexpired' => 'Non puoi trasferirti perch&egrave; &egrave; passato troppo poco tempo dal tuo ultimo trasferimento.', 
'change_city_charhasarole' => 'Prima di trasferirti, devi rinunciare al tuo corrente incarico.', 
'starving_subject' => '%s, il tuo personaggio sta morendo di fame...', 
'starving_body' => '%s, se non sfami immediatamente il tuo pseronaggio, morir&egrave; nei prossimi giorni. Il tuo personaggio %s, i tuoi averi e le tue propriet&agrave; spariranno, e dovrai creare un nuovo personaggio.', 
'charinroledied_subject' => '%s, &egrave; morto di inedia; il ruolo &egrave; vacante.', 
'item_notcure' => 'L&#8217;oggetto che vuoi usare o applicare non &egrave; di tipo curativo.', 
'cure_ok' => 'La cura ha avuto successo e hai recuperato tutta la tua salute!', 
'shop_configure_ok' => 'Il messaggio promozionale della tua bottega &egrave; stato salvato', 
'castle_addannouncement_ok' => 'Il messaggio &egrave; stato pubblicato.', 
'castle_editannouncement_ok' => 'Il messaggio &egrave; stato modificato.', 
'cannotcancelwardeclaration' => 'E&#8217; troppo tardi per cancellare la DIchiarazione di Guerra.', 
'deletewaraction_ok' => 'Hai cancellato la Dichiarazione di Guerra.', 
'senditem' => 'Invia un oggetto', 
'senditem_helper' => 'E&#8217; possibile inviare uno o pi&ugrave; oggetti o dei soldi ad un altro giocatore tramite un corriere.Il tempo di invio dipende dalla posizione di chi invia e riceve. Il tempo ed il costo crescer&agrave; a seconda della distanze e del peso del materiale inviato.', 
'senditem_sendnormalitem' => 'Stai mandando', 
'senditem_sendcoins' => 'Stai mandando ', 
'senditem_ok' => 'Hai inviato gli oggetti', 
'senditem_shortmessage' => 'Invio oggetto', 
'senditem_longmessage' => 'Stai inviando un oggetto', 
'equipped_item' => 'Non puoi inviare un oggetto che stai indossando.', 
'norole' => 'Non hai nessun Ruolo.', 
'cannotattackchurch' => 'Non puoi attaccare la Chiesa.', 
'bf_add_attack_ok' => 'Sei stato aggiunto allo schieramento degli attaccanti', 
'bf_add_defend_ok' => 'Sei stato aggiunto allo schieramento dei difensori', 
'bf_retire_ok' => 'Hai abbandonato il campo di battaglia', 
'giveitems_ok' => 'Oggetti assegnati.', 
'toplist_doesnt_exist' => 'Questa toplist non &egrave; configurata.', 
'buy_avatar_ok' => 'Hai acquistato un avatar per il tuo personaggio. Lo staff ha ricevuto una mail di notifica e presto aggiorner&agrave; il tuo profilo!', 
'senditem_costmessage' => 'Stai per inviare <b>%d %s</b> a <b>%s</b> per un peso totale di <b>%s Kg</b> ed un costo di <b>%d denaro/i</b>. Gli oggetti saranno ricevuti in <b>%s</b>.', 
'sending_alreadysending' => 'Stai inviando <b>%d %s</b> a <b>%s</b>. Gli oggetti saranno ricevuti in <b>%s</b>.', 
'renthorse_ok' => 'Hai affittato un poderoso cavallo.', 
'hirehelper_ok' => 'Hai assunto un manovale.', 
'item_wrongsex' => 'Dopo averci riflettuto attentamente, decidi di non indossare il capo.', 
'feedanimals_ok' => 'Stai sfamando i tuoi animali', 
'sail_no_porto' => 'La regione dove sei attualmente non dispone di un porto!', 
'sail_no_route' => 'Non esiste un collegamento marittimo tra queste due regioni!', 
'item_wrongrole' => 'Non hai i requisiti per indossare questo oggetto', 
'item_not_enough_str' => 'Non sei abbastanza forte per poter indossare questo oggetto', 
'incompatible_worn_items_1' => 'Non puoi indossare questo vestito se indossi un capo per il torso e per le gambe.', 
'incompatible_worn_items_2' => 'Non puoi indossare questo vestito se indossi un capo che ti copre il corpo.', 
'change_city_destregionisfull' => '%s &egrave; al momento sovrappopolata e non pu&ograve; accettare nuovi cittadini.', 
'convertcurrency_ok' => 'Hai convertito i tuoi denari di rame in denari di argento.', 
'volunteerworkhourscheck' => 'Puoi lavorare come volontario da 1 a 9 ore.', 
'paidworkhourscheck' => 'Puoi lavorare per tre, sei o nove ore.', 
'notenoughenergyglut' => 'Sei troppo stanco o affamato per eseguire l&#8217; azione.', 
'worknotenoughslots' => 'Una guardia approcciandoti ti dice che non c&#8217; &egrave; lavoro disponibile al momento.', 
'workonproject_ok' => 'Hai iniziato a lavorare su un progetto.', 
'sendmoneynotoldenough' => 'Non ti &egrave; ancora permesso spedire soldi tramite corriere.', 
'change_city' => 'Trasferisciti', 
'transfer' => 'Trasferisciti', 
'changeattributes_ok' => 'Hai ridistribuito i valori dei tuoi attributi.', 
'sellproperty_propertynotempty' => 'Il magazzino della propriet&agrave; non &egrave; vuoto, contatta il proprietario affinch&egrave; lo liberi.', 
'sellproperty_pendingactionexists' => 'Il proprietario al momento sta lavorando nella propriet&agrave;. Contattalo e digli di interrompere il lavoro.', 
'sendnotsendableitem' => 'Non puoi spedire questo tipo di oggetto.', 
'restrain' => 'Blocca', 
'craft_neededitemsmissing' => 'Mancano degli oggetti necessari per la creazione. Controlla l&#8217;inventario della bottega.', 
'structure_fullinventory' => 'Il magazzino &egrave; pieno, liberalo prima di produrre altri oggetti.', 
'change_city_destnodeisindependent' => 'Non puoi trasferirti in questa regione perch&egrave; non &egrave; governata.', 
'change_city_helper' => 'Se lo desideri puoi trasferirti in questa regione (%s), il costo &egrave; di <b>%s</b> denari.', 
'sendnormalitemslimitation' => 'Puoi spedire un solo oggetto.', 
'senditem_totalitemsmessage' => 'Possiedi un totale di <b>%d</b> %s.', 
'acquireclearancepermit_ok' => 'Hai acquistato un lasciapassare.', 
'onlyoneoccurrenceforthisitem' => 'Puoi possedere solo un oggetto di questo tipo.', 
'acquiresupercart_ok' => 'Hai acquistato un carro rinforzato.', 
'itemtrashed_ok' => 'Ti sei disfatto di alcuni oggetti.', 
'shovel_no_shovel' => 'Devi impugnare una pala per poter raccogliere la sabbia!', 
'fish_no_fishing_net' => 'Devi impugnare una rete da pesca!', 
'regionisindependent' => 'Appena ti avvicini alla struttura un folto gruppo di indigeni ti sbarra la strada.', 
'canbuyonlyone' => 'Puoi comprare un solo oggetto di questo tipo.', 
'buyship_ok' => 'Hai acquistato una nave marcantile.', 
'customerscantbemerchant' => 'Il mandante ed il ricevente devono essere diversi. Non puoi essere n&egrave; il mandante n&egrave; il ricevente.', 
'exhibit_scroll' => 'Esibisci un documento', 
'exhibit_scroll_helper' => 'Pu&ograve capitare che ti venga richiesto di esibire dei documenti ufficiali quali un lasciapassare o un permesso per lo sfruttamento di una risorsa territoriale. Da questa pagina puoi mostrare il contenuto del documento ad una persona che &egrave;presente insieme a te in questa regione.', 
'only_generic_scroll' => 'Puoi esibire solamente dei documenti!', 
'travel_to_bf' => 'Ti stai recando direttamente al campo di battaglia', 
'select_color_tint' => 'Seleziona la tintura', 
'tint_helper' => 'Da questa pagina puoi selezionare il colore da applicare al vestito selezionato. Ti ricordiamo che per tinteggiare un vestito hai bisogno di una tinozza di colore.', 
'missing_dyebowl' => 'Per tinteggiare il vestito hai bisogno di una tinozza con del colore.', 
'dye_ok' => 'Hai colorato con successo il capo.',
'move_charisrestrained' => 'Appena ti accingi a muoverti, una guardia te lo impedisce.', 
'castle_candidateisfromdifferentregion' => 'Il candidato deve essere residente nella regione.', 
'acquirequeue_ok' => 'Hai acquistato delle pozioni per aumentare la tua produttivit&agrave.', 
'craft_toomanyslot' => 'Per completare il crafting dell&#8217; oggetto non hai bisogno di cos&igrave; tante azioni.', 
'marketsellitem_itemislocked' => 'L&#8217;oggetto &egrave; personale o prestato, non puoi disfartene.', 
'declarerevolt_notpossible' => 'Ti sono giunte notizie di una rivolta in corso, decidi che scrivere una lettere &egrave; ormai inutile.', 
'house_declarerevolt_running_helper' => 'La rivolta &egrave; in corso ed il Reggente &egrave; stato informato. La tua banda armata si sta radunando sotto il Palazzo Reale, la battaglia comincer&agrave; tra <b>%s</b>.', 
'revolt_kingcantsupportrevolt' => 'Dopo averci pensato un po&#8217; su, ritieni che forse non &egrave; il caso di supportare la rivolta.', 
'revolt_choosefaction_ok' => 'Hai deciso quale parte supportare.', 
'revolt_charfactionnotchosen' => 'Non ti sei ancora schierato.', 
'revolt_leaverevolt_ok' => 'Ripensandoci, decidi di non supportare nessuna fazione. Forse &egrave; meglio allontanarsi da qui.', 
'revolt_leadercantsupportking' => 'Dopo averci pensato un po&#8217; su, ritieni che forse non &egrave; il caso di supportare il Reggente.', 
'declarerevolt_alreadyfighting' => 'Sei gi&agrave; schierato in un altro campo di combattimento.', 
'revolt_charmustbekingdomresident' => 'Appena ti avvicini, noti alcuni sguardi minacciosi da chi vorresti aiutare. Evidentemente questa gente non vuole intrusioni da forestieri.', 
'defender_underrevolt' => 'Sei stato informato su una rivolta in corso a %s, ritieni che sia meglio aspettare a dichiarare azioni ostili.', 
'chooserevoltfaction_notoldenough' => 'Non hai vissuto abbastanza in questo Regno per decidere con chi schierarti.', 
'bonus-hasalreadybonus' => 'Hai gi&agrave; questo bonus attivo o hai dei bonus attivi non compatibili.', 
'bonus-acquirebonusok' => 'Hai acquistato il bonus.', 
'travelmessage' => 'Stai viaggiando da %s a %s.', 
'hammerneeded' => 'Per fare questa azione devi impugnare un martello da lavoro.', 
'hoeneeded' => 'Per fare questa azione devi impugnare una zappa.', 
'bucketneeded' => 'Per fare questa azione devi impugnare un secchio.', 
'bellowneeded' => 'Per fare questa azione devi impugnare un mantice.', 
'reason_cleanprisons' => 'Pulisci prigioni', 
'reason_takefromstructure' => 'Ritiro da struttura',
'reason_boardvisibility' => 'Visibilit&agrave;  annuncio', 
'reason_questreward' => 'Premio per completamento Quest', 
'reason_travelerpackage' => 'Acquisto Pacchetto Viaggiatore', 
'reason_studycost' => 'Studio/Allenamento', 
'reason_marketbuy' => 'Acquisto bene da mercato', 
'reason_supercartbonus' => 'Acquisto Bonus Carro Rinforzato', 
'reason_basicpackage' => 'Acquisto Bonus Pacchetto Basic', 
'reason_senditems' => 'Invio oggetti', 
'reason_notspecified' => 'Non specificata', 
'reason_sailcost' => 'Costi navigazione', 
'reason_toplistvote' => 'Voto toplist', 
'reason_structurebuy' => 'Acquisto struttura', 
'reason_referralbonus' => 'Bonus referral', 
'reason_structuresold' => 'Vendita struttura', 
'reason_marketsale' => 'Vendita beni al mercato', 
'reason_becomeking' => 'Spese incoronazione', 
'reason_adminsend' => 'Invio da Amministrazione', 
'loot' => 'Perquisisci', 
'reason_game_diceelite' => 'Gioco dadi (Stanza dei Nobili)', 
'reason_workerpackage' => 'Acquisto Bonus Pacchetto Worker', 
'reason_buildsalary' => 'Salario per aiuto costruzione struttura', 
'reason_game_dicesimple' => 'Gioco dadi (Tavolo della Plebe)', 
'reason_purchase' => 'Acquisto', 
'reason_duelpresence' => 'Presenza duello', 
'reason_duelabsence' => 'Assenza duello', 
'reason_lootdiscovered' => 'Scoperto a derubare', 
'error-notenough-questpoints' => 'Non puoi usare questa funzione senza avere accumulato almeno %d punti Quest.', 
'achievementmissing' => 'Non hai conseguito il seguente traguardo: %s.', 
'reason_prayer' => 'Preghiera', 
'paperpieceneeded' => 'E\' necessario disporre di un foglio di carta.', 
'paperpieceandwaxsealneeded' => 'E\' necessario disporre di un foglio di carta e di un sigillo di ceralacca.', 
'reason_questtoken' => 'Inviati 10 Quest Token utili.', 
'reason_resttavern' => 'Riposo in Taverna', 
'reason_donatecoins' => 'Donazione', 
'reason_goodsandservicestax' => 'Tassa su beni e servizi', 
'reason_wage' => 'Stipendio', 
'reason_atelierlicense' => 'Acquisto Licenza Atelier', 
'error-notenabledwhenrestrained' => 'Sei in stato di fermo, non ti &egrave; permesso eseguire questa azione.', 
'reason_tavernincome' => 'Incasso Taverna', 
'reason_dailyrevenue' => 'Bonus giornaliero per personaggi attivi', 
'reason_revokerole' => 'Rimozione Ruolo', 
'reason_roleassignment' => 'Assegnazione Ruolo', 
'userisnotactive' => 'Il tuo account non &egrave; stato validato. Validalo seguendo le istruzioni contenute nella mail di benvenuto.', 
'reason_suggestionsponsorship' => 'Sponsorizzazione suggerimento', 
'reason_wardrobeapproval' => 'Approvazione customizzazione Guardaroba', 
'battlefielddismountedcantgoback' => 'Il Campo di Battaglia &egrave; stato rimosso, non puoi tornare indietro.', 
'info-answered' => 'Hai dato la tua risposta alla proposta di Matrimonio.', 
'error-proposalnotfound' => 'Proposta non trovata.', 
'error-weddingproposalnotaccepted' => 'La proposta di matrimonio non &egrave; stata inviata o non &egrave; stata accettata.', 
'error-proposalalreadyanswered' => 'Hai gi&agrave;  dato la tua risposta a questa proposta.', 
'error-charnotavailableforregfunctions' => '%s ti fa un segno di diniego, non desidera partecipare.', 
'reason_coursegain' => 'Incasso Corsi', 
'reason_wardrobeapprovalfree' => 'Approvazione Customizzazione Guardaroba Gratuita',
'error-tooyoung' => 'Sei troppo giovane per eseguire questa azione.',
'reason_diamondring' => 'Acquisto Bonus Anello di Diamante',
'reason_professionaldesk' => 'Acquisto Bonus Professional Desk',
'reason_atelier-license-weapon' => 'Acquisto Licenza Guardaroba: Arma',
'reason_atelier-license-avatar' => 'Acquisto Licenza Guardaroba: Avatar',
'reason_elixirofhealth' => 'Acquisto Bonus: Elisir di Salute',
'reason_elixirofdexterity' => 'Acquisto Bonus: Elisir di Destrezza',
'reason_elixirofstrength' => 'Acquisto Bonus: Elisir di Forza',
'reason_elixirofconstitution' => 'Acquisto Bonus: Elisir di Costituzione',
'reason_elixirofintelligence' => 'Acquisto Bonus: Elisir di Intelligenza',
'reason_elixirofstamina' => 'Acquisto Bonus: Elisir di Stamina',
'reason_sparringpartner' => 'Allenamento con Sparring Partner',
'reason_applyelixirofhealth' => 'Consumato Elisir di Salute',
'reason_applyelixirofcuredisease' => 'Consumato Elisir di Cura Malattia',
'reason_bleedingwounds' => 'Ferite Sanguinanti',
'reason_plague' => 'Peste Nera',
'reason_starving' => 'Fame',
'glance' => 'Sbircia',
'equipment_failed' => 'Non indossi gli oggetti giusti per eseguire l\'azione',
'charissick' => 'Non puoi andare in meditazione perch&egrave; sei malato.',
'reason_dailyreward' => 'Premio Quotidiano',
'curedisease' => 'Cura',
'curehealth' => 'Cura Salute',
'initiation' => 'Iniziazione',
'error_structure_tool_missing' => 'La struttura non contiene gli attrezzi giusti per poter eseguire l\'azione',
'confirm_operation_consume' => 'Confermi Consumo n. %s %s?',
'equipment_failed_craft' => 'Per completare l\'azione il corpo ed i piedi devono essere coperti. Devi inoltre indossare nella tua mano destra il seguente attrezzo: <strong>%s</strong>',
'reason_ipcheckshield' => 'Acquisto Bonus: Protezione Controllo IP',
'reason_rosary' => 'Acquisto Bonus: Rosario',
'reason_supercart' => 'Acquisto Bonus: Carro Professionale',
'reason_elixirofcuredisease' => 'Acquisto Bonus: Elisir di cura malattia',
'attack' => 'Attacca',
'error-moduleisdisabled' =>  'Questa funzione &egrave; al momento disabilitata.',
'equipmentfailed_missing' => 'Devi equipaggiare alcuni oggetti per poter eseguire l\'azione: %s',
'equipmentfailed_wrong' => 'Non puoi eseguire questa azione perch&egrave; stai indossando degli oggetti non corretti sulle seguenti parti del corpo:<br/> %s.',
'info-unequippedall' => 'Hai rimosso tutto l\'equipaggiamento.',
'senditems_updatesponsorstats' => 'Aggiorna statistiche dobloni', 
'sendinfo' => 'Il costo per spedire questi oggetti è:<span class=\'value\' id=\'cost\'>?</span> denari d\'argento e sarà consegnato il: <span class=\'value\' id=\'time\'>?</span>.',
'marketbuyitem_cannotbuyreserveditem' => 'Non puoi comprare questo oggetto, è riservato per un altro personaggio.',
'error-characterisofenemykingdom' => 'Il personaggio %s è di un Regno a te nemico, regola le questioni su un campo di battaglia.',
'reason_wardeclaration' => 'Dichiarazione Guerra',
'reason_droptostructure' => 'Deposito in inventario struttura',
'reason_warexpenses' => 'Spese di Guerra',
'reason_buildcanceled' => 'Cancellazione attività di costruzione',
'info-skillremoved' => 'Hai rimosso lo skill: %s.',
'info-scrollwritten' => 'Hai scritto una pergamena.',
'error-toomanyskillslearned' => 'Puoi imparare al massimo 3 abilit&agrave;.',
'reason_droptostructure' => 'Deposito in struttura',
'reason_taketostructure' => 'Prelievo da struttura',
'info-scrollshown' => 'Hai esibito la pergamena.', 
'senddoubloons' => 'Invia dobloni',
'reason_searchdump' => 'Ricerca immondizia',
'reason_recovering' => 'Recupero salute',
'reason_drink' => 'Consumo alcolici',
'reason_arrest' => 'Arresto',
'reason_butchering' => 'Uccisione animali',
'reason_cancelmarriage' => 'Annullamento matrimonio',
'reason_celebratemarriage' => 'Celebrazione matrimonio',
'reason_cleanprison' => 'Pulizia prigione',
'reason_collectwater' => 'Raccolta acqua',
'reason_craft' => 'Creazione oggetti',
'reason_curedisease'=> 'Cura malattia',
'reason_curehealth' => 'Ripristino salute',
'reason_damagestructure' => 'Danneggiamento struttura',
'reason_excommunicate' => 'Scomunica',
'reason_feedanimali' => 'Alimentazione animali',
'reason_fishing' => 'Pesca',
'reason_gather' => 'Raccolta prodotti animali',
'reason_getwood' => 'Raccolta legna',
'reason_glance' => 'Ispezione',
'reason_harvest' => 'Mietitura',
'reason_imprison' => 'Imprigionamento',
'reason_initiate' => 'Battesimo',
'reason_inspect' => 'Esaminazione',
'reason_move' => 'Viaggio',
'reason_pray' => 'Preghiera',
'reason_recuperateiron' => 'Recupero ferro',
'reason_repairstructure' => 'Riparazione struttura',
'reason_resting' => 'Riposo',
'reason_searchplant' => 'Ricerca erbe',
'reason_seed' => 'Semina',
'reason_extractresources' => 'Estrazione risorse',
'reason_steal' => 'Furto',
'reason_study' => 'Studio/Allenamento',
'reason_unlockcontainer' => 'Apertura contenitore',
'reason_resetenergy' => 'Reset Energia',
'reason_workonstructure' => 'Lavoro su struttura',
'reason_respawn' => 'Respawn',
'reason_sendingmessage' => 'Invio messaggio',
'reason_toplistvote' => 'Voto Toplist',
'reason_feedanimal' => 'Alimentazione animali',
'error-notenoughstrenghttoequipitem' => 'Non sei abbastanza forte per indossare o usare questo oggetto (Forza richiesta: %d).',
);
?>