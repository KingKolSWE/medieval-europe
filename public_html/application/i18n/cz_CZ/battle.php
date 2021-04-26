<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = Array
(
'fight_win' => '%s vyhrál duel.', 
'round' => 'Kolo %d:', 
'health' => '%s zdraví: %s, %s zdraví: %s', 
'nobattle' => 'Jeden bojovník je mrtvý, bitva se nebude konat.', 
'weaponshatters' => 'Zbraň bojovníka %s (%s) se rozbila!', 
'armorshatters' => 'Brnění bojovníka %s (%s) se rozbilo!', 
'hand' => 'Holýma rukama', 
'fight_tie' => 'Duel skončil nerozhodně.', 
'battleround' => 'Loupež: %s proti %s, kolo %d. Bitva se konala %s', 
'endrounddefendernotexist' => 'Bitva se nekonala, protože nebyli žádní obráncové.', 
'endroundattackernotexist' => 'Bitva se nekonala, protože nebyli žádní útočníci', 
'endroundnooneexist' => 'Bitva se nekonala, protože nikdo nedošel.', 
'startduel' => 'Utkání %s: %s proti %s', 
'roundresult_attackers_win' => 'Útočníci tohle kolo vyhráli.', 
'roundresult_defenders_win' => 'Obránci tohle kolo vyhráli.', 
'roundresult_tie' => 'Tohle kolo skončilo nerozhodně.', 
'participants' => 'Útočníci: <b>%s</b><br/>Obránci: <b>%s</b>', 
'battlewinner' => '%s %s vyhrál/a bitvu!', 
'battletie' => 'Obě frakce bojovaly, ale žádná frakce nezvítězila.', 
'roundresult_stats' => 'Přeživší útočníci: %d, Přeživší obránci: %d', 
'jartarused' => 'Bylo použito vroucího dehtu!', 
'nostrength' => '%s nemá dostatek síly, pro úder!', 
'raidresult' => 'Okradených hráčů:<b>%d</b>; Zpustošených objektů: <b>%d</b>; Vykradených budov: <b>%d</b>; Zničených budov: <b>%d</b>; Celková hodnota odcizených peněz: <b> %d</b>.', 
'revoltbattleround' => '%s v %s. Bojovalo se %s', 
'battlereport_introduction' => 'Přinášíme vám tuhle epickou bitvu a hrdinské činy našich skvělých vojáků, jejich slavné činy, ale také jejich utrpení.', 
'hit' => '<b>%s</b> zasáhl/a <b>%s</b> rukama (cíl: %s) za <span style=\'color: #c00; font-weight:bold\'>%s</span> poškození [<span style=\'color: #c00; font-weight:bold\'>%s</span> zdraví zůstalo]', 
'hitsmallrat' => '<b>%s</b> kousl/a <b>%s</b> (cíl: %s). Poškození zdraví: <span style=\'color: #c00; font-weight:bold\'>%s</span> HP [<span style=\'color: #c00; font-weight:bold\'>%s</span> HP zbylo]', 
'hitlargerat' => '<b>%s</b> kousl/a <b>%s</b> (cíl: %s). Poškození zdraví: <span style=\'color: #c00; font-weight:bold\'>%s</span> HP [<span style=\'color: #c00; font-weight:bold\'>%s</span> HP zbylo]', 
'hitchicken' => '<b>%s</b> klovla <b>%s</b> (cíl: %s). Poškození zdraví: <span style=\'color: #c00; font-weight:bold\'>%s</span> HP [<span style=\'color: #c00; font-weight:bold\'>%s</span> HP zbylo]', 
'hitnative' => '<b>%s</b> zasáhl/a <b>%s</b> rukama %s (cíl: %s) za <span style=\'color: #c00; font-weight:bold\'>%s</span> poškození [<span style=\'color: #c00; font-weight:bold\'>%s</span> zdraví zůstalo]', 
'hit2' => '<b>%s</b> zasáhl/a <b>%s</b> se zbraní %s (cíl: %s) za <span style=\'color: #c00; font-weight:bold\'>%s</span> poškození [<span style=\'color: #c00; font-weight:bold\'>%s</span> zdraví zůstalo]', 
'luckyhit' => 'Boj skončil nerozhodně. Hází se mincí, za účelem určení vítěze.Vítěz je: %s.', 
'debug' => '>>> Debug - Ladění: %s<<<', 
'miss' => '%s totálně minul/a!', 
'missbecausestun' => '( %s ztrácí tah, protože je omráčený/á)', 
'missbecausedparry' => '<b>%s</ b> uštědřil/a ránu, ale <b>%s</b> ji zablokoval/a svou parádní technikou!', 
'battlecriticalhit' => '%s uštědřil/a kritický zásah a ten nadělal obrovské škody!', 
'conqueririntroduction' => 'Bitva o dobytí regionu %s, která se odehrála %s', 
'raidintroduction' => 'Loupež: %s útočí na %s. Bitva se odehrála %s', 
'conquerrintroduction' => '%s útočí na %s (kolo %d), Bitva se odehrála %s', 
'generalintroduction' => 'Přinášíme vám tuhle epickou bitvu a hrdinské činy našich skvělých vojáků, jejich slavné činy, ale také jejich utrpení.', 
'raid' => 'Loupež', 
'conquer_r' => 'Podmanit si', 
'battleinfo' => '[INFORMACE]: %s', 
'part_head' => 'Hlava', 
'part_torso' => 'Tělo', 
'part_armor' => 'Tělo', 
'part_left_hand' => 'Levá paže/tlapka', 
'part_right_hand' => 'Pravá paže/tlapka', 
'part_legs' => 'Nohy/Tlapky', 
'part_feet' => 'Chodidla', 
'malusbonusapplication' => 'Aplikace bonusů a postihů', 
'energymalusterrain' => 'Postih %d použit na spotřebu energie v závislosti na typu terénu.', 
'jartardamage' => 'Vroucí dehet hoří %s a celkové poškození je %s zdraví!', 
'castlepresencemalus' => 'Bonus díky hradu: %d%% z počáteční energie útočníků.', 
'royalpalacepresencemalus' => 'Bonus díky královskému paláci: %d%% z počáteční energie útočníků.', 
'raidstart' => ' === Začátek nájezdu ===', 
'raidresultraidedstructureitem' => 'Vyloupeno <b>%d %s</b> (%s - vlastník: %s)', 
'raidresultmuggedcharitem' => 'Ukradeno <b>%d %s</b> z: %s', 
'raidend' => ' === Konec nájezdu ===', 
'separator' => '<br/>', 
'raidparameters' => 'Útočníků přežilo: <b>%d</b>, Podíl budov a položek, které budou ukradnuty: <b>%d%%</b>, Podíl budov, které budou zničeny: <b>%d%%</b>, Celková nosnost: <b>%d kg</b>', 
'raidresultdestroyedstructure' => 'Zničena stavba (%s, vlastněná: %s)', 
'revoltintroduction' => 'Vzpoura proti vládě - %s, bojovalo se %s', 
'charhasnomoreenergy' => '%s nemá víc energie.', 
'encumbrance' => 'Břemeno v boji', 
'stunnedchar' => '%s uštědřil/a omračující úder! %s se zhroutil/a k zemi.', 
'bleeddamage' => '%s krvácí a ztrácí <span style=\'color: #c00; font-weight:bold\'>%s</span> zdraví (<span style=\'color: #c00; font-weight:bold\'>%s</span> zdraví ješte zbývá).', 
'stunnedrecoverchar' => '%s se vzpamatoval/a a je připraven/a k boji.', 
'terrainmalus' => 'Aplikovaný postih na spotřebu energie: %d%% z důvodu terénu: %s.', 
'nativeinfo' => 'Vlastnosti bránícího vojáka: %s', 
'playerinfo' => '%s Vlastnosti: %s', 
'viewround' => 'Kolo: %s', 
'duelintroduction' => '%s proti %s', 
'nativerevoltintroduction' => 'Vzpoura původního obyvatelstva v regionu %s, bitva : %s', 
'error-battlereportnotfound' => 'Bitevní hlášení č.%d nenalezeno.', 
'parryfail' => '<b>%s</b>se pokusil/a zablokovat ránu, ale selhat/a.', 
'hitlargedog' => '<b>%s</b> kousl/a<b>%s</b> (cll: %s). Poškození: <span style=\'color: #c00; font-weight:bold\'>%s</span> HP [<span style=\'color: #c00; font-weight:bold\'>%s</span> HP zbývá]', 

);

?>