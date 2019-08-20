<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = Array
(
'pending_action_exists' => 'No puedes llevar a cabo esta acción, estás ocupado haciendo otras cosas.', 
'donate_ok' => 'Has conseguido donar los objetos indicados.', 
'take_ok' => 'Has recolectado algunos objetos.', 
'donate_executeerror' => 'Ha ocurrido un error durante el proceso de donación. Contacta con el administrador.', 
'take_executeerror' => 'Ha ocurrido un error durante la transacción. Contacta con el administrador.', 
'take_maxweightreached' => 'Estás cargando demasiadas cosas y no puedes tomar este objeto.', 
'global_notenoughmoney' => 'No tienes suficiente dinero.', 
'house_buyok' => 'Has comprado un(a) %s por %s monedas.', 
'sellhouse_ok' => 'Has vendido tu(s) %s por %s monedas. Has pagado impuestos por valor de %s monedas.', 
'sellhouse_housenotfound' => 'Esta casa no existe o no es tuya.', 
'sellhouse_itemsinhouse' => 'Tienes que vaciar la casa antes de poder venderla.', 
'drop_storablecapacityfinished' => 'Has excedido la capacidad de almacenamiento de la estructura, no puedes depositar allí más cosas.', 
'drop_ok' => 'Has depositado tus objetos.', 
'market_itemsnotowned' => 'No hay tantos objetos de este tipo.', 
'item_notexist' => 'El objeto no existe.', 
'item_notininventory' => 'El objeto no está en tu inventario.', 
'item_soldsubject' => 'Te han comprado algo en el mercado.', 
'item_soldbody' => 'Vendiste %s %s por %s monedas.', 
'itemsquantitynotowned' => 'No tienes tantos objetos de este tipo.', 
'marketsellitem_pricelessthanzero' => 'El precio de venta debe ser positivo', 
'marketbuyitem_cannotbuyownitems' => 'No puedes comprar objetos que tu mismo has puesto en venta', 
'marketcancellsell_ok' => 'Has cancelado la venta y recuperado los objetos del mercado', 
'item_notwearable' => 'No puedes llevar este objetos', 
'item_notundressable' => 'No puedes quitar este objeto', 
'getwood_no_energy' => '¡No tienes la energía suficiente para cortar leña!', 
'getwood_no_handaxe' => 'Debes usar un hacha para cortar leña', 
'knifeneeded' => 'Necesitas un cuchillo para realizar esta accion.', 
'pickaxeneeded' => 'Debes sostener un pico para completar esta accion.', 
'breedingbuy_ok' => 'Has comprado una granja', 
'terrainbuy_ok' => 'Has comprado un campo de cultivo por %s monedas.', 
'terrainsell_ok' => 'Has vendido tu campo de cultivo por %s monedas. Has pagado %s monedas en impuestos.', 
'royalp_appointvassal_ok' => 'Le has dado el rol de Vasallo de %s a %s.', 
'royalp_candidateisincharge' => 'El candidato ya tiene otro cargo.', 
'royalp_candidateisfromdifferentkingdom' => 'El candidato pertenece a un reino diferente.', 
'resign_from_role_messagesubject' => '%s ha renunciado al cargo.', 
'resign_from_role_messagebody' => '%s ha renunciado al cargo de %s aduciendo el siguiente motivo: %s', 
'resign_from_role_ok' => 'Has renunciado al cargo.', 
'revoke_role' => 'Revocar cargo.', 
'revoke_role_ok' => 'Has revocado en el cargo de %s a %s.', 
'char_hasntrole' => 'El jugador elegido no tiene ningún cargo.', 
'castle_deletelaw_ok' => 'Has derogado la ley.', 
'castle_editlaw_ok' => 'Has modificado la ley.', 
'askaudience_messagesubject' => '%s ha solicitado una audiencia.', 
'royalp_askaudience_ok' => 'Has solicitado una audiencia al soberano.', 
'appoint_priest' => 'Nombrar Párroco', 
'appoint_bishop' => 'Nombrar un Obispo', 
'appoint_cardinal' => 'Nombrar un Cardenal', 
'askaudience_ok' => 'Has enviado tu petición de audiencia.', 
'marketbuy_maxweightreached' => 'No has podido comprar el objeto porque llevas demasiado peso encima.', 
'wear_ok' => 'Has equipado: %s.', 
'undress_ok' => 'Has removido: %s.', 
'shop_craftok' => 'Has comenzado a fabricar el objeto.', 
'craft_cantcreateobject' => 'No puedes fabricar este objeto en este tipo de establecimiento.', 
'craft_chardoesnthaveneededitems' => 'No tienes todos los componentes necesarions para la fabricación.', 
'negative_quantity' => 'Por favor, introduce un número positivo.', 
'free_motivationempty' => 'Debes indicar una razón para liberar al prisionero.', 
'freeprisoner_ok' => 'Has escarcelado a %s', 
'publishsentence_parametersempty' => 'El receptor y el texto de la sentencia son obligatorios.', 
'publishsentence_ok' => 'Has dictado la sentencia.', 
'publishsentence_characterhasarole' => 'No puedes dictar sentencia contra %s, la diplomacia debe encargarse de estos casos.', 
'publishsentence_messagesubject' => 'El juez ha promulgado una sentencia.', 
'publishsentence_sentencetoolong' => 'El texto de la sentencia no puede exceder los 250 caracteres.', 
'deletesentence_notnew' => 'No es posible cancelar una sentencia ya dictada.', 
'deletesentence_messagesubject_target' => 'El juez canceló una sentencia.', 
'deletesentence_ok' => 'Sentencia eliminada.', 
'castle_candidateisfromdifferentnode' => 'Solo puedes nombrar residentes de tu religión.', 
'castle_appointsheriff_ok' => 'Acabas de nombrar %s Capitán de la Guardia de %s.', 
'castle_appointmagistrate_ok' => 'Acabas de nombrar %s Magistrado de %s.', 
'charhasnomorerole' => 'No puedes revocar el cargo de %s.', 
'change_city_samecity' => 'No puedes transferirte a %s porque ya eres ciudadano de allí.', 
'change_city_ok' => 'Te has trasnferido y ahora eres ciudadano de %s.', 
'change_city_timenotexpired' => 'No puedes trasladarte porque paso muy poco tiempo desde tu última translación.', 
'change_city_charhasarole' => 'Antes de transferirte debes renunciar a tu cargo.', 
'starving_subject' => '%s, tu personaje se está muriendo de hambre...', 
'starving_body' => '%s,si no comes algo, mañana o pasado mañana tu personaje morirá. Tanto tu personaje %s como sus posesiones serán borradas del juego y si quieres jugar de nuevo, te tendrás que crear un personaje nuevo.', 
'charinroledied_subject' => '%s, murió de hambre; el puesto esta vacante.', 
'item_notcure' => 'No te puedes curar con este tipo de objeto', 
'cure_ok' => 'La cura ha sido eficaz. Has recuperado toda la salud!', 
'shop_configure_ok' => 'El mensaje promocional de tu establecimiento ha sido guardado.', 
'castle_addannouncement_ok' => 'El anuncio ha sido publicado.', 
'castle_editannouncement_ok' => 'El anuncio ha sido modificado.', 
'cannotcancelwardeclaration' => 'Ya es demasiado tarde para cancelar la declaración de guerra.', 
'deletewaraction_ok' => 'La declaración de guerra ha sido cancelada.', 
'senditem' => 'Enviar un objeto.', 
'senditem_helper' => 'Desde aquí puedes enviar un objeto a otro personaje pagando unas monedas. El tiempo que tarde en llegar y el precio de envío depende de la localización del emisor y del destinatario, así como del peso total de lo que se envíe.', 
'senditem_sendnormalitem' => 'Estás a punto de enviar algo', 
'senditem_sendcoins' => 'Enviando', 
'senditem_ok' => 'Enviaste un objeto', 
'senditem_shortmessage' => 'Enviando objeto', 
'senditem_longmessage' => 'Estás enviando un objeto', 
'equipped_item' => 'No puedes enviar un objeto que estés usando.', 
'norole' => 'No tienes ningún rol todavía.', 
'cannotattackchurch' => 'No puedes atacar la Iglesia.', 
'bf_add_attack_ok' => 'Te unes a la facción atacante.', 
'bf_add_defend_ok' => 'Te unes a la facción defensora.', 
'bf_retire_ok' => 'Has abandonado el campo de batalla.', 
'giveitems_ok' => 'Items modificados.', 
'toplist_doesnt_exist' => 'Esta lista no esta configurada.', 
'buy_avatar_ok' => 'Has comprado un avatar para tu personaje. Los administradores han recibido una notificación por correo y muy pronto actualizarán tu perfil.', 
'senditem_costmessage' => 'Estás a punto de mandar <b>%d %s</b> a <b>%s</b>  por un total de <b>%s Kg</b> y un costo de <b>%d  moneda(s)</b>. Los items serán recividos en <b>%s</b>.', 
'sending_alreadysending' => 'Ahora estás enviando <b>%d %s</b> a <b>%s</b>. El/los item(s) serán recividos en <b>%s</b>.', 
'renthorse_ok' => 'Has alquilado un caballo.', 
'hirehelper_ok' => 'Has contratado un trabajador.', 
'item_wrongsex' => 'Luego de pensar lo suficiente, decides no usar ese tipo de ropa.', 
'feedanimals_ok' => 'Estás alimentando tu ganado.', 
'sail_no_porto' => 'La región no tiene un puerto.', 
'sail_no_route' => 'No hay una ruta naval entre esas dos regiones.', 
'item_wrongrole' => 'No tienes los requisitos necesarios para ponerte esta ropa.', 
'item_not_enough_str' => 'No tienes bastante fuerza para usar o llevar este objeto.', 
'incompatible_worn_items_1' => 'No pudiste ponerte esta ropa. Debes removerte otras ropas primero.', 
'incompatible_worn_items_2' => 'No pudiste ponerte esta ropa. Debes removerte otras ropas primero.', 
'change_city_destregionisfull' => '%s está superpoblada y ya no acepta más ciudadanos.', 
'convertcurrency_ok' => 'Has convertido las monedas.', 
'volunteerworkhourscheck' => 'Puedes trabajar voluntariamente de 1 a 9 horas.', 
'paidworkhourscheck' => 'Puedes trabajar por tres, seis o nueve horas.', 
'notenoughenergyglut' => 'Estás demasiado cansado o hambriento para iniciar esta acción.', 
'worknotenoughslots' => 'Un guardia se aproxima a tí para decirte que no hay más trabajo remunerado.', 
'workonproject_ok' => 'Has comenzado a trabajar en el proyecto.', 
'sendmoneynotoldenough' => 'No está permitido enviar dinero a través de un Correo.', 
'change_city' => 'Transferirse.', 
'transfer' => 'Trasferirse.', 
'changeattributes_ok' => 'Has redistribuído los valores de tus atributos.', 
'sellproperty_propertynotempty' => 'La habitación de almacenamiento de la propiedad no está vacía. Contacta al propiertario para que la vacíe.', 
'sellproperty_pendingactionexists' => 'El propietario se encuentra ocupado trabajando. Contáctelo y dígale que abandone su trabajo.', 
'sendnotsendableitem' => 'No puedes enviar este tipo de objeto.', 
'restrain' => 'Bloqueo', 
'craft_neededitemsmissing' => 'Algunos de los items necesarios para la fabricación estan ausentes. Por favor comprueba el inventario del taller.', 
'structure_fullinventory' => 'La habitación de almacenamiento esta llena, intenta quitar ciertos objetos antes de producir más.', 
'change_city_destnodeisindependent' => 'No puedes trasladarte a esta region porque no tiene gobierno.', 
'change_city_helper' => 'Si deseas trasladarte a esta región (%s), el precio es  <b>%s</b> monedas.', 
'sendnormalitemslimitation' => 'Solo puedes enviar un objeto.', 
'senditem_totalitemsmessage' => 'Tienes un total de <b>%d</b> %s.', 
'acquireclearancepermit_ok' => 'Has comprado un permiso de paso.', 
'onlyoneoccurrenceforthisitem' => 'Solo puedes llevar un objeto de este tipo.', 
'acquiresupercart_ok' => 'Has comprado un carro duradero.', 
'itemtrashed_ok' => 'Tiraste algunas de tus cosas.', 
'shovel_no_shovel' => '¡Debes sostener una pala para recoger la arena!', 
'fish_no_fishing_net' => '¡Debes tener una red de pesca!', 
'regionisindependent' => 'Al acercarse a la propiedad, un gran grupo de nativos se interpone en su camino.', 
'canbuyonlyone' => 'Solo puede comprar un solo artículo de este tipo.', 
'buyship_ok' => 'Has adquirido un buque mercante', 
'customerscantbemerchant' => 'El remitente y el destinatario deben ser dos usuarios diferentes. No puede ser el remitente y el destinatario al mismo tiempo.', 
'exhibit_scroll' => 'Exhiba el documento', 
'exhibit_scroll_helper' => 'Puede ocurrir que deba mostrar documentos oficiales, como un pase o un permiso para la explotación de un recurso territorial. Desde esta página, puede mostrar el contenido del documento a una persona que esté presente con usted en esta región.', 
'only_generic_scroll' => '¡Solo puedes mostrar documentos!', 
'travel_to_bf' => 'Estás viajando directamente al campo de batalla', 
'select_color_tint' => 'Selecciona un tinte', 
'tint_helper' => 'Puede seleccionar un tinte para aplicar a la ropa seleccionada. Para teñir una rota necesitas un cuenco de tinte.', 
'missing_dyebowl' => 'Para teñir ropa, necesitas un recipiente de tinte.', 
'dye_ok' => 'Teñiste con exito el articulo.', 
'move_charisrestrained' => 'Mientras te vas de la región, un guardia te bloquea.', 
'castle_candidateisfromdifferentregion' => 'El candidato debe ser residente en la región.', 
'acquirequeue_ok' => 'Has adquirido algunas pociones de resistencia.', 
'craft_toomanyslot' => 'No necesita tantas acciones para completar el artículo &#8217 .', 
'marketsellitem_itemislocked' => 'Este artículo es personal o está bloqueado, no puedes regalarlo.', 
'declarerevolt_notpossible' => 'Las noticias de que un disturbio está en proceso te llegan; escribir una carta parece inútil ahora', 
'house_declarerevolt_running_helper' => 'La revuelta ha comenzado, el Regente ha sido informado. Tu ejército se está reuniendo debajo del Palacio Real, por lo tanto, la batalla comenzará pronto en <b>%s</b>.', 
'revolt_kingcantsupportrevolt' => 'Sientes que apoyar la Rebelión no es una buena idea.', 
'revolt_choosefaction_ok' => 'Has decidido a quién apoyarás.', 
'revolt_charfactionnotchosen' => 'Aún no has decidido a quien apoyar.', 
'revolt_leaverevolt_ok' => 'Pensándolo bien, decides que no quieres apoyar a ninguna facción. Tal vez será mejor si te vas de aquí.', 
'revolt_leadercantsupportking' => 'Después de reflexionar, sientes que quizás apoyar al Regente no sea una buena idea.', 
'declarerevolt_alreadyfighting' => 'Ya estás en acción y en una zona de combate', 
'revolt_charmustbekingdomresident' => 'A medida que te acercas te das cuenta de que las personas son desconfiadas y no quieren ninguna intrusión de personas de afuera.', 
'defender_underrevolt' => 'Usted ha sido informado sobre la inminente revuelta en %s, cree que es mejor esperar antes de declarar acciones hostiles.', 
'chooserevoltfaction_notoldenough' => 'No has vivido lo suficiente en este reino como para tomar partido en la revuelta.', 
'bonus-hasalreadybonus' => 'Este bonus ya esta activo o puede que tenga otros bonus activos que no son compatibles con este.', 
'bonus-acquirebonusok' => 'Ha comprado un Bonus', 
'travelmessage' => 'Estas viajando de %s a %s.', 
'hammerneeded' => 'Debes sostener un martillo de trabajo para completar esta accion.', 
'hoeneeded' => 'Necesitas una azada para realizar esta accion.', 
'bucketneeded' => 'Para completar esta accion debes sostener un cubo.', 
'bellowneeded' => 'Para completar esta acción debes sostener un fuelle.', 
'reason_cleanprisons' => 'Limpie la prisión', 
'reason_takefromstructure' => 'Retiro del edificio.', 
'reason_boardvisibility' => 'Visibilidad del tablero de anuncios', 
'reason_questreward' => 'Premio por completar las misiones', 
'reason_travelerpackage' => 'Bonus Pack de Viajero', 
'reason_studycost' => 'Estudio/Entrenamiento', 
'reason_marketbuy' => 'Compro productos del mercado', 
'reason_supercartbonus' => 'Compro Bonus Super Carro', 
'reason_basicpackage' => 'Compro paquete basico', 
'reason_senditems' => 'Envio articulos', 
'reason_notspecified' => 'Causa no especificada', 
'reason_sailcost' => 'Costos de navegaci0n', 
'reason_toplistvote' => 'Votacion  en el Toplist', 
'reason_structurebuy' => 'Compro un Edificio', 
'reason_referralbonus' => 'Bonus por Recomendado', 
'reason_structuresold' => 'Venta de propiedad', 
'reason_marketsale' => 'Productos vendidos en el mercado', 
'reason_becomeking' => 'Gastos de coronacion', 
'reason_adminsend' => 'Envio de la administración', 
'loot' => 'Buscar', 
'reason_game_diceelite' => 'Juego de dados (Habitacion Nobles)', 
'reason_workerpackage' => 'Compra Bonus Pack de Trabajador ', 
'reason_buildsalary' => 'Salario de trabajador en la construccion', 
'reason_game_dicesimple' => 'Juego de dados (Habitacion Plebeyos)', 
'reason_purchase' => 'Compra', 
'reason_duelpresence' => 'Presente en  Duelo', 
'reason_duelabsence' => 'Ausente en Duelo', 
'reason_lootdiscovered' => 'Atrapado robando', 
'error-notenough-questpoints' => 'No puede usar esta funcion sin tener al menos %d puntos de honor.', 
'achievementmissing' => 'No has logrado alcanzar el siguiente logro: %s', 
'reason_prayer' => 'Oracion', 
'paperpieceneeded' => 'Necesitas un pedazo de papel.', 
'paperpieceandwaxsealneeded' => 'Debes tener una hoja de papel y un sello de cera.', 
'reason_questtoken' => 'Envio 10 Fichas de Madera.', 
'reason_resttavern' => 'Descansa en la taberna', 
'reason_donatecoins' => 'Donacion', 
'reason_goodsandservicestax' => 'Impuesto sobre bienes y servicios', 
'reason_wage' => 'Salario', 
'reason_atelierlicense' => 'Compra una licencia de Atelier', 
'error-notenabledwhenrestrained' => 'Usted esta actualmente bajo custodia, no puede hacer esta accion.', 
'reason_tavernincome' => 'Ingresos de taberna', 
'reason_dailyrevenue' => 'Bonificación diaria por personajes activos', 
'reason_revokerole' => 'Remocion del cargo', 
'reason_roleassignment' => 'Asignacion de cargo', 
'userisnotactive' => 'Su cuenta aun no esta validada. Validarla siguiendo las instrucciones que figuran en el correo que recibio despues del registro.', 
'reason_suggestionsponsorship' => 'Sugerencia patrocinada', 
'reason_wardrobeapproval' => 'Aprobación para personalizar su vestuario', 
'battlefielddismountedcantgoback' => 'El campo de batalla ha sido eliminado, no puedes volver.', 
'info-answered' => 'Usted ha respondido a la propuesta de matrimonio.', 
'error-proposalnotfound' => 'Propuesta no encontrada', 
'error-weddingproposalnotaccepted' => 'La propuesta de matrimonio no ha sido enviada o no ha sido aceptada.', 
'error-proposalalreadyanswered' => 'Usted ya ha respondido a esta propuesta.', 
'error-charnotavailableforregfunctions' => '%s rechaza su solicitud porque no desea participar.', 
'reason_coursegain' => 'Ganancia del curso', 
'reason_wardrobeapprovalfree' => 'Aprobacion gratuita para la personalizar el vestuario', 
'error-tooyoung' => 'Eres demasiado joven para ejecutar esta accion.', 
'reason_diamondring' => 'Compra de Bonus: Anillo de Diamantes', 
'reason_professionaldesk' => 'Compra de Bonus: Escritorio profesional', 
'reason_atelier-license-weapon' => 'Compra de Licencia de vestuario: arma/arma de fuego', 
'reason_atelier-license-avatar' => 'Compra de Licencia de vestuario: Avatar', 
'reason_elixirofhealth' => 'Compra de Bonus: Elixir de Salud', 
'reason_elixirofdexterity' => 'Compra de Bonus: Elixir de Destreza', 
'reason_elixirofstrength' => 'Compra de Bonus: Elixir de Fuerza', 
'reason_elixirofconstitution' => 'Compra de Bonus: Elixir de Constitución', 
'reason_elixirofintelligence' => 'Compra de Bonus: Elixir de Inteligencia', 
'reason_elixirofstamina' => 'Compra de Bonus: Elixir de Resistencia', 
'reason_sparringpartner' => 'Entrena con Entrenador de Combate', 
'reason_applyelixirofhealth' => 'Elixir de salud consumido', 
'reason_applyelixirofcuredisease' => 'Elixir de Curacion de enfermedad consumido', 
'reason_bleedingwounds' => 'Heridas sangrantes', 
'reason_plague' => 'Peste Negra', 
'reason_starving' => 'Hambre', 
'glance' => 'Vistazo', 
'equipment_failed' => 'No estás usando los elementos adecuados para realizar esta accion', 
'charissick' => 'No puedes entrar en Meditacion porque estas enfermo.', 
'reason_dailyreward' => 'Premio diario', 
'curedisease' => 'Curar', 
'curehealth' => 'Recuperar salud', 
'initiation' => 'Iniciación', 
'error_structure_tool_missing' => 'El edificio no contiene las herramientas adecuadas para la realizar la accion.', 
'confirm_operation_consume' => 'Confirma el consumo n. %s %s?', 
'equipment_failed_craft' => 'Para completar esta accion, el cuerpo y los pies deben estar cubiertos. También debe usar en su mano derecha la siguiente herramienta: <strong> %s </strong>', 
'reason_ipcheckshield' => 'Compra de Bonus: Protección de verificacion de IP compartida', 
'reason_rosary' => 'Compra de Bonus: Rosario', 
'reason_supercart' => 'Comprar de Bonus: Carro profesional', 
'reason_elixirofcuredisease' => 'Compra de Bonus: Elixir de Curacion de enfermedad', 
'attack' => 'Ataque', 
'error-moduleisdisabled' => 'Esta funcion esta actualmente deshabilitada.', 
'equipmentfailed_missing' => 'Accion: %s', 
'equipmentfailed_wrong' => 'No puede ejecutar esta accion porque esta usando los articulos incorrectos en las siguientes partes del cuerpo: <br/> %s.', 
'info-unequippedall' => 'Has eliminado todo el equipo.', 
'senditems_updatesponsorstats' => 'Actualizar estadísticas de Doblones comprados', 
'sendinfo' => 'Enviar estos articulos le costara en monedas de plata <span class = \'value\' id = \'cost\'>? </Span>  y se entregaran en <span class = \'value\' id = \'time\'>? </Span>.', 
'marketbuyitem_cannotbuyreserveditem' => 'No puedes comprar este artículo', 
'error-characterisofenemykingdom' => 'Él está en el campo de batalla.', 
'reason_wardeclaration' => 'Declaración de guerra', 
'reason_droptostructure' => 'Deposite en el inventario del edificio', 
'reason_warexpenses' => 'Gastos de guerra', 
'reason_buildcanceled' => 'Cancelar actividad de construcción', 
'info-skillremoved' => 'Has eliminado la habilidad:% s.', 
'info-scrollwritten' => 'Usted escribió un pergamino.', 
'error-toomanyskillslearned' => 'Puedes aprender como máximo 3 habilidades.', 
'reason_taketostructure' => 'Tomar de construir', 
'info-scrollshown' => 'Usted ha mostrado el rollo.', 
'senddoubloons' => 'Enviar doblones.', 
'reason_searchdump' => 'Buscar en el basurero', 
'reason_recovering' => 'Recuperacion de salud', 
'reason_drink' => 'Consumo de alcohol', 
'reason_arrest' => 'Arrestar', 
'reason_butchering' => 'Matando animal', 
'reason_cancelmarriage' => 'Anular matrimonio', 
'reason_celebratemarriage' => 'Celebracion matrimonial', 
'reason_cleanprison' => 'Limpieza de prisiones', 
'reason_collectwater' => 'Recogida de agua', 
'reason_craft' => 'Creacion de objetos', 
'reason_curedisease' => 'Sanar de la enfermedad', 
'reason_curehealth' => 'Recuperacion de salud', 
'reason_damagestructure' => 'Daño al edificio', 
'reason_excommunicate' => 'Excomunion', 
'reason_feedanimali' => 'Alimentacion animal', 
'reason_fishing' => 'Pescar', 
'reason_gather' => 'Recoleccion de productos de origen animal', 
'reason_getwood' => 'Recoleccion de madera', 
'reason_glance' => 'Inspeccion', 
'reason_harvest' => 'Cosecha', 
'reason_imprison' => 'Prision', 
'reason_initiate' => 'Iniciacion religiosa', 
'reason_inspect' => 'Examen', 
'reason_move' => 'Viajar', 
'reason_pray' => 'Oracion', 
'reason_recuperateiron' => 'Recuperacion de hierro', 
'reason_repairstructure' => 'Reparacion de edificios', 
'reason_resting' => 'Descanso', 
'reason_searchplant' => 'Buscando hierbas', 
'reason_seed' => 'Siembra', 
'reason_extractresources' => 'Extraccion de recursos', 
'reason_steal' => 'Robo', 
'reason_study' => 'Estudiar / Entrenamiento', 
'reason_unlockcontainer' => 'Apertura del contenedor', 
'reason_resetenergy' => 'Restablecer energia', 
'reason_workonstructure' => 'Trabajando en la construccion', 
'reason_respawn' => 'Reaparecer', 
'reason_sendingmessage' => 'Enviando mensaje', 

);

?>