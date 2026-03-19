<?php

declare(strict_types=1);

$pdo = new PDO(
    'mysql:host=127.0.0.1;dbname=Sql1874742_3;charset=utf8mb4',
    'root',
    '',
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);

$categories = [
    ['name' => 'Gioielli Wire', 'slug' => 'gioielli-wire', 'description' => 'Ciondoli, orecchini, bracciali e anelli realizzati a mano.', 'sort_order' => 10],
    ['name' => 'Gioielli in Resina', 'slug' => 'gioielli-resina', 'description' => 'Creazioni in resina artistica con inclusioni e dettagli personalizzati.', 'sort_order' => 20],
    ['name' => 'Gioielli Ibridi', 'slug' => 'gioielli-ibridi', 'description' => 'Creazioni che uniscono wire wrapping e resina artistica.', 'sort_order' => 25],
    ['name' => 'Oggettistica in Resina', 'slug' => 'oggettistica-resina', 'description' => 'Oggetti decorativi e idee regalo in resina artistica.', 'sort_order' => 28],
    ['name' => 'Stampa 3D', 'slug' => 'stampa-3d', 'description' => 'Oggetti decorativi, utili e personalizzati realizzati con stampa 3D.', 'sort_order' => 30],
    ['name' => 'Smart Objects', 'slug' => 'smart-objects', 'description' => 'Oggetti creativi con NFC, RFID e contenuti digitali.', 'sort_order' => 35],
    ['name' => 'Segnaletica Personalizzata', 'slug' => 'segnaletica-personalizzata', 'description' => 'Targhe, numeri civici e segnaletica decorativa su richiesta.', 'sort_order' => 38],
    ['name' => 'Eventi', 'slug' => 'eventi', 'description' => 'Bomboniere, segnaposto, topper e ricordi per occasioni speciali.', 'sort_order' => 40],
    ['name' => 'Pet', 'slug' => 'pet', 'description' => 'Creazioni e ricordi dedicati agli animali domestici.', 'sort_order' => 45],
    ['name' => 'Scanner 3D', 'slug' => 'scanner-3d', 'description' => 'Servizi di scansione, miniature e replica oggetti.', 'sort_order' => 48],
    ['name' => 'Reverse Engineering', 'slug' => 'reverse-engineering', 'description' => 'Ricostruzione pezzi, adattatori e ricambi tecnici.', 'sort_order' => 50],
    ['name' => 'Accessori Vape', 'slug' => 'accessori-vape', 'description' => 'Accessori personalizzati per sigarette elettroniche e organizzazione desk.', 'sort_order' => 52],
];

$products = [
    ['category_slug' => 'gioielli-wire', 'name' => 'Ciondolo Luna Wire', 'slug' => 'ciondolo-luna-wire', 'sku' => 'LW-023', 'product_type' => 'finished', 'short_description' => 'Ciondolo artigianale in wire wrapping ispirato alla luna.', 'description' => 'Pezzo dimostrativo per il catalogo iniziale, utile per testare scheda prodotto, richieste via messaggio e organizzazione dei contenuti.', 'materials' => 'Filo metallico, pietra decorativa', 'technique' => 'Wire wrapping', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 1],
    ['category_slug' => 'gioielli-wire', 'name' => 'Orecchini Wire Essenziali', 'slug' => 'orecchini-wire-essenziali', 'sku' => 'GW-024', 'product_type' => 'finished', 'short_description' => 'Orecchini lavorati a mano in wire wrapping dal segno pulito.', 'description' => 'Linea orecchini wire pensata per il catalogo base, personalizzabile per colore e finitura.', 'materials' => 'Filo metallico, minuteria', 'technique' => 'Wire wrapping', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'gioielli-wire', 'name' => 'Bracciale Wire Nodo', 'slug' => 'bracciale-wire-nodo', 'sku' => 'GW-025', 'product_type' => 'finished', 'short_description' => 'Bracciale artigianale in filo modellato con chiusura regolabile.', 'description' => 'Bracciale wire versatile per testare proposte regalo e personalizzazioni iniziali.', 'materials' => 'Filo metallico, chiusure', 'technique' => 'Wire wrapping', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'gioielli-wire', 'name' => 'Anello Wire Spirale', 'slug' => 'anello-wire-spirale', 'sku' => 'GW-026', 'product_type' => 'finished', 'short_description' => 'Anello modellato a mano con motivo a spirale.', 'description' => 'Proposta anello wire per il catalogo base, con taglia e colore su richiesta.', 'materials' => 'Filo metallico', 'technique' => 'Wire wrapping', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],

    ['category_slug' => 'gioielli-resina', 'name' => 'Segnalibro Resina Botanica', 'slug' => 'segnalibro-resina-botanica', 'sku' => 'RB-011', 'product_type' => 'finished', 'short_description' => 'Segnalibro in resina con inclusioni naturali.', 'description' => 'Prodotto seed per mostrare una seconda linea creativa del catalogo e testare la navigazione tra categorie.', 'materials' => 'Resina artistica, inclusioni floreali', 'technique' => 'Colata in resina', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'gioielli-resina', 'name' => 'Ciondolo Resina Botanica', 'slug' => 'ciondolo-resina-botanica', 'sku' => 'GR-012', 'product_type' => 'finished', 'short_description' => 'Ciondolo in resina con inclusioni botaniche.', 'description' => 'Versione ciondolo della linea in resina, pensata per idee regalo e personalizzazione.', 'materials' => 'Resina artistica, inclusioni naturali', 'technique' => 'Colata in resina', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 1],
    ['category_slug' => 'gioielli-resina', 'name' => 'Orecchini Resina Trasparente', 'slug' => 'orecchini-resina-trasparente', 'sku' => 'GR-013', 'product_type' => 'finished', 'short_description' => 'Orecchini leggeri in resina artistica con pigmenti e inclusioni.', 'description' => 'Linea orecchini in resina per arricchire la gamma gioielli del catalogo.', 'materials' => 'Resina artistica, pigmenti', 'technique' => 'Colata in resina', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'gioielli-resina', 'name' => 'Medaglione Resina Memoria', 'slug' => 'medaglione-resina-memoria', 'sku' => 'GR-014', 'product_type' => 'finished', 'short_description' => 'Medaglione decorativo in resina per ricordi e regali simbolici.', 'description' => 'Scheda tipo per richieste personalizzate con iniziali, date o piccoli elementi inclusi.', 'materials' => 'Resina artistica, decorazioni', 'technique' => 'Colata in resina', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],

    ['category_slug' => 'gioielli-ibridi', 'name' => 'Ciondolo Ibrido Wire e Resina', 'slug' => 'ciondolo-ibrido-wire-resina', 'sku' => 'GI-001', 'product_type' => 'finished', 'short_description' => 'Ciondolo che unisce montatura wire e cabochon in resina.', 'description' => 'Linea ibrida che rappresenta il ponte tra lavorazione manuale e resina artistica.', 'materials' => 'Filo metallico, resina', 'technique' => 'Wire + resina', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 1],
    ['category_slug' => 'gioielli-ibridi', 'name' => 'Orecchini Ibridi Wire e Resina', 'slug' => 'orecchini-ibridi-wire-resina', 'sku' => 'GI-002', 'product_type' => 'finished', 'short_description' => 'Orecchini con dettagli wire e pendente in resina.', 'description' => 'Proposta ibrida pensata per collezioni capsule e pezzi personalizzati.', 'materials' => 'Filo metallico, resina artistica', 'technique' => 'Wire + resina', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],

    ['category_slug' => 'oggettistica-resina', 'name' => 'Portachiavi Resina Personalizzato', 'slug' => 'portachiavi-resina-personalizzato', 'sku' => 'OR-001', 'product_type' => 'finished', 'short_description' => 'Portachiavi in resina con nome, iniziali o colore a scelta.', 'description' => 'Prodotto versatile da catalogo, utile per regali, gadget e piccole serie.', 'materials' => 'Resina artistica, minuteria', 'technique' => 'Colata in resina', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 1],
    ['category_slug' => 'oggettistica-resina', 'name' => 'Fermacarte Resina Artistica', 'slug' => 'fermacarte-resina-artistica', 'sku' => 'OR-002', 'product_type' => 'finished', 'short_description' => 'Fermacarte decorativo in resina con inclusioni e pigmenti.', 'description' => 'Oggetto decorativo pensato per scrivania, studio o regalo personalizzato.', 'materials' => 'Resina artistica', 'technique' => 'Colata in resina', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'oggettistica-resina', 'name' => 'Quadretto Resina Decorativo', 'slug' => 'quadretto-resina-decorativo', 'sku' => 'OR-003', 'product_type' => 'finished', 'short_description' => 'Piccolo quadro in resina per decorazione da tavolo o parete.', 'description' => 'Linea decorativa adatta a serie limitate e richieste a tema.', 'materials' => 'Resina artistica, base supporto', 'technique' => 'Colata in resina', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'oggettistica-resina', 'name' => 'Decorazione Resina da Appoggio', 'slug' => 'decorazione-resina-da-appoggio', 'sku' => 'OR-004', 'product_type' => 'finished', 'short_description' => 'Decorazione in resina per casa, studio o idea regalo.', 'description' => 'Prodotto seed per la linea decorazioni in resina del laboratorio.', 'materials' => 'Resina artistica', 'technique' => 'Colata in resina', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'oggettistica-resina', 'name' => 'Segnalibro Resina Floreale', 'slug' => 'segnalibro-resina-floreale', 'sku' => 'OR-005', 'product_type' => 'finished', 'short_description' => 'Segnalibro decorativo in resina con dettagli floreali o cromatici.', 'description' => 'Seconda proposta della linea segnalibri, pensata per regali e piccole collezioni.', 'materials' => 'Resina artistica, inclusioni', 'technique' => 'Colata in resina', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],

    ['category_slug' => 'stampa-3d', 'name' => 'Lampada Luna 3D', 'slug' => 'lampada-luna-3d', 'sku' => '3DL-004', 'product_type' => 'finished', 'short_description' => 'Lampada decorativa stampata in 3D con look contemporaneo.', 'description' => 'Seed iniziale per la linea stampa 3D, utile per mostrare prodotti decorativi e futuri oggetti smart.', 'materials' => 'PLA, modulo luce LED', 'technique' => 'Stampa 3D', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 1],
    ['category_slug' => 'stampa-3d', 'name' => 'Paralume 3D Geometrico', 'slug' => 'paralume-3d-geometrico', 'sku' => '3DP-005', 'product_type' => 'finished', 'short_description' => 'Paralume stampato in 3D con linee geometriche moderne.', 'description' => 'Prodotto decorativo per ampliare la linea luci e casa.', 'materials' => 'PLA', 'technique' => 'Stampa 3D', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'stampa-3d', 'name' => 'Scultura 3D Contemporanea', 'slug' => 'scultura-3d-contemporanea', 'sku' => '3DP-006', 'product_type' => 'finished', 'short_description' => 'Scultura decorativa stampata in 3D per casa e studio.', 'description' => 'Scheda tipo per una linea di piccoli oggetti artistici e scenografici.', 'materials' => 'PLA', 'technique' => 'Stampa 3D', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'stampa-3d', 'name' => 'Vaso 3D Minimal', 'slug' => 'vaso-3d-minimal', 'sku' => '3DP-007', 'product_type' => 'finished', 'short_description' => 'Vaso stampato in 3D dal look minimale e contemporaneo.', 'description' => 'Prodotto casa che aiuta a presidiare la categoria decorazione e utility.', 'materials' => 'PLA', 'technique' => 'Stampa 3D', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'stampa-3d', 'name' => 'Supporto Smartphone 3D', 'slug' => 'supporto-smartphone-3d', 'sku' => '3DP-008', 'product_type' => 'finished', 'short_description' => 'Supporto da scrivania stampato in 3D per smartphone.', 'description' => 'Oggetto utile da catalogo, con personalizzazioni su colore e incisione.', 'materials' => 'PLA', 'technique' => 'Stampa 3D', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'stampa-3d', 'name' => 'Organizer Scrivania 3D', 'slug' => 'organizer-scrivania-3d', 'sku' => '3DP-009', 'product_type' => 'finished', 'short_description' => 'Organizer modulare stampato in 3D per scrivania e studio.', 'description' => 'Linea utilita stampata in 3D, pensata per desk setup e regali pratici.', 'materials' => 'PLA', 'technique' => 'Stampa 3D', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'stampa-3d', 'name' => 'Fermalibri 3D', 'slug' => 'fermalibri-3d', 'sku' => '3DP-010', 'product_type' => 'finished', 'short_description' => 'Fermalibri stampati in 3D con linee essenziali.', 'description' => 'Prodotto decorativo e utile per ampliare la gamma home office.', 'materials' => 'PLA', 'technique' => 'Stampa 3D', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'stampa-3d', 'name' => 'Litofania 3D Personalizzata', 'slug' => 'litofania-3d-personalizzata', 'sku' => '3DP-011', 'product_type' => 'finished', 'short_description' => 'Litofania da foto con effetto luminoso in controluce.', 'description' => 'Prodotto emozionale e personalizzabile, perfetto per regali e ricordi.', 'materials' => 'PLA, modulo luce opzionale', 'technique' => 'Stampa 3D', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 1],

    ['category_slug' => 'smart-objects', 'name' => 'Biglietto da Visita NFC', 'slug' => 'biglietto-da-visita-nfc', 'sku' => 'SO-001', 'product_type' => 'finished', 'short_description' => 'Biglietto smart con tecnologia NFC per contatto digitale immediato.', 'description' => 'Prodotto smart adatto a professionisti, creativi e piccole attivita.', 'materials' => 'Supporto NFC, stampa personalizzata', 'technique' => 'NFC / RFID', 'price_label' => 'Preventivo su richiesta', 'is_customizable' => 1, 'is_featured' => 1],
    ['category_slug' => 'smart-objects', 'name' => 'Portachiavi NFC Personalizzato', 'slug' => 'portachiavi-nfc-personalizzato', 'sku' => 'SO-002', 'product_type' => 'finished', 'short_description' => 'Portachiavi smart con collegamento a contenuti digitali.', 'description' => 'Soluzione smart per profili social, portfolio, pagine contatto o messaggi dedicati.', 'materials' => 'Tag NFC, supporto personalizzato', 'technique' => 'NFC / RFID', 'price_label' => 'Preventivo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'smart-objects', 'name' => 'Oggetto Smart con Link Digitale', 'slug' => 'oggetto-smart-con-link-digitale', 'sku' => 'SO-003', 'product_type' => 'finished', 'short_description' => 'Oggetto creativo con accesso NFC o RFID a contenuti digitali.', 'description' => 'Scheda tipo per oggetti smart su richiesta, brandizzati o personalizzati.', 'materials' => 'NFC / RFID, supporto custom', 'technique' => 'NFC / RFID', 'price_label' => 'Preventivo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'smart-objects', 'name' => 'Lampada Smart con Contenuti Digitali', 'slug' => 'lampada-smart-con-contenuti-digitali', 'sku' => 'SO-004', 'product_type' => 'finished', 'short_description' => 'Lampada decorativa con collegamento a contenuti digitali o pagine dedicate.', 'description' => 'Prodotto ibrido tra design e tecnologia per regali o progetti speciali.', 'materials' => 'PLA, luce LED, NFC', 'technique' => 'Stampa 3D + NFC', 'price_label' => 'Preventivo su richiesta', 'is_customizable' => 1, 'is_featured' => 1],

    ['category_slug' => 'segnaletica-personalizzata', 'name' => 'Numero Civico Personalizzato', 'slug' => 'numero-civico-personalizzato', 'sku' => 'SP-001', 'product_type' => 'finished', 'short_description' => 'Numero civico personalizzato per casa o studio.', 'description' => 'Prodotto su misura per esterni o interni, con varianti su stile e materiali.', 'materials' => 'Resina, stampa 3D, supporti vari', 'technique' => 'Personalizzazione su richiesta', 'price_label' => 'Preventivo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'segnaletica-personalizzata', 'name' => 'Targa Casa Personalizzata', 'slug' => 'targa-casa-personalizzata', 'sku' => 'SP-002', 'product_type' => 'finished', 'short_description' => 'Targa personalizzata per casa, studio o ingresso.', 'description' => 'Linea targhe decorative e funzionali con stile contemporaneo.', 'materials' => 'Resina, stampa 3D', 'technique' => 'Personalizzazione su richiesta', 'price_label' => 'Preventivo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'segnaletica-personalizzata', 'name' => 'Targa Ufficio Personalizzata', 'slug' => 'targa-ufficio-personalizzata', 'sku' => 'SP-003', 'product_type' => 'finished', 'short_description' => 'Targa coordinata per ufficio, studio o attivita.', 'description' => 'Proposta business per ingressi, desk e segnaletica interna.', 'materials' => 'Resina, stampa 3D', 'technique' => 'Personalizzazione su richiesta', 'price_label' => 'Preventivo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'segnaletica-personalizzata', 'name' => 'Cartello Decorativo Personalizzato', 'slug' => 'cartello-decorativo-personalizzato', 'sku' => 'SP-004', 'product_type' => 'finished', 'short_description' => 'Cartello decorativo con testo, nome o frase su richiesta.', 'description' => 'Elemento decorativo pensato per casa, eventi o piccole attivita.', 'materials' => 'Resina, stampa 3D', 'technique' => 'Personalizzazione su richiesta', 'price_label' => 'Preventivo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],

    ['category_slug' => 'eventi', 'name' => 'Bomboniere Personalizzate', 'slug' => 'bomboniere-personalizzate', 'sku' => 'EV-001', 'product_type' => 'finished', 'short_description' => 'Bomboniere creative per matrimoni, battesimi e comunioni.', 'description' => 'Scheda base per richieste evento con personalizzazione di forma, colore e dettaglio.', 'materials' => 'Resina, wire, stampa 3D', 'technique' => 'Personalizzazione per eventi', 'price_label' => 'Preventivo su richiesta', 'is_customizable' => 1, 'is_featured' => 1],
    ['category_slug' => 'eventi', 'name' => 'Segnaposto Personalizzati', 'slug' => 'segnaposto-personalizzati', 'sku' => 'EV-002', 'product_type' => 'finished', 'short_description' => 'Segnaposto personalizzati per tavole evento e cerimonie.', 'description' => 'Proposta per piccoli lotti coordinati con nome, tema e palette.', 'materials' => 'Resina, stampa 3D, carta tecnica', 'technique' => 'Personalizzazione per eventi', 'price_label' => 'Preventivo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'eventi', 'name' => 'Cake Topper Personalizzato', 'slug' => 'cake-topper-personalizzato', 'sku' => 'EV-003', 'product_type' => 'finished', 'short_description' => 'Cake topper su richiesta per cerimonie e feste.', 'description' => 'Prodotto evento ad alta personalizzazione, gestito su brief cliente.', 'materials' => 'Stampa 3D, resina', 'technique' => 'Personalizzazione per eventi', 'price_label' => 'Preventivo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'eventi', 'name' => 'Oggetto Ricordo Evento', 'slug' => 'oggetto-ricordo-evento', 'sku' => 'EV-004', 'product_type' => 'finished', 'short_description' => 'Oggetto ricordo personalizzato per momenti speciali.', 'description' => 'Linea ricordo per comunioni, battesimi, cresime o commemorazioni.', 'materials' => 'Resina, wire, stampa 3D', 'technique' => 'Personalizzazione per eventi', 'price_label' => 'Preventivo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],

    ['category_slug' => 'pet', 'name' => 'Medaglietta Pet Personalizzata', 'slug' => 'medaglietta-pet-personalizzata', 'sku' => 'PET-001', 'product_type' => 'finished', 'short_description' => 'Medaglietta per animali con nome e contatto.', 'description' => 'Prodotto pet utile e personalizzabile, adatto a cane e gatto.', 'materials' => 'Resina, metallo, stampa 3D', 'technique' => 'Personalizzazione su richiesta', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 1],
    ['category_slug' => 'pet', 'name' => 'Targhetta Pet Personalizzata', 'slug' => 'targhetta-pet-personalizzata', 'sku' => 'PET-002', 'product_type' => 'finished', 'short_description' => 'Targhetta personalizzata per collare o accessorio pet.', 'description' => 'Versione targhetta della linea pet, con finiture diverse e incisioni su richiesta.', 'materials' => 'Resina, stampa 3D', 'technique' => 'Personalizzazione su richiesta', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'pet', 'name' => 'Ricordo Pet Personalizzato', 'slug' => 'ricordo-pet-personalizzato', 'sku' => 'PET-003', 'product_type' => 'finished', 'short_description' => 'Oggetto ricordo dedicato agli animali domestici.', 'description' => 'Proposta emozionale per ricordi, commemorazioni o regalo affettivo.', 'materials' => 'Resina artistica, stampa 3D', 'technique' => 'Personalizzazione su richiesta', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'pet', 'name' => 'Scultura Pet su Richiesta', 'slug' => 'scultura-pet-su-richiesta', 'sku' => 'PET-004', 'product_type' => 'service', 'short_description' => 'Scultura o mini statua dedicata al proprio animale.', 'description' => 'Servizio/prodotto personalizzato che puo partire da foto o scansione.', 'materials' => 'Stampa 3D, resina', 'technique' => 'Modellazione e personalizzazione', 'price_label' => 'Preventivo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'pet', 'name' => 'Decorazione Pet Personalizzata', 'slug' => 'decorazione-pet-personalizzata', 'sku' => 'PET-005', 'product_type' => 'finished', 'short_description' => 'Decorazione a tema pet per casa, angolo ricordo o regalo.', 'description' => 'Scheda per ampliare la linea pet con oggetti decorativi su misura.', 'materials' => 'Resina, stampa 3D', 'technique' => 'Personalizzazione su richiesta', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],

    ['category_slug' => 'scanner-3d', 'name' => 'Miniatura Persona da Scanner 3D', 'slug' => 'miniatura-persona-da-scanner-3d', 'sku' => 'SCN-001', 'product_type' => 'service', 'short_description' => 'Miniatura personalizzata ottenuta da scansione 3D.', 'description' => 'Servizio dedicato a ricordi, gift speciali o piccoli progetti artistici.', 'materials' => 'Scanner 3D, stampa 3D', 'technique' => 'Scanner 3D', 'price_label' => 'Preventivo su richiesta', 'is_customizable' => 1, 'is_featured' => 1],
    ['category_slug' => 'scanner-3d', 'name' => 'Miniatura Animale da Scanner 3D', 'slug' => 'miniatura-animale-da-scanner-3d', 'sku' => 'SCN-002', 'product_type' => 'service', 'short_description' => 'Miniatura pet ottenuta da scansione o modellazione 3D.', 'description' => 'Estensione della linea pet con lavorazione digitale su richiesta.', 'materials' => 'Scanner 3D, stampa 3D', 'technique' => 'Scanner 3D', 'price_label' => 'Preventivo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'scanner-3d', 'name' => 'Replica Oggetto da Scanner 3D', 'slug' => 'replica-oggetto-da-scanner-3d', 'sku' => 'SCN-003', 'product_type' => 'service', 'short_description' => 'Replica digitale o fisica di un oggetto tramite scansione 3D.', 'description' => 'Servizio tecnico-creativo per piccoli oggetti, ricordi o prototipi.', 'materials' => 'Scanner 3D, stampa 3D', 'technique' => 'Scanner 3D', 'price_label' => 'Preventivo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'scanner-3d', 'name' => 'Modello Digitale da Scanner 3D', 'slug' => 'modello-digitale-da-scanner-3d', 'sku' => 'SCN-004', 'product_type' => 'service', 'short_description' => 'Creazione di modello digitale ottenuto da scansione 3D.', 'description' => 'Scheda servizio per consegna file o base di lavorazione successiva.', 'materials' => 'Scanner 3D, file digitale', 'technique' => 'Scanner 3D', 'price_label' => 'Preventivo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],

    ['category_slug' => 'reverse-engineering', 'name' => 'Ricostruzione Pezzo Rotto', 'slug' => 'ricostruzione-pezzo-rotto', 'sku' => 'REV-001', 'product_type' => 'service', 'short_description' => 'Ricostruzione di pezzi danneggiati o non piu reperibili.', 'description' => 'Servizio tecnico su richiesta per piccoli componenti e parti rotte.', 'materials' => 'Rilievo, modellazione 3D', 'technique' => 'Reverse engineering', 'price_label' => 'Preventivo su richiesta', 'is_customizable' => 1, 'is_featured' => 1],
    ['category_slug' => 'reverse-engineering', 'name' => 'Ricambio Plastico Personalizzato', 'slug' => 'ricambio-plastico-personalizzato', 'sku' => 'REV-002', 'product_type' => 'service', 'short_description' => 'Ricambio plastico su misura da campione o misura tecnica.', 'description' => 'Servizio dedicato a ricambi custom e piccole produzioni tecniche.', 'materials' => 'Modellazione 3D, stampa 3D', 'technique' => 'Reverse engineering', 'price_label' => 'Preventivo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'reverse-engineering', 'name' => 'Adattatore Tecnico su Misura', 'slug' => 'adattatore-tecnico-su-misura', 'sku' => 'REV-003', 'product_type' => 'service', 'short_description' => 'Adattatore tecnico realizzato su misura per esigenze specifiche.', 'description' => 'Prodotto/servizio per piccole soluzioni tecniche e funzionali.', 'materials' => 'Modellazione 3D, stampa 3D', 'technique' => 'Reverse engineering', 'price_label' => 'Preventivo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'reverse-engineering', 'name' => 'Componente Tecnico Personalizzato', 'slug' => 'componente-tecnico-personalizzato', 'sku' => 'REV-004', 'product_type' => 'service', 'short_description' => 'Sviluppo di componente tecnico da esigenza o campione.', 'description' => 'Scheda servizio per richieste tecniche non standard e piccole serie.', 'materials' => 'Modellazione 3D, stampa 3D', 'technique' => 'Reverse engineering', 'price_label' => 'Preventivo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],

    ['category_slug' => 'accessori-vape', 'name' => 'Stand Vape Personalizzato', 'slug' => 'stand-vape-personalizzato', 'sku' => 'VAP-001', 'product_type' => 'finished', 'short_description' => 'Stand personalizzato per sigaretta elettronica e accessori.', 'description' => 'Prodotto organizzativo stampato in 3D per desk setup dedicati.', 'materials' => 'PLA, stampa 3D', 'technique' => 'Stampa 3D', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'accessori-vape', 'name' => 'Organizer Liquidi Vape', 'slug' => 'organizer-liquidi-vape', 'sku' => 'VAP-002', 'product_type' => 'finished', 'short_description' => 'Organizer da banco per liquidi e piccoli accessori vape.', 'description' => 'Accessorio pratico personalizzabile per formato e numero slot.', 'materials' => 'PLA, stampa 3D', 'technique' => 'Stampa 3D', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'accessori-vape', 'name' => 'Supporto Atomizzatori', 'slug' => 'supporto-atomizzatori', 'sku' => 'VAP-003', 'product_type' => 'finished', 'short_description' => 'Supporto stampato in 3D per atomizzatori e accessori.', 'description' => 'Elemento organizzativo per utenti che vogliono ordine e personalizzazione.', 'materials' => 'PLA, stampa 3D', 'technique' => 'Stampa 3D', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'accessori-vape', 'name' => 'Drip Tip in Resina', 'slug' => 'drip-tip-in-resina', 'sku' => 'VAP-004', 'product_type' => 'finished', 'short_description' => 'Drip tip personalizzato in resina artistica.', 'description' => 'Prodotto di nicchia per la linea vape, con varianti colore e dettaglio.', 'materials' => 'Resina artistica', 'technique' => 'Resina artistica', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
    ['category_slug' => 'accessori-vape', 'name' => 'Porta Batterie Vape', 'slug' => 'porta-batterie-vape', 'sku' => 'VAP-005', 'product_type' => 'finished', 'short_description' => 'Supporto o contenitore personalizzato per batterie vape.', 'description' => 'Accessorio pratico per completare la linea organizzazione vape.', 'materials' => 'PLA, stampa 3D', 'technique' => 'Stampa 3D', 'price_label' => 'Prezzo su richiesta', 'is_customizable' => 1, 'is_featured' => 0],
];

$findCategory = $pdo->prepare('SELECT id FROM categories WHERE slug = :slug LIMIT 1');
$insertCategory = $pdo->prepare(
    'INSERT INTO categories (name, slug, description, image, sort_order, is_active)
     VALUES (:name, :slug, :description, :image, :sort_order, :is_active)'
);
$findProduct = $pdo->prepare('SELECT id FROM products WHERE slug = :slug LIMIT 1');
$insertProduct = $pdo->prepare(
    'INSERT INTO products (
        category_id, name, slug, sku, product_type, short_description, description, materials, technique,
        production_time_hours, internal_cost, minimum_stock, internal_notes,
        price_label, is_customizable, is_featured, whatsapp_enabled, telegram_enabled, share_enabled, status
     ) VALUES (
        :category_id, :name, :slug, :sku, :product_type, :short_description, :description, :materials, :technique,
        NULL, NULL, NULL, NULL,
        :price_label, :is_customizable, :is_featured, 1, 1, 1, :status
     )'
);

foreach ($categories as $category) {
    $findCategory->execute(['slug' => $category['slug']]);
    if ($findCategory->fetchColumn() !== false) {
        continue;
    }

    $insertCategory->execute([
        'name' => $category['name'],
        'slug' => $category['slug'],
        'description' => $category['description'],
        'image' => null,
        'sort_order' => $category['sort_order'],
        'is_active' => 1,
    ]);
}

$categoryIds = [];
$statement = $pdo->query('SELECT id, slug FROM categories');
foreach ($statement->fetchAll() as $row) {
    $categoryIds[$row['slug']] = (int) $row['id'];
}

foreach ($products as $product) {
    $findProduct->execute(['slug' => $product['slug']]);
    if ($findProduct->fetchColumn() !== false) {
        continue;
    }

    $insertProduct->execute([
        'category_id' => $categoryIds[$product['category_slug']],
        'name' => $product['name'],
        'slug' => $product['slug'],
        'sku' => $product['sku'],
        'product_type' => $product['product_type'],
        'short_description' => $product['short_description'],
        'description' => $product['description'],
        'materials' => $product['materials'],
        'technique' => $product['technique'],
        'price_label' => $product['price_label'],
        'is_customizable' => $product['is_customizable'],
        'is_featured' => $product['is_featured'],
        'status' => 'published',
    ]);
}

echo "Catalogo operativo sincronizzato.\n";
