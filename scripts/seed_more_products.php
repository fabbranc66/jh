<?php

declare(strict_types=1);

$pdo = new PDO('mysql:host=127.0.0.1;dbname=Sql1874742_3;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$categories = [];
foreach ($pdo->query('SELECT id, slug, name, image FROM categories WHERE is_active = 1') as $row) {
    $categories[$row['slug']] = $row;
}

$productsByCategory = [
    'gioielli-wire' => [
        ['Collana Wire Aurora', 'Collana artigianale in wire con linea morbida e dettaglio centrale decorativo.', 'Filo metallico lavorato a mano', 'Wire wrapping', 'Prezzo su richiesta'],
        ['Coppia Orecchini Wire Goccia', 'Orecchini wire dal profilo leggero pensati per uso quotidiano o regalo.', 'Filo metallico e minuteria', 'Wire wrapping', 'Prezzo su richiesta'],
        ['Bracciale Wire Costellazione', 'Bracciale aperto con intreccio wire e tono essenziale.', 'Filo metallico sagomato', 'Wire wrapping', 'Prezzo su richiesta'],
        ['Anello Wire Intreccio Fine', 'Anello lavorato a mano con nodo centrale e finitura delicata.', 'Filo metallico lavorato a mano', 'Wire wrapping', 'Prezzo su richiesta'],
    ],
    'gioielli-resina' => [
        ['Ciondolo Resina Blu Profondo', 'Ciondolo in resina con profondita cromatica e inclusioni decorative.', 'Resina artistica pigmentata', 'Colata e rifinitura manuale', 'Prezzo su richiesta'],
        ['Orecchini Resina Petalo', 'Orecchini in resina con tono floreale e finitura luminosa.', 'Resina artistica e minuteria', 'Colata e assemblaggio', 'Prezzo su richiesta'],
        ['Medaglione Resina Ricordo', 'Medaglione pensato per piccoli inserti, simboli o dettagli commemorativi.', 'Resina artistica trasparente', 'Colata e rifinitura manuale', 'Prezzo su richiesta'],
        ['Segnalibro Resina Aurora', 'Segnalibro in resina dal taglio regalo, leggero e personalizzabile.', 'Resina artistica', 'Colata e lucidatura', 'Prezzo su richiesta'],
    ],
    'gioielli-ibridi' => [
        ['Collana Ibrida Luna', 'Collana che unisce struttura wire e inserto in resina colorata.', 'Wire e resina artistica', 'Tecnica ibrida', 'Prezzo su richiesta'],
        ['Anello Ibrido Riflesso', 'Anello con base wire e piccolo dettaglio in resina.', 'Wire e resina', 'Tecnica ibrida', 'Prezzo su richiesta'],
        ['Orecchini Ibridi Botanici', 'Orecchini con struttura metallica e punto colore in resina decorativa.', 'Wire, resina e minuteria', 'Assemblaggio ibrido', 'Prezzo su richiesta'],
        ['Ciondolo Ibrido Personalizzato', 'Ciondolo ibrido pensato come base per varianti e richieste su misura.', 'Wire e resina artistica', 'Tecnica ibrida', 'Prezzo su richiesta'],
    ],
    'oggettistica-resina' => [
        ['Portagioie Resina Essenziale', 'Piccolo portagioie decorativo in resina per regalo o appoggio.', 'Resina artistica', 'Colata e rifinitura', 'Prezzo su richiesta'],
        ['Set Sottobicchieri Resina', 'Set decorativo in resina per casa o regalo coordinato.', 'Resina artistica', 'Colata in serie ridotta', 'Prezzo su richiesta'],
        ['Targhetta Resina Nome', 'Targhetta in resina con nome o breve testo decorativo.', 'Resina artistica', 'Colata e personalizzazione', 'Prezzo su richiesta'],
        ['Ciotolina Resina Decorativa', 'Piccola ciotolina decorativa da appoggio dal tono creativo.', 'Resina artistica', 'Colata e lucidatura', 'Prezzo su richiesta'],
    ],
    'stampa-3d' => [
        ['Lampada 3D Orbita', 'Lampada stampata in 3D con forma scenografica e stile contemporaneo.', 'PLA e componenti luminosi', 'Stampa 3D', 'Preventivo su richiesta'],
        ['Portapenne 3D Modular', 'Portapenne e organizer da scrivania con disegno pulito.', 'PLA', 'Stampa 3D', 'Prezzo su richiesta'],
        ['Vaso 3D Linea Morbida', 'Vaso decorativo stampato in 3D con profilo moderno.', 'PLA', 'Stampa 3D', 'Prezzo su richiesta'],
        ['Cornice Litofania 3D', 'Cornice o supporto luminoso per litofania personalizzata.', 'PLA e supporto luminoso', 'Stampa 3D', 'Preventivo su richiesta'],
    ],
    'smart-objects' => [
        ['Tag Smart da Scrivania', 'Oggetto da scrivania con accesso rapido a contenuti digitali.', 'Materiali misti con NFC', 'Assemblaggio smart', 'Preventivo su richiesta'],
        ['Segnaposto NFC Evento', 'Segnaposto smart con contenuto digitale collegato.', 'Base personalizzata con tag NFC', 'Assemblaggio smart', 'Preventivo su richiesta'],
        ['Card Smart Profilo Digitale', 'Card personalizzata con apertura diretta a pagina o profilo digitale.', 'Supporto smart con NFC', 'Assemblaggio smart', 'Preventivo su richiesta'],
        ['Accessorio Smart da Regalo', 'Oggetto regalo con collegamento a messaggio, link o contenuto dedicato.', 'Materiali misti con tag NFC', 'Assemblaggio smart', 'Preventivo su richiesta'],
    ],
    'segnaletica-personalizzata' => [
        ['Targa Studio Personalizzata', 'Targa personalizzata per studio, ingresso o ambiente professionale.', 'Materiali misti', 'Taglio e personalizzazione', 'Preventivo su richiesta'],
        ['Placca Nome Porta', 'Placca nome per porta di casa o ufficio.', 'Materiali misti', 'Personalizzazione su misura', 'Prezzo su richiesta'],
        ['Cartello Welcome Decorativo', 'Cartello decorativo per ingresso con stile coordinato.', 'Materiali misti', 'Personalizzazione su misura', 'Prezzo su richiesta'],
        ['Segnaletica Tavolo Evento', 'Piccola segnaletica coordinata per allestimenti o eventi.', 'Materiali misti', 'Personalizzazione su misura', 'Preventivo su richiesta'],
    ],
    'eventi' => [
        ['Bomboniera Smart Evento', 'Bomboniera creativa con dettaglio digitale o personalizzato.', 'Materiali misti', 'Produzione in piccola serie', 'Preventivo su richiesta'],
        ['Segnaposto Resina Cerimonia', 'Segnaposto decorativo per tavolo evento o cerimonia.', 'Resina artistica', 'Produzione in piccola serie', 'Preventivo su richiesta'],
        ['Topper Nome Cerimonia', 'Topper personalizzato per ricorrenze ed eventi speciali.', 'Materiali misti', 'Personalizzazione su misura', 'Preventivo su richiesta'],
        ['Ricordo Coordinato Evento', 'Oggetto ricordo sviluppato come parte di una linea coordinata.', 'Materiali misti', 'Produzione in piccola serie', 'Preventivo su richiesta'],
    ],
    'pet' => [
        ['Portafoto Ricordo Pet', 'Piccolo ricordo dedicato agli animali domestici con tono affettivo.', 'Materiali misti', 'Personalizzazione su misura', 'Prezzo su richiesta'],
        ['Targhetta Pet Casa', 'Targhetta decorativa o identificativa dedicata al tuo animale.', 'Materiali misti', 'Personalizzazione su misura', 'Prezzo su richiesta'],
        ['Segnalino Pet Personalizzato', 'Piccolo accessorio personalizzato con nome o dettaglio grafico.', 'Materiali misti', 'Produzione su richiesta', 'Prezzo su richiesta'],
        ['Ricordo Zampa Decorativo', 'Oggetto ricordo dedicato al legame con il proprio animale.', 'Materiali misti', 'Produzione su richiesta', 'Prezzo su richiesta'],
    ],
    'scanner-3d' => [
        ['Miniatura Famiglia 3D', 'Miniatura 3D ricavata da scansione per ricordo o regalo.', 'Scansione e stampa 3D', 'Scanner 3D', 'Preventivo su richiesta'],
        ['Bustino 3D Personalizzato', 'Riproduzione in miniatura con taglio commemorativo o creativo.', 'Scansione e stampa 3D', 'Scanner 3D', 'Preventivo su richiesta'],
        ['Replica Oggetto Speciale', 'Replica da scansione di oggetto significativo o raro.', 'Scansione 3D', 'Scanner 3D', 'Preventivo su richiesta'],
        ['Modello 3D per Archivio', 'Digitalizzazione di un oggetto per archivio, studio o riproduzione.', 'Scansione 3D', 'Scanner 3D', 'Preventivo su richiesta'],
    ],
    'reverse-engineering' => [
        ['Ricambio Clip Tecnica', 'Ricambio tecnico su misura per piccoli oggetti o alloggiamenti.', 'Materiale tecnico stampabile', 'Reverse engineering', 'Preventivo su richiesta'],
        ['Supporto Adattatore Custom', 'Adattatore progettato partendo da esigenza pratica o componente esistente.', 'Materiale tecnico stampabile', 'Reverse engineering', 'Preventivo su richiesta'],
        ['Piastrina Ricostruita', 'Pezzo ricostruito per sostituzione o ripristino uso.', 'Materiale tecnico stampabile', 'Reverse engineering', 'Preventivo su richiesta'],
        ['Elemento Tecnico da Campione', 'Componente ricostruito partendo da campione o misure fornite.', 'Materiale tecnico stampabile', 'Reverse engineering', 'Preventivo su richiesta'],
    ],
    'accessori-vape' => [
        ['Stand Vape Compact', 'Stand da banco per organizzare dispositivi e accessori vape.', 'Materiali misti', 'Produzione custom', 'Prezzo su richiesta'],
        ['Dock Liquidi Personalizzato', 'Supporto per flaconi e accessori con layout più ordinato.', 'Materiali misti', 'Produzione custom', 'Prezzo su richiesta'],
        ['Supporto Battery Box', 'Supporto semplice per batterie e piccoli accessori da scrivania.', 'Materiali misti', 'Produzione custom', 'Prezzo su richiesta'],
        ['Tray Vape Organizer', 'Tray organizer per accessori, atom e piccoli componenti.', 'Materiali misti', 'Produzione custom', 'Prezzo su richiesta'],
    ],
];

$findProduct = $pdo->prepare('SELECT id FROM products WHERE slug = :slug LIMIT 1');
$insertProduct = $pdo->prepare(
    'INSERT INTO products (
        category_id, name, slug, sku, product_type, short_description, description, materials, technique,
        production_time_hours, internal_cost, minimum_stock, internal_notes,
        price_label, is_customizable, is_featured, whatsapp_enabled, telegram_enabled, share_enabled, status
     ) VALUES (
        :category_id, :name, :slug, :sku, :product_type, :short_description, :description, :materials, :technique,
        NULL, NULL, NULL, :internal_notes,
        :price_label, 1, 0, 1, 1, 1, \'published\'
     )'
);
$insertImage = $pdo->prepare(
    'INSERT INTO product_images (product_id, image_path, alt_text, sort_order, is_primary, created_at)
     VALUES (:product_id, :image_path, :alt_text, 0, 1, NOW())'
);

foreach ($productsByCategory as $categorySlug => $items) {
    if (!isset($categories[$categorySlug])) {
        continue;
    }

    $category = $categories[$categorySlug];

    foreach ($items as $index => [$name, $shortDescription, $materials, $technique, $priceLabel]) {
        $slug = slugify($name);
        $findProduct->execute(['slug' => $slug]);
        if ($findProduct->fetchColumn()) {
            continue;
        }

        $insertProduct->execute([
            'category_id' => $category['id'],
            'name' => $name,
            'slug' => $slug,
            'sku' => 'CAT' . str_pad((string) $category['id'], 2, '0', STR_PAD_LEFT) . '-' . str_pad((string) ($index + 101), 3, '0', STR_PAD_LEFT),
            'product_type' => 'finished',
            'short_description' => $shortDescription,
            'description' => $shortDescription . ' Pensato come articolo di riferimento per richieste personalizzate e varianti della stessa linea.',
            'materials' => $materials,
            'technique' => $technique,
            'internal_notes' => 'Articolo seed aggiunto automaticamente per ampliare la categoria.',
            'price_label' => $priceLabel,
        ]);

        $productId = (int) $pdo->lastInsertId();
        $imagePath = $category['image'] ?: 'assets/images/logo_jh.png';
        $insertImage->execute([
            'product_id' => $productId,
            'image_path' => $imagePath,
            'alt_text' => $name,
        ]);
    }
}

echo "Prodotti aggiuntivi creati.\n";

function slugify(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
    return trim($value, '-') ?: 'prodotto';
}
