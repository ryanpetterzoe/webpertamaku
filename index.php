<?php
/**
 * SMK Pertamaku Website
 * Main entry point - bootstraps config and routes
 */

// Load app configuration (session, DB, helpers)
require_once __DIR__ . '/config/app.php';

// Load and execute router
require_once __DIR__ . '/routes/web.php';
