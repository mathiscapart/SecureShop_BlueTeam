ENV_FILE=.env

WAF_COMPOSE=-f waf/docker-compose.yml
SIEM_COMPOSE=-f siem/docker-compose.yml
MONITORING_COMPOSE=-f monitoring/docker-compose.yml
APP_COMPOSE=-f docker-compose.yml

.PHONY: up down restart logs status ps waf siem monitoring app

up:
	docker network inspect blue-team >NUL 2>&1 || docker network create blue-team
	docker network inspect bw-universe >NUL 2>&1 || docker network create --subnet=10.20.30.0/24 bw-universe
	docker network inspect bw-services >NUL 2>&1 || docker network create bw-services
	docker network inspect bw-db >NUL 2>&1 || docker network create bw-db
	docker volume inspect bw-logs >NUL 2>&1 || docker volume create bw-logs
	docker volume inspect wazuh-alerts >NUL 2>&1 || docker volume create wazuh-alerts
	docker volume inspect wazuh-etc >NUL 2>&1 || docker volume create wazuh-etc
	docker compose --env-file $(ENV_FILE) $(APP_COMPOSE) up -d --build
	docker compose --env-file $(ENV_FILE) $(WAF_COMPOSE) up -d
	docker compose --env-file $(ENV_FILE) $(SIEM_COMPOSE) up -d
	docker compose --env-file $(ENV_FILE) $(MONITORING_COMPOSE) up -d

down:
	docker compose --env-file $(ENV_FILE) $(MONITORING_COMPOSE) down
	docker compose --env-file $(ENV_FILE) $(SIEM_COMPOSE) down
	docker compose --env-file $(ENV_FILE) $(WAF_COMPOSE) down
	docker compose --env-file $(ENV_FILE) $(APP_COMPOSE) down

down_all:
	docker compose --env-file $(ENV_FILE) $(MONITORING_COMPOSE) down -v
	docker compose --env-file $(ENV_FILE) $(SIEM_COMPOSE) down -v
	docker compose --env-file $(ENV_FILE) $(WAF_COMPOSE) down -v
	docker compose --env-file $(ENV_FILE) $(APP_COMPOSE) down -v

restart: down up

logs:
	docker compose --env-file $(ENV_FILE) $(WAF_COMPOSE) logs --tail=50
	docker compose --env-file $(ENV_FILE) $(SIEM_COMPOSE) logs --tail=50
	docker compose --env-file $(ENV_FILE) $(MONITORING_COMPOSE) logs --tail=50
	docker compose --env-file $(ENV_FILE) $(APP_COMPOSE) logs --tail=50

status:
	docker ps

ps:
	docker compose --env-file $(ENV_FILE) $(WAF_COMPOSE) ps
	docker compose --env-file $(ENV_FILE) $(SIEM_COMPOSE) ps
	docker compose --env-file $(ENV_FILE) $(MONITORING_COMPOSE) ps
	docker compose --env-file $(ENV_FILE) $(APP_COMPOSE) ps

waf:
	docker compose --env-file $(ENV_FILE) $(WAF_COMPOSE) up -d

siem:
	docker compose --env-file $(ENV_FILE) $(SIEM_COMPOSE) up -d

monitoring:
	docker compose --env-file $(ENV_FILE) $(MONITORING_COMPOSE) up -d

app:
	docker compose --env-file $(ENV_FILE) $(APP_COMPOSE) up -d
