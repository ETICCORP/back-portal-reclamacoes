#!/bin/bash

# Detecta branch atual
BRANCH=$(git rev-parse --abbrev-ref HEAD)

# Adiciona tudo
git add .

# Cria commit com data da semana
git commit -m "Weekly auto-commit - $(date +'%Y-%m-%d')"

# Faz push para o branch atual
git push origin "$BRANCH"
