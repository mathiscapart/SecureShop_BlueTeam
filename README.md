# Documentation Projet — SecureShop Blue Team

## Contexte et Objectifs

Ce projet s'inscrit dans un contexte de mise en situation réaliste d'une équipe Blue Team face à une application web volontairement vulnérable. L'application cible, **SecureShop**, est une boutique e-commerce développée en PHP exposant intentionnellement les principales failles de sécurité du référentiel **OWASP Top 10** : XSS stocké et réfléchi, CSRF, injection SQL, IDOR et broken authentication.

L'objectif est de construire une stack de sécurité complète permettant de **détecter, alerter et répondre** aux attaques en temps réel, sans modifier l'application cible. La stack repose sur des outils open source déployés via Docker, organisés en trois périmètres distincts : protection réseau (WAF), observabilité (Monitoring) et détection d'intrusion (SIEM).

---

## Choix Technologiques

### Tableau récapitulatif des outils retenus

| Catégorie | Outil retenu | Alternatives étudiées | Raison du choix |
| --- | --- | --- | --- |
| WAF | BunkerWeb | Coraza, ModSecurity | Solution tout-en-un (WAF + reverse proxy + hardening), déploiement Docker natif, anti-bot intégré |
| Reverse Proxy | Intégré à BunkerWeb | Caddy, Traefik | Évite une couche supplémentaire, BunkerWeb embarque le reverse proxy |
| Collecteur de logs | OpenObserve | ELK Stack, Loki | Léger, self-hosted, logs + métriques + traces dans un seul outil |
| Monitoring / Métriques | Prometheus + Grafana | — | Standards du marché, connaissance préalable de l'équipe, écosystème riche |
| Disponibilité | Blackbox Exporter | — | Sonde HTTP native pour Prometheus, indispensable pour alerter sur une panne |
| Alerting | Alertmanager + OpenObserve Alerting +
Grafana Alerting | — | Alertmanager pour les métriques Prometheus (silences, routing), OpenObserve pour les alertes sur logs |
| SIEM | Wazuh | Splunk, Graylog | Open source, SIEM + XDR + réponse active, pas de coût de licence, intégration Docker |

---

### WAF

Trois solutions WAF ont été étudiées :

- **Coraza** : WAF moderne en Go, embarquable dans Caddy ou Traefik. Compatible OWASP CRS. Idéal pour une stack cloud-native mais nécessite un reverse proxy séparé.
- **ModSecurity** : WAF historique et éprouvé, très documenté, s'intègre à Nginx/Apache. Pertinent sur une infra Nginx existante.
- **BunkerWeb** ✅ : Solution tout-en-un combinant reverse proxy, WAF (ModSecurity + OWASP CRS) et durcissement HTTP. Orientée Docker/Kubernetes, elle intègre nativement l'anti-bot et le blocage d'IP — fonctionnalités absentes des alternatives dans une configuration aussi simple. C'est le choix le plus cohérent pour sécuriser rapidement l'application sans multiplier les couches d'infrastructure.

---

### Collecteur de Logs

Trois solutions ont été comparées :

- **Elasticsearch (ELK Stack)** : Très puissant pour la recherche full-text et les grands volumes, mais gourmand en ressources dans un environnement self-hosted.
- **Loki** : Léger, indexe uniquement les labels. Pertinent en complément de Prometheus/Grafana mais limité pour l'analyse de contenu.
- **OpenObserve** ✅ : Alternative moderne à l'ELK, avec logs, métriques et traces dans un seul binaire. Particulièrement adapté à un déploiement self-hosted avec contraintes de ressources. Aucune stack complémentaire nécessaire.

---

### Monitoring

- **Grafana** : Interface de visualisation universelle, compatible avec Prometheus, Loki et OpenObserve. Interface centrale de supervision pour les dashboards et l'observabilité.
- **Prometheus** : Standard pour la collecte de métriques, modèle pull avec stockage TSDB. Connecté nativement à la majorité des exporters.
- **Blackbox Exporter** : Sonde des endpoints HTTP/HTTPS pour vérifier la disponibilité du site depuis l'extérieur. Indispensable pour alerter sur une panne de SecureShop.

L'ensemble de ces outils est maîtrisé par l'équipe, ce qui a également guidé ce choix.

---

### Alerting

- **Grafana Alerting** ✅ : Alertes directement liées aux dashboards, support Slack/email/webhook. Écarté au profit d'Alertmanager pour éviter la duplication.
- **OpenObserve Alerting** ✅ : Alertes natives sur les logs ingérés dans OpenObserve. Utilisé pour les alertes applicatives (attaques, anomalies de logs).
- **Alertmanager** ✅ : Gère le routage, la déduplication et la mise en silence des alertes Prometheus. Retenu pour sa fonctionnalité de **silence** essentielle en phase de maintenance.

---

### SIEM

- **Splunk Enterprise Security** : Référence SOC entreprise, très riche fonctionnellement, mais coûteux et surdimensionné pour ce projet.
- **Graylog Security** : Orienté centralisation et investigation, pragmatique mais sans réponse active native.
- **Wazuh** ✅ : Plateforme open source SIEM + XDR avec détection, monitoring, alerting et réponse active. Sans coût de licence, bien intégrée aux environnements open source et Docker. Sa capacité de réponse active (ex: suppression automatique de fichiers malveillants) en fait le choix le plus adapté au scénario SecureShop.

---

## Installation et Configuration

### Infrastructure Docker

Le projet est décomposé en quatre stacks Docker indépendantes, chacune avec son propre fichier `docker-compose.yml` :

| Stack | Dossier | Contenu |
| --- | --- | --- |
| Application cible | `secureShop/` | Application PHP + base MySQL |
| WAF | `WAF/` | BunkerWeb, scheduler, UI, MariaDB, Redis |
| Monitoring | `monitoring/` | Fluent-Bit, OpenObserve, Loki, Grafana, Prometheus, Alertmanager, Blackbox |
| SIEM | `SIEM/` | Wazuh Manager, Wazuh Agent |

La **segmentation réseau** est assurée via des réseaux Docker nommés :

- `blue-team` : réseau partagé entre toutes les stacks pour la collecte de logs et la communication SIEM
- `bw-universe` : réseau interne BunkerWeb (subnet `10.20.30.0/24`)
- `bw-services` : réseau entre BunkerWeb et les applications protégées
- `bw-db` : réseau isolé pour la base de données BunkerWeb

---

### WAF — BunkerWeb

BunkerWeb est configuré avec son interface d'administration accessible sur **bwui.lan**.

La configuration du proxy pour **secureweb.lan** inclut :

- Activation du WAF (ModSecurity + OWASP CRS)
- Protection anti-bot
- Blocage automatique des adresses IP via le mécanisme **Bad Behavior** (ban automatique après détection de comportements suspects répétés)
- Reverse proxy vers l'application SecureShop

---

### Monitoring

Le pipeline de collecte de logs est le suivant :

**BunkerWeb** → volume partagé `bw-logs` → **Fluent-Bit** → **OpenObserve** (stockage et alertes) + **Loki** (requêtage Grafana)

**Wazuh Agent** → volume partagé `wazuh-alerts` → **Fluent-Bit** → **OpenObserve**

### Dashboards mis en place

Des dashboards Grafana et OpenObserve ont été créés pour l'observabilité des incidents, avec des métriques dérivées des logs :

- Vue globale des accès BunkerWeb (codes HTTP, IPs sources, URLs les plus ciblées)
- Incidents de sécurité détectés par Wazuh (webshells, bruteforce, SQLi, scans)
- Disponibilité de SecureShop via Blackbox Exporter

### Règles d'alerte Prometheus / Alertmanager

| Alerte | Source | Déclencheur |
| --- | --- | --- |
| Site indisponible | Blackbox Exporter | Endpoint SecureShop ne répond plus (HTTP != 200) |
| Connexion panel admin | Logs BunkerWeb | Accès HTTP 200 sur `admin.php` |
| Upload de fichier | Logs BunkerWeb | Requête POST vers `/uploads/` |
| Brute force effectué | Logs BunkerWeb | Série d'échecs de login suivie d'un succès (même IP) |
| Énumération IDOR | Logs BunkerWeb | 10+ accès à `download_invoice.php` depuis la même IP en moins d'une minute |

---

### SIEM — Wazuh

Wazuh est déployé en mode **manager + agent** :

- Le **manager** centralise la réception des événements, applique les règles et déclenche les réponses actives
- L'**agent** surveille le répertoire de l'application SecureShop (File Integrity Monitoring) et les logs d'accès BunkerWeb

### Règles de détection personnalisées

Les règles suivantes ont été développées et intégrées dans le fichier `local_rules.xml` du manager Wazuh.

---

### 1. Upload de fichier malveillant / webshell

Règles basées sur le **File Integrity Monitoring (FIM)** du répertoire `/uploads` :

- Alerte sur tout nouveau fichier ajouté dans `/uploads/` — détection préventive de webshell
- Alerte critique sur ajout d'un fichier `.php` dans `/uploads/` — webshell confirmé, déclenche une réponse active
- Alerte sur modification de fichiers PHP du projet — possible injection de code
- Alerte critique sur modification de fichiers de configuration DB (`db.php`, `schema.sql`) — compromission possible de la couche données
- Alerte critique sur création ou modification de `.htaccess` — tentative de bypass de contrôle d'accès

---

### 2. Exécution de webshell depuis `/uploads`

Règles basées sur les logs d'accès BunkerWeb (`decoded_as: bunkerweb-access`) :

- Alerte critique sur accès HTTP 200 à un fichier dans `/uploads/` — exécution possible d'un fichier uploadé
- Alerte critique sur exécution d'un fichier `.php` depuis `/uploads/` — webshell actif confirmé

---

### 3. Attaques applicatives (SQLi, XSS, scanners)

- Événements bruts de logs d'accès BunkerWeb (règle de base, niveau 0)
- Détection de motifs SQLi dans l'URL (`select`, `union`, `sleep()`, `'--`, etc.)
- Alerte critique sur tentative SQLi ciblant spécifiquement `login.php`
- Alerte critique sur accès réussi (HTTP 200) au panel `admin.php`

---

### 4. Brute-force / authentification

- Détection de chaque tentative de login via POST sur `login.php`
- Alerte haute sur brute-force détecté : 8+ tentatives depuis la même IP en 120 secondes
- Alerte critique sur brute-force agressif : 30+ tentatives depuis la même IP en 60 secondes

---

### 5. Scans et reconnaissance (404 abuse)

- Détection des codes 404 dans les logs d'accès
- Alerte haute sur bruteforce de répertoires : 15+ erreurs 404 depuis la même IP en 30 secondes
- Alerte critique sur scan massif : 50+ erreurs 404 depuis la même IP en 60 secondes
- Alerte haute sur User-Agent de scanners connus : `sqlmap`, `nikto`, `gobuster`, `dirb`, `wfuzz`, `nmap`, `hydra`, `burpsuite`, `ZAP`

---

### 6. Énumération IDOR

- Détection de chaque accès à `download_invoice.php`
- Alerte haute sur énumération IDOR : 10+ téléchargements de factures depuis la même IP en moins d'une minute

---

### 7. Accès à fichiers sensibles

- Alerte haute sur tentative d'accès à des chemins sensibles : `.env`, `.git`, `wp-admin`, `phpmyadmin`, `/etc/passwd`, `.htpasswd`, `config.php`, fichiers `.bak`, `.swp`
