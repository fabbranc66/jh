# Deploy Aruba

Questa applicazione e pronta per hosting condiviso Aruba Linux senza SSH, usando la root del sito come entry point.

## Requisiti

- PHP 8.2 o superiore
- MySQL/MariaDB
- upload FTP o File Manager Aruba

## Struttura consigliata su Aruba

Carica nella root del dominio:

- `app/`
- `config/`
- `public/`
- `storage/`
- `vendor/`
- `index.php`
- `.htaccess`
- `.env`

## Note importanti

- Il sito pubblico parte da `index.php` in root.
- Le richieste verso `assets/*` e `upload/*` vengono inoltrate internamente a `public/`.
- Cartelle come `app`, `config`, `storage` e `vendor` sono bloccate via `.htaccess`.
- Gli URL vengono calcolati dinamicamente dall'host della richiesta, quindi funzionano sia su dominio finale sia in sottocartella.

## Passi pratici

1. Esegui `composer install --no-dev --optimize-autoloader` in locale.
2. Verifica che `vendor/` sia presente.
3. Mantieni `.env` locale per XAMPP e crea su Aruba un `.env` basato su `.env.aruba.example`.
4. Crea il database Aruba e importa `database.sql`.
5. Carica tutto via FTP/File Manager nella root del dominio o della sottocartella.
6. In Aruba imposta PHP 8.2 o superiore dal pannello Hosting.

## File ambiente consigliati

- `/.env`:
  usalo solo in locale con XAMPP
- `/.env.aruba.example`:
  modello da copiare in `.env` sul server Aruba

## .env esempio produzione Aruba

```env
APP_NAME="JH"
APP_ENV=production
APP_DEBUG=false
APP_URL="https://tuodominio.it"
APP_TIMEZONE="Europe/Rome"
APP_ASSET_VERSION="20260314-2"

DB_HOST=31.11.39.231
DB_HOSTS=31.11.39.231,127.0.0.1
DB_PORT=3306
DB_DATABASE=Sql1874742_3
DB_USERNAME=Sql1874742
DB_PASSWORD=INSERISCI_PASSWORD_ARUBA

ADMIN_USERNAME=admin
ADMIN_PASSWORD_HASH=INSERISCI_HASH_PASSWORD
```

## Regola pratica

- In locale non usare il database Aruba dentro `/.env`, altrimenti se il server remoto non risponde il sito locale si blocca.
- In produzione Aruba non usare le credenziali locali di XAMPP.
- Se l'host MySQL assegnato non risponde dal server Aruba, il progetto prova automaticamente anche `127.0.0.1`.
- Evitiamo `localhost` su Linux hosting perché PDO MySQL puo tentare la socket locale e restituire `No such file or directory`, che confonde la diagnosi.

## Dopo il deploy

- Controlla home, catalogo, contatti e admin login.
- Se aggiorni CSS/JS/logo, incrementa `APP_ASSET_VERSION`.
