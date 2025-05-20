#!/bin/bash

# Variables (Personnalisez ces valeurs)
VPS_USER="issadrici"
VPS_HOST="154.16.112.86"
PROJECT_DIR="/var/www/eatsup/eatsup_backend"

# Exécuter les commandes sur le VPS via SSH
ssh ${VPS_USER}@${VPS_HOST} << 'EOF'
PROJECT_DIR="/var/www/eatsup/eatsup_backend"

cd $PROJECT_DIR || { echo "Erreur : Le répertoire $PROJECT_DIR n'existe pas."; exit 1; }

echo "Mise à jour du projet Laravel..."
git pull origin master

php artisan migrate --force

php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "Déploiement Laravel terminé avec succès dans $PROJECT_DIR."
EOF
