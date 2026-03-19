<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\PageService;
use App\Services\ProductService;

final class PageController
{
    public function __construct(
        private View $view,
        private PageService $pages,
        private ProductService $products
    ) {
    }

    public function show(string $slug): void
    {
        if ($slug === 'laboratorio') {
            $page = $this->pages->findBySlug('laboratorio');
            $this->view->render('pages/laboratory.twig', [
                'pageTitle' => 'Laboratorio creativo digitale',
                'pageDescription' => 'Laboratorio creativo tra handmade, resina, stampa 3D e personalizzazione su richiesta.',
                'pageBandClass' => 'page-band--laboratory',
                'page' => $page,
            ]);
            return;
        }

        if ($slug === 'personalizzazioni') {
            $page = $this->pages->findBySlug('personalizzazioni');
            $this->view->render('pages/customizations.twig', [
                'pageTitle' => 'Personalizzazioni su richiesta',
                'pageDescription' => 'Nomi, iniziali, colori, incisioni, temi evento e varianti su misura per i prodotti JH.',
                'pageBandClass' => 'page-band--custom',
                'page' => $page,
            ]);
            return;
        }

        $page = $this->pages->findBySlug($slug);

        if ($page === null) {
            http_response_code(404);

            $this->view->render('pages/not-found.twig', [
                'pageTitle' => 'Pagina non trovata',
                'message' => 'La pagina richiesta non e disponibile.',
            ]);
            return;
        }

        $this->view->render('pages/page.twig', [
            'pageTitle' => $page['meta_title'] ?: $page['title'],
            'page' => $page,
            'pageBandClass' => $this->bandClassForSlug($slug),
            'relatedProducts' => $this->relatedProductsForSlug($slug),
        ]);
    }

    private function bandClassForSlug(string $slug): string
    {
        return match ($slug) {
            'ciondoli', 'orecchini', 'bracciali', 'anelli' => 'page-band--jewelry',
            'portachiavi', 'segnalibri', 'decorazioni', 'quadretti', 'lampade', 'vasi', 'organizer', 'litofanie' => 'page-band--decor',
            'bomboniere', 'segnaposto', 'cake-topper', 'ricordi-evento', 'matrimoni', 'battesimi', 'comunioni', 'cresime' => 'page-band--events',
            'chi-siamo', 'come-lavoriamo', 'artigianato-e-digitale', 'wire-wrapping', 'resina-artistica', 'stampa-3d-lavorazioni', 'smart-objects-linea' => 'page-band--laboratory',
            'nfc-rfid', 'scanner-3d-servizi', 'reverse-engineering-servizi', 'stampa-3d-servizi', 'oggetti-smart', 'miniature', 'ricambi-tecnici', 'accessori-personalizzati' => 'page-band--smart',
            default => 'page-band--catalog',
        };
    }

    private function relatedProductsForSlug(string $slug): array
    {
        $map = [
            'ciondoli' => ['ciondolo-luna-wire', 'ciondolo-resina-botanica', 'ciondolo-ibrido-wire-resina', 'ciondolo-resina-blu-profondo'],
            'orecchini' => ['orecchini-wire-essenziali', 'orecchini-resina-trasparente', 'orecchini-ibridi-wire-resina', 'coppia-orecchini-wire-goccia'],
            'bracciali' => ['bracciale-wire-nodo', 'bracciale-wire-costellazione'],
            'anelli' => ['anello-wire-spirale', 'anello-wire-intreccio-fine', 'anello-ibrido-riflesso'],
            'portachiavi' => ['portachiavi-resina-personalizzato', 'portachiavi-nfc-personalizzato', 'targhetta-resina-nome', 'ciotolina-resina-decorativa'],
            'segnalibri' => ['segnalibro-resina-botanica', 'segnalibro-resina-floreale', 'segnalibro-resina-aurora'],
            'decorazioni' => ['decorazione-resina-da-appoggio', 'quadretto-resina-decorativo', 'portagioie-resina-essenziale', 'set-sottobicchieri-resina'],
            'quadretti' => ['quadretto-resina-decorativo', 'decorazione-resina-da-appoggio', 'oggetto-ricordo-evento', 'cartello-decorativo-personalizzato'],
            'lampade' => ['lampada-luna-3d', 'lampada-smart-con-contenuti-digitali', 'lampada-3d-orbita', 'paralume-3d-geometrico'],
            'vasi' => ['vaso-3d-minimal', 'vaso-3d-linea-morbida', 'scultura-3d-contemporanea', 'decorazione-resina-da-appoggio'],
            'organizer' => ['organizer-scrivania-3d', 'portapenne-3d-modular', 'supporto-smartphone-3d', 'fermalibri-3d'],
            'litofanie' => ['litofania-3d-personalizzata', 'cornice-litofania-3d', 'lampada-luna-3d'],
            'bomboniere' => ['bomboniere-personalizzate', 'bomboniera-smart-evento', 'oggetto-ricordo-evento', 'segnaposto-personalizzati'],
            'segnaposto' => ['segnaposto-personalizzati', 'segnaposto-resina-cerimonia', 'bomboniere-personalizzate', 'cake-topper-personalizzato'],
            'cake-topper' => ['cake-topper-personalizzato', 'topper-nome-cerimonia', 'oggetto-ricordo-evento', 'segnaposto-personalizzati'],
            'ricordi-evento' => ['oggetto-ricordo-evento', 'ricordo-coordinato-evento', 'bomboniere-personalizzate', 'segnaposto-personalizzati'],
            'matrimoni' => ['bomboniere-personalizzate', 'bomboniera-smart-evento', 'segnaposto-personalizzati', 'cake-topper-personalizzato'],
            'battesimi' => ['bomboniere-personalizzate', 'oggetto-ricordo-evento', 'segnaposto-personalizzati', 'bomboniera-smart-evento'],
            'comunioni' => ['bomboniere-personalizzate', 'segnaposto-personalizzati', 'cake-topper-personalizzato', 'ricordo-coordinato-evento'],
            'cresime' => ['cake-topper-personalizzato', 'oggetto-ricordo-evento', 'bomboniere-personalizzate', 'ricordo-coordinato-evento'],
            'chi-siamo' => ['ciondolo-luna-wire', 'ciondolo-resina-botanica', 'lampada-luna-3d', 'oggetto-smart-con-link-digitale'],
            'come-lavoriamo' => ['collana-wire-aurora', 'portagioie-resina-essenziale', 'lampada-3d-orbita', 'tag-smart-da-scrivania'],
            'artigianato-e-digitale' => ['ciondolo-ibrido-wire-resina', 'lampada-smart-con-contenuti-digitali', 'miniatura-persona-da-scanner-3d', 'oggetto-smart-con-link-digitale'],
            'wire-wrapping' => ['ciondolo-luna-wire', 'collana-wire-aurora', 'bracciale-wire-nodo', 'anello-wire-spirale'],
            'resina-artistica' => ['ciondolo-resina-botanica', 'medaglione-resina-memoria', 'portagioie-resina-essenziale', 'segnalibro-resina-botanica'],
            'stampa-3d-lavorazioni' => ['lampada-luna-3d', 'lampada-3d-orbita', 'vaso-3d-minimal', 'organizer-scrivania-3d'],
            'smart-objects-linea' => ['biglietto-da-visita-nfc', 'oggetto-smart-con-link-digitale', 'tag-smart-da-scrivania', 'card-smart-profilo-digitale'],
            'nfc-rfid' => ['biglietto-da-visita-nfc', 'portachiavi-nfc-personalizzato', 'tag-smart-da-scrivania', 'card-smart-profilo-digitale'],
            'scanner-3d-servizi' => ['miniatura-persona-da-scanner-3d', 'miniatura-animale-da-scanner-3d', 'miniatura-famiglia-3d', 'replica-oggetto-da-scanner-3d'],
            'reverse-engineering-servizi' => ['ricostruzione-pezzo-rotto', 'ricambio-plastico-personalizzato', 'ricambio-clip-tecnica', 'supporto-adattatore-custom'],
            'stampa-3d-servizi' => ['lampada-3d-orbita', 'portapenne-3d-modular', 'vaso-3d-linea-morbida', 'cornice-litofania-3d'],
            'oggetti-smart' => ['oggetto-smart-con-link-digitale', 'lampada-smart-con-contenuti-digitali', 'tag-smart-da-scrivania', 'accessorio-smart-da-regalo'],
            'miniature' => ['miniatura-persona-da-scanner-3d', 'miniatura-animale-da-scanner-3d', 'miniatura-famiglia-3d', 'bustino-3d-personalizzato'],
            'ricambi-tecnici' => ['ricambio-plastico-personalizzato', 'ricostruzione-pezzo-rotto', 'ricambio-clip-tecnica', 'elemento-tecnico-da-campione'],
            'accessori-personalizzati' => ['stand-vape-personalizzato', 'dock-liquidi-personalizzato', 'supporto-battery-box', 'tray-vape-organizer'],
        ];

        return isset($map[$slug]) ? $this->products->bySlugs($map[$slug]) : [];
    }
}
