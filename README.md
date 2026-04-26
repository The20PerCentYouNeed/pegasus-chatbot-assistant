# Pegasus Chatbot Assistant

Pegasus Chatbot Assistant is an AI-powered customer support platform built for courier companies that use the Pegasus ERP system. It combines a Laravel backend, a Pegasus integration layer, and an embeddable website chat widget so businesses can offer faster, more accessible customer support through a chatbot experience.

This project was designed as a practical support solution for logistics and courier operations where customers often need quick answers about shipments, service processes, and common support requests without waiting for a human agent.

## Overview

The application connects an AI assistant to Pegasus ERP-driven support workflows and exposes that assistant through a website chatbot UI. It also includes an internal administration area for managing agents, conversations, and knowledge documents.

In practical terms, the platform enables a courier company to:

- embed a chatbot on its website
- route customer questions through an AI assistant tailored to courier support
- integrate chatbot behavior with Pegasus-backed business data and processes
- manage conversations and assistant configuration from an admin interface

## Key Features

- AI customer support assistant focused on courier-company use cases
- Integration layer for Pegasus ERP communication
- Embeddable web chat widget for customer-facing websites
- Conversation and message handling through API endpoints
- Admin resources for agents, conversations, and knowledge documents
- Queue-ready Laravel backend for asynchronous processing

## Tech Stack

- Laravel 12
- PHP 8.2+
- Laravel AI
- Filament Admin Panel
- Laravel Sanctum
- Laravel Horizon
- Vite
- Tailwind CSS

## Why This Project Matters

This project demonstrates how AI can be applied to a real business domain instead of being used as a generic chatbot demo. The focus is on a specific operational context: courier companies already running on Pegasus ERP and needing a better first-line customer support experience.

From a portfolio perspective, it highlights:

- product thinking around a real business problem
- backend integration with an external ERP system
- AI-assisted support workflows
- full-stack delivery, including both admin tooling and customer-facing UI

## Main Functional Areas

### 1. Website Chat Widget

The project includes a customer-facing chat widget that can be embedded into a company website, giving end users a simple chatbot interface for support requests.

### 2. AI Support Agents

The codebase includes dedicated support agents for Pegasus-related customer support scenarios, including specialized agent classes for customer support behavior.

### 3. Pegasus ERP Integration

The chatbot is designed to work alongside Pegasus ERP so courier businesses can align chatbot support flows with their operational systems.

### 4. Admin and Knowledge Management

The internal admin area supports management of:

- AI agents
- customer conversations
- knowledge documents used to support the assistant's responses

## Project Structure

Key areas of the codebase:

- `app/Ai` for AI agents, middleware, and tools
- `app/Services/PegasusClient.php` for Pegasus-related service integration
- `app/Filament` for the admin interface
- `routes/api.php` for chat session and message endpoints
- `public/chat-widget` for widget assets
- `resources` and `public` for frontend assets and application views

## Local Development

### Requirements

- PHP 8.2+
- Composer
- Node.js and npm
- A database supported by Laravel

### Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
```

### Important Environment Variables

Configure the Pegasus integration in your `.env` file:

```env
PEGASUS_API_URL=
PEGASUS_API_KEY=
PEGASUS_TIMEOUT=30
CHAT_RATE_LIMIT=20
```

### Run the Application

For a full local development workflow:

```bash
composer run dev
```

This starts the Laravel server, queue listener, log viewer, and Vite development server together.

If you prefer running services manually:

```bash
php artisan serve
php artisan queue:listen --tries=1 --timeout=0
npm run dev
```

### Run Tests

```bash
composer test
```

### Build Frontend Assets

```bash
npm run build
```

## Intended Use Case

This system is intended for courier and logistics businesses that want to improve customer support on their website by offering instant chatbot assistance while keeping the solution aligned with their Pegasus ERP ecosystem.
