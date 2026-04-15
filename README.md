# SecureShop

Projet de 4ème année, Red Team attaque la Blue Team.

# Blue Team

## Tool

### WAF

- Coraza (https://www.coraza.io/)
- ModSecurity (https://modsecurity.org/)
- BunkerWeb (https://www.bunkerweb.io/)

| Outil | Justification |
| --- | --- |
| **Coraza** | WAF moderne, natif Go, embarquable directement dans Caddy ou Traefik via plugin. Idéal pour une stack cloud-native sans agent séparé. Compatible OWASP CRS. |
| **ModSecurity** | WAF historique et éprouvé, très documenté, s'intègre à Nginx/Apache. Pertinent si tu as déjà une infra Nginx existante ou besoin d'un moteur de règles très mature. |
| **BunkerWeb** | Solution tout-en-un (WAF + reverse proxy + hardening HTTP), orientée simplicité de déploiement Docker/Kubernetes. Pertinent pour protéger rapidement une app sans configurer plusieurs couches. |

**BunkerWeb** est une solution pertinente pour sécuriser rapidement une application web grâce à une approche intégrée combinant reverse proxy, WAF et durcissement HTTP. Tout en un, limite la complexité d’architecture et ajoute de la sécurité dans la stack via des fonctionnalités anti-bot et blocage adresse ip qui n’était pas présente dans la stack précédente.


### Reverse Proxy

- Caddy (https://caddyserver.com/)
- Traefik (https://traefik.io/traefik)

| Outil | Justification |
| --- | --- |
| **Caddy** | Gestion automatique des certificats TLS (Let's Encrypt), configuration minimaliste en Caddyfile. Idéal pour des déploiements rapides avec HTTPS automatique. S'intègre avec Coraza. |
| **Traefik** | Conçu nativement pour Kubernetes et Docker : détection automatique des services via labels/annotations. Excellent pour une infra dynamique avec beaucoup de microservices. S'intègre avec Coraza et Prometheus. |

**Caddy** intégration simple et minimaliste et s’intègre avec Coraza. Pas d’intégration natif de l’auto détection mais possible via plugin. 

### Collecteur de log

- Elastic Search (https://www.elastic.co/fr/elastic-stack)
- Loki (https://grafana.com/oss/loki/)
- OpenOserve (https://openobserve.ai/)

| Outil | Justification |
| --- | --- |
| **Elasticsearch (ELK Stack)** | Solution complète et très puissante pour l'indexation et la recherche full-text de logs. Pertinent pour de grands volumes et des besoins d'analyse complexes, mais **gourmand en ressources**. |
| **Loki** | Collecteur de logs léger de Grafana Labs, indexe uniquement les labels (pas le contenu). Parfait en complément de Prometheus/Grafana pour une stack homogène et **économe en RAM/CPU**. |
| **OpenObserve** | Alternative légère et moderne à l'ELK Stack, avec logs, métriques et traces dans un seul binaire. Très pertinent pour un environnement self-hosted avec contraintes de ressources (mini-PC, homelab). |

**OpenObserve** léger et moderne. Stack self hosted et complète. Pas besoin de stack complémentaire. Pour la ingestion, traitement et stockage des logs 

### Monitoring

- Grafana (https://grafana.com/)
- Prometheus (https://prometheus.io/)
- Blackbox exporter (https://github.com/prometheus/blackbox_exporter)

| Outil | Justification |
| --- | --- |
| **Grafana** | Couche de visualisation universelle : dashboards riches, compatible avec Prometheus, Loki, OpenObserve, Elasticsearch. Incontournable comme interface centrale de supervision. |
| **Prometheus** | Standard de facto pour la collecte de métriques dans les environnements Kubernetes. Modèle pull avec stockage TSDB efficace. S'intègre nativement avec Traefik, Caddy et la majorité des exporters. |
| **Blackbox Exporter** | Permet à Prometheus de **sonder des endpoints externes** (HTTP, HTTPS, TCP, ICMP) pour vérifier leur disponibilité. Indispensable pour monitorer des URLs publiques ou des services tiers sans accès interne. |

Utilisation de tout les tools peu d’alternative à la hauteur. Moderne, rapide et simple.


### Alerting

- Grafana alerting(https://grafana.com/docs/grafana/latest/alerting/)
- Open Observe Alerting (https://openobserve.ai/docs/user-guide/alerts/#use-cases)
- Alertmanager (https://prometheus.io/docs/alerting/latest/alertmanager/)

| Outil | Justification |
| --- | --- |
| **Grafana Alerting** | Système d'alertes intégré à Grafana, directement lié aux dashboards et datasources (Prometheus, Loki, etc.). Idéal pour centraliser alertes et visualisation dans un seul outil, avec support Slack/email/webhook. |
| **OpenObserve Alerting** | Alertes natives sur les logs et métriques ingérés dans OpenObserve. Pertinent si OpenObserve est ta source principale de données, évite une dépendance externe à Grafana. |
| **Alertmanager** | Composant officiel de l'écosystème Prometheus, gère le **routage, la déduplication et la mise en silence** des alertes. Indispensable dès que le volume d'alertes Prometheus devient important et nécessite une gestion fine (on-call, grouping). |

**Alertmanager** intégration Prometheus, parfait pour de la mise en prod avec la fonction silent et **OpenObserve** pour les alertes de log.

### SIEM

- Wazuh (https://wazuh.com/)
- Splunk (https://www.splunk.com/fr_fr)
- Graylog (https://graylog.org/)

| Outil |  Justification |
| --- | --- |
| **Wazuh** | Plateforme open source qui combine SIEM et XDR, avec détection, monitoring, alerting, réponse active et couverture endpoint/cloud. Pertinent pour une stack self-hosted qui cherche de la visibilité sécurité sans coût de licence. |
| **Splunk Enterprise Security** | SIEM de référence en entreprise pour la corrélation avancée, la recherche et les cas d’usage SOC à grande échelle. Pertinent si la priorité est la richesse fonctionnelle, l’écosystème et l’adoption marché plutôt que le coût.  |
| **Graylog Security** | SIEM orienté centralisation des logs, investigation, alerting et réduction du bruit, avec une approche pragmatique pour les équipes sécurité. Pertinent si tu veux un outil centré opérations, lisible et moins lourd à appréhender qu’un gros stack analytique. |

**Wazuh** solution SIEM + EDR pour une possibilité intégration des machines du parc de l’entreprise, absence de coût de licence et sa bonne intégration avec des environnements open source en font le choix le plus cohérent pour une architecture moderne et maîtrisée.

---

# Installation

## Configuration

- Docker.
- Fichier de configuration yml par tool.
- Séparation des tools et segmentation réseau.
- Bunker Web config avec ui accessible sur bwui.lan :
    - Configuration du proxy pour secure web avec WAF, Anti-Bot, blocage IP
- Grafana :
    - Connexion au log de fluentbit avec Loki
    - Création d’un dashboard de log
