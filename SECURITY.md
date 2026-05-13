# Sicherheitshinweise / Security Policy

## Architektur

KBS ist für den Einsatz in **lokalen, vertrauenswürdigen Heimnetzwerken** konzipiert. Es gibt bewusst keine Authentifizierung oder Verschlüsselung, da das System nur innerhalb des eigenen Netzwerks kommuniziert.

## Bekannte Einschränkungen

### Keine Authentifizierung
- Die Flask REST-API auf den Raspberry Pis akzeptiert Anfragen von jedem Gerät im Netzwerk
- Die PHP-Weboberfläche hat keinen Login-Schutz
- **Empfehlung:** KBS nicht im öffentlichen Internet betreiben. Bei Bedarf einen Reverse Proxy mit Basic Auth oder VPN vorschalten.

### Keine Verschlüsselung (kein HTTPS)
- Die Kommunikation zwischen Webserver und Raspberry Pis erfolgt über unverschlüsseltes HTTP
- **Empfehlung:** Nur in vertrauenswürdigen Netzwerken verwenden.

### Flask Development Server
- Der eingebaute Flask-Server ist nicht für Produktionsumgebungen gedacht
- **Empfehlung:** Für kritischere Einsätze einen WSGI-Server wie `gunicorn` verwenden.

## Sicherheitslücken melden

Falls eine Sicherheitslücke gefunden wird, bitte direkt den Projektbetreiber kontaktieren und nicht als öffentliches Issue melden.
