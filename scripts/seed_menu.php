<?php

declare(strict_types=1);

$pdo = new PDO('mysql:host=127.0.0.1;dbname=Sql1874742_3;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec(
    'CREATE TABLE IF NOT EXISTS menu_items (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        parent_id INT UNSIGNED NULL,
        label VARCHAR(150) NOT NULL,
        url VARCHAR(255) NOT NULL,
        sort_order INT NOT NULL DEFAULT 0,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_menu_parent (parent_id),
        KEY idx_menu_active_sort (is_active, sort_order),
        CONSTRAINT fk_menu_parent FOREIGN KEY (parent_id) REFERENCES menu_items (id) ON UPDATE CASCADE ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
);

$pdo->exec('DELETE FROM menu_items');

$menuItems = [
    [1, null, 'Home', '/', 10, 1],
    [2, null, 'Gioielli', '/categoria/gioielli-wire', 20, 1],
    [3, 2, 'Gioielli wire', '/categoria/gioielli-wire', 10, 1],
    [4, 2, 'Gioielli in resina', '/categoria/gioielli-resina', 20, 1],
    [5, 2, 'Gioielli ibridi', '/categoria/gioielli-ibridi', 30, 1],
    [6, 2, 'Pezzi personalizzabili', '/personalizzazioni', 40, 1],
    [7, 2, 'Ciondoli', '/scopri/ciondoli', 50, 1],
    [8, 2, 'Orecchini', '/scopri/orecchini', 60, 1],
    [9, 2, 'Bracciali', '/scopri/bracciali', 70, 1],
    [10, 2, 'Anelli', '/scopri/anelli', 80, 1],
    [11, null, 'Decorazioni', '/categoria/oggettistica-resina', 30, 1],
    [12, 11, 'Portachiavi', '/scopri/portachiavi', 10, 1],
    [13, 11, 'Segnalibri', '/scopri/segnalibri', 20, 1],
    [14, 11, 'Decorazioni', '/scopri/decorazioni', 30, 1],
    [15, 11, 'Quadretti', '/scopri/quadretti', 40, 1],
    [16, 11, 'Lampade', '/scopri/lampade', 50, 1],
    [17, 11, 'Vasi', '/scopri/vasi', 60, 1],
    [18, 11, 'Organizer', '/scopri/organizer', 70, 1],
    [19, 11, 'Litofanie', '/scopri/litofanie', 80, 1],
    [20, null, 'Eventi', '/categoria/eventi', 40, 1],
    [21, 20, 'Bomboniere', '/scopri/bomboniere', 10, 1],
    [22, 20, 'Segnaposto', '/scopri/segnaposto', 20, 1],
    [23, 20, 'Cake topper', '/scopri/cake-topper', 30, 1],
    [24, 20, 'Ricordi', '/scopri/ricordi-evento', 40, 1],
    [25, 20, 'Matrimoni', '/scopri/matrimoni', 50, 1],
    [26, 20, 'Battesimi', '/scopri/battesimi', 60, 1],
    [27, 20, 'Comunioni', '/scopri/comunioni', 70, 1],
    [28, 20, 'Cresime', '/scopri/cresime', 80, 1],
    [29, null, 'Laboratorio', '/laboratorio', 50, 1],
    [30, 29, 'Chi siamo', '/scopri/chi-siamo', 10, 1],
    [31, 29, 'Come lavoriamo', '/scopri/come-lavoriamo', 20, 1],
    [32, 29, 'Artigianato e digitale', '/scopri/artigianato-e-digitale', 30, 1],
    [33, 29, 'Richieste personalizzate', '/personalizzazioni', 40, 1],
    [34, 29, 'Wire wrapping', '/scopri/wire-wrapping', 50, 1],
    [35, 29, 'Resina artistica', '/scopri/resina-artistica', 60, 1],
    [36, 29, 'Stampa 3D', '/scopri/stampa-3d-lavorazioni', 70, 1],
    [37, 29, 'Smart objects', '/scopri/smart-objects-linea', 80, 1],
    [38, null, 'Smart & 3D', '/categoria/scanner-3d', 60, 1],
    [39, 38, 'NFC / RFID', '/scopri/nfc-rfid', 10, 1],
    [40, 38, 'Scanner 3D', '/scopri/scanner-3d-servizi', 20, 1],
    [41, 38, 'Reverse engineering', '/scopri/reverse-engineering-servizi', 30, 1],
    [42, 38, 'Stampa 3D servizi', '/scopri/stampa-3d-servizi', 40, 1],
    [43, 38, 'Oggetti smart', '/scopri/oggetti-smart', 50, 1],
    [44, 38, 'Miniature', '/scopri/miniature', 60, 1],
    [45, 38, 'Ricambi tecnici', '/scopri/ricambi-tecnici', 70, 1],
    [46, 38, 'Accessori personalizzati', '/scopri/accessori-personalizzati', 80, 1],
    [47, null, 'Catalogo', '/catalogo', 70, 1],
    [48, null, 'Contatti', '/contatti', 80, 1],
];

$insertMenu = $pdo->prepare(
    'INSERT INTO menu_items (id, parent_id, label, url, sort_order, is_active)
     VALUES (:id, :parent_id, :label, :url, :sort_order, :is_active)'
);

foreach ($menuItems as [$id, $parentId, $label, $url, $sortOrder, $isActive]) {
    $insertMenu->execute([
        'id' => $id,
        'parent_id' => $parentId,
        'label' => $label,
        'url' => $url,
        'sort_order' => $sortOrder,
        'is_active' => $isActive,
    ]);
}

$pages = [
    ['Catalogo', 'catalogo', 'Il catalogo raccoglie linee, prodotti e punti di partenza per richieste personalizzate sviluppate insieme.', 'Catalogo | JH', 'Catalogo JH con linee creative, prodotti e richieste personalizzate.'],
    ['Contatti', 'contatti', 'Una pagina per contatto diretto, richieste prodotto, personalizzazioni e idee su misura legate al laboratorio.', 'Contatti | JH', 'Contatti e richieste personalizzate del laboratorio JH.'],
    ['Personalizzazioni', 'personalizzazioni', 'Una panoramica sulle richieste su misura, dalle piccole varianti fino a oggetti personalizzati per regalo, evento o uso specifico.', 'Personalizzazioni | JH', 'Prodotti personalizzabili e richieste su misura del laboratorio JH.'],
    ['Ciondoli', 'ciondoli', 'Una selezione di ciondoli artigianali tra wire, resina e combinazioni personalizzabili pensate per regalo, ricordo o piccola collezione.', 'Ciondoli JH', 'Ciondoli artigianali e personalizzabili tra wire e resina.'],
    ['Orecchini', 'orecchini', 'Orecchini leggeri, decorativi e personalizzabili, con linee essenziali o piu creative a seconda del progetto richiesto.', 'Orecchini JH', 'Orecchini handmade e personalizzabili del laboratorio JH.'],
    ['Bracciali', 'bracciali', 'Bracciali wire e interpretazioni su richiesta da usare come base per varianti, regali e piccole serie coordinate.', 'Bracciali JH', 'Bracciali artigianali personalizzabili del laboratorio JH.'],
    ['Anelli', 'anelli', 'Anelli realizzati a mano e su richiesta, con forme semplici o piu decorative, pensati per uno stile leggero e contemporaneo.', 'Anelli JH', 'Anelli artigianali e personalizzabili del laboratorio JH.'],
    ['Portachiavi', 'portachiavi', 'Portachiavi decorativi e personalizzati tra resina, stampa 3D e dettagli speciali da adattare a occasione, nome o tema.', 'Portachiavi JH', 'Portachiavi personalizzati e creativi del laboratorio JH.'],
    ['Segnalibri', 'segnalibri', 'Segnalibri creativi in resina e materiali misti, ideali per idee regalo, piccole collezioni o coordinati evento.', 'Segnalibri JH', 'Segnalibri artigianali e personalizzabili del laboratorio JH.'],
    ['Decorazioni', 'decorazioni', 'Decorazioni da appoggio, piccoli oggetti casa e pezzi creativi sviluppati come referenze per varianti piu su misura.', 'Decorazioni JH', 'Decorazioni artigianali e oggetti casa del laboratorio JH.'],
    ['Quadretti', 'quadretti', 'Quadretti e piccoli pannelli decorativi con tono creativo, pensati per regalo, ricordo o ambientazione personale.', 'Quadretti JH', 'Quadretti e pannelli decorativi del laboratorio JH.'],
    ['Lampade', 'lampade', 'Lampade decorative e soluzioni luminose stampate in 3D o integrate con dettagli creativi, da usare come base per richieste personalizzate.', 'Lampade JH', 'Lampade decorative e personalizzabili del laboratorio JH.'],
    ['Vasi', 'vasi', 'Vasi e oggetti casa in stampa 3D con linee contemporanee, pronti a evolvere in varianti colore, forma o dimensione.', 'Vasi JH', 'Vasi decorativi e stampati in 3D del laboratorio JH.'],
    ['Organizer', 'organizer', 'Organizer e supporti funzionali dal taglio creativo, adatti a scrivania, casa e piccole necessita quotidiane.', 'Organizer JH', 'Organizer creativi e personalizzabili del laboratorio JH.'],
    ['Litofanie', 'litofanie', 'Litofanie e oggetti luminosi su immagine, perfetti per ricordi, regali e interpretazioni piu emozionali della stampa 3D.', 'Litofanie JH', 'Litofanie personalizzate del laboratorio JH.'],
    ['Bomboniere', 'bomboniere', 'Bomboniere coordinate e personalizzabili per eventi, con tono artigianale ma ordinato, pensate per piccole serie curate.', 'Bomboniere JH', 'Bomboniere personalizzate per eventi e cerimonie.'],
    ['Segnaposto', 'segnaposto', 'Segnaposto e piccoli coordinati evento da adattare per stile, colore, nome, tema e tipo di cerimonia.', 'Segnaposto JH', 'Segnaposto personalizzati del laboratorio JH.'],
    ['Cake topper', 'cake-topper', 'Cake topper creativi e personalizzati per cerimonie, ricorrenze ed eventi, con forme e dettagli sviluppati su richiesta.', 'Cake topper JH', 'Cake topper personalizzati del laboratorio JH.'],
    ['Ricordi evento', 'ricordi-evento', 'Piccoli oggetti ricordo per eventi e momenti speciali, pensati come base per richieste piu dedicate e coordinate.', 'Ricordi evento JH', 'Oggetti ricordo e coordinati evento del laboratorio JH.'],
    ['Matrimoni', 'matrimoni', 'Una panoramica dedicata a richieste per matrimoni: bomboniere, segnaposto, topper e dettagli ricordo coordinati.', 'Matrimoni JH', 'Idee personalizzate per matrimoni e coordinati evento.'],
    ['Battesimi', 'battesimi', 'Soluzioni leggere e personalizzabili per battesimi, con proposte coordinate da adattare a stile e occasione.', 'Battesimi JH', 'Idee personalizzate per battesimi del laboratorio JH.'],
    ['Comunioni', 'comunioni', 'Proposte e spunti per comunioni con dettagli personalizzati, piccoli oggetti ricordo e allestimenti coerenti.', 'Comunioni JH', 'Idee personalizzate per comunioni del laboratorio JH.'],
    ['Cresime', 'cresime', 'Una linea di partenza per richieste legate alla cresima, con piccoli oggetti, topper e soluzioni personalizzate.', 'Cresime JH', 'Idee personalizzate per cresime del laboratorio JH.'],
    ['Chi siamo', 'chi-siamo', 'JH nasce come laboratorio creativo che unisce manualita, materiali artistici e strumenti digitali per costruire oggetti e idee personalizzate.', 'Chi siamo | JH', 'Il progetto JH e il suo approccio tra artigianato e digitale.'],
    ['Come lavoriamo', 'come-lavoriamo', 'Il laboratorio lavora per esempi, dialogo e adattamento progressivo: ogni scheda puo diventare punto di partenza per una variante concreta.', 'Come lavoriamo | JH', 'Metodo di lavoro del laboratorio JH tra catalogo e richieste personalizzate.'],
    ['Artigianato e digitale', 'artigianato-e-digitale', 'Manualita e tecnologie convivono nel progetto: wire, resina, stampa 3D, scanner 3D e dettagli smart si combinano in base alla richiesta.', 'Artigianato e digitale | JH', 'L incontro tra lavorazione manuale e strumenti digitali nel laboratorio JH.'],
    ['Wire wrapping', 'wire-wrapping', 'Una pagina dedicata alla lavorazione wire wrapping come tecnica, stile e base per creazioni leggere o piu decorative.', 'Wire wrapping | JH', 'Approfondimento sulla lavorazione wire wrapping del laboratorio JH.'],
    ['Resina artistica', 'resina-artistica', 'Resina artistica, inclusioni, trasparenze e colori come linguaggio visivo del laboratorio per oggetti piccoli ma molto personalizzabili.', 'Resina artistica | JH', 'Approfondimento sulla resina artistica nel laboratorio JH.'],
    ['Stampa 3D lavorazioni', 'stampa-3d-lavorazioni', 'La stampa 3D viene usata come strumento creativo e produttivo per oggetti casa, supporti, decorazioni e richieste piu sperimentali.', 'Stampa 3D | JH', 'Lavorazioni in stampa 3D del laboratorio JH.'],
    ['Smart objects', 'smart-objects-linea', 'Oggetti smart, tag NFC e piccoli elementi digitali integrati in un linguaggio artigianale ma contemporaneo.', 'Smart objects | JH', 'Linea smart del laboratorio JH tra oggetti fisici e contenuti digitali.'],
    ['NFC / RFID', 'nfc-rfid', 'Applicazioni NFC e RFID per piccoli oggetti smart, biglietti, portachiavi e soluzioni ibride tra fisico e digitale.', 'NFC e RFID | JH', 'Soluzioni NFC e RFID del laboratorio JH.'],
    ['Scanner 3D servizi', 'scanner-3d-servizi', 'Servizi di scansione 3D, miniature, repliche e base digitale per progetti personalizzati o ricostruzioni.', 'Scanner 3D | JH', 'Servizi di scanner 3D e miniature del laboratorio JH.'],
    ['Reverse engineering servizi', 'reverse-engineering-servizi', 'Ricostruzione pezzi, ricambi plastici e piccoli componenti tecnici partendo da esigenze concrete e adattamenti su misura.', 'Reverse engineering | JH', 'Servizi di reverse engineering e ricostruzione pezzi del laboratorio JH.'],
    ['Stampa 3D servizi', 'stampa-3d-servizi', 'Applicazioni operative della stampa 3D per prototipi, oggetti utili, piccole serie e supporti personalizzati.', 'Servizi stampa 3D | JH', 'Servizi e applicazioni operative di stampa 3D del laboratorio JH.'],
    ['Oggetti smart', 'oggetti-smart', 'Oggetti che uniscono fisico e contenuto digitale, pensati come regalo, supporto informativo o esperienza piu contemporanea.', 'Oggetti smart | JH', 'Oggetti smart e personalizzabili del laboratorio JH.'],
    ['Miniature', 'miniature', 'Miniature e repliche in scala sviluppate tramite scansione, modellazione e stampa 3D, con approccio creativo o commemorativo.', 'Miniature | JH', 'Miniature personalizzate del laboratorio JH.'],
    ['Ricambi tecnici', 'ricambi-tecnici', 'Una sezione dedicata a ricambi, adattatori e componenti tecnici ricostruiti o ripensati per casi specifici.', 'Ricambi tecnici | JH', 'Ricambi tecnici e adattatori personalizzati del laboratorio JH.'],
    ['Accessori personalizzati', 'accessori-personalizzati', 'Accessori personalizzati sviluppati su richiesta, dal piccolo supporto tecnico all oggetto decorativo-funzionale.', 'Accessori personalizzati | JH', 'Accessori personalizzati del laboratorio JH.'],
];

$upsertPage = $pdo->prepare(
    'INSERT INTO pages (title, slug, content, meta_title, meta_description, image_path, is_active)
     VALUES (:title, :slug, :content, :meta_title, :meta_description, :image_path, 1)
     ON DUPLICATE KEY UPDATE
        title = VALUES(title),
        content = VALUES(content),
        meta_title = VALUES(meta_title),
        meta_description = VALUES(meta_description),
        image_path = COALESCE(pages.image_path, VALUES(image_path)),
        is_active = 1,
        updated_at = CURRENT_TIMESTAMP'
);

foreach ($pages as [$title, $slug, $content, $metaTitle, $metaDescription]) {
    $upsertPage->execute([
        'title' => $title,
        'slug' => $slug,
        'content' => $content,
        'meta_title' => $metaTitle,
        'meta_description' => $metaDescription,
        'image_path' => null,
    ]);
}

echo "Menu e pagine seed completati.\n";
