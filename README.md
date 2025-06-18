# MealMates - Backend API

API REST du projet MealMates, plateforme de revente de produits alimentaires entre particuliers.

## 🧠 Objectif

Permettre la gestion sécurisée des utilisateurs, annonces de produits, et interactions entre clients via une API Symfony.

## ⚙️ Stack technique

- **Symfony 6**
- **Doctrine / MySQL**
- **JWT Auth**
- **Postman / Insomnia** (tests API)

## 📁 Fonctionnalités

- 🔐 Authentification (JWT)
- 📄 Création, modification, suppression d’annonces
- 🔍 Endpoints de recherche filtrée

## 🚀 Installation

Frontend du projet : [MealMates-React](https://github.com/Magiks0/MealMates-React)

```bash
git clone https://github.com/Magiks0/MealMates-SF.git
cd MealMates-SF
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
symfony server:start
