# Driemusketiers – login

Starten (PHP 8+ met pdo_sqlite):

    cd driemusketiers
    php -S localhost:8000

Open http://localhost:8000. De database (data/driemusketiers.sqlite) wordt automatisch aangemaakt.
Zet bij live gebruik de map `data/` buiten de webroot en gebruik HTTPS.

## Admin panel
1. Registreer eerst een account op de site.
2. Maak dat account admin: `php make_admin.php jouw@email.nl`
3. Log in; op het dashboard staat nu de knop "Admin panel" (of ga naar /admin/index.php).