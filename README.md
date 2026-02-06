# Proyecto "Agenda Digital" (VERSION DEMO)
Aplicación tipo Trello desarrollada con Laravel + Docker, que permite gestionar tareas mediante una API REST y una interfaz web con Drag & drop.

## Funcionalidades
- Crear tareas nuevas
- Visualización en 3 columnas
- Movimiento de tareas entre columnas mediante Drag & Drop
- Persistencia de tareas en base de datos "Mysql"
- Edición y eliminación de tareas
- API REST desacoplada del frontend
- Entorno completamente dockerizado

## Tecnologías utilizadas
Backend
- PHP 8.2
- Laravel
- Eloquent ORM
- MySQL 8
- API REST (JSON)

Frontend
- Blade (Laravel Views)
- HTML5
- CSS
- JavaScript (Vanilla JS)
- Drag & Drop nativo del navegador

## Comandos 

-Obtener tareas

curl -H "Accept: application/json" http://localhost:8080/api/tasks

-Crear una tarea

curl -X POST http://localhost:8080/api/tasks \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"title":"Mi primera task","status":"backlog"}'

-Actualizar tarea

curl -X PATCH http://localhost:8080/api/tasks/1 \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"status":"in_progress"}'

-Eliminar tarea

curl -X DELETE http://localhost:8080/api/tasks/1 \
  -H "Accept: application/json"

-Acceso a la app

http://localhost:8080/tasks

## Comandos ACTUALIZADOS 6/2/2026

-Clonar Repositorio:

git clone https://github.com/Lapham-dev/api-tech-test.git
 cd api-tech-test

-Levantar contenedores:

docker compose up -d --build

-Instalar Laravel:

docker compose exec app composer install

-Configuración del entorno:

docker compose exec app cp .env.example .env
 docker compose exec app php artisan key:generate

-Migrar base de datos:

docker compose exec app php artisan migrate 

-Haciendo todo paso a paso como esta en los comando nuevos la aplicacion ya estaria funcionando

## EN CASO QUE PIDA PERMISOS

En caso que pida permisos pero no deberia, dejo esta linea de codigo aca: 

sudo docker compose up -d --build

## Ideas futuras

-Agregar un sistema de usuarios con login

-Poner fechas limite a tareas

-Extension de Chromium que avise a el usuario por mail cuando una tarea se este por vencer

-Historial de acciones de cada usuario con su agenda

















